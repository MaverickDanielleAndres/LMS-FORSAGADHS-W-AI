<?php
session_start();
header("Content-Type: application/json");
require_once("../config.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "error" => "Invalid request method"]);
    exit();
}

// Check if user is logged in as Faculty
if (!isset($_SESSION['role']) || $_SESSION['role'] != "faculty") {
    echo json_encode(["success" => false, "error" => "Unauthorized access"]);
    exit();
}

$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

if (!$data) {
    echo json_encode(["success" => false, "error" => "Invalid JSON data"]);
    exit();
}

try {
    $conn->begin_transaction();

    // Validate required fields
    if (empty($data['title']) || empty($data['subject']) || empty($data['branch']) || empty($data['semester'])) {
        throw new Exception("Missing required fields: title, subject, branch, or semester");
    }

    // Cast and sanitize data
    $totalScore = isset($data['totalScore']) ? (float)$data['totalScore'] : 0;
    $numQuestions = isset($data['numQuestions']) ? (int)$data['numQuestions'] : 0;
    $duration = !empty($data['duration']) ? (int)$data['duration'] : null;
    $deadline = !empty($data['deadline']) ? $data['deadline'] : null;
    $facultyId = isset($data['facultyId']) ? (int)$data['facultyId'] : $_SESSION['fid'];

    // Validate parts data
    if (!isset($data['parts']) || !is_array($data['parts']) || empty($data['parts'])) {
        throw new Exception("No quiz parts provided");
    }

    // Insert into quizmaster
    $stmt = $conn->prepare("
        INSERT INTO quizmaster 
        (QuizTitle, QuizDescription, QuizInstructions, QuizType, QuizSubject, QuizBranch, QuizDuration, QuizDeadline, QuizUploadedBy, QuizUploadDate, QuizForSemester, TotalScore, TotalQuestions)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), ?, ?, ?)
    ");

    if (!$stmt) {
        throw new Exception("Prepare failed for quizmaster: " . $conn->error);
    }

    $stmt->bind_param(
        "ssssiiissdid",
        $data['title'],                    // s - QuizTitle
        $data['description'],              // s - QuizDescription
        $data['instructions'],             // s - QuizInstructions
        $data['activityType'],             // s - QuizType
        $data['subject'],                  // i - QuizSubject
        $data['branch'],                   // i - QuizBranch
        $duration,                         // i - QuizDuration
        $deadline,                         // s - QuizDeadline
        $facultyId,                        // s - QuizUploadedBy
        $data['semester'],                 // d - QuizForSemester
        $totalScore,                       // i - TotalScore
        $numQuestions                      // d - TotalQuestions
    );

    if (!$stmt->execute()) {
        throw new Exception("Quiz insert failed: " . $stmt->error);
    }

    $quizId = $conn->insert_id;
    $stmt->close();

    // Insert parts and questions
    foreach ($data['parts'] as $partIndex => $part) {
        if (!isset($part['questions']) || empty($part['questions'])) {
            continue; // Skip empty parts
        }

        $partNum = $partIndex + 1;
        $numQuestionsInPart = count($part['questions']);
        $partTitle = isset($part['title']) ? $part['title'] : "Part {$partNum}";
        $partType = isset($part['type']) ? $part['type'] : 'mixed';

        // Insert into quizparts
        $stmt = $conn->prepare("
            INSERT INTO quizparts (QuizId, PartNumber, PartTitle, PartType, NumQuestions, PartOrder)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        if (!$stmt) throw new Exception("Prepare failed for quizparts: " . $conn->error);

        $stmt->bind_param("iissii", $quizId, $partNum, $partTitle, $partType, $numQuestionsInPart, $partNum);
        if (!$stmt->execute()) {
            throw new Exception("Part insert failed: " . $stmt->error);
        }
        $partId = $conn->insert_id;
        $stmt->close();

        // Insert questions for this part
        foreach ($part['questions'] as $qIndex => $question) {
            if (empty($question['text'])) {
                continue; // Skip empty questions
            }

            $qNum = $qIndex + 1;
            $correctAnswer = null;
            $questionPoints = isset($question['points']) ? (float)$question['points'] : 1.0;

            // Handle correct answers based on question type
            if (isset($question['correctAnswers']) && is_array($question['correctAnswers'])) {
                if (in_array($question['type'], ['enumeration','essay','identification'])) {
                    $correctAnswer = implode(";", $question['correctAnswers']);
                } elseif ($question['type'] === 'true_false') {
                    $correctAnswer = !empty($question['correctAnswers'][0]) ? $question['correctAnswers'][0] : null;
                } elseif ($question['type'] === 'mcq') {
                    $correctAnswer = implode(",", $question['correctAnswers']);
                } elseif ($question['type'] === 'matching') {
                    $correctAnswer = json_encode($question['correctAnswers']);
                }
            }

            // Insert into quizquestions
            $stmt = $conn->prepare("
                INSERT INTO quizquestions (QuizId, PartId, QuestionNumber, QuestionText, QuestionType, QuestionPoints, QuestionOrder, CorrectAnswer)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            if (!$stmt) throw new Exception("Prepare failed for quizquestions: " . $conn->error);

            $stmt->bind_param(
                "iiissdis",
                $quizId,
                $partId,
                $qNum,
                $question['text'],
                $question['type'],
                $questionPoints,
                $qNum,
                $correctAnswer
            );
            if (!$stmt->execute()) {
                throw new Exception("Question insert failed: " . $stmt->error);
            }
            $questionId = $conn->insert_id;
            $stmt->close();

            // Insert question options for MCQ questions
            if ($question['type'] === 'mcq' && isset($question['choices']) && is_array($question['choices'])) {
                foreach ($question['choices'] as $choice) {
                    if (empty($choice['text'])) continue;

                    $isCorrect = 0;
                    if (isset($question['correctAnswers']) && is_array($question['correctAnswers'])) {
                        $isCorrect = in_array($choice['letter'], $question['correctAnswers']) ? 1 : 0;
                    }
                    
                    $order = ord($choice['letter']) - 64; // A=1, B=2...

                    $stmt = $conn->prepare("
                        INSERT INTO quizquestionoptions (QuestionId, OptionLetter, OptionText, IsCorrect, OptionOrder)
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    if (!$stmt) throw new Exception("Prepare failed for quizquestionoptions: " . $conn->error);

                    $stmt->bind_param("issii", $questionId, $choice['letter'], $choice['text'], $isCorrect, $order);
                    if (!$stmt->execute()) {
                        throw new Exception("Option insert failed: " . $stmt->error);
                    }
                    $stmt->close();
                }
            }

            // Insert options for True/False questions
            if ($question['type'] === 'true_false') {
                $tfOptions = [
                    ['letter' => 'A', 'text' => 'True'],
                    ['letter' => 'B', 'text' => 'False']
                ];

                foreach ($tfOptions as $option) {
                    $isCorrect = 0;
                    if (isset($question['correctAnswers']) && is_array($question['correctAnswers'])) {
                        $isCorrect = in_array($option['letter'], $question['correctAnswers']) ? 1 : 0;
                    }
                    
                    $order = ord($option['letter']) - 64; // A=1, B=2

                    $stmt = $conn->prepare("
                        INSERT INTO quizquestionoptions (QuestionId, OptionLetter, OptionText, IsCorrect, OptionOrder)
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    if (!$stmt) throw new Exception("Prepare failed for quizquestionoptions: " . $conn->error);

                    $stmt->bind_param("issii", $questionId, $option['letter'], $option['text'], $isCorrect, $order);
                    if (!$stmt->execute()) {
                        throw new Exception("True/False option insert failed: " . $stmt->error);
                    }
                    $stmt->close();
                }
            }
        }
    }

    // Insert AI generation record if this is an AI-generated quiz
    if (isset($data['method']) && $data['method'] === 'ai') {
        $generationType = 'full_quiz';
        $generationParams = json_encode([
            'numQuestions' => $numQuestions,
            'difficulty' => $data['difficulty'] ?? 'mixed',
            'questionType' => $data['questionType'] ?? 'mixed',
            'source' => $data['sourceType'] ?? 'unknown'
        ]);

        $stmt = $conn->prepare("
            INSERT INTO quizaigeneration (QuizId, GenerationType, GenerationParameters, GenerationStatus, GeneratedBy)
            VALUES (?, ?, ?, 'completed', ?)
        ");
        if (!$stmt) throw new Exception("Prepare failed for quizaigeneration: " . $conn->error);

        $stmt->bind_param("issi", $quizId, $generationType, $generationParams, $facultyId);
        if (!$stmt->execute()) {
            throw new Exception("AI generation record insert failed: " . $stmt->error);
        }
        $stmt->close();
    }

    $conn->commit();
    
    echo json_encode([
        "success" => true, 
        "quizId" => $quizId,
        "message" => "Quiz created successfully",
        "details" => [
            "title" => $data['title'],
            "totalQuestions" => $numQuestions,
            "totalScore" => $totalScore,
            "parts" => count($data['parts'])
        ]
    ]);

} catch (Exception $e) {
    $conn->rollback();
    error_log("Quiz creation error: " . $e->getMessage());
    echo json_encode([
        "success" => false, 
        "error" => $e->getMessage()
    ]);
}
?>
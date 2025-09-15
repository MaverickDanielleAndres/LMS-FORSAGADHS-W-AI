<?php
session_start();
header('Content-Type: application/json');
require_once '../config.php';

// Security check
if ($_SESSION['role'] !== 'faculty') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
}

try {
    // Get and validate input data
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data || json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON data received');
    }
    
    // Validate required fields
    $requiredFields = ['title', 'subject', 'branch', 'semester', 'facultyId', 'questions'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || (is_string($data[$field]) && trim($data[$field]) === '')) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    if (empty($data['questions']) || !is_array($data['questions'])) {
        throw new Exception('No questions provided or questions is not an array');
    }
    
    // Sanitize and validate data
    $title = trim($data['title']);
    $description = isset($data['description']) ? trim($data['description']) : '';
    $instructions = isset($data['instructions']) ? trim($data['instructions']) : '';
    $activityType = isset($data['activityType']) ? trim($data['activityType']) : 'quiz';
    $subject = (int)$data['subject'];
    $branch = (int)$data['branch'];
    $semester = (int)$data['semester'];
    $facultyId = (int)$data['facultyId'];
    $duration = !empty($data['duration']) ? (int)$data['duration'] : null;
    $deadline = !empty($data['deadline']) ? $data['deadline'] : null;
    $totalScore = isset($data['totalScore']) ? (float)$data['totalScore'] : 0;
    $totalQuestions = count($data['questions']);
    
    // Validate foreign key relationships
    $stmt = $conn->prepare("SELECT COUNT(*) FROM subjectmaster WHERE SubjectCode = ? AND SubjectFacultyId = ?");
    $stmt->bind_param('ii', $subject, $facultyId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->fetch_row()[0] == 0) {
        throw new Exception('Invalid subject or you do not have permission to create quizzes for this subject');
    }
    $stmt->close();
    
    // Validate datetime format for deadline
    if ($deadline) {
        $deadlineDateTime = DateTime::createFromFormat('Y-m-d\TH:i', $deadline);
        if (!$deadlineDateTime) {
            throw new Exception('Invalid deadline format');
        }
        $deadline = $deadlineDateTime->format('Y-m-d H:i:s');
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // 1. Insert quiz master record
        $stmt = $conn->prepare("INSERT INTO quizmaster 
            (QuizTitle, QuizDescription, QuizInstructions, QuizType, QuizSubject, QuizBranch, 
             QuizDuration, QuizDeadline, QuizUploadedBy, QuizUploadDate, QuizForSemester, 
             TotalScore, TotalQuestions, IsShuffled, ShowResults, AllowRetake) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), ?, ?, ?, 0, 1, 0)");
        
        $stmt->bind_param('ssssiiisiiii', 
            $title, $description, $instructions, $activityType, $subject, $branch,
            $duration, $deadline, $facultyId, $semester, $totalScore, $totalQuestions
        );
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to create quiz: ' . $stmt->error);
        }
        
        $quizId = $conn->insert_id;
        $stmt->close();
        
        // 2. Create a single quiz part for all questions
        $stmt = $conn->prepare("INSERT INTO quizparts 
            (QuizId, PartNumber, PartTitle, PartType, NumQuestions, PartOrder) 
            VALUES (?, 1, 'Generated Questions', 'mixed', ?, 1)");
        
        $stmt->bind_param('ii', $quizId, $totalQuestions);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to create quiz part: ' . $stmt->error);
        }
        
        $partId = $conn->insert_id;
        $stmt->close();
        
        // 3. Insert questions and their options
        $questionStmt = $conn->prepare("INSERT INTO quizquestions 
            (QuizId, PartId, QuestionNumber, QuestionText, QuestionType, QuestionPoints, 
             QuestionOrder, CorrectAnswer) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        $optionStmt = $conn->prepare("INSERT INTO quizquestionoptions 
            (QuestionId, OptionLetter, OptionText, IsCorrect, OptionOrder) 
            VALUES (?, ?, ?, ?, ?)");
        
        foreach ($data['questions'] as $index => $question) {
            // Validate question data
            if (!isset($question['text']) || !isset($question['points']) || !isset($question['choices'])) {
                throw new Exception("Invalid question data at index $index");
            }
            
            $questionNumber = $index + 1;
            $questionText = trim($question['text']);
            $questionType = isset($question['type']) ? $question['type'] : 'mcq';
            $questionPoints = (float)$question['points'];
            $correctAnswers = isset($question['correctAnswers']) ? $question['correctAnswers'] : [];
            $correctAnswerString = is_array($correctAnswers) ? implode('', $correctAnswers) : '';
            
            if (empty($questionText)) {
                throw new Exception("Empty question text at question $questionNumber");
            }
            
            // Insert question
            $questionStmt->bind_param('iiiisdis', 
                $quizId, $partId, $questionNumber, $questionText, $questionType, 
                $questionPoints, $questionNumber, $correctAnswerString
            );
            
            if (!$questionStmt->execute()) {
                throw new Exception("Failed to insert question $questionNumber: " . $questionStmt->error);
            }
            
            $questionId = $conn->insert_id;
            
            // Insert options if they exist
            if (isset($question['choices']) && is_array($question['choices'])) {
                foreach ($question['choices'] as $choice) {
                    if (!isset($choice['letter']) || !isset($choice['text'])) {
                        continue; // Skip invalid choices
                    }
                    
                    $optionLetter = strtoupper(trim($choice['letter']));
                    $optionText = trim($choice['text']);
                    $isCorrect = isset($choice['isCorrect']) ? (bool)$choice['isCorrect'] : false;
                    $optionOrder = ord($optionLetter) - ord('A') + 1;
                    
                    if (empty($optionText)) {
                        continue; // Skip empty options
                    }
                    
                    $optionStmt->bind_param('issii', 
                        $questionId, $optionLetter, $optionText, $isCorrect, $optionOrder
                    );
                    
                    if (!$optionStmt->execute()) {
                        throw new Exception("Failed to insert option for question $questionNumber: " . $optionStmt->error);
                    }
                }
            }
        }
        
        $questionStmt->close();
        $optionStmt->close();
        
        // 4. Log the quiz creation activity (optional)
        $logStmt = $conn->prepare("INSERT INTO quizaigeneration 
            (QuizId, GenerationType, GenerationStatus, GeneratedBy, GeneratedAt) 
            VALUES (?, 'full_quiz', 'completed', ?, NOW())");
        $logStmt->bind_param('ii', $quizId, $facultyId);
        $logStmt->execute();
        $logStmt->close();
        
        // Commit the transaction
        $conn->commit();
        
        // Log successful creation
        error_log("Quiz created successfully - ID: $quizId, Title: $title, Faculty: $facultyId, Questions: $totalQuestions");
        
        echo json_encode([
            'success' => true,
            'message' => "Quiz '$title' created successfully with $totalQuestions questions!",
            'quizId' => $quizId,
            'totalQuestions' => $totalQuestions,
            'totalPoints' => $totalScore,
            'redirect' => "quiz_list.php"
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log("Post Quiz AI Error: " . $e->getMessage());
    error_log("Input data: " . substr($input, 0, 500) . "...");
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'message' => 'Failed to save quiz to database'
    ]);
}
?>
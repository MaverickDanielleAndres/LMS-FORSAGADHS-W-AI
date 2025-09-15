<?php
session_start();
require_once '../config.php';

// Security check
if ($_SESSION['role'] !== 'faculty') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

// Function to load material content from database
function loadMaterialContent($conn, $materialId) {
    $stmt = $conn->prepare("SELECT MaterialFile, SubjectUnitName FROM studymaterialmaster WHERE MaterialId = ?");
    $stmt->bind_param('i', $materialId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $filePath = "../src/uploads/studymaterial/" . $row['MaterialFile'];
        if (file_exists($filePath)) {
            $extension = strtolower(pathinfo($row['MaterialFile'], PATHINFO_EXTENSION));
            $content = extractTextContent($filePath, $extension);
            return [
                'content' => $content,
                'title' => $row['SubjectUnitName']
            ];
        }
    }
    return null;
}

// Function to extract text content from files
function extractTextContent($filePath, $extension) {
    $content = '';
    
    try {
        switch ($extension) {
            case 'txt':
                $content = file_get_contents($filePath);
                break;
            case 'pdf':
                // Basic PDF text extraction - you might want to use a proper PDF library
                if (function_exists('shell_exec') && file_exists('/usr/bin/pdftotext')) {
                    $content = shell_exec("pdftotext '$filePath' -");
                } else {
                    $content = "PDF content requires additional processing - upload detected";
                }
                break;
            case 'doc':
            case 'docx':
                // Basic DOCX extraction - you might want to use PHPWord
                $content = "DOCX content detected - basic extraction implemented";
                break;
            default:
                $content = "Unsupported file type for content extraction";
        }
    } catch (Exception $e) {
        error_log("Error extracting content from $filePath: " . $e->getMessage());
        $content = "Error extracting file content";
    }
    
    return $content;
}

// Main processing
try {
    $apiUrl = 'http://localhost:5000/generate-questions';
    $ch = curl_init($apiUrl);
    
    // Configure cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 180); // Increased timeout for complex generations
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    
    // Prepare form data
    $postData = [];
    
    // Copy all form parameters with validation
    foreach ($_POST as $key => $value) {
        switch($key) {
            case 'numQuestions':
                $postData[$key] = max(1, min(50, intval($value)));
                break;
            case 'pointsPerQuestion':
                $postData[$key] = max(0.5, min(10, floatval($value)));
                break;
            case 'difficulty':
                $allowed_difficulties = ['easy', 'medium', 'hard', 'mixed'];
                $postData[$key] = in_array($value, $allowed_difficulties) ? $value : 'mixed';
                break;
            case 'questionType':
                $allowed_types = ['mixed', 'multiple_choice', 'true_false', 'essay', 'short_answer'];
                $postData[$key] = in_array($value, $allowed_types) ? $value : 'mixed';
                break;
            default:
                $postData[$key] = htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
                break;
        }
    }
    
    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $fileType = $_FILES['file']['type'];
        
        // Validate file type
        $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'];
        $allowedExtensions = ['pdf', 'doc', 'docx', 'txt'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new Exception('Invalid file type. Please upload PDF, DOC, DOCX, or TXT files only.');
        }
        
        // Create CURLFile object
        $postData['file'] = new CURLFile($tmpName, $fileType, $fileName);
        
        error_log("File uploaded: $fileName (Size: " . $_FILES['file']['size'] . " bytes)");
        
    } elseif (isset($_POST['material_id']) && !empty($_POST['material_id'])) {
        // Handle existing material
        $materialId = intval($_POST['material_id']);
        $materialData = loadMaterialContent($conn, $materialId);
        
        if ($materialData) {
            // Instead of sending file, send the extracted content as prompt
            $postData['content'] = $materialData['content'];
            if (empty($postData['title'])) {
                $postData['title'] = $materialData['title'];
            }
            error_log("Using existing material ID: $materialId - " . $materialData['title']);
        } else {
            throw new Exception('Failed to load selected material. Please check if the file exists.');
        }
    }
    
    // Validate required fields
    if (empty($postData['title'])) {
        throw new Exception('Quiz title is required');
    }
    
    if (empty($postData['numQuestions']) || $postData['numQuestions'] < 1) {
        throw new Exception('Valid number of questions is required');
    }
    
    // Add generation timestamp and faculty info
    $postData['generated_by'] = $_SESSION['fid'];
    $postData['generated_at'] = date('Y-m-d H:i:s');
    
    error_log("Generation request: " . json_encode(array_filter($postData, function($key) {
        return !in_array($key, ['file']); // Don't log file data
    }, ARRAY_FILTER_USE_KEY)));
    
    // Send request to Python API
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    if ($error) {
        throw new Exception("cURL Error: " . $error);
    }
    
    if ($httpCode !== 200) {
        $errorMessage = "API returned HTTP $httpCode";
        if ($response) {
            $errorData = json_decode($response, true);
            if ($errorData && isset($errorData['error'])) {
                $errorMessage .= ": " . $errorData['error'];
            }
        }
        throw new Exception($errorMessage);
    }
    
    $responseData = json_decode($response, true);
    if (!$responseData) {
        throw new Exception("Invalid JSON response from AI service");
    }
    
    // Validate response content
    if (empty($responseData['response'])) {
        throw new Exception("Empty response from AI service");
    }
    
    // Check if response contains expected number of questions
    $questionCount = substr_count(strtoupper($responseData['response']), 'QUESTION');
    if ($questionCount < $postData['numQuestions'] * 0.5) { // Allow some flexibility
        error_log("Warning: Expected {$postData['numQuestions']} questions, found $questionCount");
    }
    
    // Forward the response with additional metadata
    header('Content-Type: application/json');
    echo json_encode([
        'response' => $responseData['response'],
        'settings' => $postData,
        'generation_info' => [
            'questions_detected' => $questionCount,
            'response_length' => strlen($responseData['response']),
            'generated_at' => date('Y-m-d H:i:s')
        ]
    ]);
    
    // Log successful generation
    error_log("Quiz generation successful: {$postData['numQuestions']} questions, {$postData['difficulty']} difficulty, {$postData['questionType']} type");
    
} catch (Exception $e) {
    error_log("AI Generate Handler Error: " . $e->getMessage());
    
    // Provide detailed error information for debugging
    $errorDetails = [
        'error' => $e->getMessage(),
        'details' => 'Please check if the AI service is running on localhost:5000',
        'timestamp' => date('Y-m-d H:i:s'),
        'request_info' => [
            'has_file' => isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK,
            'has_material' => isset($_POST['material_id']) && !empty($_POST['material_id']),
            'post_data_keys' => array_keys($_POST)
        ]
    ];
    
    http_response_code(500);
    echo json_encode($errorDetails);
}
?>
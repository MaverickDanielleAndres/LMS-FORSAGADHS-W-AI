<?php
/**
 * AI Quiz Generator Configuration
 * Contains constants and helper functions for AI integration
 */

// AI Service Configuration
define('AI_SERVICE_URL', 'http://localhost:5000');
define('AI_TIMEOUT', 60); // seconds
define('AI_MAX_RETRIES', 3);

// Supported file types for AI processing
define('AI_SUPPORTED_EXTENSIONS', ['txt', 'pdf', 'doc', 'docx', 'ppt', 'pptx']);

// Question generation limits
define('AI_MIN_QUESTIONS', 1);
define('AI_MAX_QUESTIONS', 50);
define('AI_DEFAULT_QUESTIONS', 10);

// Difficulty levels
define('AI_DIFFICULTY_LEVELS', ['easy', 'medium', 'hard', 'mixed']);

// Question types
define('AI_QUESTION_TYPES', ['mcq', 'true_false', 'enumeration', 'essay', 'identification', 'mixed']);

/**
 * Check if AI service is available
 * @return bool
 */
function isAIServiceAvailable() {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, AI_SERVICE_URL . '/health');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $result !== false && $httpCode === 200;
}

/**
 * Validate file for AI processing
 * @param string $filename
 * @param int $fileSize
 * @return array
 */
function validateFileForAI($filename, $fileSize) {
    $errors = [];
    
    // Check file extension
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    if (!in_array($extension, AI_SUPPORTED_EXTENSIONS)) {
        $errors[] = "Unsupported file type. Supported types: " . implode(', ', AI_SUPPORTED_EXTENSIONS);
    }
    
    // Check file size (100MB limit)
    $maxSize = 100 * 1024 * 1024; // 100MB in bytes
    if ($fileSize > $maxSize) {
        $errors[] = "File too large. Maximum size is 100MB.";
    }
    
    // Check for empty files
    if ($fileSize === 0) {
        $errors[] = "File is empty.";
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

/**
 * Sanitize and validate question parameters
 * @param array $params
 * @return array
 */
function validateQuestionParameters($params) {
    $sanitized = [];
    
    // Number of questions
    $numQuestions = isset($params['numQuestions']) ? (int)$params['numQuestions'] : AI_DEFAULT_QUESTIONS;
    $sanitized['numQuestions'] = max(AI_MIN_QUESTIONS, min(AI_MAX_QUESTIONS, $numQuestions));
    
    // Difficulty level
    $difficulty = isset($params['difficulty']) ? strtolower(trim($params['difficulty'])) : 'mixed';
    $sanitized['difficulty'] = in_array($difficulty, AI_DIFFICULTY_LEVELS) ? $difficulty : 'mixed';
    
    // Question type
    $questionType = isset($params['questionType']) ? strtolower(trim($params['questionType'])) : 'mixed';
    $sanitized['questionType'] = in_array($questionType, AI_QUESTION_TYPES) ? $questionType : 'mixed';
    
    // Subject context
    $sanitized['subjectContext'] = isset($params['subjectContext']) ? trim($params['subjectContext']) : 'General Studies';
    
    // Topic (for topic-based generation)
    $sanitized['topic'] = isset($params['topic']) ? trim($params['topic']) : '';
    
    return $sanitized;
}

/**
 * Log AI service activity
 * @param string $action
 * @param array $data
 * @param bool $success
 */
function logAIActivity($action, $data = [], $success = true) {
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'action' => $action,
        'success' => $success,
        'data' => $data,
        'user_id' => $_SESSION['fid'] ?? 'unknown',
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ];
    
    $logFile = '../logs/ai_activity.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    file_put_contents($logFile, json_encode($logEntry) . "\n", FILE_APPEND | LOCK_EX);
}

/**
 * Format AI response errors for user display
 * @param string $error
 * @return string
 */
function formatAIError($error) {
    // Common error mappings
    $errorMappings = [
        'timeout' => 'The AI service is taking too long to respond. Please try again.',
        'service unavailable' => 'The AI service is currently unavailable. Please try again later.',
        'invalid file' => 'The uploaded file could not be processed. Please check the file format and try again.',
        'rate limit' => 'Too many requests. Please wait a moment before trying again.',
        'content too large' => 'The content is too large to process. Please try with a smaller file or fewer questions.'
    ];
    
    $lowerError = strtolower($error);
    foreach ($errorMappings as $key => $message) {
        if (strpos($lowerError, $key) !== false) {
            return $message;
        }
    }
    
    // Return sanitized original error if no mapping found
    return 'An error occurred while generating questions: ' . htmlspecialchars($error);
}

/**
 * Get AI service status information
 * @return array
 */
function getAIServiceStatus() {
    $status = [
        'available' => false,
        'version' => 'unknown',
        'capabilities' => [],
        'last_check' => date('Y-m-d H:i:s')
    ];
    
    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, AI_SERVICE_URL . '/health');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($response !== false && $httpCode === 200) {
            $healthData = json_decode($response, true);
            if ($healthData) {
                $status['available'] = true;
                $status['version'] = $healthData['version'] ?? 'unknown';
                $status['capabilities'] = $healthData['capabilities'] ?? [];
            }
        }
    } catch (Exception $e) {
        error_log('AI Service health check failed: ' . $e->getMessage());
    }
    
    return $status;
}
?>
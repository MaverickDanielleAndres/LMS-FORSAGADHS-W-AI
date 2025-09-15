<?php
session_start();
require_once '../config.php';

// Security check
if ($_SESSION['role'] !== 'faculty') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

try {
    // Get input data
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || empty($input['user_message'])) {
        throw new Exception('No message provided');
    }
    
    $userMessage = trim($input['user_message']);
    $task = isset($input['task']) ? $input['task'] : 'chat';
    
    // Input validation
    if (strlen($userMessage) > 2000) {
        throw new Exception('Message too long. Please limit to 2000 characters.');
    }
    
    if (empty($userMessage)) {
        throw new Exception('Message cannot be empty.');
    }
    
    // Rate limiting (simple implementation)
    $facultyId = $_SESSION['fid'];
    $rateLimitKey = "chat_limit_" . $facultyId;
    
    // Check rate limit (allow 20 requests per minute)
    if (!isset($_SESSION[$rateLimitKey])) {
        $_SESSION[$rateLimitKey] = ['count' => 0, 'reset_time' => time() + 60];
    }
    
    if (time() > $_SESSION[$rateLimitKey]['reset_time']) {
        $_SESSION[$rateLimitKey] = ['count' => 0, 'reset_time' => time() + 60];
    }
    
    $_SESSION[$rateLimitKey]['count']++;
    
    if ($_SESSION[$rateLimitKey]['count'] > 20) {
        throw new Exception('Rate limit exceeded. Please wait a moment before sending another message.');
    }
    
    // Prepare API request
    $apiUrl = 'http://localhost:5000/chat';
    $ch = curl_init($apiUrl);
    
    $postData = [
        'user_message' => $userMessage,
        'task' => $task,
        'faculty_id' => $facultyId
    ];
    
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($postData))
        ],
        CURLOPT_TIMEOUT => 60,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    if ($error) {
        throw new Exception("Connection error: " . $error);
    }
    
    if ($httpCode !== 200) {
        $errorMsg = "AI service unavailable (HTTP $httpCode)";
        if ($response) {
            $errorData = json_decode($response, true);
            if ($errorData && isset($errorData['error'])) {
                $errorMsg .= ": " . $errorData['error'];
            }
        }
        throw new Exception($errorMsg);
    }
    
    $responseData = json_decode($response, true);
    
    if (!$responseData) {
        throw new Exception("Invalid response from AI service");
    }
    
    // Validate response
    if (empty($responseData['response'])) {
        throw new Exception("Empty response from AI service");
    }
    
    // Log the interaction (optional - for debugging)
    error_log("AI Chat - Faculty: $facultyId, Message: " . substr($userMessage, 0, 100) . "..., Response Length: " . strlen($responseData['response']));
    
    // Return successful response
    header('Content-Type: application/json');
    echo json_encode([
        'response' => $responseData['response'],
        'timestamp' => date('Y-m-d H:i:s'),
        'message_length' => strlen($responseData['response'])
    ]);
    
} catch (Exception $e) {
    error_log("AI Chat Handler Error: " . $e->getMessage());
    
    // Return user-friendly error message
    $errorResponse = [
        'error' => $e->getMessage(),
        'response' => "I'm sorry, I'm having trouble processing your request right now. Please try again in a moment.",
        'timestamp' => date('Y-m-d H:i:s'),
        'service_status' => 'error'
    ];
    
    // Set appropriate HTTP status
    if (strpos($e->getMessage(), 'Rate limit') !== false) {
        http_response_code(429);
    } elseif (strpos($e->getMessage(), 'Unauthorized') !== false) {
        http_response_code(403);
    } else {
        http_response_code(500);
    }
    
    header('Content-Type: application/json');
    echo json_encode($errorResponse);
}
?>
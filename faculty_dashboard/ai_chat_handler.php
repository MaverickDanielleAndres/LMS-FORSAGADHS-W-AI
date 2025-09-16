<?php
session_start();
header('Content-Type: application/json');

// Security check
if ($_SESSION['role'] !== 'faculty') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

try {
    // Get input data
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data || json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON data');
    }
    
    $userMessage = trim($data['user_message'] ?? '');
    if (empty($userMessage)) {
        throw new Exception('Empty message');
    }
    
    // Prepare API call to Python service
    $apiUrl = 'http://localhost:5000/chat';
    $ch = curl_init($apiUrl);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    $payload = json_encode([
        'user_message' => $userMessage,
        'faculty_id' => $_SESSION['fid'],
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    
    curl_close($ch);
    
    if ($curlError) {
        throw new Exception("AI service unavailable: $curlError");
    }
    
    if ($httpCode !== 200) {
        throw new Exception("AI service error (HTTP $httpCode)");
    }
    
    $responseData = json_decode($response, true);
    if (!$responseData) {
        throw new Exception("Invalid response from AI service");
    }
    
    if (isset($responseData['error'])) {
        throw new Exception($responseData['error']);
    }
    
    echo json_encode([
        'response' => $responseData['response'] ?? 'I apologize, but I encountered an issue processing your request.',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    error_log("AI Chat Handler Error: " . $e->getMessage());
    
    // Provide fallback response
    $fallbackResponses = [
        "I'm currently experiencing technical difficulties. Please try again in a moment.",
        "Sorry, I'm having trouble connecting to the AI service. Please ensure it's running and try again.",
        "I apologize for the inconvenience. Please check your connection and try again."
    ];
    
    echo json_encode([
        'response' => $fallbackResponses[array_rand($fallbackResponses)],
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
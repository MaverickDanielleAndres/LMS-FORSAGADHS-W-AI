<?php
/**
 * AI Integration Test Script
 * Run this to verify your AI service is working properly
 */

require_once("ai_config.php");

header('Content-Type: application/json');

function testAIConnection() {
    $tests = [];
    
    // Test 1: AI Service Health Check
    $tests['ai_service_health'] = [
        'name' => 'AI Service Health Check',
        'status' => 'running',
        'message' => 'Checking AI service availability...'
    ];
    
    $serviceStatus = getAIServiceStatus();
    if ($serviceStatus['available']) {
        $tests['ai_service_health']['status'] = 'passed';
        $tests['ai_service_health']['message'] = 'AI service is available';
        $tests['ai_service_health']['details'] = $serviceStatus;
    } else {
        $tests['ai_service_health']['status'] = 'failed';
        $tests['ai_service_health']['message'] = 'AI service is not available. Make sure Flask app is running on port 5000.';
    }
    
    // Test 2: File Validation
    $tests['file_validation'] = [
        'name' => 'File Validation Functions',
        'status' => 'running',
        'message' => 'Testing file validation...'
    ];
    
    $testValidation = validateFileForAI('test.pdf', 1024 * 1024); // 1MB test file
    if ($testValidation['valid']) {
        $tests['file_validation']['status'] = 'passed';
        $tests['file_validation']['message'] = 'File validation working correctly';
    } else {
        $tests['file_validation']['status'] = 'failed';
        $tests['file_validation']['message'] = 'File validation failed: ' . implode(', ', $testValidation['errors']);
    }
    
    // Test 3: Parameter Validation
    $tests['parameter_validation'] = [
        'name' => 'Parameter Validation',
        'status' => 'running',
        'message' => 'Testing parameter validation...'
    ];
    
    $testParams = [
        'numQuestions' => 15,
        'difficulty' => 'medium',
        'questionType' => 'mcq',
        'subjectContext' => 'Mathematics'
    ];
    
    $validatedParams = validateQuestionParameters($testParams);
    if ($validatedParams['numQuestions'] === 15 && $validatedParams['difficulty'] === 'medium') {
        $tests['parameter_validation']['status'] = 'passed';
        $tests['parameter_validation']['message'] = 'Parameter validation working correctly';
        $tests['parameter_validation']['details'] = $validatedParams;
    } else {
        $tests['parameter_validation']['status'] = 'failed';
        $tests['parameter_validation']['message'] = 'Parameter validation not working as expected';
    }
    
    // Test 4: Database Connection (if available)
    if (file_exists('../config.php')) {
        require_once('../config.php');
        
        $tests['database_connection'] = [
            'name' => 'Database Connection',
            'status' => 'running',
            'message' => 'Testing database connection...'
        ];
        
        if (isset($conn) && $conn) {
            // Test quiz tables
            $requiredTables = ['quizmaster', 'quizparts', 'quizquestions', 'quizquestionoptions'];
            $missingTables = [];
            
            foreach ($requiredTables as $table) {
                $result = $conn->query("SHOW TABLES LIKE '$table'");
                if ($result->num_rows === 0) {
                    $missingTables[] = $table;
                }
            }
            
            if (empty($missingTables)) {
                $tests['database_connection']['status'] = 'passed';
                $tests['database_connection']['message'] = 'Database connection and quiz tables verified';
            } else {
                $tests['database_connection']['status'] = 'failed';
                $tests['database_connection']['message'] = 'Missing tables: ' . implode(', ', $missingTables);
            }
        } else {
            $tests['database_connection']['status'] = 'failed';
            $tests['database_connection']['message'] = 'Database connection failed';
        }
    }
    
    // Test 5: Directory Permissions
    $tests['directory_permissions'] = [
        'name' => 'Directory Permissions',
        'status' => 'running',
        'message' => 'Checking directory permissions...'
    ];
    
    $directories = ['../logs'];
    $permissionIssues = [];
    
    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                $permissionIssues[] = "Cannot create directory: $dir";
            }
        } elseif (!is_writable($dir)) {
            $permissionIssues[] = "Directory not writable: $dir";
        }
    }
    
    if (empty($permissionIssues)) {
        $tests['directory_permissions']['status'] = 'passed';
        $tests['directory_permissions']['message'] = 'Directory permissions are correct';
    } else {
        $tests['directory_permissions']['status'] = 'failed';
        $tests['directory_permissions']['message'] = implode(', ', $permissionIssues);
    }
    
    return $tests;
}

// Run tests
$testResults = testAIConnection();

// Calculate overall status
$overallStatus = 'passed';
$passedCount = 0;
$totalCount = count($testResults);

foreach ($testResults as $test) {
    if ($test['status'] === 'passed') {
        $passedCount++;
    } elseif ($test['status'] === 'failed') {
        $overallStatus = 'failed';
    }
}

$summary = [
    'overall_status' => $overallStatus,
    'passed' => $passedCount,
    'total' => $totalCount,
    'timestamp' => date('Y-m-d H:i:s'),
    'tests' => $testResults
];

echo json_encode($summary, JSON_PRETTY_PRINT);
?>
<?php
session_start();
header("Content-Type: application/json");
require_once("../config.php");

// Check if user is logged in as Faculty
if (!isset($_SESSION['role']) || $_SESSION['role'] != "faculty") {
    echo json_encode(['error' => 'Unauthorized access.']);
    exit();
}

$faculty_id = $_SESSION['fid'];
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['material_id']) || empty($input['material_id'])) {
    echo json_encode(['error' => 'Material ID is required.']);
    exit();
}

$material_id = (int)$input['material_id'];

try {
    // First verify that this faculty has access to this material
    $verify_sql = "SELECT smm.MaterialFile, smm.SubjectUnitName, sm.SubjectName 
                   FROM studymaterialmaster smm
                   INNER JOIN subjectmaster sm ON smm.SubjectCode = sm.SubjectCode
                   WHERE smm.MaterialId = ? AND sm.SubjectFacultyId = ?";
    
    $stmt = mysqli_prepare($conn, $verify_sql);
    if (!$stmt) {
        throw new Exception("Database prepare failed: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "ii", $material_id, $faculty_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Database execution failed: " . mysqli_stmt_error($stmt));
    }
    
    $result = mysqli_stmt_get_result($stmt);
    $material = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    if (!$material) {
        echo json_encode(['error' => 'Material not found or access denied.']);
        exit();
    }
    
    // Construct file path (adjust this path according to your file storage structure)
    $file_path = "../uploads/study_materials/" . $material['MaterialFile'];
    
    // Check if file exists
    if (!file_exists($file_path)) {
        echo json_encode(['error' => 'Material file not found on server.']);
        exit();
    }
    
    // Read file content
    $file_content = file_get_contents($file_path);
    if ($file_content === false) {
        echo json_encode(['error' => 'Failed to read material file.']);
        exit();
    }
    
    // Get file extension for proper handling
    $file_extension = pathinfo($material['MaterialFile'], PATHINFO_EXTENSION);
    
    // For text files, return content directly
    // For other files, return base64 encoded content
    if (strtolower($file_extension) === 'txt') {
        $content = $file_content;
    } else {
        $content = base64_encode($file_content);
    }
    
    echo json_encode([
        'success' => true,
        'content' => $content,
        'filename' => $material['MaterialFile'],
        'material_name' => $material['SubjectUnitName'],
        'subject_name' => $material['SubjectName'],
        'file_extension' => $file_extension,
        'encoding' => (strtolower($file_extension) === 'txt') ? 'utf8' : 'base64'
    ]);
    
} catch (Exception $e) {
    error_log("Fetch material content error: " . $e->getMessage());
    echo json_encode([
        'error' => 'Failed to fetch material content: ' . $e->getMessage()
    ]);
}
?>
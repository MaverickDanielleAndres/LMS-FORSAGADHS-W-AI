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
    $subjectCode = intval($_GET['subject_code'] ?? 0);
    $facultyId = intval($_GET['faculty_id'] ?? 0);
    
    if (!$subjectCode || !$facultyId) {
        throw new Exception('Missing required parameters');
    }
    
    // Verify faculty has access to this subject
    $stmt = $conn->prepare("SELECT COUNT(*) FROM subjectmaster WHERE SubjectCode = ? AND SubjectFacultyId = ?");
    $stmt->bind_param('ii', $subjectCode, $facultyId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->fetch_row()[0] == 0) {
        throw new Exception('Access denied to this subject');
    }
    $stmt->close();
    
    // Get materials for this subject
    $stmt = $conn->prepare("SELECT MaterialId, SubjectUnitName, MaterialFile, MaterialUploadDate 
                           FROM studymaterialmaster 
                           WHERE SubjectCode = ? 
                           ORDER BY SubjectUnitNo ASC, MaterialUploadDate DESC");
    $stmt->bind_param('i', $subjectCode);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $materials = [];
    while ($row = $result->fetch_assoc()) {
        $materials[] = [
            'MaterialId' => $row['MaterialId'],
            'SubjectUnitName' => $row['SubjectUnitName'],
            'MaterialFile' => $row['MaterialFile'],
            'UploadDate' => $row['MaterialUploadDate']
        ];
    }
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'materials' => $materials,
        'count' => count($materials)
    ]);
    
} catch (Exception $e) {
    error_log("Get Materials Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'materials' => []
    ]);
}
?>
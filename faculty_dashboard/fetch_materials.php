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

try {
    // Query to get study materials for subjects taught by this faculty
    $sql = "SELECT 
                smm.MaterialId,
                smm.SubjectUnitName as MaterialName,
                smm.MaterialFile,
                smm.MaterialUploadDate as UploadDate,
                sm.SubjectName,
                sm.SubjectCode,
                CONCAT(smm.SubjectUnitName, ' - ', sm.SubjectName) as FileInfo,
                CASE 
                    WHEN smm.MaterialFile LIKE '%.pdf' THEN 'PDF Document'
                    WHEN smm.MaterialFile LIKE '%.doc%' THEN 'Word Document'
                    WHEN smm.MaterialFile LIKE '%.ppt%' THEN 'PowerPoint Presentation'
                    WHEN smm.MaterialFile LIKE '%.txt' THEN 'Text Document'
                    ELSE 'Document'
                END as FileSize
            FROM studymaterialmaster smm
            INNER JOIN subjectmaster sm ON smm.SubjectCode = sm.SubjectCode
            WHERE sm.SubjectFacultyId = ?
            ORDER BY smm.MaterialUploadDate DESC
            LIMIT 50";
    
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception("Database prepare failed: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "i", $faculty_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Database execution failed: " . mysqli_stmt_error($stmt));
    }
    
    $result = mysqli_stmt_get_result($stmt);
    
    $materials = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $materials[] = [
            'MaterialId' => $row['MaterialId'],
            'MaterialName' => $row['MaterialName'],
            'SubjectName' => $row['SubjectName'],
            'SubjectCode' => $row['SubjectCode'],
            'FileInfo' => $row['FileInfo'],
            'FileSize' => $row['FileSize'],
            'UploadDate' => date('M j, Y', strtotime($row['UploadDate']))
        ];
    }
    
    mysqli_stmt_close($stmt);
    
    echo json_encode([
        'success' => true,
        'materials' => $materials,
        'total' => count($materials)
    ]);
    
} catch (Exception $e) {
    error_log("Fetch materials error: " . $e->getMessage());
    echo json_encode([
        'error' => 'Failed to fetch materials: ' . $e->getMessage()
    ]);
}
?>
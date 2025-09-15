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
    $facultyId = (int)$_SESSION['fid'];
    
    // Enhanced query to get materials with more details
    $sql = "SELECT 
                smm.MaterialId,
                smm.SubjectUnitName as MaterialName,
                smm.SubjectUnitNo,
                smm.MaterialCode,
                smm.MaterialFile,
                smm.MaterialUploadDate,
                sm.SubjectName,
                sm.SubjectCode,
                bm.BranchName,
                CASE 
                    WHEN smm.MaterialFile LIKE '%.pdf' THEN 'PDF'
                    WHEN smm.MaterialFile LIKE '%.doc%' THEN 'DOC'
                    WHEN smm.MaterialFile LIKE '%.ppt%' THEN 'PPT'
                    WHEN smm.MaterialFile LIKE '%.txt' THEN 'TXT'
                    ELSE 'FILE'
                END as FileType
            FROM studymaterialmaster smm
            JOIN subjectmaster sm ON smm.SubjectCode = sm.SubjectCode
            JOIN branchmaster bm ON sm.SubjectBranch = bm.BranchId
            WHERE sm.SubjectFacultyId = ?
            ORDER BY sm.SubjectName, smm.SubjectUnitNo, smm.MaterialUploadDate DESC";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Database prepare error: " . $conn->error);
    }
    
    $stmt->bind_param('i', $facultyId);
    
    if (!$stmt->execute()) {
        throw new Exception("Database execution error: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $materials = [];
    
    while ($row = $result->fetch_assoc()) {
        // Get file size if file exists
        $filePath = "../src/uploads/studymaterial/" . $row['MaterialFile'];
        $fileSize = 'N/A';
        
        if (file_exists($filePath)) {
            $sizeBytes = filesize($filePath);
            if ($sizeBytes < 1024) {
                $fileSize = $sizeBytes . ' B';
            } elseif ($sizeBytes < 1024 * 1024) {
                $fileSize = round($sizeBytes / 1024, 2) . ' KB';
            } else {
                $fileSize = round($sizeBytes / (1024 * 1024), 2) . ' MB';
            }
        }
        
        $materials[] = [
            'MaterialId' => (int)$row['MaterialId'],
            'MaterialName' => $row['MaterialName'] ?: 'Unit ' . $row['SubjectUnitNo'],
            'SubjectUnitNo' => (int)$row['SubjectUnitNo'],
            'MaterialCode' => $row['MaterialCode'],
            'MaterialFile' => $row['MaterialFile'],
            'SubjectName' => $row['SubjectName'],
            'SubjectCode' => $row['SubjectCode'],
            'BranchName' => $row['BranchName'],
            'FileType' => $row['FileType'],
            'FileSize' => $fileSize,
            'UploadDate' => date('M j, Y', strtotime($row['MaterialUploadDate'])),
            'FormattedName' => $row['SubjectName'] . ' - ' . ($row['MaterialName'] ?: 'Unit ' . $row['SubjectUnitNo'])
        ];
    }
    
    $stmt->close();
    
    // Group materials by subject for better organization
    $groupedMaterials = [];
    foreach ($materials as $material) {
        $subjectKey = $material['SubjectCode'];
        if (!isset($groupedMaterials[$subjectKey])) {
            $groupedMaterials[$subjectKey] = [
                'SubjectName' => $material['SubjectName'],
                'BranchName' => $material['BranchName'],
                'Materials' => []
            ];
        }
        $groupedMaterials[$subjectKey]['Materials'][] = $material;
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'materials' => $materials,
        'groupedMaterials' => $groupedMaterials,
        'totalCount' => count($materials)
    ]);
    
} catch (Exception $e) {
    error_log("Fetch Material Error: " . $e->getMessage());
    
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'materials' => []
    ]);
}
?>
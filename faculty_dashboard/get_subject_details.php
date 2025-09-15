<?php
session_start();
require_once("../config.php");

$subject_code = isset($_GET['subject_code']) ? mysqli_real_escape_string($conn, $_GET['subject_code']) : '';

if (empty($subject_code)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing subject code.']);
    exit();
}

$sql = "SELECT sm.SubjectCode, sm.SubjectName, sm.SubjectSemester, bm.BranchId, bm.BranchName FROM subjectmaster sm 
        INNER JOIN branchmaster bm ON sm.SubjectBranch = bm.BranchId 
        WHERE sm.SubjectCode = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $subject_code);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    $subject = mysqli_fetch_assoc($result);
    echo json_encode(['success' => true, 'subject' => $subject]);
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Subject not found.']);
}
?>
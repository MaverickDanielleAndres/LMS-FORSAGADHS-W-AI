<?php
session_start();
require_once("../config.php");

// Enable error reporting during development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure user is faculty
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "faculty") {
    header("Location: ../index.php");
    exit();
}

// Check if matid is passed
if (!isset($_GET['matid'])) {
    header("Location: ../index.php");
    exit();
}

$matid = mysqli_real_escape_string($conn, $_GET['matid']);

// Get file name and SubjectId
$getFileQuery = "
    SELECT 
        MaterialFile, 
        (SELECT SubjectId FROM subjectmaster WHERE SubjectCode = sm.SubjectCode LIMIT 1) AS SubjectId
    FROM studymaterialmaster sm
    WHERE MaterialId = '$matid'";

$fileRes = mysqli_query($conn, $getFileQuery);

if (!$fileRes || mysqli_num_rows($fileRes) === 0) {
    header("Location: ../index.php?deleted=0");
    exit();
}

$fileRow = mysqli_fetch_assoc($fileRes);
$filePath = "../src/uploads/studymaterial/" . $fileRow['MaterialFile'];
$subjectId = $fileRow['SubjectId'];

// Delete DB record
$deleteQuery = "DELETE FROM studymaterialmaster WHERE MaterialId = '$matid'";
if (mysqli_query($conn, $deleteQuery)) {
    // Delete file if it exists
    if (file_exists($filePath)) {
        unlink($filePath);
    }
    header("Location: subject_profile.php?subid=$subjectId&deleted=1");
    exit();
} else {
    header("Location: subject_profile.php?subid=$subjectId&deleted=0");
    exit();
}

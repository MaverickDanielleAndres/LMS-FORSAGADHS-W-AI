<?php
session_start();
if ($_SESSION['role'] != "faculty") {
    header("Location: ../index.php");
    exit;
}

include_once("../config.php");

if ($_POST) {
    $score = mysqli_real_escape_string($conn, $_POST['score']);
    $assignmentId = mysqli_real_escape_string($conn, $_POST['assignmentId']);
    $uploaderId = mysqli_real_escape_string($conn, $_POST['uploaderId']);

    // Validate score range
    if ($score < 0 || $score > 100) {
        echo "error";
        exit;
    }

    // Update both studscore and status in studentassignment table
    $updateQuery = "UPDATE studentactivity 
                    SET studscore = '$score', SActivityStatus = '3' 
                    WHERE SActivityUploaderId = '$uploaderId' AND ActivityId = '$assignmentId'";

    $result = mysqli_query($conn, $updateQuery);

    if ($result && mysqli_affected_rows($conn) > 0) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "error";
}

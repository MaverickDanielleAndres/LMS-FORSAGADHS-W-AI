<?php
session_start();
if ($_SESSION['role'] != "faculty") {
    header("Location: ../index.php");
    exit;
}

include_once("../config.php");

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $assignmentId = isset($_GET['asid']) ? mysqli_real_escape_string($conn, $_GET['asid']) : '';
    $uploaderId = isset($_GET['upid']) ? mysqli_real_escape_string($conn, $_GET['upid']) : '';

    if (!empty($assignmentId) && !empty($uploaderId)) {
        $updateQuery = "UPDATE studentassignment 
                        SET studscore = 0, SAssignmentStatus = 2 
                        WHERE SAssignmentUploaderId = '$uploaderId' 
                        AND AssignmentId = '$assignmentId'";

        $result = mysqli_query($conn, $updateQuery);

        if ($result && mysqli_affected_rows($conn) > 0) {
            echo "<script>alert('Score updated and assignment rejected successfully.'); 
                  window.location.href='assignment_view.php?updateid=$assignmentId';</script>";
        } else {
            echo "<script>alert('No changes made or update failed.'); 
                  window.location.href='assignment_view.php?updateid=$assignmentId';</script>";
        }
    } else {
        echo "<script>alert('Missing required data.'); 
              window.location.href='assignment_view.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request method.'); 
          window.location.href='assignment_view.php';</script>";
}
?>

<?php
session_start();
if ($_SESSION['role'] != "faculty") {
    header("Location: ../index.php");
    exit;
}

include_once("../config.php");

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $activityId = isset($_GET['asid']) ? mysqli_real_escape_string($conn, $_GET['asid']) : '';
    $uploaderId = isset($_GET['upid']) ? mysqli_real_escape_string($conn, $_GET['upid']) : '';

    if (!empty($assignmentId) && !empty($uploaderId)) {
        $updateQuery = "UPDATE studentactivity 
                        SET studscore = 0, SActivityStatus = 2 
                        WHERE SActivityUploaderId = '$uploaderId' 
                        AND ActivityId = '$activityId'";

        $result = mysqli_query($conn, $updateQuery);

        if ($result && mysqli_affected_rows($conn) > 0) {
            echo "<script>alert('Score updated and activity rejected successfully.'); 
                  window.location.href='activity_view.php?updateid=$activityId';</script>";
        } else {
            echo "<script>alert('No changes made or update failed.'); 
                  window.location.href='activity_view.php?updateid=$activityId';</script>";
        }
    } else {
        echo "<script>alert('Missing required data.'); 
              window.location.href='activity_view.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request method.'); 
          window.location.href='activity_view.php';</script>";
}
?>

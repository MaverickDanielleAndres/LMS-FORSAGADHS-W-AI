<?php
include_once("../config.php");

if (isset($_GET['branchCode'])) {
    $branchCode = mysqli_real_escape_string($conn, $_GET['branchCode']);
    
    $facSel = "SELECT FacultyId, FacultyFirstName, FacultyLastName 
               FROM facultymaster 
               WHERE FacultyBranchCode = '$branchCode'";
    $facResult = mysqli_query($conn, $facSel);
    
    $faculty = array();
    while ($row = mysqli_fetch_assoc($facResult)) {
        $faculty[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode($faculty);
}
?>
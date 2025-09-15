<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc." />
    <!-- Favicon -->
    <link rel="shortcut icon" href="../assets/favicon/favicon.ico" type="image/x-icon" />
    
    <style>
        .main-content {
				background-color: rgb(243, 243, 243);
				min-height: 100vh;
			}

			.back-btn {
				padding: .5rem 1rem;
				background-color: #27548A;
				border: none;
				border-radius: 3px;
				font-weight: 400;
				color: white;
				text-decoration: none;
			}
			.back-btn:hover {
				padding: .5rem 1rem;
				background-color: #0d3b72ff;
				border: none;
				border-radius: 3px;
				font-weight: 400;
				color: white;
				text-decoration: none;
			}

			.card {
				z-index: 10;
				box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.2);
				/* Bigger, deeper shadow */
				border-radius: 0.75rem;
				background-color: white;
			}
    </style>
</head>
<body>
<?php 
session_start();
if ($_SESSION['role'] != "student") {
    header("Location: ../index.php");
} else {
	
	$f_tmp_name = $_FILES['upload']['tmp_name'];
	$f_size = $_FILES['upload']['size'];
	$f_error = $_FILES['upload']['error'];

	$uploadsubmit = $_POST['id'];
	$submite = 1;

	#upload to database
	$filename = $enroll . "_" . $ttid . ".pdf";
	$date = gmdate("Y-m-d");
    $assid = $_GET['assid'];
	$sql = "INSERT INTO studentassignment(SAssignmentUploaderId, AssignmentId, SAssignmentFile, SAssignmentUploadDate, SAssignmentStatus)
	 VALUES ('$stuid','$ttid','$filename','$date','$submite')";
	// echo $sql;
	$result = mysqli_query($conn, $sql);
	if ($result) {
		echo "<script>alert('Assignment Submitted Successfully .. !');</script>";
		if ($f_error === 0) {
			if ($f_size <= 10000000) {
				move_uploaded_file($f_tmp_name, "../src/uploads/studentAssignment/" . $filename); // Moving Uploaded File to Server ... to uploades folder by file name f_name ... 
			} else {
				echo "<script>alert(File size is to big .. !);</script>";
			}
		} else {
			echo "Something went wrong .. !";
		}
		echo "<script>window.open('assignment_list.php','_self')</script>";
	} else {
		echo "<script>alert('Something went wrong .. !');</script>";
		echo "<script>window.open('assignment_list.php','_self')</script>";
	}
}
?>
</body>

</html>
<?php
error_reporting(E_ALL ^ E_WARNING);
session_start();
if ($_SESSION['role'] != "student") {
	header("Location: ../index.php");
} else { ?>
	<!DOCTYPE html>
	<html lang="en">

	<head>
		<?php include_once "../head.php"; ?>
		<script>
			function action() {
				<?php
				if (!isset($_GET['action'])) { ?>
					document.getElementById('btnsub').style.display = 'none';
				<?php
				}
				?>
			}
		</script>
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

			.card {
				z-index: 10;
				box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.2);
				/* Bigger, deeper shadow */
				border-radius: 0.75rem;
				background-color: white;
			}
		</style>
	</head>

	<body onload="action();">
		<!-- NAVIGATION -->
		<?php
		$nav_role = "Assignments";
		include_once 'nav.php'; ?>
		<!-- MAIN CONTENT -->
		<div class="main-content">
			<div class="container-fluid">
				<div class="row justify-content-center">
					<div class="col-12	">
						<div class="header">
							<div class="header-body">
								<div class="row align-items-end">
									<!-- Left column for title -->
									<div class="col">
										<!-- Title -->
										<h3 class="header-title">
											View Assignment
										</h3>
									</div>

									<!-- Right column for Back button -->
									<div class="col-auto text-end">
										<h5 class="header-pretitle">
											<button class="back-btn" onclick="history.back()">
												<i class="fe uil-angle-double-left"></i> Back
											</button>
										</h5>
									</div>
								</div>
							</div>
							<!-- / .row -->
						</div>
					</div>
					<!-- Card -->
					<div class="card">
						<div class="card-body">
							<?php
							include_once "../config.php";
							$ttid = $_GET['updateid'];
							$_SESSION["userrole"] = "Student";
							if (isset($ttid)) {
								$sql = "SELECT * FROM assignmentmaster WHERE AssignmentId = '$ttid'";
								$result = mysqli_query($conn, $sql);
								$row = mysqli_fetch_assoc($result);
								$u = $_SESSION['id'];
								$sql1 = "SELECT * FROM studentmaster WHERE StudentUserName = '$u'";
								$srow = mysqli_fetch_assoc(mysqli_query($conn, $sql1));
								$stuid =  $srow['StudentId'];
								$enroll =  $srow['StudentEnrollmentNo'];
								$sql2 = "SELECT * FROM studentassignment WHERE AssignmentId = '$ttid' AND SAssignmentUploaderId = '$stuid'";
								$stresult = mysqli_query($conn, $sql2);
								$strow = mysqli_fetch_assoc($stresult);
							?>
								<!-- CONTENT -->
								<div class="container-fluid">
									<div class="row">
										<div class="col-12">
											<!-- Files -->
											<!-- <div class="card" data-list='{"valueNames": ["name"]}'>
													<div class="card-body"> -->
											<h1 class="header-title">
												<?php echo $row['AssignmentTitle']; ?>
											</h1>
											<br>
											<div class="input-group">

												<span class="input-group-text col-1 ">Subject</span>
												<input type="text" value="<?php echo $row['AssignmentSubject']; ?>" aria-label="Last name" class="form-control disable" disabled>
												<span class="input-group-text col-2 ">Upload date</span>
												<input type="text" value="<?php echo $row['AssignmentUploaddate']; ?>" aria-label="First name" class="form-control" disabled>
												<span class="input-group-text col-2 ">Submission Date</span>
												<input type="text" value="<?php echo $row['AssignmentSubmissionDate']; ?>" aria-label="Last name" class="form-control disable" disabled>

											</div>
											<br>
											<div class="input-group">
												<span class="input-group-text col-1 ">Total</span>
												<input type="text" value="<?php echo $row['totalscore']; ?>" aria-label="Last name" class="form-control disable" disabled>
												<span class="input-group-text col-2 ">Score</span>
												<input type="text" value="<?php echo isset($strow['studscore']) ? $strow['studscore'] : 'Not yet scored'; ?>" class="form-control" disabled>
												<span class="input-group-text col-2 ">Description</span>
												<textarea aria-label="First name" class="form-control" disabled><?php echo $row['AssignmentDesc']; ?></textarea>
											</div>
										</div>
										<!-- </div>
											</div> -->
									</div>
									<div class="d-flex justify mt-5">
										<!-- Button -->
										<a href="../src/uploads/assignments/<?php echo $row['AssignmentFile']; ?>" download="<?php echo $row['AssignmentFile']; ?>" class="btn btn-primary" name="Download">
											Download
										</a>
										<a class="btn btn-primary ml-5" id="btnsub">
											Submit
										</a>
									</div>
									<form id="replyform" class="d-none" method="POST" enctype="multipart/form-data">
										<input type="hidden" name="id" value="<?php echo $stuid; ?>">
										<hr class="md-5">
										<h2 class="header-title my-5">
											Assignment Submission :
										</h2>
										<div class="input-group">
											<div class="row justify-content-between align-items-center">
												<div class="col">
													<div class="row align-items-center">
														<div class="col ml-n2">
															<!-- Heading -->
															<h4 class="mb-1">
																Assignment Submission File
															</h4>
															<!-- Text -->
															<small class="text-muted">
																Only allowed PDF less than 10MB
															</small>
														</div>
													</div>
													<!-- / .row -->
												</div>
												<div class="col-auto">
													<!-- Button -->
													<input type="file" name="gujmaterial" id="file1" onchange="showPreview(event);" class="btn btn-sm" accept="application/pdf" required>
												</div>
											</div>
											<hr class="mt-4 mb-5">
										</div>
										<div class="d-flex justify mt-5">
											<input type="submit" class="btn btn-primary" value="Submit Assignment" name="sub">
										</div>
									</form>
								</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		</div>


		<?php include("context.php"); ?>
		<!-- / .main-content -->
	<?php } ?>
	<!-- JAVASCRIPT -->
	<!-- Map JS -->
	<script src='https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.js'></script>
	<!-- Vendor JS -->
	<script src="../assets/js/vendor.bundle.js"></script>
	<!-- Theme JS -->
	<script src="../assets/js/theme.bundle.js"></script>
	<script>
		const btnreply = document.getElementById('btnsub');
		const replyform = document.getElementById('replyform');

		btnreply.onclick = () => {
			btnreply.classList.add('d-none');
			replyform.classList.remove('d-none');
		}
	</script>
	<script>
		function showPreview(event) {
			var file = document.getElementById('file1');
			if (file.files.length > 0) {
				// RUN A LOOP TO CHECK EACH SELECTED FILE.
				for (var i = 0; i <= file.files.length - 1; i++) {
					var fsize = file.files.item(i).size; // THE SIZE OF THE FILE.	
				}
				if (fsize >= 10000000) {
					alert("Only allowed less then 10MB.. !");
					file.value = '';
				}
			}
		}
	</script>
	</body>

	</html>
<?php }
if (isset($_POST['sub'])) {

	$f_tmp_name = $_FILES['gujmaterial']['tmp_name'];
	$f_size = $_FILES['gujmaterial']['size'];
	$f_error = $_FILES['gujmaterial']['error'];


	$uploadsubmit = $_POST['id'];
	$submite = 1;

	#upload to database
	$filename = $enroll . "_" . $ttid . ".pdf";
	$date = gmdate("Y-m-d");

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
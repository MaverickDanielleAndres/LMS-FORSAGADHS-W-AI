<?php

//this is the student_dashboard folder index.php

error_reporting(E_ALL ^ E_WARNING);
session_start();
if ($_SESSION['role'] != "student") {
	header("Location: ../index.php");
	exit();
} else {
	require_once("../config.php");
	$_SESSION["userrole"] = "Student";
	$cred = explode("_", $_SESSION["cred"]);

	// Function to verify password
	function verifyPassword($inputPassword, $storedPassword)
	{
		if (empty($storedPassword)) {
			return false;
		}
		if (password_verify($inputPassword, $storedPassword)) {
			return true;
		}
		return $inputPassword === $storedPassword;
	}

	// Query to get student data
	$qur = "SELECT *,BranchName FROM studentmaster INNER JOIN branchmaster ON studentmaster.StudentBranchCode = branchmaster.BranchCode WHERE StudentUserName='$cred[0]'";
	$res = mysqli_query($conn, $qur);
	$row = mysqli_fetch_assoc($res);

	// Check if student data exists
	if (!$row) {
		// Debug: Log the issue
		error_log("Student data not found for credentials: " . $cred[0]);
		header("Location: ../index.php");
		exit();
	}

	// Verify password matches
	if (!verifyPassword($cred[1], $row['StudentPassword'])) {
		header("Location: ../index.php");
		exit();
	}

	// Set session variables
	$_SESSION["userid"] = $row["StudentId"];
	$_SESSION["bcode"] = $row["StudentBranchCode"];

	// Branch
	$uqur = "SELECT * FROM updatemaster";
	$ures = mysqli_query($conn, $uqur);
	$bid = $row["BranchId"];

	//Assignment
	$aqur = "SELECT * FROM assignmentmaster WHERE AssignmentBranch = '$bid'";
	$ares = mysqli_query($conn, $aqur);
	$arow = mysqli_fetch_assoc($ares);
	$acrow = mysqli_num_rows($ares);

	$secsql = "SELECT SectionNumber FROM sectionmaster WHERE SectionId = ?";

	// Prepare the statement
	$stmt = mysqli_prepare($conn, $secsql);

	// Bind the parameter
	mysqli_stmt_bind_param($stmt, "i", $row['StudentSection']);

	// Execute the query
	mysqli_stmt_execute($stmt);

	// Bind the result
	mysqli_stmt_bind_result($stmt, $sectionNumber);

	// Fetch the result
	mysqli_stmt_fetch($stmt);

	// Close the statement
	mysqli_stmt_close($stmt);
?>
	<!DOCTYPE html>
	<html lang="en">

	<head>
		<?php require_once('../head.php'); ?>
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
		<!-- NAVIGATION -->
		<?php
		$nav_role = "Dashboard";
		require_once('nav.php'); ?>
		<!-- MAIN CONTENT -->
		<div class="main-content">
			<!-- HEADER -->
			<div class="header">
				<div class="container-fluid">
					<!-- Body -->
					<div class="header-body">

						<div class="row align-items-end">
							<div class="col">
								<!-- Title -->
								<h1 class="header-title">
									Student Dashboard
								</h1>
							</div>
							<div class="col-auto text-end">
								<h5 class="header-pretitle">
									<button class="back-btn btn-outline" data-toggle="modal" data-target="#logoutModal">
										Logout
									</button>
								</h5>
							</div>
						</div>
						<!-- / .row -->
					</div>
					<!-- / .header-body -->
				</div>
			</div>
			<!-- / .header -->
			<br><br>
			<div class="container-fluid">
				<div class="page-header min-height-100 border-radius-xl mt-4">
				</div>
				<div class="card card-body blur shadow-blur mx-1 mt-n6 overflow-hidden">
					<div class="row gx-4">
						<div class="col-auto">
							<div class="avatar avatar-xxl position-relative">
								<img src="../src/uploads/stuprofile/<?php echo $row['StudentProfilePic'] . "?t"; ?>" style="border-radius: 10px;" class="w-100 h-100 border-radius-lg shadow-sm">
							</div>
						</div>
						<div class="col-auto my-auto">
							<div class="h-100">
								<h1 class="mb-0 font-weight-bold text-sm">
									<?php echo $row['StudentFirstName'] . " " . $row['StudentLastName']; ?>
								</h1>
								<p class="mb-0 font-weight-bold text-sm">
									<?php echo $row['StudentEnrollmentNo']; ?>
								</p>
							</div>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-sm-6">
							<div class="card  border-1">
								<div class="card-body">
									<div class="list-group list-group-flush my-n3">
										<div class="list-group-item">
											<div class="row align-items-center">
												<div class="col">
													<h5 class="mb-0">
														Roll No
													</h5>
												</div>
												<div class="col-auto">
													<h5 class="text-muted mb-0">
														<?php echo $row['StudentRollNo']; ?>
													</h5>
												</div>
											</div>
										</div>
										<div class="list-group-item">
											<div class="row align-items-center">
												<div class="col">
													<h5 class="mb-0">
														Ongoing Quarter
													</h5>
												</div>
												<div class="col-auto">
													<h5 class="text-muted mb-0">
														<?php echo $row['StudentSemester']; ?>
													</h5>
												</div>
											</div>
										</div>
										<div class="list-group-item">
											<div class="row align-items-center">
												<div class="col">
													<h5 class="mb-0">
														Grade Level
													</h5>
												</div>
												<div class="col-auto">
													<small class="text-muted">
														<?php echo $row['BranchName']; ?>
													</small>
												</div>
											</div>
										</div>
										<div class="list-group-item">
											<div class="row align-items-center">
												<div class="col">
													<h5 class="mb-0">
														Section
													</h5>
												</div>
												<div class="col-auto">
													<small class="text-muted">
														<?php echo $sectionNumber; ?>
													</small>
												</div>
											</div>
										</div>

									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="card  border-1">
								<div class="card-body">
									<div class="list-group list-group-flush my-n3">
										<div class="list-group-item">
											<div class="row align-items-center">
												<div class="col">
													<h5 class="mb-0">
														Primary Contact Number
													</h5>
												</div>
												<div class="col-auto">
													<h5 class="text-muted mb-0">
														<?php echo $row['StudentContactNo']; ?>
													</h5>
												</div>
											</div>
										</div>
										<div class="list-group-item">
											<div class="row align-items-center">
												<div class="col">
													<h5 class="mb-0">
														Primary E-mail Id
													</h5>
												</div>
												<div class="col-auto">
													<h5 class="text-muted mb-0">
														<?php echo $row['StudentEmail']; ?>
													</h5>
												</div>
											</div>
										</div>
										<div class="list-group-item">
											<div class="row align-items-center">
												<div class="col">
													<h5 class="mb-0">
														Date Of Birth
													</h5>
												</div>
												<div class="col-auto">
													<small class="text-muted">
														<?php echo $row['StudentDOB']; ?>
													</small>
												</div>
											</div>
										</div>
										<div class="list-group-item">
											<div class="row align-items-center">
												<div class="col">
													<h5 class="mb-0">
														Parent Contact Number
													</h5>
												</div>
												<div class="col-auto">
													<small class="text-muted">
														<?php echo $row['ParentContactNo']; ?>
													</small>
												</div>
											</div>
										</div>

									</div>
								</div>
							</div>
						</div>
					</div>
				</div>


				
<!-- LOGOUT CONFIRMATION MODAL -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content border-0 shadow-lg">
			<div class="modal-header bg-warning text-dark">
				<h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body text-dark">
				Are you sure you want to logout?
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<a href="logout.php" class="btn btn-danger">Logout</a>
			</div>
		</div>
	</div>
</div>


				<script src='https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.js'></script>
				<script src="../assets/js/vendor.bundle.js"></script>
				<!-- Theme JS -->
				<script src="../assets/js/theme.bundle.js"></script>
	</body>

	</html>
<?php } ?>
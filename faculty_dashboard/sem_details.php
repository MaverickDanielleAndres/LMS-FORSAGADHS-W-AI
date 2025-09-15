<?php
error_reporting(E_ALL ^ E_WARNING);
session_start();
if ($_SESSION['role'] != "faculty") {
	header("Location: ../index.php");
} else {
?>
	<!DOCTYPE html>
	<html lang="en">

	<head>
		<?php include_once("../head.php"); ?>
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
				background-color: #0d3b72ff;
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
		<?php $nav_role = "Branch"; ?>
		<!-- NAVIGATION -->
		<?php include_once("nav.php"); ?>
		<!-- MAIN CONTENT -->
		<div class="main-content">
			<div class="header">
				<!-- HEADER -->
				<div class="header">
					<div class="container-fluid">
						<!-- Body -->
						<div class="header-body">
							<div class="row align-items-end">
								<div class="col">
									
									<h6 class="header-pretitle">
										Branch
									</h6>
									<!-- Title -->
									<h1 class="header-title">
										Profile
									</h1>
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
							<!-- / .row -->
						</div>
						<!-- / .header-body -->
					</div>
				</div>
				<!-- / .header -->
				<?php
				include_once("../config.php");
				$xbrid = $_GET['brid'];
				$xbrid = mysqli_real_escape_string($conn, $xbrid);
				$semid = $_GET['semid'];
				$semid = mysqli_real_escape_string($conn, $semid);
				$_SESSION["userrole"] = "Faculty";
				if (isset($xbrid)) {
					$sql = "SELECT * FROM branchmaster WHERE BranchCode = '$xbrid'";
					$result = mysqli_query($conn, $sql);
					$row = mysqli_fetch_assoc($result);
				?>
					<br><br>
					<div class="container-fluid">
						<!-- Body -->
						<div class="header-body mt-n5 mt-md-n6">
							<div class="row align-items-center">
								<div class="col mb-3 ml-n3 ml-md-n2">
									<h1 class="header-title">
										<?php echo $row['BranchName']; ?>
									</h1>
									<h5 class="header-pretitle mt-2">
										<?php echo $row['BranchCode']; ?>
									</h5>
								</div>
							</div>
							<hr class="navbar-divider my-4">
							<!-- / .row -->
							<div class="row align-items-center">
								<div class="col col-md-10">
									<!-- Nav -->
									<ul class="nav nav-tabs nav-overflow header-tabs">
										<?php
										$a = 1;
										while ($a <= $row['BranchSemesters']) { ?>
											<li class="nav-item">
												<a href="sem_details.php?semid=<?php echo $row['BranchCode'] . "_" . $a; ?>&brid=<?php echo $row['BranchCode']; ?>" class="nav-link h3 <?php if ($_GET['semid'] == $row['BranchCode'] . "_" . $a) {
																																															echo "active";
																																														} ?>">
													Quarter <?php echo $a; ?>
												</a>
											</li>
										<?php $a++;
										}
										?>
									</ul>
								</div>
							</div>
						</div>
					</div>
			</div>
			<!-- CONTENT -->
			<div class="container-fluid">
				<div class="row">
					<?php
					// Extract semester number from semid (e.g., "001_1" -> 1)
					$semid_parts = explode('_', $_GET['semid']);
					$current_semester = isset($semid_parts[1]) ? (int)$semid_parts[1] : 1;

					// Get branch ID from branch code
					$branch_sql = "SELECT BranchId FROM branchmaster WHERE BranchCode = '$xbrid'";
					$branch_result = mysqli_query($conn, $branch_sql);
					$branch_row = mysqli_fetch_assoc($branch_result);
					$branch_id = $branch_row['BranchId'];

					// Fixed query: Filter by SubjectBranch and SubjectSemester instead of SemCode
					$subsql = "SELECT * FROM subjectmaster 
							   INNER JOIN facultymaster ON subjectmaster.SubjectFacultyId=facultymaster.FacultyId 
							   WHERE subjectmaster.SubjectBranch = '$branch_id' 
							   AND subjectmaster.SubjectSemester = '$current_semester'";

					$subresult = mysqli_query($conn, $subsql);
					$sac = 1;
					if (mysqli_num_rows($subresult) > 0) {
						while ($roww = mysqli_fetch_assoc($subresult)) { ?>
							<div class="col-12 col-md-4 mb-md-5">
								<div class="card-group">
									<div class="card">
										<img src="../src/uploads/subprofile/<?php echo $roww['SubjectPic']; ?>" class="card-img-top img-fluid" alt="...">
										<div class="card-body">
											<h5 class="card-title"><?php echo $roww['SubjectName']; ?></h5>
											<p class="card-text"><?php echo $roww['SubjectCode']; ?></p>
											<p class="card-text"><?php echo $roww['FacultyFirstName'] . " " . $roww['FacultyLastName']; ?></p>
											<a href="subject_profile.php?subid=<?php echo $roww['SubjectId']; ?>" class="btn btn-sm btn-primary">View</a>
										</div>
									</div>
								</div>
							</div>
						<?php
							$sac++;
						}
					} else { ?>
						<div class="col-12">
							<h1 class="card header-title m-5 p-5">No Subjects Added</h1>
						</div>
					<?php
					}
					?>
				</div>
			</div>
			<?php
				} else {
					$er = $_GET['brid'];
					$er = mysqli_real_escape_string($conn, $er);
					$qur = "SELECT * FROM branchmaster WHERE BranchCode = '$er';";
					$res = mysqli_query($conn, $qur);
					$row = mysqli_fetch_assoc($res);
					if (isset($row)) { ?>
				<div class="container-fluid">
					<hr class="navbar-divider my-4">
					<div class="card">
						<div class="card-body">
							<div class="row align-items-center">
								<div class="col ml-n2">
									<h4 class="mb-1">
										<a href="branch_profile.php"><?php echo $row['BranchName']; ?></a>
									</h4>
									<p class="small mb-1">
										<?php echo $row['BranchCode']; ?>
									</p>
								</div>
								<div class="col-auto">
									<a href="branch_profile.php?brid=<?php echo $row['BranchId']; ?>" class="btn btn-m btn-primary d-none d-md-inline-block">
										View
									</a>
								</div>
							</div>
							<!-- / .row -->
						</div>
						<!-- / .card-body -->
					</div>
				</div>
		<?php
					}
				}
		?>
		</div>
		<?php include("context.php"); ?>
		<!-- / .main-content -->
		<!-- JAVASCRIPT -->
		<!-- Map JS -->
		<script src='https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.js'></script>
		<!-- Vendor JS -->
		<script src="../assets/js/vendor.bundle.js"></script>
		<!-- Theme JS -->
		<script src="../assets/js/theme.bundle.js"></script>
	</body>

	</html>
<?php } ?>
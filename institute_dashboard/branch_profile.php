<?php
error_reporting(E_ALL ^ E_WARNING);
session_start();
if ($_SESSION['role'] != "Texas") {
	header("Location: ../index.php");
} else {
?>
	<!DOCTYPE html>
	<html lang="en">

	<head>
		<?php include_once("../head.php"); ?>
		<style>
			body {
				background: linear-gradient(to top,
						rgba(194, 171, 117, 0.8),
						/* Main shade with less opacity */
						rgba(194, 171, 117, 0.4)
						/* Secondary shade with less opacity */
					),
					url('rhsopacity.png') center/cover no-repeat;
				background-size: cover;
				background-attachment: fixed;
			}



			.header-body {
				border-bottom: 2px solid white;
			}

			.header-pretitle {
				color: #558ce4;
			}

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
		<!-- NAVIGATION -->
		<?php
		$nav_role = "Branch";
		include_once("../nav.php"); ?>
		<!-- MAIN CONTENT -->
		<div class="main-content">
			<div class="header">
				<!-- HEADER -->
				<div class="header">
					<div class="container-fluid">
						<!-- Body -->
						<div class="header-body">
							<div class="row align-items-end">
								<!-- Left: Titles -->
								<div class="col">
									<h6 class="header-pretitle">
										Grade
									</h6>
									<h1 class="header-title">
										Profile
									</h1>
								</div>

								<!-- Right: Back Button -->
								<div class="col-auto">
									<button class="back-btn btn btn-outline-primary" onclick="history.back()">
										<i class="fe uil-angle-double-left"></i> Back
									</button>
								</div>
							</div>
							<!-- / .row -->
						</div>

						<!-- / .header-body -->
					</div>
				</div>
				<!-- / .header -->
				<?php
				require_once("../config.php");
				$studentenr = $_GET['brid'];
				$studentenr = mysqli_real_escape_string($conn, $studentenr);
				$_SESSION["userrole"] = "Institute";
				if (isset($studentenr)) {
					$sql = "SELECT BranchName, BranchCode, BranchSemesters, StudentId FROM branchmaster INNER JOIN studentmaster ON branchmaster.BranchCode = studentmaster.StudentBranchCode WHERE BranchId = '$studentenr'";
					$ssql = "SELECT FacultyId FROM branchmaster INNER JOIN facultymaster ON branchmaster.BranchCode = facultymaster.FacultyBranchCode WHERE BranchId = '$studentenr'";
					$tsql = "SELECT * 
						FROM sectionmaster 
						WHERE SectionBranch = (SELECT BranchName 
												FROM branchmaster 
												WHERE BranchId = '$studentenr')";
					$result = mysqli_query($conn, $sql);
					$reesult = mysqli_query($conn, $ssql);
					$tresult = mysqli_query($conn, $tsql);
					$row = mysqli_fetch_assoc($result);
					$c = isset($result) ? mysqli_num_rows($result) : 0;
					$cc = isset($reesult) ? mysqli_num_rows($reesult) : 0;
					$sections = isset($tresult) ? mysqli_num_rows($tresult) : 0;

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
								<div class="col-12 col-md-auto mt-2 mt-md-0 mb-md-3">
									<!-- Button -->
									<a href="edit_branch.php?brid=<?php echo $studentenr; ?>" class="btn btn-warning d-block d-md-inline-block btn-md">
										Edit Details
									</a>
								</div>
							</div>
							<hr class="navbar-divider my-4">
							<!-- details -->
							<div class="col mb-3 ml-n3 ml-md-n2">
								<h3 class="header-title">
									Grade Details
								</h3>
							</div>
							<div class="col-8 col-md-8">
								<div style="background: white; border-radius: 8px;">
									<ul class="list-group list-group-horizontal flex-wrap col-12">
										<li class="list-group-item flex-fill text-center col-6">Total Students Enrolled</li>
										<li class="list-group-item flex-fill text-center col-6"><?php echo $c; ?></li>
									</ul>
									<ul class="list-group list-group-horizontal flex-wrap col-12">
										<li class="list-group-item flex-fill text-center col-6">No of Faculties</li>
										<li class="list-group-item flex-fill text-center col-6"><?php echo $cc; ?></li>
									</ul>
									<ul class="list-group list-group-horizontal flex-wrap col-12">
										<li class="list-group-item flex-fill text-center col-6">No of Sections</li>
										<li class="list-group-item flex-fill text-center col-6"><?php echo $sections; ?></li>
									</ul>
								</div>
							</div>

							<!--
							<hr class="navbar-divider my-4">
							<div class="col mb-3 ml-n3 ml-md-n2">
								<h3 class="header-title">
									Quarter Details
								</h3>
							</div>
							<div class="row align-items-center">
								<div class="col">

									<ul class="nav nav-tabs nav-overflow header-tabs">
										<?php
										$a = 1;
										while ($a <= $row['BranchSemesters']) { ?>
											<li class="nav-item" <?php echo $a ? "active" : ""; ?>>
												<a href="sem_details.php?semid=<?php echo $row['BranchCode'] . "_" . $a; ?>&brid=<?php echo $row['BranchCode']; ?>" class="nav-link h3">
													Quarter <?php echo $a; ?>
												</a>
											</li>
										<?php $a++;
										}
										?>
									</ul>
								</div>
							</div>
							-->
						</div>
						<!-- / .header-body -->
					</div>
			</div>
		<?php
				} else { ?>
			<div class="container-fluid">
				<form class="mb-4" method="post">
					<div class="col">
						<div class="row">
							<div class="col-md-4">
								<div class="input-group input-group-merge input-group-reverse">
									<input class="form-control list-search" type="text" name="enr" placeholder="Enter Branch Code">
									<div class="input-group-text">
										<span class="fe fe-search"></span>
									</div>
								</div>
							</div>
							<div class="col-md-2">
								<div class="col-auto">
									<!-- Button -->
									<button class="btn btn-primary" type="submit" name="ser" value="2">
										Search
									</button>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
			<?php
					if (isset($_POST['ser'])) {
						$er = $_POST['enr'];
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
				}
		?>
		</div>

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
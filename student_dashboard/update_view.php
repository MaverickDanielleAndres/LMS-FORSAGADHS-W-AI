<?php
error_reporting(E_ALL ^ E_WARNING);
session_start();
if ($_SESSION['role'] != "student") {
	header("Location: ../index.php");
} else {
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
		$nav_role = "Updates";
		include_once 'nav.php'; ?>
		<!-- MAIN CONTENT -->
		<div class="main-content">
			<div class="container-fluid">
				<div class="row justify-content-center">
					<div class="col-12	">
						<br>
						<!-- Card -->
						<div class="card">
							<div class="card-body">
								<!-- Header -->
								<div class="mb-3">
									<div class="row align-items-center">
										<div class="col ml-n2">
											<!-- Title -->
			
											<h1 class="mb-1">
												Update
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
								<?php
								include_once "../config.php";
								$ttid = $_GET['updateid'];
								$_SESSION["userrole"] = "Faculty";
								if (isset($ttid)) {
									$sql = "SELECT * FROM updatemaster WHERE UpdateId = '$ttid'";
									$result = mysqli_query($conn, $sql);
									$row = mysqli_fetch_assoc($result);

								?>
									<!-- CONTENT -->
									<div class="container-fluid">
										<div class="row">
											<div class="col-12">
												<!-- Files -->
												<div class="card" data-list='{"valueNames": ["name"]}'>
													<div class="card-body">
														<h3 class="header-title">
															Name Info:
														</h3>
														<br>
														<div class="input-group">
															<span class="input-group-text col-3 ">Title</span>
															<input type="text" value="<?php echo $row['UpdateTitle']; ?>" aria-label="First name" class="form-control" disabled>
														</div><br>
														<div class="input-group">
															<span class="input-group-text col-3 ">Description</span>
															<textarea class="form-control disable" rows="3" disabled><?php echo $row['UpdateDescription']; ?></textarea>
														</div>
														<br>
														<div class="input-group">
															<span class="input-group-text col-3 ">Uploaded By</span>
															<input type="text" value="<?php echo $row['UpdateUploadedBy']; ?>" aria-label="First name" class="form-control" disabled>
															<span class="input-group-text col-3 ">Update Type</span>
															<input type="text" value="<?php echo $row['UpdateType']; ?>" aria-label="Last name" class="form-control disable" disabled>
														</div>
													</div>
												</div>
											</div>
										</div>
										<!-- Image -->
										<p class="text-center mb-3">
											<img src="../src/uploads/updates/<?php echo $row['UpdateFile'] . "?t"; ?>" alt="..." class="img-fluid rounded">
										</p>
										<div class="d-flex justify">
											<!-- Button -->
											<a href="../src/uploads/updates/<?php echo $row['UpdateFile']; ?>" download="<?php echo $row['TimetableImage']; ?>" class="btn btn-primary" name="Download">
												Download
											</a>
										</div>
									</div>
									<hr>
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
	</body>

	</html>
<?php } ?>
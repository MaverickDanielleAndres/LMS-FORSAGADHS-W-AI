<?php
session_start();
if ($_SESSION['role'] != "faculty") {
	header("Location: ../index.php");
} else {
	include_once("../config.php");
	$_SESSION["userrole"] = "Faculty";
	$assid = $_GET['actid'];
	$assid = mysqli_real_escape_string($conn, $assid);
	$username = $_SESSION['id'];
	$xxsql = "SELECT * FROM activitymaster WHERE ActivityId='$assid'";
	$xxresult = mysqli_query($conn, $xxsql);
	$xxrow = mysqli_fetch_assoc($xxresult);
	$subsel = "SELECT * FROM subjectmaster INNER JOIN facultymaster ON `subjectmaster`.`SubjectFacultyId` = `facultymaster`.`FacultyId` WHERE `FacultyUserName` = '$username'";
	$subresult = mysqli_query($conn, $subsel);
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


			.custom-modal {
				display: none;
				position: fixed;
				z-index: 9999;
				left: 0;
				top: 0;
				width: 100%;
				height: 100%;
				overflow: auto;
				background-color: rgba(0, 0, 0, 0.5);
			}

			.custom-modal-content {
				background-color: #fff;
				margin: 20% auto;
				padding: 20px;
				border-radius: 8px;
				width: 80%;
				max-width: 400px;
				text-align: center;
				font-size: 1.1rem;
				font-weight: bold;
				animation: fadeIn 0.3s ease-in-out;
			}

			.custom-modal-content.success {
				border: 3px solid #4CAF50;
				color: #4CAF50;
			}

			.custom-modal-content.error {
				border: 3px solid #f44336;
				color: #f44336;
			}

			@keyframes fadeIn {
				from {
					opacity: 0;
					transform: scale(0.95);
				}

				to {
					opacity: 1;
					transform: scale(1);
				}
			}

			.popup-overlay {
				position: fixed;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				background-color: rgba(0, 0, 0, 0.6);
				z-index: 9999;
				display: none;
				justify-content: center;
				align-items: center;
			}

			.popup-container {
				background-color: white;
				border-radius: 12px;
				box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
				max-width: 450px;
				width: 90%;
				padding: 0;
				transform: scale(0.8);
				transition: transform 0.3s ease;
				overflow: hidden;
			}

			.popup-overlay.show .popup-container {
				transform: scale(1);
			}

			.popup-header {
				padding: 20px 25px 15px;
				border-bottom: 1px solid #e5e5e5;
				display: flex;
				align-items: center;
				gap: 12px;
			}

			.popup-icon {
				width: 24px;
				height: 24px;
				border-radius: 50%;
				display: flex;
				align-items: center;
				justify-content: center;
				font-size: 14px;
				font-weight: bold;
			}

			.popup-icon.success {
				background-color: #d4edda;
				color: #155724;
			}

			.popup-icon.error {
				background-color: #f8d7da;
				color: #721c24;
			}

			.popup-title {
				font-size: 18px;
				font-weight: 600;
				margin: 0;
				color: #333;
			}

			.popup-body {
				padding: 20px 25px;
			}

			.popup-message {
				font-size: 16px;
				color: #555;
				line-height: 1.5;
				margin: 0;
			}

			.popup-footer {
				padding: 15px 25px 20px;
				display: flex;
				justify-content: flex-end;
				gap: 10px;
			}

			.popup-btn {
				padding: 10px 20px;
				border: none;
				border-radius: 6px;
				font-size: 14px;
				font-weight: 500;
				cursor: pointer;
				transition: all 0.2s ease;
			}

			.popup-btn.primary {
				background-color: #27548A;
				color: white;
			}

			.popup-btn.primary:hover {
				background-color: #0d3b72ff;
			}
		</style>
	</head>

	<body>
		<?php $nav_role = "Written Works"; ?>
		<!-- NAVIGATION -->
		<?php include_once("nav.php"); ?>
		<!-- MAIN CONTENT -->
		<div class="main-content">
			<div class="container-fluid">
				<div class="row justify-content-center">
					<div class="col-12 col-xl-10">
						<!-- Header -->
						<div class="header mt-md-5">
							<div class="header-body">
								<div class="row align-items-center">
									<div class="col">
										<h6 class="header-pretitle">
											Edit
										</h6>
										<!-- Title -->
										<h1 class="header-title">
											Activity
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
						</div>
						<!-- Form -->
						<br>
						<form method="POST" autocomplete="off" enctype="multipart/form-data" class="row g-3 needs-validation">
							<div class="row justify-content-between align-items-center">
								<div class="col">
									<div class="row align-items-center">
										<div class="col ml-n2">
											<!-- Heading -->
											<h4 class="mb-1">
												Activity File
											</h4>
											<!-- Text -->
											<small class="text-muted">
												Only allowed PDF less than 5MB
											</small>
										</div>
									</div>
									<!-- / .row -->
								</div>
								<div class="col-auto">
									<!-- Button -->
									<input type="file" id="img" name="assfile" class="btn btn-sm" onchange="showPreview(event);" accept="appliction/pdf">
								</div>
							</div>
							<!-- Priview Profile pic  -->
							<script>
								function showPreview(event) {
									var file = document.getElementById('img');
									if (file.files.length > 0) {
										// RUN A LOOP TO CHECK EACH SELECTED FILE.
										for (var i = 0; i <= file.files.length - 1; i++) {
											var fsize = file.files.item(i).size; // THE SIZE OF THE FILE.	
										}
										if (fsize <= 5000000) {
											var src = URL.createObjectURL(event.target.files[0]);
											var preview = document.getElementById("IMG-preview");
											preview.src = src;
											preview.style.display = "block";
										} else {
											alert("Only allowed less then 5MB.. !");
											file.value = '';
										}
									}
								}

								function showModal(id) {
									const modal = document.getElementById(id);
									modal.style.display = "flex";
									setTimeout(() => {
										modal.classList.add('show');
									}, 10);
								}

								function hideModal(id, redirectUrl = null) {
									const modal = document.getElementById(id);
									modal.classList.remove('show');
									setTimeout(() => {
										modal.style.display = "none";
										if (redirectUrl) {
											window.location.href = redirectUrl;
										}
									}, 300);
								}
							</script>
							<hr class="my-5">
							<div class="row">
								<div class="col-md-12">
									<label for="validationCustom01" class="form-label">Activity Title</label>
									<input type="text" class="form-control" id="validationCustom01" name="asstitle" value="<?php echo $xxrow['ActivityTitle']; ?>" required><br>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<label for="validationCustom01" class="form-label">Activity Description</label>
									<textarea class="form-control" id="validationCustom01" name="assdesc" required><?php echo $xxrow['ActivityDesc']; ?></textarea><br>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<label for="validationCustom01" class="form-label">Activity Subject</label>
									<select class="form-control" aria-label="Default select example" name="asssubject" required>
										<?php
										while ($subrow = mysqli_fetch_assoc($subresult)) { ?>
											<option <?php if ($xxrow['ActivitySubject'] == $subrow['SubjectCode']) { ?>selected<?php } ?> value="<?php echo $subrow['SubjectName']; ?>">
												<?php echo $subrow['SubjectName']; ?>
											</option>
										<?php
										}
										?>
									</select>
									<br>
								</div>
								<div class="col-md-6">
									<label for="validationCustom01" class="form-label">Activity Submission Date</label>
									<input type="date" id="validationCustom01" class="form-control" name="assldate" required data-flatpickr placeholder="YYYY-MM-DD" value="<?php echo $xxrow['ActivitySubmissionDate']; ?>"><br>
								</div>
							</div>
							<div class="row">
								<div class="col-md-3">
									<label for="validationCustom01" class="form-label">Total Score</label>
									<input type="int" class="form-control" id="validationCustom01" name="totalscore" value="<?php echo $xxrow['totalscore']; ?>" required><br>
								</div>
							</div>
							<!-- Divider -->
							<hr class="mt-4 mb-5">
							<div class="d-flex justify">
								<!-- Button -->
								<button class="btn btn-primary" style="margin-bottom: 30px" type="submit" value="sub" name="subbed">
									Save Changes
								</button>
							</div>
							<!-- / .row -->
						</form>
						<br>
					</div>
				</div>
				<!-- / .row -->
			</div>
		</div>

		<!-- Success Modal -->
		<div id="successModal" class="popup-overlay">
			<div class="popup-container">
				<div class="popup-header">
					<div class="popup-icon success">✓</div>
					<h3 class="popup-title">Success</h3>
				</div>
				<div class="popup-body">
					<p class="popup-message">Activity Edited Successfully</p>
				</div>
				<div class="popup-footer">
					<button class="popup-btn primary" onclick="hideModal('successModal', 'activity_list.php')">OK</button>
				</div>
			</div>
		</div>

		<!-- Error Modal -->
		<div id="errorModal" class="popup-overlay">
			<div class="popup-container">
				<div class="popup-header">
					<div class="popup-icon error">✕</div>
					<h3 class="popup-title">Error</h3>
				</div>
				<div class="popup-body">
					<p class="popup-message">Error Occurred, Activity Not Added</p>
				</div>
				<div class="popup-footer">
					<button class="popup-btn primary" onclick="hideModal('errorModal', 'add_activity.php')">OK</button>
				</div>
			</div>
		</div>


		<?php #include("context.php");
		?>
		<!-- Map JS -->
		<script src='https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.js'></script>
		<!-- Vendor JS -->
		<script src="../assets/js/vendor.bundle.js"></script>
		<!-- Theme JS -->
		<script src="../assets/js/theme.bundle.js"></script>
	</body>

	</html>
<?php
	if (isset($_POST['subbed'])) {

		$fs_name = $_FILES['assfile']['tmp_name'];
		$fs_size = $_FILES['assfile']['size'];
		$fs_error = $_FILES['assfile']['error'];
		$assname = $_POST['asstitle'];
		$assdesc = $_POST['assdesc'];
		$asssubject = $_POST['asssubject'];
		$assldate = $_POST['assldate'];
		$totalscore = $_POST['totalscore'];
		$dt = date('Y-m-d');
		$xsql = "SELECT SubjectSemester,SubjectCode from subjectmaster where SubjectName='$asssubject'";
		$xresult = mysqli_query($conn, $xsql);
		$xrow = mysqli_fetch_assoc($xresult);
		$sem = $xrow['SubjectSemester'];
		$subcode = $xrow['SubjectCode'];
		$assfile = $assname . $dt . ".pdf";

		if ($fs_error === 0) {
			if ($fs_size <= 5000000) {
				move_uploaded_file($fs_name, "../src/uploads/activities/" . $assfile); // Moving Uploaded File to Server ... to uploades folder by file name f_name ... 
			} else {
				echo "<script>alert('File size is to big .. !');</script>";
			}
		} else {
			echo "Something went wrong .. !";
		}

		$sql = "UPDATE
		activitymaster
		SET
		ActivityTitle = '$assname',
		ActivityDesc = '$assdesc',
		ActivitySubject = '$subcode',
		ActivityFile = '$assfile',
		ActivityForQuarter = '$sem',
		ActivitySubmissionDate = '$assldate',
		totalscore = '$totalscore'
		WHERE 
		ActivityId = '$assid';";
		$run = mysqli_query($conn, $sql);
		if ($run == true) {
			echo "<script>showModal('successModal', 'activity_list.php');</script>";
		} else {
			echo "<script>showModal('errorModal', 'add_activity.php');</script>";
		}
	} else {
		echo "<script>window.open('activity_list.php','_self')";
	}
}
?>
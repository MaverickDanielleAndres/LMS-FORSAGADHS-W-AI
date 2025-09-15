<?php
session_start();
if ($_SESSION['role'] != "faculty") {
	header("Location: ../index.php");
} else {
	include_once("../config.php");
	$_SESSION["userrole"] = "Faculty";
	$username = $_SESSION['id'];
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
				border-radius: 0.75rem;
				background-color: white;
			}

			/* Custom Popup Styles */
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

			.popup-icon.warning {
				background-color: #fff3cd;
				color: #856404;
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

			.popup-btn.secondary {
				background-color: #f8f9fa;
				color: #6c757d;
				border: 1px solid #dee2e6;
			}

			.popup-btn.secondary:hover {
				background-color: #e2e6ea;
			}
		</style>
	</head>

	<body>
		<?php $nav_role = "Assignment"; ?>
		<!-- NAVIGATION -->
		<?php include_once("nav.php"); ?>

		<!-- Custom Popup Modal -->
		<div id="customPopup" class="popup-overlay">
			<div class="popup-container">
				<div class="popup-header">
					<div id="popupIcon" class="popup-icon success">✓</div>
					<h3 id="popupTitle" class="popup-title">Success</h3>
				</div>
				<div class="popup-body">
					<p id="popupMessage" class="popup-message">Operation completed successfully!</p>
				</div>
				<div class="popup-footer">
					<button id="popupCloseBtn" class="popup-btn primary">OK</button>
				</div>
			</div>
		</div>

		<!-- MAIN CONTENT -->
		<div class="main-content">
			<div class="container-fluid">
				<div class="row justify-content-center">
					<div class="col-12 col-lg-10 col-xl-8">
						<!-- Header -->
						<div class="header mt-md-5">
							<div class="header-body">
								<div class="row align-items-center">
									<div class="col">
										<h6 class="header-pretitle">
											Add New
										</h6>
										<!-- Title -->
										<h1 class="header-title">
											Assignment
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
												Assignment File
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
									<input type="file" id="img" name="assfile" class="btn btn-sm" onchange="showPreview(event);" accept="application/pdf" required>
								</div>
							</div>
							<!-- Preview Profile pic  -->
							<script>
								// Custom popup functions
								function showCustomPopup(message, type = 'success', title = null) {
									const popup = document.getElementById('customPopup');
									const icon = document.getElementById('popupIcon');
									const titleEl = document.getElementById('popupTitle');
									const messageEl = document.getElementById('popupMessage');
									const closeBtn = document.getElementById('popupCloseBtn');

									// Set content
									messageEl.textContent = message;

									// Set type and styling
									icon.className = 'popup-icon ' + type;
									switch (type) {
										case 'success':
											icon.textContent = '✓';
											titleEl.textContent = title || 'Success';
											break;
										case 'error':
											icon.textContent = '✕';
											titleEl.textContent = title || 'Error';
											break;
										case 'warning':
											icon.textContent = '⚠';
											titleEl.textContent = title || 'Warning';
											break;
									}

									// Show popup
									popup.style.display = 'flex';
									setTimeout(() => {
										popup.classList.add('show');
									}, 10);

									// Close button handler
									closeBtn.onclick = function() {
										hideCustomPopup();
									};

									// Close on overlay click
									popup.onclick = function(e) {
										if (e.target === popup) {
											hideCustomPopup();
										}
									};
								}

								function hideCustomPopup() {
									const popup = document.getElementById('customPopup');
									popup.classList.remove('show');
									setTimeout(() => {
										popup.style.display = 'none';
									}, 300);
								}

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
											if (preview) {
												preview.src = src;
												preview.style.display = "block";
											}
										} else {
											showCustomPopup("File size must be less than 5MB!", "error", "File Size Error");
											file.value = '';
										}
									}
								}
							</script>
							<hr class="my-5">
							<div class="row">
								<div class="col-md-12">
									<label for="validationCustom01" class="form-label">Assignment Title</label>
									<input type="text" class="form-control" id="validationCustom01" name="asstitle" required><br>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<label for="validationCustom01" class="form-label">Assignment Description</label>
									<textarea class="form-control" id="validationCustom01" name="assdesc" required></textarea><br>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<label for="validationCustom01" class="form-label">Assignment Subject</label>
									<select class="form-control" aria-label="Default select example" name="asssubject" required>
										<option hidden>Select Subject</option>
										<?php
										while ($subrow = mysqli_fetch_assoc($subresult)) { ?>
											<option value="<?php echo $subrow['SubjectId']; ?>">
												<?php echo $subrow['SubjectName']; ?>
											</option>
										<?php
											$assupd = $subrow['SubjectFacultyId'];
										}
										?>
									</select>
									<br>
								</div>
								<div class="col-md-6">
									<label for="validationCustom01" class="form-label">Assignment Submission Date</label>
									<input type="date" id="validationCustom01" class="form-control" name="assldate" required data-flatpickr placeholder="YYYY-MM-DD" required><br>
								</div>
							</div>
							<div class="row">
								<div class="col-md-3">
									<label for="validationCustom01" class="form-label">Total Score</label>
									<input type="int" class="form-control" id="validationCustom01" name="totalscore" required><br>
								</div>
							</div>
							<!-- Divider -->
							<hr class="mt-4 mb-5">
							<div class="d-flex justify">
								<!-- Button -->
								<button class="btn btn-primary" style="margin-bottom: 30px" type="submit" value="sub" name="subbed">
									Add Assignment
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
		<?php include("context.php");
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
		$xsql = "SELECT * from subjectmaster where SubjectId='$asssubject'";
		$xresult = mysqli_query($conn, $xsql);
		$xrow = mysqli_fetch_assoc($xresult);
		$sem = $xrow['SubjectSemester'];
		$branch = $xrow['SubjectBranch'];
		$subcode = $xrow['SubjectCode'];
		$assfile = $assname . $dt . ".pdf";

		$sql = "INSERT INTO assignmentmaster (AssignmentStatus,AssignmentBranch,AssignmentTitle, AssignmentDesc, AssignmentSubject, AssignmentUploadedBy, AssignmentFile, AssignmentUploaddate, AssignmentForSemester, AssignmentSubmissionDate, totalscore) 
		VALUES (1,'$branch','$assname', '$assdesc', '$subcode', '$assupd', '$assfile', '$dt', '$sem', '$assldate', '$totalscore')";
		$run = mysqli_query($conn, $sql);
		if ($run == true) {
			if ($fs_error === 0) {
				if ($fs_size <= 5000000) {
					move_uploaded_file($fs_name, "../src/uploads/assignments/" . $assfile);
				} else {
					echo "<script>
						document.addEventListener('DOMContentLoaded', function() {
							showCustomPopup('File size is too big!', 'error', 'Upload Error');
						});
					</script>";
				}
			} else {
				echo "<script>
					document.addEventListener('DOMContentLoaded', function() {
						showCustomPopup('Something went wrong during file upload!', 'error', 'Upload Error');
					});
				</script>";
			}
			echo "<script>
				document.addEventListener('DOMContentLoaded', function() {
					showCustomPopup('Assignment Added Successfully', 'success');
					setTimeout(function() {
						window.location.href = 'assignment_list.php';
					}, 2000);
				});
			</script>";
		} else {
			echo "<script>
				document.addEventListener('DOMContentLoaded', function() {
					showCustomPopup('Error Occurred, Assignment Not Added', 'error');
					setTimeout(function() {
						window.location.href = 'add_assignment.php';
					}, 2000);
				});
			</script>";
		}
	}
}
?>
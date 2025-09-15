<?php
session_start();
if ($_SESSION['role'] != "Texas") {
	header("Location: ../index.php");
	exit();
} else {
	include_once("../config.php");
	$_SESSION["userrole"] = "Faculty";
}

$branchsel = "SELECT * FROM branchmaster";
$branchresult = mysqli_query($conn, $branchsel);
$sectionsel = "SELECT * FROM sectionmaster";
$sectionresult = mysqli_query($conn, $sectionsel);

// Function to generate strong password
function generateStrongPassword($length = 12)
{
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_[]{}';
	return substr(str_shuffle($chars), 0, $length);
}

// Function to sanitize input
function sanitizeInput($conn, $input)
{
	return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags(trim($input))));
}

// Process form submission
if (isset($_POST['subbed'])) {
	// File upload handling
	$f_tmp_name = $_FILES['stuprofile']['tmp_name'];
	$f_size = $_FILES['stuprofile']['size'];
	$f_error = $_FILES['stuprofile']['error'];
	$f_type = $_FILES['stuprofile']['type'];

	// Validate file type
	$allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
	if (!in_array($f_type, $allowed_types)) {
		echo "<script>alert('Only PNG, JPG, and JPEG files are allowed!');</script>";
	} else {
		// Sanitize all inputs
		$fname = sanitizeInput($conn, $_POST['fname']);
		$mname = sanitizeInput($conn, $_POST['mname']);
		$lname = sanitizeInput($conn, $_POST['lname']);
		$fcontact = sanitizeInput($conn, $_POST['fcontact']);
		$femail = sanitizeInput($conn, $_POST['femail']);
		$foffice = sanitizeInput($conn, $_POST['foffice']);
		$fbranch = sanitizeInput($conn, $_POST['fbranch']);
		$fsection = sanitizeInput($conn, $_POST['fsection']);
		$fquali = sanitizeInput($conn, $_POST['fqualification']);
		$fcode = sanitizeInput($conn, $_POST['fcode']);
		$fid = "FA" . strtoupper($fcode);

		// Validate password requirements
		$plainPassword = $_POST['fpass'];
		$passwordValid = strlen($plainPassword) >= 8 &&
			preg_match('/[A-Z]/', $plainPassword) &&
			preg_match('/[a-z]/', $plainPassword) &&
			preg_match('/[0-9]/', $plainPassword) &&
			preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $plainPassword);

		if (!$passwordValid) {
			echo "<script>alert('Password must meet all requirements: at least 8 characters, uppercase, lowercase, number, and special character!');</script>";
		} else {
			// Hash the password securely
			$fpass = password_hash($plainPassword, PASSWORD_DEFAULT);

			$fs_name = $fcode . ".png";

			// Validate required fields
			if (
				empty($fname) || empty($mname) || empty($lname) || empty($femail) || empty($fcontact) ||
				empty($foffice) || empty($fbranch) || empty($fsection) || empty($fquali) || empty($fcode)
			) {
				echo "<script>alert('All fields are required!');</script>";
			} else {
				// Validate email format
				if (!filter_var($femail, FILTER_VALIDATE_EMAIL)) {
					echo "<script>alert('Invalid email format!');</script>";
				} else {
					// Validate phone number (10 digits)
					if (!preg_match('/^[0-9]{10}$/', $fcontact)) {
						echo "<script>alert('Contact number must be exactly 10 digits!');</script>";
					} else {
						// Check for duplicate email
						$email_check = "SELECT FacultyEmail FROM facultymaster WHERE FacultyEmail = ?";
						$stmt = mysqli_prepare($conn, $email_check);
						mysqli_stmt_bind_param($stmt, "s", $femail);
						mysqli_stmt_execute($stmt);
						$result = mysqli_stmt_get_result($stmt);
						if (mysqli_num_rows($result) > 0) {
							echo "<script>alert('Email already exists!');</script>";
						} else {
							// Check for duplicate faculty code
							$code_check = "SELECT FacultyCode FROM facultymaster WHERE FacultyCode = ?";
							$stmt = mysqli_prepare($conn, $code_check);
							mysqli_stmt_bind_param($stmt, "s", $fcode);
							mysqli_stmt_execute($stmt);
							$result = mysqli_stmt_get_result($stmt);
							if (mysqli_num_rows($result) > 0) {
								echo "<script>alert('Faculty code already exists!');</script>";
							} else {
								// Prepare SQL statement to prevent SQL injection
								$sql = "INSERT INTO facultymaster(
										FacultyUserName,
										FacultyPassword,
										FacultyFirstName,
										FacultyMiddleName,
										FacultyLastName,
										FacultyProfilePic,
										FacultyBranchCode,
										FacultySection,
										FacultyEmail,
										FacultyContactNo,
										FacultyQualification,
										FacultyOffice,
										FacultyCode
									) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

								$stmt = mysqli_prepare($conn, $sql);
								mysqli_stmt_bind_param(
									$stmt,
									"sssssssssssss",
									$fid,
									$fpass,
									$fname,
									$mname,
									$lname,
									$fs_name,
									$fbranch,
									$fsection,
									$femail,
									$fcontact,
									$fquali,
									$foffice,
									$fcode
								);

								try {
									$run = mysqli_stmt_execute($stmt);
									if ($run) {
										// Handle file upload
										if ($f_error === 0) {
											if ($f_size <= 2000000) {
												$upload_dir = "../src/uploads/facprofile/";
												if (!is_dir($upload_dir)) {
													mkdir($upload_dir, 0755, true);
												}
												move_uploaded_file($f_tmp_name, $upload_dir . $fs_name);
											} else {
												echo "<script>alert('File size must be less than 2MB!');</script>";
											}
										} else {
											echo "<script>alert('Error uploading file!');</script>";
										}

										// Show success message with login credentials
										echo "<script>
											alert('Faculty Added Successfully!\\n\\nLogin ID: $fid\\nPassword: $plainPassword\\n\\nPlease save these credentials securely.');
											window.location.href = 'faculty_list.php';
										</script>";
									} else {
										throw new Exception("Database insertion failed");
									}
								} catch (Exception $e) {
									echo "<script>alert('Error: Faculty could not be added. Please try again.');</script>";
									error_log("Faculty insertion error: " . $e->getMessage());
								}

								mysqli_stmt_close($stmt);
							}
						}
					}
				}
			}
		}
	}
}
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

		.password-strength {
			font-size: 0.8rem;
			margin-top: 5px;
		}

		.password-strength.weak {
			color: #dc3545;
		}

		.password-strength.medium {
			color: #ffc107;
		}

		.password-strength.strong {
			color: #28a745;
		}

		.form-control.is-invalid {
			border-color: #dc3545;
		}

		.form-control.is-valid {
			border-color: #28a745;
		}

		.password-requirements {
			margin-top: 8px;
			padding: 10px;
			background-color: #f8f9fa;
			border-radius: 4px;
			border: 1px solid #dee2e6;
		}

		.password-requirements h6 {
			margin-bottom: 8px;
			color: #495057;
			font-size: 0.9rem;
		}

		.password-requirements ul {
			margin-bottom: 0;
			padding-left: 20px;
		}

		.password-requirements li {
			font-size: 0.8rem;
			color: #dc3545;
			margin-bottom: 3px;
			transition: color 0.3s ease;
		}

		.password-requirements li.valid {
			color: #28a745;
		}

		.password-requirements li.valid::before {
			content: "✓ ";
			font-weight: bold;
		}

		.password-requirements li:not(.valid)::before {
			content: "✗ ";
			font-weight: bold;
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
	$nav_role = "Faculty";
	include_once("../nav.php"); ?>
	<!-- MAIN CONTENT -->
	<div class="main-content">
		<div class="container-fluid">
			<div class="row justify-content-center">
				<div class="col-12 col-xl-10">
					<!-- Header -->
					<div class="header mt-md-5">
						<div class="header-body">
							<div class="row align-items-center justify-content-between">
								<div class="col">
									<h6 class="header-pretitle">
										Add New
									</h6>
									<h1 class="header-title">
										Faculty
									</h1>
								</div>
								<div class="col-auto text-end">
									<button class="back-btn" onclick="history.back()">
										<i class="fe uil-angle-double-left"></i> Back
									</button>
								</div>
							</div>
						</div>
					</div>
					<!-- / .row -->
				</div>
			</div>
			<!-- Form -->
			<form method="POST" autocomplete="off" enctype="multipart/form-data" class="row g-3 needs-validation" novalidate>
				<div class="row justify-content-between align-items-center">
					<div class="col">
						<div class="row align-items-center">
							<div class="col-auto">
								<!-- Personal details -->
								<!-- Avatar -->
								<div class="avatar">
									<img class="avatar-img rounded-circle" id="IMG-preview" alt="..." src="../assets/img/avatars/profiles/avatar-1.jpg">
								</div>
							</div>
							<div class="col ml-n2">
								<!-- Heading -->
								<h4 class="mb-1">
									Faculty Photo
								</h4>
								<!-- Text -->
								<small class="text-muted">
									Only allowed PNG or JPG less than 2MB
								</small>
							</div>
						</div>
						<!-- / .row -->
					</div>
					<div class="col-auto">
						<!-- Button -->
						<input type="file" id="img" name="stuprofile" class="btn btn-sm" onchange="showPreview(event);" accept="image/png,image/jpg,image/jpeg" required>
						<div class="invalid-feedback">
							Please select a profile picture.
						</div>
					</div>
				</div>
				<!-- / .row -->

				<!-- Preview Profile pic Script -->
				<script>
					function showPreview(event) {
						var file = document.getElementById('img');
						if (file.files.length > 0) {
							// Check file size
							for (var i = 0; i <= file.files.length - 1; i++) {
								var fsize = file.files.item(i).size;
								if (fsize <= 2000000) {
									var src = URL.createObjectURL(event.target.files[0]);
									var preview = document.getElementById("IMG-preview");
									preview.src = src;
									preview.style.display = "block";
									file.classList.remove('is-invalid');
									file.classList.add('is-valid');
								} else {
									alert("File size must be less than 2MB!");
									file.value = '';
									file.classList.remove('is-valid');
									file.classList.add('is-invalid');
								}
							}
						}
					}
				</script>

				<!-- Form Card Container -->
				<div class="card shadow-sm">
					<div class="card-body p-4">
						<!-- Divider inside card -->
						<hr class="my-4">

						<!-- Name Row -->
						<div class="row">
							<div class="col-12 col-md-4">
								<div class="form-group">
									<label class="form-label" for="fname">First name *</label>
									<input type="text"
										onkeypress='return ((event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122) || event.charCode == 32)'
										maxlength="30" minlength="2" class="form-control" id="fname" name="fname"
										pattern="[A-Za-z\s]{2,30}"
										title="First name should contain only letters and spaces, 2-30 characters" required>
									<div class="valid-feedback">Looks good!</div>
									<div class="invalid-feedback">First name should contain only letters and spaces (2-30 characters)</div>
								</div>
							</div>
							<div class="col-12 col-md-4">
								<div class="form-group">
									<label class="form-label" for="mname">Middle name *</label>
									<input type="text"
										onkeypress='return ((event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122) || event.charCode == 32)'
										maxlength="30" minlength="2" class="form-control" id="mname" name="mname"
										pattern="[A-Za-z\s]{2,30}"
										title="Middle name should contain only letters and spaces, 2-30 characters" required>
									<div class="valid-feedback">Looks good!</div>
									<div class="invalid-feedback">Middle name should contain only letters and spaces (2-30 characters)</div>
								</div>
							</div>
							<div class="col-12 col-md-4">
								<div class="form-group">
									<label class="form-label" for="lname">Last name *</label>
									<input type="text"
										onkeypress='return ((event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122) || event.charCode == 32)'
										maxlength="30" minlength="2" class="form-control" id="lname" name="lname"
										pattern="[A-Za-z\s]{2,30}"
										title="Last name should contain only letters and spaces, 2-30 characters" required>
									<div class="valid-feedback">Looks good!</div>
									<div class="invalid-feedback">Last name should contain only letters and spaces (2-30 characters)</div>
								</div>
							</div>
						</div>

						<!-- Email and Contact -->
						<div class="row">
							<div class="col-12 col-md-6">
								<div class="form-group">
									<label class="form-label" for="femail">Faculty Email address *</label>
									<input type="email" class="form-control" maxlength="50" id="femail" name="femail"
										pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" title="Please enter a valid email address"
										onblur="checkEmailUnique(this.value)" required>
									<div class="valid-feedback">Looks good!</div>
									<div class="invalid-feedback">Please enter a valid email address</div>
									<div id="email-error" class="text-danger" style="display: none;"></div>
								</div>
							</div>
							<div class="col-12 col-md-6">
								<div class="form-group">
									<label class="form-label" for="fcontact">Faculty Contact Number *</label>
									<input type="tel" pattern="[0-9]{10}" onkeypress="return event.charCode>=48 && event.charCode<=57"
										maxlength="10" minlength="10" id="fcontact" class="form-control" name="fcontact"
										title="Please enter a valid 10-digit phone number" required>
									<div class="valid-feedback">Looks good!</div>
									<div class="invalid-feedback">Please enter a valid 10-digit phone number</div>
								</div>
							</div>
						</div>

						<!-- Office, Branch, Section -->
						<div class="row">
							<div class="col-12 col-md-4">
								<div class="form-group">
									<label class="form-label" for="foffice">Faculty Office *</label>
									<input type="text" class="form-control" id="foffice" name="foffice" maxlength="50" minlength="2"
										pattern="[A-Za-z0-9\s\-\.]{2,50}" title="Office location should be 2-50 characters" required>
									<div class="valid-feedback">Looks good!</div>
									<div class="invalid-feedback">Office location is required (2-50 characters)</div>
								</div>
							</div>

							<div class="col-12 col-md-4">
								<div class="form-group">
									<label class="form-label" for="fbranch">Grade Level *</label>
									<select id="fbranch" class="form-control" name="fbranch" required>
										<option value="" hidden="">Select Grade Level</option>
										<?php while ($brrow = mysqli_fetch_assoc($branchresult)) { ?>
											<option value="<?php echo htmlspecialchars($brrow['BranchCode']); ?>">
												<?php echo htmlspecialchars($brrow['BranchName']); ?>
											</option>
										<?php } ?>
									</select>
									<div class="valid-feedback">Looks good!</div>
									<div class="invalid-feedback">Please select a Grade Level</div>
								</div>
							</div>

							<div class="col-12 col-md-4">
								<div class="form-group">
									<label class="form-label" for="fsection">Section *</label>
									<select id="fsection" class="form-control" name="fsection" required>
										<option value="" hidden="">Select Section</option>
										<?php while ($secrow = mysqli_fetch_assoc($sectionresult)) { ?>
											<option value="<?php echo htmlspecialchars($secrow['SectionId']); ?>">
												<?php echo htmlspecialchars($secrow['SectionNumber'] . ' - ' . $secrow['SectionBranch']); ?>
											</option>
										<?php } ?>
									</select>
									<div class="valid-feedback">Looks good!</div>
									<div class="invalid-feedback">Please select a section</div>
								</div>
							</div>
						</div>
					</div>
				</div>


				<!-- Personal & Credential Details Card -->
				<div class="card shadow-sm mb-5">
					<div class="card-body p-4">

						<!-- Qualification & Faculty Code -->
						<hr class="my-4">
						<div class="row">
							<div class="col-12 col-md-6">
								<div class="form-group">
									<label class="form-label" for="fqualification">Faculty Qualification *</label>
									<input type="text" class="form-control" id="fqualification" name="fqualification"
										maxlength="100" minlength="2"
										pattern="[A-Za-z0-9\s\.\,\-\(\)]{2,100}" title="Qualification should be 2-100 characters"
										required>
									<div class="valid-feedback">Looks good!</div>
									<div class="invalid-feedback">Qualification is required (2-100 characters)</div>
								</div>
							</div>

							<div class="col-12 col-md-6">
								<div class="form-group">
									<label class="form-label" for="fcode">Faculty Code *</label>
									<input type="text" id="fcode" oninput="generateLoginId()" class="form-control" name="fcode"
										maxlength="10" minlength="3" pattern="[A-Za-z0-9]{3,10}"
										title="Faculty code should be 3-10 alphanumeric characters"
										onblur="checkCodeUnique(this.value)" required>
									<div class="valid-feedback">Looks good!</div>
									<div class="invalid-feedback">Faculty code should be 3-10 alphanumeric characters</div>
									<div id="code-error" class="text-danger" style="display: none;"></div>
								</div>
							</div>
						</div>

						<!-- Login ID & Password -->
						<hr class="my-4">
						<div class="row">
							<div class="col-12 col-md-6">
								<div class="form-group">
									<label class="form-label">Faculty Login ID</label>
									<div class="input-group">
										<input type="text" id="loginId" class="form-control" name="fid" readonly>
										<button type="button" class="btn btn-outline-secondary" onclick="copyToClipboard('loginId')">
											<i class="fe fe-copy"></i>
										</button>
									</div>
									<small class="text-muted">Auto-generated from Faculty Code</small>
								</div>
							</div>

							<div class="col-12 col-md-6">
								<div class="form-group">
									<label class="form-label">Faculty Password *</label>
									<div class="input-group">
										<input type="text" id="generatedPassword" class="form-control" name="fpass"
											oninput="validatePassword()" minlength="8" required>
										<button type="button" class="btn btn-outline-secondary"
											onclick="copyToClipboard('generatedPassword')">
											<i class="fe fe-copy"></i>
										</button>
										<button type="button" class="btn btn-outline-primary" onclick="generateNewPassword()">
											<i class="fe fe-refresh-cw"></i>
										</button>
									</div>
									<div class="password-requirements mt-2">
										<h6 class="mb-1">Password Requirements:</h6>
										<ul class="mb-0">
											<li id="length-req">At least 8 characters</li>
											<li id="uppercase-req">At least one uppercase letter</li>
											<li id="lowercase-req">At least one lowercase letter</li>
											<li id="number-req">At least one number</li>
											<li id="special-req">At least one special character (!@#$%^&*)</li>
										</ul>
									</div>
									<div class="valid-feedback">Password meets all requirements!</div>
									<div class="invalid-feedback">Password must meet all requirements above</div>
								</div>
							</div>
						</div>

						<!-- Actions -->
						<div class="d-flex justify-content-between mt-4">
							<button type="button" class="btn btn-secondary" onclick="history.back()">Cancel</button>
							<button class="btn btn-primary" type="submit" name="subbed">
								<i class="fe fe-save"></i> Save Faculty
							</button>
						</div>

					</div>
				</div>

				<!-- / .row -->
		</div>
	</div>
	<?php include_once("context.php"); ?>

	<!-- Map JS -->
	<script src='https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.js'></script>
	<!-- Vendor JS -->
	<script src="../assets/js/vendor.bundle.js"></script>
	<!-- Theme JS -->
	<script src="../assets/js/theme.bundle.js"></script>

	<script>
		// Generate strong password
		function generateStrongPassword(length = 12) {
			const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_[]{}';
			let password = '';
			for (let i = 0; i < length; i++) {
				password += chars.charAt(Math.floor(Math.random() * chars.length));
			}
			return password;
		}

		// Generate new password
		function generateNewPassword() {
			const newPassword = generateStrongPassword();
			document.getElementById('generatedPassword').value = newPassword;
			validatePassword();
		}

		// Validate password requirements
		function validatePassword() {
			const password = document.getElementById('generatedPassword').value;
			const passwordField = document.getElementById('generatedPassword');

			// Check each requirement
			const hasLength = password.length >= 8;
			const hasUppercase = /[A-Z]/.test(password);
			const hasLowercase = /[a-z]/.test(password);
			const hasNumber = /[0-9]/.test(password);
			const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);

			// Update requirement indicators
			updateRequirement('length-req', hasLength);
			updateRequirement('uppercase-req', hasUppercase);
			updateRequirement('lowercase-req', hasLowercase);
			updateRequirement('number-req', hasNumber);
			updateRequirement('special-req', hasSpecial);

			// Check if all requirements are met
			const allRequirementsMet = hasLength && hasUppercase && hasLowercase && hasNumber && hasSpecial;

			if (allRequirementsMet) {
				passwordField.classList.remove('is-invalid');
				passwordField.classList.add('is-valid');
			} else {
				passwordField.classList.remove('is-valid');
				if (password.length > 0) {
					passwordField.classList.add('is-invalid');
				}
			}
		}

		// Update individual requirement status
		function updateRequirement(elementId, isValid) {
			const element = document.getElementById(elementId);
			if (isValid) {
				element.classList.add('valid');
			} else {
				element.classList.remove('valid');
			}
		}

		// Generate login ID from faculty code
		function generateLoginId() {
			const code = document.getElementById('fcode').value;
			const loginId = code ? 'FA' + code.toUpperCase() : '';
			document.getElementById('loginId').value = loginId;
		}

		// Copy to clipboard function
		function copyToClipboard(elementId) {
			const element = document.getElementById(elementId);
			element.select();
			element.setSelectionRange(0, 99999);
			navigator.clipboard.writeText(element.value).then(function() {
				alert('Copied: ' + element.value);
			});
		}

		// Check email uniqueness
		function checkEmailUnique(email) {
			if (email) {
				const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
				if (!emailRegex.test(email)) {
					document.getElementById('email-error').style.display = 'block';
					document.getElementById('email-error').textContent = 'Invalid email format';
					document.getElementById('femail').classList.add('is-invalid');
				} else {
					document.getElementById('email-error').style.display = 'none';
					document.getElementById('femail').classList.remove('is-invalid');
				}
			}
		}

		// Check code uniqueness
		function checkCodeUnique(code) {
			if (code) {
				generateLoginId();
			}
		}

		// Initialize password on page load
		document.addEventListener('DOMContentLoaded', function() {
			generateNewPassword();
		});

		// Form validation
		(function() {
			'use strict'
			var forms = document.querySelectorAll('.needs-validation')
			Array.prototype.slice.call(forms)
				.forEach(function(form) {
					form.addEventListener('submit', function(event) {
						if (!form.checkValidity()) {
							event.preventDefault()
							event.stopPropagation()
						}
						form.classList.add('was-validated')
					}, false)
				})
		})()
	</script>
</body>

</html>
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

			/* Password Requirements Styles */
			.password-requirements {
				background-color: #f8f9fa;
				border: 1px solid #dee2e6;
				border-radius: 0.375rem;
				padding: 15px;
				margin-top: 10px;
				display: none;
			}

			.password-requirement {
				display: flex;
				align-items: center;
				margin-bottom: 8px;
				font-size: 14px;
				color: #6c757d;
			}

			.password-requirement.valid {
				color: #28a745;
			}

			.password-requirement i {
				margin-right: 8px;
				font-size: 12px;
			}

			.change-password-btn {
				background-color: #17a2b8;
				border-color: #17a2b8;
				color: white;
			}

			.change-password-btn:hover {
				background-color: #138496;
				border-color: #117a8b;
			}

			.form-control.is-invalid {
				border-color: #dc3545;
			}

			.form-control.is-valid {
				border-color: #28a745;
			}

			.invalid-feedback {
				display: block;
				color: #dc3545;
				font-size: 0.875rem;
				margin-top: 0.25rem;
			}

			.error-message {
				color: #dc3545;
				font-size: 12px;
				margin-top: 5px;
				display: none;
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
											Edit
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
				<?php
				include_once("../config.php");
				$studentenr = $_GET['facid'];
				$studentenr = mysqli_real_escape_string($conn, $studentenr);
				$_SESSION["userrole"] = "Institute";
				if (isset($studentenr)) {
					$sql = "SELECT * FROM facultymaster WHERE FacultyId = '$studentenr'";
					$result = mysqli_query($conn, $sql);
					$row = mysqli_fetch_assoc($result);

				?>
					<form method="POST" autocomplete="off" enctype="multipart/form-data" onsubmit="return validateForm()">
						<div class="row justify-content-between align-items-center">
							<div class="col">
								<div class="row align-items-center">
									<div class="col-auto">
										<!-- Personal details -->
										<!-- Avatar -->
										<div class="avatar">
											<img class="avatar-img rounded-circle" id="IMG-preview" src="../src/uploads/facprofile/<?php echo $row['FacultyProfilePic'] . "?t"; ?>" alt="...">
										</div>
									</div>
									<div class="col ml-n2">
										<!-- Heading -->
										<h4 class="mb-1">
											Faculty Photo
										</h4>
										<small style="color: black;">
											Only allowed PNG or JPG less than 2MB
										</small>
									</div>
								</div>
								<br>
								<!-- / .row -->
							</div>
							<div class="col-auto">
								<!-- Button -->
								<input type="file" id="img" name="stuprofile" class="btn btn-sm" onchange="showPreview(event);" accept="image/jpg, image/jpeg, image/png">
							</div>
						</div>

						<!-- Divider -->
						<!-- Card Wrapper Start -->
						<div class="card shadow-sm p-4">

							<hr class="my-5">
							<div class="row">
								<div class="col-12 col-md-4">
									<!-- First name -->
									<div class="form-group">
										<label class="form-label">
											First name <span class="text-danger">*</span>
										</label>
										<input type="text" class="form-control" id="fname" name="fname" value="<?php echo $row['FacultyFirstName']; ?>" required
											pattern="[A-Za-z\s]+" title="Only letters and spaces allowed" maxlength="50"
											oninput="validateName(this)">
										<div class="error-message" id="fname-error"></div>
									</div>
								</div>
								<div class="col-12 col-md-4">
									<!-- Middle name -->
									<div class="form-group">
										<label class="form-label">
											Middle name
										</label>
										<input type="text" class="form-control" id="mname" name="mname" value="<?php echo $row['FacultyMiddleName']; ?>"
											pattern="[A-Za-z\s]*" title="Only letters and spaces allowed" maxlength="50"
											oninput="validateName(this)">
										<div class="error-message" id="mname-error"></div>
									</div>
								</div>
								<div class="col-12 col-md-4">
									<!-- Last name -->
									<div class="form-group">
										<label class="form-label">
											Last name <span class="text-danger">*</span>
										</label>
										<input type="text" class="form-control" id="lname" name="lname" value="<?php echo $row['FacultyLastName']; ?>" required
											pattern="[A-Za-z\s]+" title="Only letters and spaces allowed" maxlength="50"
											oninput="validateName(this)">
										<div class="error-message" id="lname-error"></div>
									</div>
								</div>
							</div>
							<!-- / .row -->
							<div class="row">
								<div class="col-12 col-md-6">
									<!-- Email address -->
									<div class="form-group">
										<label class="form-label">
											Faculty Email address <span class="text-danger">*</span>
										</label>
										<input type="email" class="form-control" id="femail" name="femail" value="<?php echo $row['FacultyEmail']; ?>"
											required title="Email cannot be changed">
										<div class="error-message" id="email-error"></div>
										<small class="text-muted">Enter a correct email format</small>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<label class="form-label">
										Faculty Contact Number <span class="text-danger">*</span>
									</label>
									<input type="tel" pattern="[0-9]{10}" class="form-control" maxlength="10" id="fcontact" name="fcontact"
										value="<?php echo $row['FacultyContactNo']; ?>" required
										oninput="validatePhone(this)" title="Enter 10 digit phone number">
									<div class="error-message" id="contact-error"></div>
								</div>
							</div>

							<?php
							$branchsel = "SELECT * FROM branchmaster";
							$branchresult = mysqli_query($conn, $branchsel);
							$sectionsel = "SELECT * FROM sectionmaster";
							$sectionresult = mysqli_query($conn, $sectionsel);
							?>

							<div class="row">
								<div class="col-12 col-md-4">
									<!-- Office -->
									<div class="form-group">
										<label class="form-label">
											Faculty Office <span class="text-danger">*</span>
										</label>
										<input type="text" class="form-control" id="foffice" name="foffice" value="<?php echo $row['FacultyOffice']; ?>"
											required maxlength="100" oninput="validateOffice(this)">
										<div class="error-message" id="office-error"></div>
									</div>
								</div>
								<div class="col-12 col-md-4">
									<!-- Branch -->
									<div class="form-group">
										<label class="form-label">
											Grade Level <span class="text-danger">*</span>
										</label>
										<select id="fbranch" class="form-control" name="fbranch" required>
											<option value="" hidden="">Select Grade Level</option>
											<?php while ($brrow = mysqli_fetch_assoc($branchresult)) { ?>
												<option <?php if ($brrow['BranchCode'] == $row['FacultyBranchCode']) echo "selected"; ?> value="<?php echo $brrow['BranchCode']; ?>">
													<?php echo $brrow['BranchName']; ?>
												</option>
											<?php } ?>
										</select>
										<div class="error-message" id="branch-error"></div>
									</div>
								</div>
								<div class="col-12 col-md-4">
									<!-- Section -->
									<div class="form-group">
										<label class="form-label">
											Section <span class="text-danger">*</span>
										</label>
										<select id="fsection" class="form-control" name="fsection" required>
											<option value="" hidden="">Select Section</option>
											<?php while ($secrow = mysqli_fetch_assoc($sectionresult)) { ?>
												<option <?php if ($secrow['SectionId'] == $row['FacultySection']) echo "selected"; ?> value="<?php echo $secrow['SectionId']; ?>">
													<?php echo $secrow['SectionNumber'] . ' - ' . $secrow['SectionBranch']; ?>
												</option>
											<?php } ?>
										</select>
										<div class="error-message" id="section-error"></div>
									</div>
								</div>
							</div>

						</div>
						<!-- Card Wrapper End -->
						<!-- / Personal details-->
						<hr class="my-5">

						<!-- Card Wrapper Start -->
						<div class="card shadow-sm p-4">
							<div class="row">
								<div class="col-12 col-md-6">
									<div class="form-group">
										<label class="form-label">
											Faculty Qualification <span class="text-danger">*</span>
										</label>
										<input type="text" class="form-control" id="fqualification" name="fqualification"
											value="<?php echo $row['FacultyQualification']; ?>" required maxlength="200"
											oninput="validateQualification(this)">
										<div class="error-message" id="qualification-error"></div>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group">
										<label class="form-label">
											Faculty Code <span class="text-danger">*</span>
										</label>
										<input type="text" id="fcode" class="form-control" value="<?php echo $row['FacultyCode']; ?>"
											name="fcode" required readonly title="Faculty code cannot be changed">
										<small class="text-muted">Faculty code cannot be modified</small>
									</div>
								</div>
							</div>
							<hr class="my-5">
							<!-- Login Credentials -->
							<div class="row">
								<div class="col-12 col-md-6">
									<div class="form-group">
										<label class="form-label">
											Faculty Login ID
										</label>
										<div class="input-group">
											<input type="text" class="form-control" id="loginId" name="loginId"
												value="FA<?php echo $row['FacultyCode']; ?>" readonly>
											<div class="input-group-append">
												<button class="btn btn-primary" type="button" onclick="copyToClipboard('loginId')">
													<i class="fe fe-copy"></i>
												</button>
											</div>
										</div>
										<small class="text-muted">Login ID is auto-generated from faculty code</small>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group">
										<label class="form-label">
											Faculty Password
										</label>
										<div class="input-group">
											<input type="password" class="form-control" id="generatedPassword" name="fpassword"
												value="<?php echo $row['FacultyPassword']; ?>" readonly>
											<div class="input-group-append">
												<button class="btn change-password-btn" type="button" id="changePasswordBtn" onclick="togglePasswordEdit()">
													<i class="fe fe-edit"></i> Change
												</button>
												<button class="btn btn-primary" type="button" onclick="copyToClipboard('generatedPassword')" id="copyPasswordBtn">
													<i class="fe fe-copy"></i>
												</button>
											</div>
										</div>
										<div id="passwordRequirements" class="password-requirements">
											<h6>Password Requirements:</h6>
											<div class="password-requirement" id="length-req">
												<i class="fe fe-x"></i> At least 8 characters
											</div>
											<div class="password-requirement" id="uppercase-req">
												<i class="fe fe-x"></i> At least one uppercase letter
											</div>
											<div class="password-requirement" id="lowercase-req">
												<i class="fe fe-x"></i> At least one lowercase letter
											</div>
											<div class="password-requirement" id="number-req">
												<i class="fe fe-x"></i> At least one number
											</div>
											<div class="password-requirement" id="special-req">
												<i class="fe fe-x"></i> At least one special character
											</div>
										</div>
										<!-- Hidden field to track password changes -->
										<input type="hidden" id="passwordChanged" name="passwordChanged" value="0">
										<!-- Hidden field to store original password -->
										<input type="hidden" id="originalPassword" value="<?php echo $row['FacultyPassword']; ?>">
									</div>
								</div>
							</div>

							<div class="d-flex justify-content-between">
								<!-- Generate New Password Button -->
								<button class="btn btn-outline-secondary" type="button" onclick="generateNewPassword()" id="generatePasswordBtn" style="display: none;">
									<i class="fe fe-refresh-cw"></i> Generate New Password
								</button>
								<!-- Save Button -->
								<button class="btn btn-primary" type="submit" value="sub" name="subbed" id="submitBtn">
									<i class="fe fe-save"></i> Save Changes
								</button>
							</div>
							<!-- / .row -->
					</form>
			</div>
			<!-- Card Wrapper End -->


		<?php
				} else { ?>
			<form class="mb-4" method="post">
				<div class="row">
					<div class="col-md-10">
						<div class="input-group input-group-merge input-group-reverse">
							<input class="form-control list-search" type="text" name="enr" placeholder="Enter Faculty Code" required>
							<div class="input-group-text">
								<span class="fe fe-search"></span>
							</div>
						</div>
					</div>
					<div class="col-md-2">
						<div class="col-auto">
							<!-- Button -->
							<button class="btn btn-primary " type="submit" name="ser" value="2">
								Search
							</button>
						</div>
					</div>
				</div>
			</form>

			<?php
					if (isset($_POST['ser'])) {
						$er = $_POST['enr'];
						$er = mysqli_real_escape_string($conn, $er);
						$qur = "SELECT * FROM facultymaster WHERE FacultyCode = '$er';";
						$res = mysqli_query($conn, $qur);
						$row = mysqli_fetch_assoc($res);
						if (isset($row)) { ?>
					<hr class="navbar-divider my-4">
					<div class="card">
						<div class="card-body">
							<div class="row align-items-center">
								<div class="col-auto">
									<a href="profile-posts.html" class="avatar avatar-lg">
										<img src="../src/uploads/facprofile/<?php echo $row['FacultyProfilePic'] . "?t"; ?>" alt="..." class="avatar-img rounded-circle">
									</a>
								</div>
								<div class="col ml-n2">
									<!-- Title -->
									<h4 class="mb-1">
										<a href="profile-posts.html"><?php echo $row['FacultyFirstName'] . " " . $row['FacultyLastName']; ?></a>
									</h4>
									<!-- Text -->
									<p class="small mb-1">
										<?php echo $row['FacultyCode']; ?>
									</p>
									<!-- Status -->
									<p class="small mb-1">
										<?php echo $row['FacultyBranch']; ?>
									</p>
								</div>
								<div class="col-auto">
									<!-- Button -->
									<a href="edit_faculty.php?facid=<?php echo $row['FacultyId']; ?>" class="btn btn-m btn-primary d-none d-md-inline-block">
										Edit
									</a>
								</div>
							</div> <!-- / .row -->
						</div> <!-- / .card-body -->
					</div>
	<?php
						} else {
							echo '<div class="alert alert-warning">No faculty found with the provided code.</div>';
						}
					}
				}
			}
	?>
	<br>
		</div>
		</div>
		<!-- / .row -->
		</div>
		</div>
		<?php include_once("context.php"); ?>

		<!-- JavaScript -->
		<script>
			let passwordEditMode = false;
			let currentPassword = '';
			let originalPassword = document.getElementById('originalPassword').value;

			// Preview Profile Picture
			function showPreview(event) {
				var file = document.getElementById('img');
				if (file.files.length > 0) {
					for (var i = 0; i <= file.files.length - 1; i++) {
						var fsize = file.files.item(i).size;
					}
					if (fsize <= 2000000) {
						var src = URL.createObjectURL(event.target.files[0]);
						var preview = document.getElementById("IMG-preview");
						preview.src = src;
						preview.style.display = "block";
					} else {
						alert("File size must be less than 2MB!");
						file.value = '';
					}
				}
			}

			// Toggle Password Edit Mode
			function togglePasswordEdit() {
				const passwordField = document.getElementById('generatedPassword');
				const changeBtn = document.getElementById('changePasswordBtn');
				const generateBtn = document.getElementById('generatePasswordBtn');
				const copyBtn = document.getElementById('copyPasswordBtn');
				const requirements = document.getElementById('passwordRequirements');
				const passwordChangedField = document.getElementById('passwordChanged');

				if (!passwordEditMode) {
					passwordField.removeAttribute('readonly');
					passwordField.setAttribute('type', 'text');
					passwordField.value = '';
					passwordField.placeholder = 'Enter new password';
					changeBtn.innerHTML = '<i class="fe fe-x"></i> Cancel';
					changeBtn.classList.remove('change-password-btn');
					changeBtn.classList.add('btn-danger');
					generateBtn.style.display = 'inline-block';
					copyBtn.style.display = 'none';
					requirements.style.display = 'block';
					passwordEditMode = true;
					passwordChangedField.value = '1';
					generateNewPassword();
				} else {
					passwordField.setAttribute('readonly', true);
					passwordField.setAttribute('type', 'password');
					passwordField.value = originalPassword;
					passwordField.placeholder = '';
					changeBtn.innerHTML = '<i class="fe fe-edit"></i> Change';
					changeBtn.classList.remove('btn-danger');
					changeBtn.classList.add('change-password-btn');
					generateBtn.style.display = 'none';
					copyBtn.style.display = 'inline-block';
					requirements.style.display = 'none';
					passwordEditMode = false;
					passwordChangedField.value = '0';
					clearPasswordValidation();
				}
			}

			// Generate New Password
			function generateNewPassword() {
				const length = 12;
				const uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				const lowercase = 'abcdefghijklmnopqrstuvwxyz';
				const numbers = '0123456789';
				const special = '!@#$%^&*()_+-=[]{}|;:,.<>?';

				let password = '';
				password += uppercase.charAt(Math.floor(Math.random() * uppercase.length));
				password += lowercase.charAt(Math.floor(Math.random() * lowercase.length));
				password += numbers.charAt(Math.floor(Math.random() * numbers.length));
				password += special.charAt(Math.floor(Math.random() * special.length));

				const allChars = uppercase + lowercase + numbers + special;
				for (let i = 4; i < length; i++) {
					password += allChars.charAt(Math.floor(Math.random() * allChars.length));
				}

				password = password.split('').sort(() => Math.random() - 0.5).join('');
				document.getElementById('generatedPassword').value = password;
				document.getElementById('passwordChanged').value = '1';
				currentPassword = password;
				validatePassword();
			}

			// Validate Password Requirements
			function validatePassword() {
				const password = document.getElementById('generatedPassword').value;
				const passwordField = document.getElementById('generatedPassword');

				const hasLength = password.length >= 8;
				const hasUppercase = /[A-Z]/.test(password);
				const hasLowercase = /[a-z]/.test(password);
				const hasNumber = /[0-9]/.test(password);
				const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);

				updateRequirement('length-req', hasLength);
				updateRequirement('uppercase-req', hasUppercase);
				updateRequirement('lowercase-req', hasLowercase);
				updateRequirement('number-req', hasNumber);
				updateRequirement('special-req', hasSpecial);

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

				currentPassword = password;
				return allRequirementsMet;
			}

			// Update Individual Requirement Status
			function updateRequirement(elementId, isValid) {
				const element = document.getElementById(elementId);
				const icon = element.querySelector('i');

				if (isValid) {
					element.classList.add('valid');
					icon.classList.remove('fe-x');
					icon.classList.add('fe-check');
				} else {
					element.classList.remove('valid');
					icon.classList.remove('fe-check');
					icon.classList.add('fe-x');
				}
			}

			// Clear Password Validation
			function clearPasswordValidation() {
				const passwordField = document.getElementById('generatedPassword');
				passwordField.classList.remove('is-valid', 'is-invalid');

				const requirements = ['length-req', 'uppercase-req', 'lowercase-req', 'number-req', 'special-req'];
				requirements.forEach(req => {
					const element = document.getElementById(req);
					element.classList.remove('valid');
					const icon = element.querySelector('i');
					icon.classList.remove('fe-check');
					icon.classList.add('fe-x');
				});
			}

			// Copy to Clipboard
			function copyToClipboard(elementId) {
				const element = document.getElementById(elementId);
				let textToCopy = element.value;

				// If copying password in edit mode, use current password
				if (elementId === 'generatedPassword' && passwordEditMode) {
					textToCopy = currentPassword;
				}

				if (elementId === 'generatedPassword' && !passwordEditMode) {
					// Copy the actual password from database even if it's hidden
					textToCopy = originalPassword;
				}

				navigator.clipboard.writeText(textToCopy).then(function() {
					alert('Copied: ' + textToCopy);
				}).catch(function() {
					// Fallback for older browsers
					const tempInput = document.createElement('input');
					tempInput.value = textToCopy;
					document.body.appendChild(tempInput);
					tempInput.select();
					tempInput.setSelectionRange(0, 99999);
					document.execCommand('copy');
					document.body.removeChild(tempInput);
					alert('Copied: ' + textToCopy);
				});
			}

			// Validation Functions
			function validateName(input) {
				const value = input.value;
				const errorElement = document.getElementById(input.id + '-error');

				if (value && !/^[A-Za-z\s]+$/.test(value)) {
					input.classList.add('is-invalid');
					errorElement.textContent = 'Only letters and spaces are allowed';
					errorElement.style.display = 'block';
					return false;
				} else {
					input.classList.remove('is-invalid');
					errorElement.style.display = 'none';
					return true;
				}
			}

			function validatePhone(input) {
				const value = input.value;
				const errorElement = document.getElementById('contact-error');

				if (value && !/^\d{10}$/.test(value)) {
					input.classList.add('is-invalid');
					errorElement.textContent = 'Please enter a valid 10-digit phone number';
					errorElement.style.display = 'block';
					return false;
				} else {
					input.classList.remove('is-invalid');
					errorElement.style.display = 'none';
					return true;
				}
			}

			function validateOffice(input) {
				const value = input.value.trim();
				const errorElement = document.getElementById('office-error');

				if (value.length < 2) {
					input.classList.add('is-invalid');
					errorElement.textContent = 'Office location must be at least 2 characters';
					errorElement.style.display = 'block';
					return false;
				} else {
					input.classList.remove('is-invalid');
					errorElement.style.display = 'none';
					return true;
				}
			}

			function validateQualification(input) {
				const value = input.value.trim();
				const errorElement = document.getElementById('qualification-error');

				if (value.length < 2) {
					input.classList.add('is-invalid');
					errorElement.textContent = 'Qualification must be at least 2 characters';
					errorElement.style.display = 'block';
					return false;
				} else {
					input.classList.remove('is-invalid');
					errorElement.style.display = 'none';
					return true;
				}
			}

			// Form Validation
			function validateForm() {
				let isValid = true;

				// Validate all required fields
				const fname = document.getElementById('fname');
				const lname = document.getElementById('lname');
				const fcontact = document.getElementById('fcontact');
				const foffice = document.getElementById('foffice');
				const fqualification = document.getElementById('fqualification');
				const fbranch = document.getElementById('fbranch');
				const fsection = document.getElementById('fsection');

				if (!validateName(fname)) isValid = false;
				if (!validateName(lname)) isValid = false;
				if (!validatePhone(fcontact)) isValid = false;
				if (!validateOffice(foffice)) isValid = false;
				if (!validateQualification(fqualification)) isValid = false;

				if (!fbranch.value) {
					fbranch.classList.add('is-invalid');
					isValid = false;
				} else {
					fbranch.classList.remove('is-invalid');
				}

				if (!fsection.value) {
					fsection.classList.add('is-invalid');
					isValid = false;
				} else {
					fsection.classList.remove('is-invalid');
				}

				// Validate password if in edit mode
				if (passwordEditMode) {
					if (!validatePassword()) {
						alert('Please ensure the password meets all requirements');
						isValid = false;
					}
				}

				if (!isValid) {
					alert('Please correct the errors in the form');
					return false;
				}

				return true;
			}

			// Event listener for password input
			document.getElementById('generatedPassword').addEventListener('input', function() {
				if (passwordEditMode) {
					validatePassword();
				}
			});

			// Initialize form
			document.addEventListener('DOMContentLoaded', function() {
				// Set initial password state
				currentPassword = originalPassword;
			});
		</script>

		<?php
		// Handle form submission
		if (isset($_POST['subbed'])) {
			$fname = mysqli_real_escape_string($conn, $_POST['fname']);
			$mname = mysqli_real_escape_string($conn, $_POST['mname']);
			$lname = mysqli_real_escape_string($conn, $_POST['lname']);
			$femail = mysqli_real_escape_string($conn, $_POST['femail']);
			$fcontact = mysqli_real_escape_string($conn, $_POST['fcontact']);
			$foffice = mysqli_real_escape_string($conn, $_POST['foffice']);
			$fbranch = mysqli_real_escape_string($conn, $_POST['fbranch']);
			$fsection = mysqli_real_escape_string($conn, $_POST['fsection']);
			$fqualification = mysqli_real_escape_string($conn, $_POST['fqualification']);
			$fcode = mysqli_real_escape_string($conn, $_POST['fcode']);
			$passwordChanged = $_POST['passwordChanged'];

			// Handle password update
			$passwordUpdate = "";
			if ($passwordChanged == '1') {
				$newPassword = mysqli_real_escape_string($conn, $_POST['fpassword']);
				// Hash the new password
				$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
				$passwordUpdate = ", FacultyPassword = '$hashedPassword'";
			}

			// Handle file upload
			$profilePicUpdate = "";
			if (isset($_FILES['stuprofile']) && $_FILES['stuprofile']['error'] == 0) {
				$targetDir = "../src/uploads/facprofile/";
				$fileName = time() . '_' . basename($_FILES['stuprofile']['name']);
				$targetFile = $targetDir . $fileName;
				$imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

				// Check if image file is valid
				$check = getimagesize($_FILES['stuprofile']['tmp_name']);
				if ($check !== false) {
					// Check file size (2MB limit)
					if ($_FILES['stuprofile']['size'] <= 2000000) {
						// Allow certain file formats
						if ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg") {
							if (move_uploaded_file($_FILES['stuprofile']['tmp_name'], $targetFile)) {
								$profilePicUpdate = ", FacultyProfilePic = '$fileName'";
							}
						}
					}
				}
			}

			// Update faculty record
			$updateQuery = "UPDATE facultymaster SET 
							FacultyFirstName = '$fname',
							FacultyMiddleName = '$mname',
							FacultyLastName = '$lname',
							FacultyEmail = '$femail',
							FacultyContactNo = '$fcontact',
							FacultyOffice = '$foffice',
							FacultyBranchCode = '$fbranch',
							FacultySection = '$fsection',
							FacultyQualification = '$fqualification'
							$passwordUpdate
							$profilePicUpdate
							WHERE FacultyId = '$studentenr'";

			if (mysqli_query($conn, $updateQuery)) {
				echo "<script>alert('Faculty details updated successfully!'); window.location.href = 'edit_faculty.php?facid=$studentenr';</script>";
			} else {
				echo "<script>alert('Error updating faculty details: " . mysqli_error($conn) . "');</script>";
			}
		}
		?>

		<!-- JAVASCRIPT -->
		<?php include_once("../script.php"); ?>
	</body>

	</html>
	<?php

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
        $nav_role = "Student";
        include_once("../nav.php"); ?>
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-12 col-xl-10">
                        <!-- Header -->
                        <div class="header mt-md-5">
                            <div class="header-body">
                                <div class="row align-items-center">
                                    <!-- Left: Title and Subtitles -->
                                    <div class="col">
                                        <h6 class="header-pretitle">
                                            Edit
                                        </h6>
                                        <h1 class="header-title">
                                            Student Details
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

                    </div>
                    <!-- Form -->
                    <?php
                    include_once("../config.php");
                    $studentenr = $_GET['studentenr'];
                    $studentenr = mysqli_real_escape_string($conn, $studentenr);
                    $_SESSION["userrole"] = "Faculty";
                    if (isset($studentenr)) {
                        $sql = "SELECT * FROM studentmaster WHERE StudentEnrollmentNo = '$studentenr'";
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
                                                <img id="IMG-preview" class="avatar-img rounded-circle" src="../src/uploads/stuprofile/<?php echo $row['StudentProfilePic'] . "?t"; ?>" alt="...">
                                            </div>
                                        </div>
                                        <div class="col ml-n2">
                                            <!-- Heading -->
                                            <h4 class="mb-1">
                                                Student Photo
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
                                    <input type="file" id="img" name="stuprofile" class="btn btn-sm" onchange="showPreview(event);" accept="image/jpg, image/jpeg, image/png">
                                </div>
                            </div>
                            <!-- / .row -->
                            <!-- Divider -->
                            <hr class="my-5">
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <!-- First name -->
                                    <div class="form-group">
                                        <!-- Label -->
                                        <label class="form-label">
                                            First name <span class="text-danger">*</span>
                                        </label>
                                        <!-- Input -->
                                        <input type="text" class="form-control" id="fname" name="fname" value="<?php echo $row['StudentFirstName']; ?>" required oninput="validateName(this)">
                                        <div id="fname-error" class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <!-- Middle name -->
                                    <div class="form-group">
                                        <!-- Label -->
                                        <label class="form-label">
                                            Middle name
                                        </label>
                                        <!-- Input -->
                                        <input type="text" class="form-control" id="mname" name="mname" value="<?php echo $row['StudentMiddleName']; ?>" oninput="validateName(this)">
                                        <div id="mname-error" class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <!-- Last name -->
                                    <div class="form-group">
                                        <!-- Label -->
                                        <label class="form-label">
                                            Last name <span class="text-danger">*</span>
                                        </label>
                                        <!-- Input -->
                                        <input type="text" class="form-control" id="lname" name="lname" value="<?php echo $row['StudentLastName']; ?>" required oninput="validateName(this)">
                                        <div id="lname-error" class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <!-- / .row -->
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <!-- Email address -->
                                    <div class="form-group">
                                        <!-- Label -->
                                        <label class="form-label">
                                            Student Email address <span class="text-danger">*</span>
                                        </label>
                                        <!-- Input -->
                                        <input type="email" class="form-control" id="semail" name="semail" value="<?php echo $row['StudentEmail']; ?>" required readonly style="background-color: #f8f9fa;">
                                        <small class="text-muted">Email cannot be changed</small>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">
                                        Student Contact Number <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel" pattern="[0-9]{10}" class="form-control" id="scontact" maxlength="10" name="scontact" value="<?php echo $row['StudentContactNo']; ?>" required oninput="validatePhone(this)">
                                    <div id="scontact-error" class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <!-- Email address -->
                                    <div class="form-group">
                                        <!-- Label -->
                                        <label class="form-label">
                                            Student Address <span class="text-danger">*</span>
                                        </label>
                                        <!-- Input -->
                                        <input type="text" class="form-control" id="add" name="add" value="<?php echo $row['StudentAddress']; ?>" required oninput="validateAddress(this)">
                                        <div id="add-error" class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <!-- Email address -->
                                    <div class="form-group">
                                        <!-- Label -->
                                        <label class="form-label">
                                            Date of Birth <span class="text-danger">*</span>
                                        </label>
                                        <!-- Input -->
                                        <input type="date" class="form-control" id="dob" name="dob" required data-flatpickr value="<?php echo $row['StudentDOB']; ?>" placeholder="YYYY-MM-DD" onchange="validateAge(this)">
                                        <div id="dob-error" class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <!-- / Personal details-->
                            <hr class="my-5">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            Parent's Contact Number <span class="text-danger">*</span>
                                        </label>
                                        <input type="tel" class="form-control" id="pcontact" pattern="[0-9]{10}" maxlength="10" name="pcontact" value="<?php echo $row['ParentContactNo']; ?>" required oninput="validatePhone(this)">
                                        <div id="pcontact-error" class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">
                                        Parent's Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control" id="pmail" name="pmail" value="<?php echo $row['ParentEmail']; ?>" required oninput="validateEmail(this)">
                                    <div id="pmail-error" class="invalid-feedback"></div>
                                </div>
                            </div>
                            <hr class="my-5">
                            <!-- / .row -->
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            Student Enrollment No <span class="text-danger">*</span>
                                        </label>
                                        <input type="tel" pattern="[0-9]{12}" id="myInput" onchange="cp()" oninput="cp(); validateEnrollment(this);" class="form-control" name="senr" value="<?php echo $row['StudentEnrollmentNo']; ?>" required readonly style="background-color: #f8f9fa;">
                                        <small class="text-muted">Enrollment number cannot be changed</small>
                                        <div id="senr-error" class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            Student Roll No <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" maxlength="3" class="form-control" id="sroll" name="sroll" value="<?php echo $row['StudentRollNo']; ?>" required oninput="validateRollNo(this)">
                                        <div id="sroll-error" class="invalid-feedback"></div>
                                    </div>
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
                                    <div class="form-group">
                                        <label class="form-label">
                                            Student Grade Level <span class="text-danger">*</span>
                                        </label>
                                        <select id="sbranch" class="form-control" name="sbranch" required>
                                            <option value="" hidden="">Select Branch</option>
                                            <?php
                                            while ($brrow = mysqli_fetch_assoc($branchresult)) { ?>
                                                <option <?php if ($brrow['BranchCode'] == $row['StudentBranchCode']) { ?> selected <?php } ?> value="<?php echo $brrow['BranchCode']; ?>">
                                                    <?php echo $brrow['BranchName']; ?>
                                                </option>
                                            <?php
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        Student Semester <span class="text-danger">*</span>
                                        <input readonly type="email" class="form-control" value="<?php echo $row['StudentSemester']; ?>" required oninput="validateEmail(this)">
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">
                                            Student Section <span class="text-danger">*</span>
                                        </label>
                                        <select id="ssection" class="form-control" name="ssection" required>
                                            <option value="" hidden="">Select Section</option>
                                            <?php
                                            while ($secrow = mysqli_fetch_assoc($sectionresult)) { ?>
                                                <option <?php if ($secrow['SectionId'] == $row['StudentSection']) { ?> selected <?php } ?> value="<?php echo $secrow['SectionId']; ?>">
                                                    <?php echo $secrow['SectionNumber'] . ' ' . '-' . ' ' . $secrow['SectionBranch']; ?>
                                                </option>
                                            <?php
                                            } ?>
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <hr class="mt-4 mb-5">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <p class="form-label">
                                            Student Login ID
                                        </p>
                                    </div>
                                </div>
                                <div class="col-auto col-6">
                                    <div class="input-group input-group-sm mb-3 ">
                                        <textarea id="demo" class="form-control fs-2" name="slogin" readonly maxlength="4" style="background-color: #f8f9fa;"><?php echo $row['StudentEnrollmentNo']; ?></textarea>
                                        <button type="button" class="btn btn-primary" onclick="copyToClipboard('demo')"><i class="fe fe-copy"></i></button>
                                    </div>
                                    <small class="text-muted">Auto-generated from enrollment number</small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <p class="form-label">
                                            Student Password
                                        </p>
                                    </div>
                                </div>
                                <div class="col-auto col-6">
                                    <div class="input-group input-group-sm mb-3">
                                        <input type="password" id="generatedPassword" class="form-control" name="spassword" value="<?php echo $row['StudentPassword']; ?>" readonly style="background-color: #f8f9fa;">
                                        <button type="button" class="btn btn-primary" onclick="copyToClipboard('generatedPassword')"><i class="fe fe-copy"></i></button>
                                        <button type="button" class="btn change-password-btn" id="changePasswordBtn" onclick="togglePasswordEdit()">
                                            <i class="fe fe-edit"></i> Change
                                        </button>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-secondary" id="generatePasswordBtn" onclick="generateNewPassword()" style="display: none;">
                                        <i class="fe fe-refresh-cw"></i> Generate New
                                    </button>

                                    <!-- Password Requirements -->
                                    <div id="passwordRequirements" class="password-requirements">
                                        <h6>Password Requirements:</h6>
                                        <div id="length-req" class="password-requirement">
                                            <i class="fe fe-x"></i> At least 8 characters
                                        </div>
                                        <div id="uppercase-req" class="password-requirement">
                                            <i class="fe fe-x"></i> At least one uppercase letter
                                        </div>
                                        <div id="lowercase-req" class="password-requirement">
                                            <i class="fe fe-x"></i> At least one lowercase letter
                                        </div>
                                        <div id="number-req" class="password-requirement">
                                            <i class="fe fe-x"></i> At least one number
                                        </div>
                                        <div id="special-req" class="password-requirement">
                                            <i class="fe fe-x"></i> At least one special character
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify">
                                <!-- Button -->
                                <button class="btn btn-primary" style="margin-bottom: 30px" type="submit" value="sub" name="subbed">
                                    Save Changes
                                </button>
                            </div>
                            <!-- / .row -->
                        </form>
                    <?php
                    } else { ?>
                        <form class="mb-4" method="post">
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="input-group input-group-merge input-group-reverse">
                                        <input class="form-control list-search" type="text" name="enr" placeholder="Enter Student Enrollment Number">
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
                        </form>
                        <?php
                        if (isset($_POST['ser'])) {
                            $er = $_POST['enr'];
                            $qur = "SELECT * FROM studentmaster WHERE StudentEnrollmentNo = '$er';";
                            $res = mysqli_query($conn, $qur);
                            $row = mysqli_fetch_assoc($res);
                            if (isset($row)) { ?>
                                <hr class="navbar-divider my-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <a href="profile-posts.html" class="avatar avatar-lg">
                                                    <img src="../src/uploads/stuprofile/<?php echo $row['StudentProfilePic'] . "?t"; ?>" alt="..." class="avatar-img rounded-circle">
                                                </a>
                                            </div>
                                            <div class="col ml-n2">
                                                <!-- Title -->
                                                <h4 class="mb-1">
                                                    <a href="profile-posts.html"><?php echo $row['StudentFirstName'] . " " . $row['StudentLastName']; ?></a>
                                                </h4>
                                                <!-- Text -->
                                                <p class="small mb-1">
                                                    <?php echo $row['StudentEnrollmentNo']; ?>
                                                </p>
                                                <!-- Status -->
                                                <p class="small mb-1">
                                                    <?php echo $row['StudentRollNo']; ?>
                                                </p>
                                            </div>
                                            <div class="col-auto">
                                                <!-- Button -->
                                                <a href="edit_student.php?studentenr=<?php echo $row['StudentEnrollmentNo']; ?>" class="btn btn-m btn-primary d-none d-md-inline-block">
                                                    Edit
                                                </a>
                                            </div>
                                        </div>
                                        <!-- / .row -->
                                    </div>
                                    <!-- / .card-body -->
                                </div>
                <?php
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

        <!-- JavaScript -->
        <script>
            let passwordEditMode = false;
            let originalPassword = '<?php echo isset($row['StudentPassword']) ? $row['StudentPassword'] : ''; ?>';

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
                const requirements = document.getElementById('passwordRequirements');

                if (!passwordEditMode) {
                    passwordField.removeAttribute('readonly');
                    passwordField.setAttribute('type', 'text');
                    passwordField.value = '';
                    passwordField.style.backgroundColor = 'white';
                    changeBtn.innerHTML = '<i class="fe fe-x"></i> Cancel';
                    changeBtn.classList.remove('change-password-btn');
                    changeBtn.classList.add('btn-danger');
                    generateBtn.style.display = 'inline-block';
                    requirements.style.display = 'block';
                    passwordEditMode = true;
                    generateNewPassword();
                } else {
                    passwordField.setAttribute('readonly', true);
                    passwordField.setAttribute('type', 'password');
                    passwordField.value = originalPassword;
                    passwordField.style.backgroundColor = '#f8f9fa';
                    changeBtn.innerHTML = '<i class="fe fe-edit"></i> Change';
                    changeBtn.classList.remove('btn-danger');
                    changeBtn.classList.add('change-password-btn');
                    generateBtn.style.display = 'none';
                    requirements.style.display = 'none';
                    passwordEditMode = false;
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
                element.select();
                element.setSelectionRange(0, 99999);
                navigator.clipboard.writeText(element.value).then(function() {
                    alert('Copied: ' + element.value);
                });
            }

            // Validation Functions
            function validateName(input) {
                const value = input.value.trim();
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
                const errorElement = document.getElementById(input.id + '-error');

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

            function validateEmail(input) {
                const value = input.value.trim();
                const errorElement = document.getElementById(input.id + '-error');
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                if (value && !emailRegex.test(value)) {
                    input.classList.add('is-invalid');
                    errorElement.textContent = 'Please enter a valid email address';
                    errorElement.style.display = 'block';
                    return false;
                } else {
                    input.classList.remove('is-invalid');
                    errorElement.style.display = 'none';
                    return true;
                }
            }

            function validateAddress(input) {
                const value = input.value.trim();
                const errorElement = document.getElementById(input.id + '-error');

                if (value && value.length < 5) {
                    input.classList.add('is-invalid');
                    errorElement.textContent = 'Address must be at least 5 characters long';
                    errorElement.style.display = 'block';
                    return false;
                } else {
                    input.classList.remove('is-invalid');
                    errorElement.style.display = 'none';
                    return true;
                }
            }

            function validateAge(input) {
                const value = input.value;
                const errorElement = document.getElementById(input.id + '-error');

                if (value) {
                    const today = new Date();
                    const birthDate = new Date(value);
                    const age = today.getFullYear() - birthDate.getFullYear();
                    const monthDiff = today.getMonth() - birthDate.getMonth();

                    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                        age--;
                    }

                    if (age < 16 || age > 35) {
                        input.classList.add('is-invalid');
                        errorElement.textContent = 'Age must be between 16 and 35 years';
                        errorElement.style.display = 'block';
                        return false;
                    } else {
                        input.classList.remove('is-invalid');
                        errorElement.style.display = 'none';
                        return true;
                    }
                }
                return true;
            }

            function validateEnrollment(input) {
                const value = input.value;
                const errorElement = document.getElementById(input.id + '-error');

                if (value && !/^\d{12}$/.test(value)) {
                    input.classList.add('is-invalid');
                    errorElement.textContent = 'Enrollment number must be exactly 12 digits';
                    errorElement.style.display = 'block';
                    return false;
                } else {
                    input.classList.remove('is-invalid');
                    errorElement.style.display = 'none';
                    return true;
                }
            }

            function validateRollNo(input) {
                const value = input.value.trim();
                const errorElement = document.getElementById(input.id + '-error');

                if (value && !/^\d{1,3}$/.test(value)) {
                    input.classList.add('is-invalid');
                    errorElement.textContent = 'Roll number must be 1-3 digits';
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

                // Validate all form fields
                const fname = document.getElementById('fname');
                const mname = document.getElementById('mname');
                const lname = document.getElementById('lname');
                const scontact = document.getElementById('scontact');
                const pcontact = document.getElementById('pcontact');
                const pmail = document.getElementById('pmail');
                const add = document.getElementById('add');
                const dob = document.getElementById('dob');
                const sroll = document.getElementById('sroll');
                const generatedPassword = document.getElementById('generatedPassword');

                if (!validateName(fname)) isValid = false;
                if (mname.value && !validateName(mname)) isValid = false;
                if (!validateName(lname)) isValid = false;
                if (!validatePhone(scontact)) isValid = false;
                if (!validatePhone(pcontact)) isValid = false;
                if (!validateEmail(pmail)) isValid = false;
                if (!validateAddress(add)) isValid = false;
                if (!validateAge(dob)) isValid = false;
                if (!validateRollNo(sroll)) isValid = false;

                // If password is in edit mode, validate it
                if (passwordEditMode) {
                    if (!validatePassword()) {
                        isValid = false;
                        alert('Please ensure the password meets all requirements');
                    }
                }

                if (!isValid) {
                    alert('Please correct the errors in the form before submitting');
                }

                return isValid;
            }

            // Add event listeners for real-time password validation
            document.getElementById('generatedPassword').addEventListener('input', function() {
                if (passwordEditMode) {
                    validatePassword();
                }
            });

            // Copy function (if not already defined)
            function cp() {
                // This function seems to be referenced but not defined in the provided code
                // Add implementation if needed
            }
        </script>

        <?php
        // PHP Processing for form submission
        if (isset($_POST['subbed'])) {
            $fname = mysqli_real_escape_string($conn, $_POST['fname']);
            $mname = mysqli_real_escape_string($conn, $_POST['mname']);
            $lname = mysqli_real_escape_string($conn, $_POST['lname']);
            $scontact = mysqli_real_escape_string($conn, $_POST['scontact']);
            $pcontact = mysqli_real_escape_string($conn, $_POST['pcontact']);
            $pmail = mysqli_real_escape_string($conn, $_POST['pmail']);
            $add = mysqli_real_escape_string($conn, $_POST['add']);
            $dob = mysqli_real_escape_string($conn, $_POST['dob']);
            $sroll = mysqli_real_escape_string($conn, $_POST['sroll']);
            $sbranch = mysqli_real_escape_string($conn, $_POST['sbranch']);
            $ssem = mysqli_real_escape_string($conn, $_POST['ssem']);
            $ssection = mysqli_real_escape_string($conn, $_POST['ssection']);
            $senr = mysqli_real_escape_string($conn, $_POST['senr']);
            $spassword = mysqli_real_escape_string($conn, $_POST['spassword']);

            // Hash the password if it's been changed
            $hashedPassword = password_hash($spassword, PASSWORD_DEFAULT);

            // Handle file upload
            $uploadedFile = '';
            if (isset($_FILES['stuprofile']) && $_FILES['stuprofile']['error'] == 0) {
                $targetDir = "../src/uploads/stuprofile/";
                $fileName = time() . '_' . basename($_FILES['stuprofile']['name']);
                $targetFilePath = $targetDir . $fileName;
                $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

                // Allow certain file formats
                $allowedTypes = array('jpg', 'jpeg', 'png');
                if (in_array(strtolower($fileType), $allowedTypes)) {
                    if (move_uploaded_file($_FILES['stuprofile']['tmp_name'], $targetFilePath)) {
                        $uploadedFile = $fileName;
                    }
                }
            }

            // Update query
            $updateQuery = "UPDATE studentmaster SET 
                StudentFirstName = '$fname',
                StudentMiddleName = '$mname',
                StudentLastName = '$lname',
                StudentContactNo = '$scontact',
                ParentContactNo = '$pcontact',
                ParentEmail = '$pmail',
                StudentAddress = '$add',
                StudentDOB = '$dob',
                StudentRollNo = '$sroll',
                StudentBranchCode = '$sbranch',
                StudentSemester = '$ssem',
                StudentSection = '$ssection',
                StudentPassword = '$hashedPassword'";

            if ($uploadedFile) {
                $updateQuery .= ", StudentProfilePic = '$uploadedFile'";
            }

            $updateQuery .= " WHERE StudentEnrollmentNo = '$senr'";

            if (mysqli_query($conn, $updateQuery)) {
                echo "<script>alert('Student details updated successfully!'); window.location.href = 'edit_student.php?studentenr=$senr';</script>";
            } else {
                echo "<script>alert('Error updating student details: " . mysqli_error($conn) . "');</script>";
            }
        }
        ?>

        <!-- Footer -->
        <?php include_once("../footer.php"); ?>
    </body>

    </html>
    <?php  ?>
<?php
session_start();
if ($_SESSION['role'] != "Texas") {
    header("Location: ../index.php");
    exit();
} else {
    include_once("../config.php");
    $_SESSION["userrole"] = "Institute";
}

$branchsel = "SELECT * FROM branchmaster";
$branchresult = mysqli_query($conn, $branchsel);
$sectionsel = "SELECT * FROM sectionmaster";
$sectionresult = mysqli_query($conn, $sectionsel);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once("../head.php"); ?>
    <style>
        body {
            background: linear-gradient(to top,
                    rgba(194, 171, 117, 0.8),
                    rgba(194, 171, 117, 0.4)),
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

        .password-requirements {
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        .password-requirements ul {
            margin: 0;
            padding-left: 1.2rem;
        }

        .password-requirements li {
            color: #dc3545;
            margin-bottom: 0.2rem;
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

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
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
                                <!-- Left: Titles -->
                                <div class="col">
                                    <h6 class="header-pretitle">
                                        Add New
                                    </h6>
                                    <h1 class="header-title">
                                        Student
                                    </h1>
                                </div>

                                <!-- Right: Back Button -->
                                <div class="col-auto">
                                    <button class="back-btn btn btn-outline-primary" onclick="history.back()">
                                        <i class="fe uil-angle-double-left"></i> Back
                                    </button>
                                </div>
                            </div>
                        </div>
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
                                        <img name="StuIMG" class="avatar-img rounded-circle" src="../assets/img/avatars/profiles/avatar-1.jpg" alt="..." id="IMG-preview">
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
                        </div>
                        <div class="col-auto">
                            <!-- Button -->
                            <input type="file" id="img" name="stuprofile" class="btn btn-sm" onchange="showPreview(event);" accept="image/jpg, image/jpeg, image/png" required>
                            <div class="invalid-feedback">
                                Please select a profile picture.
                            </div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <hr class="my-5">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="fname" class="form-label">First name</label>
                            <input type="text"
                                pattern="[A-Za-z\s]+"
                                title="Only letters and spaces allowed"
                                onkeypress='return ((event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122) || (event.charCode === 32))'
                                minlength="2"
                                maxlength="20"
                                class="form-control"
                                id="fname"
                                name="fname"
                                required>
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                            <div class="invalid-feedback">
                                Please enter a valid first name (2-20 characters, letters only).
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="mname" class="form-label">Middle name</label>
                                <input type="text"
                                    pattern="[A-Za-z\s]+"
                                    title="Only letters and spaces allowed"
                                    onkeypress='return ((event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122) || (event.charCode === 32))'
                                    minlength="2"
                                    maxlength="20"
                                    class="form-control"
                                    id="mname"
                                    name="mname"
                                    required>
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                                <div class="invalid-feedback">
                                    Please enter a valid middle name (2-20 characters, letters only).
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="lname" class="form-label">Last name</label>
                                <input type="text"
                                    pattern="[A-Za-z\s]+"
                                    title="Only letters and spaces allowed"
                                    onkeypress='return ((event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122) || (event.charCode === 32))'
                                    minlength="2"
                                    maxlength="20"
                                    class="form-control"
                                    id="lname"
                                    name="lname"
                                    required>
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                                <div class="invalid-feedback">
                                    Please enter a valid last name (2-20 characters, letters only).
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="semail" class="form-label">
                                    Student Email address
                                </label>
                                <input type="email"
                                    maxlength="50"
                                    class="form-control"
                                    id="semail"
                                    name="semail"
                                    onblur="checkEmailUnique(this.value)"
                                    required>
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                                <div class="invalid-feedback">
                                    Please enter a valid email address.
                                </div>
                                <div id="email-error" class="error-message"></div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="scontact" class="form-label">
                                Student Contact Number
                            </label>
                            <input type="tel"
                                pattern="[0-9]{10}"
                                title="Please enter a 10-digit phone number"
                                onkeypress="return event.charCode>=48 && event.charCode<=57"
                                maxlength="10"
                                minlength="10"
                                id="scontact"
                                class="form-control"
                                name="scontact"
                                required>
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                            <div class="invalid-feedback">
                                Please enter a valid 10-digit phone number.
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="add" class="form-label">
                                    Student Address
                                </label>
                                <textarea id="add"
                                    class="form-control"
                                    name="add"
                                    rows="3"
                                    minlength="10"
                                    maxlength="200"
                                    required></textarea>
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                                <div class="invalid-feedback">
                                    Please enter a valid address (10-200 characters).
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="dob" class="form-label">
                                    Date of Birth
                                </label>
                                <input type="date"
                                    id="dob"
                                    class="form-control"
                                    name="dob"
                                    max="<?php echo date('Y-m-d', strtotime('-16 years')); ?>"
                                    min="<?php echo date('Y-m-d', strtotime('-50 years')); ?>"
                                    required>
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                                <div class="invalid-feedback">
                                    Please enter a valid date of birth (age 16-50).
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Parent details -->
                    <hr class="my-5">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="pcontact" class="form-label">
                                    Parent's Contact Number
                                </label>
                                <input type="tel"
                                    pattern="[0-9]{10}"
                                    title="Please enter a 10-digit phone number"
                                    onkeypress="return event.charCode>=48 && event.charCode<=57"
                                    maxlength="10"
                                    minlength="10"
                                    id="pcontact"
                                    class="form-control"
                                    name="pcontact"
                                    required>
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                                <div class="invalid-feedback">
                                    Please enter a valid 10-digit phone number.
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="pmail" class="form-label">
                                Parent's Email
                            </label>
                            <input type="email"
                                maxlength="50"
                                id="pmail"
                                class="form-control"
                                name="pmail"
                                required>
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                            <div class="invalid-feedback">
                                Please enter a valid email address.
                            </div>
                        </div>
                    </div>

                    <hr class="my-5">
                    <!-- Student Academic Details -->
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="senr" class="form-label">
                                    Student Enrollment No
                                </label>
                                <input type="text"
                                    pattern="[0-9]{12}"
                                    title="Please enter a 12-digit enrollment number"
                                    oninput="generateCredentials()"
                                    onkeypress="return event.charCode>=48 && event.charCode<=57"
                                    maxlength="12"
                                    minlength="12"
                                    id="senr"
                                    class="form-control"
                                    name="senr"
                                    required>
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                                <div class="invalid-feedback">
                                    Please enter a valid 12-digit enrollment number.
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="sroll" class="form-label">
                                    Student Roll No
                                </label>
                                <input type="text"
                                    pattern="[0-9]{1,3}"
                                    title="Please enter a 1-3 digit roll number"
                                    onkeypress="return event.charCode>=48 && event.charCode<=57"
                                    maxlength="3"
                                    minlength="1"
                                    id="sroll"
                                    class="form-control"
                                    name="sroll"
                                    required>
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                                <div class="invalid-feedback">
                                    Please enter a valid roll number (1-3 digits).
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="sbranch" class="form-label">
                                    Student Grade Level
                                </label>
                                <select id="sbranch" class="form-control" name="sbranch" required>
                                    <option value="" hidden>Select Branch</option>
                                    <?php
                                    mysqli_data_seek($branchresult, 0);
                                    while ($brrow = mysqli_fetch_assoc($branchresult)) { ?>
                                        <option value="<?php echo htmlspecialchars($brrow['BranchCode']); ?>">
                                            <?php echo htmlspecialchars($brrow['BranchName']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                                <div class="invalid-feedback">
                                    Please select a branch.
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="ssem" class="form-label">
                                    Student Quarter
                                </label>
                                <input readonly type="email" class="form-control" value="4" required oninput="validateEmail(this)">
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                                <div class="invalid-feedback">
                                    Please select a semester.
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="ssection" class="form-label">
                                    Student Section
                                </label>
                                <select id="ssection" class="form-control" name="ssection" required>
                                    <option value="" hidden>Select Section</option>
                                    <?php
                                    mysqli_data_seek($sectionresult, 0);
                                    while ($secrow = mysqli_fetch_assoc($sectionresult)) { ?>
                                        <option value="<?php echo htmlspecialchars($secrow['SectionId']); ?>">
                                            <?php echo htmlspecialchars($secrow['SectionNumber'] . ' - ' . $secrow['SectionBranch']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                                <div class="invalid-feedback">
                                    Please select a section.
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="mt-4 mb-5">
                    <!-- Login Credentials -->
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="loginId" class="form-label">
                                    Student Login ID
                                </label>
                                <div class="input-group">
                                    <input type="text"
                                        class="form-control"
                                        id="loginId"
                                        name="loginId"
                                        readonly>
                                    <button type="button" class="btn btn-primary" onclick="copyToClipboard('loginId')">
                                        <i class="fe fe-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="generatedPassword" class="form-label">
                                    Student Password
                                </label>
                                <div class="input-group">
                                    <input type="text"
                                        class="form-control"
                                        id="generatedPassword"
                                        name="spassword"
                                        oninput="validatePassword()">
                                    <button type="button" class="btn btn-secondary" onclick="generateNewPassword()">
                                        <i class="fe fe-refresh-cw"></i>
                                    </button>
                                    <button type="button" class="btn btn-primary" onclick="copyToClipboard('generatedPassword')">
                                        <i class="fe fe-copy"></i>
                                    </button>
                                </div>
                                <div class="password-requirements">
                                    <small class="text-muted">Password must contain:</small>
                                    <ul>
                                        <li id="length-req">At least 8 characters</li>
                                        <li id="uppercase-req">At least one uppercase letter</li>
                                        <li id="lowercase-req">At least one lowercase letter</li>
                                        <li id="number-req">At least one number</li>
                                        <li id="special-req">At least one special character</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <hr class="mt-4 mb-5">
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary" style="margin-bottom: 30px" type="submit" name="subbed">
                            Add Student
                        </button>
                    </div>
                </form>
                <br>
            </div>
        </div>
    </div>
    </div>

    <?php include_once("context.php"); ?>

    <!-- Scripts -->
    <script src='https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.js'></script>
    <script src="../assets/js/vendor.bundle.js"></script>
    <script src="../assets/js/theme.bundle.js"></script>

    <script>
        // Image preview function
        function showPreview(event) {
            var file = document.getElementById('img');
            if (file.files.length > 0) {
                var fsize = file.files.item(0).size;
                var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                var fileType = file.files.item(0).type;

                if (!allowedTypes.includes(fileType)) {
                    alert("Only JPG, JPEG, and PNG files are allowed!");
                    file.value = '';
                    return;
                }

                if (fsize <= 2000000) {
                    var src = URL.createObjectURL(event.target.files[0]);
                    var preview = document.getElementById("IMG-preview");
                    preview.src = src;
                    preview.style.display = "block";

                    // Remove invalid class if present
                    file.classList.remove('is-invalid');
                } else {
                    alert("File size must be less than 2MB!");
                    file.value = '';
                }
            }
        }

        // Generate secure password
        function generateNewPassword() {
            const length = 12;
            const uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            const lowercase = 'abcdefghijklmnopqrstuvwxyz';
            const numbers = '0123456789';
            const special = '!@#$%^&*()_+-=[]{}|;:,.<>?';

            let password = '';

            // Ensure at least one character from each category
            password += uppercase.charAt(Math.floor(Math.random() * uppercase.length));
            password += lowercase.charAt(Math.floor(Math.random() * lowercase.length));
            password += numbers.charAt(Math.floor(Math.random() * numbers.length));
            password += special.charAt(Math.floor(Math.random() * special.length));

            // Fill the rest randomly
            const allChars = uppercase + lowercase + numbers + special;
            for (let i = 4; i < length; i++) {
                password += allChars.charAt(Math.floor(Math.random() * allChars.length));
            }

            // Shuffle the password
            password = password.split('').sort(() => Math.random() - 0.5).join('');

            document.getElementById('generatedPassword').value = password;
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

        // Generate login credentials
        function generateCredentials() {
            const enrollment = document.getElementById('senr').value;
            if (enrollment && enrollment.length === 12) {
                // Generate login ID
                const loginId = 'ST' + enrollment;
                document.getElementById('loginId').value = loginId;

                // Generate password if not already generated
                if (!document.getElementById('generatedPassword').value) {
                    generateNewPassword();
                }
            } else {
                document.getElementById('loginId').value = '';
            }
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

        // Check email uniqueness (you can implement AJAX call here)
        function checkEmailUnique(email) {
            if (email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    document.getElementById('email-error').style.display = 'block';
                    document.getElementById('email-error').textContent = 'Invalid email format';
                    document.getElementById('semail').classList.add('is-invalid');
                } else {
                    document.getElementById('email-error').style.display = 'none';
                    document.getElementById('semail').classList.remove('is-invalid');
                    // Here you can add AJAX call to check email uniqueness in database
                }
            }
        }

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

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            generateNewPassword();
        });
    </script>
</body>

</html>

<?php
if (isset($_POST['subbed'])) {
    // Validate and sanitize inputs
    $errors = [];

    // File upload validation
    if (isset($_FILES['stuprofile']) && $_FILES['stuprofile']['error'] !== UPLOAD_ERR_NO_FILE) {
        $f_tmp_name = $_FILES['stuprofile']['tmp_name'];
        $f_size = $_FILES['stuprofile']['size'];
        $f_error = $_FILES['stuprofile']['error'];
        $f_type = $_FILES['stuprofile']['type'];

        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];

        if ($f_error !== UPLOAD_ERR_OK) {
            $errors[] = "File upload error occurred.";
        } elseif (!in_array($f_type, $allowed_types)) {
            $errors[] = "Only JPG, JPEG, and PNG files are allowed.";
        } elseif ($f_size > 2000000) {
            $errors[] = "File size must be less than 2MB.";
        }
    } else {
        $errors[] = "Profile picture is required.";
    }

    // Sanitize and validate text inputs
    $fname = trim($_POST['fname'] ?? '');
    $mname = trim($_POST['mname'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $scontact = trim($_POST['scontact'] ?? '');
    $semail = trim($_POST['semail'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $pcontact = trim($_POST['pcontact'] ?? '');
    $pmail = trim($_POST['pmail'] ?? '');
    $ssem = trim($_POST['ssem'] ?? '');
    $senr = trim($_POST['senr'] ?? '');
    $sroll = trim($_POST['sroll'] ?? '');
    $sbranch = trim($_POST['sbranch'] ?? '');
    $ssection = trim($_POST['ssection'] ?? '');
    $spassword = trim($_POST['spassword'] ?? '');
    $add = trim($_POST['add'] ?? '');

    // Validation
    if (empty($fname) || !preg_match('/^[A-Za-z\s]{2,20}$/', $fname)) {
        $errors[] = "Invalid first name.";
    }
    if (empty($mname) || !preg_match('/^[A-Za-z\s]{2,20}$/', $mname)) {
        $errors[] = "Invalid middle name.";
    }
    if (empty($lname) || !preg_match('/^[A-Za-z\s]{2,20}$/', $lname)) {
        $errors[] = "Invalid last name.";
    }
    if (empty($semail) || !filter_var($semail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid student email.";
    }
    if (empty($scontact) || !preg_match('/^[0-9]{10}$/', $scontact)) {
        $errors[] = "Invalid student contact number.";
    }
    if (empty($pcontact) || !preg_match('/^[0-9]{10}$/', $pcontact)) {
        $errors[] = "Invalid parent contact number.";
    }
    if (empty($pmail) || !filter_var($pmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid parent email.";
    }
    if (empty($senr) || !preg_match('/^[0-9]{12}$/', $senr)) {
        $errors[] = "Invalid enrollment number.";
    }
    if (empty($sroll) || !preg_match('/^[0-9]{1,3}$/', $sroll)) {
        $errors[] = "Invalid roll number.";
    }
    if (empty($add) || strlen($add) < 10 || strlen($add) > 200) {
        $errors[] = "Invalid address.";
    }
    if (empty($dob) || !validateDate($dob)) {
        $errors[] = "Invalid date of birth.";
    }
    if (empty($spassword)) {
        $errors[] = "Password is required.";
    }

    // Validate password strength
    if (!validatePasswordStrength($spassword)) {
        $errors[] = "Password does not meet security requirements.";
    }

    // Check for duplicate enrollment number
    $check_enrollment = mysqli_prepare($conn, "SELECT StudentEnrollmentNo FROM studentmaster WHERE StudentEnrollmentNo = ?");
    mysqli_stmt_bind_param($check_enrollment, "s", $senr);
    mysqli_stmt_execute($check_enrollment);
    $result = mysqli_stmt_get_result($check_enrollment);
    if (mysqli_num_rows($result) > 0) {
        $errors[] = "Enrollment number already exists.";
    }

    // Check for duplicate email
    $check_email = mysqli_prepare($conn, "SELECT StudentEmail FROM studentmaster WHERE StudentEmail = ?");
    mysqli_stmt_bind_param($check_email, "s", $semail);
    mysqli_stmt_execute($check_email);
    $result = mysqli_stmt_get_result($check_email);
    if (mysqli_num_rows($result) > 0) {
        $errors[] = "Email already exists.";
    }

    // If no errors, proceed with insertion
    if (empty($errors)) {
        // Hash the password securely
        $spass_hashed = password_hash($spassword, PASSWORD_DEFAULT);

        // Generate login ID
        $loginId = "ST" . $senr;

        // Handle file upload
        $fs_name = $senr . ".png";

        if (isset($_FILES['stuprofile']) && $_FILES['stuprofile']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = "../src/uploads/stuprofile/";

            // Create directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            // Move uploaded file
            if (!move_uploaded_file($_FILES['stuprofile']['tmp_name'], $upload_dir . $fs_name)) {
                $errors[] = "Failed to upload profile picture.";
            }
        }

        if (empty($errors)) {
            // Prepare SQL statement to prevent SQL injection
            $stmt = mysqli_prepare($conn, "INSERT INTO studentmaster (
                StudentUserName, StudentDOB, StudentEnrollmentNo, StudentPassword, 
                StudentFirstName, StudentMiddleName, StudentLastName, StudentProfilePic, 
                StudentBranchCode, StudentSection, StudentSemester, StudentEmail, 
                StudentContactNo, StudentAddress, ParentEmail, ParentContactNo, StudentRollNo
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            if ($stmt) {
                mysqli_stmt_bind_param(
                    $stmt,
                    "ssssssssssissssss",
                    $loginId,
                    $dob,
                    $senr,
                    $spass_hashed,
                    $fname,
                    $mname,
                    $lname,
                    $fs_name,
                    $sbranch,
                    $ssection,
                    $ssem,
                    $semail,
                    $scontact,
                    $add,
                    $pmail,
                    $pcontact,
                    $sroll
                );

                if (mysqli_stmt_execute($stmt)) {
                    echo "<script>
                        alert('Student added successfully!');
                        window.location.href = 'student_list.php';
                    </script>";
                    exit();
                } else {
                    $errors[] = "Database error: " . mysqli_error($conn);
                }

                mysqli_stmt_close($stmt);
            } else {
                $errors[] = "Database preparation error: " . mysqli_error($conn);
            }
        }
    }

    // Display errors if any
    if (!empty($errors)) {
        $error_message = implode("\\n", $errors);
        echo "<script>
            alert('Error(s) occurred:\\n" . addslashes($error_message) . "');
        </script>";
    }
}

// Helper function to validate date
function validateDate($date)
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    if (!$d || $d->format('Y-m-d') !== $date) {
        return false;
    }

    // Check age constraints (16-50 years)
    $today = new DateTime();
    $age = $today->diff($d)->y;
    return $age >= 16 && $age <= 50;
}

// Helper function to validate password strength
function validatePasswordStrength($password)
{
    // At least 8 characters
    if (strlen($password) < 8) {
        return false;
    }

    // At least one uppercase letter
    if (!preg_match('/[A-Z]/', $password)) {
        return false;
    }

    // At least one lowercase letter
    if (!preg_match('/[a-z]/', $password)) {
        return false;
    }

    // At least one number
    if (!preg_match('/[0-9]/', $password)) {
        return false;
    }

    // At least one special character
    if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) {
        return false;
    }

    return true;
}
?>
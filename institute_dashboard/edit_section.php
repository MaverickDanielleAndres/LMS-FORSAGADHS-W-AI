<?php
session_start();
error_reporting(E_ALL ^ E_WARNING);

// Check if user is logged in as Institute
if ($_SESSION['role'] != "Texas" || $_SESSION['userrole'] != "Institute") {
    header("Location: ../index.php");
    exit();
} else {
    include_once("../config.php");

    // --- Handle Form Submission for Update ---
    $update_message = ""; // Variable to hold success/error messages
    if (isset($_POST['update_section_btn'])) {
        $section_id = intval($_POST['section_id']); // Get ID from hidden field
        $section_branch = mysqli_real_escape_string($conn, trim($_POST['section_branch']));
        $section_number = mysqli_real_escape_string($conn, trim($_POST['section_number']));

        if (empty($section_branch) || empty($section_number)) {
            $update_message = '<div class="alert alert-danger">Error: Please fill in all required fields.</div>';
        } else {
            // --- Server-side Grade Prefix Validation ---
            // 1. Get the grade number for the selected branch
            $grade_number_result = mysqli_query($conn, "SELECT BranchId FROM branchmaster WHERE BranchName = '$section_branch'");
            $grade_info = mysqli_fetch_assoc($grade_number_result);
            $expected_grade_prefix = "";
            if ($grade_info) {
                // Map BranchId to Grade Number (based on your sample BranchId 1=Grade 7)
                $expected_grade_prefix = ($grade_info['BranchId'] + 6) . '-';
            }

            // 2. Validate the section number prefix
            if (!empty($expected_grade_prefix) && strpos($section_number, $expected_grade_prefix) !== 0) {
                // Prefix does not match
                $update_message = '<div class="alert alert-danger">Error: Section Name/Number must start with the correct grade prefix (e.g., \'' . $expected_grade_prefix . '\').</div>';
            } else {
                // --- End Server-side Validation ---

                // Prepare the UPDATE query using a prepared statement
                $update_query = "UPDATE sectionmaster SET SectionNumber = ?, SectionBranch = ? WHERE SectionId = ?";
                $stmt = mysqli_prepare($conn, $update_query);

                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "ssi", $section_number, $section_branch, $section_id);

                    if (mysqli_stmt_execute($stmt)) {
                        // Success
                        $update_message = '<div class="alert alert-success">Section Updated Successfully!</div>';
                        // Refresh data after update to show new values
                        // Fetch branches again for dropdown in case of success
                        $branch_query = "SELECT * FROM branchmaster ORDER BY BranchName";
                        $branch_result = mysqli_query($conn, $branch_query);
                        // Fetch updated section data
                        $section_query = "SELECT * FROM sectionmaster WHERE SectionId = ?";
                        $stmt_fetch = mysqli_prepare($conn, $section_query);
                        mysqli_stmt_bind_param($stmt_fetch, "i", $section_id);
                        mysqli_stmt_execute($stmt_fetch);
                        $section_result = mysqli_stmt_get_result($stmt_fetch);
                        $section_data = mysqli_fetch_assoc($section_result);
                        mysqli_stmt_close($stmt_fetch);
                    } else {
                        // Error during execution
                        $error_msg = "Error updating section: " . mysqli_stmt_error($stmt);
                        error_log("Edit Section Error: " . $error_msg);
                        $update_message = '<div class="alert alert-danger">Failed to update section. Please try again.</div>';
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    // Error preparing statement
                    $error_msg = "Error preparing update statement: " . mysqli_error($conn);
                    error_log("Edit Section Prepare Error: " . $error_msg);
                    $update_message = '<div class="alert alert-danger">Failed to update section. Please try again.</div>';
                }
            } // End else for prefix validation
        } // End else for empty fields
    }
    // --- End Form Submission Handling ---


    // --- Fetch Data for Display/Form ---
    // Get section ID from URL parameter
    if (!isset($_GET['section_id']) || !is_numeric($_GET['section_id'])) {
         // Redirect if ID is missing or invalid
        header("Location: section_list.php");
        exit();
    }
    $section_id = intval($_GET['section_id']);

    // Fetch the section data to edit
    $section_query = "SELECT * FROM sectionmaster WHERE SectionId = ?";
    $stmt = mysqli_prepare($conn, $section_query);
    mysqli_stmt_bind_param($stmt, "i", $section_id);
    mysqli_stmt_execute($stmt);
    $section_result = mysqli_stmt_get_result($stmt);
    $section_data = mysqli_fetch_assoc($section_result);
    mysqli_stmt_close($stmt);

    // Check if section exists
    if (!$section_data) {
         // Redirect if section not found
        header("Location: section_list.php");
        exit();
    }

    // Fetch branches (Grade Levels) for the dropdown
    $branch_query = "SELECT BranchId, BranchName FROM branchmaster ORDER BY BranchName"; // Fetch BranchId for grade number mapping
    $branch_result = mysqli_query($conn, $branch_query);
    // --- End Fetch Data ---
}
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

        .card {
            z-index: 10;
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.2);
            border-radius: 0.75rem;
            background-color: white;
            margin-bottom: 2rem;
        }

         /* Style for action buttons - Reusing from section_list card view */
        .action-btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            border-radius: 0.2rem;
            margin: 0.1rem;
            width: 100%;
            display: block;
            text-align: center;
            border: 1px solid #27548A;
            transition: all 0.2s ease-in-out;
        }

        /* Specific button styles using #27548A variants */
        .btn-view-students {
            background-color: #27548A; /* Primary color */
            color: #ffffff; /* White text */
        }

        .btn-edit {
            background-color: #ffffff; /* White background */
            color: #27548A; /* Primary color text */
        }

        .btn-delete {
            background-color: #ffffff; /* White background */
            color: #dc3545; /* Danger color text */
            border-color: #dc3545; /* Danger color border */
        }

        /* Hover effects */
        .btn-view-students:hover {
            background-color: #1d3d68; /* Darker shade of #27548A */
            color: #ffffff;
        }

        .btn-edit:hover {
            background-color: #e9ecef; /* Light gray background on hover */
            color: #1d3d68; /* Darker shade of #27548A */
        }

        .btn-delete:hover {
            background-color: #f8d7da; /* Light red background on hover */
            color: #bd2130; /* Darker shade of red */
            border-color: #bd2130; /* Darker border */
        }

        .action-btn:hover {
            text-decoration: none;
        }

        .section-table-header {
             margin-top: 2rem;
             padding-bottom: 0.5rem;
             border-bottom: 1px solid #dee2e6;
        }

        /* Style for validation message */
        .grade-validation-message {
            display: none;
            color: #dc3545; /* Red color for error */
            font-size: 0.875em;
            margin-top: 0.25rem;
        }
    </style>
</head>

<body>
    <!-- NAVIGATION -->
    <?php
    $nav_role = "Section";
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
                                <div class="col">
                                    <h6 class="header-pretitle">
                                        Edit
                                    </h6>
                                    <h1 class="header-title">
                                        Section
                                    </h1>
                                </div>
                                <div class="col-auto">
                                    <button class="back-btn btn btn-outline-primary" onclick="history.back()">
                                        <i class="fe uil-angle-double-left"></i> Back
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Update Message -->
                    <?php if (!empty($update_message)) { echo $update_message; } ?>

                    <!-- Edit Section Form Card -->
                    <div class="card">
                        <div class="card-header">
                             <h4 class="card-header-title">Edit Section Details</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <form method="POST" autocomplete="off" class="row g-3 needs-validation" id="editSectionForm" novalidate>
                                    <!-- Hidden field to store SectionId -->
                                    <input type="hidden" name="section_id" value="<?php echo htmlspecialchars($section_data['SectionId']); ?>">

                                    <div class="row">
                                        <div class="col-12 col-md-6 mb-3">
                                            <!-- Grade Level (Branch) -->
                                            <label for="branchSelect" class="form-label">
                                                Grade Level <span class="text-danger">*</span>
                                            </label>
                                            <select id="branchSelect" class="form-select" name="section_branch" required>
                                                <option value="" hidden="">Select Grade Level</option>
                                                <?php
                                                if (mysqli_num_rows($branch_result) > 0) {
                                                    mysqli_data_seek($branch_result, 0); // Reset pointer
                                                    while ($branch = mysqli_fetch_assoc($branch_result)) {
                                                        // Extract the grade number from BranchId for data attribute
                                                        $grade_number = $branch['BranchId'] + 6; // 1+6=7, 2+6=8, 3+6=9, 4+6=10
                                                        $selected = ($branch['BranchName'] === $section_data['SectionBranch']) ? 'selected' : '';
                                                        echo '<option value="' . htmlspecialchars($branch['BranchName']) . '" ' . $selected . ' data-grade-number="' . $grade_number . '">' . htmlspecialchars($branch['BranchName']) . '</option>';
                                                    }
                                                } else {
                                                    echo '<option value="" disabled>No Grade Levels Available</option>';
                                                }
                                                ?>
                                            </select>
                                            <div class="valid-feedback">
                                                Looks good!
                                            </div>
                                            <div class="invalid-feedback">
                                                Please select a Grade Level.
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 mb-3">
                                            <!-- Section Number/Name -->
                                            <label for="sectionNumber" class="form-label">
                                                Section Name/Number <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" id="sectionNumber" class="form-control" name="section_number"
                                                placeholder="e.g., 9-Emerald" maxlength="50"
                                                value="<?php echo htmlspecialchars($section_data['SectionNumber']); ?>" required>
                                            <div class="valid-feedback">
                                                Looks good!
                                            </div>
                                            <div class="invalid-feedback">
                                                Please provide a Section Name/Number.
                                            </div>
                                             <!-- Validation Message for Grade Prefix -->
                                            <div id="gradeValidationMessage" class="grade-validation-message">
                                                Section name must start with the grade number (e.g., '9-' for Grade 9).
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-start">
                                        <!-- Submit Button -->
                                        <button class="btn btn-primary" style="margin-right: 1rem"type="submit" value="update_section" name="update_section_btn">
                                            Update Section
                                        </button>
                                        <!-- Cancel Button -->
                                         <a href="section_list.php" class="btn btn-secondary ms-2">Cancel</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div> <!-- / .card (Form Card) -->

                </div> <!-- / .col-12 col-xl-10 -->
            </div> <!-- / .row -->
        </div> <!-- / .container-fluid -->
    </div> <!-- / .main-content -->

    <!-- JAVASCRIPT -->
    <script src='https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.js'></script>
    <script src="../assets/js/vendor.bundle.js"></script>
    <script src="../assets/js/theme.bundle.js"></script>
    <script>
        // Bootstrap validation script
        (function () {
            'use strict';

            // Fetch the form
            var form = document.getElementById('editSectionForm');

             // Handle branch selection change to provide hint or validation
            document.getElementById('branchSelect').addEventListener('change', function() {
                 var selectedOption = this.options[this.selectedIndex];
                 var gradeNumber = selectedOption.getAttribute('data-grade-number');
                 var sectionInput = document.getElementById('sectionNumber');
                 var validationMsg = document.getElementById('gradeValidationMessage');

                 if (gradeNumber) {
                     // Update placeholder to guide the user
                     sectionInput.placeholder = "e.g., " + gradeNumber + "-Emerald";
                     // Clear previous validation message if branch changes
                     validationMsg.style.display = 'none';
                     sectionInput.classList.remove('is-invalid'); // Remove invalid class if previously set by JS
                 }
            });

            // Custom validation on form submission
            if (form) {
                form.addEventListener('submit', function (event) {
                    // Run default Bootstrap validation first
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    // Get selected grade number
                    var branchSelect = document.getElementById('branchSelect');
                    var selectedOption = branchSelect.options[branchSelect.selectedIndex];
                    var expectedGradeNumber = selectedOption.getAttribute('data-grade-number');
                    var sectionInput = document.getElementById('sectionNumber');
                    var validationMsg = document.getElementById('gradeValidationMessage');

                    // Check if section name starts with the correct grade number followed by a dash
                    if (expectedGradeNumber && sectionInput.value.trim() !== '') {
                        var inputValue = sectionInput.value.trim();
                        var expectedPrefix = expectedGradeNumber + '-';
                        if (!inputValue.startsWith(expectedPrefix)) {
                            // Prevent submission
                            event.preventDefault();
                            event.stopPropagation();

                            // Show validation message
                            validationMsg.style.display = 'block';
                            sectionInput.classList.add('is-invalid'); // Add Bootstrap invalid class
                            // Focus the input for user convenience
                            sectionInput.focus();
                            return; // Stop further processing
                        } else {
                            // Hide message if valid
                            validationMsg.style.display = 'none';
                            sectionInput.classList.remove('is-invalid');
                        }
                    } else if (expectedGradeNumber && sectionInput.value.trim() === '') {
                         // If grade is selected but section name is empty, let Bootstrap handle it
                         // But ensure our custom message is hidden
                         validationMsg.style.display = 'none';
                    }

                    // Add general validation class
                    form.classList.add('was-validated');
                }, false);
            }
        })()
    </script>
</body>

</html>
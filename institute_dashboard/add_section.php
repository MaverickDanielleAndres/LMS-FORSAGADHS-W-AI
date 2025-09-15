<?php
session_start();
error_reporting(E_ALL ^ E_WARNING);

// Check if user is logged in as Institute
if ($_SESSION['role'] != "Texas" || $_SESSION['userrole'] != "Institute") {
    header("Location: ../index.php");
    exit();
} else {
    include_once("../config.php");

    // Fetch branches (Grade Levels) for the dropdown
    // Also fetch BranchId to determine the grade number
    $branch_query = "SELECT BranchId, BranchName FROM branchmaster ORDER BY BranchName";
    $branch_result = mysqli_query($conn, $branch_query);

    // Fetch all sections for potential duplicate check (optional)
    // $section_list_query = "SELECT * FROM sectionmaster ORDER BY SectionId";
    // $section_list_result = mysqli_query($conn, $section_list_query);
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
            margin-bottom: 2rem; /* Add space below the card */
        }
        /* Style for action buttons */
        .action-btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            border-radius: 0.2rem;
            margin-right: 0.25rem;
        }
        .btn-edit {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #212529;
        }
        .btn-copy {
            background-color: #17a2b8;
            border-color: #17a2b8;
            color: #fff;
        }
        .btn-delete {
            background-color: #dc3545;
            border-color: #dc3545;
            color: #fff;
        }
        .action-btn:hover {
            opacity: 0.8;
        }
        .action-cell {
            white-space: nowrap; /* Keep buttons on one line */
            width: 1%; /* Make the action cell as narrow as possible */
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
    // Assuming you have a navigation item for Sections
    $nav_role = "Section"; // Or whatever highlights the section management area in nav.php
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
                                <!-- Left: Title and Subtitle -->
                                <div class="col">
                                    <h6 class="header-pretitle">
                                        Add
                                    </h6>
                                    <h1 class="header-title">
                                        Sections
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
                    </div>
                    <!-- Add Section Form Card -->
                    <div class="card">
                        <div class="card-header">
                             <h4 class="card-header-title">Add New Section</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <form method="POST" autocomplete="off" class="row g-3 needs-validation" id="addSectionForm" novalidate>
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
                                                        // Extract the grade number from BranchName (assumes format "Grade X")
                                                        // Alternatively, use BranchId if it reliably corresponds to grade (1=7, 2=8, etc.)
                                                        // Let's use BranchId logic based on your sample data (BranchId 1=Grade 7)
                                                        $grade_number = $branch['BranchId'] + 6; // 1+6=7, 2+6=8, 3+6=9, 4+6=10
                                                        echo '<option value="' . htmlspecialchars($branch['BranchName']) . '" data-grade-number="' . $grade_number . '">' . htmlspecialchars($branch['BranchName']) . '</option>';
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
                                                placeholder="e.g., 9-Emerald" maxlength="50" required>
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
                                        <button class="btn btn-primary" type="submit" value="add_section" name="add_section_btn">
                                            Add Section
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div> <!-- / .card (Form Card) -->
                </div> <!-- / .col-12 col-xl-10 -->
            </div> <!-- / .row (justify-content-center) -->
        </div> <!-- / .container-fluid -->
    </div> <!-- / .main-content -->
    <!-- JAVASCRIPT -->
    <!-- Map JS -->
    <script src='https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.js'></script>
    <!-- Vendor JS -->
    <script src="../assets/js/vendor.bundle.js"></script>
    <!-- Theme JS -->
    <script src="../assets/js/theme.bundle.js"></script>
    <script>
        // Bootstrap validation script
        (function () {
            'use strict';

            // Fetch the form
            var form = document.getElementById('addSectionForm');

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
                            // Ensure the general invalid feedback is also shown if field is empty
                            sectionInput.classList.remove('was-validated'); // Might need adjustment
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
        })();

        // Basic function for Copy button (needs implementation)
        function copySection(sectionId) {
            alert('Copy functionality for Section ID ' + sectionId + ' is not implemented yet.');
            // TODO: Implement copy logic (e.g., pre-fill the form above with this section's data)
        }
    </script>
</body>
</html>
<?php
// Handle form submission (Add Section)
if (isset($_POST['add_section_btn'])) {
    // Sanitize input data
    $section_branch = mysqli_real_escape_string($conn, trim($_POST['section_branch']));
    $section_number = mysqli_real_escape_string($conn, trim($_POST['section_number']));

    // Basic validation (HTML5 handles required fields, but double-checking is good)
    if (empty($section_branch) || empty($section_number)) {
        echo "<script>
                alert('Error: Please fill in all required fields.');
                window.location.reload();
              </script>";
        exit();
    }

    // --- Server-side Grade Prefix Validation ---
    // 1. Get the grade number for the selected branch
    $grade_number_result = mysqli_query($conn, "SELECT BranchId FROM branchmaster WHERE BranchName = '$section_branch'");
    $grade_info = mysqli_fetch_assoc($grade_number_result);
    $expected_grade_prefix = "";
    if ($grade_info) {
        // Map BranchId to Grade Number (based on your sample data: BranchId 1=Grade 7)
        $expected_grade_prefix = ($grade_info['BranchId'] + 6) . '-';
    }

    // 2. Validate the section number prefix
    if (!empty($expected_grade_prefix) && strpos($section_number, $expected_grade_prefix) !== 0) {
         // Prefix does not match
        echo "<script>
                alert('Error: Section Name/Number must start with the correct grade prefix (e.g., \\'" . $expected_grade_prefix . "\\').');
                window.location.reload(); // Or redirect back to form
              </script>";
        exit();
    }
    // --- End Server-side Validation ---

    // Prepare the INSERT query using a prepared statement to prevent SQL injection
    $insert_query = "INSERT INTO sectionmaster (SectionNumber, SectionBranch) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $insert_query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $section_number, $section_branch); // Both are strings based on DB schema
        if (mysqli_stmt_execute($stmt)) {
            // Success
            echo "<script>
                    alert('Section Added Successfully');
                    window.location.href = window.location.href; // Refresh the page to show the new list
                  </script>";
        } else {
            // Error during execution
            $error_msg = "Error adding section: " . mysqli_stmt_error($stmt);
            error_log("Add Section Error: " . $error_msg); // Log the detailed error
            echo "<script>
                    alert('Failed to add section. Please try again.');
                    window.location.reload();
                  </script>";
        }
        mysqli_stmt_close($stmt);
    } else {
        // Error preparing statement
        $error_msg = "Error preparing statement: " . mysqli_error($conn);
        error_log("Add Section Prepare Error: " . $error_msg); // Log the detailed error
        echo "<script>
                alert('Failed to add section. Please try again.');
                window.location.reload();
              </script>";
    }
    // Connection closed by script end or config
}
?>

<?php
error_reporting(E_ALL ^ E_WARNING);
session_start();

if ($_SESSION['role'] != "Texas") {
    header("Location: ../index.php");
    exit(); // Ensure script stops after redirect
} else {
    include_once("../config.php");
    $_SESSION["userrole"] = "Institute";

    // --- Data Fetching for Form Population ---
    if (isset($_GET['subcode'])) {
        $subcode = mysqli_real_escape_string($conn, $_GET['subcode']);
        // Note: semid and brid might not be strictly needed for fetching subject data,
        // but keeping them if your system relies on them for redirects etc.
        $semid = isset($_GET['semid']) ? mysqli_real_escape_string($conn, $_GET['semid']) : '';
        $brid = isset($_GET['brid']) ? mysqli_real_escape_string($conn, $_GET['brid']) : '';

        // Fetch the subject details to be edited
        $subject_query = "SELECT * FROM subjectmaster WHERE SubjectCode = ?";
        $stmt_subject = mysqli_prepare($conn, $subject_query);
        if ($stmt_subject) {
            mysqli_stmt_bind_param($stmt_subject, "s", $subcode);
            mysqli_stmt_execute($stmt_subject);
            $subject_result = mysqli_stmt_get_result($stmt_subject);
            $row = mysqli_fetch_assoc($subject_result);
            mysqli_stmt_close($stmt_subject);

            if (!$row) {
                 // Subject not found
                echo "<script>alert('Subject not found.');</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
        } else {
            // Handle query error
            echo "<script>alert('Error preparing subject query.');</script>";
            echo "<script>window.history.back();</script>";
            exit();
        }

        // Fetch ALL branches for the Grade Level dropdown
        $all_branches_query = "SELECT BranchId, BranchName, BranchSemesters FROM branchmaster ORDER BY BranchName";
        $all_branches_result = mysqli_query($conn, $all_branches_query);

        // Fetch the CURRENT branch details for the subject (to set initial quarter dropdown)
        $current_branch_query = "SELECT BranchId, BranchName, BranchSemesters FROM branchmaster WHERE BranchId = ?";
        $stmt_cb = mysqli_prepare($conn, $current_branch_query);
        $current_branch_semesters = 4; // Default fallback
        if ($stmt_cb) {
            mysqli_stmt_bind_param($stmt_cb, "i", $row['SubjectBranch']); // SubjectBranch is BranchId
            mysqli_stmt_execute($stmt_cb);
            $current_branch_result = mysqli_stmt_get_result($stmt_cb);
            if ($current_branch_row = mysqli_fetch_assoc($current_branch_result)) {
                $current_branch_semesters = $current_branch_row['BranchSemesters'];
            }
            mysqli_stmt_close($stmt_cb);
        }

        // Fetch faculty for the Faculty ID dropdown (based on current subject's branch)
        // Note: The original query used FacultyBranchCode='$brid'. Assuming FacultyBranchCode stores BranchCode.
        // However, subjectmaster.SubjectBranch stores BranchId. Need to get BranchCode for faculty query.
        $fac_branch_code = "";
        if (isset($current_branch_row)) {
            $fac_branch_code = $current_branch_row['BranchId']; // Or fetch BranchCode if stored separately
        } else {
            // Fallback if current branch wasn't fetched correctly
            $fallback_branch_query = "SELECT BranchId FROM branchmaster WHERE BranchId = ?";
            $stmt_fb = mysqli_prepare($conn, $fallback_branch_query);
            if ($stmt_fb) {
                mysqli_stmt_bind_param($stmt_fb, "i", $row['SubjectBranch']);
                mysqli_stmt_execute($stmt_fb);
                $fb_result = mysqli_stmt_get_result($stmt_fb);
                if ($fb_row = mysqli_fetch_assoc($fb_result)) {
                     $fac_branch_code = $fb_row['BranchId'];
                }
                mysqli_stmt_close($stmt_fb);
            }
        }
        $facsel = "SELECT * FROM facultymaster WHERE FacultyBranchCode = ?"; // Use BranchId
        $stmt_fac = mysqli_prepare($conn, $facsel);
        $facresult = []; // Initialize
        if ($stmt_fac) {
            mysqli_stmt_bind_param($stmt_fac, "s", $fac_branch_code);
            mysqli_stmt_execute($stmt_fac);
            $facresult = mysqli_stmt_get_result($stmt_fac);
            // Result stored in $facresult for use in the dropdown loop
        } else {
             // Handle faculty query error
             echo "<script>alert('Error preparing faculty query.');</script>";
             // Decide if you want to continue or stop
        }

    } else {
        // Redirect if subcode is not provided
        header("Location: subject_list.php");
        exit();
    }
    // --- End Data Fetching ---
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

        .back-btn:hover {
            background-color: #0d3b72ff;
        }

        .card {
            z-index: 10;
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.2);
            border-radius: 0.75rem;
            background-color: white;
        }

        /* Ensure consistent spacing for form groups */
        .form-group {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <!-- NAVIGATION -->
    <?php include_once("../nav.php"); ?>
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
                                    <h6 class="header-pretitle">Edit</h6>
                                    <h1 class="header-title">Subject Details</h1>
                                </div>
                                <!-- Right column for Back button -->
                                <div class="col-auto text-end">
                                    <h5 class="header-pretitle">
                                        <button class="back-btn" onclick="history.back()">
                                            <i class="fe uil-angle-double-left"></i> Back
                                        </button>
                                    </h5>
                                </div>
                            </div> <!-- / .row -->
                        </div>
                    </div>

                    <!-- Form -->
                    <?php if (isset($row)) { ?>
                    <form method="POST" autocomplete="off" enctype="multipart/form-data">
                        <div class="row justify-content-between align-items-center">
                            <div class="col">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="avatar">
                                            <img name="simg" class="w-100 border-radius-lg shadow-sm rounded"
                                                src="../src/uploads/subprofile/<?php echo htmlspecialchars($row['SubjectPic']); ?>?t=<?php echo time(); ?>"
                                                alt="Subject Photo" id="IMG-preview">
                                        </div>
                                    </div>
                                    <div class="col ml-n2">
                                        <h4 class="mb-1">Subject Photo</h4>
                                        <small class="text-muted">Only allowed PNG or JPG less than 2MB</small>
                                    </div>
                                </div> <!-- / .row -->
                            </div>
                            <div class="col-auto">
                                <!-- Button -->
                                <input type="file" id="img" name="subprofile" class="btn btn-sm"
                                    onchange="showPreview(event);" accept="image/jpg, image/jpeg, image/png">
                            </div>
                        </div> <!-- Priview Profile pic -->
                        <script>
                            function showPreview(event) {
                                if (event.target.files.length > 0) {
                                    var src = URL.createObjectURL(event.target.files[0]);
                                    var preview = document.getElementById("IMG-preview");
                                    preview.src = src;
                                    // Optional: Add file size/type validation here too
                                }
                            }
                        </script>
                        <hr class="my-5">

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Subject Code</label>
                                    <input type="text" class="form-control" name="icode"
                                        value="<?php echo htmlspecialchars($row['SubjectCode']); ?>" required readonly>
                                    <!-- Readonly or remove if code editing is allowed -->
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Subject Name</label>
                                    <input type="text" class="form-control" name="iname"
                                        value="<?php echo htmlspecialchars($row['SubjectName']); ?>" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Subject Grade Level</label>
                                    <select id="subjectBranchSelect" class="form-control" name="ibranch" required>
                                        <option value="" disabled>Select Grade Level</option>
                                        <?php
                                        // Populate with ALL branches
                                        if ($all_branches_result && mysqli_num_rows($all_branches_result) > 0) {
                                            mysqli_data_seek($all_branches_result, 0); // Reset pointer
                                            while ($branch = mysqli_fetch_assoc($all_branches_result)) {
                                                // Check if this branch is the CURRENT one for the subject
                                                // Compare BranchId (from branchmaster) with SubjectBranch (from subjectmaster)
                                                $selected = ($branch['BranchId'] == $row['SubjectBranch']) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($branch['BranchId']) . '" ' . $selected . ' data-semesters="' . htmlspecialchars($branch['BranchSemesters']) . '">' . htmlspecialchars($branch['BranchName']) . '</option>';
                                            }
                                        } else {
                                            echo '<option value="" disabled>No Grade Levels Available</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Subject Quarter</label>
                                    <select id="subjectSemesterSelect" class="form-control" name="isem" required>
                                        <option value="" disabled>Select Quarter</option>
                                        <?php
                                        // Populate quarters based on the CURRENT branch's semesters (initial state)
                                        for ($count = 1; $count <= $current_branch_semesters; $count++) {
                                            $selected = ($count == $row['SubjectSemester']) ? 'selected' : '';
                                            echo '<option value="' . $count . '" ' . $selected . '>' . $count . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Faculty ID</label>
                                    <select id="validationCustom01" class="form-control" name="ifac" required>
                                        <option value="" hidden="">Select Faculty</option>
                                        <?php
                                        // Check if faculty results were fetched successfully
                                        if ($stmt_fac && $facresult && mysqli_num_rows($facresult) > 0) {
                                             // Reset pointer if needed (though it's a stmt result, this might not apply, but good practice if it were a normal result)
                                             // mysqli_data_seek is for mysqli_result, not stmt_result. Looping is fine.
                                             // Rewind the result set if you need to loop again, but here we loop once.
                                             // Let's fetch all into an array for easier handling if needed, but direct loop is okay.
                                             while ($farow = mysqli_fetch_assoc($facresult)) {
                                                $selected = ($row['SubjectFacultyId'] == $farow['FacultyId']) ? 'selected' : '';
                                                echo '<option ' . $selected . ' value="' . htmlspecialchars($farow['FacultyId']) . '">' . htmlspecialchars($farow['FacultyFirstName'] . " " . $farow['FacultyLastName']) . '</option>';
                                             }
                                             // Close the faculty statement after use
                                             mysqli_stmt_close($stmt_fac);
                                        } else {
                                            echo '<option value="" disabled>No Faculty Available for this Grade</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Syllabus</label>
                                    <input type="file" class="form-control border-0" id="validationCustom01"
                                        name="isyllabus" accept="application/pdf">
                                    <br>
                                    <?php if (!empty($row['SubjectSyllabus'])) { ?>
                                        <small class="text-muted">
                                            Current Syllabus: <a href="../src/uploads/syllabus/<?php echo htmlspecialchars($row['SubjectSyllabus']); ?>" target="_blank">View PDF</a>
                                        </small>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify">
                            <button class="btn btn-primary" type="submit" value="sub" name="subbed">
                                Save Changes
                            </button>
                        </div>
                    </form>
                    <?php } else { ?>
                        <p>Subject data could not be loaded.</p>
                    <?php } ?>
                    <br>
                </div>
            </div> <!-- / .row -->
        </div> <!-- / .container-fluid -->
    </div> <!-- / .main-content -->

    <!-- JAVASCRIPT -->
    <!-- Map JS -->
    <script src='https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.js'></script>
    <!-- Vendor JS -->
    <script src="../assets/js/vendor.bundle.js"></script>
    <!-- Theme JS -->
    <script src="../assets/js/theme.bundle.js"></script>

    <!-- JavaScript for dynamic semester update -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const branchSelect = document.getElementById('subjectBranchSelect');
        const semesterSelect = document.getElementById('subjectSemesterSelect');

        if (branchSelect && semesterSelect) {
            branchSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const maxSemesters = parseInt(selectedOption.getAttribute('data-semesters'), 10) || 4; // Default to 4 if attribute missing

                // Store the currently selected semester value if needed, or just reset
                // const currentSemester = semesterSelect.value;

                // Clear existing options
                semesterSelect.innerHTML = '<option value="" disabled>Select Quarter</option>';

                // Populate new options based on the selected branch's semesters
                for (let i = 1; i <= maxSemesters; i++) {
                    const option = document.createElement('option');
                    option.value = i;
                    option.textContent = i;
                    // Optionally, try to keep the same semester number if it exists in the new range
                    // if (i == currentSemester) { option.selected = true; }
                    semesterSelect.appendChild(option);
                }

                 // Optional: Automatically select the first option
                 if (semesterSelect.options.length > 1) {
                     semesterSelect.options[1].selected = true; // Select the first generated option (value=1)
                 }
            });
        }
    });
    </script>

</body>
</html>

<?php
// --- Handle Form Submission for Update ---
if (isset($_POST['subbed'])) {
    // Sanitize input data
    $subject_code = mysqli_real_escape_string($conn, trim($_POST['icode'])); // Should match $subcode
    $subject_name = mysqli_real_escape_string($conn, trim($_POST['iname']));
    $subject_branch_id = (int) $_POST['ibranch']; // This is now BranchId
    $subject_semester = (int) $_POST['isem'];
    $subject_faculty_id = mysqli_real_escape_string($conn, trim($_POST['ifac'])); // FacultyId

    // Handle file uploads
    $simg = $row['SubjectPic']; // Default to existing picture
    if (isset($_FILES['subprofile']) && $_FILES['subprofile']['error'] == 0) {
        $f_name = $_FILES['subprofile']['tmp_name'];
        $f_size = $_FILES['subprofile']['size'];
        $f_type = $_FILES['subprofile']['type'];

        $valid_types = ['image/png', 'image/jpg', 'image/jpeg'];
        if (in_array($f_type, $valid_types)) {
            if ($f_size <= 2000000) { // 2MB
                // Generate unique filename or use subject code
                $file_extension = pathinfo($_FILES['subprofile']['name'], PATHINFO_EXTENSION);
                $new_filename = $subject_code . '.' . $file_extension;
                $target_path = "../src/uploads/subprofile/" . $new_filename;

                if (move_uploaded_file($f_name, $target_path)) {
                    $simg = $new_filename;
                } else {
                    echo "<script>alert('Error uploading Subject Photo.');</script>";
                }
            } else {
                echo "<script>alert('Subject Photo file size is too big (max 2MB)!');</script>";
            }
        } else {
            echo "<script>alert('Invalid Subject Photo type. Only PNG, JPG, JPEG allowed.');</script>";
        }
    }

    $syllabus_file = $row['SubjectSyllabus']; // Default to existing syllabus
    if (isset($_FILES['isyllabus']) && $_FILES['isyllabus']['error'] == 0) {
        $syllabus_tmp_name = $_FILES['isyllabus']['tmp_name'];
        $syllabus_size = $_FILES['isyllabus']['size'];
        $syllabus_type = $_FILES['isyllabus']['type'];

        if ($syllabus_type == 'application/pdf') {
            if ($syllabus_size <= 2000000) { // 2MB
                // Generate unique filename or use subject code
                $new_syllabus_filename = $subject_code . '.pdf';
                $syllabus_target_path = "../src/uploads/syllabus/" . $new_syllabus_filename;

                if (move_uploaded_file($syllabus_tmp_name, $syllabus_target_path)) {
                    $syllabus_file = $new_syllabus_filename;
                } else {
                    echo "<script>alert('Error uploading Syllabus PDF.');</script>";
                }
            } else {
                echo "<script>alert('Syllabus PDF file size is too big (max 2MB)!');</script>";
            }
        } else {
            echo "<script>alert('Invalid Syllabus type. Only PDF allowed.');</script>";
        }
    }

    // Determine SemCode (assuming format BranchCode_Semester)
    // Need to get BranchCode from BranchId
    $branch_code_for_sem = "";
    $get_branch_code_query = "SELECT BranchId FROM branchmaster WHERE BranchId = ?";
    $stmt_bcode = mysqli_prepare($conn, $get_branch_code_query);
    if ($stmt_bcode) {
        mysqli_stmt_bind_param($stmt_bcode, "i", $subject_branch_id);
        mysqli_stmt_execute($stmt_bcode);
        $bcode_result = mysqli_stmt_get_result($stmt_bcode);
        if ($bcode_row = mysqli_fetch_assoc($bcode_result)) {
            // Assuming BranchCode is the same as BranchId for SemCode calculation, or fetch actual BranchCode
            // Based on sample data, it seems BranchId is used (e.g., BranchId 1 -> part of 007_1)
            // Let's assume SemCode is formed like: {BranchId padded to 3 digits}_{Semester}
            // This is an assumption based on sample data. You might need to adjust.
            $padded_branch_id = str_pad($subject_branch_id, 3, '0', STR_PAD_LEFT);
            $sem_code = $padded_branch_id . '_' . $subject_semester;
        } else {
            $sem_code = $subject_branch_id . '_' . $subject_semester; // Fallback
        }
        mysqli_stmt_close($stmt_bcode);
    } else {
        $sem_code = $subject_branch_id . '_' . $subject_semester; // Fallback
    }


    // Prepare the UPDATE query using a prepared statement
    $update_query = "UPDATE subjectmaster SET
                        SubjectName = ?,
                        SubjectBranch = ?, -- This stores BranchId
                        SubjectSemester = ?,
                        SubjectFacultyId = ?,
                        SubjectSyllabus = ?,
                        SemCode = ?, -- Update SemCode based on new Branch/Semester
                        SubjectPic = ?
                     WHERE SubjectCode = ?";

    $stmt_update = mysqli_prepare($conn, $update_query);
    if ($stmt_update) {
        mysqli_stmt_bind_param($stmt_update, "siisssss",
            $subject_name,
            $subject_branch_id, // Bind BranchId
            $subject_semester,
            $subject_faculty_id,
            $syllabus_file,
            $sem_code, // Bind calculated SemCode
            $simg,
            $subject_code
        );

        if (mysqli_stmt_execute($stmt_update)) {
            // Success
            echo "<script>alert('Subject Edited Successfully');</script>";
            // Redirect back to subject list or details page
            // Use the potentially updated $semid and $brid if needed, or fetch them
            // For now, redirecting to subject list is safer
            echo "<script>window.location.href = 'subject_list.php';</script>";
            // Alternative if you need to go back to sem_details:
            // echo "<script>window.location.href = 'sem_details.php?semid=" . urlencode($sem_code) . "&brid=" . urlencode($subject_branch_id) . "';</script>";

        } else {
            // Error during execution
            $error_msg = "Error updating subject: " . mysqli_stmt_error($stmt_update);
            error_log("Edit Subject Error: " . $error_msg);
            echo "<script>alert('Error Occurred while updating subject.');</script>";
            echo "<script>window.location.href = 'edit_subject.php?subcode=" . urlencode($subject_code) . "&semid=" . urlencode($semid) . "&brid=" . urlencode($brid) . "';</script>";
        }
        mysqli_stmt_close($stmt_update);
    } else {
        // Error preparing statement
        $error_msg = "Error preparing update statement: " . mysqli_error($conn);
        error_log("Edit Subject Prepare Error: " . $error_msg);
        echo "<script>alert('Error Occurred.');</script>";
        echo "<script>window.location.href = 'edit_subject.php?subcode=" . urlencode($subject_code) . "&semid=" . urlencode($semid) . "&brid=" . urlencode($brid) . "';</script>";
    }
}
// --- End Form Submission Handling ---
?>

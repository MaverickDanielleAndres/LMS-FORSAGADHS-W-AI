<?php
error_reporting(E_ALL ^ E_WARNING);
session_start();

if ($_SESSION['role'] != "Texas") {
    header("Location: ../index.php");
    exit();
} else {
    include_once("../config.php");
    $_SESSION["userrole"] = "Institute";

    // Data Fetching
    $subject_code = isset($_GET['subjectcode']) ? mysqli_real_escape_string($conn, $_GET['subjectcode']) : '';

    if (!empty($subject_code)) {
        // Fetch subject details along with branch name and faculty name
        $subject_query = "SELECT
                            sm.SubjectCode,
                            sm.SubjectName,
                            sm.SubjectSemester,
                            sm.SubjectPic,
                            sm.SubjectSyllabus,
                            sm.SemCode,
                            bm.BranchId AS SubjectBranchId,
                            bm.BranchName AS SubjectBranchName,
                            fm.FacultyId AS FacultyId,
                            CONCAT(fm.FacultyFirstName, ' ', fm.FacultyLastName) AS FacultyName,
                            fm.FacultyCode AS FacultyCode,
                            fm.FacultyEmail AS FacultyEmail
                         FROM subjectmaster sm
                         LEFT JOIN branchmaster bm ON sm.SubjectBranch = bm.BranchId
                         LEFT JOIN facultymaster fm ON sm.SubjectFacultyId = fm.FacultyId
                         WHERE sm.SubjectCode = ?";

        $stmt = mysqli_prepare($conn, $subject_query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $subject_code);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $subject_data = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if (!$subject_data) {
                $error_message = "Subject not found.";
            }
        } else {
            $error_message = "Error preparing query.";
        }
    } else {
        $error_message = "No subject code provided.";
    }
    // End Data Fetching
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

        .header-avatar-top {
             margin-top: -60px; /* Adjust based on avatar size */
        }

        .subject-avatar {
             width: 120px; /* Adjust size as needed */
             height: 120px;
             object-fit: cover;
             border: 4px solid #fff; /* Match body background */
        }
    </style>
</head>
<body>
    <!-- NAVIGATION -->
    <?php
    $nav_role = "Subject";
    include_once("../nav.php"); ?>
    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="header">
            <div class="header">
                <div class="container-fluid">
                    <div class="header-body">
                        <div class="row align-items-end">
                            <div class="col">
                                <h6 class="header-pretitle">
                                    Subject
                                </h6>
                                <h1 class="header-title">
                                    Profile
                                </h1>
                            </div>
                            <div class="col-auto">
                                <button class="back-btn" onclick="history.back()">
                                    <i class="fe fe-arrow-left"></i> Back
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (isset($error_message)) { ?>
                <div class="container-fluid mt-4">
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                    <div class="text-center mt-3">
                        <a href="subject_list.php" class="btn btn-primary">Go to Subject List</a>
                    </div>
                </div>
            <?php } elseif (isset($subject_data)) { ?>
                <br><br>
                <div class="container-fluid">
                    <div class="header-body mt-n5 mt-md-n6">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="avatar avatar-xxl header-avatar-top">
                                    <img src="../src/uploads/subprofile/<?php echo htmlspecialchars($subject_data['SubjectPic']); ?>?t=<?php echo time(); ?>"
                                         alt="<?php echo htmlspecialchars($subject_data['SubjectName']); ?> Photo"
                                         class="avatar-img rounded-circle border border-4 border-body subject-avatar"
                                         onerror="this.onerror=null; this.src='../assets/img/avatars/default_subject.png';">
                                </div>
                            </div>
                            <div class="col mb-3 ml-n3 ml-md-n2">
                                <h1 class="header-title">
                                    <?php echo htmlspecialchars($subject_data['SubjectName']); ?>
                                </h1>
                                <h5 class="header-pretitle mt-2">
                                    <?php echo htmlspecialchars($subject_data['SubjectCode']); ?>
                                </h5>
                            </div>
                            <div class="col-12 col-md-auto mt-2 mt-md-0 mb-md-3">
                                <a href="edit_subject.php?subcode=<?php echo urlencode($subject_data['SubjectCode']); ?>&semid=<?php echo urlencode($subject_data['SemCode']); ?>&brid=<?php echo urlencode($subject_data['SubjectBranchId']); ?>" class="btn btn-warning d-block d-md-inline-block btn-md">
                                    Edit Details
                                </a>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col">
                                <ul class="nav nav-tabs nav-overflow header-tabs">
                                    <li class="nav-item">
                                        <a href="#!" class="nav-link h3 active">
                                            Basic Details
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h1 class="header-title">
                                Subject Info:
                            </h1>
                            <br>

                            <div class="input-group mb-3">
                                <span class="input-group-text col-12 col-md-2">Subject Name</span>
                                <input type="text" value="<?php echo htmlspecialchars($subject_data['SubjectName']); ?>" class="form-control" disabled>
                            </div>

                            <div class="input-group mb-3">
                                <span class="input-group-text col-12 col-md-2">Subject Code</span>
                                <input type="text" value="<?php echo htmlspecialchars($subject_data['SubjectCode']); ?>" class="form-control" disabled>
                                <span class="input-group-text col-12 col-md-2">Grade Level</span>
                                <input type="text" value="<?php echo htmlspecialchars($subject_data['SubjectBranchName'] ?? 'N/A'); ?>" class="form-control" disabled>
                            </div>

                            <div class="input-group mb-3">
                                <span class="input-group-text col-12 col-md-2">Quarter</span>
                                <input type="text" value="<?php echo htmlspecialchars($subject_data['SubjectSemester']); ?>" class="form-control" disabled>
                                <span class="input-group-text col-12 col-md-2">Sem Code</span>
                                <input type="text" value="<?php echo htmlspecialchars($subject_data['SemCode']); ?>" class="form-control" disabled>
                            </div>

                            <div class="input-group mb-3">
                                <span class="input-group-text col-12 col-md-2">Assigned Faculty</span>
                                <input type="text" value="<?php echo htmlspecialchars($subject_data['FacultyName'] ?? 'Not Assigned'); ?>" class="form-control" disabled>
                                <span class="input-group-text col-12 col-md-2">Faculty Code</span>
                                <input type="text" value="<?php echo htmlspecialchars($subject_data['FacultyCode'] ?? 'N/A'); ?>" class="form-control" disabled>
                            </div>

                            <div class="input-group mb-3">
                                 <span class="input-group-text col-12 col-md-2">Faculty Email</span>
                                <input type="text" value="<?php echo htmlspecialchars($subject_data['FacultyEmail'] ?? 'N/A'); ?>" class="form-control" disabled>
                                <span class="input-group-text col-12 col-md-2">Syllabus</span>
                                <div class="col-12 col-md-4">
                                <?php if (!empty($subject_data['SubjectSyllabus'])) { ?>
                                    <a href="../src/uploads/syllabus/<?php echo urlencode(htmlspecialchars($subject_data['SubjectSyllabus'])); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fe fe-file-text"></i> View PDF
                                    </a>
                                <?php } else { ?>
                                    <span class="text-muted">Not Uploaded</span>
                                <?php } ?>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } else { ?>
             <div class="container-fluid mt-4">
                 <div class="alert alert-info">No subject data available.</div>
                 <div class="text-center mt-3">
                     <a href="subject_list.php" class="btn btn-primary">Go to Subject List</a>
                 </div>
             </div>
        <?php } ?>
    </div>

    <script src='https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.js'></script>
    <script src="../assets/js/vendor.bundle.js"></script>
    <script src="../assets/js/theme.bundle.js"></script>
</body>
</html>

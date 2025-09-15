<?php
session_start();
if ($_SESSION['role'] != "faculty") {
    header("Location: ../index.php");
} else {
    include_once("../config.php");
    $_SESSION["userrole"] = "Faculty";
    $user = $_SESSION['fid'];
    
    // Get section ID from URL parameter
    $sectionId = isset($_GET['section']) ? (int)$_GET['section'] : 0;
    
    // Verify that this faculty is assigned to this section
    $verifyQuery = "SELECT fs.*, s.SectionNumber, s.SectionBranch, b.BranchName 
                    FROM facultysection fs 
                    INNER JOIN sectionmaster s ON fs.SectionId = s.SectionId 
                    INNER JOIN branchmaster b ON s.SectionBranch = b.BranchName
                    WHERE fs.FacultyId = ? AND fs.SectionId = ? AND fs.IsActive = 1";
    $stmt_verify = mysqli_prepare($conn, $verifyQuery);
    if ($stmt_verify) {
        mysqli_stmt_bind_param($stmt_verify, "ii", $user, $sectionId);
        mysqli_stmt_execute($stmt_verify);
        $verifyResult = mysqli_stmt_get_result($stmt_verify);
        
        if (mysqli_num_rows($verifyResult) == 0) {
            mysqli_stmt_close($stmt_verify);
            header("Location: section_list.php");
            exit();
        }
        
        $sectionInfo = mysqli_fetch_assoc($verifyResult);
        mysqli_stmt_close($stmt_verify);
    } else {
         // Handle prepare error
        error_log("Verify section query failed: " . mysqli_error($conn));
        header("Location: section_list.php");
        exit();
    }
    
    // Get students in this specific section (using prepared statement)
    $qur = "SELECT sm.*, bm.BranchName, sec.SectionNumber AS StudentSectionName
            FROM studentmaster sm 
            INNER JOIN branchmaster bm ON sm.StudentBranchCode = bm.BranchCode 
            INNER JOIN sectionmaster sec ON sm.StudentSection = sec.SectionId
            WHERE sm.StudentSection = ?
            ORDER BY sm.StudentRollNo, sm.StudentFirstName";
    
    $stmt_students = mysqli_prepare($conn, $qur);
    if ($stmt_students) {
        mysqli_stmt_bind_param($stmt_students, "i", $sectionId);
        mysqli_stmt_execute($stmt_students);
        $res = mysqli_stmt_get_result($stmt_students);
        // $res now contains the student data
    } else {
        // Handle prepare error
        error_log("Get students query failed: " . mysqli_error($conn));
        $res = false; // Indicate error
    }
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
                color: white;
                text-decoration: none; /* Ensure no underline on hover */
            }
        
        .card {
            z-index: 10;
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.2);
            border-radius: 0.75rem;
            background-color: white;
        }

        .section-info {
            background: linear-gradient(135deg, #667eea 0%, #4b58a2ff 100%);
            color: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        /* Ensure table text aligns nicely with avatar */
        .student-name-cell {
            display: flex;
            align-items: center;
        }

        .student-name-cell .avatar {
            margin-right: 0.5rem; /* Space between avatar and name */
        }

        /* Optional: Style for the section column if it needs specific formatting */
        .item-section {
            /* Add styles if needed, e.g., font-weight, color */
        }
    </style>
</head>

<body>
    <?php $nav_role = "Student"; ?>
    <!-- NAVIGATION -->
    <?php include_once("nav.php"); ?>
    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12">
                    <!-- Header -->
                    <div class="header">
                        <div class="header-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="header-pretitle">
                                        View
                                    </h6>
                                    <!-- Title -->
                                    <h1 class="header-title text-truncate">
                                        Section Students
                                    </h1>
                                </div>
                                <!-- Right column for Back button -->
                                <div class="col-auto text-end">
                                    <h5 class="header-pretitle">
                                        <a href="section_list.php" class="back-btn">
                                            <i class="fe uil-angle-double-left"></i> Back
                                        </a>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section Info Card -->
                    <div class="section-info">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-1"><?php echo htmlspecialchars($sectionInfo['SectionNumber']); ?></h3>
                                <p class="mb-0 opacity-75"><?php echo htmlspecialchars($sectionInfo['BranchName']); ?></p>
                            </div>
                            <div class="col-auto">
                                <div class="text-end">
                                    <h4 class="mb-0"><?php echo $res ? mysqli_num_rows($res) : 0; ?></h4>
                                    <small class="opacity-75">Total Students</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tab content -->
                    <?php if ($res && mysqli_num_rows($res)) { ?>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="contactsListPane" role="tabpanel">
                                <!-- Card -->
                                <div class="card" data-list='{"valueNames": ["item-roll", "item-name", "item-enroll", "item-section", "item-phone", "item-score"], "page": 10, "pagination": {"paginationClass": "list-pagination"}}' id="contactsList">
                                    <div class="card-header">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <!-- Form -->
                                                <form autocomplete="off">
                                                    <div class="input-group input-group-flush input-group-merge input-group-reverse">
                                                        <input class="form-control list-search" type="search" placeholder="Search students...">
                                                        <span class="input-group-text">
                                                            <i class="fe fe-search"></i>
                                                        </span>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="col-auto">
                                                <span class="text-muted">
                                                    <i class="fe fe-users me-1"></i>
                                                    <?php echo mysqli_num_rows($res); ?> students in this section
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover table-nowrap card-table">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <a class="list-sort text-muted" data-sort="item-roll">Roll No.</a>
                                                    </th>
                                                    <th>
                                                        <a class="list-sort text-muted" data-sort="item-name">Name</a>
                                                    </th>
                                                    <th>
                                                        <a class="list-sort text-muted" data-sort="item-enroll">Enrollment No.</a>
                                                    </th>
                                                    <th>
                                                        <a class="list-sort text-muted" data-sort="item-section">Section</a> <!-- New Column Header -->
                                                    </th>
                                                    <th>
                                                        <a class="list-sort text-muted" data-sort="item-phone">Phone</a>
                                                    </th>
                                                    <th>
                                                        <a class="list-sort text-muted" data-sort="item-score">Quarter</a>
                                                    </th>
                                                    <th>
                                                        <a class="list-sort text-muted justify-content-center">Action</a>
                                                    </th>
                                                    <th>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="list font-size-base">
                                                <?php while ($row = mysqli_fetch_assoc($res)) {
                                                    // Construct full name
                                                    $full_name = trim($row['StudentFirstName'] . " " . $row['StudentLastName']);
                                                    if (empty($full_name)) {
                                                        $full_name = "N/A";
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <span class="item-roll text-reset fw-bold"><?php echo htmlspecialchars($row['StudentRollNo']); ?></span>
                                                        </td>
                                                        <td class="student-name-cell">
                                                            <!-- Avatar -->
                                                            <div class="avatar avatar-xs align-middle">
                                                                <img class="avatar-img rounded-circle" src="../src/uploads/stuprofile/<?php echo urlencode(htmlspecialchars($row['StudentProfilePic'])); ?>" alt="<?php echo htmlspecialchars($full_name); ?>'s Profile Picture" onerror="this.onerror=null; this.src='../assets/img/avatars/default.png';">
                                                            </div>
                                                            <a class="item-name text-reset"><?php echo htmlspecialchars($full_name); ?></a>
                                                        </td>
                                                        <td>
                                                            <!-- Email -->
                                                            <span class="item-enroll text-reset"><?php echo htmlspecialchars($row['StudentEnrollmentNo']); ?></span>
                                                        </td>
                                                        <td> <!-- New Column Data -->
                                                            <span class="item-section text-reset"><?php echo htmlspecialchars($row['StudentSectionName']); ?></span>
                                                        </td>
                                                        <td>
                                                            <!-- Phone -->
                                                            <span class="item-phone text-reset"><?php echo htmlspecialchars($row['StudentContactNo']); ?></span>
                                                        </td>
                                                        <td>
                                                            <!-- Badge -->
                                                            <span class="item-score text-reset"><?php echo htmlspecialchars($row['StudentSemester']); ?></span>
                                                        </td>
                                                        <td>
                                                            <a href="student_profile.php?studentenr=<?php echo urlencode($row['StudentEnrollmentNo']); ?>" class="btn btn-sm btn-info">
                                                                <i class="fe fe-eye me-1"></i> View
                                                            </a>
                                                        </td>
                                                        <td class="text-right">
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-footer d-flex justify-content-between">
                                        <!-- Pagination (prev) -->
                                        <ul class="list-pagination-prev pagination pagination-tabs card-pagination">
                                            <li class="page-item">
                                                <a class="page-link pl-0 pr-4 border-right" href="#">
                                                    <i class="fe fe-arrow-left mr-1"></i> Prev
                                                </a>
                                            </li>
                                        </ul>
                                        <!-- Pagination -->
                                        <ul class="list-pagination pagination pagination-tabs card-pagination">
                                            <li class="active"><a class="page" href="javascript:function Z(){Z=&quot;&quot;}Z()">1</a></li>
                                            <li><a class="page" href="javascript:function Z(){Z=&quot;&quot;}Z()">2</a></li>
                                            <li><a class="page" href="javascript:function Z(){Z=&quot;&quot;}Z()">3</a></li>
                                        </ul>
                                        <!-- Pagination (next) -->
                                        <ul class="list-pagination-next pagination pagination-tabs card-pagination">
                                            <li class="page-item">
                                                <a class="page-link pl-4 pr-0 border-left" href="#">
                                                    Next <i class="fe fe-arrow-right ml-1"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php } else { ?>
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center py-5">
                                    <i class="fe fe-users text-muted mb-3" style="font-size: 4rem;"></i>
                                    <h3 class="text-muted">No Students Found</h3>
                                    <p class="text-muted">There are no students registered in this section yet.</p>
                                    <a href="section_list.php" class="btn btn-primary">
                                        <i class="fe fe-arrow-left me-1"></i> Back to Sections
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php }
                    // Close prepared statement for students if it was used
                    if (isset($stmt_students) && $stmt_students) {
                         mysqli_stmt_close($stmt_students);
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php include("context.php"); ?>
    
    <!-- JAVASCRIPT -->
    <script src="../assets/js/vendor.bundle.js"></script>
    <script src="../assets/js/theme.bundle.js"></script>

</body>

</html>
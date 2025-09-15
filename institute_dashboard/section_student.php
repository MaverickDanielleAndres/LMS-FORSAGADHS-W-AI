<?php
session_start();
error_reporting(E_ALL ^ E_WARNING);

// Check if user is logged in as Institute
if ($_SESSION['role'] != "Texas" || $_SESSION['userrole'] != "Institute") {
    header("Location: ../index.php");
    exit();
} else {
    include_once("../config.php");

    // Get section ID from URL parameter
    $sectionId = isset($_GET['section_id']) ? (int)$_GET['section_id'] : 0;

    // Validate section ID and fetch section details
    if ($sectionId > 0) {
        $section_query = "SELECT s.SectionId, s.SectionNumber, s.SectionBranch, b.BranchName
                          FROM sectionmaster s
                          INNER JOIN branchmaster b ON s.SectionBranch = b.BranchName
                          WHERE s.SectionId = ?";
        $stmt = mysqli_prepare($conn, $section_query);
        mysqli_stmt_bind_param($stmt, "i", $sectionId);
        mysqli_stmt_execute($stmt);
        $section_result = mysqli_stmt_get_result($stmt);
        $sectionInfo = mysqli_fetch_assoc($section_result);
        mysqli_stmt_close($stmt);

        if (!$sectionInfo) {
            // Section not found
            header("Location: section_list.php");
            exit();
        }

        // Get students in this specific section
        $student_query = "SELECT sm.*, bm.BranchName as StudentBranchName
                          FROM studentmaster sm
                          INNER JOIN branchmaster bm ON sm.StudentBranchCode = bm.BranchCode
                          WHERE sm.StudentSection = ?
                          ORDER BY sm.StudentRollNo, sm.StudentFirstName";
        $stmt_students = mysqli_prepare($conn, $student_query);
        mysqli_stmt_bind_param($stmt_students, "i", $sectionId);
        mysqli_stmt_execute($stmt_students);
        $res = mysqli_stmt_get_result($stmt_students);
        // $res now contains the student data

    } else {
        // Invalid section ID
        header("Location: section_list.php");
        exit();
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
    </style>
</head>

<body>
    <?php $nav_role = "Section"; // Assuming Section is the correct nav role ?>
    <!-- NAVIGATION -->
    <?php include_once("../nav.php"); ?>
    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12">
                    <!-- Header -->
                    <div class="header mt-md-5">
                        <div class="header-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="header-pretitle">
                                        View
                                    </h6>
                                    <!-- Title -->
                                    <h1 class="header-title text-truncate">
                                        Students in Section
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
                                    <h4 class="mb-0"><?php echo mysqli_num_rows($res); ?></h4>
                                    <small class="opacity-75">Total Students</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab content -->
                    <?php if (mysqli_num_rows($res) > 0) { ?>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="studentsListPane" role="tabpanel">
                                <!-- Card -->
                                <div class="card" data-list='{"valueNames": ["item-roll", "item-name", "item-enroll", "item-phone", "item-email"], "page": 10, "pagination": {"paginationClass": "list-pagination"}}' id="studentsList">
                                    <div class="card-header">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <!-- Search Form -->
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
                                                        <a class="list-sort text-muted" data-sort="item-phone">Phone</a>
                                                    </th>
                                                    <th>
                                                        <a class="list-sort text-muted" data-sort="item-email">Email</a>
                                                    </th>
                                                    <th class="text-end">
                                                        <a class="text-muted">Action</a> <!-- No sort for Action column -->
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
                                                            <span class="item-name text-reset"><?php echo htmlspecialchars($full_name); ?></span>
                                                        </td>
                                                        <td>
                                                            <span class="item-enroll text-reset"><?php echo htmlspecialchars($row['StudentEnrollmentNo']); ?></span>
                                                        </td>
                                                        <td>
                                                            <span class="item-phone text-reset"><?php echo htmlspecialchars($row['StudentContactNo']); ?></span>
                                                        </td>
                                                        <td>
                                                            <span class="item-email text-reset"><?php echo htmlspecialchars($row['StudentEmail']); ?></span>
                                                        </td>
                                                        <td class="text-end">
                                                            <!-- View Profile Button -->
                                                            
                                                            <a href="edit_student.php?studentenr=<?php echo $row['StudentEnrollmentNo']; ?>" class="btn btn-sm btn-warning">
                                                            Edit
                                                        </a>
                                                        &nbsp;
                                                        <a class="btn btn-sm btn-danger" href="sdelete.php?studentenr=<?php echo $row['StudentEnrollmentNo']; ?>" onclick="if (! confirm('Are You Sure, You want to Delete this Student ?')) return false;">
                                                            Delete
                                                        </a>
                                                        &nbsp;
                                                        <a href="student_profile.php?studentenr=<?php echo $row['StudentEnrollmentNo']; ?>" class="btn btn-sm btn-info">
                                                            <i class="fe fe-eye me-1"></i> View Profile
                                                        </a>
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
                                            <!-- Add more pages dynamically if needed -->
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
                                    <p class="text-muted">There are no students registered in the section <?php echo htmlspecialchars($sectionInfo['SectionNumber']); ?>.</p>
                                    <a href="section_list.php" class="btn btn-primary">
                                        <i class="fe fe-arrow-left me-1"></i> Back
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php }
                    // Close prepared statement for students if it was used
                    if (isset($stmt_students)) {
                         mysqli_stmt_close($stmt_students);
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <!-- JAVASCRIPT -->
    <script src='https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.js'></script>
    <script src="../assets/js/vendor.bundle.js"></script>
    <script src="../assets/js/theme.bundle.js"></script>
</body>

</html>
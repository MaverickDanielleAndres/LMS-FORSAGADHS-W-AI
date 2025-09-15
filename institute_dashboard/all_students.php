
<?php
session_start();
error_reporting(E_ALL ^ E_WARNING);

// Check if user is logged in as Institute
if ($_SESSION['role'] != "Texas" || $_SESSION['userrole'] != "Institute") {
    header("Location: ../index.php");
    exit();
} else {
    include_once("../config.php");

    // Get filter parameters from URL
    $filter_branch_id = isset($_GET['branch_id']) && is_numeric($_GET['branch_id']) ? (int)$_GET['branch_id'] : 0;
    $filter_section_id = isset($_GET['section_id']) && is_numeric($_GET['section_id']) ? (int)$_GET['section_id'] : 0;

    // Base query to fetch students with branch and section details
    $base_query = "SELECT sm.*, bm.BranchName, sec.SectionNumber
                   FROM studentmaster sm
                   INNER JOIN branchmaster bm ON sm.StudentBranchCode = bm.BranchCode
                   LEFT JOIN sectionmaster sec ON sm.StudentSection = sec.SectionId";

    // Build WHERE clause based on filters
    $where_conditions = [];
    $params = [];
    $param_types = "";

    if ($filter_branch_id > 0) {
        $where_conditions[] = "bm.BranchId = ?";
        $params[] = $filter_branch_id;
        $param_types .= "i";
    }

    if ($filter_section_id > 0) {
        $where_conditions[] = "sm.StudentSection = ?";
        $params[] = $filter_section_id;
        $param_types .= "i";
    }

    $where_clause = "";
    if (!empty($where_conditions)) {
        $where_clause = " WHERE " . implode(" AND ", $where_conditions);
    }

    // Final query with filters
    $final_query = $base_query . $where_clause . " ORDER BY bm.BranchName, sec.SectionNumber, sm.StudentRollNo, sm.StudentFirstName";

    // Execute the query
    if (!empty($params)) {
        $stmt = mysqli_prepare($conn, $final_query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, $param_types, ...$params);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
        } else {
            // Handle prepare error
            error_log("MySQL Prepare Error in all_student.php: " . mysqli_error($conn));
            $result = false;
        }
    } else {
        // No filters, execute base query
        $result = mysqli_query($conn, $final_query);
    }

    // Fetch data for filter dropdowns
    $branches_query = "SELECT BranchId, BranchName FROM branchmaster ORDER BY BranchName";
    $branches_result = mysqli_query($conn, $branches_query);

    $sections_query = "SELECT SectionId, SectionNumber, SectionBranch FROM sectionmaster ORDER BY SectionBranch, SectionNumber";
    $sections_result = mysqli_query($conn, $sections_query);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    $nav_role = "Student"; // Adjusted based on typical navigation
    include_once("../head.php"); ?>
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
        }

        /* Filter styles */
        .filter-row {
            margin-bottom: 1rem;
        }
        .filter-label {
            font-weight: bold;
            margin-right: 0.5rem;
        }
        .filter-select {
            width: auto; /* Adjust width as needed */
            display: inline-block;
            margin-right: 1rem;
        }
        .filter-btn {
             margin-right: 0.5rem;
        }
        .clear-filters-btn {
            /* Optional: Style for clear button */
        }

        /* Ensure table text aligns nicely with avatar */
        .student-name-cell {
            display: flex;
            align-items: center;
        }
        .student-name-cell .avatar {
            margin-right: 0.5rem; /* Space between avatar and name */
        }

        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            .filter-select {
                width: 100%;
                margin-bottom: 0.5rem;
            }
            .filter-row .col-auto {
                width: 100%;
                text-align: left !important; /* Force left align on small screens */
            }
            .filter-row .col-auto .d-flex {
                 justify-content: flex-start !important; /* Align buttons to start */
            }
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
                <div class="col-12">
                    <!-- Header -->
                    <div class="header mt-md-5">
                        <div class="header-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="header-pretitle">
                                        <a class="btn-link btn-outline" onclick="history.back()"><i class="fe uil-angle-double-left"></i>Back</a>
                                    </h6>
                                    <h1 class="header-title text-truncate">
                                        All Students
                                    </h1>
                                </div>
                                <div class="col-auto">
                                    <a href="add_student.php" class="btn btn-primary ml-2"> <!-- Assuming add_student.php exists -->
                                        <i class="fe fe-plus"></i> Add Student
                                    </a>
                                </div>
                            </div> <!-- / .row -->

                            <!-- Filter Row -->
                            <div class="row filter-row align-items-center">
                                <div class="col">
                                    <form method="GET" id="filterForm" class="d-flex flex-wrap align-items-center">
                                        <label for="branchFilter" class="filter-label">Grade Level:</label>
                                        <select name="branch_id" id="branchFilter" class="form-select filter-select">
                                            <option value="0">All Grade Levels</option>
                                            <?php
                                            if ($branches_result && mysqli_num_rows($branches_result) > 0) {
                                                mysqli_data_seek($branches_result, 0); // Reset pointer
                                                while ($branch = mysqli_fetch_assoc($branches_result)) {
                                                    $selected = ($branch['BranchId'] == $filter_branch_id) ? 'selected' : '';
                                                    echo '<option value="' . htmlspecialchars($branch['BranchId']) . '" ' . $selected . '>' . htmlspecialchars($branch['BranchName']) . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>

                                        <label for="sectionFilter" class="filter-label">Section:</label>
                                        <select name="section_id" id="sectionFilter" class="form-select filter-select">
                                            <option value="0">All Sections</option>
                                            <?php
                                            if ($sections_result && mysqli_num_rows($sections_result) > 0) {
                                                mysqli_data_seek($sections_result, 0); // Reset pointer
                                                while ($section = mysqli_fetch_assoc($sections_result)) {
                                                    $selected = ($section['SectionId'] == $filter_section_id) ? 'selected' : '';
                                                    // Optionally, you could group sections by branch using <optgroup> if needed
                                                    echo '<option value="' . htmlspecialchars($section['SectionId']) . '" ' . $selected . '>' . htmlspecialchars($section['SectionBranch']) . ' - ' . htmlspecialchars($section['SectionNumber']) . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>

                                        <div class="col-auto ms-auto"> <!-- Push buttons to the right -->
                                            <button type="submit" class="btn btn-primary filter-btn">
                                                <i class="fe fe-filter"></i> Apply Filters
                                            </button>
                                            <?php if ($filter_branch_id > 0 || $filter_section_id > 0) {
                                                // Build the clear filter URL by removing filter parameters
                                                $current_url = strtok($_SERVER["REQUEST_URI"], '?'); // Get base URL without query string
                                                // We want to clear filters, so we link back to the base URL without parameters
                                                // Or, if there are other non-filter parameters you want to keep, you'd need to rebuild the query string
                                                // For simplicity, assuming only filter params are relevant here.
                                                $clear_url = $current_url; // This effectively removes all GET params
                                            ?>
                                                <a href="<?php echo htmlspecialchars($clear_url); ?>" class="btn btn-outline-secondary clear-filters-btn">
                                                    <i class="fe fe-x"></i> Clear Filters
                                                </a>
                                            <?php } ?>
                                        </div>
                                    </form>
                                </div>
                            </div> <!-- / .filter-row -->

                            <div class="row align-items-center">
                                <div class="col">
                                    <!-- Nav Tabs -->
                                    <ul class="nav nav-tabs nav-overflow header-tabs">
                                        <li class="nav-item">
                                            <a href="#!" class="nav-link text-nowrap active">
                                                Filtered Students
                                                <?php if ($result) { ?>
                                                    <span class="badge rounded-pill bg-soft-secondary"><?php echo mysqli_num_rows($result); ?></span>
                                                <?php } else { ?>
                                                    <span class="badge rounded-pill bg-soft-secondary">0</span>
                                                <?php } ?>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div> <!-- / .header -->

                    <!-- Tab content -->
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="studentsListPane" role="tabpanel">
                            <!-- Card -->
                            <div class="card" data-list='{"valueNames": ["item-name", "item-enroll", "item-phone", "item-branch", "item-section"], "page": 10, "pagination": {"paginationClass": "list-pagination"}}' id="studentsList">
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
                                            <?php if ($result) { ?>
                                                <span class="text-muted">
                                                    <i class="fe fe-users me-1"></i>
                                                    <?php echo mysqli_num_rows($result); ?> student(s) found
                                                </span>
                                            <?php } ?>
                                        </div>
                                    </div> <!-- / .row -->
                                </div> <!-- / .card-header -->

                                <div class="table-responsive">
                                    <table class="table table-sm table-hover table-nowrap card-table">
                                        <thead>
                                            <tr>
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
                                                    <a class="list-sort text-muted" data-sort="item-branch">Grade Level</a>
                                                </th>
                                                <th>
                                                    <a class="list-sort text-muted" data-sort="item-section">Section</a>
                                                </th>
                                                <th>
                                                    <a class="list-sort text-muted" data-sort="item-roll">Roll No.</a>
                                                </th>
                                                <th class="text-end">
                                                    <a class="text-muted">Action</a> <!-- No sort for Action column -->
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="list font-size-base">
                                            <?php
                                            if ($result && mysqli_num_rows($result) > 0) {
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    // Construct full name
                                                    $full_name = trim($row['StudentFirstName'] . " " . $row['StudentLastName']);
                                                    if (empty($full_name)) {
                                                        $full_name = "N/A";
                                                    }
                                                    // Handle potentially missing section name
                                                    $section_display = !empty($row['SectionNumber']) ? $row['SectionNumber'] : 'N/A';
                                                    ?>
                                                    <tr>
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
                                                            <span class="item-branch text-reset"><?php echo htmlspecialchars($row['BranchName']); ?></span>
                                                        </td>
                                                        <td>
                                                            <span class="item-section text-reset"><?php echo htmlspecialchars($section_display); ?></span>
                                                        </td>
                                                        <td>
                                                            <span class="item-roll text-reset fw-bold"><?php echo htmlspecialchars($row['StudentRollNo']); ?></span>
                                                        </td>
                                                        <td class="text-end">
                                                            <!-- Edit Button -->
                                                            <a href="edit_student.php?studentenr=<?php echo urlencode($row['StudentEnrollmentNo']); ?>" class="btn btn-sm btn-warning me-1">
                                                                <i class="fe fe-edit me-1"></i> Edit
                                                            </a>
                                                            <!-- Delete Button -->
                                                            <a class="btn btn-sm btn-danger me-1" href="sdelete.php?studentenr=<?php echo urlencode($row['StudentEnrollmentNo']); ?>" onclick="return confirm('Are You Sure, You want to Delete this Student?');">
                                                                <i class="fe fe-trash me-1"></i> Delete
                                                            </a>
                                                            <!-- View Profile Button -->
                                                            <a href="student_profile.php?studentenr=<?php echo urlencode($row['StudentEnrollmentNo']); ?>" class="btn btn-sm btn-info">
                                                                <i class="fe fe-eye me-1"></i> View
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php }
                                            } else { ?>
                                                <tr>
                                                    <td colspan="7" class="text-center">
                                                        <?php if ($result === false) { ?>
                                                            <i class="fe fe-alert-triangle text-danger mb-2" style="font-size: 2rem;"></i>
                                                            <h4 class="text-danger">Error loading student data.</h4>
                                                            <p>Please try again later.</p>
                                                        <?php } else { ?>
                                                            <i class="fe fe-users text-muted mb-2" style="font-size: 2rem;"></i>
                                                            <h4 class="text-muted">No Students Found</h4>
                                                            <p>Try adjusting your filters or search term.</p>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div> <!-- / .table-responsive -->

                                <?php if ($result && mysqli_num_rows($result) > 0) { ?>
                                    <div class="card-footer d-flex justify-content-between">
                                        <!-- Pagination (prev) -->
                                        <ul class="list-pagination-prev pagination pagination-tabs card-pagination">
                                            <li class="page-item">
                                                <a class="page-link pl-0 pr-4 border-right" href="#">
                                                    <i class="fe fe-arrow-left me-1"></i> Prev
                                                </a>
                                            </li>
                                        </ul>
                                        <!-- Pagination -->
                                        <ul class="list-pagination pagination pagination-tabs card-pagination">
                                            <li class="active"><a class="page" href="javascript:void(0)">1</a></li>
                                            <!-- Add more pages dynamically if needed -->
                                        </ul>
                                        <!-- Pagination (next) -->
                                        <ul class="list-pagination-next pagination pagination-tabs card-pagination">
                                            <li class="page-item">
                                                <a class="page-link pl-4 pr-0 border-left" href="#">
                                                    Next <i class="fe fe-arrow-right ms-1"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div> <!-- / .card-footer -->
                                <?php } ?>
                            </div> <!-- / .card -->
                        </div> <!-- / .tab-pane -->
                    </div> <!-- / .tab-content -->
                </div> <!-- / .col-12 -->
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
    <script>
        // Optional: Add JavaScript for enhanced interactions if needed
        // For example, clearing filters or dynamic section loading based on branch
        // could be implemented here.
    </script>
</body>
</html>
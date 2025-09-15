<?php
session_start();
error_reporting(E_ALL ^ E_WARNING);
if ($_SESSION['role'] != "Texas") {
    header("Location: ../index.php");
} else {
    include_once("../config.php");
    $_SESSION["userrole"] = "Institute";

    // Get filter parameter
    $filter_branch_id = isset($_GET['branch_id']) && is_numeric($_GET['branch_id']) ? (int)$_GET['branch_id'] : 0;

    // Base query to fetch faculty with branch details and associated sections
    // Using GROUP_CONCAT to list section numbers for each faculty
    $base_query = "SELECT 
                        fm.*, 
                        bm.BranchName,
                        GROUP_CONCAT(
                            CASE 
                                WHEN fs.IsActive = 1 THEN sm.SectionNumber 
                                ELSE NULL 
                            END 
                            ORDER BY sm.SectionNumber 
                            SEPARATOR ', '
                        ) AS SectionsList
                   FROM facultymaster fm
                   INNER JOIN branchmaster bm ON bm.BranchCode = fm.FacultyBranchCode
                   LEFT JOIN facultysection fs ON fm.FacultyId = fs.FacultyId
                   LEFT JOIN sectionmaster sm ON fs.SectionId = sm.SectionId";

    // Build WHERE clause based on filter
    $where_conditions = [];
    $params = [];
    $param_types = "";

    if ($filter_branch_id > 0) {
        $where_conditions[] = "bm.BranchId = ?";
        $params[] = $filter_branch_id;
        $param_types .= "i";
    }

    $where_clause = "";
    if (!empty($where_conditions)) {
        $where_clause = " WHERE " . implode(" AND ", $where_conditions);
    }

    // Group by faculty to avoid duplicates and get concatenated sections
    $group_clause = " GROUP BY fm.FacultyId";

    // Final query with filter and grouping
    $final_query = $base_query . $where_clause . $group_clause . " ORDER BY fm.FacultyFirstName";

    // Execute the query
    if (!empty($params)) {
        $stmt = mysqli_prepare($conn, $final_query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, $param_types, ...$params);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
        } else {
            // Handle prepare error
            error_log("MySQL Prepare Error in faculty_list.php: " . mysqli_error($conn));
            $res = false; // Indicate error
        }
    } else {
        // No filter, execute base query
        $res = mysqli_query($conn, $final_query);
    }

    // Fetch branches for the filter dropdown
    $branch_query = "SELECT BranchId, BranchName FROM branchmaster ORDER BY BranchName";
    $branch_result = mysqli_query($conn, $branch_query);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    $nav_role = "Faculty";
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

        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            .filter-select {
                width: 100%;
                margin-bottom: 0.5rem;
            }
            .filter-row .col-auto {
                width: 100%;
                text-align: left !important;
            }
            .filter-row .col-auto .d-flex {
                 justify-content: flex-start !important;
            }
        }

        /* Style for sections list cell */
        .item-sections {
            font-size: 0.9em; /* Slightly smaller text */
        }
        .no-sections {
            color: #6c757d; /* Muted color for 'None' */
            font-style: italic;
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
                    <div class="header">
                        <div class="header-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <!-- Title -->
                                     <h5 class="header-pretitle">
                                        <a class="btn-link btn-outline" onclick="history.back()"><i class="fe uil-angle-double-left"></i>Back</a>
                                     </h5>
                                    <h1 class="header-title text-truncate">
                                        Faculty List
                                    </h1>
                                </div>
                                <div class="col-auto">
                                    <a href="add_faculty.php" class="btn btn-primary ml-2">
                                        Add Faculty
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
                                            if ($branch_result && mysqli_num_rows($branch_result) > 0) {
                                                mysqli_data_seek($branch_result, 0); // Reset pointer
                                                while ($branch = mysqli_fetch_assoc($branch_result)) {
                                                    $selected = ($branch['BranchId'] == $filter_branch_id) ? 'selected' : '';
                                                    echo '<option value="' . htmlspecialchars($branch['BranchId']) . '" ' . $selected . '>' . htmlspecialchars($branch['BranchName']) . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>

                                        <div class="col-auto ms-auto"> <!-- Push buttons to the right -->
                                            <button type="submit" class="btn btn-primary filter-btn">
                                                <i class="fe fe-filter"></i> Apply Filters
                                            </button>
                                            <?php if ($filter_branch_id > 0) { ?>
                                                <a href="faculty_list.php" class="btn btn-outline-secondary clear-filters-btn">
                                                    <i class="fe fe-x"></i> Clear Filters
                                                </a>
                                            <?php } ?>
                                        </div>
                                    </form>
                                </div>
                            </div> <!-- / .filter-row -->

                            <div class="row align-items-center">
                                <div class="col">
                                    <!-- Nav -->
                                    <ul class="nav nav-tabs nav-overflow header-tabs">
                                        <li class="nav-item">
                                            <a href="#!" class="nav-link text-nowrap active">
                                                <?php if ($res && $res !== false) { ?>
                                                    Filtered Faculty <span class="badge rounded-pill bg-soft-secondary"><?php echo mysqli_num_rows($res); ?></span>
                                                <?php } else { ?>
                                                    Faculty List
                                                <?php } ?>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div> <!-- / .header-body -->
                    </div> <!-- / .header -->

                    <!-- Tab content -->
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="contactsListPane" role="tabpanel" aria-labelledby="contactsListTab">
                            <!-- Card -->
                            <div class="card" data-list='{"valueNames": ["item-name", "item-email", "item-phone", "item-branch", "item-sections"], "page": 10, "pagination": {"paginationClass": "list-pagination"}}' id="contactsList">
                                <div class="card-header">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <!-- Form -->
                                            <form autocomplete="off">
                                                <div class="input-group input-group-flush input-group-merge input-group-reverse">
                                                    <input class="form-control list-search" type="search" placeholder="Search">
                                                    <span class="input-group-text">
                                                        <i class="fe fe-search"></i>
                                                    </span>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="col-auto">
                                             <?php if ($res && $res !== false) { ?>
                                                <span class="text-muted">
                                                    <i class="fe fe-users me-1"></i>
                                                    <?php echo mysqli_num_rows($res); ?> faculty member(s) found
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
                                                    <a class="list-sort text-muted" data-sort="item-email">Email</a>
                                                </th>
                                                <th>
                                                    <a class="list-sort text-muted" data-sort="item-phone">Phone</a>
                                                </th>
                                                <th>
                                                    <a class="list-sort text-muted" data-sort="item-branch">Grade Level</a>
                                                </th>
                                                <th>
                                                    <a class="list-sort text-muted" data-sort="item-sections">Sections</a> <!-- New Column Header -->
                                                </th>
                                                <th>
                                                    <a class="list-sort text-muted justify-content-center">Action</a>
                                                </th>
                                                <th>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="list font-size-base">
                                            <?php
                                            if ($res && mysqli_num_rows($res) > 0) {
                                                while ($row = mysqli_fetch_assoc($res)) {
                                                    // Handle sections list display
                                                    $sections_display = !empty($row['SectionsList']) ? htmlspecialchars($row['SectionsList']) : '<span class="no-sections">None</span>';
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <!-- Avatar -->
                                                            <div class="avatar avatar-xs align-middle mr-2">
                                                                <img class="avatar-img rounded-circle" src="../src/uploads/facprofile/<?php echo urlencode(htmlspecialchars($row['FacultyProfilePic'])); ?>" alt="<?php echo htmlspecialchars($row['FacultyFirstName'] . ' ' . $row['FacultyLastName']); ?>'s Profile Picture" onerror="this.onerror=null; this.src='../assets/img/avatars/default.png';">
                                                            </div>
                                                            <span class="item-name text-reset"><?php echo htmlspecialchars($row['FacultyFirstName'] . " " . $row['FacultyLastName']); ?></span>
                                                        </td>
                                                        <td>
                                                            <span class="item-email text-reset"><?php echo htmlspecialchars($row['FacultyEmail']); ?></span>
                                                        </td>
                                                        <td>
                                                            <span class="item-phone text-reset"><?php echo htmlspecialchars($row['FacultyContactNo']); ?></span>
                                                        </td>
                                                        <td>
                                                            <span class="item-branch text-reset"><?php echo htmlspecialchars($row['BranchName']); ?></span>
                                                        </td>
                                                        <td> <!-- New Column Data -->
                                                            <span class="item-sections text-reset"><?php echo $sections_display; ?></span>
                                                        </td>
                                                        <td>
                                                            <a href="edit_faculty.php?facid=<?php echo urlencode($row['FacultyId']); ?>" class="btn btn-sm btn-warning me-1">
                                                                Edit
                                                            </a>
                                                            <a class="btn btn-sm btn-danger me-1" href="fdelete.php?facid=<?php echo urlencode($row['FacultyId']); ?>" onclick="return confirm('Are You Sure, You want to Delete this Faculty?');">
                                                                Delete
                                                            </a>
                                                            <a href="faculty_profile.php?facultycode=<?php echo urlencode($row['FacultyId']); ?>" class="btn btn-sm btn-info">
                                                                View
                                                            </a>
                                                        </td>
                                                        <td class="text-right">
                                                        </td>
                                                    </tr>
                                                <?php }
                                            } else { ?>
                                                <tr>
                                                    <td colspan="7" class="text-center"> <!-- Updated colspan -->
                                                        <?php if ($res === false) { ?>
                                                            <i class="fe fe-alert-triangle text-danger mb-2" style="font-size: 2rem;"></i>
                                                            <h4 class="text-danger">Error loading faculty data.</h4>
                                                            <p>Please try again later.</p>
                                                        <?php } else { ?>
                                                            <i class="fe fe-users text-muted mb-2" style="font-size: 2rem;"></i>
                                                            <h4 class="text-muted">No Faculty Found</h4>
                                                            <p>Try adjusting your filters or search term.</p>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div> <!-- / .table-responsive -->
                                <?php if ($res && mysqli_num_rows($res) > 0) { ?>
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
                                            <li class="active"><a class="page" href="javascript:void(0)">1</a></li>
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
    <?php // include_once("context.php"); // Removed duplicate include ?>
</body>
</html>
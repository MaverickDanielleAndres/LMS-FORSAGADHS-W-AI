<?php
session_start();
error_reporting(E_ALL ^ E_WARNING);

if ($_SESSION['role'] != "Texas") {
    header("Location: ../index.php");
} else {
    include_once("../config.php");
    $_SESSION["userrole"] = "Institute";

    // Get filter parameters
    $filter_branch_id = isset($_GET['branch_id']) && is_numeric($_GET['branch_id']) ? (int)$_GET['branch_id'] : 0;
    $filter_semester = isset($_GET['semester']) && is_numeric($_GET['semester']) && $_GET['semester'] > 0 ? (int)$_GET['semester'] : 0;

    // Base query to fetch subjects with branch details
    $base_query = "SELECT sm.*, bm.BranchName
                   FROM subjectmaster sm
                   INNER JOIN branchmaster bm ON sm.SubjectBranch = bm.BranchId"; // Join with BranchId

    // Build WHERE clause based on filters
    $where_conditions = [];
    $params = [];
    $param_types = "";

    if ($filter_branch_id > 0) {
        $where_conditions[] = "bm.BranchId = ?";
        $params[] = $filter_branch_id;
        $param_types .= "i";
    }

    if ($filter_semester > 0) {
        $where_conditions[] = "sm.SubjectSemester = ?"; // Filter by Semester
        $params[] = $filter_semester;
        $param_types .= "i";
    }

    $where_clause = "";
    if (!empty($where_conditions)) {
        $where_clause = " WHERE " . implode(" AND ", $where_conditions);
    }

    // Final query with filters
    $final_query = $base_query . $where_clause . " ORDER BY bm.BranchName, sm.SubjectSemester, sm.SubjectName";

    // Execute the query
    if (!empty($params)) {
        $stmt = mysqli_prepare($conn, $final_query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, $param_types, ...$params);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
        } else {
            // Handle prepare error
            error_log("MySQL Prepare Error in subject_list.php: " . mysqli_error($conn));
            $res = false; // Indicate error
        }
    } else {
        // No filters, execute base query
        $res = mysqli_query($conn, $final_query);
    }

    // Fetch branches for the filter dropdown
    $branch_query = "SELECT BranchId, BranchName FROM branchmaster ORDER BY BranchName";
    $branch_result = mysqli_query($conn, $branch_query);

    // Determine max semesters for quarter filter (assuming max across branches or use a fixed number like 4)
    // For simplicity, we'll use 4 quarters. You could fetch max dynamically if needed.
    $max_quarters = 4;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    $nav_role = "Subject"; // Adjust based on your navigation structure
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

        /* Modal styles (if needed, copied from original) */
        .add-subject-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .add-subject-modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 400px;
            border-radius: 10px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
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
                                    <h5 class="header-pretitle">
                                        <a class="btn-link btn-outline" onclick="history.back()"><i class="fe uil-angle-double-left"></i>Back</a>
                                    </h5>
                                    <h1 class="header-title text-truncate">
                                        Subject List
                                    </h1>
                                </div>
                                <div class="col-auto">
                                    <button onclick="openAddSubjectModal()" class="btn btn-primary ml-2">
                                        Add Subject
                                    </button>
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

                                        <label for="semesterFilter" class="filter-label">Quarter:</label>
                                        <select name="semester" id="semesterFilter" class="form-select filter-select">
                                            <option value="0">All Quarters</option>
                                            <?php for ($q = 1; $q <= $max_quarters; $q++) {
                                                $selected = ($q == $filter_semester) ? 'selected' : '';
                                                echo '<option value="' . $q . '" ' . $selected . '>' . $q . '</option>';
                                            } ?>
                                        </select>

                                        <div class="col-auto ms-auto"> <!-- Push buttons to the right -->
                                            <button type="submit" class="btn btn-primary filter-btn">
                                                <i class="fe fe-filter"></i> Apply Filters
                                            </button>
                                            <?php if ($filter_branch_id > 0 || $filter_semester > 0) { ?>
                                                <a href="subject_list.php" class="btn btn-outline-secondary clear-filters-btn">
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
                                                <?php if ($res && $res !== false) { ?>
                                                    Filtered Subjects <span class="badge rounded-pill bg-soft-secondary"><?php echo mysqli_num_rows($res); ?></span>
                                                <?php } else { ?>
                                                    Subject List
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
                        <div class="tab-pane fade show active" id="subjectsListPane" role="tabpanel">
                            <!-- Card -->
                            <div class="card" data-list='{"valueNames": ["item-name", "item-code", "item-branch", "item-semester"], "page": 10, "pagination": {"paginationClass": "list-pagination"}}' id="subjectsList">
                                <div class="card-header">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <!-- Search Form -->
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
                                                    <i class="fe fe-book me-1"></i> <!-- Changed icon -->
                                                    <?php echo mysqli_num_rows($res); ?> subject(s) found
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
                                                    <a class="list-sort text-muted" data-sort="item-name">Subject Name</a>
                                                </th>
                                                <th>
                                                    <a class="list-sort text-muted" data-sort="item-code">Subject Code</a>
                                                </th>
                                                <th>
                                                    <a class="list-sort text-muted" data-sort="item-branch">Grade Level</a> <!-- Updated Header -->
                                                </th>
                                                <th>
                                                    <a class="list-sort text-muted" data-sort="item-semester">Quarter</a> <!-- Updated Header -->
                                                </th>
                                                <th class="text-end">
                                                    <a class="text-muted justify-content-center">Action</a> <!-- No sort for Action -->
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="list font-size-base">
                                            <?php
                                            if ($res && mysqli_num_rows($res) > 0) {
                                                while ($row = mysqli_fetch_assoc($res)) { ?>
                                                    <tr>
                                                        <td>
                                                            <span class="item-name text-reset"><?php echo htmlspecialchars($row['SubjectName']); ?></span>
                                                        </td>
                                                        <td>
                                                            <span class="item-code text-reset"><?php echo htmlspecialchars($row['SubjectCode']); ?></span>
                                                        </td>
                                                        <td> <!-- Grade Level Column -->
                                                            <span class="item-branch text-reset"><?php echo htmlspecialchars($row['BranchName']); ?></span>
                                                        </td>
                                                        <td> <!-- Quarter Column -->
                                                            <span class="item-semester text-reset"><?php echo htmlspecialchars($row['SubjectSemester']); ?></span>
                                                        </td>
                                                        <td class="text-end">
                                                            <a href="edit_subject.php?semid=<?php echo urlencode($row['SemCode']); ?>&brid=<?php echo urlencode($row['SubjectBranch']); ?>&subcode=<?php echo urlencode($row['SubjectCode']); ?>" class="btn btn-sm btn-warning me-1">
                                                                Edit
                                                            </a>
                                                            <a class="btn btn-sm btn-danger me-1" href="subdelete.php?subcode=<?php echo urlencode($row['SubjectCode']); ?>" onclick="return confirm('Are You Sure, You want to Delete this Subject?');">
                                                                Delete
                                                            </a>
                                                            <a href="subject_profile.php?subjectcode=<?php echo urlencode($row['SubjectCode']); ?>" class="btn btn-sm btn-info">
                                                                View
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php }
                                            } else { ?>
                                                <tr>
                                                    <td colspan="5" class="text-center"> <!-- Updated colspan -->
                                                        <?php if ($res === false) { ?>
                                                            <i class="fe fe-alert-triangle text-danger mb-2" style="font-size: 2rem;"></i>
                                                            <h4 class="text-danger">Error loading subject data.</h4>
                                                            <p>Please try again later.</p>
                                                        <?php } else { ?>
                                                            <i class="fe fe-book text-muted mb-2" style="font-size: 2rem;"></i> <!-- Changed icon -->
                                                            <h4 class="text-muted">No Subjects Found</h4>
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

    <!-- Add Subject Modal -->
    <div id="addSubjectModal" class="add-subject-modal">
        <div class="add-subject-modal-content">
            <span class="close" onclick="closeAddSubjectModal()">&times;</span>
            <h3>Select Grade Level and Quarter</h3>
            <form id="addSubjectForm">
                <div class="mb-3">
                    <label for="modalBranchSelect" class="form-label">Grade Level:</label>
                    <select class="form-select" id="modalBranchSelect" name="branch" required>
                        <option value="">Select Grade Level</option>
                        <?php
                        // Reset pointer for modal dropdown
                        if ($branch_result && mysqli_num_rows($branch_result) > 0) {
                            mysqli_data_seek($branch_result, 0);
                            while ($branch = mysqli_fetch_assoc($branch_result)) {
                                // Pass BranchSemesters for dynamic quarter population in modal JS if needed
                                echo "<option value='" . htmlspecialchars($branch['BranchCode']) . "' data-semesters='" . htmlspecialchars($branch['BranchSemesters']) . "'>" . htmlspecialchars($branch['BranchName']) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="modalSemesterSelect" class="form-label">Quarter:</label>
                    <select class="form-select" id="modalSemesterSelect" name="semester" required>
                        <option value="">Select Quarter</option>
                        <!-- Options will be populated by JavaScript -->
                    </select>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary me-2" onclick="closeAddSubjectModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Subject</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <!-- Map JS -->
    <script src='https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.js'></script>
    <!-- Vendor JS -->
    <script src="../assets/js/vendor.bundle.js"></script>
    <!-- Theme JS -->
    <script src="../assets/js/theme.bundle.js"></script>
    <script>
        // --- Modal Functions (Copied/Adapted from Original) ---
        // Open Add Subject Modal
        function openAddSubjectModal() {
            document.getElementById('addSubjectModal').style.display = 'block';
            // Reset form fields when opening
            document.getElementById('addSubjectForm').reset();
            document.getElementById('modalSemesterSelect').innerHTML = '<option value="">Select Quarter</option>';
        }

        // Close Add Subject Modal
        function closeAddSubjectModal() {
            document.getElementById('addSubjectModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            var modal = document.getElementById('addSubjectModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        // Handle branch selection change in modal
        document.getElementById('modalBranchSelect').addEventListener('change', function() {
            var branchSelect = this;
            var semesterSelect = document.getElementById('modalSemesterSelect');
            // Get max semesters from data attribute (if available)
            var maxSemesters = branchSelect.options[branchSelect.selectedIndex].getAttribute('data-semesters');

            // Clear semester options
            semesterSelect.innerHTML = '<option value="">Select Quarter</option>';

            // Populate semester options based on selected branch
            if (maxSemesters && maxSemesters > 0) {
                for (var i = 1; i <= maxSemesters; i++) {
                    var option = document.createElement('option');
                    option.value = i;
                    option.textContent = i;
                    semesterSelect.appendChild(option);
                }
            } else {
                 // Fallback if data-semesters is missing or invalid
                 for (var i = 1; i <= 4; i++) { // Assume max 4 quarters
                    var option = document.createElement('option');
                    option.value = i;
                    option.textContent = i;
                    semesterSelect.appendChild(option);
                }
            }
        });

        // Handle form submission in modal
        document.getElementById('addSubjectForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var branchCode = document.getElementById('modalBranchSelect').value;
            var semester = document.getElementById('modalSemesterSelect').value;
            if (branchCode && semester) {
                var semCode = branchCode + '_' + semester;
                // Redirect to add_subject.php with parameters
                window.location.href = 'add_subject.php?brid=' + encodeURIComponent(branchCode) + '&semid=' + encodeURIComponent(semCode);
            } else {
                alert('Please select both grade level and quarter');
            }
        });
        // --- End Modal Functions ---
    </script>
</body>
</html>
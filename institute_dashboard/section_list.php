<?php
session_start();
error_reporting(E_ALL ^ E_WARNING);

// Check if user is logged in as Institute
if ($_SESSION['role'] != "Texas" || $_SESSION['userrole'] != "Institute") {
    header("Location: ../index.php");
    exit(); // Ensure script stops after redirect
} else {
    include_once("../config.php");

    // Get the selected branch ID from GET parameter or default to the first one (Grade 7)
    $selected_branch_id = isset($_GET['branch_id']) ? intval($_GET['branch_id']) : 1; // Default to Grade 7 (BranchId = 1)

    // Fetch all branches for the tabs
    $branch_query = "SELECT * FROM branchmaster ORDER BY BranchId";
    $branch_result = mysqli_query($conn, $branch_query);

    // Fetch sections for the selected branch with student count
    // Note: Using SectionBranch name to join as per sectionmaster schema
    $section_query = "SELECT s.*, b.BranchName, COUNT(st.StudentId) as StudentCount
                      FROM sectionmaster s
                      INNER JOIN branchmaster b ON s.SectionBranch = b.BranchName
                      LEFT JOIN studentmaster st ON s.SectionId = st.StudentSection
                      WHERE b.BranchId = ?
                      GROUP BY s.SectionId
                      ORDER BY s.SectionNumber";

    $stmt = mysqli_prepare($conn, $section_query);
    mysqli_stmt_bind_param($stmt, "i", $selected_branch_id);
    mysqli_stmt_execute($stmt);
    $section_result = mysqli_stmt_get_result($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    
    <?php
    $nav_role = "Section";
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
        .header-tabs .nav-link {
            cursor: pointer; /* Indicate tabs are clickable */
        }
        .card {
            z-index: 10;
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.2);
            border-radius: 0.75rem;
            background-color: white;
        }

        .section-card {
            transition: transform 0.2s ease-in-out;
            /* cursor: pointer; */ /* Removed cursor pointer as actions are now buttons */
        }

        .section-card:hover {
            transform: translateY(-2px);
        }

        .section-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        

        .card-actions {
            margin-top: auto; /* Push actions to the bottom */
            padding-top: 10px;
        }

        /* Ensure card body takes full height for flex alignment */
        .section-card .card-body {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .card-title {
            margin-bottom: 0.5rem; /* Reduce margin below title */
        }

        .text-muted.mb-2 {
             margin-bottom: 0.5rem; /* Reduce margin below branch name */
        }

        /* Style for action buttons */
.action-btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    border-radius: 0.2rem;
    margin: 0.1rem auto; /* Center the button */
    width: 90%; /* Adjust width */
    display: block;
    text-align: center;
    border: 1px solid #27548A;
    transition: all 0.2s ease-in-out; /* Add smooth transition for hover effects */
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

/* Hover effects - Enhanced */
.btn-view-students:hover {
    background-color: #1d3d68; /* Darker shade of primary for background */
    color: #ffffff; /* Keep white text */
    /* Optional: Slightly scale up or add a shadow */
    transform: translateY(-2px); /* Move up slightly */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Add shadow */
}

.btn-edit:hover {
    background-color: #e9ecef; /* Light gray background on hover */
    color: #1d3d68; /* Darker shade of primary for text */
    /* Optional: Slightly scale up or add a shadow */
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.btn-delete:hover {
    background-color: #f8d7da; /* Light red background on hover */
    color: #bd2130; /* Darker shade of red for text */
    border-color: #bd2130; /* Darker border */
    /* Optional: Slightly scale up or add a shadow */
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* General hover effect for any action button */
.action-btn:hover {
    text-decoration: none; /* Remove underline if it appears */
    /* Opacity change is now handled by specific button hovers */
}




        
    </style>
</head>
<body>
    <!-- NAVIGATION -->
    <?php
    $nav_role = "Section"; // Highlight the Section tab in navigation if applicable
    include_once("../nav.php"); ?>
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
                                    <!-- Pretitle with Back Button -->
                                    <h5 class="header-pretitle">
                                        <a class="btn-link btn-outline" onclick="history.back()"><i class="fe uil-angle-double-left"></i>Back</a>
                                    </h5>
                                    <!-- Title -->
                                    <h1 class="header-title text-truncate">
                                        Section Management
                                    </h1>
                                </div>
                                <div class="col-auto">
                                    <!-- Add Section Button -->
                                    <a href="add_section.php" class="btn btn-primary ml-2">
                                        <i class="fe fe-plus"></i> Add Section
                                    </a>
                                </div>
                            </div> <!-- / .row -->

                            <div class="row align-items-center">
                                <div class="col">
                                    <!-- Nav Tabs for Branches -->
                                    <ul class="nav nav-tabs nav-overflow header-tabs" id="branchTabs">
                                        <?php
                                        mysqli_data_seek($branch_result, 0); // Reset pointer
                                        while ($branch = mysqli_fetch_assoc($branch_result)) {
                                            $is_active = ($branch['BranchId'] == $selected_branch_id) ? 'active' : '';
                                            echo '<li class="nav-item">';
                                            echo '  <a class="nav-link ' . $is_active . '" href="?branch_id=' . $branch['BranchId'] . '" data-branch-id="' . $branch['BranchId'] . '">';
                                            echo '    ' . htmlspecialchars($branch['BranchName']);
                                            echo '  </a>';
                                            echo '</li>';
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div> <!-- / .row -->
                        </div>
                    </div> <!-- / .header -->

                    <!-- Tab Content (Sections List) -->
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="sectionsListPane" role="tabpanel">
                            <!-- Card -->
                            <div class="card">
                                <div class="card-header" style="margin-top: 30px;">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h4 class="card-header-title">
                                                Sections for <?php
                                                    // Get the name of the selected branch for the header
                                                    mysqli_data_seek($branch_result, 0);
                                                    while ($b = mysqli_fetch_assoc($branch_result)) {
                                                        if ($b['BranchId'] == $selected_branch_id) {
                                                            echo htmlspecialchars($b['BranchName']);
                                                            break;
                                                        }
                                                    }
                                                ?>
                                            </h4>
                                            <p class="card-header-text">
                                                Manage sections and view enrolled students
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php if (mysqli_num_rows($section_result) > 0) { ?>
                                        <div class="row">
                                            <?php while ($section = mysqli_fetch_assoc($section_result)) { ?>
                                                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                                                    <div class="card section-card h-100">
                                                        <div class="card-body text-center">
                                                            <div class="mb-3">
                                                                <i class="fe fe-users text-primary" style="font-size: 3rem;"></i>
                                                            </div>
                                                            <h5 class="card-title mb-2">
                                                                <?php echo htmlspecialchars($section['SectionNumber']); ?>
                                                            </h5>
                                                            <p class="text-muted mb-2">
                                                                <?php echo htmlspecialchars($section['BranchName']); ?>
                                                            </p>
                                                            <div class="mb-3">
                                                                <span class="badge bg-soft-primary section-badge">
                                                                    <?php echo $section['StudentCount']; ?> Students
                                                                </span>
                                                            </div>
                                                            <div class="card-actions">
                                                                <!-- View Students Button -->
                                                                <a href="section_student.php?section_id=<?php echo $section['SectionId']; ?>"
                                                                   class="btn btn-sm action-btn btn-view-students">
                                                                    <i class="fe fe-eye me-1"></i> View Students
                                                                </a>

                                                                <!-- Edit Button -->
                                                                <a href="edit_section.php?section_id=<?php echo $section['SectionId']; ?>"
                                                                   class="btn btn-sm action-btn btn-edit">
                                                                    <i class="fe fe-edit me-1"></i> Edit
                                                                </a>

                                                                <!-- Delete Button -->
                                                                <a class="btn btn-sm action-btn btn-delete"
                                                                   href="delete_section.php?section_id=<?php echo $section['SectionId']; ?>"
                                                                   onclick="return confirm('Are you sure you want to delete the section <?php echo addslashes($section['SectionNumber']); ?>? This might affect students assigned to it.');">
                                                                    <i class="fe fe-trash me-1"></i> Delete
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } else { ?>
                                        <div class="text-center py-5">
                                            <i class="fe fe-inbox text-muted mb-3" style="font-size: 4rem;"></i>
                                            <h3 class="text-muted">No Sections Found</h3>
                                            <p class="text-muted">There are no sections created for this grade level yet.</p>
                                            <a href="add_section.php" class="btn btn-primary mt-2">
                                                <i class="fe fe-plus me-1"></i> Add Section
                                            </a>
                                        </div>
                                    <?php } ?>
                                </div> <!-- / .card-body -->
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
    </script>
</body>
</html>
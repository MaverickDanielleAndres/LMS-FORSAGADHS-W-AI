<?php
session_start();
error_reporting(E_ALL ^ E_WARNING);
if ($_SESSION['role'] != "student") {
    header("Location: ../index.php");
} else {
    include_once "../config.php";
    $_SESSION["userrole"] = "Student";

    $uqur = "SELECT * FROM updatemaster Order by UpdateUploadDate DESC";
    $ures = mysqli_query($conn, $uqur);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once "../head.php"; ?>
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
            /* Bigger, deeper shadow */
            border-radius: 0.75rem;
            background-color: white;
        }

        .update-card {
            transition: transform 0.2s;
        }
        
        .update-card:hover {
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
    <!-- NAVIGATION -->
    <?php
    $nav_role = "Updates";
    include_once "nav.php"; ?>
    <!-- MAIN CONTENT -->
    <div class="main-content">
        <!-- HEADER -->
        <div class="header">
            <div class="container-fluid">
                <!-- Body -->
                <div class="header-body">
                    <div class="row align-items-end">
                        <div class="col">
                            
                            <!-- Title -->
                            <h1 class="header-title">
                                Updates List
                            </h1>
                        </div>
                        <div class="col-auto text-end">
									<h5 class="header-pretitle">
										<button class="back-btn" onclick="history.back()">
											<i class="fe uil-angle-double-left"></i> Back
										</button>
									</h5>
								</div>
                        <!-- Note: Removed "Add Update" button for students -->
                    </div>
                    
                    <!-- Tab Navigation -->
                    <div class="row align-items-center mt-3">
                        <div class="col">
                            <ul class="nav nav-tabs nav-overflow header-tabs">
                                <li class="nav-item">
                                    <a href="#!" class="nav-link text-nowrap active">
                                        All Updates <span class="badge rounded-pill bg-soft-secondary"><?php echo mysqli_num_rows($ures); ?></span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- / .row -->
                </div>
                <!-- / .header-body -->
            </div>
        </div>
        <!-- CARDS -->
        <div class="container-fluid">
            <div class="row">
                <?php
                // Reset the result pointer since we used mysqli_num_rows
                mysqli_data_seek($ures, 0);
                
                if (mysqli_num_rows($ures) > 0) {
                    while ($urow = mysqli_fetch_assoc($ures)) { ?>
                        <div class="col-12 col-md-6 col-lg-4" style="margin-bottom: 20px;">
                            <div class="card-group">
                                <div class="card update-card">
                                    <img src="../src/uploads/updates/<?php echo $urow['UpdateFile'] . "?t"; ?>" class="card-img-top" alt="Update Image" style="height: 200px; object-fit: cover;">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <span class="badge badge-soft-primary"><?php echo date('M d, Y', strtotime($urow['UpdateUploadDate'])); ?></span>
                                            <small class="text-muted"><?php echo $urow['UpdateUploadedBy']; ?></small>
                                        </div>
                                        
                                        <h5 class="card-title"><?php echo $urow['UpdateTitle']; ?></h5>
                                        
                                        <div class="mt-3">
                                            <!-- Modified button group - removed Edit button for students -->
                                            <div class="btn-group w-100" role="group">
                                                <a href="update_view.php?updateid=<?php echo $urow['UpdateId']; ?>" class="btn btn-sm btn-outline-info">
                                                    <i class="fe fe-eye"></i> View
                                                </a>
                                                <!-- Edit button removed for students -->
                                                <a download="<?php echo $urow['UpdateFile']; ?>" href="../src/uploads/updates/<?php echo $urow['UpdateFile']; ?>" class="btn btn-sm btn-outline-success">
                                                    <i class="fe fe-download"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                } else { ?>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <h3 class="text-muted">No Updates Available</h3>
                                <p class="text-muted">There are currently no updates to display.</p>
                                <!-- Removed "Add First Update" button for students -->
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
    <!-- / .main-content -->
    <!-- JAVASCRIPT -->
    <!-- Map JS -->
    <script src='https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.js'></script>
    <!-- Vendor JS -->
    <script src="../assets/js/vendor.bundle.js"></script>
    <!-- Theme JS -->
    <script src="../assets/js/theme.bundle.js"></script>
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
</body>

</html>
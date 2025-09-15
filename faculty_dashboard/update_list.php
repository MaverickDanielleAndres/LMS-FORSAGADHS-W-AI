<?php
session_start();
error_reporting(E_ALL ^ E_WARNING);
if ($_SESSION['role'] != "faculty") {
    header("Location: ../index.php");
} else {
    include_once "../config.php";
    $_SESSION["userrole"] = "Faculty";

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
                            <h5 class="header-pretitle">
										<a class="btn-link btn-outline" onclick="history.back()"><i class="fe uil-angle-double-left"></i>Back</a>
									</h5>
                            <!-- Title -->
                            <h1 class="header-title">
                                List
                            </h1>
                        </div>
                        <!-- Right column for buttons -->
                        <div class="col-auto text-end">
                            <a href="add_update.php" class="btn btn-primary">
                                <i class="fe fe-plus"></i> Add Update
                            </a>
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
                                            <div class="btn-group w-100" role="group">
                                                <a href="update_view.php?updateid=<?php echo $urow['UpdateId']; ?>" class="btn btn-sm btn-outline-info">
                                                    <i class="fe fe-eye"></i> View
                                                </a>
                                                <a href="edit_update.php?updid=<?php echo $urow['UpdateId']; ?>" class="btn btn-sm btn-outline-warning">
                                                    <i class="fe fe-edit"></i> Edit
                                                </a>
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
                                <a href="add_update.php" class="btn btn-primary">
                                    <i class="fe fe-plus"></i> Add First Update
                                </a>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
    <?php include_once("context.php"); ?>
    <!-- / .main-content -->
    <!-- JAVASCRIPT -->
    <!-- Map JS -->
    <script src='https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.js'></script>
    <!-- Vendor JS -->
    <script src="../assets/js/vendor.bundle.js"></script>
    <!-- Theme JS -->
    <script src="../assets/js/theme.bundle.js"></script>
</body>

</html>
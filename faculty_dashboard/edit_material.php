<?php
session_start();
if ($_SESSION['role'] != "faculty") {
    header("Location: ../index.php");
} else {
    include_once("../config.php");
    $_SESSION["userrole"] = "Faculty";
    $matid = $_GET['matid'];
    $matid = mysqli_real_escape_string($conn, $matid);
    $matsql =  "SELECT * FROM `studymaterialmaster` WHERE `MaterialId` = '$matid'";
    $matrow = mysqli_fetch_assoc(mysqli_query($conn, $matsql));
    $subcode = $matrow['SubjectCode'];

    $subsql = "SELECT * FROM subjectmaster INNER JOIN branchmaster ON branchmaster.BranchId = subjectmaster.SubjectBranch WHERE SubjectCode = '$subcode'";
    $subrow = mysqli_fetch_assoc(mysqli_query($conn, $subsql));
    $subid = $subrow['SubjectId'];
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
                /* Bigger, deeper shadow */
                border-radius: 0.75rem;
                background-color: white;
            }

            /* Custom Alert Modal Styles */
            .custom-alert-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.6);
                z-index: 9999;
                backdrop-filter: blur(3px);
            }

            .custom-alert-modal {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: white;
                border-radius: 12px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                min-width: 350px;
                max-width: 500px;
                animation: alertSlideIn 0.3s ease-out;
            }

            @keyframes alertSlideIn {
                from {
                    opacity: 0;
                    transform: translate(-50%, -60%);
                }

                to {
                    opacity: 1;
                    transform: translate(-50%, -50%);
                }
            }

            .custom-alert-header {
                padding: 20px 25px 15px;
                border-bottom: 1px solid #e9ecef;
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .custom-alert-icon {
                width: 24px;
                height: 24px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: bold;
                font-size: 14px;
            }

            .custom-alert-icon.success {
                background: #28a745;
            }

            .custom-alert-icon.error {
                background: #dc3545;
            }

            .custom-alert-icon.warning {
                background: #ffc107;
                color: #212529;
            }

            .custom-alert-title {
                font-weight: 600;
                font-size: 18px;
                margin: 0;
                color: #333;
            }

            .custom-alert-body {
                padding: 15px 25px 20px;
            }

            .custom-alert-message {
                color: #666;
                font-size: 15px;
                line-height: 1.5;
                margin: 0;
            }

            .custom-alert-footer {
                padding: 15px 25px 20px;
                text-align: right;
                border-top: 1px solid #e9ecef;
            }

            .custom-alert-btn {
                background: #007bff;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 6px;
                font-size: 14px;
                font-weight: 500;
                cursor: pointer;
                transition: background-color 0.2s ease;
                min-width: 80px;
            }

            .custom-alert-btn:hover {
                background: #0056b3;
            }

            .custom-alert-btn.btn-success {
                background: #28a745;
            }

            .custom-alert-btn.btn-success:hover {
                background: #1e7e34;
            }

            .custom-alert-btn.btn-danger {
                background: #dc3545;
            }

            .custom-alert-btn.btn-danger:hover {
                background: #c82333;
            }
        </style>
    </head>

    <body>
        <?php $nav_role = "Branch"; ?>
        <!-- NAVIGATION -->
        <?php include_once("nav.php"); ?>

        <!-- Custom Alert Modal -->
        <div id="customAlertOverlay" class="custom-alert-overlay">
            <div class="custom-alert-modal">
                <div class="custom-alert-header">
                    <div id="customAlertIcon" class="custom-alert-icon">
                        <span id="customAlertIconText">!</span>
                    </div>
                    <h4 id="customAlertTitle" class="custom-alert-title"></h4>
                </div>
                <div class="custom-alert-body">
                    <p id="customAlertMessage" class="custom-alert-message"></p>
                </div>
                <div class="custom-alert-footer">
                    <button id="customAlertBtn" class="custom-alert-btn" onclick="closeCustomAlert()">OK</button>
                </div>
            </div>
        </div>

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

                                        <h6 class="header-pretitle">
                                            Edit
                                        </h6>
                                        <!-- Title -->
                                        <h1 class="header-title">
                                            Study Material
                                        </h1>
                                    </div>
                                    <!-- Right column for Back button -->
                                    <div class="col-auto text-end">
                                        <h5 class="header-pretitle">
                                            <button class="back-btn" onclick="history.back()">
                                                <i class="fe uil-angle-double-left"></i> Back
                                            </button>
                                        </h5>
                                    </div>
                                </div>
                                <!-- / .row -->
                            </div>
                        </div>
                        <!-- Form -->
                        <br>
                        <form method="POST" enctype="multipart/form-data" class="row g-3 needs-validation">

                            <div class="row">
                                <div class="col-md-2">
                                    <label for="validationCustom01" class="form-label">Unit No.</label>
                                    <select class="form-control" aria-label="Default select example" name="uno" required>
                                        <option value="" hidden>Select</option>
                                        <?php
                                        for ($i = 1; $i <= 6; $i++) { ?>
                                            "<option <?php if ($i == $matrow['SubjectUnitNo']) { ?> selected <?php } ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>";
                                        <?php }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-10">
                                    <label for="validationCustom01" class="form-label">Unit Name</label>
                                    <input type="text" class="form-control" id="validationCustom01" name="uname" value="<?php echo $matrow['SubjectUnitName']; ?>" required><br>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="validationCustom01" class="form-label">Subject Name</label>
                                    <input type="text" class="form-control" id="validationCustom01" value="<?php echo $subrow['SubjectName']; ?>" disabled><br>
                                </div>
                                <div class="col-md-6">
                                    <label for="validationCustom01" class="form-label">Branch Name</label>
                                    <input type="text" class="form-control" id="validationCustom01" value="<?php echo $subrow['BranchName']; ?>" disabled><br>
                                </div>
                            </div>
                            <hr class="my-3">
                            <div class="row justify-content-between align-items-center">
                                <div class="col">
                                    <div class="row align-items-center">
                                        <div class="col ml-n2">
                                            <!-- Heading -->
                                            <h4 class="mb-1">
                                                Study Material File (English)
                                            </h4>
                                            <!-- Text -->
                                            <small class="text-muted">
                                                Only allowed PDF less than 5MB
                                            </small>
                                        </div>
                                    </div>
                                    <!-- / .row -->
                                </div>
                                <div class="col-auto">
                                    <!-- Button -->
                                    <input type="file" name="engmaterial" value="<?php echo $matrow['EngMaterialFile']; ?>" id="file" onchange="showPreview(event);" class="btn btn-sm" accept="application/pdf">
                                </div>
                            </div>

                            <hr class="mt-4 mb-5">
                            <div class="d-flex justify">
                                <!-- Button -->
                                <button style="margin-bottom: 30px" class="btn btn-primary" type="submit" value="sub" name="subbed">
                                    Save Changes
                                </button>
                            </div>
                            <!-- / .row -->
                        </form>
                        <br>
                    </div>
                </div>
                <!-- / .row -->
            </div>
        </div>
        <?php include("context.php");
        ?>

        <!-- Custom Alert JavaScript -->
        <script>
            function showCustomAlert(message, type = 'info', title = null, callback = null) {
                const overlay = document.getElementById('customAlertOverlay');
                const icon = document.getElementById('customAlertIcon');
                const iconText = document.getElementById('customAlertIconText');
                const titleElement = document.getElementById('customAlertTitle');
                const messageElement = document.getElementById('customAlertMessage');
                const btn = document.getElementById('customAlertBtn');

                // Set default titles based on type
                const defaultTitles = {
                    'success': 'Success',
                    'error': 'Error',
                    'warning': 'Warning',
                    'info': 'Information'
                };

                // Set default icons based on type
                const defaultIcons = {
                    'success': '✓',
                    'error': '✕',
                    'warning': '!',
                    'info': 'i'
                };

                // Configure alert appearance
                titleElement.textContent = title || defaultTitles[type];
                messageElement.textContent = message;
                iconText.textContent = defaultIcons[type];

                // Reset icon classes and add appropriate one
                icon.className = 'custom-alert-icon ' + type;

                // Reset button classes and add appropriate one
                btn.className = 'custom-alert-btn';
                if (type === 'success') {
                    btn.classList.add('btn-success');
                } else if (type === 'error') {
                    btn.classList.add('btn-danger');
                }

                // Store callback for later use
                window.customAlertCallback = callback;

                // Show modal
                overlay.style.display = 'block';

                // Focus on button for accessibility
                setTimeout(() => btn.focus(), 100);
            }

            function closeCustomAlert() {
                const overlay = document.getElementById('customAlertOverlay');
                overlay.style.display = 'none';

                // Execute callback if provided
                if (window.customAlertCallback && typeof window.customAlertCallback === 'function') {
                    window.customAlertCallback();
                    window.customAlertCallback = null;
                }
            }

            // Close modal when clicking outside
            document.getElementById('customAlertOverlay').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeCustomAlert();
                }
            });

            // Close modal with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && document.getElementById('customAlertOverlay').style.display === 'block') {
                    closeCustomAlert();
                }
            });

            function showPreview(event) {
                var file = document.getElementById('file');
                if (file.files.length > 0) {
                    // RUN A LOOP TO CHECK EACH SELECTED FILE.
                    for (var i = 0; i <= file.files.length - 1; i++) {
                        var fsize = file.files.item(i).size; // THE SIZE OF THE FILE.	
                    }
                    if (fsize >= 5000000) {
                        showCustomAlert('Only files less than 5MB are allowed!', 'warning', 'File Size Warning', function() {
                            file.value = '';
                        });
                        return;
                    }
                }
                var file1 = document.getElementById('file1');
                if (file1 && file1.files.length > 0) {
                    // RUN A LOOP TO CHECK EACH SELECTED FILE.
                    for (var i = 0; i <= file1.files.length - 1; i++) {
                        var fsize1 = file1.files.item(i).size; // THE SIZE OF THE FILE.	
                    }
                    if (fsize1 >= 5000000) {
                        showCustomAlert('Only files less than 5MB are allowed!', 'warning', 'File Size Warning', function() {
                            file1.value = '';
                        });
                        return;
                    }
                }
            }
        </script>

        <!-- Map JS -->
        <script src='https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.js'></script>
        <!-- Vendor JS -->
        <script src="../assets/js/vendor.bundle.js"></script>
        <!-- Theme JS -->
        <script src="../assets/js/theme.bundle.js"></script>
    </body>

    </html>
<?php
if (isset($_POST['subbed'])) {
    $unitno = $_POST['uno'];
    $unitname = $_POST['uname'];
    $dt = date('Y-m-d');

    // ✅ Generate unique material code even for same unit
    $materialcode = $subcode . "_" . $unitno . "_ID" . $matid;
    $newMaterialFile = $materialcode . "_MATERIAL.pdf";

    $fs_name = $_FILES['engmaterial']['tmp_name'];
    $fs_size = $_FILES['engmaterial']['size'];
    $fs_error = $_FILES['engmaterial']['error'];

    $existingFile = $matrow['MaterialFile'];

    // ✅ If new file uploaded
    if ($fs_error === 0 && $fs_size > 0) {
        if ($fs_size <= 5000000) {
            if ($existingFile !== $newMaterialFile && file_exists("../src/uploads/studymaterial/" . $existingFile)) {
                unlink("../src/uploads/studymaterial/" . $existingFile);
            }
            move_uploaded_file($fs_name, "../src/uploads/studymaterial/" . $newMaterialFile);
        } else {
            echo "<script>
        showCustomAlert(
            'Material file is too big. Max 5MB allowed.',
            'warning',
            'File Size Limit'
        );
      </script>";

            exit();
        }
    } else {
        // ✅ Rename old file if name changed
        if ($existingFile !== $newMaterialFile && file_exists("../src/uploads/studymaterial/" . $existingFile)) {
            rename("../src/uploads/studymaterial/" . $existingFile, "../src/uploads/studymaterial/" . $newMaterialFile);
        }
    }

    // ✅ Update query now succeeds
    $sql = "UPDATE `studymaterialmaster` SET 
                `SubjectUnitNo` = '$unitno',
                `SubjectUnitName` = '$unitname',
                `MaterialCode` = '$materialcode',
                `MaterialUploadDate` = '$dt',
                `MaterialFile` = '$newMaterialFile' 
            WHERE `MaterialId` = '$matid'";

    $run = mysqli_query($conn, $sql);
    if ($run) {
    echo "<script>
            showCustomAlert(
                'Study Material Edited Successfully',
                'success',
                'Success',
                function () {
                    window.location.href = 'subject_profile.php?subid=$subid';
                }
            );
          </script>";
} else {
    echo "<script>
            showCustomAlert(
                'Error Occurred, Study Material Not Edited',
                'error',
                'Update Failed',
                function () {
                    window.location.href = 'edit_material.php?subcode=$subcode&matid=$matid';
                }
            );
          </script>";
}

}
?>
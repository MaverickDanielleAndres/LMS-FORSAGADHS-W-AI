
<?php
error_reporting(E_ALL ^ E_WARNING);
session_start();
if ($_SESSION['role'] != "faculty") {
	header("Location: ../index.php");
} else {
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
                color: white; /* Ensure text stays white */
			}

        .card {
            z-index: 10;
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.2);
            border-radius: 0.75rem;
            background-color: white;
        }

        /* Optional: Style for the section info if needed */
        .section-info-text {
            /* font-style: italic; */
            /* color: #6c757d; */ /* Bootstrap muted color */
        }
	</style>
	</head>

	<body>
		<?php $nav_role = "Student"; ?>
		<!-- NAVIGATION -->
		<?php include_once('nav.php'); ?>
		<!-- MAIN CONTENT -->
		<div class="main-content">
			<div class="header ml-5 mr-5">
				<!-- HEADER -->
				<div class="header">
					<div class="container-fluid">
						<!-- Body -->
						<div class="header-body">
							<div class="row align-items-end">
								<div class="col">
									
									<h6 class="header-pretitle">
										Student
									</h6>
									<!-- Title -->
									<h1 class="header-title">
										Profile
									</h1>
								</div>
								<!-- Right column for Back button -->
								<div class="col-auto text-end">
									<h5 class="header-pretitle">
										<button class="back-btn" onclick="history.back()">
											<i class="fe fe-arrow-left"></i> Back
										</button>
									</h5>
								</div>
							</div>
							<!-- / .row -->
						</div>
						<!-- / .header-body -->
					</div>
				</div>
				<!-- / .header -->
				<?php
				include_once("../config.php");
				$studentenr = isset($_GET['studentenr']) ? mysqli_real_escape_string($conn, $_GET['studentenr']) : ''; // Check if set
				$_SESSION["userrole"] = "Faculty";
				if (!empty($studentenr)) { // Check if not empty
                    // --- Modified Query to include Section Information ---
                    // Join with sectionmaster to get the section name
                    $sql = "SELECT 
                                sm.*, 
                                bm.BranchName,
                                sec.SectionNumber AS StudentSectionName -- Alias for clarity
                            FROM studentmaster sm
                            INNER JOIN branchmaster bm ON sm.StudentBranchCode = bm.BranchCode
                            LEFT JOIN sectionmaster sec ON sm.StudentSection = sec.SectionId -- Use LEFT JOIN in case section is not assigned
                            WHERE sm.StudentEnrollmentNo = ?";

                    // Use prepared statement for security
                    $stmt = mysqli_prepare($conn, $sql);
                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, "s", $studentenr);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        $row = mysqli_fetch_assoc($result);
                        mysqli_stmt_close($stmt);

                        if (!$row) {
                            // Handle case where student is not found
                            echo "<div class='container-fluid mt-4'><div class='alert alert-danger'>Student not found.</div></div>";
                            // You might want to include a back button or link to student list here
                            include("../assets/js/vendor.bundle.js");
                            include("../assets/js/theme.bundle.js");
                            echo "</body></html>";
                            exit(); // Stop script execution
                        }
                    } else {
                        // Handle query preparation error
                         echo "<div class='container-fluid mt-4'><div class='alert alert-danger'>Error loading student data.</div></div>";
                         include("../assets/js/vendor.bundle.js");
                         include("../assets/js/theme.bundle.js");
                         echo "</body></html>";
                         exit();
                    }
                    // --- End Modified Query ---

				?>
					<br><br>
					<div class="container-fluid">
						<!-- Body -->
						<div class="header-body mt-n5 mt-md-n6">
							<div class="row align-items-center">
								<div class="col-auto">
									<!-- Avatar -->
									<div class="avatar avatar-xxl"> <!-- Corrected class name -->
										<img src="../src/uploads/stuprofile/<?php echo htmlspecialchars($row['StudentProfilePic']); ?>?t=<?php echo time(); ?>" alt="<?php echo htmlspecialchars($row['StudentFirstName'] . ' ' . $row['StudentLastName']); ?>'s Profile Picture" style="border-radius: 10px;" class="w-100 h-100 border-radius-lg shadow-sm" onerror="this.onerror=null; this.src='../assets/img/avatars/default.png';">
									</div>
								</div>
								<div class="col mb-3 ml-n3 ml-md-n2">
									<h1 class="header-title">
										<?php echo htmlspecialchars($row['StudentFirstName'] . " " . $row['StudentLastName']); ?>
									</h1>
									<h5 class="header-pretitle mt-2">
										<?php echo htmlspecialchars($row['StudentEnrollmentNo']); ?>
									</h5>
									<h5 class="header-pretitle mt-2">
										<?php echo "Quarter : " . htmlspecialchars($row['StudentSemester']); ?>
									</h5>
                                    <?php if (!empty($row['StudentSectionName'])) { ?>
                                        <h6 class="header-pretitle mt-2 section-info-text">
                                            Section: <?php echo htmlspecialchars($row['StudentSectionName']); ?>
                                        </h6>
                                    <?php } else { ?>
                                         <h6 class="header-pretitle mt-2 section-info-text text-muted">
                                            Section: Not Assigned
                                        </h6>
                                    <?php } ?>
								</div>

							</div>
							<!-- / .row -->
							<div class="row align-items-center">
								<div class="col">
									<!-- Nav -->
									<ul class="nav nav-tabs nav-overflow header-tabs">
										<li class="nav-item">
											<a href="#!" class="nav-link h3 active"> <!-- Changed link -->
												Basic Details
											</a>
										</li>
									</ul>
								</div>
							</div>
						</div>
						<!-- / .header-body -->
					</div>
					<br>
					<div class="row">
						<div class="col-md-12">
							<!-- Files -->
							<div class="card center" data-list='{"valueNames": ["name"]}'>
								<div class="card-body">
									<h1 class="header-title">
										Student Info:
									</h1>
									<br>
									<div class="input-group mb-3"> <!-- Added margin -->
										<span class="input-group-text col-12 col-md-2">Student Name</span>
										<input type="text" value="<?php echo htmlspecialchars($row['StudentFirstName'] . " " . $row['StudentMiddleName'] . " " . $row['StudentLastName']); ?>" aria-label="First name" class="form-control" disabled>
									</div>

									<div class="input-group mb-3">
										<span class="input-group-text col-12 col-md-2">Enrollment No.</span>
										<input type="text" value="<?php echo htmlspecialchars($row['StudentEnrollmentNo']); ?>" aria-label="First name" class="form-control" disabled>
										<span class="input-group-text col-12 col-md-2">Grade Level</span>
										<input type="text" value="<?php echo htmlspecialchars($row['BranchName']); ?>" aria-label="Last name" class="form-control" disabled>
									</div>

									<div class="input-group mb-3">
										<span class="input-group-text col-12 col-md-2">Quarter</span>
										<input type="text" value="<?php echo htmlspecialchars($row['StudentSemester']); ?>" aria-label="First name" class="form-control" disabled>
                                        <span class="input-group-text col-12 col-md-2">Section</span>
										<input type="text" value="<?php echo htmlspecialchars($row['StudentSectionName'] ?? 'Not Assigned'); ?>" aria-label="Section" class="form-control" disabled>
									</div>

									<div class="input-group mb-3">
										<span class="input-group-text col-12 col-md-2">Roll No.</span>
										<input type="text" value="<?php echo htmlspecialchars($row['StudentRollNo']); ?>" aria-label="Last name" class="form-control" disabled>
										<span class="input-group-text col-12 col-md-2">E-mail</span>
										<input type="text" value="<?php echo htmlspecialchars($row['StudentEmail']); ?>" aria-label="Last name" class="form-control" disabled>
									</div>

									<div class="input-group mb-3">
										<span class="input-group-text col-12 col-md-2">Contact No.</span>
										<input type="text" value="<?php echo htmlspecialchars($row['StudentContactNo']); ?>" aria-label="First name" class="form-control" disabled>
										<span class="input-group-text col-12 col-md-2">Parent Contact No.</span>
										<input type="text" value="<?php echo htmlspecialchars($row['ParentContactNo']); ?>" aria-label="First name" class="form-control" disabled>
									</div>

									<div class="input-group mb-3">
										<span class="input-group-text col-12 col-md-2">Date Of Birth</span>
										<input type="text" value="<?php echo htmlspecialchars($row['StudentDOB']); ?>" aria-label="Last name" class="form-control" disabled>
									</div>

									<div class="input-group input-group-lg mb-3">
										<span class="input-group-text col-12 col-md-2">Address</span>
										<input type="text" value="<?php echo htmlspecialchars($row['StudentAddress']); ?>" aria-label="Last name" class="form-control" disabled>
									</div>
								</div>
							</div>
						</div>
					</div>
			</div>
		</div>

	<?php
				} else {
                    // Handle case where studentenr is not provided
                    echo "<div class='container-fluid mt-4'><div class='alert alert-info'>No student specified.</div></div>";
                ?>
                 <!-- Optional: Search form or back button -->
                 <div class="text-center mt-3">
                     <button class="btn btn-secondary" onclick="history.back()">Go Back</button>
                 </div>
                <?php
                }
	?>
	</div>

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
<?php } ?>
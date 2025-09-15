<?php
session_start();
if ($_SESSION['role'] != "faculty") {
	header("Location: ../index.php");
} else {
	include_once("../config.php");
	$_SESSION["userrole"] = "Faculty";
	$user = $_SESSION['fid'];
	
	// Get faculty information
	$facbranch = "SELECT * FROM facultymaster WHERE FacultyId = '$user'";
	$result = mysqli_fetch_assoc(mysqli_query($conn, $facbranch));
	$facultyBranch = $result['FacultyBranchCode'];
	
	// Get sections assigned to this faculty with student count
	$qur = "SELECT s.*, b.BranchName, 
	               COUNT(st.StudentId) as StudentCount
	        FROM facultysection fs 
	        INNER JOIN sectionmaster s ON fs.SectionId = s.SectionId 
	        INNER JOIN branchmaster b ON s.SectionBranch = b.BranchName
	        LEFT JOIN studentmaster st ON s.SectionId = st.StudentSection
	        WHERE fs.FacultyId = '$user' AND fs.IsActive = 1
	        GROUP BY s.SectionId
	        ORDER BY s.SectionNumber";
	$res = mysqli_query($conn, $qur);
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

		.card {
			z-index: 10;
			box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.2);
			border-radius: 0.75rem;
			background-color: white;
		}

		.section-card {
			transition: transform 0.2s ease-in-out;
			cursor: pointer;
		}

		.section-card:hover {
			transform: translateY(-2px);
		}

		.section-badge {
			font-size: 0.75rem;
			padding: 0.25rem 0.5rem;
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
										My Sections
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
							<div class="row align-items-center">
								<div class="col">
									<!-- Nav -->
									<ul class="nav nav-tabs nav-overflow header-tabs">
										<li class="nav-item">
											<a href="#!" class="nav-link text-nowrap active">
												Assigned Sections <span class="badge rounded-pill bg-soft-secondary"><?php echo mysqli_num_rows($res); ?></span>
											</a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					
					<!-- Sections Content -->
					<?php if (mysqli_num_rows($res)) { ?>
						<div class="tab-content">
							<div class="tab-pane fade show active" id="sectionsListPane" role="tabpanel">
								<!-- Card -->
								<div class="card">
									<div class="card-header" style="margin-top: 30px;">
										<div class="row align-items-center">
											<div class="col" >
												<h4 class="card-header-title" >
													Sections You Handle
												</h4>
												<p class="card-header-text" >
													Click on any section to view students in that section
												</p>
                                                
											</div>
										</div>
									</div>
									<div class="card-body">
										<div class="row">
											<?php while ($row = mysqli_fetch_assoc($res)) { ?>
												<div class="col-lg-4 col-md-6 col-sm-12 mb-4">
													<div class="card section-card h-100" onclick="viewSectionStudents(<?php echo $row['SectionId']; ?>)">
														<div class="card-body text-center">
															<div class="mb-3">
																<i class="fe fe-users text-primary" style="font-size: 3rem;"></i>
															</div>
															<h5 class="card-title mb-2">
																<?php echo $row['SectionNumber']; ?>
															</h5>
															<p class="text-muted mb-3">
																<?php echo $row['BranchName']; ?>
															</p>
															<div class="mb-3">
																<span class="badge bg-soft-primary section-badge">
																	<?php echo $row['StudentCount']; ?> Students
																</span>
															</div>
															<a href="section_students.php?section=<?php echo $row['SectionId']; ?>" class="btn btn-sm btn-primary action-btn btn-view-students">
																<i class="fe fe-eye me-1"></i> View Students
															</a>
														</div>
													</div>
												</div>
											<?php } ?>
										</div>
									</div>
								</div>
							</div>
						</div>

					<?php } else { ?>
						<div class="col-12">
							<div class="card">
								<div class="card-body text-center py-5">
									<i class="fe fe-inbox text-muted mb-3" style="font-size: 4rem;"></i>
									<h3 class="text-muted">No Sections Assigned</h3>
									<p class="text-muted">You don't have any sections assigned to you yet. Please contact the administrator.</p>
								</div>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
	<?php include("context.php"); ?>
	
	<!-- JAVASCRIPT -->
	<script src="../assets/js/vendor.bundle.js"></script>
	<script src="../assets/js/theme.bundle.js"></script>
	
	<script>
		function viewSectionStudents(sectionId) {
			window.location.href = 'section_students.php?section=' + sectionId;
		}
	</script>

</body>

</html>
<?php
session_start();
include_once('../disable_navigation.php');




error_reporting(E_ALL ^ E_WARNING);
if ($_SESSION['role'] != "Texas") {
	header("Location: ../index.php");
	exit();
} else {
	include_once("../config.php");
	$_SESSION["userrole"] = "Institute";
	$inname = $_SESSION['name'];
	$iqur = "SELECT * FROM institutemaster WHERE InstituteId = '$inname'";
	$iqurres = mysqli_query($conn, $iqur);
	$iqurrow = mysqli_fetch_assoc($iqurres);
	$squr = "SELECT * FROM studentmaster";
	$sres = mysqli_query($conn, $squr);
	$fqur = "SELECT * FROM facultymaster ORDER BY FacultyId DESC";
	$fres = mysqli_query($conn, $fqur);
	$bqur = "SELECT * FROM branchmaster";
	$bres = mysqli_query($conn, $bqur);
	$uqur = "SELECT * FROM updatemaster";
	$ures = mysqli_query($conn, $uqur);
	$qqur = "SELECT * FROM accountquerymaster";
	$qres = mysqli_query($conn, $qqur);
	$cqur = "SELECT * FROM sectionmaster";
	$cres = mysqli_query($conn, $cqur);
}
?>
<?php include_once("../head.php"); ?>
<!DOCTYPE html>
<html lang="en">

<head>
	
	<style>
		#sidebar {
			background-color: #987554;
			color: white;
		}

		.nav-item>a i,
		.nav-link i {
			color: white;
		}

		.nav-item {
			position: relative;
		}

		.nav-item::after {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background-color: gray;
			opacity: 0.2;
			/* Adjust opacity here */
			transform: scaleX(0);
			transform-origin: bottom right;
			transition: transform 0.3s ease, opacity 0.3s ease;
			z-index: -1;
		}

		.nav-item:hover::after {
			transform: scaleX(1);
			transform-origin: bottom left;
			opacity: 0.6;
			/* Adjust opacity on hover */
		}

		.nav-item.active::after {
			transform: scaleX(1);
			transform-origin: bottom left;
			opacity: 0.6;
			/* Adjust opacity when active */
		}


		.pb-5 {
			margin-bottom: -30px;
		}

		.navbar-brand img {
			margin-top: -40px;
			margin-bottom: -20px;
		}

		body {
			background: linear-gradient(to top,
					rgba(194, 171, 117, 0.8),
					/* Main shade with less opacity */
					rgba(194, 171, 117, 0.4)
					/* Secondary shade with less opacity */
				),
				url('rhsopacity.png') center/cover no-repeat;
			background-size: cover;
			background-attachment: fixed;
		}



		.header-body {
			border-bottom: 2px solid white;
		}

		.header-pretitle {
			color: #558ce4;
		}

		.main-content {
			background-color: rgb(243, 243, 243);
			min-height: 40vh;
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
			/* Bigger, deeper shadow */
			border-radius: 0.75rem;
			background-color: white;
		}
	</style>
</head>

<body>
	<!-- NAVIGATION -->
	<?php
	$nav_role = "Dashboard";
	include_once("../nav.php"); ?>
	<!-- MAIN CONTENT -->
	<div class="main-content">
		<!-- HEADER -->
		<div class="header">
			<div class="container-fluid">
				<div class="header-body">

					<div class="row align-items-end">
						<div class="col">
							<h6 class="header-pretitle">
								<?php echo $_SESSION['userrole']; ?>
							</h6>
							
							<h1 class="header-title" style="border-bottom: 10px single white;">
								Dashboard
							</h1>
						</div>
						<div class="col-auto text-end">
								<h5 class="header-pretitle">
									<button class="back-btn btn-outline" data-toggle="modal" data-target="#logoutModal">
										Logout
									</button>
								</h5>
							</div>
						
					</div>
				</div>
			</div>
		</div>
		<br><br>
		<div class="container-fluid">
			<div class="page-header min-height-100 border-radius-xl mt-4">
			</div>
			<div class="card card-body blur shadow-blur mx-1 mt-n6 overflow-hidden">
				<div class="row gx-4">
					<div class="col-auto">
						<div class="avatar avatar-xl position-relative">
							<img src="../src/uploads/inprofile/<?php echo $iqurrow['InstituteProfilePic'] . "?t=" . time(); ?>" class="w-100 h-100 border-radius-lg shadow-sm" style="border-radius: 10px;">
						</div>
					</div>
					<div class="col-auto my-auto">
						<div class="h-100">
							<h1 class="mb-0 font-weight-bold text-sm">
								<?php echo $iqurrow['InstituteName']; ?>
							</h1>
							<p class="mb-0 font-weight-bold text-sm">
								<?php echo $iqurrow['InstituteRole']; ?> |
								<a type="button" href="change_profile_pic.php">Change Image</a>
							</p>

						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- CARDS -->
	</div>
	<!-- / .main-content -->
	<!-- MAIN CONTENT -->
	<div class="main-content ">
		<div class="container-fluid">
			<div class="row justify-content-center">
				<div class="col-12 col-lg-12">
					<!-- Card -->
					<div class="row">
						<div class="col-12 col-lg-6">
							<div class="row">
								<div class="col-12 col-lg-12">
									<!-- Card -->
									<div class="card card-fill">
										<div class="card-header">
											<!-- Title -->
											<h4 class="card-header-title">
												New Facultys
											</h4><!-- Default switch -->
											<!-- Link -->
											<a href="faculty_list.php" class="small">View all</a>
										</div>
										<div class="card-body">
											<!-- List group -->
											<div class="list-group list-group-flush my-n3">
												<?php
												$c = 1;
												while ($frow = mysqli_fetch_assoc($fres) and $c <= 5) {
												?>
													<div class="list-group-item">
														<div class="row align-items-center">
															<div class="col-auto">
																<!-- Avatar -->
																<div class="avatar avatar-sm align-middle mr-2">
																	<img src="../src/uploads/facprofile/<?php echo $frow['FacultyProfilePic'] . "?t"; ?>" alt="..." class="avatar-img rounded-circle">
																</div>
															</div>
															<div class="col ml-n2">
																<!-- Title -->
																<h4 class="font-weight-normal mb-1">
																	<?php echo $frow['FacultyFirstName'] . " " . $frow['FacultyLastName']; ?>
																</h4>
																<!-- Text -->
																<small class="text-muted">
																	<?php echo $frow['FacultyCode']; ?>
																</small>
															</div>
															<div class="col-auto">
																<!-- Dropdown -->
																<a href="edit_faculty.php?facid=<?php echo $frow['FacultyId']; ?>" class="btn btn-sm btn-info">
																	Edit
																</a>
															</div>
														</div>
														<!-- / .row -->
													</div>
												<?php
													$c++;
												}
												?>
											</div>
										</div>
										<!-- / .card-body -->
									</div>
								</div>
							</div>
						</div>
						<div class="col-12 col-lg-6">
							<!-- Card -->
							<div class="card">
								<div class="card-body">
									<div class="row align-items-center">
										<div class="col">
											<!-- Title -->
											<h6 class="text-uppercase text-muted mb-2">
												Total Students
											</h6>
											<!-- Heading -->
											<span class="h2 mb-0">
												<?php echo mysqli_num_rows($sres); ?>
											</span>
										</div>
										<div class="col-auto">
											<!-- Icon -->
											<i class="h2 fe uil-user text-muted mb-0"></i>
										</div>
									</div>
									<!-- / .row -->
								</div>
							</div>
							<!-- Card -->
							<div class="card">
								<div class="card-body">
									<div class="row align-items-center">
										<div class="col">
											<!-- Title -->
											<h6 class="text-uppercase text-muted mb-2">
												Total Faculty
											</h6>
											<!-- Heading -->
											<span class="h2 mb-0">
												<?php echo mysqli_num_rows($fres); ?>
											</span>
										</div>
										<div class="col-auto">
											<!-- Icon -->
											<i class="h2 fe uil-user text-muted mb-0"></i>
										</div>
									</div>
									<!-- / .row -->
								</div>
							</div>
							<!-- Card -->
							<div class="card">
								<div class="card-body">
									<div class="row align-items-center">
										<div class="col">
											<!-- Title -->
											<h6 class="text-uppercase text-muted mb-2">
												Total Grade Level
											</h6>
											<div class="row align-items-center g-0">
												<div class="col-auto">
													<!-- Heading -->
													<span class="h2 mr-2 mb-0">
														<?php echo mysqli_num_rows($bres); ?>
													</span>
												</div>
											</div>
											<!-- / .row -->
										</div>
										<div class="col-auto">
											<!-- Icon -->
											<span class="h2 fe uil-code-branch text-muted mb-0"></span>
										</div>
									</div>
									<!-- / .row -->
								</div>
							</div>
							<!-- Card -->
							<!-- <div class="card">
								<div class="card-body">
									<div class="row align-items-center">
										<div class="col">
											<h6 class="text-uppercase text-muted mb-2">
												Total Querys
											</h6>
											<span class="h2 mb-0">
												<?php echo mysqli_num_rows($qres); ?>
											</span>
										</div>
										<div class="col-auto">
											<span class="h2 fe uil-graduation-cap text-muted mb-0"></span>
										</div>
									</div>
								</div>
							</div> -->

							<div class="card">
								<div class="card-body">
									<div class="row align-items-center">
										<div class="col">
											<!-- Title -->
											<h6 class="text-uppercase text-muted mb-2">
												Total Sections
											</h6>
											<!-- Heading -->
											<span class="h2 mb-0">
												<?php echo mysqli_num_rows($cres); ?>
											</span>
										</div>
										<div class="col-auto">
											<!-- Icon -->
											<span class="h2 fe uil-graduation-cap text-muted mb-0"></span>
										</div>
									</div>
									<!-- / .row -->
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>
			<!-- / .row -->
		</div>
		<!-- / .row -->
	</div>
	<!-- / .main-content -->
	<!-- MAIN CONTENT -->

	<div class="main-content">
		<div class="container-fluid">
			<div class="row justify-content-center">
				<div class="card">
					<div class="card-header">
						<h2 class="card-header-title ">
							Updates
						</h2>
						<a href="update_list.php" class="small">View List</a>
					</div>
					<div class="tab-content">
						<div class="tab-pane fade show active" id="contactsListPane" role="tabpanel" aria-labelledby="contactsListTab">
							<div class="card" data-list='{"valueNames": ["item-name", "item-title", "item-email", "item-phone", "item-score", "item-company"], "page": 10, "pagination": {"paginationClass": "list-pagination"}}' id="contactsList">
								<div class="card-header">
									<div class="row align-items-center">
										<div class="col">
											<form  autocomplete="off">
												<div class="input-group input-group-flush input-group-merge input-group-reverse">
													<input class="form-control list-search" type="search" placeholder="Search by Date">
													<span class="input-group-text">
														<i class="fe fe-search"></i>
													</span>
												</div>
											</form>
										</div>
										<div class="col-auto">
										</div>
									</div>
								</div>
								<div class="table-responsive">
									<table class="table table-sm table-hover table-nowrap card-table" id="myTable">
										<thead>
											<tr>
												<th>
													<a class="list-sort text-muted" data-sort="item-phone">#</a>
												</th>
												<th>
													<a class="list-sort text-muted" data-sort="item-phone">Date</a>
												</th>
												<th>
													<a class="list-sort text-muted" data-sort="item-name">Title</a>
												</th>
												<th>
													<a class="list-sort text-muted" data-sort="item-company">Uploaded By</a>
												</th>
												<th>
													<a class="list-sort text-muted">Info</a>
												</th>
												<th colspan="2">
													<a class="list-sort text-muted">Download</a>
												</th>
											</tr>
										</thead>
										<tbody class="list font-size-base">
											<?php
											$a = 1;
											while ($urow = mysqli_fetch_assoc($ures)) { ?>
												<tr>
													<td>
														<?php echo $a++; ?>
													</td>
													<td>
														<span class="item-phone"><?php echo $urow['UpdateUploadDate']; ?></span>
													</td>
													<td>
														<span class="item-name"><?php echo $urow['UpdateTitle']; ?></span>
													</td>
													<td>
														<span class="item-company"><?php echo $urow['UpdateUploadedBy']; ?></span>
													</td>
													<td>
														<button type="button" class="btn btn-sm btn-info" data-toggle="collapse" data-target="#demo<?php echo $i++; ?>" data-parent="#myTable">View</button>
													</td>
													<td>
														<a download="<?php echo $urow['UpdateFile']; ?>" href="../src/uploads/updates/<?php echo $urow['UpdateFile']; ?>" type="button" class="btn btn-sm btn-success">Download</a>
													</td>
												<tr id="demo<?php echo $j++; ?>" class="collapse">
													<td colspan="6" class="hiddenRow">
														<div><?php echo $urow['UpdateDescription']; ?></div>
													</td>
												</tr>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
								<div class="card-footer d-flex justify-content-between">
									<ul class="list-pagination-prev pagination pagination-tabs card-pagination">
										<li class="page-item">
											<a class="page-link pl-0 pr-4 border-right" href="#">
												<i class="fe fe-arrow-left mr-1"></i> Prev
											</a>
										</li>
									</ul>
									<ul class="list-pagination pagination pagination-tabs card-pagination">
										<li class="active"><a class="page" href="javascript:function Z(){Z=&quot;&quot;}Z()">1</a></li>
										<li><a class="page" href="javascript:function Z(){Z=&quot;&quot;}Z()">2</a></li>
										<li><a class="page" href="javascript:function Z(){Z=&quot;&quot;}Z()">3</a></li>
									</ul>
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
						<div class="tab-pane fade" id="contactsCardsPane" role="tabpanel" aria-labelledby="contactsCardsTab">
							<div data-list="{&quot;valueNames&quot;: [&quot;item-name&quot;, &quot;item-title&quot;, &quot;item-email&quot;, &quot;item-phone&quot;, &quot;item-score&quot;, &quot;item-company&quot;], &quot;page&quot;: 9, &quot;pagination&quot;: {&quot;paginationClass&quot;: &quot;list-pagination&quot;}}" id="contactsCards">
								<div class="row align-items-center mb-4">
									<div class="col">
										<form  autocomplete="off">
											<div class="input-group input-group-lg input-group-merge input-group-reverse">
												<input class="form-control list-search" type="search" placeholder="Search">
												<span class="input-group-text">
													<i class="fe fe-search"></i>
												</span>
											</div>
										</form>
									</div>
									<div class="col-auto mr-n3">
										<form  autocomplete="off">
											<div class="choices" data-type="select-one" tabindex="0" role="listbox" aria-haspopup="true" aria-expanded="false">
												<div class="form-select form-select-sm form-control-flush">
													<select class="form-select form-select-sm form-control-flush form-control" data-choices="{&quot;searchEnabled&quot;: false}" hidden="" tabindex="-1" data-choice="active">
														<option value="9 per page">9 per page</option>
													</select>
													<div class="choices__list choices__list--single">
														<div class="choices__item choices__item--selectable" data-item="" data-id="1" data-value="9 per page" data-custom-properties="null" aria-selected="true">9 per
															page
														</div>
													</div>
												</div>
												<div class="choices__list dropdown-menu" aria-expanded="false">
													<div class="choices__list" role="listbox">
														<div class="choices__item dropdown-item choices__item--selectable is-highlighted" data-select-text="Press to select" data-choice="" data-choice-selectable="" data-id="1" data-value="9 per page" role="option" aria-selected="true">
															9 per page
														</div>
														<div class="choices__item dropdown-item choices__item--selectable" data-select-text="Press to select" data-choice="" data-choice-selectable="" data-id="2" data-value="All" role="option">
															All
														</div>
													</div>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	
<!-- LOGOUT CONFIRMATION MODAL -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content border-0 shadow-lg">
			<div class="modal-header bg-warning text-dark">
				<h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body text-dark">
				Are you sure you want to logout?
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<a href="logout.php" class="btn btn-danger">Logout</a>
			</div>
		</div>
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
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
		var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
			return new bootstrap.Tooltip(tooltipTriggerEl)
		})
	</script>

</body>

</html>
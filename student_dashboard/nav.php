<style>
	#sidebar {
		background-color:
			#DDA853;
		color: white;
	}

	.nav-item>a i,
	.nav-link i {
		color: white;
	}

	.nav-item {
		position: relative;
	}

	.nav-item a:active {
		color: black;
	}


	.nav-item::after {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		color: black;
		background-color: #d8d0c5;
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
		margin-bottom: -20px;
	}

	.navbar-brand img {
		margin-top: -20px;
		margin-bottom: -20px;
	}




	.custom-toggler span {
		display: block;
		width: 25px;
		height: 3px;
		margin: 5px auto;
		background-color: white;
		border-radius: 2px;
		transition: all 0.3s ease-in-out;
	}

	.custom-toggler {
		background: transparent;
		border: none;
		padding: 10px;
		outline: none;
	}

	.custom-toggler:hover span {
		background-color: #ddd;
	}
</style>



<nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light" id="sidebar">
	<div class="container-fluid">
		<!-- Toggler -->
		<button class="navbar-toggler custom-toggler" type="button" data-toggle="collapse" data-target="#sidebarCollapse">
			<span></span>
			<span></span>
			<span></span>
		</button>
		<!-- Brand -->
		<div class="justify-start d-flex flex-column justify-content-center align-items-center pt-4">
			<div class="navbar-brand">
				<img src="../assets/img/sagadlogo.png?t=<?php time(); ?>"
					class="navbar-brand-img mx-auto d-none d-md-block"
					alt="...">
			</div>
			<p class="pb-5">Sagad High School</p>
		</div>

		<!-- User (xs) -->
		<div class="navbar-user d-md-none">
			<!-- Dropdown -->
			<div class="dropdown">
			</div>
		</div>
		<!-- Collapse -->
		<div class="collapse navbar-collapse" id="sidebarCollapse">
			<!-- Form -->
			<form class="mt-4 mb-3 d-md-none">
				<div class="input-group input-group-rounded input-group-merge input-group-reverse">
					<input class="form-control" type="search" placeholder="Search" aria-label="Search">
					<div class="input-group-text">
						<span class="fe fe-search"></span>
					</div>
				</div>
			</form>
			<!-- Navigation -->
			<ul class="navbar-nav">
				<li class="nav-item">
					<a style="color:white;" href="../student_dashboard" class="nav-link <?php if ($nav_role == "Dashboard") {
																							echo "active";
																						} ?>">
						<i class="fe fe-home"></i> Dashboard
					</a>
				</li>
				<li class="nav-item">
					<a style="color:white;" href="subjects.php" class="nav-link <?php if ($nav_role == "Subjects") {
																					echo "active";
																				} ?>">
						<i class="fe fe-file"></i>Subjects
					</a>
				</li>
				<li class="nav-item">
					<a style="color:white;" href="assignment_list.php" class="nav-link <?php if ($nav_role == "Assignments") {
																							echo "active";
																						} ?>">
						<i class="fe uil-book"></i></i> Assignments
					</a>
				</li>
				<li class="nav-item">
					<a style="color:white;" href="activity_list.php" class="nav-link <?php if ($nav_role == "Written Works") {
																							echo "active";
																						} ?>">
						<i class="fe uil-pen"></i></i> Written Works
					</a>
				</li>
				<li class="nav-item">
					<a style="color:white;" href="openAI.php" class="nav-link <?php if ($nav_role == "Reviewer") {
																					echo "active";
																				} ?>">
						<i class="fe fe-message-square"></i></i> Reviewer
					</a>
				</li>

				<li class="nav-item">
					<a style="color:white;" href="update_list.php" class="nav-link <?php if ($nav_role == "Updates") {
																						echo "active";
																					} ?>">
						<i class="fe fe-bell"></i>Updates
					</a>
				</li>
				<!--
				<li class="nav-item">
					<a style="color:white;" href="timetable_view.php" class="nav-link <?php if ($nav_role == "Time Table") {
																							echo "active";
																						} ?>">
						<i class="fe uil-calendar-alt"></i>Time Tables
					</a>
				</li>
				-->
			</ul>
			<!-- Divider -->
			<hr class="navbar-divider my-3">
			<!-- Heading -->
			<h6 style="color:white;" class="navbar-heading">
				Legal Center
			</h6>
			<!-- Navigation -->
			<ul class="navbar-nav mb-md-4">
				<!--
				<li class="nav-item">
					<a style="color:white;" href="account_related.php" class="nav-link <?php if ($nav_role == "Account related Details") {
																							echo "active";
																						} ?>">
						<i class="fe fe-user"></i>Account Queries
					</a>
				</li>
																-->
				<li class="nav-item">
					<a style="color:white;" href="study_related.php" class="nav-link <?php if ($nav_role == "Study related querys") {
																							echo "active";
																						} ?>">
						<i class="fe fe-book"></i>Study Queries
					</a>
				</li>
				<li class="nav-item">
					<a style="color:white;" href="query_list.php" class="nav-link <?php if ($nav_role == "All querys") {
																						echo "active";
																					} ?>">
						<i class="fe uil-file-question-alt"></i>All Queries
					</a>
				</li>

				<li class="nav-item">
					<a style="color: white;" href="termsandpolicy.php" class="nav-link <?php if ($nav_role == "Terms and Condition") {
																							echo "active";
																						} ?>">
						<i style="color: white;" class="fe fe-info"></i>Terms & Condition
					</a>
				</li>
			</ul>
			</ul>
			<div class="mt-auto"></div>
			<!-- User (md) -->
			<!-- User (md) -->
			<div class="navbar-user d-md-flex" style="overflow: hidden;" id="sidebarUser">
				<hgroup class="text-center navbar-heading" style="color:white;">
					<!-- Updated logout button to trigger modal -->
					<button class="btn btn-link" style="color:WHITE; margin-top:30px" data-toggle="modal" data-target="#logoutModal">
						Logout
					</button>
					<h6 style="margin: -1px;">
						&copy; 2025 <a style="color:#f7dfb1;" href="https://www.rhspasig.com/" target="_blank">SAGAD HS</a> LMS.<br> All rights reserved.
					</h6>
				</hgroup>
			</div>
		</div>
	</div>
</nav>

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
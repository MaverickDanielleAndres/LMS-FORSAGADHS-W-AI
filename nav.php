<style>
	#sidebar {
		background-color: #DDA853;
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
		transform: scaleX(0);
		transform-origin: bottom right;
		transition: transform 0.3s ease, opacity 0.3s ease;
		z-index: -1;
	}

	.nav-item:hover::after {
		transform: scaleX(1);
		transform-origin: bottom left;
		opacity: 0.6;
	}

	.nav-item.active::after {
		transform: scaleX(1);
		transform-origin: bottom left;
		opacity: 0.6;
	}

	.pb-5 {
		margin-bottom: -20px;
	}

	.navbar-brand img {
		margin-top: -20px;
		margin-bottom: -20px;
	}

	.navbar-toggler>span {
		color: white;
	}
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">

<!-- NAVIGATION -->
<nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light pt-1" id="sidebar">
	<div class="container-fluid">
		<!-- Toggler -->
		<button style="filter: brightness(0) invert(1);" class="navbar-toggler" type="button" data-toggle="collapse" data-target="#sidebarCollapse" aria-controls="sidebarCollapse" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon" style="filter: brightness(0) invert(1);"></span>
		</button>

		<!-- Brand -->
		<div class="justify-start d-flex flex-column justify-content-center align-items-center pt-4">
			<div class="navbar-brand">
				<img src="../assets/img/sagadlogo.png?t=<?php time(); ?>" class="navbar-brand-img mx-auto d-none d-md-block" alt="...">
			</div>
			<p class="pb-5">Sagad High School</p>
		</div>

		<!-- User (xs) -->
		<div class="navbar-user d-md-none">
			<div class="dropdown"></div>
		</div>

		<!-- Collapse -->
		<div class="collapse navbar-collapse" id="sidebarCollapse">
			<form class="mt-4 mb-3 d-md-none">
				<div class="input-group input-group-rounded input-group-merge input-group-reverse">
					<input class="form-control" type="search" placeholder="Search" aria-label="Search">
					<div class="input-group-text">
						<span class="fe fe-search"></span>
					</div>
				</div>
			</form>

			<ul class="navbar-nav">
				<li class="nav-item">
					<a href="../institute_dashboard" class="nav-link text-white <?php if ($nav_role == "Dashboard") echo "active"; ?>">
						<i class="fe fe-home" style="color:white;"></i> Dashboard
					</a>
				</li>

				<li class="nav-item">
					<a href="section_list.php" class="nav-link text-white <?php if ($nav_role == "Section") echo "active"; ?>">
						<i class="fe uil-user"></i> Section
					</a>
				</li>
								<li class="nav-item">
					<a href="all_students.php" class="nav-link text-white <?php if ($nav_role == "Student") echo "active"; ?>">
						<i class="fe uil-user"></i> Students
					</a>
				</li>
				<li class="nav-item">
					<a href="faculty_list.php" class="nav-link text-white <?php if ($nav_role == "Faculty") echo "active"; ?>">
						<i class="fe uil-graduation-cap"></i> Faculties
					</a>
				</li>
				<li class="nav-item">
					<a href="branch_list.php" class="nav-link text-white <?php if ($nav_role == "Branch") echo "active"; ?>">
						<i class="fe uil-code-branch"></i> Branches
					</a>
				</li>
				<li class="nav-item">
					<a href="subject_list.php" class="nav-link text-white <?php if ($nav_role == "Subject") echo "active"; ?>">
						<i class="fe fe-file"></i>Subjects
					</a>
				</li>
				<li class="nav-item">
					<a style="color: white;" href="update_list.php" class="nav-link <?php if ($nav_role == "Updates") {
																						echo "active";
																					} ?>">
						<i class="fe fe-bell"></i>Updates
					</a>
				</li>

			</ul>



			<hr class="navbar-divider my-2">
			<h6 class="navbar-heading text-white">Legal Center</h6>
			<ul class="navbar-nav mb-md-4" style="margin-bottom:-30px;">
				<li class="nav-item">
					<a href="termsandpolicy.php" style="color: white;" class="nav-link text-white <?php if ($nav_role == "Updates") echo "active"; ?>">
						<i class="fe fe-user"></i>Terms & Conditions
					</a>
				</li>
			</ul>

			<div class="mt-auto"></div>

			<!-- User (md) -->
			<div class="navbar-user d-md-flex" style="overflow: hidden;" id="sidebarUser">
				<hgroup class="text-center navbar-heading" style="color:white;">
					<!-- Updated logout button to trigger modal -->
					<button class="btn btn-link" style="color:white; margin-top:50px" data-toggle="modal" data-target="#logoutModal">
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
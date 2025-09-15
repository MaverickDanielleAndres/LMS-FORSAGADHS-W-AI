<?php
error_reporting(E_ALL ^ E_WARNING);
session_start();
require_once("../config.php");
if ($_SESSION['role'] != "faculty" or !isset($_GET['subid'])) {
	header("Location: ../index.php");
} else {
	$xbrid = $_GET['subid'];
	$xbrid = mysqli_real_escape_string($conn, $xbrid);
	$qur = "SELECT *,BranchName,FacultyFirstName,FacultyLastName FROM ((subjectmaster INNER JOIN branchmaster ON subjectmaster.SubjectBranch = branchmaster.BranchId) INNER JOIN facultymaster ON subjectmaster.SubjectFacultyId = facultymaster.FacultyId) WHERE SubjectId = '$xbrid'";
	$res = mysqli_query($conn, $qur);
	$row = mysqli_fetch_assoc($res);
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
				color: white;
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
		<?php
		$nav_role = "Branch";
		include_once('nav.php'); ?>
		<div class="main-content">
			<div class="container-fluid">
				<div class="header-body">
					<div class="row align-items-end">
						<div class="col">
							<h5 class="header-pretitle">
								<a class="btn-link btn-outline" onclick="history.back()"><i class="fe uil-angle-double-left"></i>Back</a>
							</h5>
							<h6 class="header-pretitle">
								Subject
							</h6>
							<h1 class="header-title">
								Profile
							</h1>
						</div>
						<?php
						if ($row['SubjectFacultyId'] == $_SESSION['fid']) { ?>
							<div class="col-auto">
								<a href="add_material.php?subcode=<?php echo $row['SubjectCode']; ?>" class="btn btn-primary ml-2">
									Add Material
								</a>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
			<br> <br>
			<div class="container-fluid">
				<div class="row">
					<div class="col-12">
						<!-- Files -->
						<div class="card">
							<div class="card-body">
								<h2 class="header-title">
									Subject Info :
								</h2>
								<br>
								<div class="input-group">
									<span class="input-group-text col-2 ">Subject name</span>
									<input type="text" value="<?php echo $row['SubjectName']; ?>" aria-label="First name" class="form-control" disabled>
								</div>
								<br>
								<div class="input-group">
									<span class="input-group-text col-2 ">Subject Quarter</span>
									<input type="text" value="<?php echo $row['SubjectSemester']; ?>" aria-label="First name" class="form-control" disabled>
									<span class="input-group-text col-2 ">Subject Grade Level</span>
									<input type="text" value="<?php echo $row['BranchName']; ?>" aria-label="Last name" class="form-control" disabled>
								</div>
								<br>
								<div class="input-group">
									<span class="input-group-text col-2 ">Subject Faculty</span>
									<input type="text" value="<?php echo $row['FacultyFirstName'] . " " . $row['FacultyLastName']; ?>" aria-label="First name" class="form-control" disabled>
									<span class="input-group-text col-2 ">Subject Code</span>
									<input type="text" value="<?php echo $row['SubjectCode']; ?>" aria-label="Last name" class="form-control" disabled>
								</div>
							</div>
						</div>
					</div>
				</div>
				<hr class="navbar-divider my-4">
			</div>
			<?php
			$xsubid = $row['SubjectCode'];
			$qurr = "SELECT * FROM studymaterialmaster WHERE SubjectCode = '$xsubid' Order by SubjectUnitNo ASC";
			$ress = mysqli_query($conn, $qurr);
			?>
			<div class="container-fluid">
				<div class="row justify-content-center">
					<div class="col-12">
						<div class="header">
							<div class="header-body">
								<div class="row align-items-center">
									<div class="col">
										<ul class="nav nav-tabs nav-overflow header-tabs">
											<li class="nav-item">
												<a href="#" class="nav-link text-nowrap active">
													Subject Materials <span class="badge rounded-pill bg-soft-secondary"><?php echo mysqli_num_rows($ress); ?></span>
												</a>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
						<!-- Tab content -->
						<?php

						if (mysqli_num_rows($ress) > 0) { ?>
							<div class="tab-content">
								<div class="tab-pane fade show active" id="contactsListPane" role="tabpanel" aria-labelledby="contactsListTab">
									<!-- Card -->
									<div class="card" data-list='{"valueNames": ["item-name", "item-title", "item-email", "item-phone", "item-score", "item-company"], "page": 10, "pagination": {"paginationClass": "list-pagination"}}' id="contactsList">
										<div class="card-header">
											<div class="row align-items-center">
												<div class="col">
													<!-- Form -->
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
												</div>
											</div>
											<!-- / .row -->
										</div>
										<div class="table-responsive">
											<table class="table table-sm table-hover table-nowrap card-table">
												<thead>
													<tr>
														<th>
															<a class="list-sort text-muted" data-sort="item-name">Unit No</a>
														</th>
														<th colspan="3">
															<a class="list-sort text-muted" data-sort="item-company">Unit Name</a>
														</th>
														
														<th>
															<a class="list-sort text-muted justify-content-center">Action</a>
														</th>
														<th>
															<a class="list-sort text-muted justify-content-center">Download</a>
														</th>
													</tr>
												</thead>
												<tbody class="list font-size-base">
													<?php
													while ($roww = mysqli_fetch_assoc($ress)) { ?>
														<tr>
															<td>
																<a class="item-name text-reset"><?php echo $roww['SubjectUnitNo']; ?></a>
															</td>
															<td colspan="3">
																<!-- Email -->
																<span class="item-company text-reset"><?php echo $roww['SubjectUnitName']; ?></span>
															</td>

															<td>
																<a href="edit_material.php?matid=<?php echo $roww['MaterialId']; ?>" class="btn btn-sm btn-warning">
																	Edit
																</a>
																<a 
																	href="javascript:void(0);"
																	class="btn btn-sm btn-danger"
																	onclick="showDeleteModal(this)" 
																	data-delete-url="delete_material.php?matid=<?php echo $roww['MaterialId']; ?>">
																	Delete
																</a>
															</td>

															<td>
																<a href="../src/uploads/studymaterial/<?php echo $roww['EngMaterialFile']; ?>" download="<?php echo $roww['EngMaterialFile']; ?>" class="btn btn-sm btn-success">
																	Download
																</a>

															</td>
														</tr>
													<?php } ?>
													<!--over-->
											</table>
										</div>
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
												<li class="active"><a class="page" href="javascript:function Z(){Z=&quot;&quot;}Z()">1</a></li>
												<li><a class="page" href="javascript:function Z(){Z=&quot;&quot;}Z()">2</a></li>
												<li><a class="page" href="javascript:function Z(){Z=&quot;&quot;}Z()">3</a></li>
											</ul>
											<!-- Pagination (next) -->
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
							</div>
						<?php
						} else { ?>
							<div class="col-12">
								<h1 class="card header-title m-5 p-5"> Oops, No Materials To Show</h1>
							</div>
						<?php
						}
						?>
					</div>
				</div>
			</div>
			<?php include_once("context.php");
			?>
			<script src='https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.js'></script>
			<script src="../assets/js/vendor.bundle.js"></script>
			<script src="../assets/js/theme.bundle.js"></script>
<!-- Delete Confirmation Modal -->
<div class="custom-alert-overlay" id="deleteConfirmOverlay">
	<div class="custom-alert-modal">
		<div class="custom-alert-header">
			<div class="custom-alert-icon warning">!</div>
			<h5 class="custom-alert-title">Confirm Delete</h5>
		</div>
		<div class="custom-alert-body">
			<p class="custom-alert-message">Are you sure you want to delete this material?</p>
		</div>
		<div class="custom-alert-footer">
			<a href="delete_material.php?matid=<?php echo $roww['MaterialId']; ?>" class="custom-alert-btn btn-danger" id="confirmDeleteLink">Delete</a>

			<a href="#" class="custom-alert-btn" onclick="closeDeleteModal()">Cancel</a>
		</div>
	</div>
</div>

<script>
	function showDeleteModal(el) {
		const deleteUrl = el.getAttribute('data-delete-url');
		const link = document.getElementById('confirmDeleteLink');
		link.setAttribute('href', deleteUrl);
		document.getElementById('deleteConfirmOverlay').style.display = 'block';
	}

	function closeDeleteModal() {
		document.getElementById('confirmDeleteLink').setAttribute('href', '#');
		document.getElementById('deleteConfirmOverlay').style.display = 'none';
	}
</script>


<script>
window.onload = function () {
	const params = new URLSearchParams(window.location.search);
	if (params.get("deleted") === "1") {
		showCustomModal("Success", "Study material deleted successfully.", "success");
	} else if (params.get("deleted") === "0") {
		showCustomModal("Error", "Failed to delete material. Please try again.", "error");
	}

	// ✅ Remove only the `deleted` param and retain `subid`
	params.delete("deleted");
	const newParams = params.toString();
	const newUrl = window.location.pathname + (newParams ? `?${newParams}` : "");
	window.history.replaceState({}, "", newUrl);
};


function showCustomModal(title, message, type) {
	const overlay = document.createElement("div");
	overlay.className = "custom-alert-overlay";
	overlay.style.display = "block";
	overlay.innerHTML = `
		<div class="custom-alert-modal">
			<div class="custom-alert-header">
				<div class="custom-alert-icon ${type}">${type === 'success' ? '✓' : '!'}</div>
				<h5 class="custom-alert-title">${title}</h5>
			</div>
			<div class="custom-alert-body">
				<p class="custom-alert-message">${message}</p>
			</div>
			<div class="custom-alert-footer">
				<a href="#" class="custom-alert-btn ${type === 'success' ? 'btn-success' : 'btn-danger'}" onclick="this.closest('.custom-alert-overlay').remove()">OK</a>
			</div>
		</div>`;
	document.body.appendChild(overlay);
}
</script>



	</body>

	</html>
<?php } ?>
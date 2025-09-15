<?php
session_start();
if ($_SESSION['role'] != "faculty") {
	header("Location: ../index.php");
} else {
	include_once("../config.php");
	$_SESSION["userrole"] = "Faculty";
	$fqur = "SELECT * FROM facultymaster WHERE FacultyId = '" . $_SESSION['fid'] . "'";
	$fres = mysqli_query($conn, $fqur);
	$frow = mysqli_fetch_assoc($fres);
	$bcode = $frow['FacultyId'];

	// Handle delete action
	if (isset($_GET['delete']) && isset($_GET['qid'])) {
		$qid = $_GET['qid'];
		// Check if query status is solved (2) before deleting
		$checkStatus = "SELECT Querystatus FROM studyquerymaster WHERE QueryId = '$qid'";
		$statusResult = mysqli_query($conn, $checkStatus);
		$statusRow = mysqli_fetch_assoc($statusResult);

		if ($statusRow['Querystatus'] == 2) {
			// Delete the query
			$deleteQuery = "DELETE FROM studyquerymaster WHERE QueryId = '$qid'";
			mysqli_query($conn, $deleteQuery);
			echo "<script>window.location.href = window.location.pathname;</script>";
		}
	}

	$qur = "SELECT *, StudentFirstName, StudentLastName FROM studyquerymaster INNER JOIN studentmaster ON studyquerymaster.QueryFromId = studentmaster.StudentId WHERE QueryToId = '$bcode' ORDER BY studyquerymaster.QueryTopic ASC";
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
</style>
</head>

<body>
	<?php $nav_role = "Study related querys"; ?>
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
										Query List
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
												All Query <span class="badge rounded-pill bg-soft-secondary"><?php echo mysqli_num_rows($res); ?></span>
											</a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<!-- Tab content -->
					<?php if (mysqli_num_rows($res) > 0) { ?>
						<div class="main-content">
							<div class="">
								<div class="row justify-content-center">
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
													<table class="table table-sm table-hover table-nowrap card-table" id="myTable">
														<thead>
															<tr>
																<th>
																	<a class="list-sort text-muted" data-sort="item-title">#</a>
																</th>
																<th>
																	<a class="list-sort text-muted" data-sort="item-title" href="#">QueryDate</a>
																</th>
																<th>
																	<a class="list-sort text-muted" data-sort="item-email" href="#">From</a>
																</th>
																<th>
																	<a class="list-sort text-muted" data-sort="item-title" href="#">QueryTopic</a>
																</th>
																<th>
																	<a class="list-sort text-muted">Status</a>
																</th>
																<th colspan="2">
																	<a class="list-sort text-muted">Action</a>
																</th>
															</tr>
														</thead>
														<tbody class="list font-size-base">
															<?php
															$a = 1;
															$j = 1;
															while ($urow = mysqli_fetch_assoc($res)) { ?>
																<tr>
																	<td>
																		<?php echo $a++; ?>
																	</td>
																	<td>
																		<span class="item-title"><?php echo $urow['QueryGenDate']; ?></span>
																	</td>
																	<td>
																		<span class="item-title"><?php echo $urow['StudentFirstName'] . " " . $urow['StudentLastName']; ?></span>
																	</td>
																	<td>
																		<span class="item-title"><?php echo $urow['QueryTopic']; ?></span>
																	</td>
																	<td>
																		<?php
																		$Querystatus = $urow['Querystatus'];
																		if ($Querystatus == 1) { ?>
																			<span class="badge bg-soft-primary">New</span>
																		<?php
																		}
																		if ($Querystatus == 2) { ?>
																			<span class="badge bg-soft-warning">Solved</span>
																		<?php
																		}
																		?>
																	</td>
																	<td>
																		<!-- Badge -->
																		<a href="query_profile.php?qid=<?php echo $urow['QueryId']; ?>" type="button" class="btn btn-sm btn-info">View</a>&nbsp;
																		<?php if ($urow['QueryDocument'] != "") { ?>
																			<a download="<?php echo $urow['QueryDocument']; ?>" href="../src/uploads/querydocument/<?php echo $urow['QueryDocument']; ?>" type="button" class="btn btn-sm btn-success">Download</a>&nbsp;
																		<?php } ?>
																		<a href="qrystatus.php?qid=<?php echo $urow['QueryId']; ?>" type="button" class="btn btn-sm btn-warning">Done</a>&nbsp;
																		<?php if ($urow['Querystatus'] == 2) { ?>
																			<a href="?delete=1&qid=<?php echo $urow['QueryId']; ?>" type="button" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this query?')">Delete</a>
																		<?php } else { ?>
																			<button type="button" class="btn btn-sm btn-danger" disabled title="Query must be solved before it can be deleted">Delete</button>
																		<?php } ?>
																	</td>
																<tr id="demo<?php echo $j++; ?>" class="collapse">
																	<td colspan="6" class="hiddenRow">
																		<div><?php echo $urow['QueryQuestion']; ?></div>
																	</td>
																</tr>
																</tr>
															<?php } ?>
															<!--over-->
														</tbody>
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
														<li><a class="page" href="javascript:function Z(){Z=&quot;&quot;}Z()">4</a></li>
														<li><a class="page" href="javascript:function Z(){Z=&quot;&quot;}Z()">5</a></li>
														<li><a class="page" href="javascript:function Z(){Z=&quot;&quot;}Z()">6</a></li>
														<li><a class="page" href="javascript:function Z(){Z=&quot;&quot;}Z()">7</a></li>
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
								</div>
							</div>
						</div>
					<?php } else { ?>
						<div class="col-12">
							<h1 class="card header-title m-5 p-5"> Oops, No Queries To Show</h1>
						</div>
					<?php
					}
					?>
				</div>
			</div>
		</div>
	</div>
	<?php include("context.php"); ?>
	</div>
	<!-- / .main-content -->
	<!-- JAVASCRIPT -->
	<!-- Map JS -->
	<script src='https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.js'></script>
	<!-- Vendor JS -->
	<script src="../assets/js/vendor.bundle.js"></script>
	<!-- Theme JS -->
	<script src="../assets/js/theme.bundle.js"></script>
	<!-- Delete Popup -->

</body>

</html>
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

			#scoreModal {
				display: none;
				position: fixed;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				background-color: rgba(0, 0, 0, 0.5);
				justify-content: center;
				align-items: center;
				z-index: 1000;
			}

			#scoreForm {
				flex-direction: column;
				background: white;
				padding: 20px;
				border-radius: 8px;
				width: 320px;
				text-align: center;
			}

			h3 {
				text-align: left;
			}

			#modalbtns {
				display: flex;
				justify-content: center;
				margin-top: 2rem;
			}

			#modalbtns button {
				margin-right: 10px;
				color: white;
			}

			.scoreinput{
				padding: 10px;
				border-radius: 20px;
				width: 200px;
				text-align: center;
			}
		</style>
	</head>

	<body>
		<?php $nav_role = "Assignment"; ?>
		<!-- NAVIGATION -->
		<?php include_once 'nav.php'; ?>
		<!-- MAIN CONTENT -->
		<div class="main-content">
			<div class="container-fluid">
				<div class="header-body">
					<div class="row align-items-end">
						<div class="col">

							<h1 class="header-title">
								Assignment
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
				</div>
			</div>
			<div class="container-fluid">
				<div class="row justify-content-center">
					<div class="col-12	">
						<br>
						<?php
						include_once "../config.php";
						$ttid = $_GET['updateid'];
						$ttid = mysqli_real_escape_string($conn, $ttid);
						$_SESSION["userrole"] = "Faculty";

						if (isset($ttid)) {
							// Assignment + subject info
							$sql = "SELECT * FROM assignmentmaster 
							INNER JOIN subjectmaster ON assignmentmaster.AssignmentSubject = subjectmaster.SubjectCode 
							WHERE AssignmentId = '$ttid'";
							$result = mysqli_query($conn, $sql);
							$row = mysqli_fetch_assoc($result); // This is for general assignment info

							// Get the total score for this assignment
							$totalScore = $row['totalscore'];

							// Student submissions + assignment status
							$asql = "SELECT * FROM studentassignment 
							INNER JOIN studentmaster ON studentassignment.SAssignmentUploaderId = studentmaster.StudentId 
							WHERE AssignmentId = '$ttid'";
							$aresult = mysqli_query($conn, $asql);

						?>
							<div class="card">
								<div class="card-body">
									<h3 class="header-title">
										<?php echo $row['AssignmentTitle']; ?> Info:
									</h3>
									<br>
									<div class="input-group">
										<span class="input-group-text col-3 ">Title</span>
										<input type="text" value="<?php echo $row['AssignmentTitle']; ?>" aria-label="First name" class="form-control" disabled>
										<span class="input-group-text col-3 ">Subject</span>
										<input type="text" value="<?php echo $row['SubjectName']; ?>" aria-label="Last name" class="form-control disable" disabled>
									</div>
									<br>
									<div class="input-group">
										<span class="input-group-text col-3 ">Upload date</span>
										<input type="text" value="<?php echo $row['AssignmentUploaddate']; ?>" aria-label="First name" class="form-control" disabled>
										<span class="input-group-text col-3 ">Submission Date</span>
										<input type="text" value="<?php echo $row['AssignmentSubmissionDate']; ?>" aria-label="Last name" class="form-control disable" disabled>
									</div>
									<br>
									<div class="input-group">
										<span class="input-group-text col-3 ">Total Score</span>
										<input type="text" value="<?php echo $totalScore; ?>" aria-label="Total Score" class="form-control" disabled>
										<span class="input-group-text col-3 ">Description</span>
										<textarea aria-label="Description" class="form-control" disabled><?php echo $row['AssignmentDesc']; ?></textarea>
									</div>
									<div class="d-flex justify col-3 mt-3">
										<!-- Button -->
										<a href="../src/uploads/assignments/<?php echo $row['AssignmentFile']; ?>" download="<?php echo $row['AssignmentFile']; ?>" class="btn btn-success" name="Download">
											Download
										</a>
									</div>
								</div>
							</div>
					</div>
				</div>
				<hr class="navbar-divider my-4 mt-20">
			</div>
			<!-- CONTENT -->
			<!-- Tab content -->
			<div class="container-fluid" >
				<div class="row justify-content-center" >
					<div class="col-12" >
						<div class="header">
							<div class="header-body">
								<div class="row align-items-center" >
									<div class="col" >
										<ul class="nav nav-tabs nav-overflow header-tabs">
											<li class="nav-item">
												<a href="#" class="nav-link text-nowrap active">
													Assignment Submissions <span class="badge rounded-pill bg-soft-secondary"><?php echo mysqli_num_rows($aresult); ?></span>
												</a>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
						<?php if (mysqli_num_rows($aresult) > 0) { ?>
							<div class="tab-content">
								<div class="tab-pane fade show active" id="contactsListPane" role="tabpanel" aria-labelledby="contactsListTab">
									<!-- Card -->
									<div class="card" data-list='{"valueNames": ["item-name", "item-title", "item-email", "item-phone", "item-score", "item-company"], "page": 10, "pagination": {"paginationClass": "list-pagination"}}' id="contactsList">
										<div class="card-header" >
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
										<div class="table-responsive" >
											<table class="table table-sm table-hover table-nowrap card-table" >
												<thead>
													<tr>
														<th>
															<a class="list-sort text-muted" data-sort="item-name">No</a>
														</th>
														<th>
															<a class="list-sort text-muted" data-sort="item-name">Student Name</a>
														</th>
														<th>
															<a class="list-sort text-muted" data-sort="item-score">Assignment Status</a>
														</th>
														<th>
															<a class="list-sort text-muted" data-sort="item-phone">Upload Date</a>
														</th>
														<th>
															<a class="list-sort text-muted justify-content-center">Action</a>
														</th>
													</tr>
												</thead>
												<tbody class="list font-size-base">
													<?php
													$counter = 1;
													while ($row = mysqli_fetch_assoc($aresult)) { ?>
														<tr>
															<td>
																<span class="text-reset item-score"><?php echo $counter++; ?></span>
															</td>
															<td>
																<span type="text" class="text-reset item-name"><?php echo $row['StudentFirstName']; ?> <?php echo $row['StudentLastName']; ?></span>
															</td>
															<td>
																<?php
																$status = $row['SAssignmentStatus'];
																if ($status == 0) {
																?>
																	<span class="badge bg-soft-primary">New</span>
																<?php
																} else if ($status == 1) {
																?>
																	<span class="badge bg-soft-success">Submitted</span>
																<?php
																} else if ($status == 2) {
																?>
																	<span class="badge bg-soft-danger">Rejected</span>
																<?php
																} else if ($status == 3) {
																?>
																	<span class="badge bg-soft-warning">
																		Score: <?php echo $row['studscore']; ?>/<?php echo $totalScore; ?>
																	</span>
																<?php
																}
																?>
															</td>
															<td>
																<span type="text" class="text-reset item-phone" name="bsem" required><?php echo $row['SAssignmentUploadDate']; ?></span>
															</td>
															<td>
																<?php if ($status != 3) { // Only show action buttons if not already scored 
																?>
																	<a href="assign_status.php?upid=<?php echo $row['SAssignmentUploaderId']; ?>&asid=<?php echo $ttid; ?>&a=<?php echo 1; ?>" class="btn btn-sm btn-danger">
																		Reject
																	</a>
																	&nbsp;
																	<button class="btn btn-sm btn-success score-btn"
																		data-assignment-id="<?php echo $ttid; ?>"
																		data-uploader-id="<?php echo $row['SAssignmentUploaderId']; ?>"
																		data-total-score="<?php echo $totalScore; ?>">
																		Score
																	</button>
																	&nbsp;
																<?php } else { ?>
																	<button class="btn btn-sm btn-warning edit-score-btn"
																		data-assignment-id="<?php echo $ttid; ?>"
																		data-uploader-id="<?php echo $row['SAssignmentUploaderId']; ?>"
																		data-current-score="<?php echo $row['studscore']; ?>"
																		data-total-score="<?php echo $totalScore; ?>">
																		Edit Score
																	</button>
																	&nbsp;
																<?php } ?>
																<a href="../src/uploads/studentAssignment/<?php echo $row['SAssignmentFile']; ?>" download="<?php echo $row['SAssignmentFile']; ?>" class="btn btn-sm btn-info" name="Download">
																	Download
																</a>
															</td>
														</tr>
													<?php } ?>

												</tbody>
												<!--over-->
											</table>
										</div>
									</div>
								</div>
							</div>
						<?php } else { ?>
							<div class="col-12">
								<h1 class="card header-title m-5 p-5"> Oops, No Submissions To Show</h1>
							</div>
					<?php }
						} ?>
					</div>
				</div>
			</div>
			<div id="scoreModal">
				<form id="scoreForm">
					<h3 id="modalTitle">Enter Score</h3>
					<p id="scoreRange"></p>
					<input class="scoreinput" type="number" id="scoreInput" name="score" min="0" required placeholder="Enter score">
					<div id="modalbtns">
						<button class="btn btn-sm btn-danger" type="button" onclick="closeModal()">Cancel</button>
						<button class="btn btn-sm btn-success" type="submit">Save Score</button>
					</div>
				</form>
			</div>
			<?php include("context.php"); ?>
			<!-- / .main-content -->
			<!-- JAVASCRIPT -->
			<!-- Map JS -->
			<script src='https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.js'></script>
			<!-- Vendor JS -->
			<script src="../assets/js/vendor.bundle.js"></script>
			<!-- Theme JS -->
			<script src="../assets/js/theme.bundle.js"></script>
			<script>
				const modal = document.getElementById('scoreModal');
				const form = document.getElementById('scoreForm');
				const modalTitle = document.getElementById('modalTitle');
				const scoreInput = document.getElementById('scoreInput');
				const scoreRange = document.getElementById('scoreRange');
				let currentAssignmentId = null;
				let currentUploaderId = null;
				let currentTotalScore = null;

				// Handle new scoring
				document.querySelectorAll('.score-btn').forEach(button => {
					button.addEventListener('click', function() {
						currentAssignmentId = this.getAttribute('data-assignment-id');
						currentUploaderId = this.getAttribute('data-uploader-id');
						currentTotalScore = this.getAttribute('data-total-score');
						modalTitle.textContent = 'Enter Score';
						scoreInput.value = '';
						scoreInput.setAttribute('max', currentTotalScore);
						scoreInput.setAttribute('placeholder', `Enter score (0-${currentTotalScore})`);
						scoreRange.textContent = `Score Range: 0 to ${currentTotalScore}`;
						modal.style.display = 'flex';
					});
				});

				// Handle edit scoring
				document.querySelectorAll('.edit-score-btn').forEach(button => {
					button.addEventListener('click', function() {
						currentAssignmentId = this.getAttribute('data-assignment-id');
						currentUploaderId = this.getAttribute('data-uploader-id');
						const currentScore = this.getAttribute('data-current-score');
						currentTotalScore = this.getAttribute('data-total-score');
						modalTitle.textContent = 'Edit Score';
						scoreInput.value = currentScore;
						scoreInput.setAttribute('max', currentTotalScore);
						scoreInput.setAttribute('placeholder', `Enter score (0-${currentTotalScore})`);
						scoreRange.textContent = `Score Range: 0 to ${currentTotalScore}`;
						modal.style.display = 'flex';
					});
				});

				function closeModal() {
					modal.style.display = 'none';
					form.reset();
					currentAssignmentId = null;
					currentUploaderId = null;
					currentTotalScore = null;
				}

				form.addEventListener('submit', function(e) {
					e.preventDefault();
					const score = parseInt(document.getElementById('scoreInput').value);
					const maxScore = parseInt(currentTotalScore);

					if (score < 0 || score > maxScore) {
						alert(`Please enter a score between 0 and ${maxScore}.`);
						return;
					}

					if (!currentAssignmentId || !currentUploaderId) {
						alert("Error: Assignment data not found.");
						return;
					}

					// Send to server via fetch
					fetch('save_score.php', {
							method: 'POST',
							headers: {
								'Content-Type': 'application/x-www-form-urlencoded'
							},
							body: `score=${score}&assignmentId=${currentAssignmentId}&uploaderId=${currentUploaderId}`
						})
						.then(response => response.text())
						.then(data => {
							if (data.trim() === 'success') {
								alert("Score saved successfully!");
								closeModal();
								// Reload the page to show updated status
								location.reload();
							} else {
								alert("Failed to save score. Error: " + data);
							}
						})
						.catch(error => {
							alert("An error occurred while saving the score.");
							console.error(error);
						});
				});

				// Close modal when clicking outside
				modal.addEventListener('click', function(e) {
					if (e.target === modal) {
						closeModal();
					}
				});
			</script>
	</body>

	</html>
<?php } ?>
<?php
// manual_quiz_handler.php
session_start();
error_reporting(E_ALL ^ E_WARNING);

// Check if user is logged in as Faculty
if ($_SESSION['role'] != "faculty") {
    header("Location: ../index.php");
    exit();
} else {
    include_once("../config.php");
    $_SESSION["userrole"] = "Faculty";
 
    $faculty_id = $_SESSION['fid'];

    // Handle potential errors/success from post_quiz.php redirect (optional)
    $error_message = "";
    $success_message = "";
    if (isset($_GET['error'])) {
        $error_message = urldecode($_GET['error']);
    }
    if (isset($_GET['success'])) {
        $success_message = urldecode($_GET['success']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once("../head.php"); ?>
    <link rel="stylesheet" type="text/css" href="manual_quiz.css">
</head>
<body>
    <?php $nav_role = "Quiz"; ?>
    <?php include_once("nav.php"); ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10">
                    <div class="header mt-md-4">
                        <div class="header-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="header-pretitle">
                                        <a class="btn-link btn-outline" href="quiz_maker.php"><i class="fe fe-arrow-left"></i> Back to Quiz Maker</a>
                                    </h6>
                                    <h1 class="header-title text-truncate">
                                        <i class="fe fe-edit"></i> Manual Quiz Creation
                                    </h1>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($error_message)) { ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($error_message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php } ?>
                    <?php if (!empty($success_message)) { ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($success_message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php } ?>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-header-title mb-0"><i class="fe fe-settings me-2"></i>Activity Details</h4>
                        </div>
                        <div class="card-body">
                            <form id="manualQuizForm">
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <label for="manualQuizTitle" class="form-label fw-bold">Title *</label>
                                        <input type="text" class="form-control" id="manualQuizTitle" name="quizTitle" placeholder="e.g., Midterm Exam" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="manualActivityType" class="form-label fw-bold">Activity Type</label>
                                        <select class="form-select" id="manualActivityType" name="activityType">
                                            <option value="quiz">Quiz</option>
                                            <option value="exam">Exam</option>
                                            <option value="exercise">Exercise</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="manualQuizDeadline" class="form-label fw-bold">Deadline</label>
                                        <input type="datetime-local" class="form-control deadline-picker" id="manualQuizDeadline" name="quizDeadline">
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label for="manualQuizDescription" class="form-label fw-bold">Description</label>
                                        <input type="text" class="form-control" id="manualQuizDescription" name="quizDescription" placeholder="Brief description">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="manualQuizInstructions" class="form-label fw-bold">Instructions</label>
                                        <textarea class="form-control" id="manualQuizInstructions" name="quizInstructions" rows="2" placeholder="Detailed instructions for students..."></textarea>
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <label for="manualQuizDuration" class="form-label fw-bold">Duration (mins)</label>
                                        <input type="number" class="form-control" id="manualQuizDuration" name="quizDuration" min="1" placeholder="e.g., 90">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="manualQuizSubject" class="form-label fw-bold">Subject *</label>
                                        <!-- Assuming you have a way to get subjects for this faculty -->
  
                                        <select class="form-select" id="manualQuizSubject" name="quizSubject" required>
                                            <option value="">Select Subject</option>
                                            <?php
                                            // Example query to get subjects assigned to this faculty
                                            $subject_sql = "SELECT sm.SubjectCode, sm.SubjectName, bm.BranchId, bm.BranchName FROM subjectmaster sm 
                                            INNER JOIN branchmaster bm ON sm.SubjectBranch = bm.BranchId 
                                            WHERE sm.SubjectFacultyId = ?";
                                            $stmt = mysqli_prepare($conn, $subject_sql);
                                            if ($stmt) {
                                                mysqli_stmt_bind_param($stmt, "i", $faculty_id);
                                                mysqli_stmt_execute($stmt);
                                                $subject_result = mysqli_stmt_get_result($stmt);
                                                while ($subj = mysqli_fetch_assoc($subject_result)) {
                                                    echo "<option value='{$subj['SubjectCode']}'>{$subj['SubjectName']}</option>";
                                                }
                                                mysqli_stmt_close($stmt);
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="manualQuizBranch" class="form-label fw-bold">Branch</label>
                                        <!-- Branch is automatically populated from the selected subject -->
  
                                        <select class="form-select" id="manualQuizBranch" name="quizBranch" required readonly>
                                            <option value="">Select Branch</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <label for="manualQuizSemester" class="form-label fw-bold">Semester *</label>
                                        <!-- Semester is automatically populated from the selected subject -->  
                                        <select class="form-select" id="manualQuizSemester" name="quizSemester" required readonly>
                                            <option value="">Select Semester</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="manualQuizTotalScore" class="form-label fw-bold">Total Points</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="manualQuizTotalScore" name="totalScore" readonly min="0" step="0.5">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-header-title mb-0"><i class="fe fe-layers me-2"></i>Parts & Questions</h4>
                            <button type="button" class="btn btn-add btn-sm" id="addPartBtn">
                                <i class="fe fe-plus"></i> Add Part
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="partsContainer">
                                <div class="part-block draggable" data-part-number="1">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="mb-0">Part 1</h5>
                                        <button type="button" class="btn btn-sm btn-remove remove-part-btn">
                                            <i class="fe fe-x"></i>
                                        </button>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Part Title (Optional)</label>
                                            <input type="text" class="form-control part-title" placeholder="e.g., Multiple Choice">
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label">No. of Questions</label>
                                            <input type="number" class="form-control num-questions" min="1" value="5">
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label">Type</label>
                                            <select class="form-select part-type">
                                                <option value="mcq">Multiple Choice</option>
                                                <option value="true_false">True/False</option>
                                                <option value="enumeration">Enumeration</option>
                                                <option value="essay">Essay</option>
                                                <option value="identification">Identification</option>
                                                <option value="matching">Matching</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="questions-container mt-3"></div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-3">
                                <button type="button" class="btn btn-primary" id="generateManualStructureBtn">
                                    <span class="spinner-border spinner-border-sm loading-spinner" id="manualGenerateSpinner" role="status" aria-hidden="true"></span>
                                    Generate Question Structure
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="text-center loading-spinner" id="globalSpinnerManual">
                        <div class="spinner-border text-primary" role="status">
                          <span class="visually-hidden">Processing...</span>
                        </div>
                        <p class="mt-2">Generating structure...</p>
                    </div>

                    <div class="card" id="manualCreationCard" style="display: none;">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-header-title mb-0"><i class="fe fe-edit me-2"></i>Manual Activity Questions</h4>
                        </div>
                        <div class="card-body">
                            <form id="manualEditForm">
                                <div id="manualPartsContainer"></div>
                                <div class="d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn btn-post" id="postManualBtn">
                                        <span class="spinner-border spinner-border-sm loading-spinner" id="postSpinnerManual" role="status" aria-hidden="true"></span>
                                        <i class="fe fe-send me-1"></i> Post Activity
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- --- Confirmation Modal --- -->  
    <div id="confirmationModal" class="confirmation-modal">
        <div class="confirmation-modal-content">
            <div class="confirmation-modal-header">
                <h5 class="confirmation-modal-title">Success!</h5>
                <span class="confirmation-modal-close">&times;</span>
            </div>
            <div class="confirmation-modal-body">
                <p>The quiz "<span id="modalQuizTitle"></span>" has been successfully posted.</p>
                <p>You can now assign it to students or view it in the quiz list.</p>
            </div>
            <div class="confirmation-modal-footer">
                <button type="button" class="btn btn-modal-close">Close</button>
                <a href="quiz_maker.php" class="btn btn-primary">Back to Quiz Maker</a>
            </div>
        </div>
    </div>
    <!-- --- End Confirmation Modal --- -->

    <script src='https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.js'></script>
    <script src="../assets/js/vendor.bundle.js"></script>
    <script src="../assets/js/theme.bundle.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addPartBtn = document.getElementById('addPartBtn');
            const partsContainer = document.getElementById('partsContainer');
            const generateManualStructureBtn = document.getElementById('generateManualStructureBtn');
            const manualGenerateSpinner = document.getElementById('manualGenerateSpinner');
            const globalSpinnerManual = document.getElementById('globalSpinnerManual');
            const manualCreationCard = document.getElementById('manualCreationCard');
            const manualPartsContainer = document.getElementById('manualPartsContainer');
            const manualEditForm = document.getElementById('manualEditForm');
            const postManualBtn = document.getElementById('postManualBtn');
            const postSpinnerManual = document.getElementById('postSpinnerManual');

            let partCounter = 1;

            // --- Drag and Drop for Parts ---
            let draggedPart = null;
            partsContainer.addEventListener('dragover', function(e) { e.preventDefault(); });
            partsContainer.addEventListener('drop', function(e) {
                 e.preventDefault();
                 if (draggedPart) {
                     const afterElement = getDragAfterElement(partsContainer, e.clientY);
                     if (afterElement == null) {
                         partsContainer.appendChild(draggedPart);
                     } else {
                         partsContainer.insertBefore(draggedPart, afterElement);
                     }
                     draggedPart = null;
                 }
            });

            function getDragAfterElement(container, y) {
                const draggableElements = [...container.querySelectorAll('.part-block:not(.dragging)')];
                return draggableElements.reduce((closest, child) => {
                    const box = child.getBoundingClientRect();
                    const offset = y - box.top - box.height / 2;
                    if (offset < 0 && offset > closest.offset) {
                        return { offset: offset, element: child };
                    } else { return closest; }
                }, { offset: Number.NEGATIVE_INFINITY }).element;
            }

            // --- Add Part Button ---
            addPartBtn.addEventListener('click', function() {
                 partCounter++;
                 const newPart = document.createElement('div');
                 newPart.className = 'part-block draggable';
                 newPart.draggable = true;
                 newPart.dataset.partNumber = partCounter;
                 newPart.innerHTML = `
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="mb-0">Part ${partCounter}</h5>
                        <button type="button" class="btn btn-sm btn-remove remove-part-btn">
                            <i class="fe fe-x"></i>
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Part Title (Optional)</label>
                            <input type="text" class="form-control part-title" placeholder="e.g., Multiple Choice">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">No. of Questions</label>
                            <input type="number" class="form-control num-questions" min="1" value="5">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Type</label>
                            <select class="form-select part-type">
                                <option value="mcq">Multiple Choice</option>
                                <option value="true_false">True/False</option>
                                <option value="enumeration">Enumeration</option>
                                <option value="essay">Essay</option>
                                <option value="identification">Identification</option>
                                <option value="matching">Matching</option>
                            </select>
                        </div>
                    </div>
                    <div class="questions-container mt-3"></div>
                 `;
                 partsContainer.appendChild(newPart);

                 newPart.querySelector('.remove-part-btn').addEventListener('click', function() {
                     if (partsContainer.children.length > 1) {
                         newPart.remove();
                         renumberParts();
                     } else {
                         alert('You must have at least one part.');
                     }
                 });
                 newPart.addEventListener('dragstart', function(e) {
                     draggedPart = newPart;
                     setTimeout(() => newPart.classList.add('dragging'), 0);
                 });
                 newPart.addEventListener('dragend', function(e) {
                     setTimeout(() => newPart.classList.remove('dragging'), 0);
                 });
                 partsContainer.appendChild(newPart);

// Add event listener for points inputs in this part
newPart.addEventListener('input', function(e) {
    if (e.target.classList.contains('points-input')) {
        recalculateTotalScore();
    }
});
            });

            // --- Remove Part (Initial Part) ---
            partsContainer.addEventListener('click', function(e) {
                 if (e.target.closest('.remove-part-btn')) {
                     const partBlock = e.target.closest('.part-block');
                     if (partsContainer.children.length > 1) {
                         partBlock.remove();
                         renumberParts();
                     } else {
                         alert('You must have at least one part.');
                     }
                 }
            });

            // --- Add drag listeners to initial parts ---
            document.querySelectorAll('.part-block').forEach(part => {
                part.addEventListener('dragstart', function(e) {
                    draggedPart = part;
                    setTimeout(() => part.classList.add('dragging'), 0);
                });
                part.addEventListener('dragend', function(e) {
                    setTimeout(() => part.classList.remove('dragging'), 0);
                });
            });

            function renumberParts() {
                const partBlocks = partsContainer.querySelectorAll('.part-block');
                 partBlocks.forEach((block, index) => {
                     block.dataset.partNumber = index + 1;
                     block.querySelector('h5').textContent = `Part ${index + 1}`;
                 });
                 partCounter = partBlocks.length;
            }

            // --- Subject Selection Handling ---
            const subjectSelect = document.getElementById('manualQuizSubject');
            const branchSelect = document.getElementById('manualQuizBranch');
            const semesterSelect = document.getElementById('manualQuizSemester');
            const totalScoreInput = document.getElementById('manualQuizTotalScore');

            subjectSelect.addEventListener('change', function() {
    const subjectCode = this.value;
    if (!subjectCode) {
        branchSelect.innerHTML = '<option value="">Select Branch</option>';
        semesterSelect.innerHTML = '<option value="">Select Semester</option>';
        totalScoreInput.value = "0";
        return;
    }

    // Fetch subject details to populate branch and semester
    fetch(`get_subject_details.php?subject_code=${encodeURIComponent(subjectCode)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const subjectData = data.subject;

                // ✅ Populate branch with selected value
                branchSelect.innerHTML = `<option value="${subjectData.BranchId}" selected>${subjectData.BranchName}</option>`;

                // ✅ Populate semester with selected value
                semesterSelect.innerHTML = `<option value="${subjectData.SubjectSemester}" selected>${subjectData.SubjectSemester}</option>`;
            } else {
                console.error('Error fetching subject details:', data.error);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });
});


            // --- Generate Manual Structure ---
            generateManualStructureBtn.addEventListener('click', function() {
                 manualGenerateSpinner.style.display = 'inline-block';
                 globalSpinnerManual.style.display = 'block';

                 setTimeout(() => { // Simulate processing
                     try {
                         manualPartsContainer.innerHTML = '';
                         const partBlocks = partsContainer.querySelectorAll('.part-block');
                         partBlocks.forEach(partBlock => {
                             const partNumber = partBlock.dataset.partNumber;
                             const partTitle = partBlock.querySelector('.part-title').value || `Part ${partNumber}`;
                             const numQuestions = parseInt(partBlock.querySelector('.num-questions').value) || 1;
                             const partType = partBlock.querySelector('.part-type').value;

                             const manualPartBlock = document.createElement('div');
                             manualPartBlock.className = 'part-block mb-4';
                             manualPartBlock.dataset.partNumber = partNumber;
                             manualPartBlock.innerHTML = `
                                <h4 class="mb-3">${partTitle}</h4>
                                <input type="hidden" class="manual-part-title" value="${partTitle}">
                                <input type="hidden" class="manual-part-type" value="${partType}">
                                <div class="questions-container"></div>
                             `;
                             const questionsContainer = manualPartBlock.querySelector('.questions-container');

                             for (let i = 1; i <= numQuestions; i++) {
                                 const questionBlock = document.createElement('div');
                                 questionBlock.className = 'question block mb-3 p-3';
                                 questionBlock.dataset.questionNumber = i;
                                 questionBlock.dataset.partNumber = partNumber;
                                 questionBlock.dataset.questionType = partType;

                                 let questionHtml = `
                                     <div class="mb-2">
                                         <label class="form-label fw-bold">Question ${i}:</label>
                                         <textarea class="form-control question-text" rows="2" placeholder="Enter your question..." required></textarea>
                                     </div>
                                     <div class="mb-2">
                                         <label class="form-label">Points:</label>
                                         <input type="number" class="form-control points-input" min="1" value="1" step="0.5">
                                     </div>
                                 `;

                                 if (partType === 'mcq') {
                                     questionHtml += `
                                         <div class="choices-area">
                                             <label class="form-label">Choices:</label>
                                             ${['A', 'B', 'C', 'D'].map(letter => `
                                                 <div class="input-group input-group-sm mb-1 choice-input">
                                                     <span class="input-group-text">${letter}</span>
                                                     <input type="text" class="form-control choice-text" placeholder="Choice ${letter}" data-choice="${letter}" required>
                                                     <div class="input-group-text">
                                                         <input class="form-check-input correct-answer-checkbox" type="checkbox" data-choice="${letter}">
                                                         <label class="form-check-label ms-1">Correct</label>
                                                     </div>
                                                 </div>
                                             `).join('')}
  
                                         </div>
                                     `;
                                 } else if (partType === 'true_false') {
                                     questionHtml += `
                                         <div class="choices-area true-false-choices">
                                             <label class="form-label">Answer:</label>
                                             <div class="true-false-choice">
                                                 <input type="radio" class="form-check-input true-false-radio" name="answer_${i}" value="true" checked>
                                                 <label class="form-check-label">True</label>
                                             </div>
                                             <div class="true-false-choice">
                                                 <input type="radio" class="form-check-input true-false-radio" name="answer_${i}" value="false">
                                                 <label class="form-check-label">False</label>
                                             </div>
                                         </div>
                                     `;
                                 } else if (partType === 'enumeration') {
                                     questionHtml += `
                                         <div class="choices-area">
                                             <label class="form-label">Model Answer (for reference):</label>
                                             <textarea class="form-control" rows="2" placeholder="Enter key points for the answer..." data-enum-answer></textarea>
                                         </div>
                                     `;
                                 } else if (partType === 'essay') {
                                     questionHtml += `
                                         <div class="choices-area">
                                             <label class="form-label">Guidelines/Key Points (for grading reference):</label>
                                             <textarea class="form-control" rows="2" placeholder="Enter grading criteria or key points..." data-essay-guidelines></textarea>
                                         </div>
                                     `;
                                 } else if (partType === 'identification') {
                                     questionHtml += `
                                         <div class="choices-area">
                                             <label class="form-label">Correct Answer:</label>
                                             <input type="text" class="form-control" placeholder="Enter the correct term..." data-identification-answer>
                                         </div>
                                     `;
                                 } else if (partType === 'matching') {
                                     questionHtml += `
                                         <div class="choices-area">
                                             <label class="form-label">Column A (Terms):</label>
                                             <textarea class="form-control mb-2" rows="2" placeholder="Enter terms (one per line)..." data-matching-column-a></textarea>
                                             <label class="form-label">Column B (Definitions):</label>
                                             <textarea class="form-control" rows="2" placeholder="Enter definitions (one per line)..." data-matching-column-b></textarea>
                                         </div>
                                     `;
                                 }
                                 questionBlock.innerHTML = questionHtml;
                                 questionsContainer.appendChild(questionBlock);
                             }
                             manualPartsContainer.appendChild(manualPartBlock);
                         });

                         manualCreationCard.style.display = 'block';
                         manualCreationCard.scrollIntoView({ behavior: 'smooth' });

                     } catch (e) {
                         console.error("Manual Gen Error:", e);
                         alert("An error occurred while generating the structure.");
                     } finally {
                         manualGenerateSpinner.style.display = 'none';
                         globalSpinnerManual.style.display = 'none';
                     }
                 }, 500); // End of timeout
            });

            // --- Post Manual Quiz ---
            manualEditForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                await postQuiz('manual');
            });

            async function postQuiz(method) {
                if (method !== 'manual') return;

                // Basic client-side validation for main form fields
                const title = document.getElementById('manualQuizTitle').value.trim();
                const subject = document.getElementById('manualQuizSubject').value;
                const branch = document.getElementById('manualQuizBranch').value;
                const semester = document.getElementById('manualQuizSemester').value;

                if (!title) {
                    alert('Please enter a quiz title.');
                    return;
                }
                if (!subject) {
                    alert('Please select a subject.');
                    return;
                }
                if (!branch) {
                    alert('Please select a branch.');
                    return;
                }
                if (!semester) {
                    alert('Please select a semester.');
                    return;
                }

                const formData = new FormData(document.getElementById('manualQuizForm')); // Get basic details

                // --- Collect Manual Quiz Data ---
                let quizData = {
                    method: method,
                    title: title,
                    description: document.getElementById('manualQuizDescription').value,
                    instructions: document.getElementById('manualQuizInstructions').value,
                    activityType: document.getElementById('manualActivityType').value,
                    deadline: document.getElementById('manualQuizDeadline').value || null,
                    duration: document.getElementById('manualQuizDuration').value || null,
                    subject: subject,
                    branch: branch,
                    semester: semester,
                    facultyId: <?php echo json_encode($faculty_id); ?>,
                    parts: []
                };

                let globalQuestionCounter = 1; // Global question number across all parts
let totalScore = 0;            // Total points accumulator

quizData.parts = []; // reset before collecting

const partBlocks = manualPartsContainer.querySelectorAll('.part-block');
if (partBlocks.length === 0) {
    alert('No parts found. Please generate the question structure first.');
    return;
}

partBlocks.forEach((partBlock, pIndex) => {
    const partNumber = partBlock.dataset.partNumber;
    const partTitle = partBlock.querySelector('.manual-part-title').value;
    const partType = partBlock.querySelector('.manual-part-type').value;

    // ✅ FIXED SELECTOR (was '.question block', should be '.question.block')
    const questionBlocks = partBlock.querySelectorAll('.question.block');

    const partData = {
        number: parseInt(partNumber),
        title: partTitle,
        type: partType,
        questions: []
    };

    questionBlocks.forEach(qBlock => {
        const qNum = globalQuestionCounter;
        const questionText = qBlock.querySelector('.question-text');
        const pointsInput = qBlock.querySelector('.points-input');
        const type = qBlock.dataset.questionType;

        if (!questionText || !questionText.value.trim()) {
            console.warn(`Question ${qNum} is empty, skipping.`);
            return;
        }

        const points = parseFloat(pointsInput.value) || 1.00;
        totalScore += points;

        const questionObj = {
            number: qNum,
            text: questionText.value,
            type: type,
            points: points,
            choices: [],
            correctAnswers: []
        };

        // --- Handle answer types ---
        if (type === 'mcq') {
            const choiceInputs = qBlock.querySelectorAll('.choice-text');
            const correctCheckboxes = qBlock.querySelectorAll('.correct-answer-checkbox');
            choiceInputs.forEach((input, index) => {
                const letter = String.fromCharCode(65 + index); // A, B, C, D
                const text = input.value;
                questionObj.choices.push({ letter, text });
                const checkbox = Array.from(correctCheckboxes).find(cb => cb.dataset.choice === letter);
                if (checkbox && checkbox.checked) questionObj.correctAnswers.push(letter);
            });
        } else if (type === 'true_false') {
            const selectedRadio = qBlock.querySelector('.true-false-radio:checked');
            if (selectedRadio) {
                questionObj.correctAnswers = [selectedRadio.value];
            }
        } else if (type === 'enumeration') {
            const modelAnswerArea = qBlock.querySelector('textarea[data-enum-answer]');
            if (modelAnswerArea) questionObj.correctAnswers = [modelAnswerArea.value];
        } else if (type === 'essay') {
            const guidelinesArea = qBlock.querySelector('textarea[data-essay-guidelines]');
            if (guidelinesArea) questionObj.correctAnswers = [guidelinesArea.value];
        } else if (type === 'identification') {
            const answerInput = qBlock.querySelector('input[data-identification-answer]');
            if (answerInput) questionObj.correctAnswers = [answerInput.value];
        } else if (type === 'matching') {
            const colA = qBlock.querySelector('textarea[data-matching-column-a]');
            const colB = qBlock.querySelector('textarea[data-matching-column-b]');
            if (colA && colB) {
                questionObj.correctAnswers = {
                    columnA: colA.value.split('\n').filter(line => line.trim() !== ''),
                    columnB: colB.value.split('\n').filter(line => line.trim() !== '')
                };
            }
        }

        partData.questions.push(questionObj);
        globalQuestionCounter++;
    });

    if (partData.questions.length > 0) {
        quizData.parts.push(partData);
    }
});

// ✅ FIX: convert to numbers, not strings
quizData.numQuestions = globalQuestionCounter - 1;
quizData.totalScore = parseFloat(totalScore.toFixed(2));


                console.log("Manual Quiz Data to Post:", quizData);

                postManualBtn.disabled = true;
                postSpinnerManual.style.display = 'inline-block';

                try {
    const response = await fetch('post_quiz.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(quizData)
    });

    const text = await response.text();
    console.log('Raw response:', text); // Debug line
    
    let result;
    try {
        result = JSON.parse(text);
    } catch (parseError) {
        throw new Error(`Invalid JSON response: ${text}`);
    }
                    if (result.success) {
                        // Show the modal
                        showConfirmationModal(title);
                        // Reset form or redirect
                        // window.location.href = 'quiz_maker.php?success=' + encodeURIComponent('Manual activity posted successfully!');
                    } else {
                        throw new Error(result.error || 'Unknown error posting manual activity.');
                    }
                } catch (error) {
                    console.error('Manual Post Error:', error);
                    alert('Error posting manual activity: ' + error.message);
                } finally {
                    postManualBtn.disabled = false;
                    postSpinnerManual.style.display = 'none';
                }
            }

            // --- Confirmation Modal Logic ---
            const modal = document.getElementById("confirmationModal");
            const span = document.getElementsByClassName("confirmation-modal-close")[0];
            const modalCloseBtn = document.querySelector(".btn-modal-close");
            const modalTitleSpan = document.getElementById("modalQuizTitle");

            function showConfirmationModal(quizTitle) {
                modalTitleSpan.textContent = quizTitle;
                modal.style.display = "block";
            }

            // When the user clicks on <span> (x), close the modal
            span.onclick = function() {
                modal.style.display = "none";
            }

            // When the user clicks on close button, close the modal
            modalCloseBtn.onclick = function() {
                modal.style.display = "none";
            }

            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
            // --- End Confirmation Modal Logic ---

            // --- Dynamic Total Score Update ---
            // Add event listener for points input changes
            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('points-input')) {
                    // Recalculate total score when any points input changes
                    recalculateTotalScore();
                }
            });

            // Function to recalculate total score
            function recalculateTotalScore() {
    let total = 0;
    const pointsInputs = document.querySelectorAll('#manualPartsContainer .points-input');
    pointsInputs.forEach(input => {
        const value = parseFloat(input.value);
        if (!isNaN(value)) {
            total += value;
        }
    });
    const totalScoreDisplay = document.querySelector('#manualQuizTotalScore');
    if (totalScoreDisplay) {
        totalScoreDisplay.value = total.toFixed(2);
    }
}

// Add event delegation for dynamically created points inputs
manualPartsContainer.addEventListener('input', function(e) {
    if (e.target.classList.contains('points-input')) {
        recalculateTotalScore();
    }
});

            // Initial calculation on page load
            recalculateTotalScore();

            // --- End Dynamic Total Score Update ---

        });
    </script>
</body>
</html>
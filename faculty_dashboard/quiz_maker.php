<?php
session_start();
error_reporting(E_ALL ^ E_WARNING);
if ($_SESSION['role'] != "faculty") {
    header("Location: ../index.php");
    exit();
}
include_once("../config.php");
$fid = (int)$_SESSION['fid'];
$error   = isset($_GET['error'])   ? urldecode($_GET['error'])   : '';
$success = isset($_GET['success']) ? urldecode($_GET['success']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once("../head.php"); ?>
    <link rel="stylesheet" type="text/css" href="manual_quiz.css">
    <style>
        .ai-chat-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .ai-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 1.5rem;
            border-radius: 12px 12px 0 0;
        }
        .ai-chat {
            height: 300px;
            overflow-y: auto;
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
        }
        .chat-message {
            margin-bottom: 1rem;
            display: flex;
            gap: 0.75rem;
        }
        .chat-message.user {
            flex-direction: row-reverse;
        }
        .chat-message.user .message-content {
            background: #667eea;
            color: #fff;
            border-radius: 18px 18px 4px 18px;
        }
        .chat-message.ai .message-content {
            background: #f1f3f4;
            color: #333;
            border-radius: 18px 18px 18px 4px;
        }
        .message-content {
            padding: 0.75rem 1rem;
            max-width: 80%;
            word-wrap: break-word;
            line-height: 1.4;
        }
        .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #fff;
            font-size: 14px;
        }
        .avatar.user { background: #667eea; }
        .avatar.ai { background: #764ba2; }
        .ai-input-area {
            padding: 1rem;
        }
        .ai-input-group {
            display: flex;
            gap: 0.75rem;
            align-items: flex-end;
        }
        .ai-textarea {
            flex: 1;
            border: 2px solid #e9ecef;
            border-radius: 20px;
            padding: 12px 16px;
            resize: none;
            max-height: 100px;
            transition: border-color 0.3s;
        }
        .ai-textarea:focus {
            border-color: #667eea;
            outline: none;
        }
        .ai-send-btn {
            background: #667eea;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .ai-send-btn:hover:not(:disabled) {
            background: #5a6fd8;
        }
        .ai-send-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .upload-area {
            border: 3px dashed #dee2e6;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: #f8f9fa;
            margin-bottom: 1rem;
        }
        .upload-area.dragover {
            border-color: #667eea;
            background: #fff5f8;
        }
        .file-info {
            display: none;
            margin-top: 1rem;
            padding: 1rem;
            background: #e8f5e9;
            border-radius: 8px;
        }
        .generation-settings {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        .form-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .form-group {
            flex: 1;
        }
        .spinner {
            width: 16px;
            height: 16px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Enhanced Questions List Styles */
        .generated-questions-section {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            display: none;
        }
        .questions-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: #fff;
            padding: 1.5rem;
            border-radius: 12px 12px 0 0;
        }
        .questions-list {
            padding: 1.5rem;
        }
        .question-item {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            margin-bottom: 1rem;
            padding: 1.5rem;
            transition: all 0.3s ease;
            position: relative;
        }
        .question-item:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        }
        .question-header {
            display: flex;
            justify-content: between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        .question-number {
            background: #667eea;
            color: #fff;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9rem;
        }
        .question-points {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-left: auto;
        }
        .question-text {
            width: 100%;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 0.75rem;
            margin-bottom: 1rem;
            font-size: 1rem;
            resize: vertical;
            min-height: 60px;
        }
        .choices-container {
            margin-bottom: 1rem;
        }
        .choice-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.5rem;
            padding: 0.5rem;
            background: #fff;
            border-radius: 6px;
            border: 1px solid #e9ecef;
            transition: background-color 0.2s;
        }
        .choice-item:hover {
            background: #f8f9fa;
        }
        .choice-letter {
            min-width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        .choice-letter.correct {
            background: #28a745;
            color: #fff;
        }
        .choice-letter.incorrect {
            background: #6c757d;
            color: #fff;
        }
        .choice-text {
            flex: 1;
            border: none;
            background: transparent;
            padding: 0.5rem;
            font-size: 0.95rem;
        }
        .choice-text:focus {
            outline: none;
            background: #fff;
            border: 1px solid #667eea;
            border-radius: 4px;
        }
        .correct-radio {
            margin-left: auto;
        }
        .question-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e9ecef;
        }
        .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
        }
        .generation-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 2px solid #e9ecef;
        }
        .questions-summary {
            display: flex;
            gap: 1rem;
        }
        .summary-badge {
            background: #667eea;
            color: #fff;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.9rem;
        }
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            display: none;
        }
        .loading-content {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }
    </style>
</head>
<body>
    <?php $nav_role = "Quiz"; include_once("nav.php"); ?>
    
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <h5>Generating Questions...</h5>
            <p class="text-muted">Please wait while AI creates your quiz questions</p>
        </div>
    </div>

    <div class="main-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10">
                    <div class="header mt-md-4">
                        <div class="header-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="header-pretitle">
                                        <a class="btn-link btn-outline" href="javascript:history.back()">
                                            <i class="fe fe-arrow-left"></i> Back
                                        </a>
                                    </h6>
                                    <h1 class="header-title text-truncate">
                                        <i class="fe fe-zap"></i> AI Quiz Maker
                                    </h1>
                                </div>
                                <div class="col-auto">
                                    <a href="manual_quiz_handler.php" class="btn btn-outline-primary">
                                        <i class="fe fe-edit-3 me-2"></i>Manual Creation
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($error)) { ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php } ?>
                    <?php if (!empty($success)) { ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($success); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php } ?>

                    <!-- AI Chat Assistant -->
                    <div class="ai-chat-container">
                        <div class="ai-header">
                            <h4 class="mb-0"><i class="fe fe-message-circle me-2"></i>AI Assistant</h4>
                            <small>Ask me to generate questions on any topic</small>
                        </div>
                        <div class="ai-chat" id="aiChat">
                            <div class="chat-message ai">
                                <div class="avatar ai">AI</div>
                                <div class="message-content">
                                    Hello! I'm your AI Quiz Assistant. I can help you create questions on any topic. Try asking me:
                                    <br><br>
                                    • "Create 10 multiple choice questions about photosynthesis"<br>
                                    • "Generate true/false questions on World War II"<br>
                                    • "Make essay questions about literature"
                                    <br><br>
                                    What would you like to create today?
                                </div>
                            </div>
                        </div>
                        <div class="ai-input-area">
                            <div class="ai-input-group">
                                <textarea class="ai-textarea" id="aiInput" placeholder="Ask me to generate questions..." rows="1"></textarea>
                                <button class="ai-send-btn" id="aiSendBtn">
                                    <i class="fe fe-send"></i>
                                </button>
                            </div>
                            <small class="text-muted">Press Shift+Enter for new line, Enter to send</small>
                        </div>
                    </div>

                    <!-- Quiz Configuration -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-header-title mb-0"><i class="fe fe-settings me-2"></i> Quiz Configuration</h4>
                        </div>
                        <div class="card-body">
                            <form id="quizForm">
                                <!-- Basic Information -->
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <label for="quizTitle" class="form-label fw-bold">Quiz Title *</label>
                                        <input type="text" class="form-control" id="quizTitle" placeholder="e.g., Midterm Exam" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="quizSubject" class="form-label fw-bold">Subject *</label>
                                        <select class="form-select" id="quizSubject" required>
                                            <option value="">Select Subject</option>
                                            <?php
                                            $stmt = $conn->prepare("SELECT SubjectCode,SubjectName FROM subjectmaster WHERE SubjectFacultyId=? ORDER BY SubjectName");
                                            $stmt->bind_param('i',$fid); 
                                            $stmt->execute(); 
                                            $res=$stmt->get_result();
                                            while($r=$res->fetch_assoc()) {
                                                echo "<option value='{$r['SubjectCode']}'>{$r['SubjectName']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="quizDeadline" class="form-label fw-bold">Deadline</label>
                                        <input type="datetime-local" class="form-control deadline-picker" id="quizDeadline">
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label for="quizDescription" class="form-label fw-bold">Description</label>
                                        <input type="text" class="form-control" id="quizDescription" placeholder="Brief description">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="quizInstructions" class="form-label fw-bold">Instructions</label>
                                        <textarea class="form-control" id="quizInstructions" rows="2" placeholder="Instructions for students..."></textarea>
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <label for="quizDuration" class="form-label fw-bold">Duration (mins)</label>
                                        <input type="number" class="form-control" id="quizDuration" min="1" placeholder="e.g., 60">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="quizBranch" class="form-label fw-bold">Branch</label>
                                        <select class="form-select" id="quizBranch" disabled>
                                            <option value="">Select Branch</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="quizSemester" class="form-label fw-bold">Semester</label>
                                        <select class="form-select" id="quizSemester" disabled>
                                            <option value="">Select Semester</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Existing Material Selection -->
                                <div class="mb-4">
                                    <h5><i class="fe fe-book me-2"></i>Select Existing Material</h5>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="existingMaterial" class="form-label">Choose from your uploaded materials</label>
                                            <select class="form-select" id="existingMaterial">
                                                <option value="">Select a material...</option>
                                            </select>
                                        </div>
                                    </div>
                                    <small class="text-muted">Materials will be loaded when you select a subject</small>
                                </div>

                                <!-- File Upload -->
                                <div class="mb-4">
                                    <h5><i class="fe fe-upload me-2"></i> Upload Material (Optional)</h5>
                                    <div class="upload-area" id="uploadArea">
                                        <div class="upload-icon">
                                            <i class="fe fe-upload" style="font-size: 2rem; color: #dee2e6;"></i>
                                        </div>
                                        <p>Drag & drop files here or click to browse</p>
                                        <small class="text-muted">Supports: PDF, DOC, DOCX, PPT, PPTX, TXT</small>
                                        <input type="file" id="fileInput" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt" style="display:none">
                                    </div>
                                    <div class="file-info" id="fileInfo"></div>
                                </div>

                                <!-- Generation Settings -->
                                <div class="generation-settings">
                                    <h5 class="mb-3"><i class="fe fe-sliders me-2"></i> Generation Settings</h5>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="numQuestions" class="form-label">Number of Questions</label>
                                            <input type="number" class="form-control" id="numQuestions" min="1" max="20" value="5">
                                            <small class="text-muted">Generate 1-20 questions at a time</small>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="difficulty" class="form-label">Difficulty Level</label>
                                            <select class="form-select" id="difficulty">
                                                <option value="mixed">Mixed Difficulty</option>
                                                <option value="easy">Easy</option>
                                                <option value="medium">Medium</option>
                                                <option value="hard">Hard</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="questionType" class="form-label">Question Type</label>
                                            <select class="form-select" id="questionType">
                                                <option value="mixed">Mixed Types</option>
                                                <option value="multiple_choice">Multiple Choice</option>
                                                <option value="true_false">True/False</option>
                                                <option value="essay">Essay Questions</option>
                                                <option value="short_answer">Short Answer</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="pointsPerQuestion" class="form-label">Points per Question</label>
                                            <input type="number" class="form-control" id="pointsPerQuestion" min="0.5" step="0.5" value="2">
                                        </div>
                                    </div>
                                </div>

                                <!-- Generate Button -->
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-primary" id="generateBtn">
                                        <i class="fe fe-zap me-2"></i>Generate Questions
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Generated Questions Section -->
                    <div class="generated-questions-section" id="questionsSection">
                        <div class="questions-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-0"><i class="fe fe-list me-2"></i>Generated Questions</h4>
                                    <small>Review and edit your questions below</small>
                                </div>
                                <div class="questions-summary">
                                    <span class="summary-badge" id="questionCount">0 Questions</span>
                                    <span class="summary-badge" id="totalPoints">0 Points</span>
                                </div>
                            </div>
                        </div>
                        <div class="questions-list" id="questionsList">
                            <!-- Questions will be dynamically inserted here -->
                        </div>
                        <div class="generation-actions">
                            <div>
                                <button type="button" class="btn btn-outline-primary" id="generateMoreBtn">
                                    <i class="fe fe-plus me-2"></i>Generate More Questions
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="clearAllBtn">
                                    <i class="fe fe-trash-2 me-2"></i>Clear All
                                </button>
                            </div>
                            <div>
                                <button type="button" class="btn btn-success btn-lg" id="saveQuizBtn">
                                    <i class="fe fe-save me-2"></i>Save Quiz to Database
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/vendor.bundle.js"></script>
    <script src="../assets/js/theme.bundle.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initializeApp();
        });

        let generatedQuestions = [];
        let facultyId = <?= json_encode($fid) ?>;
        let questionIdCounter = 1;

        function initializeApp() {
            setupEventListeners();
            setupFileUpload();
            updateQuestionsDisplay();
        }

        function setupEventListeners() {
            // Subject change handler
            document.getElementById('quizSubject').addEventListener('change', function() {
                loadSubjectDetails();
                loadExistingMaterials();
            });
            
            // AI input handlers
            const aiInput = document.getElementById('aiInput');
            const aiSendBtn = document.getElementById('aiSendBtn');
            
            aiInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendAIMessage();
                }
            });
            
            aiSendBtn.addEventListener('click', sendAIMessage);
            
            // Generate buttons
            document.getElementById('generateBtn').addEventListener('click', generateQuestions);
            document.getElementById('generateMoreBtn').addEventListener('click', generateQuestions);
            
            // Action buttons
            document.getElementById('clearAllBtn').addEventListener('click', clearAllQuestions);
            document.getElementById('saveQuizBtn').addEventListener('click', saveQuizToDatabase);

            // Material selection handlers
            document.getElementById('existingMaterial').addEventListener('change', function() {
                if (this.value) {
                    clearFileSelection();
                }
            });

            document.getElementById('fileInput').addEventListener('change', function() {
                if (this.files.length > 0) {
                    document.getElementById('existingMaterial').value = '';
                }
                handleFileSelect();
            });
        }

        async function loadSubjectDetails() {
            const subjectCode = document.getElementById('quizSubject').value;
            if (!subjectCode) {
                document.getElementById('quizBranch').innerHTML = '<option value="">Select Branch</option>';
                document.getElementById('quizSemester').innerHTML = '<option value="">Select Semester</option>';
                return;
            }
            
            try {
                const response = await fetch(`get_subject_details.php?subject_code=${encodeURIComponent(subjectCode)}`);
                const data = await response.json();
                
                if (data.success) {
                    const subject = data.subject;
                    document.getElementById('quizBranch').innerHTML = `<option value="${subject.BranchId}" selected>${subject.BranchName}</option>`;
                    document.getElementById('quizSemester').innerHTML = `<option value="${subject.SubjectSemester}" selected>${subject.SubjectSemester}</option>`;
                }
            } catch (error) {
                console.error('Error loading subject details:', error);
            }
        }

        async function loadExistingMaterials() {
            const subjectCode = document.getElementById('quizSubject').value;
            const materialSelect = document.getElementById('existingMaterial');
            
            if (!subjectCode) {
                materialSelect.innerHTML = '<option value="">Select a subject first</option>';
                return;
            }
            
            try {
                const response = await fetch(`get_materials.php?subject_code=${encodeURIComponent(subjectCode)}&faculty_id=${facultyId}`);
                const data = await response.json();
                
                let options = '<option value="">Select a material...</option>';
                if (data.success && data.materials.length > 0) {
                    data.materials.forEach(material => {
                        options += `<option value="${material.MaterialId}">${material.SubjectUnitName}</option>`;
                    });
                } else {
                    options += '<option value="">No materials available</option>';
                }
                
                materialSelect.innerHTML = options;
            } catch (error) {
                console.error('Error loading materials:', error);
                materialSelect.innerHTML = '<option value="">Error loading materials</option>';
            }
        }

        async function sendAIMessage() {
            const input = document.getElementById('aiInput');
            const message = input.value.trim();
            if (!message) return;
            
            const sendBtn = document.getElementById('aiSendBtn');
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<div class="spinner"></div>';
            
            addChatMessage(message, 'user');
            input.value = '';
            
            try {
                const response = await fetch('ai_chat_handler.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        user_message: message,
                        task: 'chat'
                    })
                });
                
                const data = await response.json();
                addChatMessage(data.response || 'Sorry, I encountered an error.', 'ai');
            } catch (error) {
                addChatMessage('Network error. Please check your connection.', 'ai');
            } finally {
                sendBtn.disabled = false;
                sendBtn.innerHTML = '<i class="fe fe-send"></i>';
            }
        }

        function addChatMessage(message, sender) {
            const chatContainer = document.getElementById('aiChat');
            const messageDiv = document.createElement('div');
            messageDiv.className = `chat-message ${sender}`;
            
            const avatar = document.createElement('div');
            avatar.className = `avatar ${sender}`;
            avatar.textContent = sender === 'user' ? 'U' : 'AI';
            
            const content = document.createElement('div');
            content.className = 'message-content';
            content.innerHTML = message.replace(/\n/g, '<br>');
            
            messageDiv.appendChild(avatar);
            messageDiv.appendChild(content);
            chatContainer.appendChild(messageDiv);
            
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        function setupFileUpload() {
            const uploadArea = document.getElementById('uploadArea');
            const fileInput = document.getElementById('fileInput');
            
            uploadArea.addEventListener('click', () => fileInput.click());
            uploadArea.addEventListener('dragover', handleDragOver);
            uploadArea.addEventListener('dragleave', handleDragLeave);
            uploadArea.addEventListener('drop', handleDrop);
        }

        function handleDragOver(e) {
            e.preventDefault();
            e.stopPropagation();
            document.getElementById('uploadArea').classList.add('dragover');
        }

        function handleDragLeave(e) {
            e.preventDefault();
            e.stopPropagation();
            document.getElementById('uploadArea').classList.remove('dragover');
        }

        function handleDrop(e) {
            e.preventDefault();
            e.stopPropagation();
            document.getElementById('uploadArea').classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                document.getElementById('fileInput').files = files;
                handleFileSelect();
            }
        }

        function handleFileSelect() {
            const fileInput = document.getElementById('fileInput');
            const fileInfo = document.getElementById('fileInfo');
            
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                
                fileInfo.innerHTML = `
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <strong>${file.name}</strong>
                            <br><small class="text-muted">${fileSize} MB</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearFileSelection()">
                            <i class="fe fe-x"></i>
                        </button>
                    </div>
                `;
                fileInfo.style.display = 'block';
            }
        }

        function clearFileSelection() {
            document.getElementById('fileInput').value = '';
            document.getElementById('fileInfo').style.display = 'none';
        }

        async function generateQuestions() {
            if (!validateForm()) return;
            
            showLoading(true);
            
            try {
                const formData = new FormData();
                
                // Add all form data
                formData.append('title', document.getElementById('quizTitle').value);
                formData.append('numQuestions', document.getElementById('numQuestions').value);
                formData.append('difficulty', document.getElementById('difficulty').value);
                formData.append('questionType', document.getElementById('questionType').value);
                formData.append('pointsPerQuestion', document.getElementById('pointsPerQuestion').value);
                formData.append('facultyId', facultyId);
                
                // Handle file upload
                if (document.getElementById('fileInput').files.length > 0) {
                    formData.append('file', document.getElementById('fileInput').files[0]);
                }
                // Handle existing material selection
                else if (document.getElementById('existingMaterial').value) {
                    formData.append('material_id', document.getElementById('existingMaterial').value);
                }
                
                const response = await fetch('ai_generate_handler.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success !== false && data.response) {
                    const newQuestions = parseAIResponse(data.response);
                    if (newQuestions.length > 0) {
                        generatedQuestions = generatedQuestions.concat(newQuestions);
                        updateQuestionsDisplay();
                        showSuccessMessage(`Generated ${newQuestions.length} new questions!`);
                    } else {
                        throw new Error('No valid questions could be parsed from the AI response');
                    }
                } else {
                    throw new Error(data.error || 'Generation failed');
                }
                
            } catch (error) {
                console.error('Generate error:', error);
                showErrorMessage('Error generating questions: ' + error.message);
            } finally {
                showLoading(false);
            }
        }

        function validateForm() {
            const title = document.getElementById('quizTitle').value.trim();
            const subject = document.getElementById('quizSubject').value;
            const numQuestions = parseInt(document.getElementById('numQuestions').value);
            
            if (!title) {
                showErrorMessage('Please enter a quiz title');
                return false;
            }
            
            if (!subject) {
                showErrorMessage('Please select a subject');
                return false;
            }
            
            if (!numQuestions || numQuestions < 1 || numQuestions > 20) {
                showErrorMessage('Please enter a valid number of questions (1-20)');
                return false;
            }
            
            return true;
        }

        function parseAIResponse(response) {
            const lines = response.split('\n').filter(line => line.trim());
            const questions = [];
            let currentQuestion = null;
            let answerKey = {};
            let inAnswerKey = false;
            
            lines.forEach(line => {
                line = line.trim();
                
                if (line.includes('ANSWER KEY') || line.includes('Answer Key')) {
                    inAnswerKey = true;
                    return;
                }
                
                if (inAnswerKey) {
                    const answerMatch = line.match(/^(\d+)\.?\s*([A-D])/i);
                    if (answerMatch) {
                        answerKey[parseInt(answerMatch[1])] = answerMatch[2].toUpperCase();
                    }
                    return;
                }
                
                const questionMatch = line.match(/^(?:QUESTION\s+)?(\d+)[:.]?\s*(.+?)(?:\s*\(Points:\s*([\d.]+)\))?$/i);
                if (questionMatch) {
                    if (currentQuestion) questions.push(currentQuestion);
                    currentQuestion = {
                        id: 'q_' + questionIdCounter++,
                        number: parseInt(questionMatch[1]),
                        text: questionMatch[2].replace(/\(Points:.*?\)$/i, '').trim(),
                        choices: [],
                        type: 'multiple_choice',
                        points: questionMatch[3] ? parseFloat(questionMatch[3]) : parseFloat(document.getElementById('pointsPerQuestion').value) || 2
                    };
                } else if (currentQuestion) {
                    const choiceMatch = line.match(/^([A-D])[\).]?\s*(.+)$/i);
                    if (choiceMatch) {
                        currentQuestion.choices.push({
                            letter: choiceMatch[1].toUpperCase(),
                            text: choiceMatch[2],
                            isCorrect: false
                        });
                    }
                }
            });
            
            if (currentQuestion) questions.push(currentQuestion);
            
            // Apply correct answers from answer key
            questions.forEach(q => {
                if (answerKey[q.number]) {
                    const correctLetter = answerKey[q.number];
                    q.choices.forEach(choice => {
                        choice.isCorrect = choice.letter === correctLetter;
                    });
                }
            });
            
            return questions;
        }

        function updateQuestionsDisplay() {
            const questionsSection = document.getElementById('questionsSection');
            const questionsList = document.getElementById('questionsList');
            const questionCount = document.getElementById('questionCount');
            const totalPoints = document.getElementById('totalPoints');
            
            if (generatedQuestions.length === 0) {
                questionsSection.style.display = 'none';
                return;
            }
            
            questionsSection.style.display = 'block';
            
            const totalPointsValue = generatedQuestions.reduce((sum, q) => sum + q.points, 0);
            questionCount.textContent = `${generatedQuestions.length} Questions`;
            totalPoints.textContent = `${totalPointsValue} Points`;
            
            let html = '';
            generatedQuestions.forEach((question, index) => {
                html += createQuestionHTML(question, index);
            });
            
            questionsList.innerHTML = html;
            
            // Scroll to questions section
            questionsSection.scrollIntoView({ behavior: 'smooth' });
        }

        function createQuestionHTML(question, index) {
            return `
                <div class="question-item" data-question-id="${question.id}">
                    <div class="question-header">
                        <span class="question-number">Question ${index + 1}</span>
                        <div class="question-points">
                            <label class="form-label mb-0 small">Points:</label>
                            <input type="number" class="form-control form-control-sm" style="width:70px" 
                                   value="${question.points}" min="0.5" step="0.5" 
                                   onchange="updateQuestionPoints('${question.id}', this.value)">
                        </div>
                    </div>
                    <textarea class="question-text" onchange="updateQuestionText('${question.id}', this.value)">${question.text}</textarea>
                    <div class="choices-container">
                        ${question.choices.map((choice, choiceIndex) => `
                            <div class="choice-item">
                                <span class="choice-letter ${choice.isCorrect ? 'correct' : 'incorrect'}">${choice.letter}</span>
                                <input type="text" class="choice-text" value="${choice.text}" 
                                       onchange="updateChoiceText('${question.id}', ${choiceIndex}, this.value)">
                                <div class="correct-radio">
                                    <input type="radio" name="correct_${question.id}" 
                                           ${choice.isCorrect ? 'checked' : ''}
                                           onchange="updateCorrectAnswer('${question.id}', ${choiceIndex})">
                                    <label class="form-check-label small ms-1">Correct</label>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                    <div class="question-actions">
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteQuestion('${question.id}')">
                            <i class="fe fe-trash-2"></i> Delete
                        </button>
                    </div>
                </div>
            `;
        }

        function updateQuestionPoints(questionId, points) {
            const question = generatedQuestions.find(q => q.id === questionId);
            if (question) {
                question.points = parseFloat(points) || 0;
                updateQuestionsDisplay();
            }
        }

        function updateQuestionText(questionId, text) {
            const question = generatedQuestions.find(q => q.id === questionId);
            if (question) {
                question.text = text;
            }
        }

        function updateChoiceText(questionId, choiceIndex, text) {
            const question = generatedQuestions.find(q => q.id === questionId);
            if (question && question.choices[choiceIndex]) {
                question.choices[choiceIndex].text = text;
            }
        }

        function updateCorrectAnswer(questionId, choiceIndex) {
            const question = generatedQuestions.find(q => q.id === questionId);
            if (question) {
                question.choices.forEach((choice, index) => {
                    choice.isCorrect = index === choiceIndex;
                });
                
                // Update the visual indicators
                const questionElement = document.querySelector(`[data-question-id="${questionId}"]`);
                const choiceLetters = questionElement.querySelectorAll('.choice-letter');
                choiceLetters.forEach((letter, index) => {
                    if (index === choiceIndex) {
                        letter.className = 'choice-letter correct';
                    } else {
                        letter.className = 'choice-letter incorrect';
                    }
                });
                
                updateQuestionsDisplay();
            }
        }

        function deleteQuestion(questionId) {
            if (confirm('Are you sure you want to delete this question?')) {
                generatedQuestions = generatedQuestions.filter(q => q.id !== questionId);
                updateQuestionsDisplay();
                showSuccessMessage('Question deleted successfully');
            }
        }

        function clearAllQuestions() {
            if (confirm('Are you sure you want to clear all generated questions?')) {
                generatedQuestions = [];
                updateQuestionsDisplay();
                showSuccessMessage('All questions cleared');
            }
        }

        async function saveQuizToDatabase() {
            if (generatedQuestions.length === 0) {
                showErrorMessage('No questions to save. Please generate some questions first.');
                return;
            }
            
            // Validate that all questions have correct answers
            const invalidQuestions = generatedQuestions.filter(q => 
                !q.choices.some(c => c.isCorrect)
            );
            
            if (invalidQuestions.length > 0) {
                showErrorMessage(`Please select correct answers for all questions. ${invalidQuestions.length} question(s) are missing correct answers.`);
                return;
            }
            
            showLoading(true, 'Saving quiz to database...');
            
            try {
                const payload = {
                    title: document.getElementById('quizTitle').value,
                    description: document.getElementById('quizDescription').value || '',
                    instructions: document.getElementById('quizInstructions').value || '',
                    activityType: 'quiz',
                    subject: parseInt(document.getElementById('quizSubject').value),
                    branch: parseInt(document.getElementById('quizBranch').value),
                    semester: parseInt(document.getElementById('quizSemester').value),
                    duration: document.getElementById('quizDuration').value ? parseInt(document.getElementById('quizDuration').value) : null,
                    deadline: document.getElementById('quizDeadline').value || null,
                    facultyId: facultyId,
                    totalScore: generatedQuestions.reduce((sum, q) => sum + q.points, 0),
                    totalQuestions: generatedQuestions.length,
                    questions: generatedQuestions.map((q, index) => ({
                        number: index + 1,
                        text: q.text,
                        type: 'mcq',
                        points: q.points,
                        choices: q.choices,
                        correctAnswers: q.choices.filter(c => c.isCorrect).map(c => c.letter)
                    }))
                };
                
                console.log('Saving quiz with payload:', payload);
                
                const response = await fetch('post_quiz_ai.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showSuccessMessage('Quiz saved successfully!');
                    setTimeout(() => {
                        window.location.href = 'quiz_list.php?success=' + encodeURIComponent(data.message);
                    }, 2000);
                } else {
                    throw new Error(data.error || 'Save failed');
                }
                
            } catch (error) {
                console.error('Save error:', error);
                showErrorMessage('Error saving quiz: ' + error.message);
            } finally {
                showLoading(false);
            }
        }

        function showLoading(show, message = 'Generating Questions...') {
            const overlay = document.getElementById('loadingOverlay');
            const loadingContent = overlay.querySelector('.loading-content h5');
            
            if (show) {
                loadingContent.textContent = message;
                overlay.style.display = 'flex';
            } else {
                overlay.style.display = 'none';
            }
        }

        function showSuccessMessage(message) {
            // Create and show success alert
            const alertHtml = `
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fe fe-check-circle me-2"></i>${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            const container = document.querySelector('.container-fluid .row .col-12');
            const firstCard = container.querySelector('.card');
            firstCard.insertAdjacentHTML('beforebegin', alertHtml);
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                const alert = container.querySelector('.alert-success');
                if (alert) alert.remove();
            }, 5000);
        }

        function showErrorMessage(message) {
            // Create and show error alert
            const alertHtml = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fe fe-alert-circle me-2"></i>${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            const container = document.querySelector('.container-fluid .row .col-12');
            const firstCard = container.querySelector('.card');
            firstCard.insertAdjacentHTML('beforebegin', alertHtml);
            
            // Auto-dismiss after 8 seconds
            setTimeout(() => {
                const alert = container.querySelector('.alert-danger');
                if (alert) alert.remove();
            }, 8000);
        }

        // Initialize on page load
        window.addEventListener('load', function() {
            // Set minimum date for deadline to current date
            const now = new Date();
            const minDate = now.toISOString().slice(0, 16);
            document.getElementById('quizDeadline').setAttribute('min', minDate);
        });
    </script>
</body>
</html>
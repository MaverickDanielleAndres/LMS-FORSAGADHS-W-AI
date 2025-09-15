
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
    </style>
</head>
<body>
    <?php $nav_role = "Quiz"; include_once("nav.php"); ?>
    
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
                                            <input type="number" class="form-control" id="numQuestions" min="5" max="50" value="10">
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
                                        <span class="spinner-border spinner-border-sm loading-spinner" id="generateSpinner" role="status" aria-hidden="true"></span>
                                        <i class="fe fe-zap me-2"></i>Generate Quiz
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Generated Questions Display -->
                    <div class="card" id="questionsCard" style="display: none;">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-header-title mb-0"><i class="fe fe-list me-2"></i>Generated Questions</h4>
                            <div>
                                <span class="badge bg-primary" id="questionCount">0 Questions</span>
                                <span class="badge bg-success" id="totalPoints">0 Points</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="questionsContainer"></div>
                            <div class="d-flex justify-content-between mt-3">
                                <div>
                                    <button type="button" class="btn btn-outline-secondary" id="regenerateBtn">
                                        <i class="fe fe-refresh-cw me-2"></i>Regenerate
                                    </button>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-secondary me-2" id="cancelBtn">Cancel</button>
                                    <button type="button" class="btn btn-success" id="saveBtn">
                                        <span class="spinner-border spinner-border-sm loading-spinner" id="saveSpinner" role="status" aria-hidden="true"></span>
                                        <i class="fe fe-save me-2"></i>Save Quiz
                                    </button>
                                </div>
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

        function initializeApp() {
            setupEventListeners();
            setupFileUpload();
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
            
            // Generate button
            document.getElementById('generateBtn').addEventListener('click', generateQuiz);
            
            // Regenerate button
            document.getElementById('regenerateBtn').addEventListener('click', regenerateQuiz);
            
            // Save button
            document.getElementById('saveBtn').addEventListener('click', saveQuiz);
            
            // Cancel button
            document.getElementById('cancelBtn').addEventListener('click', function() {
                document.getElementById('questionsCard').style.display = 'none';
            });

            // Existing material selection
            document.getElementById('existingMaterial').addEventListener('change', function() {
                if (this.value) {
                    // Clear file selection when material is selected
                    clearFileSelection();
                }
            });

            // File input change should clear material selection
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

        async function generateQuiz() {
            if (!validateForm()) return;
            
            const generateBtn = document.getElementById('generateBtn');
            const generateSpinner = document.getElementById('generateSpinner');
            
            generateBtn.disabled = true;
            generateSpinner.style.display = 'inline-block';
            
            try {
                const formData = new FormData();
                
                // Add all form data
                formData.append('title', document.getElementById('quizTitle').value);
                formData.append('description', document.getElementById('quizDescription').value);
                formData.append('instructions', document.getElementById('quizInstructions').value);
                formData.append('subject', document.getElementById('quizSubject').value);
                formData.append('branch', document.getElementById('quizBranch').value);
                formData.append('semester', document.getElementById('quizSemester').value);
                formData.append('duration', document.getElementById('quizDuration').value);
                formData.append('deadline', document.getElementById('quizDeadline').value);
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
                
                if (data.response) {
                    parseAndDisplayQuestions(data.response);
                } else {
                    throw new Error(data.error || 'Generation failed');
                }
                
            } catch (error) {
                console.error('Generate error:', error);
                alert('Error generating quiz: ' + error.message);
            } finally {
                generateBtn.disabled = false;
                generateSpinner.style.display = 'none';
            }
        }

        function validateForm() {
            const title = document.getElementById('quizTitle').value.trim();
            const subject = document.getElementById('quizSubject').value;
            const numQuestions = parseInt(document.getElementById('numQuestions').value);
            
            if (!title) {
                alert('Please enter a quiz title');
                return false;
            }
            
            if (!subject) {
                alert('Please select a subject');
                return false;
            }
            
            if (!numQuestions || numQuestions < 1 || numQuestions > 50) {
                alert('Please enter a valid number of questions (1-50)');
                return false;
            }
            
            return true;
        }

        function parseAndDisplayQuestions(aiResponse) {
            generatedQuestions = parseAIResponse(aiResponse);
            if (generatedQuestions.length === 0) {
                alert('No valid questions could be parsed from the AI response. Please try again.');
                return;
            }
            displayQuestions();
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
                    q.correctAnswers = [correctLetter];
                }
            });
            
            return questions;
        }

        function displayQuestions() {
            const questionsCard = document.getElementById('questionsCard');
            const questionsContainer = document.getElementById('questionsContainer');
            const questionCount = document.getElementById('questionCount');
            const totalPoints = document.getElementById('totalPoints');
            
            let totalPointsValue = generatedQuestions.reduce((sum, q) => sum + q.points, 0);
            
            questionCount.textContent = `${generatedQuestions.length} Questions`;
            totalPoints.textContent = `${totalPointsValue} Points`;
            
            let html = '';
            generatedQuestions.forEach((question, index) => {
                html += `
                    <div class="question-block mb-4 p-3" style="border: 2px solid #e9ecef; border-radius: 8px; background: #f8f9fa;">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="mb-0 text-primary">Question ${question.number}</h6>
                            <div class="d-flex align-items-center gap-2">
                                <label class="form-label mb-0 small">Points:</label>
                                <input type="number" class="form-control form-control-sm" style="width:70px" 
                                       value="${question.points}" min="0.5" step="0.5" 
                                       onchange="updateQuestionPoints(${index}, this.value)">
                            </div>
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" rows="2" onchange="updateQuestionText(${index}, this.value)">${question.text}</textarea>
                        </div>
                        <div class="choices">
                            ${question.choices.map((choice, choiceIndex) => `
                                <div class="choice-item mb-2 d-flex align-items-center gap-2">
                                    <span class="badge ${choice.isCorrect ? 'bg-success' : 'bg-secondary'}">${choice.letter}</span>
                                    <input type="text" class="form-control" value="${choice.text}" 
                                           onchange="updateChoiceText(${index}, ${choiceIndex}, this.value)">
                                    <div class="form-check">
                                        <input type="radio" class="form-check-input" name="correct_${index}"
                                               ${choice.isCorrect ? 'checked' : ''}
                                               onchange="updateCorrectAnswer(${index}, ${choiceIndex})">
                                        <label class="form-check-label small">Correct</label>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            });
            
            questionsContainer.innerHTML = html;
            questionsCard.style.display = 'block';
            questionsCard.scrollIntoView({ behavior: 'smooth' });
        }

        function updateQuestionPoints(questionIndex, points) {
            generatedQuestions[questionIndex].points = parseFloat(points) || 0;
            updateTotalPoints();
        }

        function updateQuestionText(questionIndex, text) {
            generatedQuestions[questionIndex].text = text;
        }

        function updateChoiceText(questionIndex, choiceIndex, text) {
            generatedQuestions[questionIndex].choices[choiceIndex].text = text;
        }

        function updateCorrectAnswer(questionIndex, choiceIndex) {
            // For multiple choice, only one can be correct
            generatedQuestions[questionIndex].choices.forEach((choice, index) => {
                choice.isCorrect = index === choiceIndex;
            });
            
            // Update the visual badges
            const questionBlock = event.target.closest('.question-block');
            const badges = questionBlock.querySelectorAll('.badge');
            badges.forEach((badge, index) => {
                if (index === choiceIndex) {
                    badge.className = 'badge bg-success';
                } else {
                    badge.className = 'badge bg-secondary';
                }
            });
            
            updateTotalPoints();
        }

        function updateTotalPoints() {
            const total = generatedQuestions.reduce((sum, q) => sum + q.points, 0);
            document.getElementById('totalPoints').textContent = `${total} Points`;
        }

        function regenerateQuiz() {
            if (!confirm('Are you sure you want to regenerate the quiz? This will replace all current questions.')) {
                return;
            }
            document.getElementById('questionsCard').style.display = 'none';
            generateQuiz();
        }

        async function saveQuiz() {
            if (!generatedQuestions.length) {
                alert('No questions to save');
                return;
            }
            
            // Validate that all questions have correct answers
            const invalidQuestions = generatedQuestions.filter(q => 
                !q.choices.some(c => c.isCorrect)
            );
            
            if (invalidQuestions.length > 0) {
                alert(`Please select correct answers for all questions. Questions ${invalidQuestions.map(q => q.number).join(', ')} are missing correct answers.`);
                return;
            }
            
            const saveBtn = document.getElementById('saveBtn');
            const saveSpinner = document.getElementById('saveSpinner');
            
            saveBtn.disabled = true;
            saveSpinner.style.display = 'inline-block';
            
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
                    alert('Quiz saved successfully!');
                    window.location.href = 'quiz_list.php?success=' + encodeURIComponent(data.message);
                } else {
                    throw new Error(data.error || 'Save failed');
                }
                
            } catch (error) {
                console.error('Save error:', error);
                alert('Error saving quiz: ' + error.message);
            } finally {
                saveBtn.disabled = false;
                saveSpinner.style.display = 'none';
            }
        }

        function displayQuestions() {
    const container = document.getElementById('questionsContainer');
    container.innerHTML = '';

    generatedQuestions.forEach((q, index) => {
        const div = document.createElement('div');
        div.className = 'question-block mb-3 p-3 border rounded bg-light';
        div.innerHTML = `
            <div class="d-flex justify-content-between align-items-start mb-2">
                <strong>Question ${index + 1}</strong>
                <div>
                    <label class="me-2">Points:</label>
                    <input type="number" class="form-control form-control-sm d-inline-block" style="width:80px" value="${q.points}" onchange="updateQuestionPoints(${index}, this.value)">
                    <button class="btn btn-sm btn-outline-danger ms-2" onclick="deleteQuestion(${index})"><i class="fe fe-trash"></i></button>
                </div>
            </div>
            <textarea class="form-control mb-2" rows="2" onchange="updateQuestionText(${index}, this.value)">${q.text}</textarea>
            <div class="choices">
                ${q.choices.map((c, i) => `
                    <div class="choice-item mb-1 d-flex align-items-center gap-2">
                        <span class="badge ${c.isCorrect ? 'bg-success' : 'bg-secondary'}">${c.letter}</span>
                        <input type="text" class="form-control" value="${c.text}" onchange="updateChoiceText(${index}, ${i}, this.value)">
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="correct_${index}" ${c.isCorrect ? 'checked' : ''} onchange="updateCorrectAnswer(${index}, ${i})">
                            <label class="form-check-label small">Correct</label>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;
        container.appendChild(div);
    }

    document.getElementById('questionsCard').style.display = 'block';
    updateTotalPoints();
}

function deleteQuestion(index) {
    if (confirm('Delete this question?')) {
        generatedQuestions.splice(index, 1);
        displayQuestions();
    }
}
    </script>
</body>
</html>
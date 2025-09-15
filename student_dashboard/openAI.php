<?php
session_start();
if ($_SESSION['role'] != "student") {
    header("Location: ../index.php");
} else {
    include_once("../config.php");
    $u = $_SESSION["id"];
    $_SESSION["userrole"] = "Student";
    $qur = "SELECT * FROM `studentmaster` WHERE `StudentUserName`='$u'";
    $row = mysqli_fetch_assoc(mysqli_query($conn, $qur));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="../assets/favicon/favicon.png" type="image/x-icon" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <link rel="stylesheet" href="libs.bundle.css">
    <?php include_once("../head.php"); ?>

     <style>
        @media (max-width: 1246px) {
            .row {
                flex-direction: column;
                align-items: center;
            }

            .col-8 {
                width: 100%;
                margin-bottom: 20px;
            }

            .col-4 {
                width: 80%;
            }
        }

        @media (max-width: 877px) {
            .row {
                flex-direction: column;
                align-items: center;
            }

            .col-8 {
                width: 100%;
            }

            .col-4 {
                width: 100%;
            }
        }

        @media (max-width: 766px) {
            .row {
                flex-direction: column;
                align-items: center;
            }

            .col-8 {
                width: 100%;
            }

            .col-4 {
                width: 80%;
            }
        }

        @media (max-width: 520px) {
            .row {
                flex-direction: column;
                align-items: center;
            }

            .col-8 {
                width: 100%;
            }

            .col-4 {
                width: 100%;
            }
        }

        .container {
            max-width: 100%;
            padding: 10px;
        }

        .chat-container {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            margin-top: 10px;
            margin-left: auto;
            margin-right: auto;
            flex-grow: 1;
            height: 80vh;
            width: 100%;
        }

        .chat-box {
            background: #ffffff;
            border-radius: 20px;
            padding: 20px;
            flex-grow: 1;
            overflow-y: auto;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            word-wrap: break-word;
            width: 100%;
            position: relative;
        }

        .chat-box p,
        .ai-response-container {
            clear: both;
        }

        .chat-box::-webkit-scrollbar {
            width: 10px;
        }

        .user-message {
            float: right;
            background-color: #183B4E;
            margin-left: auto;
            margin-right: 0;
            border-radius: 15px;
            padding: 10px;
            max-width: 80%;
            word-wrap: break-word;
            color: white;
            margin-bottom: 10px;
            white-space: pre-wrap;
        }

        .ai-response-container {
            float: left;
            margin-right: auto;
            margin-left: 0;
            margin-bottom: 10px;
            display: flex;
            align-items: flex-start;
        }

        .ai-response-icon {
            width: 30px;
            height: 30px;
            margin-right: 10px;
            border-radius: 50%;
            background-color: #444;
        }

        .ai-response-text {
            background-color: #E5E4E2;
            color: black;
            padding: 10px;
            border-radius: 15px;
            max-width: 80%;
            word-wrap: break-word;
        }

        .task-selector {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 15px 0;
            flex-wrap: wrap;
        }

        .task-btn {
            padding: 8px 16px;
            background-color: #f0f0f0;
            color: #333;
            border: 2px solid #ddd;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
            min-width: 80px;
        }

        .task-btn:hover {
            background-color: #e0e0e0;
            border-color: #ccc;
        }

        .task-btn.active {
            background-color: #183B4E;
            color: white;
            border-color: #183B4E;
        }

        .clear-btn {
            padding: 8px 16px;
            background-color: #dc3545;
            color: white;
            border: 2px solid #dc3545;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
            min-width: 80px;
        }

        .clear-btn:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        /* NEW: Material selector button style */
        .material-btn {
            padding: 8px 16px;
            background-color: #28a745;
            color: white;
            border: 2px solid #28a745;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
            min-width: 80px;
        }

        .material-btn:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .progress-bar {
            width: 100%;
            height: 4px;
            background-color: #f0f0f0;
            border-radius: 2px;
            overflow: hidden;
            margin: 10px 0;
            display: none;
        }

        .progress-fill {
            height: 100%;
            background-color: #183B4E;
            width: 0%;
            transition: width 0.5s ease;
        }

        .loading-indicator {
            text-align: center;
            color: #666;
            font-style: italic;
            margin: 10px 0;
            display: none;
        }

        .input-container {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            background-color: white;
            border-radius: 20px;
            width: 100%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            gap: 5px;
        }

        input[type="text"] {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            background-color: #E5E4E2;
            color: #333;
            outline: none;
        }

        /* Multi-line textarea for chat */
        .multi-line-input {
            flex: 1;
            border: none;
            border-radius: 25px;
            padding: 12px 10px;
            font-size: 16px;
            background-color: #E5E4E2;
            color: #333;
            outline: none;
            resize: none;
            font-family: inherit;
            line-height: 1.4;
            min-height: 20px;
            max-height: 100px;
            overflow-y: auto;
        }

        .multi-line-input:focus {
            border-color: #183B4E;
        }

        .multi-line-input::placeholder {
            color: gray;
        }

        .multi-line-input::-webkit-scrollbar {
            width: 6px;
        }

        .multi-line-input::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .multi-line-input::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .multi-line-input::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        input[type="text"]:focus {
            border-color: #183B4E;
        }

        input[type="text"]::placeholder {
            color: gray;
        }

        input[type="file"] {
            display: none;
        }

        .file-label {
            display: inline-block;
            cursor: pointer;
            background-image: url('attachment.png');
            background-size: cover;
            width: 40px;
            height: 40px;
            margin-right: 10px;
            transition: transform 0.2s;
        }

        .file-label:hover {
            transform: scale(1.1);
        }

        .file-name {
            font-size: 14px;
            color: #333;
            vertical-align: middle;
        }

        #uploadBtn, #sendBtn {
            padding: 12px 20px;
            background-color: #183B4E;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        #uploadBtn:hover, #sendBtn:hover {
            background-color: #275589;
        }

        #uploadBtn:disabled #sendBtn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

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

        .analysis-type {
            font-weight: bold;
            color: #183B4E;
            margin-bottom: 10px;
        }

        .model-info {
            font-size: 12px;
            color: #666;
            margin-top: 10px;
            font-style: italic;
        }

        .input-hint {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
            text-align: center;
        }

        /* NEW: Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }

        .modal-title {
            font-size: 24px;
            font-weight: bold;
            color: #183B4E;
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }

        .subject-group {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }

        .subject-header {
            background-color: #183B4E;
            color: white;
            padding: 12px 15px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .subject-header:hover {
            background-color: #275589;
        }

        .subject-materials {
            display: none;
            padding: 0;
        }

        .subject-materials.show {
            display: block;
        }

        .material-item {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.2s;
        }

        .material-item:hover {
            background-color: #f8f9fa;
        }

        .material-item:last-child {
            border-bottom: none;
        }

        .material-info {
            flex-grow: 1;
        }

        .material-name {
            font-weight: 500;
            color: #333;
            margin-bottom: 4px;
        }

        .material-details {
            font-size: 12px;
            color: #666;
        }

        .material-select-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 15px;
            cursor: pointer;
            font-size: 12px;
            transition: background-color 0.2s;
        }

        .material-select-btn:hover {
            background-color: #218838;
        }

        .expand-icon {
            transition: transform 0.2s;
        }

        .expand-icon.rotated {
            transform: rotate(180deg);
        }

        .no-materials {
            padding: 20px;
            text-align: center;
            color: #666;
            font-style: italic;
        }

        .loading-materials {
            padding: 20px;
            text-align: center;
            color: #666;
        }

        .selected-material-info {
            background-color: #e8f5e8;
            border: 1px solid #28a745;
            border-radius: 8px;
            padding: 10px;
            margin: 10px 0;
            display: none;
        }

        .selected-material-info.show {
            display: block;
        }

        .selected-material-name {
            font-weight: bold;
            color: #28a745;
            margin-bottom: 5px;
        }

        .selected-material-subject {
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
<?php $nav_role = "Reviewer"; ?>
<!-- NAVIGATION -->
<?php include_once("nav.php"); ?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <!-- Header -->
                <div class="header">
                    <div class="header-body">
                       <div class="row align-items-center">
                                <!-- Left Column: Title -->
                                <div class="col">
                                    <h1 class="header-title text-truncate">
                                        Reviewer
                                    </h1>
                                </div>

                                <!-- Right Column: Back Button -->
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

                <div class="container">
                    <div class="row">
                        <!-- File Analysis Container -->
                        <div class="col-8">
                            <div class="chat-container">
                                <div class="chat-box" id="chatBox1">
                                    <div class="ai-response-container">
                                        <div class="ai-response-text">
                                            <div class="analysis-type">Ready for Analysis</div>
                                            Upload any document (PDF, TXT, DOC, DOCX, PPT, PPTX) or select a study material from your subjects and I'll provide comprehensive analysis based on your selected task.
                                        </div>
                                    </div>
                                </div>
                                <div class="task-selector">
                                    <button class="task-btn active" data-task="summarize">Summarize</button>
                                    <button class="task-btn" data-task="question">Question Generator</button>
                                    <button class="material-btn" onclick="openMaterialModal()">📚 Study Materials</button>
                                    <button class="clear-btn" onclick="clearSummarizePanel()">Clear</button>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill"></div>
                                </div>
                                <div class="loading-indicator">Processing...</div>
                                
                                <!-- Selected Material Info -->
                                <div class="selected-material-info" id="selectedMaterialInfo">
                                    <div class="selected-material-name" id="selectedMaterialName"></div>
                                    <div class="selected-material-subject" id="selectedMaterialSubject"></div>
                                </div>
                                
                                <div class="input-container">
                                    <label class="file-label" for="fileInput" title="Upload File"></label>
                                    <input type="file" id="fileInput" onchange="displayFileName()" accept=".txt,.pdf,.doc,.docx,.ppt,.pptx" />
                                    <span id="fileName" class="file-name">No file chosen</span>
                                    <button onclick="uploadFile()" id="uploadBtn">Analyze</button>
                                </div>
                                <div class="input-hint">📕 PDF , 📘 DOCX, 📊 PPTx, 📝 TXT or 📚 Study Materials</div>
                            </div>
                        </div>

                        <!-- Chat Container -->
                        <div class="col-4">
                            <div class="chat-container">
                                <div class="chat-box" id="chatBox2">
                                    <!-- Chat messages will appear here -->
                                </div>
                                <div class="task-selector">
                                    <button class="task-btn active" data-task="chat" data-chat="true">Chat</button>
                                    <button class="clear-btn" onclick="clearChatBox2()">Clear</button>
                                </div>
                                <div class="loading-indicator" id="chatLoading">Thinking...</div>
                                <div class="input-container">
                                    <textarea rows="1" class="multi-line-input" id="userMessage" placeholder="Type a message..."></textarea>
                                    <button onclick="sendMessage()" id="sendBtn">Send</button>
                                </div>
                                <div class="input-hint">Shift+Enter for new line, Enter to send</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Material Selection Modal -->
<div id="materialModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">📚 Select Study Material</h2>
            <span class="close" onclick="closeMaterialModal()">&times;</span>
        </div>
        <div id="materialList">
            <div class="loading-materials">
                <i class="fe fe-loader"></i> Loading your study materials...
            </div>
        </div>
    </div>
</div>

<script>
// Global variables
let currentFileTask = 'summarize';
let currentChatTask = 'chat';
let selectedMaterial = null;

// Task selector functionality
document.querySelectorAll('.task-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const isChat = this.dataset.chat === 'true';
        const taskButtons = isChat ? 
            document.querySelectorAll('.task-btn[data-chat="true"]') : 
            document.querySelectorAll('.task-btn:not([data-chat="true"])');
        
        taskButtons.forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        if (isChat) {
            currentChatTask = this.dataset.task;
        } else {
            currentFileTask = this.dataset.task;
        }
    });
});

// Material Modal Functions
function openMaterialModal() {
    document.getElementById('materialModal').style.display = 'block';
    loadStudyMaterials();
}

function closeMaterialModal() {
    document.getElementById('materialModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('materialModal');
    if (event.target == modal) {
        closeMaterialModal();
    }
}

function loadStudyMaterials() {
    const materialList = document.getElementById('materialList');
    materialList.innerHTML = '<div class="loading-materials"><i class="fe fe-loader"></i> Loading your study materials...</div>';
    
    fetch('get_materials.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayMaterials(data.materials);
            } else {
                materialList.innerHTML = '<div class="no-materials">❌ Error loading materials: ' + data.error + '</div>';
            }
        })
        .catch(error => {
            materialList.innerHTML = '<div class="no-materials">❌ Network error. Please try again.</div>';
            console.error('Error:', error);
        });
}

function displayMaterials(materials) {
    const materialList = document.getElementById('materialList');
    
    if (materials.length === 0) {
        materialList.innerHTML = '<div class="no-materials">📚 No study materials available yet.</div>';
        return;
    }
    
    // Group materials by subject
    const groupedMaterials = {};
    materials.forEach(material => {
        if (!groupedMaterials[material.SubjectName]) {
            groupedMaterials[material.SubjectName] = [];
        }
        groupedMaterials[material.SubjectName].push(material);
    });
    
    let html = '';
    Object.keys(groupedMaterials).forEach(subjectName => {
        const subjectMaterials = groupedMaterials[subjectName];
        const subjectId = subjectName.replace(/\s+/g, '').toLowerCase();
        
        html += `
            <div class="subject-group">
                <div class="subject-header" onclick="toggleSubject('${subjectId}')">
                    <span>${subjectName} (${subjectMaterials.length} materials)</span>
                    <span class="expand-icon" id="icon-${subjectId}">▼</span>
                </div>
                <div class="subject-materials" id="materials-${subjectId}">
        `;
        
        subjectMaterials.forEach(material => {
            html += `
                <div class="material-item">
                    <div class="material-info">
                        <div class="material-name">${material.SubjectUnitName}</div>
                        <div class="material-details">Unit ${material.SubjectUnitNo} • ${material.MaterialFile}</div>
                    </div>
                    <button class="material-select-btn" onclick="selectMaterial(${JSON.stringify(material).replace(/"/g, '&quot;')})">
                        Select
                    </button>
                </div>
            `;
        });
        
        html += `
                </div>
            </div>
        `;
    });
    
    materialList.innerHTML = html;
}

function toggleSubject(subjectId) {
    const materialsDiv = document.getElementById('materials-' + subjectId);
    const iconSpan = document.getElementById('icon-' + subjectId);
    
    if (materialsDiv.classList.contains('show')) {
        materialsDiv.classList.remove('show');
        iconSpan.classList.remove('rotated');
    } else {
        materialsDiv.classList.add('show');
        iconSpan.classList.add('rotated');
    }
}

function selectMaterial(material) {
    selectedMaterial = material;
    
    // Update selected material display
    const selectedInfo = document.getElementById('selectedMaterialInfo');
    const selectedName = document.getElementById('selectedMaterialName');
    const selectedSubject = document.getElementById('selectedMaterialSubject');
    
    selectedName.textContent = material.SubjectUnitName;
    selectedSubject.textContent = `${material.SubjectName} • Unit ${material.SubjectUnitNo}`;
    selectedInfo.classList.add('show');
    
    // Clear file input since we're using material instead
    const fileInput = document.getElementById('fileInput');
    const fileNameSpan = document.getElementById('fileName');
    fileInput.value = '';
    const materialPath = `../src/uploads/studymaterial/${material.MaterialFile}`;
fetch(materialPath)
    .then(response => {
        if (!response.ok) throw new Error('Failed to fetch file metadata');
        return response.blob();
    })
    .then(blob => {
        const sizeMB = (blob.size / 1024 / 1024).toFixed(2) + ' MB';
        let icon = '📚'; // ✅ Unified icon for all study materials
        fileNameSpan.innerHTML = `${icon} ${material.MaterialFile} (${sizeMB})`;
    })
    .catch(error => {
        fileNameSpan.textContent = 'Study material selected';
        console.error('Failed to fetch material metadata:', error);
    });
    
    closeMaterialModal();
}

// Clear summarize panel function
function clearSummarizePanel() {
    const chatBox = document.getElementById('chatBox1');
    chatBox.innerHTML = `
        <div class="ai-response-container">
            <div class="ai-response-text">
                <div class="analysis-type">Ready for Analysis</div>
                Upload any document (PDF, TXT, DOC, DOCX, PPT, PPTX) or select a study material from your subjects and I'll provide comprehensive analysis based on your selected task.
            </div>
        </div>
    `;
    
    // Clear file input and selected material
    const fileInput = document.getElementById('fileInput');
    const fileNameSpan = document.getElementById('fileName');
    const selectedInfo = document.getElementById('selectedMaterialInfo');
    
    fileInput.value = '';
    fileNameSpan.textContent = 'No file chosen';
    selectedInfo.classList.remove('show');
    selectedMaterial = null;
}

function clearChatBox2() {
    const chatBox = document.getElementById('chatBox2');
    chatBox.innerHTML = `
        <div class="ai-response-container">
            <div class="ai-response-text">
                <div class="analysis-type">Chat Reset</div>
                Hello! I'm your Sagad Assistant. I can help you with document analysis, general conversation, and explanations. How can I assist you today?
            </div>
        </div>
    `;

    // Also reset the chat input box
    const messageInput = document.getElementById('userMessage');
    messageInput.value = '';
    messageInput.style.height = 'auto';
}

// Auto-resize textarea function
function autoResizeTextarea(textarea) {
    textarea.style.height = 'auto';
    const scrollHeight = textarea.scrollHeight;
    const maxHeight = 100; // 5 lines approximately
    
    if (scrollHeight > maxHeight) {
        textarea.style.height = maxHeight + 'px';
        textarea.style.overflowY = 'auto';
    } else {
        textarea.style.height = scrollHeight + 'px';
        textarea.style.overflowY = 'hidden';
    }
}

// Enhanced message input handling
document.getElementById("userMessage").addEventListener("input", function() {
    autoResizeTextarea(this);
});

document.getElementById("userMessage").addEventListener("keydown", function(event) {
    if (event.key === "Enter") {
        if (event.shiftKey) {
            // Allow new line with Shift+Enter
            return;
        } else {
            // Send message with Enter
            event.preventDefault();
            sendMessage();
        }
    }
});

// File handling functions
function displayFileName() {
    const fileInput = document.getElementById('fileInput');
    const fileNameSpan = document.getElementById('fileName');
    const selectedInfo = document.getElementById('selectedMaterialInfo');
    
    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        const fileName = file.name;
        const fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
        
        // Add file type icon
        const extension = fileName.split('.').pop().toLowerCase();
        let icon = '📄';
        if (extension === 'pdf') icon = '📕';
        if (['doc', 'docx'].includes(extension)) icon = '📘';
        if (['ppt', 'pptx'].includes(extension)) icon = '📊';
        if (extension === 'txt') icon = '📝';
        
        fileNameSpan.innerHTML = `${icon} ${fileName} (${fileSize})`;
        
        // Clear selected material when file is selected
        selectedMaterial = null;
        selectedInfo.classList.remove('show');
    } else {
        fileNameSpan.textContent = 'No file chosen';
    }
}

function showProgress() {
    const progressBar = document.querySelector('.progress-bar');
    const progressFill = document.querySelector('.progress-fill');
    const loadingIndicator = document.querySelector('.loading-indicator');
    
    progressBar.style.display = 'block';
    loadingIndicator.style.display = 'block';
    
    let progress = 0;
    const interval = setInterval(() => {
        progress += Math.random() * 15;
        if (progress > 95) progress = 95;
        progressFill.style.width = progress + '%';
    }, 500);
    
    return interval;
}

function hideProgress(interval) {
    const progressBar = document.querySelector('.progress-bar');
    const progressFill = document.querySelector('.progress-fill');
    const loadingIndicator = document.querySelector('.loading-indicator');
    
    clearInterval(interval);
    progressFill.style.width = '100%';
    
    setTimeout(() => {
        progressBar.style.display = 'none';
        loadingIndicator.style.display = 'none';
        progressFill.style.width = '0%';
    }, 500);
}

// ✅ FIXED: Updated uploadFile function to use correct endpoints
function uploadFile() {
    const fileInput = document.getElementById("fileInput");
    const uploadBtn = document.getElementById("uploadBtn");

    const progressInterval = showProgress();
    uploadBtn.disabled = true;
    uploadBtn.textContent = "Processing...";

    let filePromise;

    if (selectedMaterial) {
        // Load material file as blob
        const materialPath = `../src/uploads/studymaterial/${selectedMaterial.MaterialFile}`;
        filePromise = fetch(materialPath)
            .then(response => {
                if (!response.ok) throw new Error('Failed to fetch study material');
                return response.blob().then(blob => new File([blob], selectedMaterial.MaterialFile));
            });
    } else if (fileInput.files.length > 0) {
        filePromise = Promise.resolve(fileInput.files[0]);
    } else {
        alert("Please upload a file or select a study material.");
        hideProgress(progressInterval);
        uploadBtn.disabled = false;
        uploadBtn.textContent = "Analyze";
        return;
    }

    filePromise.then(file => {
        document.getElementById('selectedMaterialInfo').classList.remove('show');
        // Validate file type
        const extension = file.name.split('.').pop().toLowerCase();
        const allowedExtensions = ['pdf', 'txt', 'doc', 'docx', 'ppt', 'pptx'];

        if (!allowedExtensions.includes(extension)) {
            alert('Only document files (PDF, TXT, DOC, DOCX, PPT, PPTX) are allowed.');
            hideProgress(progressInterval);
            uploadBtn.disabled = false;
            uploadBtn.textContent = "Analyze";
            return;
        }

        const formData = new FormData();
        formData.append("file", file);

        let endpoint = '';
        if (currentFileTask === 'question') {
            endpoint = 'generate-questions';
        } else if (currentFileTask === 'summarize') {
            endpoint = 'upload';
        } else {
            endpoint = 'analyze';
        }

        fetch(`http://localhost:5000/${endpoint}`, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            hideProgress(progressInterval);
            let analysisType = '';
            if (currentFileTask === 'question') {
                analysisType = 'Question Generator Complete';
            } else if (currentFileTask === 'summarize') {
                analysisType = 'Summary Complete';
            } else {
                analysisType = `${currentFileTask.charAt(0).toUpperCase() + currentFileTask.slice(1)} Complete`;
            }
            appendMessage(`<div class="analysis-type">${analysisType}</div>`, "ai", 'chatBox1');
            appendMessage(data.response || `❌ ${data.error}`, "ai", 'chatBox1');
        })
        .catch(error => {
            hideProgress(progressInterval);
            appendMessage(`<div class="analysis-type">Error</div>Network error: ${error.message}`, "ai", 'chatBox1');
        })
        .finally(() => {
            uploadBtn.disabled = false;
            uploadBtn.textContent = "Analyze";
        });
    }).catch(error => {
        hideProgress(progressInterval);
        appendMessage(`<div class="analysis-type">Error</div>Failed to load material: ${error.message}`, "ai", 'chatBox1');
        uploadBtn.disabled = false;
        uploadBtn.textContent = "Analyze";
    });
}


// Chat functions
function sendMessage() {
    const messageInput = document.getElementById("userMessage");
    const message = messageInput.value.trim();
    const sendBtn = document.getElementById("sendBtn");
    const chatLoading = document.getElementById("chatLoading");
    
    if (message === "") return;

    // Display user message
    appendMessage(message, "user", 'chatBox2');
    
    // Clear input and show loading
    messageInput.value = "";
    messageInput.style.height = 'auto';
    sendBtn.disabled = true;
    sendBtn.textContent = "...";
    chatLoading.style.display = 'block';

    // Send to server
    fetch('http://localhost:5000/chat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ 
            user_message: message,
            task: currentChatTask,
            model_preference: 'auto'
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        chatLoading.style.display = 'none';
        
        if (data.response) {
            appendMessage(data.response, "ai", 'chatBox2');
        } else if (data.error) {
            appendMessage("Error: " + data.error, "ai", 'chatBox2');
        }
    })
    .catch(error => {
        chatLoading.style.display = 'none';
        appendMessage("Network error: " + error.message + ". Please check your connection and try again.", "ai", 'chatBox2');
    })
    .finally(() => {
        sendBtn.disabled = false;
        sendBtn.textContent = "Send";
    });
}

// Message display function
function appendMessage(content, sender, chatBoxId) {
    const chatBox = document.getElementById(chatBoxId);

    if (sender === "ai") {
        const aiContainer = document.createElement("div");
        aiContainer.classList.add("ai-response-container");

        // Add icon only for chatBox2
        if (chatBoxId === 'chatBox2') {
            const aiIcon = document.createElement("img");
            aiIcon.src = "chatbot.png";
            aiIcon.alt = "Sagad Assistant";
            aiIcon.classList.add("ai-response-icon");
            aiContainer.appendChild(aiIcon);
        }

        const aiMessage = document.createElement("div");
        aiMessage.innerHTML = marked.parse(content);
        aiMessage.classList.add("ai-response-text");

        aiContainer.appendChild(aiMessage);
        chatBox.appendChild(aiContainer);
    } else {
        const messageElement = document.createElement("p");
        messageElement.textContent = content;
        messageElement.classList.add("user-message");
        chatBox.appendChild(messageElement);
    }

    chatBox.scrollTop = chatBox.scrollHeight;
}

// Initialize
window.onload = function() {
    // Default message for chat
    appendMessage("Hello! I'm your Sagad Assistant. I can help you with document analysis, general conversation, and explanations. How can I assist you today?", "ai", 'chatBox2');
    
    // Initialize textarea auto-resize
    const textarea = document.getElementById('userMessage');
    autoResizeTextarea(textarea);
    
    // Check server status
    fetch('http://localhost:5000/health')
    .then(response => response.json())
    .then(data => {
        if (data.status === 'healthy') {
            console.log('Server is running');
        }
    })
    .catch(error => {
        console.warn('Server not accessible:', error);
    });
};
</script>

<script src='https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.js'></script>
<!-- Vendor JS -->
<script src="../assets/js/vendor.bundle.js"></script>
<!-- Theme JS -->
<script src="../assets/js/theme.bundle.js"></script>

</body>
</html>
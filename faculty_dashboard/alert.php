
<style>
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

<!-- Custom Alert Modal -->
        <div id="customAlertOverlay" class="custom-alert-overlay">
            <div class="custom-alert-modal">
                <div class="custom-alert-header">
                    <div id="customAlertIcon" class="custom-alert-icon">
                        <span id="customAlertIconText">!</span>
                    </div>
                    <h4 id="customAlertTitle" class="custom-alert-title"></h4>
                </div>
                <div class="custom-alert-body">
                    <p id="customAlertMessage" class="custom-alert-message"></p>
                </div>
                <div class="custom-alert-footer">
                    <button id="customAlertBtn" class="custom-alert-btn" onclick="closeCustomAlert()">OK</button>
                </div>
            </div>
        </div>

        <!-- Custom Confirm Modal -->
        <div id="customConfirmOverlay" class="custom-alert-overlay">
            <div class="custom-alert-modal">
                <div class="custom-alert-header">
                    <div id="customConfirmIcon" class="custom-alert-icon warning">
                        <span>!</span>
                    </div>
                    <h4 id="customConfirmTitle" class="custom-alert-title">Confirm</h4>
                </div>
                <div class="custom-alert-body">
                    <p id="customConfirmMessage" class="custom-alert-message">Are you sure?</p>
                </div>
                <div class="custom-alert-footer">
                    <button class="custom-alert-btn btn-danger" onclick="handleConfirm(false)">No</button>
                    <button class="custom-alert-btn btn-success" onclick="handleConfirm(true)">Yes</button>
                </div>
            </div>
        </div>


<script>
function showCustomAlert(type, title, message) {
    var overlay = document.getElementById("customAlertOverlay");
    var icon = document.getElementById("customAlertIcon");
    var iconText = document.getElementById("customAlertIconText");
    var alertTitle = document.getElementById("customAlertTitle");
    var alertMessage = document.getElementById("customAlertMessage");

    // Reset classes
    icon.className = "custom-alert-icon";
    if (type === "success") icon.classList.add("success"), iconText.textContent = "✓";
    else if (type === "error") icon.classList.add("error"), iconText.textContent = "!";
    else if (type === "warning") icon.classList.add("warning"), iconText.textContent = "!";

    alertTitle.textContent = title;
    alertMessage.textContent = message;
    overlay.style.display = "block";
}
function closeCustomAlert() {
    document.getElementById("customAlertOverlay").style.display = "none";
}
</script>

<script>
let confirmCallback = null;

function showCustomConfirm(title, message, callback) {
    document.getElementById("customConfirmTitle").textContent = title;
    document.getElementById("customConfirmMessage").textContent = message;
    document.getElementById("customConfirmOverlay").style.display = "block";
    confirmCallback = callback;
}

function handleConfirm(confirmed) {
    document.getElementById("customConfirmOverlay").style.display = "none";
    if (typeof confirmCallback === 'function') {
        confirmCallback(confirmed);
        confirmCallback = null;
    }
}
</script>



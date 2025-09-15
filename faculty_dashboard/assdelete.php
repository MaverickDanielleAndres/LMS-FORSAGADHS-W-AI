<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc." />
    <!-- Favicon -->
    <link rel="shortcut icon" href="../assets/favicon/favicon.ico" type="image/x-icon" />
    <title>LMS by Titanslab</title>
    <style>
        body {
            background-color: #f9fbfd;
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

    <!-- Custom Alert JavaScript -->
    <script>
        function showCustomAlert(message, type = 'info', title = null, callback = null) {
            const overlay = document.getElementById('customAlertOverlay');
            const icon = document.getElementById('customAlertIcon');
            const iconText = document.getElementById('customAlertIconText');
            const titleElement = document.getElementById('customAlertTitle');
            const messageElement = document.getElementById('customAlertMessage');
            const btn = document.getElementById('customAlertBtn');

            // Set default titles based on type
            const defaultTitles = {
                'success': 'Success',
                'error': 'Error',
                'warning': 'Warning',
                'info': 'Information'
            };

            // Set default icons based on type
            const defaultIcons = {
                'success': '✓',
                'error': '✕',
                'warning': '!',
                'info': 'i'
            };

            // Configure alert appearance
            titleElement.textContent = title || defaultTitles[type];
            messageElement.textContent = message;
            iconText.textContent = defaultIcons[type];

            // Reset icon classes and add appropriate one
            icon.className = 'custom-alert-icon ' + type;

            // Reset button classes and add appropriate one
            btn.className = 'custom-alert-btn';
            if (type === 'success') {
                btn.classList.add('btn-success');
            } else if (type === 'error') {
                btn.classList.add('btn-danger');
            }

            // Store callback for later use
            window.customAlertCallback = callback;

            // Show modal
            overlay.style.display = 'block';

            // Focus on button for accessibility
            setTimeout(() => btn.focus(), 100);
        }

        function closeCustomAlert() {
            const overlay = document.getElementById('customAlertOverlay');
            overlay.style.display = 'none';

            // Execute callback if provided
            if (window.customAlertCallback && typeof window.customAlertCallback === 'function') {
                window.customAlertCallback();
                window.customAlertCallback = null;
            }
        }

        // Close modal when clicking outside
        document.getElementById('customAlertOverlay').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCustomAlert();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.getElementById('customAlertOverlay').style.display === 'block') {
                closeCustomAlert();
            }
        });
    </script>

    <?php
    session_start();
    if ($_SESSION['role'] != "faculty") {
        header("Location: ../index.php");
    } else {
        include_once("../config.php");
        $_SESSION["userrole"] = "Faculty";
        $assid = $_GET['assid'];
        $assid = mysqli_real_escape_string($conn, $assid);
        try {
            $qur = "DELETE FROM assignmentmaster WHERE AssignmentId = '$assid'";
            $res = mysqli_query($conn, $qur);
            if ($res) {
                echo "<script>showCustomAlert('Assignment Deleted Successfully!', 'success', 'Success', function() { window.location.href = 'assignment_list.php'; });</script>";
            } else {
                echo "<script>showCustomAlert('This Assignment Has Submissions, So Cannot Delete!', 'warning', 'Cannot Delete', function() { window.location.href = 'assignment_list.php'; });</script>";
            }
        } catch (Exception $e) {
            echo "<script>showCustomAlert('This Assignment Has Submissions, So Cannot Delete!', 'warning', 'Cannot Delete', function() { window.location.href = 'assignment_list.php'; });</script>";
        }
    }
    ?>
</body>

</html>
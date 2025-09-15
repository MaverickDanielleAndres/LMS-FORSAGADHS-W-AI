<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc." />
    <link rel="shortcut icon" href="../assets/favicon/favicon.ico" type="image/x-icon" />
    <style>
        body {
            background-color: #f9fbfd;
        }

        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 9999;
            display: none;
            justify-content: center;
            align-items: center;
        }

        .popup-container {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            max-width: 450px;
            width: 90%;
            padding: 0;
            transform: scale(0.8);
            transition: transform 0.3s ease;
            overflow: hidden;
        }

        .popup-overlay.show .popup-container {
            transform: scale(1);
        }

        .popup-header {
            padding: 20px 25px 15px;
            border-bottom: 1px solid #e5e5e5;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .popup-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
        }

        .popup-icon.success {
            background-color: #d4edda;
            color: #155724;
        }

        .popup-icon.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .popup-title {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
            color: #333;
        }

        .popup-body {
            padding: 20px 25px;
        }

        .popup-message {
            font-size: 16px;
            color: #555;
            line-height: 1.5;
            margin: 0;
        }

        .popup-footer {
            padding: 15px 25px 20px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .popup-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .popup-btn.primary {
            background-color: #27548A;
            color: white;
        }

        .popup-btn.primary:hover {
            background-color: #0d3b72ff;
        }
    </style>
</head>

<body>
    <?php
    session_start();
    if ($_SESSION['role'] != "faculty") {
        header("Location: ../index.php");
    } else {
        include_once("../config.php");
        $_SESSION["userrole"] = "Faculty";
        $assid = $_GET['actid'];
        $assid = mysqli_real_escape_string($conn, $assid);
        try {
            $qur = "DELETE FROM activitymaster WHERE ActivityId = '$assid'";
            $res = mysqli_query($conn, $qur);
            if ($res) {
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        showCustomPopup('Activity Deleted Successfully', 'success', 'Success', 'activity_list.php');
                    });
                </script>";
            } else {
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        showCustomPopup('This Activity Has Submissions, So Cannot Delete.', 'error', 'Error', 'activity_list.php');
                    });
                </script>";
            }
        } catch (Exception $e) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    showCustomPopup('This Activity Has Submissions, So Cannot Delete.', 'error', 'Error', 'activity_list.php');
                });
            </script>";
        }
    }
    ?>

    <!-- Custom Modal Popup -->
    <div id="customPopup" class="popup-overlay">
        <div class="popup-container">
            <div class="popup-header">
                <div id="popupIcon" class="popup-icon success">✓</div>
                <h3 id="popupTitle" class="popup-title">Success</h3>
            </div>
            <div class="popup-body">
                <p id="popupMessage" class="popup-message">Operation completed successfully!</p>
            </div>
            <div class="popup-footer">
                <button id="popupCloseBtn" class="popup-btn primary">OK</button>
            </div>
        </div>
    </div>

    <script>
        function showCustomPopup(message, type = 'success', title = null, redirectUrl = null) {
            const popup = document.getElementById('customPopup');
            const icon = document.getElementById('popupIcon');
            const titleEl = document.getElementById('popupTitle');
            const messageEl = document.getElementById('popupMessage');
            const closeBtn = document.getElementById('popupCloseBtn');

            messageEl.textContent = message;
            icon.className = 'popup-icon ' + type;

            switch (type) {
                case 'success':
                    icon.textContent = '✓';
                    titleEl.textContent = title || 'Success';
                    break;
                case 'error':
                    icon.textContent = '✕';
                    titleEl.textContent = title || 'Error';
                    break;
                case 'warning':
                    icon.textContent = '⚠';
                    titleEl.textContent = title || 'Warning';
                    break;
            }

            popup.style.display = 'flex';
            setTimeout(() => popup.classList.add('show'), 10);

            closeBtn.onclick = function() {
                hideCustomPopup();
                if (redirectUrl) {
                    window.location.href = redirectUrl;
                }
            };

            popup.onclick = function(e) {
                if (e.target === popup) {
                    hideCustomPopup();
                    if (redirectUrl) {
                        window.location.href = redirectUrl;
                    }
                }
            };

            if (redirectUrl) {
                setTimeout(() => {
                    hideCustomPopup();
                    window.location.href = redirectUrl;
                }, 2000);
            }
        }

        function hideCustomPopup() {
            const popup = document.getElementById('customPopup');
            popup.classList.remove('show');
            setTimeout(() => {
                popup.style.display = 'none';
            }, 300);
        }
    </script>
</body>

</html>
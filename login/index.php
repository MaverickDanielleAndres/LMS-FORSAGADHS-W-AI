
<?php


//This is the login folder index.php



session_start();
require_once("../config.php");

// Initialize login attempts tracking
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = array();
}

// Function to check if user is locked out
function isLockedOut($username) {
    if (!isset($_SESSION['login_attempts'][$username])) {
        return false;
    }
    
    $attempts = $_SESSION['login_attempts'][$username];
    if ($attempts['count'] >= 6) {
        $lockoutTime = $attempts['lockout_time'];
        if (time() - $lockoutTime < 180) { // 3 minutes = 180 seconds
            return true;
        } else {
            // Reset attempts after lockout period
            unset($_SESSION['login_attempts'][$username]);
            return false;
        }
    }
    return false;
}

// Function to get remaining lockout time
function getRemainingLockoutTime($username) {
    if (!isset($_SESSION['login_attempts'][$username])) {
        return 0;
    }
    
    $attempts = $_SESSION['login_attempts'][$username];
    if ($attempts['count'] >= 6) {
        $elapsed = time() - $attempts['lockout_time'];
        return max(0, 180 - $elapsed); // 180 seconds = 3 minutes
    }
    return 0;
}

// Function to record failed login attempt
function recordFailedAttempt($username) {
    if (!isset($_SESSION['login_attempts'][$username])) {
        $_SESSION['login_attempts'][$username] = array('count' => 0, 'lockout_time' => 0);
    }
    
    $_SESSION['login_attempts'][$username]['count']++;
    
    if ($_SESSION['login_attempts'][$username]['count'] >= 6) {
        $_SESSION['login_attempts'][$username]['lockout_time'] = time();
    }
}

// Function to reset login attempts on successful login
function resetLoginAttempts($username) {
    if (isset($_SESSION['login_attempts'][$username])) {
        unset($_SESSION['login_attempts'][$username]);
    }
}

// Function to verify password (handles both plain text and hashed passwords)
function verifyPassword($inputPassword, $storedPassword) {
    // If stored password is empty or null, return false
    if (empty($storedPassword)) {
        return false;
    }
    
    // First try password_verify for hashed passwords
    if (password_verify($inputPassword, $storedPassword)) {
        return true;
    }
    
    // If that fails, try direct comparison for plain text passwords
    return $inputPassword === $storedPassword;
}

// Handle form submission
$error_message = '';
$success_message = '';
$lockout_time = 0;

if (isset($_POST['login'])) {
    $na = strtoupper(trim($_POST['name']));
    $pass = trim($_POST['password']);
    $na2 = $_POST['SelectedType'];
    
    // Construct username based on user type
    $u = $na2 . mysqli_real_escape_string($conn, $na);
    
    // Check if user is locked out
    if (isLockedOut($u)) {
        $lockout_time = getRemainingLockoutTime($u);
        $error_message = "Account locked due to too many failed attempts. Please try again in " . gmdate("i:s", $lockout_time) . ".";
    } else {
        $login_success = false;
        $debug_info = ""; // For debugging
        
        if ($na2 == "IN") {
            $sql = "SELECT * FROM institutemaster WHERE InstituteUserName = ?";
            $stmt = mysqli_prepare($conn, $sql);
            
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $u);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                
                if ($row) {
                    if (verifyPassword($pass, $row['InstitutePassword'])) {
                        $login_success = true;
                        $_SESSION['id'] = $u;
                        $_SESSION['name'] = $row['InstituteId'];
                        $_SESSION['role'] = "Texas";
                        $_SESSION['login_time'] = time();
                        resetLoginAttempts($u);
                        $success_message = "Login successful! Redirecting to dashboard...";
                        echo "<script>
                            setTimeout(function() {
                                history.replaceState(null, null, location.href);
                                window.location.href='../institute_dashboard/';
                            }, 1500);
                        </script>";
                    }
                }
                mysqli_stmt_close($stmt);
            }
            
            if (!$login_success) {
                $_POST['SelectedLoginType'] = "INSTITUTE";
            }
            
        } else if ($na2 == "FA") {
            $sql = "SELECT * FROM facultymaster WHERE FacultyUserName = ?";
            $stmt = mysqli_prepare($conn, $sql);
            
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $u);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                
                if ($row) {
                    if (verifyPassword($pass, $row['FacultyPassword'])) {
                        $login_success = true;
                        $_SESSION['fid'] = $row['FacultyId'];
                        $_SESSION['id'] = $u;
                        $_SESSION['role'] = "faculty";
                        $_SESSION['login_time'] = time();
                        resetLoginAttempts($u);
                        $success_message = "Login successful! Redirecting to dashboard...";
                        echo "<script>
                            setTimeout(function() {
                                history.replaceState(null, null, location.href);
                                window.location.href='../faculty_dashboard/';
                            }, 1500);
                        </script>";
                    }
                }
                mysqli_stmt_close($stmt);
            }
            
            if (!$login_success) {
                $_POST['SelectedLoginType'] = "FACULTY";
            }
            
        } else if ($na2 == "ST") {
            $sql = "SELECT * FROM studentmaster WHERE StudentUserName = ?";
            $stmt = mysqli_prepare($conn, $sql);
            
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $u);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                
                // Debug: Check if user exists
                if ($row) {
                    // Debug: Check password verification
                    if (verifyPassword($pass, $row['StudentPassword'])) {
                        $login_success = true;
                       $_SESSION['cred'] = $u . "_" . $pass;
                        $_SESSION['student_id'] = $row['StudentId'];
                        $_SESSION['id'] = $u;
                        $_SESSION['role'] = "student";
                        $_SESSION['login_time'] = time();
                        resetLoginAttempts($u);
                        $success_message = "Login successful! Redirecting to dashboard...";
                        echo "<script>
                            setTimeout(function() {
                                history.replaceState(null, null, location.href);
                                window.location.href='../student_dashboard/';
                            }, 1500);
                        </script>";
                    } else {
                        $debug_info = "Password verification failed for user: " . $u;
                    }
                } else {
                    $debug_info = "No user found with username: " . $u;
                }
                mysqli_stmt_close($stmt);
            } else {
                $debug_info = "Database query failed for student login";
            }
            
            if (!$login_success) {
                $_POST['SelectedLoginType'] = "STUDENT";
            }
        }
        
        if (!$login_success) {
            recordFailedAttempt($u);
            $remaining_attempts = 6 - $_SESSION['login_attempts'][$u]['count'];
            
            if ($remaining_attempts > 0) {
                $error_message = "Incorrect Username or Password! " . $remaining_attempts . " attempts remaining.";
                // Uncomment the line below for debugging
                // $error_message .= " Debug: " . $debug_info;
            } else {
                $lockout_time = getRemainingLockoutTime($u);
                $error_message = "Account locked due to too many failed attempts. Please try again in " . gmdate("i:s", $lockout_time) . ".";
            }
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="../assets/favicon/favicon.png" type="image/x-icon" />
    <link rel="stylesheet" href="../assets/css/libs.bundle.css" />
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link rel="stylesheet" href="../assets/css/theme-dark.bundle.css" />
    <title>Sagad High School LMS Login</title>
    
    <style>
        body::before {
            display: none;
        }
        
        input:invalid {
            border-color: #dc3545;
        }
        
        .modern-alert {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1050;
            max-width: 400px;
            width: 90%;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            border: none;
            backdrop-filter: blur(10px);
            animation: slideIn 0.3s ease-out;
        }
        
        .modern-alert.alert-danger {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.9), rgba(220, 53, 69, 0.8));
            color: white;
        }
        
        .modern-alert.alert-success {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.9), rgba(40, 167, 69, 0.8));
            color: white;
        }
        
        .modern-alert.alert-warning {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.9), rgba(255, 193, 7, 0.8));
            color: #212529;
        }
        
        @keyframes slideIn {
            from {
                transform: translate(-50%, -50%) scale(0.8);
                opacity: 0;
            }
            to {
                transform: translate(-50%, -50%) scale(1);
                opacity: 1;
            }
        }
        
        @keyframes slideOut {
            from {
                transform: translate(-50%, -50%) scale(1);
                opacity: 1;
            }
            to {
                transform: translate(-50%, -50%) scale(0.8);
                opacity: 0;
            }
        }
        
        .lockout-timer {
            font-family: 'Courier New', monospace;
            font-size: 1.2em;
            font-weight: bold;
            color: #dc3545;
        }
        
        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .back-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }
        
        .sign-in-container {
            background: rgba(255, 255, 255, 0);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(1px);
        }
        
        .indexbg {
                background-size: cover;       /* Cover entire element */
                background-position: center;  /* Center the image */
                background-repeat: no-repeat; /* Don't repeat */
        }
        
        .form-control:disabled {
            background-color: rgba(255, 255, 255, 0.1);
            opacity: 0.6;
        }
        
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .alert-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1049;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-close {
            border: none;
            color: white;
            background: none;
        }
        .indexbg {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: -1;
    filter: brightness(0.6) blur(3px); /* Darkens and softens background */
}

    </style>
</head>

<body class="border-top border-top-2 border-primary">
    <img class="indexbg" src="sagadbg.jpg" alt="">

    <!-- Back Button -->
    <?php if (isset($_POST['SelectedLoginType'])) { ?>
        <a href="#" class="back-btn" onclick="goBackToSelection()">
            <i class="uil uil-arrow-left"></i> Back to Selection
        </a>
    <?php } ?>

    <!-- Modern Alert with Overlay -->
    <?php if (!empty($error_message) || !empty($success_message)) { ?>
        <div class="alert-overlay" id="alertOverlay">
            <div class="modern-alert alert <?php 
                if (!empty($success_message)) {
                    echo 'alert-success';
                } else {
                    echo ($lockout_time > 0) ? 'alert-warning' : 'alert-danger';
                }
            ?>" id="modernAlert">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <strong><?php echo !empty($success_message) ? $success_message : $error_message; ?></strong>
                        <?php if ($lockout_time > 0) { ?>
                            <div class="lockout-timer mt-2" id="lockoutTimer">
                                Time remaining: <span id="countdown"><?php echo gmdate("i:s", $lockout_time); ?></span>
                            </div>
                        <?php } ?>
                    </div>
                    <?php if (empty($success_message)) { ?>
                        <button type="button" class="btn-close" onclick="closeAlert()">X</button>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>

    <div class="d-flex align-items-center my-auto" style="height: 100vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-5 col-xl-4 my-5">
                    <div class="sign-in-container">
                        <h1 class="display-3 text-center mb-3">
                            Sign in
                        </h1>
                        <p class="text-muted text-center mb-5">
                            access to
                            <?php if (isset($_POST['SelectedLoginType'])) { ?>
                                <span class="font-weight-bold text-secondary"><?php echo $_POST['SelectedLoginType']; ?></span>
                            <?php } else { ?>
                                <span>your</span>
                            <?php } ?>
                            dashboard.
                        </p>
                        
                        <?php if (!isset($_POST['SelectedLoginType'])) { ?>
                            <form method="POST" id="selectionForm">
                                <div id="loginType" class="d-flex justify-content-between">
                                    <div class="mx-2">
                                        <button type="submit" class="card border shadow bg-body rounded text-secondary" value="INSTITUTE" name="SelectedLoginType">
                                            <img src="../assets/img/admin.png" class="card-img rounded mx-auto d-block avatar avatar-xl">
                                            <div class="card-body">
                                                <h5 class="card-title">INSTITUTE</h5>
                                            </div>
                                        </button>
                                    </div>
                                    <div class="mx-2">
                                        <button type="submit" class="card border shadow bg-body rounded text-secondary" value="FACULTY" name="SelectedLoginType">
                                            <img src="../assets/img/faculty.png" class="card-img rounded mx-auto d-block avatar avatar-xl">
                                            <div class="card-body">
                                                <h5 class="card-title">FACULTY</h5>
                                            </div>
                                        </button>
                                    </div>
                                    <div class="mx-2">
                                        <button type="submit" class="card border shadow bg-body rounded text-secondary" value="STUDENT" name="SelectedLoginType">
                                            <img src="../assets/img/student.png" class="card-img rounded mx-auto d-block avatar avatar-xl">
                                            <div class="card-body">
                                                <h5 class="card-title">STUDENT</h5>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        <?php } else { ?>
                            <form method="POST" autocomplete="off" id="loginForm">
                                <div class="form-group">
                                    <label class="form-label">Username</label>
                                    <input type="hidden" name="SelectedType" value="<?php echo substr($_POST['SelectedLoginType'], 0, 2); ?>">
                                    <input type="text" class="form-control" placeholder="Username" name="name" 
                                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" 
                                           <?php echo ($lockout_time > 0) ? 'disabled' : 'required'; ?>>
                                </div>
                                <label class="form-label">Password</label>
                                <div class="col-12 logo_outer">
                                    <div class="input-group mb-4">
                                        <input name="password" id="password" type="password" class="input form-control" 
                                               placeholder="Password" <?php echo ($lockout_time > 0) ? 'disabled' : 'required'; ?> 
                                               aria-label="password" aria-describedby="basic-addon1" />
                                        <div class="input-group-append">
                                            <span class="input-group-text" style="border-radius: 1px 5px 5px 1px;" onclick="password_show_hide();">
                                                <i class="fe uil-eye-slash" id="show_eye"></i>
                                                <i class="fe uil-eye d-none" id="hide_eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <input type="submit" class="btn btn-lg btn-block btn-primary mb-3" name="login" value="Login" 
                                       <?php echo ($lockout_time > 0) ? 'disabled' : ''; ?> id="loginBtn">
                            </form>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/vendor.bundle.js"></script>
    <script src="../assets/js/theme.bundle.js"></script>
    
    <script>
        // Prevent back/forward navigation
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
        };

        // Disable right-click context menu
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        // Disable F12, Ctrl+Shift+I, Ctrl+U
        document.addEventListener('keydown', function(e) {
            if (e.key === 'F12' || 
                (e.ctrlKey && e.shiftKey && e.key === 'I') ||
                (e.ctrlKey && e.key === 'u')) {
                e.preventDefault();
            }
        });

        function password_show_hide() {
            const x = document.getElementById("password");
            var show_eye = document.getElementById("show_eye");
            var hide_eye = document.getElementById("hide_eye");
            
            if (x.type === "password") {
                x.type = "text";
                show_eye.style.display = "none";
                hide_eye.style.display = "block";
            } else {
                x.type = "password";
                show_eye.style.display = "block";
                hide_eye.style.display = "none";
            }
        }

        function closeAlert() {
            const alert = document.getElementById('modernAlert');
            const overlay = document.getElementById('alertOverlay');
            if (alert && overlay) {
                alert.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(() => {
                    overlay.remove();
                }, 300);
            }
        }

        function goBackToSelection() {
            // Create a form to submit without the SelectedLoginType
            const form = document.createElement('form');
            form.method = 'POST';
            form.style.display = 'none';
            document.body.appendChild(form);
            form.submit();
        }

        // Auto-hide alert after 5 seconds (except success messages)
        <?php if (empty($success_message)) { ?>
        setTimeout(function() {
            closeAlert();
        }, 5000);
        <?php } ?>

        // Countdown timer for lockout
        <?php if ($lockout_time > 0) { ?>
            let remainingTime = <?php echo $lockout_time; ?>;
            const countdownElement = document.getElementById('countdown');
            const loginBtn = document.getElementById('loginBtn');
            const usernameInput = document.querySelector('input[name="name"]');
            const passwordInput = document.getElementById('password');
            
            const timer = setInterval(function() {
                remainingTime--;
                
                if (remainingTime <= 0) {
                    clearInterval(timer);
                    location.reload();
                } else {
                    const minutes = Math.floor(remainingTime / 60);
                    const seconds = remainingTime % 60;
                    countdownElement.textContent = 
                        minutes.toString().padStart(2, '0') + ':' + 
                        seconds.toString().padStart(2, '0');
                }
            }, 1000);
        <?php } ?>

        // Prevent "Leave site" warning by clearing form data on page load
        window.addEventListener('load', function() {
            // Clear all form data to prevent beforeunload warning
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.reset();
            });
        });

        // Override the beforeunload event to prevent warnings
        window.addEventListener('beforeunload', function (e) {
            // Don't show warning - just allow navigation
            delete e['returnValue'];
        });
    </script>
</body>
</html>

<?php
include_once 'context.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('../head.php'); ?>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms and Privacy</title>
    <style>

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
				padding: .5rem 1rem;
				background-color: #0d3b72ff;
				border: none;
				border-radius: 3px;
				font-weight: 400;
				color: white;
				text-decoration: none;
			}

        html,
        body {
            height: 100%;
            margin: 0;
            background-color: rgb(243, 243, 243);
            /* Match with .main-content */
        }

        .main-content {
            min-height: 100vh;
            background-color: rgb(243, 243, 243);
            padding-top: 20px;
            /* Optional space from top */
        }

        .terms-privacy-panel {
            max-width: 600px;
            margin: 0 auto 40px auto;
            background-color: #ffffffff;
            border: 1px solid #b08968;
            border-radius: 12px;
            padding: 35px;
            box-shadow: 0 5px 8px rgba(0, 0, 0, 0.15);
            text-align: justify;
        }

        .terms-privacy-panel h3 {
            color: #9a6b50;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .terms-privacy-panel p {
            color: #5a4a3b;
            line-height: 1.6;
        }

        .submit-btn {
            background-color: #9a6b50;
            color: white;
            border: none;
        }

        .submit-btn:hover {
            background-color: #b08968;
        }

        .form-check-label {
            color: #5a4a3b;
        }

        .custom-alert {
            background-color: #f8f9fa;
            border: 1px solid #b08968;
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 5px 8px rgba(0, 0, 0, 0.15);
            color: #5a4a3b;
            text-align: center;
            font-weight: bold;
            display: none;
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
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

        .card {
            z-index: 10;
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.2);
            border-radius: 0.75rem;
            background-color: white;
        }
    </style>
</head>

<body>
    <div class="main-content">
        <?php $nav_role = "Terms and Condition"; ?>
        <?php include_once('nav.php'); ?>

        <div class="terms-privacy-panel position-relative">

            <h3 style="position: relative;" id="terms-and-privacy">
                Terms and Conditions
                <a style="font-size: 18px; position:absolute; top: 0; right: 0;" class="btn-link btn-outline" onclick="history.back()">
                    <i class="fe uil-angle-double-left" style="font-size: 18px;"></i>Back
                </a>
            </h3>

            <p>- By using this Learning Management System (LMS), you agree to the terms and conditions outlined below. This system is designed exclusively for educational purposes. Unauthorized access or misuse of this platform is strictly prohibited. Personal information, such as grades and user profiles, will only be accessed by authorized individuals such as teachers and students. Users are responsible for maintaining the confidentiality of their login credentials.</p>
            <p>- The LMS is subject to periodic updates to improve functionality and security. All users must comply with these updates to ensure optimal operation and security of the system. Misuse, unauthorized access, or tampering with system data is a violation of our terms.</p>
            <p>- Users must not share their login credentials with others or use another user's credentials. The system is monitored for unauthorized activity, and violations may result in suspension or termination of access.</p>
            <p>- You agree to abide by the academic integrity policies of your institution while using this LMS. Plagiarism or misuse of content from the LMS without proper attribution is strictly prohibited.</p>
            <hr>
            <h3>Privacy Policy</h3>
            <p>- Your privacy is our priority. The LMS complies with the Data Privacy Act to ensure the secure treatment of personal data. Information collected is used solely for educational purposes and will not be shared without user consent. Data is encrypted and regularly updated to protect against potential breaches.</p>
            <p>- Accessibility is a key principle of our design. The system supports keyboard navigation, screen readers, and contrast settings to ensure inclusivity for all students, including those with disabilities.</p>
            <p>- Our AI-powered features, such as the personalized reviewer, are designed to be fair, transparent, and free from bias. Users are informed about how AI is utilized, ensuring trust in the system's integrity.</p>
            <p>- We do not sell or share personal information with third parties for marketing purposes. User data is handled with strict confidentiality and used solely to enhance educational experiences.</p>

            <form id="termsForm">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="agreeTerms" id="agreeTerms">
                    <label class="form-check-label" for="agreeTerms">
                        I agree to the Terms and Privacy Policy.
                    </label>
                </div>
                <button type="button" class="btn submit-btn mt-3" onclick="submitForm();">Submit</button>
            </form>

            <div id="customAlert" class="custom-alert"></div>
        </div>
    </div>

    <script>
        function submitForm() {
            const agreeTerms = document.getElementById('agreeTerms').checked;
            const customAlert = document.getElementById('customAlert');

            if (agreeTerms) {
                customAlert.innerHTML = "Thank you for agreeing to the Terms and Privacy Policy.";
                customAlert.style.backgroundColor = "#d4edda";
                customAlert.style.color = "#155724";
                customAlert.style.display = "block";
            } else {
                customAlert.innerHTML = "You must agree to the Terms and Privacy Policy to proceed.";
                customAlert.style.backgroundColor = "#f8d7da";
                customAlert.style.color = "#721c24";
                customAlert.style.display = "block";
            }

            setTimeout(() => {
                customAlert.style.display = "none";
            }, 3000);
        }

        window.onload = function() {
            document.getElementById('customAlert').style.display = 'none';
        };
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
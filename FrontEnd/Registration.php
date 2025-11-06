<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registration</title>

    <!-- CSS Files -->
    <link rel="stylesheet" href="./assets/css/reset.css" />
    <link rel="stylesheet" href="./assets/css/Registration.css" />
    <link rel="stylesheet" href="./assets/css/fonts.css" />
    <link rel="stylesheet" href="./assets/css/notifications.css" />

  <!-- JS -->
  <script src="./assets/js/loader.js"></script>
  <script src="./assets/js/registration-validation.js" defer></script>
  <script src="./assets/js/registration.js" defer></script>
  <script src="./assets/js/notifications.js" defer></script>
  <script src="./assets/js/terms-conditions.js" defer></script>
</head>

<body>
    <?php
        include_once './pages/loader.php';
    ?>
    <?php
        include_once './pages/notifications.php';
    ?>

  <div class="login-container">
    <!-- LEFT ILLUSTRATION -->
   <div id="img-container">
      <img src="./assets/imgs/users-login.png" id="image" />     
    </div>

    <!-- RIGHT LOGIN FORM -->

    <!-- Registration Form -->
    <div class="container">
        <form id="registration-form" action="../BackEnd/api/postRegistrationForm.php" method="post">
            <div class="user-details">
                <div class="page-title">
                    <h3>Welcome!</h3>
                    <p class="subtitle">Sign in to your Account</p>
                </div>

                <!-- Guardian First Name -->
                <div class="input-box">
                    <input type="text" name="Guardian-First-Name" id="guardian-first-name" placeholder=" " required />
                    <span class="details">Enrollee's Guardian First Name</span>
                </div>

                <!-- Guardian Middle Name -->
                <div class="input-box">
                    <input type="text" name="Guardian-Middle-Name" id="guardian-middle-name" placeholder=" " required />
                    <span class="details">Enrollee's Guardian Middle Name</span>
                </div>

                <!-- Guardian Last Name -->
                <div class="input-box">
                    <input type="text" name="Guardian-Last-Name" id="guardian-last-name" placeholder=" " required />
                    <span class="details">Enrollee's Guardian Last Name</span>
                </div>

                <!-- Guardian Contact Number -->
                <div class="input-box">
                    <input type="text" name="Contact-Number" id="contact-number" placeholder=" " maxlength="11" pattern="[0-9]{11}" required />
                    <span class="details">Enrollee's Guardian Contact Number</span>
                </div>

                <!-- Terms and Conditions Section -->
                <div class="terms-conditions-section">
                    <div class="terms-header">
                        <p class="terms-instruction">Please read the Privacy Policy and Terms & Conditions</p>
                        <button type="button" id="open-terms-btn" class="open-terms-btn">Read Terms & Conditions</button>
                    </div>
                    
                    <div class="terms-agreement">
                        <input type="checkbox" name="terms-acceptance" id="terms-acceptance" disabled required />
                        <label for="terms-acceptance" class="terms-label" id="terms-label">
                            I have read and agree to the Terms & Conditions and Privacy Policy
                            <span class="required">*</span>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="button">
                <button type="submit" name="submit" class="btn submit-btn">Register</button>
                </div>
            </div>
            <!-- Sign In Link -->
            <p>
              Already have an account?
              <a href="Login.php" class="signin-link" style="text-decoration: none;">Sign In.</a>
            </p>
        </form>
    </div>
  </div>

  <!-- Terms and Conditions Modal -->
  <div id="terms-modal" class="terms-modal">
    <div class="terms-modal-content">
      <div class="terms-modal-header">
        <h2>Privacy Policy and Terms & Conditions</h2>
        <span class="close-modal" id="close-terms-modal">&times;</span>
      </div>
      <div class="terms-modal-body" id="terms-modal-body">
        <div class="scroll-indicator">Scroll down to continue</div>
        
        <h3>Privacy Policy</h3>
        <p>This Privacy Policy describes how your personal information is collected, used, and shared when you use our Student Information System.</p>
        
        <h4>Information We Collect</h4>
        <p>We collect the following information:</p>
        <ul>
          <li>Guardian's full name (First, Middle, Last)</li>
          <li>Guardian's contact number</li>
          <li>Student enrollment information</li>
          <li>Academic records and documents</li>
          <li>Personal information as required by DepEd regulations</li>
        </ul>

        <h4>How We Use Your Information</h4>
        <p>We use the information we collect to:</p>
        <ul>
          <li>Process student enrollment applications</li>
          <li>Maintain accurate student records</li>
          <li>Communicate important information regarding enrollment and academic matters</li>
          <li>Comply with DepEd requirements and regulations</li>
          <li>Improve our services and system functionality</li>
        </ul>

        <h4>Data Security</h4>
        <p>We implement appropriate security measures to protect your personal information from unauthorized access, alteration, disclosure, or destruction.</p>

        <h4>Data Retention</h4>
        <p>We retain your information as long as necessary to fulfill the purposes outlined in this privacy policy and in compliance with DepEd record-keeping requirements.</p>

        <h3>Terms & Conditions</h3>
        
        <h4>Acceptance of Terms</h4>
        <p>By registering and using this Student Information System, you agree to be bound by these Terms and Conditions.</p>

        <h4>Registration Requirements</h4>
        <p>You must provide accurate and complete information during registration. Providing false information may result in rejection or cancellation of enrollment.</p>

        <h4>User Responsibilities</h4>
        <ul>
          <li>Maintain confidentiality of your account credentials</li>
          <li>Provide accurate and truthful information</li>
          <li>Update information promptly when changes occur</li>
          <li>Use the system only for its intended purpose</li>
          <li>Comply with all applicable laws and DepEd regulations</li>
        </ul>

        <h4>System Usage</h4>
        <p>The system is provided for enrollment and student information management purposes. Misuse of the system may result in account suspension or termination.</p>

        <h4>Limitation of Liability</h4>
        <p>The school and system administrators shall not be liable for any indirect, incidental, or consequential damages arising from the use of this system.</p>

        <h4>Modifications</h4>
        <p>We reserve the right to modify these terms and conditions at any time. Continued use of the system constitutes acceptance of modified terms.</p>

        <h4>Contact Information</h4>
        <p>If you have questions about this Privacy Policy or Terms & Conditions, please contact the school administration.</p>

        <p class="end-marker"><strong>End of Terms & Conditions - You may now accept the agreement</strong></p>
      </div>
      <div class="terms-modal-footer">
        <button type="button" id="close-terms-btn" class="btn-close-terms">Close</button>
      </div>
    </div>
  </div>

</body>
</html>

<?php
require 'global.php';
session_start();

// Generate CSRF token if not exists
$csrfToken = $settings->generateCsrfToken();

// If user is already logged in, redirect
if (isset($_SESSION['user_id']) && isset($_SESSION['active'])) {
    header("Location: ".$urlval."pages/add-money/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Login</title>
  
  <!-- FontAwesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  
  <style>
    /* ===== Modern CSS Reset ===== */
    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    
    /* ===== Variables ===== */
    :root {
      --primary-color: #4361ee;
      --primary-hover: #3a56d4;
      --error-color: #ef476f;
      --success-color: #06d6a0;
      --text-color: #2b2d42;
      --text-light: #8d99ae;
      --bg-color: #f8f9fa;
      --card-bg: #ffffff;
      --border-radius: 8px;
      --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      --transition: all 0.3s ease;
    }
    
    /* ===== Base Styles ===== */
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: var(--bg-color);
      color: var(--text-color);
      line-height: 1.6;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
    }
    
    /* ===== Login Container ===== */
    .login-container {
      width: 100%;
      max-width: 420px;
      background: var(--card-bg);
      padding: 40px;
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow);
      text-align: center;
      transition: var(--transition);
    }
    .login-container:hover {
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
    }
    
    /* ===== Logo/Icon ===== */
    .login-logo {
      width: 80px;
      height: 80px;
      background-color: var(--primary-color);
      color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 25px;
      font-size: 2rem;
    }
    
    /* ===== Headings ===== */
    h2 {
      color: var(--text-color);
      margin-bottom: 10px;
      font-size: 1.8rem;
      font-weight: 600;
    }
    .subtitle {
      color: var(--text-light);
      margin-bottom: 30px;
      font-size: 0.95rem;
    }
    
    /* ===== Form Elements ===== */
    .form-group {
      margin-bottom: 20px;
      text-align: left;
    }
    label {
      font-weight: 500;
      color: var(--text-color);
      display: block;
      margin-bottom: 8px;
      font-size: 0.95rem;
    }
    .input-group {
      position: relative;
    }
    .input-icon {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-light);
    }
    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 14px 14px 14px 45px;
      font-size: 1rem;
      border-radius: var(--border-radius);
      border: 1px solid #e0e0e0;
      background-color: #f9f9f9;
      transition: var(--transition);
    }
    input[type="text"]:focus,
    input[type="password"]:focus {
      outline: none;
      border-color: var(--primary-color);
      background-color: #fff;
      box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
    }
    
    /* ===== Captcha Row ===== */
    .captcha-row {
      margin: 20px 0;
      text-align: left;
    }
    .captcha-image {
      width: 200px;
      height: auto;
      display: block;
      margin-bottom: 8px;
    }
    #refresh-captcha {
      background: var(--primary-color);
      color: #fff;
      border: none;
      padding: 8px 12px;
      border-radius: var(--border-radius);
      cursor: pointer;
      margin-top: 4px;
    }
    #refresh-captcha:hover {
      background: var(--primary-hover);
    }
    
    /* ===== Buttons ===== */
    .sub_btn {
      width: 100%;
      padding: 14px;
      font-size: 1rem;
      font-weight: 600;
      color: white;
      background-color: var(--primary-color);
      border: none;
      border-radius: var(--border-radius);
      cursor: pointer;
      transition: var(--transition);
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 8px;
      margin-top: 20px;
    }
    .sub_btn:hover {
      background-color: var(--primary-hover);
      transform: translateY(-2px);
    }
    .sub_btn:active {
      transform: translateY(0);
    }
    
    /* ===== Error Container (for AJAX errors) ===== */
    .error-container {
      display: none;
      margin-bottom: 20px;
      font-size: 0.95rem;
      color: var(--error-color);
      background-color: rgba(239, 71, 111, 0.1);
      border: 1px solid rgba(239, 71, 111, 0.3);
      padding: 12px;
      border-radius: var(--border-radius);
      text-align: left;
    }
    
    /* ===== Countdown Timer ===== */
    #countdown-timer {
      font-size: 1.2em;
      color: #e74c3c;
      margin-top: 10px;
      display: none;
    }
    
    /* ===== Popup ===== */
    .popup-modal {
      display: none;
      position: fixed;
      z-index: 9999;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.4);
    }
    .popup-content {
      background-color: #fff;
      margin: 15% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 80%;
      max-width: 500px;
      position: relative;
      border-radius: var(--border-radius);
    }
    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
    }
    .close:hover {
      color: #000;
    }
    
    /* ===== Link ===== */
    a {
      display: inline-block;
      margin-top: 12px;
      color: var(--primary-color);
      text-decoration: none;
      font-size: 0.9rem;
    }
    a:hover {
      text-decoration: underline;
    }
    
    /* ===== Responsive Adjustments ===== */
    @media (max-width: 600px) {
      .login-container {
        padding: 30px 20px;
      }
      h2 {
        font-size: 1.5rem;
      }
      .login-logo {
        width: 70px;
        height: 70px;
        font-size: 1.7rem;
      }
    }
    
    /* ===== Password Toggle Icon ===== */
    .password-toggle {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-light);
      cursor: pointer;
      transition: var(--transition);
    }
    .password-toggle:hover {
      color: var(--primary-color);
    }
  </style>
</head>
<body>

<div class="login-container">
    <!-- Optional Logo -->
    <div class="login-logo">
        <i class="fas fa-user"></i>
    </div>
    
    <h2>User Login</h2>
    <p class="subtitle">Enter your credentials to access your account</p>
    
    <!-- Error container for AJAX-based errors -->
    <div id="error-container" class="error-container"></div>
    
    <!-- Countdown timer (shown when user must wait) -->
    <div id="countdown-timer"></div>
    
    <!-- AJAX Login Form -->
    <form id="login-form">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">

      <!-- Username Field -->
      <div class="form-group">
        <label for="username">Username</label>
        <div class="input-group">
          <i class="input-icon fas fa-user"></i>
          <input type="text" name="username" id="username" required placeholder="Enter your username">
        </div>
      </div>
      
      <!-- Password Field -->
      <div class="form-group">
        <label for="password">Password</label>
        <div class="input-group">
          <i class="input-icon fas fa-lock"></i>
          <input type="password" name="password" id="password" required placeholder="Enter your password">
          <!-- Password Toggle Icon -->
          <i class="password-toggle fas fa-eye" id="togglePassword"></i>
        </div>
      </div>
      
      <!-- Captcha Row -->
      <div class="captcha-row">
        <label for="captcha">Enter CAPTCHA:</label>
        <img src="captcha.php" alt="CAPTCHA" class="captcha-image" id="captcha-image">
        <button type="button" id="refresh-captcha">Refresh</button>
      </div>
      <input type="text" name="captcha" id="captcha" required placeholder="Type the code shown above">
      
      <!-- Submit Button -->
      <button class="sub_btn" type="submit">
        <i class="fas fa-sign-in-alt"></i> Login
      </button>
      
      <!-- Register Link -->
      <a href="register.php">Don't have an account? Register here</a>
    </form>
</div>

<!-- Popup Modal for messages (inactive account, errors, etc.) -->
<div id="rules-popup" class="popup-modal">
  <div class="popup-content">
    <span class="close" onclick="closeRulesPopup()">
      <i class="fas fa-times"></i>
    </span>
    <p class="message"></p>
  </div>
</div>

<script>
  // Password Toggle
  document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = this;
    
    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      toggleIcon.classList.remove('fa-eye');
      toggleIcon.classList.add('fa-eye-slash');
    } else {
      passwordInput.type = 'password';
      toggleIcon.classList.remove('fa-eye-slash');
      toggleIcon.classList.add('fa-eye');
    }
  });

  // Refresh Captcha
  document.getElementById('refresh-captcha').addEventListener('click', function() {
    const captchaImg = document.getElementById('captcha-image');
    captchaImg.src = 'captcha.php?' + Date.now();
  });

  // jQuery logic for AJAX form submission
  $(document).ready(function() {
    const form = $('#login-form');
    const errorContainer = $('#error-container');
    const countdownTimer = $('#countdown-timer');
    
    form.submit(function(event) {
      event.preventDefault();
      
      $.ajax({
        url: '../shop3/ajax/login.php', // Adjust to your actual AJAX endpoint
        method: 'POST',
        data: form.serialize(),
        dataType: 'json',
        success: function(response) {
          if (response.status === 'success') {
            // Redirect on success
            window.location.href = response.redirect;
          } else {
            // Refresh CAPTCHA on error
            $('#captcha-image').attr('src', 'captcha.php?' + Date.now());
            
            if (response.active === 0) {
              showRulesPopup(
                "<p>Your account is inactive. To activate your account, please top up your balance with at least $20.</p>" +
                "<p>Attention: Accounts that remain inactive for more than 15 days will be automatically deleted.</p>"
              );
              setTimeout(function() {
                window.location.reload();
              }, response.delay * 1000);
            } else {
              // If there's a remaining_time, user must wait
              if (response.remaining_time) {
                startTimer(response.remaining_time);
                disableForm();
              }
              // Display any error messages in a popup
              const message = response.errors.join('<br>');
              showRulesPopup(message);
            }
          }
        },
        error: function() {
          showRulesPopup('An error occurred. Please try again.');
          // Refresh CAPTCHA on AJAX error
          $('#captcha-image').attr('src', 'captcha.php?' + Date.now());
        }
      });
    });
    
    function startTimer(duration) {
      let timer = duration;
      countdownTimer.show();
      countdownTimer.text("Please wait " + timer + " seconds before retrying.");
      const interval = setInterval(function() {
        timer--;
        countdownTimer.text("Please wait " + timer + " seconds before retrying.");
        if (timer <= 0) {
          clearInterval(interval);
          countdownTimer.hide();
          enableForm();
        }
      }, 1000);
    }

    function disableForm() {
      form.find('input, button').prop('disabled', true);
    }

    function enableForm() {
      form.find('input, button').prop('disabled', false);
    }

    function showRulesPopup(message) {
      const popup = document.getElementById('rules-popup');
      const messageContainer = popup.querySelector('.message');
      messageContainer.innerHTML = message;
      popup.style.display = 'block';
    }
  });

  function closeRulesPopup() {
    document.getElementById('rules-popup').style.display = 'none';
  }
</script>

</body>
</html>

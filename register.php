<?php
require 'config.php';

$errors = [];
$accountCreated = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);
    $contactOption = $_POST['contact_option'];
    $contactValue = trim($_POST['contact_value']);
    $encodedSecretCode = trim($_POST['secret_code']);
    $secretCode = base64_encode(base64_encode($encodedSecretCode));
    $captcha = $_POST['captcha'];

    session_start();
    if (strlen($username) < 6) {
        $errors[] = "Username must be at least 6 characters.";
    }

    // Validate based on selected contact option
    if ($contactOption === "Jabber") {
        $jabber = $contactValue;
        $telegram = null;
    } else {
        $jabber = null;
        $telegram = $contactValue;
    }
    
    if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $password)) {
        $errors[] = "Password must be at least 6 characters, contain an uppercase letter, a digit, and a special character.";
    }
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match. Please enter the same password.";
    }
    if ($captcha !== $_SESSION['captcha_code']) {
        $errors[] = "Invalid CAPTCHA.";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, jabber, telegram, secret_code, seller_percentage) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$username, $hashed_password, !empty($jabber) ? $jabber : null, !empty($telegram) ? $telegram : null, $secretCode, 0]);
            $accountCreated = true;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errors[] = "The username is already taken. Please choose a different one.";
            } else {
                $errors[] = "Failed to register. Please try again later.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <!-- FontAwesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* Modern CSS Reset */
    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    


    .popup-modal {
  display: none; /* Initially hidden, will be set to block when success */
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
  text-align: center;
}

.popup-content h3 {
  margin-bottom: 10px;
  color: var(--primary-color);
}

.popup-content p {
  font-size: 16px;
  color: var(--text-color);
}

.popup-content .close {
  position: absolute;
  top: 10px;
  right: 15px;
  font-size: 24px;
  color: #aaa;
  cursor: pointer;
  transition: color 0.3s ease;
}

.popup-content .close:hover {
  color: #000;
}

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
    
    /* Form Elements */
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
    
    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 14px;
      font-size: 1rem;
      border-radius: var(--border-radius);
      border: 1px solid #e0e0e0;
      background-color: #f9f9f9;
      margin-bottom: 15px;
      transition: var(--transition);
    }
    
    input[type="text"]:focus,
    input[type="password"]:focus {
      outline: none;
      border-color: var(--primary-color);
      background-color: #fff;
      box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
    }
    
    select {
      width: 100%;
      padding: 10px;
      border-radius: var(--border-radius);
      border: 1px solid #ccc;
      margin-bottom: 15px;
      font-size: 1rem;
    }
    
    /* Secret Code Help Popup */
    .popup-message {
      display: none;
      position: absolute;
      background-color: #555;
      color: white;
      padding: 10px;
      border-radius: 5px;
      width: 250px;
      top: 20px;
      left: 10px;
      z-index: 10;
    }
    .popup-message::after {
      content: "";
      position: absolute;
      top: -5px;
      left: 10px;
      border-width: 5px;
      border-style: solid;
      border-color: transparent transparent #555 transparent;
    }
    .help-link {
      cursor: pointer;
      color: #007bff;
      text-decoration: underline;
      position: relative;
      font-size: 0.9em;
    }
    .help-container {
      position: relative;
      display: inline-block;
    }
    
    /* Captcha Row */
    .captcha-row {
      margin-bottom: 15px;
      text-align: left;
    }
    .captcha-image {
      width: 200px;
      height: auto;
      display: block;
      margin-bottom: 8px;
    }
    
    /* Submit Button */
    input[type="submit"] {
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
      margin-top: 20px;
    }
    input[type="submit"]:hover {
      background-color: var(--primary-hover);
    }
    
    a {
      display: block;
      margin-top: 12px;
      color: var(--primary-color);
      text-decoration: none;
      font-size: 0.9rem;
    }
    a:hover {
      text-decoration: underline;
    }
    
    /* Responsive Adjustments */
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
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-logo">
      <i class="fas fa-user-plus"></i>
    </div>
    
    <h2>Register</h2>
    <p class="subtitle">Create your account</p>
    
    <?php if (!empty($errors)) : ?>
      <div class="error-container" style="background-color: rgba(239,71,111,0.1); color: var(--error-color); border: 1px solid rgba(239,71,111,0.3); padding: 12px; border-radius: var(--border-radius); margin-bottom: 20px; text-align: left;">
        <ul>
          <?php foreach ($errors as $error) : ?>
            <li><?php echo htmlspecialchars($error); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
    
    <form method="POST" action="register.php" id="registerForm">
      <label for="username">Username</label>
      <input type="text" name="username" id="username" required>
      
      <label for="contact_option">Preferred Contact Option</label>
      <select name="contact_option" id="contact_option" required>
        <option value="Jabber" selected>Jabber</option>
        <option value="Telegram">Telegram</option>
      </select>
      
      <input type="text" name="contact_value" id="contact_value" placeholder="Enter your Jabber email or Telegram username" required>
      
      <label for="password">Password</label>
<input type="password" name="password" id="password" required placeholder="Enter your password">

<!-- Password Error Container -->
<div id="passwordErrorContainer" style="display: none; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
  <ul id="passwordRules" style="list-style: none; padding: 0; margin: 0; display: flex; flex-wrap: wrap; gap: 10px;">
    <li id="lengthRule">At least 8-20 characters</li>
    <li id="specialCharRule">At least one special character (!@#$%^&*)</li>
    <li id="numberRule">At least one number</li>
    <li id="letterRule">At least one letter</li>
  </ul>
  <p id="confirmMessage" style="margin-top: 10px; display: none;">Passwords do not match.</p>
</div>

<label for="confirm_password">Confirm Password</label>
<input type="password" name="confirm_password" id="confirm_password" required placeholder="Re-enter your password">

      
      <label for="secret_code">Secret Code
        <span class="help-container">
          <span class="help-link" onclick="showPopup()">What's this?</span>
          <div class="popup-message" id="popupMessage">
            Set your secret code from 6 digits, and keep it secure. You will need this code when you want to change or edit your account profile.
          </div>
        </span>
      </label>
      <input type="text" name="secret_code" pattern="\d{6}" maxlength="6" required>
      
      <div class="captcha-row">
        <label for="captcha">Enter CAPTCHA:</label>
        <img src="captcha.php" alt="CAPTCHA" class="captcha-image" id="captcha-image">
      </div>
      <input type="text" name="captcha" id="captcha" required placeholder="Type the code shown above">
      
      <input type="submit" value="Register">
      
      <a href="login.php">Already have an account? Login here</a>
    </form>
    
    <?php if ($accountCreated): ?>
  <div id="successModal" class="popup-modal" style="display: block;">
    <div class="popup-content" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
      <span class="close" onclick="closeSuccessModal()" style="cursor: pointer;">
        <i class="fas fa-times"></i>
      </span>
      <h3>You successfully created your account!</h3>
      <p>You will be redirected to the login page in 5 seconds...</p>
    </div>
  </div>
  <script>
    function closeSuccessModal() {
      document.getElementById('successModal').style.display = 'none';
    }
    // Auto-redirect after 5 seconds
    setTimeout(function() {
      window.location.href = 'login.php';
    }, 5000);
  </script>
<?php endif; ?>


  
  <script>
    function showPopup() {
      var popup = document.getElementById("popupMessage");
      popup.style.display = (popup.style.display === "none" || popup.style.display === "") ? "block" : "none";
    }
    
    document.addEventListener("click", function(event) {
      var popup = document.getElementById("popupMessage");
      var helpLink = document.querySelector(".help-link");
      if (popup.style.display === "block" && !popup.contains(event.target) && event.target !== helpLink) {
        popup.style.display = "none";
      }
    });
    
    document.getElementById("contact_option").addEventListener("change", function() {
      const contactValue = document.getElementById("contact_value");
      if (this.value === "Jabber") {
        contactValue.placeholder = "Enter your Jabber email";
      } else {
        contactValue.placeholder = "Enter Telegram @username or phone number";
      }
    });
    
    // Password and Confirm Password Validation
    const passwordInput = document.getElementById('password');
const confirmPasswordInput = document.getElementById('confirm_password');
const confirmMessage = document.getElementById('confirmMessage');
const passwordErrorContainer = document.getElementById('passwordErrorContainer');

const passwordRules = {
    lengthRule: /^.{8,20}$/,
    specialCharRule: /[!@#$%^&*]/,
    numberRule: /\d/,
    letterRule: /[a-zA-Z]/
};

const rulesList = {
    lengthRule: document.getElementById('lengthRule'),
    specialCharRule: document.getElementById('specialCharRule'),
    numberRule: document.getElementById('numberRule'),
    letterRule: document.getElementById('letterRule')
};

passwordInput.addEventListener('input', function() {
    const value = passwordInput.value;
    // Show the error container when there's input.
    passwordErrorContainer.style.display = value.length > 0 ? "block" : "none";
    
    Object.keys(passwordRules).forEach(rule => {
        if (passwordRules[rule].test(value)) {
            rulesList[rule].style.color = 'green';
            rulesList[rule].style.textDecoration = 'line-through';
        } else {
            rulesList[rule].style.color = 'red';
            rulesList[rule].style.textDecoration = 'none';
        }
    });
    
    checkPasswordsMatch();
});

confirmPasswordInput.addEventListener('input', checkPasswordsMatch);

function checkPasswordsMatch() {
    if (passwordInput.value && confirmPasswordInput.value && passwordInput.value !== confirmPasswordInput.value) {
        confirmMessage.style.display = 'block';
    } else {
        confirmMessage.style.display = 'none';
    }
}

  </script>
  
  <script src="js/catpcha.js"></script>
</body>
</html>

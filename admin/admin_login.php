<?php
ob_start(); 
session_start();
require '../config.php'; 

// Initialize variables
$error = '';
$login_success = false;
$username = '';


if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Security validation failed. Please try again.';
    } else {
        // Retrieve and sanitize username and password from POST
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']) ? true : false;

        // Check if username and password are set
        if (!empty($username) && !empty($password)) {
            try {
                // Prepare a statement to get admin details
                $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = :username LIMIT 1");
                $stmt->bindParam(':username', $username);
                $stmt->execute();

                // Fetch the admin record
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($admin && password_verify($password, $admin['password'])) {
                    // If login is successful, set session variables
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    
                    if ($remember) {
                        // Set cookie to expire in 30 days
                        ini_set('session.cookie_lifetime', 5 * 60);
                        session_regenerate_id(true);
                    }

                    // Log the successful login
                    $ip = $_SERVER['REMOTE_ADDR'];
                    $timestamp = date('Y-m-d H:i:s');
                    $logStmt = $pdo->prepare("INSERT INTO admin_login_logs (admin_id, ip_address, login_time, status) VALUES (:admin_id, :ip, :time, 'success')");
                    $logStmt->execute([
                        ':admin_id' => $admin['id'],
                        ':ip' => $ip,
                        ':time' => $timestamp
                    ]);

                    $login_success = true; // Set the flag for successful login
                } else {
                    // If credentials are invalid, set error message
                    $error = 'Invalid username or password.';
                    
                    // Log the failed login attempt
                    if (!empty($username)) {
                        $ip = $_SERVER['REMOTE_ADDR'];
                        $timestamp = date('Y-m-d H:i:s');
                        $logStmt = $pdo->prepare("INSERT INTO admin_login_logs (username, ip_address, login_time, status) VALUES (:username, :ip, :time, 'failed')");
                        $logStmt->execute([
                            ':username' => $username,
                            ':ip' => $ip,
                            ':time' => $timestamp
                        ]);
                    }
                }
            } catch (PDOException $e) {
                $error = 'Database error. Please try again later.';
                // Log the error for admin review
                error_log('Login error: ' . $e->getMessage());
            }
        } else {
            $error = 'Please enter both username and password.';
        }
    }
}

ob_end_flush(); // End output buffering
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Modern CSS Reset */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        /* Variables */
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
        
        /* Base Styles */
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
        
        /* Login Container */
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
        
        /* Logo/Icon */
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
        
        /* Headings */
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
        
        /* Remember Me Checkbox */
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 0.9rem;
        }
        
        .checkbox-container {
            display: flex;
            align-items: center;
        }
        
        .checkbox-container input[type="checkbox"] {
            margin-right: 8px;
            accent-color: var(--primary-color);
        }
        
        .forgot-password {
            color: var(--primary-color);
            text-decoration: none;
            transition: var(--transition);
        }
        
        .forgot-password:hover {
            text-decoration: underline;
        }
        
        /* Button */
        button {
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
        }
        
        button:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
        }
        
        button:active {
            transform: translateY(0);
        }
        
        /* Error Message */
        .error {
            color: var(--error-color);
            background-color: rgba(239, 71, 111, 0.1);
            border: 1px solid rgba(239, 71, 111, 0.3);
            padding: 12px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
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
        
        /* Password Toggle */
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
    <div class="login-logo">
        <i class="fas fa-shield-alt"></i>
    </div>
    
    <h2>Admin Login</h2>
    <p class="subtitle">Enter your credentials to access the dashboard</p>

    <!-- Display error message if set -->
    <?php if (!empty($error)): ?>
        <div class="error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <!-- Login Form -->
    <form action="" method="POST" autocomplete="off">
        <!-- CSRF Token -->
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        
        <div class="form-group">
            <label for="username">Username</label>
            <div class="input-group">
                <i class="input-icon fas fa-user"></i>
                <input 
                    type="text" 
                    name="username" 
                    id="username" 
                    value="<?php echo htmlspecialchars($username); ?>" 
                    required 
                    autofocus
                    placeholder="Enter your username"
                >
            </div>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <div class="input-group">
                <i class="input-icon fas fa-lock"></i>
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    required
                    placeholder="Enter your password"
                >
                <i class="password-toggle fas fa-eye" id="togglePassword"></i>
            </div>
        </div>

       

        <button type="submit">
            <i class="fas fa-sign-in-alt"></i>
            Login
        </button>
    </form>
</div>

<script>
    // Toggle password visibility
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
</script>

<!-- JavaScript for redirection after successful login -->
<?php if ($login_success): ?>
    <script>
        // Show success message briefly before redirecting
        document.querySelector('.login-container').innerHTML = `
            <div class="login-logo" style="background-color: var(--success-color)">
                <i class="fas fa-check"></i>
            </div>
            <h2>Login Successful</h2>
            <p class="subtitle">Redirecting to dashboard...</p>
        `;
        
        // Redirect to dashboard after a short delay
        setTimeout(function() {
            window.location.href = './thedash.php';
        }, 1500);
    </script>
<?php endif; ?>

</body>
</html>


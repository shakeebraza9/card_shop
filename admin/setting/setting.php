<?php
// Include the global configuration file
require_once("../global.php");

// Initialize variables
$message = '';
$messageType = '';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $checker_name   = $_POST['checker_name'];
    $checker_status = $_POST['checker_status'];
    $margin         = $_POST['margin'];
    $mirror         = $_POST['mirror'];
    
    try {
        $pdo->beginTransaction();
        
        // Update the existing settings
        $updateStmt = $pdo->prepare("UPDATE site_settings SET value = :value WHERE `key` = :key");
        $updateStmt->execute(['value' => $checker_name,   'key' => 'checker_name']);
        $updateStmt->execute(['value' => $checker_status, 'key' => 'checker_status']);
        $updateStmt->execute(['value' => $margin,         'key' => 'margin']);
        
        // For mirror, use INSERT ... ON DUPLICATE KEY UPDATE to ensure the row exists
        $mirrorStmt = $pdo->prepare("
            INSERT INTO site_settings (`key`, value)
            VALUES ('Mirror', :mirror)
            ON DUPLICATE KEY UPDATE value = :mirror
        ");
        $mirrorStmt->execute(['mirror' => $mirror]);
        
        $pdo->commit();
        $message = "Settings updated successfully!";
        $messageType = "success";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $message = "Error updating settings: " . $e->getMessage();
        $messageType = "error";
    }
}

// Fetch current settings
$settings = [];
$query = "SELECT `key`, value FROM site_settings WHERE `key` IN ('checker_name', 'checker_status', 'margin', 'Mirror')";
$stmt = $pdo->query($query);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $row) {
    $settings[$row['key']] = $row['value'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Settings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Enhanced Inline CSS */
        :root {
            --primary-color: #4361ee;
            --primary-hover: #3a56d4;
            --secondary-color: #f8f9fa;
            --text-color: #333;
            --text-light: #6c757d;
            --success-color: #2ecc71;
            --error-color: #e74c3c;
            --border-color: #e0e0e0;
            --shadow-color: rgba(0, 0, 0, 0.1);
            --card-bg: #ffffff;
            --input-bg: #f8f9fa;
            --input-focus: #edf2ff;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: #f0f2f5;
            padding: 0;
            margin: 0;
            min-height: 100vh;
        }
        
        .container {
            max-width: 800px;
            margin: 40px auto;
            background-color: var(--card-bg);
            border-radius: 10px;
            box-shadow: 0 8px 30px var(--shadow-color);
            overflow: hidden;
        }
        
        .header {
            background-color: var(--primary-color);
            color: white;
            padding: 25px 30px;
            position: relative;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            border: none;
            padding: 0;
        }
        
        .header p {
            margin-top: 5px;
            opacity: 0.9;
            font-size: 14px;
        }
        
        .form-container {
            padding: 30px;
        }
        
        .message {
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            animation: fadeIn 0.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .success {
            background-color: rgba(46, 204, 113, 0.15);
            color: #27ae60;
            border-left: 4px solid var(--success-color);
        }
        
        .error {
            background-color: rgba(231, 76, 60, 0.15);
            color: #c0392b;
            border-left: 4px solid var(--error-color);
        }
        
        .message i {
            margin-right: 10px;
            font-size: 18px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group:last-of-type {
            margin-bottom: 30px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-color);
            font-size: 15px;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 15px;
            color: var(--text-color);
            background-color: var(--input-bg);
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
            background-color: var(--input-focus);
        }
        
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236c757d' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            padding-right: 40px;
        }
        
        .status-toggle {
            display: flex;
            align-items: center;
        }
        
        .status-option {
            flex: 1;
            text-align: center;
            padding: 10px;
            border: 1px solid var(--border-color);
            background-color: var(--input-bg);
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }
        
        .status-option:first-child {
            border-radius: 6px 0 0 6px;
            border-right: none;
        }
        
        .status-option:last-child {
            border-radius: 0 6px 6px 0;
        }
        
        .status-option.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .status-option input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }
        
        .status-option i {
            margin-right: 5px;
        }
        
        .btn {
            display: inline-block;
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 500;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(67, 97, 238, 0.15);
        }
        
        .btn:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(67, 97, 238, 0.2);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .btn i {
            margin-right: 8px;
        }
        
        .form-footer {
            display: flex;
            justify-content: flex-end;
        }
        
        .setting-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            border-left: 4px solid var(--primary-color);
        }
        
        .setting-card h3 {
            margin-bottom: 15px;
            font-size: 18px;
            color: var(--primary-color);
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
        }
        
        .input-with-icon input,
        .input-with-icon select {
            padding-left: 40px;
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 20px;
                width: auto;
            }
            
            .header {
                padding: 20px;
            }
            
            .form-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-cogs"></i> Site Settings</h1>
            <p>Configure your website settings and preferences</p>
        </div>
        
        <div class="form-container">
            <?php if (!empty($message)): ?>
                <div class="message <?php echo $messageType; ?>">
                    <i class="fas fa-<?php echo $messageType == 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="setting-card">
                    <h3><i class="fas fa-check-square"></i> Checker Configuration</h3>
                    
                    <div class="form-group">
                        <label for="checker_name">Checker Name:</label>
                        <div class="input-with-icon">
                            <i class="fas fa-tag"></i>
                            <select name="checker_name" id="checker_name" class="form-control">
                                <option value="Lux Checker" <?php echo (isset($settings['checker_name']) && $settings['checker_name'] == 'Lux Checker') ? 'selected' : ''; ?>>Lux Checker</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="mirror">Mirror:</label>
                        <div class="input-with-icon">
                            <i class="fas fa-clone"></i>
                            <select name="mirror" id="mirror" class="form-control">
                                <option value="Mirror1" <?php echo (isset($settings['Mirror']) && $settings['Mirror'] == 'Mirror1') ? 'selected' : ''; ?>>Mirror1</option>
                                <option value="Mirror2" <?php echo (isset($settings['Mirror']) && $settings['Mirror'] == 'Mirror2') ? 'selected' : ''; ?>>Mirror2</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                    
                    <div class="form-group">
                        <label for="checker_status">Checker Status:</label>
                        <div class="status-toggle">
                            <label class="status-option <?php echo (isset($settings['checker_status']) && $settings['checker_status'] == '1') ? 'active' : ''; ?>">
                                <input type="radio" name="checker_status" value="1" <?php echo (isset($settings['checker_status']) && $settings['checker_status'] == '1') ? 'checked' : ''; ?>>
                                <i class="fas fa-power-off"></i> ON
                            </label>
                            <label class="status-option <?php echo (isset($settings['checker_status']) && $settings['checker_status'] == '0') ? 'active' : ''; ?>">
                                <input type="radio" name="checker_status" value="0" <?php echo (isset($settings['checker_status']) && $settings['checker_status'] == '0') ? 'checked' : ''; ?>>
                                <i class="fas fa-power-off"></i> OFF
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="setting-card">
                    <h3><i class="fas fa-sliders-h"></i> BTC </h3>
                    
                    <div class="form-group">
                        <label for="margin">Margin:</label>
                        <div class="input-with-icon">
                            <i class="fas fa-arrows-alt-h"></i>
                            <input type="number" name="margin" id="margin" class="form-control" value="<?php echo isset($settings['margin']) ? $settings['margin'] : ''; ?>">
                        </div>
                    </div>
                    
                    
                <div class="form-footer">
                    <button type="submit" class="btn">
                        <i class="fas fa-save"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Toggle active class for status options
        document.querySelectorAll('.status-option input').forEach(input => {
            input.addEventListener('change', function() {
                // Remove active class from all options
                document.querySelectorAll('.status-option').forEach(option => {
                    option.classList.remove('active');
                });
                
                // Add active class to selected option
                if (this.checked) {
                    this.parentElement.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>
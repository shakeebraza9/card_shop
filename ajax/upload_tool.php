<?php
require '../config.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: admin_login.php?redirect=panel.php");
    exit();
}

$errors = [];
$successMessage = '';
$formData = [
    'name' => '',
    'description' => '',
    'price' => '',
    'section' => 'Tools'
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // CSRF Protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = "Security validation failed. Please try again.";
    } else {
        // Sanitize and validate inputs
        $formData['name'] = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS));
        $formData['description'] = trim(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS));
        $formData['price'] = trim(filter_input(INPUT_POST, 'price', FILTER_SANITIZE_SPECIAL_CHARS));
        $formData['section'] = filter_input(INPUT_POST, 'section', FILTER_SANITIZE_SPECIAL_CHARS);
        
        // Validate required fields
        if (empty($formData['name'])) $errors[] = "File name is required";
        if (empty($formData['description'])) $errors[] = "Description is required";
        if (empty($formData['section'])) $errors[] = "Section is required";
        
        // File upload validation
        if (!isset($_FILES['file']) || $_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) {
            $errors[] = "Please select a file to upload";
        } elseif ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $uploadErrors = [
                UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the upload_max_filesize directive in php.ini",
                UPLOAD_ERR_FORM_SIZE => "The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form",
                UPLOAD_ERR_PARTIAL => "The uploaded file was only partially uploaded",
                UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder",
                UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk",
                UPLOAD_ERR_EXTENSION => "A PHP extension stopped the file upload"
            ];
            $errorMessage = isset($uploadErrors[$_FILES['file']['error']]) 
                ? $uploadErrors[$_FILES['file']['error']] 
                : "Unknown upload error";
            $errors[] = "File upload error: " . $errorMessage;
            error_log("File upload error: " . $errorMessage);
        } else {
            $file = $_FILES['file'];
            
            // Validate file size
            $maxFileSize = 10 * 1024 * 1024; // 10MB
            if ($file['size'] > $maxFileSize) {
                $errors[] = "File size exceeds the maximum limit of 10MB";
            }
        }
        
        // Check if file with same name already exists in the database
        if (empty($errors)) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM uploads WHERE name = ? AND section = ?");
            $stmt->execute([$formData['name'], $formData['section']]);
            $count = $stmt->fetchColumn();
            
            if ($count > 0) {
                $errors[] = "A {$formData['section']} with the name '{$formData['name']}' already exists. Please choose a different name.";
                error_log("Admin notification: The name '{$formData['name']}' already exists in the '{$formData['section']}' section.");
            }
        }
        
        // Process upload if no errors
        if (empty($errors)) {
            $uploadDir = 'uploads/' . strtolower($formData['section']) . '/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true); 
            }
            
            $uploadFile = __DIR__ . '/' . $uploadDir . basename($file['name']);
            
            if (file_exists($uploadFile)) {
                $errors[] = "A file with the name '{$file['name']}' already exists. Please rename your file and try again.";
            } else {
                if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
                    $stmt = $pdo->prepare("INSERT INTO uploads (name, description, file_path, price, section) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$formData['name'], $formData['description'], $uploadFile, $formData['price'], $formData['section']]);
                    
                    header("Location: upload_tool.php?success=1&file_name=" . urlencode($formData['name']));
                    exit();
                } else {
                    $errors[] = "Failed to upload the file.";
                    error_log("Failed to upload the file.");
                }
            }
        }
    }
}

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle success message from redirect
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $fileName = isset($_GET['file_name']) ? htmlspecialchars($_GET['file_name']) : '';
    $successMessage = "{$fileName} uploaded successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Tool</title>
    <script>
        // Preview file name before upload
        function updateFileLabel() {
            const fileInput = document.getElementById('file-input');
            const fileLabel = document.getElementById('file-label');
            
            if (fileInput.files.length > 0) {
                fileLabel.textContent = fileInput.files[0].name;
            } else {
                fileLabel.textContent = 'Choose a file';
            }
        }
        
        // Form validation
        function validateForm() {
            const form = document.getElementById('upload-form');
            const fileInput = document.getElementById('file-input');
            const errorContainer = document.getElementById('validation-errors');
            errorContainer.innerHTML = '';
            
            let isValid = true;
            
            // Check required fields
            const requiredFields = ['name', 'description', 'section'];
            requiredFields.forEach(field => {
                const input = form.elements[field];
                if (!input.value.trim()) {
                    addError(`${field.charAt(0).toUpperCase() + field.slice(1)} is required`);
                    isValid = false;
                }
            });
            
            // Check file is selected
            if (fileInput.files.length === 0) {
                addError('Please select a file to upload');
                isValid = false;
            }
            
            function addError(message) {
                const errorElement = document.createElement('p');
                errorElement.textContent = message;
                errorContainer.appendChild(errorElement);
                errorContainer.style.display = 'block';
            }
            
            return isValid;
        }
        
        // Auto-hide messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const messages = document.querySelectorAll('.message');
            if (messages.length > 0) {
                setTimeout(function() {
                    messages.forEach(msg => {
                        msg.style.opacity = '0';
                        setTimeout(() => msg.style.display = 'none', 500);
                    });
                }, 5000);
            }
        });
    </script>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f5f7fb; padding: 20px; margin: 0; box-sizing: border-box;">

<div style="max-width: 600px; margin: 40px auto; background: white; border-radius: 8px; padding: 30px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
    <h2 style="text-align: center; margin-bottom: 25px; color: #4a6cf7; font-weight: 600;">Upload a File</h2>
    
    <!-- Validation errors container -->
    <div id="validation-errors" style="padding: 15px; margin-bottom: 20px; border-radius: 4px; background-color: rgba(231, 76, 60, 0.1); border-left: 4px solid #e74c3c; color: #c0392b; display: none; transition: opacity 0.5s ease;"></div>
    
    <!-- Display error/success messages -->
    <?php if (!empty($errors)): ?>
        <div style="padding: 15px; margin-bottom: 20px; border-radius: 4px; background-color: rgba(231, 76, 60, 0.1); border-left: 4px solid #e74c3c; color: #c0392b; transition: opacity 0.5s ease;">
            <?php foreach ($errors as $error): ?>
                <p style="margin: 5px 0;"><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php elseif (!empty($successMessage)): ?>
        <div style="padding: 15px; margin-bottom: 20px; border-radius: 4px; background-color: rgba(46, 204, 113, 0.1); border-left: 4px solid #2ecc71; color: #27ae60; transition: opacity 0.5s ease;">
            <p style="margin: 5px 0;"><?php echo htmlspecialchars($successMessage); ?></p>
        </div>
    <?php endif; ?>

    <form id="upload-form" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
        <!-- CSRF Protection -->
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        
        <div style="margin-bottom: 20px;">
            <label for="name" style="display: block; margin-bottom: 8px; font-weight: 500; color: #555;">File Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($formData['name']); ?>" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; transition: all 0.3s ease; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 20px;">
            <label for="description" style="display: block; margin-bottom: 8px; font-weight: 500; color: #555;">Description</label>
            <textarea id="description" name="description" rows="4" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; transition: all 0.3s ease; box-sizing: border-box;"><?php echo htmlspecialchars($formData['description']); ?></textarea>
        </div>

        <div style="margin-bottom: 20px;">
            <label for="price" style="display: block; margin-bottom: 8px; font-weight: 500; color: #555;">Price (if applicable)</label>
            <input type="text" id="price" name="price" value="<?php echo htmlspecialchars($formData['price']); ?>" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; transition: all 0.3s ease; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 20px;">
            <label for="section" style="display: block; margin-bottom: 8px; font-weight: 500; color: #555;">Select Section</label>
            <select id="section" name="section" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; transition: all 0.3s ease; box-sizing: border-box;">
                <?php foreach (['Tools', 'Leads', 'Pages'] as $section): ?>
                    <option value="<?php echo $section; ?>" <?php echo ($formData['section'] === $section) ? 'selected' : ''; ?>>
                        <?php echo $section; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div style="margin: 25px 0;">
            <label for="file-input" style="display: block; background-color: #f0f2f5; border: 2px dashed #ccc; border-radius: 4px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.3s ease;">
                <span id="file-label" style="display: block; font-weight: 500; color: #666; margin-bottom: 5px;">Choose a file</span>
                <input type="file" id="file-input" name="file" onchange="updateFileLabel()" style="display: none;">
            </label>
            <small style="display: block; margin-top: 8px; font-size: 12px; color: #777; text-align: center;">Select your file to upload</small>
        </div>
        
        <div style="display: flex; justify-content: space-between; margin-top: 30px;">
            <button type="submit" style="background-color: #4a6cf7; color: white; border: none; padding: 12px 24px; border-radius: 4px; font-size: 16px; font-weight: 500; cursor: pointer; transition: all 0.3s ease; text-align: center; text-decoration: none; flex: 1; margin-right: 10px;">Upload File</button>
            <a href="tpanel.php" style="background-color: #f0f2f5; color: #555; border: 1px solid #ddd; padding: 12px 24px; border-radius: 4px; font-size: 16px; font-weight: 500; cursor: pointer; transition: all 0.3s ease; text-align: center; text-decoration: none; flex: 0.5; display: inline-block;">Back to Dashboard</a>
        </div>
    </form>
</div>

</body>
</html>


<?php
require '../../global.php';
session_start();

// Check if user is logged in, if not redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$tool_id = isset($_GET['tool_id']) ? intval($_GET['tool_id']) : 0;

// Fetch the tool information from the database with decryption of file path
$key = 'Laraib666Rabbani!'; // Use the encryption key from your configuration
$stmt = $pdo->prepare("SELECT 
                        id, 
                        AES_DECRYPT(file_path, :key) AS file_path 
                    FROM uploads 
                    WHERE id = :tool_id");
$stmt->bindParam(':key', $key, PDO::PARAM_STR);
$stmt->bindParam(':tool_id', $tool_id, PDO::PARAM_INT);
$stmt->execute();
$tool = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the tool was found
if (!$tool) {
    echo "File not found.";
    exit();
}

// Decrypt file path
$file_path = $tool['file_path'];

// Check if the file exists on the server
if (!file_exists($file_path)) {
    echo "File not found on the server.";
    exit();
}

// Check if the tool_id belongs to the logged-in user in the orders table
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = :user_id AND tool_id = :tool_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindParam(':tool_id', $tool_id, PDO::PARAM_INT);
$stmt->execute();
$order = $stmt->fetch();

if (!$order) {
    // JavaScript for alert without changing the URL hash
    echo "<script>
        alert('Purchase required to access this feature.');
        window.location.replace('dashboard.php#my-orders'); // Redirect without changing hash
    </script>";
    exit();
}

// Set headers to force download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file_path));

// Read the file and output it to the browser
readfile($file_path);
exit();
?>

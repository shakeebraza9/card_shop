<?php
// Turn on error reporting, but log it instead of displaying.
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Log a debug message at script start:
error_log("deleteAllLogs.php - script started");

session_start();
header("Content-Type: application/json");

// Load global configuration (which should define $pdo). 
// Make sure global.php doesn’t output anything (no echo, print, whitespace).
require '../../global.php';

// Log if $pdo is set or not:
error_log("deleteAllLogs.php - after global.php, pdo is " . (isset($pdo) ? "SET" : "NOT SET"));

// Check user session
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    error_log("deleteAllLogs.php - user not authenticated");
    echo json_encode(["error" => "User not authenticated"]);
    exit;
}

// Attempt the update
try {
    $stmt = $pdo->prepare("UPDATE transaction_access_log SET deleted = 1 WHERE buyer_id = ?");
    $stmt->execute([$userId]);
    error_log("deleteAllLogs.php - logs updated for user_id=$userId");
    echo json_encode(["success" => "All logs have been marked as deleted."]);
} catch (Exception $e) {
    // Log the exception message
    error_log("deleteAllLogs.php - exception: " . $e->getMessage());
    echo json_encode(["error" => "An error occurred while deleting logs."]);
}

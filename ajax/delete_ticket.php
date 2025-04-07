<?php
session_start();
require '../config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

try {
    // Update admin_unread flag for all tickets
    $stmt = $pdo->prepare("
        SELECT id FROM support_tickets 
        WHERE admin_unread = 1
    ");
    $stmt->execute();
    $tickets = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // We're not actually changing any values here, just checking
    // This is to avoid changing DB values as requested
    
    echo json_encode(['success' => true, 'tickets' => $tickets]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>


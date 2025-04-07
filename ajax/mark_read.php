<?php
session_start();
require '../config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

// Check if required parameters are provided
if (!isset($_POST['ticket_id']) || !isset($_POST['status'])) {
    echo json_encode(['success' => false, 'error' => 'Ticket ID and status are required']);
    exit();
}

$ticketId = $_POST['ticket_id'];
$status = $_POST['status'];

// Validate status
if (!in_array($status, ['open', 'closed'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid status']);
    exit();
}

try {
    // Update ticket status
    $stmt = $pdo->prepare("
        UPDATE support_tickets 
        SET status = ? 
        WHERE id = ?
    ");
    $stmt->execute([$status, $ticketId]);
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>


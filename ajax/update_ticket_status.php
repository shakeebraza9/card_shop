<?php
session_start();
require '../config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

// Check if ticket_id is provided
if (!isset($_POST['ticket_id'])) {
    echo json_encode(['success' => false, 'error' => 'Ticket ID is required']);
    exit();
}

$ticketId = $_POST['ticket_id'];

try {
    // Start transaction
    $pdo->beginTransaction();
    
    // Delete all replies for this ticket
    $stmt = $pdo->prepare("DELETE FROM support_replies WHERE ticket_id = ?");
    $stmt->execute([$ticketId]);
    
    // Delete the ticket
    $stmt = $pdo->prepare("DELETE FROM support_tickets WHERE id = ?");
    $stmt->execute([$ticketId]);
    
    // Commit transaction
    $pdo->commit();
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>


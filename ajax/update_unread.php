<?php
include_once('../global.php');
require '../config.php';




try {
    // Update support tickets
    $query = "UPDATE support_tickets SET admin_unread = 1 WHERE admin_unread != 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    try {
        // Update support replies that are not yet read and sent by the user
        $query = "UPDATE support_replies SET is_read = 1 WHERE is_read != 1 AND sender = 'user'";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Error updating support replies: ' . $e->getMessage()]);
        exit();
    }
    

    // If both queries succeed
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    // If the support tickets update fails
    echo json_encode(['success' => false, 'error' => 'Error updating support tickets: ' . $e->getMessage()]);
}
?>

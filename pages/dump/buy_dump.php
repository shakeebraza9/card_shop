<?php
session_start();
require '../../global.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Unknown error occurred.'];

// Ensure the buyer is logged in.
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to make a purchase.']);
    exit();
}

$buyer_id = $_SESSION['user_id'];
$transaction_did = filter_input(INPUT_POST, 'transaction_did', FILTER_VALIDATE_INT);

if (!$transaction_did) {
    echo json_encode(['success' => false, 'message' => 'Invalid Dump ID.']);
    exit();
}

try {
    $pdo->beginTransaction();

    // Lock the dump row for update to avoid concurrent purchases.
    $stmt = $pdo->prepare("
        SELECT id, data_segment_one, payment_method_type, seller_id, price 
        FROM dmptransaction_data 
        WHERE id = ? AND buyer_id IS NULL 
        FOR UPDATE
    ");
    $stmt->execute([$transaction_did]);
    $dump = $stmt->fetch();

    if (!$dump) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Dump not found or already sold.']);
        exit();
    }

    $price     = $dump['price'];
    $seller_id = $dump['seller_id'];
    $dump_type = 'Dumps';  // For logging
    $transaction_did   = $dump['id'];

    // Retrieve seller percentage from seller's account (default to 100 if not set)
    $stmt = $pdo->prepare("SELECT seller_percentage FROM users WHERE id = ?");
    $stmt->execute([$seller_id]);
    $sellerData = $stmt->fetch();
    $seller_percentage = isset($sellerData['seller_percentage']) ? $sellerData['seller_percentage'] : 100;
    $seller_earnings = ($price * $seller_percentage) / 100;

    // Verify buyer's balance.
    $stmt = $pdo->prepare("SELECT balance, username FROM users WHERE id = ?");
    $stmt->execute([$buyer_id]);
    $buyer = $stmt->fetch();

    if (!$buyer || $buyer['balance'] < $price) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Not enough balance to complete the purchase.']);
        exit();
    }

    // Deduct the price from the buyer's balance.
    $updateBuyerStmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
    $updateBuyerStmt->execute([$price, $buyer_id]);

    // Mark the dump as sold.
    $updateDumpStmt = $pdo->prepare("
        UPDATE dmptransaction_data 
        SET buyer_id = ?, status = 'sold', purchased_at = NOW() 
        WHERE id = ?
    ");
    $updateDumpStmt->execute([$buyer_id, $transaction_did]);

    // Update seller's earnings.
    $updateSellerStmt = $pdo->prepare("
        UPDATE users 
        SET dumps_balance = dumps_balance + ?, 
            dumps_total_earned = dumps_total_earned + ?, 
            total_earned = total_earned + ? 
        WHERE id = ?
    ");
    $updateSellerStmt->execute([$seller_earnings, $seller_earnings, $seller_earnings, $seller_id]);

    $pdo->commit();

    // Log the purchase activity.
    $logData = [
        'user_id'    => $buyer_id,
        'user_name'  => $_SESSION['username'],
        'item_id'    => $transaction_did,
        'buy_itm'    => "transaction_did: $transaction_did",
        'item_price' => $price,
        'item_type'  => $dump_type
    ];
    $settings->insertActivityLog($logData);

    $response['success'] = true;
    $response['message'] = 'Purchase successful. Please visit the My Dumps section to view your purchased dumps.';
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Transaction error in buy_dumps.php: ' . $e->getMessage());
    $response['message'] = 'Transaction failed. Please try again. Error: ' . $e->getMessage();
}

echo json_encode($response);
exit();
?>
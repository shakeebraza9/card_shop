<?php
session_start();
require '../../global.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Unknown error occurred.'];

// Ensure user is logged in.
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'You must be logged in to make a purchase.';
    echo json_encode($response);
    exit();
}

$buyer_id = $_SESSION['user_id'];
$calrecord_id = $_POST['calrecord_id'] ?? null;

if (!$calrecord_id) {
    $response['message'] = 'Card ID is missing.';
    echo json_encode($response);
    exit();
}

try {
    $pdo->beginTransaction();

    // Lock the card row to prevent concurrent purchases.
    $stmt = $pdo->prepare("
        SELECT id, seller_id, price, creference_code, payment_method_type 
        FROM cncustomer_records 
        WHERE id = ? AND status = 'unsold' 
        FOR UPDATE
    ");
    $stmt->execute([$calrecord_id]);
    $card = $stmt->fetch();

    if (!$card) {
        $pdo->rollBack();
        $response['message'] = 'Sorry, this card has already been purchased by another user.';
        echo json_encode($response);
        exit();
    }

    $price = $card['price'];
    $seller_id = $card['seller_id'];
    $payment_method_type = 'Cards';

    // Check buyer's balance.
    $stmt = $pdo->prepare("SELECT balance, username FROM users WHERE id = ?");
    $stmt->execute([$buyer_id]);
    $buyer = $stmt->fetch();

    if (!$buyer || $buyer['balance'] < $price) {
        $pdo->rollBack();
        $response['message'] = 'Not enough balance to complete the purchase.';
        echo json_encode($response);
        exit();
    }

    // Deduct the price from the buyer's balance.
    $updateBuyerStmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
    $updateBuyerStmt->execute([$price, $buyer_id]);

    // Mark the card as sold.
    $updateCardStmt = $pdo->prepare("
        UPDATE cncustomer_records 
        SET buyer_id = ?, status = 'sold', purchased_at = NOW() 
        WHERE id = ?
    ");
    $updateCardStmt->execute([$buyer_id, $calrecord_id]);

    // Retrieve seller percentage (default to 100 if not set).
    $sellerPercentageStmt = $pdo->prepare("SELECT seller_percentage FROM users WHERE id = ?");
    $sellerPercentageStmt->execute([$seller_id]);
    $sellerData = $sellerPercentageStmt->fetch();
    $seller_percentage = isset($sellerData['seller_percentage']) ? $sellerData['seller_percentage'] : 100;
    
    // Calculate seller earnings.
    $seller_earnings = ($price * $seller_percentage) / 100;

    // Update seller's account with the earnings.
    $updateSellerStmt = $pdo->prepare("
        UPDATE users 
        SET credit_cards_balance = credit_cards_balance + ?, 
            credit_cards_total_earned = credit_cards_total_earned + ?, 
            total_earned = total_earned + ? 
        WHERE id = ?
    ");
    $updateSellerStmt->execute([$seller_earnings, $seller_earnings, $seller_earnings, $seller_id]);

    $pdo->commit();

    // Log the transaction.
    $logData = [
        'user_id'    => $buyer_id,
        'user_name'  => $buyer['username'],
        'item_id'    => $calrecord_id,
        'buy_itm'    => "calrecord_id: $calrecord_id",
        'item_price' => $price,
        'item_type'  => $payment_method_type
    ];
    $settings->insertActivityLog($logData);

    $response['success'] = true;
    $response['message'] = 'Purchase successful. Please visit the My Cards section to view your purchased cards.';
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Transaction failed in buy_card.php: " . $e->getMessage());
    $response['message'] = 'Transaction failed. Please try again. Error: ' . $e->getMessage();
}

echo json_encode($response);
exit();
?>
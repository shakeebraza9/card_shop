<?php
session_start();
require '../global.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Unknown error occurred.'];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'You must be logged in to make a purchase.';
    echo json_encode($response);
    exit();
}

$buyer_id = $_SESSION['user_id'];
$sessionCards = $_SESSION['cards'] ?? [];
$sessionDumps = $_SESSION['dumps'] ?? [];

// Merge items (if needed)
$items = array_merge(
    array_map(fn($item) => ['id' => $item['id'], 'type' => 'card'], $sessionCards),
    array_map(fn($item) => ['id' => $item['id'], 'type' => 'dump'], $sessionDumps)
);

if (empty($items)) {
    $response['message'] = 'No items in the cart.';
    echo json_encode($response);
    exit();
}

try {
    $pdo->beginTransaction(); 

    $totalPrice = 0;
    $validCards = [];
    $validDumps = [];
    $skippedMessages = [];

    // Process cards: Check each card for availability
    foreach ($sessionCards as $card) {
        $cardId = $card['id'];
        $stmt = $pdo->prepare("SELECT seller_id, price FROM cncustomer_records WHERE id = ? AND status = 'unsold' FOR UPDATE");
        $stmt->execute([$cardId]);
        $cardData = $stmt->fetch();

        if ($cardData) {
            $totalPrice += $cardData['price'];
            $validCards[] = [
                'id' => $cardId,
                'seller_id' => $cardData['seller_id'],
                'price' => $cardData['price'],
            ];
        } else {
            // Instead of rolling back, simply skip this card and log a message.
            $skippedMessages[] = "Card with ID $cardId is no longer available or already sold.";
        }
    }

    // Process dumps: Check each dump for availability
    foreach ($sessionDumps as $dump) {
        $dumpId = $dump['id'];
        $stmt = $pdo->prepare("SELECT seller_id, price FROM dmptransaction_data WHERE id = ? AND buyer_id IS NULL FOR UPDATE");
        $stmt->execute([$dumpId]);
        $dumpData = $stmt->fetch();

        if ($dumpData) {
            $totalPrice += $dumpData['price'];
            $validDumps[] = [
                'id' => $dumpId,
                'seller_id' => $dumpData['seller_id'],
                'price' => $dumpData['price'],
            ];
        } else {
            $skippedMessages[] = "Dump with ID $dumpId is no longer available or already sold.";
        }
    }

    // If no valid items remain, rollback and return an error.
    if (empty($validCards) && empty($validDumps)) {
        $pdo->rollBack();
        $response['message'] = "None of the items in your cart are available. " . implode(" ", $skippedMessages);
        echo json_encode($response);
        exit();
    }

    // Check buyer balance for the sum of available items.
    $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
    $stmt->execute([$buyer_id]);
    $buyer = $stmt->fetch();

    if ($buyer && $buyer['balance'] >= $totalPrice) {

        // Process valid cards
        foreach ($validCards as $card) {
            $updateBuyerStmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
            $updateBuyerStmt->execute([$card['price'], $buyer_id]);

            $updateCardStmt = $pdo->prepare("UPDATE cncustomer_records SET buyer_id = ?, status = 'sold', created_at = NOW(), purchased_at = NOW() WHERE id = ?");
            $updateCardStmt->execute([$buyer_id, $card['id']]);

            $sellerPercentageStmt = $pdo->prepare("SELECT seller_percentage FROM users WHERE id = ?");
            $sellerPercentageStmt->execute([$card['seller_id']]);
            $seller = $sellerPercentageStmt->fetch();
            $seller_percentage = $seller['seller_percentage'] ?? 100;
            $seller_earnings = ($card['price'] * $seller_percentage) / 100;

            $updateSellerStmt = $pdo->prepare("
                UPDATE users 
                SET credit_cards_balance = credit_cards_balance + ?, credit_cards_total_earned = credit_cards_total_earned + ?, total_earned = total_earned + ? 
                WHERE id = ?
            ");
            $updateSellerStmt->execute([$seller_earnings, $seller_earnings, $seller_earnings, $card['seller_id']]);

            $insertActivityLogStmt = $pdo->prepare("
                INSERT INTO activity_log (user_id, user_name, item_id, buy_itm, item_price, item_type, created_at) 
                VALUES (?, ?, ?, ?, ?, 'Cards', NOW())
            ");
            $insertActivityLogStmt->execute([$buyer_id, $_SESSION['username'], $card['id'], 'calrecord_id=' . $card['id'], $card['price']]);
        }

        // Process valid dumps
        foreach ($validDumps as $dump) {
            $updateBuyerStmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
            $updateBuyerStmt->execute([$dump['price'], $buyer_id]);

            $updateDumpStmt = $pdo->prepare("UPDATE dmptransaction_data SET buyer_id = ?, status = 'sold', purchased_at = NOW() WHERE id = ?");
            $updateDumpStmt->execute([$buyer_id, $dump['id']]);

            $sellerPercentageStmt = $pdo->prepare("SELECT seller_percentage FROM users WHERE id = ?");
            $sellerPercentageStmt->execute([$dump['seller_id']]);
            $seller = $sellerPercentageStmt->fetch();
            $seller_percentage = $seller['seller_percentage'] ?? 100;
            $seller_earnings = ($dump['price'] * $seller_percentage) / 100;

            $updateSellerStmt = $pdo->prepare("
                UPDATE users 
                SET dumps_balance = dumps_balance + ?, dumps_total_earned = dumps_total_earned + ?, total_earned = total_earned + ? 
                WHERE id = ?
            ");
            $updateSellerStmt->execute([$seller_earnings, $seller_earnings, $seller_earnings, $dump['seller_id']]);

            $insertActivityLogStmt = $pdo->prepare("
                INSERT INTO activity_log (user_id, user_name, item_id, buy_itm, item_price, item_type, created_at) 
                VALUES (?, ?, ?, ?, ?, 'Dumps', NOW())
            ");
            $insertActivityLogStmt->execute([$buyer_id, $_SESSION['username'], $dump['id'], 'transaction_did=' . $dump['id'], $dump['price']]);
        }

        $pdo->commit();

        unset($_SESSION['cards'], $_SESSION['dumps']);

        // Build a success message; include info on any items skipped, if needed.
        $successMessage = "Purchase successful for available items.";
        if (!empty($skippedMessages)) {
            $successMessage .= " The following items were skipped: " . implode(" ", $skippedMessages);
        }

        $response['success'] = true;
        if (!empty($validCards) && !empty($validDumps)) {
            $response['message'] = "Purchase successful. Please visit the 'My Cards' and 'My Dumps' sections to view your purchased items. " . $successMessage;
        } elseif (!empty($validCards)) {
            $response['message'] = "Purchase successful. Please visit the 'My Cards' section to view your purchased cards. " . $successMessage;
        } elseif (!empty($validDumps)) {
            $response['message'] = "Purchase successful. Please visit the 'My Dumps' section to view your purchased dumps. " . $successMessage;
        }
    } else {
        $pdo->rollBack();
        $response['message'] = 'Not enough balance to complete the purchase.';
    }
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Transaction failed in checkout.php: " . $e->getMessage());
    $response['message'] = 'Transaction failed. Please try again. Error: ' . $e->getMessage();
}

echo json_encode($response);
exit();
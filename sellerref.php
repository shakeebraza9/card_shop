<?php
// sellerref.php
require_once __DIR__ . '/global.php'; // Adjust path as needed

/**
 * Reverse the seller's credited earnings for a dead item.
 *
 * @param int    $seller_id   The seller's user ID.
 * @param float  $itemAmount  The price of the item (card/dump).
 * @param PDO    $pdo         The PDO connection.
 * @param string $itemType    'card' or 'dump'
 *
 * @return bool  True on success, false on failure.
 */
function reverseSellerTransaction($seller_id, $itemAmount, $pdo, $itemType = 'card') {
    // Retrieve seller percentage from the users table (default to 100 if not set)
    $stmt = $pdo->prepare("SELECT seller_percentage FROM users WHERE id = ?");
    $stmt->execute([$seller_id]);
    $sellerData = $stmt->fetch();
    $seller_percentage = isset($sellerData['seller_percentage']) ? $sellerData['seller_percentage'] : 100;
    
    // Calculate seller earnings. For example, if price is $10 and percentage is 10%, earnings = $1.
    $seller_earnings = ($itemAmount * $seller_percentage) / 100;
    
    // Choose update query based on item type.
    if (strtolower($itemType) === 'card') {
        $query = "
            UPDATE users 
            SET credit_cards_balance = credit_cards_balance - ?,
                credit_cards_total_earned = credit_cards_total_earned - ?,
                total_earned = total_earned - ?
            WHERE id = ?
        ";
    } else { // dump
        $query = "
            UPDATE users 
            SET dumps_balance = dumps_balance - ?,
                dumps_total_earned = dumps_total_earned - ?,
                total_earned = total_earned - ?
            WHERE id = ?
        ";
    }
    
    $stmt = $pdo->prepare($query);
    return $stmt->execute([$seller_earnings, $seller_earnings, $seller_earnings, $seller_id]);
}

/**
 * Automatically reverse seller transactions for dead cards.
 *
 * This function scans the credit_cards table for rows where cc_status is 'dead' (case-insensitive)
 * and seller_reversed is not set (or is 0), then calls reverseSellerTransaction() for each card.
 * It then updates the seller_reversed flag to 1.
 */
function autoReverseDeadSellerTransactionsCards($pdo) {
    $stmt = $pdo->prepare("SELECT id, price, seller_id, cc_status, seller_reversed FROM credit_cards WHERE LOWER(cc_status) IN ('dead','disable','disabled', 'DISABLED') AND (seller_reversed IS NULL OR seller_reversed = 0)");
    $stmt->execute();
    $cards = $stmt->fetchAll();
    
    foreach ($cards as $card) {
        if (in_array(strtolower($card['cc_status']), ['dead','disable','disabled'])) {
            try {
                $pdo->beginTransaction();
                $cardAmount = $card['price'];
                $seller_id = $card['seller_id'];
                
                if (!reverseSellerTransaction($seller_id, $cardAmount, $pdo, 'card')) {
                    throw new Exception("Failed to reverse seller earnings for card ID: " . $card['id']);
                }
                
                // Mark the card as reversed (using a separate flag).
                $updateStmt = $pdo->prepare("UPDATE credit_cards SET seller_reversed = 1 WHERE id = ?");
                $updateStmt->execute([$card['id']]);
                
                $pdo->commit();
            } catch (Exception $e) {
                $pdo->rollBack();
                // Optionally, handle errors silently.
            }
        }
    }
}

/**
 * Automatically reverse seller transactions for dead dumps.
 *
 * This function scans the dumps table for rows where dump_status is 'dead' (case-insensitive)
 * and seller_reversed is not set (or is 0), then calls reverseSellerTransaction() for each dump.
 * It then updates the seller_reversed flag to 1.
 */
function autoReverseDeadSellerTransactionsDumps($pdo) {
    $stmt = $pdo->prepare("SELECT id, price, seller_id, dump_status, seller_reversed FROM dumps WHERE LOWER(dump_status) IN ('dead','disable','disabled', 'DISABLED') AND (seller_reversed IS NULL OR seller_reversed = 0)");
    $stmt->execute();
    $dumps = $stmt->fetchAll();
    
    foreach ($dumps as $dump) {
        if (in_array(strtolower($dump['dump_status']), ['dead','disable','disabled'])) {

            try {
                $pdo->beginTransaction();
                $dumpAmount = $dump['price'];
                $seller_id = $dump['seller_id'];
                
                if (!reverseSellerTransaction($seller_id, $dumpAmount, $pdo, 'dump')) {
                    throw new Exception("Failed to reverse seller earnings for dump ID: " . $dump['id']);
                }
                
                // Mark the dump as reversed.
                $updateStmt = $pdo->prepare("UPDATE dumps SET seller_reversed = 1 WHERE id = ?");
                $updateStmt->execute([$dump['id']]);
                
                $pdo->commit();
            } catch (Exception $e) {
                $pdo->rollBack();
                // Optionally, handle errors silently.
            }
        }
    }
}

// Automatically run the reversal checks.
autoReverseDeadSellerTransactionsCards($pdo);
autoReverseDeadSellerTransactionsDumps($pdo);



try {
    $pdo->beginTransaction();

   // Process credit cards:
$stmtCards = $pdo->prepare("
SELECT id, seller_id, price 
FROM cncustomer_records 
WHERE status = 'sold' 
  AND (LOWER(cc_status) = 'live' OR cc_status = 'unchecked')
  AND (processed IS NULL OR processed != 1)
");
$stmtCards->execute();
$cards = $stmtCards->fetchAll(PDO::FETCH_ASSOC);


    foreach ($cards as $card) {
        // Get seller percentage for this card
        $stmtSeller = $pdo->prepare("SELECT seller_percentage FROM users WHERE id = ?");
        $stmtSeller->execute([$card['seller_id']]);
        $seller = $stmtSeller->fetch(PDO::FETCH_ASSOC);
        $seller_percentage = isset($seller['seller_percentage']) ? (float)$seller['seller_percentage'] : 100;

        // Calculate seller earnings for the card sale
        $seller_earnings = ($card['price'] * $seller_percentage) / 100;

        // Increment the seller's lifetime earnings
        $stmtUpdateUser = $pdo->prepare("
            UPDATE users 
            SET total_earned = total_earned + ? 
            WHERE id = ?
        ");
        $stmtUpdateUser->execute([$seller_earnings, $card['seller_id']]);

        // Mark this card as processed to avoid double counting
        $stmtMarkCard = $pdo->prepare("UPDATE credit_cards SET processed = 1 WHERE id = ?");
        $stmtMarkCard->execute([$card['id']]);
    }

    // Process dumps:
    $stmtDumps = $pdo->prepare("
        SELECT id, seller_id, price 
        FROM dumps 
        WHERE status = 'sold' 
          AND dump_status IN ('live', 'unchecked')
          AND (processed IS NULL OR processed != 1)
    ");
    $stmtDumps->execute();
    $dumps = $stmtDumps->fetchAll(PDO::FETCH_ASSOC);

    foreach ($dumps as $dump) {
        // Get seller percentage for this dump
        $stmtSeller = $pdo->prepare("SELECT seller_percentage FROM users WHERE id = ?");
        $stmtSeller->execute([$dump['seller_id']]);
        $seller = $stmtSeller->fetch(PDO::FETCH_ASSOC);
        $seller_percentage = isset($seller['seller_percentage']) ? (float)$seller['seller_percentage'] : 100;

        // Calculate seller earnings for the dump sale
        $seller_earnings = ($dump['price'] * $seller_percentage) / 100;

        // Increment the seller's lifetime earnings
        $stmtUpdateUser = $pdo->prepare("
            UPDATE users 
            SET total_earned = total_earned + ? 
            WHERE id = ?
        ");
        $stmtUpdateUser->execute([$seller_earnings, $dump['seller_id']]);

        // Mark this dump as processed to avoid double counting
        $stmtMarkDump = $pdo->prepare("UPDATE dumps SET processed = 1 WHERE id = ?");
        $stmtMarkDump->execute([$dump['id']]);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Lifetime earnings updated successfully.']);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Cron update failed: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
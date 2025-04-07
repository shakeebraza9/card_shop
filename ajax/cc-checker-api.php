<?php
session_start();
// Set CORS and content headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Load global configuration and database connection ($pdo)
require '../global.php'; 

/**
 * Output response in JSON or XML format.
 */
function outputResponse($response, $format = 'JSON') {
    if (strtoupper($format) === 'XML') {
        echo array_to_xml($response);
    } else {
        echo json_encode($response);
    }
}


if (ob_get_level() > 0) {
    ob_get_clean();
}

// Query for the checker_status setting from site_settings (stored as key/value)
$stmt = $pdo->prepare("SELECT value FROM site_settings WHERE `key` = 'checker_status' LIMIT 1");
$stmt->execute();
$siteSettings = $stmt->fetch(PDO::FETCH_ASSOC);

// If the setting is missing or not equal to 1, output an error and exit.
if (!$siteSettings || (int)$siteSettings['value'] !== 1) {
    outputResponse(["error" => "Card checking is currently disabled."]);
    exit;
}

/**
 * Convert array to XML.
 */
function array_to_xml($data, $rootElement = "<response/>") {
    $xml = new SimpleXMLElement($rootElement);
    foreach ($data as $key => $value) {
        $xml->addChild($key, htmlspecialchars("$value"));
    }
    return $xml->asXML();
}




function updateUserBalance($userId, $amount, $pdo) {
    if ($amount < 0) {
        $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $balance = $stmt->fetchColumn();
        if ($balance === false || $balance < abs($amount)) {
            return false;
        }
    }
  
    $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
    return $stmt->execute([$amount, $userId]);
}
function updateUserBalanceMinus($userId, $pdo) {
    $amount = -0.50;
    $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $balance = $stmt->fetchColumn();
    if ($balance === false || $balance < abs($amount)) {
        return false;
    }
    $stmt = $pdo->prepare("UPDATE users SET balance = balance - 0.50 WHERE id = ?");
    return $stmt->execute([$userId]);
}



// Validate required parameters
if (empty($_REQUEST['cardnum']) || empty($_REQUEST['expm']) || empty($_REQUEST['expy'])) {
    outputResponse(["error" => "Missing required parameters"]);
    exit;
}


// Get user id (replace with proper authentication as needed)
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    outputResponse(["error" => "User authentication failed"]);
    exit;
}

$cardnum = $_REQUEST['cardnum'];

// Include the refundable field in the query.
$stmt = $pdo->prepare("SELECT id, price, purchased_at, cc_status, card_number, refundable FROM credit_cards WHERE card_number = ? AND buyer_id = ?");
$stmt->execute([$cardnum, $userId]);
$card = $stmt->fetch();

if (!$card) {
    outputResponse(["error" => "Card not found"]);
    exit;
}



// Normalize the refundable field and block non‑refundable cards.
if (!empty($card['refundable'])) {
    $refundable = strtolower(trim(str_replace(['-', ' '], '', $card['refundable'])));
    if ($refundable === 'nonrefundable' || $refundable === 'notrefundable') {
        outputResponse([
            "error" => "This card is non-refundable and cannot be checked.",
            "status" => "disabled"
        ]);
        exit;
    }
}



// Enforce server-side protection: if card is already marked DISABLED, don't allow check.
if ($card['cc_status'] === 'DISABLED') {
    outputResponse(["error" => "Check function is disabled for this card.", "status" => "disabled"]);
    exit;
}




// NEW: If the card is dead and a user tries to check it again, update its status to DISABLED.
if (strtolower($card['cc_status']) === 'dead') {
    $stmt = $pdo->prepare("UPDATE credit_cards SET cc_status = 'DISABLED', checked_at = NOW() WHERE id = ?");
    $stmt->execute([$card['id']]);
    outputResponse(["error" => "Stop cheating, you will be banned", "status" => "disabled"]);
    exit;
}


// Server-side refundable check: if the refundable period has passed, disable the card.
if (!empty($card['purchased_at']) && !empty($card['refundable'])) {
    if (preg_match('/(\d+)/', $card['refundable'], $m)) {
        $limit = (int)$m[1]; // refundable time in minutes
        $purchaseTime = strtotime($card['purchased_at']);
        if ($purchaseTime !== false && (time() - $purchaseTime > $limit * 60)) {
            // Update cc_status to DISABLED if not already done
            if ($card['cc_status'] !== 'ERROR') {
                $stmt = $pdo->prepare("UPDATE credit_cards SET cc_status = 'ERROR' WHERE id = ?");
                $stmt->execute([$card['id']]);
            }
            outputResponse(["error" => "Check disabled after {$limit} minutes.", "status" => "disabled"]);
            exit;
        }
    }
}

// Determine checker type: if cvv is provided then it's a CC check, otherwise a Dump check.
$checkerType = isset($_REQUEST['cvv']) ? 'CC' : 'Dump';

// Retrieve the mirror setting from the database
$stmt = $pdo->prepare("SELECT value FROM site_settings WHERE `key` = 'Mirror' LIMIT 1");
$stmt->execute();
$mirrorSetting = $stmt->fetch(PDO::FETCH_ASSOC);

// Map mirror keys to URLs
$mirrorUrls = [
    'Mirror1' => 'https://mirror1.luxchecker.vc',
    'Mirror2' => 'https://mirror2.luxchecker.vc',
    'Mirror3' => 'https://mirror3.luxchecker.vc'
];

// If the setting isn't found, default to Mirror1
$selectedMirrorKey = isset($mirrorSetting['value']) ? $mirrorSetting['value'] : 'Mirror1';
$selectedMirrorUrl = isset($mirrorUrls[$selectedMirrorKey]) ? $mirrorUrls[$selectedMirrorKey] : $mirrorUrls['Mirror1'];

// Determine checker type: if cvv is provided then it's a CC check, otherwise a Dump check.
$checkerType = isset($_REQUEST['cvv']) ? 'CC' : 'Dump';

// Build API URL based on selected mirror and checker type
if ($checkerType === 'CC') {
    $api_url = "{$selectedMirrorUrl}/apiv2/ck.php";
} else {
    $api_url = "{$selectedMirrorUrl}/apiv2/dk.php";
}


// Build API request data
$api_data = [
    'cardnum' => $cardnum,
    'expm'    => $_REQUEST['expm'],
    'expy'    => $_REQUEST['expy'],
    'key'     => 'e2f61051f26ca3a0a6beab34953735ac',
    'username'=> 'Mcjic9281'
];
if ($checkerType === 'CC') {
    $api_data['cvv'] = $_REQUEST['cvv'];
}

// Execute API Request via cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($api_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Handle API connection errors
if ($http_status !== 200 || empty($response)) {
    outputResponse(["error" => "Failed to connect to API"]);
    exit;
}

// Decode API response
$response_data = json_decode($response, true);
$status = (isset($response_data['result']) && $response_data['result'] == 1) ? 'LIVE' : 'DEAD';
$response_data['status'] = $status;

if ($status === 'DEAD') {
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("SELECT price FROM credit_cards WHERE id = ?");
        $stmt->execute([$card['id']]);
        $cardAmount = $stmt->fetchColumn(); 
        $stmt = $pdo->prepare("UPDATE credit_cards SET cc_status = 'dead', checked_at = NOW() WHERE id = ?");
        $stmt->execute([$card['id']]);
 
        if ($cardAmount !== false) {
            updateUserBalance($_SESSION['user_id'], $cardAmount, $pdo);
        }
        
        // Log the card check activity
        $logStmt = $pdo->prepare("INSERT INTO card_activity_log (card_id, card_number, status, user_id, date_checked) VALUES (?, ?, ?, ?, NOW())");
        $logStmt->execute([$card['id'], $card['card_number'], $status, $userId]);
        
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error updating card status: " . $e->getMessage());
        outputResponse(["error" => "Error processing refund"]);
        exit;
    }
    outputResponse(["status" => "DEAD", "message" => "Card is dead. Amount refunded."]);
    exit;
} else {
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("UPDATE credit_cards SET cc_status = 'Live', checked_at = NOW() WHERE id = ?");
        $stmt->execute([$card['id']]);
        
        $logStmt = $pdo->prepare("INSERT INTO card_activity_log (card_id, card_number, status, user_id, date_checked) VALUES (?, ?, ?, ?, NOW())");
        $logStmt->execute([$card['id'], $card['card_number'], $status, $userId]);
        
        $pdo->commit();
        updateUserBalanceMinus($_SESSION['user_id'], $pdo);
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error updating card status: " . $e->getMessage());
        outputResponse(["error" => "Error updating card status"]);
        exit;
    }
    outputResponse(["status" => "LIVE", "message" => "Card is live!"]);
    exit;
}




?>

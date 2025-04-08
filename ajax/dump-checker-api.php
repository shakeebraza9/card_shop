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

// Clean any existing output buffers.
if (ob_get_level() > 0) {
    ob_get_clean();
}

// Query for the checker_status setting from site_settings
$stmt = $pdo->prepare("SELECT value FROM site_settings WHERE `key` = 'checker_status' LIMIT 1");
$stmt->execute();
$siteSettings = $stmt->fetch(PDO::FETCH_ASSOC);

// If the setting is missing or not equal to 1, output an error and exit.
if (!$siteSettings || (int)$siteSettings['value'] !== 1) {
    outputResponse(["error" => "Card checking is currently disabled."]);
}

/**
 * Update user's balance.
 * If deducting (i.e. negative amount), ensure sufficient funds.
 */
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

/**
 * Deduct $0.50 from the user's balance.
 */
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

// Validate required parameters for dump check.
if (empty($_REQUEST['track1']) || empty($_REQUEST['expm']) || empty($_REQUEST['expy']) || empty($_REQUEST['pin'])) {
    outputResponse(["error" => "Missing required parameters"]);
}

// Get user id from request.
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    outputResponse(["error" => "User authentication failed"]);
}

// Sanitize input parameters.
$track1 = trim($_REQUEST['track1']);
$expm   = trim($_REQUEST['expm']);
$expy   = trim($_REQUEST['expy']);
$pin    = trim($_REQUEST['pin']);

// Retrieve the purchased dump record from the dumps table.
$stmt = $pdo->prepare("SELECT id, price, purchased_at, dump_status, track1, track2, Refundable FROM dumps WHERE (track1 = ? OR track2 = ?) AND buyer_id = ?");
$stmt->execute([$track1, $track1, $userId]);
$dump = $stmt->fetch();

if (!$dump) {
    outputResponse(["error" => "Dump not found"]);
}

// Normalize the refundable field and block non‑refundable dumps.
if (!empty($dump['Refundable'])) {
    $refundable = strtolower(trim(str_replace(['-', ' '], '', $dump['Refundable'])));
    if ($refundable === 'nonrefundable' || $refundable === 'notrefundable') {
        outputResponse([
            "error"  => "This dump is non-refundable and cannot be checked.",
            "status" => "disabled"
        ]);
    }
}

// Enforce server-side protection: if dump is already marked DISABLED, don't allow check.
if ($dump['dump_status'] === 'DISABLED') {
    outputResponse(["error" => "Check function is disabled for this dump.", "status" => "disabled"]);
}

// NEW: If the dump is dead and a user tries to check it again, update its status to DISABLED.
if (strtolower($dump['dump_status']) === 'dead') {
    $stmt = $pdo->prepare("UPDATE dumps SET dump_status = 'DISABLED', checked_at = NOW() WHERE id = ?");
    $stmt->execute([$dump['id']]);
    outputResponse(["error" => "Stop cheating, you will be banned", "status" => "disabled"]);
}

// Server-side refundable check: if the refundable period has passed, disable the dump.
if (!empty($dump['purchased_at']) && !empty($dump['Refundable'])) {
    if (preg_match('/(\d+)/', $dump['Refundable'], $m)) {
        $limit = (int)$m[1]; // refundable time in minutes
        $purchaseTime = strtotime($dump['purchased_at']);
        if ($purchaseTime !== false && (time() - $purchaseTime > $limit * 60)) {
            if ($dump['dump_status'] !== 'ERROR') {
                $stmt = $pdo->prepare("UPDATE dumps SET dump_status = 'ERROR' WHERE id = ?");
                $stmt->execute([$dump['id']]);
            }
            outputResponse(["error" => "Check disabled after {$limit} minutes.", "status" => "disabled"]);
        }
    }
}

// Determine checker type: if verification_code is provided then it's a CC check, otherwise a Dump check.
$checkerType = isset($_REQUEST['verification_code']) ? 'CC' : 'Dump';

// API Mirrors
$mirrors = [
    'https://mirror1.luxchecker.vc',
    'https://mirror2.luxchecker.vc'
];

// Select API mirror based on checker type.
$api_url = ($checkerType === 'CC') ? "{$mirrors[0]}/apiv2/ck.php" : "{$mirrors[0]}/apiv2/dk.php";

// Build API request data.
$api_data = [
    'track1'  => $track1,
    'expm'    => $expm,
    'expy'    => $expy,
    'pin'     => $pin,
    'key'     => 'e2f61051f26ca3a0a6beab34953735ac',
    'username'=> 'Mcjic9281'
];
if ($checkerType === 'CC') {
    $api_data['verification_code'] = $_REQUEST['verification_code'];
}

// Execute API Request via cURL.
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($api_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Handle API connection errors.
if ($http_status !== 200 || empty($response)) {
    outputResponse(["error" => "Failed to connect to API"]);
}

// Decode API response.
$response_data = json_decode($response, true);
$status = (isset($response_data['result']) && $response_data['result'] == 1) ? 'LIVE' : 'DEAD';
$response_data['status'] = $status;

if ($status === 'DEAD') {
    try {
        $pdo->beginTransaction();
        
        // Update dump status to dead and record the check time.
        $stmt = $pdo->prepare("UPDATE dumps SET dump_status = 'dead', checked_at = NOW() WHERE id = ?");
        $stmt->execute([$dump['id']]);
        
        // Refund the dump's price.
        updateUserBalance($userId, $dump['price'], $pdo);
        
        // Log the dump check activity.
        $dumpTrack = $dump['track1'] ?? $dump['track2'];
        $logStmt = $pdo->prepare("INSERT INTO dumps_activity_log (dump_id, track1, buyer_id, date_checked, status) VALUES (?, ?, ?, NOW(), ?)");
        $logStmt->execute([$dump['id'], $dumpTrack, $userId, $status]);
        
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error updating dump status: " . $e->getMessage());
        outputResponse(["error" => "Error processing refund"]);
    }
    outputResponse(["status" => "DEAD", "message" => "Dump is dead. Amount refunded."]);
} else {
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("UPDATE dumps SET dump_status = 'Live', checked_at = NOW() WHERE id = ?");
        $stmt->execute([$dump['id']]);
        
        $logStmt = $pdo->prepare("INSERT INTO dumps_activity_log (dump_id, track1, buyer_id, date_checked, status) VALUES (?, ?, ?, NOW(), ?)");
        $logStmt->execute([$dump['id'], $dump['track1'] ?? $dump['track2'], $userId, $status]);
        
        $pdo->commit();
        updateUserBalanceMinus($userId, $pdo);
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error updating dump status: " . $e->getMessage());
        outputResponse(["error" => "Error updating dump status"]);
    }
    outputResponse(["status" => "LIVE", "message" => "Dump is live!"]);
}
?>

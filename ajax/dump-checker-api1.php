<?php
session_start();
// Set CORS and content headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Load global configuration and database connection ($pdo)
require __DIR__ . '/../global.php';



/**
 * Output response in JSON format.
 */
function outputResponse($response, $format = 'JSON') {
    echo json_encode($response);
}

// Validate required parameters for dump check: track1, expm, expy, and pin
if (empty($_REQUEST['track1']) || empty($_REQUEST['expm']) || empty($_REQUEST['expy']) || empty($_REQUEST['pin'])) {
    outputResponse(["error" => "Missing required parameters"]);
    exit;
}

// Get buyer_id from session
$buyerId = $_SESSION['user_id'];
if (!$buyerId) {
    outputResponse(["error" => "Buyer ID not provided"]);
    exit;
}

$track1 = trim($_REQUEST['track1']);
$expm   = trim($_REQUEST['expm']);
$expy   = trim($_REQUEST['expy']);
$pin    = trim($_REQUEST['pin']);

// Retrieve the purchased dump record from the dumps table
$stmt = $pdo->prepare("SELECT id, price, purchased_at, dump_status, track1, track2, Refundable, luxchecker FROM dumps WHERE (track1 = ? OR track2 = ?) AND buyer_id = ?");
$stmt->execute([$track1, $track1, $buyerId]);
$dump = $stmt->fetch();

if (!$dump) {
    outputResponse(["error" => "Dump not found"]);
    exit;
}

if ($dump['luxchecker'] != 1) {
    outputResponse(["error" => "Sorry The Dump is Non-Refundable"]);
    exit;
}


// Normalize the refundable field and block non-refundable dumps.
if (!empty($dump['Refundable'])) {
    $refundable = strtolower(trim(str_replace(['-', ' '], '', $dump['Refundable'])));
    if ($refundable === 'nonrefundable' || $refundable === 'notrefundable') {
        outputResponse([
            "error" => "This dump is non-refundable and cannot be checked.",
            "status" => "disabled"
        ]);
        exit;
    }
}

// Enforce server-side protection: if dump_status is DISABLED, block the check.
if ($dump['dump_status'] === 'DISABLED') {
    outputResponse(["error" => "Check function is disabled for this dump.", "status" => "disabled"]);
    exit;
}

// If the dump is already dead, update its status to DISABLED to block further checks.
if (strtolower($dump['dump_status']) === 'dead') {
    $stmt = $pdo->prepare("UPDATE dumps SET dump_status = 'DISABLED', checked_at = NOW() WHERE id = ?");
    $stmt->execute([$dump['id']]);
    outputResponse(["error" => "Stop cheating, you will be banned", "status" => "disabled"]);
    exit;
}

// Check refundable time limit if purchased_at is set and Refundable is provided.
if (!empty($dump['purchased_at']) && !empty($dump['Refundable'])) {
    if (preg_match('/(\d+)/', $dump['Refundable'], $m)) {
        $refundLimit = (int)$m[1]; // refundable time in minutes
        $purchaseTime = strtotime($dump['purchased_at']);
        if ($purchaseTime !== false && (time() - $purchaseTime > $refundLimit * 60)) {
            // Update dump_status to DISABLED if refund period has passed.
            $stmt = $pdo->prepare("UPDATE dumps SET dump_status = 'DISABLED', checked_at = NOW() WHERE id = ?");
            $stmt->execute([$dump['id']]);
            outputResponse(["error" => "Check disabled after {$refundLimit} minutes.", "status" => "disabled"]);
            exit;
        }
    }
}

// API Mirrors for dumps
$mirrors = [
    'https://mirror1.luxchecker.vc',
    'https://mirror2.luxchecker.vc'
];
$api_url = $mirrors[0] . "/apiv2/dk.php";

// Build API request data
$api_data = [
    'track1' => $track1,
    'expm'   => $expm,
    'expy'   => $expy,
    'pin'    => $pin,
    'key'    => 'e2f61051f26ca3a0a6beab34953735ac',
    'username' => 'Mcjic9281'
];

// Execute API Request via cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($api_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_status !== 200 || empty($response)) {
    outputResponse(["error" => "Failed to connect to API"]);
    exit;
}

$response_data = json_decode($response, true);
$status = (isset($response_data['result']) && $response_data['result'] == 1) ? 'LIVE' : 'DEAD';
$response_data['status'] = $status;

try {
    $pdo->beginTransaction();

    if ($status === 'DEAD') {
        // If dump is dead, refund the buyer by adding the dump price back.
        updateUserBalance($buyerId, $dump['price'], $pdo);
    }
    // Deduct a fixed amount for checking (for example, 0.50)
    updateUserBalanceMinus($buyerId, $pdo);

    $stmt = $pdo->prepare("UPDATE dumps SET dump_status = ?, checked_at = NOW() WHERE id = ?");
    $stmt->execute([$status, $dump['id']]);

    $logStmt = $pdo->prepare("INSERT INTO dumps_activity_log (dump_id, track1, buyer_id, date_checked, status) VALUES (?, ?, ?, NOW(), ?)");
    $dumpTrack = !empty($dump['track1']) ? $dump['track1'] : $dump['track2'];
    $logStmt->execute([$dump['id'], $dumpTrack, $buyerId, $status]);

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Error updating dump status: " . $e->getMessage());
    outputResponse(["error" => "Error updating dump status: " . $e->getMessage()]);
    exit;
}

outputResponse($response_data);
exit;
?>

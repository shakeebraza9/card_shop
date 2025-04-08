<?php
ob_start();
session_start();
require 'config.php';

// Capture filter values from POST data
$dumpBin = isset($_POST['dump_bin']) ? trim($_POST['dump_bin']) : '';
$dumpCountry = isset($_POST['dump_country']) ? trim($_POST['dump_country']) : '';
$dumpType = isset($_POST['dump_type']) ? trim($_POST['dump_type']) : 'all';
$dumpPin = isset($_POST['dump_pin']) ? trim($_POST['dump_pin']) : 'all';

// SQL query to fetch unsold dmptransaction_data with filtering options
$sql = "SELECT id, data_segment_one, data_segment_two, ex_mm, ex_yy, pin, payment_method_type, price, country 
        FROM dmptransaction_data 
        WHERE buyer_id IS NULL AND status = 'unsold'";
$params = [];

// Apply filters if provided
if (!empty($dumpBin)) {
    $bins = array_map('trim', explode(',', $dumpBin));
    $sql .= " AND (" . implode(" OR ", array_fill(0, count($bins), "data_segment_two LIKE ?")) . ")";
    foreach ($bins as $bin) {
        $params[] = $bin . '%';
    }
}
if (!empty($dumpCountry)) {
    $sql .= " AND UPPER(TRIM(country)) = ?";
    $params[] = strtoupper(trim($dumpCountry));
}
if ($dumpType !== 'all') {
    $sql .= " AND payment_method_type = ?";
    $params[] = $dumpType;
}
if ($dumpPin === 'yes') {
    $sql .= " AND pin IS NOT NULL";
} elseif ($dumpPin === 'no') {
    $sql .= " AND pin IS NULL";
}

// Order results and prepare statement
$sql .= " ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$dumps = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Add image path for each dump type
foreach ($dumps as &$dump) {
    $dumpType = strtolower($dump['payment_method_type']);
    $dump['image_path'] = "https://cardvault.club/shop2/shop/images/cards/{$dumpType}.png";
}

header('Content-Type: application/json');
echo json_encode($dumps);

ob_end_flush();
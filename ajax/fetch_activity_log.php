<?php
header('Content-Type: application/json');

// Include global settings and ensure no output is generated before JSON output.
require '../global.php';

// Retrieve DataTables parameters
$draw            = isset($_GET['draw']) ? $_GET['draw'] : 1;
$start           = isset($_GET['start']) ? $_GET['start'] : 0;
$length          = isset($_GET['length']) ? $_GET['length'] : 10;
$searchValue     = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';
$orderColumnIndex= isset($_GET['order'][0]['column']) ? $_GET['order'][0]['column'] : 0;
$orderDir        = isset($_GET['order'][0]['dir']) ? $_GET['order'][0]['dir'] : 'asc';
$itemType        = isset($_GET['item_type']) ? $_GET['item_type'] : '';

// Define allowed columns for ordering – make sure these match your DataTables columns.
$columns = ['user_name', 'buy_itm', 'item_price', 'item_type', 'created_at'];

// Ensure that the order column index is valid:
$orderColumn = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : $columns[0];

$query = "SELECT * FROM activity_log WHERE 1";

// Append search conditions if provided.
if ($searchValue) {
    $query .= " AND (user_name LIKE :search OR buy_itm LIKE :search OR item_price LIKE :search OR item_type LIKE :search)";
}

if ($itemType) {
    $query .= " AND item_type = :item_type";
}

$query .= " ORDER BY $orderColumn $orderDir LIMIT :start, :length";

$stmt = $pdo->prepare($query);

if ($searchValue) {
    $stmt->bindValue(':search', "%$searchValue%", PDO::PARAM_STR);
}

if ($itemType) {
    $stmt->bindValue(':item_type', $itemType, PDO::PARAM_STR);
}

$stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
$stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);

$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Run a count query. You might want two counts:
// 1. Total records without filtering.
// 2. Records after filtering (for recordsFiltered).
$totalQuery = "SELECT COUNT(*) FROM activity_log";
$totalStmt = $pdo->prepare($totalQuery);
$totalStmt->execute();
$totalRecords = $totalStmt->fetchColumn();

// For recordsFiltered, include the same WHERE conditions as your main query (except LIMIT and ORDER BY).
$filteredQuery = "SELECT COUNT(*) FROM activity_log WHERE 1";
if ($searchValue) {
    $filteredQuery .= " AND (user_name LIKE :search OR buy_itm LIKE :search OR item_price LIKE :search OR item_type LIKE :search)";
}
if ($itemType) {
    $filteredQuery .= " AND item_type = :item_type";
}
$filteredStmt = $pdo->prepare($filteredQuery);
if ($searchValue) {
    $filteredStmt->bindValue(':search', "%$searchValue%", PDO::PARAM_STR);
}
if ($itemType) {
    $filteredStmt->bindValue(':item_type', $itemType, PDO::PARAM_STR);
}
$filteredStmt->execute();
$recordsFiltered = $filteredStmt->fetchColumn();

$response = [
    'draw'            => intval($draw),
    'recordsTotal'    => intval($totalRecords),
    'recordsFiltered' => intval($recordsFiltered),
    'data'            => $data
];

echo json_encode($response);

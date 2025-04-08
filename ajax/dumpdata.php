<?php
include_once('../global.php'); 
session_start();

// Get POST variables
$filters = [
    'dump_bin' => isset($_POST['dump_bin']) ? trim($_POST['dump_bin']) : '',
    'dump_country' => isset($_POST['dump_country']) ? trim($_POST['dump_country']) : '',
    'dump_type' => isset($_POST['dump_type']) ? trim($_POST['dump_type']) : 'all',
    'dump_pin' => isset($_POST['dump_pin']) ? trim($_POST['dump_pin']) : 'all',
    'base_name' => isset($_POST['base_name']) ? trim($_POST['base_name']) : 'all',
    'code' => isset($_POST['code']) ? trim($_POST['code']) : 'all',
    'track_pin' => isset($_POST['track_pin']) ? trim($_POST['track_pin']) : 'all',
    'Refundable' => isset($_POST['Refundable']) ? trim($_POST['Refundable']) : 'all',
];

$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;

// Fetch filtered data
$result = $settings->fetchFilteredData($filters, $start, $length);
$data = [];

foreach ($result['data'] as $dump) {
    $data[] = [
        'card_logo'   => '<div class="card-logo-wrapper"><img src="'.$urlval.'images/cards/' . strtolower($dump['payment_method_type']) . '.png" alt="' . htmlspecialchars($dump['payment_method_type']) . '" class="card-logo"></div>',
        'data_segment_two'      => htmlspecialchars(substr($dump['data_segment_two'], 0, 6)),
        'expiry'      => htmlspecialchars($dump['ex_mm'] . '/' . $dump['ex_yy']),
        'code'        => isset($dump['code']) && !is_null($dump['code']) ? substr($dump['code'], 0, 3) : '',
        'pin'         => !empty($dump['pin']) ? 'Yes' : 'No',
        'track'       => !empty($dump['data_segment_one']) ? 'Yes' : 'No',
        'country'     => htmlspecialchars($dump['country']),
        'base_name' => isset($dump['base_name']) ? htmlspecialchars($dump['base_name']) : '',
    'Refundable' => isset($dump['Refundable']) ? htmlspecialchars($dump['Refundable']) : '',
        'price'       => '$' . htmlspecialchars($dump['price']),
        'actions'     => '<div class="action-buttons">
                            <a href="#" class="buy-button" style="background-color:#0c182f;" onclick="showConfirm(\'' . $dump['id'] . '\', \'' . $dump['price'] . '\')">
                                <span class="price">$' . htmlspecialchars($dump['price']) . '</span>
                                <span class="buy-now">Buy Now</span>
                            </a>
                            <button class="add-to-cart-button" style="background-color:#6c5ce7; color:#fff; margin-left:10px; border:none; padding:5px 10px; border-radius:5px; cursor:pointer;" 
                                onclick="addToDump(\'' . $dump['id'] . '\')">
                                <span class="button-text">Add to Cart</span>
                                <i class="card-icon fas fa-shopping-cart"></i>
                            </button>
                        </div>',
    ];
}



// Return JSON response
echo json_encode([
    'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
    'recordsTotal' => $result['totalRecords'],
    'recordsFiltered' => $result['totalRecords'],
    'data' => $data,
]);
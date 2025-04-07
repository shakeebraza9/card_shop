<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once('../global.php'); 
session_start();

function getCardType($card_number) {
    $card_number = preg_replace('/\D/', '', $card_number);
    if (preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/', $card_number)) return 'Visa';
    if (preg_match('/^5[1-5][0-9]{14}$/', $card_number) || preg_match('/^2(2[2-9]|[3-6][0-9]|7[01])[0-9]{12}$/', $card_number)) return 'Mastercard';
    if (preg_match('/^3[47][0-9]{13}$/', $card_number)) return 'Amex';
    if (preg_match('/^6(?:011|5[0-9]{2}|4[4-9][0-9]|22[1-9][0-9]|622[1-9][0-9]{1,2})[0-9]{12}$/', $card_number)) return 'Discover';
    return 'visa';
}

function formatCardData($creditCards) {
    $formattedData = [];
    $ative = $_SESSION['active'] === 0 ? 'disabled' : '';

    foreach ($creditCards as $card) {
        $otherinfo = (!empty($card['otherinfo']) && $card['otherinfo'] != 'NA' && $card['otherinfo'] != 'No') ? 'Yes' : 'No';

        if (is_null($card['card_type'])) {
            $cardType = getCardType($card['creference_code'] ?? '');
            $cardimg = strtolower($cardType);
        } else { 
            $cardimg = strtolower($card['card_type']);
        }
        
        $formattedData[] = [
            'card_logo'   => '<div class="card-logo-wrapper"><img src="https://cardvault.club/shop2/shop3/images/cards/' . $cardimg . '.png" alt="Card Logo" class="card-logo"></div>',
            'email'       => htmlspecialchars($card['email'] ?? 'NA'),
            'phone_number'       => htmlspecialchars($card['phone_number'] ?? 'NA'),
            'mmn'       => htmlspecialchars($card['mmn'] ?? 'NA'),
            'date_of_birth'       => htmlspecialchars($card['date_of_birth'] ?? 'NA'),
            'creference_code' => htmlspecialchars(substr($card['creference_code'] ?? '', 0, 6)),
            'expiry'      => htmlspecialchars($card['ex_mm'] ?? '') . '/' . htmlspecialchars($card['yyyy_exp'] ?? ''),
            'country'     => htmlspecialchars($card['country'] ?? ''),
            'state'       => htmlspecialchars($card['state'] ?? ''),
            'city'        => htmlspecialchars($card['city'] ?? ''),
            'base_name'   => htmlspecialchars($card['base_name'] ?? ''),
            'zip'         => substr($card['zip'] ?? '', 0, 3) . '****',
            'price'       => '$' . htmlspecialchars($card['price'] ?? ''),
            'otherinfo'   => $otherinfo,
            'refundable'  => htmlspecialchars($card['refundable'] ?? ''),
            'actions'     => '
                <div class="action-buttons">
                    <a href="#" class="buy-button ' . $ative . '" style="background-color:#0c182f;" onclick="showConfirm(\'' . $card['id'] . '\', \'' . $card['price'] . '\')">
                        <span class="price">$' . htmlspecialchars($card['price']) . '</span>
                        <span class="buy-now">Buy Now</span>
                    </a>
                    <button class="add-to-cart-button ' . $ative . '" style="background-color:#6c5ce7; color:#fff; margin-left:10px; border:none; padding:5px 10px; border-radius:5px; cursor:pointer;" 
                            onclick="addToCart(\'' . $card['id'] . '\')">
                        <span class="button-text">Add to Cart</span>
                        <i class="card-icon fas fa-shopping-cart"></i>
                    </button>
                </div>'
        ];
    }

    return $formattedData;
}

// Fetch the data with filters and pagination
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;

$filters = [
    'cc_bin'     => $_POST['cc_bin'] ?? '',
    'cc_country' => $_POST['cc_country'] ?? '',
    'cc_state'   => $_POST['cc_state'] ?? '',
    'cc_city'    => $_POST['cc_city'] ?? '',
    'cc_zip'     => $_POST['cc_zip'] ?? '',
    'cc_type'    => $_POST['cc_type'] ?? 'all',
    'basename'   => $_POST['basename'] ?? 'all',
    'refundable' => $_POST['refundable'] ?? ''
];

// Uncomment to debug filters (check debug.txt after AJAX call)
// file_put_contents('debug.txt', print_r($filters, true));

$response = $settings->getCreditCardData($start, $length, $filters);
$formattedData = formatCardData($response['data']);

echo json_encode([
    'draw'            => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
    'recordsTotal'    => $response['totalRecords'],
    'recordsFiltered' => $response['totalRecords'],
    'data'            => $formattedData
]);
?>
<?php
require '../global.php';

// Get the data from the AJAX request
$itemId = $_GET['item_id'];
$itemType = $_GET['item_type'];

// Default response
$response = '';

// Query to fetch additional data based on item type and ID
if ($itemType === 'Leads' || $itemType === 'Pages' || $itemType === 'Tools') {
    // You can add more specific queries depending on your requirements
    $query = "SELECT * FROM uploads WHERE id = :item_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':item_id', $itemId, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch the data
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        // Format the response as needed
        $response = 'Name: ' . $data['name'] . ', Description: ' . $data['description'];
    } else {
        $response = 'No additional data found.';
    }
} elseif ($itemType === 'Cards') {
    // Handle the "BHE" cards query
    $query = "SELECT * FROM cncustomer_records WHERE id = :item_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':item_id', $itemId, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch the data
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        // Format the response with all card details
        $response = 'Card Number: ' . $data['creference_code'] . 
                    ', Expiry: ' . $data['ex_mm'] . '/' . $data['ex_yy'] .
                    ', verification_code: ' . $data['verification_code'] . 
                    ', Name on Card: ' . $data['billing_name'] ;
    } else {
        $response = 'No Cards data found.';
    }
}elseif ($itemType === 'Dumps') {
  
    $query = "SELECT * FROM dmptransaction_data WHERE  id = :item_id";
    $stmt = $pdo->prepare($query);
    
    $stmt->bindValue(':item_id', $itemId, PDO::PARAM_INT);
    $stmt->execute();


    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
      
        $response = 'data_segment_two: ' . $data['data_segment_two'] . ', Code: ' . $data['code'] . ', data_segment_two: ' . $data['data_segment_two'] . ', Expiration: ' . $data['ex_mm'] . '/' . $data['ex_yy'];
    } else {
        $response = 'No other Dumps data found.';
    }
}



echo $response;
?>
<?php
header('Content-Type: application/json; charset=UTF-8');

session_start();
require '../global.php';

if (!isset($_SESSION['cards'])) {
    $_SESSION['cards'] = [];
}
if (!isset($_SESSION['dumps'])) {
    $_SESSION['dumps'] = [];
}

$input = json_decode(file_get_contents('php://input'), true);
$cardId = $input['cardId'] ?? null;
$type = $input['type'] ?? null;

if ($cardId && $type) {
    try {

        if ($type == 'card') {
            $stmt = $pdo->prepare("SELECT id, creference_code, billing_name, price, payment_method_type FROM cncustomer_records WHERE id = :cardId");
        } elseif ($type == 'dump') {
            $stmt = $pdo->prepare("SELECT id, data_segment_one, data_segment_two, ex_mm, ex_yy, pin, price, country, base_name, seller_name, status, payment_method_type FROM dmptransaction_data WHERE id = :cardId");
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid type.']);
            exit;
        }

        $stmt->bindParam(':cardId', $cardId, PDO::PARAM_INT);
        $stmt->execute();

        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($item) {
            
            if ($type == 'dump') {
                
                $cardtpe=$item['payment_method_type']??'visa';
                $itemData = [
                    'id' => $item['id'],
                    'bin' => substr($item['data_segment_two'], 0, 6), 
                    'price' => $item['price'],
                    'image' => '/shop/images/cards/' . strtolower($cardtpe) . '.png',
                    'type' => 'dump'
                ];
          
                if (isset($_SESSION['dumps'][$cardId])) {
                    $_SESSION['dumps'][$cardId] = $itemData; 
                } else {
                    $_SESSION['dumps'][$cardId] = $itemData;
                }
            } else {
                $cardtpe=$item['payment_method_type']??'visa';
                $itemData = [
                    'id' => $item['id'],
                    'bin' => substr($item['creference_code'], 0, 6), 
                    'price' => $item['price'],
                    'image' => '/shop/images/cards/' . strtolower($cardtpe) . '.png',
                    'type' => 'card'
                ];
           
                if (isset($_SESSION['cards'][$cardId])) {
                    $_SESSION['cards'][$cardId] = $itemData; 
                } else {
                    $_SESSION['cards'][$cardId] = $itemData; 
                }
            }

            $cardTotal = array_sum(array_column($_SESSION['cards'], 'price')) ?? 0;
            $dumpTotal = array_sum(array_column($_SESSION['dumps'], 'price')) ?? 0;
            $total = $cardTotal + $dumpTotal;

            foreach ($_SESSION['cards'] as &$card) {
                $card['bin'] = htmlspecialchars($card['bin'], ENT_QUOTES, 'UTF-8');
            }
            
            foreach ($_SESSION['dumps'] as &$dump) {
                $dump['bin'] = htmlspecialchars($dump['bin'], ENT_QUOTES, 'UTF-8');
            }
            echo json_encode([
                'success' => true,
                'cards' => array_values($_SESSION['cards']??[]),
                'dumps' => array_values($_SESSION['dumps']??[]),
                'total' =>  $total, 
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Item not found.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Item ID or type is missing.']);
}



?>
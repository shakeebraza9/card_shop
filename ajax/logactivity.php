<?php
session_start();
include_once('../../db_connection.php'); // your DB connection file

if (isset($_POST['cardId'], $_POST['cardNumber'], $_POST['status'])) {
    $cardId = $_POST['cardId'];
    $cardNumber = $_POST['cardNumber'];
    $status = $_POST['status'];
    $user_id = $_SESSION['user_id'];
    $date_checked = date("Y-m-d H:i:s");

    $stmt = $pdo->prepare("INSERT INTO card_activity_log (card_id, creference_code, status, user_id, date_checked) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$cardId, $cardNumber, $status, $user_id, $date_checked])) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Could not log activity"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Missing parameters"]);
}
?>
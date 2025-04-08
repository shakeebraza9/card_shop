<?php
// my_dumps.php
session_start();
include_once('../../config.php');  // Defines $pdo, $encryptionKey, $urlval, etc.
include_once('../../global.php');  // Any additional global settings

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: {$urlval}login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Log the user ID for debugging
error_log("Fetching sold dumps for user_id: " . $user_id);

// Build and execute the query to fetch sold dumps with decrypted fields
try {
    // Quote the encryption key for use in the SQL query
    $quotedKey = $pdo->quote($encryptionKey);

    // Build the query: note we use CONVERT(AES_DECRYPT(...)) to get UTF-8 text.
    $sql = "
      SELECT 
        id,
        CONVERT(AES_DECRYPT(data_segment_one, $quotedKey) USING utf8) AS data_segment_one,
        CONVERT(AES_DECRYPT(data_segment_two, $quotedKey) USING utf8) AS data_segment_two,
        ex_mm,
        ex_yy,
        pin,
        payment_method_type,
        price,
        country,
        purchased_at
      FROM dmptransaction_data 
      WHERE buyer_id = ? AND status = 'sold'
      ORDER BY purchased_at DESC
    ";

    $stmt = $pdo->prepare($sql);
    if (!$stmt->execute([$user_id])) {
        $errorInfo = $stmt->errorInfo();
        error_log("Sold dumps query error: " . implode(" - ", $errorInfo));
    }
    $soldDumps = $stmt->fetchAll(PDO::FETCH_ASSOC);

    error_log("Found " . count($soldDumps) . " sold dumps for user_id: " . $user_id);

} catch (Exception $e) {
    error_log("Exception in sold dumps query: " . $e->getMessage());
    $soldDumps = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Dumps</title>
    <style>
    /* Basic styling for demonstration */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th,
    td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: left;
    }

    th {
        background-color: #0c182f;
        color: #fff;
    }
    </style>
</head>

<body>
    <h2>My Purchased Dumps</h2>
    <?php if (empty($soldDumps)): ?>
    <p>No purchased dumps available.</p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Track 1</th>
                <th>Track 2</th>
                <th>Expiry</th>
                <th>PIN</th>
                <th>Card Type</th>
                <th>Price</th>
                <th>Country</th>
                <th>Purchased At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($soldDumps as $dump): ?>
            <tr>
                <td><?php echo htmlspecialchars($dump['id']); ?></td>
                <td><?php echo htmlspecialchars($dump['data_segment_one']); ?></td>
                <td><?php echo htmlspecialchars($dump['data_segment_two']); ?></td>
                <td><?php echo htmlspecialchars($dump['ex_mm'] . '/' . $dump['ex_yy']); ?></td>
                <td><?php echo htmlspecialchars($dump['pin']); ?></td>
                <td><?php echo htmlspecialchars($dump['payment_method_type']); ?></td>
                <td><?php echo '$' . htmlspecialchars($dump['price']); ?></td>
                <td><?php echo htmlspecialchars($dump['country']); ?></td>
                <td><?php echo htmlspecialchars($dump['purchased_at']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</body>

</html>
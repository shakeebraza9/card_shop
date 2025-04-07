<?php
// Database connection
include_once('../../config.php');

// Fetch all sellers and their actual balance
$stmt = $pdo->prepare("SELECT id, username, (credit_cards_balance + dumps_balance) AS actual_balance FROM users WHERE seller = 1");
$stmt->execute();
$sellers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sellers Balance Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            max-width: 800px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background-color: #218838;
        }
        input[type='number'] {
            width: 80px;
            padding: 5px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
            -moz-appearance: textfield;
        }
        input[type='number']::-webkit-outer-spin-button,
        input[type='number']::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            z-index: 1000;
        }
        .popup p {
            font-size: 18px;
        }
        .popup p strong {
            font-weight: bold;
            color: #007bff;
        }
        .popup button {
            margin-top: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="overlay" id="overlay"></div>
    <div class="container">
        <h2>Sellers Balance Management</h2>
        <table>
            <thead>
                <tr>
                    <th>Seller</th>
                    <th>Actual Balance ($)</th>
                    <th>Update Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sellers as $seller): ?>
                <tr>
                    <td><?php echo htmlspecialchars($seller['username']); ?></td>
                    <td><input type="number" id="balance_<?php echo $seller['id']; ?>" value="<?php echo number_format($seller['actual_balance'], 2); ?>"></td>
                    <td>
                        <button onclick="showConfirmationPopup(<?php echo $seller['id']; ?>, '<?php echo htmlspecialchars($seller['username']); ?>', <?php echo number_format($seller['actual_balance'], 2); ?>)">Update</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div id="confirmation-popup" class="popup">
        <p id="confirmation-message"></p>
        <button onclick="proceedUpdate()">Yes</button>
        <button onclick="cancelUpdate()">No</button>
    </div>

    <div id="popup" class="popup">
        <p id="popup-message"></p>
        <button onclick="refreshPage()">OK</button>
    </div>

    <script>
        let pendingSellerId, pendingSellerName, pendingNewBalance, pendingOldBalance;

        function showConfirmationPopup(sellerId, sellerName, oldBalance) {
            pendingSellerId = sellerId;
            pendingSellerName = sellerName;
            pendingOldBalance = oldBalance;
            pendingNewBalance = document.getElementById('balance_' + sellerId).value;
            
            if (pendingNewBalance === "" || isNaN(pendingNewBalance)) {
                alert("Please enter a valid balance amount.");
                return;
            }
            
            document.getElementById('confirmation-message').innerHTML = `Are you sure you want to change the actual balance of <strong>${sellerName}</strong> from <strong>$${oldBalance}</strong> to <strong>$${pendingNewBalance}</strong>?`;
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('confirmation-popup').style.display = 'block';
        }

        function proceedUpdate() {
            document.getElementById('confirmation-popup').style.display = 'none';
            
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "update_balance.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById('popup-message').innerHTML = `The amount for <strong>${pendingSellerName}</strong> has changed from <strong>$${pendingOldBalance}</strong> to <strong>$${pendingNewBalance}</strong>`;
                    document.getElementById('popup').style.display = 'block';
                }
            };
            xhr.send("sellerId=" + pendingSellerId + "&newBalance=" + pendingNewBalance);
        }

        function cancelUpdate() {
            location.reload();
        }

        function refreshPage() {
            location.reload();
        }
    </script>
</body>
</html>
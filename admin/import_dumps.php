<?php


require '../config.php'; // Include your PDO-based config



// Initialize the message variables to avoid undefined variable warnings
$successMessage = '';
$duplicateMessage = '';
$errorMessage = '';

$importedCount = 0;
$duplicateCount = 0; // Track the number of duplicates

// Fetch sellers (users where seller = 1)
try {
    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE seller = 1");
    $stmt->execute();
    $sellers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorMessage = 'Error fetching sellers: ' . $e->getMessage();
}

function getCardType($card_number) {
    // Check if the card number is in Track 1 format, e.g.:
    // %B4000003830885892^DOE/JOHN^2401121000000000000000000000000?
    if (preg_match('/^%[A-Z](\d+)\^/', $card_number, $matches)) {
        $numeric = $matches[1]; // Extracted card number without the sentinel or name info
    } else {
        // Otherwise, strip out non-digit characters
        $numeric = preg_replace('/\D/', '', $card_number);
    }

    $patterns = [
        'Visa'        => '/^4\d{12,18}$/',
        'mastercard'  => '/^(5[1-5]\d{14}|222[1-9]\d{12}|22[3-9]\d{13}|2[3-6]\d{14}|27[01]\d{13}|2720\d{12})$/',
        'amex'        => '/^3[47]\d{13}$/', // 15 digits
        'discover'    => '/^(6011\d{12}|65\d{14}|64[4-9]\d{13}|6221[2-9]\d{10}|622[2-8]\d{10}|6229[01]\d{10}|62292[0-5]\d{10})$/',
        'jcb'         => '/^35(2[89]|[3-8]\d)\d{12}$/',
        'diners'      => '/^(3[0689]\d{12}|30[0-5]\d{11})$/',
        'unionpay'    => '/^62\d{14,18}$/',
        'maestro'     => '/^(50|56|57|58|59|6[0-9])\d{10,16}$/'
    ];

    foreach ($patterns as $type => $pattern) {
        if (preg_match($pattern, $numeric)) {
            return $type;
        }
    }

    return 'N/A';
}


// Handle file upload and form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['import_file']) && $_FILES['import_file']['error'] == 0) {
        $file = $_FILES['import_file']['tmp_name'];
        $data = file_get_contents($file);
    } else {
        $data = $_POST['data'];
    }

    try {
        $seller_id  = $_POST['seller_id'];
        $price      = $_POST['price'];

        $pos_track1 = $_POST['pos_track1'];
        $pos_code   = $_POST['pos_code'];
        $pos_track2 = $_POST['pos_track2'];
        $pos_pin    = $_POST['pos_pin'];
        $pos_country= $_POST['pos_country'];

        // NEW: Set refund status based on checkbox value
        $refund = (isset($_POST['refund']) && !empty($_POST['refund_duration'])) ? $_POST['refund_duration'] : 'Not-Refundable';
        $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
        $stmt->execute([$seller_id]);
        $seller = $stmt->fetch(PDO::FETCH_ASSOC);
        $seller_name = $seller['username'];

        $lines = explode("\n", $data);
        foreach ($lines as $line) {
            $details = explode('|', $line);

            if (count($details) >= max($pos_track1, $pos_code, $pos_track2, $pos_pin, $pos_country)) {
                if ($details[$pos_track1] == 0) {
                    $track1 = NULL;
                } else {
                    @$track1 = $details[$pos_track1 - 1];
                }
                $track2 = $details[$pos_track2 - 1] ?? "";
                $code   = $details[$pos_code - 1] ?? 'NA';
                $pin    = isset($details[$pos_pin - 1]) ? $details[$pos_pin - 1] : '0';
                $country= isset($details[$pos_country - 1]) ? strtoupper(trim(preg_replace('/\s+/', ' ', $details[$pos_country - 1]))) : 'Unknown';

                $stmt = $pdo->prepare("SELECT COUNT(*) FROM dumps WHERE track2 = ?");
                $stmt->execute([$track2]);
                $count = $stmt->fetchColumn();

                if ($count > 0) {
                    $duplicateCount++;
                } else {
                    preg_match('/^;?(\d{14,16})=(\d{2})(\d{2})(\d{3})?/', $track2, $matches);


                    $card_number = isset($matches[1]) ? $matches[1] : '0';
                    $exp_yy = isset($matches[2]) ? $matches[2] : '0';
                    $exp_mm = isset($matches[3]) ? $matches[3] : '0';
                    $codex  = isset($matches[4]) ? $matches[4] : '0';
                    if (!empty($track1) && $track1 != NULL) {
                        // Optional debug output
                        echo 1;
                        $card_numberww = explode("^", $track1)[0];
                    } else {
                        $card_numberww = explode("=", $track2)[0];
                    }

                    $card_type = getCardType($card_numberww);

                    // UPDATED: Use "Refundable" as the column name
                    // Replace the original query with this updated one:
$query = "INSERT INTO dumps 
(track1, code, base_name, track2, pin, monthexp, yearexp, seller_id, seller_name, price, status, card_type, country, Refundable)
VALUES (
AES_ENCRYPT(?, ?),  -- Encrypted track1
?,                  -- code (codex)
?,                  -- base_name (code)
AES_ENCRYPT(?, ?),  -- Encrypted track2
?,                  -- pin
?,                  -- monthexp
?,                  -- yearexp
?,                  -- seller_id
?,                  -- seller_name
?,                  -- price
'unsold',           -- status
?,                  -- card_type
?,                  -- country
?                   -- Refundable
)";
      
      $stmt = $pdo->prepare($query);
      $stmt->execute([
          $track1,         // Plain text track1
          $encryptionKey,  // Key for track1 encryption
          $codex,          // Code (unchanged)
          $code,           // Base name (unchanged)
          $track2,         // Plain text track2
          $encryptionKey,  // Key for track2 encryption
          $pin,            // PIN
          $exp_mm,         // Month Expiry
          $exp_yy,         // Year Expiry
          $seller_id,      // Seller ID
          $seller_name,    // Seller Name
          $price,          // Price
          $card_type,      // Card Type
          $country,        // Country
          $refund          // Refundable status
      ]);
      
                    
                    $importedCount++;
                }
            } else {
                $errorMessage = 'Data format incorrect, please check the format and try again.';
            }
        }

        header("Location: id.php?imported=$importedCount&duplicates=$duplicateCount");
        exit;

    } catch (Exception $e) {
        $errorMessage = 'Error importing data: ' . $e->getMessage();
    }
}

if (isset($_GET['imported']) && $_GET['imported'] > 0) {
    $successMessage = $_GET['imported'] . " items were imported successfully.";
}
if (isset($_GET['duplicates']) && $_GET['duplicates'] > 0) {
    $duplicateMessage = $_GET['duplicates'] . " duplicates were detected and ignored.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Dumps</title>
    <link rel="stylesheet" href="../css/importer.css"> <!-- Link to external CSS -->
    <style>
        .refund-container {
            display: inline-flex;
            align-items: center;
            margin: 10px 0;
        }
        .refund-container input[type="checkbox"] {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            border: 2px solid #0c182f;
            border-radius: 4px;
            margin-right: 8px;
            position: relative;
            cursor: pointer;
            outline: none;
            transition: background-color 0.2s ease-in-out;
        }
        .refund-container input[type="checkbox"]:checked {
            background-color: #0c182f;
        }
        .refund-container input[type="checkbox"]:checked::after {
            content: '';
            position: absolute;
            top: 3px;
            left: 7px;
            width: 5px;
            height: 10px;
            border: solid #fff;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }
        .refund-container label {
            font-size: 16px;
            color: #0c182f;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Import Dumps</h2>
        <form action="import_dumps.php" method="POST" enctype="multipart/form-data">
            <!-- File upload for CSV or TXT files -->
            <textarea name="data" id="data" placeholder=""></textarea>
            <input type="file" name="import_file" accept=".csv, .txt">
            <!-- Set field positions for data mapping -->
            <div class="grid-container">
                <input type="number" name="pos_track1" placeholder="Track 1 Pos" required>
                <input type="number" name="pos_track2" placeholder="Track 2 Pos" required>
                <input type="number" name="pos_pin" placeholder="PIN Pos (if available)">
                <input type="number" name="pos_country" placeholder="Country Pos" required>
                <input type="number" name="pos_code" placeholder="Base POS" required>
            </div>
            <!-- Select the user who will benefit from the sale -->
            <select name="seller_id" id="seller_id" required>
                <option value="">Select Seller</option>
                <?php foreach ($sellers as $seller): ?>
                <option value="<?= $seller['id'] ?>"><?= $seller['username'] ?></option>
                <?php endforeach; ?>
            </select>
            <!-- Set the price for the batch -->
            <input type="number" name="price" id="price" step="0.01" min="0" placeholder="Price (USD)" required>
            <!-- Refund Checkbox -->
            <div class="refund-container">
    <input type="checkbox" id="refund" name="refund" value="1">
    <label for="refund">Refund Available</label>
</div>
<div id="refundDurationContainer" style="display: none; margin-left: 10px;">
    <label for="refund_duration" style="font-size:16px; color:#0c182f;">Select Refund Duration:</label>
    <select id="refund_duration" name="refund_duration">
        <option value="5 Minutes">5 Minutes</option>
        <option value="10 Minutes">10 Minutes</option>
        <option value="20 Minutes">20 Minutes</option>
    </select>
</div>


            <button type="submit" class="import-button">Import Dumps</button><br><br>
            <button class="back-button" onclick="history.back()">Back</button>
        </form>
        <!-- Display success and duplicate messages below the button -->
        <?php if ($successMessage): ?>
        <div class="success-message"><?= $successMessage ?></div>
        <?php endif; ?>
        <?php if ($duplicateMessage): ?>
        <div class="duplicate-message"><?= $duplicateMessage ?></div>
        <?php endif; ?>
        <?php if ($errorMessage): ?>
        <div class="error-message"><?= $errorMessage ?></div>
        <?php endif; ?>
    </div>
<script>

    document.addEventListener('DOMContentLoaded', function() {
    var refundCheckbox = document.getElementById('refund');
    refundCheckbox.addEventListener('change', function() {
        var refundDropdown = document.getElementById('refundDurationContainer');
        refundDropdown.style.display = this.checked ? 'block' : 'none';
    });
});
</script>
</body>
</html>

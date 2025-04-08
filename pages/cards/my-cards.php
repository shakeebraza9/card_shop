<?php
// ***** IMPORTANT: Remove any whitespace before this tag *****
ob_start(); // Start output buffering
error_reporting(E_ALL);
ini_set('display_errors', 1);



// When this file is requested with ?action=fetch_activity_log, return only JSON.
if (isset($_GET['action']) && $_GET['action'] === 'fetch_activity_log') {
    // Include the header to load $pdo (if header.php outputs anything, we’ll clear it)
    include_once('../../header.php');
    
    
    // Clear any output from header.php
    ob_clean();

    try {
        // $pdo should be defined by header.php
        $stmt = $pdo->prepare("SELECT * FROM card_activity_log WHERE user_id = :user_id AND deleted = 0 ORDER BY date_checked DESC");
$stmt->bindValue(':user_id', (int)$_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        $activityLogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Activity log error: " . $e->getMessage());
        $activityLogs = [];
    }
    header('Content-Type: application/json');
    echo json_encode($activityLogs);
    exit;
}

// For a normal page load, flush the buffer.
ob_end_flush();

include_once('../../header.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);




// If $soldCards is already defined (from an earlier query),
// filter out cards with cc_status = 'dead'
if (isset($soldCards) && is_array($soldCards)) {
    // Fetch activity log data
    try {
        $stmt = $pdo->prepare("SELECT creference_code, status FROM card_activity_log");
        $stmt->execute();
        $activityLogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $activityLogs = [];
    }

    // Convert activity log data into an associative array for quick lookup
    $deadCards = [];
    foreach ($activityLogs as $log) {
        if (strtolower($log['status']) === 'dead') {
            $deadCards[$log['creference_code']] = true;
        }
    }

    // Filter out sold cards that exist in activity log with status "dead"
    $soldCards = array_filter($soldCards, function($card) use ($deadCards) {
        return isset($card['cc_status']) && strtolower($card['cc_status']) !== 'dead' &&
               !isset($deadCards[$card['creference_code']]);
    });

    $soldCards = array_values($soldCards);
}



// -----------------------------------------------------------
// 1) Fetch your card_activity_log data from the database here
// -----------------------------------------------------------
try {
    $stmt = $pdo->prepare("SELECT * FROM card_activity_log WHERE user_id = :user_id AND deleted = 0 ORDER BY date_checked DESC");
$stmt->bindValue(':user_id', (int)$_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $activityLogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $activityLogs = [];
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>My Cards & Activity Log</title>
    <style>
    .live {
        color: green;
        font-weight: bold;
    }

    .dead,
    .kill {
        color: red;
        font-weight: bold;
    }

    .check-card-button {
        background-color: #28a745;
    }

    .check-card-button {
        padding: 6px 10px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        margin: 0 5px 0 0;
    }

    .check-card-button:disabled {
        background-color: #d6d6d6;
        color: #999;
        cursor: not-allowed;
    }




    /* Style the search box */
    .dataTables_filter {
        display: none !important;
    }

    .table {
        background-color: white !important;
    }



    /* --- Your original styles --- */
    .credit-card-item {
        transition: transform 0.6s ease-in-out;
    }

    table {
        width: 100%;
        background-color: white;
        border-collapse: collapse;
        margin: 20px 0;
        font-size: 16px;
        text-align: left;
    }

    thead tr {
        background-color: #0c182f !important;
        color: white;
        text-align: left;
        font-weight: bold;
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 10px 15px;
    }

    tbody tr:nth-child(even) {
        background-color: rgb(255, 255, 255);
    }

    tbody tr:hover {
        background-color: #f1f1f1;
    }

    table button {
        padding: 5px 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .copy-button {
        background-color: #0c182f;
        color: white;
        margin: 0px 5px 0px 0px !important;
    }

    .copy-button:hover {
        background-color: #218838;
    }

    .check-card-button {
        background-color: #0c182f;
        color: white;
    }

    .activity-log-table th {
        background-color: #0c182f !important;
    }

    @media (max-width: 768px) {
        table {
            font-size: 14px;
            background-color: White !important;
        }

        td,
        th {
            padding: 8px 15px;
            text-wrap: nowrap !important;
        }

        .main-tbl321 {
            width: 100% !important;
            overflow-x: scroll !important;
        }

        a.buy-button {
            height: 30px !important;
        }
    }

    .ribbon {
        position: absolute;
        top: -3px;
        left: -21px;
        background: #4CAF50;
        color: white;
        padding: 5px 16px;
        transform: rotate(-45deg);
        transform-origin: top right;
        font-size: 12px;
        font-weight: bold;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        z-index: 1;
        border-radius: 8px;
        animation: swing 2s ease-in-out infinite;
    }

    @keyframes swing {
        0% {
            transform: rotate(-45deg);
        }

        25% {
            transform: rotate(-43deg);
        }

        50% {
            transform: rotate(-47deg);
        }

        75% {
            transform: rotate(-43deg);
        }

        100% {
            transform: rotate(-45deg);
        }
    }

    @keyframes shake-up-down {
        0% {
            transform: translateY(0);
        }

        25% {
            transform: translateY(-5px);
        }

        50% {
            transform: translateY(5px);
        }

        75% {
            transform: translateY(-5px);
        }

        100% {
            transform: translateY(0);
        }
    }

    .shake {
        animation: shake-up-down 0.5s ease-in-out;
    }

    #rules-btn:hover {
        animation: shake-up-down 0.5s ease-in-out;
    }

    .activity-log-row.live {
        background: linear-gradient(90deg, #00c853, #00c853) !important;
        color: #fff !important;
        font-weight: bold;
        border: 1px solid #004d40;
    }

    .activity-log-row.live td {
        background: transparent !important;
        padding: 5px;
    }

    .activity-log-row.dead {
        background: linear-gradient(90deg, #ff8a80, #d50000) !important;
        color: #fff !important;
        font-weight: bold;
        border: 1px solid #b71c1c;
    }

    .activity-log-row.dead td {
        background: transparent !important;
        padding: 5px;
    }

    .activity-log-row.disabled {
        background: linear-gradient(90deg, #616161, #bdbdbd) !important;
        color: #fff !important;
        font-weight: bold;
        border: 1px solid #424242;
    }

    .activity-log-row.disabled td {
        background: transparent !important;
        padding: 5px;
    }
    </style>
</head>

<body>
    <div class="main-content">
        <div id="my-cards" class="uuper">
            <div style="display:flex;justify-content: space-between;">
                <h2>My Cards Section</h2>
                <div style="position: relative; width: 300px; font-family: Arial, sans-serif;">
                    Button to open the dropdown
                    <button id="dropdownBtn"
                        style="width: 100%; padding: 12px; background-color: #0c182f; border: 1px solid rgb(31, 54, 96); border-radius: 5px; color: white; text-align: left; font-size: 14px; cursor: pointer; transition: background-color 0.3s ease;">
                        Select Columns
                    </button>
                    <!-- Dropdown menu -->
                    <div id="dropdownMenu"
                        style="display: none; position: absolute; top: 100%; left: 0; width: 100%; background-color: white; border: 1px solid #ccc; border-radius: 5px; padding: 10px; box-sizing: border-box; z-index: 999; max-height: 300px; overflow-y: auto; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15); transition: max-height 0.3s ease;">
                        <!-- Column checkboxes -->
                        <label><input type="checkbox" value="1" checked> Card Number</label><br>
                        <label><input type="checkbox" value="2" checked> Expiration</label><br>
                        <label><input type="checkbox" value="3" checked> verification_code</label><br>
                        <label><input type="checkbox" value="4" checked> Name on Card</label><br>
                        <label><input type="checkbox" value="5" checked> Base Name</label><br>
                        <label><input type="checkbox" value="6" checked> Address</label><br>
                        <label><input type="checkbox" value="7" checked> City</label><br>
                        <label><input type="checkbox" value="8" checked> MNN</label><br>
                        <label><input type="checkbox" value="9" checked> Account Number</label><br>
                        <label><input type="checkbox" value="10" checked> Sort Code</label><br>
                        <label><input type="checkbox" value="11" checked> ZIP</label><br>
                        <label><input type="checkbox" value="12" checked> Country</label><br>
                        <label><input type="checkbox" value="13" checked> Phone Number</label><br>
                        <label><input type="checkbox" value="14" checked> Date of Birth</label><br>
                        <label><input type="checkbox" value="15" checked> Email</label><br>
                        <label><input type="checkbox" value="16" checked> SIN/SSN</label><br>
                        <label><input type="checkbox" value="17" checked> Pin</label><br>
                        <label><input type="checkbox" value="18" checked> Driver License</label><br>
                    </div>
                    <div id="customSearchContainer" style="margin-bottom: 10px;">
                        <input type="text" id="customSearch" placeholder="Search"
                            style="width: 300px; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                    </div>

                </div>
            </div>

            <?php if (empty($soldCards)): ?>
            <p>No purchased cards available.</p>
            <?php else: ?>
            <div class="main-tbl321"
                style="overflow-x: auto; max-width: 100%; border: 1px solid #ddd; margin-top: 20px;">
                <table id="soldDumpsTable"
                    style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; margin-top: 20px;">
                    <thead>
                        <div id="top-scrollbar"
                            style="overflow-x: auto; overflow-y: hidden; width: 100%; border: 1px solid #ddd; margin-bottom: 5px;">
                            <div id="scroll-content" style="height: 1px;"></div>
                        </div>


                        <tr style="background-color: #f4f4f4; border-bottom: 2px solid #ddd;">
                            <th style="padding: 10px; border: 1px solid #ddd; width: 18%;">ID</th>
                            <th style="padding: 10px; border: 1px solid #ddd; width: 18%;">Card Number</th>
                            <th style="padding: 10px; border: 1px solid #ddd;">Expiration</th>
                            <th style="padding: 10px; border: 1px solid #ddd;">verification_code</th>
                            <th style="padding: 10px; border: 1px solid #ddd;">Name on Card</th>
                            <th style="padding: 10px; border: 1px solid #ddd;">Base Name</th>
                            <th style="padding: 10px; border: 1px solid #ddd;">Address</th>
                            <th style="padding: 10px; border: 1px solid #ddd;">City</th>
                            <th style="padding: 10px; border: 1px solid #ddd;">MNN</th>
                            <th style="padding: 10px; border: 1px solid #ddd;">Account Number</th>
                            <th style="padding: 10px; border: 1px solid #ddd;">Sort Code</th>
                            <th style="padding: 10px; border: 1px solid #ddd;">ZIP</th>
                            <th style="padding: 10px; border: 1px solid #ddd;">Country</th>
                            <th style="padding: 10px; border: 1px solid #ddd;">Phone Number</th>
                            <th style="padding: 10px; border: 1px solid #ddd;">Date of Birth</th>
                            <th style="padding: 10px; border: 1px solid #ddd;">Email</th>
                            <th style="padding: 10px; border: 1px solid #ddd;">SIN/SSN</th>
                            <th style="padding: 10px; border: 1px solid #ddd;">Pin</th>
                            <th style="padding: 10px; border: 1px solid #ddd;">Driver License</th>
                            <th style="padding: 10px; border: 1px solid #ddd;">Actions</th>
                        </tr>
                    </thead>
                    <?php foreach ($soldCards as $card): 
                            if (isset($card['deleted']) && $card['deleted'] == 1) continue;
                            $disableTime = 300;
                            $disableCheck = false;
                            if (isset($card['purchased_at'])) {
                                $purchaseTime = strtotime($card['purchased_at']);
                                if ($purchaseTime) {
                                    if (isset($card['refundable'])) {
                                        if ($card['refundable'] == 5) { $disableTime = 300; }
                                        elseif ($card['refundable'] == 10) { $disableTime = 600; }
                                        elseif ($card['refundable'] == 20) { $disableTime = 1200; }
                                    }
                                    if ((time() - $purchaseTime) > $disableTime) { $disableCheck = true; }
                                }
                            }
                        ?>
                    <tr id="card-<?php echo htmlspecialchars($card['id']); ?>">
                        <!-- ID cell with the "New" ribbon if needed -->
                        <td style="padding: 10px; position: relative;">
                            <?php echo htmlspecialchars($card['id']); ?>
                            <?php if ($card['is_view'] == 0): ?>
                            <span class="ribbon">New</span>
                            <?php endif; ?>
                        </td>
                        <!-- Card number cell without the "New" tag -->
                        <td style="padding: 10px;"
                            data-card-number="<?php echo htmlspecialchars($card['creference_code']); ?>">
                            <?php echo htmlspecialchars($card['creference_code']); ?>
                        </td>
                        <td style="padding: 10px;">
                            <?php echo htmlspecialchars($card['ex_mm'] . '/' . $card['ex_yy']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($card['verification_code']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($card['billing_name']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($card['base_name'] ?? 'N/A'); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($card['address']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($card['city']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($card['security_hint']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($card['account_ref']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($card['sort_code']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($card['zip']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($card['country']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($card['phone_number']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($card['date_of_birth'] ?? 'N/A'); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($card['email'] ?? 'N/A'); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($card['sinssn'] ?? 'N/A'); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($card['pin'] ?? 'N/A'); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($card['drivers'] ?? 'N/A'); ?></td>
                        <td style="padding: 10px; display: flex; justify-content: center; align-items: center;">
                            <button class="copy-button"
                                style="padding: 6px 10px; border: none; border-radius: 3px; cursor: pointer; margin-right: 5px;"
                                onclick="copyCardInfo(<?php echo json_encode($card['id']); ?>)">Copy</button>
                            <?php if ($card['cc_status'] == 'unchecked'): ?>
                            <button id="check-button-<?php echo $card['id']; ?>" class="check-card-button"
                                style="padding: 6px 10px; border: none; border-radius: 3px; cursor: pointer; margin: 0 5px 0 0;"
                                data-cc-status="<?php echo htmlspecialchars($card['cc_status']); ?>"
                                data-purchased-at="<?php echo strtotime($card['purchased_at'] ?? 0); ?>"
                                data-disable-time="<?php echo $disableTime; ?>"
                                data-refundable="<?php echo addslashes($card['refundable']); ?>" onclick="return checkCard(
            '<?php echo addslashes($card['creference_code']); ?>',
            '<?php echo addslashes($card['ex_mm']); ?>',
            '<?php echo addslashes($card['ex_yy']); ?>',
            '<?php echo addslashes($card['verification_code'] ?? ''); ?>',
            '<?php echo $card['id']; ?>',
            '<?php echo addslashes($card['cc_status']); ?>'
        )" <?php if ($disableCheck) echo 'disabled title="Check disabled after ' . ($disableTime / 60) . ' minutes"'; ?>>Check</button>
                            <?php endif; ?>



                            <a type="button" onclick="deleteRow(<?php echo $card['id']; ?>)"
                                id="clear-btn-<?php echo $card['id']; ?>" class="btn text-center btn-with-icon"
                                style="background-color: #f44336; color: white; padding: 5px 15px; width:70px; border-radius: 4px; border: none; cursor: pointer; margin-top: -1px;">
                                <i class="fa fa-times"></i> <span class="btn-text">Delete</span>
                            </a>

                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <!-- Activity Log Section -->
            <div id="card-activity-log">
                <div style="display: flex; align-items: center; gap: 20px;">
                    <h2>Card Activity Log</h2>
                    <button id="rules-btnnew"
                        style="padding: 5px 15px; background-color: #f39c12; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; display: flex; align-items: center; gap: 5px;"
                        onclick="openRulesPopup()">
                        <i class="fas fa-gavel"></i> Rules
                    </button>
                    <button id="delete-all-logs"
                        style="padding: 5px 15px; background-color: #d9534f; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">
                        Delete Cards Activity Logs
                    </button>
                </div>
                <div class="main-tbl321">
                    <table id="activityLogTable" class="activity-log-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Card Number</th>
                                <th>Date Checked</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($activityLogs)): ?>
                            <tr>
                                <td colspan="2" style="text-align: center;">No activity logged yet</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($activityLogs as $log): ?>
                            <tr class="activity-log-row <?php echo strtolower($log['status']); ?>">
                                <td><?php echo htmlspecialchars($log['card_id']); ?></td>
                                <td><?php echo htmlspecialchars($log['creference_code']); ?></td>
                                <td><?php echo htmlspecialchars($log['date_checked']); ?></td>
                                <td><?php echo htmlspecialchars($log['status']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div id="rules-popup2" class="popup-modal" style="display: none;">
        <div class="popup-content">
            <span class="close" onclick="closeRulesPopup()"><i class="fas fa-times"></i></span>
            <h2>Purchased Information </h2>
            <p> Here are the updated rules for using the system:
            <p>
            <ul>
                • For a smooth checking experience, please check one card at a time. After receiving a response from the
                checker—either
                <span class="live">LIVE</span> or <span class="dead">DEAD</span>—you can proceed to check the remaining
                cards.
                </p>
                <p>
                    • Checking a card may, in most cases, '<span class="kill">KILL</span>' the card. If the card's
                    status results in a
                    <span class="live">LIVE</span> response, you will not be eligible for a refund. Therefore, please do
                    not request a refund for a card with a
                    <span class="live">LIVE</span> status.
                </p>
                <p>
                    • The fee for checking a card is $0.50. If the card's result is <span class="dead">DEAD</span>, you
                    will receive a full refund for the card amount, along with the credit for checking the card.
                </p>
                <p>
                    • For <span class="live">LIVE</span> cards, you will not receive a refund for the checking fee.
                </p>
                <p>
                    • We use the most advanced third-party checking systems. If a card's status is returned as <span
                        class="live">LIVE</span>, we will not issue a refund. This checking service is not part of our
                    default system, so we rely on and trust the third-party providers we use to verify the cards.
                </p>
                <p>
                    <b> • Do not attempt to abuse the card checker in an effort to find bugs or exploit the system. We
                        have mechanisms in place that notify us when a user tries to use the checker in any way other
                        than intended. In such cases, we reserve the right to ban the user without prior notice, and any
                        deposited funds will be forfeited.
                    </b>
                </p>
                <p>
                    • We reserve the right to disable the checker at any time without prior notice in the event of
                    malfunctions or misuse.
                </p>
                <p>
                    • You have the option to delete your entire card activity log by clicking the 'Delete Cards Activity
                    Logs' button, if you prefer not to keep a record of previously checked cards. You also have the
                    option to delete purchased and used cards individually by clicking the 'Delete' button next to each
                    card.
                </p>
        </div>
    </div>

    </div>

    <?php include_once('../../footer.php'); ?>

    <!-- JavaScript Section -->
    <script>
    // ------------------- Realtime Activity Log Update -------------------
    setInterval(function() {
        $.ajax({
            url: '?action=fetch_activity_log',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                updateActivityLogTable(data);
            },
            error: function(xhr, status, error) {
                console.error("Error fetching activity log:", error);
            }
        });
    }, 5000); // Update every 5 seconds

    function updateActivityLogTable(logs) {
        var table = $('#activityLogTable').DataTable();
        table.clear();

        if (!logs.length) {
            // Add a single row indicating no activity
            var rowNode = table.row.add([
                'No activity logged yet',
                '',
                '',
                ''
            ]).node();
            // Optionally, add a custom class for styling the "no activity" row
            $(rowNode).addClass('activity-log-row');
        } else {
            logs.forEach(function(log) {
                var rowClass = log.status.toLowerCase(); // e.g., "live" or "dead"
                var rowNode = table.row.add([
                    log.card_id,
                    log.card_number,
                    log.date_checked,
                    log.status
                ]).node();
                // Add your custom classes to the row so your CSS applies
                $(rowNode).addClass('activity-log-row ' + rowClass);
            });
        }

        table.draw(false);
    }



    // ------------------- Realtime Check Button Disable -------------------
    function updateCheckButtons() {
        $('.check-card-button').each(function() {
            var purchasedAt = parseInt($(this).data('purchased-at'), 10);
            var disableTime = parseInt($(this).data('disable-time'), 10);
            if (!purchasedAt || !disableTime) return;
            var currentTime = Math.floor(Date.now() / 1000);
            if (currentTime - purchasedAt >= disableTime) {
                $(this).prop('disabled', true);
                $(this).attr('title', 'Check disabled after ' + (disableTime / 60) + ' minutes');
            }
        });
    }
    setInterval(updateCheckButtons, 1000);

    // ------------------- Modified checkCard Function (No Popup, No Refresh) -------------------
    var isChecking = false;

    function checkCard(cardNumber, expm, expy, verification_code, cardId, cardStatus) {
        // Get the button by its unique ID.
        var $btn = $('#check-button-' + cardId);

        // Retrieve the refundable flag from the button's data attribute.
        var refundable = $btn.attr('data-refundable') || "unknown";
        // Normalize: convert to lowercase, trim, and remove hyphens and spaces.
        var refundStr = refundable.toLowerCase().trim().replace(/[-\s]/g, "");

        console.log("Refundable parameter:", refundable, "Normalized:", refundStr);

        // Block if "non-refundable" in any format
        if (refundStr.includes("nonrefundable")) {
            alertify.error("The Card is not refundable. Please don't do this or you will be banned.");
            return false;
        }

        if (cardStatus && cardStatus.toLowerCase() === 'dead') {
            alertify.error("This card is dead; check operation disabled.");
            return false;
        }

        if ($btn.prop('disabled')) return false;

        // Use Alertify's confirm dialog
        alertify.confirm(
            "Are you sure you want to check this card?",
            "Checking it may KILL the card, and if it returns a LIVE status, you will not be able to claim a refund. Do you want to proceed?",
            function() { // User clicked OK
                // Disable the button and update its text.
                $btn.text("Checking...").prop('disabled', true);
                var userId = <?php echo json_encode($_SESSION['user_id']); ?>;
                $.ajax({
                    url: '../../ajax/cc-checker-api.php',
                    type: 'POST',
                    data: {
                        cardnum: cardNumber,
                        expm: expm,
                        expy: expy,
                        verification_code: verification_code,
                        user_id: userId
                    },
                    success: function(response) {
                        if (response.status === 'LIVE') {
                            alertify.success("Card is LIVE!");
                            $btn.text("LIVE").css("background-color", "#218838");
                            $('#card-' + cardId).addClass("activity-log-row live");
                        } else if (response.status === 'DEAD') {
                            alertify.error("Card is DEAD! (Amount refunded)");
                            removeCardFromUI(cardId);
                        } else if (response.status === 'disabled') {
                            $btn.text("DISABLED");
                        } else {
                            alertify.error("Error: " + response.error);
                            $btn.text("Check").prop('disabled', false);
                        }
                    },
                    error: function(xhr, status, error) {
                        alertify.error("Error checking card: " + error);
                        $btn.text("Check").prop('disabled', false);
                    },
                    complete: function() {
                        if ($btn.text() === "Checking...") {
                            $btn.text("Check").prop('disabled', false);
                        }
                    }
                });
            },
            function() { // User clicked Cancel
                alertify.error("Checking Cancelled By The User.");
            }
        );

        return false;
    }

    //NEW FUN DELETE
    document.addEventListener('DOMContentLoaded', function() {
        var deleteAllBtn = document.getElementById('delete-all-logs');
        if (deleteAllBtn) {
            deleteAllBtn.addEventListener('click', function() {
                if (typeof alertify !== "undefined") {
                    alertify.confirm(
                        "Delete All Checked Logs",
                        "Are you sure you want to delete all logs?",
                        function() { // User clicked Yes
                            $.ajax({
                                url: 'deleteAllLogs.php',
                                type: 'POST',
                                dataType: 'json',
                                success: function(response) {
                                    if (response.success) {
                                        var tbody = document.querySelector(
                                            '#activityLogTable tbody');
                                        if (tbody) {
                                            tbody.innerHTML =
                                                '<tr><td colspan="4" style="text-align:center;">No activity logged yet</td></tr>';
                                        }
                                        alertify.success("Activity logs cleared.");
                                    } else {
                                        alertify.error(response.error ||
                                            "Error clearing logs.");
                                    }
                                },
                                error: function(xhr, status, error) {
                                    alertify.error("Error deleting logs: " + error);
                                }
                            });
                        },
                        function() { // User clicked Cancel
                            alertify.error("Operation cancelled.");
                        }
                    );
                } else {
                    if (confirm("Are you sure you want to delete all logs?")) {
                        $.ajax({
                            url: 'deleteAllLogs.php',
                            type: 'POST',
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    var tbody = document.querySelector(
                                        '#activityLogTable tbody');
                                    if (tbody) {
                                        tbody.innerHTML =
                                            '<tr><td colspan="4" style="text-align:center;">No activity logged yet</td></tr>';
                                    }
                                    alert("Activity logs cleared.");
                                } else {
                                    alert("Error: " + response.error);
                                }
                            },
                            error: function(xhr, status, error) {
                                alert("Error deleting logs: " + error);
                            }
                        });
                    }
                }
            });
        } else {
            console.error('Button with id "delete-all-logs" not found.');
        }
    });



    //Scroll


    $(document).ready(function() {
        var $topScrollbar = $('#top-scrollbar');
        var $tableContainer = $('.main-tbl321');

        // Function to sync the top scrollbar's inner width with the table container's scrollWidth
        function syncTopScrollbarWidth() {
            var tableScrollWidth = $tableContainer.get(0).scrollWidth;
            $('#scroll-content').width(tableScrollWidth);
        }

        // Initial sync
        syncTopScrollbarWidth();

        // Update on window resize (or table changes)
        $(window).resize(syncTopScrollbarWidth);

        // Sync scrolling: When the top scrollbar is scrolled, update the table container's scrollLeft.
        $topScrollbar.on('scroll', function() {
            $tableContainer.scrollLeft($(this).scrollLeft());
        });
        // If needed, sync in reverse.
        $tableContainer.on('scroll', function() {
            $topScrollbar.scrollLeft($(this).scrollLeft());
        });
    });








    function removeCardFromUI(cardId) {
        var table = $('#soldDumpsTable').DataTable();
        table.row('#card-' + cardId).remove().draw(false);
    }

    // ------------------- Other Functions: Delete, Dropdown, etc. -------------------
    $(document).on('click', '.check-card-button', function(e) {
        var status = $(this).data('cc-status');
        if (status && status.toLowerCase() === 'dead') {
            e.preventDefault();
            alertify.error("This card is dead; check operation disabled.");
            return false;
        }
    });

    $(document).ready(function() {
        // Initialize DataTables
        var table = $('#soldDumpsTable').DataTable({
            paging: true,
            // If you want the default search box hidden, do searching: false
            // searching: false,
            // Otherwise keep searching: true so you can see the default box
            searching: true,
            ordering: false,
            info: true,
            lengthChange: true,
            autoWidth: false,
            responsive: true
        });

        // Hook up the custom search
        $('#customSearch').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Column toggling
        $('#dropdownMenu input[type="checkbox"]').on('change', function() {
            var columnIndex = $(this).val();
            var column = table.column(columnIndex);
            column.visible($(this).prop('checked'));
        });

        // Show/hide dropdown
        $("#dropdownBtn").on('click', function() {
            $("#dropdownMenu").toggle();
        });
        $(document).on('click', function(event) {
            if (!$(event.target).closest('#dropdownBtn, #dropdownMenu').length) {
                $("#dropdownMenu").hide();
            }
        });




        $(document).click(function(event) {
            if (!$(event.target).closest('#dropdownBtn, #dropdownMenu').length) {
                $("#dropdownMenu").hide();
            }
        });
        $('#dropdownMenu input[type="checkbox"]').change(function() {
            var columnIndex = $(this).val();
            var column = $('#soldDumpsTable').DataTable().column(columnIndex);
            column.visible(this.checked);
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        fetch('update_is_view.php', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                /* Optional: handle update */
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });

    const rulesBtn = document.getElementById('rules-btnnew');
    if (rulesBtn) {
        setInterval(() => {
            rulesBtn.classList.add('shake');
            setTimeout(() => {
                rulesBtn.classList.remove('shake');
            }, 500);
        }, 2000);
    } else {
        console.error('Button with id "rules-btnnew" not found.');
    }

    function deleteRow(cardId) {
        if (!confirm("Are you sure you want to delete this card?")) return;

        $.ajax({
            url: 'deleteCard.php',
            type: 'POST',
            data: {
                ticket_id: cardId
            }, // Corrected key to match PHP
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alertify.success("Card deleted successfully");
                    $("#card-" + cardId).fadeOut(500, function() {
                        $(this).remove();
                    });
                } else {
                    alertify.error(response.message || "Failed to delete the card.");
                }
            },
            error: function(xhr, status, error) {
                alertify.error("Error deleting card: " + error);
            }
        });
    }


    //RULES BUTTON SCRIPT
    function openRulesPopup() {
        var popup = document.getElementById("rules-popup2");
        if (popup) {
            popup.style.display = "flex";
        } else {
            console.error('Rules popup not found!');
        }
    }

    function closeRulesPopup() {
        var popup = document.getElementById("rules-popup2");
        if (popup) {
            popup.style.display = "none";
        }
    }

    // Ensure button exists and add event listener
    document.addEventListener("DOMContentLoaded", function() {
        var rulesBtn = document.getElementById("rules-btnnew");
        if (rulesBtn) {
            rulesBtn.addEventListener("click", openRulesPopup);
        } else {
            console.error('Button with id "rules-btnnew" not found.');
        }
    });


    $(document).ready(function() {
        $('#activityLogTable').DataTable({
            paging: true,
            ordering: false,
            info: true,
            lengthChange: true,
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            autoWidth: false,
            responsive: true
        });
    });
    </script>
</body>

</html>
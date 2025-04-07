<?php
require '../config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
  header("Location: admin_login.php?redirect=panel.php");
  exit();
}

// Helper function to format time differences
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    if ($diff < 60) return $diff . ' seconds ago';
    if ($diff < 3600) return round($diff/60) . ' minutes ago';
    if ($diff < 86400) return 'Today, ' . date('g:i a', $timestamp);
    return date('M j, Y, g:i a', $timestamp);
}

// Default fallback username
$adminUsername = 'Admin';
if (isset($_SESSION['admin_id'])) {
    $stmt = $pdo->prepare("SELECT username FROM admins WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result && isset($result['username'])) {
        $adminUsername = $result['username'];
    }
}

try {
    // Existing Stats queries
    $newUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE()")->fetchColumn();
    $usersAwaiting = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'awaiting_verification'")->fetchColumn();
    $newSellers = $pdo->query("SELECT COUNT(*) FROM users WHERE seller = 1 AND DATE(created_at) = CURDATE()")->fetchColumn();
    $sellersAwaiting = $pdo->query("SELECT COUNT(*) FROM users WHERE seller = 1 AND status = 'awaiting_approval'")->fetchColumn();
    $unreadMessages = $pdo->query("SELECT COUNT(*) FROM support_replies WHERE sender = 'user' AND is_read = 0")->fetchColumn();
    $newMessageCount = $pdo->query("SELECT COUNT(*) FROM support_replies WHERE DATE(created_at) = CURDATE()")->fetchColumn();
    $newTickets = $pdo->query("SELECT COUNT(*) FROM support_tickets WHERE status = 'open'")->fetchColumn();
    
    $stmtTickets = $pdo->prepare("SELECT * FROM support_tickets WHERE status = 'open' ORDER BY created_at DESC LIMIT 3");
    $stmtTickets->execute();
    $supportTickets = $stmtTickets->fetchAll(PDO::FETCH_ASSOC);
    
    $allActivity = $pdo->query("SELECT * FROM activity_log ORDER BY created_at DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    
    $stmtTopSellers = $pdo->prepare("SELECT username, seller_actual_balance, role FROM users WHERE seller = 1 ORDER BY seller_actual_balance DESC LIMIT 5");
    $stmtTopSellers->execute();
    $topSellers = $stmtTopSellers->fetchAll(PDO::FETCH_ASSOC);
    
    $stmtCardActivity = $pdo->prepare("SELECT COUNT(*) AS total FROM card_activity_log WHERE DATE(date_checked) = CURDATE()");
    $stmtCardActivity->execute();
    $cardActivitySummary = $stmtCardActivity->fetch(PDO::FETCH_ASSOC);
    
    $stmtDumpActivity = $pdo->prepare("SELECT COUNT(*) AS total FROM dumps_activity_log WHERE DATE(date_checked) = CURDATE()");
    $stmtDumpActivity->execute();
    $dumpActivitySummary = $stmtDumpActivity->fetch(PDO::FETCH_ASSOC);
    
    $stmtCardLogs = $pdo->prepare("SELECT * FROM card_activity_log ORDER BY date_checked DESC LIMIT 10");
    $stmtCardLogs->execute();
    $cardActivityLogs = $stmtCardLogs->fetchAll(PDO::FETCH_ASSOC);
    
    $stmtDumpLogs = $pdo->prepare("SELECT * FROM dumps_activity_log ORDER BY date_checked DESC LIMIT 10");
    $stmtDumpLogs->execute();
    $dumpActivityLogs = $stmtDumpLogs->fetchAll(PDO::FETCH_ASSOC);
    
    // Sales and financial stats
    $totalCardsSold = $pdo->query("SELECT COUNT(*) FROM cncustomer_records WHERE status = 'sold'")->fetchColumn();
    $totalDumpsSold = $pdo->query("SELECT COUNT(*) FROM dumps WHERE status = 'sold'")->fetchColumn();
    $totalBalanceFromUsers = $pdo->query("SELECT SUM(balance) FROM users")->fetchColumn();
    $totalPayableAmountForSellers = $pdo->query("SELECT SUM(total_earned) FROM users")->fetchColumn();
    $profit = $totalBalanceFromUsers - $totalPayableAmountForSellers;
    
    // Additional KPIs
    $stmtTopUsersBalance = $pdo->prepare("SELECT username, balance FROM users ORDER BY balance DESC LIMIT 5");
    $stmtTopUsersBalance->execute();
    $topUsersByBalance = $stmtTopUsersBalance->fetchAll(PDO::FETCH_ASSOC);

    $stmtTopUsersX = $pdo->prepare("SELECT username, total_earned FROM users ORDER BY total_earned DESC LIMIT 5");
    $stmtTopUsersX ->execute();
    $topUsersByX = $stmtTopUsersX->fetchAll(PDO::FETCH_ASSOC);
    
    $activeUsersCount = $pdo->query("SELECT COUNT(*) FROM users WHERE active = 1")->fetchColumn();
    $inactiveUsersCount = $pdo->query("SELECT COUNT(*) FROM users WHERE active = 0")->fetchColumn();

    $totalCreditCardsBalance = $pdo->query("SELECT SUM(credit_cards_total_earned) FROM users")->fetchColumn();
    $totalDumpsBalance = $pdo->query("SELECT SUM(dumps_balance) FROM users")->fetchColumn();
    
    $stmtTopUsersEarned = $pdo->prepare("SELECT username, total_earned FROM users ORDER BY total_earned DESC LIMIT 5");
    $stmtTopUsersEarned->execute();
    $topUsersByTotalEarned = $stmtTopUsersEarned->fetchAll(PDO::FETCH_ASSOC);
    
    $bannedUsersCount = $pdo->query("SELECT COUNT(*) FROM users WHERE banned = 1")->fetchColumn();
    $activeSellersCount = $pdo->query("SELECT COUNT(*) FROM users WHERE seller = 1 AND active = 1")->fetchColumn();
    
} catch (PDOException $e) {
    die("A database error occurred: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
    .grid-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        max-width: 1400px;
        margin: 0 auto 24px auto;
    }

    .kpi-box {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
        padding: 20px;
    }


    @keyframes borderGlow {
        0% {
            box-shadow: 0 0 5px #4f46e5, 0 0 10px #4f46e5;
            border-color: #4f46e5;
        }

        25% {
            box-shadow: 0 0 5px #ec4899, 0 0 10px #ec4899;
            border-color: #ec4899;
        }

        50% {
            box-shadow: 0 0 5px #f59e0b, 0 0 10px #f59e0b;
            border-color: #f59e0b;
        }

        75% {
            box-shadow: 0 0 5px #10b981, 0 0 10px #10b981;
            border-color: #10b981;
        }

        100% {
            box-shadow: 0 0 5px #4f46e5, 0 0 10px #4f46e5;
            border-color: #4f46e5;
        }
    }



    /* Basic styling for layout */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f5f7fb;
        margin: 0;
        padding: 20px;
        color: #333;
    }

    .container {
        max-width: 1400px;
        margin: 0 auto;
    }

    h1,
    h2,
    h3 {
        margin: 0;
    }

    /* KPI Box Styling */
    .kpi-box {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
        padding: 20px;
        margin-bottom: 24px;
    }

    .kpi-box h2 {
        font-size: 20px;
        font-weight: 600;
        color: #1a202c;
        margin-bottom: 16px;
    }

    .kpi-box p {
        font-size: 28px;
        font-weight: 700;
        color: #10b981;
        margin: 0;
    }

    /* Flex Containers */
    .flex-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .flex-item {
        flex: 1 1 calc(25% - 20px);
        min-width: 300px;
    }

    /* Chart Section */
    #chartContainer {
        position: relative;
        width: 600px;
        height: 300px;
        margin: 20px auto;
        padding: 12px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    #chartContainer select {
        padding: 4px 8px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    /* Tab Navigation */
    .tabs {
        display: flex;
        border-bottom: 1px solid #e5e7eb;
        margin-bottom: 16px;
    }

    .tab-btn {
        flex: 1;
        padding: 12px;
        background: #f9fafb;
        border: none;
        font-size: 14px;
        font-weight: 600;
        color: #4f46e5;
        cursor: pointer;
    }

    .tab-btn.active {
        border-bottom: 2px solid #4f46e5;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }
    </style>
</head>

<body>
    <div class="container">
        <!-- Dashboard Title -->
        <div style="margin-bottom: 24px;">
            <h1 style="font-size: 28px; font-weight: 600; color: #1a202c;">Dashboard Overview</h1>
            <p style="font-size: 16px; color: #718096; margin-top: 8px;">
                Welcome back, <?php echo htmlspecialchars($adminUsername); ?>. Here's what's happening today.
            </p>
        </div>


        <!-- KPI Stats -->
        <div class="flex-container">
            <!-- New Users -->
            <div class="flex-item kpi-box" style="border-left: 4px solid rgb(0, 73, 28);">
                <h3>New Users</h3>
                <p><?php echo $newUsers; ?></p>
                <p style="font-size: 14px; color: #718096;"><?php echo $usersAwaiting; ?> awaiting verification</p>
            </div>

            <!-- New Sellers -->
            <div class="flex-item kpi-box" style="border-left: 4px solid #8b5cf6;">
                <h3>New Sellers</h3>
                <p><?php echo $newSellers; ?></p>
                <p style="font-size: 14px; color: #718096;"><?php echo $sellersAwaiting; ?> awaiting approval</p>
            </div>

            <div class="flex-item kpi-box"
                style="border-left: 4px solid rgb(255, 145, 200); <?php if($unreadMessages > 0) echo 'animation: borderGlow 4s infinite;'; ?>">
                <h3>Support Messages</h3>
                <p><?php echo $unreadMessages;; ?> unread
                    <a href="sc.php"
                        style="color: #ec4899; font-weight: bold; margin-left: 8px; font-size: medium; text-decoration: none;">REPLY</a>
                </p>
            </div>






            <!-- Active Support Tickets -->
            <div class="flex-item kpi-box" style="border-left: 4px solid #f59e0b;">
                <h3>Active Support Tickets</h3>
                <p><?php echo $newTickets; ?></p>
                <p style="font-size: 14px; color: #718096;">Avg response: 2.5 hrs</p>
            </div>
            <div class="flex-item kpi-box" style="border-left: 4px solid #11e411;">
                <h3>Active Users</h3>
                <p><?php echo $activeUsersCount; ?></p>
            </div>
            <div class="flex-item kpi-box" style="border-left: 4px solid #fa0060;">
                <h3>Inactive Users</h3>
                <p><?php echo $inactiveUsersCount; ?></p>
            </div>
        </div>

        <!-- Additional KPIs Section -->
        <div class="flex-container">
            <div class="flex-item kpi-box width: auto;">
                <h2>Users with the Highest Top-Up Amounts</h2>
                <?php foreach($topUsersByBalance as $user): ?>
                <div
                    style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                    <span
                        style="font-size: 14px; font-weight: 600;"><?php echo htmlspecialchars($user['username']); ?></span>
                    <span style="font-size: 14px;">$<?php echo number_format($user['balance'], 2); ?></span>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="flex-item kpi-box width: auto;">
                <h2>Sellers' Payout Amounts</h2>
                <?php foreach($topUsersByX as $user): ?>
                <div
                    style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                    <span
                        style="font-size: 14px; font-weight: 600;"><?php echo htmlspecialchars($user['username']); ?></span>
                    <span style="font-size: 14px;">$<?php echo number_format($user['total_earned'], 2); ?></span>
                </div>
                <?php endforeach; ?>
            </div>

        </div>




        <!-- Additional Textual Summary -->
        <div style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.08);">
            <h3 style="margin-bottom: 10px;">Shop Summary</h3>
            <p style="margin-bottom: 6px; border-bottom: 1px solid #e5e7eb;">
                Total Balances from users:
                <strong>$<?php echo number_format($totalBalanceFromUsers, 2); ?></strong>
            </p>
            <p style="margin-bottom: 6px; border-bottom: 1px solid #e5e7eb;">
                Total Payable Amount for Sellers:
                <strong>$<?php echo number_format($totalPayableAmountForSellers, 2); ?></strong>
            </p>

            <p style="margin-bottom: 6px; border-bottom: 1px solid #e5e7eb;">
                Profit:
                <strong>$<?php echo number_format($profit, 2); ?></strong>
            </p>
        </div>

        <br>
        <?php
// Prepare an array with your stat details
$statCategories = [
  
  [
    'title' => 'Overall Cards Sold',
    'value' => number_format($totalCardsSold, 0)
  ],
  [
    'title' => 'Overall Dumps Sold',
    'value' => number_format($totalDumpsSold, 0)
  ]
];
?>

        <!-- Grid container for KPI boxes -->
        <div class="grid-container"
            style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin: auto; margin-bottom: 24px;">
            <?php foreach ($statCategories as $stat): ?>
            <div class="kpi-box"
                style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.08);">
                <h2 style="font-size: 20px; font-weight: 600; color: #1a202c;"><?php echo $stat['title']; ?></h2>
                <p style="font-size: 28px; font-weight: 700; color: #10b981;"><?php echo $stat['value']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>



    </div>
    <!-- Activity Logs Section with Tabs -->
    <div style="margin-bottom: 24px;">
        <h2 style="font-size: 20px; font-weight: 600; color: #1a202c; margin-bottom: 16px;">Activity Logs</h2>
        <div class="tabs">
            <button class="tab-btn active" data-tab="tab-all">All Activity</button>
            <button class="tab-btn" data-tab="tab-card">Card Activity</button>
            <button class="tab-btn" data-tab="tab-dump">Dumps Activity</button>
        </div>
        <div id="tab-all" class="tab-content active">
            <?php foreach($allActivity as $activity): ?>
            <div style="padding: 16px; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center;">
                <div
                    style="width: 40px; height: 40px; border-radius: 50%; background-color: #eef2ff; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
                    <span
                        style="color: #4f46e5; font-weight: 600;"><?php echo strtoupper(substr($activity['item_type'] ?? '', 0, 2)); ?></span>
                </div>
                <div style="flex: 1;">
                    <p style="margin: 0; font-size: 14px;">
                        <span
                            style="font-weight: 600;"><?php echo htmlspecialchars($activity['item_type'] ?? ''); ?></span>
                        -
                        <?php echo htmlspecialchars($activity['buy_itm'] ?? ''); ?> for
                        $<?php echo htmlspecialchars($activity['item_price'] ?? ''); ?> by
                        <span
                            style="color: #4f46e5;"><?php echo htmlspecialchars($activity['user_name'] ?? ''); ?></span>
                    </p>
                    <p style="margin: 4px 0 0 0; font-size: 12px; color: #6b7280;">
                        <?php echo timeAgo($activity['created_at'] ?? ''); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div id="tab-card" class="tab-content">
            <?php foreach($cardActivityLogs as $log): ?>
            <div style="padding: 16px; border-bottom: 1px solid #e5e7eb;">
                <p style="margin: 0; font-size: 14px;">
                    Card #: <strong><?php echo htmlspecialchars($log['creference_code'] ?? ''); ?></strong> - Status:
                    <?php echo htmlspecialchars($log['status'] ?? ''); ?>
                </p>
                <p style="margin: 4px 0 0 0; font-size: 12px; color: #6b7280;">
                    <?php echo timeAgo($log['date_checked'] ?? ''); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <div id="tab-dump" class="tab-content">
            <?php foreach($dumpActivityLogs as $log): ?>
            <div style="padding: 16px; border-bottom: 1px solid #e5e7eb;">
                <p style="margin: 0; font-size: 14px;">
                    Track1: <strong><?php echo htmlspecialchars($log['track1'] ?? ''); ?></strong> - Status:
                    <?php echo htmlspecialchars($log['status'] ?? ''); ?>
                </p>
                <p style="margin: 4px 0 0 0; font-size: 12px; color: #6b7280;">
                    <?php echo timeAgo($log['date_checked'] ?? ''); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Footer -->
    <div
        style="margin-top: 40px; text-align: center; padding: 20px; color: #6b7280; font-size: 14px; border-top: 1px solid #e5e7eb;">
        <p>© 2025 Admin Dashboard. All rights reserved.</p>
        <p>Last updated: <?php echo date('F j, Y, g:i a'); ?></p>
    </div>
    </div>

    <!-- JavaScript for Tab Switching -->
    <script>
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            tabButtons.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));

            btn.classList.add('active');
            document.getElementById(btn.getAttribute('data-tab')).classList.add('active');
        });
    });
    </script>


</body>

</html>
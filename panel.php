<?php
include_once('global.php');
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php?redirect=importers.php");
    exit();
}

$query = "SELECT COUNT(*) AS unread_count FROM support_tickets WHERE unread != 1";
$stmt = $pdo->prepare($query);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$unread_count = $result['unread_count'] ?? 0;

// Count new messages in any ticket
$query_new_messages = "SELECT COUNT(*) AS new_messages FROM support_replies WHERE is_read = 0 AND sender = 'user'";
$stmt_new_messages = $pdo->prepare($query_new_messages);
$stmt_new_messages->execute();
$result_new_messages = $stmt_new_messages->fetch(PDO::FETCH_ASSOC);
$new_message_count = $result_new_messages['new_messages'] ?? 0;

?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Importers</title>
    <style>
    /* Basic Reset */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Arial, sans-serif;
    }

    /* Body and Container Styles */
    body {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background: #f0f2f5;
        padding: 20px;
    }

    .container {
        width: 100%;
        max-width: 500px;
        background: #fff;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        text-align: center;
    }

    h2 {
        color: #333;
        font-size: 1.8em;
        margin-bottom: 20px;
    }

    .button-group {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-top: 20px;
    }

    /* Button Styles */
    .import-button {
        padding: 15px;
        font-size: 1.1em;
        font-weight: bold;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        color: #fff;
        transition: background-color 0.3s;
    }

    .import-button.tools {
        background-color: #4CAF50;
    }

    .import-button.cards {
        background-color: #2196F3;
    }

    .import-button.dumps {
        background-color: #FF5722;
    }

    .import-button.users {
        background-color:rgba(54, 8, 180, 0.51);
    }

    .import-button.updates {
        background-color:rgb(5, 54, 119);
    }

    .import-button.support {
        background-color: #9C27B0;
    }

    .import-button.settings {
        background-color:rgb(92, 1, 1);
    }

    .import-button:hover {
        opacity: 0.9;
    }

    /* Small screen adjustments */
    @media (max-width: 600px) {
        h2 {
            font-size: 1.5em;
        }

        .container {
            padding: 20px;
        }
    }
    </style>
</head>

<body>

    <div class="container">
        <h2>Admin Importers</h2>
        <div class="button-group">
            <button class="import-button tools" onclick="window.location.href='upload_tool.php'">Tools</button>
            <button class="import-button users" onclick="window.location.href='manage_users.php'">User Settings</button>
            <button class="import-button cards" onclick="window.location.href='import_cards.php'">Credit Cards</button>
            <button class="import-button dumps" onclick="window.location.href='import_dumps.php'">Dumps</button>
            <button class="import-button updates" onclick="window.location.href='update.php'">Admin Update</button>
          

                <button class="import-button support" onclick="window.location.href='support_chat.php'">
    Support Chat (<?php echo $unread_count; ?> unread tickets, <?php echo $new_message_count; ?> new messages)
</button>


            <button class="import-button cards" onclick="window.location.href='activitylog.php'">Activity log</button>
            <button class="import-button settings"
                onclick="window.location.href='<?= $urlval ?>admin/setting/setting.php'">Settings</button>

        </div>
    </div>

</body>

</html>
<?php


require '../config.php';
require '../encrypt.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: admin_login.php?redirect=panel.php");
    exit();
}

$stmt = $pdo->prepare("
    SELECT t.id AS ticket_id, t.created_at AS ticket_created_at, t.unread, t.status, t.admin_unread, t.subject,
           u.username AS user_username, m.sender, m.is_read, m.message AS content, m.created_at AS message_created_at
    FROM support_tickets t
    LEFT JOIN users u ON t.user_id = u.id
    LEFT JOIN support_replies m ON t.id = m.ticket_id
    ORDER BY t.admin_unread DESC, t.created_at DESC
");
$stmt->execute();
$rawTickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

$tickets = [];
foreach ($rawTickets as $row) {
    $ticketId = $row['ticket_id'];

    if (!isset($tickets[$ticketId])) {
        $tickets[$ticketId] = [];
    }
    
    // Decrypt the message content
    if (!empty($row['content'])) {
        $row['content'] = decryptMessage($row['content']);
    }

    // Add the decrypted row to the ticket array
    $tickets[$ticketId][] = $row;
}

// Count total tickets and unread tickets
$totalTickets = count($tickets);
$unreadTickets = 0;
$openTickets = 0;
$closedTickets = 0;

foreach ($tickets as $ticketMessages) {
    $hasUnread = false;
    foreach ($ticketMessages as $message) {
        if ($message['sender'] === 'user' && $message['is_read'] == 0) {
            $hasUnread = true;
            break;
        }
    }
    if ($hasUnread) {
        $unreadTickets++;
    }
    
    if (strtolower($ticketMessages[0]['status']) === 'open') {
        $openTickets++;
    } else {
        $closedTickets++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Support Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a6cf7;
            --primary-dark: #3a56d4;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --white: #ffffff;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
            --gray-400: #ced4da;
            --gray-500: #adb5bd;
            --gray-600: #6c757d;
            --gray-700: #495057;
            --gray-800: #343a40;
            --gray-900: #212529;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fb;
            color: var(--gray-800);
            line-height: 1.6;
        }

        .main-dashboard {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header Styles */
        .support-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 20px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo i {
            font-size: 24px;
            color: var(--primary-color);
        }

        .logo h1 {
            font-size: 24px;
            font-weight: 600;
            color: var(--gray-800);
        }

        .admin-controls {
            display: flex;
            gap: 10px;
        }

        .admin-controls button {
            padding: 8px 16px;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }

        .refresh-btn {
            background-color: var(--light-color);
            color: var(--gray-700);
        }

        .refresh-btn:hover {
            background-color: var(--gray-200);
        }

        .logout-btn {
            background-color: var(--danger-color);
            color: var(--white);
        }

        .logout-btn:hover {
            background-color: #c82333;
        }

        /* Dashboard Stats */
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .stat-card {
            background-color: var(--white);
            border-radius: var(--border-radius);
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-icon i {
            font-size: 20px;
            color: var(--white);
        }

        .stat-icon.total {
            background-color: var(--primary-color);
        }

        .stat-icon.unread {
            background-color: var(--warning-color);
        }

        .stat-icon.open {
            background-color: var(--success-color);
        }

        .stat-icon.closed {
            background-color: var(--secondary-color);
        }

        .stat-info h3 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-info p {
            font-size: 14px;
            color: var(--gray-600);
        }

        /* Ticket Filters */
        .ticket-filters {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            background-color: var(--white);
            padding: 10px 15px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .filter-btn {
            padding: 8px 16px;
            border: none;
            background-color: var(--gray-200);
            color: var(--gray-700);
            border-radius: 20px;
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
        }

        .filter-btn:hover {
            background-color: var(--gray-300);
        }

        .filter-btn.active {
            background-color: var(--primary-color);
            color: var(--white);
        }

        .search-container {
            margin-left: auto;
            position: relative;
        }

        .search-container input {
            padding: 8px 16px 8px 36px;
            border: 1px solid var(--gray-300);
            border-radius: 20px;
            width: 250px;
            font-size: 14px;
            transition: var(--transition);
        }

        .search-container input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(74, 108, 247, 0.2);
        }

        .search-container i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-500);
        }

        /* Ticket Grid */
        .ticket-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .ticket-wrapper {
            position: relative;
            transition: var(--transition);
        }

        .ticket-wrapper.unread .ticket-summary {
            border-left: 4px solid var(--warning-color);
        }

        .ticket-wrapper.open .ticket-summary {
  position: relative;
  border-left: 4px solid var(--success-color);
  overflow: hidden; /* Ensures the pseudo-element doesn’t extend outside */
}

.ticket-wrapper.open .ticket-summary::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  bottom: 0;
  width: 4px; /* Same as your border width */
  /* Create a striped effect using a repeating linear gradient */
  background: repeating-linear-gradient(
    to bottom,
    var(--success-color) 0,
    var(--success-color) 10px,
    transparent 10px,
    transparent 20px
  );
  animation: snakeBorder 2s linear infinite;
}

@keyframes snakeBorder {
  from {
    background-position: 0 0;
  }
  to {
    background-position: 0 20px;
  }
}


        .ticket-wrapper.closed .ticket-summary {
            border-left: 4px solid var(--secondary-color);
        }

        .ticket-summary {
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            cursor: pointer;
            transition: var(--transition);
        }

        .ticket-summary:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            background-color: var(--gray-100);
            border-bottom: 1px solid var(--gray-200);

        }

        .ticket-status {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .status-indicator.open {
            background-color: var(--success-color);
            
        }


        .status-indicator.closed {
            background-color: var(--secondary-color);
        }

        .status-text {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .unread-badge {
            background-color: var(--warning-color);
            color: var(--dark-color);
            font-size: 12px;
            font-weight: 700;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-content {
            padding: 15px;
        }

        .ticket-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--gray-800);
        }

        .ticket-user, .ticket-preview, .ticket-date {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
            font-size: 14px;
            color: var(--gray-700);
        }

        .ticket-preview {
            color: var(--gray-600);
            font-style: italic;
        }

        .card-actions {
            padding: 10px 15px;
            border-top: 1px solid var(--gray-200);
            text-align: right;
        }

        .view-btn {
            padding: 6px 12px;
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: var(--transition);
        }

        .view-btn:hover {
            background-color: var(--primary-dark);
        }

        /* Ticket Conversation */
        .ticket-conversation {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80%;
            max-width: 800px;
            height: 80vh;
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            overflow: hidden;
            flex-direction: column;
        }

        .conversation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: var(--primary-color);
            color: var(--white);
        }

        .conversation-header h3 {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 18px;
            font-weight: 600;
        }

        .conversation-actions {
            display: flex;
            gap: 10px;
        }

        .conversation-actions button {
            padding: 6px 12px;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: var(--transition);
        }

        .close-ticket, .reopen-ticket {
            background-color: var(--white);
            color: var(--primary-color);
        }

        .close-ticket:hover, .reopen-ticket:hover {
            background-color: var(--gray-100);
        }

        .delete-ticket {
            background-color: var(--danger-color);
            color: var(--white);
        }

        .delete-ticket:hover {
            background-color: #c82333;
        }

        .close-conversation {
            background-color: transparent;
            color: var(--white);
            border: 1px solid var(--white);
        }

        .close-conversation:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .conversation-body {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background-color: var(--gray-100);
        }

        .message {
            margin-bottom: 20px;
            max-width: 80%;
            border-radius: var(--border-radius);
            padding: 15px;
            position: relative;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .user-message {
            background-color: var(--white);
            margin-right: auto;
            border-left: 4px solid var(--info-color);
        }

        .admin-message {
            background-color: #e7f3ff;
            margin-left: auto;
            border-right: 4px solid var(--primary-color);
        }

        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .message-sender {
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .message-time {
            color: var(--gray-600);
            font-size: 12px;
        }

        .message-content {
            font-size: 15px;
            line-height: 1.5;
            word-break: break-word;
        }

        .conversation-footer {
            padding: 15px 20px;
            background-color: var(--white);
            border-top: 1px solid var(--gray-200);
        }

        .reply-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .form-group {
            width: 100%;
        }

        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--gray-300);
            border-radius: var(--border-radius);
            resize: none;
            font-family: inherit;
            font-size: 15px;
            transition: var(--transition);
        }

        textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(74, 108, 247, 0.2);
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
        }

        .send-reply {
            padding: 8px 16px;
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }

        .send-reply:hover {
            background-color: var(--primary-dark);
        }

        .conversation-footer.closed {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .closed-message {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--gray-600);
            font-style: italic;
        }

        /* No Tickets */
        .no-tickets {
            grid-column: 1 / -1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 50px;
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .no-tickets i {
            font-size: 48px;
            color: var(--gray-400);
            margin-bottom: 20px;
        }

        .no-tickets p {
            font-size: 18px;
            color: var(--gray-600);
        }

        /* Dashboard Footer */
        .dashboard-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-top: 20px;
        }

        .dashboard-footer p {
            font-size: 14px;
            color: var(--gray-600);
        }

        .back-button {
            padding: 8px 16px;
            background-color: var(--secondary-color);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }

        .back-button:hover {
            background-color: var(--gray-700);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: var(--white);
            border-radius: var(--border-radius);
            width: 400px;
            max-width: 90%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: modalFadeIn 0.3s ease;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid var(--gray-200);
        }

        .modal-header h3 {
            font-size: 18px;
            font-weight: 600;
        }

        .close-modal {
            font-size: 24px;
            cursor: pointer;
            color: var(--gray-600);
            transition: var(--transition);
        }

        .close-modal:hover {
            color: var(--danger-color);
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 15px 20px;
            border-top: 1px solid var(--gray-200);
        }

        .modal-footer button {
            padding: 8px 16px;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-cancel {
            background-color: var(--gray-200);
            color: var(--gray-700);
        }

        .btn-cancel:hover {
            background-color: var(--gray-300);
        }

        .btn-confirm {
            background-color: var(--danger-color);
            color: var(--white);
        }

        .btn-confirm:hover {
            background-color: #c82333;
        }

        /* Overlay */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 900;
        }

        /* Toast Container */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }
        
        .toast {
            padding: 12px 20px;
            margin-bottom: 10px;
            border-radius: 4px;
            color: white;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            animation: toastFadeIn 0.3s ease, toastFadeOut 0.3s ease 2.7s;
            opacity: 0;
            max-width: 300px;
        }
        
        .toast.success {
            background-color: #28a745;
        }
        
        .toast.error {
            background-color: #dc3545;
        }
        
        .toast.info {
            background-color: #17a2b8;
        }
        
        .toast.warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        @keyframes toastFadeIn {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes toastFadeOut {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(20px);
            }
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .ticket-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .dashboard-stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .ticket-grid {
                grid-template-columns: 1fr;
            }
            
            .dashboard-stats {
                grid-template-columns: 1fr;
            }
            
            .ticket-filters {
                flex-wrap: wrap;
            }
            
            .search-container {
                margin-left: 0;
                margin-top: 10px;
                width: 100%;
            }
            
            .search-container input {
                width: 100%;
            }
            
            .ticket-conversation {
                width: 95%;
            }
        }

        @media (max-width: 576px) {
            .support-header {
                flex-direction: column;
                gap: 15px;
            }
            
            .admin-controls {
                width: 100%;
                justify-content: space-between;
            }
            
            .conversation-header {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
            
            .conversation-actions {
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>
</head>

<body>
    <div class="main-dashboard">
        <header class="support-header">
            <div class="logo">
                <i class="fas fa-headset"></i>
                <h1>Support Dashboard</h1>
            </div>
            <div class="admin-controls">
                <button class="refresh-btn" onclick="location.reload()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </header>

        <div class="dashboard-stats">
            <div class="stat-card">
                <div class="stat-icon total">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $totalTickets; ?></h3>
                    <p>Total Tickets</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon unread">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $unreadTickets; ?></h3>
                    <p>Unread Tickets</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon open">
                    <i class="fas fa-lock-open"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $openTickets; ?></h3>
                    <p>Open Tickets</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon closed">
                    <i class="fas fa-lock"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $closedTickets; ?></h3>
                    <p>Closed Tickets</p>
                </div>
            </div>
        </div>

        <div class="ticket-filters">
            <button class="filter-btn active" data-filter="all">All Tickets</button>
            <button class="filter-btn" data-filter="unread">Unread</button>
            <button class="filter-btn" data-filter="open">Open</button>
            <button class="filter-btn" data-filter="closed">Closed</button>
            <div class="search-container">
                <input type="text" id="ticket-search" placeholder="Search tickets...">
                <i class="fas fa-search"></i>
            </div>
        </div>

        <div class="ticket-grid">
            <?php if (!empty($tickets)): ?>
                <?php foreach ($tickets as $ticketId => $messages): 
                    $unreadCount = 0;
                    foreach ($messages as $message) {
                        if ($message['sender'] === 'user' && $message['is_read'] == 0) {
                            $unreadCount++;
                        }
                    }
                    $status = strtolower($messages[0]['status']);
                    $ticketClass = $unreadCount > 0 ? 'unread' : '';
                    $ticketClass .= ' ' . $status;
                ?>
                <div class="ticket-wrapper <?php echo $ticketClass; ?>" data-ticket-id="<?php echo htmlspecialchars($ticketId); ?>">
                    <!-- Ticket Card -->
                    <div class="ticket-summary card" onclick="toggleConversation(<?php echo htmlspecialchars($ticketId); ?>)">
                        <div class="card-header">
                            <div class="ticket-status">
                                <span class="status-indicator <?php echo $status; ?>"></span>
                                <span class="status-text"><?php echo ucfirst($status); ?></span>
                            </div>
                            <?php if ($unreadCount > 0): ?>
                                <div class="unread-badge" title="<?php echo $unreadCount; ?> unread messages">
                                    <?php echo $unreadCount; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-content">
                            <h3 class="ticket-title">
                                #<?php echo htmlspecialchars($ticketId); ?>
                                <?php if (!empty($messages[0]['subject'])): ?>
                                    - <?php echo htmlspecialchars($messages[0]['subject']); ?>
                                <?php endif; ?>
                            </h3>
                            <p class="ticket-user">
                                <i class="fas fa-user"></i>
                                <?php echo htmlspecialchars($messages[0]['user_username']); ?>
                            </p>
                            <p class="ticket-preview">
                                <?php 
                                    $lastUserMessage = '';
                                    foreach ($messages as $message) {
                                        if (!empty($message['content'])) {
                                            $lastUserMessage = $message['content'];
                                            break;
                                        }
                                    }
                                    echo htmlspecialchars(substr($lastUserMessage, 0, 50)) . (strlen($lastUserMessage) > 50 ? '...' : '');
                                ?>
                            </p>
                            <p class="ticket-date">
                                <i class="far fa-clock"></i>
                                <?php 
                                    $date = new DateTime($messages[0]['ticket_created_at']);
                                    echo $date->format('M d, Y - H:i');
                                ?>
                            </p>
                        </div>
                        <div class="card-actions">
                            <button class="view-btn">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </div>
                    </div>

                    <!-- Ticket Conversation -->
                    <div class="ticket-conversation" id="conversation-<?php echo htmlspecialchars($ticketId); ?>">
                        <div class="conversation-header">
                            <h3>
                                <i class="fas fa-comments"></i>
                                Ticket #<?php echo htmlspecialchars($ticketId); ?> Conversation
                            </h3>
                            <div class="conversation-actions">
                                <?php if ($status === 'open'): ?>
                                    <button onclick="closeTicket(<?php echo htmlspecialchars($ticketId); ?>)" class="close-ticket">
                                        <i class="fas fa-lock"></i> Close
                                    </button>
                                <?php else: ?>
                                   
                                <?php endif; ?>
                                <button onclick="deleteTicket(<?php echo htmlspecialchars($ticketId); ?>)" class="delete-ticket">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                                <button onclick="toggleConversation(<?php echo htmlspecialchars($ticketId); ?>)" class="close-conversation">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="conversation-body">
                            <?php foreach ($messages as $message): 
                                if (empty($message['content'])) continue;
                            ?>
                                <div class="message <?php echo $message['sender'] === 'admin' ? 'admin-message' : 'user-message'; ?>">
                                    <div class="message-header">
                                        <span class="message-sender">
                                            <?php if ($message['sender'] === 'admin'): ?>
                                                <i class="fas fa-user-shield"></i> Admin
                                            <?php else: ?>
                                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($messages[0]['user_username']); ?>
                                            <?php endif; ?>
                                        </span>
                                        <span class="message-time">
                                            <?php 
                                                $msgDate = new DateTime($message['message_created_at']);
                                                echo $msgDate->format('M d, Y - H:i');
                                            ?>
                                        </span>
                                    </div>
                                    <div class="message-content">
                                        <?php echo nl2br(htmlspecialchars($message['content'] ?? '[Message could not be decrypted]')); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if ($status === 'open'): ?>
                            <div class="conversation-footer">
                                <form method="POST" action="admin_reply.php" class="reply-form">
                                    <input type="hidden" name="ticket_id" value="<?php echo htmlspecialchars($ticketId); ?>">
                                    <div class="form-group">
                                        <textarea name="message" placeholder="Type your reply here..." rows="3" maxlength="500" required></textarea>
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" class="send-reply">
                                            <i class="fas fa-paper-plane"></i> Send Reply
                                        </button>
                                    </div>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="conversation-footer closed">
                                <div class="closed-message">
                                    <i class="fas fa-lock"></i>
                                    This ticket is closed. Reopen it to send a reply.
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-tickets">
                    <i class="fas fa-ticket-alt"></i>
                    <p>No support tickets found.</p>
                </div>
            <?php endif; ?>
        </div>

        <footer class="dashboard-footer">
            <p>&copy; <?php echo date('Y'); ?> Admin Support Dashboard. All rights reserved.</p>
            <button class="back-button" onclick="history.back()">
                <i class="fas fa-arrow-left"></i> Back
            </button>
        </footer>
    </div>

    <div id="confirmation-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title">Confirm Action</h3>
                <span class="close-modal">&times;</span>
            </div>
            <div class="modal-body">
                <p id="modal-message">Are you sure you want to perform this action?</p>
            </div>
            <div class="modal-footer">
                <button id="modal-cancel" class="btn-cancel">Cancel</button>
                <button id="modal-confirm" class="btn-confirm">Confirm</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Update unread messages status
            updateUnreadStatus();
            
            // Initialize filter buttons
            initializeFilters();
            
            // Initialize search functionality
            initializeSearch();
            
            // Initialize modal
            initializeModal();
        });

        // Function to toggle conversation visibility
        function toggleConversation(ticketId) {
            const conversationElement = document.getElementById(`conversation-${ticketId}`);
            const overlay = document.querySelector('.overlay') || createOverlay();
            
            if (conversationElement.style.display === 'flex') {
                conversationElement.style.display = 'none';
                overlay.style.display = 'none';
                document.body.style.overflow = 'auto';
            } else {
                // Close any open conversations first
                document.querySelectorAll('.ticket-conversation').forEach(conv => {
                    conv.style.display = 'none';
                });
                
                conversationElement.style.display = 'flex';
                overlay.style.display = 'block';
                document.body.style.overflow = 'hidden';
                
                // Mark messages as read
                markTicketAsRead(ticketId);
                
                // Scroll to the bottom of the conversation
                const conversationBody = conversationElement.querySelector('.conversation-body');
                conversationBody.scrollTop = conversationBody.scrollHeight;
            }
        }

        // Create overlay if it doesn't exist
        function createOverlay() {
            const overlay = document.createElement('div');
            overlay.className = 'overlay';
            overlay.addEventListener('click', function() {
                document.querySelectorAll('.ticket-conversation').forEach(conv => {
                    conv.style.display = 'none';
                });
                overlay.style.display = 'none';
                document.body.style.overflow = 'auto';
            });
            document.body.appendChild(overlay);
            return overlay;
        }

        // Function to mark ticket as read
        function markTicketAsRead(ticketId) {
            const ticketWrapper = document.querySelector(`.ticket-wrapper[data-ticket-id="${ticketId}"]`);
            
            // Send AJAX request to mark messages as read
            $.ajax({
                url: "../ajax/mark_read.php",
                method: "POST",
                data: { ticket_id: ticketId },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        // Update UI to reflect read status
                        const unreadBadge = ticketWrapper.querySelector('.unread-badge');
                        if (unreadBadge) {
                            unreadBadge.remove();
                        }
                        ticketWrapper.classList.remove('unread');
                        
                        // Update the unread count in the stats
                        updateUnreadCount();
                    }
                }
            });
        }

        // Function to update unread count in stats
        function updateUnreadCount() {
            const unreadTickets = document.querySelectorAll('.ticket-wrapper.unread').length;
            const unreadCountElement = document.querySelector('.stat-card:nth-child(2) .stat-info h3');
            if (unreadCountElement) {
                unreadCountElement.textContent = unreadTickets;
            }
        }

        // Function to close a ticket
        function closeTicket(ticketId) {
            showConfirmationModal(
                'Close Ticket',
                `Are you sure you want to close ticket #${ticketId}?`,
                function() {
                    $.ajax({
                        url: "../ajax/update_ticket_status.php",
                        method: "POST",
                        data: { 
                            ticket_id: ticketId,
                            status: 'closed'
                        },
                        dataType: "json",
                        success: function(response) {
                            if (response.success) {
                                // Update UI to reflect closed status
                                const ticketWrapper = document.querySelector(`.ticket-wrapper[data-ticket-id="${ticketId}"]`);
                                ticketWrapper.classList.remove('open');
                                ticketWrapper.classList.add('closed');
                                
                                // Update status indicator and text
                                const statusIndicator = ticketWrapper.querySelector('.status-indicator');
                                const statusText = ticketWrapper.querySelector('.status-text');
                                if (statusIndicator) statusIndicator.className = 'status-indicator closed';
                                if (statusText) statusText.textContent = 'Closed';
                                
                                // Update conversation actions
                                const conversationActions = document.querySelector(`#conversation-${ticketId} .conversation-actions`);
                                if (conversationActions) {
                                    conversationActions.innerHTML = `
                                        <button onclick="reopenTicket(${ticketId})" class="reopen-ticket">
                                            <i class="fas fa-lock-open"></i> Reopen
                                        </button>
                                        <button onclick="deleteTicket(${ticketId})" class="delete-ticket">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                        <button onclick="toggleConversation(${ticketId})" class="close-conversation">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    `;
                                }
                                
                                // Update conversation footer
                                const conversationFooter = document.querySelector(`#conversation-${ticketId} .conversation-footer`);
                                if (conversationFooter) {
                                    conversationFooter.className = 'conversation-footer closed';
                                    conversationFooter.innerHTML = `
                                        <div class="closed-message">
                                            <i class="fas fa-lock"></i>
                                            This ticket is closed. Reopen it to send a reply.
                                        </div>
                                    `;
                                }
                                
                                // Update stats
                                updateTicketStats();
                                
                                // Show success message
                                showToast('Ticket closed successfully', 'success');
                            } else {
                                showToast('Failed to close ticket', 'error');
                            }
                        },
                        error: function() {
                            showToast('An error occurred', 'error');
                        }
                    });
                }
            );
        }

        // Function to reopen a ticket
        function reopenTicket(ticketId) {
            showConfirmationModal(
                'Reopen Ticket',
                `Are you sure you want to reopen ticket #${ticketId}?`,
                function() {
                    $.ajax({
                        url: "../ajax/update_ticket_status.php",
                        method: "POST",
                        data: { 
                            ticket_id: ticketId,
                            status: 'open'
                        },
                        dataType: "json",
                        success: function(response) {
                            if (response.success) {
                                // Update UI to reflect open status
                                const ticketWrapper = document.querySelector(`.ticket-wrapper[data-ticket-id="${ticketId}"]`);
                                ticketWrapper.classList.remove('closed');
                                ticketWrapper.classList.add('open');
                                
                                // Update status indicator and text
                                const statusIndicator = ticketWrapper.querySelector('.status-indicator');
                                const statusText = ticketWrapper.querySelector('.status-text');
                                if (statusIndicator) statusIndicator.className = 'status-indicator open';
                                if (statusText) statusText.textContent = 'Open';
                                
                                // Update conversation actions
                                const conversationActions = document.querySelector(`#conversation-${ticketId} .conversation-actions`);
                                if (conversationActions) {
                                    conversationActions.innerHTML = `
                                        <button onclick="closeTicket(${ticketId})" class="close-ticket">
                                            <i class="fas fa-lock"></i> Close
                                        </button>
                                        <button onclick="deleteTicket(${ticketId})" class="delete-ticket">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                        <button onclick="toggleConversation(${ticketId})" class="close-conversation">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    `;
                                }
                                
                                // Update conversation footer
                                const conversationFooter = document.querySelector(`#conversation-${ticketId} .conversation-footer`);
                                if (conversationFooter) {
                                    conversationFooter.className = 'conversation-footer';
                                    conversationFooter.innerHTML = `
                                        <form method="POST" action="admin_reply.php" class="reply-form">
                                            <input type="hidden" name="ticket_id" value="${ticketId}">
                                            <div class="form-group">
                                                <textarea name="message" placeholder="Type your reply here..." rows="3" maxlength="500" required></textarea>
                                            </div>
                                            <div class="form-actions">
                                                <button type="submit" class="send-reply">
                                                    <i class="fas fa-paper-plane"></i> Send Reply
                                                </button>
                                            </div>
                                        </form>
                                    `;
                                }
                                
                                // Update stats
                                updateTicketStats();
                                
                                // Show success message
                                showToast('Ticket reopened successfully', 'success');
                            } else {
                                showToast('Failed to reopen ticket', 'error');
                            }
                        },
                        error: function() {
                            showToast('An error occurred', 'error');
                        }
                    });
                }
            );
        }

        // Function to delete a ticket
        function deleteTicket(ticketId) {
            showConfirmationModal(
                'Delete Ticket',
                `Are you sure you want to delete ticket #${ticketId}? This action cannot be undone.`,
                function() {
                    $.ajax({
                        url: "../ajax/delete_ticket.php",
                        method: "POST",
                        data: { ticket_id: ticketId },
                        dataType: "json",
                        success: function(response) {
                            if (response.success) {
                                // Remove ticket from UI
                                const ticketWrapper = document.querySelector(`.ticket-wrapper[data-ticket-id="${ticketId}"]`);
                                const conversation = document.getElementById(`conversation-${ticketId}`);
                                const overlay = document.querySelector('.overlay');
                                
                                if (ticketWrapper) ticketWrapper.remove();
                                if (conversation) conversation.remove();
                                if (overlay) overlay.style.display = 'none';
                                
                                document.body.style.overflow = 'auto';
                                
                                // Update stats
                                updateTicketStats();
                                
                                // Show success message
                                showToast('Ticket deleted successfully', 'success');
                                
                                // If no tickets left, show no tickets message
                                if (document.querySelectorAll('.ticket-wrapper').length === 0) {
                                    const ticketGrid = document.querySelector('.ticket-grid');
                                    ticketGrid.innerHTML = `
                                        <div class="no-tickets">
                                            <i class="fas fa-ticket-alt"></i>
                                            <p>No support tickets found.</p>
                                        </div>
                                    `;
                                }
                            } else {
                                showToast('Failed to delete ticket', 'error');
                            }
                        },
                        error: function() {
                            showToast('An error occurred', 'error');
                        }
                    });
                }
            );
        }

        // Function to update unread status
        function updateUnreadStatus() {
            $.ajax({
                url: "../ajax/update_unread.php",
                method: "POST",
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        console.log('Unread status updated successfully');
                    } else {
                        console.error('Error:', response.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                }
            });
        }

        // Function to initialize filter buttons
        function initializeFilters() {
            const filterButtons = document.querySelectorAll('.filter-btn');
            
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    // Get filter value
                    const filter = this.getAttribute('data-filter');
                    
                    // Filter tickets
                    filterTickets(filter);
                });
            });
        }

        // Function to filter tickets
        function filterTickets(filter) {
            const tickets = document.querySelectorAll('.ticket-wrapper');
            
            tickets.forEach(ticket => {
                if (filter === 'all') {
                    ticket.style.display = 'block';
                } else if (filter === 'unread' && ticket.classList.contains('unread')) {
                    ticket.style.display = 'block';
                } else if (filter === 'open' && ticket.classList.contains('open')) {
                    ticket.style.display = 'block';
                } else if (filter === 'closed' && ticket.classList.contains('closed')) {
                    ticket.style.display = 'block';
                } else {
                    ticket.style.display = 'none';
                }
            });
            
            // Check if no tickets are visible
            const visibleTickets = document.querySelectorAll('.ticket-wrapper[style="display: block"]');
            const ticketGrid = document.querySelector('.ticket-grid');
            const noTicketsElement = document.querySelector('.no-tickets');
            
            if (visibleTickets.length === 0 && !noTicketsElement) {
                const noTicketsMessage = document.createElement('div');
                noTicketsMessage.className = 'no-tickets';
                noTicketsMessage.innerHTML = `
                    <i class="fas fa-ticket-alt"></i>
                    <p>No ${filter === 'all' ? '' : filter} tickets found.</p>
                `;
                ticketGrid.appendChild(noTicketsMessage);
            } else if (visibleTickets.length > 0 && noTicketsElement) {
                noTicketsElement.remove();
            }
        }

        // Function to initialize search functionality
        function initializeSearch() {
            const searchInput = document.getElementById('ticket-search');
            
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const tickets = document.querySelectorAll('.ticket-wrapper');
                    
                    tickets.forEach(ticket => {
                        const ticketTitle = ticket.querySelector('.ticket-title').textContent.toLowerCase();
                        const ticketUser = ticket.querySelector('.ticket-user').textContent.toLowerCase();
                        const ticketPreview = ticket.querySelector('.ticket-preview').textContent.toLowerCase();
                        
                        if (ticketTitle.includes(searchTerm) || ticketUser.includes(searchTerm) || ticketPreview.includes(searchTerm)) {
                            ticket.style.display = 'block';
                        } else {
                            ticket.style.display = 'none';
                        }
                    });
                    
                    // Check if no tickets are visible
                    const visibleTickets = document.querySelectorAll('.ticket-wrapper[style="display: block"]');
                    const ticketGrid = document.querySelector('.ticket-grid');
                    const noTicketsElement = document.querySelector('.no-tickets');
                    
                    if (visibleTickets.length === 0 && !noTicketsElement) {
                        const noTicketsMessage = document.createElement('div');
                        noTicketsMessage.className = 'no-tickets';
                        noTicketsMessage.innerHTML = `
                            <i class="fas fa-search"></i>
                            <p>No tickets found matching "${searchTerm}".</p>
                        `;
                        ticketGrid.appendChild(noTicketsMessage);
                    } else if (visibleTickets.length > 0 && noTicketsElement) {
                        noTicketsElement.remove();
                    }
                });
            }
        }

        // Function to update ticket stats
        function updateTicketStats() {
            const totalTickets = document.querySelectorAll('.ticket-wrapper').length;
            const unreadTickets = document.querySelectorAll('.ticket-wrapper.unread').length;
            const openTickets = document.querySelectorAll('.ticket-wrapper.open').length;
            const closedTickets = document.querySelectorAll('.ticket-wrapper.closed').length;
            
            const statElements = document.querySelectorAll('.stat-info h3');
            if (statElements.length >= 4) {
                statElements[0].textContent = totalTickets;
                statElements[1].textContent = unreadTickets;
                statElements[2].textContent = openTickets;
                statElements[3].textContent = closedTickets;
            }
        }

        // Function to initialize modal
        function initializeModal() {
            const modal = document.getElementById('confirmation-modal');
            const closeModal = document.querySelector('.close-modal');
            const cancelButton = document.getElementById('modal-cancel');
            
            if (closeModal) {
                closeModal.addEventListener('click', function() {
                    modal.style.display = 'none';
                });
            }
            
            if (cancelButton) {
                cancelButton.addEventListener('click', function() {
                    modal.style.display = 'none';
                });
            }
            
            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }

        // Function to show confirmation modal
        function showConfirmationModal(title, message, confirmCallback) {
            const modal = document.getElementById('confirmation-modal');
            const modalTitle = document.getElementById('modal-title');
            const modalMessage = document.getElementById('modal-message');
            const confirmButton = document.getElementById('modal-confirm');
            
            modalTitle.textContent = title;
            modalMessage.textContent = message;
            
            // Remove previous event listeners
            const newConfirmButton = confirmButton.cloneNode(true);
            confirmButton.parentNode.replaceChild(newConfirmButton, confirmButton);
            
            // Add new event listener
            newConfirmButton.addEventListener('click', function() {
                confirmCallback();
                modal.style.display = 'none';
            });
            
            // Show modal
            modal.style.display = 'flex';
        }

        // Function to show toast notification
        function showToast(message, type = 'info') {
            // Check if toast container exists, if not create it
            let toastContainer = document.querySelector('.toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.className = 'toast-container';
                document.body.appendChild(toastContainer);
            }
            
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            
            // Set icon based on type
            let icon = '';
            switch (type) {
                case 'success':
                    icon = '<i class="fas fa-check-circle"></i>';
                    break;
                case 'error':
                    icon = '<i class="fas fa-exclamation-circle"></i>';
                    break;
                case 'warning':
                    icon = '<i class="fas fa-exclamation-triangle"></i>';
                    break;
                default:
                    icon = '<i class="fas fa-info-circle"></i>';
            }
            
            toast.innerHTML = `${icon} ${message}`;
            toastContainer.appendChild(toast);
            
            // Force reflow to trigger animation
            void toast.offsetWidth;
            toast.style.opacity = '1';
            
            // Remove toast after animation
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }
    </script>
</body>
</html>


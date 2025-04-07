<?php
require '../config.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
  header("Location: admin_login.php?redirect=panel.php");
  exit();
}

// Process AJAX requests BEFORE any HTML output.
if (isset($_POST['action'])) {
    // Update user
    if ($_POST['action'] === 'updateUser') {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            http_response_code(403);
            echo json_encode(["success" => false, "message" => "Unauthorized"]);
            exit();
        }
        $id       = $_POST['id'];
        $username = $_POST['username'];
        $jabber   = $_POST['jabber'];
        $telegram = $_POST['telegram'];
        $balance  = $_POST['balance'];
        $seller   = $_POST['seller'];
        $status   = $_POST['status'];
        $total_earned  = $_POST['total_earned']; 
        $banned   = $_POST['banned']; 
        $seller_percentage   = $_POST['seller_percentage'];
        $active   = $_POST['active'];
        $stmt = $pdo->prepare("UPDATE users SET username = ?, jabber = ?, telegram = ?, balance = ?, seller = ?, status = ?, total_earned = ?, banned = ?, seller_percentage = ?, active = ? WHERE id = ?");
        if ($stmt->execute([$username, $jabber, $telegram, $balance, $seller, $status, $total_earned, $banned, $seller_percentage, $active, $id])) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to update user"]);
        }
        exit();
    }
    // Add user
    if ($_POST['action'] === 'addUser') {
      if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
          http_response_code(403);
          echo json_encode(["success" => false, "message" => "Unauthorized"]);
          exit();
      }
      // Hash the password with BCRYPT, same as in your registration script
      $hashed_password = password_hash($_POST['password'], PASSWORD_BCRYPT);
  
      $stmt = $pdo->prepare("INSERT INTO users (username, password, jabber, telegram, balance, seller, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
      if ($stmt->execute([$_POST['username'], $hashed_password, $_POST['jabber'], $_POST['telegram'], $_POST['balance'], $_POST['seller'], $_POST['status']])) {
          echo json_encode(["success" => true]);
      } else {
          echo json_encode(["success" => false, "message" => "Failed to add user"]);
      }
      exit();
  }
  
    // Reset balance – set total_earned to 0
    if ($_POST['action'] === 'resetBalance') {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            http_response_code(403);
            echo json_encode(["success" => false, "message" => "Unauthorized"]);
            exit();
        }
        $id = $_POST['id'];
        $stmt = $pdo->prepare("UPDATE users SET total_earned = 0 WHERE id = ?");
        if ($stmt->execute([$id])) {
            echo json_encode(["success" => true, "message" => "Balance reset successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to reset balance"]);
        }
        exit();
    }
}

// ------------------------------
// Continue with normal page output
// ------------------------------

// Compute statistics for display
$totalUsers   = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$activeUsers  = $pdo->query("SELECT COUNT(*) FROM users WHERE active = 1")->fetchColumn();
$totalBalance = $pdo->query("SELECT SUM(balance) FROM users")->fetchColumn();
$totalSellers = $pdo->query("SELECT COUNT(*) FROM users WHERE seller = 1")->fetchColumn();

// Fetch all users
$stmt = $pdo->query("
    SELECT 
        id, username, password, jabber, telegram, balance, seller, seller_percentage,
        credit_cards_balance, credit_cards_total_earned, dumps_balance, dumps_total_earned,
        banned, role, status, active, total_earned
    FROM users
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CardVault User Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* Basic variables and styles */
    :root {
      --primary: #4361ee;
      --primary-light: #4895ef;
      --secondary: #4cc9f0;
      --accent: #f72585;
      --success: #2ecc71;
      --dark: #1a1a2e;
      --dark-light: #16213e;
      --light: #f8f9fa;
      --border: rgba(255,255,255,0.1);
      --text: #e6e6e6;
      --text-secondary: #b0b0b0;
      --card-bg: rgba(22,33,62,0.8);
      --card-hover: rgba(26,26,46,0.9);
      --backdrop: rgba(0,0,0,0.7);
    }
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; transition: all 0.25s ease; }
    body { background: linear-gradient(135deg, var(--dark) 0%, var(--dark-light) 100%); color: var(--text); min-height: 100vh; overflow-x: hidden; }
    .container { max-width: 100%; margin: 0 auto; padding: 20px; }
    .dashboard-header { display: flex; justify-content: space-between; align-items: center; padding: 20px; margin-bottom: 30px; }
    .dashboard-title { font-size: 28px; font-weight: 700; background: linear-gradient(to right, var(--primary-light), var(--accent)); -webkit-background-clip: text; background-clip: text; color: transparent; text-shadow: 0 0 10px rgba(67,97,238,0.3); position: relative; }
    .dashboard-title::after { content: ''; position: absolute; bottom: -8px; left: 0; width: 40px; height: 4px; background: linear-gradient(to right, var(--primary-light), var(--accent)); border-radius: 2px; }
    .controls { display: flex; gap: 15px; align-items: center; }
    .search-container { display: flex; align-items: center; background-color: var(--dark-light); border-radius: 50px; padding: 10px 20px; width: 300px; border: 1px solid var(--border); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    .search-container:focus-within { box-shadow: 0 0 0 2px var(--primary-light), 0 4px 20px rgba(67,97,238,0.3); border-color: var(--primary-light); }
    .search-container i { color: var(--primary-light); margin-right: 10px; font-size: 16px; }
    .search-input { border: none; background: transparent; outline: none; width: 100%; font-size: 14px; color: var(--text); }
    .theme-toggle { background: var(--dark-light); border: 1px solid var(--border); color: var(--text); width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    .theme-toggle:hover { background: var(--primary); color: white; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(67,97,238,0.4); }
    .card { background: var(--card-bg); border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.2); backdrop-filter: blur(10px); border: 1px solid var(--border); margin-bottom: 30px; }
    .card-header { padding: 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); }
    .card-title { font-size: 18px; font-weight: 600; color: var(--text); }
    .table-container { overflow-x: auto; padding: 0; }
    table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 14px; }
    th, td { padding: 16px 20px; border-bottom: 1px solid var(--border); }
    tr:hover { background-color: var(--card-hover); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    /* Modal styles */
    .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: var(--backdrop); z-index: 100; justify-content: center; align-items: center; backdrop-filter: blur(5px); animation: fadeIn 0.3s ease; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    .modal-content { background: var(--dark-light); border-radius: 16px; width: 500px; max-width: 90%; padding: 30px; border: 1px solid var(--border); transform: translateY(20px); animation: slideUp 0.3s forwards; }
    @keyframes slideUp { to { transform: translateY(0); } }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid var(--border); }
    .modal-title { font-size: 20px; font-weight: 600; background: linear-gradient(to right, var(--primary-light), var(--secondary)); -webkit-background-clip: text; background-clip: text; color: transparent; }
    .modal-body { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; max-height: 70vh; overflow-y: auto; padding-right: 10px; }
    @media (max-width: 992px) { .modal-body { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 600px) { .modal-body { grid-template-columns: 1fr; } }
    .close-btn { background: none; border: none; font-size: 24px; cursor: pointer; color: var(--text-secondary); transition: all 0.2s; }
    .close-btn:hover { color: var(--danger); transform: rotate(90deg); }
    .form-group { margin-bottom: 20px; }
    .form-control { width: 100%; padding: 12px 15px; background: var(--dark); border: 1px solid var(--border); border-radius: 8px; font-size: 14px; color: var(--text); }
    .modal-footer { display: flex; justify-content: flex-end; gap: 15px; margin-top: 25px; padding-top: 20px; border-top: 1px solid var(--border); }
    /* Reset Balance button group */
    .reset-group { display: flex; align-items: center; gap: 10px; }
    /* Loader & Notification */
    .loader { width: 100%; height: 3px; position: fixed; top: 0; left: 0; background: linear-gradient(to right, var(--primary), var(--accent), var(--primary-light)); z-index: 1000; animation: gradientMove 1.5s ease infinite; }
    @keyframes gradientMove { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
    .notification { position: fixed; bottom: 30px; right: 30px; z-index: 1000; transform: translateY(100px); opacity: 0; transition: all 0.3s ease; }
    .notification-content { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: white; padding: 15px 25px; border-radius: 8px; display: flex; align-items: center; gap: 10px; box-shadow: 0 5px 20px rgba(67,97,238,0.4); }
    .show-notification { transform: translateY(0); opacity: 1; }
    /* Column controls */
    #columnControls { margin-bottom: 15px; }
    #columnControls label { margin-right: 10px; font-size: 14px; cursor: pointer; }
  </style>
</head>
<body>
  <div class="loader"></div>
  <div class="container">
    <div class="dashboard-header">
      <h1 class="dashboard-title">Cardvault User Management</h1>
      <div class="controls">
        <div class="search-container">
          <i class="fas fa-search"></i>
          <input type="text" id="searchInput" class="search-input" placeholder="Search users...">
        </div>
        <button class="theme-toggle" id="themeToggle" data-tooltip="Toggle Theme">
          <i class="fas fa-moon"></i>
        </button>
      </div>
    </div>
     <!-- Stat Cards -->
     <div class="stats-container">

  <!-- Total Balance: filter rows with balance > 0 -->
  <div class="stat-card glow filter-card" data-filter="balance" data-operator="gt" data-value="0" style="cursor:pointer;">
    <div class="stat-icon stat-balance"><i class="fas fa-dollar-sign"></i></div>
    <div class="stat-value"><?php echo $totalBalance; ?></div>
    <div class="stat-label">Total Balance</div>
  </div>

  <!-- Total Users: resets filter (show all rows) -->
  <div class="stat-card glow filter-card" data-filter="all" style="cursor:pointer;">
    <div class="stat-icon stat-users"><i class="fas fa-users"></i></div>
    <div class="stat-value"><?php echo $totalUsers; ?></div>
    <div class="stat-label">Total Users</div>
  </div>

  <!-- Active Users: filter rows where active = 1 -->
  <div class="stat-card glow filter-card" data-filter="active" data-value="1" style="cursor:pointer;">
    <div class="stat-icon stat-active"><i class="fas fa-check"></i></div>
    <div class="stat-value"><?php echo $activeUsers; ?></div>
    <div class="stat-label">Active Users</div>
  </div>

  <!-- Total Sellers: filter rows where seller = 1 -->
  <div class="stat-card glow filter-card" data-filter="seller" data-value="1" style="cursor:pointer;">
    <div class="stat-icon stat-users"><i class="fas fa-users"></i></div>
    <div class="stat-value"><?php echo $totalSellers; ?></div>
    <div class="stat-label">Total Sellers</div>
  </div>
</div>




</div>
    <!-- Column Toggle Controls -->
    <!-- <div id="columnControls">
      <label><input type="checkbox" data-col="id" checked> ID</label>
      <label><input type="checkbox" data-col="username" checked> Username</label>
      <label><input type="checkbox" data-col="jabber" checked> Jabber</label>
      <label><input type="checkbox" data-col="telegram" checked> Telegram</label>
      <label><input type="checkbox" data-col="balance" checked> Balance</label>
      <label><input type="checkbox" data-col="seller" checked> Seller</label>
      <label><input type="checkbox" data-col="seller_percentage" checked> Seller %</label>
      <label><input type="checkbox" data-col="credit_cards_balance" checked> CC Balance</label>
      <label><input type="checkbox" data-col="credit_cards_total_earned" checked> CC Earned</label>
      <label><input type="checkbox" data-col="dumps_balance" checked> Dumps Balance</label>
      <label><input type="checkbox" data-col="dumps_total_earned" checked> Dumps Earned</label>
      <label><input type="checkbox" data-col="total_earned" checked> Payable amount</label>
      <label><input type="checkbox" data-col="balances_combined" checked> Overall Balance</label>
      <label><input type="checkbox" data-col="banned" checked> Banned</label>
      <label><input type="checkbox" data-col="role" checked> Role</label>
      <label><input type="checkbox" data-col="status" checked> Status</label>
      <label><input type="checkbox" data-col="active" checked> Active</label>
    </div> -->
    <!-- User Table -->
    <div class="card">
      <div class="card-header">
        <h2 class="card-title">User Management</h2>
        <button class="btn btn-primary" onclick="addNewUser()">Add New User</button>
      </div>
      <div class="table-container">
        <table id="userTable">
          <thead>
            <tr>
              <th data-col="id">ID</th>
              <th data-col="username">Username</th>
              <th data-col="jabber">Jabber</th>
              <th data-col="telegram">Telegram</th>
              <th data-col="balance">Balance</th>
              <th data-col="seller">Seller</th>
              <th data-col="seller_percentage">Seller %</th>
              <th data-col="credit_cards_balance">CC Balance</th>
              <th data-col="credit_cards_total_earned">CC Earned</th>
              <th data-col="dumps_balance">Dumps Balance</th>
              <th data-col="dumps_total_earned">Dumps Earned</th>
              <th data-col="total_earned">Payable amount</th>
              <th data-col="balances_combined">Overall Balance</th>
              <th data-col="banned">Banned</th>
              <th data-col="role">Role</th>
              <th data-col="status">Status</th>
              <th data-col="active">Active</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($user = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
              <tr data-id="<?php echo $user['id']; ?>">
                <td data-col="id"><?php echo $user['id']; ?></td>
                <td data-col="username"><?php echo htmlspecialchars($user['username']); ?></td>
                <td data-col="jabber"><?php echo htmlspecialchars($user['jabber'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td data-col="telegram"><?php echo htmlspecialchars($user['telegram'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td data-col="balance"><?php echo htmlspecialchars($user['balance']); ?></td>
                <td data-col="seller"><?php echo htmlspecialchars($user['seller']); ?></td>
                <td data-col="seller_percentage"><?php echo htmlspecialchars($user['seller_percentage']); ?></td>
                <td data-col="credit_cards_balance"><?php echo htmlspecialchars($user['credit_cards_balance']); ?></td>
                <td data-col="credit_cards_total_earned"><?php echo htmlspecialchars($user['credit_cards_total_earned']); ?></td>
                <td data-col="dumps_balance"><?php echo htmlspecialchars($user['dumps_balance']); ?></td>
                <td data-col="dumps_total_earned"><?php echo htmlspecialchars($user['dumps_total_earned']); ?></td>
                <td data-col="total_earned"><?php echo htmlspecialchars($user['total_earned']); ?></td>
                <td data-col="balances_combined">
                  <?php echo number_format(floatval($user['credit_cards_balance']) + floatval($user['dumps_balance']), 2); ?>
                </td>
                <td data-col="banned"><?php echo htmlspecialchars($user['banned']); ?></td>
                <td data-col="role"><?php echo htmlspecialchars($user['role']); ?></td>
                <td data-col="status"><?php echo htmlspecialchars($user['status']); ?></td>
                <td data-col="active"><?php echo htmlspecialchars($user['active']); ?></td>
                
                <td>
                  <button class="btn btn-primary" onclick="openEditModal(<?php echo $user['id']; ?>)">Edit</button>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <!-- Edit Modal -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">Edit User</h2>
        <button class="close-btn" onclick="closeModal()">&times;</button>
      </div>
      <form id="editForm">
        <input type="hidden" id="editId">
        <div class="modal-body">
          <!-- Column 1 -->
          <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" class="form-control" placeholder="Enter username">
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="text" id="password" class="form-control" placeholder="Enter password">
          </div>
          <div class="form-group">
            <label for="jabber">Jabber</label>
            <input type="text" id="jabber" class="form-control" placeholder="Enter Jabber">
          </div>
          <div class="form-group">
            <label for="telegram">Telegram</label>
            <input type="text" id="telegram" class="form-control" placeholder="Enter Telegram">
          </div>
          <div class="form-group">
            <label for="balance">Balance</label>
            <input type="number" id="balance" class="form-control" step="0.01" placeholder="Enter balance">
          </div>
          <!-- Column 2 -->
          <div class="form-group">
            <label for="seller">Seller</label>
            <select id="seller" class="form-control">
              <option value="0">No</option>
              <option value="1">Yes</option>
            </select>
          </div>
          <div class="form-group">
            <label for="seller_percentage">Seller Percentage</label>
            <input type="number" id="seller_percentage" class="form-control" step="0.01" placeholder="Enter seller percentage">
          </div>
          <div class="form-group">
            <label for="credit_cards_balance">CC Balance</label>
            <input type="number" id="credit_cards_balance" class="form-control" step="0.01" placeholder="Enter CC Balance">
          </div>
          <div class="form-group">
            <label for="credit_cards_total_earned">CC TE</label>
            <input type="number" id="credit_cards_total_earned" class="form-control" step="0.01" placeholder="Enter CC Total Earned">
          </div>
          <div class="form-group">
            <label for="dumps_balance">Dumps Balance</label>
            <input type="number" id="dumps_balance" class="form-control" step="0.01" placeholder="Enter Dumps Balance">
          </div>
          <!-- Column 3 -->
          <div class="form-group">
            <label for="dumps_total_earned">Dumps TE</label>
            <input type="number" id="dumps_total_earned" class="form-control" step="0.01" placeholder="Enter Dumps Total Earned">
          </div>
          <div class="form-group">
            <label for="banned">Banned</label>
            <select id="banned" class="form-control">
              <option value="0">No</option>
              <option value="1">Yes</option>
            </select>
          </div>
          <div class="form-group">
            <label for="role">Role</label>
            <select id="role" class="form-control">
              <option value="user">User</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <div class="form-group">
            <label for="status">Status</label>
            <select id="status" class="form-control">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
          <div class="form-group">
            <label for="active">Active</label>
            <select id="active" class="form-control">
              <option value="0">No</option>
              <option value="1">Yes</option>
            </select>
          </div>
          <div class="form-group">
            <label for="total_earned">Payable amount</label>
            <div class="reset-group">
              <input type="number" id="total_earned" class="form-control" step="0.01" placeholder="Enter Current Balance">
              <button type="button" id="resetBalance" class="btn btn-danger">Reset</button>
            </div>
          </div>
        </div> <!-- end modal-body -->
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" onclick="closeModal()"><i class="fas fa-times"></i> Cancel</button>
          <button type="button" class="btn btn-primary" onclick="saveChanges()"><i class="fas fa-save"></i> Save Changes</button>
        </div>
      </form>
    </div>
  </div>
  
  <!-- Add User Modal (similar structure) -->
  <div id="addModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">Add New User</h2>
        <button class="close-btn" onclick="closeAddModal()">&times;</button>
      </div>
      <form id="addForm">
        <div class="form-group form-floating">
          <input type="text" id="newUsername" class="form-control" placeholder=" ">
          <label class="form-label" for="newUsername">Username</label>
        </div>
        <div class="form-group form-floating">
          <input type="password" id="newPassword" class="form-control" placeholder=" ">
          <label class="form-label" for="newPassword">Password</label>
        </div>
        <div class="form-group form-floating">
          <input type="text" id="newJabber" class="form-control" placeholder=" ">
          <label class="form-label" for="newJabber">Jabber</label>
        </div>
        <div class="form-group form-floating">
          <input type="text" id="newTelegram" class="form-control" placeholder=" ">
          <label class="form-label" for="newTelegram">Telegram</label>
        </div>
        <div class="form-group form-floating">
          <input type="number" id="newBalance" class="form-control" step="0.01" value="0.00" placeholder=" ">
          <label class="form-label" for="newBalance">Balance</label>
        </div>
        <div class="form-group">
          <label class="form-label" for="newSeller">Seller</label>
          <select id="newSeller" class="form-control">
            <option value="0">No</option>
            <option value="1">Yes</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label" for="newStatus">Status</label>
          <select id="newStatus" class="form-control">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" onclick="closeAddModal()"><i class="fas fa-times"></i> Cancel</button>
          <button type="button" class="btn btn-primary" onclick="addUser()"><i class="fas fa-plus"></i> Add User</button>
        </div>
      </form>
    </div>
  </div>
  
  <!-- Success Notification -->
  <div id="notification" class="notification" style="display: none;">
    <div class="notification-content">
      <i class="fas fa-check-circle"></i>
      <span id="notificationMessage">Operation successful!</span>
    </div>
  </div>
  
  <script>

document.querySelectorAll('.filter-card').forEach(card => {
  card.addEventListener('click', () => {
    const filterKey = card.getAttribute('data-filter');
    
    // If the filter is "all", reset the filter (show all rows)
    if (filterKey === 'all') {
      document.querySelectorAll('#userTable tbody tr').forEach(row => {
        row.style.display = '';
      });
      return;
    }
    
    const filterValue = card.getAttribute('data-value');
    const operator = card.getAttribute('data-operator'); // "gt" for greater than, etc.

    document.querySelectorAll('#userTable tbody tr').forEach(row => {
      // Get the cell's text content based on the filter key.
      // Assumes your table has <td data-col="balance">, <td data-col="active">, etc.
      const cell = row.querySelector(`td[data-col="${filterKey}"]`);
      if (!cell) {
        row.style.display = 'none';
        return;
      }
      
      const cellValue = cell.textContent.trim();
      
      let show = false;
      if (operator === 'gt') {
        // For numeric comparisons, parse as float.
        show = parseFloat(cellValue) > parseFloat(filterValue);
      } else {
        // Default equality check.
        show = cellValue === filterValue;
      }
      
      row.style.display = show ? '' : 'none';
    });
  });
});



// Filter table rows based on seller value.
function filterTable(sellerValue) {
  const rows = document.querySelectorAll("#userTable tbody tr");
  rows.forEach(row => {
    const sellerCell = row.querySelector('td[data-col="seller"]');
    if (sellerCell && sellerCell.textContent.trim() === sellerValue) {
      row.style.display = "";
    } else {
      row.style.display = "none";
    }
  });
}

// Attach click events for filter cards.
const filterSellersElem = document.getElementById("filterSellers");
if (filterSellersElem) {
  filterSellersElem.addEventListener("click", function() {
    filterTable("1");
  });
}

const filterUsersElem = document.getElementById("filterUsers");
if (filterUsersElem) {
  filterUsersElem.addEventListener("click", function() {
    filterTable("0");
  });
}

const searchInput = document.getElementById('searchInput');
searchInput.addEventListener('keyup', function () {
  const searchValue = this.value.toLowerCase();
  const rows = document.querySelectorAll('#userTable tbody tr');
  rows.forEach(row => {
    // Combine the text content of all cells into one string and check if it contains the search term.
    const rowText = row.textContent.toLowerCase();
    row.style.display = rowText.includes(searchValue) ? '' : 'none';
  });
});


    // Theme toggle
    const themeToggle = document.getElementById('themeToggle');
    themeToggle.addEventListener('click', function() {
      document.body.classList.toggle('light-mode');
      const icon = themeToggle.querySelector('i');
      if (document.body.classList.contains('light-mode')) {
        icon.classList.remove('fa-moon');
        icon.classList.add('fa-sun');
      } else {
        icon.classList.remove('fa-sun');
        icon.classList.add('fa-moon');
      }
    });
    
    // Column toggle functionality
    document.addEventListener('DOMContentLoaded', function() {
      document.querySelectorAll('#columnControls input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
          const col = this.getAttribute('data-col');
          const display = this.checked ? '' : 'none';
          document.querySelectorAll('th[data-col="' + col + '"]').forEach(el => { el.style.display = display; });
          document.querySelectorAll('td[data-col="' + col + '"]').forEach(el => { el.style.display = display; });
        });
      });
    });
    
    // Open Edit Modal
    function openEditModal(id) {
      const row = document.querySelector('tr[data-id="' + id + '"]');
      if (!row) return;
      const getCell = col => {
        const cell = row.querySelector('td[data-col="' + col + '"]');
        return cell ? cell.textContent.trim() : '';
      };
      document.getElementById('editId').value = id;
      document.getElementById('username').value = getCell('username');
      document.getElementById('password').value = getCell('password');
      document.getElementById('jabber').value = getCell('jabber');
      document.getElementById('telegram').value = getCell('telegram');
      document.getElementById('balance').value = getCell('balance');
      document.getElementById('seller').value = getCell('seller');
      document.getElementById('seller_percentage').value = getCell('seller_percentage');
      document.getElementById('credit_cards_balance').value = getCell('credit_cards_balance');
      document.getElementById('credit_cards_total_earned').value = getCell('credit_cards_total_earned');
      document.getElementById('dumps_balance').value = getCell('dumps_balance');
      document.getElementById('dumps_total_earned').value = getCell('dumps_total_earned');
      document.getElementById('banned').value = getCell('banned');
      document.getElementById('role').value = getCell('role');
      document.getElementById('status').value = getCell('status');
      document.getElementById('active').value = getCell('active');
      document.getElementById('total_earned').value = getCell('total_earned');
      document.getElementById('editModal').style.display = 'flex';
    }
    
    function closeModal() {
      document.getElementById('editModal').style.display = 'none';
    }
    
    function addNewUser() {
      document.getElementById('addModal').style.display = 'flex';
    }
    
    function closeAddModal() {
      document.getElementById('addModal').style.display = 'none';
    }
    
    // AJAX for adding a new user
    function addUser() {
      const username = document.getElementById('newUsername').value;
      const password = document.getElementById('newPassword').value;
      const jabber = document.getElementById('newJabber').value;
      const telegram = document.getElementById('newTelegram').value;
      const balance = document.getElementById('newBalance').value;
      const seller = document.getElementById('newSeller').value;
      const status = document.getElementById('newStatus').value;
      if (!username || !password) {
        alert("Username and password are required.");
        return;
      }
      const formData = new FormData();
      formData.append('action', 'addUser');
      formData.append('username', username);
      formData.append('password', password);
      formData.append('jabber', jabber);
      formData.append('telegram', telegram);
      formData.append('balance', balance);
      formData.append('seller', seller);
      formData.append('status', status);
      fetch(window.location.href, { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showNotification('User added successfully!');
            closeAddModal();
            window.location.reload();
          } else {
            showNotification('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error adding user:', error);
          showNotification('Error adding user.');
        });
    }
    
    // AJAX for saving changes (update user)
    function saveChanges() {
      const formData = new FormData();
      formData.append('action', 'updateUser');
      formData.append('id', document.getElementById('editId').value);
      formData.append('username', document.getElementById('username').value);
      formData.append('jabber', document.getElementById('jabber').value);
      formData.append('telegram', document.getElementById('telegram').value);
      formData.append('balance', document.getElementById('balance').value);
      formData.append('credit_cards_balance', document.getElementById('credit_cards_balance').value);
      formData.append('dumps_balance', document.getElementById('dumps_balance').value);
      formData.append('seller_percentage', document.getElementById('seller_percentage').value);
      formData.append('credit_cards_total_earned', document.getElementById('credit_cards_total_earned').value);
      formData.append('dumps_total_earned', document.getElementById('dumps_total_earned').value);
      formData.append('total_earned', document.getElementById('total_earned').value);
      formData.append('role', document.getElementById('role').value);
      formData.append('banned', document.getElementById('banned').value);
      formData.append('seller', document.getElementById('seller').value);
      formData.append('status', document.getElementById('status').value);
      formData.append('active', document.getElementById('active').value);
      
      fetch(window.location.href, { method: 'POST', body: formData })
        .then(res => res.json())
        .then(result => {
          if (result.success) {
            showNotification('User updated successfully!');
            window.location.reload();
          } else {
            showNotification('Error: ' + result.message);
          }
        })
        .catch(err => {
          console.error('Update failed', err);
          showNotification('Error updating user.');
        });
    }
    
    // Reset balance AJAX functionality
    document.getElementById('resetBalance').addEventListener('click', function() {
      const totalEarnedInput = document.getElementById('total_earned');
      if (!totalEarnedInput) {
        console.error('Total Earned input not found.');
        return;
      }
      // Set the current balance input value to 0
      totalEarnedInput.value = 0;
      
      // Retrieve the user ID from the hidden input in the edit modal
      const userId = document.getElementById('editId').value;
      if (!userId) {
        alert('User ID not found.');
        return;
      }
      const formData = new FormData();
      formData.append('action', 'resetBalance');
      formData.append('id', userId);
      formData.append('total_earned', 0);
      
      fetch(window.location.href, { method: 'POST', body: formData })
        .then(response => response.json())
        .then(result => {
           if(result.success) {
               alert('Balance reset successfully.');
           } else {
               alert('Error resetting balance: ' + result.message);
           }
        })
        .catch(err => {
           console.error('Reset error:', err);
           alert('Error resetting balance.');
        });
    });
    
    // Notification function
    function showNotification(message) {
      const notification = document.getElementById('notification');
      document.getElementById('notificationMessage').textContent = message;
      notification.style.display = 'block';
      notification.classList.add('show-notification');
      setTimeout(() => {
          notification.classList.remove('show-notification');
          setTimeout(() => { notification.style.display = 'none'; }, 300);
      }, 3000);
    }
    
    // Hide loader after page load
    setTimeout(() => {
      document.querySelector('.loader').style.display = 'none';
    }, 1000);
  </script>
  
  <style>
        :root {
            --primary: #4361ee;
            --primary-light: #4895ef;
            --primary-dark: #3a0ca3;
            --secondary: #4cc9f0;
            --accent: #f72585;
            --success: #2ecc71;
            --warning: #f39c12;
            --danger: #e74c3c;
            --dark: #1a1a2e;
            --dark-light: #16213e;
            --light: #f8f9fa;
            --border: rgba(255, 255, 255, 0.1);
            --text: #e6e6e6;
            --text-secondary: #b0b0b0;
            --card-bg: rgba(22, 33, 62, 0.8);
            --card-hover: rgba(26, 26, 46, 0.9);
            --backdrop: rgba(0, 0, 0, 0.7);
        }

        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: all 0.25s ease;
        }

        body {
            background: linear-gradient(135deg, var(--dark) 0%, var(--dark-light) 100%);
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            margin-bottom: 30px;
            position: relative;
            z-index: 10;
        }

        .dashboard-title {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(to right, var(--primary-light), var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 0 0 10px rgba(67, 97, 238, 0.3);
            position: relative;
        }

        .dashboard-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 40px;
            height: 4px;
            background: linear-gradient(to right, var(--primary-light), var(--accent));
            border-radius: 2px;
        }

        .controls {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .search-container {
            display: flex;
            align-items: center;
            background-color: var(--dark-light);
            border-radius: 50px;
            padding: 10px 20px;
            width: 300px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .search-container:focus-within {
            box-shadow: 0 0 0 2px var(--primary-light), 0 4px 20px rgba(67, 97, 238, 0.3);
            border-color: var(--primary-light);
        }

        .search-container i {
            color: var(--primary-light);
            margin-right: 10px;
            font-size: 16px;
        }

        .search-input {
            border: none;
            background: transparent;
            outline: none;
            width: 100%;
            font-size: 14px;
            color: var(--text);
        }

        .search-input::placeholder {
            color: var(--text-secondary);
        }

        .theme-toggle {
            background: var(--dark-light);
            border: 1px solid var(--border);
            color: var(--text);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .theme-toggle:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
        }

        .card {
            background: var(--card-bg);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border);
            margin-bottom: 30px;
        }

        .card-header {
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-title i {
            color: var(--primary-light);
        }

        .table-container {
            overflow-x: auto;
            padding: 0;
            position: relative;
        }

        .table-container::-webkit-scrollbar {
            height: 8px;
            background-color: var(--dark);
        }

        .table-container::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 4px;
        }

        .table-container::-webkit-scrollbar-thumb:hover {
            background: var(--primary-light);
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 0;
            font-size: 14px;
        }

        th {
            background-color: var(--dark);
            color: var(--text);
            font-weight: 600;
            text-align: left;
            padding: 16px 20px;
            position: sticky;
            top: 0;
            z-index: 10;
            border-bottom: 2px solid var(--border);
        }

        th:first-child {
            border-top-left-radius: 8px;
        }

        th:last-child {
            border-top-right-radius: 8px;
        }

        td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            color: var(--text);
        }

        tr:hover {
            background-color: var(--card-hover);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .icon {
            margin-right: 5px;
            color: var(--primary-light);
            font-size: 14px;
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: all 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(67, 97, 238, 0.5);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger) 0%, #ff6b6b 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
        }

        .btn-danger:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(231, 76, 60, 0.5);
        }

        .btn i {
            margin-right: 8px;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .status-active {
            background: rgba(46, 204, 113, 0.15);
            color: #2ecc71;
            border: 1px solid rgba(46, 204, 113, 0.3);
        }

        .status-inactive {
            background: rgba(231, 76, 60, 0.15);
            color: #e74c3c;
            border: 1px solid rgba(231, 76, 60, 0.3);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: var(--backdrop);
            z-index: 100;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background: var(--dark-light);
            border-radius: 16px;
            width: 500px;
            max-width: 90%;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
            padding: 30px;
            border: 1px solid var(--border);
            transform: translateY(20px);
            animation: slideUp 0.3s forwards;
        }

        @keyframes slideUp {
            to { transform: translateY(0); }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border);
        }

        .modal-title {
            font-size: 20px;
            font-weight: 600;
            background: linear-gradient(to right, var(--primary-light), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .modal-body {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    max-height: 70vh;
    overflow-y: auto;
    padding-right: 10px;
}

@media (max-width: 992px) {
    .modal-body {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 600px) {
    .modal-body {
        grid-template-columns: 1fr;
    }
}

#resetBalance { display: none !important;
}

        .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--text-secondary);
            transition: all 0.2s;
        }

        .close-btn:hover {
            color: var(--danger);
            transform: rotate(90deg);
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text);
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            background: var(--dark);
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            color: var(--text);
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }

        .form-control::placeholder {
            color: var(--text-secondary);
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }

        .form-floating {
            position: relative;
        }

        .form-floating .form-control {
            height: 60px;
            padding: 20px 15px 10px;
        }

        .form-floating .form-label {
            position: absolute;
            top: 0;
            left: 15px;
            height: 100%;
            padding: 20px 0 10px;
            pointer-events: none;
            transform-origin: 0 0;
            transition: all 0.2s ease;
            color: var(--text-secondary);
        }

        .form-floating .form-control:focus ~ .form-label,
        .form-floating .form-control:not(:placeholder-shown) ~ .form-label {
            transform: translateY(-10px) scale(0.85);
            color: var(--primary-light);
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 25px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border: 1px solid var(--border);
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            border-color: var(--primary-light);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            font-size: 20px;
        }

        .stat-users {
            background: linear-gradient(135deg, rgba(67, 97, 238, 0.2) 0%, rgba(76, 201, 240, 0.2) 100%);
            color: var(--primary-light);
        }

        .stat-revenue {
            background: linear-gradient(135deg, rgba(46, 204, 113, 0.2) 0%, rgba(26, 188, 156, 0.2) 100%);
            color: var(--success);
        }

        .stat-transactions {
            background: linear-gradient(135deg, rgba(247, 37, 133, 0.2) 0%, rgba(181, 23, 158, 0.2) 100%);
            color: var(--accent);
        }

        .stat-active {
            background: linear-gradient(135deg, rgba(243, 156, 18, 0.2) 0%, rgba(230, 126, 34, 0.2) 100%);
            color: var(--warning);
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 14px;
            font-weight: 500;
        }

        .stat-change {
            margin-top: 10px;
            display: flex;
            align-items: center;
            font-size: 13px;
            font-weight: 600;
        }

        .stat-change.positive {
            color: var(--success);
        }

        .stat-change.negative {
            color: var(--danger);
        }

        .stat-change i {
            margin-right: 5px;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @media (max-width: 992px) {
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }

            .controls {
                width: 100%;
                flex-direction: column;
                align-items: stretch;
            }

            .search-container {
                width: 100%;
            }

            .stats-container {
                grid-template-columns: 1fr;
            }

            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .theme-toggle {
                position: absolute;
                top: 20px;
                right: 20px;
            }

            th, td {
                padding: 12px 15px;
                font-size: 13px;
            }

            .btn {
                padding: 8px 12px;
                font-size: 12px;
            }
        }

        .loader {
            width: 100%;
            height: 3px;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(to right, var(--primary), var(--accent), var(--primary-light));
            z-index: 1000;
            background-size: 200% 200%;
            animation: gradientMove 1.5s ease infinite;
        }

        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        [data-tooltip] {
            position: relative;
        }

        [data-tooltip]:before {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            padding: 8px 12px;
            background: var(--dark);
            color: var(--text);
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            pointer-events: none;
            z-index: 100;
        }

        [data-tooltip]:hover:before {
            opacity: 1;
            visibility: visible;
            bottom: calc(100% + 10px);
        }

        body.light-mode {
            --dark: #ffffff;
            --dark-light: #f8f9fa;
            --light: #1a1a2e;
            --border: rgba(0, 0, 0, 0.1);
            --text: #333333;
            --text-secondary: #666666;
            --card-bg: rgba(255, 255, 255, 0.9);
            --card-hover: rgba(248, 249, 250, 0.95);
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        body.light-mode .dashboard-title {
            text-shadow: 0 0 10px rgba(67, 97, 238, 0.2);
        }

        body.light-mode th {
            background-color: #f1f3f5;
            color: #333;
        }

        body.light-mode .form-control {
            background: #fff;
            color: #333;
        }

        body.light-mode .modal-content {
            background: #fff;
        }

        body.light-mode .table-container::-webkit-scrollbar {
            background-color: #f1f3f5;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--accent);
            color: white;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .glow {
            position: relative;
        }

        .glow::after {
            content: '';
            position: absolute;
            top: -20px;
            left: -20px;
            right: -20px;
            bottom: -20px;
            background: radial-gradient(circle, rgba(67, 97, 238, 0.3) 0%, transparent 70%);
            z-index: -1;
            opacity: 0;
            transition: opacity 0.5s;
        }

        .glow:hover::after {
            opacity: 1;
        } 
#columnControls { margin-bottom: 15px; }
#columnControls label { margin-right: 10px; font-size: 14px; cursor: pointer; }

        /* Additional styles for notification */
        .notification {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .notification-content {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 5px 20px rgba(67, 97, 238, 0.4);
        }

        .notification-content i {
            font-size: 20px;
        }

        .show-notification {
            transform: translateY(0);
            opacity: 1;
        }




        
    </style>
</body>
</html>

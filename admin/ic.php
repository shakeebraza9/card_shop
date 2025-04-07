<?php
include_once('../global.php');
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get admin info from session
$admin_username = $_SESSION['admin_username'] ?? 'Admin';
$admin_avatar = $_SESSION['admin_avatar'] ?? '../images/default.png';

// Function to check if a menu item is active
function isActive($page) {
    $current_page = $_GET['page'] ?? 'cards';
    return $current_page === $page ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <style>

#contentArea {
  padding-top: var(--header-height) !important; /* or simply 64px */
  padding-left: 500px;
}
    /* Base styles with improved variables */
    :root {
      --primary: #4f46e5;
      --primary-light: #6366f1;
      --primary-dark: #4338ca;
      --success: #10b981;
      --warning: #f59e0b;
      --danger: #ef4444;
      --info: #3b82f6;
      --text: #1f2937;
      --text-light: #6b7280;
      --bg: #ffffff;
      --bg-light: #f9fafb;
      --bg-dark: #f3f4f6;
      --border: #e5e7eb;
      --sidebar-width: 260px;
      --sidebar-width-collapsed: 70px;
      --header-height: 64px;
      --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
      --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
      --radius: 0.375rem;
      --transition: all 0.3s ease;
    }
    
    /* Dark mode support */
    .dark-mode {
      --primary: #6366f1;
      --primary-light: #818cf8;
      --primary-dark: #4f46e5;
      --text: #f9fafb;
      --text-light: #d1d5db;
      --bg: #111827;
      --bg-light: #1f2937;
      --bg-dark: #0f172a;
      --border: #374151;
    }
    
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    
    body {
      margin: 0;
      padding: 0;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
      color: var(--text);
      background-color: var(--bg);
      transition: var(--transition);
      height: 100vh;
      overflow: hidden;
    }
    
    /* Improved sidebar with collapse functionality */
    #sidebar {
      width: var(--sidebar-width);
      height: 100%;
      background-color: var(--bg-light);
      border-right: 1px solid var(--border);
      position: fixed;
      left: 0;
      top: 0;
      bottom: 0;
      z-index: 50;
      overflow-y: auto;
      overflow-x: hidden;
      transition: var(--transition);
    }
    
    #sidebar.collapsed {
      width: var(--sidebar-width-collapsed);
    }
    
    #sidebar.collapsed .sidebar-text {
      display: none;
    }
    
    #sidebar.collapsed .sidebar-header h1 {
      display: none;
    }
    
    #sidebar.collapsed .sidebar-section-title {
      text-align: center;
    }
    
    #sidebar.collapsed .sidebar-menu-item {
      text-align: center;
      padding: 0.625rem 0;
    }
    
    #sidebar.collapsed .sidebar-menu-item i {
      margin-right: 0;
    }
    
    /* Main content area with responsive adjustments */
    #main-content {
      flex: 1;
      margin-left: var(--sidebar-width);
      height: 100vh;
      overflow-y: auto;
      transition: var(--transition);
    }
    
    #main-content.expanded {
      margin-left: var(--sidebar-width-collapsed);
    }
    
    /* Header styling */
    header {
      height: var(--header-height);
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 1.5rem;
      background-color: var(--bg);
      position: sticky;
      top: 0;
      z-index: 30;
      box-shadow: var(--shadow);
    }
    
  
    
    /* Sidebar components */
    .sidebar-header {
      padding: 1.25rem;
      display: flex;
      align-items: center;
      border-bottom: 1px solid var(--border);
    }
    
    .sidebar-logo {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--primary), var(--primary-light));
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: bold;
      box-shadow: var(--shadow);
      flex-shrink: 0;
    }
    
    .sidebar-header h1 {
      margin-left: 0.75rem;
      font-size: 1.25rem;
      font-weight: 700;
      background: linear-gradient(to right, var(--primary), var(--primary-light));
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      white-space: nowrap;
    }
    
    .sidebar-section {
      margin-bottom: 1rem;
    }
    
    .sidebar-section-title {
      padding: 0.5rem 1.25rem;
      font-size: 0.75rem;
      font-weight: 600;
      color: var(--text-light);
      text-transform: uppercase;
    }
    
    .sidebar-menu {
      list-style: none;
      padding: 0;
      margin: 0.5rem 0;
    }
    
    .sidebar-menu-item a {
      display: flex;
      align-items: center;
      padding: 0.625rem 1.25rem;
      color: var(--text);
      text-decoration: none;
      border-left: 3px solid transparent;
      transition: var(--transition);
    }
    
    .sidebar-menu-item a:hover {
      background-color: rgba(0, 0, 0, 0.05);
      color: var(--primary);
    }
    
    .sidebar-menu-item a.active {
      color: var(--primary);
      border-left-color: var(--primary);
      background-color: rgba(79, 70, 229, 0.1);
    }
    
    .sidebar-menu-item i {
      margin-right: 0.75rem;
      font-size: 1.25rem;
      width: 20px;
      text-align: center;
    }
    
    .sidebar-footer {
      position: sticky;
      bottom: 0;
      padding: 1rem;
      border-top: 1px solid var(--border);
      background-color: var(--bg-light);
      display: flex;
      align-items: center;
    }
    
    .sidebar-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      overflow: hidden;
      margin-right: 0.75rem;
      flex-shrink: 0;
    }
    
    .sidebar-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    
    .sidebar-user-info {
      overflow: hidden;
    }
    
    .sidebar-username {
      font-weight: 500;
      font-size: 0.875rem;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    
    .sidebar-role {
      font-size: 0.75rem;
      color: var(--text-light);
    }
    
    /* Header components */
    .header-actions {
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    
    .toggle-sidebar {
      background: none;
      border: none;
      color: var(--text);
      cursor: pointer;
      font-size: 1.25rem;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0.5rem;
      border-radius: var(--radius);
      transition: var(--transition);
    }
    
    .toggle-sidebar:hover {
      background-color: var(--bg-light);
    }
    
    .toggle-theme {
      background: none;
      border: none;
      color: var(--text);
      cursor: pointer;
      font-size: 1.25rem;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0.5rem;
      border-radius: var(--radius);
      transition: var(--transition);
    }
    
    .toggle-theme:hover {
      background-color: var(--bg-light);
    }
    
    .logout-button {
      background-color: var(--danger);
      color: white;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: var(--radius);
      cursor: pointer;
      font-size: 0.875rem;
      transition: var(--transition);
    }
    
    .logout-button:hover {
      background-color: #dc2626;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
      #sidebar {
        transform: translateX(-100%);
      }
      
      #sidebar.mobile-visible {
        transform: translateX(0);
      }
      
      #main-content {
        margin-left: 0;
      }
      
      .mobile-sidebar-toggle {
        display: block;
      }
    }
  </style>
  <!-- Add Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div style="display: flex; height: 100vh; width: 100%;">
    <!-- Sidebar -->
    <div id="sidebar">
      <!-- Sidebar header -->
      <div class="sidebar-header">
        <div class="sidebar-logo">
          AD
        </div>
        <h1 class="sidebar-text">Admin Dashboard</h1>
      </div>
      
      <!-- Main navigation -->
<div class="sidebar-section">
  <div class="sidebar-section-title">
    <span class="sidebar-text">Main</span>
  </div>
  <ul class="sidebar-menu">
    <li class="sidebar-menu-item">
      <a href="./thedash.php" class="<?php echo isActive('dashboard'); ?>">
        <i class="fas fa-tachometer-alt"></i>
        <span class="sidebar-text">Dashboard</span>
      </a>
    </li>
    <li class="sidebar-menu-item">
      <a href="./um.php" class="<?php echo isActive('users'); ?>">
        <i class="fas fa-users"></i>
        <span class="sidebar-text">User Settings</span>
      </a>
    </li>
    <li class="sidebar-menu-item">
      <a href="./au.php" class="<?php echo isActive('update'); ?>">
        <i class="fas fa-sync-alt"></i>
        <span class="sidebar-text">Admin Update</span>
      </a>
    </li>
  </ul>
</div>

<!-- Uploaders & Settings section -->
<div class="sidebar-section">
  <div class="sidebar-section-title">
    <span class="sidebar-text">Uploaders & Settings</span>
  </div>
  <ul class="sidebar-menu">
    <li class="sidebar-menu-item">
      <a href="./ut.php" class="<?php echo isActive('upload'); ?>">
        <i class="fas fa-upload"></i>
        <span class="sidebar-text">Upload Tools</span>
      </a>
    </li>
    <li class="sidebar-menu-item">
      <a href="./ic.php" class="<?php echo isActive('cards'); ?>">
        <i class="fas fa-credit-card"></i>
        <span class="sidebar-text">Import Cards</span>
      </a>
    </li>
    <li class="sidebar-menu-item">
      <a href="./id.php" class="<?php echo isActive('dumps'); ?>">
        <i class="fas fa-database"></i>
        <span class="sidebar-text">Import Dumps</span>
      </a>
    </li>
    <li class="sidebar-menu-item">
      <a href="./sc.php" class="<?php echo isActive('support'); ?>">
        <i class="fas fa-comments"></i>
        <span class="sidebar-text">Support Chat</span>
      </a>
    </li>
    <li class="sidebar-menu-item">
      <a href="./al.php" class="<?php echo isActive('activity'); ?>">
        <i class="fas fa-history"></i>
        <span class="sidebar-text">Activity Log</span>
      </a>
    </li>
    <li class="sidebar-menu-item">
      <a href="./setting.php" class="<?php echo isActive('settings'); ?>">
        <i class="fas fa-cog"></i>
        <span class="sidebar-text">Settings</span>
      </a>
    </li>
  </ul>
</div>


      <!-- Sidebar footer with user profile -->
      <div class="sidebar-footer">
        <div class="sidebar-avatar">
          <img src="<?php echo htmlspecialchars($admin_avatar); ?>" alt="Admin">
        </div>
        <div class="sidebar-user-info">
          <div class="sidebar-username"><?php echo htmlspecialchars($admin_username); ?></div>
          <div class="sidebar-role sidebar-text">Administrator</div>
        </div>
      </div>
    </div>
    
    <!-- Main content area -->
    <div id="main-content">
      <header>
        <div class="header-left">
          <button class="toggle-sidebar" id="toggleSidebar">
            <i class="fas fa-bars"></i>
          </button>
        </div>
        <div class="header-actions">
          <button class="toggle-theme" id="toggleTheme">
            <i class="fas fa-moon"></i>
          </button>
          <form action="/shop2/shop3/admin/admin_logout.php" method="post" style="display: inline;">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <button type="submit" class="logout-button">
              <i class="fas fa-sign-out-alt"></i> Logout
            </button>
          </form>
        </div>
      </header>
      <br>
      <main id="contentArea">
      <?php include('./import_cards.php'); ?>
      </main>
    </div>
  </div>
  
  <script>
  
     
    
    // Toggle sidebar
    document.getElementById('toggleSidebar').addEventListener('click', function() {
      const sidebar = document.getElementById('sidebar');
      const mainContent = document.getElementById('main-content');
      
      if (window.innerWidth <= 768) {
        // On mobile, show/hide the sidebar
        sidebar.classList.toggle('mobile-visible');
      } else {
        // On desktop, collapse/expand the sidebar
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
      }
    });
    
    // Toggle dark mode
    document.getElementById('toggleTheme').addEventListener('click', function() {
      document.body.classList.toggle('dark-mode');
      
      // Update icon
      const icon = this.querySelector('i');
      if (document.body.classList.contains('dark-mode')) {
        icon.classList.remove('fa-moon');
        icon.classList.add('fa-sun');
      } else {
        icon.classList.remove('fa-sun');
        icon.classList.add('fa-moon');
      }
      
      // Save preference to localStorage
      const isDarkMode = document.body.classList.contains('dark-mode');
      localStorage.setItem('darkMode', isDarkMode);
    });
    
    // Check for saved theme preference
    document.addEventListener('DOMContentLoaded', function() {
      const savedDarkMode = localStorage.getItem('darkMode') === 'true';
      if (savedDarkMode) {
        document.body.classList.add('dark-mode');
        document.querySelector('.toggle-theme i').classList.remove('fa-moon');
        document.querySelector('.toggle-theme i').classList.add('fa-sun');
      }
      
      // Handle responsive behavior on load
      if (window.innerWidth <= 768) {
        document.getElementById('sidebar').classList.remove('collapsed');
        document.getElementById('main-content').classList.remove('expanded');
      }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
      const sidebar = document.getElementById('sidebar');
      const mainContent = document.getElementById('main-content');
      
      if (window.innerWidth <= 768) {
        sidebar.classList.remove('collapsed');
        mainContent.classList.remove('expanded');
      }
    });
  </script>
</body>
</html>
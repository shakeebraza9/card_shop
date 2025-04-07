<?php
require '../config.php'; 

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
  header("Location: admin_login.php?redirect=panel.php");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_action'])) {

    if ($_POST['admin_action'] === 'add') {
        $text = trim($_POST['text']);
        $photo = '';
        $gif = '';
        $video = '';

        $tag = trim($_POST['tag']);
        if ($tag === '@all') {
            $tag = $_SESSION['username'];
        }
        $milestone = trim($_POST['milestone']);
        $event = trim($_POST['event']);
        $active = isset($_POST['active']) ? 1 : 0;

        if (!empty($_FILES['photo']['name'])) {
            $targetDir = "uploads/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $photo = $targetDir . basename($_FILES['photo']['name']);
            move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
        }

        if (!empty($_FILES['gif']['name'])) {
            $targetDir = "uploads/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $gif = $targetDir . basename($_FILES['gif']['name']);
            move_uploaded_file($_FILES['gif']['tmp_name'], $gif);
        }
        if (!empty($_FILES['video']['name'])) {
            $targetDir = "uploads/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $video = $targetDir . basename($_FILES['video']['name']);
            move_uploaded_file($_FILES['video']['tmp_name'], $video);
        }

        $stmt = $pdo->prepare("INSERT INTO Updates (Text, Photo, Gif, Video, Tag, Milestone, Event, Active, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$text, $photo, $gif, $video, $tag, $milestone, $event, $active]);
    }

    if ($_POST['admin_action'] === 'toggle' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("UPDATE Updates SET Active = NOT Active WHERE id = ?");
        $stmt->execute([$id]);
    }

    if ($_POST['admin_action'] === 'delete' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM Updates WHERE id = ?");
        $stmt->execute([$id]);
    }

    header("Location: " . $_SERVER['PHP_SELF']); 
    exit();
}

// Pagination logic
$items_per_page = 10; // Number of updates per page
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$total_updates = $pdo->query("SELECT COUNT(*) FROM Updates")->fetchColumn();
$total_pages = ceil($total_updates / $items_per_page);
$offset = ($current_page - 1) * $items_per_page;

$stmt = $pdo->prepare("SELECT * FROM Updates ORDER BY created_at DESC LIMIT :offset, :items_per_page");
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':items_per_page', $items_per_page, PDO::PARAM_INT);
$stmt->execute();
$updates = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Content Creator</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- Load TinyMCE from self-hosted location -->
  <script src="../js/tinymce/tinymce.min.js"></script>
  <script>
    tinymce.init({
      selector: 'textarea[name="text"]',
      plugins: 'emoticons lists link image code',
      toolbar: 'undo redo | bold italic underline | bullist numlist | link image | emoticons | code',
      menubar: false,
      branding: false,
      height: 200,
      content_style: "body { font-family: Roboto, sans-serif; font-size: 16px; }"
    });
  </script>

  <style>
    body {
      font-family: 'Roboto', sans-serif;
      background-color: #ffffff;
      margin: 0;
      padding: 20px;
      min-height: 100vh;
    }
    .container {
      max-width: 800px;
      width: 100%;
      margin: 0 auto;
    }
    .box-shadow {
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
      border-radius: 20px;
      padding: 30px;
      margin-bottom: 30px;
      background-color: #f8f9fa;
    }
    h1, h2 {
      color: #333;
      text-align: center;
      margin-bottom: 30px;
      font-weight: 500;
      letter-spacing: 1px;
    }
    .content-box {
      background: #ffffff;
      border-radius: 15px;
      padding: 20px;
      margin-bottom: 20px;
      border: 1px solid #e0e0e0;
    }
    textarea {
      width: 100%;
      height: 100px;
      border: 1px solid #e0e0e0;
      border-radius: 10px;
      resize: vertical;
      font-size: 16px;
      margin-bottom: 20px;
      padding: 15px;
      background: #ffffff;
      color: #333;
    }
    textarea::placeholder {
      color: #999;
    }
    .actions {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
    }
    .action-btn {
      flex: 1;
      min-width: 120px;
      padding: 12px;
      border: none;
      border-radius: 10px;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #ffffff;
      background: #007bff;
    }
    .action-btn:hover {
      background: #0056b3;
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    .action-btn i {
      margin-right: 8px;
    }
    .post-btn {
      display: block;
      width: 100%;
      padding: 15px;
      background: #28a745;
      color: white;
      border: none;
      border-radius: 10px;
      font-size: 18px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    .post-btn:hover {
      background: #218838;
      transform: translateY(-3px);
      box-shadow: 0 7px 20px rgba(0, 0, 0, 0.1);
    }
    .card {
      background: #ffffff;
      border-radius: 15px;
      padding: 20px;
      margin-bottom: 20px;
      color: #333;
      border: 1px solid #e0e0e0;
    }
    .card-content {
      margin-bottom: 15px;
    }
    .card-actions {
      display: flex;
      justify-content: space-between;
    }
    .card-btn {
      padding: 8px 15px;
      border: none;
      border-radius: 5px;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    .view-btn { background-color: #17a2b8; color: white; }
    .view-btn:hover { background-color: #138496; }
    .delete-btn { background-color: #dc3545; color: white; }
    .delete-btn:hover { background-color: #c82333; }
    .toggle-btn { background-color: #ffc107; color: #212529; }
    .toggle-btn:hover { background-color: #e0a800; }
    .pagination {
      display: flex;
      justify-content: center;
      margin-top: 30px;
    }
    .page-btn {
      padding: 8px 15px;
      margin: 0 5px;
      border: 1px solid #007bff;
      border-radius: 5px;
      background-color: #f8f9fa;
      color: #007bff;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
    }
    .page-btn:hover {
      background-color: #007bff;
      color: #ffffff;
    }
    .page-btn.active {
      background-color: #007bff;
      color: #ffffff;
      pointer-events: none;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="box-shadow">
      <h1>Admin Content Creator</h1>
      <div class="content-box">
        <form method="POST" enctype="multipart/form-data">
          <textarea name="text" placeholder="What's on your mind?"></textarea>
          <div class="actions">
            <button type="button" class="action-btn" onclick="document.getElementById('photo').click();">
              <i class="fas fa-image"></i> Photo
            </button>
            <button type="button" class="action-btn" onclick="document.getElementById('gif').click();">
              <i class="fas fa-gift"></i> GIF
            </button>
            <button type="button" class="action-btn" onclick="document.getElementById('video').click();">
              <i class="fas fa-video"></i> Video
            </button>
            <button type="button" class="action-btn" onclick="toggleMilestone();">
              <i class="fas fa-trophy"></i> Milestone
            </button>
          </div>
          <input type="file" name="photo" id="photo" accept="image/*" style="display:none;">
          <input type="file" name="gif" id="gif" accept="image/gif" style="display:none;">
          <input type="file" name="video" id="video" accept="video/*" style="display:none;">
          
          <div id="milestone-section" style="display:none; margin-top:15px;">
            <input type="text" name="milestone" id="milestone" placeholder="Milestone description">
            <input type="date" name="event" id="event">
          </div>
          <label><input type="checkbox" name="active" value="1"> Active</label>
          <button type="submit" name="admin_action" value="add" class="post-btn">Post Update</button>
        </form>
      </div>
    </div>

    <div class="box-shadow">
      <h2>Recent Updates</h2>
      <?php if ($updates): ?>
        <?php foreach ($updates as $update): ?>
          <div class="card">
            <div class="card-content">
              <p><?= htmlspecialchars($update['Text']) ?></p>
              <?php if (!empty($update['Photo'])): ?>
                <p><strong>Photo:</strong> <img src="<?= htmlspecialchars($update['Photo']) ?>" alt="Photo" style="max-width:100px;"></p>
              <?php endif; ?>
              <?php if (!empty($update['Gif'])): ?>
                <p><strong>GIF:</strong> <img src="<?= htmlspecialchars($update['Gif']) ?>" alt="GIF" style="max-width:100px;"></p>
              <?php endif; ?>
              <?php if (!empty($update['Video'])): ?>
                <p><strong>Video:</strong> <?= htmlspecialchars($update['Video']) ?></p>
              <?php endif; ?>
              <?php if (!empty($update['Tag'])): ?>
                <p><strong>Tag:</strong> <?= htmlspecialchars($update['Tag']) ?></p>
              <?php endif; ?>
              <?php if (!empty($update['Milestone'])): ?>
                <p><strong>Milestone:</strong> <i class="fas fa-trophy" style="color:#ffc107;"></i> <?= htmlspecialchars($update['Milestone']) ?> on <?= htmlspecialchars($update['Event']) ?></p>
              <?php endif; ?>
              <p><strong>Status:</strong> <?= $update['Active'] ? 'Active' : 'Inactive' ?></p>
            </div>
            <div class="card-actions">
              <form method="POST" style="display:inline;">
                <input type="hidden" name="id" value="<?= $update['id'] ?>">
                <button type="submit" name="admin_action" value="toggle" class="card-btn toggle-btn">
                  <?= $update['Active'] ? 'Deactivate' : 'Activate' ?>
                </button>
              </form>
              <form method="POST" style="display:inline;">
                <input type="hidden" name="id" value="<?= $update['id'] ?>">
                <button type="submit" name="admin_action" value="delete" class="card-btn delete-btn">Delete</button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="text-align:center;">No updates yet.</p>
      <?php endif; ?>

      <?php if ($total_pages > 1): ?>
      <div class="pagination">
        <?php if ($current_page > 1): ?>
          <a href="?page=<?= $current_page - 1 ?>" class="page-btn">Previous</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
          <a href="?page=<?= $i ?>" class="page-btn <?= $i === $current_page ? 'active' : '' ?>">
            <?= $i ?>
          </a>
        <?php endfor; ?>
        <?php if ($current_page < $total_pages): ?>
          <a href="?page=<?= $current_page + 1 ?>" class="page-btn">Next</a>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <script>
    function toggleMilestone() {
      var section = document.getElementById('milestone-section');
      section.style.display = (section.style.display === 'none' || section.style.display === '') ? 'block' : 'none';
    }
  </script>
</body>
</html>

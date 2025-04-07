<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Correct path to config.php
require '../../config.php';

// Ensure session is started
session_start();

// Define the logged-in user's username
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

// Define the base URL for uploaded files
$baseUrl = 'https://cardvault.club/shop2/shop3/';

try {
    // Fetch the latest active updates
    $stmt = $pdo->query("SELECT * FROM Updates WHERE Active = 1 ORDER BY created_at DESC LIMIT 10");
    $updates = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($updates) {
        foreach ($updates as $update) {
            // Replace "@all" in the text with the logged-in user's username
            $displayText = str_replace('@all', $username, $update['Text']);

            echo '<div class="update-item" style="border:1px solid #e0e0e0; padding:10px; margin-bottom:10px; border-radius:5px;">';
            echo '<p>' . htmlspecialchars($displayText) . '</p>';
            
            if (!empty($update['Photo'])) {
                echo '<p><strong>Photo:</strong><br><img src="' . $baseUrl . htmlspecialchars($update['Photo']) . '" alt="Photo" style="max-width:1000px;"></p>';
            }
            if (!empty($update['Gif'])) {
                echo '<p><strong>GIF:</strong><br><img src="' . $baseUrl . htmlspecialchars($update['Gif']) . '" alt="GIF" style="max-width:1000px;"></p>';
            }
            if (!empty($update['Video'])) {
                echo '<p><strong>Video:</strong><br><video src="' . $baseUrl . htmlspecialchars($update['Video']) . '" controls style="max-width:100%;"></video></p>';
            }
            echo '<small>Posted on: ' . htmlspecialchars($update['created_at']) . '</small>';
            echo '</div>';
        }
    } else {
        echo '<p style="text-align:center;">No updates available.</p>';
    }
} catch (PDOException $e) {
    echo "Error fetching updates: " . $e->getMessage();
}
?>

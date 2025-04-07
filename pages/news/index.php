<?php
include_once('../../header.php');
?>
<!-- Load jQuery BEFORE any inline scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="main-content">

    <div id="news" class="uuper">
        <h2>News Section</h2>
        <?php if (!empty($newsItems)): ?>
            <?php foreach ($newsItems as $news): ?>
                <div class="news-item">
                    <h3><?php echo htmlspecialchars($news['title']); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($news['content'])); ?></p>
                    <small>Published on: <?php echo htmlspecialchars($news['created_at']); ?></small>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No news available at the moment.</p>
        <?php endif; ?>
    </div>

    <!-- Admin Updates Popup Modal -->
    <div id="adminUpdatesPopup" style="visibility: hidden; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; display: flex; justify-content: center; align-items: center;">
      <div class="admin-popup-inner" style="background: #fff; padding: 20px 40px; max-width: 90%; max-height: 90%; overflow: auto; position: relative; border-radius: 10px; text-align: center;">
        <span style="position: absolute; top: 10px; right: 10px; font-size: 24px; cursor: pointer;" onclick="closeAdminUpdatesPopup()">×</span>
        <h2 style="margin-bottom: 20px;">Admin Updates</h2>
        <div id="popupUpdatesContent">
          <p>Loading updates...</p>
        </div>
      </div>
    </div>

    <!-- Popup Custom Styles -->
    <style>
      /* Style the popup content paragraphs */
      .admin-popup-inner p {
          font-size: 16px;
          line-height: 1.5;
          margin: 15px 0;
          text-align: center;
      }
      /* Style bold text inside the popup */
      .admin-popup-inner b,
      .admin-popup-inner strong {
          font-weight: bold;
          color: #007bff;
      }
      /* Additional styling for headings if present */
      .admin-popup-inner h1,
      .admin-popup-inner h2,
      .admin-popup-inner h3 {
          margin: 20px 0 10px;
          color: #333;
      }
    </style>

    <script>
      // Helper function to decode HTML entities
      function decodeHTMLEntities(str) {
          return $("<textarea/>").html(str).val();
      }

      function openAdminUpdatesPopup() {
        $.ajax({
          url: 'fetch_updates.php',
          method: 'GET',
          success: function(data) {
            // Decode any escaped HTML entities
            var decoded = decodeHTMLEntities(data);
            // If there are no updates, don't show the popup.
            if ($.trim(decoded) === "" || decoded.indexOf("No updates available") !== -1) {
              $('#adminUpdatesPopup').css('visibility', 'hidden');
            } else {
              $('#popupUpdatesContent').html(decoded);
              $('#adminUpdatesPopup').css('visibility', 'visible').fadeIn();
            }
          },
          error: function(xhr, status, error) {
            console.error('Failed to fetch updates:', error);
            $('#popupUpdatesContent').html('<p>Error loading updates. Please try again later.</p>');
            $('#adminUpdatesPopup').css('visibility', 'visible').fadeIn();
          }
        });
      }

      function closeAdminUpdatesPopup() {
        $('#adminUpdatesPopup').fadeOut();
      }

      function loadUpdates() {
          $.ajax({
              url: 'fetch_updates.php',
              method: 'GET',
              success: function(data) {
                  $('#updates-list').html(data);
              },
              error: function(xhr, status, error) {
                  console.error('Failed to fetch updates:', error);
                  $('#updates-list').html('<p>Error loading updates. Please try again later.</p>');
              }
          });
      }

      function checkAccountStatus() {
          $.ajax({
              url: 'check_status.php',
              method: 'POST',
              dataType: 'json',
              success: function(response) {
                  if (response.status === 'inactive') {
                      $('.message').html(response.message);
                      $('#rules-popup').fadeIn();
                  }
              },
              error: function(xhr, status, error) {
                  console.error('Failed to check account status:', error);
              }
          });
      }

      function closeRulesPopup() {
          $('#rules-popup').fadeOut();
      }

      $(document).ready(function() {
          openAdminUpdatesPopup();
          loadUpdates();
          setInterval(loadUpdates, 10000); // Refresh every 10 seconds
          checkAccountStatus();
      });
    </script>

</div>

<div id="rules-popup" class="popup-modal" style="display: none;">
    <div class="popup-content" style="position: absolute; top: 50%; right: 20%; padding: 20px; background: #fff; border-radius: 10px; text-align: center;">
        <span class="close" onclick="closeRulesPopup()">
            <i class="fas fa-times"></i>
        </span>
        <p class="message"></p>
    </div>
</div>
    </div>
<?php
include_once('../../footer.php');
?>

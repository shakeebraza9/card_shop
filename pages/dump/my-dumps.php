<?php
// Start output buffering and error reporting
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

session_start();
include_once('../../config.php');   // defines $pdo, $encryptionKey, $urlval

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: {$urlval}login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch sold dumps with decryption
try {
    $quotedKey = $pdo->quote($encryptionKey);
    $sql = "
        SELECT
          id,
          CONVERT(AES_DECRYPT(track1, $quotedKey) USING utf8) AS track1,
          CONVERT(AES_DECRYPT(track2, $quotedKey) USING utf8) AS track2,
          monthexp,
          yearexp,
          pin,
          card_type,
          price,
          country,
          purchased_at
        FROM dumps
        WHERE buyer_id = ? AND status = 'sold'
        ORDER BY purchased_at DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $soldDumps = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Error fetching sold dumps: " . $e->getMessage());
    $soldDumps = [];
}

// Now include the header and proceed with your HTML/CSS
include_once('../../header.php');
ob_end_flush();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>My Dumps & Activity Log</title>
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
        /* --- Basic Styles --- */
        .check-dump-button {
            padding: 6px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            margin: 0 5px 0 0;
        }
        .check-dump-button:disabled {
            background-color: #d6d6d6;
            color: #999;
            cursor: not-allowed;
        }

        .dataTables_filter {
            display: none !important;
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
            font-weight: bold; 
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 10px 15px; 
        }
        tbody tr:nth-child(even) { 
            background-color: #fff; 
        }
        tbody tr:hover { 
            background-color: #f1f1f1; 
        }
        button { 
            padding: 5px 10px; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
        }
        .copy-button { 
            background-color: #0c182f; 
            color: white; 
            margin-right: 5px; 
            margin-top: 0px !important;
            margin-bottom: 0px !important;
        }
        .copy-button:hover { 
            background-color: #218838; 
        }
        .activity-log-table th { 
            background-color: #0c182f !important; 
        }
        @media (max-width: 768px) {
            table { font-size: 14px; }
            td, th { padding: 8px 15px; white-space: nowrap; }
            .main-tbl321 { width: 100% !important; overflow-x: scroll !important; }
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
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            z-index: 1;
            border-radius: 8px;
            animation: swing 2s ease-in-out infinite;
        }
        @keyframes swing {
            0% { transform: rotate(-45deg); }
            25% { transform: rotate(-43deg); }
            50% { transform: rotate(-47deg); }
            75% { transform: rotate(-43deg); }
            100% { transform: rotate(-45deg); }
        }
        @keyframes shake-up-down {
            0% { transform: translateY(0); }
            25% { transform: translateY(-5px); }
            50% { transform: translateY(5px); }
            75% { transform: translateY(-5px); }
            100% { transform: translateY(0); }
        }
        .shake { animation: shake-up-down 0.5s ease-in-out; }
        #rules-btn:hover { animation: shake-up-down 0.5s ease-in-out; }
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
    <div id="my-dumps" class="uuper">
        <div style="display: flex; justify-content: space-between;">
            <h2>My Dumps Section</h2>
            <div style="position: relative; width: 300px; font-family: Arial, sans-serif;">
                <button id="dropdownBtn" style="width: 100%; padding: 12px; background-color: #0c182f; border: 1px solid #1f3660; border-radius: 5px; color: white; text-align: left; font-size: 14px; cursor: pointer;">
                    Select Columns
                </button>
                <div id="dropdownMenu" style="display: none; position: absolute; top: 100%; left: 0; width: 100%; background-color: white; border: 1px solid #ccc; border-radius: 5px; padding: 10px; box-sizing: border-box; z-index: 999; max-height: 300px; overflow-y: auto;">
                    <label><input type="checkbox" value="1" checked> Track 1</label><br>
                    <label><input type="checkbox" value="2" checked> Track 2</label><br>
                    <label><input type="checkbox" value="3" checked> PIN</label><br>
                    <label><input type="checkbox" value="4" checked> Country</label><br>
                </div>
                <div id="customSearchContainer" style="margin-bottom: 10px;">
                    <input type="text" id="customSearch" placeholder="Search" style="width: 300px; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                </div>
            </div>
        </div>

        <?php if (empty($soldDumps)): ?>
            <p>No purchased dumps available.</p>
        <?php else: ?>
            <div class="main-tbl321" style="overflow-x: auto; max-width: 100%; border: 1px solid #ddd; margin-top: 20px;">
                <table id="soldDumpsTable" style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; margin-top: 20px;">
                    <thead style="background: #f4f4f4; border-bottom: 2px solid #ddd;">
                    <div id="top-scrollbar" style="overflow-x: auto; overflow-y: hidden; width: 100%; border: 1px solid #ddd; margin-bottom: 5px;">
                        <div id="scroll-content" style="height: 1px;"></div>
                        </div>
                        <tr>
                            <th style="padding: 10px; border: 1px solid #ddd; width: 10%;">ID</th>
                            <th style="padding: 10px; border: 1px solid #ddd;">Track 1</th>
                            <th style="padding: 10px; border: 1px solid #ddd;">Track 2</th>
                            <th style="padding: 10px; border: 1px solid #ddd;">PIN</th>
                            <th style="padding: 10px; border: 1px solid #ddd;">Country</th>
                            <th style="padding: 10px; border: 1px solid #ddd;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($soldDumps as $dump): 
                            if (isset($dump['deleted']) && $dump['deleted'] == 1) continue;
                            // Determine if the "Check" button should be disabled based on purchase time/refundable period.
                            $disableTime = 300; // default: 5 minutes (300 seconds)
                            $disableCheck = false;
                            if (isset($dump['purchased_at'])) {
                                $purchaseTime = strtotime($dump['purchased_at']);
                                if ($purchaseTime) {
                                    if (isset($dump['Refundable'])) {
                                        if ($dump['Refundable'] == 5) { $disableTime = 300; }
                                        elseif ($dump['Refundable'] == 10) { $disableTime = 600; }
                                        elseif ($dump['Refundable'] == 20) { $disableTime = 1200; }
                                    }
                                    if ((time() - $purchaseTime) > $disableTime) { 
                                        $disableCheck = true; 
                                    }
                                }
                            }
                        ?>
                        <tr id="dump-<?php echo htmlspecialchars($dump['id']); ?>">
                            <td style="padding: 10px; position: relative;">
                                <?php echo htmlspecialchars($dump['id']); ?>
                                <?php if (isset($dump['is_view']) && $dump['is_view'] == 0): ?>
                                    <span class="ribbon">New</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 10px;"><?php echo htmlspecialchars($dump['track1'] ?? ''); ?></td>
                            <td style="padding: 10px;"><?php echo htmlspecialchars($dump['track2'] ?? ''); ?></td>
                            <td style="padding: 10px;"><?php echo htmlspecialchars($dump['pin'] ?: 'No'); ?></td>
                            <td style="padding: 10px;"><?php echo htmlspecialchars($dump['country'] ?? ''); ?></td>
                            <td style="padding: 10px; display: flex; justify-content: center; align-items: center;">
                                <button class="copy-button" style="padding: 6px 10px; border: none; border-radius: 3px; cursor: pointer; margin-right: 5px;" onclick="copyDumpInfo(<?php echo json_encode($dump['id']); ?>)">Copy</button>
                                <button id="check-dump-button-<?php echo htmlspecialchars($dump['id']); ?>" style="padding: 6px 10px; border: none; border-radius: 3px; cursor: pointer; margin: 0 5px 0 0;" class="check-dump-button" onclick='checkDump(
                                        <?php echo json_encode($dump['id']); ?>,
                                        <?php echo json_encode($dump["track1"] ?? ""); ?>,
                                        <?php echo json_encode($dump["track2"] ?? ""); ?>,
                                        <?php echo json_encode($dump["pin"] ?? ""); ?>,
                                        <?php echo json_encode($dump["monthexp"] ?? ""); ?>,
                                        <?php echo json_encode($dump["yearexp"] ?? ""); ?>
                                    )'
                                    <?php if($disableCheck) echo 'disabled title="Check disabled after ' . ($disableTime/60) . ' minutes"'; ?>
                                >Check</button>
                                <a type="button" onclick="deleteRow(<?php echo json_encode($dump['id']); ?>)" id="clear-btn-<?php echo $dump['id']; ?>" 
                                   class="btn text-center btn-with-icon" style="background-color: #f44336; color: white; padding: 5px 15px; width:70px; border-radius: 4px; border: none; cursor: pointer; margin-top: -1px;">
                                    <i class="fa fa-times"></i> <span class="btn-text">Delete</span>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div id="bottom-scrollbar" style="overflow-x: auto; overflow-y: hidden; width: 100%; border: 1px solid #ddd; margin-top: 5px;">
    <div id="scroll-content-bottom" style="height: 1px;"></div>
</div>

            </div>

        <?php endif; ?>
        

        <!-- Activity Log Section -->
        <div id="dumps-activity-log">
            <div style="display: flex; align-items: center; gap: 20px;">
                <h2>Dumps Activity Log</h2>
                <button id="rules-btnnew" style="padding: 5px 15px; background-color: #f39c12; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; display: flex; align-items: center; gap: 5px;" onclick="openRulesPopup()">
                    <i class="fas fa-gavel"></i> Rules
                </button>
                <button id="delete-all-logs" style="padding: 5px 15px; background-color: #d9534f; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">
                    Delete Dumps Activity Logs
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
                            <tr><td colspan="4" style="text-align:center;">No activity logged yet</td></tr>
                        <?php else: ?>
                            <?php foreach ($activityLogs as $log): ?>
                                <tr class="activity-log-row <?php echo strtolower($log['status']); ?>">
                                    <td><?php echo htmlspecialchars($log['dump_id']); ?></td>
                                    <td><?php echo htmlspecialchars(explode('=', $log['track1'])[0]); ?></td>
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

<!-- Rules Popup -->
<div id="rules-popup2" class="popup-modal" style="display: none;">
<div class="popup-content">
  <span class="close" onclick="closeRulesPopup()"><i class="fas fa-times"></i></span>
  <h2>Purchased Information</h2>
<p>Here are the updated rules for using the system:</p>

<p>• For a smooth checking experience, please check one dump at a time. After receiving a response from the checker—either 
<span class="live">LIVE</span> or <span class="dead">DEAD</span>—you can proceed to check the remaining dumps.</p>

<p>• Checking a dump may, in most cases, '<span class="kill">KILL</span>' the dump. If the dump's status results in a 
<span class="live">LIVE</span> response, you will not be eligible for a refund. Therefore, please do not request a refund for a dump with a 
<span class="live">LIVE</span> status.</p>

<p>• The fee for checking a dump is $0.50. If the dump's result is <span class="dead">DEAD</span>, you will receive a full refund for the dump amount, along with the credit for checking the dump.</p>

<p>• For <span class="live">LIVE</span> dumps, you will not receive a refund for the checking fee.</p>

<p>• We use the most advanced third-party checking systems. If a dump's status is returned as <span class="live">LIVE</span>, we will not issue a refund. This checking service is not part of our default system, so we rely on and trust the third-party providers we use to verify the dumps.</p>

<p><b>• Do not attempt to abuse the dump checker in an effort to find bugs or exploit the system. We have mechanisms in place that notify us when a user tries to use the checker in any way other than intended. In such cases, we reserve the right to ban the user without prior notice, and any deposited funds will be forfeited.</b></p>

<p>• We reserve the right to disable the checker at any time without prior notice in the event of malfunctions or misuse.</p>

<p>• You have the option to delete your entire dump activity log by clicking the 'Delete Dumps Activity Logs' button, if you prefer not to keep a record of previously checked dumps. You also have the option to delete purchased and used dumps individually by clicking the 'Delete' button next to each dump.</p>

</div>
                            </div>
                            </div>
<?php include_once('../../footer.php'); ?>

<script>

var dumpCheckStatus = {};

function showLoadingOverlay() {
  // Create an overlay div if it doesn't exist
  if (!document.getElementById('loadingOverlay')) {
    var overlay = document.createElement('div');
    overlay.id = 'loadingOverlay';
    overlay.style.position = 'fixed';
    overlay.style.top = '0';
    overlay.style.left = '0';
    overlay.style.width = '100%';
    overlay.style.height = '100%';
    overlay.style.background = 'rgba(0,0,0,0.5)';
    overlay.style.zIndex = '10000';
    document.body.appendChild(overlay);
  }
}

function hideLoadingOverlay() {
  var overlay = document.getElementById('loadingOverlay');
  if (overlay) {
    overlay.parentNode.removeChild(overlay);
  }
}




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
  var $tbody = $('#activityLogTable tbody');
  $tbody.empty();
  if (!logs.length) {
    $tbody.append('<tr><td colspan="4" style="text-align:center;">No activity logged yet</td></tr>');
  } else {
    logs.forEach(function(log) {
      var rowClass = log.status.toLowerCase();
      var row = '<tr class="activity-log-row ' + rowClass + '">' +
                '<td>' + log.dump_id + '</td>' +
               '<td>' + (log.track1 ? log.track1.split('=')[0] : '') + '</td>' +
                '<td>' + log.date_checked + '</td>' +
                '<td>' + log.status + '</td>' +
                '</tr>';
      $tbody.append(row);
    });
  }
}

// ------------------- Delete All Logs Functionality -------------------
document.addEventListener('DOMContentLoaded', function() {
  var deleteAllBtn = document.getElementById('delete-all-logs');
  if (deleteAllBtn) {
      deleteAllBtn.addEventListener('click', function() {
          alertify.confirm(
              "Delete All Dump Logs",
              "Are you sure you want to delete all dump logs?",
              function() { // User clicked Yes
                  $.ajax({
                      url: 'deleteAllLogs.php',
                      type: 'POST',
                      dataType: 'json',
                      success: function(response) {
                          if (response.success) {
                              $('#activityLogTable tbody').html('<tr><td colspan="4" style="text-align:center;">No activity logged yet</td></tr>');
                              alertify.success("Activity logs cleared.");
                          } else {
                              alertify.error(response.error || "Error clearing logs.");
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
      });
  } else {
      console.error('Button with id "delete-all-logs" not found.');
  }
});

// ------------------- Other Functions -------------------
function removeDumpFromUI(dumpId) {
  var table = $('#soldDumpsTable').DataTable();
  table.row('#dump-' + dumpId).remove().draw(false);
}

$(document).ready(function() {
  var table = $('#soldDumpsTable').DataTable({
      paging: true,
      searching: true,
      ordering: false,
      info: true,
      lengthChange: true,
      autoWidth: false,
      responsive: true
  });
  
  $('#customSearch').on('keyup', function() {
      table.search(this.value).draw();
  });
  
  $('#dropdownMenu input[type="checkbox"]').on('change', function() {
      var columnIndex = $(this).val();
      var column = table.column(columnIndex);
      column.visible($(this).prop('checked'));
  });
  
  $("#dropdownBtn").on('click', function() {
      $("#dropdownMenu").toggle();
  });
  
  $(document).on('click', function(event) {
      if (!$(event.target).closest('#dropdownBtn, #dropdownMenu').length) {
          $("#dropdownMenu").hide();
      }
  });
});

document.addEventListener('DOMContentLoaded', function() {
  fetch('update_dump_isview.php', { method: 'POST' })
    .then(response => response.json())
    .then(data => { /* Optionally handle response data */ })
    .catch(error => { console.error('Error:', error); });
});

const rulesBtn = document.getElementById('rules-btnnew');
if (rulesBtn) {
  setInterval(() => {
      rulesBtn.classList.add('shake');
      setTimeout(() => { rulesBtn.classList.remove('shake'); }, 500);
  }, 2000);
} else {
  console.error('Button with id "rules-btnnew" not found.');
}

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

function checkDump(dumpId, track1, track2, pin, expm, expy) {
  var button = $("#check-dump-button-" + dumpId);
  if (dumpCheckStatus[dumpId] || button.prop("disabled")) return;
  
  // Do not change the button text immediately; wait for confirmation.
  
  alertify.confirm(
    "Confirm Check",
    "Are you sure you want to check this dump? This may kill the dump. If it returns LIVE, no refund is given. Proceed?",
    function() { // OK callback
      
      // (Optional) If you have a flag for card checking enabled, check it here:
      if (typeof cardCheckingEnabled !== "undefined" && !cardCheckingEnabled) {
        alertify.error("Card checking is currently disabled.");
        return;
      }
      
      // Now change the button text to "Checking..." and disable it.
      button.text("Checking...").prop("disabled", true);
      dumpCheckStatus[dumpId] = true;
      
      showLoadingOverlay();
      var buyerId = <?php echo json_encode($_SESSION['user_id']); ?>;
      if (!track1 || track1.toUpperCase() === "N/A") {
        track1 = track2;
      }
      var data = {
        track1: track1,
        track2: track2,
        pin: pin,
        expm: expm,
        expy: expy,
        buyer_id: buyerId,
        type: 'JSON'
      };
      var timeoutHandle = setTimeout(function() {
        hideLoadingOverlay();
        alertify.error("Check timed out. Please try again.");
        dumpCheckStatus[dumpId] = false;
        button.text("Check").prop("disabled", false);
      }, 30000);
      
      $.ajax({
        url: '<?= $urlval?>/ajax/dump-checker-api.php',
        type: 'POST',
        data: data,
        beforeSend: function() {
          // Optionally, you can also display a loading message.
        },
        success: function(response) {
          clearTimeout(timeoutHandle);
          hideLoadingOverlay();
          if (response.status === 'LIVE') {
            alertify.success("Dump is LIVE!");
            $("#dump-status-" + dumpId)
              .text("LIVE")
              .css("background-color", "#218838");
            button.text("LIVE");
          } else if (response.status === 'DEAD') {
            alertify.error("Dump is DEAD! (Balance refunded)");
            $("#dump-status-" + dumpId)
              .text("DEAD")
              .css("background-color", "#d9534f");
            button.text("DEAD");
          } else {
            alertify.error("Error: " + response.error);
            button.text("Check").prop("disabled", false);
          }
          // Refresh the page after 2 seconds to reflect changes
          setTimeout(function() {
            location.reload();
          }, 2000);
        },
        error: function(xhr, status, error) {
          clearTimeout(timeoutHandle);
          hideLoadingOverlay();
          alertify.error("Error checking dump: " + error);
          button.text("Check").prop("disabled", false);
        }
      });
    },
    function() { // Cancel callback
      alertify.warning("Dump check canceled.");
      dumpCheckStatus[dumpId] = false;
      button.text("Check").prop("disabled", false);
    }
  );
}


function removeCardFromUI(dumpId) {
      var table = $('#soldDumpsTable').DataTable();
      table.row('#dump-status-' + dumpId).remove().draw(false);
    }

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

$(document).ready(function() {
    var $topScrollbar = $('#top-scrollbar');
    var $bottomScrollbar = $('#bottom-scrollbar');
    var $tableContainer = $('.main-tbl321');

    function syncScrollWidths() {
        var tableScrollWidth = $tableContainer.get(0).scrollWidth;
        $('#scroll-content').width(tableScrollWidth);
        $('#scroll-content-bottom').width(tableScrollWidth);
    }

    syncScrollWidths();
    $(window).resize(syncScrollWidths);

    // Sync scrolling
    $topScrollbar.on('scroll', function() {
        $tableContainer.scrollLeft($(this).scrollLeft());
        $bottomScrollbar.scrollLeft($(this).scrollLeft());
    });

    $bottomScrollbar.on('scroll', function() {
        $tableContainer.scrollLeft($(this).scrollLeft());
        $topScrollbar.scrollLeft($(this).scrollLeft());
    });

    $tableContainer.on('scroll', function() {
        $topScrollbar.scrollLeft($(this).scrollLeft());
        $bottomScrollbar.scrollLeft($(this).scrollLeft());
    });
});


//DELETE
function deleteRow(dumpId) {
    if (!confirm("Are you sure you want to delete this dump?")) return;

    $.ajax({
        url: 'delete_dump.php',
        type: 'POST',
        data: { ticket_id: dumpId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alertify.success("Dump deleted successfully");
                // Use the correct row id selector "dump-" + dumpId
                $("#dump-" + dumpId).fadeOut(500, function() { 
                    $(this).remove(); 
                });
            } else {
                alertify.error(response.message || "Failed to delete the dump.");
            }
        },
        error: function(xhr, status, error) {
            alertify.error("Error deleting dump: " + error);
        }
    });
}



//Table settings

$(document).ready(function() {
  $('#activityLogTable').DataTable({
    paging: true,        // Enable pagination
    ordering: false,     // Disable ordering (if you don't need sorting)
    info: true,          // Show table information (e.g., "Showing 1 to 10 of 50 entries")
    lengthChange: true, // Hide the option to change page length
    autoWidth: false,
    responsive: true
  });
});



</script>
</body>
</html>

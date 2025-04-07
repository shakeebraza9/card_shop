<?php
include_once('../../header.php');
include_once('encrypt.php'); 


if (isset($_GET['ticket_id']) && isset($_GET['mark_as_read']) && $_GET['mark_as_read'] == 1) {
    $ticketId = $_GET['ticket_id'];
    $stmtUpdate = $pdo->prepare("UPDATE support_replies SET is_read = 1 WHERE ticket_id = ? AND sender = 'admin'");
    $stmtUpdate->execute([$ticketId]);
    echo json_encode(['success' => true]);
    exit;
}


// Check if any ticket is open (to disable the new ticket form if needed)
$hasOpenTicket = false;
foreach ($tickets as $ticket) {
    if ($ticket['status'] === 'open') {
        $hasOpenTicket = true;
        break;
    }
}
?>
<!-- Inline CSS for red pulse notification dot -->
<style>
.notification-dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    background-color: red;
    border-radius: 50%;
    animation: pulse 1s infinite;
    margin-left: 5px;
}


.conversation-details {
    max-height: 300px;
    overflow-y: auto; 
    padding: 10px; 
    background: #f9f9f9; 
    border: 1px solid #ccc; 
    border-radius: 5px; 
}


@keyframes pulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.3); opacity: 0.7; }
    100% { transform: scale(1); opacity: 1; }
}
</style>

<!-- Main Content Area -->
<div class="main-content">
    <div id="support" class="uuper">
        <h2>Support</h2>

        <!-- Ticket Form -->
        <div class="ticket-form" style="border: 1px solid #ccc; padding: 10px; border-radius: 5px;">
            <h3>Open a New Ticket</h3>
            <form id="ticket-form">
                <textarea name="message" id="ticket-message" placeholder="Describe your issue..." rows="4"
                    maxlength="500" required 
                    <?php echo $hasOpenTicket ? 'disabled' : ''; ?>
                    style="width: 100%; padding: 8px; border-radius: 4px;"></textarea>
                <small id="ticket-char-count">0/500</small>
                <button type="submit" id="submit-ticket-btn"
                    style="background-color: #0c182f; color: white; padding: 10px; border: none; border-radius: 4px; cursor: pointer;"
                    <?php echo $hasOpenTicket ? 'disabled' : ''; ?>>
                    Submit Ticket
                </button>
            </form>
            <?php if ($hasOpenTicket): ?>
            <p class="disabled-message" style="color: red;">Please have an admin close this ticket before opening a new one.</p>
            <?php endif; ?>
        </div>

        <!-- Ticket List -->
        <?php if (!empty($tickets)): ?>
        <div class="ticket-list">
            <?php foreach ($tickets as $ticket): ?>
           
            <div class="ticket-item" style="border-bottom: 1px solid #ddd; padding: 10px;">
                <div class="ticket-summary" style="cursor: pointer;" onclick="handleTicketClick(event, <?php echo htmlspecialchars($ticket['id']); ?>)">
                    <span>Ticket #<?php echo htmlspecialchars($ticket['id']); ?> - <?php echo htmlspecialchars($ticket['created_at']); ?></span>
                    Status:
                    <small style="color: <?= $status_style ?>;">
                        <?php echo ucfirst(htmlspecialchars($ticket['status'])); ?>
                    </small>
                    <?php $stmtUnread = $pdo->prepare("SELECT COUNT(*) FROM support_replies WHERE ticket_id = ? AND is_read = 0 AND sender = 'admin'");
$stmtUnread->execute([$ticket['id']]);
if ($stmtUnread->fetchColumn() > 0): ?>
    <span class="unread-indicator" style="color: red; font-weight: bold; margin-left: 5px;">
        (Unread Messages)
    </span>
<?php endif; ?>
                </div>

                <!-- Conversation Details -->
                <div id="conversation-<?php echo htmlspecialchars($ticket['id']); ?>"
                     class="conversation-details"
                     style="display: none; max-height: 300px; overflow-y: auto; padding: 10px; background: #f9f9f9; border: 1px solid #ccc; border-radius: 5px;">
                    
                    <?php
                    // Retrieve replies for this ticket
                    $stmt = $pdo->prepare("SELECT * FROM support_replies WHERE ticket_id = ? ORDER BY created_at ASC");
                    $stmt->execute([$ticket['id']]);
                    $replies = $stmt->fetchAll();

                    $userReplyCount = 0; // Count consecutive user replies

                    foreach ($replies as $reply):
                        // Decrypt the message before display
                        $decryptedMessage = decryptMessage($reply['message']); 
                        $messageClass = ($reply['sender'] === 'user') ? 'user-message' : 'admin-message';
                        $senderName = ($reply['sender'] === 'user') ? htmlspecialchars($username) : 'Admin';
                        // Add a checkmark if the reply is marked as read
                        $readTick = ($reply['is_read'] == 1)
                            ? "<span class='read-tick' style='color: green; font-size: 14px; margin-left: 5px;'>&#10003;</span>"
                            : "";
                        if ($reply['sender'] === 'user') {
                            $userReplyCount++;
                        } else {
                            $userReplyCount = 0; // Reset on admin reply
                        }
                    ?>
                    <div class="<?php echo $messageClass; ?>" data-reply-id="<?php echo htmlspecialchars($reply['id']); ?>"
                         style="padding: 5px; border-radius: 5px; margin-bottom: 5px;">
                        <p class="message-tag"><strong><?php echo htmlspecialchars($senderName); ?>:</strong></p>
                        <p><?php echo htmlspecialchars($decryptedMessage); ?></p>
                        <small><?php echo htmlspecialchars($reply['created_at']); ?> <?php echo $readTick; ?></small>
                    </div>
                    <?php endforeach; ?>

                    <!-- Reply Form (only if allowed) -->
                    <?php if ($ticket['status'] === 'open' && $userReplyCount < 3): ?>
                    <form method="POST" action="submit_reply.php" class="reply-section"
                          onsubmit="submitReplyy(event, <?php echo htmlspecialchars($ticket['id']); ?>)">
                        <input type="hidden" name="ticket_id" value="<?php echo htmlspecialchars($ticket['id']); ?>">
                        <textarea name="message" id="reply-message-<?php echo htmlspecialchars($ticket['id']); ?>"
                                  placeholder="Reply..." rows="2" maxlength="500" required
                                  style="width: 100%; padding: 8px; border-radius: 4px;"></textarea>
                        <small id="reply-char-count-<?php echo htmlspecialchars($ticket['id']); ?>">0/500</small>
                        <button type="submit" id="reply-btn-<?php echo htmlspecialchars($ticket['id']); ?>"
                                style="background-color: #0c182f; color:white; padding: 10px; border: none; border-radius: 4px; cursor: pointer;">
                            Send
                        </button>
                    </form>
                    <?php elseif ($userReplyCount >= 3 && $ticket['status'] === 'open'): ?>
                    <p class="disabled-message" style="color: red;">You cannot send more than 3 consecutive messages without an admin reply.</p>
                    <?php elseif ($ticket['status'] === 'closed'): ?>
                    <p class="disabled-message" style="color: gray;">The conversation has been closed by the Admin. You may proceed by opening a new ticket if further assistance is required.</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="ticket-list">
            <p>No open tickets at the moment.</p>
        </div>
        <?php endif; ?>
    </div>
</div>
        </div>
<?php
include_once('../../footer.php');
?>

<!-- JavaScript Section -->
<script>
// When the document is ready...
document.addEventListener("DOMContentLoaded", function() {
    // --- New Ticket Submission ---
    const ticketForm = document.getElementById("ticket-form");
    const submitBtn = document.getElementById("submit-ticket-btn");
    const messageInput = document.getElementById("ticket-message");

    ticketForm.addEventListener("submit", function(e) {
        e.preventDefault();
        const message = messageInput.value.trim();
        if (message.length === 0) return;

        submitBtn.disabled = true;
        fetch("submit_ticket.php", {
            method: "POST",
            body: new FormData(ticketForm),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload the page to show the new ticket
                window.location.reload();
                messageInput.value = "";
            } else {
                showPopupMessage(data.message || "Error submitting ticket.", "error");
            }
        })
        .catch(error => {
            showPopupMessage("An error occurred. Please try again.", "error");
        })
        .finally(() => {
            submitBtn.disabled = false;
        });
    });
});

// --- Reply Submission ---
function submitReplyy(event, ticketId) {
    event.preventDefault();
    const form = event.target;
    const textArea = form.querySelector("textarea");
    const replyBtn = form.querySelector("button[type='submit']");
    const formData = new FormData(form);

    replyBtn.disabled = true;
    replyBtn.innerText = "Sending...";

    fetch(form.action, {
        method: "POST",
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Get the conversation container
            const conversation = document.getElementById(`conversation-${ticketId}`);
            // Create a new message element for the submitted reply
            const newMessage = document.createElement("div");
            newMessage.className = "user-message";
            newMessage.style.padding = "5px";
            newMessage.style.borderRadius = "5px";
            newMessage.style.marginBottom = "5px";
            newMessage.innerHTML = `
                <p class="message-tag"><strong>${data.username}:</strong></p>
                <p>${data.message}</p>
                <small>${data.created_at}</small>
            `;
            // Insert the new message before the reply form
            conversation.insertBefore(newMessage, form);
            // Automatically scroll to the bottom of the conversation
            conversation.scrollTop = conversation.scrollHeight;
            textArea.value = '';

            // Remove the red pulsing notification dot if present
            const ticketSummaries = document.querySelectorAll(".ticket-summary");
            ticketSummaries.forEach(function(summary) {
                // Check if this summary corresponds to the current ticketId
                if (summary.getAttribute("onclick").indexOf(ticketId) > -1) {
                    const redPulse = summary.querySelector(".notification-dot");
                    if (redPulse) {
                        redPulse.remove();
                    }
                }
            });
        } else {
            showPopupMessage(data.message || "Error submitting reply.", "error");
            setTimeout(function() {
                location.reload();
            }, 5000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showPopupMessage("A network error occurred. Please try again.", "error");
    })
    .finally(() => {
        replyBtn.disabled = false;
        replyBtn.innerText = "Send";
    });
}

// --- Toggle Conversation & Mark as Read ---
function handleTicketClick(event, ticketId) {
    event.preventDefault();

    // Send an AJAX request to mark admin messages as read for this ticket
    fetch(`?ticket_id=${ticketId}&mark_as_read=1`)
      .then(response => response.json())
      .then(data => {
          if(data.success) {
              // Remove the "(Unread Messages)" indicator from the UI
              const ticketSummary = document.querySelector(`[onclick*="handleTicketClick(event, ${ticketId})"]`);
              const unreadIndicator = ticketSummary ? ticketSummary.querySelector(".unread-indicator") : null;
              if (unreadIndicator) {
                  unreadIndicator.remove();
              }
          }
      })
      .catch(error => console.error("Error marking messages as read:", error));

    // Toggle the ticket conversation display
    toggleConversation(ticketId);
}


// Toggle the display of the conversation container and auto-scroll to the latest message if opened
function toggleConversation(ticketId) {
    const conversation = document.getElementById(`conversation-${ticketId}`);
    if (conversation) {
        if (conversation.style.display === "none" || conversation.style.display === "") {
            conversation.style.display = "block";

            // Ensure auto-scrolling to the last message
            setTimeout(() => {
                const lastMessage = conversation.querySelector('.user-message:last-child, .admin-message:last-child');
                if (lastMessage) {
                    lastMessage.scrollIntoView({ behavior: 'smooth', block: 'end' });
                }
            }, 50); // Delay to ensure rendering is complete
        } else {
            conversation.style.display = "none";
        }
    }
}



// --- Dummy Popup Message Function ---
// Replace this with your own implementation (e.g. using a modal or toast)
function showPopupMessage(message, type) {
    alert(message);
}
</script>

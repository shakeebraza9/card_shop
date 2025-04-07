<?php

include_once('../../header.php');
?>
<style>
/* Grid & Tool Styles */
.grid-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    padding: 10px;
}

.tool-item {
    background-color: #fff;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.tool-item:hover {
    transform: scale(1.05);
}

h3 {
    margin: 0;
    font-size: 1.2em;
}

p {
    margin: 10px 0;
}

a.buy-button {
    display: inline-block;
    background-color: #0c182f;
    color: #fff;
    text-decoration: none;
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 14px;
    font-weight: bold;
    transition: background-color 0.3s ease;
    height: 38px !important;
    width: 100px !important;
    text-align: center !important;
}

a.buy-button:hover {
    background-color: #218838;
    cursor: pointer;
}

/* Pagination Styles */
#pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

#pagination button {
    background-color: #0c182f;
    color: #fff;
    border: none;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    margin: 0 5px;
}

#pagination button:hover {
    background-color: #3e8e41;
}

#pagination button:disabled {
    background-color: #ddd;
    color: #666;
    cursor: not-allowed;
}

#pagination span {
    font-size: 16px;
    margin: 0 10px;
}

/* Responsive Grid */
@media (max-width: 600px) {
    .grid-container {
        grid-template-columns: 1fr;
    }
}

@media (min-width: 601px) and (max-width: 1024px) {
    .grid-container {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1025px) {
    .grid-container {
        grid-template-columns: repeat(3, 1fr);
    }
}

/* Popup Modal */
.popup-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    display: none;
    justify-content: center;
    align-items: center;
}

.popup-content {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    position: relative;
}

.popup-content .close {
    position: absolute;
    top: 10px;
    right: 10px;
    cursor: pointer;
}
</style>

<div class="main-content">
    <div id="leads" class="uuper">
        <h2>pages Section</h2>
        <div style="text-align: center; margin-bottom: 10px;">
            <h3 style="margin: 0; font-size: 18px; color: #333;">Search for pages</h3>
        </div>
        <div
            style="position: relative; width: 100%; max-width: 400px; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
            <input type="text" id="searchBar" placeholder="Search pages..."
                style="width: 100%; padding: 12px 20px; border-radius: 25px; border: 1px solid #ccc; font-size: 16px; transition: all 0.3s ease-in-out;"
                onfocus="this.style.borderColor='#007bff';" onblur="this.style.borderColor='#ccc';" />
            <i class="search-icon fas fa-search"
                style="position: absolute; right: 15px; color: #aaa; font-size: 20px; cursor: pointer; transition: color 0.3s ease;"
                onmouseover="this.style.color='#007bff';" onmouseout="this.style.color='#aaa';">
            </i>
        </div>

        <?php if (empty($files['Pages'])): ?>
        <p>No files available in the pages section.</p>
        <?php else: ?>
        <div class="grid-container" id="file-grid2">
            <!-- Files will be inserted here -->
        </div>
        <!-- Pagination controls -->
        <div id="pagination"></div>
        <?php endif; ?>
    </div>
</div>

<!-- Popup Modal -->
<div id="rules-popup" class="popup-modal">
    <div class="popup-content">
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

<script>
// Global variable to store the current search query
let currentSearchQuery = '';

// Listen for search input changes
document.getElementById("searchBar").addEventListener("input", function() {
    currentSearchQuery = this.value;
    fetchFiles(1, currentSearchQuery); // Reset to first page on search change
});

function fetchFiles(currentPage = 1, searchQuery = '') {
    fetch(`get_files.php?section=pages&page=${currentPage}&search=${encodeURIComponent(searchQuery)}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch files');
            }
            return response.json();
        })
        .then(data => {
            const gridContainer = document.getElementById("file-grid2");
            gridContainer.innerHTML = '';

            if (data.files && data.files.length > 0) {
                data.files.forEach(file => {
                    const price = parseFloat(file.price);
                    const formattedPrice = isNaN(price) ? 'N/A' : `$${price.toFixed(2)}`;

                    // Pass PHP session info to JS – assumes $_SESSION['active'] is set
                    const isActive = <?= htmlspecialchars(json_encode($_SESSION['active'] === 1)); ?>;

                    const fileItem = `
                        <div class="tool-item">
                            <h3>${escapeHtml(file.name)}</h3>
                            <p>${escapeHtml(file.description).replace(/\n/g, '<br>')}</p>
                            <p>Price: ${formattedPrice}</p>
                            <a class="buy-button ${!isActive ? 'disabled' : ''}" 
                                href="${isActive ? `buy_tool.php?tool_id=${file.id}&section=leads` : 'javascript:void(0);'}" 
                                data-id="${file.id}" data-section="leads">
                                <span class="price">${formattedPrice}</span>
                                <span class="buy-now">Buy Now</span>
                            </a>
                        </div>
                    `;
                    gridContainer.innerHTML += fileItem;
                });
                createPagination(data.currentPage, data.totalPages);
            } else {
                gridContainer.innerHTML = '<p>No pages found.</p>';
                document.getElementById("pagination").innerHTML = '';
            }
        })
        .catch(error => {
            console.error('Error fetching files:', error);
            // alert('No files available in the pages section.');
        });
}

function createPagination(currentPage, totalPages) {
    const paginationContainer = document.getElementById("pagination");
    paginationContainer.innerHTML = '';

    const prevButton = document.createElement("button");
    prevButton.textContent = "Previous";
    prevButton.disabled = currentPage <= 1;
    prevButton.addEventListener("click", () => {
        if (currentPage > 1) {
            fetchFiles(currentPage - 1, currentSearchQuery);
        }
    });

    const nextButton = document.createElement("button");
    nextButton.textContent = "Next";
    nextButton.disabled = currentPage >= totalPages;
    nextButton.addEventListener("click", () => {
        if (currentPage < totalPages) {
            fetchFiles(currentPage + 1, currentSearchQuery);
        }
    });

    const pageInfo = document.createElement("span");
    pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;

    paginationContainer.appendChild(prevButton);
    paginationContainer.appendChild(pageInfo);
    paginationContainer.appendChild(nextButton);
}

function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Initial file fetch
fetchFiles();

// Popup message handling
function showPopupMessage(message, type) {
    const popup = document.getElementById('rules-popup');
    const messageElement = popup.querySelector('.message');
    messageElement.textContent = message;
    popup.style.display = 'flex';
}

function closeRulesPopup() {
    const popup = document.getElementById('rules-popup');
    popup.style.display = 'none';
}

// Buy button handler using event delegation
document.addEventListener("click", function(event) {
    const button = event.target.closest(".buy-button");
    if (button) {
        event.preventDefault(); // Prevent default link navigation

        // If the button is disabled, do nothing
        if (button.classList.contains('disabled')) return;

        const toolId = button.getAttribute("data-id");
        const section = button.getAttribute("data-section");

        alertify.confirm(
            "Confirmation",
            "Are you sure you want to buy this item?",
            function() {
                // Send AJAX POST request on confirmation
                fetch("buy_tool.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded",
                        },
                        body: `tool_id=${encodeURIComponent(toolId)}&section=${encodeURIComponent(section)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showPopupMessage(data.success, 'success');
                            setTimeout(() => window.location.reload(), 2000);
                        } else {
                            showPopupMessage(data.error, 'error');
                        }
                    })
                    .catch(error => {
                        console.error("Error buying tool:", error);
                        alertify.error("An error occurred. Please try again later.");
                    });
            },
            function() {
                alertify.error("Purchase cancelled.");
            }
        ).set("labels", {
            ok: "Confirm",
            cancel: "Cancel"
        });
    }
});
</script>
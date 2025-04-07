<?php
// If this is an AJAX request, fetch and output files as JSON
require '../../global.php';

if (isset($_GET['ajax'])) {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $search = isset($_GET['search']) ? $_GET['search'] : ''; // Capture search query

    $files = $settings->getFilesBySection2('pages', 12, $page, $search);

    header('Content-Type: application/json');
    echo json_encode([
        'files' => $files['files'],
        'currentPage' => $files['currentPage'],
        'totalPages' => $files['totalPages']
    ]);
    exit;
}

include_once('../../newuser.php');
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Tools Section</title>
    <style>
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

    .buy-button {
        display: inline-block;
        background-color: #28a745;
        color: #fff;
        padding: 8px 12px;
        border: none;
        border-radius: 4px;
        text-decoration: none;
        cursor: pointer;
        text-align: center;
    }

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
    }

    a.buy-button {
        height: 38px !important;
        width: 100px !important;
        text-align: center !important;
    }

    a.buy-button:hover {
        background-color: #218838;
        cursor: pointer;
    }

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
    </style>
</head>

<body>
    <div class="main-content">
        <div id="leads" class="uuper">
            <h2>Tools Section</h2>
            <div style="text-align: center; margin-bottom: 10px;">
                <h3 style="margin: 0; font-size: 18px; color: #333;">Search for tools</h3>
            </div>
            <div
                style="position: relative; width: 100%; max-width: 400px; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                <input type="text" id="searchBar" placeholder="Search tools..."
                    style="width: 100%; padding: 12px 20px; border-radius: 25px; border: 1px solid #ccc; font-size: 16px; transition: all 0.3s ease-in-out;"
                    onfocus="this.style.borderColor='#007bff';" onblur="this.style.borderColor='#ccc';" />
                <i class="search-icon fas fa-search"
                    style="position: absolute; right: 15px; color: #aaa; font-size: 20px; cursor: pointer; transition: color 0.3s ease;"
                    onmouseover="this.style.color='#007bff';" onmouseout="this.style.color='#aaa';">
                </i>
            </div>
            <div class="grid-container" id="file-grid2">
                <!-- Files will be inserted here -->
            </div>
            <!-- Pagination controls -->
            <div id="pagination"></div>
        </div>
    </div>
    <div id="rules-popup" class="popup-modal" style="display: none;">
        <div class="popup-content" style="position: absolute;top: 50%;right: 20%;">
            <span class="close" onclick="closeRulesPopup()">
                <i class="fas fa-times"></i>
            </span>
            <p class="message"></p>
        </div>
    </div>
    </div>
    <?php include_once('../../footer.php'); ?>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Fetch files
        fetchFiles();

        // Redirect on clicking the search bar
        const searchBar = document.getElementById("searchBar");
        searchBar.addEventListener("click", function(e) {
            e.preventDefault();
            handleInactiveAccount();
        });

        // Disable and redirect pagination buttons
        document.addEventListener("click", function(event) {
            if (event.target.closest("#pagination button")) {
                event.preventDefault();
                handleInactiveAccount();
            }
        });

        // Disable and redirect buying buttons
        document.addEventListener("click", function(event) {
            if (event.target.closest(".buy-button")) {
                event.preventDefault();
                handleInactiveAccount();
            }
        });

        // Function to fetch files (same as before)
        function fetchFiles(currentPage = 1, searchQuery = '') {
            fetch(`?ajax=true&page=${currentPage}&search=${encodeURIComponent(searchQuery)}`)
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

                            const fileItem = `
                                <div class="tool-item">
                                    <h3>${escapeHtml(file.name)}</h3>
                                    <p>${escapeHtml(file.description).replace(/\n/g, '<br>')}</p>
                                    <p>Price: ${formattedPrice}</p>
                                    <a class="buy-button disabled"
                                        href="javascript:void(0);"
                                        data-id="${file.id}" data-section="leads">
                                        <span class="price">${formattedPrice}</span>
                                        <span class="buy-now">Buy Now</span>
                                    </a>
                                </div>
                            `;
                            gridContainer.innerHTML += fileItem;
                        });
                        createPagination(data.currentPage, data.totalPages);
                    }
                })
                .catch(error => {
                    console.error('Error fetching files:', error);
                    alert('No files available in the Tools section.');
                });
        }

        // Disable and redirect pagination
        function createPagination(currentPage, totalPages) {
            const paginationContainer = document.getElementById("pagination");
            paginationContainer.innerHTML = '';
            const prevButton = document.createElement("button");
            prevButton.textContent = "Previous";
            prevButton.disabled = true;
            prevButton.addEventListener("click", function(e) {
                e.preventDefault();
                handleInactiveAccount();
            });
            const nextButton = document.createElement("button");
            nextButton.textContent = "Next";
            nextButton.disabled = true;
            nextButton.addEventListener("click", function(e) {
                e.preventDefault();
                handleInactiveAccount();
            });
            const pageInfo = document.createElement("span");
            pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
            paginationContainer.appendChild(prevButton);
            paginationContainer.appendChild(pageInfo);
            paginationContainer.appendChild(nextButton);
        }

        // Escape HTML to prevent XSS
        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/\"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Handle inactive account
        function handleInactiveAccount() {
            alert("Your account is inactive. You need to top up some balance.");
            // Redirect to a specific page
            window.location.href =
                '<?= $urlval?>pages/inactive/index.php'; // Replace with your redirect destination
        }
    });
    </script>
</body>

</html>
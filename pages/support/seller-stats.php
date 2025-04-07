<?php
include_once('../../header.php');
$balance_saller = $user['credit_cards_balance'] + $user['dumps_balance'];

// Ensure seller percentage is defined (default to 0 if not set)
$sellerPercentage = isset($user['seller_percentage']) ? (float)$user['seller_percentage'] : 0;

// Fetch seller's uploaded cards (read-only) with price included
$seller_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT id, card_number, cc_status, purchased_at, price 
                       FROM credit_cards 
                       WHERE seller_id = ? AND (deleted IS NULL OR deleted != 1)");
$stmt->execute([$seller_id]);
$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);



// Fetch seller's uploaded dumps (read-only) with price included
$stmt = $pdo->prepare("SELECT id, track1, track2, dump_status, purchased_at, price 
                       FROM dumps 
                       WHERE seller_id = ? AND (deleted IS NULL OR deleted != 1)");
$stmt->execute([$seller_id]);
$dumps = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmtBalance = $pdo->prepare("SELECT total_earned FROM users WHERE id = ? LIMIT 1");
$stmtBalance->execute([$seller_id]);
$balanceData1 = $stmtBalance->fetch(PDO::FETCH_ASSOC);

$sellerPercentage = isset($user['seller_percentage']) ? min((float)$user['seller_percentage'], 100) : 0;

$stmtBalance = $pdo->prepare("SELECT seller_actual_balance FROM users WHERE id = ? LIMIT 1");
$stmtBalance->execute([$seller_id]);
$balanceData = $stmtBalance->fetch(PDO::FETCH_ASSOC);


?>

<style>
.upload-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

.upload-table th,
.upload-table td {
    border: 1px solid #ddd;
    padding: 10px 12px;
    text-align: left;
}

.upload-table thead {
    background-color: #0c182f;
    color: #fff;
}

.upload-table tr:nth-child(even) {
    background-color: #f9f9f9;
}


#seller-stats {
    width: 100% !important;
    max-width: 100% !important;
    display: block !important;
    margin: 0px !important;
    box-shadow: none !important;
}

.stats-container {
    box-shadow: none !important;
    background-color: #f9f9f94a !important;
    border-radius: 0px;
    border: 1px solid #e6e6e7 !important;
}

.stats-container h3 {
    font-size: 32px !important;
    font: weight 400px;
    ;
    color: #0c182f !important;
    margin-bottom: 10px !important;
    padding-bottom: 10px !important;
    border-bottom: 1px solid #e6e6e7 !important;
}

.stat-item strong {
    font-size: 18px !important;
    font: weight 400px;
    ;
    color: #0c182f !important;

}

/* Styling for input fields */
.inpt-wtdr {
    display: flex;
    flex-direction: column;
    gap: 10px;
    max-width: 300px;
    margin: auto;
    margin-top: 20px;
}

.withdrawal_amount {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 14px;
}

/* Styling for buttons */
#withdrawal_amount3 {
    padding: 10px 20px;
    border: none;
    background-color: #0c182f;
    color: white;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btw-sbm {
    display: block;
    padding: 10px 20px;
    border: none;
    background-color: #0c182f;
    color: white;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

#withdrawal_amount3:hover {
    background-color: #0c182f;
}

#withdrawal_amount3:disabled {
    background-color: #6c757d;
    cursor: not-allowed;
}


.loader {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #0c182f;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-left: 5px;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}


.btw-sbm.disabled-btn {
    background-color: #d6d6d6;
    color: #999;
    cursor: not-allowed;
}

#error-message {
    margin-top: 10px;
    font-size: 14px;
    color: red;
    text-align: center;
}
</style>
<!-- Main Content Area -->
<div class="main-content">

    <?php if ($user['seller'] == 1): ?>
    <div id="seller-stats" class="section uuper">
        <h2><i class="fas fa-chart-bar"></i> Seller Stats</h2> <!-- Main title -->

        <!-- Seller Percentage -->
        <div class="stats-container">
            <h3>Seller Percentage</h3>
            <div class="stat-item">Percentage:
                <strong><?php echo number_format($user['seller_percentage'], 2); ?>%</strong>
            </div>
            <div class="stat-item">
                Payable Amount: <strong>
                    <?php 
      echo '$' . number_format($balanceData1['total_earned'] ?? 0, 2);
    ?>
                </strong>
            </div>
            <div class="stat-item">
                Overall Balance: <strong>
                    <?php 
      // Display the stored seller_actual_balance with a dollar sign and formatted to 2 decimals.
      echo '$' . number_format($balanceData['seller_actual_balance'] ?? 0, 2);
    ?>
                </strong>
            </div>

            <div class="stat-item">Total earned from Credit Cards:
                <strong>$<?php echo number_format($user['credit_cards_total_earned'], 2); ?></strong>
            </div>
            <div class="stat-item">Total earned from Dumps:
                <strong>$<?php echo number_format($user['dumps_total_earned'], 2); ?></strong>
            </div>
        </div>

        <!-- Credit Cards Stats -->
        <div class="stats-container">
            <h3>Credit Cards Stats</h3>
            <div class="stat-item">Uploaded Cards: <strong><?php echo $totalCardsUploaded; ?></strong></div>
            <div class="stat-item">Unsold Cards: <strong><?php echo $unsoldCards; ?></strong></div>
            <div class="stat-item">Sold Cards: <strong><?php echo $soldCardsCount; ?></strong></div>
        </div>

        <!-- Dumps Stats -->
        <div class="stats-container">
            <h3>Dumps Stats</h3>
            <div class="stat-item">Uploaded Dumps: <strong><?php echo $totalDumpsUploaded; ?></strong></div>
            <div class="stat-item">Unsold Dumps: <strong><?php echo $unsoldDumps; ?></strong></div>
            <div class="stat-item">Sold Dumps: <strong><?php echo $soldDumpsCount; ?></strong></div>
        </div>



        <?php
// Calculate summary for cards
$totalCards = count($cards);
$liveCards = 0;
$deadCards = 0;
foreach ($cards as $card) {
    $status = strtolower($card['cc_status'] ?? '');
    if ($status === 'live') {
        $liveCards++;
    } elseif ($status === 'dead' || $status === 'disabled') {
        $deadCards++;
    }
}
$liveCardPercentage = ($totalCards > 0) ? round(($liveCards / $totalCards) * 100) : 0;
$deadCardPercentage = ($totalCards > 0) ? round(($deadCards / $totalCards) * 100) : 0;

// Calculate summary for dumps
$totalDumps = count($dumps);
$liveDumps = 0;
$deadDumps = 0;
foreach ($dumps as $dump) {
    $status = strtolower($dump['dump_status'] ?? '');
    if ($status === 'live') {
        $liveDumps++;
    } elseif ($status === 'dead' || $status === 'disabled') {
        $deadDumps++;
    }
}
$liveDumpPercentage = ($totalDumps > 0) ? round(($liveDumps / $totalDumps) * 100) : 0;
$deadDumpPercentage = ($totalDumps > 0) ? round(($deadDumps / $totalDumps) * 100) : 0;
?>

        <!-- Additional CSS for summary containers -->
        <style>
        .summary-container {
            padding: 10px;
            background-color: #fff;
            border: 1px solid #e6e6e7;
            margin-bottom: 20px;
        }

        .summary-container h3 {
            margin: 0 0 10px;
            font-size: 24px;
            color: #0c182f;
        }

        .summary-container p {
            margin: 0;
            font-size: 16px;
            color: #333;
        }
        </style>



        <!-- Collapsible Uploaded Cards Section -->
        <div class="collapsible-container">
            <h3 class="collapsible-header">
                Cards Checking Stats.
                <span class="toggle-icon">[-]</span>
            </h3>
            <div class="collapsible-content" style="display: none;">
                <h3>My Checked Cards Summary</h3>
                <p>
                    <!-- Example pill badges for Card summary -->
                <div style="margin-top: 10px; display: flex; gap: 10px; flex-wrap: wrap;">
                    <!-- Unsold Cards -->
                    <span class="filter-pill" data-filter="unsold" style="
    display: inline-block;
    padding: 8px 14px;
    border-radius: 20px;
    background-color: #007bff;
    color: #fff;
    font-weight: bold;
    cursor: pointer;">
                        Unsold Cards: <?php echo $unsoldCards; ?>
                    </span>

                    <!-- Dead Cards -->
                    <span class="filter-pill" data-filter="dead" style="
    display: inline-block;
    padding: 8px 14px;
    border-radius: 20px;
    background-color: #dc3545;
    color: #fff;
    font-weight: bold;
    cursor: pointer;">
                        DEAD: <?php echo $deadCards; ?>
                    </span>

                    <!-- Live Cards -->
                    <span class="filter-pill" data-filter="live" style="
    display: inline-block;
    padding: 8px 14px;
    border-radius: 20px;
    background-color: #28a745;
    color: #fff;
    font-weight: bold;
    cursor: pointer;">
                        LIVE: <?php echo $liveCards; ?>
                    </span>
                </div>
                <div>
                    <br>
                    <h5>Live/Dead Cards Summary</h5>
                    <p>
                        <!--  Percentage -->
                        <span>
                            <li><span style="color: green;  font-weight: 700;"> Overall LIVE Percentage:
                                    <?php echo $liveCardPercentage; ?>%</span> </li>
                            <li><span style="color: red;  font-weight: 700;">Overall DEAD Percentage:
                                    <?php echo $deadCardPercentage; ?>%</span> </li>
                        </span>



                    </p>
                    <?php if (empty($cards)): ?>
                    <p>No cards uploaded.</p>
                    <?php else: ?>
                    <table id="cardsTable" class="upload-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Card Number</th>
                                <th>Status</th>
                                <th>Sold Status</th>
                                <th>Seller %</th>
                                <th>Earned</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($cards as $card): ?>
                            <?php 
    $rawStatus = strtolower($card['cc_status'] ?? '');
    if ($rawStatus === 'disabled' || $rawStatus === 'dead') {
        $displayStatus = 'DEAD';
        $status_color = 'red';
    } elseif ($rawStatus === 'live') {
        $displayStatus = 'LIVE';
        $status_color = 'green';
    } elseif ($rawStatus === 'unchecked') {
        $displayStatus = '-';
        $status_color = 'inherit';
    } else {
        $displayStatus = strtoupper($card['cc_status'] ?? '');
        $status_color = 'inherit';
    }
    $soldText = !empty($card['purchased_at']) ? 'Sold' : 'Not Sold';
  ?>
                            <tr data-status="<?php echo $rawStatus; ?>"
                                data-sold="<?php echo empty($card['purchased_at']) ? 'unsold' : 'sold'; ?>">

                                <td><?php echo $i++; ?></td>
                                <td><?php echo htmlspecialchars($card['card_number'] ?? ''); ?></td>
                                <td style="color: <?php echo $status_color; ?>;">
                                    <?php echo htmlspecialchars($displayStatus); ?></td>
                                <td><?php echo htmlspecialchars($soldText); ?></td>
                                <td><?php echo number_format($sellerPercentage, 2); ?>%</td>
                                <?php 
      if ($rawStatus === 'dead' || $rawStatus === 'disabled') {
          $earned = 0;
      } elseif (!empty($card['purchased_at']) && isset($card['price']) && is_numeric($card['price'])) {
          $earned = (float)$card['price'] * $sellerPercentage / 100;
      } else {
          $earned = null;
      }
    ?>
                                <td><?php echo !is_null($earned) ? '$' . number_format($earned, 2) : '-'; ?></td>
                            </tr>
                            <?php endforeach; ?>

                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const pills = document.querySelectorAll('.filter-pill');
                pills.forEach(function(pill) {
                    pill.addEventListener('click', function() {
                        const filter = pill.getAttribute('data-filter');
                        const rows = document.querySelectorAll('#cardsTable tbody tr');
                        if (filter === 'unsold') {
                            rows.forEach(function(row) {
                                row.style.display = (row.getAttribute('data-sold') ===
                                    'unsold') ? '' : 'none';
                            });
                        } else {
                            rows.forEach(function(row) {
                                row.style.display = (filter === 'all' || row
                                        .getAttribute('data-status') === filter) ? '' :
                                    'none';
                            });
                        }
                    });
                });
            });
            </script>



            <!-- Collapsible Uploaded Dumps Section -->
            <div class="collapsible-container">
                <h3 class="collapsible-header">
                    Dumps Checking Stats.
                    <span class="toggle-icon">[-]</span>
                </h3>
                <div class="collapsible-content" style="display: none;">
                    <h3>My Checked Dumps Summary</h3>
                    <p>
                    <div id="dump-filters" style="margin-top: 10px; display: flex; gap: 10px; flex-wrap: wrap;">
                        <!-- Unsold Dumps  -->
                        <span class="dump-filter-pill" data-filter="unsold" style="
    display: inline-block;
    padding: 8px 14px;
    border-radius: 20px;
    background-color: #007bff;
    color: #fff;
    font-weight: bold;
    cursor: pointer;">
                            Unsold Dumps: <?php echo $unsoldDumps; ?>
                        </span>

                        <!-- Dead Dumps -->
                        <span class="dump-filter-pill" data-filter="dead" style="
    display: inline-block;
    padding: 8px 14px;
    border-radius: 20px;
    background-color: #dc3545;
    color: #fff;
    font-weight: bold;
    cursor: pointer;">
                            DEAD: <?php echo $deadDumps; ?>
                        </span>

                        <!-- Live Dumps -->
                        <span class="dump-filter-pill" data-filter="live" style="
    display: inline-block;
    padding: 8px 14px;
    border-radius: 20px;
    background-color: #28a745;
    color: #fff;
    font-weight: bold;
    cursor: pointer;">
                            LIVE: <?php echo $liveDumps; ?>
                        </span>
                    </div>
                    <div>
                        <br>
                        <h5>Live/Dead Dumps Summary</h5>
                        <p>
                            <!-- Percentage -->
                            <span>
                                <li> <span style="color: green; font-weight: 700; "> Overall LIVE Percentage:
                                        <?php echo $liveDumpPercentage; ?>%</span> </li>
                                <li><span style="color: red;  font-weight: 700;">Overall DEAD Percentage: <span
                                            style="color: red;"><?php echo $deadDumpPercentage; ?>%</span></li>
                            </span>




                        </p>
                        <?php if (empty($dumps)): ?>
                        <p>No dumps uploaded.</p>
                        <?php else: ?>
                        <table id="dumps" class="upload-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Card Number</th>
                                    <th>Status</th>
                                    <th>Sold Status</th>
                                    <th>Seller %</th>
                                    <th>Earned</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $j = 1; ?>
                                <?php foreach ($dumps as $dump): ?>
                                <?php 
    $rawStatus = strtolower($dump['dump_status'] ?? '');
    if ($rawStatus === 'disabled' || $rawStatus === 'dead') {
        $displayStatus = 'DEAD';
        $status_color = 'red';
    } elseif ($rawStatus === 'live') {
        $displayStatus = 'LIVE';
        $status_color = 'green';
    } elseif ($rawStatus === 'unchecked' || $rawStatus === 'unsold') {
        $displayStatus = '-';
        $status_color = 'inherit';
    } else {
        $displayStatus = strtoupper($dump['dump_status'] ?? '');
        $status_color = 'inherit';
    }
    $soldText = !empty($dump['purchased_at']) ? 'Sold' : 'Not Sold';
  ?>
                                <tr data-status="<?php echo $rawStatus; ?>"
                                    data-sold="<?php echo empty($dump['purchased_at']) ? 'unsold' : 'sold'; ?>">

                                    <td><?php echo $j++; ?></td>
                                    <td>
                                        <?php echo isset($dump['track2']) ? htmlspecialchars(explode('=', $dump['track2'])[0]) : ''; ?>
                                    </td>
                                    <td style="color: <?php echo $status_color; ?>;">
                                        <?php echo htmlspecialchars($displayStatus); ?></td>
                                    <td><?php echo htmlspecialchars($soldText); ?></td>
                                    <td><?php echo number_format($sellerPercentage, 2); ?>%</td>
                                    <?php 
      if ($rawStatus === 'dead' || $rawStatus === 'disabled') {
          $earned = 0;
      } elseif (!empty($dump['purchased_at']) && isset($dump['price']) && is_numeric($dump['price'])) {
          $earned = (float)$dump['price'] * $sellerPercentage / 100;
      } else {
          $earned = null;
      }
    ?>
                                    <td><?php echo !is_null($earned) ? '$' . number_format($earned, 2) : '-'; ?></td>
                                </tr>
                                <?php endforeach; ?>

                            </tbody>
                        </table>
                        <?php endif; ?>
                    </div>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const pills = document.querySelectorAll('.dump-filter-pill');
                    pills.forEach(function(pill) {
                        pill.addEventListener('click', function() {
                            const filter = pill.getAttribute('data-filter');
                            // Updated selector: using #dumps instead of #dumpsTable
                            const rows = document.querySelectorAll('#dumps tbody tr');
                            if (filter === 'unsold') {
                                rows.forEach(function(row) {
                                    row.style.display = (row.getAttribute(
                                        'data-sold') === 'unsold') ? '' : 'none';
                                });
                            } else {
                                rows.forEach(function(row) {
                                    row.style.display = (filter === 'all' || row
                                            .getAttribute('data-status') === filter) ?
                                        '' : 'none';
                                });
                            }
                        });
                    });
                });
                </script>


                <script>
                function paginateTable(tableId, rowsPerPage) {
                    const table = document.getElementById(tableId);
                    if (!table) return;
                    const tbody = table.querySelector('tbody');
                    if (!tbody) return;
                    const rows = Array.from(tbody.querySelectorAll('tr'));
                    const totalRows = rows.length;
                    const totalPages = Math.ceil(totalRows / rowsPerPage);

                    // Create a container for pagination buttons
                    let paginationContainer = document.createElement('div');
                    paginationContainer.className = 'pagination-container';
                    paginationContainer.style.textAlign = 'center';
                    paginationContainer.style.marginTop = '10px';

                    // Function to display rows for a given page number
                    function displayPage(page) {
                        const start = (page - 1) * rowsPerPage;
                        const end = start + rowsPerPage;
                        rows.forEach((row, index) => {
                            row.style.display = (index >= start && index < end) ? '' : 'none';
                        });
                    }

                    // Create buttons for each page
                    for (let i = 1; i <= totalPages; i++) {
                        let btn = document.createElement('button');
                        btn.textContent = i;
                        btn.style.padding = '6px 12px';
                        btn.style.margin = '0 5px';
                        btn.style.border = 'none';
                        btn.style.borderRadius = '4px';
                        btn.style.backgroundColor = '#0c182f';
                        btn.style.color = '#fff';
                        btn.style.cursor = 'pointer';
                        btn.addEventListener('click', () => {
                            displayPage(i);
                            // Remove active class from all buttons
                            const buttons = paginationContainer.querySelectorAll('button');
                            buttons.forEach(b => b.classList.remove('active'));
                            btn.classList.add('active');
                        });
                        paginationContainer.appendChild(btn);
                    }

                    // Insert the pagination controls after the table
                    table.parentNode.insertBefore(paginationContainer, table.nextSibling);

                    // Display the first page by default and set first button active
                    if (totalPages > 0) {
                        displayPage(1);
                        const firstBtn = paginationContainer.querySelector('button');
                        if (firstBtn) firstBtn.classList.add('active');
                    }
                }

                // Initialize pagination on the dumps table with 10 rows per page
                document.addEventListener('DOMContentLoaded', function() {
                    paginateTable('dumps', 10);
                });
                </script>



                <script>
                function paginateTable(tableId, rowsPerPage) {
                    const table = document.getElementById(tableId);
                    if (!table) return;
                    const tbody = table.querySelector('tbody');
                    const rows = Array.from(tbody.getElementsByTagName('tr'));
                    const totalRows = rows.length;
                    const totalPages = Math.ceil(totalRows / rowsPerPage);

                    // Create a container for pagination buttons
                    let paginationContainer = document.createElement('div');
                    paginationContainer.className = 'pagination-container';

                    // Function to display rows for a given page number
                    function displayPage(page) {
                        const start = (page - 1) * rowsPerPage;
                        const end = start + rowsPerPage;
                        rows.forEach((row, index) => {
                            row.style.display = (index >= start && index < end) ? '' : 'none';
                        });
                    }

                    // Create buttons for each page
                    for (let i = 1; i <= totalPages; i++) {
                        let btn = document.createElement('button');
                        btn.textContent = i;
                        btn.addEventListener('click', () => {
                            displayPage(i);
                            // Remove active class from all buttons
                            const buttons = paginationContainer.getElementsByTagName('button');
                            Array.from(buttons).forEach(b => b.classList.remove('active'));
                            // Add active class to this button
                            btn.classList.add('active');
                        });
                        paginationContainer.appendChild(btn);
                    }

                    // Insert the pagination controls after the table
                    table.parentNode.insertBefore(paginationContainer, table.nextSibling);

                    // Display the first page by default and set first button active
                    if (totalPages > 0) {
                        displayPage(1);
                        const firstBtn = paginationContainer.getElementsByTagName('button')[0];
                        firstBtn.classList.add('active');
                    }
                }

                document.addEventListener('DOMContentLoaded', function() {
                    // Set desired rows per page (adjust as needed)
                    paginateTable('cardsTable', 10);
                    paginateTable('dumpsTable', 10);
                });
                </script>





                <!-- Additional CSS for Collapsible Sections -->
                <style>
                .pagination-container {
                    text-align: center;
                    margin-top: 10px;
                }

                .pagination-container button {
                    background-color: #0c182f;
                    color: #fff;
                    border: none;
                    padding: 6px 12px;
                    margin: 0 5px;
                    border-radius: 4px;
                    cursor: pointer;
                    transition: background-color 0.3s ease, color 0.3s ease;
                }

                .pagination-container button:hover {
                    background-color: #333;
                }

                .pagination-container button.active {
                    background-color: #fff;
                    color: #0c182f;
                    font-weight: bold;
                }



                .collapsible-container {
                    margin-bottom: 20px;
                    border: 1px solid #e6e6e7;
                    background-color: #f9f9f94a;
                }

                .collapsible-header {
                    background-color: #0c182f;
                    color: #fff;
                    padding: 10px;
                    cursor: pointer;
                    font-size: 20px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin: 0;
                }

                .collapsible-content {
                    padding: 10px;
                    display: none;
                    /* Initially expanded; change to none for collapsed by default */
                }

                .toggle-icon {
                    font-size: 18px;
                }

                .upload-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 10px;
                }

                .upload-table th,
                .upload-table td {
                    border: 1px solid #ddd;
                    padding: 10px 12px;
                    text-align: left;
                }

                .upload-table thead {
                    background-color: #0c182f;
                    color: #fff;
                }
                </style>

                <!-- JavaScript for Collapsible Toggle -->
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var headers = document.querySelectorAll('.collapsible-header');
                    headers.forEach(function(header) {
                        header.addEventListener('click', function() {
                            var content = header.nextElementSibling;
                            if (content.style.display === 'none' || content.style.display ===
                                '') {
                                content.style.display = 'block';
                                header.querySelector('.toggle-icon').textContent = '[-]';
                            } else {
                                content.style.display = 'none';
                                header.querySelector('.toggle-icon').textContent = '[+]';
                            }
                        });
                    });
                });
                </script>






                <div class="stats-container">
                    <h3>Do you want to Withdraw</h3>
                    <div>
                        <button id="withdrawal_amount3" onclick="toggleInputForm(this)">Withdraw Balance</button>
                    </div>
                    <div class="inpt-wtdr" style="display: none;">
                        <form id="withdrawalForm">
                            <div class="inpt-wtdr">
                                <input type="text" class="withdrawal_amount" name="BTC_Address"
                                    placeholder="BTC Address" required>
                                <input type="text" class="withdrawal_amount" name="Secret_Code"
                                    placeholder="Secret Code" required maxlength="6" minlength="6" pattern="\d{6}">

                                <p class="withdrawal_amount readonly"
                                    style="background-color: #f5f5f5; color: #333; font-weight: bold; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                                    Your balance is:
                                    $<?php echo number_format($balanceData1['total_earned'] ?? 0, 2); ?>
                                </p>


                                <!-- Error message container -->
                                <div id="error-message"
                                    style="color: red; font-size: 14px; text-align: center; display: none;">
                                    Please fill in both BTC Address and Secret Code.
                                </div>

                                <input type="button" value="Submit"
                                    class="btw-sbm <?= (isset($balanceData1['total_earned']) && $balanceData1['total_earned'] == 0) ? 'disabled-btn' : '' ?>"
                                    <?= (isset($balanceData1['total_earned']) && $balanceData1['total_earned'] == 0) ? 'disabled' : '' ?>
                                    id="submitBtn">

                            </div>
                        </form>

                    </div>


                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>
</div>
<?php
include_once('../../footer.php');
?>
<script>
function toggleInputForm(button) {

    button.disabled = true;
    button.innerHTML = 'Loading <span class="loader"></span>';


    setTimeout(() => {

        const form = document.querySelector('.inpt-wtdr');
        if (form.style.display === 'none') {
            form.style.display = 'flex';
            button.innerHTML = 'Hide Form';
        } else {
            form.style.display = 'none';
            button.innerHTML = 'Withdraw Balance';
        }


        button.disabled = false;
    }, 1000);
}


document.getElementById("submitBtn").addEventListener("click", function() {
    var btcAddress = document.querySelector('input[name="BTC_Address"]').value;
    var secretCode = document.querySelector('input[name="Secret_Code"]').value;

    if (!btcAddress || !secretCode) {
        document.getElementById("error-message").style.display = 'block';
        return false;
    }

    // AJAX request to check session and validate the secret code
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "validate_withdrawal.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function() {
        if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.status === "success") {

                var formData = new FormData();
                formData.append("BTC_Address", btcAddress);
                formData.append("Secret_Code", secretCode);

                showPopupMessage(response.message);
                setTimeout(function() {
                    window.location.href = "<?= $urlval?>pages/support/index.php";
                }, 5000);



                withdrawalXhr.send(formData);

            } else {

                showPopupMessage(response.message);
            }
        } else {
            showPopupMessage("Something went wrong. Please try again.");
        }
    };

    xhr.send("btcAddress=" + btcAddress + "&secretCode=" + secretCode);
});

function closeRulesPopup() {
    const popup = document.getElementById('rules-popup');
    popup.style.display = 'none';
}
</script>
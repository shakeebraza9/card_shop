<?php

// Check if the admin is logged in. Adjust the session variable name as needed.
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

include_once('../global.php');

function getDistinctItemTypes($pdo) {
    $query = "SELECT DISTINCT item_type FROM activity_log";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    $itemTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $itemTypes;
}

function getDistinctUsernames($pdo) {
    $query = "SELECT DISTINCT user_name FROM activity_log ORDER BY user_name ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    $usernames = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $usernames;
}

$itemTypes = getDistinctItemTypes($pdo);
$usernames = getDistinctUsernames($pdo);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log</title>
    <!-- Include DataTable CSS and JS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../admin/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #4895ef;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --info: #4895ef;
            --warning: #f72585;
            --danger: #f72585;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --gray-light: #f1f3f5;
            --border-radius: 0.5rem;
            --box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e9ecef;
        }

        .dashboard-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }

        .dashboard-title i {
            color: var(--primary);
            margin-right: 0.5rem;
        }

        .filter-container {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
        }

        .filter-container:hover {
            box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.1);
        }

        .filter-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--dark);
            display: flex;
            align-items: center;
        }

        .filter-title i {
            margin-right: 0.5rem;
            color: var(--primary);
        }

        .filter-row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -0.5rem 1rem;
        }

        .filter-col {
            flex: 1;
            min-width: 200px;
            padding: 0 0.5rem;
            margin-bottom: 1rem;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-label {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--gray);
            margin-bottom: 0.5rem;
        }

        .filter-control {
            padding: 0.6rem 1rem;
            border: 1px solid #dee2e6;
            border-radius: var(--border-radius);
            font-size: 0.9rem;
            color: var(--dark);
            background-color: white;
            transition: var(--transition);
        }

        .filter-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
            outline: none;
        }

        .filter-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 1rem;
        }

        .btn {
            padding: 0.6rem 1.5rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            transition: var(--transition);
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--secondary);
            transform: translateY(-2px);
        }

        .btn-outline {
            background-color: transparent;
            color: var(--primary);
            border: 1px solid var(--primary);
            margin-right: 0.5rem;
        }

        .btn-outline:hover {
            background-color: var(--primary-light);
            color: white;
            transform: translateY(-2px);
        }

        .table-container {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
        }

        .table-container:hover {
            box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.1);
        }

        #activityLogTable {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        #activityLogTable thead th {
            background-color: var(--primary);
            color: white;
            font-weight: 500;
            padding: 1rem;
            text-align: left;
            border: none;
            position: relative;
        }

        #activityLogTable thead th:first-child {
            border-top-left-radius: var(--border-radius);
        }

        #activityLogTable thead th:last-child {
            border-top-right-radius: var(--border-radius);
        }

        #activityLogTable tbody tr {
            transition: var(--transition);
            cursor: pointer;
        }

        #activityLogTable tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
            transform: translateY(-2px);
        }

        #activityLogTable tbody td {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            color: var(--dark);
        }

        #activityLogTable tbody tr:last-child td {
            border-bottom: none;
        }

        /* DataTables custom styling */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.5rem 1rem;
            margin: 0 0.2rem;
            border-radius: var(--border-radius);
            border: none !important;
            background: var(--gray-light) !important;
            color: var(--dark) !important;
            transition: var(--transition);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--primary-light) !important;
            color: white !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary) !important;
            color: white !important;
        }

        .dataTables_wrapper .dataTables_info {
            padding-top: 1rem;
            color: var(--gray);
        }

        /* Modal styling */
        .modal-content {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
        }

        .modal-header {
            background-color: var(--primary);
            color: white;
            border-top-left-radius: var(--border-radius);
            border-top-right-radius: var(--border-radius);
            border-bottom: none;
            padding: 1.5rem;
        }

        .modal-title {
            font-weight: 600;
            font-size: 1.25rem;
        }

        .modal-header .close {
            color: white;
            opacity: 0.8;
            text-shadow: none;
            transition: var(--transition);
        }

        .modal-header .close:hover {
            opacity: 1;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            border-top: 1px solid #e9ecef;
            padding: 1.5rem;
        }

        .detail-item {
            margin-bottom: 1.25rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid #e9ecef;
        }

        .detail-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: var(--gray);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .detail-value {
            font-size: 1rem;
            color: var(--dark);
        }

        .btn-close-modal {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-close-modal:hover {
            background-color: var(--secondary);
            transform: translateY(-2px);
        }

        /* Flatpickr customization */
        .flatpickr-calendar {
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            border: none;
        }

        .flatpickr-day.selected {
            background: var(--primary);
            border-color: var(--primary);
        }

        .flatpickr-day.selected:hover {
            background: var(--primary-light);
            border-color: var(--primary-light);
        }

        .flatpickr-day:hover {
            background: var(--gray-light);
        }

        /* Active filter badge */
        .active-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .filter-badge {
            display: inline-flex;
            align-items: center;
            background-color: var(--primary-light);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 2rem;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .filter-badge i {
            margin-left: 0.5rem;
            cursor: pointer;
        }

        /* Responsive design */
        @media (max-width: 992px) {
            .dashboard-container {
                padding: 1.5rem;
            }
            
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .dashboard-title {
                margin-bottom: 1rem;
            }
            
            .filter-row {
                flex-direction: column;
            }
            
            .filter-col {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .table-container {
                padding: 1rem;
                overflow-x: auto;
            }
            
            #activityLogTable thead th,
            #activityLogTable tbody td {
                padding: 0.75rem;
            }
            
            .modal-header,
            .modal-body,
            .modal-footer {
                padding: 1rem;
            }
            
            .filter-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
                margin-right: 0;
            }
        }

        /* Loading animation */
        .dataTables_processing {
            background-color: rgba(255, 255, 255, 0.9) !important;
            color: var(--primary) !important;
            border-radius: var(--border-radius) !important;
            box-shadow: var(--box-shadow) !important;
            padding: 1rem !important;
            font-weight: 500 !important;
        }

        .header {
            display: flex !important!;
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-title"><i class="fas fa-history"></i> Activity Log</h1>
        </div>

        <div class="filter-container">
            <h2 class="filter-title"><i class="fas fa-filter"></i> Filter Options</h2>
            <form id="filterForm">
                <div class="filter-row">
                    <div class="filter-col">
                        <div class="filter-group">
                            <label class="filter-label" for="usernameFilter">Username:</label>
                            <select id="usernameFilter" class="filter-control">
                                <option value="">All Users</option>
                                <?php foreach ($usernames as $user) { ?>
                                <option value="<?= htmlspecialchars($user['user_name']); ?>"><?= htmlspecialchars($user['user_name']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="filter-col">
                        <div class="filter-group">
                            <label class="filter-label" for="itemTypeFilter">Filter by Item Type:</label>
                            <select id="itemTypeFilter" class="filter-control">
                                <option value="">All</option>
                                <?php foreach ($itemTypes as $item) { ?>
                                <option value="<?= htmlspecialchars($item['item_type']); ?>"><?= htmlspecialchars($item['item_type']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="filter-row">
                    <div class="filter-col">
                        <div class="filter-group">
                            <label class="filter-label" for="startDateFilter">Start Date:</label>
                            <input type="text" id="startDateFilter" class="filter-control datepicker" placeholder="Select start date">
                        </div>
                    </div>
                    <div class="filter-col">
                        <div class="filter-group">
                            <label class="filter-label" for="endDateFilter">End Date:</label>
                            <input type="text" id="endDateFilter" class="filter-control datepicker" placeholder="Select end date">
                        </div>
                    </div>
                </div>
                <div class="filter-actions">
                    <button type="button" id="resetFilters" class="btn btn-outline">
                        <i class="fas fa-undo"></i> Reset Filters
                    </button>
                    <button type="button" id="applyFilters" class="btn btn-primary">
                        <i class="fas fa-search"></i> Apply Filters
                    </button>
                </div>
            </form>
            <div class="active-filters" id="activeFilters">
                <!-- Active filters will be displayed here -->
            </div>
        </div>

        <div class="table-container">
            <table id="activityLogTable" class="display">
                <thead>
                    <tr>
                        <th>User Name</th>
                        <th>Item</th>
                        <th>Item id</th>
                        <th>Item Price</th>
                        <th>Item Type</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded dynamically -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for displaying row data -->
    <div id="activityLogModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-info-circle"></i> Activity Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="detail-item">
                        <div class="detail-label">User Name</div>
                        <div class="detail-value" id="modalUserName"></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Item</div>
                        <div class="detail-value" id="modalItem"></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Item id</div>
                        <div class="detail-value" id="modalItemId"></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Item Price</div>
                        <div class="detail-value" id="modalItemPrice"></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Item Type</div>
                        <div class="detail-value" id="modalItemType"></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Date</div>
                        <div class="detail-value" id="modalDate"></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Additional Information</div>
                        <div class="detail-value" id="modalAdditionalData"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-close-modal" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Initialize date pickers
        flatpickr(".datepicker", {
            dateFormat: "Y-m-d",
            allowInput: true,
            altInput: true,
            altFormat: "F j, Y",
            maxDate: "today"
        });

        // Initialize DataTable with the same configuration as original
        var table = $('#activityLogTable').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": false,
            "ajax": {
                "url": "../ajax/fetch_activity_log.php",
                "data": function(d) {
                    // Keep the original parameter name for item_type
                    d.item_type = $('#itemTypeFilter').val();
                    
                    // Add new filter parameters
                    d.user_name = $('#usernameFilter').val();
                    d.start_date = $('#startDateFilter').val();
                    d.end_date = $('#endDateFilter').val();
                }
            },
            "order": [[5, 'desc']], // Order by date column
            "columns": [
                { "data": "user_name" },
                { "data": "buy_itm" },
                { "data": "item_id" },
                { "data": "item_price" },
                { "data": "item_type" },
                { "data": "created_at" }
            ],
            "language": {
"processing": "<div class=\"spinner-border text-primary\" role=\"status\"><span class=\"sr-only\">Loading...</span></div> Loading data...",
                "emptyTable": "No activity records found",
                "zeroRecords": "No matching records found"
            },
            "responsive": true
        });

        // Apply filters button click
        $('#applyFilters').on('click', function() {
            updateActiveFilters();
            reloadTable();
        });

        // Reset filters button click
        $('#resetFilters').on('click', function() {
            $('#filterForm')[0].reset();
            $('#activeFilters').empty();
            reloadTable();
        });

        // Function to reload table with animation
        function reloadTable() {
            $('.table-container').css('opacity', '0.7');
            table.ajax.reload(function() {
                $('.table-container').css('opacity', '1');
            });
        }

        // Function to update active filters display
        function updateActiveFilters() {
            $('#activeFilters').empty();
            
            var username = $('#usernameFilter').val();
            var itemType = $('#itemTypeFilter').val();
            var startDate = $('#startDateFilter').val();
            var endDate = $('#endDateFilter').val();
            
            if (username) {
                addFilterBadge('User: ' + username, function() {
                    $('#usernameFilter').val('');
                    updateActiveFilters();
                    reloadTable();
                });
            }
            
            if (itemType) {
                addFilterBadge('Type: ' + itemType, function() {
                    $('#itemTypeFilter').val('');
                    updateActiveFilters();
                    reloadTable();
                });
            }
            
            if (startDate) {
                var displayDate = flatpickr.formatDate(new Date(startDate), "F j, Y");
                addFilterBadge('From: ' + displayDate, function() {
                    $('#startDateFilter').val('').trigger('change');
                    updateActiveFilters();
                    reloadTable();
                });
            }
            
            if (endDate) {
                var displayDate = flatpickr.formatDate(new Date(endDate), "F j, Y");
                addFilterBadge('To: ' + displayDate, function() {
                    $('#endDateFilter').val('').trigger('change');
                    updateActiveFilters();
                    reloadTable();
                });
            }
        }

        // Function to add a filter badge
        function addFilterBadge(text, removeCallback) {
            var badge = $('<span class="filter-badge">' + text + ' <i class="fas fa-times"></i></span>');
            badge.find('i').on('click', removeCallback);
            $('#activeFilters').append(badge);
        }

        // Event listener for row click
        $('#activityLogTable tbody').on('click', 'tr', function() {
            $(this).addClass('selected').siblings().removeClass('selected');
            
            var data = table.row(this).data(); // Get the data of the clicked row

            // Populate modal with the row data - keeping original field names
            $('#modalUserName').text(data.user_name);
            $('#modalItem').text(data.buy_itm);
            $('#modalItemId').text(data.item_id);
            $('#modalItemPrice').text(data.item_price);
            $('#modalItemType').text(data.item_type);
            $('#modalDate').text(data.created_at);
            
            // Show loading indicator in additional data section
            $('#modalAdditionalData').html('<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="sr-only">Loading...</span></div> Loading...');

            // Fetch additional data with AJAX - keeping original parameters
            $.ajax({
                url: '../ajax/fetch_additional_data.php',
                method: 'GET',
                data: {
                    item_id: data.item_id,
                    item_type: data.item_type
                },
                success: function(response) {
                    // Display the fetched data in the modal
                    $('#modalAdditionalData').text(response || 'No additional data available');
                },
                error: function() {
                    $('#modalAdditionalData').text('Failed to load additional data');
                }
            });

            // Open the modal with fade effect
            $('#activityLogModal').modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });
        });

        // Add responsive behavior
        $(window).resize(function() {
            table.responsive.recalc();
        });
    });
    </script>
</body>
</html>
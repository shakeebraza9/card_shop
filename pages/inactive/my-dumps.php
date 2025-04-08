<?php
include_once('../../newuser.php');
?>

<style>
.sold-dumps-table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    font-size: 16px;
    text-align: left;
}

.sold-dumps-table thead tr {
    background-color: #0c182f;
    border-bottom: 2px solid #dddddd;
}

.sold-dumps-table th,
.sold-dumps-table td {
    padding: 12px 15px;

    border: 1px solid #dddddd;
}

.sold-dumps-table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

.sold-dumps-table tbody tr:hover {
    background-color: #f1f1f1;
}

.copy-button,
.check-dump-button {
    padding: 8px 12px;
    margin: 0 5px;
    border: none;
    background-color: #0c182f;
    color: white;
    border-radius: 4px;
    cursor: pointer;
}

.check-card-button {
    background-color: #0c182f;
    color: white;
}

.check-card-button:hover {
    background-color: #e0a800;
}

.copy-button:hover,
.check-dump-button:hover {
    background-color: #0056b3;
}

.copy-button {
    background-color: #0c182f;
    color: white;

    margin: 0px 5px 0px 0px !important;
}

@media (max-width: 768px) {
    table {
        font-size: 14px;
    }

    td,
    th {
        padding: 8px 15px;
        text-wrap: nowrap !important;
    }

    a.buy-button {
        font-size: 12px;
        padding: 6px 10px;
    }

    .main-tbl321 {
        width: 100% !important;
        overflow-x: scroll !important;
    }

    a.buy-button {
        height: 30px !important;

    }
}

@keyframes shake-up-down {
    0% {
        transform: translateY(0);
    }

    25% {
        transform: translateY(-5px);
    }

    50% {
        transform: translateY(5px);
    }

    75% {
        transform: translateY(-5px);
    }

    100% {
        transform: translateY(0);
    }
}


.shake {
    animation: shake-up-down 0.5s ease-in-out;
}

#rules-btn:hover {
    animation: shake-up-down 0.5s ease-in-out;
}

.activity-log-table th {
    background-color: #0c182f;
    font-weight: bold;
    color: white;
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
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    z-index: 1;
    border-radius: 8px;
    animation: swing 2s ease-in-out infinite;
}

@keyframes swing {
    0% {
        transform: rotate(-45deg);
    }

    25% {
        transform: rotate(-43deg);
    }

    50% {
        transform: rotate(-47deg);
    }

    75% {
        transform: rotate(-43deg);
    }

    100% {
        transform: rotate(-45deg);
    }
}

@keyframes shake-up-down {
    0% {
        transform: translateY(0);
    }

    25% {
        transform: translateY(-5px);
    }

    50% {
        transform: translateY(5px);
    }

    75% {
        transform: translateY(-5px);
    }

    100% {
        transform: translateY(0);
    }
}


.shake {
    animation: shake-up-down 0.5s ease-in-out;
}
</style>

<!-- Main Content Area -->
<div class="main-content">
    <div id="my-dumps" class="uuper">
        <h2>My Dumps Section</h2>
        <?php if (empty($soldDumps)): ?>
        <p>No purchased dumps available.</p>
        <?php else: ?>
        <div class="main-tbl321">
            <table class="sold-dumps-table" id="soldDumpsTable">
                <thead style="background:#0c182f; color:white;">
                    <tr>
                        <th>ID</th>
                        <th>Track 1</th>
                        <th>Track 2</th>
                        <th>PIN</th>
                        <th>Country</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($soldDumps as $dump): ?>
                    <tr id="dump-<?php echo htmlspecialchars($dump['id']); ?>" class="dump-item">
                        <td style="padding: 10px; position: relative;">
                            <?php if ($dump['is_view'] == 0): ?>
                            <span class="ribbon">
                                New
                            </span>
                            <?php endif; ?>
                            <?php echo htmlspecialchars($dump['id']); ?>

                        </td>
                        <td><?php echo htmlspecialchars(empty($dump['data_segment_one']) ? '' : $dump['data_segment_one']); ?></td>
                        <td><?php echo htmlspecialchars($dump['data_segment_two']); ?></td>
                        <td><?php echo htmlspecialchars($dump['pin'] ?: 'No'); ?></td>
                        <td><?php echo htmlspecialchars($dump['country']); ?></td>
                        <td
                            style="padding: 10px;display: flex;justify-content: center;align-content: center;align-items: center;">
                            <button class="copy-button" style="padding: 6px 10px; 
                                border: none; 
                                border-radius: 3px; 
                                cursor: pointer; 
                                margin-right: 5px;"
                                onclick="copyDumpInfo(<?php echo htmlspecialchars($dump['id']); ?>)">Copy</button>

                            <button class="check-card-button" style="padding: 6px 10px; 
                        border: none; border-radius: 3px; cursor: pointer; margin:0px 5px 0px 0px;"
                                onclick="checkCard(<?php echo htmlspecialchars($dump['id']); ?>)">Check</button>
                            <a type="button" onclick="deleteRow(<?php echo htmlspecialchars($dump['id']); ?>)"
                                id="clear-btn" class="btn text-center btn-with-icon" style="background-color: #f44336; color: white; padding: 5px 15px; width:70px; border-radius: 4px; border: none; cursor: pointer; 
                                margin-top: -1px;">
                                <i class="fa fa-times"></i>
                                <span class="btn-text" style="text-align:center !important;">Delete</span>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
        <?php endif; ?>




        <div id="dumps-activity-log">

            <div style="display: flex; align-items: center; gap: 20px;">
                <h2>Dumps Activity Log</h2>
                <button id="rules-btnnew"
                    style="padding: 5px 15px; background-color: #f39c12; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; display: flex; align-items: center; gap: 5px;"
                    onclick="openRulesPopup()">
                    <i class="fas fa-gavel"></i>
                    Rules
                </button>
            </div>

            <div class="main-tbl321">
                <table id="activity-log-table" class="activity-log-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Card Number</th>
                            <th>Date Checked</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($checkedDumpsHistory)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center;">No activity logged yet</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($checkedDumpsHistory as $history): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($history['id']); ?></td>
                            <td><?php echo htmlspecialchars($history['creference_code']); ?></td>
                            <td><?php echo htmlspecialchars($history['date_checked']); ?></td>
                            <td><?php echo htmlspecialchars($history['status']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
</div>

<div id="rules-popup2" class="popup-modal">
    <div class="popup-content">
        <span class="close" onclick="closeRulesPopup()">
            <i class="fas fa-times"></i>
        </span>
        <h2>Purchased Information</h2>
        <p>Here are the updated rules for using the system:</p>
        <ul>

            <li>1. Purchased information will be automatically removed from these sections after 30 days. </li>
            <li>2. Users are advised to download or copy their information before the 30-day period ends to avoid losing
                access.</li>

        </ul>
    </div>
</div>
<?php
include_once('../../footer.php');
?>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const deletedIds = JSON.parse(localStorage.getItem('deletedRows')) || [];

    deletedIds.forEach(id => {
        const row = document.getElementById(`dump-${id}`);
        if (row) {
            row.style.display = 'none';
        }
    });
});


// function deleteRow(cardId) {
//     if (confirm('Are you sure you want to delete this row?')) {

//         const row = document.getElementById(`dump-${cardId}`);
//         if (row) {
//             row.style.display = 'none';
//         }


//         const deletedIds = JSON.parse(localStorage.getItem('deletedRows')) || [];
//         if (!deletedIds.includes(cardId)) {
//             deletedIds.push(cardId);
//             localStorage.setItem('deletedRows', JSON.stringify(deletedIds));
//         }
//     }
// }

$(document).ready(function() {
    $('#soldDumpsTable').DataTable({
        "paging": true,
        "searching": false,
        "ordering": false,
        "info": true,
        "lengthChange": true,
        "autoWidth": true,
        "responsive": true
    });
});

$(document).ready(function() {
    $('#activity-log-table').DataTable({
        "paging": true,
        "searching": false,
        "ordering": false,
        "info": true,
        "lengthChange": true,
        "autoWidth": true,
        "responsive": true
    });
});

function openRulesPopup() {
    document.getElementById("rules-popup2").style.display = "flex";
}


function closeRulesPopup() {
    document.getElementById('rules-popup2').style.display = 'none';
}

const rulesBtn = document.getElementById('rules-btnnew');
if (rulesBtn) {
    setInterval(() => {
        rulesBtn.classList.add('shake');
        setTimeout(() => {
            rulesBtn.classList.remove('shake');
        }, 500);
    }, 2000);
} else {
    console.error('Button with id "rules-btnnew" not found.');
}

document.addEventListener('DOMContentLoaded', function() {
    fetch('update_dump_isview.php', {
            method: 'POST',
        })
        .then(response => response.json())
        .then(data => {

        })
        .catch(error => {
            console.error('Error:', error);
        });
});


function deleteRow(id) {

    alertify.confirm(
        'Confirm Deletion',
        `Are you sure you want to delete this dump?`,
        function() {


            fetch('delete_dump.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        ticket_id: id
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alertify.success('Dump has been deleted.');

                        // Hide the row by targeting the dump ID (matching the row ID)
                        var dumpElement = document.getElementById('dump-' + id);
                        if (dumpElement) {
                            dumpElement.style.display = 'none'; // Hide the row
                        } else {
                            console.error('Dump element not found');
                        }

                        // Optionally refresh the page after 5 seconds
                        setTimeout(function() {
                            location.reload();
                        }, 5000);
                    } else {
                        alertify.error('Error: ' + data.message);
                        setTimeout(function() {
                            location.reload();
                        }, 5000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alertify.error('Something went wrong!');
                });
        },
        function() {
            alertify.error('Delete cancelled.');
        }
    ).set('labels', {
        ok: 'Confirm',
        cancel: 'Cancel'
    });
}
</script>


<script>
$(document).ready(function() {
    // Function to handle inactive account scenario
    function handleInactiveAccount() {
        alert("Your account is inactive. You need to top up some balance.");
        window.location.href = '<?= $urlval?>pages/inactive/index.php';
    }

    // Disable "Rules" button and show inactive account message instead of popup
    $("#rules-btnnew").on('click', function(e) {
        e.preventDefault();
        handleInactiveAccount();
    });

    // Disable "Select Columns" dropdown and all related interactions
    $("#dropdownBtn, #dropdownMenu input[type='checkbox']").on('click', function(e) {
        e.preventDefault();
        handleInactiveAccount();
    });

    // Disable table action buttons (Copy, Check, Delete)
    $(".copy-button, .check-card-button, #clear-btn").on('click', function(e) {
        e.preventDefault();
        handleInactiveAccount();
    });

    // Disable DataTable pagination buttons (if any)
    $(document).on('click', '.paginate_button', function(e) {
        e.preventDefault();
        handleInactiveAccount();
    });

    // Disable all inputs in the table
    $('#soldDumpsTable').on('click', 'input, button', function(e) {
        e.preventDefault();
        handleInactiveAccount();
    });

    // Disable log table interactions
    $('#cnproducts_activity_log').on('click', 'tr', function(e) {
        e.preventDefault();
        handleInactiveAccount();
    });
});
</script>
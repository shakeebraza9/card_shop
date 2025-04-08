<?php
include_once('../../header.php');
?>

<style>
.popup-modal {
    backdrop-filter: none !important;
}

.message-success {
    color: green;
    font-weight: bold;
    text-align: center;
}

.message-error {
    color: red;
    font-weight: bold;
    text-align: center;
}

/* Table container */
table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    font-size: 16px;
    font-family: Arial, sans-serif;
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Table header */
thead tr {
    background-color: #007bff;
    color: white;
    text-align: left;
}

thead th {
    padding: 12px 15px;
    border: 1px solid #ddd;
}

/* Table rows */
tbody tr {
    border-bottom: 1px solid #ddd;
}

tbody tr:nth-of-type(even) {
    background-color: #f9f9f9;
}

tbody tr:hover {
    background-color: #f1f1f1;
}

/* Table cells */
td {
    padding: 10px 15px;
    border: 1px solid #ddd;
    vertical-align: middle;
}

/* Action button */
a.buy-button {
    display: inline-block;
    background-color: #28a745;
    color: #fff;
    text-decoration: none;
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 14px;
    font-weight: bold;
    transition: background-color 0.3s ease;
}

a.buy-button:hover {
    background-color: #218838;
    cursor: pointer;
}

#credit-card-filters {
    display: flex !important;
    align-items: center !important;
    gap: 20px !important;
}

#credit-cards {
    display: inline-block !important;
    width: auto !important;
    margin-top: 20px;
    border-radius: 0px !important;
    box-shadow: none !important;
}

/* Responsive design */
@media (max-width: 768px) {
    table {
        font-size: 14px;
    }

    .main-content {
        overflow: scroll !important;
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

a.buy-button {
    height: 38px !important;
    width: 100px !important;
    text-align: center !important;
}

td {
    padding: 10px;
    border: 1px solid #ddd;
    position: relative;
}
</style>

<div class="main-content">
    <div id="credit-cards" class="uuper">
        <h2>Credit Cards Section</h2>

        <div class="filter-container-cards">
            <form id="credit-card-filters" method="post" action="#credit-cards">
                <div class="inpt-dmps-bx">
                    <label for="credit-card-bin">BIN</label>
                    <input type="text" name="cc_bin" id="credit-card-bin"
                        placeholder="Comma-separated for multiple - e.g., 123456, 654321">
                </div>

                <div class="inpt-dmps-bx">
                    <label for="credit-card-country">Country</label>
                    <select name="cc_country" id="credit-card-country">
                        <option value="">All</option>
                        <?php foreach ($creditCardCountries as $country): ?>
                        <option value="<?php echo htmlspecialchars($country); ?>">
                            <?php echo htmlspecialchars($country); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="inpt-dmps-bx">
                    <label for="state">State</label>
                    <input type="text" name="cc_state" id="state" placeholder="">
                </div>

                <div class="inpt-dmps-bx">
                    <label for="city">City</label>
                    <input type="text" name="cc_city" id="city" placeholder="">
                </div>

                <div class="inpt-dmps-bx">
                    <label for="zip">ZIP</label>
                    <input type="text" name="cc_zip" id="zip" placeholder="">
                </div>

                <div class="inpt-dmps-bx">
                    <?php $cardTypes = $settings->getDistinctCardTypes2(); ?>
                    <label for="type">Type</label>
                    <select name="cc_type" id="type">
                        <option value="all">All</option>
                        <?php foreach ($cardTypes as $type): ?>
                        <option value="<?= htmlspecialchars($type) ?>"><?= ucfirst($type) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="inpt-dmps-bx">
                    <label for="basename">Base name</label>
                    <select name="basename" id="basename">
                        <option value="all">All</option>
                        <?php
                        $baseNames = $settings->getCreditCardBaseNames();
                        foreach ($baseNames as $baseName) {
                            if ($baseName['base_name'] != NULL) {
                                echo '<option value="' . htmlspecialchars($baseName['base_name']) . '">'
                                     . htmlspecialchars($baseName['base_name']) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <!-- Refundable Filter -->
                <div class="inpt-dmps-bx">
                    <label for="refundable">Refundable</label>
                    <select name="refundable" id="refundable">
                        <option value="">All</option>
                        <?php foreach ($pdo->query("SELECT DISTINCT refundable FROM cncustomer_records WHERE refundable IS NOT NULL AND refundable != '' AND buyer_id IS NULL") as $row): ?>
                        <option value="<?= htmlspecialchars($row['refundable']) ?>">
                            <?= htmlspecialchars($row['refundable']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>


                </div>
                <!-- End Refundable Filter -->

                <div class="inpt-dmps-bx" style="display: flex; gap: 9px; margin-top: 20px;">
                    <button type="submit" id="search-btn" class="btn btn-with-icon"
                        style="background-color: #0c182f; color: white; padding: 10px 20px; border-radius: 4px; border: none; cursor: pointer;">
                        <i class="fa fa-search"></i>
                        <span class="btn-text" style="margin-left: -7px;">Search</span>
                    </button>
                    <a type="button" id="clear-btn" class="btn btn-with-icon"
                        style="background-color: #f44336; color: white; padding: 10px 20px; border-radius: 4px; border: none; cursor: pointer;">
                        <i class="fa fa-times"></i>
                        <span class="btn-text" style="margin-left: -7px;">Clear</span>
                    </a>
                </div>
            </form>
        </div>

        <?php if (!empty($creditCards)): ?>
        <div class="main-tbl321">
            <div id="customLoader" style="display: none; text-align: center; margin-bottom: 15px;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <table id="creditCardsTable" style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background-color:#0c182f;">
                        <th style="padding: 10px; border: 1px solid #ddd;">Type</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">BIN</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Expiry</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Country</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">State</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">City</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">ZIP</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Other Information</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Base Name</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Refundable</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Price</th>
                        <th style="padding: 10px; border: 1px solid #ddd; width: 250px !important;">Buy</th>
                    </tr>
                </thead>
            </table>
        </div>
        <?php else: ?>
        <p>No credit cards available.</p>
        <?php endif; ?>
    </div>
</div>
</div>

<?php
include_once('../../footer.php');
?>




<script>
function showConfirm(cardId, price) {
    alertify.confirm(
        'Confirm Purchase',
        `Are you sure you want to buy this card for $${price}?`,
        function() {
            $.ajax({
                url: 'buy_card.php',
                type: 'POST',
                data: {
                    calrecord_id: cardId
                },
                success: function(response) {
                    try {
                        const result = typeof response === 'string' ? JSON.parse(response) : response;
                        if (result.success) {
                            showPopupMessage(
                                result.message ||
                                'Purchase successful. Please visit the My Cards section to view your purchased cards.'
                            );
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else {
                            showPopupMessage('error', result.message || 'An error occurred.');
                        }
                    } catch (error) {
                        console.error('JSON parse error:', error);
                        showPopupMessage('error', 'Unexpected server response. Please try again.');
                    }
                },
                error: function() {
                    showPopupMessage('error', 'Transaction failed. Please try again.');
                }
            });
        },
        function() {
            alertify.error('Purchase cancelled.');
        }
    ).set('labels', {
        ok: 'Confirm',
        cancel: 'Cancel'
    });
    return false;
}

function closeRulesPopup() {
    const popup = document.getElementById('rules-popup');
    popup.style.display = 'none';
}

$(document).ready(function() {
    var customLoader = $('#customLoader');
    $('#creditCardsTable').DataTable({
        processing: false,
        serverSide: true,
        searching: false,
        ordering: false,
        ajax: {
            url: '<?= $urlval?>ajax/carddata.php',
            type: 'POST',
            beforeSend: function() {
                customLoader.show();
            },
            complete: function() {
                customLoader.hide();
            },
            error: function() {
                alert('Failed to load data. Please try again.');
            },
            data: function(d) {
                d.cc_bin = $('#credit-card-bin').val();
                d.cc_country = $('#credit-card-country').val();
                d.cc_state = $('#state').val();
                d.cc_city = $('#city').val();
                d.cc_zip = $('#zip').val();
                d.cc_type = $('#type').val();
                d.basename = $('#basename').val();
                d.refundable = $('#refundable').val();
            }
        },
        columns: [{
                data: 'card_logo'
            },
            {
                data: 'creference_code'
            },
            {
                data: 'expiry'
            },
            {
                data: 'country'
            },
            {
                data: 'state'
            },
            {
                data: 'city'
            },
            {
                data: 'zip'
            },
            {
                data: 'otherinfo',
                render: function(data, type, row) {
                    if (String(data).toLowerCase() === 'yes') {
                        // Check each field and assign Yes/No accordingly
                        var phoneVal = row.phone_number ? "Yes" : "No";
                        var emailVal = row.email ? "Yes" : "No";
                        var serialsecurity_hint = row.security_hint ? "Yes" : "No";
                        var dbnsVal = row.date_of_birth ? "Yes" : "No";

                        var popup = '<div class="popup-modal popup-pretty">';
                        popup += '<table style="width:100%; border-collapse:collapse;">';
                        popup += '<tr>';
                        popup +=
                            '<td style="border:1px solid #ccc; padding:5px; text-align:center;"><strong>📞 Phone</strong> ' +
                            phoneVal + '</td>';
                        popup +=
                            '<td style="border:1px solid #ccc; padding:5px; text-align:center;"><strong>📧 Email</strong> ' +
                            emailVal + '</td>';
                        popup +=
                            '<td style="border:1px solid #ccc; padding:5px; text-align:center;"><strong>🔢 security_hint</strong> ' +
                            serialsecurity_hint + '</td>';
                        popup +=
                            '<td style="border:1px solid #ccc; padding:5px; text-align:center;"><strong>📅 DOB</strong> ' +
                            dbnsVal + '</td>';
                        popup += '</tr>';
                        popup += '</table>';
                        popup += '</div>';
                        return 'Yes <span class="info-icon-container">' +
                            '<i class="fas fa-info-circle"></i>' +
                            popup +
                            '</span>';
                    } else {
                        return data;
                    }
                }
            },
            {
                data: 'base_name'
            },
            {
                data: 'refundable'
            },
            {
                data: 'price'
            },
            {
                data: 'actions'
            }
        ]
    });

    // Reload table when filters change
    $('#credit-card-filters select').on('change', function() {
        $('#creditCardsTable').DataTable().ajax.reload();
    });

    // Manual Search
    $('#search-btn').on('click', function(event) {
        event.preventDefault();
        $('#creditCardsTable').DataTable().ajax.reload();
    });

    // Clear Filters
    $('#clear-btn').on('click', function(event) {
        event.preventDefault();
        document.getElementById('credit-card-filters').reset();
        $('#creditCardsTable').DataTable().ajax.reload();
    });
});




function addToCart(cardId) {
    fetch('<?= $urlval?>ajax/addtocart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                cardId: cardId,
                type: 'card'
            }),
        })
        .then(response => {
            console.log('Response:', response); // Check the actual response content
            return response.json();
        })
        .then(data => {
            if (data.success) {
                updateCartSidebar(data.cards, data.dumps, data.total);
                updateCartCount();
                const cartSidebar = document.getElementById('cartSidebar');
                cartSidebar.classList.add('open');
            } else {
                alert('Failed to add to cart.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred.');
        });
}
</script>

<script>
$(document).ready(function() {
    // When mouse enters the info icon container
    $(document).on('mouseenter', '.info-icon-container', function() {
        var $container = $(this);
        var $popup = $container.find('.popup-modal');

        // Show the popup (so we can measure it if needed)
        $popup.css('display', 'block');

        // Position the popup to the right of its container (within the table cell)
        // Height is set to auto to fit content.
        $popup.css({
            position: 'absolute',
            top: '-20px',
            height: 'auto',
            borderRadius: '4px',
            transform: 'scale(0.8)',
            zIndex: 1000,
            backgroundColor: 'transparent'

        });
    }).on('mouseleave', '.info-icon-container', function() {
        $(this).find('.popup-modal').css('display', 'none');
    });
});
</script>
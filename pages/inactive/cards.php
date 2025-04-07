<?php
include_once('../../newuser.php');


?>
<style>
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
    /* Alternating row color */
}

tbody tr:hover {
    background-color: #f1f1f1;
    /* Row hover effect */
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
                    <label for="dumps_per_page">Base name</label>
                    <select name="basename" id="basename">
                        <option value="all">All</option>

                        <?php
        $baseNames = $settings->getCreditCardBaseNames();
             
        foreach ($baseNames as $baseName) {
            if($baseName['base_name'] != NULL){

                echo '<option value="' . htmlspecialchars($baseName['base_name']) . '">' . htmlspecialchars($baseName['base_name']) . '</option>';
            }
        }
        ?>
                    </select>
                </div>

                <!-- 
    <div class="inpt-dmps-bx" style="display: flex; gap: 9px; margin-top: 20px;">
        <button type="submit" id="search-btn" class="btn btn-with-icon" style="background-color: #0c182f; color: white; padding: 10px 20px; border-radius: 4px; border: none; cursor: pointer;">
            <i class="fa fa-search"></i>
            <span class="btn-text">Search</span>
        </button>
        <a type="button" id="clear-btn" class="btn btn-with-icon" style="background-color: #f44336; color: white; padding: 10px 20px; border-radius: 4px; border: none; cursor: pointer;">
            <i class="fa fa-times"></i>
            <span class="btn-text">Clear</span>
        </a>
    </div> -->
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



        <?php
// var_dump($creditCards);
?>
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
                        <!-- <th style="padding: 10px; border: 1px solid #ddd;">MNN</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Account Number</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Sort Code</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Cardholder Name</th> -->
                        <th style="padding: 10px; border: 1px solid #ddd;">ZIP</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Price</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Other Information</th>
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
                    card_id: cardId
                },
                success: function(response) {
                    try {
                        const result = typeof response === 'string' ? JSON.parse(response) : response;

                        if (result.success) {
                            showPopupMessage(result.message ||
                                'Purchase Successful": "Purchase successful. Please visit the My Cards section view your purchased cards.'
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
    var table = $('#creditCardsTable').DataTable({
        processing: false,
        serverSide: false,
        searching: false,
        paging: true, // Enable paging
        pageLength: 10, // Display 10 rows per page
        info: false,
        ordering: false,
        ajax: {
            url: '<?= $urlval ?>ajax/carddata.php',
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
                d.dumps_per_page = $('#dumps_per_page').val();
            }
        },
        columns: [{
                data: 'card_logo'
            },
            {
                data: 'card_number'
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
                data: 'price'
            },
            {
                data: 'otherinfo'
            },
            {
                data: null,
                render: function(data, type, row, meta) {
                    return 'Not Allowed';
                }
            }
        ],
        drawCallback: function(settings) {
            // Disable the "Next" button
            $('.paginate_button.next').addClass('disabled');
        }
    });

    // Disable Type, Base Name, and Country filters
    $('#type, #basename, #credit-card-country').prop('disabled', true);

    // Redirect on clicking disabled filters
    $('#type, #basename, #credit-card-country').on('click', function(e) {
        e.preventDefault();
        handleInactiveAccount();
    });

    // Intercept "Show entries" dropdown click
    $('div.dataTables_length select').on('click', function(e) {
        e.preventDefault();
        handleInactiveAccount();
    });

    // Intercept the "Next" button click
    $(document).on('click', '.paginate_button.next', function(e) {
        e.preventDefault();
        handleInactiveAccount();
    });

    // Redirect on clicking other filters
    $('#credit-card-filters select:not(#type, #basename, #credit-card-country), #credit-card-filters input, #search-btn, #clear-btn')
        .on('click', function(e) {
            e.preventDefault();
            handleInactiveAccount();
        });

    // Function to handle inactive account scenario
    function handleInactiveAccount() {
        alert("Your account is inactive. You need to top up some balance.");
        // Redirect to the specific page
        window.location.href =
        '<?= $urlval?>pages/inactive/index.php'; // Change this to your desired destination
    }
});
</script>
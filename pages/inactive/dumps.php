<?php
include_once('../../newuser.php');
// var_dump($_SESSION['cards']);
// var_dump($_SESSION['dumps']);
// exit();
?>
<style>
.dumps-table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    font-size: 16px;
    text-align: left;
    background-color: white;
}

.dumps-table thead {
    background-color: #0c182f;
    color: #ffffff;
}

.dumps-table th,
.dumps-table td {
    padding: 10px;
    border: 1px solid #ddd;
}

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

a.buy-button {
    height: 38px !important;
    width: 100px !important;
    text-align: center !important;
}

a.buy-button:hover {
    background-color: #218838;
    cursor: pointer;
}



.card-logo {
    width: 45px;
    height: auto;
    vertical-align: middle;
}

.filter-container-dumps {
    display: inline-block !important;
    width: auto !important;
    margin-top: 20px;
    border-radius: 0px !important;
    box-shadow: none !important;
}

form#dump-filters {
    display: flex !important;
    align-items: center !important;
    gap: 20px !important;
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

    form#dump-filters {
        display: block !important;
    }
}

.buy-button-dump {
    height: 38px !important;
    width: 100px !important;
    text-align: center !important;
}

.buy-button-dump .buy-now {
    display: block;
}

.buy-button-dump:hover .buy-now {
    display: none;
}

.buy-button-dump .price {
    display: none;
}

.buy-button-dump:hover .price {
    display: block;
}
</style>
<!-- Main Content Area -->
<div class="main-content">
    <div id="dumps" class="uuper">
        <h2>Dumps Section</h2>
        <div class="filter-container-dumps">
            <form id="dump-filters" method="post" action="#dumps">
                <div class="inpt-dmps-bx">
                    <label for="dump-bin">BIN</label>
                    <input type="text" name="dump_bin" id="dump-bin"
                        placeholder="Comma-separated for multiple - e.g., 123456, 654321">
                </div>

                <div class="inpt-dmps-bx">
                    <label for="dump-country">Country</label>
                    <select name="dump_country" id="dump-country">
                        <option value="">All</option>
                        <?php foreach ($dumpCountries as $country): ?>
                        <option value="<?php echo htmlspecialchars($country); ?>">
                            <?php echo htmlspecialchars($country); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="inpt-dmps-bx">

                    <?php $cardTypes = $settings->getDistinctCardTypes(); ?>
                    <label for="dump-type">Type</label>
                    <select name="dump_type" id="dump-type">
                        <option value="all">All</option>
                        <?php foreach ($cardTypes as $type): ?>
                        <option value="<?= htmlspecialchars($type) ?>"><?= ucfirst($type) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="inpt-dmps-bx">
                    <label for="dump-pin">PIN</label>
                    <select name="dump_pin" id="dump-pin">
                        <option value="all">All</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </select>
                </div>
                <div class="inpt-dmps-bx">
                    <label for="track-pin">Track 1</label>
                    <select name="track-pin" id="track-pin">
                        <option value="all">All</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </select>
                </div>

                <div class="inpt-dmps-bx">
                    <label for="base_name">Code</label>
                    <select name="code" id="code">
                        <option value="all">All</option>

                        <?php
                        $codes = $settings->getDumpCode();

                        foreach ($codes as $code) {
                            if ($code['code'] != NULL) {
                                // Correct placement of substr() inside the echo statement
                                echo '<option value="' . htmlspecialchars($code['code']) . '">' . substr($code['code'], 0, 3) . '</option>';
                            }
                        }
                        
                        ?>
                    </select>
                </div>
                <div class="inpt-dmps-bx">
                    <label for="base_name"> Base name</label>
                    <select name="base_name" id="base_name">
                        <option value="all">All</option>

                        <?php
                        $getDumpCodes = $settings->getDumpBaseNames();
                        foreach ($getDumpCodes as $getDumpCode) {
                        
                            if (isset($getDumpCode['base_name']) && $getDumpCode['base_name'] != 'NA') {
                                echo '<option value="' . htmlspecialchars($getDumpCode['base_name']) . '">' . htmlspecialchars($getDumpCode['base_name']) . '</option>';
                            }

                        }
                        ?>
                    </select>
                </div>


                <!-- <div class="inpt-dmps-bx" style="display: flex; gap: 9px; margin-top: 20px;">
        <button type="submit" id="search-btn" class="btn btn-with-icon" style="background-color: #0c182f; color: white; padding: 10px 20px; border-radius: 4px; border: none; cursor: pointer;">
            <i class="fa fa-search"></i>
            <span class="btn-text">Search</span>
        </button>
        <a type="button" id="clear-btn" class="btn btn-with-icon" style="background-color: #f44336; color: white; padding: 10px 20px; border-radius: 4px; border: none; cursor: pointer;">
            <i class="fa fa-times"></i>
            <span class="btn-text">Clear</span>
        </a>
    </div>  -->
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


        <div id="dumps-list" class="main-tbl321">
            <?php if (!empty($dumps)): ?>
            <div id="customLoader" style="display: none; text-align: center; margin-bottom: 15px;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <table id="dumpsTable" class="dumps-table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>BIN</th>
                        <th>Exp Date </th>
                        <th>Code</th>
                        <th>PIN</th>
                        <th>Track 1</th>
                        <th>Country</th>
                        <th>Price</th>
                        <th style="width: 250px !important;">Buy</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <?php else: ?>
            <p>No dumps available.</p>
            <?php endif; ?>

        </div>
    </div>

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


<?php
include_once('../../footer.php');
?>
<script type="text/javascript">
$(document).ready(function() {
    var customLoader = $('#customLoader');
    var table = $('#dumpsTable').DataTable({
        processing: false,
        serverSide: false,
        paging: true, // Enable paging
        pageLength: 10, // Limit to 10 rows per page
        info: false,
        searching: false,
        ordering: false,
        ajax: {
            url: '<?= $urlval ?>ajax/dumpdata.php',
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
                d.dump_bin = $('#dump-bin').val();
                d.dump_country = $('#dump-country').val();
                d.dump_type = $('#dump-type').val();
                d.dump_code = $('#dump-code').val();
                d.dump_pin = $('#dump-pin').val();
                d.base_name = $('#base_name').val();
                d.code = $('#code').val();
                d.track_pin = $('#track-pin').val();
            }
        },
        columns: [{
                data: 'card_logo'
            },
            {
                data: 'track2'
            },
            {
                data: 'expiry'
            },
            {
                data: 'code'
            },
            {
                data: 'pin'
            },
            {
                data: 'track'
            },
            {
                data: 'country'
            },
            {
                data: 'price'
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

    // Disable specific filters (Type, Base Name, Country, PIN, Track 1, Code)
    $('#dump-type, #base_name, #dump-country, #dump-pin, #track-pin, #code').prop('disabled', true);

    // Redirect on clicking disabled filters
    $('#dump-type, #base_name, #dump-country, #dump-pin, #track-pin, #code').on('click', function(e) {
        e.preventDefault();
        handleInactiveAccount();
    });

    // Redirect on clicking "Show entries" dropdown
    $('div.dataTables_length select').on('click', function(e) {
        e.preventDefault();
        handleInactiveAccount();
    });

    // Redirect on clicking other filter options and buttons
    $('#dump-filters select:not(#dump-type, #base_name, #dump-country, #dump-pin, #track-pin, #code), #dump-filters input, #search-btn, #clear-btn')
        .on('click', function(e) {
            e.preventDefault();
            handleInactiveAccount();
        });

    // Intercept the "Next" button click
    $(document).on('click', '.paginate_button.next', function(e) {
        e.preventDefault();
        handleInactiveAccount();
    });

    // Function to handle inactive account scenario
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
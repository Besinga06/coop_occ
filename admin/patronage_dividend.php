<?php
require('includes/header.php');
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// -------------------------
// Fetch Members and Totals
// -------------------------
$members = $db->query("
    SELECT 
        c.cust_id, 
        c.name,
        IFNULL((
            SELECT SUM(CAST(amount AS REAL)) 
            FROM tbl_capital_share cs 
            WHERE cs.cust_id = c.cust_id 
              AND strftime('%Y', cs.contribution_date) = '$year'
        ),0) AS total_share,
        (
            IFNULL((
                SELECT SUM(CAST(total_amount AS REAL)) 
                FROM tbl_sales s 
                WHERE s.cust_id = c.cust_id 
                  AND s.sales_type = 1
                  AND strftime('%Y', s.sales_date) = '$year'
            ),0)
            +
            IFNULL((
                SELECT SUM(CAST(p.amount_paid AS REAL))
                FROM tbl_payments p
                WHERE p.sales_no IN (
                    SELECT s2.sales_no FROM tbl_sales s2
                    WHERE s2.cust_id = c.cust_id 
                      AND s2.sales_type = 0
                      AND strftime('%Y', s2.sales_date) = '$year'
                )
            ),0)
        ) AS total_purchase
    FROM tbl_customer c 
    WHERE c.cust_id != 1
    ORDER BY c.name ASC
");

// -------------------------
// Overall Totals
// -------------------------
$overall = $db->query("
    SELECT 
        IFNULL((SELECT SUM(CAST(amount AS REAL))
                FROM tbl_capital_share
                WHERE strftime('%Y', contribution_date) = '$year'
                  AND cust_id != 1),0) AS overall_share,
        (
            IFNULL((SELECT SUM(CAST(total_amount AS REAL)) 
                    FROM tbl_sales s 
                    JOIN tbl_customer c ON s.cust_id = c.cust_id
                    WHERE s.sales_type = 1
                      AND strftime('%Y', s.sales_date) = '$year'
                      AND c.cust_id != 1),0)
            +
            IFNULL((SELECT SUM(CAST(p.amount_paid AS REAL))
                    FROM tbl_payments p
                    WHERE p.sales_no IN (
                        SELECT s2.sales_no FROM tbl_sales s2 
                        JOIN tbl_customer c2 ON s2.cust_id = c2.cust_id
                        WHERE s2.sales_type = 0
                          AND strftime('%Y', s2.sales_date) = '$year'
                          AND c2.cust_id != 1
                    )),0)
        ) AS overall_purchase
")->fetchArray(SQLITE3_ASSOC);

$overallShare = $overall['overall_share'];
$overallPurchase = $overall['overall_purchase'];

$history = $db->query("
    SELECT 
        dc.id,
        dc.year,
        dc.dividend_amount,
        dc.patronage_amount,
        COUNT(dr.id) AS total_members,
        IFNULL(SUM(dr.dividend), 0) AS total_dividend,
        IFNULL(SUM(dr.patronage), 0) AS total_patronage,
        IFNULL(SUM(dr.total_benefit), 0) AS total_benefit,
        -- Overall capital shares for the year
        IFNULL((SELECT SUM(CAST(amount AS REAL)) 
                FROM tbl_capital_share 
                WHERE strftime('%Y', contribution_date) = dc.year
                  AND cust_id != 1), 0) AS overall_share,
        -- Overall total purchases for the year
        (
            IFNULL((SELECT SUM(CAST(total_amount AS REAL)) 
                    FROM tbl_sales s 
                    JOIN tbl_customer c ON s.cust_id = c.cust_id
                    WHERE s.sales_type = 1
                      AND strftime('%Y', s.sales_date) = dc.year
                      AND c.cust_id != 1),0)
            +
            IFNULL((SELECT SUM(CAST(p.amount_paid AS REAL))
                    FROM tbl_payments p
                    WHERE p.sales_no IN (
                        SELECT s2.sales_no FROM tbl_sales s2 
                        JOIN tbl_customer c2 ON s2.cust_id = c2.cust_id
                        WHERE s2.sales_type = 0
                          AND strftime('%Y', s2.sales_date) = dc.year
                          AND c2.cust_id != 1
                    )),0)
        ) AS overall_purchase
    FROM distribution_cycles dc
    LEFT JOIN distribution_records dr ON dr.cycle_id = dc.id
    GROUP BY dc.id, dc.year, dc.dividend_amount, dc.patronage_amount
    ORDER BY dc.year DESC
");

?>
<style>
    .navbar-brand {
        display: flex;
        align-items: center;
        /* vertically center image + text */
        gap: 0px;
        /* space between logo and text */
        font-weight: 800;
        color: white;
        /* adjust to your navbar color */
        text-decoration: none;
        font-size: 50px;
    }

    .navbar-brand img {
        height: 40px;
        /* adjust logo height */
        width: auto;
        object-fit: contain;
    }

    .navbar-brand span {
        white-space: nowrap;
        /* prevent text from wrapping to next line */
    }
</style>

<body class="layout-boxed navbar-top">
    <!-- Main navbar -->
    <div class="navbar navbar-inverse bg-teal-400 navbar-fixed-top">
        <div class="navbar-header">
            <a class="navbar-brand" href="index.php"><img style="height: 40px!important" src="../images/farmers-logo.png" alt=""><span>Lourdes Farmers Multi-Purpose Cooperative</span></a>
            <ul class="nav navbar-nav visible-xs-block">
                <li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
            </ul>
        </div>
        <div class="navbar-collapse collapse" id="navbar-mobile">
            <?php require('includes/sidebar.php'); ?>
        </div>
    </div>

    <div class="page-container">
        <div class="page-content">
            <div class="content-wrapper">

                <div class="page-header page-header-default">
                    <div class="page-header-content">
                        <div class="page-title">
                            <h4><i class="icon-arrow-left52 position-left"></i>
                                <span class="text-semibold">Members' Financial </span> - Patronage & Dividend
                            </h4>
                        </div>
                    </div>
                    <div class="breadcrumb-line">
                        <ul class="breadcrumb">
                            <li><a href="dividends.php"><i class="icon-coins"></i> Members' Financial</a></li>
                            <li class="active"><i class="icon-stats-bars2 position-left"></i> Patronage & Dividend</li>
                        </ul>
                        <ul class="breadcrumb-elements">
                            <li>
                                <a href="distribution_disbursement.php">
                                    <i class="icon-stats-bars2 text-orange-400"></i> Benefit Disbursement
                                </a>

                            </li>
                        </ul>
                    </div>
                </div>

                <div class="content">

                    <!-- Totals Summary -->
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="panel bg-success-400">
                                <div class="panel-body">
                                    <h3 class="no-margin">₱ <?= number_format($overallShare, 2); ?></h3>
                                    Total Capital Shares
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="panel bg-primary-400">
                                <div class="panel-body">
                                    <h3 class="no-margin">₱ <?= number_format($overallPurchase, 2); ?></h3>
                                    Total Member Purchases
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Input Section -->
                    <div class="panel panel-white border-top-xlg border-top-teal">
                        <div class="panel-heading">
                            <h6 class="panel-title">
                                <i class="icon-coin-dollar text-teal-400 position-left"></i> Enter Pools for Distribution
                            </h6>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Year</label>
                                    <input type="number" id="year" name="year" class="form-control" value="<?= $year ?>" min="2000" max="<?= date('Y') + 1 ?>">
                                </div>
                                <div class="col-md-3">
                                    <label>Dividend Pool (₱)</label>
                                    <input type="number" step="0.01" min="0" id="dividend_pool" name="dividend_pool" class="form-control" placeholder="Enter amount...">
                                </div>
                                <div class="col-md-3">
                                    <label>Patronage Pool (₱)</label>
                                    <input type="number" step="0.01" min="0" id="patronage_pool" name="patronage_pool" class="form-control" placeholder="Enter amount...">
                                </div>
                                <div class="col-md-3">
                                    <label>&nbsp;</label><br>
                                    <button id="btn-calc" class="btn bg-teal-400 btn-labeled">
                                        <b><i class="icon-calculator4"></i></b> Calculate
                                    </button>
                                    <button id="btn-save" class="btn bg-warning-400 btn-labeled">
                                        <b><i class="icon-floppy-disk"></i></b> Save
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>


                    <!-- Members Financial Table -->
                    <div class="panel panel-white border-top-xlg border-top-warning">
                        <div class="panel-heading">
                            <h6 class="panel-title"><i class="icon-stats-bars2 text-warning position-left"></i> Members Financial Breakdown</h6>
                        </div>
                        <div class="panel-body panel-theme">
                            <table class="table table-bordered table-hover" id="member-table">
                                <thead>
                                    <tr>
                                        <th>Member</th>
                                        <th>Capital Share</th>
                                        <th>Total Purchases</th>
                                        <th>Dividend</th>
                                        <th>Patronage</th>
                                        <th>Total Benefit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $members->fetchArray(SQLITE3_ASSOC)) { ?>
                                        <tr data-id="<?= $row['cust_id']; ?>" data-share="<?= $row['total_share']; ?>" data-purchase="<?= $row['total_purchase']; ?>">
                                            <td><?= htmlspecialchars($row['name']); ?></td>
                                            <td class="text-right">₱ <?= number_format($row['total_share'], 2); ?></td>
                                            <td class="text-right">₱ <?= number_format($row['total_purchase'], 2); ?></td>
                                            <td class="dividend text-right" title="Dividend = Capital Share ÷ Total Shares × Dividend Pool">₱ 0.00 <i class="icon-info22 text-info"></i></td>
                                            <td class="patronage text-right" title="Patronage = Total Purchases ÷ Overall Purchases × Patronage Pool">₱ 0.00 <i class="icon-info22 text-info"></i></td>
                                            <td class="total-benefit text-right" title="Total Benefit = Dividend + Patronage">₱ 0.00 <i class="icon-info22 text-info"></i></td>
                                        </tr>
                                    <?php } ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Distribution History Table -->
                    <div class="panel panel-white border-top-xlg border-top-indigo-400">
                        <div class="panel-heading">
                            <h6 class="panel-title"><i class="icon-history text-indigo-400 position-left"></i> Distribution History</h6>
                        </div>
                        <div class="panel-body panel-theme">
                            <table class="table table-bordered table-hover" id="history-table">
                                <thead>
                                    <tr>
                                        <th>Year</th>
                                        <th>Total Members</th>
                                        <th>Overall Capital Share (₱)</th>
                                        <th>Overall Purchases (₱)</th>
                                        <th>Dividend Pool (₱)</th>
                                        <th>Patronage Pool (₱)</th>
                                        <th>Total Dividend (₱)</th>
                                        <th>Total Patronage (₱)</th>
                                        <th>Total Benefit (₱)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $history->fetchArray(SQLITE3_ASSOC)) { ?>
                                        <tr>
                                            <td><?= $row['year'] ?></td>
                                            <td class="text-center"><?= $row['total_members'] ?></td>
                                            <td class="text-right">₱ <?= number_format($row['overall_share'], 2) ?></td>
                                            <td class="text-right">₱ <?= number_format($row['overall_purchase'], 2) ?></td>
                                            <td class="text-right">₱ <?= number_format($row['dividend_amount'], 2) ?></td>
                                            <td class="text-right">₱ <?= number_format($row['patronage_amount'], 2) ?></td>
                                            <td class="text-right">₱ <?= number_format($row['total_dividend'], 2) ?></td>
                                            <td class="text-right">₱ <?= number_format($row['total_patronage'], 2) ?></td>
                                            <td class="text-right">₱ <?= number_format($row['total_benefit'], 2) ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>


                </div>
                <?php require('includes/footer-text.php'); ?>
            </div>
        </div>
    </div>

    <?php require('includes/footer.php'); ?>

    <!-- Confirm Save Modal -->
    <div id="confirmSaveModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content border-teal-400">
                <div class="modal-header bg-teal-400 text-white">
                    <h5 class="modal-title">Confirm Save</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <p>Are you sure you want to save the distribution for year <span id="modalYear"></span>? This action cannot be undone.</p>
                <div class="modal-footer">
                    <button type="button" id="modalCancel" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" id="modalConfirm" class="btn bg-teal-400">Yes, Save</button>
                </div>
            </div>
        </div>
    </div>



    <!-- DataTables JS & jGrowl -->
    <script src="../assets/js/plugins/tables/datatables/datatables.min.js"></script>
    <script src="../assets/js/plugins/notifications/jgrowl.min.js"></script>
    <script src="../js/validator.min.js"></script>

    <script>
        $(function() {
            const overallShare = <?= $overallShare ?>;
            const overallPurchase = <?= $overallPurchase ?>;

            // Initialize DataTable
            $('#member-table').DataTable({
                "order": [
                    [0, "asc"]
                ],
                "lengthMenu": [
                    [10, 25, 50],
                    [10, 25, 50]
                ]
            });

            // Initialize DataTable for Distribution History
            $('#history-table').DataTable({
                "order": [
                    [0, "desc"]
                ],
                "lengthMenu": [
                    [10, 25, 50],
                    [10, 25, 50]
                ]
            });

            function recalc() {
                const divPool = parseFloat($('#dividend_pool').val()) || 0;
                const patPool = parseFloat($('#patronage_pool').val()) || 0;
                const divRate = overallShare > 0 ? divPool / overallShare : 0;
                const patRate = overallPurchase > 0 ? patPool / overallPurchase : 0;

                $('#member-table tbody tr').each(function() {
                    const share = parseFloat($(this).data('share')) || 0;
                    const purchase = parseFloat($(this).data('purchase')) || 0;
                    const dividend = share * divRate;
                    const patronage = purchase * patRate;
                    const total = dividend + patronage;

                    $(this).find('.dividend').text('₱ ' + dividend.toFixed(2));
                    $(this).find('.patronage').text('₱ ' + patronage.toFixed(2));
                    $(this).find('.total-benefit').text('₱ ' + total.toFixed(2));

                    // Update tooltip dynamically
                    $(this).find('.dividend').attr('title', `Dividend = ${share} ÷ ${overallShare} × ${divPool} = ${dividend.toFixed(2)}`);
                    $(this).find('.patronage').attr('title', `Patronage = ${purchase} ÷ ${overallPurchase} × ${patPool} = ${patronage.toFixed(2)}`);
                    $(this).find('.total-benefit').attr('title', `Total Benefit = ${dividend.toFixed(2)} + ${patronage.toFixed(2)} = ${total.toFixed(2)}`);
                });

                // Re-enable Bootstrap tooltips
                $('[data-toggle="tooltip"]').tooltip();
            }

            $('#btn-calc').on('click', function(e) {
                e.preventDefault();
                recalc();
            });

            let saveData = null; // store data temporarily

            $('#btn-save').on('click', function(e) {
                e.preventDefault();
                const divPool = parseFloat($('#dividend_pool').val()) || 0;
                const patPool = parseFloat($('#patronage_pool').val()) || 0;
                const year = $('#year').val();

                if (divPool <= 0 && patPool <= 0) {
                    $.jGrowl('Please enter valid pool amounts before saving.', {
                        header: 'Warning Notification',
                        theme: 'alert-styled-right bg-warning'
                    });
                    return;
                }

                // Prepare members data
                let members = [];
                $('#member-table tbody tr').each(function() {
                    const id = $(this).data('id');
                    const share = parseFloat($(this).data('share')) || 0;
                    const purchase = parseFloat($(this).data('purchase')) || 0;
                    const dividend = overallShare > 0 ? (share / overallShare) * divPool : 0;
                    const patronage = overallPurchase > 0 ? (purchase / overallPurchase) * patPool : 0;
                    const total = dividend + patronage;
                    members.push({
                        id,
                        share,
                        purchase,
                        dividend,
                        patronage,
                        total
                    });
                });

                // Store data temporarily
                saveData = {
                    year,
                    dividend_amount: divPool,
                    patronage_amount: patPool,
                    members
                };

                // Set modal text and show
                $('#modalYear').text(year);
                $('#confirmSaveModal').modal('show');
            });

            // Handle modal confirm
            $('#modalConfirm').on('click', function() {
                if (!saveData) return;

                $.ajax({
                    type: 'POST',
                    url: '../transaction.php',
                    data: {
                        action: 'save_distribution',
                        year: saveData.year,
                        dividend_amount: saveData.dividend_amount,
                        patronage_amount: saveData.patronage_amount,
                        members: JSON.stringify(saveData.members)
                    },
                    success: function(resp) {
                        $('#confirmSaveModal').modal('hide');

                        if (resp.trim() === "1") {
                            // Success notification
                            $.jGrowl('Distribution and Member Benefits saved successfully!', {
                                header: 'Success Notification',
                                theme: 'alert-styled-right bg-success'
                            });

                            // Mark rows as saved and make bold
                            $('#member-table tbody tr').each(function() {
                                $(this).addClass('saved'); // optional CSS class
                                $(this).find('td.dividend, td.patronage, td.total-benefit').css('font-weight', 'bold');
                            });

                            // Disable calculate/save buttons to avoid duplicates
                            $('#btn-calc').prop('disabled', true);
                            $('#btn-save').prop('disabled', true);

                        } else {
                            let msg = resp.includes('|') ? resp.split('|')[1] : resp;
                            $.jGrowl(msg, {
                                header: 'Error Notification',
                                theme: 'alert-styled-right bg-danger'
                            });
                        }
                    },
                    error: function() {
                        $('#confirmSaveModal').modal('hide');
                        $.jGrowl('Error connecting to server.', {
                            header: 'Error Notification',
                            theme: 'alert-styled-right bg-danger'
                        });
                    }
                });
            });

        });
    </script>
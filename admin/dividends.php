<?php
require('includes/header.php');

// Get current year
$currentYear = date('Y');

// -------------------------
// Capital Share Breakdown per Member (Current Year Only)
// -------------------------
$shares = $db->query("
    SELECT 
        c.cust_id,
        c.name,
        SUM(CASE WHEN strftime('%m', cs.contribution_date) = '01' THEN cs.amount ELSE 0 END) AS Jan,
        SUM(CASE WHEN strftime('%m', cs.contribution_date) = '02' THEN cs.amount ELSE 0 END) AS Feb,
        SUM(CASE WHEN strftime('%m', cs.contribution_date) = '03' THEN cs.amount ELSE 0 END) AS Mar,
        SUM(CASE WHEN strftime('%m', cs.contribution_date) = '04' THEN cs.amount ELSE 0 END) AS Apr,
        SUM(CASE WHEN strftime('%m', cs.contribution_date) = '05' THEN cs.amount ELSE 0 END) AS May,
        SUM(CASE WHEN strftime('%m', cs.contribution_date) = '06' THEN cs.amount ELSE 0 END) AS Jun,
        SUM(CASE WHEN strftime('%m', cs.contribution_date) = '07' THEN cs.amount ELSE 0 END) AS Jul,
        SUM(CASE WHEN strftime('%m', cs.contribution_date) = '08' THEN cs.amount ELSE 0 END) AS Aug,
        SUM(CASE WHEN strftime('%m', cs.contribution_date) = '09' THEN cs.amount ELSE 0 END) AS Sep,
        SUM(CASE WHEN strftime('%m', cs.contribution_date) = '10' THEN cs.amount ELSE 0 END) AS Oct,
        SUM(CASE WHEN strftime('%m', cs.contribution_date) = '11' THEN cs.amount ELSE 0 END) AS Nov,
        SUM(CASE WHEN strftime('%m', cs.contribution_date) = '12' THEN cs.amount ELSE 0 END) AS `Dec`,
        SUM(cs.amount) AS Total
    FROM tbl_customer c
    LEFT JOIN tbl_capital_share cs ON cs.cust_id = c.cust_id
    WHERE c.cust_id != 1
      AND strftime('%Y', cs.contribution_date) = '$currentYear'
    GROUP BY c.cust_id
    ORDER BY c.name ASC
");

// -------------------------
// Overall Totals (All Members) - Current Year
// -------------------------
$overall = $db->query("
    SELECT 
        SUM(CASE WHEN strftime('%m', cs.contribution_date) = '01' THEN cs.amount ELSE 0 END) AS Jan,
        SUM(CASE WHEN strftime('%m', cs.contribution_date) = '02' THEN cs.amount ELSE 0 END) AS Feb,
        SUM(CASE WHEN strftime('%m', cs.contribution_date) = '03' THEN cs.amount ELSE 0 END) AS Mar,
        SUM(CASE WHEN strftime('%m', cs.contribution_date) = '04' THEN cs.amount ELSE 0 END) AS Apr,
        SUM(CASE WHEN strftime('%m', cs.contribution_date) = '05' THEN cs.amount ELSE 0 END) AS May,
        SUM(CASE WHEN strftime('%m', cs.contribution_date) = '06' THEN cs.amount ELSE 0 END) AS Jun,
        SUM(CASE WHEN strftime('%m', cs.contribution_date) = '07' THEN cs.amount ELSE 0 END) AS Jul,
        SUM(CASE WHEN strftime('%m', cs.contribution_date) = '08' THEN cs.amount ELSE 0 END) AS Aug,
        SUM(CASE WHEN strftime('%m', cs.contribution_date) = '09' THEN cs.amount ELSE 0 END) AS Sep,
        SUM(CASE WHEN strftime('%m', cs.contribution_date) = '10' THEN cs.amount ELSE 0 END) AS Oct,
        SUM(CASE WHEN strftime('%m', cs.contribution_date) = '11' THEN cs.amount ELSE 0 END) AS Nov,
        SUM(CASE WHEN strftime('%m', cs.contribution_date) = '12' THEN cs.amount ELSE 0 END) AS `Dec`,
        SUM(cs.amount) AS Total
    FROM tbl_capital_share cs
    WHERE cs.cust_id != 1
      AND strftime('%Y', cs.contribution_date) = '$currentYear'
")->fetchArray(SQLITE3_ASSOC);

    // -------------------------
    // Members Purchases Breakdown (Current Year Only)
    // -------------------------
    $purchases = $db->query("
        SELECT 
            c.cust_id,
            c.name,
            COALESCE(SUM(CASE WHEN s.sales_type = 1 THEN s.total_amount ELSE 0 END), 0) AS total_cash_sales,
            COALESCE((
                SELECT SUM(p.amount_paid)
                FROM tbl_payments p
                WHERE p.sales_no IN (
                    SELECT s2.sales_no FROM tbl_sales s2 
                    WHERE s2.cust_id = c.cust_id AND s2.sales_type = 0
                    AND strftime('%Y', s2.sales_date) = '$currentYear'
                )
            ), 0) AS total_paid_charge,
            COALESCE(SUM(CASE WHEN s.sales_type = 1 THEN s.total_amount ELSE 0 END), 0)
            + COALESCE((
                SELECT SUM(p.amount_paid)
                FROM tbl_payments p
                WHERE p.sales_no IN (
                    SELECT s2.sales_no FROM tbl_sales s2 
                    WHERE s2.cust_id = c.cust_id AND s2.sales_type = 0
                    AND strftime('%Y', s2.sales_date) = '$currentYear'
                )
            ), 0) AS total_purchase
        FROM tbl_customer c
        LEFT JOIN tbl_sales s ON c.cust_id = s.cust_id
        WHERE c.cust_id != 1
        AND strftime('%Y', s.sales_date) = '$currentYear'
        GROUP BY c.cust_id
        ORDER BY c.name ASC
    ");

    // -------------------------
    // Overall Totals (All Members) - Purchases (Current Year)
    // -------------------------
    $overall_purchase = $db->query("
        SELECT 
            COALESCE(SUM(CASE WHEN s.sales_type = 1 THEN s.total_amount ELSE 0 END), 0)
            + COALESCE((
                SELECT SUM(p.amount_paid)
                FROM tbl_payments p
                WHERE p.sales_no IN (
                    SELECT s2.sales_no FROM tbl_sales s2
                    WHERE s2.sales_type = 0
                    AND strftime('%Y', s2.sales_date) = '$currentYear'
                )
            ), 0) AS total_purchase
        FROM tbl_sales s
        JOIN tbl_customer c ON s.cust_id = c.cust_id
        WHERE c.cust_id != 1
        AND strftime('%Y', s.sales_date) = '$currentYear'
    ")->fetchArray(SQLITE3_ASSOC);

    // Load members for Add Contribution Modal
    $members = $db->query("SELECT cust_id, name FROM tbl_customer WHERE cust_id != 1 ORDER BY name ASC");
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
    <!-- /Navbar -->

    <div class="page-container">
        <div class="page-content">
            <div class="content-wrapper">

                <!-- Page header -->
                <div class="page-header page-header-default">
                    <div class="page-header-content">
                        <div class="page-title">
                            <h4>
                                <span class="text-semibold"></span>Members' Financial (<?= $currentYear ?>)
                            </h4>
                        </div>
                    </div>
                    <div class="breadcrumb-line">
                        <ul class="breadcrumb">
                            <li class="active"><i class="icon-cash3 position-left"></i> Members' Financial</li>
                        </ul>
                        <ul class="breadcrumb-elements">
                            <li>
                                <a href="javascript:;" data-toggle="modal" data-target="#modal_share">
                                    <i class="icon-coin-dollar text-teal-400"></i> Add Capital Share
                                </a>
                            </li>
                            <li>
                                <a href="patronage_dividend.php">
                                    <i class="icon-stats-bars2 text-orange-400"></i> Patronage & Dividend
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- /Page header -->

                <div class="content">

                    <!-- Summary Cards -->
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="panel bg-success-400">
                                <div class="panel-body">
                                    <h3 class="no-margin">₱ <?= number_format($overall['Total'], 2); ?></h3>
                                    Total Capital Shares (<?= $currentYear ?>)
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="panel bg-primary-400">
                                <div class="panel-body">
                                    <h3 class="no-margin">₱ <?= number_format($overall_purchase['total_purchase'], 2); ?></h3>
                                    Total Member Purchases (<?= $currentYear ?>)
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Summary Cards -->

                    <!-- Capital Share Breakdown Table -->
                    <div class="panel panel-white border-top-xlg border-top-success">
                        <div class="panel-heading">
                            <h6 class="panel-title">
                                <i class="icon-cash3 text-success position-left"></i>Capital Share Contributions by Month (<?= $currentYear ?>)
                            </h6>
                        </div>
                        <div class="panel-body panel-theme">

                            <table class="table datatable-button-html5-basic table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>Member</th>
                                        <th>Jan</th>
                                        <th>Feb</th>
                                        <th>Mar</th>
                                        <th>Apr</th>
                                        <th>May</th>
                                        <th>Jun</th>
                                        <th>Jul</th>
                                        <th>Aug</th>
                                        <th>Sep</th>
                                        <th>Oct</th>
                                        <th>Nov</th>
                                        <th>Dec</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $shares->fetchArray(SQLITE3_ASSOC)) { ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['name']); ?></td>
                                            <td style="text-align:right"><?= number_format($row['Jan'], 2); ?></td>
                                            <td style="text-align:right"><?= number_format($row['Feb'], 2); ?></td>
                                            <td style="text-align:right"><?= number_format($row['Mar'], 2); ?></td>
                                            <td style="text-align:right"><?= number_format($row['Apr'], 2); ?></td>
                                            <td style="text-align:right"><?= number_format($row['May'], 2); ?></td>
                                            <td style="text-align:right"><?= number_format($row['Jun'], 2); ?></td>
                                            <td style="text-align:right"><?= number_format($row['Jul'], 2); ?></td>
                                            <td style="text-align:right"><?= number_format($row['Aug'], 2); ?></td>
                                            <td style="text-align:right"><?= number_format($row['Sep'], 2); ?></td>
                                            <td style="text-align:right"><?= number_format($row['Oct'], 2); ?></td>
                                            <td style="text-align:right"><?= number_format($row['Nov'], 2); ?></td>
                                            <td style="text-align:right"><?= number_format($row['Dec'], 2); ?></td>
                                            <td style="text-align:right"><b><?= number_format($row['Total'], 2); ?></b></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>

                            <!-- Overall Totals -->
                            <div class="well" style="margin-top:20px;">
                                <h5><b>Overall Totals (All Members - <?= $currentYear ?>)</b></h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Jan</th>
                                        <th>Feb</th>
                                        <th>Mar</th>
                                        <th>Apr</th>
                                        <th>May</th>
                                        <th>Jun</th>
                                        <th>Jul</th>
                                        <th>Aug</th>
                                        <th>Sep</th>
                                        <th>Oct</th>
                                        <th>Nov</th>
                                        <th>Dec</th>
                                        <th>Total</th>
                                    </tr>
                                    <tr style="font-weight:bold;">
                                        <td style="text-align:right"><?= number_format($overall['Jan'], 2); ?></td>
                                        <td style="text-align:right"><?= number_format($overall['Feb'], 2); ?></td>
                                        <td style="text-align:right"><?= number_format($overall['Mar'], 2); ?></td>
                                        <td style="text-align:right"><?= number_format($overall['Apr'], 2); ?></td>
                                        <td style="text-align:right"><?= number_format($overall['May'], 2); ?></td>
                                        <td style="text-align:right"><?= number_format($overall['Jun'], 2); ?></td>
                                        <td style="text-align:right"><?= number_format($overall['Jul'], 2); ?></td>
                                        <td style="text-align:right"><?= number_format($overall['Aug'], 2); ?></td>
                                        <td style="text-align:right"><?= number_format($overall['Sep'], 2); ?></td>
                                        <td style="text-align:right"><?= number_format($overall['Oct'], 2); ?></td>
                                        <td style="text-align:right"><?= number_format($overall['Nov'], 2); ?></td>
                                        <td style="text-align:right"><?= number_format($overall['Dec'], 2); ?></td>
                                        <td style="text-align:right"><?= number_format($overall['Total'], 2); ?></td>
                                    </tr>
                                </table>
                            </div>

                        </div>
                    </div>

                    <!-- Members Purchases Table -->
                    <div class="panel panel-white border-top-xlg border-top-primary">
                        <div class="panel-heading">
                            <h6 class="panel-title">
                                <i class="icon-cart text-primary position-left"></i> Members’ Purchases (<?= $currentYear ?>)
                            </h6>
                        </div>
                        <div class="panel-body panel-theme">
                            <table class="table datatable-button-html5-basic table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>Member</th>
                                        <th>Cash Sales</th>
                                        <th>Paid Charge Sales</th>
                                        <th>Total Purchases</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $purchases->fetchArray(SQLITE3_ASSOC)) { ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['name']); ?></td>
                                            <td style="text-align:right"><?= number_format($row['total_cash_sales'], 2); ?></td>
                                            <td style="text-align:right"><?= number_format($row['total_paid_charge'], 2); ?></td>
                                            <td style="text-align:right; font-weight:bold;"><?= number_format($row['total_purchase'], 2); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>

                            <!-- Overall Total Purchases -->
                            <div class="well" style="margin-top:20px;">
                                <h5><b>Overall Total Purchases (All Members - <?= $currentYear ?>)</b></h5>
                                <h4 style="text-align:right; color:#337ab7;">₱ <?= number_format($overall_purchase['total_purchase'], 2); ?></h4>
                            </div>
                        </div>
                    </div>

                </div>
                <?php require('includes/footer-text.php'); ?>
            </div>
        </div>
    </div>


    <?php require('includes/footer.php'); ?>

    <!-- Modal Add Capital Share -->
    <div id="modal_share" class="modal fade" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" data-toggle="tooltip" title="Press Esc" class="close" data-dismiss="modal">&times;</button>
                    <h5 class="modal-title">Add Capital Share</h5>
                </div>

                <div class="modal-body">
                    <form action="#" id="form-share" class="form-horizontal" data-toggle="validator" role="form">
                        <input type="hidden" name="save-capital-share">

                        <div class="form-body" style="padding-top: 20px">
                            <div id="display-msg"></div>

                            <!-- Member -->
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Member</label>
                                <div class="col-sm-9">
                                    <div class="input-group input-group-xlg">
                                        <span class="input-group-addon"><i class="icon-user text-size-base"></i></span>
                                        <select class="form-control" name="cust_id" required>
                                            <option value="">-- Select Member --</option>
                                            <?php
                                            $members = $db->query("SELECT cust_id, name FROM tbl_customer WHERE cust_id != 1 ORDER BY name ASC");
                                            while ($m = $members->fetchArray(SQLITE3_ASSOC)) { ?>
                                                <option value="<?= $m['cust_id']; ?>"><?= htmlspecialchars($m['name']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>

                            <!-- Amount -->
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Amount</label>
                                <div class="col-sm-9">
                                    <div class="input-group input-group-xlg">
                                        <span class="input-group-addon"><i class="icon-coin-dollar text-size-base"></i></span>
                                        <input class="form-control filterme" name="amount" type="number" step="0.01" min="0" placeholder="Enter amount (₱)" data-error="Please enter a valid amount." required>
                                    </div>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>

                            <!-- Contribution Date -->
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Date</label>
                                <div class="col-sm-9">
                                    <div class="input-group input-group-xlg">
                                        <span class="input-group-addon"><i class="icon-calendar text-size-base"></i></span>
                                        <input class="form-control" name="contribution_date" type="date" value="<?= date('Y-m-d'); ?>" data-error="Please select a date." required>
                                    </div>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button id="btn-submit" type="submit" class="btn bg-teal-400 btn-labeled">
                                <b><i class="icon-add"></i></b> Save Contribution
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script type="text/javascript" src="../assets/js/plugins/tables/datatables/datatables.min.js"></script>
    <script type="text/javascript" src="../assets/js/plugins/notifications/jgrowl.min.js"></script>
    <script src="../js/validator.min.js"></script>
    `
    <script>
        $(function() {
            $('.datatable-button-html5-basic').DataTable({
                "order": [
                    [0, "asc"]
                ],
                "aLengthMenu": [
                    [5, 15, 100],
                    [5, 15, 100]
                ]
            });

            // Handle Capital Share Form
            $('#form-share').validator().on('submit', function(e) {
                if (!e.isDefaultPrevented()) {
                    var data = $(this).serialize();
                    $.ajax({
                        type: 'POST',
                        url: '../transaction.php',
                        data: data,
                        success: function(msg) {
                            if (msg == '1') {
                                $.jGrowl('Capital Share successfully added.', {
                                    header: 'Success Notification',
                                    theme: 'alert-styled-right bg-success'
                                });
                                $('#btn-submit').prop('disabled', true);

                                setTimeout(function() {
                                    location.reload();
                                }, 1500);
                            } else {
                                alert('Something went wrong!');
                            }
                        },
                        error: function() {
                            alert('Error connecting to server.');
                        }
                    });
                    return false;
                }
            });
        });
    </script>
</body>

</html>
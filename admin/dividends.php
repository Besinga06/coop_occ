<?php
require('includes/header.php');


function money($value)
{
    if ($value == 0 || $value === null) {
        return '~';
    }
    return rtrim(rtrim(number_format($value, 2), '0'), '.');
}



// Get current year
$currentYear = date('Y');

// -------------------------
// Capital Share Breakdown per Member (Current Year Only) - NEW STRUCTURE
// -------------------------
$shares = $db->query("
    SELECT 
        m.member_id,
        CONCAT(m.first_name,' ',m.last_name) AS name,

        SUM(CASE WHEN MONTH(t.transaction_date) = 1 THEN t.amount ELSE 0 END) AS Jan,
        SUM(CASE WHEN MONTH(t.transaction_date) = 2 THEN t.amount ELSE 0 END) AS Feb,
        SUM(CASE WHEN MONTH(t.transaction_date) = 3 THEN t.amount ELSE 0 END) AS Mar,
        SUM(CASE WHEN MONTH(t.transaction_date) = 4 THEN t.amount ELSE 0 END) AS Apr,
        SUM(CASE WHEN MONTH(t.transaction_date) = 5 THEN t.amount ELSE 0 END) AS May,
        SUM(CASE WHEN MONTH(t.transaction_date) = 6 THEN t.amount ELSE 0 END) AS Jun,
        SUM(CASE WHEN MONTH(t.transaction_date) = 7 THEN t.amount ELSE 0 END) AS Jul,
        SUM(CASE WHEN MONTH(t.transaction_date) = 8 THEN t.amount ELSE 0 END) AS Aug,
        SUM(CASE WHEN MONTH(t.transaction_date) = 9 THEN t.amount ELSE 0 END) AS Sep,
        SUM(CASE WHEN MONTH(t.transaction_date) = 10 THEN t.amount ELSE 0 END) AS Oct,
        SUM(CASE WHEN MONTH(t.transaction_date) = 11 THEN t.amount ELSE 0 END) AS Nov,
        SUM(CASE WHEN MONTH(t.transaction_date) = 12 THEN t.amount ELSE 0 END) AS `Dec`,

        SUM(t.amount) AS Total

    FROM tbl_members m

    LEFT JOIN accounts a 
        ON a.member_id = m.member_id

    LEFT JOIN account_types at 
        ON at.account_type_id = a.account_type_id

    LEFT JOIN transactions t 
        ON t.account_id = a.account_id
        AND YEAR(t.transaction_date) = '$currentYear'

    WHERE at.type_name = 'capital_share'

    GROUP BY m.member_id

    ORDER BY name ASC
");


$overall = $db->query("
    SELECT 

        SUM(CASE WHEN MONTH(t.transaction_date) = 1 THEN t.amount ELSE 0 END) AS Jan,
        SUM(CASE WHEN MONTH(t.transaction_date) = 2 THEN t.amount ELSE 0 END) AS Feb,
        SUM(CASE WHEN MONTH(t.transaction_date) = 3 THEN t.amount ELSE 0 END) AS Mar,
        SUM(CASE WHEN MONTH(t.transaction_date) = 4 THEN t.amount ELSE 0 END) AS Apr,
        SUM(CASE WHEN MONTH(t.transaction_date) = 5 THEN t.amount ELSE 0 END) AS May,
        SUM(CASE WHEN MONTH(t.transaction_date) = 6 THEN t.amount ELSE 0 END) AS Jun,
        SUM(CASE WHEN MONTH(t.transaction_date) = 7 THEN t.amount ELSE 0 END) AS Jul,
        SUM(CASE WHEN MONTH(t.transaction_date) = 8 THEN t.amount ELSE 0 END) AS Aug,
        SUM(CASE WHEN MONTH(t.transaction_date) = 9 THEN t.amount ELSE 0 END) AS Sep,
        SUM(CASE WHEN MONTH(t.transaction_date) = 10 THEN t.amount ELSE 0 END) AS Oct,
        SUM(CASE WHEN MONTH(t.transaction_date) = 11 THEN t.amount ELSE 0 END) AS Nov,
        SUM(CASE WHEN MONTH(t.transaction_date) = 12 THEN t.amount ELSE 0 END) AS `Dec`,

        SUM(t.amount) AS Total

    FROM transactions t

    INNER JOIN accounts a 
        ON a.account_id = t.account_id

    INNER JOIN account_types at 
        ON at.account_type_id = a.account_type_id

    WHERE at.type_name = 'capital_share'
    AND YEAR(t.transaction_date) = '$currentYear'
")->fetch_assoc();


// -------------------------
// Savings Breakdown per Member (Current Year Only)
// -------------------------
$savings = $db->query("
    SELECT 
        m.member_id,
        CONCAT(m.first_name,' ',m.last_name) AS name,

        SUM(CASE WHEN MONTH(t.transaction_date) = 1 THEN t.amount ELSE 0 END) AS Jan,
        SUM(CASE WHEN MONTH(t.transaction_date) = 2 THEN t.amount ELSE 0 END) AS Feb,
        SUM(CASE WHEN MONTH(t.transaction_date) = 3 THEN t.amount ELSE 0 END) AS Mar,
        SUM(CASE WHEN MONTH(t.transaction_date) = 4 THEN t.amount ELSE 0 END) AS Apr,
        SUM(CASE WHEN MONTH(t.transaction_date) = 5 THEN t.amount ELSE 0 END) AS May,
        SUM(CASE WHEN MONTH(t.transaction_date) = 6 THEN t.amount ELSE 0 END) AS Jun,
        SUM(CASE WHEN MONTH(t.transaction_date) = 7 THEN t.amount ELSE 0 END) AS Jul,
        SUM(CASE WHEN MONTH(t.transaction_date) = 8 THEN t.amount ELSE 0 END) AS Aug,
        SUM(CASE WHEN MONTH(t.transaction_date) = 9 THEN t.amount ELSE 0 END) AS Sep,
        SUM(CASE WHEN MONTH(t.transaction_date) = 10 THEN t.amount ELSE 0 END) AS Oct,
        SUM(CASE WHEN MONTH(t.transaction_date) = 11 THEN t.amount ELSE 0 END) AS Nov,
        SUM(CASE WHEN MONTH(t.transaction_date) = 12 THEN t.amount ELSE 0 END) AS `Dec`,

        SUM(t.amount) AS Total

    FROM tbl_members m

    LEFT JOIN accounts a 
        ON a.member_id = m.member_id

    LEFT JOIN account_types at 
        ON at.account_type_id = a.account_type_id

    LEFT JOIN transactions t 
        ON t.account_id = a.account_id
        AND YEAR(t.transaction_date) = '$currentYear'

    WHERE at.type_name = 'savings'

    GROUP BY m.member_id

    ORDER BY name ASC
");

// Overall Totals
$overall_savings = $db->query("
    SELECT 
        SUM(CASE WHEN MONTH(t.transaction_date) = 1 THEN t.amount ELSE 0 END) AS Jan,
        SUM(CASE WHEN MONTH(t.transaction_date) = 2 THEN t.amount ELSE 0 END) AS Feb,
        SUM(CASE WHEN MONTH(t.transaction_date) = 3 THEN t.amount ELSE 0 END) AS Mar,
        SUM(CASE WHEN MONTH(t.transaction_date) = 4 THEN t.amount ELSE 0 END) AS Apr,
        SUM(CASE WHEN MONTH(t.transaction_date) = 5 THEN t.amount ELSE 0 END) AS May,
        SUM(CASE WHEN MONTH(t.transaction_date) = 6 THEN t.amount ELSE 0 END) AS Jun,
        SUM(CASE WHEN MONTH(t.transaction_date) = 7 THEN t.amount ELSE 0 END) AS Jul,
        SUM(CASE WHEN MONTH(t.transaction_date) = 8 THEN t.amount ELSE 0 END) AS Aug,
        SUM(CASE WHEN MONTH(t.transaction_date) = 9 THEN t.amount ELSE 0 END) AS Sep,
        SUM(CASE WHEN MONTH(t.transaction_date) = 10 THEN t.amount ELSE 0 END) AS Oct,
        SUM(CASE WHEN MONTH(t.transaction_date) = 11 THEN t.amount ELSE 0 END) AS Nov,
        SUM(CASE WHEN MONTH(t.transaction_date) = 12 THEN t.amount ELSE 0 END) AS `Dec`,
        SUM(t.amount) AS Total
    FROM transactions t
    INNER JOIN accounts a ON a.account_id = t.account_id
    INNER JOIN account_types at ON at.account_type_id = a.account_type_id
    WHERE at.type_name = 'savings'
    AND YEAR(t.transaction_date) = '$currentYear'
")->fetch_assoc();

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
            JOIN tbl_sales s2 ON p.sales_no = s2.sales_no
            WHERE s2.cust_id = c.cust_id AND s2.sales_type = 0 AND YEAR(s2.sales_date) = '$currentYear'
        ), 0) AS total_paid_charge,
        COALESCE(SUM(CASE WHEN s.sales_type = 1 THEN s.total_amount ELSE 0 END), 0) 
        + COALESCE((
            SELECT SUM(p.amount_paid)
            FROM tbl_payments p
            JOIN tbl_sales s2 ON p.sales_no = s2.sales_no
            WHERE s2.cust_id = c.cust_id AND s2.sales_type = 0 AND YEAR(s2.sales_date) = '$currentYear'
        ), 0) AS total_purchase
    FROM tbl_customer c
    LEFT JOIN tbl_sales s ON c.cust_id = s.cust_id AND YEAR(s.sales_date) = '$currentYear'
    WHERE c.cust_id != 1
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
            JOIN tbl_sales s2 ON p.sales_no = s2.sales_no
            WHERE s2.sales_type = 0 AND YEAR(s2.sales_date) = '$currentYear'
        ), 0) AS total_purchase
    FROM tbl_sales s
    JOIN tbl_customer c ON s.cust_id = c.cust_id
    WHERE c.cust_id != 1 AND YEAR(s.sales_date) = '$currentYear'
")->fetch_assoc();

// Load members for Add Contribution Modal
$members = $db->query("
    SELECT member_id, first_name, last_name
    FROM tbl_members
    WHERE type = 'regular'
    ORDER BY last_name ASC, first_name ASC
");
$membersArray = $members->fetch_all(MYSQLI_ASSOC);

$all_members = $db->query("
    SELECT member_id, first_name, last_name
    FROM tbl_members
    ORDER BY last_name ASC, first_name ASC
");
$allMembersArray = $all_members->fetch_all(MYSQLI_ASSOC);
?>

<style>
    .navbar-brand {
        display: flex;
        align-items: center;

        gap: 0px;

        font-weight: 800;
        color: white;

        text-decoration: none;
        font-size: 50px;
    }

    .navbar-brand img {
        height: 65px;

        width: auto;
        object-fit: contain;
    }

    .navbar-brand span {
        white-space: nowrap;

    }
</style>




<body class="layout-boxed navbar-top">
    <!-- Main navbar -->
    <div class="navbar navbar-inverse bg-teal-400 navbar-fixed-top">
        <div class="navbar-header">
            <a class="navbar-brand" href="index.php"><img src="../images/your_logo.png" alt=""><span>OCC Cooperative</span></a>
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
                            <h4>Members' Financial (<?= $currentYear ?>)</h4>
                        </div>
                    </div>
                    <div class="breadcrumb-line">
                        <ul class="breadcrumb">
                            <li class="active"><i class="icon-cash3 position-left"></i> Members' Financial</li>
                        </ul>
                        <ul class="breadcrumb-elements">
                            <li>
                                <a href="javascript:;" data-toggle="modal" data-target="#modal_savings">
                                    <i class="icon-coins text-blue-400"></i> Add Savings
                                </a>
                            </li>
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
                                    <h3 class="no-margin">₱ <?= money($overall['Total']); ?></h3>

                                    Total Capital Shares (<?= $currentYear ?>)
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="panel bg-primary-400">
                                <div class="panel-body">
                                    <h3 class="no-margin">₱ <?= money($overall_purchase['total_purchase']); ?></h3>

                                    Total Member Purchases (<?= $currentYear ?>)
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Summary Cards -->

                    <!-- Capital Share Breakdown Table -->
                    <div class="panel panel-white border-top-xlg border-top-success">
                        <div class="panel-heading">
                            <h6 class="panel-title"><i class="icon-cash3 text-success position-left"></i>Capital Share Contributions by Month (<?= $currentYear ?>)</h6>
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
                                    <?php while ($row = $shares->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['name']); ?></td>
                                            <?php for ($m = 1; $m <= 12; $m++) {
                                                $monthName = date('M', mktime(0, 0, 0, $m, 1));
                                                echo '<td style="text-align:right">' . money($row[$monthName]) . '</td>';
                                            } ?>

                                            <td style="text-align:right"><b><?= money($row['Total']); ?></b></td>

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
                                        <?php
                                        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                                        foreach ($months as $m) {
                                            echo '<td style="text-align:right">' . money($overall[$m]) . '</td>';
                                        }
                                        ?>
                                        <td style="text-align:right"><?= money($overall['Total']); ?></td>

                                    </tr>
                                </table>
                            </div>

                        </div>
                    </div>

                    <!-- Savings Breakdown Table -->
                    <div class="panel panel-white border-top-xlg border-top-info">
                        <div class="panel-heading">
                            <h6 class="panel-title"><i class="icon-coins text-blue position-left"></i>Savings by Month (<?= $currentYear ?>)</h6>
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
                                    <?php while ($row = $savings->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['name']); ?></td>
                                            <?php
                                            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                                            foreach ($months as $m) {
                                                echo '<td style="text-align:right">' . money($row[$m]) . '</td>';
                                            }
                                            ?>
                                            <td style="text-align:right; font-weight:bold;"><?= money($row['Total']); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>

                            <!-- Overall Totals -->
                            <div class="well" style="margin-top:20px;">
                                <h5><b>Overall Totals (All Members - <?= $currentYear ?>)</b></h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <?php foreach ($months as $m) {
                                            echo '<th>' . $m . '</th>';
                                        } ?>
                                        <th>Total</th>
                                    </tr>
                                    <tr style="font-weight:bold;">
                                        <?php foreach ($months as $m) {
                                            echo '<td style="text-align:right">' . money($overall_savings[$m]) . '</td>';
                                        } ?>
                                        <td style="text-align:right"><?= money($overall_savings['Total']); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>


                    <!-- Members Purchases Table -->
                    <div class="panel panel-white border-top-xlg border-top-primary">
                        <div class="panel-heading">
                            <h6 class="panel-title"><i class="icon-cart text-primary position-left"></i> Members’ Purchases (<?= $currentYear ?>)</h6>
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
                                    <?php while ($row = $purchases->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['name']); ?></td>
                                            <td style="text-align:right"><?= money($row['total_cash_sales']); ?></td>
                                            <td style="text-align:right"><?= money($row['total_paid_charge']); ?></td>
                                            <td style="text-align:right; font-weight:bold;"><?= money($row['total_purchase']); ?></td>

                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>


                            <div class="well" style="margin-top:20px;">
                                <h5><b>Overall Total Purchases (All Members - <?= $currentYear ?>)</b></h5>
                                <h4 style="text-align:right; color:#337ab7;">₱ <?= money($overall_purchase['total_purchase']); ?></h4>

                            </div>
                        </div>
                    </div>

                </div>
                <?php require('includes/footer-text.php'); ?>
            </div>
        </div>
    </div>


    <?php require('includes/footer.php'); ?>


    <!-- Modal Add Savings -->
    <div id="modal_savings" class="modal fade" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h5 class="modal-title">Add Savings</h5>
                </div>
                <div class="modal-bodys">
                    <form id="form-savings" class="form-horizontal" data-toggle="validator" role="form">
                        <input type="hidden" name="save-savings" value="1">

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Member</label>
                            <div class="col-sm-9">
                                <select class="form-control select-member" name="member_id" required>
                                    <option value="">-- Select Member --</option>
                                    <?php foreach ($allMembersArray as $m) { ?>
                                        <option value="<?= $m['member_id']; ?>">
                                            <?= htmlspecialchars($m['last_name'] . ', ' . $m['first_name']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Amount</label>
                            <div class="col-sm-9">
                                <input class="form-control" name="amount" type="number" step="0.01" min="0" placeholder="Enter amount (₱)" required>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn bg-blue-400 btn-labeled">
                                <b><i class="icon-add"></i></b> Save Savings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Add Capital Share -->
    <div id="modal_share" class="modal fade" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" data-toggle="tooltip" title="Press Esc" class="close" data-dismiss="modal">&times;</button>
                    <h5 class="modal-title">Add Capital Share</h5>
                </div>

                <div class="modal-bodys">

                    <form action="#" id="form-share" class="form-horizontal" data-toggle="validator" role="form">
                        <input type="hidden" name="save-capital-share" value="1">


                        <div class="form-body" style="padding-top: 20px">
                            <div id="display-msg"></div>

                            <!-- Member -->
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Member</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="icon-user"></i>
                                        </span>
                                        <select class="form-control select-member" name="member_id" required>
                                            <option value="">-- Select Regular Member --</option>

                                            <?php foreach ($membersArray as $m) { ?>
                                                <option value="<?= $m['member_id']; ?>">
                                                    <?= htmlspecialchars($m['last_name'] . ', ' . $m['first_name']); ?>
                                                </option>
                                            <?php } ?>

                                        </select>


                                    </div>

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




    <script src="../js/select2.min.js"></script>

    <script type="text/javascript" src="../assets/js/plugins/tables/datatables/datatables.min.js"></script>
    <script type="text/javascript" src="../assets/js/plugins/notifications/jgrowl.min.js"></script>
    <script src="../js/validator.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#modal_share .select-member').select2({
                dropdownParent: $('#modal_share'),
                placeholder: "Search Member",
                allowClear: true,
                width: '100%'
            });

            $('#modal_savings .select-member').select2({
                dropdownParent: $('#modal_savings'),
                placeholder: "Search Member",
                allowClear: true,
                width: '100%'
            });
        });

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
                        type: "POST",
                        url: "../transaction.php",
                        data: data,
                        success: function(resp) {
                            $.jGrowl("Contribution saved successfully!", {
                                header: 'Success',
                                theme: 'bg-success'
                            });
                            $('#modal_share').modal('hide');
                            setTimeout(function() {
                                location.reload();
                            }, 5000);
                        },
                        error: function() {
                            $.jGrowl("Error saving contribution.", {
                                header: 'Error',
                                theme: 'bg-danger'
                            });
                        }
                    });
                    return false;
                }
            });
        });

        $('#form-savings').validator().on('submit', function(e) {
            if (!e.isDefaultPrevented()) {
                var data = $(this).serialize();
                $.ajax({
                    type: "POST",
                    url: "../transaction.php",
                    data: data,
                    success: function(resp) {
                        $.jGrowl("Savings saved successfully!", {
                            header: 'Success',
                            theme: 'bg-success'
                        });
                        $('#modal_savings').modal('hide');
                        setTimeout(function() {
                            location.reload();
                        }, 5000);
                    },
                    error: function() {
                        $.jGrowl("Error saving savings.", {
                            header: 'Error',
                            theme: 'bg-danger'
                        });
                    }
                });
                return false;
            }
        });
    </script>
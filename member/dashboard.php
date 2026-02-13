<?php require('../admin/includes/header.php'); ?>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Manila');
date_default_timezone_get();
$year = date('Y'); // current year
$today = date("Y-m-d");
$date_add = date('Y-m-d', strtotime('+1 day', strtotime($today)));

$deposit = 0;
$deposit_query = "SELECT SUM(amount) AS total FROM tbl_deposits WHERE date_added BETWEEN '$today' AND '$date_add'";
$result_deposit = $db->query($deposit_query);
$row = $result_deposit->fetch_assoc();
$deposit = $row['total'] ?? 0;

$all_subtotal = 0;
$all_discount = 0;
$all_total = 0;
$total_sales = 0;

$query = "
SELECT 
    sales_no,
    SUM(subtotal) AS subtotal,
    SUM(discount) AS discount,
    SUM(total_amount) AS total_amount
FROM tbl_sales
WHERE sales_date BETWEEN '$today' AND '$date_add'
GROUP BY sales_no
";
$result = $db->query($query);

if (!isset($_SESSION['is_login_yes'], $_SESSION['user_id']) || $_SESSION['is_login_yes'] != 'yes') {
    die("Unauthorized access. Please log in again.");
}

$user_id = (int) $_SESSION['user_id'];


$member_result = $db->query("
    SELECT member_id, cust_id 
    FROM tbl_members 
    WHERE user_id = $user_id
    LIMIT 1
");

if (!$member_result || $member_result->num_rows == 0) {
    die("Member is not linked to a customer record.");
}

$member_data = $member_result->fetch_assoc();
$member_id = (int) $member_data['member_id'];
$cust_id   = (int) $member_data['cust_id'];

if ($cust_id <= 0) {
    die("Invalid customer account.");
}

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $subtotal = $row['subtotal'];
        $discount = $row['discount'];
        $total_amount = $row['total_amount'];
        $all_subtotal += $subtotal;
        $all_discount += $discount;
        $all_total += $total_amount;
        $total_sales++;
    }
}

$vat_sales = $all_subtotal * 0.12;


$customer_select = "SELECT COUNT(*) AS total_customer FROM tbl_customer";
$customer_result = $db->query($customer_select);
$customer_row = $customer_result->fetch_assoc();
$customer_total = $customer_row['total_customer'];

$user_select = "SELECT COUNT(*) AS total_user FROM tbl_users WHERE usertype != 4";
$user_result = $db->query($user_select);
$user_row = $user_result->fetch_assoc();
$user_total = $user_row['total_user'];

$supplier_select = "SELECT COUNT(*) AS total_supplier FROM tbl_supplier";
$supplier_result = $db->query($supplier_select);
$supplier_row = $supplier_result->fetch_assoc();
$supplier_total = $supplier_row['total_supplier'];


// Loan Stats Queries
$loan_apps = $db->query("SELECT COUNT(*) AS total FROM tbl_loan_application")->fetch_assoc()['total'];
$loan_pending = $db->query("SELECT COUNT(*) AS total FROM tbl_loan_application WHERE status='pending'")->fetch_assoc()['total'];
$loan_approved = $db->query("SELECT COUNT(*) AS total FROM tbl_loan_approval")->fetch_assoc()['total'];
$loan_disbursed = $db->query("SELECT COUNT(*) AS total FROM tbl_loan_disbursement")->fetch_assoc()['total'];
$total_disbursed = $db->query("SELECT IFNULL(SUM(amount_released),0) AS total FROM tbl_loan_disbursement")->fetch_assoc()['total'];
$total_repaid = $db->query("SELECT IFNULL(SUM(amount_paid),0) AS total FROM tbl_loan_repayment")->fetch_assoc()['total'];

$outstanding = $db->query("
    SELECT 
    (SELECT IFNULL(SUM(total_payable),0) FROM tbl_loan_transactions) - 
    (SELECT IFNULL(SUM(amount_paid),0) FROM tbl_loan_repayment) AS outstanding
")->fetch_assoc()['outstanding'];

$fund_balance = $db->query("SELECT IFNULL(SUM(current_balance),0) AS total FROM tbl_loan_fund")->fetch_assoc()['total'];

// Monthly disbursement for line chart
$monthly_disb = $db->query("SELECT DATE_FORMAT(release_date, '%Y-%m') AS month, SUM(amount_released) AS total 
                            FROM tbl_loan_disbursement 
                            GROUP BY month ORDER BY month ASC");
$months = [];
$values = [];
while ($row = $monthly_disb->fetch_assoc()) {
    $months[] = $row['month'];
    $values[] = $row['total'];
}

// Monthly repayment
$monthly_rep = $db->query("SELECT DATE_FORMAT(payment_date, '%Y-%m') AS month, SUM(amount_paid) AS total 
                            FROM tbl_loan_repayment 
                            GROUP BY month ORDER BY month ASC");
$months2 = [];
$values2 = [];
while ($row = $monthly_rep->fetch_assoc()) {
    $months2[] = $row['month'];
    $values2[] = $row['total'];
}


$contributions = $db->query("
    SELECT IFNULL(SUM(amount), 0) AS total_amount
    FROM tbl_capital_share
    WHERE cust_id = $cust_id
      AND YEAR(contribution_date) = $year
")->fetch_assoc()['total_amount'];
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

    /* Mobile App Style */
    @media (max-width:768px) {
        .content-wrapper {
            padding: 10px;
        }

        .panel {
            border-radius: 14px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, .06);
        }

        .col-sm-6.col-md-3 {
            margin-bottom: 10px;
        }

        .panel .icon-3x {
            font-size: 28px !important;
        }

        .table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }

        .navbar-nav {
            display: none;
        }

        body {
            padding-bottom: 75px;
        }
    }

    .mobile-bottom-nav {
        display: none;
    }

    @media (max-width:768px) {
        .mobile-bottom-nav {
            display: flex;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            border-top: 1px solid #ddd;
            justify-content: space-around;
            padding: 8px 0;
            z-index: 9999;
        }

        .mobile-bottom-nav a {
            text-align: center;
            font-size: 11px;
            color: #444;
        }

        .mobile-bottom-nav i {
            display: block;
            font-size: 20px;
            margin-bottom: 2px;
        }

        .mobile-bottom-nav a.active {
            color: #26a69a;
        }
    }

    /* DEFAULT */
    .mobile-view {
        display: none;
    }

    .desktop-view {
        display: block;
    }

    /* MOBILE MODE */
    @media (max-width: 768px) {

        .desktop-view {
            display: none;
        }

        .mobile-view {
            display: block;
        }

        body {
            background: #f4f6f9;
            padding-bottom: 80px;
        }

        /* Header */
        .mobile-header {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            font-weight: 700;
            font-size: 20px;
        }

        .mobile-help {
            background: #26a69a;
            color: #fff;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
        }

        /* Balance Card */
        .mobile-balance-card {
            background: #26a69a;
            ;
            color: #fff;
            margin: 15px;
            padding: 20px;
            border-radius: 18px;
            position: relative;
        }

        .mobile-balance-card h2 {
            margin: 10px 0;
            font-size: 28px;
        }

        .quick-save {
            position: absolute;
            right: 15px;
            bottom: 15px;
            background: #fff;
            color: #1e88e5;
            border: none;
            padding: 8px 14px;
            border-radius: 20px;
            font-weight: 600;
        }

        /* Actions */
        .mobile-actions {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            padding: 15px;
            text-align: center;
        }

        .mobile-actions a {
            background: #fff;
            padding: 15px 5px;
            border-radius: 12px;
            font-size: 12px;
            color: #333;
            box-shadow: 0 3px 6px rgba(0, 0, 0, .08);
            text-decoration: none;
        }
    }

    .balance-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 8px;
    }

    .balance-tabs .tab {
        font-size: 13px;
        padding: 4px 12px;
        border-radius: 20px;
        background: rgba(255, 255, 255, .25);
        cursor: pointer;
        font-weight: 600;
    }

    .balance-tabs .tab.active {
        background: #fff;
        color: #1e88e5;
    }

    @media (max-width:768px) {

        /* Hide top navbar on mobile */
        .navbar.navbar-inverse {
            display: none !important;
        }
    }

    @media (max-width:768px) {

        .panel,
        .panel-white,
        .content>.row,
        .panel-heading {
            display: none !important;
        }
    }
</style>

<body class="layout-boxed navbar-top">
    <!-- Main navbar -->
    <div class="navbar navbar-inverse bg-teal-400 navbar-fixed-top">
        <div class="navbar-header">
            <a class="navbar-brand" href="dashboard.php"><img style="height: 65px!important" src="../images/your_logo.png" alt=""><span>OCC Cooperative</span></a>
            <ul class="nav navbar-nav visible-xs-block">
                <li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
            </ul>
        </div>
        <div class="navbar-collapse collapse" id="navbar-mobile">
            <?php require('../admin/includes/sidebar.php'); ?>
        </div>
    </div>
    <!-- /main navbar -->
    <!-- Page container -->
    <div class="page-container">

        <div class="mobile-view">

            <!-- HEADER -->
            <div class="mobile-header">
                Wellcome back, <?= $_SESSION['fullname'] ?>

            </div>

            <!-- BALANCE CARD -->
            <div class="mobile-balance-card">
                <small>Capital Share</small>
                <h2>₱ <?= number_format($contributions, 2) ?></h2>
                <button class="quick-save" onclick="location.href='capital_share.php'">+ Save</button>
            </div>

            <!-- QUICK STATS -->
            <div style="padding:15px;">
                <div style="display:flex; gap:10px;">
                    <div style="flex:1; background:#fff; padding:12px; border-radius:12px;">
                        <small>Sales</small>
                        <h4><?= $total_sales ?></h4>
                    </div>
                    <div style="flex:1; background:#fff; padding:12px; border-radius:12px;">
                        <small>Members</small>
                        <h4><?= $customer_total ?></h4>
                    </div>
                </div>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="mobile-actions">
                <a href="capital_share.php"><i class="icon-plus-circle"></i>Deposit</a>
                <a href="loan.php"><i class="icon-coins"></i>Loan</a>
                <a href="#"><i class="icon-history"></i>History</a>
                <a href="../admin/profile.php"><i class="icon-user"></i>Profile</a>
            </div>

            <!-- LOAN SUMMARY -->
            <div style="margin:15px;">
                <div style="background:#fff;padding:15px;border-radius:15px;">
                    <b>Loan Overview</b><br><br>
                    Disbursed: ₱ <?= number_format($total_disbursed, 2) ?><br>
                    Repaid: ₱ <?= number_format($total_repaid, 2) ?><br>
                    Outstanding: ₱ <?= number_format($outstanding, 2) ?>
                </div>





            </div>

        </div>


    </div>
    <?php require('../admin/includes/footer-text.php'); ?>
    <!-- Page content -->
    <div class="page-content desktop-view">
        <!-- Main content -->
        <div class="content-wrapper">
            <!-- Page header -->
            <div class="page-header page-header-default"></div>
            <!-- /page header -->




            <!-- Content area -->
            <div class="content">
                <div class="row">
                    <div class="col-sm-6 col-md-3">
                        <div class="panel panel-body">
                            <div class="media no-margin">
                                <div class="media-left media-middle">
                                    <i class="icon-cart icon-3x text-danger-400"></i>
                                </div>
                                <div class="media-body text-right">
                                    <h3 class="no-margin text-semibold"><?= $total_sales ?></h3>
                                    <span class="text-uppercase text-size-mini text-muted">today's Sale</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="panel panel-body panel-body-accent">
                            <div class="media no-margin">
                                <div class="media-left media-middle">
                                    <i class="icon-users icon-3x text-success-400"></i>
                                </div>
                                <div class="media-body text-right">
                                    <h3 class="no-margin text-semibold"><?= $user_total ?></h3>
                                    <span class="text-uppercase text-size-mini text-muted">Employee</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="panel panel-body">
                            <div class="media no-margin">
                                <div class="media-left media-middle">
                                    <i class="icon-users icon-3x text-indigo-400"></i>
                                </div>
                                <div class="media-body text-right">
                                    <h3 class="no-margin text-semibold"><?= $customer_total ?></h3>
                                    <span class="text-uppercase text-size-mini text-muted">Member</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="panel panel-body">
                            <div class="media no-margin">
                                <div class="media-left media-middle">
                                    <i class="icon-users icon-3x text-blue-400"></i>
                                </div>
                                <div class="media-body text-right">
                                    <h3 class="no-margin text-semibold"><?= $supplier_total ?></h3>
                                    <span class="text-uppercase text-size-mini text-muted">Supplier</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-white">
                    <div class="panel-heading">
                        <h6 class="panel-title"><i class="icon-chart text-teal-400"></i> Daily Sales</h6>
                    </div>
                    <!-- <input type="text" id="myInputTextField"> -->
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-6 col-md-3">
                                <div class="panel panel-body bg-success-400 has-bg-image">
                                    <div class="media no-margin">
                                        <div class="media-left media-middle">
                                            <i class="icon-cart icon-3x opacity-75"></i>
                                        </div>
                                        <div class="media-body text-right">
                                            <h3 class="no-margin" id="no-sales"><?= $total_sales ?></h3>
                                            <span class="text-uppercase text-size-mini">No. of Sales</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="panel panel-body bg-blue-400 has-bg-image">
                                    <div class="media no-margin">
                                        <div class="media-right media-middle">
                                            <i class="icon-3x opacity-75">₱</i>
                                        </div>
                                        <div class="media-body text-right">
                                            <h3 class="no-margin"><?= number_format($all_total, 2) ?></h3>
                                            <span class="text-uppercase text-size-mini">Sub Total</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="panel panel-body bg-danger-400 has-bg-image">
                                    <div class="media no-margin">
                                        <div class="media-right media-middle">
                                            <i class="icon-3x opacity-75">₱</i>
                                        </div>
                                        <div class="media-body text-right">
                                            <h3 class="no-margin"><?= number_format($all_discount, 2) ?></h3>
                                            <span class="text-uppercase text-size-mini">Discount</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="panel panel-body bg-indigo-400 has-bg-image">
                                    <div class="media no-margin">
                                        <div class="media-left media-middle">
                                            <i class="icon-3x opacity-75">₱</i>
                                        </div>
                                        <div class="media-body text-right">
                                            <h3 class="no-margin"><?= number_format($all_total, 2) ?></h3>
                                            <span class="text-uppercase text-size-mini">Total Amount</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- /content area -->
        </div>
        <!-- /main content -->
    </div>
    <!-- /page content -->
    </div>
    <!-- /page container -->
</body>

<div class="mobile-bottom-nav">
    <a href="transaction_history.php">
        <i class="icon-history"></i>
        transaction
    </a>
    <a href="dashboard.php">
        <i class="icon-piggy-bank"></i>
        Savings
    </a>
    <a href="dashboard.php" class="active">
        <i class="icon-home"></i>
        Home
    </a>
    <a href="loan.php">
        <i class="icon-coins"></i>
        Loans
    </a>
    <a href="../admin/profile.php">
        <i class="icon-user"></i>
        Profile
    </a>
</div>
<?php require('../admin/includes/footer.php'); ?>


<script type="text/javascript" src="../assets/js/plugins/ui/moment/moment.min.js"></script>
<script type="text/javascript" src="../assets/js/plugins/pickers/daterangepicker.js"></script>
<script type="text/javascript" src="../assets/js/plugins/pickers/anytime.min.js"></script>
<script type="text/javascript" src="../assets/js/plugins/pickers/pickadate/picker.js"></script>
<script type="text/javascript" src="../assets/js/plugins/pickers/pickadate/picker.date.js"></script>
<script type="text/javascript" src="../assets/js/plugins/pickers/pickadate/picker.time.js"></script>
<script type="text/javascript" src="../assets/js/plugins/pickers/pickadate/legacy.js"></script>
<script type="text/javascript" src="../assets/js/pages/picker_date.js"></script>


<script type="text/javascript">
    $(function() {
        $('[data-toggle="tooltip"]').tooltip();

    });

    $('#form-seller').on('submit', function(e) {
        $(':input[type="submit"]').prop('disabled', true);
        var data = $("#form-seller").serialize();
        $.ajax({
            type: 'POST',
            url: '../transaction.php',
            data: data,
            success: function(msg) {
                location.reload();
            },
            error: function(msg) {
                alert('Something went wrong!');
            }
        });
        return false;
    });
</script>
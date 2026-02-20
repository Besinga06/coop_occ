<?php require('../admin/includes/header.php'); ?>

<?php

if (!isset($_SESSION['is_login_yes'], $_SESSION['user_id']) || $_SESSION['is_login_yes'] != 'yes') {
    die("Unauthorized access. Please log in again.");
}

$user_id = (int) $_SESSION['user_id'];


// GET MEMBER
$member = $db->query("
SELECT member_id
FROM tbl_members
WHERE user_id='$user_id'
")->fetch_assoc();

$member_id = $member['member_id'];

// GET CAPITAL SHARE
$capital = $db->query("
SELECT
COALESCE(SUM(
CASE
WHEN tt.type_name IN ('deposit', 'capital_share')
THEN t.amount
WHEN tt.type_name='withdrawal'
THEN -t.amount
ELSE 0
END
),0) AS capital_balance
FROM accounts a
JOIN account_types at ON at.account_type_id=a.account_type_id
LEFT JOIN transactions t ON t.account_id=a.account_id
LEFT JOIN transaction_types tt ON tt.transaction_type_id=t.transaction_type_id
WHERE a.member_id='$member_id'
AND at.type_name='capital_share'
")->fetch_assoc();

$capital_balance = $capital['capital_balance'];


// GET MIN CAPITAL
$min = $db->query("
SELECT setting_value
FROM system_settings
WHERE setting_key='min_capital_required'
")->fetch_assoc();

$min_capital = $min['setting_value'];


// CHECK ELIGIBILITY
$eligible = $capital_balance >= $min_capital;

?>

<link rel="stylesheet" href="../css/mobile-dashboard.css">

<body class="layout-boxed navbar-top">


    <!-- NAVBAR -->
    <div class="navbar navbar-inverse bg-teal-400 navbar-fixed-top">
        <div class="navbar-header">
            <a class="navbar-brand" href="dashboard.php">
                <img style="height: 65px!important" src="../images/your_logo.png">
                <span>OCC Cooperative</span>
            </a>
        </div>
        <div class="navbar-collapse collapse">
            <?php require('../admin/includes/sidebar.php'); ?>
        </div>
    </div>

    <!-- PAGE CONTAINER -->
    <div class="page-container">
        <!-- MOBILE VIEW -->
        <div class="mobile-view">
            <div class="mobile-header">
                Loan Page
            </div>
            <div class="mobile-loan-summary">
                <div class="loan-card" style="background:#26a69a;color:white;">
                    <div>
                        <small>Capital Share</small>
                        <h4>
                            ₱ <?= number_format($capital_balance, 2) ?>
                        </h4>
                    </div>
                    <div>
                        <small>Required</small>
                        <h4>
                            ₱ <?= number_format($min_capital, 2) ?>
                        </h4>
                    </div>
                </div>
            </div>
            <?php if ($eligible) { ?>
                <div class="alert alert-success">
                    ✅ Eligible for Loan
                </div>
                <a href="apply_loan.php" class="btn btn-primary btn-block">
                    Apply Loan
                </a>
            <?php } else { ?>
                <div class="alert alert-danger">
                    ❌ Not Eligible
                </div>
            <?php } ?>
        </div>



        <!-- DESKTOP VIEW -->
        <div class="page-content desktop-view">

            <!-- Main content -->
            <div class="content-wrapper">
                <div class="page-header page-header-default">
                    <div class="page-header-content">
                        <div class="page-title">
                            <h4>
                                <i class="icon-user position-left"></i>
                                Loan Status
                            </h4>
                        </div>
                    </div>

                    <div class="breadcrumb-line">
                        <ul class="breadcrumb">
                            <li>
                                <a href="dashboard.php">
                                    <i class="icon-home"></i>Dashboard
                                </a>
                            </li>
                            <li class="active"> Loan Status</li>
                        </ul>
                    </div>
                </div>
                <!-- Content area -->
                <div class="content">

                    <div class="row">
                        <!-- Capital Share Panel -->
                        <div class="col-sm-6 col-md-3">
                            <div class="panel panel-body bg-success-400 has-bg-image">
                                <div class="media no-margin">
                                    <div class="media-left media-middle">
                                        <i class="icon-coins icon-3x opacity-75"></i>
                                    </div>
                                    <div class="media-body text-right">
                                        <h3 class="no-margin">
                                            ₱ <?= number_format($capital_balance, 2) ?>
                                        </h3>
                                        <span class="text-uppercase text-size-mini">
                                            Your Capital Share
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Required Capital -->
                        <div class="col-sm-6 col-md-3">
                            <div class="panel panel-body bg-danger-400 has-bg-image">
                                <div class="media no-margin">
                                    <div class="media-left media-middle">
                                        <i class="icon-wallet icon-3x opacity-75"></i>
                                    </div>
                                    <div class="media-body text-right">
                                        <h3 class="no-margin">
                                            ₱ <?= number_format($min_capital, 2) ?>
                                        </h3>
                                        <span class="text-uppercase text-size-mini">
                                            Required Capital
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Eligibility Status -->
                        <div class="col-sm-6 col-md-3">
                            <div class="panel panel-body <?= $eligible ? 'bg-teal-400' : 'bg-warning-400' ?> has-bg-image">
                                <div class="media no-margin">
                                    <div class="media-left media-middle">
                                        <i class="icon-check icon-3x opacity-75"></i>
                                    </div>
                                    <div class="media-body text-right">
                                        <h3 class="no-margin">
                                            <?= $eligible ? 'Eligible' : 'Not Eligible' ?>
                                        </h3>
                                        <span class="text-uppercase text-size-mini">
                                            Loan Status
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Apply Loan Panel -->
                    <div class="panel panel-white">

                        <div class="panel-heading">
                            <h6 class="panel-title">
                                Apply Loan
                            </h6>
                        </div>
                        <div class="panel-body text-center">
                            <?php if ($eligible) { ?>
                                <?php if (!isset($_GET['apply'])) { ?>
                                    <!-- APPLY BUTTON -->
                                    <a href="?apply=1" class="btn bg-teal-400 btn-lg">
                                        Apply Loan
                                    </a>
                                <?php } else { ?>
                                    <!-- APPLICATION FORM -->
                                    <form method="POST">
                                        <div class="form-group text-left">
                                            <label>Loan Amount</label>
                                            <input type="number"
                                                name="loan_amount"
                                                class="form-control"
                                                required>
                                        </div>
                                        <div class="form-group text-left">
                                            <label>Loan Purpose</label>
                                            <textarea
                                                name="loan_purpose"
                                                class="form-control"
                                                required></textarea>
                                        </div>
                                        <br>
                                        <button type="submit"
                                            name="applyLoan"
                                            class="btn bg-teal-400 btn-lg">
                                            Submit Application
                                        </button>
                                        <a href="loan.php"
                                            class="btn btn-default btn-lg">
                                            Cancel
                                        </a>
                                    </form>
                                <?php } ?>
                            <?php } else { ?>
                                <div class="alert alert-warning">
                                    <strong>You are not eligible yet.</strong>
                                </div>
                                <!-- REQUIREMENTS UI -->
                                <div class="panel panel-body bg-grey-100">
                                    <h5 class="text-semibold text-teal-400">
                                        Requirements to Qualify:
                                    </h5>
                                    <ul class="list list-icons text-left">
                                        <li class="<?= $capital_balance >= $min_capital ? 'text-success' : 'text-danger' ?>">
                                            <i class="<?= $capital_balance >= $min_capital ? 'icon-checkmark' : 'icon-cross' ?>"></i>
                                            Minimum Capital Share of
                                            ₱ <?= number_format($min_capital, 2) ?>
                                        </li>
                                        <li class="text-success">
                                            <i class="icon-checkmark"></i>
                                            Active Member Account
                                        </li>
                                        <li class="text-success">
                                            <i class="icon-checkmark"></i>
                                            No Pending Loan Defaults
                                        </li>
                                    </ul>
                                    <br>
                                    <a href="capital_share.php"
                                        class="btn bg-success-400 btn-block">
                                        Add Capital Share
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <!-- /content wrapper -->
                    <!-- MOBILE BOTTOM NAV -->
                    <div class="mobile-bottom-nav">
                        <a href="transaction_history.php">
                            <i class="icon-history"></i>
                            Transaction
                        </a>
                        <a href="dashboard.php">
                            <i class="icon-home"></i>
                            Home
                        </a>
                        <a href="loan.php" class="active">
                            <i class="icon-coins"></i>
                            Loans
                        </a>
                        <a href="../admin/profile.php">
                            <i class="icon-user"></i>
                            Profile
                        </a>
                    </div>
                </div>
                <!-- /page content -->
                <?php require('../admin/includes/footer-text.php'); ?>
                <?php require('../admin/includes/footer.php'); ?>


</body>
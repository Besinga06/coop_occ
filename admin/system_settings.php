<?php require('includes/header.php'); ?>
<?php require('db_connect.php'); ?>

<?php

if (!isset($_SESSION['is_login_yes'], $_SESSION['user_id']) || $_SESSION['is_login_yes'] != 'yes') {
    die("Unauthorized access.");
}

function get_setting($key)
{
    global $db;

    $stmt = $db->prepare("
        SELECT setting_value
        FROM system_settings
        WHERE setting_key = ?
        LIMIT 1
    ");

    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['setting_value'] ?? '';
}

$success = false;
if (isset($_POST['save_settings'])) {
    foreach ($_POST['settings'] as $key => $value) {
        $stmt = $db->prepare("
            INSERT INTO system_settings (setting_key, setting_value)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
        ");
        $stmt->bind_param("ss", $key, $value);
        $stmt->execute();
    }
    $success = true;
}
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
        height: 40px;
        width: auto;
        object-fit: contain;
    }

    .navbar-brand span {
        white-space: nowrap;
    }
</style>

<body class="layout-boxed navbar-top">
    <div class="navbar navbar-inverse bg-teal-400 navbar-fixed-top">
        <div class="navbar-header">
            <a class="navbar-brand" href="dashboard.php">
                <img style="height:65px!important" src="../images/your_logo.png">
                <span>OCC Cooperative</span>
            </a>
        </div>
        <div class="navbar-collapse collapse">
            <?php require('includes/sidebar.php'); ?>
        </div>

    </div>

    <div class="page-container">
        <div class="page-content">
            <div class="content-wrapper">
                <div class="page-header page-header-default">
                    <div class="page-header-content">
                        <div class="page-title">
                            <h4>
                                <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold">Dashboard </span> - System Settings</h4>
                            </h4>
                        </div>
                    </div>
                    <div class="breadcrumb-line">
                        <ul class="breadcrumb">
                            <li><a href="index.php"><i class="icon-home2 position-left"></i>Dashboard</a></li>
                            <li class="active"><i class="icon-cog"></i> System Settings</li>
                        </ul>
                    </div>
                </div>

                <div class="content">
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-styled-left alert-bordered">
                            Settings saved successfully!
                        </div>
                    <?php endif; ?>


                    <form method="POST">
                        <div class="panel panel-white">
                            <div class="panel-heading">
                                <h6 class="panel-title">Cooperative System Configuration</h6>
                            </div>
                            <div class="panel-body">
                                <ul class="nav nav-tabs nav-tabs-solid bg-teal-400">
                                    <li class="active">
                                        <a href="#general" data-toggle="tab">
                                            <i class="icon-cog"></i> General Overview
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#loan" data-toggle="tab">
                                            <i class="icon-cash"></i> Loan Settings
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#savings" data-toggle="tab">
                                            <i class="icon-piggy-bank"></i> Savings Settings
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#capital" data-toggle="tab">
                                            <i class="icon-stack"></i> Capital Share
                                        </a>
                                    </li>

                                </ul>
                                <!-- TAB CONTENT -->
                                <div class="tab-content">
                                    <div class="tab-pane active" id="general">

                                        <br>
                                        <div class="row">

                                            <!-- LOAN CARD -->
                                            <div class="col-md-4">
                                                <div class="panel panel-flat border-top-success">
                                                    <div class="panel-heading">
                                                        <h6 class="panel-title">
                                                            <i class="icon-cash text-success"></i>
                                                            Loan Configuration
                                                        </h6>
                                                    </div>

                                                    <div class="panel-body">

                                                        <div class="well well-sm">
                                                            <small>Minimum Membership</small>
                                                            <h4><?= get_setting('min_membership_months') ?> months</h4>
                                                        </div>

                                                        <div class="well well-sm">
                                                            <small>Minimum Savings Required</small>
                                                            <h4>₱ <?= number_format(get_setting('min_savings_required'), 2) ?></h4>
                                                        </div>

                                                        <div class="well well-sm">
                                                            <small>Minimum Capital Required</small>
                                                            <h4>₱ <?= number_format(get_setting('min_capital_required'), 2) ?></h4>
                                                        </div>

                                                        <div class="well well-sm">
                                                            <small>Require Comaker</small>
                                                            <h4>
                                                                <?= get_setting('require_comaker') == '1'
                                                                    ? '<span class="label label-success">YES</span>'
                                                                    : '<span class="label label-default">NO</span>' ?>
                                                            </h4>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>


                                            <!-- SAVINGS CARD -->
                                            <div class="col-md-4">
                                                <div class="panel panel-flat border-top-info">
                                                    <div class="panel-heading">
                                                        <h6 class="panel-title">
                                                            <i class="icon-piggy-bank text-info"></i>
                                                            Savings Configuration
                                                        </h6>
                                                    </div>

                                                    <div class="panel-body">

                                                        <div class="well well-sm">
                                                            <small>Minimum Balance</small>
                                                            <h4>₱ <?= number_format(get_setting('savings_min_balance'), 2) ?></h4>
                                                        </div>

                                                        <div class="well well-sm">
                                                            <small>Interest Rate</small>
                                                            <h4><?= get_setting('savings_interest_rate') ?>%</h4>
                                                        </div>

                                                        <div class="well well-sm">
                                                            <small>Withdrawal Limit</small>
                                                            <h4>₱ <?= number_format(get_setting('savings_withdrawal_limit'), 2) ?></h4>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>


                                            <!-- CAPITAL CARD -->
                                            <div class="col-md-4">
                                                <div class="panel panel-flat border-top-warning">
                                                    <div class="panel-heading">
                                                        <h6 class="panel-title">
                                                            <i class="icon-stack text-warning"></i>
                                                            Capital Share Configuration
                                                        </h6>
                                                    </div>

                                                    <div class="panel-body">

                                                        <div class="well well-sm">
                                                            <small>Minimum Capital</small>
                                                            <h4>₱ <?= number_format(get_setting('capital_min_required'), 2) ?></h4>
                                                        </div>

                                                        <div class="well well-sm">
                                                            <small>Maximum Capital</small>
                                                            <h4>₱ <?= number_format(get_setting('capital_max_limit'), 2) ?></h4>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                    <!-- LOAN TAB -->
                                    <div class="tab-pane" id="loan">

                                        <br>

                                        <div class="row">

                                            <div class="col-md-6">

                                                <div class="form-group">
                                                    <label>Minimum Membership Months</label>
                                                    <input type="number"
                                                        name="settings[min_membership_months]"
                                                        class="form-control"
                                                        value="<?= get_setting('min_membership_months') ?>">
                                                </div>


                                                <div class="form-group">
                                                    <label>Minimum Savings Required</label>
                                                    <input type="number"
                                                        name="settings[min_savings_required]"
                                                        class="form-control"
                                                        value="<?= get_setting('min_savings_required') ?>">
                                                </div>

                                            </div>


                                            <div class="col-md-6">

                                                <div class="form-group">
                                                    <label>Minimum Capital Required</label>
                                                    <input type="number"
                                                        name="settings[min_capital_required]"
                                                        class="form-control"
                                                        value="<?= get_setting('min_capital_required') ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Require Comaker</label>

                                                    <select name="settings[require_comaker]" class="form-control">

                                                        <option value="1"
                                                            <?= get_setting('require_comaker') == '1' ? 'selected' : '' ?>>
                                                            Yes
                                                        </option>

                                                        <option value="0"
                                                            <?= get_setting('require_comaker') == '0' ? 'selected' : '' ?>>
                                                            No
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- SAVINGS TAB -->
                                    <div class="tab-pane" id="savings">

                                        <br>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Minimum Balance</label>
                                                    <input type="number"
                                                        name="settings[savings_min_balance]"
                                                        class="form-control"
                                                        value="<?= get_setting('savings_min_balance') ?>">
                                                </div>

                                                <div class="form-group">
                                                    <label>Interest Rate (%)</label>
                                                    <input type="number"
                                                        step="0.01"
                                                        name="settings[savings_interest_rate]"
                                                        class="form-control"
                                                        value="<?= get_setting('savings_interest_rate') ?>">
                                                </div>
                                            </div>

                                            <div class="col-md-6">

                                                <div class="form-group">
                                                    <label>Withdrawal Limit per day</label>
                                                    <input type="number"
                                                        name="settings[savings_withdrawal_limit]"
                                                        class="form-control"
                                                        value="<?= get_setting('savings_withdrawal_limit') ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                    <!-- CAPITAL TAB -->
                                    <div class="tab-pane" id="capital">

                                        <br>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Minimum Capital Required</label>
                                                    <input type="number"
                                                        name="settings[capital_min_required]"
                                                        class="form-control"
                                                        value="<?= get_setting('capital_min_required') ?>">
                                                </div>
                                            </div>


                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Maximum Capital Limit</label>
                                                    <input type="number"
                                                        name="settings[capital_max_limit]"
                                                        class="form-control"
                                                        value="<?= get_setting('capital_max_limit') ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- END TAB CONTENT -->


                            </div>

                            <div class="panel-footer text-right">
                                <button type="submit"
                                    name="save_settings"
                                    class="btn bg-teal-400 btn-lg">
                                    <i class="icon-checkmark"></i>
                                    Save Settings
                                </button>
                            </div>
                        </div>
                    </form>

                </div>

            </div>

        </div>

    </div>

    </div>

    <?php require('includes/footer-text.php'); ?>
    <?php require('includes/footer.php'); ?>
</body>
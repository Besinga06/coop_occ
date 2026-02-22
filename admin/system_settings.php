<?php require('includes/header.php'); ?>
<?php require('db_connect.php'); ?>

<?php

if (
    !isset($_SESSION['is_login_yes'], $_SESSION['user_id'], $_SESSION['usertype'])
    || $_SESSION['is_login_yes'] != 'yes'
    || $_SESSION['usertype'] != 1
) {
    die("Unauthorized access.");
}

// Function to get setting
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

// Handle tab-specific saving
$success_tab = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_general'])) {
        foreach ($_POST['settings'] as $key => $value) {
            $stmt = $db->prepare("
                INSERT INTO system_settings (setting_key, setting_value)
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
            ");
            $stmt->bind_param("ss", $key, $value);
            $stmt->execute();
        }
        $success_tab = 'general';
    } elseif (isset($_POST['save_loan'])) {
        foreach ($_POST['settings'] as $key => $value) {
            $stmt = $db->prepare("
                INSERT INTO system_settings (setting_key, setting_value)
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
            ");
            $stmt->bind_param("ss", $key, $value);
            $stmt->execute();
        }
        $success_tab = 'loan';
    } elseif (isset($_POST['save_savings'])) {
        foreach ($_POST['settings'] as $key => $value) {
            $stmt = $db->prepare("
                INSERT INTO system_settings (setting_key, setting_value)
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
            ");
            $stmt->bind_param("ss", $key, $value);
            $stmt->execute();
        }
        $success_tab = 'savings';
    } elseif (isset($_POST['save_capital'])) {
        foreach ($_POST['settings'] as $key => $value) {
            $stmt = $db->prepare("
                INSERT INTO system_settings (setting_key, setting_value)
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
            ");
            $stmt->bind_param("ss", $key, $value);
            $stmt->execute();
        }
        $success_tab = 'capital';
    }
}

// Get loan types
$loanTypes = $db->query("
    SELECT *
    FROM loan_types
    ORDER BY created_at DESC
");


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loan_type_id'])) {
    $id = (int)$_POST['loan_type_id'];
    $name = trim($_POST['loan_type_name']);
    $interest = floatval($_POST['interest_rate']);
    $term_value = (int)$_POST['term_value'];
    $term_unit = trim($_POST['term_unit']); // must be 'days', 'weeks', 'months'
    $frequency = strtolower(trim($_POST['payment_frequency'])); // 'daily','weekly','monthly'
    $comaker = (int)$_POST['require_comaker']; // 0 or 1
    $status = trim($_POST['status']); // 'active' or 'inactive'

    $stmt = $db->prepare("
    UPDATE loan_types
    SET loan_type_name=?, interest_rate=?, term_value=?, term_unit=?, payment_frequency=?, require_comaker=?, status=?
    WHERE loan_type_id=?
");
    $stmt->bind_param("sdissisi", $name, $interest, $term_value, $term_unit, $frequency, $comaker, $status, $id);
    $stmt->execute();

    echo "<script>
        jQuery(function(){
            jQuery.jGrowl('Loan type updated successfully!', { header: 'Success', life: 3000, theme: 'bg-success-400' });
        });
    </script>";
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
            <a class="navbar-brand" href="index.php">
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
                            <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold">Dashboard</span> - System Settings</h4>
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

                    <div class="panel panel-white">
                        <div class="panel-heading">
                            <h6 class="panel-title">Cooperative System Configuration</h6>
                        </div>
                        <div class="panel-body">

                            <ul class="nav nav-tabs nav-tabs-solid bg-teal-400">
                                <li class="active"><a href="#general" data-toggle="tab"><i class="icon-cog"></i> General Overview</a></li>
                                <li><a href="#loan" data-toggle="tab"><i class="icon-cash"></i> Loan Settings</a></li>
                                <li><a href="#savings" data-toggle="tab"><i class="icon-piggy-bank"></i> Savings Settings</a></li>
                                <li><a href="#capital" data-toggle="tab"><i class="icon-stack"></i> Capital Share</a></li>
                            </ul>

                            <div class="tab-content">

                                <!-- GENERAL TAB -->
                                <div class="tab-pane active" id="general">
                                    <form method="POST">
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
                                                            <input type="number" class="form-control" name="settings[min_membership_months]" value="<?= get_setting('min_membership_months') ?>">
                                                        </div>
                                                        <div class="well well-sm">
                                                            <small>Minimum Savings Required</small>
                                                            <input type="number" class="form-control" name="settings[min_savings_required]" value="<?= get_setting('min_savings_required') ?>">
                                                        </div>
                                                        <div class="well well-sm">
                                                            <small>Minimum Capital Required</small>
                                                            <input type="number" class="form-control" name="settings[min_capital_required]" value="<?= get_setting('min_capital_required') ?>">
                                                        </div>
                                                        <div class="well well-sm">
                                                            <small>Require Comaker</small>
                                                            <select class="form-control" name="settings[require_comaker]">
                                                                <option value="1" <?= get_setting('require_comaker') == '1' ? 'selected' : '' ?>>Yes</option>
                                                                <option value="0" <?= get_setting('require_comaker') == '0' ? 'selected' : '' ?>>No</option>
                                                            </select>
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
                                                            <input type="number" class="form-control" name="settings[savings_min_balance]" value="<?= get_setting('savings_min_balance') ?>">
                                                        </div>
                                                        <div class="well well-sm">
                                                            <small>Interest Rate</small>
                                                            <input type="number" step="0.01" class="form-control" name="settings[savings_interest_rate]" value="<?= get_setting('savings_interest_rate') ?>">
                                                        </div>
                                                        <div class="well well-sm">
                                                            <small>Withdrawal Limit</small>
                                                            <input type="number" class="form-control" name="settings[savings_withdrawal_limit]" value="<?= get_setting('savings_withdrawal_limit') ?>">
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
                                                            <input type="number" class="form-control" name="settings[capital_min_required]" value="<?= get_setting('capital_min_required') ?>">
                                                        </div>
                                                        <div class="well well-sm">
                                                            <small>Maximum Capital</small>
                                                            <input type="number" class="form-control" name="settings[capital_max_limit]" value="<?= get_setting('capital_max_limit') ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="text-right">
                                            <button type="submit" name="save_general" class="btn bg-teal-400 btn-lg"><i class="icon-checkmark"></i> Save General</button>
                                        </div>

                                        <
                                            </form>
                                </div>

                                <!-- LOAN TAB -->
                                <div class="tab-pane" id="loan">
                                    <form method="POST">
                                        <br>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Minimum Membership Months</label>
                                                    <input type="number" name="settings[min_membership_months]" class="form-control" value="<?= get_setting('min_membership_months') ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Minimum Savings Required</label>
                                                    <input type="number" name="settings[min_savings_required]" class="form-control" value="<?= get_setting('min_savings_required') ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Minimum Capital Required</label>
                                                    <input type="number" name="settings[min_capital_required]" class="form-control" value="<?= get_setting('min_capital_required') ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Require Comaker</label>
                                                    <select name="settings[require_comaker]" class="form-control">
                                                        <option value="1" <?= get_setting('require_comaker') == '1' ? 'selected' : '' ?>>Yes</option>
                                                        <option value="0" <?= get_setting('require_comaker') == '0' ? 'selected' : '' ?>>No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <h5 class="text-semibold">Loan Charges Configuration</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Processing Fee Type</label>
                                                    <select name="settings[loan_processing_fee_type]" class="form-control">
                                                        <option value="percent" <?= get_setting('loan_processing_fee_type') == 'percent' ? 'selected' : '' ?>>Percent (%)</option>
                                                        <option value="fixed" <?= get_setting('loan_processing_fee_type') == 'fixed' ? 'selected' : '' ?>>Fixed Amount (₱)</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Processing Fee Value</label>
                                                    <input type="number" step="0.01" name="settings[loan_processing_fee_value]" class="form-control" value="<?= get_setting('loan_processing_fee_value') ?>">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Penalty Type</label>
                                                    <select name="settings[loan_penalty_type]" class="form-control">
                                                        <option value="percent" <?= get_setting('loan_penalty_type') == 'percent' ? 'selected' : '' ?>>Percent (%)</option>
                                                        <option value="fixed" <?= get_setting('loan_penalty_type') == 'fixed' ? 'selected' : '' ?>>Fixed Amount (₱)</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Penalty Value</label>
                                                    <input type="number" step="0.01" name="settings[loan_penalty_value]" class="form-control" value="<?= get_setting('loan_penalty_value') ?>">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Penalty Frequency</label>
                                                    <select name="settings[loan_penalty_frequency]" class="form-control">
                                                        <option value="daily" <?= get_setting('loan_penalty_frequency') == 'daily' ? 'selected' : '' ?>>Daily</option>
                                                        <option value="weekly" <?= get_setting('loan_penalty_frequency') == 'weekly' ? 'selected' : '' ?>>Weekly</option>
                                                        <option value="monthly" <?= get_setting('loan_penalty_frequency') == 'monthly' ? 'selected' : '' ?>>Monthly</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Grace Period (Days)</label>
                                                    <input type="number" name="settings[loan_grace_period_days]" class="form-control" value="<?= get_setting('loan_grace_period_days') ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-right">
                                            <button type="submit" name="save_loan" class="btn bg-teal-400 btn-lg"><i class="icon-checkmark"></i> Save Loan Settings</button>
                                        </div>


                                    </form>

                                    <hr>
                                    <h5>Loan Types</h5>
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Interest</th>
                                                <th>Term</th>
                                                <th>Payment</th>
                                                <th>Comaker</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($loanTypes->num_rows > 0): ?>
                                                <?php while ($row = $loanTypes->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($row['loan_type_name']) ?></td>
                                                        <td align="center"><?= $row['interest_rate'] ?>%</td>
                                                        <td align="center"><?= $row['term_value'] ?> <?= ucfirst($row['term_unit']) ?></td>
                                                        <td align="center"><?= ucfirst($row['payment_frequency']) ?></td>
                                                        <td align="center"><?= $row['require_comaker'] ? 'YES' : 'NO' ?></td>
                                                        <td align="center"><?= ucfirst($row['status']) ?></td>
                                                        <td align="center">
                                                            <button type="button" class="btn btn-primary btn-xs editLoan"
                                                                data-id="<?= $row['loan_type_id'] ?>"
                                                                data-name="<?= htmlspecialchars($row['loan_type_name']) ?>"
                                                                data-interest="<?= $row['interest_rate'] ?>"
                                                                data-termvalue="<?= $row['term_value'] ?>"
                                                                data-termunit="<?= $row['term_unit'] ?>"
                                                                data-frequency="<?= $row['payment_frequency'] ?>"
                                                                data-comaker="<?= $row['require_comaker'] ?>"
                                                                data-status="<?= $row['status'] ?>">Update</button>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">No loan types found</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- SAVINGS TAB -->
                                <div class="tab-pane" id="savings">
                                    <form method="POST">
                                        <br>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Minimum Balance</label>
                                                    <input type="number" name="settings[savings_min_balance]" class="form-control" value="<?= get_setting('savings_min_balance') ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Interest Rate (%)</label>
                                                    <input type="number" step="0.01" name="settings[savings_interest_rate]" class="form-control" value="<?= get_setting('savings_interest_rate') ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Withdrawal Limit per day</label>
                                                    <input type="number" name="settings[savings_withdrawal_limit]" class="form-control" value="<?= get_setting('savings_withdrawal_limit') ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-right">
                                            <button type="submit" name="save_savings" class="btn bg-teal-400 btn-lg"><i class="icon-checkmark"></i> Save Savings Settings</button>
                                        </div>


                                    </form>
                                </div>

                                <!-- CAPITAL TAB -->
                                <div class="tab-pane" id="capital">
                                    <form method="POST">
                                        <br>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Minimum Capital Required</label>
                                                    <input type="number" name="settings[capital_min_required]" class="form-control" value="<?= get_setting('capital_min_required') ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Maximum Capital Limit</label>
                                                    <input type="number" name="settings[capital_max_limit]" class="form-control" value="<?= get_setting('capital_max_limit') ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-right">
                                            <button type="submit" name="save_capital" class="btn bg-teal-400 btn-lg"><i class="icon-checkmark"></i> Save Capital Settings</button>
                                        </div>


                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>

                </div> <!-- content -->
            </div> <!-- content-wrapper -->
        </div> <!-- page-content -->
    </div> <!-- page-container -->

    <!-- Edit Loan Modal -->
    <div id="editLoanModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <form id="editLoanForm" method="POST" data-toggle="validator">
                <div class="modal-content">
                    <div class="modal-header bg-teal-400">
                        <h5 class="modal-title">Edit Loan Type</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-bodys">

                        <input type="hidden" name="loan_type_id" id="edit_id">

                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="loan_type_name" id="edit_name" required>
                        </div>

                        <div class="form-group">
                            <label>Interest (%)</label>
                            <input type="number" step="0.01" class="form-control" name="interest_rate" id="edit_interest" required>
                        </div>

                        <div class="form-group">
                            <label>Term Value</label>
                            <input type="number" class="form-control" name="term_value" id="edit_termvalue" required>
                        </div>

                        <div class="form-group">
                            <label>Term Unit</label>
                            <select class="form-control" name="term_unit" id="edit_termunit" required>
                                <option value="days">Days</option>
                                <option value="weeks">Weeks</option>
                                <option value="months">Months</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Payment Frequency</label>
                            <select class="form-control" name="payment_frequency" id="edit_frequency">
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                            </select>

                        </div>

                        <div class="form-group">
                            <label>Require Comaker</label>
                            <select class="form-control" name="require_comaker" id="edit_comaker">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" name="status" id="edit_status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn bg-teal-400">Save Changes</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php require('includes/footer.php'); ?>

    <?php require('includes/footer-text.php'); ?>
    <script src="../assets/js/plugins/notifications/jgrowl.min.js"></script>
    <script src="../js/validator.min.js"></script>

    <script>
        $(document).ready(function() {

            // Prefill modal
            $('.editLoan').click(function() {
                $('#edit_id').val($(this).data('id'));
                $('#edit_name').val($(this).data('name'));
                $('#edit_interest').val($(this).data('interest'));
                $('#edit_termvalue').val($(this).data('termvalue'));
                $('#edit_termunit').val($(this).data('termunit'));
                $('#edit_frequency').val($(this).data('frequency'));
                $('#edit_comaker').val($(this).data('comaker'));
                $('#edit_status').val($(this).data('status'));
                $('#editLoanModal').modal('show');
            });

            // Validator + submit via JS
            $('#editLoanForm').validator().on('submit', function(e) {
                if (!e.isDefaultPrevented()) {
                    e.preventDefault(); // stop normal submission

                    $.post('<?= $_SERVER['PHP_SELF'] ?>', $(this).serialize(), function(response) {
                        jQuery.jGrowl('Loan type updated successfully!', {
                            header: 'Success',
                            life: 3000,
                            theme: 'bg-success-400'
                        });
                        $('#editLoanModal').modal('hide');
                        // Optionally, reload table via AJAX instead of full page
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    });
                } else {
                    jQuery.jGrowl('Please fill in all required fields correctly.', {
                        header: 'Error',
                        life: 3000,
                        theme: 'bg-danger-400'
                    });
                    return false;
                }
            });

            // Optional: jGrowl for general settings save
            <?php if ($success_tab): ?>
                jQuery.jGrowl('<?= ucfirst($success_tab) ?> settings saved successfully!', {
                    header: 'Success',
                    life: 3000,
                    theme: 'bg-success-400'
                });
            <?php endif; ?>
        });
    </script>
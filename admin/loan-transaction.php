<?php
require('includes/header.php');


$loanQuery = "
    SELECT 
        l.loan_app_id,
        c.name AS member_name,
        la.approved_amount,
        la.approved_term,
        la.interest_rate,
        d.amount_released,
        d.release_date,
        d.mode AS release_mode,
        t.total_payable,
        t.disbursement_date,
        t.status AS loan_status
    FROM tbl_loan_application l
    JOIN tbl_customer c ON l.customer_id = c.cust_id
    JOIN tbl_loan_approval la ON la.loan_app_id = l.loan_app_id
    JOIN tbl_loan_disbursement d ON d.loan_app_id = l.loan_app_id
    JOIN tbl_loan_transactions t ON l.loan_app_id = t.loan_app_id
    ORDER BY t.disbursement_date DESC
";

$loans_active = $db->query($loanQuery);
$loans_paid   = $db->query($loanQuery);

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
    <!-- Main navbar -->
    <div class="navbar navbar-inverse bg-teal-400 navbar-fixed-top">
        <div class="navbar-header">
            <a class="navbar-brand" href="index.php"><img style="height: 65px!important" src="../images/your_logo.png" alt=""><span>OCC Cooperative</span></a>
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
                            <h4><i class="icon-cash3 position-left"></i> Active Loans</h4>
                        </div>
                    </div>
                    <div class="breadcrumb-line">
                        <ul class="breadcrumb">
                            <li><a href="loan.php"><i class="icon-cash3"></i> Loan</a></li>
                            <li class="active">Active Loans </li>
                        </ul>
                    </div>
                </div>

                <div class="content">
                    <!-- Active Loans -->
                    <div class="panel panel-white border-top-xlg border-top-primary">
                        <div class="panel-heading">
                            <h6 class="panel-title">
                                <i class="icon-coins text-primary position-left"></i> Active Loans
                            </h6>
                        </div>
                        <div class="panel-body panel-theme">
                            <table class="table table-bordered table-hover" id="loan-ledger">
                                <thead>
                                    <tr>
                                        <th>Member</th>
                                        <th>Approved Amount</th>
                                        <th>Term</th>
                                        <th>Interest</th>
                                        <th>Total Payable</th>
                                        <th>Paid</th>
                                        <th>Balance</th>
                                        <th>Monthly Due</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($loan = $loans_active->fetch_assoc()): ?>
                                        <?php
                                        $loanId = (int)$loan['loan_app_id'];


                                        $stmt_paid = $db->prepare("SELECT SUM(amount_paid) AS total_paid FROM tbl_loan_repayment WHERE loan_app_id = ?");
                                        $stmt_paid->bind_param("i", $loanId);
                                        $stmt_paid->execute();
                                        $paid = $stmt_paid->get_result()->fetch_assoc()['total_paid'] ?? 0;

                                        $balance = $loan['total_payable'] - $paid;
                                        if ($balance <= 0) continue;
                                        $stmt_schedule = $db->prepare("
                                        SELECT schedule_id, due_date, principal_due, interest_due, penalty_due, total_due
                                        FROM tbl_loan_schedule
                                        WHERE loan_app_id = ? AND status = 'unpaid'
                                        ORDER BY due_date ASC
                                        LIMIT 1
                                          ");
                                        $stmt_schedule->bind_param("i", $loanId);
                                        $stmt_schedule->execute();
                                        $nextSchedule = $stmt_schedule->get_result()->fetch_assoc();

                                        $monthly_due = $nextSchedule['total_due'] ?? 0;
                                        $schedule_id = $nextSchedule['schedule_id'] ?? null;

                                        ?>
                                        <tr>
                                            <td><b><?= htmlspecialchars($loan['member_name']) ?></b></td>
                                            <td class="text-right"><?= number_format($loan['approved_amount'], 2) ?></td>
                                            <td><?= (int)$loan['approved_term'] ?> mos</td>
                                            <td><?= number_format($loan['interest_rate']) ?>%</td>
                                            <td class="text-right"><?= number_format($loan['total_payable'], 2) ?></td>
                                            <td class="text-success text-right"><?= number_format($paid, 2) ?></td>
                                            <td class="text-danger text-right"><?= number_format($balance, 2) ?></td>
                                            <td class="text-danger text-right"><?= number_format($monthly_due, 2) ?></td>
                                            <td>
                                                <a href="loan_ledger_detail.php?loan_id=<?= $loanId ?>" class="btn btn-xxs btn-primary">View</a>
                                                <button class="btn btn-xxs btn-success"
                                                    data-toggle="modal"
                                                    data-target="#modal-add-payment"
                                                    data-loan="<?= $loanId ?>"
                                                    data-schedule="<?= $schedule_id ?>"
                                                    data-balance="<?= $balance ?>"
                                                    data-principal="<?= $nextSchedule['principal_due'] ?? 0 ?>"
                                                    data-interest="<?= $nextSchedule['interest_due'] ?? 0 ?>"
                                                    data-penalty="<?= $nextSchedule['penalty_due'] ?? 0 ?>"
                                                    data-monthly="<?= $monthly_due ?>"
                                                    data-member="<?= htmlspecialchars($loan['member_name']) ?>">Pay</button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>

                                </tbody>

                            </table>
                        </div>
                    </div>


                    <div class="panel panel-white border-top-xlg border-top-success">
                        <div class="panel-heading">
                            <h6 class="panel-title">
                                <i class="icon-checkmark text-success position-left"></i> Fully Paid Loans
                            </h6>
                        </div>
                        <div class="panel-body panel-theme">
                            <table class="table table-bordered table-hover" id="fully-paid-loans">
                                <thead>
                                    <tr>
                                        <th>Member</th>
                                        <th>Approved Amount</th>
                                        <th>Term</th>
                                        <th>Interest</th>
                                        <th>Total Payable</th>
                                        <th>Paid</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($loan = $loans_paid->fetch_assoc()): ?>
                                        <?php
                                        $loanId = (int)$loan['loan_app_id'];


                                        $stmt_paid = $db->prepare("SELECT SUM(amount_paid) AS total_paid FROM tbl_loan_repayment WHERE loan_app_id = ?");
                                        $stmt_paid->bind_param("i", $loanId);
                                        $stmt_paid->execute();
                                        $paid = $stmt_paid->get_result()->fetch_assoc()['total_paid'] ?? 0;

                                        $balance = $loan['total_payable'] - $paid;
                                        if ($balance > 0) continue;
                                        ?>
                                        <tr>
                                            <td><b><?= htmlspecialchars($loan['member_name']) ?></b></td>
                                            <td class="text-right"><?= number_format($loan['approved_amount'], 2) ?></td>
                                            <td><?= (int)$loan['approved_term'] ?> mos</td>
                                            <td><?= number_format($loan['interest_rate']) ?>%</td>
                                            <td class="text-right"><?= number_format($loan['total_payable'], 2) ?></td>
                                            <td class="text-success text-right"><?= number_format($paid, 2) ?></td>
                                            <td><span class="label label-success">Fully Paid</span></td>
                                            <td>
                                                <a href="loan_ledger_detail.php?loan_id=<?= $loanId ?>" class="btn btn-xxs btn-primary">View</a>
                                            </td>
                                        </tr>

                                    <?php endwhile; ?>

                                </tbody>

                            </table>
                        </div>
                    </div>



                    <?php require('includes/footer-text.php'); ?>
                </div>
            </div>
        </div>

        <?php require('includes/footer.php'); ?>

        <!-- Add Payment Modal -->
        <div id="modal-add-payment" class="modal fade">
            <div class="modal-dialog">
                <form id="form-payment">
                    <div class="modal-content">
                        <div class="modal-header bg-teal-400">
                            <h5 class="modal-title">Add Repayment</h5>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="loan_app_id" name="loan_app_id">
                            <input type="hidden" id="schedule_id" name="schedule_id">
                            <input type="hidden" id="full_balance" name="full_balance">
                            <input type="hidden" id="principal_due" name="principal_due">
                            <input type="hidden" id="monthly_due" name="monthly_due">
                            <input type="hidden" id="interest_due" name="interest_due">
                            <input type="hidden" id="penalty_due" name="penalty_due">

                            <div id="payment-msg"></div>

                            <div class="form-group">
                                <label>Payment Type</label>
                                <select id="payment_type" class="form-control" required>
                                    <option value="full">Full Payment</option>
                                    <option value="monthly">Monthly Due</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Loaner</label>
                                <input type="text" id="loaner_name" class="form-control" readonly>
                            </div>

                            <div class="form-group">
                                <label>Amount Paid</label>
                                <input type="number" id="amount_paid" name="amount_paid" class="form-control" required step="0.01" min="0.01">
                                <small class="text-muted">
                                    Full Balance: ₱<span id="lbl-full-balance">0.00</span>
                                    Monthly Due: ₱<span id="lbl-monthly-due">0.00</span>
                                </small>
                            </div>

                            <div class="form-group" hidden>
                                <label>Payment Method</label>
                                <select class="form-control" name="payment_method" required>
                                    <option value="cash">Cash</option>
                                    <option value="bank">Bank</option>
                                    <option value="check">Check</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                            <button type="submit" id="btn-payment" class="btn btn-success">Confirm Payment</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Receipt Modal -->
        <div id="modal-receipt" class="modal fade">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-teal-400">
                        <h5 class="modal-title">Payment Receipt</h5>
                    </div>
                    <div class="modal-body" id="receipt-content"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" id="btn-print-receipt">Print</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="../assets/js/plugins/tables/datatables/datatables.min.js"></script>
        <script src="../js/validator.min.js"></script>
        <script src="../assets/js/plugins/notifications/jgrowl.min.js"></script>


        <script>
            $(document).ready(function() {
                $('#loan-ledger').DataTable({
                    "order": [
                        [0, "asc"]
                    ],
                    "pageLength": 5
                });
                $('#fully-paid-loans').DataTable({
                    "order": [
                        [0, "desc"]
                    ],
                    "pageLength": 5
                });

                // Show Add Payment Modal
                $('#modal-add-payment').on('show.bs.modal', function(e) {
                    var button = $(e.relatedTarget);
                    var loanId = button.data('loan');
                    var scheduleId = button.data('schedule');
                    var balance = parseFloat(button.data('balance'));
                    var monthlyDue = parseFloat(button.data('monthly')) || 0;
                    var principal = parseFloat(button.data('principal'));
                    var interest = parseFloat(button.data('interest'));
                    var penalty = parseFloat(button.data('penalty') || 0);
                    var member = button.data('member');

                    $('#loan_app_id').val(loanId);
                    $('#schedule_id').val(scheduleId);
                    $('#full_balance').val(balance.toFixed(2));
                    $('#principal_due').val(principal.toFixed(2));
                    $('#interest_due').val(interest.toFixed(2));
                    $('#penalty_due').val(penalty.toFixed(2));
                    $('#lbl-full-balance').text(balance.toFixed(2));
                    $('#lbl-monthly-due').text(monthlyDue.toFixed(2));
                    $('#amount_paid').val(balance.toFixed(2));
                    $('#loaner_name').val(member);
                    $('#payment_type').val('full');

                    $('#payment_type').off('change').on('change', function() {
                        var type = $(this).val();
                        if (type === 'full') $('#amount_paid').val(balance.toFixed(2));
                        else if (type === 'monthly') $('#amount_paid').val(monthlyDue.toFixed(2));
                        else $('#amount_paid').val('');
                    });

                    $('#amount_paid').off('input').on('input', function() {
                        var val = parseFloat($(this).val()) || 0;
                        if (val > balance) {
                            $(this).val(balance.toFixed(2));
                            $.jGrowl('Custom payment cannot exceed full balance ₱' + balance.toFixed(2), {
                                header: 'Validation Error',
                                theme: 'bg-danger'
                            });
                        }
                    });
                });

                // Form submit
                $('#form-payment').on('submit', function(e) {
                    e.preventDefault();
                    var balance = parseFloat($('#full_balance').val()) || 0;
                    var amountPaid = parseFloat($('#amount_paid').val()) || 0;
                    var paymentType = $('#payment_type').val();
                    if (paymentType === 'custom' && amountPaid > balance) {
                        $.jGrowl('Error: Custom payment cannot exceed full balance ₱' + balance.toFixed(2), {
                            header: 'Validation Error',
                            theme: 'bg-danger'
                        });
                        return;
                    }
                    $('#btn-payment').prop('disabled', true);

                    $.ajax({
                        url: '../transaction.php',
                        method: 'POST',
                        data: $(this).serialize() + '&save_payment=1',
                        success: function(resp) {
                            $('#btn-payment').prop('disabled', false);
                            try {
                                var json = JSON.parse(resp);
                                if (json.success) {
                                    // Show success notification first
                                    $.jGrowl('Payment successful!', {
                                        header: 'Success',
                                        theme: 'bg-success'
                                    });

                                    $('#modal-add-payment').modal('hide');


                                    setTimeout(function() {
                                        $('#receipt-content').load('loan_payment_receipt.php?receipt=' + json.receipt, function() {
                                            $('#modal-receipt').modal('show');
                                        });
                                    }, 500);
                                } else {
                                    $.jGrowl('Error: ' + json.message, {
                                        header: 'Error',
                                        theme: 'bg-danger'
                                    });
                                }
                            } catch (e) {
                                $.jGrowl('Invalid response: ' + resp, {
                                    header: 'Error',
                                    theme: 'bg-danger'
                                });
                            }
                        },
                        error: function(xhr, status, err) {
                            $('#btn-payment').prop('disabled', false);
                            $.jGrowl('Request failed: ' + err, {
                                header: 'Error',
                                theme: 'bg-danger'
                            });
                        }
                    });
                });

                // Print receipt
                $('#btn-print-receipt').click(function() {
                    var printContents = document.getElementById('receipt-content').innerHTML;
                    var originalContents = document.body.innerHTML;
                    document.body.innerHTML = printContents;
                    window.print();
                    document.body.innerHTML = originalContents;
                    location.reload();
                });
            });
        </script>
</body>
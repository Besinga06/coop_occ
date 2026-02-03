<?php require('includes/header.php'); ?>

<?php
if (!isset($_GET['loan_id'])) {
    die("Invalid request: Missing loan ID.");
}
$loan_id = (int)$_GET['loan_id'];

// Prepare the query
$stmt = $db->prepare("
    SELECT l.loan_app_id,
           c.name AS member_name,
           la.approved_amount,
           la.approved_term,
           la.interest_rate,
           d.amount_released,
           d.release_date,
           d.mode AS release_mode,
           t.total_payable,
           t.status
    FROM tbl_loan_application l
    JOIN tbl_customer c ON l.customer_id = c.cust_id
    JOIN tbl_loan_approval la ON la.loan_app_id = l.loan_app_id
    LEFT JOIN tbl_loan_disbursement d ON d.loan_app_id = l.loan_app_id
    LEFT JOIN tbl_loan_transactions t ON t.loan_app_id = l.loan_app_id
    WHERE l.loan_app_id = ?
");
$stmt->bind_param("i", $loan_id);
$stmt->execute();
$result = $stmt->get_result();
$loan = $result->fetch_assoc();
$stmt->close();

if (!$loan) {
    die("Loan not found.");
}
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
            <a class="navbar-brand" href="index.php"><img style="height: 40px!important" src="../images/your_logo.png" alt=""><span>OCC Cooperative</span></a>
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
                            <h4><i class="icon-books position-left"></i> Loan Info -<?= $loan['member_name']; ?></h4>
                        </div>
                    </div>
                    <div class="breadcrumb-line">
                        <ul class="breadcrumb">
                            <li><a href="loan-transaction.php"><i class="icon-coins"></i> Active Loans</a></li>
                            <li class="active">Info</li>
                        </ul>
                    </div>
                </div>

                <div class="content">
                    <div class="panel panel-flat">
                        <div class="panel-body">
                            <div class="tabbable">
                                <ul class="nav nav-tabs bg-slate nav-justified">
                                    <li class="active"><a href="#information" data-toggle="tab">Information</a></li>
                                    <li><a href="#ledger" data-toggle="tab">Payment History</a></li>
                                    <li><a href="#penalties" data-toggle="tab">Penalties</a></li>
                                </ul>

                                <div class="tab-content">

                                    <!-- INFORMATION TAB -->
                                    <div class="tab-pane active" id="information">
                                        <div class="panel panel-white border-top-xlg border-top-teal-400">
                                            <div class="panel-heading">
                                                <h6 class="panel-title"><i class="icon-list position-left text-teal-400"></i> Information</h6>
                                            </div>
                                            <div class="panel-body">
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <td>Member</td>
                                                        <td><?= htmlspecialchars($loan['member_name']); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Approved Amount</td>
                                                        <td>₱<?= number_format($loan['approved_amount'], 2); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Term</td>
                                                        <td><?= (int)$loan['approved_term']; ?> months</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Interest Rate</td>
                                                        <td><?= number_format($loan['interest_rate'], 2); ?>%</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Released Amount</td>
                                                        <td>
                                                            <?= $loan['amount_released'] ? '₱' . number_format($loan['amount_released'], 2) . ' (' . $loan['release_mode'] . ')' : '-' ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Release Date</td>
                                                        <td><?= $loan['release_date'] ?? '-' ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Total Payable</td>
                                                        <td><?= $loan['total_payable'] ? '₱' . number_format($loan['total_payable'], 2) : '-' ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Status</td>
                                                        <td><?= ucfirst($loan['status']); ?></td>
                                                    </tr>
                                                </table>

                                                <?php if ($loan['status'] == 'pending'): ?>
                                                    <form id="form-disburse" method="post">
                                                        <input type="hidden" name="loan_id" value="<?= $loan['loan_app_id'] ?>">
                                                        <div class="form-group">
                                                            <label>Amount to Disburse</label>
                                                            <input type="number" name="amount_released" class="form-control" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Mode</label>
                                                            <select name="mode" class="form-control">
                                                                <option value="cash">Cash</option>
                                                                <option value="bank_transfer">Bank Transfer</option>
                                                            </select>
                                                        </div>
                                                        <button type="submit" name="disburse_loan" class="btn btn-success">Disburse Loan</button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- PAYMENT HISTORY TAB -->
                                    <div class="tab-pane" id="ledger">
                                        <div class="panel panel-white border-top-xlg border-top-teal-400">
                                            <div class="panel-heading">
                                                <h6 class="panel-title"><i class="icon-list position-left text-teal-400"></i> Payment History</h6>
                                            </div>
                                            <div class="panel-body">
                                                <table class="table datatable-button-html5-basic table-hover table-bordered">
                                                    <thead>
                                                        <tr style="background: #eee">
                                                            <th>Date</th>
                                                            <th>Receipt</th>
                                                            <th>Description</th>
                                                            <th>Debit (Due)</th>
                                                            <th>Credit (Payment)</th>
                                                            <th>Balance</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $ledger = [];
                                                        $balance = round($loan['total_payable'] ?? $loan['amount_released'] ?? 0, 2);

                                                        // Add initial loan disbursement
                                                        $ledger[] = [
                                                            'date' => $loan['release_date'] ?? date('Y-m-d'),
                                                            'type' => 'disbursement',
                                                            'desc' => 'Loan Disbursed<br>
               <small>Amount: ₱' . number_format($loan['amount_released'], 2) . ' | Total Payable: ₱' . number_format($loan['total_payable'], 2) . '</small>',
                                                            'debit' => 0,
                                                            'credit' => 0,
                                                            'receipt' => ''
                                                        ];

                                                        // Fetch payments only using MySQLi prepared statement
                                                        $stmt_pay = $db->prepare("
    SELECT payment_date, amount_paid, principal_component, interest_component, payment_method, receipt_number
    FROM tbl_loan_repayment
    WHERE loan_app_id = ?
    ORDER BY payment_date ASC
");
                                                        $stmt_pay->bind_param("i", $loan_id);
                                                        $stmt_pay->execute();
                                                        $result_pay = $stmt_pay->get_result();

                                                        while ($r = $result_pay->fetch_assoc()) {
                                                            $ledger[] = [
                                                                'date' => $r['payment_date'],
                                                                'type' => 'payment',
                                                                'desc' => 'Payment - ' . $r['payment_method'] . '<br>
                   <small>Principal: ₱' . number_format($r['principal_component'], 2) . ' | Interest+Penalty: ₱' . number_format($r['interest_component'], 2) . '</small>',
                                                                'debit' => 0,
                                                                'credit' => round($r['amount_paid'], 2),
                                                                'receipt' => $r['receipt_number']
                                                            ];
                                                        }

                                                        $stmt_pay->close();

                                                        // Sort ledger by date, disbursement first
                                                        usort($ledger, function ($a, $b) {
                                                            $diff = strtotime($a['date']) - strtotime($b['date']);
                                                            if ($diff === 0) {
                                                                $order = ['disbursement' => 0, 'payment' => 1];
                                                                return $order[$a['type']] - $order[$b['type']];
                                                            }
                                                            return $diff;
                                                        });

                                                        // Display ledger with running balance
                                                        foreach ($ledger as $row) {
                                                            if ($row['type'] === 'payment') {
                                                                $balance = round($balance - $row['credit'], 2);
                                                            }

                                                            // Receipt HTML (clickable)
                                                            $receipt_html = '';
                                                            if ($row['type'] === 'payment' && !empty($row['receipt'])) {
                                                                $receipt_html = '<a href="javascript:void(0);" class="view-receipt" data-receipt="' . htmlspecialchars($row['receipt']) . '">' . htmlspecialchars($row['receipt']) . '</a>';
                                                            }

                                                            echo "<tr>
        <td>" . date("M d, Y", strtotime($row['date'])) . "</td>
        <td>" . $receipt_html . "</td>
        <td>" . $row['desc'] . "</td>
        <td style='text-align:right'>-</td>
        <td style='text-align:right'>" . ($row['credit'] ? '₱' . number_format($row['credit'], 2) : '-') . "</td>
        <td style='text-align:right'><b>₱" . number_format($balance, 2) . "</b></td>
    </tr>";
                                                        }
                                                        ?>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- PENALTIES TAB -->
                                    <div class="tab-pane" id="penalties">
                                        <div class="panel panel-white border-top-xlg border-top-teal-400">
                                            <div class="panel-heading">
                                                <h6 class="panel-title"><i class="icon-list position-left text-teal-400"></i> Penalties</h6>
                                            </div>
                                            <div class="panel-body">
                                                <?php
                                                // Fetch penalties using MySQLi prepared statement
                                                $stmt_pen = $db->prepare("
    SELECT due_date, penalty_due, status 
    FROM tbl_loan_schedule 
    WHERE loan_app_id = ? AND penalty_due > 0 
    ORDER BY due_date ASC
");
                                                $stmt_pen->bind_param("i", $loan_id);
                                                $stmt_pen->execute();
                                                $result_pen = $stmt_pen->get_result();

                                                if ($result_pen->num_rows > 0) {
                                                    echo '<table class="table table-bordered">
            <thead>
                <tr>
                    <th>Due Date</th>
                    <th>Penalty</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>';

                                                    while ($p = $result_pen->fetch_assoc()) {
                                                        echo "<tr>
                <td>" . date("M d, Y", strtotime($p['due_date'])) . "</td>
                <td>₱" . number_format($p['penalty_due'], 2) . "</td>
                <td>" . htmlspecialchars($p['status']) . "</td>
              </tr>";
                                                    }

                                                    echo '</tbody></table>';
                                                } else {
                                                    echo "<p>No penalties recorded yet.</p>";
                                                }

                                                $stmt_pen->close();
                                                ?>

                                            </div>
                                        </div>
                                    </div>

                                </div> <!-- tab-content -->
                            </div> <!-- tabbable -->


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

                        </div>
                    </div>
                </div> <!-- content -->

                <?php require('includes/footer-text.php'); ?>

            </div>
        </div>
    </div>

    <?php require('includes/footer.php'); ?>

    <script src="../assets/js/plugins/tables/datatables/datatables.min.js"></script>
    <script src="../js/validator.min.js"></script>
    <script>
        $(document).ready(function() {
            // Open receipt modal when clicking a receipt number
            $('.view-receipt').on('click', function() {
                var receiptNumber = $(this).data('receipt');

                // Load receipt content via AJAX
                $('#receipt-content').load('loan_payment_receipt.php?receipt=' + receiptNumber, function() {
                    $('#modal-receipt').modal('show');
                });
            });

            // Print receipt
            $('#btn-print-receipt').click(function() {
                var printContents = document.getElementById('receipt-content').innerHTML;
                var originalContents = document.body.innerHTML;
                document.body.innerHTML = printContents;
                window.print();
                document.body.innerHTML = originalContents;
                location.reload(); // reload to restore JS events
            });
        });
    </script>
</body>

</html>
<?php
require('db_connect.php');

if (!isset($_GET['receipt'])) {
    die("Invalid request: Missing receipt number.");
}

$receipt_number = $_GET['receipt'];

// Fetch repayment info
$payment = $db->querySingle("
    SELECT r.loan_app_id,
           r.schedule_id,
           r.amount_paid,
           r.principal_component,
           r.interest_component,
           r.payment_method,
           r.receipt_number,
           s.due_date,
           l.requested_amount,
           c.name AS member_name,
           c.address
    FROM tbl_loan_repayment r
    JOIN tbl_loan_schedule s ON s.schedule_id = r.schedule_id
    JOIN tbl_loan_application l ON l.loan_app_id = r.loan_app_id
    JOIN tbl_customer c ON c.cust_id = l.customer_id
    WHERE r.receipt_number = '$receipt_number'
", true);

if (!$payment) {
    die("Receipt not found.");
}
?>

<div class="receipt-div" id="print-receipt">
    <div class="text-center">
        <p class="title"><b>LOURDES FARMERS MULTI-PURPOSE COOPERATIVE</b></p>
        <p>Brgy Lourdes, Alubijid Mis'Or</p>
        <p>Loan Payment Receipt</p>
        <hr>
    </div>

    <table style="width:100%; margin-bottom:10px;">
        <tr>
            <td><b>Receipt No:</b> <?= htmlspecialchars($payment['receipt_number']) ?></td>
            <td class="text-right"><b>Payment Date:</b> <?= date('Y-m-d H:i:s') ?></td>
        </tr>
        <tr>
            <td><b>Member:</b> <?= htmlspecialchars($payment['member_name']) ?></td>
            <td class="text-right"><b>Address:</b> <?= htmlspecialchars($payment['address']) ?></td>
        </tr>
    </table>

    <table class="table-loan" style="width:100%; border-collapse: collapse;" border="1">
        <thead>
            <tr>
                <th>Loan Amount</th>
                <th>Principal Paid</th>
                <th>Interest + Penalty Paid</th>
                <th>Total Paid</th>
                <th>Payment Method</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="right"><?= number_format($payment['requested_amount'], 2) ?></td>
                <td align="right"><?= number_format($payment['principal_component'], 2) ?></td>
                <td align="right"><?= number_format($payment['interest_component'], 2) ?></td>
                <td align="right"><b><?= number_format($payment['amount_paid'], 2) ?></b></td>
                <td align="center"><?= htmlspecialchars(ucfirst($payment['payment_method'])) ?></td>
            </tr>
        </tbody>
    </table>

    <br><br>
    <table style="width:100%;">
        <tr>
            <td>
                <p>Issued by:</p><br><br>
                _________________________<br>
                Authorized Signature
            </td>
            <td align="right">
                <p>Received by:</p><br><br>
                _________________________<br>
                Borrower Signature
            </td>
        </tr>
    </table>
</div>

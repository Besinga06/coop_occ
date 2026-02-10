<?php
require('db_connect.php');

if (!isset($_GET['loan_id'])) {
    die("Invalid request: Missing loan ID.");
}
$loan_id = (int) $_GET['loan_id'];

// MySQL query
$query = "
    SELECT l.loan_app_id,
           c.name AS member_name,
           c.address,
           la.approved_amount,
           la.approved_term,
           la.interest_rate,
           la.approval_date
    FROM tbl_loan_application l
    JOIN tbl_customer c ON l.customer_id = c.cust_id
    JOIN tbl_loan_approval la ON la.loan_app_id = l.loan_app_id
    WHERE l.loan_app_id = ?
";

// Prepare and execute
$stmt = $db->prepare($query);
$stmt->bind_param("i", $loan_id);
$stmt->execute();
$result = $stmt->get_result();
$loan = $result->fetch_assoc();

if (!$loan) {
    die("Loan not found.");
}

// Variables
$loan_number = "LN-" . str_pad($loan['loan_app_id'], 6, "0", STR_PAD_LEFT);
$principal = $loan['approved_amount'];
$interest_rate = $loan['interest_rate'];
$term = $loan['approved_term'];
$approval_date = $loan['approval_date'];

// Flat-rate interest
$total_interest = ($principal * ($interest_rate / 100)) * $term;
$total_payable = $principal + $total_interest;
$monthly_payment = $total_payable / $term;

// For schedule
$schedule = [];
$next_due = new DateTime($approval_date);
for ($i = 1; $i <= $term; $i++) {
    $next_due->modify('+1 month');
    $schedule[] = [
        'no' => $i,
        'due_date' => $next_due->format('Y-m-d'),
        'amount' => $monthly_payment
    ];
}
?>


<div class="receipt-div" id="print-receipt">
    <div class="text-center">
        <p class="title"><b>OCC COOPERATIVE</b></p>
        <p>Opol Community College Mis'Or</p>
        <p><b>Loan Approval Receipt</b></p>
        <hr>
    </div>

    <table style="width:100%; margin-bottom:10px;">
        <tr>
            <td><b>Loan No:</b> <?= $loan_number ?></td>
            <td class="text-right"><b>Date Approved:</b> <?= $approval_date ?></td>
        </tr>
        <tr>
            <td><b>Member:</b> <?= htmlspecialchars($loan['member_name']) ?></td>
            <td class="text-right"><b>Address:</b> <?= htmlspecialchars($loan['address']) ?></td>
        </tr>
    </table>

    <table class="table-loan" style="width:100%; border-collapse: collapse;" border="1">
        <thead>
            <tr>
                <th>Principal Amount</th>
                <th>Term (months)</th>
                <th>Interest Rate</th>
                <th>Total Interest</th>
                <th>Total Payable</th>
                <th>Monthly Payment</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="right"><?= number_format($principal, 2) ?></td>
                <td align="center"><?= $term ?></td>
                <td align="center"><?= $interest_rate ?>%</td>
                <td align="right"><?= number_format($total_interest, 2) ?></td>
                <td align="right"><b><?= number_format($total_payable, 2) ?></b></td>
                <td align="right"><b><?= number_format($monthly_payment, 2) ?></b></td>
            </tr>
        </tbody>
    </table>

    <br><br>

    <!-- ✅ Payment Schedule -->
    <h5>Payment Schedule</h5>
    <table style="width:100%; border-collapse: collapse;" border="1">
        <thead>
            <tr>
                <th>#</th>
                <th>Due Date</th>
                <th>Amount Due</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($schedule as $s): ?>
                <tr>
                    <td align="center"><?= $s['no'] ?></td>
                    <td align="center"><?= $s['due_date'] ?></td>
                    <td align="right"><?= number_format($s['amount'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="2" align="right"><b>Total</b></td>
                <td align="right"><b><?= number_format($total_payable, 2) ?></b></td>
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
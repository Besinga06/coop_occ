<?php
// require('db.php'); // adjust to your DB connection -->

// $loanId = (int)$_GET['loan_id'];

// Next unpaid schedule
// $next = $db->querySingle("
//     SELECT schedule_id, principal_due, interest_due, total_due
//     FROM tbl_loan_schedule
//     WHERE loan_app_id=$loanId AND status='unpaid'
//     ORDER BY due_date ASC LIMIT 1
// ", true);

// Total payable vs. paid
// $loan = $db->querySingle("SELECT total_payable FROM tbl_loan_transactions WHERE loan_app_id=$loanId", true);
// $paidRow = $db->querySingle("SELECT SUM(amount_paid) as total_paid FROM tbl_loan_repayment WHERE loan_app_id=$loanId", true);

// $paid = $paidRow['total_paid'] ?? 0;
// $balance = $loan['total_payable'] - $paid;

// echo json_encode([
//     'monthly_due' => number_format($next['total_due'] ?? 0, 2),
//     'monthly_due_raw' => $next['total_due'] ?? 0,
//     'principal_due' => $next['principal_due'] ?? 0,
//     'interest_due' => $next['interest_due'] ?? 0,
//     'full_balance' => number_format($balance, 2)
// ]);

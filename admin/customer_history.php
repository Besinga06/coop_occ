<?php require('includes/header.php'); ?>

<?php
if (!isset($_GET['cust_id'])) {
    die("Invalid request: Missing customer ID.");
}

$cust_id = (int)$_GET['cust_id'];
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

/* ---------- CUSTOMER INFO ---------- */
$customer_result = $db->query("
    SELECT * 
    FROM tbl_customer 
    WHERE cust_id = $cust_id
");
$customer = $customer_result->fetch_assoc();

/* ---------- CAPITAL SHARE TOTAL (YEAR) ---------- */
$capital_result = $db->query("
    SELECT SUM(amount) AS total_capital
    FROM tbl_capital_share
    WHERE cust_id = $cust_id
      AND YEAR(contribution_date) = $year
");
$capital = $capital_result->fetch_assoc();

/* ---------- CAPITAL SHARE CONTRIBUTIONS ---------- */
$contributions = $db->query("
    SELECT *
    FROM tbl_capital_share
    WHERE cust_id = $cust_id
      AND YEAR(contribution_date) = $year
    ORDER BY contribution_date DESC
");

/* ---------- CASH SALES SUMMARY ---------- */
$cash_sales = $db->query("
    SELECT 
        s.sales_no,
        s.sales_date,
        p.product_name, 
        s.quantity_order AS total_quantity,
        s.total_amount
    FROM tbl_sales s
    INNER JOIN tbl_products p ON s.product_id = p.product_id
    WHERE s.sales_type = 1
      AND s.cust_id = $cust_id
      AND YEAR(s.sales_date) = $year
    ORDER BY s.sales_date, s.sales_no
");

$charge_sales = $db->query("
    SELECT 
        s.sales_no,
        s.sales_date,
        c.cust_id,
        c.name AS customer_name,

        p.product_name,
        s.quantity_order AS total_quantity,

        /* Original amounts */
        s.subtotal,
        s.total_amount,

        /* TOTAL PAYMENTS FROM BOTH SOURCES */
        (COALESCE(pay.total_paid, 0) + COALESCE(s.amount_paid, 0)) AS payments_made,

        /* CORRECT BALANCE */
        s.total_amount - (COALESCE(pay.total_paid, 0) + COALESCE(s.amount_paid, 0)) AS balance

    FROM tbl_sales s
    INNER JOIN tbl_customer c ON c.cust_id = s.cust_id
    INNER JOIN tbl_products p ON s.product_id = p.product_id

    /*  Aggregate installment payments */
    LEFT JOIN (
        SELECT sales_no, SUM(amount_paid) AS total_paid
        FROM tbl_payments
        GROUP BY sales_no
    ) pay ON pay.sales_no = s.sales_no

    WHERE s.sales_type = 0
      AND s.sales_status != 3
      AND s.cust_id = $cust_id
      AND YEAR(s.sales_date) = $year

    ORDER BY s.sales_date, s.sales_no
");


/* ---------- DISBURSED BENEFITS ---------- */
$disbursed = $db->query("
    SELECT
        dd.reference_no,
        dd.amount_disbursed,
        dd.payment_method,
        dd.disbursed_at,
        dr.cycle_id
    FROM distribution_disbursements dd
    LEFT JOIN distribution_records dr ON dd.record_id = dr.id
    WHERE dd.cust_id = $cust_id
      AND YEAR(dd.disbursed_at) = $year
    ORDER BY dd.disbursed_at DESC
");

/* ---------- PAYMENTS ---------- */
$payments = $db->query("
    SELECT *
    FROM tbl_payments
    WHERE sales_no IN (
        SELECT sales_no
        FROM tbl_sales
        WHERE cust_id = $cust_id
          AND YEAR(sales_date) = $year
    )
    AND YEAR(date_payment) = $year
    ORDER BY date_payment DESC
");
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
                            <h4><i class="icon-user position-left"></i> Member History - <?= htmlspecialchars($customer['name']); ?> (<?= $year; ?>)</h4>
                        </div>
                    </div>
                    <div class="breadcrumb-line">
                        <ul class="breadcrumb">
                            <li><a href="customer.php"><i class="icon-users"></i> Members</a></li>
                            <li class="active">History</li>
                        </ul>
                        <ul class="breadcrumb-elements">
                            <li><a href="#" id="btn-download-pdf"><i class="icon-file-pdf text-teal-400"></i> Download PDF</a></li>
                        </ul>
                    </div>
                </div>

                <div class="content" id="history-content">
                    <div class="panel panel-flat">
                        <div class="panel-body">
                            <div class="tabbable">
                                <ul class="nav nav-tabs bg-slate nav-justified">
                                    <li class="active"><a href="#info" data-toggle="tab">Information</a></li>
                                    <li><a href="#capital" data-toggle="tab">Capital Share</a></li>
                                    <li><a href="#cash" data-toggle="tab">Cash Sales</a></li>
                                    <li><a href="#charge" data-toggle="tab">Charge Sales</a></li>
                                    <li><a href="#benefits" data-toggle="tab">Disbursed Benefits</a></li>
                                </ul>

                                <div class="tab-content">

                                    <!-- INFORMATION TAB -->
                                    <div class="tab-pane active" id="info">
                                        <div class="panel panel-white border-top-xlg border-top-teal-400">
                                            <div class="panel-heading">
                                                <h6 class="panel-title"><i class="icon-list position-left text-teal-400"></i> Information</h6>
                                            </div>
                                            <div class="panel-body">
                                                <?php
                                                /* ---------- TOTAL CASH SALES ---------- */
                                                $total_cash_result = $db->query("
                                                SELECT COALESCE(SUM(total_amount), 0) AS total_cash
                                              FROM tbl_sales
                                              WHERE cust_id = $cust_id
                                              AND sales_type = 1
                                              AND YEAR(sales_date) = $year
                                                                        ");
                                                $total_cash_row = $total_cash_result->fetch_assoc();
                                                $total_cash = $total_cash_row['total_cash'];

                                                /* ---------- TOTAL CHARGE PAID ---------- */
                                                $total_charge_paid_result = $db->query("
                                                SELECT 
                                               COALESCE(SUM(per_sale.payments_made), 0) AS total_charge_paid
                                               FROM (
                                               SELECT 
                                               s.sales_no,

                                              
                                               (COALESCE(p.total_paid, 0) + COALESCE(s.amount_paid, 0)) AS payments_made

                                               FROM tbl_sales s

                                               LEFT JOIN (
                                              SELECT sales_no, SUM(amount_paid) AS total_paid
                                              FROM tbl_payments
                                              GROUP BY sales_no
                                              ) p ON s.sales_no = p.sales_no

                                              WHERE s.cust_id = $cust_id
                                              AND s.sales_type = 0
                                              AND s.sales_status != 3
                                              AND YEAR(s.sales_date) = $year
                                                               ) per_sale
                                                ");

                                                $total_charge_paid_row = $total_charge_paid_result->fetch_assoc();
                                                $total_charge_paid = $total_charge_paid_row['total_charge_paid'];
                                                ?>

                                                <table class="table table-bordered">
                                                    <tr>
                                                        <td>Name</td>
                                                        <td><?= htmlspecialchars($customer['name']); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Address</td>
                                                        <td><?= htmlspecialchars($customer['address']); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Contact</td>
                                                        <td><?= htmlspecialchars($customer['contact']); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Total Capital Share (<?= $year; ?>)</td>
                                                        <td>₱<?= number_format($capital['total_capital'] ?? 0, 2); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Total Cash Sales (<?= $year; ?>)</td>
                                                        <td>₱<?= number_format($total_cash, 2); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Total Paid (Charge Sales <?= $year; ?>)</td>
                                                        <td>₱<?= number_format($total_charge_paid, 2); ?></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- CAPITAL SHARE TAB -->
                                    <div class="tab-pane" id="capital">
                                        <div class="panel panel-white border-top-xlg border-top-teal-400">
                                            <div class="panel-heading">
                                                <h6 class="panel-title"><i class="icon-piggy-bank position-left text-teal-400"></i> Capital Share Contributions (<?= $year; ?>)</h6>
                                            </div>
                                            <div class="panel-body">
                                                <table class="table table-bordered table-hover">
                                                    <thead>
                                                        <tr style="background:#eee">
                                                            <th>Date</th>
                                                            <th>Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $hasContrib = false;

                                                        while ($c = $contributions->fetch_assoc()) {
                                                            $hasContrib = true;
                                                            echo "<tr>
                                                             <td>" . date('M d, Y', strtotime($c['contribution_date'])) . "</td>
                                                             <td>₱" . number_format($c['amount'], 2) . "</td>
                                                             </tr>";
                                                        }

                                                        if (!$hasContrib) {
                                                            echo "<tr><td colspan='2'>No contributions found for $year.</td></tr>";
                                                        }
                                                        ?>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- CASH SALES TAB -->
                                    <div class="tab-pane" id="cash">
                                        <div class="panel panel-white border-top-xlg border-top-teal-400">
                                            <div class="panel-heading">
                                                <h6 class="panel-title"><i class="icon-cart position-left text-teal-400"></i> Cash Sales Summary (<?= $year; ?>)</h6>
                                            </div>
                                            <div class="panel-body">
                                                <table class="table table-bordered table-hover">
                                                    <thead>
                                                        <tr style="background:#eee">
                                                            <th>Product</th>
                                                            <th class="text-center">Total Quantity</th>
                                                            <th class="text-right">Total Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $hasCash = false;
                                                        $total_cash_sum = 0;

                                                        while ($row = $cash_sales->fetch_assoc()) {
                                                            $hasCash = true;

                                                            $qty = isset($row['total_quantity']) ? (int)$row['total_quantity'] : 0;
                                                            $amount = isset($row['total_amount']) ? $row['total_amount'] : 0;

                                                            $total_cash_sum += $amount;
                                                        ?>
                                                            <tr>
                                                                <td><?= htmlspecialchars($row['product_name']); ?></td>
                                                                <td class="text-center"><?= $qty; ?></td>
                                                                <td class="text-right">₱<?= number_format($amount, 2); ?></td>
                                                            </tr>
                                                        <?php } ?>

                                                        <?php if (!$hasCash) { ?>
                                                            <tr>
                                                                <td colspan="3">No cash sales found for <?= $year; ?>.</td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>

                                                    <?php if ($hasCash) { ?>

                                                        <tfoot>
                                                            <tr>
                                                                <th colspan="2" class="text-right">Total:</th>
                                                                <th class="text-right">₱<?= number_format($total_cash_sum, 2); ?></th>
                                                            </tr>
                                                        </tfoot>
                                                    <?php } ?>
                                                </table>

                                            </div>
                                        </div>
                                    </div>

                                    <!-- CHARGE SALES TAB -->
                                    <div class="tab-pane" id="charge">
                                        <div class="panel panel-white border-top-xlg border-top-teal-400">
                                            <div class="panel-heading">
                                                <h6 class="panel-title"><i class="icon-credit-card position-left text-teal-400"></i> Charge / Unpaid Sales Summary (<?= $year; ?>)</h6>
                                            </div>
                                            <div class="panel-body">
                                                <table class="table table-bordered table-hover">
                                                    <thead>
                                                        <tr style="background:#eee">
                                                            <th>Product</th>
                                                            <th class="text-center">Total Quantity</th>
                                                            <th class="text-right">Total Amount</th>
                                                            <th class="text-right">Paid</th>
                                                            <th class="text-right">Balance</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $hasCharge = false;
                                                        $total_amt = $paid_amt = $bal_amt = 0;

                                                        while ($row = $charge_sales->fetch_assoc()) {
                                                            $hasCharge = true;

                                                            $paid = $row['total_amount'] - $row['balance'];
                                                            $total_amt += $row['total_amount'];
                                                            $paid_amt += $paid;
                                                            $bal_amt += $row['balance'];

                                                            echo "<tr>
                                                            <td>" . htmlspecialchars($row['product_name']) . "</td>

                                                            <!-- FIXED QUANTITY -->
                                                            <td class='text-center'>" . (int)$row['total_quantity'] . "</td>

                                                           <!-- RIGHT ALIGNED MONEY -->
                                                           <td class='text-right'>₱" . number_format($row['total_amount'], 2) . "</td>
                                                           <td class='text-right'>₱" . number_format($paid, 2) . "</td>
                                                           <td class='text-right'>₱" . number_format($row['balance'], 2) . "</td>
                                                           </tr>";
                                                        }

                                                        if (!$hasCharge) {
                                                            echo "<tr><td colspan='5'>No charge sales found for $year.</td></tr>";
                                                        }
                                                        ?>

                                                    </tbody>
                                                    <?php if ($hasCharge) { ?>
                                                        <tfoot>
                                                            <tr style="font-weight:bold;">
                                                                <th colspan="2" class="text-right">Totals:</th>
                                                                <th class="text-right">₱<?= number_format($total_amt, 2); ?></th>
                                                                <th class="text-right">₱<?= number_format($paid_amt, 2); ?></th>
                                                                <th class="text-right">₱<?= number_format($bal_amt, 2); ?></th>
                                                            </tr>
                                                        </tfoot>
                                                    <?php } ?>
                                                </table>

                                            </div>
                                        </div>
                                    </div>

                                    <!-- DISBURSED BENEFITS TAB -->
                                    <div class="tab-pane" id="benefits">
                                        <div class="panel panel-white border-top-xlg border-top-teal-400">
                                            <div class="panel-heading">
                                                <h6 class="panel-title"><i class="icon-gift position-left text-teal-400"></i> Distribution / Disbursed Benefits (<?= $year; ?>)</h6>
                                            </div>
                                            <div class="panel-body">
                                                <table class="table table-bordered table-hover">
                                                    <thead>
                                                        <tr style="background:#eee">
                                                            <th>Date</th>
                                                            <th>Cycle</th>
                                                            <th>Amount</th>
                                                            <th>Payment Method</th>
                                                            <th>Reference No</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $hasDisb = false;

                                                        while ($d = $disbursed->fetch_assoc()) {
                                                            $hasDisb = true;

                                                            echo "<tr>
                                                                 <td>" . date('M d, Y h:i A', strtotime($d['disbursed_at'])) . "</td>
                                                                <td>Cycle #" . htmlspecialchars($d['cycle_id']) . "</td>
                                                                <td>₱" . number_format($d['amount_disbursed'], 2) . "</td>
                                                                <td>" . htmlspecialchars(ucfirst($d['payment_method'])) . "</td>
                                                                <td>" . htmlspecialchars($d['reference_no']) . "</td>
                                                                </tr>";
                                                        }

                                                        if (!$hasDisb) {
                                                            echo "<tr><td colspan='5'>No disbursed benefits found for $year.</td></tr>";
                                                        }
                                                        ?>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                </div> <!-- tab-content -->
                            </div> <!-- tabbable -->
                        </div>
                    </div>
                </div>

                <?php require('includes/footer-text.php'); ?>
            </div>
        </div>
    </div>

    <?php require('includes/footer.php'); ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        document.getElementById('btn-download-pdf').addEventListener('click', async function() {
            const {
                jsPDF
            } = window.jspdf;
            const pdf = new jsPDF('p', 'mm', 'a4');
            const element = document.getElementById('history-content');

            const activeTab = document.querySelector('.nav-tabs li.active a');
            const tabs = document.querySelectorAll('.tab-pane');
            tabs.forEach(tab => tab.classList.add('active', 'show'));
            await new Promise(resolve => setTimeout(resolve, 400));

            const canvas = await html2canvas(element, {
                scale: 2,
                useCORS: true,
                scrollY: -window.scrollY
            });
            const imgData = canvas.toDataURL('image/png');
            const imgProps = pdf.getImageProperties(imgData);
            const pdfWidth = pdf.internal.pageSize.getWidth();
            const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
            let position = 0;

            if (pdfHeight < pdf.internal.pageSize.getHeight()) {
                pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
            } else {
                let heightLeft = pdfHeight;
                while (heightLeft > 0) {
                    pdf.addImage(imgData, 'PNG', 0, position, pdfWidth, pdfHeight);
                    heightLeft -= pdf.internal.pageSize.getHeight();
                    if (heightLeft > 0) {
                        pdf.addPage();
                        position = -heightLeft;
                    }
                }
            }

            pdf.save("Member_History_<?= $year; ?>.pdf");

            tabs.forEach(tab => tab.classList.remove('active', 'show'));
            if (activeTab) $(activeTab).tab('show');
        });
    </script>

</body>

</html>
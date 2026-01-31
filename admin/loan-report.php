<?php
require('includes/header.php');
require('db_connect.php');

?>

<style type="text/css">
    #show-search-member {
        background-color: #26a69a;
        min-height: 300px;
        max-height: 300px;
        overflow-y: auto;
        z-index: 100;
        position: absolute;
        width: 100%;
        display: none;
    }

    #show-search-member::-webkit-scrollbar-track {
        background-color: #F5F5F5;
    }

    #show-search-member::-webkit-scrollbar {
        width: 12px;
        background-color: #F5F5F5;
    }

    #show-search-member::-webkit-scrollbar-thumb {
        background-color: #3c8881;
    }

    .ul-search {
        list-style-type: none;
        background: #26a69a;
        color: #fff;
        margin-left: -25px;
        font-size: 12px;
    }

    .ul-search li {
        padding: 10px;
        height: 40px;
        font-size: 12px;
        cursor: pointer;
        border-bottom: 1px solid #dddddd;
    }

    #member-input {
        width: 200px;
    }

    #searchclearmember {
        position: absolute;
        right: 5px;
        top: 0;
        bottom: 0;
        height: 14px;
        margin: auto;
        font-size: 14px;
        cursor: pointer;
        color: #ccc;
    }

    .paging_simple_numbers,
    .dataTables_info {
        margin-top: 10px;
    }
</style>

<?php
// -------------------------
// FILTERS
// -------------------------
$member_filter = '';
$status_filter = '';
$selected_member = '';
$status_text = 'All';

// Member filter
if (!empty($_SESSION['loan-report-member'])) {
    $member_id = (int)$_SESSION['loan-report-member'];
    $member_filter = " AND l.customer_id = $member_id ";

    $stmt = $db->prepare("SELECT name FROM tbl_customer WHERE cust_id = ?");
    $stmt->bind_param("i", $member_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $selected_member = $row['name'] ?? '';
    $stmt->close();
}

// Status filter
$status_map = [
    'pending' => 'Pending',
    'approved' => 'Approved',
    'disbursed' => 'Disbursed',
    'rejected' => 'Rejected'
];
if (!empty($_SESSION['loan-report-status'])) {
    $status_val = $_SESSION['loan-report-status'];
    $status_filter = " AND l.status = '" . $db->real_escape_string($status_val) . "' ";
    $status_text = $status_map[$status_val] ?? 'All';
}

// -------------------------
// SUMMARY PANEL DATA
// -------------------------
$summary_sql = "
    SELECT 
        COUNT(l.loan_app_id) AS total_loans,
        COALESCE(SUM(l.requested_amount),0) AS total_requested,
        COALESCE(SUM(a.approved_amount),0) AS total_approved,
        COALESCE(SUM(t.disbursed_amount),0) AS total_disbursed
    FROM tbl_loan_application l
    LEFT JOIN tbl_loan_approval a ON a.loan_app_id = l.loan_app_id
    LEFT JOIN tbl_loan_transactions t ON t.loan_app_id = l.loan_app_id
    WHERE 1=1 $member_filter $status_filter
";

$summary_query = $db->query($summary_sql);
$summary = $summary_query ? $summary_query->fetch_assoc() : [
    'total_loans' => 0,
    'total_requested' => 0,
    'total_approved' => 0,
    'total_disbursed' => 0
];
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
        height: 40px !important;
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
            <a class="navbar-brand" href="index.php"><img src="../images/farmers-logo.png" alt=""><span>Lourdes Farmers Multi-Purpose Cooperative</span></a>
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

                <!-- Page Header -->
                <div class="page-header page-header-default">
                    <div class="page-header-content">
                        <div class="page-title">
                            <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold">Dashboard </span> - Loan Report</h4>
                        </div>
                    </div>
                    <div class="breadcrumb-line">
                        <ul class="breadcrumb">
                            <li><a href="index.php"><i class="icon-home2 position-left"></i> Dashboard</a></li>
                            <li><a href="javascript:;"><i class="icon-chart position-left"></i> Reports</a></li>
                            <li class="active"><i class="icon-dots position-left"></i> Loan Report</li>
                        </ul>
                    </div>
                </div>

                <div class="content">

                    <!-- Summary Panels -->
                    <div class="row">
                        <?php
                        $colors = ['bg-success-400', 'bg-blue-400', 'bg-purple-400', 'bg-orange-400'];
                        $titles = ['No. of Loans', 'Total Requested', 'Total Approved', 'Total Disbursed'];
                        $values = [$summary['total_loans'], number_format($summary['total_requested'], 2), number_format($summary['total_approved'], 2), number_format($summary['total_disbursed'], 2)];
                        foreach ($titles as $i => $title) {
                            echo '<div class="col-sm-6 col-md-3">
                <div class="panel panel-body ' . $colors[$i] . ' has-bg-image">
                    <div class="media no-margin">
                        <div class="media-left media-middle">
                            <i class="icon-coin-dollar icon-3x opacity-75"></i>
                        </div>
                        <div class="media-body text-right">
                            <h3>' . $values[$i] . '</h3>
                            <span class="text-uppercase text-size-mini">' . $title . '</span>
                        </div>
                    </div>
                </div>
            </div>';
                        }
                        ?>
                    </div>

                    <!-- Filter Form -->
                    <div class="panel panel-body">
                        <form class="heading-form" id="form-loan" method="POST">
                            <input type="hidden" name="submit-loan">
                            <ul class="breadcrumb-elements" style="float:left">
                                <li style="padding-top: 2px;padding-right: 2px">
                                    <div class="btn-group">
                                        <input type="hidden" value="<?php echo $_SESSION['loan-report-member'] ?? ''; ?>" name="member_id" id="member_id">
                                        <input style="width: 230px" autocomplete="off" type="search" class="form-control"
                                            id="member-input" value="<?php echo $selected_member; ?>" name="membername">
                                        <span id="searchclearmember" class="glyphicon glyphicon-remove-circle"></span>
                                        <div id="show-search-member"></div>
                                    </div>
                                </li>
                                <input type="hidden" id="input-status" name="status" value="<?php echo $_SESSION['loan-report-status'] ?? ''; ?>">
                                <li class="text-center" style="padding-top: 2px;padding-right: 2px;width:auto;">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default btn-rounded"><span id="span-status"><?php echo $status_text; ?></span></button>
                                        <button type="button" class="btn btn-default btn-rounded dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li onclick="select_status(this)" status-val="pending" status-name="Pending"><a href="#">Pending</a></li>
                                            <li onclick="select_status(this)" status-val="approved" status-name="Approved"><a href="#">Approved</a></li>
                                            <li onclick="select_status(this)" status-val="disbursed" status-name="Disbursed"><a href="#">Disbursed</a></li>
                                            <li onclick="select_status(this)" status-val="rejected" status-name="Rejected"><a href="#">Rejected</a></li>
                                            <li onclick="select_status(this)" status-val="" status-name="All"><a href="#">All</a></li>
                                        </ul>
                                    </div>
                                </li>
                                <li style="padding-top: 2px;padding-right: 2px">
                                    <button type="submit" class="btn bg-teal-400"><b><i class="icon-search4"></i></b></button>
                                </li>
                                <li style="padding-top: 2px;padding-right: 2px">
                                    <button type="button" onclick="clear_filter()" class="btn bg-slate-400"><b><i class="icon-filter4"></i></b></button>
                                </li>
                            </ul>
                        </form>
                    </div>

                    <!-- Loan Table -->
                    <div class="panel panel-white border-top-xlg border-top-teal-400">
                        <div class="panel-heading">
                            <h6 class="panel-title"><i class="icon-list text-teal-400"></i> List of Loans</h6>
                        </div>

                        <div class="panel-body">
                            <table class="table datatable-loan table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>Loan No.</th>
                                        <th>Member Name</th>
                                        <th>Requested Amount</th>
                                        <th>Approved Amount</th>
                                        <th>Disbursed Amount</th>
                                        <th>Term (Months)</th>
                                        <th>Interest Rate</th>
                                        <th>Status</th>
                                        <th>Repayment Progress</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    $loan_sql = "
    SELECT 
        l.loan_app_id,
        c.name AS member_name,
        l.requested_amount,
        COALESCE(a.approved_amount, 0) AS approved_amount,
        COALESCE(a.approved_term, 0) AS approved_term,
        COALESCE(a.interest_rate, 0) AS interest_rate,
        COALESCE(t.total_payable, 0) AS total_payable,
        COALESCE(t.disbursed_amount, 0) AS disbursed_amount,
        l.status
    FROM tbl_loan_application l
    LEFT JOIN tbl_customer c ON c.cust_id = l.customer_id
    LEFT JOIN tbl_loan_approval a ON a.loan_app_id = l.loan_app_id
    LEFT JOIN tbl_loan_transactions t ON t.loan_app_id = l.loan_app_id
    WHERE 1=1 $member_filter $status_filter
    ORDER BY l.application_date DESC
";

                                    $loan_query = $db->query($loan_sql);

                                    while ($row = $loan_query->fetch_assoc()) {
                                        // Fetch total paid for this loan
                                        $stmt_paid = $db->prepare("SELECT COALESCE(SUM(amount_paid),0) AS total_paid FROM tbl_loan_repayment WHERE loan_app_id = ?");
                                        $stmt_paid->bind_param("i", $row['loan_app_id']);
                                        $stmt_paid->execute();
                                        $result_paid = $stmt_paid->get_result();
                                        $paid_total = ($result_paid->num_rows > 0) ? $result_paid->fetch_assoc()['total_paid'] : 0;
                                        $stmt_paid->close();

                                        $progress = ($row['total_payable'] > 0)
                                            ? round(($paid_total / $row['total_payable']) * 100, 2)
                                            : 0;

                                        echo "<tr>";
                                        echo "<td>{$row['loan_app_id']}</td>";
                                        echo "<td>" . htmlspecialchars($row['member_name']) . "</td>";
                                        echo "<td>" . number_format($row['requested_amount'], 2) . "</td>";
                                        echo "<td>" . number_format($row['approved_amount'], 2) . "</td>";
                                        echo "<td>" . number_format($row['disbursed_amount'], 2) . "</td>";
                                        echo "<td>{$row['approved_term']}</td>";
                                        echo "<td>{$row['interest_rate']}%</td>";
                                        echo "<td>" . ucfirst($row['status']) . "</td>";
                                        echo "<td>{$progress}%</td>";
                                        echo "</tr>";
                                    }
                                    ?>

                                </tbody>
                            </table>
                        </div>
                    </div>

                </div><!-- content -->
            </div>
        </div>
    </div>

    <?php require('includes/footer.php'); ?>

    <script src="../assets/js/plugins/tables/datatables/datatables.min.js"></script>
    <script src="../assets/js/plugins/notifications/jgrowl.min.js"></script>

    <script>
        function select_status(el) {
            var val = el.getAttribute('status-val');
            var name = el.getAttribute('status-name');
            document.getElementById('input-status').value = val;
            document.getElementById('span-status').innerText = name;
        }

        function clear_filter() {
            document.getElementById('member-input').value = '';
            document.getElementById('member_id').value = '';
            document.getElementById('input-status').value = '';
            document.getElementById('form-loan').submit();
        }
    </script>
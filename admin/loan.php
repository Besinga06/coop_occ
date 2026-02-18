    <?php
    require('includes/header.php');



    require('../db_connect.php');


    $pending_loans = $db->query("
        SELECT 
            l.loan_id,
            l.requested_amount,
            l.term_months,
            l.status,
            l.application_date,
            CONCAT(m.first_name,' ',m.last_name) AS member_name,
            lt.loan_type_name
        FROM loan_applications l
        JOIN tbl_members m ON m.member_id = l.member_id
        JOIN loan_types lt ON lt.loan_type_id = l.loan_type_id
        WHERE l.status = 'pending'
        ORDER BY l.loan_id DESC
    ");



    $approved_loans = $db->query("
        SELECT 
            l.loan_id,
            l.approved_amount,
            l.term_months,
            l.status,
            l.approved_date,
            CONCAT(m.first_name,' ',m.last_name) AS member_name,
            lt.loan_type_name
        FROM loan_applications l
        JOIN tbl_members m ON m.member_id = l.member_id
        JOIN loan_types lt ON lt.loan_type_id = l.loan_type_id
        WHERE l.status = 'approved'
        ORDER BY l.loan_id DESC
    ");


    $disbursed_loans = $db->query("
        SELECT 
            l.loan_id,
            l.approved_amount,
            l.term_months,
            l.released_date,
            CONCAT(m.first_name,' ',m.last_name) AS member_name,
            lt.loan_type_name
        FROM loan_applications l
        JOIN tbl_members m ON m.member_id = l.member_id
        JOIN loan_types lt ON lt.loan_type_id = l.loan_type_id
        WHERE l.status = 'released'
        ORDER BY l.released_date DESC
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
                                <h4><i class="icon-arrow-left52 position-left"></i>
                                    <span class="text-semibold">Dashboard</span> - Loans
                                </h4>
                            </div>
                        </div>
                        <div class="breadcrumb-line">
                            <ul class="breadcrumb">
                                <li><a href="index.php"><i class="icon-home2 position-left"></i> Dashboard</a></li>
                                <li class="active"><i class="icon-cash3 position-left"></i> Loans</li>
                            </ul>
                            <ul class="breadcrumb-elements">

                                <li>
                                    <a href="javascript:;" data-toggle="modal" data-target="#modal-new">
                                        <i class="icon-add position-left text-teal-400"></i> New Loan
                                    </a>
                                </li>

                                <li>
                                    <a href="loan-transaction.php">
                                        <i class="icon-coins position-left text-primary"></i> View Active Loans
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>



                    <div class="content">


                        <div class="panel panel-white border-top-xlg border-top-warning">
                            <div class="panel-heading">
                                <h6 class="panel-title">
                                    <i class="icon-hour-glass2 text-warning position-left"></i> Pending Loan Applications
                                </h6>
                            </div>
                            <div class="panel-body panel-theme">
                                <table class="table datatable-button-html5-basic table-hover table-bordered" id="pending-loans-table">
                                    <thead>
                                        <tr>
                                            <!-- <th>ID</th> -->
                                            <th>Name</th>
                                            <th>Amount</th>
                                            <th>Term</th>
                                            <th>Status</th>
                                            <th>Date Applied</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $pending_loans->fetch_assoc()) { ?>
                                            <tr (<?= (int)$row['loan_id']; ?>)>
                                                <td hidden><?= (int)$row['loan_id']; ?></td>
                                                <td><b><?= htmlspecialchars($row['member_name']); ?></b></td>
                                                <td style="text-align:right"><?= number_format($row['loan_amount'], 2); ?></td>
                                                <td><?= (int)$row['term_months']; ?> months</td>
                                                <td><span class="label label-warning"><?= htmlspecialchars(ucfirst($row['status'])); ?></span></td>
                                                <td><?= htmlspecialchars($row['application_date']); ?></td>
                                            </tr>
                                        <?php } ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>


                        <div class="panel panel-white border-top-xlg border-top-success">
                            <div class="panel-heading">
                                <h6 class="panel-title">
                                    <i class="icon-checkmark4 text-success position-left"></i> Approved Loans
                                </h6>
                            </div>
                            <div class="panel-body panel-theme">
                                <table class="table datatable-button-html5-basic table-hover table-bordered" id="approved-loans-table">
                                    <thead>
                                        <tr>
                                            <!-- <th>ID</th> -->
                                            <th>Name</th>
                                            <th>Amount</th>
                                            <th>Term</th>
                                            <th>Status</th>
                                            <th>Date Approved</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $approved_loans->fetch_assoc()) { ?>
                                            <tr>
                                                <td hidden><?= (int)$row['loan_id']; ?></td>
                                                <td><b><?= htmlspecialchars($row['member_name']); ?></b></td>
                                                <td style="text-align:right"><?= number_format($row['loan_amount'], 2); ?></td>
                                                <td><?= (int)$row['term_months']; ?> months</td>
                                                <td><span class="label label-success"><?= htmlspecialchars(ucfirst($row['status'])); ?></span></td>
                                                <td><?= htmlspecialchars($row['application_date']); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-success" onclick="openDisburseModal(<?= (int)$row['loan_id']; ?>, <?= htmlspecialchars(json_encode($row['loan_amount'])); ?>)">
                                                        <i class="icon-wallet"></i> Disburse
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php } ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>


                        <div class="panel panel-white border-top-xlg border-top-primary">
                            <div class="panel-heading">
                                <h6 class="panel-title">
                                    <i class="icon-wallet text-primary position-left"></i> Disbursed Loans
                                </h6>
                            </div>
                            <div class="panel-body panel-theme">
                                <table class="table datatable-button-html5-basic table-hover table-bordered" id="disbursed-loans-table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Amount Released</th>
                                            <th>Term</th>
                                            <th>Release Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $disbursed_loans->fetch_assoc()) { ?>
                                            <tr>
                                                <td hidden><?= (int)$row['loan_id']; ?></td>
                                                <td><b><?= htmlspecialchars($row['member_name']); ?></b></td>
                                                <td style="text-align:right"><?= number_format($row['amount_released'], 2); ?></td>
                                                <td><?= (int)$row['term_months']; ?> months</td>
                                                <td><?= htmlspecialchars($row['release_date']); ?></td>
                                                <td>
                                                    <button class="btn btn-info btn-print-disbursement"
                                                        data-id="<?= (int)$row['loan_id']; ?>">
                                                        <i class="icon-printer"></i> Print
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php } ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>


                    </div>

                    <?php require('includes/footer-text.php'); ?>

                </div>
            </div>
        </div>
    </body>


    <div id="modal-new" class="modal fade" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog ">
            <div class="modal-content">
                <form id="form-new" class="form-horizontal" role="form">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h5 class="modal-title">New Loan Application</h5>
                    </div>
                    <div class="modal-bodys">
                        <input type="hidden" name="save-loan-application" value="1">
                        <div id="display-msg"></div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Member</label>
                            <div class="col-sm-9">
                                <select class="form-control select-member-search" name="customer_id" required>
                                    <option value="">-- Select Member --</option>
                                    <?php
                                    $members = $db->query("
                                    SELECT member_id, first_name, last_name 
                                    FROM tbl_members 
                                    WHERE type='regular'
                                    AND status='active'
                                    ");

                                    while ($m = $members->fetch_assoc()) {
                                        echo "<option value='{$m['member_id']}'>
                                           {$m['first_name']} {$m['last_name']}
                                           </option>";
                                    }
                                    ?>

                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Loan Amount</label>
                            <div class="col-sm-9">
                                <input type="number" step="0.01" class="form-control" name="requested_amount" placeholder="Enter amount" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Term (months)</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="term_months" placeholder="Enter term in months" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Purpose</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="purpose" placeholder="Loan purpose"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="btn-submit" type="submit" class="btn bg-teal-400 btn-labeled">
                            <b><i class="icon-add"></i></b> Save Loan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="modal-disburse" class="modal fade" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="form-disburse" class="form-horizontal" role="form">

                    <!-- Header -->
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h5 class="modal-title">Disburse Loan</h5>
                    </div>

                    <!-- Direct form content (no modal-body) -->
                    <input type="hidden" name="disburse_loan" value="1">
                    <input type="hidden" name="loan_id" id="disb-loan-id" value="">

                    <div class="form-group m-2">
                        <label class="col-sm-3 control-label">Amount</label>
                        <div class="col-sm-9">
                            <input type="number" step="0.01" class="form-control" name="amount_released" id="disb-amount" required>
                        </div>
                    </div>

                    <div class="form-group m-2">
                        <label class="col-sm-3 control-label">Mode</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="mode">
                                <option value="cash">Cash</option>
                                <option value="check">Check</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                        </div>
                    </div>

                    <div id="disburse-msg" class="m-2"></div>

                    <!-- Footer -->
                    <div class="modal-footer">
                        <button id="btn-disburse" type="submit" class="btn bg-success btn-labeled">
                            <b><i class="icon-wallet"></i></b> Disburse
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div id="disbursementReceiptModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Loan Disbursement Receipt</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="disbursement-receipt-body">
                    <!-- Receipt content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="printDisbursementReceipt()" class="btn btn-primary">
                        <i class="icon-printer"></i> Print
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>



    <?php require('includes/footer.php'); ?>

    <script src="../js/select2.min.js"></script>

    <script type="text/javascript" src="../assets/js/plugins/tables/datatables/datatables.min.js"></script>
    <script src="../js/validator.min.js"></script>
    <script type="text/javascript" src="../assets/js/plugins/notifications/jgrowl.min.js"></script>
    <script type="text/javascript">
        $('#modal-new').on('shown.bs.modal', function() {
            $('.select-member-search').select2({
                dropdownParent: $('#modal-new'),
                placeholder: "Search Member",
                allowClear: true,
                width: '100%'
            });
        });


        $(function() {
            // Initialize DataTables
            $('.datatable-button-html5-basic').DataTable({
                "order": [
                    [0, "desc"]
                ],
                "aLengthMenu": [
                    [6, 15, 100],
                    [6, 15, 100],
                    [6, 15, 100]
                ]
            });
        });

        // Open Disburse Loan Modal
        function openDisburseModal(loanId, loanAmount) {
            $('#disb-loan-id').val(loanId);
            $('#disb-amount').val(parseFloat(loanAmount));
            $('#disburse-msg').html('');
            $('#modal-disburse').modal('show');
        }

        // View Loan Details
        function view_details(id) {
            window.location = 'loan-details.php?id=' + id;
        }

        // New Loan Form Submission
        $('#form-new').validator().on('submit', function(e) {
            if (!e.isDefaultPrevented()) {
                $.ajax({
                    type: 'POST',
                    url: '../transaction.php',
                    data: $(this).serialize(),
                    success: function(msg) {
                        msg = msg.trim();
                        if (msg === "1") {
                            $.jGrowl('New loan successfully added.', {
                                header: 'Success Notification',
                                theme: 'alert-styled-right bg-success'
                            });
                            $('#modal-new').modal('hide');
                            setTimeout(function() {
                                window.location.reload();
                            }, 1200);
                        } else {
                            $.jGrowl('Save failed: ' + msg, {
                                header: 'Error Notification',
                                theme: 'alert-styled-right bg-danger'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        $.jGrowl('AJAX Error: ' + error, {
                            header: 'Error Notification',
                            theme: 'alert-styled-right bg-danger'
                        });
                    }
                });
                return false;
            }
        });

        // Disburse Loan Form Submission
        $('#form-disburse').on('submit', function(e) {
            e.preventDefault();
            var $btn = $('#btn-disburse');
            $btn.prop('disabled', true);

            $.ajax({
                type: 'POST',
                url: '../transaction.php',
                data: $(this).serialize(),
                success: function(msg) {
                    msg = msg.trim();
                    $btn.prop('disabled', false);
                    if (msg === "1") {
                        $.jGrowl('Loan successfully disbursed.', {
                            header: 'Success Notification',
                            theme: 'alert-styled-right bg-success'
                        });
                        $('#modal-disburse').modal('hide');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1200);
                    } else {
                        $.jGrowl('Disbursement failed: ' + msg, {
                            header: 'Error Notification',
                            theme: 'alert-styled-right bg-danger'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    $btn.prop('disabled', false);
                    $.jGrowl('AJAX Error: ' + error, {
                        header: 'Error Notification',
                        theme: 'alert-styled-right bg-danger'
                    });
                }
            });
        });

        // Open Disbursement Receipt Modal
        $(document).on('click', '.btn-print-disbursement', function() {
            var loanId = $(this).data('id');
            $('#disbursement-receipt-body').html('<p class="text-center">Loading receipt...</p>');

            $.get('disbursement_receipt.php', {
                loan_id: loanId
            }, function(data) {
                $('#disbursement-receipt-body').html(data);
                $('#disbursementReceiptModal').modal('show');
            });
        });

        // Print function for receipt
        function printDisbursementReceipt() {
            var printContents = document.getElementById("disbursement-receipt-body").innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        }
    </script>

    </html>
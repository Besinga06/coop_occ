<?php
require('includes/header.php');

// Get year filter
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Fetch cycles for the selected year
$cycles = $db->query("SELECT * FROM distribution_cycles WHERE year = '$year' ORDER BY id DESC");

// Fetch disbursement records (already disbursed)
$disbursements = $db->query("
    SELECT d.id, d.amount_disbursed, d.payment_method, d.reference_no, d.disbursed_at, d.remarks,
           c.name AS customer_name, dc.year, dr.dividend, dr.patronage, dr.total_benefit
    FROM distribution_disbursements d
    JOIN distribution_records dr ON dr.id = d.record_id
    JOIN tbl_customer c ON c.cust_id = d.cust_id
    JOIN distribution_cycles dc ON dc.id = d.cycle_id
    ORDER BY d.disbursed_at DESC
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
                                <span class="text-semibold">Patronage & Dividend</span> - Disbursement
                            </h4>
                        </div>
                    </div>
                    <div class="breadcrumb-line">
                        <ul class="breadcrumb">
                            <li><a href="patronage_dividend.php"><i class="icon-coins"></i>Patronage & Dividend</a></li>
                            <li class="active"><i class="icon-cash"></i> Disbursement</li>
                        </ul>
                    </div>
                </div>

                <div class="content">
                    <!-- Cycle Selection -->
                    <div class="panel panel-white border-top-xlg border-top-teal">
                        <div class="panel-heading">
                            <h6 class="panel-title"><i class="icon-calendar3 text-teal-400 position-left"></i> Select Distribution Cycle</h6>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Year</label>
                                    <input type="number" id="year" name="year" class="form-control" value="<?= $year ?>" min="2000" max="<?= date('Y') + 1 ?>">
                                </div>
                                <div class="col-md-4">
                                    <label>Cycle</label>
                                    <select id="cycle_id" class="form-control">
                                        <option value="">-- Select Cycle --</option>
                                        <?php while ($cycle = $cycles->fetch_assoc()) { ?>
                                            <option value="<?= $cycle['id'] ?>">
                                                Cycle <?= $cycle['id'] ?> - <?= $cycle['year'] ?>
                                            </option>
                                        <?php } ?>

                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Disbursement Table -->
                    <div class="panel panel-white border-top-xlg border-top-warning">
                        <div class="panel-heading">
                            <h6 class="panel-title"><i class="icon-coins text-warning position-left"></i> Pending / Completed Disbursements</h6>
                        </div>
                        <div class="panel-body panel-theme">
                            <table class="table table-bordered table-hover" id="disbursement-table">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Dividend (₱)</th>
                                        <th>Patronage (₱)</th>
                                        <th>Total Benefit (₱)</th>
                                        <th>Amount Disbursed (₱)</th>
                                        <th>Payment Method</th>
                                        <th>Reference No.</th>
                                        <th>Disbursed At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- JS will populate table based on selected cycle -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <?php require('includes/footer.php'); ?>

    <!-- Disbursement Modal -->
    <div id="disburseModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-teal-400 text-white">
                    <h5 class="modal-title">Confirm Disbursement</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class=>
                    <input type="hidden" id="disburse_record_id">
                    <p>Are you sure you want to disburse the following benefits?</p>
                    <p><strong>Member:</strong> <span id="disburse_member_name"></span></p>
                    <p><strong>Total Benefits:</strong> ₱ <span id="disburse_total_benefit"></span></p>
                    <p><strong>Payment Method:</strong> Cash</p>
                    <p hidden><strong>Reference No:</strong> <span id="disburse_reference_no_auto"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" id="disburse_cancel" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" id="disburse_confirm" class="btn btn-teal">Disburse</button>
                </div>
            </div>
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
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="../assets/js/plugins/tables/datatables/datatables.min.js"></script>
    <script src="../assets/js/plugins/notifications/jgrowl.min.js"></script>
    <script>
        $(function() {

            $('#disbursement-table').DataTable({
                "order": [
                    [7, "desc"]
                ],
                "lengthMenu": [
                    [10, 25, 50],
                    [10, 25, 50]
                ]
            });

            // Load records when cycle is selected
            $('#cycle_id').on('change', function() {
                const cycle_id = $(this).val();
                const year = $('#year').val();
                if (!cycle_id) return;

                $.ajax({
                    url: '../transaction.php',
                    type: 'POST',
                    data: {
                        action: 'get_distribution_records',
                        cycle_id,
                        year
                    },
                    dataType: 'json',
                    success: function(records) {
                        const table = $('#disbursement-table').DataTable();
                        table.clear();

                        records.forEach(r => {
                            const ref = r.reference_no ? `<a href="#" class="view-receipt" 
                                    data-ref="${r.reference_no}" 
                                    data-member="${r.customer_name}" 
                                    data-amount="${r.total_benefit}" 
                                    data-date="${r.disbursed_at}">${r.reference_no}</a>` : '';

                            table.row.add([
                                r.customer_name,
                                '₱ ' + parseFloat(r.dividend).toFixed(2),
                                '₱ ' + parseFloat(r.patronage).toFixed(2),
                                '₱ ' + parseFloat(r.total_benefit).toFixed(2),
                                r.amount_disbursed ? '₱ ' + parseFloat(r.amount_disbursed).toFixed(2) : '₱ 0.00',
                                r.payment_method || '',
                                ref,
                                r.disbursed_at || '',
                                r.amount_disbursed ? '<button class="btn btn-success btn-sm" disabled>Disbursed</button>' :
                                '<button class="btn btn-teal btn-sm btn-disburse" data-id="' + r.id + '">Disburse</button>'
                            ]).draw(false);
                        });
                    }
                });
            });

            let currentRecordId = null;
            let currentMemberName = '';
            let currentTotalBenefit = '';
            let currentReferenceNo = '';

            // Open modal when disburse button clicked
            $('#disbursement-table').on('click', '.btn-disburse', function() {
                const row = $(this).closest('tr');
                currentRecordId = $(this).data('id');
                currentMemberName = row.find('td:eq(0)').text();
                currentTotalBenefit = row.find('td:eq(3)').text().replace('₱', '').trim();
                currentReferenceNo = 'DISB-' + new Date().toISOString().replace(/[-T:.Z]/g, '').slice(0, 14);

                $('#disburse_record_id').val(currentRecordId);
                $('#disburse_member_name').text(currentMemberName);
                $('#disburse_total_benefit').text(parseFloat(currentTotalBenefit).toFixed(2));
                $('#disburse_reference_no_auto').text(currentReferenceNo);

                $('#disburseModal').modal('show');
            });

            // Confirm Disbursement
            $('#disburse_confirm').on('click', function() {
                $.ajax({
                    url: '../transaction.php',
                    type: 'POST',
                    data: {
                        action: 'save_disbursement',
                        record_id: currentRecordId,
                        payment_method: 'cash',
                        reference_no: currentReferenceNo
                    },
                    dataType: 'json',
                    success: function(resp) {
                        $('#disburseModal').modal('hide');
                        if (resp.success) {
                            $.jGrowl('Member benefits disbursed successfully!', {
                                header: 'Success',
                                theme: 'bg-success'
                            });
                            $('#cycle_id').trigger('change');

                            const member = currentMemberName;
                            const amount = parseFloat(currentTotalBenefit).toFixed(2);
                            const ref = currentReferenceNo;
                            const date = resp.disbursed_at || new Date().toLocaleString();

                            // Receipt layout
                            $('#receipt-content').html(`
                        <div class="receipt-div" style="font-family:Arial, sans-serif; font-size:14px;">
                            <div class="text-center">
                                <p class="title"><b>LOURDES FARMERS MULTI-PURPOSE COOPERATIVE</b></p>
                                <p>Brgy Lourdes, Alubijid Mis'Or</p>
                                <p>Benefit Disbursement Receipt</p>
                                <hr>
                            </div>

                            <table style="width:100%; margin-bottom:10px;">
                                <tr>
                                    <td><b>Member:</b> ${member}</td>
                                    <td class="text-right"><b>Date:</b> ${date}</td>
                                </tr>
                                <tr>
                                    <td><b>Total Benefit:</b> ₱ ${amount}</td>
                                    <td class="text-right"><b>Payment Method:</b> Cash</td>
                                </tr>
                                <tr>
                                    <td><b>Reference No:</b> ${ref}</td>
                                    <td></td>
                                </tr>
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
                                        Member Signature
                                    </td>
                                </tr>
                            </table>

                            <div class="text-center mt-3">
                                <button class="btn btn-success btn-sm btn-print-receipt-inline">Print Receipt</button>
                            </div>
                        </div>
                    `);

                            $('#modal-receipt').modal('show');

                        } else {
                            alert('Error saving disbursement.');
                        }
                    },
                    error: function() {
                        alert('Server error.');
                    }
                });
            });

            // Clickable reference number to show receipt
            $('#disbursement-table').on('click', '.view-receipt', function(e) {
                e.preventDefault();
                const member = $(this).data('member');
                const amount = parseFloat($(this).data('amount')).toFixed(2);
                const ref = $(this).data('ref');
                const date = $(this).data('date');

                $('#receipt-content').html(`
            <div class="receipt-div" style="font-family:Arial, sans-serif; font-size:14px;">
                <div class="text-center">
                    <p class="title"><b>LOURDES FARMERS MULTI-PURPOSE COOPERATIVE</b></p>
                    <p>Brgy Lourdes, Alubijid Mis'Or</p>
                    <p>Benefit Disbursement Receipt</p>
                    <hr>
                </div>

                <table style="width:100%; margin-bottom:10px;">
                    <tr>
                        <td><b>Member:</b> ${member}</td>
                        <td class="text-right"><b>Date:</b> ${date}</td>
                    </tr>
                    <tr>
                        <td><b>Total Benefit:</b> ₱ ${amount}</td>
                        <td class="text-right"><b>Payment Method:</b> Cash</td>
                    </tr>
                    <tr>
                        <td><b>Reference No:</b> ${ref}</td>
                        <td></td>
                    </tr>
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
                            Member Signature
                        </td>
                    </tr>
                </table>

                <div class="text-center mt-3">
                    <button class="btn btn-success btn-sm btn-print-receipt-inline">Print Receipt</button>
                </div>
            </div>
        `);

                $('#modal-receipt').modal('show');
            });

            // Print Receipt using delegated event
            $(document).on('click', '.btn-print-receipt-inline', function() {
                var printContents = document.querySelector('.receipt-div').innerHTML;
                var w = window.open('', '_blank', 'width=600,height=700');
                w.document.write('<html><head><title>Receipt</title></head><body>');
                w.document.write(printContents);
                w.document.write('</body></html>');
                w.document.close();
                w.print();
            });

        });
    </script>

</body>
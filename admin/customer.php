<?php require('includes/header.php'); ?>
<?php
$query = "SELECT * FROM tbl_customer where cust_id!=1 ";
$result = $db->query($query);
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
        height: 65px;

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
            <a class="navbar-brand" href="index.php"><img src="../images/your_logo.png" alt=""><span>OCC Cooperative</span></a>
            <ul class="nav navbar-nav visible-xs-block">
                <li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
            </ul>
        </div>
        <div class="navbar-collapse collapse" id="navbar-mobile">
            <?php require('includes/sidebar.php'); ?>
        </div>
    </div>
    <!-- /main navbar -->

    <!-- Page container -->
    <div class="page-container">

        <!-- Page content -->
        <div class="page-content">

            <!-- Main content -->
            <div class="content-wrapper">

                <!-- Page header -->
                <div class="page-header page-header-default">
                    <div class="page-header-content">
                        <div class="page-title">
                            <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold">Dashboard</span> - Member</h4>
                        </div>
                    </div>

                    <div class="breadcrumb-line">
                        <ul class="breadcrumb">
                            <li><a href="index.php"><i class="icon-home2 position-left"></i> Dashboard</a></li>
                            <li class="active"><i class="icon-users position-left"></i>Member</li>
                        </ul>
                        <ul class="breadcrumb-elements">
                            <li><a href="javascript:;" data-toggle="modal" data-target="#modal_new"><i class="icon-add position-left text-teal-400"></i> New Member</a></li>
                        </ul>
                    </div>
                </div>
                <!-- /page header -->

                <!-- Content area -->
                <div class="content">

                    <div class="panel  panel-white border-top-xlg border-top-teal-400">
                        <div class="panel-heading">
                            <h6 class="panel-title"><i class="icon-list text-teal-400 position-left"></i> Member List</h6>
                        </div>
                        <div class="panel-body">
                            <table class="table datatable-button-html5-basic table-hover table-bordered" width="100%">
                                <thead>
                                    <tr style="border-bottom: 4px solid #ddd;background: #eee;">
                                        <th>Memeber ID</th>
                                        <th>Name</th>
                                        <th>Address</th>
                                        <th>Contact</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()) { ?>
                                        <tr style="cursor:pointer;" onclick="view_details(this)" title="View Customer History">
                                            <td>34236<?= $row['cust_id']; ?></td>
                                            <td><?= $row['name']; ?></td>
                                            <td><?= $row['address']; ?></td>
                                            <td><?= $row['contact']; ?></td>
                                            <td onclick="event.stopPropagation(); edit_details(this);"
                                                cust_id="<?= $row['cust_id']; ?>"
                                                name="<?= $row['name']; ?>"
                                                address="<?= $row['address']; ?>"
                                                contact="<?= $row['contact']; ?>"
                                                title="Edit" style="width: 40px;text-align: center;">
                                                <button type="button" class="btn border-teal text-teal-400 btn-flat btn-icon btn-xs">
                                                    <i class="icon-pencil7"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /content area -->
                <?php require('includes/footer-text.php'); ?>

            </div>
            <!-- /main content -->

        </div>
        <!-- /page content -->

    </div>
    <!-- /page container -->
</body>
<?php require('includes/footer.php'); ?>
<!-- Modal Add Member -->
<div id="modal_new" class="modal fade">
    <div class="modal-dialog modal-lg"> <!-- wider modal -->
        <div class="modal-content">

            <form action="#" id="form-customer" class="form-horizontal" role="form">
                <input type="hidden" name="save-customer">

                <div class="modal-header bg-teal">
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    <h5 class="modal-title"><i class="icon-user-plus"></i> Register New Member</h5>
                </div>

                <div class="modal-body">

                    <!-- NAME ROW -->
                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label font-weight-semibold">First Name</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="icon-user"></i></span>
                                <input class="form-control input-lg" name="first_name" placeholder="Enter first name" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="control-label font-weight-semibold">Last Name</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="icon-user"></i></span>
                                <input class="form-control input-lg" name="last_name" placeholder="Enter last name" required>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- PERSONAL INFO -->
                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label font-weight-semibold">Gender</label>
                            <select name="gender" class="form-control input-lg" required>
                                <option value="">-- Select Gender --</option>
                                <option>Male</option>
                                <option>Female</option>
                                <option>Other</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="control-label font-weight-semibold">Contact Number</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="icon-phone"></i></span>
                                <input class="form-control input-lg" name="contact" placeholder="09XXXXXXXXX">
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- ACCOUNT INFO -->
                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label font-weight-semibold">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="icon-envelop"></i></span>
                                <input type="email" class="form-control input-lg" name="email" placeholder="example@email.com" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="control-label font-weight-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="icon-lock2"></i></span>
                                <input type="password" class="form-control input-lg" name="password" placeholder="Enter password" required>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- ADDRESS -->
                    <div class="form-group">
                        <label class="control-label font-weight-semibold">Complete Address</label>
                        <textarea name="address" rows="3" class="form-control" placeholder="Street, Barangay, City, Province"></textarea>
                    </div>

                    <!-- CAPITAL SHARE -->
                    <div class="form-group">
                        <label class="control-label font-weight-semibold">Capital Share Contribution</label>
                        <div class="input-group">
                            <span class="input-group-addon">₱</span>
                            <input class="form-control input-lg" name="capital_share" type="number" step="0.01" min="0" value="0">
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn bg-teal-600 btn-lg">
                        <i class="icon-check"></i> Save Member
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>



<div id="modal_edit" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="#" id="form-customer-edit" class="form-horizontal" data-toggle="validator" role="form">
                <input type="hidden" name="update-customer">
                <input type="hidden" id="cust_id" name="cust_id">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h5 class="modal-title">Edit Member Form</h5>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Name</label>
                        <div class="col-sm-9">
                            <div class="input-group input-group-xlg">
                                <span class="input-group-addon"><i class="icon-pencil7 text-size-base"></i></span>
                                <input class="form-control" id="name" name="name" placeholder="Name" type="text" required data-error="Name is required.">
                            </div>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Address</label>
                        <div class="col-sm-9">
                            <textarea name="address" id="address" rows="5" cols="5" class="form-control" placeholder="Address"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Contact</label>
                        <div class="col-sm-9">
                            <div class="input-group input-group-xlg">
                                <span class="input-group-addon"><i class="icon-pencil7 text-size-base"></i></span>
                                <input class="form-control" id="contact" name="contact" placeholder="Contact" type="text" data-error="Contact is required.">
                            </div>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn bg-teal-400 btn-labeled">
                        <b><i class="icon-pencil"></i></b> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- /modal add -->
<script type="text/javascript" src="../assets/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="../assets/js/plugins/notifications/jgrowl.min.js"></script>
<script src="../js/validator.min.js"></script>
<script type="text/javascript">
    $(function() {

        // Table setup
        $.extend($.fn.dataTable.defaults, {
            autoWidth: false,
            dom: '<"datatable-header"fBl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
            language: {
                search: '<span>Filter:</span> _INPUT_',
                searchPlaceholder: 'Type to filter...',
                lengthMenu: '<span>Show:</span> _MENU_',
                paginate: {
                    'first': 'First',
                    'last': 'Last',
                    'next': '&rarr;',
                    'previous': '&larr;'
                }
            }
        });

        // Basic initialization
        $('.datatable-button-html5-basic').DataTable({
            "order": [
                [0, "desc"]
            ],
            buttons: {
                dom: {
                    button: {
                        className: 'btn btn-default'
                    }
                },
                buttons: [] // No copy/csv/pdf buttons
            }
        });
    });

    function view_details(el) {
        var cust_id = $(el).find('td:first').text().replace('34236', '');
        window.location = 'customer_history.php?cust_id=' + cust_id;
    }

    function edit_details(el) {
        $("#modal_edit").modal('show');
        $("#cust_id").val($(el).attr('cust_id'));
        $("#name").val($(el).attr('name'));
        $("#address").val($(el).attr('address'));
        $("#contact").val($(el).attr('contact'));
    }

    $('#form-customer').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            $(':input[type="submit"]').prop('disabled', true);
            var data = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: '../transaction.php',
                data: data,
                success: function(msg) {
                    console.log(msg);
                    if (msg == '1') {
                        $.jGrowl('New member successfully added.', {
                            header: 'Success Notification',
                            theme: 'alert-styled-right bg-success'
                        });
                        setTimeout(function() {
                            window.location = 'customer.php';
                        }, 1500);
                    } else if (msg == 'duplicate') {
                        $.jGrowl('Member already exists. Please use a unique name or contact.', {
                            header: 'Duplicate Entry',
                            theme: 'alert-styled-right bg-warning'
                        });
                        $(':input[type="submit"]').prop('disabled', false);
                    } else {
                        alert('Something went wrong!');
                        $(':input[type="submit"]').prop('disabled', false);
                    }
                },
                error: function() {
                    alert('Something went wrong!');
                    $(':input[type="submit"]').prop('disabled', false);
                }
            });
            return false;
        }
    });

    $('#form-customer-edit').validator().on('submit', function(e) {
        if (!e.isDefaultPrevented()) {
            $(':input[type="submit"]').prop('disabled', true);
            var data = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: '../transaction.php',
                data: data,
                success: function(msg) {
                    if (msg == '1') {
                        $.jGrowl('Customer successfully updated.', {
                            header: 'Success Notification',
                            theme: 'alert-styled-right bg-success'
                        });
                        setTimeout(function() {
                            window.location = 'customer.php';
                        }, 1500);
                    } else {
                        alert('Something went wrong!');
                    }
                },
                error: function(msg) {
                    alert('Something went wrong!');
                }
            });
            return false;
        }
    });
</script>

</html>
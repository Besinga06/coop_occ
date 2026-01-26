<?php require('includes/header.php'); ?>
<?php
$query = "SELECT * FROM tbl_users WHERE user_id=" . $_SESSION['user_id'] . " ";
$result = $db->query($query);
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
			<a class="navbar-brand" href="index.php"><img style="height: 40px!important" src="../images/farmers-logo.png" alt=""><span>Lourdes Farmers Multi-Purpose Cooperative</span></a>
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
							<h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold">Dashboard</span> - My Profile</h4>
						</div>
					</div>

					<div class="breadcrumb-line">
						<ul class="breadcrumb">
							<li><a href="index.php"><i class="icon-home2 position-left"></i>Dashboard</a></li>
							<li class="active"><i class="icon-users position-left"></i>My Profile</li>
						</ul>
						<ul class="breadcrumb-elements">

						</ul>
					</div>
				</div>
				<!-- /page header -->

				<!-- Content area -->
				<div class="content">

					<div class="panel  panel-white border-top-xlg border-top-teal-400">
						<div class="panel-heading">
							<h6 class="panel-title"><i class="icon-list text-teal-400 position-left"></i> My Profile</h6>
						</div>
						<!-- <input type="text" id="myInputTextField"> -->
						<div class="panel-body">
							<?php
							$name = '';
							$username = '';
							$password = '';
							$user_id = '';
							$query = "SELECT * FROM tbl_users WHERE user_id=" . $_SESSION['user_id'] . "";
							$result = $db->query($query);
							while ($row = $result->fetch_assoc()) {
								$name = $row['fullname'];
								$username = $row['username'];
								$password = $row['password'];
								$user_id = $row['user_id'];
							}
							?>
							<form action="#" id="form-edit" class="form-horizontal" data-toggle="validator" role="form">
								<input type="hidden" name="field_status" value="0">
								<input type="hidden" name="update-cashier"></input>
								<input type="hidden" value="<?= $user_id ?>" name="user_id" id="user_id"></input>
								<div class="form-body" style="padding-top: 20px">

									<div class="form-group">
										<label for="exampleInputuname_4" class="col-sm-3 control-label">Employee Name</label>
										<div class="col-sm-9">
											<div class="input-group input-group-xlg">
												<span class="input-group-addon"><i class="icon-pencil7"></i></span>
												<input value="<?= $name ?>" class="form-control currency" autocomplete="off" name="name" id="fullname" placeholder="Full Name" type="text" data-error=" Employee Name is required." required>
											</div>
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="exampleInputuname_4" class="col-sm-3 control-label">Userame</label>
										<div class="col-sm-9">
											<div class="input-group input-group-xlg">
												<span class="input-group-addon"><i class="icon-user"></i></span>
												<input value="<?= $username ?>" class="form-control currency" autocomplete="off" name="username" id="username" disabled="" placeholder="Username" type="text">
											</div>
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="exampleInputuname_4" class="col-sm-3 control-label">Password</label>
										<div class="col-sm-9">
											<div class="input-group input-group-xlg">
												<span class="input-group-addon"><i class="icon-lock"></i></span>
												<input class="form-control currency" autocomplete="off" name="password" id="password" placeholder="Password" type="password">
											</div>
											<div class="help-block">Leave blank if you don't want to change it </div>
										</div>
									</div>
									<div class="form-group">
										<label for="exampleInputuname_4" class="col-sm-3 control-label"></label>
										<div class="col-sm-9">
											<button type="submit" class="btn bg-teal-400 btn-labeled"><b><i class="icon-pencil"></i></b> Save Changes</button>
										</div>
									</div>


								</div>
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

<script type="text/javascript" src="../assets/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="../assets/js/plugins/notifications/jgrowl.min.js"></script>
<script src="../js/validator.min.js"></script>
<script type="text/javascript">
	$(function() {

		// Table setup
		// ------------------------------
		// Setting datatable defaults
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
				buttons: [{
						extend: 'copyHtml5',
						className: 'btn btn-default',
						text: '<i class="icon-copy3 position-left"></i> Copy'
					},
					{
						extend: 'csvHtml5',
						className: 'btn btn-default',
						text: '<i class="icon-copy3 position-left"></i> CSV'
					},
					{
						extend: 'pdfHtml5',
						className: 'btn btn-default',
						text: '<i class="icon-copy3 position-left"></i> pdf'
					}

				]
			}
		});
	});
</script>
<script type="text/javascript">
	$(document).ready(function() {

	});
	$('#form-edit').validator().on('submit', function(e) {
		if (e.isDefaultPrevented()) {} else {
			$(':input[type="submit"]').prop('disabled', true);
			var data = $("#form-edit").serialize();
			$.ajax({
				type: 'POST',
				url: '../transaction.php',
				data: data,
				success: function(msg) {
					console.log(msg);
					if (msg == '1') {
						$.jGrowl('Profile successfully updated.', {
							header: 'Success Notification',
							theme: 'alert-styled-right bg-success'
						});
						setTimeout(function() {
							location.reload();
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

	function closer() {
		window.location = 'customer.php';
	}

	function view_details(el) {
		var cust_id = $(el).attr('cust_id');
		window.location = 'customer-details.php?cust_id=' + cust_id;
	}
</script>

</html>
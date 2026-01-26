<?php  require('includes/header.php');?>
<?php 
    $percentageLoading = 0;
    $today = date("Y-m-d");
    $query_sales = "SELECT *  FROM tbl_sales WHERE strftime('%Y-%m-%d', date) = '$today' ";
	$result_sales = $db->query($query_sales);
	$daly_sales = 0;
	while($febuary = $result_sales->fetchArray()) {
		$daly_sales++;
	}
	$daly_sales = 9;

	if ( $daly_sales < 50 ) {
		$percentageLoading = 50;
	}elseif ( $daly_sales > 50 && $daly_sales < 100   ) {
		$percentageLoading = 120;
	}elseif ( $daly_sales > 100 && $daly_sales < 200   ) {
		$percentageLoading = 300;
	}elseif ( $daly_sales > 200 && $daly_sales < 300   ) {
		$percentageLoading = 500;
	}else{
		$percentageLoading = 1000;
	}
	
?>
<body class="layout-boxed navbar-top">

	<!-- Main navbar -->
	<div class="navbar navbar-inverse bg-teal-400 navbar-fixed-top">
		<div class="navbar-header">
			<a class="navbar-brand" href="index.php"><img style="height: 40px!important" src="../images/logo2.png" alt=""></a>

			<ul class="nav navbar-nav visible-xs-block">
				<li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
			</ul>
		</div>

		<div class="navbar-collapse collapse" id="navbar-mobile">
			<?php  require('includes/sidebar.php');?>
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
							<h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold">Dashboard</span> - Database Management</h4>
						</div>
					</div>

					<div class="breadcrumb-line">
						<ul class="breadcrumb">
							<li><a href="index.php"><i class="icon-home2 position-left"></i> Dashboard</a></li>
							<li class="active">Database Management</li>
						</ul>

						<ul class="breadcrumb-elements">
                            <li data-toggle="tooltip" title="" style="padding-top: 2px;padding-right: 2px" data-original-title="Search"><button type="submit" class="btn bg-teal-400 btn-labeled"><b><i class="icon-search4"></i></b> Scan Database</button></li>
						</ul>
					</div>
				</div>
				<!-- /page header -->
               
				<!-- Content area -->
				<div class="content">
					<div class="row">
						<div class="col-md-12">
							<div class="panel  panel-white border-top-xlg border-top-teal-400">
								<div class="panel-heading">
									<h6 class="panel-title"><i class="icon-list text-teal-400 position-left"></i>  Daily Collection</h6>
									<div class="heading-elements">
										<ul class="breadcrumb-elements">
								    <li data-toggle="tooltip" title="Employee" style="padding-top: 2px;padding-right: 2px">
								    	<form class="heading-form" id="form-reports" method="POST">
							            <input type="hidden" name="daile-sales-report2" ></input>
							    	<div class="input-group">
				                        <span class="input-group-addon"><i class="icon-calendar"></i></span>
				                        <input style="width: 220px" name="date" type="text" class="form-control pickadate-selectors picker__input picker__input--active" value=" <?php if (isset($_SESSION['daily-report-input2'])!="") {?>   <?= $_SESSION['daily-report-input2'] ?> <?php }else{?> <?= date("m-d-Y")?> <?php } ?>" readonly="" id="P1916777366" aria-haspopup="true" aria-expanded="true" aria-readonly="false" aria-owns="P1916777366_root">
				                    </div>
								    </li>
								    <li data-toggle="tooltip" title="Search" style=""><button type="submit" class="btn bg-teal-400 btn-labeled"><b><i class="icon-search4"></i></b> Search</button></li>
								    <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li>
								</ul></form>
									</div>
								</div>
								<div class="panel-body panel-theme">
									<?php 
								        $today = date("Y-m-d");
								     	$start = strtotime('today GMT');
								     	$date_add = date('Y-m-d', strtotime('+1 day', $start));
								     	if (isset($_SESSION['daily-report2'])) {
								     		if ($today==$_SESSION['daily-report2'] ) {
								     			 $query = "SELECT * FROM tbl_sales WHERE  sales_date BETWEEN  '$today' AND '$date_add' AND sales_status!=3 GROUP BY sales_no ";
								     	    }else{
								     	    	$query = "SELECT * FROM tbl_sales WHERE  date(sales_date)='".$_SESSION['daily-report2']."' AND sales_status!=3 GROUP BY sales_no ";
								     	     }
								     	}else{
								     		$query = "SELECT * FROM tbl_sales WHERE  sales_date BETWEEN  '$today' AND '$date_add' AND sales_status!=3 GROUP BY sales_no ";
								     	}
								     	$result = $db->query($query);
								     	$all_subtotal = 0;
								     	$all_discount = 0;
								     	$all_total = 0;
						                while($row = $result->fetchArray()) {
						                	$subtotal = $row['subtotal'];
						                	$discount = $row['discount'];
						                	$total_amount = $row['total_amount'];
						                	$all_subtotal+=$subtotal;
						                	$all_discount+=$discount;
						                	$all_total+=$total_amount;
						                }
						                $vat_sales = $all_subtotal*.12;

						  
						                $expence_amount = 0;
								        $today = date("Y-m-d");
								        $start = strtotime('today GMT');
								        $date_add = date('Y-m-d', strtotime('+1 day', $start));
							            if (isset($_SESSION['daily-report2'])) {
								     		if ($today==$_SESSION['daily-report2'] ) {
								     			 $query2 = "SELECT * FROM tbl_expences INNER JOIN tbl_users ON tbl_expences.user_id=tbl_users.user_id   WHERE date(date_expence) BETWEEN  '$today' AND '$date_add'  ";
								     	    }else{
								     	    	$query2 = "SELECT * FROM tbl_expences INNER JOIN tbl_users ON tbl_expences.user_id=tbl_users.user_id   WHERE  date(date_expence)='".$_SESSION['daily-report2']."'  ";
								     	     }
								     	}else{
								     		$query2 = "SELECT * FROM tbl_expences INNER JOIN tbl_users ON tbl_expences.user_id=tbl_users.user_id   WHERE  date_expence BETWEEN  '$today' AND '$date_add' ";
								     	}
							            $result = $db->query($query2);
							            while($row = $result->fetchArray()) {
							            	$expence_amount+=$row['expence_amount'];

							            }


								    ?>
								    <div class="row">
										<div class="col-sm-6 col-md-3">
											<div class="panel panel-body bg-blue-400 has-bg-image">
												<div class="media no-margin">
													<div class="media-body">
														<h3 class="no-margin"><?= number_format($all_subtotal,2) ?></h3>
														<span class="text-uppercase text-size-mini">Sub Total</span>
													</div>

													<div class="media-right media-middle">
														<i class="icon-cash icon-3x opacity-75"></i>
													</div>
												</div>
											</div>
										</div>

										<div class="col-sm-6 col-md-3">
											<div class="panel panel-body bg-danger-400 has-bg-image">
												<div class="media no-margin">
													<div class="media-body">
														<h3 class="no-margin"><?= number_format($all_discount,2) ?></h3>
														<span class="text-uppercase text-size-mini">Discount</span>
													</div>

													<div class="media-right media-middle">
														<i class="icon-3x opacity-75">%</i>
													</div>
												</div>
											</div>
										</div>

										<div class="col-sm-6 col-md-3">
											<div class="panel panel-body bg-indigo-400 has-bg-image">
												<div class="media no-margin">
													<div class="media-left media-middle">
														<i class="icon-cash icon-3x opacity-75"></i>
													</div>

													<div class="media-body text-right">
														<h3 class="no-margin"><?= number_format($all_total-$all_discount,2) ?></h3>
														<span class="text-uppercase text-size-mini">Total Amount</span>
													</div>
												</div>
											</div>
										</div>

										<div class="col-sm-6 col-md-3">
											<div class="panel panel-body bg-success-400 has-bg-image">
												<div class="media no-margin">
													<div class="media-left media-middle">
														<i class="icon-cash icon-3x opacity-75"></i>
													</div>

													<div class="media-body text-right">
														<h3 class="no-margin"><?= number_format($expence_amount,2) ?></h3>
														<span class="text-uppercase text-size-mini">Expences</span>
													</div>
												</div>
											</div>
										</div>

									</div>
									<div align="right" style="padding-right: 60px"><h4>Daily Collection : <?= number_format($all_total-$expence_amount,2) ?></h4></div>

									   <hr><br>
									   <button   id="btn-update" type="button" onclick="update_daily_sales()" class="btn bg-teal-400 btn-labeled"><b><i class="icon-search4"></i></b> Update Database</button>
								</div>
							</div>
						</div>
						<div class="col-md-12">
							<div class="panel  panel-white border-top-xlg border-top-teal-400">
								<div class="panel-heading">
									<h6 class="panel-title"><i class="icon-list text-teal-400 position-left"></i>  List Of Modules </h6>
								</div>
								<div class="panel-body panel-theme">
									  <table class="table table-bordered">
									   	    <tr>
									   	    	<td>Emdployee</td>
									   	    	<td class="text-center"></td>
									   	    	<td style="width: 100px;text-align: center;"> <button onclick="updateDatabase(1)" type="button" class="btn bg-teal-400 btn-labeled"><b><i class="icon-pencil"></i></b> Update</button></td>
									   	    </tr>
									   	    <tr>
									   	    	<td>Customer</td>
									   	    	<td class="text-center"></td>
									   	    	<td style="width: 100px;text-align: center;"> <button onclick="updateDatabase(2)" type="button" class="btn bg-teal-400 btn-labeled"><b><i class="icon-pencil"></i></b> Update</button></td>
									   	    </tr>
									   	    <tr>
									   	    	<td>Products</td>
									   	    	<td class="text-center"></td>
									   	    	<td style="width: 100px;text-align: center;"> <button onclick="updateDatabase(3)" type="button" class="btn bg-teal-400 btn-labeled"><b><i class="icon-pencil"></i></b> Update</button></td>
									   	    </tr>
									   	    <tr>
									   	    	<td>Product History</td>
									   	    	<td class="text-center"></td>
									   	    	<td style="width: 100px;text-align: center;"> <button onclick="updateDatabase(4)" type="button" class="btn bg-teal-400 btn-labeled"><b><i class="icon-pencil"></i></b> Update</
									   	    </tr>
									   	    <tr>
									   	    	<td>Product Damage</td>
									   	    	<td class="text-center"></td>
									   	    	<td style="width: 100px;text-align: center;"> <button onclick="updateDatabase(5)" type="button" class="btn bg-teal-400 btn-labeled"><b><i class="icon-pencil"></i></b> Update</
									   	    </tr>
									   	    <tr>
									   	    	<td>Supplier</td>
									   	    	<td class="text-center"></td>
									   	    	<td style="width: 100px;text-align: center;"> <button onclick="updateDatabase(6)" type="button" class="btn bg-teal-400 btn-labeled"><b><i class="icon-pencil"></i></b> Update</
									   	    </tr>
									   	    <tr>
									   	    	<td>Receiving</td>
									   	    	<td class="text-center"></td>
									   	    	<td style="width: 100px;text-align: center;"> <button onclick="updateDatabase(7)" type="button" class="btn bg-teal-400 btn-labeled"><b><i class="icon-pencil"></i></b> Update</
									   	    </tr>
									   	    <tr>
									   	    	<td>System History</td>
									   	    	<td class="text-center"></td>
									   	    	<td style="width: 100px;text-align: center;"> <button onclick="updateDatabase(8)" type="button" class="btn bg-teal-400 btn-labeled"><b><i class="icon-pencil"></i></b> Update</
									   	    </tr>

									   	    
									   </table>
								</div>
							</div>
						</div>

					</div>
					
				</div>
				<!-- /content area -->
				<?php  require('includes/footer-text.php');?>

			</div>
			<!-- /main content -->

		</div>
		<!-- /page content -->

	</div>
	<!-- /page container -->
	<div id="modal-loading" class="modal fade" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-bodys">
					<div class="row">
						<div class="text-center">
							<div id="show-text"></div>
							<div class="progress">
							  <div class="progress-bar user-progress-bar progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="10" >
							    <span></span>
							  </div>
							</div>
					    </div>
					</div>
				</div>
				<div id="modal-daily-sales" class="modal-footer" style="display: none;">
					<button type="button" class="btn btn-link" onclick="modalClose()">Close</button>
				</div>
			</div>
		</div>
	</div>

	<div id="modal-loading2" class="modal fade" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-bodys">
					<div class="row">
						<div class="text-center">
							<div id="show-text2"></div>
							<div class="progress">
							  <div class="progress-bar user-progress-bar2 progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="10" >
							    <span></span>
							  </div>
							</div>
					    </div>
					</div>
				</div>
				<div id="modal-daily-sales2" class="modal-footer" style="display: none;">
					<button type="button" class="btn btn-link" onclick="modalClose()">Close</button>
				</div>
			</div>
		</div>
	</div>
</body>
<?php  require('includes/footer.php');?>
<script type="text/javascript" src="../assets/js/plugins/notifications/jgrowl.min.js"></script>  
<script type="text/javascript" src="../assets/js/plugins/ui/moment/moment.min.js"></script>
<script type="text/javascript" src="../assets/js/plugins/pickers/daterangepicker.js"></script>
<script type="text/javascript" src="../assets/js/plugins/pickers/anytime.min.js"></script>
<script type="text/javascript" src="../assets/js/plugins/pickers/pickadate/picker.js"></script>
<script type="text/javascript" src="../assets/js/plugins/pickers/pickadate/picker.date.js"></script>
<script type="text/javascript" src="../assets/js/plugins/pickers/pickadate/picker.time.js"></script>
<script type="text/javascript" src="../assets/js/plugins/pickers/pickadate/legacy.js"></script>
 <script type="text/javascript">
 	$(function() {
	    $('.pickadate-selectors').pickadate({
	        format: 'mm/dd/yyyy',
	        hiddenPrefix: 'prefix__',
	        hiddenSuffix: '__suffix',
	        clear: ''
	    });
	 });
    function updateDatabase(el)
	{   
       /* var percentageLoading = 100;  
	    $("#modal-loading2").modal('show');
		$("#btn-update2").attr('disabled', true);
		$("#show-text2").html('<div class="alert alert-info alert-styled-left alert-bordered"> <span class="text-semibold">Heads up!</span> Currently updating database!!! please wait . </div>');*/
		$.ajax({
               type      :      'POST',
               url       :      '../transaction.php',
               data      :       {update_database_spicific:"",stype:el},
                success  :       function(msg)     
                {  
                    console.log(msg);
                    /*var i = 0;
					var counterBack = setInterval(function(){
					  i++;
					  if (i <= 90){
					    $('.user-progress-bar2').css('width', i+'%');
					    $(".user-progress-bar2 span").text(i+'% Complete');
					  } else {
					    clearInterval(counterBack);
					  }
					  
					}, percentageLoading);
					if (msg=='saves') {
						setTimeout(function(){
						    $("#modal-daily-sales2").show(); 
							i=100;
							$('.user-progress-bar2').css('width', i+'%');
							$('.user-progress-bar2').addClass('progress-bar-success');
							$(".user-progress-bar2 span").text(i+'% Complete');
							$("#show-text").html('<div class="alert alert-success alert-styled-left alert-bordered"> <span class="text-semibold">Well done! Database successfully updated. </div>');
						}, 3000);
					}else{
						$("#show-text2").html('<div class="alert alert-danger alert-styled-left alert-bordered"> <span class="text-semibold">Oh snap! Error found while updateding databse!!! </div>');
						setTimeout(function(){ 
							$("#modal-daily-sale2s").show(); 
							var i2 = i;
							i=100;
							$('.user-progress-bar2').css('width', '50%');
							$('.user-progress-bar2').addClass('progress-bar-danger');
							$(".user-progress-bar2 span").text(i2+'% Complete - Error Found ');
						}, 3000);
					}*/
                },
                error  :       function(msg)     
                { 
                	$("#btn-update2").attr('disabled', false);
                	$("#modal-daily-sales2").show(); 
                    alert('Something went wrong!');
                }
        });
        return false;
	}

	function update_daily_sales()
	{   
        var percentageLoading = 100;  
	    $("#modal-loading").modal('show');
		$("#btn-update").attr('disabled', true);
		$("#show-text").html('<div class="alert alert-info alert-styled-left alert-bordered"> <span class="text-semibold">Heads up!</span> Currently updating database!!! please wait . </div>');
		$.ajax({
               type      :      'POST',
               url       :      '../transaction.php',
               data      :       {update_daily_sales:""},
                success  :       function(msg)     
                {  
                    console.log(msg);
                    var i = 0;
					var counterBack = setInterval(function(){
					  i++;
					  if (i <= 90){
					    $('.user-progress-bar').css('width', i+'%');
					    $(".user-progress-bar span").text(i+'% Complete');
					  } else {
					    clearInterval(counterBack);
					  }
					  
					}, percentageLoading);
					if (msg=='saves') {
						setTimeout(function(){
						    $("#modal-daily-sales").show(); 
							i=100;
							$('.user-progress-bar').css('width', i+'%');
							$('.user-progress-bar').addClass('progress-bar-success');
							$(".user-progress-bar span").text(i+'% Complete');
							$("#show-text").html('<div class="alert alert-success alert-styled-left alert-bordered"> <span class="text-semibold">Well done! Database successfully updated. </div>');
						}, 3000);
					}else{
						$("#show-text").html('<div class="alert alert-danger alert-styled-left alert-bordered"> <span class="text-semibold">Oh snap! Error found while updateding databse!!! </div>');
						setTimeout(function(){ 
							$("#modal-daily-sales").show(); 
							var i2 = i;
							i=100;
							$('.user-progress-bar').css('width', '50%');
							$('.user-progress-bar').addClass('progress-bar-danger');
							$(".user-progress-bar span").text(i2+'% Complete - Error Found ');
						}, 3000);
					}
                },
                error  :       function(msg)     
                { 
                	$("#btn-update").attr('disabled', false);
                	$("#modal-daily-sales").show(); 
                    alert('Something went wrong!');
                }
        });
        return false;
	}

	function modalClose(){
		location.reload();
	}

	$('#form-reports').on('submit', function (e) 
    {
        $(':input[type="submit"]').prop('disabled', true);
        var data = $("#form-reports").serialize();
        $.ajax({
               type      :      'POST',
               url       :      '../transaction.php',
               data      :       data,
                success  :       function(msg)     
                { 
                    location.reload();
                },
                error  :       function(msg)     
                { 
                    alert('Something went wrong!');
                }
        });
        return false;
    });

 </script>
</html>

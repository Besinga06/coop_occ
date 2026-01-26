<?php  require('includes/header.php');?>
<style type="text/css">
#show-search-user{
    background-color: #26a69a;
	min-height: 300px;
    max-height: 300px;
	overflow-y: auto;
	z-index: 100;
	position: absolute;
	width: 100%;
	display: none;
}
#show-search-user::-webkit-scrollbar-track
{
	background-color: #F5F5F5;
}

#show-search-user::-webkit-scrollbar
{
	width: 12px;
	background-color: #F5F5F5;
}

#show-search-user::-webkit-scrollbar-thumb
{
	background-color:#3c8881;
}

#show-search-customer{
    position: absolute;
    min-height: 300px;
    max-height: 300px;
    overflow-y:scroll;
    background: #26a69a;
    width: 100%;
    z-index: 10;
    padding: 0px !important;
    display: none;
}

#show-search-customer::-webkit-scrollbar-track
{
	background-color: #F5F5F5;
}

#show-search-customer::-webkit-scrollbar
{
	width: 12px;
	background-color: #F5F5F5;
}

#show-search-customer::-webkit-scrollbar-thumb
{
	background-color:#3c8881;
}

.ul-search{
    list-style-type: none;
    background: #26a69a;
    color: #fff;
    margin-left: -25px;
    font-size: 12px;
}

.ul-search li{
    padding-top: 10px;
    padding-left: 10px;
    padding-bottom:10px;
    height: 40px;
    font-size: 12px;
    cursor: pointer;
}
.ul-search li{
   border-bottom: 1px solid #dddddd;
}
.name-span{
    font-size: 12px;
}
#customer-input {
    width: 200px;
}
#searchclear {
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

#user-input {
    width: 200px;
}
#searchclearuser {
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

.containers {
  display: block;
  position: relative;
  padding-left: 25px;
  margin-bottom: 22px;
  cursor: pointer;
  font-size: 14px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
  color: #5a5959;
}


.containers input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}


.checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 25px;
  width: 25px;
  background-color:#bfbfbf;
}


.containers:hover input ~ .checkmark {
  background-color:#bfbfbf;
}


.containers input:checked ~ .checkmark {
  background-color: #26a69a;
}


.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}


.containers input:checked ~ .checkmark:after {
  display: block;
}


.containers .checkmark:after {
  left: 9px;
  top: 5px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 3px 3px 0;
  -webkit-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  transform: rotate(45deg);
}
</style>

<?php
    
    require('db_connect.php');
    $query = "SELECT  *  FROM tbl_panda_balance  ";
    $result = $db->query($query);
    while ($row = $result->fetchArray())
	{   
		$amount = $row['amount']/100*70;
		
	}
	
?>
<body class="layout-boxed navbar-top">
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

	<div class="page-container">
		<div class="page-content">
			<div class="content-wrapper">
				<div class="page-header page-header-default">
					<div class="page-header-content">
						<div class="page-title">
							<h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold">Dashboard </span> - Food Panda  Report</h4>
						</div>
					</div>
					<div class="breadcrumb-line">
						<ul class="breadcrumb">
							<li><a href="index.php"><i class="icon-home2 position-left"></i> Dashboard</a></li>
							<li><a href="javascript:;">Reports</a></li>
							<li class="active">Food Panda Payment</li>
						</ul>
						<ul class="breadcrumb-elements">
							<li><a href="javascript:;" data-toggle="modal" data-target="#modal-add"><i class="icon-add position-left text-teal-400"  ></i>Add Payment</a></li>
						</ul>
					</div>
				</div>
				<div class="content">
				    <div class="panel panel-body ">
					    <h3>Accounts Recievable : <span id="total_amount"></span></h3>
					</div>
					<div class="panel panel-white border-top-xlg border-top-teal-400">
						<div class="panel-heading">
							<h6 class="panel-title"><i class="icon-chart text-teal-400"></i> List of payments <a class="heading-elements-toggle"><i class="icon-more"></i></a></h6>
						</div>
						
						<div class="panel-body product-div2">
				
						    <table class="table datatable-button-html5-basic table-hover table-bordered  ">
						       <thead>
						       	    <tr style="border-bottom: 4px solid #ddd;background: #eee">
							   	    	<th>Payment ID</th>
									    <th>Date</th>
										<th>Employee</th>
										<th style="text-align:right">Amount</th>
							   	    </tr>
						       </thead>
							   <tbody>
									<?php 
									    $total_amount = 0;
										$query = "SELECT * FROM tbl_panda_payment INNER JOIN tbl_users ON tbl_panda_payment.user_id=tbl_users.user_id ORDER BY payment_id DESC ";
										$result = $db->query($query);
										while($row = $result->fetchArray()) {
											$total_amount+=$row['amount'];
										
									?>
									<tr>
									    <td><?= $row['payment_id'] ?></td>
									    <td><?= date('F d, Y h:i A', strtotime($row['date_added'])); ?></td>
										<td><?= $row['fullname'] ?></td>
										<td style="text-align:right;font-weight:900"><?= number_format($row['amount'],2) ?></td>
									</tr>
									<?php } ?>
							   </tbody>
						       <tfoot>
							        <tr style="border-bottom: 4px solid #ddd;background: #eee">
									    <th>Payment ID</th>
									    <th>Date</th>
										<th>Employee</th>
										<th style="text-align:right">Amount</th>
							   	    </tr>
							   </tfoot>
							</table>
						</div>
				    </div>

				</div>
				<?php  require('includes/footer-text.php');?>

			</div>

		</div>

	</div>
</body>
<?php  require('includes/footer.php');?>
 <div id="modal-all" class="modal fade" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-full">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="title-all"></h5>
				<button type="button" class="close" title="Click to close (Esc)" data-dismiss="modal">&times;</button>
			</div>

			<div class="modal-body"   >
			    <form action="#" id="form-payment" class="form-horizontal" data-toggle="validator" role="form">
			     <div id="show-data-all"></div> 
				
			</div>

			<div class="modal-footer" id="footer-sales">
			     <div class="row pull-right">
			     	 <div class="col-md-6  no-padding ">
			     	      <div id="show-button"></div>
			     	 </div>
			     </div>
				
				</form>
			</div>
		</div>
	</div>
</div>
<div id="modal-add" class="modal fade" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" data-toggle="tooltip" title="Press Esc" class="close" data-dismiss="modal">&times;</button> 
                <h5 class="modal-title">New Payment Form</h5>
            </div>
            <div class="modal-bodys"> 
                <form action="#" id="form-new" class="form-horizontal" data-toggle="validator" role="form">
                    <input type="hidden" name="save-panda-payment" ></input>
                    <input type="hidden" value="<?=  $balance ?>" name="balance"  >
                    <div class="form-body" style="padding-top: 20px">
                        <div id="display-msg"></div>
                        <div class="form-group">
                            <label for="exampleInputuname_4" class="col-sm-3 control-label" >Amount</label>
                            <div class="col-sm-9">
                                <div class="input-group input-group-xlg">
                                    <span class="input-group-addon" ><i class="icon-pencil7 text-size-base"></i></span>
                                    <input  class="form-control filterme"  autocomplete="off"  name="amount" id="amount" placeholder="Enter amount" type="text"   data-error=" Please enter valid amount." required >
                                </div>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
            <button id="btn-submit" type="submit" class="btn bg-teal-400 btn-labeled"><b><i class="icon-add"></i></b> Save Payment</button>
            </div>
            </form>
        </div>
    </div>
</div>

<script src="../js/validator.min.js"></script>
<script type="text/javascript" src="../assets/js/plugins/notifications/jgrowl.min.js"></script>
<script type="text/javascript" src="../assets/js/plugins/ui/moment/moment.min.js"></script>
<script type="text/javascript" src="../assets/js/plugins/pickers/daterangepicker.js"></script>
<script type="text/javascript" src="../assets/js/plugins/pickers/anytime.min.js"></script>
<script type="text/javascript" src="../assets/js/plugins/pickers/pickadate/picker.js"></script>
<script type="text/javascript" src="../assets/js/plugins/pickers/pickadate/picker.date.js"></script>
<script type="text/javascript" src="../assets/js/plugins/pickers/pickadate/picker.time.js"></script>
<script type="text/javascript" src="../assets/js/plugins/pickers/pickadate/legacy.js"></script>
<script type="text/javascript" src="../assets/js/pages/picker_date.js"></script>

<!-- Theme JS files -->
<script type="text/javascript" src="../assets/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="../assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
<script type="text/javascript" src="../assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
<script type="text/javascript" src="../assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
<script type="text/javascript">
	var payment = '<?= $total_amount ?>';
	var amount = '<?= $amount ?>';
	var total_amount = parseFloat(amount) -  parseFloat(payment);
	$("#total_amount").text(total_amount.toFixed(2));
	$('#form-sales').on('submit', function (e) 
	{
	    $(':input[type="submit"]').prop('disabled', true);
	    var data = $("#form-sales").serialize();
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

	function closer()
	{
		window.location='products.php';
	}



	$( "#user-input" ).keyup(function() {
	    $("#show-search-user").show();
	    var keywords = $(this).val(); 
	    if (keywords!="") {
	        $.ajax({
	            type      :      'GET',
	            url       :      '../transaction.php',
	            data      :       {search_user:"",keywords:keywords},
	            success  :       function(msg)     
	            {  
	                $("#show-search-user").html(msg);
	            },
	            error  :       function(msg)     
	            { 
	                alert('Something went wrong!');
	            }
	        });
	    }else{
	        $( "#user-input" ).click();
	    }

	});

	$( "#user-input" ).click(function() {
	    $("#show-search-user").show();
	    $("#show-search-customer").hide();
	    $.ajax({
	        type      :      'GET',
	        url       :      '../transaction.php',
	        data      :       {search_user:"",keywords :""},
	        success  :       function(msg)     
	        {   
	            $("#show-search-user").html(msg);
	        },
	        error  :       function(msg)     
	        { 
	            alert('Something went wrong!');
	        }
	    });
	});



	function select_user(el)
	{   
	    var user_id = $(el).attr('user_id');
	    var name = $(el).attr('name');
	    $("#user_id").val(user_id);
	    $("#user-input").val(name);
	    $("#show-search-user").hide();
	}
   
    $("#searchclear").click(function(){
    	$("#customer-input").val("");
        $("#show-search-customer").hide();
    });
    
    $("#searchclearuser").click(function(){
    	$("#user-input").val("");
        $("#show-search-user").hide();
    });

    function clear_filter()
    {  
    	$.ajax({
	        type      :      'POST',
	        url       :      '../transaction.php',
	        data      :       {clear_filter_deposits:""},
	        success  :       function(msg)     
	        {   
	            location.reload();
	        }
	        
	    });
	}
	
	$('#form-new').validator().on('submit', function (e) 
        {
        if (e.isDefaultPrevented()) 
        {
        }else { 
            var amount = parseFloat($("#amount").val()); 
            var balance = total_amount;  
            if(amount > balance){
                $.jGrowl('Amount not allowed.', {
                    header: 'Error Notification',
                    theme: 'alert-styled-right bg-danger'
                });
                return false;
            }
            $(':input[type="submit"]').prop('disabled', true);
            var data = $("#form-new").serialize();
            $.ajax({
                    type      :      'POST',
                    url       :      '../transaction.php',
                    data      :       new FormData(this),
                    contentType: false,  
                    cache: false,  
                    processData:false,  
                    success  :       function(msg)     
                    {  console.log(msg);
                        $.jGrowl('Payment successfully saved.', {
                            header: 'Success Notification',
                            theme: 'alert-styled-right bg-success'
                        });
                         setTimeout(function(){  location.reload();   }, 1000);
                    },
                    error  :       function(msg)     
                    { 
                        alert('Something went wrong!');
                    }
            });
            return false;
        } 
    });



</script>

 
</html>

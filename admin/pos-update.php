<?php session_start(); ?>
<?php 
require('db_connect.php');
$check_session  = $_SESSION['is_login_yes'];

$settings = "SELECT * FROM tbl_settings";
$result_settings = $db->query($settings);
while($row = $result_settings->fetchArray()) {
	$tax = $row['tax']; 
}

?>
<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>POS</title>
	<link href="../assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="../assets/css/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="../assets/css/core.css" rel="stylesheet" type="text/css">
	<link href="../assets/css/components.css" rel="stylesheet" type="text/css">
	<link href="../assets/css/colors.css" rel="stylesheet" type="text/css">
	<link href="../css/my_css.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="../select/chosen.css">
    <link rel="stylesheet" href="../css/pos.css">
 </head>
<?php
	$products = "SELECT * FROM tbl_products ORDER BY product_name ASC";
	$result_products = $db->query($products);
?>
<link href="../css/my_css.css" rel="stylesheet" type="text/css" />
<body>
    <div id="spinner_div"></div>
    <div id="main-div" >  <!-- main body  -->
        <!-- top row -->
        <div class="row top-row">
            <div class="col-md-1  top-row1">
		        <img src="../images/logo.png" style="height: 80px;width: 80px" >
		    </div>
		    <div class="col-md-10  top-row2">
                <div class="row input-div">
                    <div class="col-md-4">
        		        <div class="form-group has-feedback has-feedback-left input-text" >
                            <input value="<?php if(!empty($_SESSION['pos-customer'])) { echo $_SESSION['pos-name']; } else{ echo 'Walk-in Customer'; }?>" class="form-control" placeholder="Customer" type="text" id="customer-input">
                            <div class="form-control-feedback">
                                    <i class="icon-search4 text-size-base"></i>
                            </div>
                             <div id="show-search-customer" ></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group has-feedback has-feedback-left input-text">
                            <input class="form-control" placeholder="product" type="text" id="product-input">
                            <div class="form-control-feedback">
                                    <i class="icon-search4 text-size-base"></i>
                            </div> 
                            <div id="show-search" ></div>
                        </div>   
                    </div>

                    <div class="col-md-2">
                        <div class="form-group has-feedback has-feedback-left input-text">
                            <input class="form-control"  style="width: 100px"  placeholder="1" onkeypress='return numbersonly(event)' value="1" type="text" id="quatity-input">
                            <div class="form-control-feedback">
                                    <i class="icon-cart text-size-base"></i>
                            </div>
                        </div>
                        
                    </div>
                    <div class="col-md-1" id="show-loader">
                        
                    </div>
                   
            </div>
         </div>
		     <div class="col-md-1  top-row3">
               <?php if ($_SESSION['session_type']=="admin") {?>
		       <a href="index.php"> <img src="../images/home-icon.png" style="height: 80px;width: 80px" ></a>
		       <?php }else{?>
               <?php }?>
            </div>
		    </div>
		</div>
		<!-- /top row -->
		<!-- middle row -->
		<div class="row middle-row" >
            <div class="col-md-9 no-padding">
		        <div class="cart-row">
		        	<table class="table-head-cart">
		        		<thead>
		        			<tr>
		        			    <th width="text-align:center;background:red!important">#</th>
								<th width="350px">Name</th>
								<th style="text-align: ;width: 120px;">&nbsp;&nbsp;&nbsp; Unit</th>
								<th style="text-align: center;width: 120px;">Price</th>
								<th style="max-width:70px;text-align: center">Quantity</th>
								<th style='text-align: left;width: 150px'>Total</th>
							</tr>
		        		</thead>
		        	</table>
		        	<div id="cart-divs" >
			        	<table class="table-body">
			        		<tbody id="show-cart"></tbody>
			        	</table>
		        	</div>
		        </div>
		        <div style="width: 100%;text-align: center;padding-top: 6px;color: #fff">
					<p> © 2018 CURT POS. All rights reserved | Developed by <a style="text-decoration: none;color: #fff"  href="https://www.facebook.com/niel.daculan" target="_blank">Niel M. Daculan</a></p>
				</div>
		    </div>
		    <div class="col-md-3 middle-div no-padding">
		    	<div align="center"><h4 style="color: #fff">#0000000000<span id="sales-no"></span></h4></div>
		        <table class="table-summary">	
		             <tr class="td-payable">
		              	<td >&nbsp;</td>
		              	<td></td>
		              </tr>         
		              <tr class="td-payable">
		              	<td >Sub Total</td>
		              	<td id="show-subtotal" style="font-weight: bold;text-align: right;padding-right: 25px"></td>
		              </tr>
		              <tr class="td-payable">
		              	<td >Discount</td>
		              	<td style="font-weight: bold;text-align: right;padding-right: 25px" id="show-discount"></td>
		              </tr>
		               <tr class="td-payable">
		              	<td >Vat Sales</td>
		              	<td id="show-vat-sales" style="font-weight: bold;text-align: right;padding-right: 25px"></td>
		              </tr>
		               <tr class="td-payable">
		              	<td >Vat Amount(<?= $tax ?>%)</td>
		              	<td id="show-vat-amount" style="font-weight: bold;text-align: right;padding-right: 25px"></td>
		              </tr>

		              <tr class="td-payable">
		              	<td >&nbsp;</td>
		              	<td></td>
		              </tr>
		        </table>
		        <div class="amount-due-div">
		             <span style="font-size: 12px">Amount Due</span> 
		         	 <div class="grand-total-div"><p id="grand-total"></p></div>
		        </div>

		        <div class="payment-div" onclick="add_payment()" >
		              <span class="payment-shortcut">F1</span> 
		               <p class="payment-text">PAYMENT</p>
		         </div>
		        
		        <div class="other-div">
                    <div class="col-md-6  no-padding customer-div" onclick="add_discount()">
                       <span class="discount-shortcut">F2</span>
                        <div class="button-text">Discount</div>
                    </div>
                    <div class="col-md-6  no-padding customer-div" onclick="my_sale();">
                         <span class="discount-shortcut">F4</span>
                        <div class="button-text" >My Sales</div>
                    </div>
                </div> 
                <div class="other-div">
                   <div class="col-md-6  no-padding customer-div">
                       <span class="discount-shortcut">F10</span>
                        <div class="button-text" onclick="view_products()">Products</div>
                    </div>
                    <div class="col-md-6  no-padding customer-div">
                       <span class="discount-shortcut">F8</span>
                        <div class="button-text" onclick="new_customer()">New Customer</div>
                    </div>
                     
                </div>  
                <div class="other-div">
                    <div class="col-md-12 no-padding cancel-sale-btn">
                         <span class="discount-shortcut">F9</span>
                        <div class="button-text-cancel" onclick="cancel_sale()">Cancel Update</div>
                    </div>
                   
                </div>   
		    </div>
		    </div>
		</div>
		<!-- middle row -->
		
    </div><!-- /main body  -->

    <!--  modal add payment-->
    <div id="modal-payment" class="modal fade" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-sm after-sales">
			<div class="modal-content ">
				<div class="modal-header">

				</div>
				<div class="modal-body" id="show-payment" >
				     <form action="#" id="form-payment" class="form-horizontal" data-toggle="validator" role="form">
				        <input type="hidden" name="save-payment" ></input>
				        <input type="hidden" id="cust_id" value="<?php if(!empty($_SESSION['pos-customer'])) { echo $_SESSION['pos-customer']; } else{ echo '1'; }?>" ></input>
                        <div class="row ">
					        <div class="col-md-12">
					            <div class=" bottom-div" style="">
					         	    Amount Due : 
					         	     <div id="amount-due-div"><span id="amount-due"></span></div>
					         	</div>
					         	<div class="form-group">
		                            <div class="col-sm-12">
		                                <div class="form-group  input-text" >
				                            <input class="form-control filterme" type="text" autocomplete="off"   name="payment" id="payment" placeholder="Payment" type="text"   >
				                            <div class="form-control-feedback">
				                                    <i class="icon-pencil7 text-size-base"></i>
				                            </div>
				                        </div>
		                            </div>
		                        </div> 
		                        <div style="background: #eee;padding-left: 50px;padding-top: 30px;padding-bottom: 30px;margin-top: -20px" >
			                        <table style="width: 100%">
			                        	<tr>
			                        		<td><button type="button" class="btn btn-primary btn-keyboards" onclick="select_key(1)">1</button></td>
			                        		<td><button type="button" class="btn btn-primary btn-keyboards" onclick="select_key(2)">2</button></td>
			                        		<td><button type="button" class="btn btn-primary btn-keyboards" onclick="select_key(3)">3</button></td>
			                        	</tr>
			                        	<tr>
			                        		<td colspan="3">&nbsp;</td>
			                        	</tr>
			                        	<tr>
			                        		<td><button type="button" class="btn btn-primary btn-keyboards" onclick="select_key(4)">4</button></td>
			                        		<td><button type="button" class="btn btn-primary btn-keyboards" onclick="select_key(5)">5</button></td>
			                        		<td><button type="button" class="btn btn-primary btn-keyboards" onclick="select_key(6)">6</button></td>
			                        	</tr>
			                        	<tr>
			                        		<td colspan="3">&nbsp;</td>
			                        	</tr>
			                        	<tr>
			                        		<td><button type="button" class="btn btn-primary btn-keyboards" onclick="select_key(7)">7</button></td>
			                        		<td><button type="button" class="btn btn-primary btn-keyboards" onclick="select_key(8)">8</button></td>
			                        		<td><button type="button" class="btn btn-primary btn-keyboards" onclick="select_key(9)">9</button></td>
			                        	</tr>
			                        	<tr>
			                        		<td colspan="3">&nbsp;</td>
			                        	</tr>
			                        	<tr>
			                        		<td><button type="button" class="btn btn-warning btn-keyboards" onclick="clear_last()">x</button></td>
			                        		<td><button type="button" class="btn btn-info btn-keyboards" onclick="select_key('.')">.</button></td>
			                        		<td><button type="button" class="btn btn-primary btn-keyboards" onclick="select_key(0)">0</button></td>
			                        	</tr>
			                        	<tr>
			                        		<td colspan="3">&nbsp;</td>
			                        	</tr>
			                        	<tr>
			                        		<td colspan="3">
                                                <button type="button" class="btn btn-danger btn-clear" onclick="clear_all()">Clear</button>
                                                <button type="submit" class="btn btn-success btn-clear" >ENTER</button>
                                            </td>
			                        </table>
		                        </div>
					        </div>
						</div>
					
				</div>
					</form>
			</div>
		</div>
	</div>
	 <!-- /modal add payment -->

	<div id="modal-all" class="modal fade" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-full">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="title-all"></h5>
					<button type="button" class="close " onclick="refresh()" title="Click to close" >&times;</button>
				</div>

				<div class="modal-body"  >
				   <!--  <form action="#" id="form-payment" class="form-horizontal" data-toggle="validator" role="form"> -->
				     <div id="show-data-all" style="max-height: 580px;min-height: 200px;overflow-y:auto "></div> 
					
				</div>

				<div class="modal-footer" id="footer-sales">
				     <div class="row pull-right">
				     	<!--  <div class="col-md-6 " >
				     	     <button type="button" class="btn btn-danger btn-labeled " data-dismiss="modal"><b><i class="icon-cross"></i></b> Close[Esc]</button> 
				     	 </div> -->
				     	 <div class="col-md-6  no-padding ">
				     	      <div id="show-button"></div>
				     	 </div>
				     </div>
					
				<!-- 	</form> -->
				</div>
			</div>
		</div>
	</div>

	<div id="modal-discount" class="modal fade" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="title-all"></h5>
				</div>
				<div class="modal-body" id="show-payment" >
				   <form action="#" id="form-discount" class="form-horizontal" data-toggle="validator" role="form">
				     <input type="hidden" name="save-discount" ></input>
				      <div class="row ">
					        <div class="col-md-12">
					         	<div class="form-group">
		                            <div class="col-sm-12">
		                                <div class="form-group has-feedback has-feedback-left input-text" >
				                            <input class="form-control filterme"  type="text" autocomplete="off"   name="discount" id="discount" placeholder="Discount" type="text"    >
				                            <div class="form-control-feedback">
				                                    <i class="icon-pencil7 text-size-base"></i>
				                            </div>
				                        </div>
		                            </div>
		                        </div> 
		                        <div style="background: #eee;padding-left: 50px;padding-top: 30px;padding-bottom: 30px;margin-top: -20px" >
			                        <table style="width: 100%">
			                        	<tr>
			                        		<td><button type="button" class="btn btn-primary btn-keyboards" onclick="select_key2(1)">1</button></td>
			                        		<td><button type="button" class="btn btn-primary btn-keyboards" onclick="select_key2(2)">2</button></td>
			                        		<td><button type="button" class="btn btn-primary btn-keyboards" onclick="select_key2(3)">3</button></td>
			                        	</tr>
			                        	<tr>
			                        		<td colspan="3">&nbsp;</td>
			                        	</tr>
			                        	<tr>
			                        		<td><button type="button" class="btn btn-primary btn-keyboards" onclick="select_key2(4)">4</button></td>
			                        		<td><button type="button" class="btn btn-primary btn-keyboards" onclick="select_key2(5)">5</button></td>
			                        		<td><button type="button" class="btn btn-primary btn-keyboards" onclick="select_key2(6)">6</button></td>
			                        	</tr>
			                        	<tr>
			                        		<td colspan="3">&nbsp;</td>
			                        	</tr>
			                        	<tr>
			                        		<td><button type="button" class="btn btn-primary btn-keyboards" onclick="select_key2(7)">7</button></td>
			                        		<td><button type="button" class="btn btn-primary btn-keyboards" onclick="select_key2(8)">8</button></td>
			                        		<td><button type="button" class="btn btn-primary btn-keyboards" onclick="select_key2(9)">9</button></td>
			                        	</tr>
			                        	<tr>
			                        		<td colspan="3">&nbsp;</td>
			                        	</tr>
			                        	<tr>
			                        		<td><button type="button" class="btn btn-warning btn-keyboards" onclick="clear_last2()">x</button></td>
			                        		<td><button type="button" class="btn btn-danger btn-keyboards" onclick="select_key2('.')">.</button></td>
			                        		<td><button type="button" class="btn btn-primary btn-keyboards" onclick="select_key2(0)">0</button></td>
			                        	</tr>
			                        	<tr>
			                        		<td colspan="3">&nbsp;</td>
			                        	</tr>
			                        	<tr>
			                        		<td colspan="3"><button type="button" class="btn btn-warning btn-clear" onclick="clear_all2()">Clear</button>  <button type="submit" class="btn btn-success btn-clear" >ENTER</button></td>
			                        	</tr>
			                        </table>
		                        </div>
					        </div>
					        
					</div>
					
				</div>
				
					</form>
			</div>
		</div>
	</div>

	<div id="modal-new" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
				
					<h5 class="modal-title"><i class="icon-menu7"></i> &nbsp;New Customer Form</h5>
				</div>
				<div class="modal-body">
					<form action="#" id="form-customer" class="form-horizontal" data-toggle="validator" role="form">
					    <input type="hidden" name="save-customer-sales" ></input>
				        <div class="form-body">
                            <div class="form-group">
	                            <label for="exampleInputuname_4" class="col-sm-3 control-label" >Name</label>
	                            <div class="col-sm-9">
	                                <div class="input-group input-group-xlg">
	                                	<span class="input-group-addon" ><i class="icon-pencil7 text-size-base"></i></span>
	                                    <input class="form-control"  name="name" placeholder="Name" type="text"    data-error=" Name is required." required >
	                                </div>

	                                <div class="help-block with-errors"></div>
	                            </div>
	                        </div>  

	                        <div class="form-group">
	                            <label for="exampleInputuname_4" class="col-sm-3 control-label" >Address</label>
	                            <div class="col-sm-9">
	                                <div class="input-group input-group-xlg">
	                                	<span class="input-group-addon" ><i class="icon-pencil7 text-size-base"></i></span>
	                                    <input class="form-control"  name="address" placeholder="Address" type="text"    data-error=" Address is required."  >
	                                </div>

	                                <div class="help-block with-errors"></div>
	                            </div>
	                        </div>  

	                        <div class="form-group">
	                            <label for="exampleInputuname_4" class="col-sm-3 control-label" >Contact</label>
	                            <div class="col-sm-9">
	                                <div class="input-group input-group-xlg">
	                                	<span class="input-group-addon" ><i class="icon-pencil7 text-size-base"></i></span>
	                                    <input class="form-control"  name="contact" placeholder="Contact" type="text"    data-error=" Contact is required."  >
	                                </div>

	                                <div class="help-block with-errors"></div>
	                            </div>
	                        </div>  

                        </div>
				</div>
				<hr>
				<div class="modal-footer">
							<button type="submit" class="btn bg-teal-400 btn-labeled"><b><i class="icon-reading"></i></b> Save Customer</button>
						</div>
				</div>
			</div>
		</div>
	</div>

    <div id="print-receipt"></div>
	<input type="hidden" id="new-sales"></input>
	<input type="hidden"  id="discount-open" > 
	<input type="hidden"  id="payment-open" > 
	<input type="" id="sales_no">
    <script type="text/javascript" src="../assets/js/core/libraries/jquery.min.js"></script>
    <script type="text/javascript" src="../assets/js/core/libraries/bootstrap.min.js"></script>
    <script type="text/javascript" src="../js/pos-update.js"></script>
    <script type="text/javascript" src="../js/jquery.scannerdetection.js"></script>
    <script type="text/javascript" src="../js/jquery.key.js"></script>
    <script type="text/javascript" src="../assets/js/plugins/notifications/jgrowl.min.js"></script>
    <script src="../js/validator.min.js"></script>
    <script type="text/javascript">
        function numbersonly(e){
		    var unicode=e.charCode? e.charCode : e.keyCode
		    if (unicode!=8){ //if the key isn't the backspace key (which we should allow)
		        if (unicode<48||unicode>57) //if not a number
		            return false //disable key press
		    }
		}

        $(window).load(function() { 
        	var session = "<?=$check_session ?>"; 
        	if (session=="") {
        		window.location = '../index.php';
        	}
	        $("#spinner_div").fadeOut("slow"); 

	    });
        $(document).scannerDetection({
			timeBeforeScanTest: 200, // wait for the next character for upto 200ms
			startChar: [120], // Prefix character for the cabled scanner (OPL6845R)
			endChar: [13], // be sure the scan is complete if key 13 (enter) is detected
			avgTimeByChar: 40, // it's not a barcode if a character takes longer than 40ms
			onComplete: function(barcode, qty){   
				   $("#show-loader").html('<i class="icon-spinner2 spinner" style="z-index: 30;position: absolute;font-size: 50px;color: #fff"></i>');
				   $.ajax({
					       type      :      'POST',
					       url       :      '../transaction.php',
					       data      :       {save_cartbarcode:"",barcode:barcode},
					        success  :       function(msg)     
					        {  console.log(msg);
					            if (msg=='1'){
					                total();
					                view_cart();
					                $("#show-loader").html('');
					            }
					            if (msg=='2'){
						            alert('Product is not exist');
						              $("#show-loader").html('');
						        } 
					        },
					        error  :       function(msg)     
					        { 
					            alert('Something went wrong!');
					        }
					 });
			} 
		});
		
        $.key('esc', function() { 
        	$("#discount-open").val('');
        	$("#payment-open").val('');
		    if ($("#new-sales").val()=="yes") { 
		     	location.reload();
		     	//$('.modal').modal('hide');
		    }else{
		  	    $('.modal').modal('hide');
		    }
		});

    	$.key('f1', function() {
    		
    		$("#discount-open").val('');
    		$("#amount-due").html($("#grand-total").text());
    		if ($("#payment-open").val()!='yes') {
    			$('.modal').modal('hide');
			   if (parseFloat($("#grand-total").text())==0) {
	                $.jGrowl('No product order.Please select product before you can add payment.', {
	                    header: 'Error Notification',
	                    theme: 'alert-styled-right bg-danger'
	                });
	            }else  if (parseFloat($("#grand-total").text())<1) {
	                $.jGrowl('Cannot proceed payment. Please check your discount.', {
	                    header: 'Error Notification',
	                    theme: 'alert-styled-right bg-danger'
	                });
	            }else{
	                $("#modal-payment").modal('show');
	            }
			    setTimeout(function(){ $("#payment").focus(); }, 500);
			    $("#payment-open").val('yes');
			}

		});

		$.key('f2', function() {
			$("#payment-open").val('');
			if ($("#discount-open").val()!='yes') {
				$('.modal').modal('hide');
	            if (parseFloat($("#grand-total").text())==0) {
	                $.jGrowl('No product order.Please select product before you can add discount.', {
	                    header: 'Error Notification',
	                    theme: 'alert-styled-right bg-danger'
	                });
	            }else{
	                $("#modal-discount").modal('show');
	                setTimeout(function(){ $("#discount").focus(); }, 100);
	            }
	            $("#discount-open").val('yes');
			}
            
	    	
		});

		$.key('f8', function() {
            $('.modal').modal('hide');
            $("#modal-new").modal('show');
		});

		$.key('f9', function() {
            $('.modal').modal('hide');
	    	cancel_sale();
		});

		$.key('f4', function() {
            $('.modal').modal('hide');
	    	my_sale();
		});


		$('.filterme').keypress(function(eve) {
		if ((eve.which != 46 || $(this).val().indexOf('.') != -1) && (eve.which < 48 || eve.which > 57) || (eve.which == 46 && $(this).caret().start == 0) ) {
		eve.preventDefault();
		}
		$('.filterme').keyup(function(eve) {
		if($(this).val().indexOf('.') == 0) {    $(this).val($(this).val().substring(1));
		}
		});
		});

		$('#payment').keyup(function(e){
		 	if(e.keyCode == 8)
		 		var str = $('#payment').val(); 
                $('#payment').val(str.substring(0,str.length - 1));
		 }); 

		$('#discount').keyup(function(e){
		 	if(e.keyCode == 8)
		 		var str = $('#discount').val(); 
                $('#discount').val(str.substring(0,str.length - 1));
		 });
    </script>
</body>

</html>

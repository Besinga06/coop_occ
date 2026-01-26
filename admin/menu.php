<?php  require('includes/header.php');?>

<?php
    $unit = "SELECT * FROM tbl_units ORDER BY unit ASC";
    $result_unit = $db->query($unit);
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
                            <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold">Dashboard</span> - Menu</h4>
                        </div>
                    </div>
                    <div class="breadcrumb-line">
                        <ul class="breadcrumb">
                            <li><a href="index.php"><i class="icon-home2 position-left"></i> Dashboard</a></li>
                            <li class="active"><i class="icon-clipboard3 position-left"></i>Menu</li>
                        </ul>
                        <ul class="breadcrumb-elements">
                            <!-- <li><a href="javascript:;" data-toggle="modal" data-target="#modal-add"><i class="icon-add position-left text-teal-400"  ></i> Add Quantity</a></li>
                            <li><a href="javascript:;" data-toggle="modal" data-target="#modal-deduc"><i class="icon-subtract  position-left text-teal-400"  ></i> Deduct Quantity</a></li> -->
                            <li><a href="javascript:;" data-toggle="modal" data-target="#modal-new"><i class="icon-clipboard3 position-left text-teal-400"  ></i>New Menu</a></li>
                        </ul>
                    </div>
                </div>
                <!-- /page header -->
                <!-- Content area -->
                <div class="content">
                    <div class="row" id="row" style="display: none">
                        <?php 
                            $query = "SELECT * FROM tbl_menu ORDER BY menu_name ASC";
                            $result = $db->query($query);
                            while($row = $result->fetchArray()) {
                                $image = $row['image_link'];
                                if ($image!="") {
                                    $image_file = '../uploads/'.$image;
                                }else{
                                    $image_file = '../images/no-image.png';
                                }
                            ?> 
                                <div class="col-sm-2 menu-content">
                                    <div class="panel">
                                        <!-- <div class="panel-heading">
                                            <h6 class="panel-title"><?= $row['menu_name']?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h6>
                                            <div class="heading-elements">
                                                <ul class="icons-list">
                                                    <li><a data-action="collapse"></a></li>
                                                </ul>
                                            </div>
                                        </div> -->
                                        <div class="panel-body" style="padding: 0px">
                                            <div title="Click to view details" onclick="view_details(this)" menu_id="<?= $row['menu_id'];?>" class="menu-img" style="background: url(<?= $image_file ?>)"></div>
                                            <div class="menu-content">
                                                <div style="height: 60px;max-height: 60px;overflow: auto">
                                                   <p class="title"><?= $row['menu_name']?><p>
                                                </div>
                                                <div class="stock-inventory">
                                                    <div class="form-group">
                                                        <label> Stock: </label>
                                                        <input type="text"  onkeypress='return numbersonly(event)'  class="touchspin-empty menu-qty<?= $row['menu_id']?>"  value="<?= $row['quantity']?>">
                                                    </div>
                                                </div>
                                                <div class="checkbox checkbox-switchery">
                                                    <label> 
                                                        <input  type="checkbox" class="switchery available<?= $row['menu_id']?> available-switchery" <?= $row['available']  === 1 ?  'checked' : ""  ?>>
                                                        Availability
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-menu-footer">
                                                <input class="available"  value="<?= $row['available']?>"  hidden="" />
                                                <input class="menu_id"  value="<?= $row['menu_id']?>" hidden="" />
                                                <input class="quantity"  value="<?= $row['quantity']?>"  hidden=""/>
                                            <div class="flex-item">
                                               <button type="button"   class="btn btn-default btn-block cancel-update"><i class="icon-x position-left"></i> Cancel</button>
                                            </div>
                                            <div class="flex-item">
                                              <button type="button" available="<?= $row['available']?>" menu_id="<?= $row['menu_id']?>"  quantity="<?= $row['quantity']?>"  class="btn bg-teal-400 btn-block save-update"><i class="icon-checkmark4 position-left"></i> Update</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>  
                            <?php } ?>
                    </div>        
                </div>
                <?php  require('includes/footer-text.php');?>
                <!-- /content area -->
            </div>
            <!-- /main content -->
        </div>
        <!-- /page content -->
    </div>
    <!-- /page container -->
</body>
<div id="modal-new" class="modal fade" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" data-toggle="tooltip" title="Press Esc" class="close" data-dismiss="modal">&times;</button> 
                <h5 class="modal-title">New Menu Form</h5>
            </div>
            <div class="modal-bodys">
                <form action="#" id="form-new" class="form-horizontal" data-toggle="validator" role="form">
                    <input type="hidden" name="save-menu" ></input>
                    <div class="form-body" style="padding-top: 20px">
                        <div id="display-msg"></div>
                        <div class="form-group">
                            <label for="exampleInputuname_4" class="col-sm-3 control-label" >Image</label> 
                            <div class="col-sm-9">
                                <div class="input-group input-group-xlg"> <span class="input-group-addon" ><i class="icon-upload"></i></span> <input  class="form-control " placeholder="Enter quantity" type="file" name="fileToUpload"   data-error=" Please select image." required > </div>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputuname_4" class="col-sm-3 control-label"  >Name</label>
                            <div class="col-sm-9">
                                <div class="input-group input-group-xlg">
                                    <span class="input-group-addon" ><i class="icon-pencil7 text-size-base"></i></span>
                                    <input onblur="checkProductDuplicate(this)"  class="form-control currency" autocomplete="off"  name="product_name" id="discount" placeholder="Enter Menu Name" type="text"    data-error=" Menu Name is required." required >
                                </div>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputuname_4" class="col-sm-3 control-label" >Code</label>
                            <div class="col-sm-9">
                                <div class="input-group input-group-xlg">
                                    <span class="input-group-addon" ><i class="icon-pencil7 text-size-base"></i></span>
                                    <input  class="form-control currency" autocomplete="off"  name="product_code"  id="product-code" placeholder="Enter  code" type="text"   minlength="8"  data-error="Code is required& minimuim of 8 numbers." required >
                                    <span class="input-group-addon text-teal" style="cursor: pointer;" onclick="auto_generate()" data-toggle="tooltip" title="Auto Generate" ><i class=" icon-database-refresh text-size-base te"></i></span>
                                </div>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div> <input type="hidden"  name="cat_id" value="1" />
                        <!-- <div class="form-group">
                            <label for="exampleInputuname_4" class="col-sm-3 control-label" >Category</label>
                            <div class="col-sm-9">
                                <div class="input-group input-group-xlg">
                                    <span class="input-group-addon" ><i class="icon-pencil7 text-size-base"></i></span>
                                    <select class="form-control" name="cat_id" data-error="Category  is required." required  >
                                       <option class="form-control" value="">Select  category here..</option>
                                       <?php 
                                            $query = "SELECT * FROM tbl_category";
                                            $result = $db->query($query);
                                            while($row = $result->fetchArray()) {
                                        ?>
                                            <option class="form-control" value="<?= $row['cat_id'];?>"><?= $row['category_name'];?></option>
                                        <?php } ?>
                                    <select>
                                </div>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div> -->
                        <div class="form-group">
                            <label for="exampleInputuname_4" class="col-sm-3 control-label" >Price</label>
                            <div class="col-sm-9">
                                <div class="input-group input-group-xlg">
                                    <span class="input-group-addon" ><i class="icon-pencil7 text-size-base"></i></span>
                                    <input  class="form-control filterme"  autocomplete="off"  name="selling_price" id="discount" placeholder="Enter selling price" type="text"   data-error=" Please enter valid amount." required >
                                </div>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputuname_4" class="col-sm-3 control-label" >Unit</label>
                            <div class="col-sm-9">
                                <div class="input-group input-group-xlg">
                                    <span class="input-group-addon" ><i class="icon-pencil7 text-size-base"></i></span>
                                    <input type="text"  class="form-control"  placeholder="pcs,kg,ml,pack,box,etc." name="unit"  data-error=" Please enter unit." required>
                                </div>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                        <!-- <div class="form-group" style>
                            <label for="exampleInputuname_4" class="col-sm-3 control-label" >Track Inventory</label>
                            <div class="col-sm-9">
                                <input type="checkbox" value="1" name="is_track" style="height: 30px" />
                            </div>
                        </div> -->
                    </div>
            </div>
            <div class="modal-footer">
            <button id="btn-submit" type="submit" class="btn bg-teal-400 btn-labeled"><b><i class="icon-add"></i></b> Save Menu</button>
            </div>
            </form>
        </div>
    </div>
</div>

<?php  require('includes/footer.php');?>
<script src="../js/validator.min.js"></script>
<script type="text/javascript" src="../assets/js/plugins/notifications/jgrowl.min.js"></script> 
<script type="text/javascript" src="../assets/js/plugins/forms/styling/uniform.min.js"></script>
<script type="text/javascript" src="../assets/js/plugins/forms/styling/switchery.min.js"></script>
<script type="text/javascript" src="../assets/js/plugins/forms/styling/switch.min.js"></script>
<script type="text/javascript" src="../assets/js/plugins/forms/inputs/touchspin.min.js"></script>

<script type="text/javascript">
    $(window).load(function() { 
        var session = "<?=$check_session ?>"; 
        if (session=="") {
            window.location = '../index.php';
        }
        $("#row").fadeIn(2000); 

    });
   $(function() {
        // Switchery
        // ------------------------------

        // Initialize multiple switches
        if (Array.prototype.forEach) {
            var elems = Array.prototype.slice.call(document.querySelectorAll('.switchery'));
            elems.forEach(function(html) {
                var switchery = new Switchery(html);
            });
        }
        else {
            var elems = document.querySelectorAll('.switchery');
            for (var i = 0; i < elems.length; i++) {
                var switchery = new Switchery(elems[i]);
            }
        }

        $(".touchspin-empty").TouchSpin();
    });

	$('#form-new').validator().on('submit', function (e) 
        {
        if (e.isDefaultPrevented()) 
        {
        }else { 
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
                        $.jGrowl('Menu successfully saved.', {
                            header: 'Success Notification',
                            theme: 'alert-styled-right bg-success'
                        });
                         setTimeout(function(){  window.location='menu-details.php?menu_id='+msg;   }, 1000);
                    },
                    error  :       function(msg)     
                    { 
                        alert('Something went wrong!');
                    }
            });
            return false;
        } 
    });

    function closer()
    {
    	window.location='products.php';
    }

    function view_details(el)
    {
    	var menu_id = $(el).attr('menu_id');
    	window.location='menu-details.php?menu_id='+menu_id;
    }

    function auto_generate()
    {
    	$.ajax({
               type      :      'GET',
               url       :      '../transaction.php',
               data      :       {auto_generate_menu:""},
                success  :       function(msg)     
                { 
                    $("#product-code").val(msg);
                },
                error  :       function(msg)     
                { 
                    alert('Something went wrong!');
                }
        });
        return false;
    }

    function changePage(el)
    {
    	$("#length_change").val($(el).attr('val'));
    	$("#length_change").trigger('change');
    }

    function checkProductDuplicate(e){
		/// error kang boss dave na platform
    	// let product_name = $(e).val();
    	// $.ajax({
        //         type      :      'GET',
        //         //dataType  : 'JSON',
        //         url       :      '../transaction.php',
        //         data      :       {checkproductExist:"", product_name : product_name},
        //         success  :       function(msg)     
        //         {     console.log(msg);
        //             if(msg === '1'){
        //             	$("#btn-submit").prop('disabled', true);
        //             	$("#display-msg").html(`
        //             		<div class="alert alert-danger  alert-dismissible">
		// 						<button type="button" class="close" data-dismiss="alert" aria-label="Close" style="padding-right: 20px">
		// 					    <span aria-hidden="true">&times;</span>
		// 					  </button>
		// 						<span class="font-weight-semibold"><b>Oh snap!</b></span> Product Name is already added.
		// 				    </div>
        //             	`)
        //             }else{
        //             	$("#btn-submit").prop('disabled', false);
        //             	$("#display-msg").html('');
        //             }
        //         },
        //         error  :       function(msg)     
        //         { 
        //             alert('Something went wrong!');
        //         }
        // });
    }

	function check_menu(el)
	{
		if($(el).is(':checked'))
		{
			$(el).closest('tr').find('input.quantity').prop('disabled', false);
		}else{

		$(el).closest('tr').find('input.quantity').prop('disabled', true);
		$(el).closest('tr').find('input.quantity').val('');
		}
    }

    
    $(".available-switchery").click(function(){
       
        if ($(this).is(':checked')) {
            console.log("checkes");
        }else{
            console.log("not check");
        }
    });
    
    $(".cancel-update").click(function(){
        let menu_id = $(this).closest('div.panel-menu-footer').find('input.menu_id').val();
        let quantity = $(this).closest('div.panel-menu-footer').find('input.quantity').val();
        let available = $(this).closest('div.panel-menu-footer').find('input.available').val();
        $(".menu-qty"+menu_id).val(quantity);
        
        if ($(".available"+menu_id).is(':checked')) {
                if(available === '0'){  
                  $(".available"+menu_id).click();
                }
        }else{
            if(available !== '0'){ 
                $(".available"+menu_id).click();
            }
        }
    }); 

    $(".save-update").click(function(){
        $(this).attr('disabled',true);
        let menu_id = $(this).closest('div.panel-menu-footer').find('input.menu_id').val();
        let quantity = $(this).closest('div.panel-menu-footer').find('input.quantity').val();
        let available = $(this).closest('div.panel-menu-footer').find('input.available').val();
        let is_available = 1;
        if ($(".available"+menu_id).is(':checked')) {
            $(this).closest('div.panel-menu-footer').find('input.available').val(1);
        }else{
            $(this).closest('div.panel-menu-footer').find('input.available').val(0);
            is_available = 0;
        }
        $(this).closest('div.panel-menu-footer').find('input.quantity').val($(".menu-qty"+menu_id).val());
        $.ajax({
        type: 'POST',
        url: '../transaction.php',
        //dataType: 'JSON',
        data: { update_menu_single: "", menu_id: menu_id, available: is_available, quantity: $(".menu-qty"+menu_id).val() },
        success: (msg) =>  {  
            $.jGrowl('Menu successfully updated.', {
                header: 'Success Notification',
                theme: 'alert-styled-right bg-success'
            });
            $(this).attr('disabled',false);
        },
        error: function (msg) {
            $(this).attr('disabled',false);
            alert('Something went wrong!');
        }
     });
        
    });

</script>

</html>

<?php  require('includes/header.php');?>
<?php
	//$unit = "SELECT * FROM tbl_units ORDER BY unit ASC";
	//$result_unit = $db->query($unit);
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
                            <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold">Dashboard</span> - Menu Details</h4>
                        </div>
                    </div>
                    <div class="breadcrumb-line">
                        <ul class="breadcrumb">
                            <li><a href="index.php"><i class="icon-home2 position-left"></i> Dashboard</a></li>
                            <li><a href="menu.php"><i class="icon-barcode2 position-left"></i> Menu</a></li>
                            <li class="active"><i class="icon-dots position-left"></i>Menu Details</li>
                        </ul>
                        <ul class="breadcrumb-elements">
                            <li data-toggle="tooltip" title="Edit Details " ><a  href="javascript:;"  onclick="update_product()" ><i class="icon-pencil3 position-left"></i> </a></li>
                            <li data-toggle="tooltip" title="Upload  Image" ><a  href="javascript:;"  onclick="upload_image()" ><i class="icon-upload position-left"></i> </a></li>
                            <!-- <li data-toggle="tooltip" title="Add Ingredients"><a     href="javascript:;" data-toggle="modal" data-target="#modal_add_ingredients" ><i class="icon-lab position-left"></i> </a></li> -->
                            <li data-toggle="tooltip" title="Generate Bacode"><a     href="javascript:;" data-toggle="modal" data-target="#modal_add" ><i class=" icon-barcode2 position-left"></i> </a></li>
                            <!-- <li data-toggle="tooltip" title="Deduct Quantity" ><a    href="javascript:;" onclick="deduc_inventory()" ><i class="icon-subtract position-left"></i> </a></li>
                                <li data-toggle="tooltip" title="Add Quantity" ><a    href="javascript:;" onclick="add_quantity()" ><i class="icon-add position-left"></i> </a></li> -->
                        </ul>
                    </div>
                </div>
                <!-- /page header -->
                <?php  
                    $menu_id = $_GET['menu_id'];
                    $quantity = 0;
                       $query = "SELECT * FROM tbl_menu   WHERE menu_id='$menu_id'";
                                   $result = $db->query($query);
                       while($row = $result->fetchArray()) {
                       	$menu_name = $row['menu_name'];
                       	$product_code = $row['product_code'];
                       	$selling_price = $row['price'];
                       	$quantity = $row['quantity'];
                       	$critical_qty = $row['critical_qty'];
                    	$unit = $row['unit'];
                    	$is_track = $row['is_track'];
                       	$image = $row['image_link']; 
                       	if ($image!="") {
                       		$image_file = '../uploads/'.$image;
                       	}else{
                       		$image_file = '../images/no-image.png';
                       	}
                       }
                    ?>
                <!-- Content area -->
                <div class="content">
                   <div class="panel panel-flat">
						<div class="panel-body">
                        <div class="tab-pane active" id="information">
                                        <div class="panel panel-white border-top-xlg border-top-teal-400">
                                            <div class="panel-heading">
                                                <h6 class="panel-title"><i class="icon-list position-left text-teal-400"></i> Information</h6>
                                            </div>
                                            <!-- <input type="text" id="myInputTextField"> -->
                                            <div class="panel-body">
                                                <table class="table text-nowrap table-bordered  ">
                                                    <tr class="border-double">
                                                        <td class="text-size-small">Image</td>
                                                        <td> <img alt="<?=$image_file?>"  style="width: 150px;height: 150px;border: 2px solid #eee" src="<?=$image_file?>" /> </td>
                                                    </tr>
                                                    <tr class="border-double">
                                                        <td class="text-size-small">Code</td>
                                                        <td> <img alt="<?= $product_code ?>" src="barcode.php?codetype=Code39&size=40&text=<?= $product_code ?>&print=true" /> </td>
                                                    </tr>
                                                    <tr class="border-double">
                                                        <td class="text-size-small">Name</td>
                                                        <td><?= $menu_name ?> </td>
                                                    </tr>
                                                    <tr class="border-double">
                                                        <td class="text-size-small"> Price</td>
                                                        <td><?= number_format($selling_price,2) ?> </td>
                                                    </tr>
                                                    <tr class="border-double">
                                                        <td class="text-size-small">Stock</td>
                                                        <td><?= $quantity ?> </td>
                                                    </tr>
                                                    <tr class="border-double">
                                                        <td class="text-size-small">Unit</td>
                                                        <td><?= $unit ?> </td>
                                                    </tr>
                                                    <!-- <tr class="border-double">
                                                        <td class="text-size-small">Track Inventory</td>
                                                        <td><input type="checkbox" onclick="return false;" value="1" name="is_track" <?php echo $is_track == 1? 'checked' : '' ?>  style="height: 30px" /></td>
                                                    </tr> -->
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                            <!-- <div class="tabbable">
                               <ul class="nav nav-tabs bg-slate nav-justified">
									<li class="active"><a href="#information" data-toggle="tab">Information</a></li>
									<li><a href="#ingredients" data-toggle="tab">Ingredients</a></li>
								</ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="information">
                                        <div class="panel panel-white border-top-xlg border-top-teal-400">
                                            <div class="panel-heading">
                                                <h6 class="panel-title"><i class="icon-list position-left text-teal-400"></i> Information</h6>
                                            </div>
                                            <div class="panel-body">
                                                <table class="table text-nowrap table-bordered  ">
                                                    <tr class="border-double">
                                                        <td class="text-size-small">Image</td>
                                                        <td> <img alt="<?=$image_file?>"  style="width: 150px;height: 150px;border: 2px solid #eee" src="<?=$image_file?>" /> </td>
                                                    </tr>
                                                    <tr class="border-double">
                                                        <td class="text-size-small">Code</td>
                                                        <td> <img alt="<?= $product_code ?>" src="barcode.php?codetype=Code39&size=40&text=<?= $product_code ?>&print=true" /> </td>
                                                    </tr>
                                                    <tr class="border-double">
                                                        <td class="text-size-small">Name</td>
                                                        <td><?= $menu_name ?> </td>
                                                    </tr>
                                                    <tr class="border-double">
                                                        <td class="text-size-small"> Price</td>
                                                        <td><?= number_format($selling_price,2) ?> </td>
                                                    </tr>
                                                    <tr class="border-double">
                                                        <td class="text-size-small">In Stock</td>
                                                        <td><?= $quantity ?> </td>
                                                    </tr>
                                                    <tr class="border-double">
                                                        <td class="text-size-small">Unit</td>
                                                        <td><?= $unit ?> </td>
                                                    </tr>
                                                    <tr class="border-double">
                                                        <td class="text-size-small">Track Inventory</td>
                                                        <td><input type="checkbox" onclick="return false;" value="1" name="is_track" <?php echo $is_track == 1? 'checked' : '' ?>  style="height: 30px" /></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="ingredients">
                                        <div class="panel panel-white border-top-xlg border-top-teal-400">
                                            <div class="panel-heading">
                                                <h6 class="panel-title"><i class="icon-list position-left text-teal-400"></i> Ingredients</h6>
                                            </div>
                                          
                                            <div class="panel-body">
                                            <table class="table  table-hover table-bordered" id="table-product">
                                                    <thead>
                                                        <tr class="tr-table">
                                                            <th>Image</th>
                                                            <th >Product Code</th>
                                                            <th>Product Name</th>
                                                            <th>Quantity</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                            $query = "SELECT * FROM tbl_menu_ingredents INNER JOIN tbl_products ON tbl_menu_ingredents.product_id = tbl_products.product_id  WHERE menu_id='$menu_id'";
                                                            $result = $db->query($query);
                                                            while($row = $result->fetchArray()) {
                                                                $image = $row['image'];
                                                                if ($image!="") {
                                                                    $image_file = '../uploads/'.$image;
                                                                }else{
                                                                    $image_file = '../images/no-image.png';
                                                                }
                                                                
                                                            ?>
                                                        <tr>
                                                            
                                                            <td><img alt="<?=$image_file?>"  style="width: 90px;height: 90px;border: 2px solid #eee" src="<?=$image_file?>" /></td>
                                                            <td><img alt="<?= $product_code ?>" src="barcode.php?codetype=Code39&size=40&text=<?= $row['product_code'];?>&print=true" /></td>
                                                            <td><b><?= $row['product_name'];?></b></td>
                                                            <td><b><?= $row['quantity_menu'];?> </b> <?= $row['unit'];?></td>
                                                            <td style="text-align: center">
                                                            <a  data-toggle="tooltip" title="Delete" onclick="delete_ingredients(<?= $row['ingrdents_id']?>)" href="javascript:;"  title="Cancel" href="javascript:;" ><i class="icon-trash position-left text-danger"></i> </a>
                                                            </td>
                                                        </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
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
    <div id="modal_add_ingredients" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Add Ingredients Form</h5>
            </div>
            <div class="modal-body" >
                <form class="form-horizontal" id="form-ingredients"  >
				   <input  type="hidden" name="save_menu_ingredients"  ></input> 
				   <input  type="hidden" name="menu_id" value="<?= $menu_id ?>" ></input> 
                    <table class="table  table-hover table-bordered" id="table-product">
                        <thead>
                            <tr class="tr-table">
                                <th></th>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $query = "SELECT * FROM tbl_products";
                                $result = $db->query($query);
                                while($row = $result->fetchArray()) {
                                	$image = $row['image'];
                                	if ($image!="") {
                                		$image_file = '../uploads/'.$image;
                                	}else{
                                		$image_file = '../images/no-image.png';
                                	}
                                ?>
                            <tr>
                                <td style="width:40px;text-align: center">
                                    <input onclick="check_menu(this)"  type="checkbox" name="product_id[]" value="<?= $row['product_id'];?>" />
                                </td>
                                <td><b><?= $row['product_name'];?></b></td>
                                <td style="text-align: center;width: 150px">
                                    <input class="form-control currency filterme quantity"  autocomplete="off"  name="quantity[]"  disabled=""  />
                                </td>
                                <td style="text-align: left;width: 150px"><?= $row['unit'];?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <div class="modal-footer"> 
                        <button type="submit" class="btn bg-teal-400 btn-labeled"><b><i class="icon-add"></i></b> Submit Ingredients</button>
                </form>
                </div>
            </div>
        </div>
    </div>
	</div>
	<div id="modal_upload" class="modal fade">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button> 
                    <h5 class="modal-title">Upload Image Form</h5>
                </div>
                <div class="modal-bodys" id="show-code" >
                    <form id="form-upload" class="form-horizontal" data-toggle="validator" method="POST" enctype="multipart/form-data >
                        <input  type="hidden" name="save_image_menu"  >
                        <input type="hidden"  name="menu_id" value="<?= $menu_id ?>" >
                        <input type="hidden" name="server_data" value="<?=$HTTP_HOST?>">
                        <div class="form-group">
                            <label for="exampleInputuname_4" class="col-sm-3 control-label" >Image</label> 
                            <div class="col-sm-9">
                                <div class="input-group input-group-xlg"> <span class="input-group-addon" ><i class="icon-upload"></i></span> <input  class="form-control " placeholder="Enter quantity" type="file" name="fileToUpload"   data-error=" Please select image." required > </div>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer"> 
                <button type="submit" class="btn bg-teal-400 btn-labeled"><b><i class="icon-pencil7"></i></b> Update Image</button>
                </form> 
                </div> 
            </div>
        </div>
    </div>
    <div id="modal_add" class="modal fade">
        <div class="modal-dialog modal-full">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h5 class="modal-title">Generate Barcode</h5>
                </div>
                <div class="modal-body" >
                    <div class="row">
                        <div class="col-md-4">
                            <form class="form-horizontal" id="form-generate"  >
                                <input  type="hidden" name="generate-barcode" value="<?= $product_code?>" ></input>
                                <div class="form-group">
                                    <label for="exampleInputuname_4"  class="col-sm-3 control-label" >Quantity</label>
                                    <div class="col-sm-9">
                                        <div class="input-group input-group-xlg">
                                            <span class="input-group-addon" ><i class="icon-pencil7"></i></span>
                                            <input  class="form-control currency" autocomplete="off"  name="quantity" id="quantity" placeholder="Enter quantity" type="text"   data-error=" Please enter valid quantity." required >
                                        </div>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputuname_4" class="col-sm-3 control-label" ></label>
                                    <div class="col-sm-9">
                                        <button type="submit" class="btn bg-teal-400 btn-labeled"><b><i class="icon-barcode2"></i></b> Generate</button>
                                        <button type="button" onclick="print_receipt()" class="btn btn-primary btn-labeled"><b><i class="icon-printer"></i></b> Print</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-8" id="show-code" style="height: 600px;overflow-y: auto;" ></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- <div id="modal_addQty" class="modal fade">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button> 
                    <h5 class="modal-title">Add Quantity Form</h5>
                </div>
                <div class="modal-bodys" id="show-code" >
                    <form action="#" id="form-add-qty" class="form-horizontal" data-toggle="validator" role="form">
                        <input  type="hidden" name="save_qty_menu"  ></input> 
                        <input name="menu_id" type="hidden" value="<?= $menu_id ?>" >
                        <div class="form-group">
                            <label for="exampleInputuname_4" class="col-sm-3 control-label" >Quantity</label> 
                            <div class="col-sm-9">
                                <div class="input-group input-group-xlg"> <span class="input-group-addon" ><i class="icon-pencil7"></i></span> <input   class="form-control currency" onkeypress='return numbersonly(event)' autocomplete="off"  name="quantity" id="quantityDeduc" placeholder="Enter quantity" type="text"   data-error=" Please enter valid quantity." required > </div>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer"> 
                <button type="submit" class="btn bg-teal-400 btn-labeled"><b><i class="icon-add"></i></b> Submit Damage</button>
                </form> 
                </div> 
            </div>
        </div>
    </div>
    <div id="modal_deduc" class="modal fade">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button> 
                    <h5 class="modal-title">Deduc Quantity Form</h5>
                </div>
                <div class="modal-bodys" id="show-code" >
                    <form action="#" id="form-deduc" class="form-horizontal" data-toggle="validator" role="form">
                        <input  type="hidden" name="save_deduc_menu"  ></input> 
                        <input name="menu_id" type="hidden" value="<?= $menu_id ?>" >
                        <div class="form-group">
                            <label for="exampleInputuname_4" class="col-sm-3 control-label" >Quantity</label> 
                            <div class="col-sm-9">
                                <div class="input-group input-group-xlg"> <span class="input-group-addon" ><i class="icon-pencil7"></i></span> <input   class="form-control currency"  onkeypress='return numbersonly(event)' autocomplete="off"  name="quantity" id="quantityDeduc" placeholder="Enter quantity" type="text"   data-error=" Please enter valid quantity." required > </div>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer"> 
                <button type="submit" class="btn bg-teal-400 btn-labeled"><b><i class="icon-add"></i></b> Submit Damage</button>
                </form> 
                </div> 
            </div>
        </div>
    </div> -->
    <div id="modal-update" class="modal fade" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" data-toggle="tooltip" title="Press Esc" class="close" data-dismiss="modal">&times;</button> 
                    <h5 class="modal-title">Update Menu Form</h5>
                </div>
                <div class="modal-bodys">
                    <form action="#" id="form-update" class="form-horizontal" data-toggle="validator" role="form">
                        <input type="hidden" name="update-menu" ></input>
                        <input type="hidden" name="menu_id" value="<?=$menu_id ?>" ></input>
                        <div class="form-body" style="padding-top: 20px">
                            <div class="form-group">
                                <label for="exampleInputuname_4" class="col-sm-3 control-label"  >Menu Name</label>
                                <div class="col-sm-9">
                                    <div class="input-group input-group-xlg">
                                        <span class="input-group-addon" ><i class="icon-pencil7 text-size-base"></i></span>
                                        <input  class="form-control currency" value="<?= $menu_name ?>" autocomplete="off"  name="product_name" id="discount" placeholder="Enter Menu Name" type="text"    data-error=" Product Name is required." required >
                                    </div>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputuname_4" class="col-sm-3 control-label" > Price</label>
                                <div class="col-sm-9">
                                    <div class="input-group input-group-xlg">
                                        <span class="input-group-addon" ><i class="icon-pencil7 text-size-base"></i></span>
                                        <input  class="form-control filterme" autocomplete="off"  value="<?= $selling_price ?>" name="selling_price" id="discount" placeholder="Enter selling price" type="text"   data-error=" Please enter valid quantity." required >
                                    </div>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputuname_4" class="col-sm-3 control-label" >Product Code</label>
                                <div class="col-sm-9">
                                    <div class="input-group input-group-xlg">
                                        <span class="input-group-addon" ><i class="icon-pencil7 text-size-base"></i></span>
                                        <input  class="form-control currency" autocomplete="off" value="<?= $product_code ?>"  name="product_code"  id="product-code" placeholder="Enter product code" type="text"   minlength="8"  data-error=" Product Code is required& minimuim of 8 numbers." required >
                                        <span class="input-group-addon text-teal" style="cursor: pointer;" onclick="auto_generate()" data-toggle="tooltip" title="Auto Generate" ><i class=" icon-database-refresh text-size-base te"></i></span>
                                    </div>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <!-- <div class="form-group">
                                <label for="exampleInputuname_4" class="col-sm-3 control-label" >Product Code</label>
                                <div class="col-sm-9">
                                    <div class="input-group input-group-xlg">
                                    	<span class="input-group-addon" ><i class="icon-pencil7 text-size-base"></i></span>
                                        <input onkeypress='return numbersonly(event)'  class="form-control currency" value="<?= $product_code ?>" autocomplete="off"  name="product_code"  id="product-code" placeholder="Enter product code" type="text"   minlength="8"  data-error=" Product Code is required& minimuim of 13 numbers." required >
                                        
                                    </div>
                                
                                    <div class="help-block with-errors"></div>
                                </div>
                                </div>  -->
                            <div class="form-group">
                                <label for="exampleInputuname_4" class="col-sm-3 control-label" >Unit</label>
                                <div class="col-sm-9">
                                    <div class="input-group input-group-xlg">
                                        <span class="input-group-addon" ><i class="icon-pencil7 text-size-base"></i></span>
                                        <input type="text"  class="form-control"  placeholder="pcs,kg,ml,pack,box,etc." name="unit" value="<?= $unit?>"  data-error=" Please enter unit." required>
                                    </div>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <!-- <div class="form-group">
                                <label for="exampleInputuname_4" class="col-sm-3 control-label" >Track Inventory</label>
                                <div class="col-sm-9">
                                    <input type="checkbox" <?php echo $is_track == 1? 'checked' : '' ?> value="1" name="is_track" style="height: 30px" />
                                </div>
                            </div> -->
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn bg-teal-400 btn-labeled"><b><i class="icon-pencil7"></i></b> Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
</body>
<?php  require('includes/footer.php');?>
<script type="text/javascript" src="../assets/js/plugins/tables/datatables/datatables.min.js"></script>
<script src="../js/validator.min.js"></script> 
<script type="text/javascript" src="../assets/js/plugins/notifications/jgrowl.min.js"></script>
<script type="text/javascript">
    $(function() {  
	    $('[data-toggle="tooltip"]').tooltip(); 
	    $.extend( $.fn.dataTable.defaults, {
	        autoWidth: false,
	        dom: '<"datatable-header"fBl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
	        language: {
	            search: '<span>Filter:</span> _INPUT_',
	            searchPlaceholder: 'Type to filter...',
	            lengthMenu: '<span>Show:</span> _MENU_',
	            paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }
	        }
	    });

	    // Basic initialization
	    $('.datatable-button-html5-basic').DataTable({
	    	"searching": false,
	    	"order": [[ 0, "desc" ]],
	    	"lengthMenu": [ [5, 25, 50, -1], [5, 25, 50, "All"] ]
	    });
	});
	
	$('#form-ingredients').on('submit', function (e) 
    {
        $(':input[type="submit"]').prop('disabled', true);
		var data = $(this).serialize();
		$.ajax({
				type      :      'POST',
				url       :      '../transaction.php',
				data      :       data,
				success  :       function(msg)     
				{ 
					$.jGrowl('Ingredients  successfully added.', {
						header: 'Success Notification',
						theme: 'alert-styled-right bg-success'
					}); 
					setTimeout(function(){ location.reload()  }, 1500);
					
				},
				error  :       function(msg)     
				{ 
					alert('Something went wrong!');
				}
		});
		return false;
    });
   
	$('#form-generate').validator().on('submit', function (e) 
    {
        if (e.isDefaultPrevented()) 
        {
        }else { 
            $(':input[type="submit"]').prop('disabled', true);
            var data = $(this).serialize();
            $.ajax({
                   type      :      'POST',
                   url       :      '../transaction.php',
                   data      :       data,
                    success  :       function(msg)     
                    { 
                         console.log(msg);
                         $("#show-code").html(msg);
                        $(':input[type="submit"]').prop('disabled', false);
                       
                    },
                    error  :       function(msg)     
                    { 
                        alert('Something went wrong!');
                    }
            });
            return false;
        } 
    });

    $('#form-update').validator().on('submit', function (e) 
    {
        if (e.isDefaultPrevented()) 
        {
        }else { 
            $(':input[type="submit"]').prop('disabled', true);
            var data = $(this).serialize();
            $.ajax({
                   type      :      'POST',
                   url       :      '../transaction.php',
                   data      :       data,
                    success  :       function(msg)     
                    {   console.log(msg);
                         
                        if (msg==1) {
                         	$.jGrowl('Product details successfully updated.', {
					            header: 'Success Notification',
					            theme: 'alert-styled-right bg-success'
					        }); 
					        setTimeout(function(){ location.reload()  }, 1500);
                         }else{
                         	alert('Something went wrong!');
                         }
                       
                    },
                    error  :       function(msg)     
                    { 
                        alert('Something went wrong!');
                    }
            });
            return false;
        } 
    });


    $('#form-damage').validator().on('submit', function (e) 
    {
        if (e.isDefaultPrevented()) 
        {
        }else { 
            $(':input[type="submit"]').prop('disabled', true);
            var data = $(this).serialize();
            $.ajax({
                   type      :      'POST',
                   url       :      '../transaction.php',
                   data      :       data,
                    success  :       function(msg)     
                    { 

                         if (msg==1) {
                         	$.jGrowl('Damage product succesfully save.', {
					            header: 'Success Notification',
					            theme: 'alert-styled-right bg-success'
					        }); 
					        setTimeout(function(){ location.reload()  }, 1500);
                         }else if(msg==2) {
                         	$.jGrowl('Quantity entered is greater than quantity left.', {
					            header: 'Error Notification',
					            theme: 'alert-styled-right bg-warning'
					        });
					        setTimeout(function(){ $("#loader").html("");  }, 1000);
					        
                         }else{
                         	alert('Something went wrong!');
                         }
                       
                    },
                    error  :       function(msg)     
                    { 
                        alert('Something went wrong!');
                    }
            });
            return false;
        } 
	});
	
	$('#form-deduc').validator().on('submit', function (e) 
	{  
		
        if (e.isDefaultPrevented()) 
        {
        }else { 
            //$(':input[type="submit"]').prop('disabled', true);
            var data = $(this).serialize();
            $.ajax({
                   type      :      'POST',
                   url       :      '../transaction.php',
                   data      :       data,
                    success  :       function(msg)     
                    {   console.log(msg);

                        //  if (msg==1) {
                        //  	$.jGrowl('Invetory succesfully updated.', {
					    //         header: 'Success Notification',
					    //         theme: 'alert-styled-right bg-success'
					    //     }); 
					    //     setTimeout(function(){ location.reload()  }, 1500);
                        //  }else if(msg==2) {
                        //  	$.jGrowl('Quantity entered is greater than quantity left.', {
					    //         header: 'Error Notification',
					    //         theme: 'alert-styled-right bg-warning'
					    //     });
					    //     setTimeout(function(){ $("#loader").html("");  }, 1000);
					        
                        //  }else{
                        //  	alert('Something went wrong!');
                        //  }
						//  $(':input[type="submit"]').prop('disabled', false);
                    },
                    error  :       function(msg)     
                    { 
						alert('Something went wrong!');
						$(':input[type="submit"]').prop('disabled', false);
                    }
            });
            return false;
        } 
    });

    $('#form-add-qty').validator().on('submit', function (e) 
	{  
		
        if (e.isDefaultPrevented()) 
        {
        }else { 
            //$(':input[type="submit"]').prop('disabled', true);
            var data = $(this).serialize();
            $.ajax({
                   type      :      'POST',
                   url       :      '../transaction.php',
                   data      :       data,
                    success  :       function(msg)     
                    {   console.log(msg);

                        //  if (msg==1) {
                        //  	$.jGrowl('Invetory succesfully updated.', {
					    //         header: 'Success Notification',
					    //         theme: 'alert-styled-right bg-success'
					    //     }); 
					    //     setTimeout(function(){ location.reload()  }, 1500);
                        //  }else if(msg==2) {
                        //  	$.jGrowl('Quantity entered is greater than quantity left.', {
					    //         header: 'Error Notification',
					    //         theme: 'alert-styled-right bg-warning'
					    //     });
					    //     setTimeout(function(){ $("#loader").html("");  }, 1000);
					        
                        //  }else{
                        //  	alert('Something went wrong!');
                        //  }
						//  $(':input[type="submit"]').prop('disabled', false);
                    },
                    error  :       function(msg)     
                    { 
						alert('Something went wrong!');
						$(':input[type="submit"]').prop('disabled', false);
                    }
            });
            return false;
        } 
    });

	
    $('#form-upload').submit(function(e) {
       e.preventDefault();
       $(':input[type="submit"]').prop('disabled', true);
        $.ajax({
            method   : 'post',
            url      :'upload_image_menu.php', 
            data     : new FormData(this),  
            contentType: false,  
            cache: false,  
            processData:false,  
            success : function (data)
            {  
            	$.jGrowl('Image successfully uploaded.', {
					header: 'Success Notification',
					theme: 'alert-styled-right bg-success'
			    });
			    setTimeout(function(){ location.reload()  }, 1500);
                        
            }
        });
        return false;
    });


    function print_receipt()
    {
            var contents = $("#show-code").html();
            var frame1 = $('<iframe />');
            frame1[0].name = "frame1";
            frame1.css({ "position": "absolute", "top": "-1000000px" });
            $("body").append(frame1);
            var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
            frameDoc.document.open();
            //Create a new HTML document.
            frameDoc.document.write('<html><head><title></title>');
            frameDoc.document.write('</head><body>');
            //Append the external CSS file.
          /* frameDoc.document.write('<link href="css/print.css" rel="stylesheet" type="text/css" />');*/
            frameDoc.document.write(contents);
            frameDoc.document.write('</body></html>');
            frameDoc.document.close();
            setTimeout(function () {
                window.frames["frame1"].focus();
                window.frames["frame1"].print();
                frame1.remove();
            }, 500);
    }

    function add_quantity()
    {
    	//$('').appendTo('body');
        $("#modal_addQty").modal('show');
	}

	function deduc_inventory()
    {
    	//$('').appendTo('body');
        $("#modal_deduc").modal('show');
    }

    function closer()
    {
    	window.location='products.php';
    }
    
    function update_product()
    {
    	$("#modal-update").modal('show');
    }

    function upload_image()
    {
    	$("#modal_upload").modal('show');
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

	function delete_ingredients(e){
		$.ajax({
               type      :      'GET',
               url       :      '../transaction.php',
               data      :       {delete_ingredients:"", ingrdents_id: e},
                success  :       function(msg)     
                { 
                    $.jGrowl('Ingredients successfully deleted.', {
						header: 'Success Notification',
						theme: 'alert-styled-right bg-success'
					});
					setTimeout(function(){ location.reload()  }, 1500);
                },
                error  :       function(msg)     
                { 
                    alert('Something went wrong!');
                }
        });
        return false;
	}
   
</script>


</html>

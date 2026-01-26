<?php
session_start();
require('db_connect.php');
?>
<link href="../assets/css/bootstrap.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../assets/js/core/libraries/jquery.min.js"></script>

<div class="content">
    <?php if($_GET['type'] == 'sales') {  ?>
        <title>Sales Report - <?= date('Y-m-d h:i:s') ?></title>
        <table class="table datatable-button-html5-basic table-hover table-bordered "  >
            <thead>
                <tr class="tr-table">
                    <th  width="300px"  >Date</th>
                    <th  width="250px" >Bill No.</th>
                    <th  width="300px" >Employee</th>
                    <th  width="300px" >Customer</th>
                    <th  width="250px" >Status</th>
                    <th  width="250px" >Register</th>
                    <th  width="250px" >Amount Due</th>
                </tr>
            </thead>
            <tbody>
                <?php

                    
                    $data = array();
                    $query_status = "";
                    $query_register  = "";
                    $query_status  = "";
                    if (isset($_SESSION['sale-report-status'])) {
                        $query_status = "AND tbl_sales.sales_status=".$_SESSION['sale-report-status']." ";
                    }

                    if (isset($_SESSION['sale-report-register'])) {
                        $query_register = "AND tbl_sales.register='".$_SESSION['sale-report-register']."' ";
                    }

 

                    
                    if (isset($_SESSION['sale-report'])) {
                        if (isset($_SESSION['sales-date-required'])) {
                            $from = $_SESSION['sale-report-from'];
                            $to = $_SESSION['sale-report-to'];
                            $today = date("Y-m-d");
                            $start_date = strtotime('today GMT');
                            $date_add = date('Y-m-d', strtotime('+1 day', $start_date)); 
                            if (isset($_SESSION['sale-report-user'])) {
                                $user_query = "AND tbl_sales.user_id='".$_SESSION['sale-report-user']."' ";
                            }else{
                                $user_query = "";
                            }
                            if (isset($_SESSION['sale-report-customer'])) {
                                $customer_query = "AND tbl_sales.cust_id='".$_SESSION['sale-report-customer']."' ";
                            }else{
                                $customer_query = "";
                            }

                            if ($today==$from || $today==$to) { 
                                $query_sales = "SELECT * FROM tbl_sales INNER JOIN tbl_users ON tbl_sales.user_id=tbl_users.user_id  LEFT JOIN tbl_customer ON tbl_sales.cust_id=tbl_customer.cust_id  WHERE  sales_date BETWEEN  '$today' AND '$date_add' $user_query  $customer_query $query_status $query_register GROUP BY tbl_sales.sales_no ORDER BY sales_id DESC  ";
                                $query = "SELECT count(sales_id)  as rows FROM tbl_sales INNER JOIN tbl_users ON tbl_sales.user_id=tbl_users.user_id  LEFT JOIN tbl_customer ON tbl_sales.cust_id=tbl_customer.cust_id  WHERE  sales_date BETWEEN  '$today' AND '$date_add' $user_query  $customer_query $query_status $query_register GROUP BY tbl_sales.sales_no ";
                                $queryTotal = "SELECT * FROM tbl_sales INNER JOIN tbl_users ON tbl_sales.user_id=tbl_users.user_id  LEFT JOIN tbl_customer ON tbl_sales.cust_id=tbl_customer.cust_id  WHERE  sales_date BETWEEN  '$today' AND '$date_add' $user_query  $customer_query $query_status $query_register GROUP BY tbl_sales.sales_no ";
                            }else{
                                $query_sales = "SELECT * FROM tbl_sales INNER JOIN tbl_users ON tbl_sales.user_id=tbl_users.user_id  LEFT JOIN tbl_customer ON tbl_sales.cust_id=tbl_customer.cust_id  WHERE  sales_date BETWEEN  '$from' AND '$to' $user_query  $customer_query $query_status $query_register GROUP BY tbl_sales.sales_no  ORDER BY sales_id DESC  ";
                                $query = "SELECT count(sales_id)  as rows FROM tbl_sales INNER JOIN tbl_users ON tbl_sales.user_id=tbl_users.user_id  LEFT JOIN tbl_customer ON tbl_sales.cust_id=tbl_customer.cust_id  WHERE  sales_date BETWEEN  '$from' AND '$to' $user_query  $customer_query $query_status $query_register GROUP BY tbl_sales.sales_no";
                                $queryTotal = "SELECT * FROM tbl_sales INNER JOIN tbl_users ON tbl_sales.user_id=tbl_users.user_id  LEFT JOIN tbl_customer ON tbl_sales.cust_id=tbl_customer.cust_id  WHERE  sales_date BETWEEN  '$from' AND '$to' $user_query  $customer_query $query_status $query_register GROUP BY tbl_sales.sales_no";
                            }

                        }else{
                            if (isset($_SESSION['sale-report-user'])) {
                                $user_query = "AND tbl_sales.user_id='".$_SESSION['sale-report-user']."' ";
                            }else{
                                $user_query = "";
                            }
                            if (isset($_SESSION['sale-report-customer'])) {
                                $customer_query = "AND tbl_sales.cust_id='".$_SESSION['sale-report-customer']."' ";
                            }else{
                                $customer_query = "";
                            }

                            $query_sales = "SELECT * FROM tbl_sales INNER JOIN tbl_users ON tbl_sales.user_id=tbl_users.user_id  LEFT JOIN tbl_customer ON tbl_sales.cust_id=tbl_customer.cust_id  WHERE  sales_status!='test_sales' $user_query  $customer_query $query_status $query_register GROUP BY tbl_sales.sales_no  ORDER BY sales_id DESC";
                            $query = "SELECT count(sales_id)  as rows FROM tbl_sales INNER JOIN tbl_users ON tbl_sales.user_id=tbl_users.user_id  LEFT JOIN tbl_customer ON tbl_sales.cust_id=tbl_customer.cust_id  WHERE  sales_status!='test_sales' $user_query  $customer_query $query_status $query_register GROUP BY sales_no  "; 
                            $queryTotal = "SELECT * FROM tbl_sales INNER JOIN tbl_users ON tbl_sales.user_id=tbl_users.user_id  LEFT JOIN tbl_customer ON tbl_sales.cust_id=tbl_customer.cust_id  WHERE  sales_status!='test_sales' $user_query  $customer_query $query_status $query_register GROUP BY sales_no  "; 
                            
                        }
                        
                    }else{
                        $query_sales = "SELECT * FROM tbl_sales INNER JOIN tbl_users ON tbl_sales.user_id=tbl_users.user_id  LEFT JOIN tbl_customer ON tbl_sales.cust_id=tbl_customer.cust_id    GROUP BY tbl_sales.sales_no ORDER BY sales_id ";
                        $query = "SELECT count(sales_id)  as rows FROM tbl_sales GROUP BY sales_no  "; 
                        $queryTotal = "SELECT count(sales_id)  as rows FROM tbl_sales GROUP BY sales_no  "; 
                    } 

                    $total = 0;
                    $recordsTotal = 0;
                    $records = $db->query($query_sales); 
                    while($row = $records->fetchArray()) {
                        $recordsTotal ++;
                        if ($row['sales_status']==2){
                            $sales_status = '<label class="label label-primary">Updated</label>';
                        }elseif ($row['sales_status']==1){
                            $sales_status = '<label class="label label-primary">Active</label>';
                        }elseif ($row['sales_status']==3){
                            $sales_status = '<label class="label label-danger">Cancelled</label>';
                        }

                        if ($row['register']==0) {
                            $register = '<label class="label label-primary">Open</label>';
                        }else{
                            $register = '<label class="label label-success">Closed</label>';
                        }

                        $total+=$row['total_amount'];
                       
                            
            ?>
                <tr>
                    <td  ><?= date('F d, Y h:i A', strtotime($row['sales_date'])) ?></td>
                    <td ><?=  $row['sales_no'] ?></td>
                    <td ><?=  $row['fullname'] ?></td>
                    <td ><?=  $row['name'] ?></td>
                    <td ><?=  $sales_status ?></td>
                    <td><?=  $register ?></td>
                    <td><?=  number_format($row['total_amount'],2) ?></td>
                </tr>

                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td>Total</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><?=  number_format($total,2) ?></td>
                </tr>
            </tfoot>
        </table>
    <?php } ?>
    <?php if($_GET['type'] == 'inventory') {  ?>
        <title>Nnventory Report - <?= date('Y-m-d h:i:s') ?></title>
        <table class="table datatable-button-html5-basic table-hover table-bordered "  >
            <thead>
                <tr class="tr-table">
                    <th  width="300px"  >Date</th>
                    <th  width="250px" >Bill No.</th>
                    <th  width="300px" >Employee</th>
                    <th  width="300px" >Customer</th>
                    <th  width="250px" >Amount Due</th>
                    <th  width="250px" >Status</th>
                    <th  width="250px" >Register</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $startDate = strtotime('today GMT');
                    $today = date("Y-m-d");
                    $date_add = date('Y-m-d', strtotime('+1 day', $startDate));
                    if( isset($_SESSION['inv-report']) !="" ){
                        $from = $_SESSION['inv-report-from'];
                        $to = $_SESSION['inv-report-to'];
                    }else{
                        $from = $today;
                        $to = $today;
                    }

                    
                    if ($today==$from || $today==$to) {
                        $data_query = 
                        "   SELECT * FROM tbl_product_history   
                            WHERE  hist_date BETWEEN  '$today' AND '$date_add'
                            ORDER BY tph_id ASC
                            
                        ";
                    }else{
                        $data_query = 
                        "   SELECT * FROM tbl_product_history   
                            WHERE  hist_date BETWEEN  '$from' AND '$to'
                            ORDER BY tph_id ASC
                            
                        ";
                    }
                $records = $db->query($data_query);  
                while($row = $records->fetchArray()) {   
                    $details = $row['details'];
                    $date = $row['hist_date'];
                    $quantity = ''; 
                    $product_name = '';
                    $unit = '';
                    $employee = '';
                    $customer = '';
                    $all_details = '';
                    if($row['details_type'] == 1){
                        $queryDetails = 
                            "   SELECT * FROM tbl_sales 
                                INNER JOIN tbl_products ON tbl_sales.product_id = tbl_products.product_id
                                INNER JOIN tbl_users ON tbl_sales.user_id = tbl_users.user_id
                                INNER JOIN tbl_customer ON tbl_sales.cust_id = tbl_customer.cust_id
                                WHERE  sales_no='$details'
                            ";
                        $result = $db->query($queryDetails);
                        $receiving_no = '';
                        $receiving_quantity = '';
                        while($row = $result->fetchArray()) { 
                            $product_name = $row['product_name'];
                            $quantity = '-'.$row['quantity_order'];
                            $unit = $row['unit'];
                            $employee = $row['fullname'];
                            $customer = $row['name'];
                            $all_details = 'Bill No. '. $row['sales_no'] .'';
                        }
                        
                    }elseif( $row['details_type'] == 2 ){
                        $queryDetails = 
                            "   SELECT * FROM tbl_receivings 
                                INNER JOIN tbl_products ON tbl_receivings.product_id = tbl_products.product_id
                                INNER JOIN tbl_users ON tbl_receivings.user_id = tbl_users.user_id
                            WHERE  receiving_no=$details 
                            ";
                        $result = $db->query($queryDetails);
                        while($row = $result->fetchArray()) { 
                            $receiving_no = $row['receiving_no']; 
                            $product_name = $row['product_name'];
                            $quantity = $row['receiving_quantity'];
                            $unit = $row['unit'];
                            $employee = $row['fullname'];
                            $all_details = 'Receiving  No. '. $row['receiving_no'] .'';
                        }
                    }elseif( $row['details_type'] == 3 ){
                        $queryDetails = 
                            "   SELECT * FROM tbl_damage 
                                INNER JOIN tbl_products ON tbl_damage.product_id = tbl_products.product_id
                                INNER JOIN tbl_users ON tbl_damage.user_id = tbl_users.user_id
                                WHERE  damage_id=$details 
                                
                            ";
                        $result = $db->query($queryDetails);
                        while($row = $result->fetchArray()) {  
                            $product_name = $row['product_name'];
                            $quantity = '-'.$row['quantity_damage'];
                            $unit = $row['unit'];
                            $employee = $row['fullname'];
                            $all_details = 'Damage ID : '. $row['damage_id'] .' ';
                        }
                    }  
                            
            ?>
                <tr>
                    <td><?= date('F d, Y h:i A', strtotime($date)) ?></td>
                    <td><?= $product_name ?></td>
                    <td><?= $employee ?></td>
                    <td><?= $customer ?></td>
                    <td><?= $unit ?></td>
                    <td><?= $quantity ?></td>
                    <td><?= $all_details ?></td>

                </tr>

                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td>Total</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    <?php } ?>
    <?php if($_GET['type'] == 'product-available') {  ?>
        <title>Product Available Report - <?= date('Y-m-d h:i:s') ?></title>
        <table class="table datatable-button-html5-basic table-hover table-bordered "  >
            <thead>
                <tr style="border-bottom: 4px solid #ddd;background: #eee">
                    <th >Image</th>
                    <th >Product Code</th>
                    <th >Product ID</th>
                    <th >Product Name</th>
                    <th > In Stock</th>
                </tr>
            </thead>
            <tr>
                <?php 


                    if (isset($_SESSION['inventory-report-product'])) {
                                                                                
                        $query = "SELECT * FROM tbl_products WHERE product_id='".$_SESSION['inventory-report-product']."'";
                        
                    }else{
                        $query = "SELECT * FROM tbl_products";
                    }
                    $i = 0 ;
                    $recordsTotal = 0;
                    $result = $db->query($query);
                    $total = 0;
                    while($row = $result->fetchArray()) {
                        $i++;
                        $recordsTotal++;
                        $image = $row['image'];
                        if ($image!="") {
                            $image_file = '../uploads/'.$image;
                        }else{
                            $image_file = '../images/no-image.png';
                        }
                        
                    ?>
                    <td> <img alt="<?=$image_file?>"  style="width: 90px;height: 90px;border: 2px solid #eee" src="<?=$image_file?>" /> </td>
                    <td><?= $row['product_code'];?></td>
                    <td style="width: 160px">21324<?= $row['product_id'];?></td>
                    <td><b><?= $row['product_name'];?></b></td>
                    <td style="text-align: center;width: 140px"><?= $row['quantity'];?></td>
                </tr>
            </tr>
        <?php } ?>
        <?php if ($i==0) {?>
            <tr>
                <td colspan="7" align="center"><h2>No data found!</h2></td>
            </tr>
        <?php }?>
        </table>
    <?php } ?>
</div>
<script type="text/javascript" src="../assets/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="../assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
<script type="text/javascript" src="../assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
<script type="text/javascript" src="../assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>

<script>

$(function() { 
    var oTable = $('.datatable-button-html5-basic').DataTable({
        "paging": false,
        "searching": false,
        "iDisplayLength": <?=  $recordsTotal ?>,
        "initComplete": function(settings, json) {
            var exportType = "<?= $_GET['export'] ?>";
            setTimeout( function () {
                if(exportType === 'pdf'){
                    $('.pdfbtn' ).click();
                }else{
                    $('.csvbtn' ).click();
                }
            }, 2000);
        
            setTimeout( function () {
                window.location.href='<?= $_GET['backlink'] ?>';
            }, 5000);
        },
        "dom": "Bfrtip",
        "buttons": [
                {
                    extend: 'csvHtml5',
                    className: 'btn btn-default csvbtn',
                    text: '<i class="icon-copy3 position-left"></i> CSV',
                    footer: true,
                },
                {
                    extend: 'pdfHtml5',
                    footer: true,
                    orientation: 'landscape',
                    pageSize: 'LEGAL',
                    className: 'btn btn-default pdfbtn',
                    text: '<i class="icon-copy3 position-left"></i> pdf',
                    customize : function(doc){
                        var colCount = new Array();
                        $('.datatable-button-html5-basic').find('tbody tr:first-child td').each(function(){
                            if($(this).attr('colspan')){
                                for(var i=1;i<=$(this).attr('colspan');$i++){
                                    colCount.push('*');
                                }
                            }else{ colCount.push('*'); }
                        });
                        doc.content[1].table.widths = colCount;
                    }
                }

            ],
        "columnDefs": [
            // { className: 'right', targets: 4 },
            // { className: 'center', targets: 5 },
            // { className: 'center', targets: 6 }
        ],
            
        });
			
		});

</script>
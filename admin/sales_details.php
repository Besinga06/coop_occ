
<?php
    require('db_connect.php');
    //$sales_no = 68;
    $query = "SELECT * FROM tbl_sales  INNER JOIN tbl_products ON tbl_sales.product_id=tbl_products.product_id INNER JOIN tbl_users ON tbl_sales.user_id=tbl_users.user_id  LEFT JOIN tbl_customer  ON tbl_sales.cust_id=tbl_customer.cust_id WHERE tbl_sales.sales_no='".$sales_no."'  ";
    $result = $db->query($query);
    while($row = $result->fetchArray()) {
        $cashier = $row['fullname'];
        $customer = $row['name']; 
        $total_amount = $row['total_amount'];
        $other_amount = $row['other_amount'];
        $subtotal = $row['subtotal'];
        $discount = $row['discount'];
        $sales_no = $row['sales_no'];
        $amount_paid = $row['amount_paid'];
        $vat_sales = number_format($row['total_amount']-($row['tax_percent']/100),2);
        $vat_amount = number_format($row['subtotal']*($row['tax_percent']/100),2);
        $sales_date = $row['sales_date'];
        $sales_type = $row['sales_type'];
        $delivery_address= $row['delivery_address'];
        $salesman= $row['salesman'];
        $po_no= $row['po_no'];
        $check_no= $row['check_no'];
        
        
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Receipt</title>
    <style type="text/css">
        .heading-elements{
           background:none!important;
           margin-top: -30px 
        }

        .panel-receipt{
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        @media print {
            .pagebreak {
                clear: both;
                page-break-after: always;
            }
        }
        p.text-title {
            line-height: 10px !important;
       } 
    </style>
</head>
<body>
    <div class="content"  style="margin: 0px!important">
        <div class="panel panel-flat">
            <div class="panel-body">
                <div class="tabbable">
                    <ul class="nav nav-tabs bg-slate nav-justified">
                        <li class="active"><a href="#receipt" data-toggle="tab">Receipt</a></li>
                        <li><a href="#details" data-toggle="tab">Details</a></li>
                        <li><a href="#products" data-toggle="tab">Products</a></li>
                        <?php  if($sales_type == 0) { ?>
                        <li><a href="#payments" data-toggle="tab">Payments</a></li>
                        <?php } ?>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="receipt">
                            <!-- <div class="receipt-div" id="print-receipt" > -->
                            <div class="center" style="padding-top: 20px" >
                                <button onclick="print_receipt()" type="button" class="btn bg-teal-400 btn-labeled btn-labeled-left"><b><i class="icon-printer"></i></b> Print</button>
                            </div>
                                <?php
                                require('receipt.php');
                                ?>
                               
                           
                            
                        </div>
                        <div class="tab-pane" id="details">
                            <table class="table datatable-button-html5-basic table-hover table-bordered   dataTable no-footer" >
                               <tr>
                                    <td>PO#</td> <td style="text-align: center;font-weight: bold"><?= $po_no ?></td>
                                </tr>
                                <tr>
                                    <td>Employee</td> <td style="text-align: center;font-weight: bold"><?= $cashier ?></td>
                                </tr>
                                <tr>
                                    <td>Customer</td> <td style="text-align: center;font-weight: bold"><?= $customer ?></td>
                                </tr>
                                <tr>
                                    <td>Sub Total</td> <td style="text-align: right;font-weight: bold"><?= number_format($subtotal,2) ?></td>
                                </tr>
                                <tr>
                                    <td>Discount</td> <td style="text-align: right;font-weight: bold"><?= number_format($discount,2) ?></td>
                                </tr>
                                <?php  if($sales_type == 0) { ?>
                                    <td>Other Amount</td> <td style="text-align: right;font-weight: bold"><?= number_format($other_amount,2) ?></td>
                                <?php } ?>
                            <!--  <tr>
                                    <td>Vat Sales</td> <td style="text-align: right;font-weight: bold"><?= $vat_sales ?></td>
                                </tr>
                                <tr>
                                    <td>Vat Amount</td> <td style="text-align: right;font-weight: bold"><?= number_format($vat_amount,2) ?></td>
                                </tr> -->
                                <tr>
                                    <td>Amount Due</td> <td style="text-align: right;font-weight: bold"><?= number_format($total_amount,2) ?></td>
                                </tr>
                                <tr>
                                    <td>Cash</td> <td style="text-align: right;font-weight: bold"><?= number_format($amount_paid,2) ?></td>
                                </tr>
                                <tr>
                                    <td>Change</td> <td style="text-align: right;font-weight: bold"><?= number_format($amount_paid-$total_amount > 0 ? $amount_paid-$total_amount : 0,2) ?></td>
                                </tr>
                                <tr>
                                    <td>Cheque # </td> <td style="text-align: right;font-weight: bold"><?=  $check_no ?></td>
                                </tr>
                            </table>
                            <textarea name="delivery_address"  rows="3" cols="3" class="form-control" id="delivery_address"  onchange="delivery_address(this)" placeholder="Delivery Address" spellcheck="false" style="margin:10px 0px" ><?= $delivery_address ?></textarea>
                            <input class="form-control" value="<?= $salesman ?>"  name="salesman" placeholder="Salesman" style="margin:10px 0px" onchange="salesman(this)"  />
                        </div>
                        
                        <div class="tab-pane" id="products">
                            <table class="table datatable-button-html5-basic table-hover table-bordered   dataTable no-footer">
                            <tr>
                                <thead>
                                    <th width="50%">Name</th>
                                    <th style="text-align: center;">Quantity</th>
                                </thead>
                            </tr>
                                <?php
                                    $result = $db->query($query);
                                    while($row2 = $result->fetchArray()) {
                                ?>
                                <tr>
                                    <td class="product-name"><?= $row2['product_name']?></td>
                                    <td class="text-center"><b><?= $row2['quantity_order']?></b> (<?= $row2['unit']?>)</td>
                                </tr>
                                <?php }?>
                            </table>
                        </div>
                        <div class="tab-pane" id="payments">
                        <table class="table datatable-button-html5-basic table-hover table-bordered   dataTable no-footer">
                            <tr>
                                <thead>
                                    <th>CR No.</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                </thead>
                            </tr>
                             <?php
                                $query2 = "SELECT * FROM tbl_payments   WHERE sales_no='".$sales_no."'  ";
                                $result2 = $db->query($query2);
                                $payment_counter = 0;
                                while($row2 = $result2->fetchArray()) {
                                    $payment_counter++;
                                ?>
                                <tr>
                                    <td><?= $row2['cr_no']?></td>
                                    <td><?= date('F d, Y h:i A', strtotime($row2['date_payment']))?></td>
                                    <td class="text-right"><?= number_format($row2['amount_paid'],2)?></td>
                                </tr>
                                <?php }?>
                                <?php  if($payment_counter == 0 ) { ?>
                                <tr >
                                    <td colspan="3" class="text-center"><h4>No payments yet!</h4></td>
                                </tr>
                                <?php } ?>
                            </table>
                        <div>
                    </div>
                </div>
            </div>
        </div>
</div>
</body>
</html>            
                 
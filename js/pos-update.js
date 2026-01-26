$(document).ready(function()
{
    view_cart();
    total();
});

$( "#product-input" ).keyup(function() {
    $("#show-loader").html('<i class="icon-spinner2 spinner" style="z-index: 30;position: absolute;font-size: 50px;color: #fff"></i>');
    $("#show-search").show();
    var keywords = $(this).val(); 
    if (keywords!="") {
        $.ajax({
            type      :      'GET',
            url       :      '../transaction.php',
            data      :       {search_products:"",keywords:keywords},
            success  :       function(msg)     
            {  
                $("#show-search").html(msg);
                $("#show-loader").html('');
            },
            error  :       function(msg)     
            { 
                alert('Something went wrong!');
            }
        });
    }else{
        $("#show-search").hide();
        $("#show-loader").html('');
    }
});

/*$("#product-input").focusout(function(){
    $("#show-search").hide();
    $("#show-loader").html('');
});*/

$( "#customer-input" ).keyup(function() {
    $("#show-loader").html('<i class="icon-spinner2 spinner" style="z-index: 30;position: absolute;font-size: 50px;color: #fff"></i>');
    $("#show-search-customer").show();
    var keywords = $(this).val(); 
    if (keywords!="") {
        $.ajax({
            type      :      'GET',
            url       :      '../transaction.php',
            data      :       {search_customer:"",keywords:keywords},
            success  :       function(msg)     
            {  
                $("#show-search-customer").html(msg);
                $("#show-loader").html('');
            },
            error  :       function(msg)     
            { 
                alert('Something went wrong!');
            }
        });
    }else{
        $("#show-search-customer").hide();
        $("#show-loader").html('');
    }

});

/*$("#customer-input").focusout(function(){
    $(this).val();
    $("#show-search-customer").hide();
    $("#show-loader").html('');
});
*/
function select_customer(el)
{
    var cust_id = $(el).attr('cust_id');
    var name = $(el).attr('name');
    $("#cust_id").val(cust_id);
    $("#customer-input").val(name);
    $("#show-search-customer").hide();
    $.ajax({
        type      :      'GET',
        url       :      '../transaction.php',
        data      :       {session_customer:"",cust_id:cust_id,name:name},
        success  :       function(msg)    
        { 
            alert(msg);
        }
    });
}

function select_product(el)
{
    $("#show-loader").html('<i class="icon-spinner2 spinner" style="z-index: 30;position: absolute;font-size: 50px;color: #fff"></i>');
    var product_id = $(el).attr('product_id');
    var product_name = $(el).attr('product_name');
    var quantity = $("#quatity-input").val(); 
    $("#product-input").val("");
    $("#show-search").hide();
    $.ajax({
       type      :      'POST',
       url       :      '../transaction.php',
       dataType  :       'JSON',
       data      :       {save_cart:"",product_id:product_id,quantity:quantity},
        success  :       function(msg)     
        { 
            //alert(msg);
           if (msg['message']=='save'){
                total();
                beep_success();
                view_cart();
                $("#show-loader").html('');
            }else if (msg['message']=='save2'){
                total();
                beep_success2();
                view_cart();
                $("#show-loader").html('');
            }else if (msg['message']=='unsave'){
                 beep_error();
                $.jGrowl('Desired quantity <b>('+msg['quantity_order']+')</b> is greather than quantity left <b>('+msg['quantity_left']+')</b>.Please check your inventory.', {
                    header: 'Error Notification',
                    theme: 'alert-styled-right bg-danger'
                }); 
                $("#show-loader").html('');
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

function view_cart()
{  
    $.ajax({
        type      :      'GET',
        url       :      '../transaction.php',
        data      :       {view_cart_update:""},
        success  :       function(msg)     
        {  
            //total();
            $("#show-cart").html(msg);
        }
    });
}

function cancel_sale()
{  
    $.ajax({
        type      :      'GET',
        url       :      '../transaction.php',
        data      :       {cancel_sale:""},
        success  :       function(msg)     
        {  
           beep_success();
           total();
           view_cart();
        }
    });
}       
        
$('#form-payment').on('submit', function (e) 
{
    $(':input[type="submit"]').prop('disabled', true);
    $(':input[type="submit"]').append('<span id="loader">&nbsp;&nbsp; <i class="icon-spinner2 spinner"></i></span>');
    var payable = parseFloat($("#grand-total").text().replace (/,/g, "")); 
    var payment = parseFloat($("#payment").val()); 
    var change_amount = payment - payable; 
    if (payment<payable) {
         beep_error();
        $(':input[type="submit"]').prop('disabled', false);
        $.jGrowl('Insuficient amount <b>('+payment.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')+')</b>.Please enter higher amount <b>('+$("#grand-total").text()+')</b>.', {
            header: 'Error Notification',
            theme: 'alert-styled-right bg-danger'
        });
        setTimeout(function(){ $("#loader").html("");  }, 1000);
        
    }else if ($("#payment").val()=="") {
         beep_error();
        $(':input[type="submit"]').prop('disabled', false);
        $.jGrowl('Please enter valid amount.', {
            header: 'Error Notification',
            theme: 'alert-styled-right bg-danger'
        });
         $("#payment").focus();
        setTimeout(function(){ $("#loader").html("");  }, 1000);
        
    }else{
        $("#discount-open").val('');
        $("#payment-open").val('');
        var data = $(this).serialize();
        var vat_sales = $("#show-vat-sales").text();
        var subtotal = $("#show-subtotal").text();
        var discount = $("#show-discount").text();
        after_sales(payable,payment,vat_sales,discount,subtotal,change_amount);
        $.ajax({
               type      :      'POST',
               url       :      '../transaction.php',
               data      :       data,
                success  :       function(msg)     
                {    
                    //console.log(msg);
                    print_receipt_data();
                    if (msg=='1'){
                        $("#modal-quantity").modal('hide');
                        $("#quantity").val("");
                        view_cart();
                        $("#loader").html("");
                        beep_success();
                        //print_receipt();
                        $("#btn-print").show();
                        total();
                    }else{
                        alert('Something went wrong!');
                    }
                },
                error  :       function(msg)     
                { 
                    alert('Something went wrong!');
                }
        });
    }
    return false;
});

function print_receipt_data()
{
    $("#print-receipt").load('receipt.php');
}

function print_receipt()
{
    //$("#print-receipt").html('test');
    var contents = $("#print-receipt").html();
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
    /*frameDoc.document.write('<link href="css/print.css" rel="stylesheet" type="text/css" />');*/
    frameDoc.document.write(contents);
    frameDoc.document.write('</body></html>');
    frameDoc.document.close();
    setTimeout(function () {
            window.frames["frame1"].focus();
            window.frames["frame1"].print();
            frame1.remove();
    }, 500);
}




function after_sales(payable,payment,vat_sales,discount,subtotal,change_amount)
{
    view_cart();
    total();
    $("#new-sales").val('yes');
    $("#footer-sales").hide();
    //$(".after-sales").addClass('modal-lg')
    $("#show-payment").html(' <div class="row "> <div class="col-md-12"> <div class="list-group no-border no-padding-top"> <a href="javascript:;" class="list-group-item"><i class="icon-cash3"></i> Sub Total <span class="pull-right" >'+subtotal+'</span></a> <a href="javascript:;" class="list-group-item"><i class="icon-cash3"></i> Discount <span class="pull-right " >'+discount+'</span></a> <a href="javascript:;" class="list-group-item"><i class="icon-cash3"></i> VAT Sales(12%) <span class="pull-right " >'+vat_sales+'</span></a> <div class="list-group-divider"></div> <a href="#" class="list-group-item" style="font-weight: bold;font-size: 14px"><i class="icon-cash3"></i> Amount Due <span class="pull-right"  >'+payable.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')+'</span></a> <a href="#" class="list-group-item" style="font-weight: bold;font-size: 14px"><i class="icon-cash3"></i> Cash <span class="pull-right"  >'+payment.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')+'</span></a> <a href="#" class="list-group-item" style="font-weight: bold;font-size: 14px"><i class="icon-cash3"></i> Change <span class="pull-right"  >'+change_amount.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')+'</span></a> </div> </div> <div style="width:100%;text-align:center" ><button id="btn-print" style="display:none" onclick="print_receipt()" type="button" class="btn bg-teal-400 btn-labeled"><b><i class="icon-printer"></i></b> Print Receipt</button></div></div>')
}

function total()
{  
    $.ajax({
        type      :      'GET',
        dataType  :      'JSON',
        url       :      '../transaction.php',
        data      :       {view_total_update:""},
        success  :       function(msg)     
        { 
           $("#sales-no").html(msg['sales_no']);
           $("#show-vat-sales").html(msg['vat_sales']);
           $("#show-vat-amount").html(msg['vat_amount']);
           $("#show-subtotal").html(msg['subtotal_amount']);
           $("#grand-total").html(msg['total_amount']);
           $("#show-discount").html(msg['discount']); 
        }
    });
}

function delete_cart(el)
{
     $("#show-loader").html('<i class="icon-spinner2 spinner" style="z-index: 30;position: absolute;font-size: 50px;color: #fff"></i>');
    var cart_id = $(el).attr('cart_id');
    $.ajax({
        type      :      'GET',
        url       :      '../transaction.php',
        data      :       {delete_cart_update:"",cart_id:cart_id},
        success  :       function(msg)     
        {  
            total();
            view_cart();
            beep_success();
            $("#show-loader").html('');
        },
        error  :       function(msg)     
        { 
            alert('Something went wrong!');
        }
    });

}

function update_cart(el)
{
    $("#show-loader").html('<i class="icon-spinner2 spinner" style="z-index: 30;position: absolute;font-size: 50px;color: #fff"></i>');
    var cart_id = $(el).attr('cart_id');
    var quantity_order = $(el).attr('quantity_order');
    var new_quantity_order = $(el).val();
    if (new_quantity_order<1) {
          var new_quantity_order = quantity_order;
    }
    $.ajax({
        type      :      'GET',
        url       :      '../transaction.php',
        dataType  :      'JSON',
        data      :       {update_cart:"",cart_id:cart_id,new_quantity_order:new_quantity_order},
        success  :       function(msg)     
        {  
            if (msg['message']=='ok'){
                total();
                view_cart();
                 beep_success();
                $("#show-loader").html('');
            }else if (msg['message']=='not_ok'){
                 beep_error();
                $.jGrowl('Desired quantity <b>('+msg['quantity_order']+')</b> is greather than quantity left <b>('+msg['quantity_left']+')</b>.Please check your inventory.', {
                    header: 'Success Notification',
                    theme: 'alert-styled-right bg-danger'
                }); 
                $("#show-loader").html('');
            }else{
                alert('Something went wrong!');
            }
        },
        error  :       function(msg)     
        { 
            alert('Something went wrong!');
        }
    });
}

$('#quatity-input').keyup(function(){
    var quantity = $(this).val();
    if (quantity<1) {
        $(this).val(1);
    }
});

$('#form-discount').on('submit', function (e) 
{   $(':input[type="submit"]').append('<span id="loader">&nbsp;&nbsp; <i class="icon-spinner2 spinner"></i></span>');
    var discount = parseFloat($("#discount").val()); 
    if (discount==0) {
         $("#discount").focus();
        //setTimeout(function(){ $("#loader").html("");  },500);
          $.jGrowl('Please enter valid discount.', {
            header: 'Error Notification',
            theme: 'alert-styled-right bg-danger'
        });
          $(':input[type="submit"]').append('<span id="loader"></span>');
         
    }else if ($("#discount").val()=="") {
         $.jGrowl('Please enter valid discount.', {
            header: 'Error Notification',
            theme: 'alert-styled-right bg-danger'
        });
          $("#discount").focus();
         $(':input[type="submit"]').append('<span id="loader"></span>');
    }else{
        var data = $(this).serialize();
         $(':input[type="submit"]').append('<span id="loader"></span>');
         $("#discount-open").val();
        $.ajax({
               type      :      'POST',
               url       :      '../transaction.php',
               data      :       data,
                success  :       function(msg)     
                { 
                    $("#modal-discount").modal('hide');
                    if (msg=='1'){
                        view_cart();
                        total();
                         beep_success();
                        $("#modal-quantity").modal('hide');
                        $("quantity").val("");
                        
                    }else{
                        alert('Something went wrong!');
                    }
                },
                error  :       function(msg)     
                { 
                    alert('Something went wrong!');
                }
        });
    }
    return false;
});


function my_sale()
{
    
    $("#show-data-all").html('<div style="width:100%;height:100%;position:absolute;left:50%;right:50%;top:40%;"><img src="../images/LoaderIcon.gif"  ></div>');
    $("#show-button").html('');
    $("#title-all").html('My sales for today');
    $("#modal-all").modal('show');
    setTimeout(function(){ $("#show-data-all").load('my-sale-today.php');  }, 1000);
}

function view_products()
{
    $("#show-data-all").html('<div style="width:100%;height:100%;position:absolute;left:50%;right:50%;top:40%;"><img src="../images/LoaderIcon.gif"  ></div>');
    $("#show-button").html('');
    $("#title-all").html('List of products');
    $("#modal-all").modal('show');
    setTimeout(function(){ $("#show-data-all").load('sales-products.php');  }, 1000);
    
}



function add_discount()
{
   $("#payment-open").val('');
   $('.modal').modal('hide');
   if ($("#discount-open").val()!='yes') {
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
}

function add_payment()
{   
    if ($("#payment-open").val()!='yes') {
        $("#amount-due").html($("#grand-total").text()); 
        if (parseFloat($("#grand-total").text())<1) {
            $.jGrowl('No product order.Please select product to proceed payment', {
                header: 'Error Notification',
                theme: 'alert-styled-right bg-danger'
            });
        }else{
            $("#modal-payment").modal('show');
            setTimeout(function(){ $("#payment").focus(); }, 500);
        }
        $("#payment-open").val('yes');
    }
}

function view_customer()
{
    $("#show-button").html('<button type="button" onclick="new_customer()" class="btn btn-info btn-labeled "><b><i class="icon-add"></i></b> New Customer</button>');
    $("#title-all").html('List of customer');
    $("#modal-all").modal('show');
    $("#show-data-all").load('customer-view.php');
}

function select_key(el)
{ 

    var cusrrent_payment =  $("#payment").val();
    var new_payment =  cusrrent_payment + el;
    $("#payment").val(new_payment);
}

function clear_all()
{ 
    $("#payment").val("");
}

function clear_all2()
{ 
    $("#discount").val("");
}



function select_key2(el)
{ 
     
    var cusrrent_payment =  $("#discount").val();
    var new_payment =  cusrrent_payment + el;
    $("#discount").val(new_payment);
}

function clear_last2()
{
     var str = $('#discount').val(); 
    $('#discount').val(str.substring(0,str.length - 1));
}

function clear_last()
{
     var str = $('#payment').val(); 
    $('#payment').val(str.substring(0,str.length - 1));
}

function refresh()
{
    location.reload();
}

$( "#quatity-input" ).keyup(function() {
    if ($(this).val()=="") {
        $(this).val(1);  
    }
});

function beep_success(){
    var obj = document.createElement("audio");
    obj.src="../audio/scanner.ogg";
    //obj.volume=0.1;
    obj.autoPlay=false;
    obj.preLoad=true;       
    obj.play();      
}

function beep_success2(){
    var obj = document.createElement("audio");
    obj.src="../audio/double-beep.ogg";
    //obj.volume=0.1;
    obj.autoPlay=false;
    obj.preLoad=true;       
    obj.play();      
}

function beep_error(){
    var obj = document.createElement("audio");
    obj.src="../audio/error.ogg";
    obj.autoPlay=false;
    obj.preLoad=true;       
    obj.play();      
}

function new_customer()
{
   $(".modal").modal('hide');
   $("#modal-new").modal('show');
}




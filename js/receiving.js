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
        $( "#product-input" ).click();
        $("#show-loader").html('');
    }
});

$( "#product-input" ).click(function() {
    $("#show-loader").html('<i class="icon-spinner2 spinner" style="z-index: 30;position: absolute;font-size: 50px;color: #fff"></i>');
    $("#show-search").show();
    $("#show-search-customer").hide();
    $.ajax({
        type      :      'GET',
        url       :      '../transaction.php',
        data      :       {search_products:"",keywords:""},
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
});

$("#searchproduct").click(function() {
    $("#show-search").html("");
    $("#product-input").val("");
    $("#show-search").hide();
});  


$( "#customer-input" ).keyup(function() {
    $("#show-loader").html('<i class="icon-spinner2 spinner" style="z-index: 30;position: absolute;font-size: 50px;color: #fff"></i>');
    $("#show-search-customer").show();
    var keywords = $(this).val(); 
    if (keywords!="") {
        $.ajax({
            type      :      'GET',
            url       :      '../transaction.php',
            data      :       {search_supplier:"",keywords:keywords},
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
        $( "#customer-input" ).click();
        $("#show-loader").html('');
    }

});

$( "#customer-input" ).click(function() {
    $("#show-loader").html('<i class="icon-spinner2 spinner" style="z-index: 30;position: absolute;font-size: 50px;color: #fff"></i>');
    $("#show-search-customer").show();
    $("#show-search").hide();
    $.ajax({
        type      :      'GET',
        url       :      '../transaction.php',
        data      :       {search_supplier:"",keywords:""},
        success  :       function(msg)     
        {   console.log( msg);
            $("#show-search-customer").html(msg);
            $("#show-loader").html('');
        },
        error  :       function(msg)     
        { 
            alert('Something went wrong!');
        }
    });
});

$("#searchcustomer").click(function() {
    $("#show-search-customer").html("");
    $("#customer-input").val("");
    $("#cust_id").val("");
    $.ajax({
        type      :      'GET',
        url       :      '../transaction.php',
        data      :       {session_supplier:"",supplier_id:"",name:''},
        success  :       function(msg)    
        { 
           
        }
    });
     $("#show-search-customer").hide();
}); 


function select_customer(el)
{
    var supplier_id = $(el).attr('cust_id');
    var name = $(el).attr('name');
    $("#cust_id").val(supplier_id);
    $("#customer-input").val(name);
    $("#show-search-customer").hide();
    $.ajax({
        type      :      'GET',
        url       :      '../transaction.php',
        data      :       {session_supplier:"",supplier_id:supplier_id,name:name},
        success  :       function(msg)    
        { 
            console.log(msg);
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
       data      :       {save_cart2:"",product_id:product_id,quantity:quantity},
        success  :       function(msg)     
        { 
           if (msg['message']=='save'){
                total();
                view_cart();
                beep_success();
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
        data      :       {view_cart2:""},
        success  :       function(msg)     
        {  
            $("#show-cart").html(msg);
        }
    });
}

function cancel_receving_confirm(){
    $("#receiving-cancel-input").val('yes');
    $("#modal-confirm-receiving").modal('show');
}

function cancel_receving()
{  
    $("#show-loader").html('<i class="icon-spinner2 spinner" style="z-index: 30;position: absolute;font-size: 50px;color: #fff"></i>');
    $("#modal-confirm-receiving").modal('hide');
    $.ajax({
        type      :      'GET',
        url       :      '../transaction.php',
        data      :       {cancel_receiving:""},
        success  :       function(msg)     
        {  
            beep_success();
            $.jGrowl('Receiving successfully cancelled.', {
                header: 'Success Notification',
                theme: 'alert-styled-right bg-success'
            }); 
            total();
            view_cart();
            $("#show-loader").html('');
        }
    });
    
}


function total()
{  
    $.ajax({
        type      :      'GET',
        dataType  :      'JSON',
        url       :      '../transaction.php',
        data      :       {view_total2:""},
        success  :       function(msg)     
        {  console.log(msg);
           $("#show-subtotal").html(msg['subtotal_amount']);
           $("#grand-total").html(msg['total_amount']);
           $("#show-discount").html(msg['discount']); 
           $("#grand-total").html(msg['total_amount']);
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
        data      :       {delete_cart2:"",cart_id:cart_id},
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
    if (event.keyCode == 13) { 
        $("#show-loader").html('<i class="icon-spinner2 spinner" style="z-index: 30;position: absolute;font-size: 50px;color: #fff"></i>');
        var cart_id = $(el).attr('cart_id');
        var quantity_order = $(el).attr('quantity_order');
        var new_quantity_order = $(el).val();
        $.ajax({
            type      :      'GET',
            url       :      '../transaction.php',
            dataType  :      'JSON',
            data      :       {update_cart2:"",cart_id:cart_id,new_quantity_order:new_quantity_order},
            success  :       function(msg)     
            {  console.log(msg);
                if (msg['message']=='ok'){
                    total();
                    view_cart();
                    beep_success();
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
}


function update_price(el)
{
    if (event.keyCode == 13) { 
        $("#show-loader").html('<i class="icon-spinner2 spinner" style="z-index: 30;position: absolute;font-size: 50px;color: #fff"></i>');
        var cart_id = $(el).attr('cart_id');
        var price = $(el).val(); 
        if (price=="") {
            var price = 0; 
        }
        $.ajax({
            type      :      'GET',
            url       :      '../transaction.php',
            dataType  :      'JSON',
            data      :       {update_price:"",cart_id:cart_id,price:price},
            success  :       function(msg)     
            {  console.log(msg);
                if (msg['message']=='ok'){
                    total();
                    view_cart();
                    beep_success();
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
}

function view_products()
{
    $("#show-data-all").html('<div style="width:100%;height:100%;position:absolute;left:50%;right:50%;top:40%;"><img src="../images/LoaderIcon.gif"  ></div>');
    $("#show-button").html('');
    $("#title-all").html('List of products');
    $("#modal-all").modal('show');
    setTimeout(function(){ $("#show-data-all").load('sales-products.php');  }, 1000);
    
}

function refresh()
{
    location.reload();
}

$( "#quatity-input" ).change(function() {
    if ($(this).val()=="") {
        $(this).val(1);  
    }
});

function new_supplier()
{
    $("#modal_new").modal('show');
}


function add_discount()
{
   var amount = parseFloat($("#show-discount").text().replace (/,/g, ""));
    $("#payment-open").val('');
    if ($("#discount-open").val()!='yes') {
        $('.modal').modal('hide');
        $("#current-form").val('2');
        $("#modal-discount").modal('show');
        $("#discount").val(amount);
         setTimeout(function(){ $("#discount").focus(); }, 500);
        $("#discount-open").val('yes');
    }
}

$('#form-discount').on('submit', function (e) 
{   
    var discount = parseFloat($("#discount").val()); 
    if ($("#discount").val()=="") {
         $.jGrowl('Please enter valid amount.', {
            header: 'Error Notification',
            theme: 'alert-styled-right bg-danger'
        });
        $("#discount").focus();
    }else{
        var data = $(this).serialize();
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
                        $("#payment-open").val('');
                         $("#discount-open").val('');
                        
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

function beep_success(){
    var obj = document.createElement("audio");
    obj.src="../audio/scanner.ogg";
    obj.autoPlay=false;
    obj.preLoad=true;       
    obj.play();      
}

function beep_success2(){
    var obj = document.createElement("audio");
    obj.src="../audio/double-beep.ogg";
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





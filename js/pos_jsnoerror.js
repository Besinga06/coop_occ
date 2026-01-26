$(document).ready(function () {

    if (is_update == true) {
        total_update();
        view_cart_update();
    } else {
        total();
        view_cart();
    }
});

$("#product-input").keyup(function () {
    $("#show-loader").html('<i class="icon-spinner2 spinner" style="z-index: 30;position: absolute;font-size: 50px;color: #fff"></i>');
    $("#show-search").show();
    var keywords = $(this).val();
    if (keywords != "") {
        $.ajax({
            type: 'GET',
            url: '../transaction.php',
            data: { searh_menu: "", keywords: keywords },
            success: function (msg) {
                $("#show-search").html(msg);
                $("#show-loader").html('');
            },
            error: function (msg) {
                alert('Something went wrong!');
            }
        });
    } else {
        $("#product-input").click();
        $("#show-loader").html('');
    }
});


$("#product-input").click(function () {
    $("#show-loader").html('<i class="icon-spinner2 spinner" style="z-index: 30;position: absolute;font-size: 50px;color: #fff"></i>');
    $("#show-search").show();
    $("#show-search-customer").hide();
    $.ajax({
        type: 'GET',
        url: '../transaction.php',
        data: { searh_menu: "", keywords: "" },
        success: function (msg) {
            $("#show-search").html(msg);
            $("#show-loader").html('');

        },
        error: function (msg) {
            alert('Something went wrong!');
        }
    });
});

$("#searchproduct").click(function () {
    $("#show-search").html("");
    $("#product-input").val("");
    $("#show-search").hide();
});


$("#customer-input").keyup(function () {
    $("#show-loader").html('');
    $("#show-loader").html('<i class="icon-spinner2 spinner" style="z-index: 30;position: absolute;font-size: 50px;color: #fff"></i>');
    $("#show-search-customer").show();
    var keywords = $(this).val();
    if (keywords != "") {
        $.ajax({
            type: 'GET',
            url: '../transaction.php',
            data: { search_customer: "", keywords_search: keywords },
            success: function (msg) {
                $("#show-search-customer").html(msg);
                $("#show-loader").html('');
            },
            error: function (msg) {
                alert('Something went wrong!');
            }
        });
    } else {
        $("#customer-input").click();
        $("#show-loader").html('');
    }

});

$("#customer-input").click(function () {
    $("#show-loader").html('<i class="icon-spinner2 spinner" style="z-index: 30;position: absolute;font-size: 50px;color: #fff"></i>');
    $("#show-search-customer").show();
    $("#show-search").hide();
    $.ajax({
        type: 'GET',
        url: '../transaction.php',
        data: { search_customer: "", keywords_search: "" },
        success: function (msg) {
            $("#show-search-customer").html(msg);
            $("#show-loader").html('');
        },
        error: function (msg) {
            alert('Something went wrong!');
        }
    });
});

$("#searchcustomer").click(function () {
    $("#show-search-customer").html("");
    $("#customer-input").val("Walk-in Customer");
    $("#cust_id").val(1);
    $.ajax({
        type: 'GET',
        url: '../transaction.php',
        data: { session_customer: "", cust_id: 1, name: 'Walk-in Customer', is_update: is_update },
        success: function (msg) {

        }
    });
    $("#show-search-customer").hide();
});


function select_customer(el) {
    var cust_id = $(el).attr('cust_id');
    var name = $(el).attr('name');
    $("#cust_id").val(cust_id);
    $("#customer-input").val(name);
    $("#show-search-customer").hide();
    $.ajax({
        type: 'GET',
        url: '../transaction.php',
        data: { session_customer: "", cust_id: cust_id, name: name, is_update: is_update },
        success: function (msg) {

        }
    });
}

function select_product(el) {
    $("#show-loader").html('<i class="icon-spinner2 spinner" style="z-index: 30;position: absolute;font-size: 50px;color: #fff"></i>');
    var product_id = $(el).attr('menu_id');
    var product_name = $(el).attr('menu_name');
    var quantity = $("#quatity-input").val();
    $("#product-input").val("");
    $("#show-search").hide(); 
    $.ajax({
        type: 'POST',
        url: '../transaction.php',
        dataType: 'JSON',
        data: { save_cart: "", product_id: product_id, quantity: quantity, is_update: is_update },
        success: function (msg) {  console.log(msg);
            if (msg['message'] == 'save') {
                beep_success();
                if (is_update == true) {
                    total_update();
                    view_cart_update();
                } else {
                    total();
                    view_cart();
                }
                $("#show-loader").html('');
            } else if (msg['message'] == 'save2') {
                beep_success2();
                if (is_update == true) {
                    view_cart_update();
                    total_update();
                } else {
                    view_cart();
                    total();
                }
                $("#show-loader").html('');
            } else if (msg['message'] == 'unsave') {
                beep_error();
                $.jGrowl('Desired quantity <b>(' + msg['quantity_order'] + ')</b> is greather than quantity left <b>(' + msg['quantity_left'] + ')</b>.Please check your inventory.', {
                    header: 'Error Notification',
                    theme: 'alert-styled-right bg-danger'
                });
                $("#show-loader").html('');
            } else {
                alert('Something went wrong!');
            }
        },
        error: function (msg) {
            alert('Something went wrong!');
        }
     });
    $("#quatity-input").val(1);
    return false;
}

function view_cart() {
    $.ajax({
        type: 'GET',
        url: '../transaction.php',
        data: { view_cart: "" },
        success: function (msg) {
            $("#show-cart").html(msg);
        }
    });
}

function view_cart_update() {
    $.ajax({
        type: 'GET',
        url: '../transaction.php',
        data: { view_cart_update: "" },
        success: function (msg) {
            $("#show-cart").html(msg);
        }
    });
}


function cancel_sale_confirm() {
    if (parseFloat($("#grand-total").text()) < 1) {
        $.jGrowl('Unable to  cancel', {
            header: 'Error Notification',
            theme: 'alert-styled-right bg-danger'
        });
    } else {
        $("#cancel-input").val('yes');
        $('.modal').modal('hide');
        $("#modal-cancel").modal('show');
    }
}

function cancel_sale() {
    $.ajax({
        type: 'GET',
        url: '../transaction.php',
        data: { cancel_sale: "" },
        success: function (msg) {
            setTimeout(function () { location.reload(); }, 1500);
            beep_success();
            $.jGrowl('Sales successfully cancelled.', {
                header: 'Success Notification',
                theme: 'alert-styled-right bg-success'
            });
        }
    });
}

$('#form-payment').on('submit', function (e) {
    $(':input[type="submit"]').prop('disabled', true);
    var payable = parseFloat($("#grand-total").text().replace(/,/g, ""));
    var payment = parseFloat($("#payment").val());
    var change_amount = payment - payable;
    if (payment < payable) {
        beep_error();
        $(':input[type="submit"]').prop('disabled', false);
        $.jGrowl('Insuficient amount <b>(' + payment.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,') + ')</b>.Please enter higher amount <b>(' + $("#grand-total").text() + ')</b>.', {
            header: 'Error Notification',
            theme: 'alert-styled-right bg-danger'
        });
    } else if ($("#payment").val() == "") {
        beep_error();
        $(':input[type="submit"]').prop('disabled', false);
        $.jGrowl('Please enter valid amount.', {
            header: 'Error Notification',
            theme: 'alert-styled-right bg-danger'
        });
        $("#payment").focus();

    } else {
        $("#discount-open").val('');
        $("#payment-open").val('');
        var data = $(this).serialize();
        var vat_sales = $("#show-vat-sales").text();
        var subtotal = $("#show-subtotal").text();
        var discount = $("#show-discount").text();
        after_sales(payable, payment, vat_sales, discount, subtotal, change_amount);
        $.ajax({
            type: 'POST',
            url: '../transaction.php',
            data: data,
            dataType: 'JSON',
            success: function (msg) {
                if (msg.success == '1') {
                    $("#modal-quantity").modal('hide');
                    $("#quantity").val("");
                    view_cart();
                    $("#loader").html("");
                    beep_success();
                    total();
                    print_receipt_data(msg.sales_no);
                } else {
                    alert('Something went wrong!');
                }
            },
            error: function (msg) {
                alert('Something went wrong!');
            }
        });
    }
    return false;
});






function after_sales(payable, payment, vat_sales, discount, subtotal, change_amount) {
    view_cart();
    total();
    $("#new-sales").val('yes');
    $("#footer-sales").hide();
    $(".modal-body").addClass('remove-height');
    $("#show-payment").html(' <div class="row  money-div"> <div class="col-md-12"> <div class="list-group no-border no-padding-top"> <a href="javascript:;" class="list-group-item"><i class="icon-cash3"></i> Sub Total <span class="pull-right" >' + subtotal + '</span></a> <a href="javascript:;" class="list-group-item"><i class="icon-cash3"></i> Discount <span class="pull-right " >' + discount + '</span></a>  <div class="list-group-divider"></div> <a href="#" class="list-group-item" style="font-weight: bold;font-size: 14px"><i class="icon-cash3"></i> Amount Due <span class="pull-right"  >' + payable.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,') + '</span></a> <a href="#" class="list-group-item" style="font-weight: bold;font-size: 14px"><i class="icon-cash3"></i> Cash <span class="pull-right"  >' + payment.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,') + '</span></a> <a href="#" class="list-group-item" style="font-weight: bold;font-size: 20px"><i class="icon-cash3"></i> Change <span class="pull-right"  >' + change_amount.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,') + '</span></a> </div> </div> <div style="width:100%;text-align:center" > </div></div><div style="width:100%;text-align:center"> <button id="btn-print"  onclick="refresh()" type="button" class="btn bg-danger-400 btn-labeled"><b>ESC</b>  Close </button></div></div></div>');

}



function print_receipt_data(el) {
    $("#print-receipt").load('receipt.php?sales_no=' + el);
    ///setTimeout(function(){ print_receipt(); }, 3000);
    // setInterval(function(){
    //     var printData = $("#print-receipt").html();
    //     if(printData!==""){
    //         print_receipt();
    //     }
    // }, 1000);

    var myVar = setInterval(myTimer, 500);

    function myTimer() {
        var printData = $("#print-receipt").html();
        if (printData !== "") {
            myStopFunction();
            print_receipt();
        }
    }

    function myStopFunction() {
        clearInterval(myVar);
    }
}

function print_receipt() {
    var contents = $("#print-receipt").html();
    var frame1 = $('<iframe />');
    frame1[0].name = "frame1";
    frame1.css({ "position": "absolute", "top": "-1000000px" });
    $("body").append(frame1);
    var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
    frameDoc.document.open();
    frameDoc.document.write('<html><head><title></title>');
    frameDoc.document.write('</head><body>');
    frameDoc.document.write(contents);
    frameDoc.document.write('</body></html>');
    frameDoc.document.close();
    setTimeout(function () {
        window.frames["frame1"].focus();
        window.frames["frame1"].print();
        frame1.remove();
    }, 500);
}

function total() {
    $.ajax({
        type: 'GET',
        dataType: 'JSON',
        url: '../transaction.php',
        data: { view_total: "" },
        success: function (msg) {   console.log(msg);
            $("#total_cart").val(msg['total_cart']);
            // $("#show-vat-sales").html(msg['vat_sales']);
            // $("#show-vat-amount").html(msg['vat_amount']);
            $("#show-subtotal").html(msg['subtotal_amount']);
            $("#grand-total").html(msg['total_amount']);
            $("#show-discount").html(msg['discount']);
        }
    });
}

function total_update() {
    $.ajax({
        type: 'GET',
        dataType: 'JSON',
        url: '../transaction.php',
        data: { view_total_update: "" },
        success: function (msg) {
            $("#total_cart").val(msg['total_cart']);
            $("#show-vat-sales").html(msg['vat_sales']);
            $("#show-vat-amount").html(msg['vat_amount']);
            $("#show-subtotal").html(msg['subtotal_amount']);
            $("#grand-total").html(msg['total_amount']);
            $("#show-discount").html(msg['discount']);
        }
    });
}

function delete_cart(el) {
    $("#show-loader").html('<i class="icon-spinner2 spinner" style="z-index: 30;position: absolute;font-size: 50px;color: #fff"></i>');
    var cart_id = $(el).attr('cart_id');
    $.ajax({
        type: 'GET',
        url: '../transaction.php',
        data: { delete_cart: "", cart_id: cart_id },
        success: function (msg) {
            total();
            view_cart();
            beep_success();
            $("#show-loader").html('');
        },
        error: function (msg) {
            alert('Something went wrong!');
        }
    });

}

function delete_cart_update(el) {
    $("#show-loader").html('<i class="icon-spinner2 spinner" style="z-index: 30;position: absolute;font-size: 50px;color: #fff"></i>');
    var cart_id = $(el).attr('cart_id');
    $.ajax({
        type: 'GET',
        url: '../transaction.php',
        data: { delete_cart_update: "", cart_id: cart_id },
        success: function (msg) {
            total_update();
            view_cart_update();
            beep_success();
            $("#show-loader").html('');
        },
        error: function (msg) {
            alert('Something went wrong!');
        }
    });

}


function update_cart(el) { 
    if (event.keyCode == 13) { 
        $("#show-loader").html('<i class="icon-spinner2 spinner" style="z-index: 30;position: absolute;font-size: 50px;color: #fff"></i>');
        var cart_id = $(el).attr('cart_id');
        var quantity_order = $(el).attr('quantity_order');
        var new_quantity_order = $(el).val();
        if (new_quantity_order < 1) {
            var new_quantity_order = quantity_order;
        } 
        $.ajax({
            type: 'GET',
            url: '../transaction.php',
            dataType: 'JSON',
            data: { update_cart: "", cart_id: cart_id, new_quantity_order: new_quantity_order },
            success: function (msg) {
                if (msg['message'] == 'ok') {
                    total();
                    view_cart();
                    beep_success();
                    $("#show-loader").html('');
                } else if (msg['message'] == 'not_ok') {
                    beep_error();
                    $.jGrowl('Desired quantity <b>(' + msg['quantity_order'] + ')</b> is greather than quantity left <b>(' + msg['quantity_left'] + ')</b>.Please check your inventory.', {
                        header: 'Success Notification',
                        theme: 'alert-styled-right bg-danger'
                    });
                    $("#show-loader").html('');
                } else {
                    alert('Something went wrong!');
                }
            },
            error: function (msg) {
                alert('Something went wrong!');
            }
        });
    }
}


function update_cart_old(el) {

    $("#show-loader").html('<i class="icon-spinner2 spinner" style="z-index: 30;position: absolute;font-size: 50px;color: #fff"></i>');
    var cart_id = $(el).attr('cart_id');
    var quantity_order = $(el).attr('quantity_order');
    var new_quantity_order = $(el).val();
    if (new_quantity_order < 1) {
        var new_quantity_order = quantity_order;
    }
    $.ajax({
        type: 'GET',
        url: '../transaction.php',
        dataType: 'JSON',
        data: { update_cart: "", cart_id: cart_id, new_quantity_order: new_quantity_order },
        success: function (msg) {
            if (msg['message'] == 'ok') {
                total();
                view_cart();
                beep_success();
                $("#show-loader").html('');
            } else if (msg['message'] == 'not_ok') {
                beep_error();
                $.jGrowl('Desired quantity <b>(' + msg['quantity_order'] + ')</b> is greather than quantity left <b>(' + msg['quantity_left'] + ')</b>.Please check your inventory.', {
                    header: 'Success Notification',
                    theme: 'alert-styled-right bg-danger'
                });
                $("#show-loader").html('');
            } else {
                alert('Something went wrong!');
            }
        },
        error: function (msg) {
            alert('Something went wrong!');
        }
    });
}

function update_cart3(el) {
    $("#show-loader").html('<i class="icon-spinner2 spinner" style="z-index: 30;position: absolute;font-size: 50px;color: #fff"></i>');
    var cart_id = $(el).attr('cart_id');
    var quantity_order = $(el).attr('quantity_order');
    var new_quantity_order = $(el).val();
    if (new_quantity_order < 1) {
        //var new_quantity_order = quantity_order;
    }
    $.ajax({
        type: 'GET',
        url: '../transaction.php',
        dataType: 'JSON',
        data: { update_cart3: "", cart_id: cart_id, new_quantity_order: new_quantity_order },
        success: function (msg) {
            if (msg['message'] == 'ok') {
                total_update();
                view_cart_update();
                beep_success();
                $("#show-loader").html('');
            } else if (msg['message'] == 'not_ok') {
                beep_error();
                $.jGrowl('Desired quantity <b>(' + msg['quantity_order'] + ')</b> is greather than quantity left <b>(' + msg['quantity_left'] + ')</b>.You can update the product but please check your inventory.', {
                    header: 'Warning Notification',
                    theme: 'alert-styled-right bg-danger'
                });
                $("#show-loader").html('');
            } else {
                alert('Something went wrong!');
            }
        },
        error: function (msg) {
            alert('Something went wrong!');
        }
    });
}

$('#quatity-input').change(function () {
    var quantity = $(this).val();
    if (quantity < 1) {
        $(this).val(1);
    }
});

$('#form-discount').on('submit', function (e) {
    var discount = parseFloat($("#discount").val());
    if ($("#discount").val() == "") {
        $.jGrowl('Please enter valid amount.', {
            header: 'Error Notification',
            theme: 'alert-styled-right bg-danger'
        });
        $("#discount").focus();
    } else {
        var data = $(this).serialize();
        $("#discount-open").val();
        $.ajax({
            type: 'POST',
            url: '../transaction.php',
            data: data,
            success: function (msg) {
                $("#modal-discount").modal('hide');
                if (msg == '1') {
                    view_cart();
                    total();
                    beep_success();
                    $("#modal-quantity").modal('hide');
                    $("quantity").val("");
                    $("#payment-open").val('');
                    $("#discount-open").val('');

                } else {
                    alert('Something went wrong!');
                }
            },
            error: function (msg) {
                alert('Something went wrong!');
            }
        });
    }
    return false;
});


function my_sale() {

    $("#show-data-all").html('<div style="width:100%;height:100%;position:absolute;left:50%;right:50%;top:40%;"><img src="../images/LoaderIcon.gif"  ></div>');
    $("#show-button").html('');
    $("#title-all").html('My sales for today');
    $("#modal-all").modal('show');
    setTimeout(function () { $("#show-data-all").load('my-sale-today.php'); }, 1000);
}

function view_products() {
    $("#show-data-all").html('<div style="width:100%;height:100%;position:absolute;left:50%;right:50%;top:40%;"><img src="../images/LoaderIcon.gif"  ></div>');
    $("#show-button").html('');
    $("#title-all").html('List of products');
    $("#modal-all").modal('show');
    setTimeout(function () { $("#show-data-all").load('sales-products.php'); }, 1000);

}



function add_discount() {
    var amount = parseFloat($("#show-discount").text().replace(/,/g, ""));
    if (amount == 0) {
        amount = "";
    }
    $("#payment-open").val('');
    if ($("#discount-open").val() != 'yes') {
        $('.modal').modal('hide');
        $("#current-form").val('2');
        $("#modal-discount").modal('show');
        $("#discount").val(amount);
        setTimeout(function () { $("#discount").focus(); }, 500);
        $("#discount-open").val('yes');
    }
}



function view_customer() {
    $("#show-button").html('<button type="button" onclick="new_customer()" class="btn btn-info btn-labeled "><b><i class="icon-add"></i></b> New Customer</button>');
    $("#title-all").html('List of customer');
    $("#modal-all").modal('show');
    $("#show-data-all").load('customer-view.php');
}

function select_key(el) {

    var cusrrent_payment = $("#payment").val();
    var new_payment = cusrrent_payment + el;
    $("#payment").val(new_payment);
}

function clear_all() {
    $("#payment").val("");
}

function clear_all2() {
    $("#discount").val("");
}



function select_key2(el) {

    var cusrrent_payment = $("#discount").val();
    var new_payment = cusrrent_payment + el;
    $("#discount").val(new_payment);
}

function clear_last2() {
    var str = $('#discount').val();
    $('#discount').val(str.substring(0, str.length - 1));
}

function clear_last() {
    var str = $('#payment').val();
    $('#payment').val(str.substring(0, str.length - 1));
}

function refresh() {
    location.reload();
}

function refresh2() {
    window.location = 'close-open-register-report.php';
}



function beep_success() {
    var obj = document.createElement("audio");
    obj.src = "../audio/scanner.ogg";
    obj.autoPlay = false;
    obj.preLoad = true;
    obj.play();
}

function beep_success2() {
    var obj = document.createElement("audio");
    obj.src = "../audio/double-beep.ogg";
    obj.autoPlay = false;
    obj.preLoad = true;
    obj.play();
}

function beep_error() {
    var obj = document.createElement("audio");
    obj.src = "../audio/error.ogg";
    obj.autoPlay = false;
    obj.preLoad = true;
    obj.play();
}

function new_customer() {
    $(".modal").modal('hide');
    $("#modal-new").modal('show');
}

function cancel_void() {
    $.ajax({
        type: 'GET',
        //dataType  : 'JSON',
        url: '../transaction.php',
        data: { cancelUpdate: "" },
        success: function (msg) {
            window.location.href = 'close-open-register-report.php';

        },
        error: function (msg) {
            alert('Something went wrong!');
        }
    });
}







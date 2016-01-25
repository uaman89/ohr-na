/**
 * Created with JetBrains PhpStorm.
 * User: bogdan
 * Date: 12.07.13
 * Time: 10:55
 * To change this template use File | Settings | File Templates.
 */
function me(e){
    /*if(e.charCode==8){alert('good');}*/
    if((e.charCode>47&&e.charCode<58)||e.keyCode==8 ||e.keyCode==37 ||e.keyCode==39){
        return true;
    }else
        return false;
}
//перещот нового количества
function recalculation(id){

    var val = parseInt($('#quantity'+id).val());//новое количество товара
    if(val>0){
        updateOrder(id,val);
//        alert(val);
        var price = parseFloat($('#price'+id).text());//цена товара
//        alert(price);
        var summ = parseFloat(price * val).toFixed(2);//навая сумма
        $('#summ'+id).html(summ+' грн.');

    }

}
function updateOrder(id,val){
    $.ajax({
        type: "POST",
        data: "&quantity["+id+"]="+val ,
        url: "/order/update/",
        success: function(msg){
           // alert(msg);
            $('#orderSummAll').html(msg);
            ShowCart();
            ShowSum();

        }
    });
}
//удаление товара
function ajaxRemoveProductInCart(id){
    $.ajax({
        type: "POST",
        data: "&id="+id ,
        url: "/order/del_pos/",
        success: function(msg){
            $("#reloadOrder").hide();
            $('#mybasket').html(msg);
            ShowCart();
            ShowSum();
        },
        beforeSend : function(){
//            $("#reloadOrder").show();
        }
    });
}


function ShowCart(){
    $.ajax({
        type: "POST",
        url: "/order/show_cart/",
        success: function(msg){
            $("#cart").html( msg );
        },
        beforeSend : function(){
            $("#cart").html( '<div style="text-align: center;"><img src="/images/design/loader.gif" /></div>' );
        }
    });
}
function ShowSum(){
    $.ajax({
        type: "POST",
        url: "/order/sum/",
        success: function(msg){
            $("#sum").html( msg );
        },
        beforeSend : function(){
            $("#sum").html( '<div style="text-align: center;"><img src="/images/design/loader.gif" /></div>' );
        }
    });
}
function sendOrder(){
    if(!$("#shopping_cart_form").validationEngine('validate')) return false;
//    alert($('.delivery-select').val());
    $.ajax({
        type: "POST",
        data: $("#shopping_cart_form").serialize() ,
        url: '/order/result/',
        success: function(msg){
            var resSend = parseInt($(msg).find('#resSendVal').val());
//            alert($(msg).find('#resSendVal').val());
//            console.log($(msg).find('input#resSendVal'));
            if(resSend==1){
                $.fancybox({
                    content:msg,
                    padding : 0,
                    afterClose : function() {
                        location.href = '/catalog/';
                    }
                });
            }else{
                $.fancybox.close();
                $('#my_d_basket').html(msg);
            }
        },
        beforeSend : function(){
            $.fancybox('<img src="/images/design/ajax-loader.gif" alt="" />');
        }
    });
    return false;
}
function print_r(arr, level) {
    var print_red_text = "";
    if(!level) level = 0;
    var level_padding = "";
    for(var j=0; j<level+1; j++) level_padding += "    ";
    if(typeof(arr) == 'object') {
        for(var item in arr) {
            var value = arr[item];
            if(typeof(value) == 'object') {
                print_red_text += level_padding + "'" + item + "' :\n";
                print_red_text += print_r(value,level+1);
            }
            else
                print_red_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
        }
    }

    else  print_red_text = "===>"+arr+"<===("+typeof(arr)+")";
    return print_red_text;
}
function orderclear(){
    $.ajax({
        type: "POST",
        url: "/order/del_all/",
        success: function(msg){
            $("#reloadOrder").hide();
//            alert(msg);
            $('#my_d_basket').html(msg);
            ShowCart();
        },
        beforeSend : function(){
            $("#reloadOrder").show();
        }
    });
}

function showFullCart(){

    $.ajax({
        type: "POST",
        url: '/order.php?=task=full_cart',
        success: function(msg){
            $("#reloadOrder").hide();
//            alert(msg);
            $('#my_d_basket').html(msg);
        }
    });
}
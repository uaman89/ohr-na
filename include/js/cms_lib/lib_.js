$(document).ready(function(){


    if( ('#mform').length >0 ) {
        $('#m-right').html( $('#mform').html() );
    }
    $("#form_mod_feedback").validationEngine();


    var QueryString = function () {

        var query_string = {};
        var query = window.location.search.substring(1);
        var vars = query.split("&");
        for (var i=0;i<vars.length;i++) {
            var pair = vars[i].split("=");

            if (typeof query_string[pair[0]] === "undefined") {
                query_string[pair[0]] = pair[1];

            } else if (typeof query_string[pair[0]] === "string") {
                var arr = [ query_string[pair[0]], pair[1] ];
                query_string[pair[0]] = arr;

            } else {
                query_string[pair[0]].push(pair[1]);
            }
        }
        return query_string;
    } ();

    var uri = window.location.pathname;

    jQuery.each(QueryString, function(i, val) {
        if(i.indexOf("parcod")!= '-1'){
            var obj = $(".link-categ a[href='"+uri+"']").parent().parent().children().children('.menu-filter');
            obj.css('display', 'block');
            obj.css('height', 'auto');
            obj.css('border', '2px solid #0cb795');
            return false;
        }
    });

    $('.listCat li').hover(function(){
        $('.listCat li').children().children('.menu-filter').attr('style', '');

    });

    $('.btn-categ').click( function(){

      $('.btn-categ').removeClass('active-t');
      $(this).addClass('active-t');
       var id = $(this).children().children('.tab-item').data('item');

       $('.categ-prop').removeClass('active-prod');

       $('#p'+id).addClass('active-prod');
    });

   $('#enterUser').click(function(){

       $.ajax({
           type: "POST",
           url: "/login.html",
           success:function(msg){

               $.fancybox(msg,{
                   'padding'		: 0
               });

           }

       });
   });


    $("nav .param-key-one-item").click(function (){
        if( !$(this).hasClass('param-no-selected') ){


            if( $(this).hasClass('param-selected') ) {

                $(this).removeClass('param-selected');
                $("input[type=checkbox]", this).prop('checked', false);
            }else{

                $(this).addClass('param-selected');
                $("input[type=checkbox]", this).prop('checked', true);
            }

        }


    });



    $('#regUser').click(function(){
       $.ajax({
           type: "POST",
           url: "/registration",
           success:function(msg){

               $.fancybox(msg,{
                   'padding'		: 0
               });

           }

       });
   });

 });



$(window).load(function(){

    $('.arrow-top').live( "click", function() {
        var valueCount = parseInt($(this).parent().parent().children('input').val());
        valueCount++;
        $(this).parent().parent().children('input').val(valueCount);
        var idCount = parseInt($(this).parent().parent().children('input').data('c'));
        recalculation(idCount);
    });


    $('.arrow-bottom').live( "click", function() {
        var valueCount = parseInt($(this).parent().parent().children('input').val());

        if(valueCount>1) {
            valueCount--;
            $(this).parent().parent().children('input').val(valueCount);
            var idCount = parseInt($(this).parent().parent().children('input').data('c'));
            recalculation(idCount);
        }


    });

    $('.filter-menu-c').click(function(){
        getValueUsingClass( $(this).parent() ) ;
    });

    $('#foo1').carouFredSel({
        item: {
            visible: 3
        },
        auto: false,
        prev: '#foo1_prev',
        next: '#foo1_next',
        mousewheel: true,
        swipe: {
            onMouse: true,
            onTouch: true
        }
    });

    $('#foo1 > a').click(function(){

//        $('.img-big > a > img').attr('src', $(this).data('img'));
//        $('.img-big > .sel-img').attr('href', $(this).data('big-img'));
        var itemBig = $(this).data('id');

        $('.img-big >a').css('z-index', 1);
        $('#i'+itemBig).css('z-index', 100);

    });


    $('#Login').validationEngine();

    $('#f1').validationEngine();

    $("nav .filter-block-all-param").each(function() {
        var w = 0;
        var h = 0;

        $(this).children('.paramBlock').each(function() {

            w = w + $(this).width();

            if( $(this).height() > h) {
                h = $(this).height();
            }
        });

        $(this).children('.paramBlock').css('height', h);

        if(w==0){
            $(this).parent().parent().parent().addClass('none-sub');
            $(this).parent().parent('.menu-param').remove();


        }




    });






});


function searchValue(value){
    var len  = value.length;
    if(len<3){

        return false;
    }

    $.ajax({
        type: "POST",
        url: "/search/result/?query="+value,
        success:function(msg){

            $('#searchw').remove();
            $( "#findBox").append( "<div id='searchw'>"+msg+"</div>" );

        }
    });
}

function preload(image)
{var d=document; if(!d.wb_pre) d.wb_pre=new Array();
var l=d.wb_pre.length; d.wb_pre[l]=new Image; d.wb_pre[l].src=image;
}

function over_on(n,ovr)
{var d=document,x; x=d[n];if (!(x) && d.all) x=d.all[n];
if (x){        document.wb_image=x; document.wb_normal=x.src; x.src=ovr; }}

function over_off()
{var x=document.wb_image; if (document.wb_normal) x.src=document.wb_normal;}


function ajaxResponse(responseText) {
    try {
        $response = $.parseJSON(responseText);
        if ($response.err) {
            switch ($response.err) {
                case 'user_login':
                    window.location.href = '/login.html';
                    break;
                case 'msg':
                    if ($response.div_id) {
                        $("#" + $response.div_id).validationEngine('showPrompt', $response.err_cont, 'err', 'topRight', true);
                        $(".parentFormundefined").click(function () {
                            $(this).remove();
                        });
                    } else
                        alert($response.err_cont);
                    break;
                case 'func':
                    if ($response.func) {
                        if ($response.param)
                            actions[$response.func]({param:$response.param});
                        else
                            actions[$response.func]();
                    }
                    break;
            }

            return false;
        }
        if ($response.ok) {
            switch ($response.ok) {
                case 'file':
                    $("#userAvatarTrueId").val($response.file);
                    $("#userEditFormIDAvatar img").attr('src', '/uploads/tmp/' + $response.file);
                    $("#userAvatarDelBtn").fadeTo('fast', 1);
                    break;
                case 'msg':
                    if ($response.div_id)
                        $("#" + $response.div_id).validationEngine('showPrompt', $response.ok_cont, 'pass', 'topRight', true);
                    else
                        alert($response.ok_cont);
                    break;
                case "msg_div":
                    $("#" + $response.div_id).fadeTo('fast', 0, function () {
                        $(this).html($response.ok_cont).fadeTo('fast', 1);
                        if ($response.div_id2 && $response.ok_cont2)
                            $("#" + $response.div_id2).html($response.ok_cont2);
                        if ($response.func)
                            actions[$response.func]();
                    });
                    break;
                case 'func':
                    if ($response.func) {
                        if ($response.param)
                            actions[$response.func]({param:$response.param});
                        else
                            actions[$response.func]();
                    }
                    break;
                case 'return_html':
                    if ($response.return_html)
                        return $response.return_html
                    break;
            }
            return true;
        }
    } catch (e) {
        alert("Возникла ошибка. Попробуйте ещё раз или обратитесь к администрации." + e.message);
    }
}

var actions = {

}


function validForm(){
    if($('#order_comments').validationEngine('validate')){
        $.ajax({
            type: "POST",
            data: $('#order_comments').serialize(),
            url: "/order/step4/",
            success:function(msg){

                $.fancybox(msg, {
                    'afterClose'		: function() {
                        window.location.href = "/";

                    }
                });

                $('.msg').html();
            },
            beforeSend: function() {
                $('.msg').html('<img src="/images/design/ajax-loader-cart.gif"/>');

            }

        });

    }else{
        return false;
    }
}

function addToCart(idForm, idRes, id){

    idResp = idRes;
    $.ajax({
        type: "POST",
        data: $('#'+idForm).serialize()+'&lang_pg='+_JS_LANG_ID+'&task=add_to_cart',
        url: "/modules/mod_order/order.php",

        success:function(msg){

            $('#al'+id).html('<div class="msg" align="center">Товар добавлен в <a href="/order/">корзину</a></div>');
            $('#cart').html(msg);

        },
        beforeSend: function() {
            //$('#multiAdds'+id).hide();
            $('#al'+id).html('<div class="msg" align="center" style="width:80px;" ><img src="/images/design/ajax-loader-cart.gif"/></div>');
            $('#cart').html('<div align="center" style="width:175px;" ><br/><img src="/images/design/ajax-loader-cart.gif"/></div>');
        }
      });

} // end of function addToCart

function addToCartSet(idForm, idRes, id, listid, countlist){
    idResp = idRes;

    var countSet = parseInt( $('#'+idForm+ ' .quantity').val() );



    $.ajax({
        type: "POST",
        data: 'listid='+listid+'&countset='+countSet+'&countlist='+countlist+'&lang_pg='+_JS_LANG_ID+'&task=add_to_cart_set',
        url: "/modules/mod_order/order.php",

        success:function(msg){

            $('#al'+id).html('<div class="msg" align="center">Товар добавлен в <a href="/order/">корзину</a></div>');
            $('#cart').html(msg);

        },
        beforeSend: function() {
            //$('#multiAdds'+id).hide();
            $('#al'+id).html('<div class="msg" align="center" style="width:80px;" ><img src="/images/design/ajax-loader-cart.gif"/></div>');
            $('#cart').html('<div align="center" style="width:175px;" ><br/><img src="/images/design/ajax-loader-cart.gif"/></div>');
        }
      });
} // end of function addToCart



function emailCheck (emailStr) {
    if (emailStr=="") return true;
    var emailPat=/^(.+)@(.+)$/;
    var matchArray=emailStr.match(emailPat);
    if (matchArray==null)
    {
        return false;
    }
    return true;
}

function verify(is_ajax_send) {
    if(is_ajax_send==1){
        if(!$("#form_mod_feedback").validationEngine('validate')) return false;
        save_order();
        return false;
    }
    return true;
}

function verify2() {
    var themessage = "Проверьте правильность заполнения данных:\n";
    if (document.forms.feedback.name.value=="") {
        themessage = themessage + " - Вы не заполнили поле : ФИО\n";
    }
    if ((!emailCheck(document.forms.feedback.e_mail.value))||(document.forms.feedback.e_mail.value=='')) {
        themessage = themessage + " - Неправильный e-mail адрес\n";
    }
    if (document.forms.feedback.tel.value=="") {
        themessage = themessage + " - Вы не указали контактный телефон\n";
    }
    if (document.forms.feedback.question.value=="") {
        themessage = themessage + " - Укажите текст вашего сообщения\n";
    }

    if (themessage == "Проверьте правильность заполнения данных:\n")
    {
        save_order_left();
        return true;
    }
    else
        alert(themessage);
    return false;
}

function save_order(){
    $.ajax({
        type: "POST",
        data: $("#form_mod_feedback").serialize() ,
        url: "/contacts/send/",
        success: function(msg){
            //alert(msg);
            $("#feedback").html( msg );
        },
        beforeSend : function(){
            $("#feedback").html('<div style="text-align:center;"><img src="/images/design/popup/ajax-loader.gif" alt="" title="" /></div>');
        }
    });
}

function save_order_left(){
    $.ajax({
        type: "POST",
        data: $("#feedback").serialize() ,
        url: "/feedback_ajax/",
        success: function(msg){
            //alert(msg);
            $("#container_feedback").html( msg );
        },
        beforeSend : function(){
            //$("#sss").html("");
            $("#rez").html('<div style="text-align:center;"><img src="/images/design/popup/ajax-loader.gif" alt="" title="" /></div>');
        }
    });
}
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

    $('#regUser').click(function(){
       $.ajax({
           type: "POST",
           url: "/registration",
           success:function(msg){

               $.fancybox(msg,{
                   'padding': 0
               });

           }

       });
   });

    $('#globalSearchField').focusout(function(){
       $('#searchw').hide(200);
    });

    $('#globalSearchField').focusin(function(){
       $('#searchw').show(150);
    });

 }); // end document.ready()

function make_phone_mask( id ){
    //console.log('make_mask for: ' + id);
    $( '#'+id ).mask(
        '38(xxx)xxxxxxx',
        {
            'translation': { 'x': { 'pattern': /[0-9]/ } },
            'placeholder': "38(xxx)xxxxxxx"
        }
    );
    $( '#'+id).bind('keypress', function(event){
        console.log('event.charCode',event.charCode);
        var denyInput = true;
        if (
            (event.charCode >= 48 && event.charCode <= 57) //0-9
            || event.charCode == 8 //backspace
            || event.charCode == 37 //left
            || event.charCode == 39 //right
            || event.charCode == 46 //delete
            || event.charCode == 0 //?
        ){
             denyInput = false;
        }

        if (denyInput) return false;
    });
}

function check_phone_length(field, rules, i, options){
    if ( (field.val().length < 14 ) && field.val().length > 0 )
        return "Введите номер полностью.";
    if ( (field.val().length > 14 ) && field.val().length > 0 )
        return "Слишком длинный номер (макс. 12 цифр).";
}

$(window).load(function(){
    auto_height_slider_items();

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

    $('#phoneMob').addClass('validate[required, funcCall[check_phone_length] ]');
    make_phone_mask('phoneMob');

    $('#profile').validationEngine( );
    $('form[name="order_comment"]').validationEngine( );

    $('#Login').validationEngine();

    $('#f1').validationEngine( );


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
            $( "#findBox").append( '<div id="searchw">' +
                                       '<span id="hideSearchRes" class="hide-search-res"></span>'
                                       + msg +
                                   '</div>'
                           );
            $('#hideSearchRes').click(function(){
                $('#searchw').hide();
            })
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
                case "add_success":
                    $("#" + $response.div_id).html('<div style="text-align: center; font-size: 14pt;"><h2>Спасибо за Ваш отзыв!</h2>Отзыв успешно доставлен и будет доступен после модерации</div>').delay(3000);
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

function goto_comments(){
    $('html, body').animate({
        scrollTop: $(".top-tabs").offset().top - 85
    }, 1500);

    $(document).ready(function(){
        $('#responseTab').trigger("click");
    });
}

function auto_height_slider_items( ){
    //wrapper_parents
    var wrapper_parents = $(".caroufredsel_wrapper").parent();

    $.each(wrapper_parents, function(){
        var cur_height;
        var bigger = 0;
        var smaller = 0;
        var big_blocks = {};
        //var smalls = {};
        var i = 0;
        var parent = $(this);
        //console.log('parent:' + $(parent).attr('class'));

        //проверяем вісоту названия блока с товаром
        $(parent).find(".caroufredsel_wrapper .prod .prod-name").each(function(){
            cur_height = $(this).outerHeight();
            //console.log('cur_height' + cur_height);
            if ( cur_height > 35 ) {
                bigger = cur_height;
                big_blocks[i] = this;
            }
            else{
                smaller = cur_height;
                //smalls[i] = this; //not use
            }
            i++;
        });

        if ( bigger > smaller ) {
             var diff = bigger - smaller;

            $.each(big_blocks, function () {
                var block = $(this).parent();
                $(block).find(".new-prop").css("margin-top", diff);        //novinka
                $(block).find(".hit-prop").css("margin-top", diff);        //xit
                $(block).find(".shareprop-prop").css("margin-top", diff);  //akciya

                $(block).find(".short-prop").css("min-height", 52);        //описание товара
            });

            //для вертикального слайдера (список товаров в категории)
            if ( $(parent).attr('class') == 'props-tab-left') {
                var count = Math.round( $(parent).height() / $(parent).find(".prod").height() );
                console.log('count: ' + count);
                if (count == 2) {
                    $(parent).height(685);
                    $(parent).find(".caroufredsel_wrapper").height( 685 );
                }
                //else if (count == 3 );
            }

            /* not use
             //change style for prod-name, which have two line of text
            $.each(smalls, function () {
                $(this).wrap('<div style="display: table; width: 100%;"></div>');
                $(this).css('display', 'table-cell');
                $(this).css('vertical-align', 'middle');
            });
            */
        }
        else {
            if ($(parent).attr('class') == 'props-tab-left') {
                if ($(parent).find(".prod").length < 2)
                    $(parent).height(325);
                else if ($(parent).find(".prod").length < 3)
                    $(parent).height(650);
            }
        }
        });//end each( parents )
}//end auto_heigt_slider_item


/*-----------------------------------------
*             var diff = bigger - smaller;
 console.log('bigger: ' + bigger + ', smaller: ' + smaller + ', diff: ' + diff);

 //wrapper_parent
 if ( $(parent).attr('class') == 'props-tab-left' ){
 $(parent).height(parent.height() + diff + 45);
 }
 else{
 if ( $(parent).parent().attr('class')!='props-tab' ) {                 //костиль на костилі
 $(parent).height(parent.height() + diff);
 }
 }

 // wrapper
 var wrapper = $(parent).find(".caroufredsel_wrapper");
 if ( $(parent).find(".prod").length < 3 ){
 $(wrapper).height( 705 );
 }
 else{
 $(wrapper).height( $(wrapper).height() + diff);
 }
 // wrapper -> prod_parent
 var prod_parent = $(parent).find(".caroufredsel_wrapper .prod").parent();
 $(prod_parent).css('height', $(prod_parent).height() + diff);

 // wrapper -> prod_parents -> prod
 if ( $(parent).attr('class') != 'props-tab-left') {
 $(parent).find(".caroufredsel_wrapper .prod").css('height', $(parent).find(".caroufredsel_wrapper .prod").height() + diff);
 }
 // wrapper -> prod_parents -> prod -> prod-name
 $(parent).find(".caroufredsel_wrapper .prod-name").outerHeight(bigger);
 */

$.fn.HasScrollBar = function() {
    //note: clientHeight= height of holder
    //scrollHeight= we have content till this height
    var _elm = $(this)[0];
    var _hasScrollBar = false;
    if ((_elm.clientHeight < _elm.scrollHeight) || (_elm.clientWidth < _elm.scrollWidth)) {
        _hasScrollBar = true;
    }
    return _hasScrollBar;
}

function remove_prop_descr_scroll( parent ){
    //console.log('parent: ' + parent);
    $(parent + ' .order-prop-name').each(function() {
        if ($(this)[0].clientHeight < $(this)[0].scrollHeight) {
            $(this).children('.dots').show();
            $(this).addClass('order-prop-name-popup');
            //console.log('has scrollbar');
        }
        $(this).fadeTo( 200, 1);
        //console.log('clinet: ' + $(this)[0].clientHeight + ', scroll: ' + $(this)[0].scrollHeight);
    });
}

function makeHistoryRequest(url_use, param, msq_id){
    elsement=document.getElementById(msq_id);
    $.ajax({
        type: "POST",
        data: param,
        url: url_use,

        success:function(msg){
            elsement.innerHTML=msg;

            block_id = '#' + msq_id;
            //console.log('id:' + block_id);
            remove_prop_descr_scroll( block_id );
            //console.log('#'+msq_id);
            //elsement.style.display='block';
            last = $('.hist:last-child').find('.order-prop-name').last();
            if ( $(last).hasClass('order-prop-name-popup') ) {
                $('.hist').last().css('margin-bottom', '80px');
                //console.log('last have popup. add margin bottom');
            }
            //else console.log('dont add margin');
        }
    });
}

//// button_np присваиваете класс на кнопку по кторой будет вызываться карта
//// npochta   - присваиваете этот класс полю для подбора адреса, смотрите файл start.html

$(document).ready(function(){
/*
$(".npochta").focus(function () {
    $(".npochta").val("");
    });
*/

$(".button_np").colorbox({width:"1000px", height:"650px", iframe:true, close:"x", scrolling:false, fixed:true});

/////////////////// если   lang = 1 в подборе работает только русский язык, если lang = 2 то русский и украинский
    window.lang = 2;

$.getScript('/np/ajax.js');

});

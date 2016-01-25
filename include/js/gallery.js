$(document).ready(function(){
    $('.fancybox').fancybox();

    $('#carouselLeft').carouFredSel({
        auto: false,
        prev: '#prev2',
        next: '#next2',
        mousewheel: true,
        swipe: {
            onMouse: true,
            onTouch: true
        }
    });

    sellImg(0);
 });

// Ф-ыя вивиоду зображення в детальному перегляді галереї. 
function showImage (path, path_org, alt, title) {
      $("#imageLarge").html( '<a href="'+path_org+'" class="fancybox"><img align="middle" src="'+path+'" alt="'+alt+'" title="'+title+'"/></a>' );
}

function sellImg(nomer){
    //alert(path);
    var elementHtml = $(".image-block-big>ul>li:eq("+nomer+") div a");
//        alert(elementHtml.find('json').html());
    if(elementHtml.find('json').html()!=undefined){
        var path_small = elementHtml.find('path-small').text();
        var alt = elementHtml.find('alt').text();
        var title = elementHtml.find('title').text();
        $(".image-block-big>ul>li:eq("+nomer+") div a").html('<img src="'+path_small+'" alt="'+alt+'" title="'+title+'" />');
    }
    $(".image-block-big>ul>li").hide();
    $(".image-block-big>ul>li:eq("+nomer+")").show();

    $("ul.image-block-big-okno>li").removeClass('current');
    $("ul.image-block-big-okno>li:eq("+nomer+")").addClass('current');
}
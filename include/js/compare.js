/**
 * Created with JetBrains PhpStorm.
 * User: bogdan
 * Date: 07.08.13
 * Time: 16:15
 * To change this template use File | Settings | File Templates.
 */
var expiresCookie = {
    expires : 0,
    path : '/'
}

function addComparePropInCompare(id,id_cat){
    addCompareProp(id,id_cat);
    $('.compare-prop-for-id'+id).html('');
    return false;
}

function addComparePropAndShowEnd(id,id_cat){
    addCompareProp(id,id_cat);
    return false;
}

function addCompareProp(id,id_cat){
    if (!navigator.cookieEnabled) {
        alert('Включите cookie для комфортной работы с этим сайтом');
    }
    $("#compare"+id+"Block").parent().parent().toggleClass('param-selected');
//    alert(getCookie('compare'+id_cat));
    var nameCookieCat = 'compare'+id_cat;
    var cookie = getCookie(nameCookieCat);
    if(cookie ==undefined){
        setCookie(nameCookieCat,id,expiresCookie);
        addForComparePanelProp(id,id_cat);
    }else{
        var mass = [];
        var str = '';
        mass = getCookie(nameCookieCat).split(',');
        if(in_array(id,mass)){
            var newMass = [];
            var count = mass.length,el;
            for(var i=0;i<count;i++){
                el = mass[i];
                if(id!=el) newMass.push(el);
            }
            str = newMass.toString(',');
            removeForComparePanelProp(id);
            if(str==''){
                deleteCookie(nameCookieCat);
            }else{
                setCookie(nameCookieCat,str,expiresCookie);
            }
        }else{
            mass.push(id);
            str = mass.toString(',');
            addForComparePanelProp(id,id_cat);
            setCookie(nameCookieCat,str,expiresCookie);
        }
    }
}

function clearCookie(id_cat){
    var nameCookieCat = 'compare'+id_cat;
//    alert(nameCookieCat);
    $('.list-prop .param-selected').removeClass('param-selected');
    deleteCookie(nameCookieCat);
    $('#comparePanel').html('');
    checkComparePanel();
}

function addForComparePanelProp(id,id_cat){
    var obje = $('#comparePanel');
    if(obje.html()!=undefined){
        var nameProp = $('#propNameToCompare'+id).text();
        var link = $('#compareStart'+id).attr('href')
        var text = '<a class="compare-panel-one-item" href="'+link+'"';
        text += 'onclick="return addComparePropAndShowEnd('+id+','+id_cat+')" id="comparePanelProp'+id+'">'
        text += '<span class="compare-kill"></span>';
        text += ' <span class="compare-name">'+nameProp+'</span>';
        text += '</a>';
        obje.append(text);
    }
    checkComparePanel();
}

function removeForComparePanelProp(id){
    var obje = $('#comparePanel');
    if(obje.html()!=undefined){
        var removeOb = $('#comparePanelProp'+id);
//        alert(removeOb.html());
        if(removeOb.html()!=undefined){
            removeOb.remove();
        }
    }
    checkComparePanel();
}

function checkComparePanel(){
    if($('#comparePanel').html()==''){
        $('#comparePanelFon').hide();
    }else{
        $('#comparePanelFon').show();
    }
}


// возвращает cookie с именем name, если есть, если нет, то undefined
function getCookie(name) {
    var matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}

// устанавливает cookie c именем name и значением value
// options - объект с свойствами cookie (expires, path, domain, secure)
function setCookie(name, value, options) {
    options = options || {};

    var expires = options.expires;

    if (typeof expires == "number" && expires) {
        var d = new Date();
        d.setTime(d.getTime() + expires*1000);
        expires = options.expires = d;
    }
    if (expires && expires.toUTCString) {
        options.expires = expires.toUTCString();
    }

    value = encodeURIComponent(value);

    var updatedCookie = name + "=" + value;

    for(var propName in options) {
        updatedCookie += "; " + propName;
        var propValue = options[propName];
        if (propValue !== true) {
            updatedCookie += "=" + propValue;
        }
    }

    document.cookie = updatedCookie;
}

// удаляет cookie с именем name
function deleteCookie(name) {
    setCookie(name, '', expiresCookie);
    setCookie(name, '', {
        expires: -1 ,
        path : '/'
    });
}

function in_array(needle, haystack, strict) {	// Checks if a value exists in an array
    //
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)

    var found = false, key, strict = !!strict;

    for (key in haystack) {
        if ((strict && haystack[key] === needle) || (!strict && haystack[key] == needle)) {
            found = true;
            break;
        }
    }

    return found;
}

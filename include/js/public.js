function showText(id){
    height = parseInt($('#publicText'+id).css('height'));
    heightTextSmall = parseInt($('#publicTextHeight'+id).val());
    heightTextReal = parseInt($('#publicTextHeightReal'+id).val());
    if(height==heightTextReal){
        $('#publicText'+id).animate({height:heightTextSmall}, 300);
    }else{
        $('#publicText'+id).animate({height:heightTextReal}, 300);
    }
}
function checkBlock(id){
    heightBlock = parseInt($('#blockContent'+id).css('height'));
    //alert('heightBlock='+heightBlock+' id='+id);
    heightImg = parseInt($('#blockImg'+id).css('height'));
    if(heightBlock>195 || (heightImg < heightBlock && heightImg>0)){
        if(heightImg < heightBlock && heightImg>0){
            alert('heightBlock='+heightBlock+' heightImg='+heightImg);
            heightRiznica = heightBlock - heightImg;
            //alert('heightRiznica='+heightRiznica+' id='+id);
        }else
            heightRiznica = heightBlock - 195;
        heightText = parseInt($('#publicText'+id).css('height'));  
        heightTextReal = heightText - heightRiznica;
        $('#publicTextHeightReal'+id).val(heightText);
        $('#publicTextHeight'+id).val(heightTextReal);

        $('#publicText'+id).css('height',heightTextReal+'px');
        $('#blockDetail'+id).css('display','block');
        //$('#publicText'+id).addClass('public-hide');
    }
}
function sendPublic(){
    if(!$("#public_addform").validationEngine('validate')) return false;
    $.ajax({
        type: "POST",
        data: $("#public_addform").serialize() ,
        success: function(msg){
            //alert(msg);
            $("#rez").html( msg );
        },
        beforeSend : function(){
            //$("#sss").html("");
            $("#rez").html('<div style="text-align:center;"><img src="/images/design/popup/ajax-loader-big.gif" alt="" title="" /></div>');
        }
    });
}
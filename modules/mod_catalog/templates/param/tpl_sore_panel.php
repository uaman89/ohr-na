<?php
$num = strlen($link_href);
?><div class="sore-panel">
    <div class="sore-panel-name"><?=$name_block?>:</div>
    <div class="sore-panel-one-item">
        <?if($asc_desc=='desc' AND $srt==true){?>
        <span><?=$name_elem1?></span>
        <?}else{


            ?><a href="<?=$catLink.$link_href?>" title="<?=$name_elem1?>"
                 onclick="return gelPropConetnt('<?=$catLink?>','<?=$link_href?><?php echo ($num>0)? '&' : '?'?><?="asc_desc=desc"?>');"><?=$name_elem1?></a><?
        }?>
    </div>
    <div class="sore-panel-probel">|</div>
    <div class="sore-panel-one-item">
        <?if($asc_desc=='asc' AND $srt==true ){?>
            <span><?=$name_elem2?></span>
        <?}else{
            ?><a href="<?=$catLink.$link_href?>" title="<?=$name_elem2?>"
                 onclick="return gelPropConetnt('<?=$catLink?>','<?=$link_href?><?php echo ($num>0)? '&' : '?'?><?="asc_desc=asc"?>');"><?=$name_elem2?></a><?
        }?>
    </div>
</div><?
<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bogdan
 * Date: 07.08.13
 * Time: 17:11
 * To change this template use File | Settings | File Templates.
 */
$flag = false;
if(isset($_COOKIE['compare'.$id_cat]) && !empty($_COOKIE['compare'.$id_cat])){
    $arr = explode(',',$_COOKIE['compare'.$id_cat]);
    if(in_array($id,$arr)){
        $flag = true;
    }
}
?><div class="item-compare<?if($flag){?> param-selected<?}?>">
    <div class="param-key-checkbox">
        <div class="filter-checkbox" onclick="return addCompareProp(<?=$id?>,<?=$id_cat?>);" id="compare<?=$id;?>Block"></div>
        <input type="checkbox" name="compare<?=$id;?>" value="1" id="compare<?=$id;?>Input">
    </div>
    <div class="param-key-label">
        <a href="<?=$linkCat?>compare/?prop=<?=$id;?>" class="compare-start" id="compareStart<?=$id;?>"
           onclick="return addComparePropAndShowEnd(<?=$id?>,<?=$id_cat?>);">
            <label><?=$Catalog->multi['TXT_ADD_IN_COMPARE']?></label>
        </a>
        <a href="<?=$linkCat?>compare/" class="compare-end" id="compareEnd<?=$id;?>">
            <label><?=$Catalog->multi['TXT_COMPARE_TRUE']?></label>
        </a>
    </div>
    <script type="text/javascript">
        $('#compare<?=$id;?>Input').hide();
        $('#compare<?=$id;?>Block').show();
    </script>
</div><?
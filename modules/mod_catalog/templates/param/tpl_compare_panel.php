<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bogdan
 * Date: 07.08.13
 * Time: 17:40
 * To change this template use File | Settings | File Templates.
 */
for($i=0;$i<$count;$i++){
    $id = $arr_poradok[$i];
    $name = $arr[$id];
    ?><a class="compare-panel-one-item" href="<?=$linkCat?>compare/?kill=<?=$id?>"
    onclick="return addComparePropAndShowEnd(<?=$id?>,<?=$id_cat?>)" id="comparePanelProp<?=$id?>">
        <span class="compare-kill"></span>
        <span class="compare-name"><?=$name?></span>
    </a><?
}
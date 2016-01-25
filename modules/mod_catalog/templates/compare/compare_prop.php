<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bogdan
 * Date: 08.08.13
 * Time: 17:47
 * To change this template use File | Settings | File Templates.
 */
?><table class="compare-prop">
<tr><td class="compare-prop-title"><?=$Catalog->multi['TXT_KILL_COMPARE']?></td><?
for($i=0;$i<$count;$i++){
    $cod = $arr_move[$i];
    $row = $arr_prop[$cod];
    $id_cat = $row['id_cat'];
    $name = $row['name'];
    ?><td class="compare-prop-for-id<?=$cod?>">
    <a href="javascript:addComparePropInCompare(<?=$cod?>,<?=$id_cat?>)" title="<?=$Catalog->multi['TXT_KILL_COMPARE'].' '.$name?>">
        <img src="/images/design/order-kill.png" alt="<?=$Catalog->multi['TXT_KILL_COMPARE'].' '.$name?>" title="<?=$Catalog->multi['TXT_KILL_COMPARE'].' '.$name?>">
    </a>
    </td><?
}
?></tr>
    <tr>
        <td><?=$Catalog->multi['TXT_FOTO_PROP']?></td><?
        for($i=0;$i<$count;$i++){
            $cod = $arr_move[$i];
            $row = $arr_prop[$cod];
            $origin_img = $row['origin_img'];
            $image = $row['image'];
            $name = $row['name'];
            $first_img_alt = $row['first_img_alt'];
            $first_img_title = $row['first_img_alt'];
            ?><td class="compare-prop-for-id<?=$cod?>">
            <?if(!empty($origin_img)){?><a href="<?=$origin_img?>" class="fancybox" title="<?=$name?>"><?}?>
                <img src="<?=$image?>" alt="<?=$first_img_alt?>" title="<?=$first_img_title?>">
            <?if(!empty($origin_img)){?></a><?}?>
            </td><?
        }
        ?></tr>
    <tr>
        <td><?=$Catalog->multi['TXT_PRICE']?>:</td><?
        for($i=0;$i<$count;$i++){
            $cod = $arr_move[$i];
            $row = $arr_prop[$cod];
            $price = $row['price'];
            $opt_price = $row['opt_price'];
            ?><td class="compare-prop-for-id<?=$cod?>">
                <?if(!empty($opt_price)){?><span class="compare-prop-old-price"><?=$opt_price?></span><?}?>
                <?if(!empty($price)){?><span class="compare-prop-price"><?=$price?></span><?}?>
            </td><?
        }
        ?></tr><?
    echo $compare_param;
    ?>
    <tr>
        <td><?=$Catalog->multi['TXT_BREND']?>:</td><?
        for($i=0;$i<$count;$i++){
            $cod = $arr_move[$i];
            $row = $arr_prop[$cod];
            ?><td class="compare-prop-for-id<?=$cod?>">
            <?if(!empty($row['brand_name'])){echo $row['brand_name'];}?>
            </td><?
        }
        ?></tr>
    <tr>
        <td><?=$Catalog->multi['FLD_ART_NUM']?>:</td><?
        for($i=0;$i<$count;$i++){
            $cod = $arr_move[$i];
            $row = $arr_prop[$cod];
            ?><td class="compare-prop-for-id<?=$cod?>">
            <?if(!empty($row['art_num'])){echo $row['art_num'];}?>
            </td><?
        }
        ?></tr>
    <tr>
        <td><?=$Catalog->multi['_TXT_META_DESCRIPTION']?>:</td><?
        for($i=0;$i<$count;$i++){
            $cod = $arr_move[$i];
            $row = $arr_prop[$cod];
            $name = $row['name'];
            $name_special = $row['name_special'];
            $link = $row['link'];
            $full = $row['full'];
            ?><td class="compare-prop-for-id<?=$cod?>">
                <div class="compare-prop-name">
                    <a href="<?=$link?>" title="<?=$name_special?>"><?=$name?></a>
                </div>
                <div class="compare-prop-full"><?=$full?></div>
            </td><?
        }
        ?></tr><?/*?>
    <tr>
        <td></td><?
        for($i=0;$i<$count;$i++){
            $cod = $arr_move[$i];
            $row = $arr_prop[$cod];
            ?><td class="compare-prop-for-id<?=$cod?>">
            <?=View::factory('/modules/mod_catalog/templates/tpl_catalog_buy_key.php')
                ->bind('Catalog', $Catalog)
                ->bind('id',$cod)
                ->bind('prop',$row);?>
            </td><?
        }
        ?></tr><?*/?>
</table><?
<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bogdan
 * Date: 19.06.13
 * Time: 10:42
 * To change this template use File | Settings | File Templates.
 */
?><div class="image-block-big">
    <ul><?
        for ($j = 0; $j < $items_count; $j++) {
            $alt = $items[$items_keys[$j]]['name'][$lang_id]; // Заголовок
            $title = $items[$items_keys[$j]]['text'][$lang_id]; // Описание
            $path = $items[$items_keys[$j]]['path2']; // Путь уменьшенной копии
            $path_org = $items[$items_keys[$j]]['path_original']; // Путь оригинального изображения
            ?>
            <li>
            <div>
                <a href="<?=$path_org;?>" class="fancybox" title="<?=$title;?>" rel="gal">
                    <json>
                        <path-small><?=$path;?></path-small>
                        <alt><?=$alt;?></alt>
                        <title><?=$title;?></title>
                    </json>
                </a>
            </div>
            </li><?
        }
        ?></ul>
</div>
<div class="image-block">
    <div class="image-block-key image-block-left-key" id="prev2"></div>
    <div class="image-block-key image-block-right-key" id="next2"></div>
    <div class="image-block-small-okno">
        <ul id="carouselLeft" class="image-block-big-okno" style="width: <?=(74*$items_count)?>px"><?
            for ($j = 0; $j < $items_count; $j++) {
                $alt = $items[$items_keys[$j]]['name'][$lang_id]; // Заголовок
                $title = $items[$items_keys[$j]]['text'][$lang_id]; // Описание
                $path = $items[$items_keys[$j]]['path']; // Путь уменьшенной копии
                ?>
                <li>
                <span>
                        <a href="javascript:sellImg(<?=$j?>)">
                            <img src="<?=$path;?>" alt="<?=$alt?>" title="<?=$title;?>">
                        </a>
                    </span>
                </li><?
            }
            ?></ul>
    </div>
</div><?
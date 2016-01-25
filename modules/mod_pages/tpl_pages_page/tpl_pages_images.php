<div class="leftBlockHead"><?=$multi['_TXT_FILES_TO_PAGE']?>:</div>




<div class="leftBlockHead"><?= $multi['SYS_IMAGE_GALLERY'];?></div>
<div class="image-block " align="center">
    <ul id="carouselLeft" class="vhidden jcarousel-skin-menu"><?
        for ($j = 0; $j < $items_count; $j++) {
            $alt = $items[$items_keys[$j]]['name'][$lang_id]; // Заголовок
            $title = $items[$items_keys[$j]]['text'][$lang_id]; // Описание
            $path = $items[$items_keys[$j]]['path']; // Путь уменьшенной копии
            $path_org = $items[$items_keys[$j]]['path_original']; // Путь оригинального изображения
            ?>
            <li>
                <a href="<?=$path_org;?>" class="highslide" onclick="return hs.expand(this);">
                    <img src="<?=$path;?>" alt="<?=$alt?>" title="<?=$title;?>">
                </a>

                <div class="highslide-caption"><?=$title;?></div>
            </li><?
        }
        ?></ul>
</div>
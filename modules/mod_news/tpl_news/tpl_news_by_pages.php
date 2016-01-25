<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bogdan
 * Date: 18.06.13
 * Time: 14:49
 * To change this template use File | Settings | File Templates.
 */
?><div><?
for($i=0;$i<$rows;$i++){

    $row = $arr[$i];
    $name = $row['name'];
    $link = $row['link'];
    $img = $row['src'];
    if( !empty($row['src']) ){
        $img = '<img src="'.$img.'" alt="'.$row['img_alt'].'" title="'.$row['img_title'].'" />';
    }

    ?><div class="news-last">
        <?if( isset($img) & !empty($img)): ?>
            <div class="news-last-img">
                <?if( isset($img) ):?>
                    <?if( !empty($link) ):?>
                        <a href="<?=$link?>" title="<?=$name?>">
                    <?endif;?>
                    <?=$img?>
                    <?if( !empty($link) ):?>
                        </a>
                    <?endif;?>
                <?endif;?>


            </div>

        <?endif;?>

        <div class="short-content-page">
            <?if(!empty($row['date'])){?><div class="news-date"><?=$row['date']?></div><?}?>
            <?if( !empty($link) ):?>
                <a href="<?=$link?>" title="<?=$name?>"><?=$name?></a>

            <?endif;?>


            <div class="news-last-short">
                <?=$row['short']?>
            </div>

        </div>
    </div><?
}
?></div><?
echo $pages;
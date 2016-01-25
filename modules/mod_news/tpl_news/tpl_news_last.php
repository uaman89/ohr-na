<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bogdan
 * Date: 20.06.13
 * Time: 13:19
 * To change this template use File | Settings | File Templates.
 */
?>
<div class="news-last">
    <div class="h1main"><div class="line2"></div><span><?=$title;?></span></div>
    <?
    for( $i=0; $i<$rows; $i++ )
    {
        $row = $arr[$i];
        $name = $row['name'];
        $link = $row['link'];
        ?>
        <div class="news-last-item">

            <?if(!empty($row['img'])){?>
                <div class="news-last-img">
                    <a href="<?=$link;?>" title="<?=$name?>">
                        <img src="<?=$row['src'];?>" alt="<?=$row['img_alt']?>"  title="<?=$row['img_title']?>"/>
                    </a>
                </div>
            <?}?>
            <div class="short-content">
                <?if(!empty($row['date'])){?><div class="news-date"><?=$row['date'];?></div><?}?>
                <div class="news-last-name">
                    <a href="<?=$link;?>" title="<?=$name?>"><?=$name?></a>
                </div>
            </div>
        </div>
    <?
    }
    ?>
</div>
<?
<?php

?>
    <div class="h1main"><div class="line2"></div><span><?=$title;?></span></div>
    <div class="newsLast">
<?
for( $i=0; $i<$rows; $i++ )
{
    $row = $arr[$i];
    $name = $row['name'];
    $link = $row['link'];
    $short = $row['short'];
    ?>
    <div class="news-last">

        <?if(!empty($row['img'])){?>
            <div class="news-last-img">
                <a href="<?=$link;?>" title="<?=$name?>">
                    <img src="<?=$row['src'];?>" alt="<?=$row['img_alt']?>"  title="<?=$row['img_title']?>"/>
                </a>
            </div>
        <?}?>
        <div class="short-content-page">
            <?if(!empty($row['date'])){?><div class="news-date"><?=$row['date'];?></div><?}?>
            <div class="news-last-name">
                <a href="<?=$link;?>" title="<?=$name?>"><?=$name?></a>
            </div>
            <div class="news-last-short">
                <?=$short?>
            </div>
        </div>
    </div>
<?
}
?>

    </div>
<?
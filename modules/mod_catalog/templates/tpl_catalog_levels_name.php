<?if($rows>0):?>
<div class="listCategoryItemShort">
<?for($i=0; $i<$rows; $i++):?>
    <div class="item float-to-left <?=$width;?>">
    <a href="<?=$cat_data[$i]['href'];?>" title="<?=addslashes($cat_data[$i]['name']);?>"><?=$cat_data[$i]['name'];?></a>
</div>
<?endfor;?>
</div>
<?endif;?>
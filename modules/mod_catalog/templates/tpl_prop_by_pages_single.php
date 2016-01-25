<div class="listProdItem">

    <? if(!empty($prop['image'])): ?>
        <div class="itemImg">
            <a href="<?=$prop['link']?>" title="<?=$prop['name']?>">
                <img src="<?=$prop['image']?>" alt="" title=""/>
            </a>
        </div>
    <?endif;?>
    <div><a href="<?=$prop['link']?>" title="<?=$prop['name']?>"><?=$prop['name']?></a></div>
    <div class="price">
        <?=$prop['price']?>
    </div>

</div>
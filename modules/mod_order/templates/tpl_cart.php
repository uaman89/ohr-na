<div class="count">
    <span class="price">
        В корзине <a class="icoCart" href="<?=_LINK?>order/" title="<?=$cart_text;?>"><?=$tow;?> товаров</a>
        <br />
        на сумму
    </span>
    <?
    if($tow==0){
        ?><span class="price">00.00</span> <?=$curr?><?
    }
    else{
        ?><span class="price"><a class="icoCart" href="<?=_LINK?>order/" title="<?=$cart_text;?>"><?=$sum?> <?=$curr?></a></span><?
    }
    ?>
</div>
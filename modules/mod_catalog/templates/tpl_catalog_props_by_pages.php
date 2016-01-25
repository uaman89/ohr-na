<?if(!empty($Catalog->id_cat)){

}

echo $sore;?>




<div class="categoryContent">
    <?
    foreach ($props as $prop):
            //echo View::factory('/modules/mod_catalog/templates/tpl_prop_by_pages_single.php')
            //    ->bind('prop',$prop);
            $id = $prop['id'];

            ?>
            <div class="listProdItem">
                <? if(!empty($prop['image'])):
                    $top = 0;
                    ?>
                    <div class="itemImg">

                        <?php if($prop['best']):
                            ?> <div class="hit-prop" style="top:<?=$top?>px"></div><?
                            $top= $top+18;
                            ?>

                        <?endif;?>

                        <?php if($prop['new']): ?>
                            <div class="new-prop" style="top:<?=$top?>px"></div><?
                            $top= $top+18; ?>
                        <?endif;?>

                        <?php if($prop['shareprop']):?>
                            <div class="shareprop-prop" style="top:<?=$top?>px"></div>
                        <?endif;?>

                        <a href="<?=$prop['link']?>" title="<?=$prop['name']?>">
                            <img  src="<?=$prop['image']?>" alt='Фото - <?=$prop['name']?>' title='<?=$prop['name']?> от компании «Ohrana.ua»'/>
                        </a>
                    </div>
                <?endif;?>

                <div class="prod-list-body">
                <a class="prod-name" href="<?=$prop['link']?>" title="<?=$prop['name']?>"><?=$prop['name']?></a>
                    <?php if(isset($prop['manufacpar']) and !empty($prop['manufacpar']) ): ?>
                        <div class="manufac-list-prop"> <?=$prop['manufacpar']?></div>
                    <?php endif;?>
                    <div class="price-btn">
                        <?if( !empty($prop['group_price']) && $prop['exist'] ==1 && $prop['group_price']!=0){?>
                            <div class="price-prod">
                                <span> <?=$prop['group_price']?></span>
                            </div>
                            <?php if( isset($prop['retailprice']) ) :?>
                            <div class="retail-price-list">
                                Розница: <?=$prop['retailprice'];?>
                            </div>
                            <?php endif;?>

                            <?php if($group_user_id==5 && $prop['shareprop']==1 && isset($prop['oldprice']) && !empty($prop['oldprice']) ) :?>
                                <div class="old-share-price"><?=$Catalog->Currency->ShowPrice($prop['oldprice']);?></div>
                            <?php endif;?>
                            <div class="rating-small-block">
                                <?
                                if ( $prop['rating'] > 0 ){
                                    echo '<ul class="stars">';
                                    for ($j = 1; $j < 6; $j++) {
                                        if ($j == $prop['rating']) echo '<li class="on"></li>';
                                        else echo '<li></li>';
                                    }
                                    echo '</ul><br/>';
                                }
                                if ( $prop['resp_count'] > 0 ): ?>
                                    <a href="<?=$prop['link']?>?otzivy">Отзывы (<?=$prop['resp_count']?>)</a>
                                <?endif;?>
                            </div>
                            <?
                            if( !isset($prop['listid']) or empty($prop['listid']) ): ?>

                            <form action="#" method="post" name="catalog" id="catalog<?=$id;?>">

                                <input type="hidden" size="2" value="1" class="quantity" onkeypress="return me()" id="productId[<?=$id;?>]" name="productId[<?=$id;?>]" maxlength="2"/>

                                <div class="buybutton">

                                    <a href="#" id="multiAdds<?=$id;?>" onclick="addToCart('catalog<?=$id;?>', 'cart', '<?=$id;?>');return false;">
                                        <span><?=$Catalog->multi['TXT_BUY'];?></span>
                                    </a>
                                    <div id="al<?=$id;?>"></div>
                                </div>
                            </form>
                            <?php else:

                                $listid = implode("," , $prop['listid']);


                                $countList = implode(",", array_keys($prop['listid']) ) ;
                                ?>
                                <form action="#" method="post" name="catalog" id="catalog<?=$id;?>">

                                    <input type="hidden" size="2" value="1" class="quantity" onkeypress="return me()" id="productId[<?=$id;?>]" name="productId[<?=$id;?>]" maxlength="2"/>
                                    <div class="buybutton">
                                        <a href="#" id="multiAdds<?=$id;?>" onclick="addToCartSet('catalog<?=$id;?>', 'cart', '<?=$id;?>', '<?=$countList?>', '<?=$listid?>');return false;">
                                            <span><?=$Catalog->multi['TXT_BUY'];?></span>
                                        </a>
                                        <div id="al<?=$id;?>"></div>
                                    </div>
                                </form>
                            <?php endif;?>
                       <?}
                        else echo "нет в наличии";?>
                    </div>
                <div class="short-prod">
                    <?=$prop['short']?>
                </div>
                </div>


            </div>
            <?
    endforeach; ?>

	
	
</div>



<?if(!empty($pagination)):?>
<div class="links">
    <?=$pagination?>
</div>
<?endif;?>
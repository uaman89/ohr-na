<?php
?><div class="menu-filter">
    <?php if( is_array($dataMenu) ): ?>
    <div class="sub_menu">
<?

    foreach($dataMenu as $k=>$v) :
        if ( $_SERVER["SERVER_NAME"] == "ohrana.seotm.biz")
            $v['short'] = str_replace("ohrana.ua", "ohrana.seotm.biz", $v['short']);
        ?>
        <div class="item_sub_menu">
            <a href="<?=$v['short']?>">
            <?php if( !empty($v['img']) ):?>
                <span>
               <img src="/images/spr/mod_menu/3/<?=$v['img']?>" alt="<?=$v['name']?>">
                </span>
            <?php endif;?>
               <span class="menu_text"><?=$v['name']?></span>
            </a>
        </div>

        <?
    endforeach;
?>
    </div>
    <?php endif;?>

        <?php if( is_array($dataMenuText) ):?>
            <div class="sub_menu_text">
                <?php echo $dataMenuText['descr']?>
            </div>
        <?php endif;?>

    </div><?
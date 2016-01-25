<?=$content?>

<? if(!empty($sitemap)){?>
        <?=$sitemap;?>
<? }else{?>

    <? if(!empty($sublevels)): ?>
        <div class="pages-sublevels">
            <?=$sublevels?>
        </div>
    <? endif?>

    <? if(!empty($images)):?>
        <div class="pages-gallery-box">
            <?=$images?>
        </div>
    <? endif; ?>

    <? if(!empty($files)):?>
        <div class="pages-files-box">
            <?=$files?>
        </div>
    <? endif; ?>

    <? if(!empty($tags)):?>
        <div class="pages-files-box">
            <?=$multi['TXT_THEMATIC_LINKS']?>:
            <?=$tags?>
        </div>
    <? endif; ?>

    <? if(!empty($comments)):?>
            <?=$comments?>
    <? endif;
   }
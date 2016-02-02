<?php
/**
 * @uses: /include/classes/PageUser.class.php
 */
if(!is_ajax){?>
<div class="wrapper-body">
<?if( !isset($PageUser->left) ):?>
<div class="body-right">
    <div class="catalog-filter" id="showFilterRes">
        <div class="hide-elem" id="showFilterReload"></div>
        <?php if (empty($PageUser->Catalog->lable)):?>
        <div id="showFilterHtml" class="show-filter-html"><?
            $PageUser->Catalog->showFilter();
            ?></div>
        <?php endif;?>
            </div><?
//$PageUser->Logon->LoginForm();
    ?><div class="new-prod-inform"><?
    ?><div class="new-prod-inform-title"><?
        echo $PageUser->Catalog->multi['_TXT_NEW_INFORM_TITLE_'];
    ?></div><?
    echo $PageUser->Catalog->BestProducts(3, 1, true);
        ?></div><?
    $PageUser->News->showNewsLastColumn(3);



    ?>
</div>
<?endif;?>
<div class="body-center <? echo ( isset($PageUser->left) ) ? "width100":"" ?>">


<div itemscope="" itemtype="http://schema.org/Product">
    <div class="hide-elem" id="reloadOrder"></div>

    <?}?>

<?if(!is_ajax){?>
       <? if(!empty($breadcrumb)):?>
            <div class="path"><?= $breadcrumb; ?></div>
        <?php endif; ?>
<?}?>
<?if(!is_ajax){?>
    <?
    if(!empty($h1)):?>

        <div class="h1main"><div class="line2"></div><span itemprop="name"><h1><?php echo $h1; ?></h1></span></div>
    <?php endif;?>
<?}?>
			
<?if(!is_ajax){?>
    <div id="my_d_basket" class="my_d_basket">
    <?}?>
        <div class="<?=$showContent2Box?>">



            <?php
            echo $content;

            ?>
        </div>
<?if(!is_ajax){?>
    </div>

	
<?php if ($_SERVER['REQUEST_URI'] != '/order/'): ?>
<!--Social Buttons-->

			<div id="socialbar" style="margin-top:10px;">
			
			<div class="fb">
			<div id="fb-root"></div>
			<script>(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s); js.id = id;
			js.src = "//connect.facebook.net/ru_RU/all.js#xfbml=1";
			fjs.parentNode.insertBefore(js, fjs);}
			(document, 'script', 'facebook-jssdk'));
			</script>
			<div class="fb-like" data-href="" data-width="450" data-layout="button_count" data-show-faces="false" data-send="false"></div>
			</div>
			
			
			<div class="vk">
			<!-- Put this script tag to the <head> of your page -->
			<script type="text/javascript" src="http://vk.com/js/api/share.js?86" charset="windows-1251"></script>

			<!-- Put this script tag to the place, where the Share button will be -->
			<script type="text/javascript"><!--
			document.write(VK.Share.button(false,{type: "round", text: "Поделиться"}));
			--></script>
			</div>
			
	
			
			<div class="tweet">
			<a href="https://twitter.com/share" class="twitter-share-button" data-lang="ru">Твитнуть</a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
			</div>
			
			<div class="gplus">
<!-- Поместите этот тег туда, где должна отображаться кнопка +1. -->
<div class="g-plusone" data-size="medium" data-annotation="inline" data-width="300"></div>

<!-- Поместите этот тег за последним тегом виджета кнопка +1. -->
<script type="text/javascript">
  window.___gcfg = {lang: 'ru'};

  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/platform.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>
			</div>
			
			
			</div>
			
<!--Social Buttons-->	
<?php endif;?>	
	
	
	</div>
</div>	
	
</div>
<?}

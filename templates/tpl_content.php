<?php
/**
 * @uses: /include/classes/PageUser.class.php
 */
if(!is_ajax){
?>
<div class="wrapper-body">
    <? if (!isset($PageUser->left)): ?>
    <div class="body-right">
        <?php if (isset($PageUser->Catalog->leftProps)): ?>
            <div class="new-prod-inform">
                <div class="new-prod-inform-title">Похожие товары</div>
                <?= $PageUser->Catalog->leftProps ?>
            </div>
        <?php endif; ?>
        <?php if (empty($PageUser->Catalog->lable) /*&& !isset($PageUser->Catalog->leftProps)*/): ?>
            <div class="catalog-filter" id="showFilterRes">
                <div class="hide-elem" id="showFilterReload"></div>

                <div id="showFilterHtml" class="show-filter-html"><?
                    $PageUser->Catalog->showFilter();
                    ?></div>
            </div>
        <?php endif; ?>

        <?php if (isset($PageUser->Catalog->relPropsForCat)){ //если есть, заменяет блок новинки?>
            <div class="new-prod-inform">
                <div class="new-prod-inform-title">Похожие товары</div>
                <style>
                    .props-tab-left { height: 1033px; } <? /* <-- костиль*/ ?>
                </style>
                <?= $PageUser->Catalog->relPropsForCat ?>
            </div><?
        }
        else{
            //вертикальній слайдер новинки:
            //$PageUser->Logon->LoginForm();
            if (empty($PageUser->Catalog->id)) {
                ?>
                <div class="new-prod-inform"><?
                ?>
                <div class="new-prod-inform-title"><?
                echo $PageUser->Catalog->multi['_TXT_NEW_INFORM_TITLE_'];
                ?></div>
                <div class="props-tab-left" style="height: 1033px;">
                    <div id="new-prod-slider">
                        <? echo $PageUser->Catalog->BestProducts(50, 1); ?>
                    </div>
                    <div class="prop-prev-left" id="hit-prev-left" style="display: block;"></div>
                    <div class="prop-next-left" id="hit-next-left" style="display: block;"></div>
                </div>
                <script>
                    $(document).ready(function(){
                        $("#new-prod-slider").carouFredSel({
                            direction: "up",
                            align: "top",
                            height: 1221,
                            items : 3,
                            auto: {
                                play: true,
                                timeoutDuration: 10000
                            },
                            prev : {
                                button: "#hit-prev-left",
                                key: "up"
                            },
                            next : {
                                button: "#hit-next-left",
                                key: "down"
                            },
                            scroll: {
                                pauseOnHover: true
                            }

                        });

                    });
                </script>
                </div><?
            }
        }

        //новости:
        $PageUser->News->showNewsLastColumn(3);
        ?>

        </div>
    <?endif;?>
    <div class="body-center <? echo ( isset($PageUser->left) ) ? "width100":"" ?>">

    <div class="hide-elem" id="reloadOrder"></div>

    <? if ( isset($PageUser->Catalog->id) ): ?> <div itemscope="" itemtype="http://schema.org/Product"> <? endif; ?>
<?}?>

<?if(!is_ajax){?>
    <? if(!empty($breadcrumb)):?>
        <div class="path breadcrumb" itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"><?= $breadcrumb; ?></div>
    <?php endif; ?>
<?}?>
<?if(!is_ajax){?>
    <?
    if(!empty($h1)):?>

        <div class="h1main"><div class="line2"></div>
            <? if ( isset($PageUser->Catalog->id) ): ?> <span itemprop="name"> <? endif; ?>
                <h1><?php echo $h1; ?></h1>
            <? if ( isset($PageUser->Catalog->id) ): ?> </span> <? endif; ?>
        </div>
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

    </div>
<?if(!is_ajax){?>
    <? if ( isset($PageUser->Catalog->id) ): ?> </div> <? endif; //for <div itemtype="http://schema.org/Product">?>
    <?php if ($_SERVER['REQUEST_URI']!== '/order/' && $_SERVER['REQUEST_URI']!== '/myaccount/'): ?>
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

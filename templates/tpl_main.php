<?
/**
 * @uses: /include/classes/PageUser.class.php
 */

?>

<?php 
$lowerURI=strtolower($_SERVER['REQUEST_URI']);
if($_SERVER['REQUEST_URI']!=$lowerURI)
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: http://" . $_SERVER['HTTP_HOST'] . $lowerURI);
exit();
}
 ?>
 
<!DOCTYPE html>
<html lang="<?=$PageUser->LangShortName;?>">
<head>
    <?
    if (!isset($PageUser->Catalog->isContent))
        $PageUser->Catalog->isContent = $PageUser->Catalog->IsContent($PageUser->Catalog->id_cat);

    if( ($PageUser->FrontendPages->page==PAGE_CATALOG && $PageUser->Catalog->isContent>0)
        || !empty($PageUser->TagsLayout->id_tag) )
    {
        $PageUser->Catalog->checHash();
    }
    ?>
    <meta charset="<?=$PageUser->page_encode;?>" />
    <title><?=htmlspecialchars($PageUser->title);?></title>
    <meta name="Description" content="<? if( $PageUser->Description ) echo htmlspecialchars($PageUser->Description);else echo '';?>" />
    <meta name="Keywords" content="<? if( $PageUser->Keywords ) echo htmlspecialchars($PageUser->Keywords);else echo '';?>" />
    <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
    <?
    //echo '<br>$_SERVER["QUERY_STRING"]='.$_SERVER["QUERY_STRING"];
    //если это страница каталога с фмльтрами, то для гугла указывем дополнительные параметры
    //if( strstr($_SERVER["QUERY_STRING"], "parcod")){
    //более того, проверяем, есть ли любые дополнительные параметры в УРЛ,
    //и если есть, то будем закрыать от индексации и прописыать каноникал.
    if( strstr($_SERVER['REQUEST_URI'], '?') || strstr($_SERVER['REQUEST_URI'], 'page')){
        //закрываем от индексации страницы результатов работы фильтров каталога товаров
        ?>
        <meta name="robots" content="noindex, nofollow"/>
        <?

        if(!isset($_SERVER['REDIRECT_URL'])) {
            $link = substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'], '/')+1);
        }
        else{ $link = $_SERVER['REDIRECT_URL']; }
        $canonical = 'http://'.NAME_SERVER.$link;
        //echo '<br>$canonical='.$canonical;
        //Добавление этой ссылки и атрибута позволяет владельцам сайтов определять наборы идентичного содержания и сообщать Google:
        //"Из всех страниц с идентичным содержанием эта является наиболее полезной.
        //Установите для нее наивысший приоритет в результатах поиска."
        ?>
        <link rel="canonical" href="<?=$canonical;?>"/>
    <?
    }
    ?>

    <link rel="icon" type="image/vnd.microsoft.icon"  href="/images/design/favicon.ico" />
    <link rel="SHORTCUT ICON" href="/images/design/favicon.ico" />

    <link href="/include/css/main1.css" type="text/css" rel="stylesheet" />
    <!--[if IE ]>
    <link href="/include/css/browsers/ie.css" rel="stylesheet" type="text/css" media="screen" />
    <![endif]-->
    <!--[if lt IE 9]>
    <link href="/include/css/browsers/ie8.css" rel="stylesheet" type="text/css" media="screen" />
    <![endif]-->
    <!--[if lt IE 7]>
    <script type="text/javascript" src="/include/js/iepngfix_tilebg.js"></script>
    <![endif]-->

    <!--Include jQuery scripts-->
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <?/*<script type="text/javascript" src='http://<?=NAME_SERVER."/sys/js/jQuery/jquery.js";?>'></script>*/?>
    <script type="text/javascript" src='http://<?=NAME_SERVER."/sys/js/jQuery/jquery.form.js";?>'></script>

    <script src="/include/js/cms_lib/lib1.js" type="text/javascript"></script>

    <!-- optionally include helper plugins -->
    <script type="text/javascript" language="javascript" src="/include/js/carouFredSel/jquery.carouFredSel-6.2.1-packed.js"></script>
    <script type="text/javascript" language="javascript" src="/include/js/carouFredSel/jquery.mousewheel.min.js"></script>
    <script type="text/javascript" language="javascript" src="/include/js/carouFredSel/jquery.touchSwipe.min.js"></script>
    <script type="text/javascript" language="javascript" src="/include/js/gallery.js"></script>




    <!-- Enable HTML5 tags for old browsers -->
    <script type="text/javascript" src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>

    <!-- Старт валидации -->
    <script type="text/javascript" src="/include/js/validator/js/jquery.validationEngine.js"></script>
    <script type="text/javascript" src="/include/js/validator/js/languages/jquery.validationEngine-ru.js"></script>
    <link href="/include/js/validator/css/validationEngine.jquery.css" type="text/css" rel="stylesheet" media="screen"/>
    <!-- Конец валидации -->



    <!-- Комментарий вконтакте В тег <head> на странице Вашего сайта необходимо добавить следующий код: -->
    <script src="//vk.com/js/api/openapi.js" type="text/javascript"></script>
    <script src="/include/js/cms_lib/popup.js" type="text/javascript"></script>

    <!-- for comments -->
    <link href="/include/css/comments.css" rel="stylesheet" type="text/css" media="screen" />


    <?if (defined("MOD_ORDER") AND MOD_ORDER){?>
        <!-- для публичних обявлений -->
        <script src="/include/js/cms_lib/order.js" type="text/javascript"></script>
    <?}?>

    <!-- увеличалка -->
    <script src="/include/js/fancybox/jquery.fancybox.js" type="text/javascript"></script>
    <link href="/include/js/fancybox/jquery.fancybox.css" type="text/css" rel="stylesheet" media="screen"/>

    <script type="text/javascript" src="/include/js/highslide/highslide.js"></script>
    <link rel="stylesheet" type="text/css" href="/include/js/highslide/highslide.css" media="screen" />
    <script type="text/javascript">
        hs.graphicsDir = '/include/js/highslide/graphics/';
        hs.outlineType = 'rounded-white';
    </script>
    <script type="text/javascript">
        var _JS_LANG_ID = <?=_LANG_ID?>;
        var show_consultant = true;
    </script>

<!--    <script type="text/javascript" src="/include/js/jquery-ui-1.10.0/jquery-ui-1.10.0.custom.js"></script>-->
    <script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
    <script src="/include/js/jquery.mask.min.js"></script>
    <link href="/include/js/jquery-ui-1.10.0/jquery-ui-1.10.0.custom.css" type="text/css" rel="stylesheet" media="screen"/>
    <script src="/include/js/compare.js" type="text/javascript"></script>
    <script src="/include/js/filter.js" type="text/javascript"></script>
    <script src="/include/js/clamp.js/clamp.min.js" type="text/javascript"></script>
    <?
    //Insert codes from Site Settings
    //print_r($PageUser->Settings);
    echo $PageUser->Settings['site_codes_head']."\n";
    ?>
</head>

<body>

    <?  /* for tests
    //var_dump($_COOKIE['SEOCMSSES']);
    //var_dump($_COOKIE);
        $sess = '';
        if ( isset($_SESSION['session_id']) )
            $sess = $_SESSION['session_id'];
        $cook = '';
        if (isset($_COOKIE['SEOCMSSES']))
            $cook = $_COOKIE['SEOCMSSES'];
        echo '
            <script>
                console.log("session: '.$sess.'\n");
                console.log("coookie: '.$cook.'\n\n");
            </script>';
    */?>

<!--[if lt IE 9]>
<div style=" margin:10px auto 0px auto; padding:20px; background:#DDDDDD; border:1px solid gray; width:980px; font-size:14px;">
    Уважаемый Пользователь!</br>
    Вы используете <span class="red">устаревший WEB-браузер</span>.</br>
    Предлагаем Вам установить и использовать последние версии WEB-браузеров, например:<br/>
    <ul>
        <li>Google Chrome <a href="https://www.google.com/chrome">https://www.google.com/chrome</a></li>
        <li>Mozilla Firefox <a href="http://www.mozilla.org/ru/firefox/new/">http://www.mozilla.org/ru/firefox/new/</a></li>
        <li>Opera <a href="http://www.opera.com/download/">http://www.opera.com/download/</a></li>
    </ul>
    Последние версии WEB-браузеров доступны для установки на сайтах разработчиков и содержат улучшенные свойства безопасности, повышенную скорость работы, меньшее количество ошибок. Эти простые действия помогут Вам максимально использовать функциональность сайта, избежать ошибок в работе, повысить уровень безопасности.
</div>
<script>
    function hideAllError(str) {return true;}
    window.onerror = hideAllError;
    show_consultant = false;
</script>
<![endif]-->


<div class="wrapper-top-menu">
    <div class="top-menu">
        <?$PageUser->FrontendPages->ShowHorisontalMenu();?>

        <div class="user-menu">
            <?php if( !isset($PageUser->Logon->user_id) ):?>
                    <a id="enterUser"><?=$PageUser->FrontendPages->multi['_TXT_ENTER_LINK_']?></a>
                    <a id="regUser"><?=$PageUser->FrontendPages->multi['_FLD_REGISTRATION']?></a>


            <?php
                else:
            ?>
                    <a href="/myaccount" id="enterCabinet"><?=$PageUser->FrontendPages->multi['_TXT_ENTER_CABINET_']?></a>
                    <a href="/logout.html" id="exit"><?=$PageUser->FrontendPages->multi['TXT_LOGOUT']?></a>


            <?php
                endif;
            ?>

        </div>

    </div>
</div>
<div class="middle-header">
    <div class="logo">
        <a href="/"><?=$PageUser->FrontendPages->getSpecContentByCod(1, '<img>');?></a>
    </div>
    <div class="phone-search">
        <?=$PageUser->FrontendPages->getSpecContentByCod(2, '<div>');?>
        <?=$PageUser->Search->formSearchSmall();?>
    </div>

    <div class="cart" id="cart" onclick="location.href='/order/'">
        <?=$PageUser->Order->Cart();?>
    </div>

</div>
<div class="wrapper-menu">
        <nav>
        <?=$PageUser->Catalog->ShowMainCategories();?>
        </nav>
</div>

   <?=$contentHtml?>
</div></div></div></div>
<footer>

<div class="wrapper-footer">
    <div class="btm-block work-time">
        <div class="btm-header">
            <?=$PageUser->FrontendPages->treeSpecContent[3]['name'];?>
        </div>
        <div class="btm-block-body">
            <?=$PageUser->FrontendPages->getSpecContentByCod(3, '<p>');?>
        </div>

    </div>

    <div class="btm-block buyer">
        <div class="btm-header">
            <?=$PageUser->FrontendPages->multi['_TXT_BUYER_'];?>
        </div>
        <?php $PageUser->FrontendPages->ShowHorisontalMenu(100);?>

    </div>

    <div class="btm-block helpful">
        <div class="btm-header"> <?=$PageUser->FrontendPages->multi['_TXT_HELPFUL_'];?></div>
        <ul>            <li>
                <a href="/news/" class="">Новости</a>
            </li>
            <li>
                <a href="/catalog/share/" class="">Акции</a>
            </li>
            <li>
                <a href="/catalog/novinki/" class="">Новинки</a>
            </li>
        </ul>

     </div>

    <div class="copyright">
        &copy Все права защищены <a href="https://plus.google.com/103011928691980591170" rel="publisher">ohrana.ua</a>
        Разработка сайта <a href="http://seotm.ua" rel="nofollow">SEOTM.UA</a>
        <br/>
        Продвижение сайта <noindex><a target="_blank" href="http://seo.abweb.com.ua/" title="Продвижение сайта Abweb" rel="nofollow">ABWEB</a></noindex>
	 </div>
</div>

</footer>

<?
//Insert codes from Site Settings
echo $PageUser->Settings['site_codes_body']."\n";
?>

</body>
</html>

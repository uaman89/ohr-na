<?php                                     /* news_settings.backend.php */
// ================================================================================================
// System : SEOCMS
// Module : news_settings.backend.php
// Version : 1.0.0
// Date : 23.05.2007
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : script for all actions with settings for News on the back-end
//
// ================================================================================================
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( $_SERVER['DOCUMENT_ROOT'].'/modules/mod_news/news.defines.php' );

if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];

//============================================================================================
// START
// Blocking to execute a script from outside (not Admin-part)
//============================================================================================
$goto = "http://".NAME_SERVER."/admin/index.php?logout=1";
//echo '<br>$goto='.$goto;
if ( !isset($_SESSION[ 'session_id']) OR empty($_SESSION[ 'session_id']) OR empty( $module ) ) {
     //$Msg->show_msg( '_NOT_AUTH' );
     //return false;
     ?><script>window.location.href="<?=$goto?>";</script><?;
}

$logon = check_init("Authorization","Authorization");
if (!$logon->LoginCheck()) {
    //return false;
    ?><script>window.location.href="<?=$goto?>";</script><?;
}
//=============================================================================================
// END
//=============================================================================================
$News = check_init('News_settings', 'News_settings', "'".$logon->user_id."', '".$module."'");

if( !isset($_REQUEST['task']) || empty($_REQUEST['task']) ) $News->task='show';
else $News->task=$_REQUEST['task'];

if(isset($_REQUEST['img'])) $News->img = 1;
else $News->img = 0;

if(isset($_REQUEST['short_descr'])) $News->short_descr = 1;
else $News->short_descr = 0;

if(isset($_REQUEST['full_descr'])) $News->full_descr = 1;
else $News->full_descr = 0;

if(isset($_REQUEST['relat_prod'])) $News->relat_prod = 1;
else $News->relat_prod = 0;

if(isset($_REQUEST['subscr'])) $News->subscr = 1;
else $News->subscr = 0;

if(isset($_REQUEST['dt'])) $News->dt =1;
else $News->dt = 0;

if(isset($_REQUEST['rss_import'])) $News->rss_import =1;
else $News->rss_import = 0;

if(isset($_REQUEST['top_news'])) $News->top_news =1;
else $News->top_news = 0;

if(isset($_REQUEST['newsline'])) $News->newsline =1;
else $News->newsline = 0;

if(isset($_REQUEST['title'])) $News->title = $_REQUEST['title'];
else $News->title = NULL;

if(isset($_REQUEST['description']))  $News->description = $_REQUEST['description'];
else $News->description = NULL;

if(isset($_REQUEST['keywords'])) $News->keywords = $_REQUEST['keywords'];
else $News->keywords= NULL;

if(isset($_REQUEST['rss_id'])) $News->rss_id = $_REQUEST['rss_id'];
else $News->rss_id = NULL;

if(isset($_REQUEST['rss_path'])) $News->rss_path = $_REQUEST['rss_path'];
else $News->rss_path = NULL;

if(isset($_REQUEST['rss_descr'])) $News->rss_descr = $_REQUEST['rss_descr'];
else $News->rss_descr = NULL;

if($News->task=='save'){
        if ( $News->SaveSettings() ) echo '<b style="color: green;font-size: 20px">save ok!</b><br/>';
        else echo '<b style="color: red;font-size: 20px">not save!</b><br/>';
}
$News->ShowSettings();
?>

<?
// ================================================================================================
//    System     : CMS
//    Module     : News
//    Date       : 01.03.2011
//    Licensed To: Yaroslav Gyryn
//    Purpose    : Defines News
// ================================================================================================
include_once( SITE_PATH.'/include/defines.php' );
include_once( SITE_PATH.'/modules/mod_news/news.class.php' );
include_once( SITE_PATH.'/modules/mod_news/newsLayout.class.php' );
include_once( SITE_PATH.'/modules/mod_news/newsCtrl.class.php' );
include_once( SITE_PATH.'/modules/mod_news/news_settings.class.php' );

define("MOD_NEWS", true);

define("TblModNews","mod_news");
define("TblModNewsCat","mod_news_spr_category");   // Спільний загальний довідник для всіх модулів
define("TblModNewsTop","mod_news_top_txt");           // Топ новина
define("TblModNewsSprMain","mod_news_spr_main");   // Головне в новині

define("TblModNewsNames","mod_news_names");
define("TblModNewsShort","mod_news_short");
define("TblModNewsFull","mod_news_full");
define("TblModNewsRelatProd","mod_news_relat_prod");

// --------------- defines for news subscribe  ---------------
define("TblModNewsSubscr","mod_news_subscribers");
define("TblModNewsSubscrCat","mod_news_subscribe_cat");
define("TblModNewsDispatch","mod_news_dispatch");
define("TblModNewsDispatchSet","mod_news_dispatch_set");

// --------------- defines for news settings  ---------------
define("TblModNewsSet","mod_news_set");
define("TblModNewsSetSprTitle","mod_news_set_spr_title");
define("TblModNewsSetSprDescription","mod_news_set_spr_description");
define("TblModNewsSetSprKeywords","mod_news_set_spr_keywords");

// --------------------------  defines for RSS chanels ---------------------
define("TblModNewsRss","mod_news_rss");
define("TblModNewsRssSprDescr","mod_news_rss_decription");
?>
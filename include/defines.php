<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
//ini_set("memory_limit","32M");
date_default_timezone_set('Europe/Kiev');

if (!defined("SITE_PATH"))          define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
if (!defined("NAME_SERVER"))        define( "NAME_SERVER", $_SERVER['SERVER_NAME'] );

if (!defined("SEOCMS_DEBUGNAME"))   define( "SEOCMS_DEBUGNAME", "SEOCMS_make_debug" );

if (!defined("MAKE_DEBUG")){
    if( isset($_REQUEST['make_debug']) ){
        define( "MAKE_DEBUG", intval($_REQUEST['make_debug']) );
    }
    elseif( isset($_COOKIE[SEOCMS_DEBUGNAME]) ){
        define( "MAKE_DEBUG", $_COOKIE[SEOCMS_DEBUGNAME] );
    }
    else define( "MAKE_DEBUG", "1" );
}

define('USE_CACHE', false);

if(!defined("CATALOG_TRASLIT"))         define( "CATALOG_TRASLIT", "1" );//Каталог без /catalog/

if(!defined("DEBUG_LANG"))         define( "DEBUG_LANG", "3" );
if(!defined("USE_TAGS"))           define( "USE_TAGS", "0" );
if(!defined("USE_COMMENTS"))       define( "USE_COMMENTS", "1" );
if(!defined("DEBUG_CURR"))         define( "DEBUG_CURR", "1" ); // debug currency = 1 (USD)
if(!defined("DISCOUNT"))           define( "DISCOUNT", "0.5" ); // default user discount
if(!defined("META_TITLE"))         define( "META_TITLE", "Ohrana.ua" );
if(!defined("META_DESCRIPTION"))   define( "META_DESCRIPTION", "" );
if(!defined("META_KEYWORDS"))      define( "META_KEYWORDS", "" );

//Привязка модуля к Id динамической страницы
if(!defined("PAGE_ARTICLE"))       define( "PAGE_ARTICLE", "73" );
if(!defined("PAGE_ASKED"))         define( "PAGE_ASKED", "79" );
if(!defined("PAGE_CATALOG"))       define( "PAGE_CATALOG", "72" );
if(!defined("PAGE_COMMENT"))       define( "PAGE_COMMENT", "80" );
if(!defined("PAGE_DEALERS"))       define( "PAGE_DEALERS", "86" );
if(!defined("PAGE_DICTIONARY"))    define( "PAGE_DICTIONARY", "78" );
if(!defined("PAGE_FEEDBACK"))      define( "PAGE_FEEDBACK", "74" );
if(!defined("PAGE_GALLERY"))       define( "PAGE_GALLERY", "76" );
if(!defined("PAGE_NEWS"))          define( "PAGE_NEWS", "63" );
if(!defined("PAGE_DEPARTMENT"))    define( "PAGE_DEPARTMENT", "81" );
if(!defined("PAGE_VIDEO"))         define( "PAGE_VIDEO", "75" );
if(!defined("PAGE_PUBLIC"))        define( "PAGE_PUBLIC", "95" );
if(!defined("PAGE_SITEMAP"))       define( "PAGE_SITEMAP", "96" );

include_once( SITE_PATH.'/admin/include/defines.inc.php' );

include_once( SITE_PATH.'/sys/classes/view.php' );
include_once( SITE_PATH.'/sys/classes/arr.php' );
include_once( SITE_PATH.'/sys/classes/date.php' );
include_once( SITE_PATH.'/sys/classes/utf8.php' );
include_once( SITE_PATH.'/sys/classes/url.php' );
include_once( SITE_PATH.'/sys/classes/html.php' );
include_once( SITE_PATH.'/sys/classes/form.php' );
include_once( SITE_PATH.'/sys/classes/image.php' );
include_once( SITE_PATH.'/sys/classes/image_driver/Image_GD.php' );
include_once( SITE_PATH.'/sys/classes/exeption.php' );


include_once( SITE_PATH.'/include/classes/PageUser.class.php' );
include_once( SITE_PATH.'/include/classes/FrontForm.class.php' );
include_once( SITE_PATH.'/include/classes/FrontSpr.class.php' );
include_once( SITE_PATH.'/include/classes/UserAuthorize.class.php' );
include_once( SITE_PATH.'/include/classes/FrontTags.class.php' );
include_once( SITE_PATH.'/include/classes/FrontComments.class.php' );

include_once( SITE_PATH.'/modules/mod_cache/cache_define.php' );



include_once( SITE_PATH.'/modules/mod_catalog/catalog.defines.php' );

include_once( SITE_PATH.'/modules/mod_feedback/feedback.defines.php' );

include_once( SITE_PATH.'/modules/mod_news/news.defines.php' );
include_once( SITE_PATH.'/modules/mod_order/order.defines.php' );
include_once( SITE_PATH.'/modules/mod_pages/pages.defines.php' );
include_once( SITE_PATH.'/modules/mod_search/search.defines.php' );
include_once( SITE_PATH.'/modules/mod_user/user.defines.php' );

/*================================comments begin===========================*/
include_once( SITE_PATH.'/modules/mod_comments/comments.model.php' );
/*================================comments end===========================*/

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // Если к нам идёт Ajax запрос, то ловим его
    define( "is_ajax", true );
} else {
    define( "is_ajax", false );
}

?>
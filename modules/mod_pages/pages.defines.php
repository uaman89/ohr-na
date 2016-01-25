<?
// ================================================================================================
//    System       : CMS
//    Module      : Dynamic Pages control
//    Date          : 06.09.2013
//    Licensed To : Yaroslav Gyryn
//    Purpose      : Defines Pages
// ================================================================================================
include_once( SITE_PATH.'/include/defines.php' );
include_once( SITE_PATH.'/modules/mod_pages/pages.class.php' );
include_once( SITE_PATH.'/modules/mod_pages/backend/pages_backend.class.php' );
include_once( SITE_PATH.'/modules/mod_pages/pagesLayout.class.php' );
include_once( SITE_PATH.'/modules/mod_pages/backend/pages.backend.clear.class.php' );

define("MOD_PAGES", true);

define("TblModPages","mod_pages");
define("TblModPagesTxt","mod_pages_txt");
define("TblModPagesSpecContent","mod_pages_spec_content");//таблица служебного контента
define("TblModPagesSlider","mod_pages_slider"); //таблица слайдера
define("TblModClear","mod_clear");

define("Pages_Img_Path_Small","/images/mod_pages/");
define("Pages_Img_Path",SITE_PATH."/images/mod_pages/");

define("PAGES_USE_SHORT_DESCR", 1);
define("PAGES_USE_SPECIAL_POS", 1);
define("PAGES_USE_IMAGE", 1);
define("PAGES_USE_IS_MAIN", 1);
?>

<?php
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );

// ================================================================================================
// System : SEOCMS
// Module : catalog.defines.php
// Version : 1.0.0
// Date : 21.03.2006
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : All Definitions for module of Catalog
//
// ================================================================================================

include_once( SITE_PATH.'/include/defines.php' );
include_once( SITE_PATH.'/modules/mod_catalog/catalog.class.php' );
include_once( SITE_PATH.'/modules/mod_catalog/CatalogModel.php' );
include_once( SITE_PATH.'/modules/mod_catalog/catalogParamLayout.class.php');
include_once( SITE_PATH.'/modules/mod_catalog/catalogLayout.class.php' );
include_once( SITE_PATH.'/modules/mod_catalog/backend/catalog_category.class.php' );
include_once( SITE_PATH.'/modules/mod_catalog/backend/catalog_content.class.php' );
include_once( SITE_PATH.'/modules/mod_catalog/backend/catalog_settings.class.php' );
include_once( SITE_PATH.'/modules/mod_catalog/backend/catalog_params.class.php' );
include_once( SITE_PATH.'/modules/mod_catalog/backend/catalog_ImpExp.class.php' );
include_once( SITE_PATH.'/modules/mod_catalog/backend/catalog_price.class.php');
include_once( SITE_PATH.'/modules/mod_catalog/catalog_stat.class.php');
include_once( SITE_PATH.'/modules/mod_catalog/backend/catalog_stat_ctrl.class.php');
include_once( SITE_PATH.'/modules/mod_catalog/backend/catalog_stat_rep.class.php');
include_once( SITE_PATH.'/modules/mod_catalog/backend/catalog_response.class.php');
include_once( SITE_PATH.'/modules/mod_catalog/backend/catalog_colors.class.php');
include_once( SITE_PATH.'/modules/mod_catalog/backend/catalog_RelatProp.class.php');

define("MOD_CATALOG", true);
define("MOD_CATALOG_RETAIL_PRICE", 5);

define("MOD_CATALOG_PARAM",'98,99,100,101,102,103,104,105,106');

//-------------- defines for catalog categories --------------------
define("TblModCatalog","mod_catalog");
define("TblModCatalogPropCharacteristic","mod_catalog_prop_characteristic");
define("TblModPropGroupPrice","mod_catalog_group_price");
define("TblModCatalogSprName","mod_catalog_spr_name");
define("TblModCatalogSprNameInd","mod_catalog_spr_name_individual");
define("TblModCatalogSprH1","mod_catalog_spr_h1");
define("TblModCatalogSprDescr","mod_catalog_spr_descr");
define("TblModCatalogSprDescr2","mod_catalog_spr_descr2");
define("TblModCatalogSprManufac","mod_catalog_spr_manufac");
define("TblModCatalogSprGroup","mod_catalog_spr_group");
define("TblModCatalogSprTxt","mod_catalog_spr_txt");
define("TblModCatalogRelat","mod_catalog_relat");
if(!defined("COUNT_ADD_RELAT_CATEGS")) define("COUNT_ADD_RELAT_CATEGS", 5);
define("TblModCatalogTranslit","mod_catalog_translit");
define("TblModCatalogSprMTitle","mod_catalog_spr_mtitle");
define("TblModCatalogSprMDescr","mod_catalog_spr_mdescr");
define("TblModCatalogSprKeywords","mod_catalog_spr_keywords");
define("TblModCatalogSprColors","mod_catalog_spr_colors");
define("TblModCatalogSprSizes","mod_catalog_spr_sizes");

define("TblModCatalogSetProp","mod_catalog_set_prop");

//-------------- defines for catalog settings --------------------
define("TblModCatalogSet","mod_catalog_set");
define("TblModCatalogSetSprTitle","mod_catalog_set_spr_title");
define("TblModCatalogSetSprDescription","mod_catalog_set_spr_description");
define("TblModCatalogSetSprKeywords","mod_catalog_set_spr_keywords");

//-------------- defines for catalog parameters --------------------
define("TblModCatalogParams","mod_catalog_param");
define("TblModCatalogParamsSprName","mod_catalog_param_spr_name");
define("TblModCatalogParamsSprPrefix","mod_catalog_param_spr_prefix");
define("TblModCatalogParamsSprSufix","mod_catalog_param_spr_sufix");
define("TblModCatalogParamsProp","mod_catalog_param_prop");
define("TblModCatalogParamsPropImg","mod_catalog_param_prop_img");
define("TblModCatalogParamsVal","mod_catalog_param_val");
//define("TblModCatalogParamsSPR","mod_catalog_param_spr_"); // for dynamic creation of tables
define("PARAM_VAR_NAME","parcod"); // for dynamic creation and reading links to images influence on parameters of products
define("PARAM_VAR_SEPARATOR","_"); // separator between PARAM_VAR_NAME and ID of the parameters in the link
define("TblModCatalogParamsSprDescr","mod_catalog_param_spr_descr");
define("TblModCatalogParamsSprMTitle","mod_catalog_param_spr_mtitle");
define("TblModCatalogParamsSprMDescr","mod_catalog_param_spr_mdescr");
define("TblModCatalogParamsSprMKeywords","mod_catalog_param_spr_mkeywords");

//-------------- defines for catalog positions --------------------
define("TblModCatalogProp","mod_catalog_prop");
define("TblModCatalogPropSprName","mod_catalog_prop_spr_name");
define("TblModCatalogPropSprH1","mod_catalog_prop_spr_h1");
define("TblModCatalogPropSprShort","mod_catalog_prop_spr_short");
define("TblModCatalogPropSprFull","mod_catalog_prop_spr_full");
define("TblModCatalogPropSprSpecif","mod_catalog_prop_spr_specif");
define("TblModCatalogPropSprReviews","mod_catalog_prop_spr_reviews");
define("TblModCatalogPropSprSupport","mod_catalog_prop_spr_support");
define("TblModCatalogPropRelat","mod_catalog_prop_relat");
define("TblModCatalogPropRelat2","mod_catalog_prop_relat2");
define("TblModCatalogPropRelatForCateg","mod_catalog_prop_relat_for_categ");
define("TblModCatalogResponse", "mod_catalog_response");
if (!defined("COUNT_ADD_RELAT_PROP")) define("COUNT_ADD_RELAT_PROP", 5);
define("TblModCatalogPriceLevels","mod_catalog_prop_price_levels");
define("TblModCatalogPropSprMTitle","mod_catalog_prop_spr_mtitle");
define("TblModCatalogPropSprMDescr","mod_catalog_prop_spr_mdescr");
define("TblModCatalogPropSprMKeywords","mod_catalog_prop_spr_mkeywords");
define("TblModCatalogPropGroups","mod_catalog_prop_groups");
define("TblModCatalogPropMultiCategs","mod_catalog_prop_multi_categs");
define("TblModCatalogCatMultiCategs","mod_catalog_cat_multi_categs");
define("TblModCatalogPropSizes","mod_catalog_prop_sizes");
define("TblModCatalogPropColors","mod_catalog_prop_colors");
//------------ defines for Images ----------------
define("TblModCatalogPropImg","mod_catalog_prop_img");
define("TblModCatalogPropImgTxt","mod_catalog_prop_img_txt");
define("Img_Path","/images/mod_catalog_prod");
/*
if (!defined("MAX_IMAGE_WIDTH")) define("MAX_IMAGE_WIDTH","5000");
if (!defined("MAX_IMAGE_HEIGHT")) define("MAX_IMAGE_HEIGHT","5000");
if (!defined("STORE_IMAGE_WIDTH")) define("STORE_IMAGE_WIDTH","1280");
if (!defined("STORE_IMAGE_HEIGHT")) define("STORE_IMAGE_HEIGHT","1280");
if (!defined("MAX_IMAGE_SIZE")) define("MAX_IMAGE_SIZE",8182 * 1024);
if (!defined("UPLOAD_IMAGES_COUNT")) define("UPLOAD_IMAGES_COUNT", 5);
if (!defined("MAX_UPLOAD_IMAGES_COUNT")) define("MAX_UPLOAD_IMAGES_COUNT", 50);
if (!defined("WATERMARK_TEXT")) define("WATERMARK_TEXT","");
if (!defined("ADDITIONAL_FILES_TEXT")) define("ADDITIONAL_FILES_TEXT","_cmszoom_");
if (!defined("MAX_IMAGES_QUANTITY")) define("MAX_IMAGES_QUANTITY","85");
*/
//------------ defines for Files ----------------
define("TblModCatalogPropFiles","mod_catalog_prop_files");
define("TblModCatalogPropFilesTxt","mod_catalog_prop_files_txt");
define("Catalog_Upload_Files_Path","/images/mod_catalog_prod_files");
if (!defined("MAX_FILE_SIZE")) define("MAX_FILE_SIZE",100048 * 1024);
if (!defined("UPLOAD_FILES_COUNT")) define("UPLOAD_FILES_COUNT", 5);
if (!defined("MAX_UPLOAD_FILES_COUNT")) define("MAX_UPLOAD_FILES_COUNT", 20);

//------------ defines for Price-list ----------------
define("TblModCatalogFileManager","mod_file_manager");

//------------ defines for Statictic ----------------
define("TblModCatalogStatLog","mod_catalog_stat_log");
?>
<?php                              /* catalog_settings.backend.php */
/**
* catalog_settings.backend.php   
* script for all actions with Catalog Settings
* @package Catalog Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 24.02.2011
* @copyright (c) 2010+ by SEOTM
*/
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] ); 
include_once( $_SERVER['DOCUMENT_ROOT'].'/modules/mod_catalog/catalog.defines.php' );

if(!defined("_LANG_ID")) {$pg = new PageAdmin();} 

if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];

//Blocking to execute a script from outside (not Admin-part) 
if ( !$pg->logon->isAccessToScript($module)) exit;

$Catalog = new Catalog_settings($pg->logon->user_id, $module,NULL,NULL,NULL,"500");

if( !isset($_REQUEST['task']) || empty($_REQUEST['task']) ) $Catalog->task='show';
else $Catalog->task=$_REQUEST['task'];

if(isset($_REQUEST['content_func'])) $Catalog->content_func = $_REQUEST['content_func'];
else $Catalog->content_func = NULL;

if(isset($_REQUEST['params_func'])) $Catalog->params_func = $_REQUEST['params_func'];
else $Catalog->params_func = NULL;

if( !isset($_REQUEST['manufac']) ) $Catalog->manufac=0;
else $Catalog->manufac=1;

if( !isset($_REQUEST['name']) ) $Catalog->name=0;
else $Catalog->name=1;

if( !isset($_REQUEST['img']) ) $Catalog->img=0;
else $Catalog->img=1;

if( !isset($_REQUEST['files']) ) $Catalog->files=0;
else $Catalog->files=1;


if( !isset($_REQUEST['share']) ) $Catalog->share=0;
else $Catalog->share=1;

if( !isset($_REQUEST['short_descr']) ) $Catalog->short_descr=0;
else $Catalog->short_descr=1;

if( !isset($_REQUEST['full_descr']) ) $Catalog->full_descr=0;
else $Catalog->full_descr=1;

if( !isset($_REQUEST['specif']) ) $Catalog->specif=0;
else $Catalog->specif=1;

if( !isset($_REQUEST['reviews']) ) $Catalog->reviews=0;
else $Catalog->reviews=1;

if( !isset($_REQUEST['support']) ) $Catalog->support=0;
else $Catalog->support=1;

if( !isset($_REQUEST['sizes']) ) $Catalog->sizes=0;
else $Catalog->sizes=1;

if( !isset($_REQUEST['exist']) ) $Catalog->exist=0;
else $Catalog->exist=1;

if( !isset($_REQUEST['number_name']) ) $Catalog->number_name=0;
else $Catalog->number_name=1;

if( !isset($_REQUEST['price']) ) $Catalog->price=0;
else $Catalog->price=1;

if( !isset($_REQUEST['opt_price']) ) $Catalog->opt_price=0;
else $Catalog->opt_price=1;

if( !isset($_REQUEST['sizesCount']) ) $Catalog->sizesCount=0;
else $Catalog->sizesCount=1;

if( !isset($_REQUEST['grnt']) ) $Catalog->grnt=0;
else $Catalog->grnt=1;

if( !isset($_REQUEST['dt']) ) $Catalog->dt=0;
else $Catalog->dt=1;

if( !isset($_REQUEST['responses']) ) $Catalog->responses=0;
else $Catalog->responses=1;

if( !isset($_REQUEST['rating']) ) $Catalog->rating=0;
else $Catalog->rating=1;

if( !isset($_REQUEST['price_currency']) ) $Catalog->price_currency=0;
else $Catalog->price_currency=1;

if( !isset($_REQUEST['opt_price_currency']) ) $Catalog->opt_price_currency=0;
else $Catalog->opt_price_currency=1;

if( !isset($_REQUEST['price_levels']) ) $Catalog->price_levels=0;
else $Catalog->price_levels=1;

if( !isset($_REQUEST['price_levels_currency']) ) $Catalog->price_levels_currency=0;
else $Catalog->price_levels_currency=1;

if( !isset($_REQUEST['share']) ) $Catalog->share=0;
else $Catalog->share=1;

if( !isset($_REQUEST['tags']) ) $Catalog->tags=0;
else $Catalog->tags=1;

if( !isset($_REQUEST['new']) ) $Catalog->new=0;
else $Catalog->new=1;

if( !isset($_REQUEST['best']) ) $Catalog->best=0;
else $Catalog->best=1;

if( !isset($_REQUEST['shareprop']) ) $Catalog->shareprop=0;
else $Catalog->shareprop=1;

if( !isset($_REQUEST['art_num']) ) $Catalog->art_num=0;
else $Catalog->art_num=1;

if( !isset($_REQUEST['barcode']) ) $Catalog->barcode=0;
else $Catalog->barcode=1;


if(isset($_REQUEST['files_path'])) $Catalog->files_path = $_REQUEST['files_path'];
else $Catalog->files_path = Catalog_Upload_Files_Path;

if(isset($_REQUEST['img_path'])) $Catalog->img_path = $_REQUEST['img_path'];
else $Catalog->img_path = Img_Path;

if( !isset($_REQUEST['id_group']) ) $Catalog->id_group=0;
else $Catalog->id_group=1;

if(isset($_REQUEST['title'])) $Catalog->title = $_REQUEST['title'];
else $Catalog->title = NULL;

if(isset($_REQUEST['description'])) $Catalog->description = $_REQUEST['description'];
else $Catalog->description = NULL;

if(isset($_REQUEST['keywords'])) $Catalog->keywords = $_REQUEST['keywords'];
else $Catalog->keywords= NULL; 

if( !isset($_REQUEST['cod_pli']) ) $Catalog->cod_pli=0;
else $Catalog->cod_pli=1;

if( !isset($_REQUEST['relat_prop']) ) $Catalog->relat_prop=0;
else $Catalog->relat_prop=1;

if( !isset($_REQUEST['multi_categs']) ) $Catalog->multi_categs=0;
else $Catalog->multi_categs=1;

if( !isset($_REQUEST['name_quick_edit']) ) $Catalog->name_quick_edit=0;
else $Catalog->name_quick_edit=1;

if( !isset($_REQUEST['cat_name_ind']) ) $Catalog->cat_name_ind=0;
else $Catalog->cat_name_ind=1;

if( !isset($_REQUEST['cat_descr']) ) $Catalog->cat_descr=0;
else $Catalog->cat_descr=1;

if( !isset($_REQUEST['cat_descr2']) ) $Catalog->cat_descr2=0;
else $Catalog->cat_descr2=1;

if( !isset($_REQUEST['cat_img']) ) $Catalog->cat_img=0;
else $Catalog->cat_img=1;

if( !isset($_REQUEST['cat_sublevels']) ) $Catalog->cat_sublevels=0;
else $Catalog->cat_sublevels=1;

if( !isset($_REQUEST['imgColors']) ) $Catalog->imgColors=0;
else $Catalog->imgColors=1;

if( !isset($_REQUEST['cat_content']) ) $Catalog->cat_content=0;
else $Catalog->cat_content=1;

if( !isset($_REQUEST['cat_params']) ) $Catalog->cat_params=0;
else $Catalog->cat_params=1;

if( !isset($_REQUEST['cat_relat']) ) $Catalog->cat_relat=0;
else $Catalog->cat_relat=1;

if( !isset($_REQUEST['priceFromSizeColor']) ) $Catalog->priceFromSizeColor=0;
else $Catalog->priceFromSizeColor=1;

if( !isset($_REQUEST['info_prop']) ) $Catalog->info_prop=0;
else $Catalog->info_prop=1;

if( !isset($_REQUEST['info_cat']) ) $Catalog->info_cat=0;
else $Catalog->info_cat=1;


$scriptact = $_SERVER['PHP_SELF']."?module=$Catalog->module";

 switch( $Catalog->task ) {
    case 'show':      
                $Catalog->ShowSettings();
                break;
    case 'save':      
                if ( $Catalog->SaveSettings() ) echo "<script>window.location.href='$scriptact';</script>";
                break;
 }
?>

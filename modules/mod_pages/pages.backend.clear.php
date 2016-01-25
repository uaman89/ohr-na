<?
/*
    * pages.clear.backend.php
    * @package Pages Package of SEOCMS
    * @author Bogdan Iglinsky  <bi@seotm.com>
    * @version 1.0, 27.05.2013
    * @copyright (c) 2013+ by SEOTM
 */
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once(SITE_PATH.'/modules/mod_pages/pages.defines.php');

if(!defined("_LANG_ID")) {$pg = check_init("PageAdmin","PageAdmin");}

if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];
//echo '$module='.$module;
//Blocking to execute a script from outside (not Admin-part)
//if ( !$pg->logon->isAccessToScript($module)) exit;

$ClearPages = new ClearPages($pg->logon->user_id, $module);

if( !isset( $_REQUEST['name'] ) ) $ClearPages->name = NULL;
else $ClearPages->name = $_REQUEST['name'];

if( !isset( $_REQUEST['send'] ) ) $ClearPages->send = 0;
else $ClearPages->send = $_REQUEST['send'];

if(!empty($ClearPages->name)){
    if(MOD_PAGES){
        $ClearPages->clearTableOne(TblModPagesTxt,'Динамические страници','Описание динамических страниц','content','pname');
    }
    if(MOD_CATALOG){
        $ClearPages->clearTableAndTableName(TblModCatalogSprDescr,TblModCatalogSprName,'Категория каталога','Первое описание');
        if(defined("TblModCatalogSprDescr2")){
            $ClearPages->clearTableAndTableName(TblModCatalogSprDescr2,TblModCatalogSprName,'Категория каталога','Второе описание');
        }
        if(defined("TblModCatalogSprDescr3")){
            $ClearPages->clearTableAndTableName(TblModCatalogSprDescr3,TblModCatalogSprName,'Категория каталога','Третье описание');
        }
    }
    if(MOD_ARTICLE){
        $ClearPages->clearTableOne(TblModArticleTxt,'Стати','Краткое описание стати','short');
        $ClearPages->clearTableOne(TblModArticleTxt,'Стати','Полное описание стати','full');
    }
    if(MOD_ARTICLE){
        $ClearPages->clearTableOne(TblModArticleTxt,'Стати','Краткое описание стати','short');
        $ClearPages->clearTableOne(TblModArticleTxt,'Стати','Полное описание стати','full');
    }
    if(MOD_GALLERY){
        $ClearPages->clearTableOne(TblModGalleryTxt,'Фотогалерея','Краткое описание галереи','short');
    }
    if(defined(TblModSliderSprContent)){
        $ClearPages->clearTableOne(TblModSliderSprContent,'Слайдер','Описание сладера','descr');
    }
    if(MOD_NEWS){
        $ClearPages->clearTableAndTableName(TblModNewsSprShrt,TblModNewsSprSbj,'Новости','Краткое описание');
        $ClearPages->clearTableAndTableName(TblModNewsSprFull,TblModNewsSprSbj,'Новости','Полное описание');
    }
    if(MOD_COMMENTS){
        $ClearPages->clearTableOne(TblModCommentsTxt,'Коментарии пользователей','Полное описание','full');
    }
}else{
//    echo '$ClearPages->send='.$ClearPages->send;
    if($ClearPages->send==1){
        ?><div style="color: red;font-size: 20px">Заполните поле "Домен для чистки"</div><?
    }
}

$ClearPages->show();
?>
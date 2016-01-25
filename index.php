<?php
#todo history
include_once( $_SERVER['DOCUMENT_ROOT'].'/include/defines.php' );
/*var PageUser $Page */
$Page = check_init('PageUser', 'PageUser');

/*Проверка на каталог*/
if(defined("MOD_CATALOG") AND MOD_CATALOG AND CATALOG_TRASLIT){
    $res=$Page->Catalog->CheckCateg();
    if($res){
       include_once(SITE_PATH.'/modules/mod_catalog/catalog.php');
       exit();
    }
}

/*END*/
$ModulesPlug = check_init('ModulesPlug', 'ModulesPlug');
$id_module = $ModulesPlug->GetModuleIdByPath( 'mod_pages/pages.backend.php' );
if(!isset ($Page->FrontendPages))
    $Page->FrontendPages = check_init('FrontendPages', 'FrontendPages', "'$id_module'");

$Page->FrontendPages->lang_id = _LANG_ID;
$Page->FrontendPages->module = $id_module;
if( !isset( $_REQUEST['page'] ) ){
    if($Page->FrontendPages->is_main_page==1)
        $page = $Page->FrontendPages->main_page;
    else
        $page=1;
}
else $page = $_REQUEST['page'];

if( !isset ( $_REQUEST['pn'] ) ) $pn = NULL;
else $pn = $_REQUEST['pn'];

if( !isset ( $_REQUEST['q'] ) ) $q = NULL;
else $q = $_REQUEST['q'];

if( !isset ( $_REQUEST['task'] ) ) $Page->FrontendPages->task = NULL;
else $Page->FrontendPages->task = $_REQUEST['task'];

if( !isset ( $_REQUEST['preview'] ) ) $preview = NULL;
else $preview = $_REQUEST['preview'];

if( !isset ( $_REQUEST['tag'] ) ) $tag = NULL;
else $tag = urldecode($_REQUEST['tag']);

if( !empty($q) AND !strstr($q, 'index.php') ) {
    if($q=='dis_publish') $Page->FrontendPages->Disable('publish');
    if($q=='en_publish') $Page->FrontendPages->Enable('publish');
    if($q=='dis_visible') $Page->FrontendPages->Disable('visible');
    if($q=='en_visible') $Page->FrontendPages->Enable('visible');

    $Page->FrontendPages->page=$Page->FrontendPages->GetIdByFolderName($q);
    if( empty($Page->FrontendPages->page) ) {
        $Page->Set_404();
    }
}
else {
    if( !empty($pn) ) $Page->FrontendPages->page=$Page->FrontendPages->GetIdByName($pn);
    else $Page->FrontendPages->page=$page;
}
$Page->FrontendPages->preview = $preview;
$Page->FrontendPages->page_txt = $Page->FrontendPages->GetPageTxt($Page->FrontendPages->page);
$Page->FrontendPages->GetTitle()==NULL          ? $title = META_TITLE               : $title = $Page->FrontendPages->GetTitle();
$Page->FrontendPages->GetDescription()==NULL    ? $Description = META_DESCRIPTION   : $Description = $Page->FrontendPages->GetDescription();
$Page->FrontendPages->GetKeywords()==NULL       ? $Keywords = META_KEYWORDS         : $Keywords = $Page->FrontendPages->GetKeywords();
$Page->SetTitle( $title );
$Page->SetDescription( $Description );
$Page->SetKeywords( $Keywords );
$Page->FrontendPages->PageUser=$Page;
ob_start();
if(!is_ajax){
    $Page->FrontendPages->ShowContent();
}


switch($Page->FrontendPages->task)
{
    case 'show_tags':
        $Tags = New FrontTags();
        $id_tag = $Tags->Spr->GetCodByName(TblSysModTagsSprName, $tag);
        $Tags->ShowItems($id_tag);
        break;
}
$Page->content = ob_get_clean();
$Page->out();
?>
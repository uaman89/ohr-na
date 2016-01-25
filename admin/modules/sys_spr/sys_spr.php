<?php
/**
 * sys_spr.php
 * script for all actions with reference-books
 * @package System Package of SEOCMS
 * @author Igor Trokhymchuk  <ihor@seotm.com>
 * @version 1.1, 02.04.2012
 * @copyright (c) 2005+ by SEOTM
 */
if (!defined("SITE_PATH")){
    define("SITE_PATH", $_SERVER['DOCUMENT_ROOT']);
}
include_once( SITE_PATH . '/admin/include/defines.inc.php' );
include_once( SITE_PATH . '/admin/modules/sys_spr/sys_spr.class.php' );
if (!defined("_LANG_ID")) {
    $pg = new PageAdmin();
}

//echo '<br>$_REQUEST='.print_r($_REQUEST);
$module = AntiHacker::AntiHackRequest('module');
//echo '<br>$module='.$module;
//============================================================================================
// START
// Blocking to execute a script from outside (not Admin-part)
//============================================================================================
$Msg = new ShowMsg();
$goto = "http://" . NAME_SERVER . "/admin/index.php?logout=1";
//echo '<br>$goto='.$goto;
if (!isset($_SESSION['session_id']) OR empty($_SESSION['session_id']) OR empty($module)) {
    //$Msg->show_msg( '_NOT_AUTH' );
    //return false;
    ?><script>window.location.href="<?= $goto ?>";</script><?
;
    exit;
}

$logon = check_init('logon', 'Authorization');
//if ( ! defined('BASEPATH')) {
if (!$logon->LoginCheck()) {
    //return false;
    ?><script>window.location.href="<?= $goto ?>";</script><?
;
}
//=============================================================================================
// END
//=============================================================================================
//echo '<br>222$module='.$module;

$spr = AntiHacker::AntiHackRequest('spr');
$mas_module = explode("?", $module);
$module = $mas_module[0];
if(empty($spr)) {
    $spr = $mas_module[1];
    $mas_spr = explode("=", $mas_module[1]);
    $spr = $mas_spr[1];
}
$sys_spr = new SysSpr($logon->user_id, $module, NULL, NULL, NULL, '100%', $spr);

$sys_spr->task = AntiHacker::AntiHackRequest('task', 'show');
if (isset($_REQUEST['item_img']) and (!empty($_REQUEST['item_img']))) {
    $sys_spr->task = 'delitemimg';
    $sys_spr->item_img = $_REQUEST['item_img'];
}else{
    $sys_spr->item_img = NULL;
}
$sys_spr->sort = AntiHacker::AntiHackRequest('sort');
$sys_spr->start = AntiHacker::AntiHackRequest('start', '0');
$sys_spr->display = AntiHacker::AntiHackRequest('display', '20');
$sys_spr->id = AntiHacker::AntiHackRequest('id');
$sys_spr->cod = AntiHacker::AntiHackRequest('cod');
$sys_spr->cod_old = AntiHacker::AntiHackRequest('cod_old');
$sys_spr->name = AntiHacker::AntiHackArrayRequest('name');
$sys_spr->img = AntiHacker::AntiHackArrayRequest('img');
$sys_spr->fln = AntiHacker::AntiHackRequest('fln', _LANG_ID);
$sys_spr->srch = AntiHacker::AntiHackRequest('srch');
$sys_spr->module_name = AntiHacker::AntiHackRequest('module_name');
$sys_spr->root_script = AntiHacker::AntiHackRequest('root_script');
$sys_spr->parent_script = AntiHacker::AntiHackRequest('parent_script');
$sys_spr->parent_id = AntiHacker::AntiHackRequest('parent_id');
$sys_spr->info_msg = AntiHacker::AntiHackRequest('info_msg');
$sys_spr->edit_lang = AntiHacker::AntiHackRequest('edit_lang', _LANG_ID);


$sys_spr->categoryCod = AntiHacker::AntiHackRequest('categoryCod');





//------- settings fielsd start ---------
$sys_spr->usemove = AntiHacker::AntiHackRequest('usemove');
$sys_spr->move = AntiHacker::AntiHackArrayRequest('move');
if (!isset($_REQUEST['replace_to'])){
    $sys_spr->replace_to = NULL;
}else{
    $sys_spr->replace_to = stripslashes(trim($_REQUEST['replace_to']));
}

$sys_spr->uselevels = AntiHacker::AntiHackRequest('uselevels');
$sys_spr->level = AntiHacker::AntiHackRequest('level', 0);
$sys_spr->node = AntiHacker::AntiHackRequest('node', 0);
$sys_spr->level_new = AntiHacker::AntiHackRequest('level_new', '0');

$sys_spr->usecolors = AntiHacker::AntiHackRequest('usecolors');
$sys_spr->colorBit = AntiHacker::AntiHackArrayRequest('colorBit');

$sys_spr->usecodpli = AntiHacker::AntiHackRequest('usecodpli');
$sys_spr->cod_pli = AntiHacker::AntiHackRequest('cod_pli');

$sys_spr->usemeta = AntiHacker::AntiHackRequest('usemeta');
$sys_spr->mtitle = AntiHacker::AntiHackArrayRequest('mtitle');
$sys_spr->mdescr = AntiHacker::AntiHackArrayRequest('mdescr');
$sys_spr->mkeywords = AntiHacker::AntiHackArrayRequest('mkeywords');

$sys_spr->usetranslit = AntiHacker::AntiHackRequest('usetranslit');
$sys_spr->translit = AntiHacker::AntiHackArrayRequest('translit');
$sys_spr->translit_from = AntiHacker::AntiHackArrayRequest('translit_from');

$sys_spr->usedescr = AntiHacker::AntiHackRequest('usedescr');
$sys_spr->descr = AntiHacker::AntiHackArrayRequest('descr');

$sys_spr->useshort = AntiHacker::AntiHackRequest('useshort', 0);
$sys_spr->short = AntiHacker::AntiHackArrayRequest('short');
$sys_spr->short_title = AntiHacker::AntiHackArrayRequest('short_title');



$sys_spr->useimg = AntiHacker::AntiHackRequest('useimg', '0');
if (!isset($_FILES['image'])){
    $sys_spr->image = NULL;
}else{
    $sys_spr->image = $_FILES['image'];
}

$sys_spr->usehref = AntiHacker::AntiHackRequest('usehref', '0');
$sys_spr->href = AntiHacker::AntiHackArrayRequest('href');

$sys_spr->usevisible = AntiHacker::AntiHackRequest('usevisible', '0');
if (isset($_REQUEST['visible'])){
    $sys_spr->visible = 1; //видимый
}else{
    $sys_spr->visible = 0;
}

$sys_spr->useuploadimages = AntiHacker::AntiHackRequest('useuploadimages', '0');
if($sys_spr->useuploadimages==1){
    $sys_spr->UploadImages = check_init('UploadImage', 'UploadImage', $sys_spr->module.", null, 'uploads/images/".$sys_spr->spr."', '".TblModUploadImg."'");
}
$sys_spr->useuploadfiles = AntiHacker::AntiHackRequest('useuploadfiles', '0');
if($sys_spr->useuploadfiles==1){
    $sys_spr->UploadFile = check_init('UploadClass', 'UploadClass', $sys_spr->module.", null, 'uploads/files/".$sys_spr->spr."','".TblModUploadFiles."'");
}
//------- settings fielsd end ---------

//--- For Catalog parameters ---
$sys_spr->id_cat = AntiHacker::AntiHackRequest('id_cat');
$sys_spr->id_param = AntiHacker::AntiHackRequest('id_param');

if( $sys_spr->task=='savereturn') {$sys_spr->task='save'; $sys_spr->action='return';}
else $sys_spr->action=NULL;

if ($sys_spr->module != NULL){
    $sys_spr->script_ajax = "module=$sys_spr->module&spr=$sys_spr->spr&display=$sys_spr->display&start=$sys_spr->start&sort=$sys_spr->sort&fln=$sys_spr->fln&usedescr=$sys_spr->usedescr&&useshort=$sys_spr->useshort&useimg=$sys_spr->useimg&uselevels=$sys_spr->uselevels&level=$sys_spr->level&node=$sys_spr->node&usemeta=$sys_spr->usemeta&usevisible=$sys_spr->usevisible&root_script=$sys_spr->root_script&parent_script=$sys_spr->parent_script&parent_id=$sys_spr->parent_id&srch=$sys_spr->srch&module_name=$sys_spr->module_name";
    if (!empty($sys_spr->id_cat))
        $sys_spr->script_ajax .= "&id_cat=" . $sys_spr->id_cat;
    if (!empty($sys_spr->id_param))
        $sys_spr->script_ajax .= "&id_param=" . $sys_spr->id_param;
    $sys_spr->script = "index.php?" . $sys_spr->script_ajax;
    //echo '<br> $sys_spr->script='.$sys_spr->script;
    //phpinfo();
    //echo '<br>$sys_spr->task='.$sys_spr->task;
    switch ($sys_spr->task) {
        case 'show':
            $sys_spr->show();
            break;
        case 'show_sublevel':
            $sys_spr->show();
            //$sys_spr->ShowContentHTML();
            break;
        case 'edit':
            if ($sys_spr->use_edit_ajax == 1) {
                if (!$sys_spr->EditWithAjax())
                    echo "<script>window.location.href='$sys_spr->script';</script>";
            }
            else {
                $sys_spr->edit();
            }
            break;
        case 'new':
            if ($sys_spr->use_edit_ajax == 1) {
                $sys_spr->EditWithAjax();
            } else {
                $sys_spr->edit();
            }
            break;
        case 'edit_lng_panel':
            //echo '<br>$_REQUEST='.print_r($_REQUEST);
            $sys_spr->EditLngPanel();
            break;
        case 'add_lang':
            if (!$sys_spr->add_lang($logon->user_id, $module, $id, NULL, $spr))
                echo "<script>window.location.href='$sys_spr->script';</script>";
            break;
        case 'add_img_on_lang':
            if ($sys_spr->SavePicture() != NULL) {
                $sys_spr->Form->ShowErrBackEnd($sys_spr->Err);
                return false;
            }
            $sys_spr->EditLngPanelImg($sys_spr->img[$sys_spr->edit_lang]);
            break;
        case 'save':
            //phpinfo();
            if ($sys_spr->use_edit_ajax == 0) {
                if ($sys_spr->SavePicture() != NULL) {
                    $sys_spr->edit();
                    return false;
                }
            }
            $sys_spr->CheckFields();
            //echo '<br>$sys_spr->Err='.$sys_spr->Err;
            if (empty($sys_spr->Err)) {
                if ($sys_spr->save()) {
                    $sys_spr->info_msg = $sys_spr->Msg->show_text('_OK_SAVE');
                    if( $sys_spr->action=='return' ){
                        echo "<script>window.location.href='".$sys_spr->script."&info_msg=".$sys_spr->info_msg."&task=edit&id=".$sys_spr->id."';</script>";
                    }else{
                        echo "<script>window.location.href='".$sys_spr->script."&info_msg=".$sys_spr->info_msg."';</script>";
                    }

                }
            } else {
                if ($sys_spr->use_edit_ajax == 1) {
                    $sys_spr->EditWithAjax();
                } else {
                    $sys_spr->edit();
                }
            }
            break;
        case 'delete':
            if (!isset($_REQUEST['id_del']))
                $id_del = NULL;
            else
                $id_del = $_REQUEST['id_del'];
            if (!empty($id_del)) {
                $del = $sys_spr->del($id_del);
                if ($del == 0)
                    $Msg->show_msg('_ERROR_DELETE');
            }
            else
                $Msg->show_msg('_ERROR_SELECT_FOR_DEL');
            echo '<script>window.location.href="', $sys_spr->script, '";</script>';
            break;
        case 'cancel':
            echo '<script>window.location.href="', $sys_spr->script, '";</script>';
            break;
        case 'up':
            $sys_spr->up($sys_spr->spr, $sys_spr->level);
            $sys_spr->ShowContentHTML();
            //echo "<script>window.location.href='$sys_spr->script';</script>";
            break;
        case 'down':
            $sys_spr->down($sys_spr->spr, $sys_spr->level);
            $sys_spr->ShowContentHTML();
            //echo "<script>window.location.href='$sys_spr->script';</script>";
            break;
        case 'replace':
            $sys_spr->Form->ReplaceByCod($sys_spr->spr, 'move', $sys_spr->id, $sys_spr->replace_to);
            $sys_spr->ShowContentHTML();
            break;
        case 'delitemimg':
            //echo '<br>$sys_spr->item_img='.$sys_spr->item_img;
            if (!$sys_spr->DelItemImage($sys_spr->item_img, $sys_spr->edit_lang)) {
                $sys_spr->Err = $sys_spr->Msg->show_text('MSG_IMAGE_NOT_DELETED') . "<br>";
            }
            if ($sys_spr->use_edit_ajax == 1) {
                $sys_spr->EditLngPanelImg($sys_spr->img[$sys_spr->edit_lang]);
            } else {
                $sys_spr->edit();
                //echo "<script>window.location.href='$sys_spr->script';</script>";
            }
            break;
        case 'make_search':
            $sys_spr->showList();
            break;
        case 'change_visible':
            if (!isset($_REQUEST['new_visible']))
                $sys_spr->new_visible = NULL;
            else
                $sys_spr->new_visible = $sys_spr->Form->GetRequestNumData($_REQUEST['new_visible']);
            //echo
            $sys_spr->ChangeVisibleProp($sys_spr->cod, $sys_spr->new_visible);
            $sys_spr->ShowVisibility($sys_spr->cod, $sys_spr->new_visible);
            break;

        case 'add_new_tags':
            $sys_spr->EditWithAjax();
            break;
    } //end switch
} //end if
?>
<?php
/**
* user.backend.php
* script for all actions with External Users
* @package User Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 24.02.2011
* @copyright (c) 2010+ by SEOTM
*/
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/modules/mod_user/user.defines.php' );

if(!defined("_LANG_ID")) {$pg = new PageAdmin();}

if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];

//Blocking to execute a script from outside (not Admin-part)
if ( !$pg->logon->isAccessToScript($module)) exit;

if( !isset($_REQUEST['task']) || empty($_REQUEST['task']) ) $task='showblogs';
else $task=$_REQUEST['task'];

if( !isset( $_REQUEST['fln'] ) ) $fln = _LANG_ID;
else $fln = $_REQUEST['fln'];

if( !isset( $_REQUEST['fltr'] ) ) $fltr = NULL;
else $fltr = $_REQUEST['fltr'];

if( !isset( $_REQUEST['fltr2'] ) ) $fltr2 = NULL;
else $fltr2 = $_REQUEST['fltr2'];

if( !isset( $_REQUEST['srch'] ) ) $srch = NULL;
else $srch = $_REQUEST['srch'];

if(!isset($_REQUEST['srch_dtfrom'])) $srch_dtfrom=NULL;
else $srch_dtfrom=$_REQUEST['srch_dtfrom'];

if(!isset($_REQUEST['srch_dtto'])) $srch_dtto=NULL;
else $srch_dtto=$_REQUEST['srch_dtto'];


if(!isset($_REQUEST['srch_alias'])) $srch_alias=NULL;
else $srch_alias=$_REQUEST['srch_alias'];


if(!isset($_REQUEST['sort'])) $sort=NULL;
else $sort=$_REQUEST['sort'];

if(!isset($_REQUEST['start'])) $start=0;
else $start=$_REQUEST['start'];

if(!isset($_REQUEST['display']) || ( isset($srch) and empty($srch)) ) $display=10;
else $display=$_REQUEST['display'];

//echo "display".$display;
if( !isset($_REQUEST['id']) ) $id=NULL;
else $id = $_REQUEST['id'];

if ( !isset($_REQUEST['sys_user_id'])) $sys_user_id = NULL;
else  $sys_user_id = $_REQUEST['sys_user_id'];

if ( !isset($_REQUEST['state'])) $state  = NULL;
else  $state  = $_REQUEST['state'];

if ( !isset($_REQUEST['aboutMe'])) $aboutMe  = NULL;
else  $aboutMe = $_REQUEST['aboutMe'];

if ( !isset($_REQUEST['blogId'])) $blogId  = NULL;
else  $blogId = $_REQUEST['blogId'];

if ( !isset($_REQUEST['id_del'])) $id_del  = NULL;
else  $id_del = $_REQUEST['id_del'];

if ( !isset($_REQUEST['showBlog'])) $showBlog = 0;
else  $showBlog = $_REQUEST['showBlog'];

if ( !isset($_REQUEST['headerBlog'])) $headerBlog = 0;
else  $headerBlog = $_REQUEST['headerBlog'];

if ( !isset($_REQUEST['blogContent'])) $blogContent = 0;
else  $blogContent = $_REQUEST['blogContent'];

$Blog=new UserBlogCtrl($pg->logon->user_id, $module, 10, $sort, $start, '100%');
$Blog->task = $task;
$Blog->blogId=$blogId;
$Blog->display = $display;
$Blog->sort = $sort;
$Blog->start = $start;
$Blog->fln = $fln;
$Blog->srch = $srch;
$Blog->fltr = $fltr;
$Blog->fltr2 = $fltr2;
$Blog->srch_dtfrom = $srch_dtfrom;
$Blog->srch_dtto = $srch_dtto;
$Blog->headerBlog = $headerBlog;
$Blog->sys_user_id = $sys_user_id;
$Blog->blogContent = $blogContent;
$Blog->showBlog = $showBlog;
//$Blog->script_ajax = "module=$Blog->module&display=$Blog->display&start=$Blog->start&sort=$Blog->sort&id=$Blog->id&fln=$Blog->fln&fltr=$Blog->fltr&fltr2=$Blog->fltr2&srch=$Blog->srch&srch_dtfrom=$Blog->srch_dtfrom&srch_dtto=$Blog->srch_dtto&srch_alias=$Blog->srch_alias";
//$Blog->script="index.php?".$Blog->script_ajax;
$Blog->script="index.php?";
//phpinfo();
switch( $task ) {
    case "showblogs":
        $Blog->ShowContent();
        break;
    case "editUserBlog":
        $Blog->EditUserBlog();
        break;
    case "delete":
        $Blog->DelUserBlogs($id_del);
        echo "<script language='JavaScript'>location.href='".$Blog->script.'module='.$Blog->module.'&task=showblogs&sys_user_id='.$Blog->sys_user_id."';</script>";
        break;
    case 'cancel':
        echo "<script language='JavaScript'>location.href='".$Blog->script.'module='.$Blog->module.'&task=showblogs&sys_user_id='.$Blog->sys_user_id."';</script>";
        break;
    case "save":
        if($Blog->save()) echo "Під час збереження блогу виникла помилка! Спробуйте ще раз.";
        echo "<script language='JavaScript'>location.href='".$Blog->script.'module='.$Blog->module.'&task=showblogs&sys_user_id='.$Blog->sys_user_id."';</script>";
        break;
    case "savereturn":
        if($Blog->save()) echo "Під час збереження блогу виникла помилка! Спробуйте ще раз.";
        $Blog->EditUserBlog();
        break;
    case "new":
         $Blog->EditUserBlog();
        break;
}

?>


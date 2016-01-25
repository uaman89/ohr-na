<?php
/**
* search.backend.php  
* script for all actions with Search results
* @package Search Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 24.02.2011
* @copyright (c) 2010+ by SEOTM
*/
include_once( $_SERVER['DOCUMENT_ROOT'].'/modules/mod_search/search.defines.php' );

if(!defined("_LANG_ID")) {$pg = new PageAdmin();} 

if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];

//Blocking to execute a script from outside (not Admin-part) 
if ( !$pg->logon->isAccessToScript($module)) exit;

if( !isset($_REQUEST['task']) || empty($_REQUEST['task']) ) $task='show';
else $task=$_REQUEST['task'];

if( !isset( $_REQUEST['fln'] ) ) $fln = _LANG_ID;
else $fln = $_REQUEST['fln'];

if( !isset( $_REQUEST['fltr'] ) ) $fltr = NULL;
else $fltr = $_REQUEST['fltr'];

if( !isset( $_REQUEST['srch'] ) ) $srch = NULL;
//echo '<br> srch='.$srch;

if(!isset($_REQUEST['sort'])) $sort=NULL;
else $sort=$_REQUEST['sort'];

if(!isset($_REQUEST['start'])) $start=0;
else $start=$_REQUEST['start'];

if(!isset($_REQUEST['display']) || empty($srch) ) $display=20;
else $display=$_REQUEST['display'];

if( !isset($_REQUEST['id']) ) $id=NULL;
else $id = $_REQUEST['id'];

if( !isset($_REQUEST['date']) ) $date=date('Y-m-d');
else $date = $_REQUEST['date'];


if( !isset($_REQUEST['time']) ) $time=NULL;
else $time = $_REQUEST['time'];

if( !isset($_REQUEST['ip']) ) $ip=NULL;
else $ip = $_REQUEST['ip'];

if( !isset($_REQUEST['result']) ) $result=NULL;
else $result = $_REQUEST['result'];





$mod = new Search();
$mod->module = $module;
$mod->user_id = $pg->logon->user_id;

$mod->task = $task;
$mod->display = $display;
$mod->start = $start;
$mod->sort = $sort;
$mod->fltr = $fltr;

$mod->date = $date;
 	
	


	
	if( !isset( $_REQUEST['fln'] ) ) $mod->fln = _LANG_ID;
 		else 
		$mod->fln = $_REQUEST['fln'];
		
$script = 'module='.$mod->module.'&display='.$mod->display.'&start='.$mod->start.'&sort='.$mod->sort.'&fltr='.$mod->fltr.'&fln='.$mod->fln;
$script = $_SERVER['PHP_SELF']."?$script";


switch( $task ) {

    case 'show':      	$mod->show(); 
								break;
	
	
	
	case 'delete':
                       $del = $mod->del( $id_del );
                       if ( !empty($id_del) ) {
                         $del=$mod->del( $id_del );
                         if ( $del > 0 ) echo "<script>window.alert('".$pg->Msg->get_msg('_SYS_DELETED_OK')." $del');</script>";
                         else $pg->Msg->show_msg('_ERROR_DELETE');
                       }
                       else $pg->Msg->show_msg('_ERROR_SELECT_FOR_DEL');
                       echo "<script>window.location.href='$script';</script>";
                       break;
					   
	
}

?>
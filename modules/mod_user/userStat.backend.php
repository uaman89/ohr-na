<?php                                     /* userStat.backend.php */
// ================================================================================================
// System : SEOCMS
// Module : userStat.backend.php
// Version : 1.0.0
// Date : 30.01.2006
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : script for all actions with Registration statistic of External users on the back-end
//
// ================================================================================================

include_once( SITE_PATH.'/modules/mod_user/user.defines.php' );

$logon = new  Authorization();

$Msg = new ShowMsg();

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

if(!isset($_REQUEST['display']) & empty($srch) ) $display=20;
else $display=$_REQUEST['display'];

if( !isset($_REQUEST['id']) ) $id=NULL;
else $id = $_REQUEST['id'];


 $UserStat = new UserStat( $logon->user_id, $module, 20, $sort, $start, '100%' );
 $UserStat->user_id = $logon->user_id;
 $UserStat->module = $module;
 $UserStat->display = $display;
 $UserStat->sort = $sort;
 $UserStat->start = $start;
 $UserStat->fln = $fln;
 $UserStat->srch = $srch;
 $UserStat->fltr = $fltr;


 $scriptact=$_SERVER['PHP_SELF']."?module=$UserStat->module&display=$UserStat->display&start=$UserStat->start&sort=$UserStat->sort&fltr=$UserStat->fltr&srch=$UserStat->srch";

 switch( $task ) {
    case 'show':      $UserStat->show();
                      break;
    case 'delete':
                      if( !isset($_REQUEST['id_del']) ) $id_del=NULL;
                      else $id_del = $_REQUEST['id_del'];
                      if ( !empty($id_del) ) {
                         $del=$UserStat->del( $id_del );
                         if ( $del > 0 ) echo "<script>window.alert('".$Msg->get_msg('_SYS_DELETED_OK')." $del');</script>";
                         else $Msg->show_msg('_ERROR_DELETE');
                      }
                      else $Msg->show_msg('_ERROR_SELECT_FOR_DEL');
                      echo "<script>window.location.href='$scriptact';</script>";
                      break;
 }

?>
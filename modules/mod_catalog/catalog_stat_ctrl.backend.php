<?php                                     /* catalog_stat_ctrl.backend.php */
// ================================================================================================
// System : SEOCMS
// Module : catalog_stat_ctrl.backend.php
// Version : 1.0.0
// Date : 06.08.2007
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : script for all actions  of control with Catalog Statistic on the back-end
//
// ================================================================================================
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/modules/mod_catalog/catalog.defines.php' );

if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];

//============================================================================================
// START
// Blocking to execute a script from outside (not Admin-part) 
//============================================================================================
$Msg = new ShowMsg();
$goto = "http://".NAME_SERVER."/admin/index.php?logout=1";
//echo '<br>$goto='.$goto;
if ( !isset($_SESSION[ 'session_id']) OR empty($_SESSION[ 'session_id']) OR empty( $module ) ) {
     //$Msg->show_msg( '_NOT_AUTH' );
     //return false;
     ?><script>window.location.href="<?=$goto?>";</script><?;
}

$logon = new  Authorization();
if (!$logon->LoginCheck()) {
    //return false;
    ?><script>window.location.href="<?=$goto?>";</script><?; 
}
//=============================================================================================
// END
//=============================================================================================

if( !isset($_REQUEST['task']) || empty($_REQUEST['task']) ) $task='show';
else $task=$_REQUEST['task'];

if ( isset($_REQUEST['del_for_period']) ) $task = 'del_for_period';

//echo '<br> $task='.$task;

if( !isset( $_REQUEST['fln'] ) ) $fln = _LANG_ID;
else $fln = $_REQUEST['fln'];

if( !isset( $_REQUEST['fltr'] ) ) $fltr = NULL;
else $fltr = $_REQUEST['fltr'];

if( !isset( $_REQUEST['srch'] ) ) $srch = NULL;
else $srch=$_REQUEST['srch'];

if(!isset($_REQUEST['sort'])) $sort=NULL;
else $sort=$_REQUEST['sort'];

if(!isset($_REQUEST['start'])) $start=0;
else $start=$_REQUEST['start'];

if(!isset($_REQUEST['display']) ) $display=100;
else $display=$_REQUEST['display'];

if(!isset($_REQUEST['del_dt_from']) ) $del_dt_from = NULL;
else $del_dt_from = $_REQUEST['del_dt_from'];

if(!isset($_REQUEST['del_dt_to']) ) $del_dt_to = NULL;
else $del_dt_to = $_REQUEST['del_dt_to'];

if(!isset($_REQUEST['show_dt_from']) ) $show_dt_from = NULL;
else $show_dt_from = $_REQUEST['show_dt_from'];

if(!isset($_REQUEST['show_dt_to']) ) $show_dt_to = NULL;
else $show_dt_to = $_REQUEST['show_dt_to'];
  
 $Catalog = new Catalog_Stat_Ctrl($logon->user_id, $module, $display, $sort, $start, '100%');
 $Catalog->user_id = $logon->user_id;
 $Catalog->module = $module;
 $Catalog->display = $display;
 $Catalog->sort = $sort;
 $Catalog->start = $start;
 $Catalog->fln = $fln;
 $Catalog->srch = $srch;
 $Catalog->fltr = $fltr;
 
 $Catalog->del_dt_from = $del_dt_from;
 $Catalog->del_dt_to = $del_dt_to;
 $Catalog->show_dt_from = $show_dt_from;
 $Catalog->show_dt_to = $show_dt_to;

 $Catalog->script=$_SERVER['PHP_SELF']."?module=$Catalog->module&display=$Catalog->display&start=$Catalog->start&sort=$Catalog->sort&fltr=$Catalog->fltr&srch=$Catalog->srch&level=$Catalog->level";
 //phpinfo(); 
 switch( $task ) {
    case 'show':      
        $Catalog->show();
        break;
    case 'save':
        break;
    case 'delete':
        if( !isset($_REQUEST['id_del']) ) $id_del=NULL;
        else $id_del = $_REQUEST['id_del'];
        if ( !empty($id_del) ) {
         $del=$Catalog->del( $id_del );
         if ( $del > 0 ) echo "<script>window.alert('".$Msg->get_msg('_SYS_DELETED_OK')." $del');</script>";
         else $Msg->show_msg('_ERROR_DELETE');
        }
        else $Msg->show_msg('_ERROR_SELECT_FOR_DEL');
        $Catalog->show();
        //echo "<script>window.location.href='$Catalog->script';</script>";
        break;
    case 'del_for_period':
        $del = $Catalog->DelForPeriod();
        if ( $del > 0 ) echo "<script>window.alert('".$Msg->get_msg('_SYS_DELETED_OK')." $del');</script>";
        else $Msg->show_msg('_ERROR_DELETE');
        $Catalog->show();
        break;
 }

?>
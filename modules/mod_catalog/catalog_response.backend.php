 <?php
/**
* catalog_response.backend.php   
* script for all actions with Catalog Responce
* @package Catalog Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 24.02.2011
* @copyright (c) 2010+ by SEOTM
*/
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] ); 
include_once( SITE_PATH.'/modules/mod_catalog/catalog.defines.php' );

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

if( !isset( $_REQUEST['fltr2'] ) ) $fltr2 = NULL;
else $fltr2 = $_REQUEST['fltr2'];

if( !isset( $_REQUEST['srch'] ) ) $srch = NULL;
else $srch = $_REQUEST['srch'];

if(!isset($_REQUEST['sort'])) $sort=NULL;
else $sort=$_REQUEST['sort'];

if(!isset($_REQUEST['start'])) $start=0;
else $start=$_REQUEST['start'];

if(!isset($_REQUEST['display']) || empty($srch) ) $display=20;
else $display=$_REQUEST['display'];



if( !isset($_REQUEST['id']) ) $id=NULL;
else $id = $_REQUEST['id'];

if( !isset($_REQUEST['id_prop']) ) $id_prop = NULL;
else $id_prop = $_REQUEST['id_prop'];

if ( !empty($id_prop)  ) { // $ltr2=categ=51 => $flrt2=51
  $arr_fltr2_tmp=explode("=",$id_prop);
  if (isset($arr_fltr2_tmp[1])) $id_prop=$arr_fltr2_tmp[1];
}

if( !isset($_REQUEST['name']) ) $name=NULL;
else $name = $_REQUEST['name'];

if( !isset($_REQUEST['email']) ) $email=NULL;
else $email = $_REQUEST['email'];

if( !isset($_REQUEST['response']) ) $response=NULL;
else $response = $_REQUEST['response'];

if( !isset($_REQUEST['rating']) ) $rating=0;
else $rating = $_REQUEST['rating'];

if( !isset($_REQUEST['dt']) ) $dt=date('Y-m-d');
else $dt = $_REQUEST['dt'];

if( !isset($_REQUEST['status']) ) $status = NULL;
else $status = $_REQUEST['status'];

if( !isset($_REQUEST['move']) ) $move = NULL;
else $move = $_REQUEST['move'];


$mas_module=explode("?",$module); 
$module=$mas_module[0];


//if( $task=='moderate') {if( !defined("AJAX_RELOAD")) define("AJAX_RELOAD", 1); }
 
 $Catalog = new CatalogResponse($pg->logon->user_id, $module, $display, $sort, $start, '100%');
 $Catalog->user_id = $pg->logon->user_id;
 $Catalog->module = $module;
 $Catalog->display = $display;
 $Catalog->sort = $sort;
 $Catalog->start = $start;
 $Catalog->fln = $fln;
 $Catalog->fltr = $fltr;
 $Catalog->fltr2 = $fltr2; 
 $Catalog->srch = $srch;
                   
 $Catalog->id=$id;
 $Catalog->id_prop=$id_prop;
 $Catalog->name=addslashes($name); 
 $Catalog->email=addslashes($email);
 $Catalog->response=addslashes($response); 
 $Catalog->rating=$rating;
 $Catalog->dt=$dt; 
 $Catalog->status=$status;
 $Catalog->move=$move;
 if( defined("AJAX_RELOAD") ) $Catalog->ajax_reload = AJAX_RELOAD;
 else $Catalog->ajax_reload = NULL;



 $Catalog->script=$_SERVER['PHP_SELF']."?module=$Catalog->module&display=$Catalog->display&start=$Catalog->start&sort=$Catalog->sort&fltr=$Catalog->fltr&srch=$Catalog->srch";
 //echo '<br>$task='.$task.' $Catalog->script='.$Catalog->script;  
 switch( $task ) {
    case 'show':      $Catalog->show();
                      break;
    case 'edit':
                      if (!$Catalog->edit( $id )) echo "<script>window.location.href='$Catalog->script';</script>";
                      break;
    case 'new':       $Catalog->edit( NULL );
                      break;
    case 'save':
                      if ( $Catalog->ChecResponseFields( $id )!=NULL ) {
                          $Catalog->edit( $id );
                          return false;
                      }
                      if ( $Catalog->save( $id ) ){
                          echo "<script>window.location.href='$Catalog->script';</script>";
                      }
                      break;
    case 'delete':
                      if( !isset($_REQUEST['id_del']) ) $id_del=NULL;
                      else $id_del = $_REQUEST['id_del'];
                      if ( !empty($id_del) ) {
                         $del=$Catalog->del( $id_del );
                         if ( $del > 0 ) echo "<script>window.alert('".$pg->Msg->get_msg('_SYS_DELETED_OK')." $del');</script>";
                         else $pg->Msg->show_msg('_ERROR_DELETE');
                      }
                      else $pg->Msg->show_msg('_ERROR_SELECT_FOR_DEL');
                      echo "<script>window.location.href='$Catalog->script';</script>";
                      break;
    case 'cancel':
                      echo "<script>window.location.href='$Catalog->script';</script>";
                      break;
    case 'up':
                      $Catalog->up(TblModCatalog);
                      echo "<script>window.location.href='$Catalog->script';</script>";
                      break;

    case 'down':
                      $Catalog->down(TblModCatalog);
                      echo "<script>window.location.href='$Catalog->script';</script>";
                      break; 
    case 'moderate':
                      $Catalog->Moderate();
                      $Catalog->show();
                      //echo "<script>window.location.href='$Catalog->script';</script>";
                      break; 
 }

?>

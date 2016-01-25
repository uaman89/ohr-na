<?php
/**
* catalog_params.backend.php
* script for all actions with Catalog parameters
* @package Catalog Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 24.02.2011
* @copyright (c) 2010+ by SEOTM
*/

if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( $_SERVER['DOCUMENT_ROOT'].'/modules/mod_catalog/catalog.defines.php' );

if(!defined("_LANG_ID")) { $pg = check_init('PageAdmin', 'PageAdmin');}

if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];

//Blocking to execute a script from outside (not Admin-part)
if ( !$pg->logon->isAccessToScript($module)) exit;

if( !isset($_REQUEST['task']) || empty($_REQUEST['task']) ) $task='show';
else $task=$_REQUEST['task'];

 //echo '<br> $task='.$task;

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

if(!isset($_REQUEST['display']) AND empty($srch) ) $display=20;
else {
    if($task=='show_srch_res') $display=20;
    else $display=$_REQUEST['display'];
}


// read the parent parameters
if(isset($_REQUEST['parent'])) $parent = $_REQUEST['parent'];
else $parent = NULL;

if(isset($_REQUEST['parent_module'])) $parent_module = $_REQUEST['parent_module'];
else $parent_module = NULL;

if(isset($_REQUEST['parent_id'])) $parent_id = $_REQUEST['parent_id'];
else $parent_id = NULL;

if(isset($_REQUEST['parent_display'])) $parent_display = $_REQUEST['parent_display'];
else $parent_display = NULL;

if(isset($_REQUEST['parent_start'])) $parent_start = $_REQUEST['parent_start'];
else $parent_start = NULL;

if(isset($_REQUEST['parent_sort'])) $parent_sort = $_REQUEST['parent_sort'];
else $parent_sort = NULL;

if(isset($_REQUEST['parent_task'])) $parent_task = $_REQUEST['parent_task'];
else $parent_task = NULL;

if(isset($_REQUEST['parent_level'])) $parent_level = $_REQUEST['parent_level'];
else $parent_level = NULL;

if(isset($_REQUEST['parent_script'])) $parent_script= $_REQUEST['parent_script'];
else $parent_script = NULL;


// read self parameters
if( !isset($_REQUEST['id']) ) $id=NULL;
else $id = $_REQUEST['id'];

if( !isset($_REQUEST['id_cat']) ) $id_cat=NULL;
else $id_cat = $_REQUEST['id_cat'];

if( !isset($_REQUEST['name']) ) $name=NULL;
else $name = $_REQUEST['name'];

if( !isset($_REQUEST['prefix']) ) $prefix=NULL;
else $prefix = $_REQUEST['prefix'];

if( !isset($_REQUEST['sufix']) ) $sufix=NULL;
else $sufix = $_REQUEST['sufix'];

if( !isset($_REQUEST['type']) ) $type=NULL;
else $type = $_REQUEST['type'];

if( !isset($_REQUEST['move']) ) $move=NULL;
else $move = $_REQUEST['move'];

if (isset($_REQUEST['is_img'])) $is_img=1;
else $is_img=0;

if (isset($_REQUEST['modify'])) $modify=1;
else $modify=0;

if( !isset($_REQUEST['id_cat_move_from']) ) $id_cat_move_from=NULL;
else $id_cat_move_from = $_REQUEST['id_cat_move_from'];
if ( !empty($id_cat_move_from)  ) { // $ltr2=categ=51 => $flrt2=51
  $arr_fltr2_tmp=explode("=",$id_cat_move_from);
  if (isset($arr_fltr2_tmp[1])) $id_cat_move_from=$arr_fltr2_tmp[1];
}

if( !isset($_REQUEST['id_cat_move_to']) ) $id_cat_move_to=NULL;
else $id_cat_move_to = $_REQUEST['id_cat_move_to'];
if ( !empty($id_cat_move_to)  ) { // $ltr2=categ=51 => $flrt2=51
  $arr_fltr2_tmp=explode("=",$id_cat_move_to);
  if (isset($arr_fltr2_tmp[1])) $id_cat_move_to=$arr_fltr2_tmp[1];
}

if (isset($_REQUEST['use_parent_params'])) $use_parent_params=1;
else $use_parent_params=0;

if( !isset($_REQUEST['descr']) ) $descr=NULL;
else $descr = $_REQUEST['descr'];

if( !isset($_REQUEST['mtitle']) ) $mtitle=NULL;
else $mtitle = $_REQUEST['mtitle'];

if( !isset($_REQUEST['mdescr']) ) $mdescr=NULL;
else $mdescr = $_REQUEST['mdescr'];

if( !isset($_REQUEST['mkeywords']) ) $mkeywords=NULL;
else $mkeywords = $_REQUEST['mkeywords'];



$Catalog = new CatalogParams($pg->logon->user_id, $module, 20, $sort, $start, '100%');

$Catalog->user_id = $pg->logon->user_id;
$Catalog->module = $module;
$Catalog->display = $display;
$Catalog->sort = $sort;
$Catalog->start = $start;
$Catalog->fln = $fln;
$Catalog->srch = $srch;
$Catalog->fltr = $fltr;
$Catalog->fltr2 = $fltr2;

$Catalog->parent=$parent;
$Catalog->parent_module=$parent_module;
$Catalog->parent_id=$parent_id;
$Catalog->parent_display=$parent_display;
$Catalog->parent_start=$parent_start;
$Catalog->parent_sort=$parent_sort;
$Catalog->parent_task=$parent_task;
$Catalog->parent_level=$parent_level;
$Catalog->parent_script=$parent_script;

$Catalog->id=$id;
$Catalog->id_cat=$id_cat;
$Catalog->name=$name;
$Catalog->prefix=$prefix;
$Catalog->sufix=$sufix;
$Catalog->type=$type;
$Catalog->move=$move;
$Catalog->is_img=$is_img;
$Catalog->modify=$modify;
$Catalog->id_cat_move_from=$id_cat_move_from;
$Catalog->id_cat_move_to=$id_cat_move_to;
$Catalog->use_parent_params=$use_parent_params;

$Catalog->descr = $descr;
$Catalog->mtitle = $mtitle;
$Catalog->mdescr = $mdescr;
$Catalog->mkeywords = $mkeywords;

//phpinfo();
//echo '<br> $task='.$task.' $Catalog->parent_script='.$Catalog->parent_script.' $is_img='.$is_img;

$Catalog->script=$_SERVER['PHP_SELF']."?module=$Catalog->module&display=$Catalog->display&start=$Catalog->start&sort=$Catalog->sort&fltr=$Catalog->fltr&srch=$Catalog->srch&id_cat=$Catalog->id_cat&parent_script=$Catalog->parent_script&parent_module=$Catalog->parent_module&parent_id=$Catalog->parent_id&parent_display=$Catalog->parent_display&parent_start=$Catalog->parent_start&parent_sort=$Catalog->parent_sort&parent_task=$Catalog->parent_task&parent_level=$Catalog->parent_level";


switch( $task ) {
    case 'show':
        $Catalog->show();
        break;
    case 'edit':
        if (!$Catalog->edit() ) echo "<script>window.location.href='$Catalog->script';</script>";
        break;
    case 'new':
        $Catalog->edit();
        break;
    case 'save':
        if ( $Catalog->CheckParamsFields( $id )!=NULL ) {
           $Catalog->edit();
           return false;
        }
        if ( $Catalog->save() ){
           echo "<script>window.location.href='$Catalog->script';</script>";
           echo '<br>$Catalog->script='.$Catalog->script;
        }
        else echo '<br>'.$pg->Msg->show_text('MSG_ERR_NOT_SAVE');
        break;
    case 'delete':
        if( !isset($_REQUEST['id_del']) ) $id_del=NULL;
        else $id_del = $_REQUEST['id_del'];
        if ( !empty($id_del) ) {
           $del=$Catalog->del( $id_del );
           //if ( $del > 0 ) echo "<script>window.alert('".$pg->Msg->get_msg('_SYS_DELETED_OK')." $del');</script>";
           //else $pg->Msg->show_msg('_ERROR_DELETE');
           if(!$del) $pg->Msg->show_msg('_ERROR_DELETE');
        }
        else $pg->Msg->show_msg('_ERROR_SELECT_FOR_DEL');
        echo "<script>window.location.href='$Catalog->script';</script>";
        break;
    case 'cancel':
        echo "<script>window.location.href='$Catalog->script';</script>";
        break;
    case 'showvalues':
        $Catalog->ShowValues();
        break;
    case 'up':
        $Catalog->up_param(TblModCatalogParams);
        echo "<script>window.location.href='$Catalog->script';</script>";
        break;
    case 'down':
        $Catalog->down_param(TblModCatalogParams);
        echo "<script>window.location.href='$Catalog->script';</script>";
        break;
    case 'show_get_copy_params':
        $Catalog->ShowFormGetCopyOfParams();
        break;
    case 'copy_params_to_category':
        $rows = $Catalog->CopyParamsToCateg();
        if($rows>0) echo "<script>window.alert('".$pg->Msg->show_text('MSG_COPY_OK_PARAMETERS')." $rows');</script>";
        echo '<br>OK!';
        echo "<script>window.location.href='$Catalog->script';</script>";
        break;
}

?>

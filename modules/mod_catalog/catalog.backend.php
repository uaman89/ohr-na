<?php
/**
* catalog.backend.php
* script for all actions with Catalog categories
* @package Catalog Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 24.02.2011
* @copyright (c) 2010+ by SEOTM
*/
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/modules/mod_catalog/catalog.defines.php' );

if(!defined("_LANG_ID")) {$pg = check_init('PageAdmin', 'PageAdmin');}

if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];

//Blocking to execute a script from outside (not Admin-part)
if ( !$pg->logon->isAccessToScript($module)) exit;

if( !isset($_REQUEST['task']) || empty($_REQUEST['task']) ) $task='show';
else $task=$_REQUEST['task'];

if ( isset($_REQUEST['delimg']) ) $task = 'delimg';

if( !isset( $_REQUEST['fln'] ) ) $fln = _LANG_ID;
else $fln = $_REQUEST['fln'];

if( !isset( $_REQUEST['fltr'] ) ) $fltr = NULL;
else $fltr = $_REQUEST['fltr'];

if( !isset( $_REQUEST['srch'] ) ) $srch = NULL;
else $srch = $_REQUEST['srch'];

if(!isset($_REQUEST['sort'])) $sort=NULL;
else $sort=$_REQUEST['sort'];

if(!isset($_REQUEST['start'])) $start=0;
else $start=$_REQUEST['start'];

if(!isset($_REQUEST['display']) ) $display=20;
else $display=$_REQUEST['display'];

if( !isset($_REQUEST['id']) ) $id=NULL;
else $id = $_REQUEST['id'];

if( !isset($_REQUEST['group']) ) $group=NULL;
else $group = $_REQUEST['group'];

if( !isset($_REQUEST['level']) ) $level=NULL;
else $level = $_REQUEST['level'];

if( !isset($_REQUEST['description']) ) $description=NULL;
else $description = $_REQUEST['description'];

if( !isset($_REQUEST['name_ind']) ) $name_ind=NULL;
else $name_ind = $_REQUEST['name_ind'];

if( !isset($_REQUEST['h1']) ) $h1=NULL;
else $h1 = $_REQUEST['h1'];


if( !isset($_REQUEST['descr']) ) $descr=NULL;
else $descr = $_REQUEST['descr'];

if( !isset($_REQUEST['descr2']) ) $descr2=NULL;
else $descr2 = $_REQUEST['descr2'];

if( !isset($_REQUEST['move']) ) $move=NULL;
else $move = $_REQUEST['move'];

if (isset($_REQUEST['visible']))
    $visible = 2; //видимый
else
    $visible = 0;

if (isset($_REQUEST['show_in_menu']))
    $show_in_menu = 1; //видимый
else
    $show_in_menu = 0;

if( !isset($_REQUEST['category_sizes']) ) $category_sizes = NULL;
else $category_sizes = $_REQUEST['category_sizes'];

if( !isset($_REQUEST['img_cat']) ) $img_cat = NULL;
else $img_cat = $_REQUEST['img_cat'];

if( !isset($_REQUEST['new_id_cat']) ) $new_id_cat = NULL;
else $new_id_cat = $_REQUEST['new_id_cat'];

if ( !empty($new_id_cat)  ) { // $ltr2=categ=51 => $flrt2=51
  $arr_fltr2_tmp=explode("=",$new_id_cat);
  if (isset($arr_fltr2_tmp[1])) $new_id_cat = $arr_fltr2_tmp[1];
}

if( !isset($_REQUEST['id_cat1']) ) $id_cat1 = NULL;
else $id_cat1 = $_REQUEST['id_cat1'];

if( !isset($_REQUEST['arr_relat_categs']) ) $arr_relat_categs = NULL;
else $arr_relat_categs = $_REQUEST['arr_relat_categs'];

if( is_array($arr_relat_categs)){
    for($i=0; $i<count($arr_relat_categs); $i++){
      if ( !empty($arr_relat_categs[$i]) ) { // $ltr2=categ=51 => $flrt2=51
          $arr_fltr2_tmp=explode("=", $arr_relat_categs[$i]);
          if (isset($arr_fltr2_tmp[1])) $arr_relat_categs[$i] = $arr_fltr2_tmp[1];
      }
    }
}

if( !isset( $_REQUEST['replace_to'] ) ) $replace_to = NULL;
else $replace_to = $_REQUEST['replace_to'];

//------ Meta data START -------
if( !isset( $_REQUEST['mtitle'] ) ) $mtitle = NULL;
else $mtitle = $_REQUEST['mtitle'];

if( !isset( $_REQUEST['mdescr'] ) ) $mdescr = NULL;
else $mdescr = $_REQUEST['mdescr'];

if( !isset( $_REQUEST['keywords'] ) ) $keywords = NULL;
else $keywords = $_REQUEST['keywords'];

//------ Meta data END -------

if( !isset( $_REQUEST['translit'] ) ) $translit = NULL;
else $translit = $_REQUEST['translit'];

if( !isset( $_REQUEST['translit_old'] ) ) $translit_old = NULL;
else $translit_old = $_REQUEST['translit_old'];

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

if( $task=='savereturn') {$task='save'; $action='return';}
else $action=NULL;

if (!isset($_REQUEST['cod_pli']))
    $cod_pli = $id;
else
    $cod_pli = $_REQUEST['cod_pli'];

if (!isset($_REQUEST['multi_categs']))
    $multi_categs = NULL;
else
    $multi_categs = $_REQUEST['multi_categs'];



 $Catalog = new Catalog_category($pg->logon->user_id, $module, $display, $sort, $start, '100%');
// $Catalog->user_id = $pg->logon->user_id;
// $Catalog->user_id_group = $pg->logon->type;

 $Catalog->module = $module;
 $Catalog->task = $task;
 $Catalog->display = $display;
 $Catalog->sort = $sort;
 $Catalog->start = $start;
 $Catalog->fln = $fln;
 $Catalog->srch = $srch;
 $Catalog->fltr = $fltr;

 $Catalog->id=$id;
 $Catalog->cod_pli=$cod_pli;
 $Catalog->level=$level;
 $Catalog->group=$group;
 $Catalog->description=$description;
 $Catalog->name_ind=$name_ind;
 $Catalog->h1=$h1;
 $Catalog->descr=$descr;
 $Catalog->descr2=$descr2;
 $Catalog->move=$move;
 $Catalog->visible=$visible;
 $Catalog->show_in_menu=$show_in_menu;
 $Catalog->img_cat=$img_cat;
 $Catalog->new_id_cat = $new_id_cat;
 // for relations categories
 $Catalog->id_cat1 = $id_cat1;
 $Catalog->arr_relat_categs = $arr_relat_categs;

 $Catalog->mtitle=$mtitle;
 $Catalog->mdescr=$mdescr;
 $Catalog->keywords=$keywords;

 $Catalog->translit=$translit;
 $Catalog->translit_old=$translit_old;
 $Catalog->category_sizes=$category_sizes;

 $Catalog->id_cat_move_from=$id_cat_move_from;
 $Catalog->id_cat_move_to=$id_cat_move_to;

$Catalog->multi_categs = $multi_categs;

 $Catalog->script_ajax="module=$Catalog->module&display=$Catalog->display&start=$Catalog->start&sort=$Catalog->sort&fltr=$Catalog->fltr&srch=$Catalog->srch&level=$Catalog->level";
 $Catalog->script="index.php?".$Catalog->script_ajax;

 //echo '<br>$Catalog->task='.$Catalog->task.' $Catalog->id='.$Catalog->id;
 switch( $Catalog->task ) {
    case 'show':
        $Catalog->show();
        break;
     case 'show_main_category':
         $Catalog->showMainCategory();
         break;
    case 'edit':
        if (!$Catalog->edit())
            echo "<script>window.location.href='".$Catalog->script."';</script>";
        break;
    case 'new':
        $Catalog->edit();
        break;
    case 'save':
        $Catalog->ClearCategoryCache();

        if ( $Catalog->CheckFields()!=NULL ) {
            $Catalog->edit();
            return false;
          }

        if( !isset($_FILES['filename']) ) $filename = NULL;
        else $filename = $_FILES['filename'];
        if ( $filename!=null and $_FILES["filename"]["error"]==0) {
          //print_r($filename);
          $filename_name = $_FILES["filename"]["name"];
          //echo '<br> $filename='.$filename.' $filename_name='.$filename_name.' $_FILES["filename"]["name"]='.$_FILES["filename"]["name"];
          //$Catalog->img_cat = $filename_name;
          $tmp_f_name = $_FILES["filename"]["tmp_name"];
          $ext = substr($filename_name,1 + strrpos($filename_name, "."));
          $Catalog->img_cat = time().'_'.$Catalog->id.'.'.$ext;
        $article = $Catalog->GetSettings();
        $uploaddir = SITE_PATH.$article['img_path'].'/categories/';
        if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777);
        else @chmod($uploaddir,0777);
        $uploaddir1 = $uploaddir.$Catalog->img_cat;
        if ( !copy($tmp_f_name,$uploaddir1) ) {
                $Catalog->Err = $Catalog->Err.$Catalog->Msg->show_text('MSG_ERR_FILE_MOVE').'<br>';
                $Catalog->edit();
                @chmod($uploaddir,0755);
                return false;
        }
        @chmod($uploaddir,0755);
//        echo '<br> $Catalog->img_cat='.$Catalog->img_cat;

        }
        if ( $Catalog->save() ){
            $Catalog->ClearCategoryCache();
            $Catalog->UploadImages->SaveImages($Catalog->id);
            if( $action=='return' ) { echo "<script>window.location.href='".$Catalog->script."&level=".$Catalog->new_id_cat."&task=edit&id=".$Catalog->id."';</script>";}
            else { echo "<script>window.location.href='".$Catalog->script."';</script>";}
        }
        else echo '<br>'.$pg->Msg->show_text('MSG_ERR_NOT_SAVE');
        break;
    case 'delete':
        if( !isset($_REQUEST['id_del']) ) $id_del=NULL;
        else $id_del = $_REQUEST['id_del'];
        if ( !empty($id_del) ) {
         $del=$Catalog->del( $id_del );
         if( !$del) $pg->Msg->show_msg('_ERROR_DELETE');
         else{

             echo "<script>window.alert('".$pg->Msg->get_msg('_SYS_DELETED_OK')." $del');</script>";
         }
        }
        else $pg->Msg->show_msg('_ERROR_SELECT_FOR_DEL');
        //$Catalog->show();

        echo "<script>window.location.href='$Catalog->script';</script>";
        break;

    case 'cancel':
        echo "<script>window.location.href='$Catalog->script';</script>";
        break;

    //========== move categories to another coategories START ===========
    case 'show_move_to_category':
        if( !isset($_REQUEST['id_del']) ) $id_del=NULL;
        else $id_del = $_REQUEST['id_del'];
        //if ( !empty($id_del) ) {
            //if( empty($Catalog->level) OR $Catalog->level==0) $Catalog->id_cat_move_from=0;
            $del=$Catalog->ShowMoveToCategoryForm( $id_del );
        //}
        //else {
        //    $pg->Msg->show_msg('_ERROR_SELECT_FOR_MOVE');
        //    $Catalog->show();
        //}
        break;
    case 'move_to_category':
        if( !isset($_REQUEST['id_del']) ) $id_del=NULL;
        else $id_del = $_REQUEST['id_del'];
        if ( (!empty($Catalog->id_cat_move_from) OR $Catalog->id_cat_move_from==0) AND (!empty($Catalog->id_cat_move_to) OR $Catalog->id_cat_move_to==0) ) {
           $res = $Catalog->MoveToCategory( $id_del );
           //echo '<br>$res='.$res;
           if( $res ) {
               //echo "<script>window.alert('".$pg->Msg->get_msg('TXT_MOVE_OK', TblModCatalogSprTxt)." $Catalog->del');</script>";
               $id_del=NULL;
           }
           else {
               $pg->Msg->show_msg('MSG_ERR_', TblModCatalogSprTxt);
           }
           //echo '<br>$res='.$res;
           $Catalog->ShowMoveToCategoryForm( $id_del );
           $Catalog->ClearCategoryCache();
        }
        else {
            if( empty($Catalog->id_cat_move_from) AND $Catalog->id_cat_move_from!=0 ) $pg->Msg->show_msg('ERR_EMPTY_CATEGORY_MOVE_FROM');
            if( empty($Catalog->id_cat_move_to) AND $Catalog->id_cat_move_to!=0 ) $pg->Msg->show_msg('ERR_EMPTY_CATEGORY_MOVE_TO');
            $Catalog->ShowMoveToCategoryForm( $id_del );
            $Catalog->ClearCategoryCache();
        }
        break;
    //========== move categories to another coategories END ===========

    case 'up':
        $Catalog->up(TblModCatalog, 'level', $Catalog->level);
        $Catalog->ShowContentHTML();
        $Catalog->ClearCategoryCache();
        //echo "<script>window.location.href='$Catalog->script';</script>";
        break;

    case 'down':
        $Catalog->down(TblModCatalog, 'level', $Catalog->level);
        $Catalog->ShowContentHTML();
        $Catalog->ClearCategoryCache();
        //echo "<script>window.location.href='$Catalog->script';</script>";
        break;
    case 'replace':
        $Catalog->Form->Replace(TblModCatalog, 'move', $Catalog->id, $replace_to);
        $Catalog->ShowContentHTML();
        $Catalog->ClearCategoryCache();
        break;
    case 'delimg':
        if ( $Catalog->DelImg() ) {
            echo "<script>window.location.href='$Catalog->script';</script>";
        }
        else {
            $pg->Msg->show_msg('_ERROR_DELETE');
            $Catalog->edit( $id );
            return false;
        }
        break;
    //============ RELATION CATEGORIES START ===============
    case 'control_relat_categs_form':
        $Catalog->ShowControlRelatCategsForm();
        $Catalog->ClearCategoryCache();
        break;

    case 'add_relat_categs':
        $Catalog->AddRelatCategs();
        $Catalog->ShowControlRelatCategsForm();
        $Catalog->ClearCategoryCache();
        break;

    case 'del_relat_categs':
        if( !isset($_REQUEST['id_del']) ) $id_del=NULL;
        else $id_del = $_REQUEST['id_del'];
        //echo '<br>$id_del[1]='.$id_del[1];
        if ( !empty($id_del) ) {
         $del=$Catalog->DelRelatCategs( $id_del );
         if ( $del == 0 ) $pg->Msg->show_msg('_ERROR_DELETE');
        }
        else $pg->Msg->show_msg('_ERROR_SELECT_FOR_DEL');
        $Catalog->ShowControlRelatCategsForm();
        $Catalog->ClearCategoryCache();
        break;

    case 'up_relat_categ':
        $Catalog->up_relat_categ(TblModCatalogRelat);
        $Catalog->ShowControlRelatCategsForm();
        $Catalog->ClearCategoryCache();
        break;

    case 'down_relat_categ':
        $Catalog->down_relat_categ(TblModCatalogRelat);
        $Catalog->ShowControlRelatCategsForm();
        $Catalog->ClearCategoryCache();
        break;
    //============ RELATION CATEGORIES END ===============

    default:
        $Catalog->show();
        break;
 }
?>
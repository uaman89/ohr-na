<?php
/**
* catalog.php
* script for all actions with catalog
* @package Catalog Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 22.12.2010
* @copyright (c) 2010+ by SEOTM
*/


if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/include/defines.php' );
// var PageUser $Page
$Page = check_init('PageUser', 'PageUser');
$Page->FrontendPages->page = PAGE_CATALOG;
$Page->FrontendPages->page_txt = $Page->FrontendPages->GetPageTxt($Page->FrontendPages->page);

if(isset($Page->Catalog)) $Catalog = &$Page->Catalog;
else $Catalog = check_init('CatalogLayout', 'CatalogLayout');

$new_link = '';
//for .htaccess
if( isset($_REQUEST['str_cat'])){
  $categ_parent = NULL;
  $categ = NULL;
  //echo '<br>$_REQUEST[str_cat]=';print_r($_REQUEST['str_cat']);
  $cnt = count($_REQUEST['str_cat']);
  for($i=0;$i<$cnt;$i++){
      //Если первые 4 символа названия категории = строке "page", значит этот постраничность
      //и необходимо значение сохраниить в перменную $_REQUEST['page'].
      if(substr($_REQUEST['str_cat'][$i],0,4) != 'page'){
            $categ_parent = $categ;
            $categ = $Catalog->GetIdCategByTranslit($Catalog->Form->GetRequestTxtData($_REQUEST['str_cat'][$i], 1), $categ_parent, $Catalog->lang_id);
      }else{
            $_REQUEST['page'] = substr($_REQUEST['str_cat'][$i],4);
      }
  }
  //echo '<br>$Catalog->translit_lang_id='.$Catalog->translit_lang_id.' $Catalog->lang_id='.$Catalog->lang_id;
  //в случае, если транслит для категории в базе данных указан на языке, отличном от того, на который переключился пользователй на сайте
  //- то формируем новую ссылку к категории.
  if($Catalog->translit_lang_id!=$Catalog->lang_id AND !empty($Catalog->translit_lang_id)){
      $new_link = $Catalog->Link($categ);
  }
}
else {
    if( !isset($_REQUEST['categ']) ) $categ=NULL;
    else $categ = $Catalog->Form->GetRequestNumData($_REQUEST['categ']);
}
$Catalog->id_cat = $categ;

//for .htaccess
if( isset($_REQUEST['str_id'])){
  $Catalog->id = $Catalog->GetIdPropByTranslit($Catalog->Form->GetRequestTxtData($_REQUEST['str_id'], 1), $Catalog->id_cat, $Catalog->lang_id);
  //echo '<br>$Catalog->translit_prop_lang_id='.$Catalog->translit_prop_lang_id.' $Catalog->lang_id='.$Catalog->lang_id.' $new_link='.$new_link;
//в случае, если транслит для товара в базе данных указан на языке, отличном от того, на который переключился пользователй на сайте
  //или если трансли для товара одинаковый, но разный транслит для категорий - то формируем новую ссылку к товару.
  if( ($Catalog->translit_prop_lang_id!=$Catalog->lang_id AND !empty($Catalog->translit_prop_lang_id)) OR !empty($new_link)){
    $new_link = $Catalog->Link($categ, $Catalog->id);
  }
}
else {
    if( !isset($_REQUEST['curcod']) ) $Catalog->id=NULL;
    else $Catalog->id = $Catalog->Form->GetRequestNumData($_REQUEST['curcod']);
}
//необходим для 301-го редиректа с УРЛ одной язsковой версии на корректную другую
if(!empty($new_link)){
    //echo '<br>$new_link='.$new_link;
    header ('HTTP/1.1 301 Moved Permanently');
    header ('Location: '.$new_link);
    exit();
}


if( !isset($_REQUEST['task']) ) $Catalog->task=NULL;
else $Catalog->task = $Catalog->Form->GetRequestTxtData($_REQUEST['task'], 1);

if(!isset($_REQUEST['sort'])) $Catalog->sort='price';
else $Catalog->sort = $Catalog->Form->GetRequestTxtData($_REQUEST['sort'], 1);

if(!isset($_REQUEST['start'])) $Catalog->start=0;
else $Catalog->start = $Catalog->Form->GetRequestNumData($_REQUEST['start']);

if(!isset($_REQUEST['display'])) $Catalog->display=10;
else $Catalog->display = $Catalog->Form->GetRequestNumData($_REQUEST['display']);

if(!isset($_REQUEST['page'])) $Catalog->page=1;
else $Catalog->page = $Catalog->Form->GetRequestTxtData($_REQUEST['page'], 1);
if($Catalog->page>1) $Catalog->start = ($Catalog->page-1)*$Catalog->display;
if($Catalog->page=='all') {
    $Catalog->start = 0;
    $Catalog->display = 999999;
}

if( !isset($_REQUEST['search_keywords']) ) $Catalog->search_keywords=NULL;
else $Catalog->search_keywords = $Catalog->Form->GetRequestTxtData($_REQUEST['search_keywords'], 1);

if( !isset($_REQUEST['search_type']) ) $Catalog->search_type=NULL;
else $Catalog->search_type = $Catalog->Form->GetRequestTxtData($_REQUEST['search_type'], 1);

if( !isset($_REQUEST['search_in_description']) ) $Catalog->search_in_description=NULL;
else $Catalog->search_in_description = $Catalog->Form->GetRequestTxtData($_REQUEST['search_in_description'], 1);

if( !isset($_REQUEST['inc_subcat']) ) $Catalog->inc_subcat=NULL;
else $Catalog->inc_subcat = $Catalog->Form->GetRequestTxtData($_REQUEST['inc_subcat'], 1);

if( !isset($_REQUEST['id_manufac']) ) $Catalog->id_manufac=NULL;
else $Catalog->id_manufac = $_REQUEST['id_manufac'];

$Catalog->row_sel_brand = array();
if(!empty($Catalog->id_manufac))$Catalog->row_sel_brand = explode(',',$Catalog->id_manufac);

if( !isset($_REQUEST['id_group']) ) $Catalog->id_group=NULL;
else $Catalog->id_group = $Catalog->Form->GetRequestNumData($_REQUEST['id_group']);

if( !isset($_REQUEST['img']) ) $Catalog->id_img=NULL;
else $Catalog->id_img = $Catalog->Form->GetRequestNumData($_REQUEST['img']);

if( !isset($_REQUEST['from']) ) $Catalog->from=NULL;
else $Catalog->from = $Catalog->Form->GetRequestNumData($_REQUEST['from']);

if( !isset($_REQUEST['to']) ) $Catalog->to=NULL;
else $Catalog->to= $Catalog->Form->GetRequestNumData($_REQUEST['to']);

if( !isset($_REQUEST['file']) ) $Catalog->id_file=NULL;
else $Catalog->id_file = $Catalog->Form->GetRequestNumData($_REQUEST['file']);

if( !isset( $_REQUEST['def_currency'] ) ) $Catalog->def_currency = NULL;
else $Catalog->def_currency = $Catalog->Form->GetRequestNumData($_REQUEST['def_currency']);

if( !isset($_REQUEST['asc_desc']) ){
    $Catalog->asc_desc='desc';
    $Catalog->srt = false;
}
else{
    $Catalog->srt = true;
    $Catalog->asc_desc = $Catalog->Form->GetRequestTxtData($_REQUEST['asc_desc'], 1);
}

if( !isset($_REQUEST['exist']) ) $Catalog->exist=NULL;
else $Catalog->exist = $Catalog->Form->GetRequestNumData($_REQUEST['exist']);

if( !isset($_REQUEST['id_param']) ) $Catalog->id_param=NULL;
else $Catalog->id_param = $Catalog->Form->GetRequestNumData($_REQUEST['id_param']);

if( !isset($_REQUEST['arr_params']) ) $Catalog->arr_params=NULL;
else $Catalog->arr_params =  $Catalog->Form->GetRequestTxtData($_REQUEST['arr_params'], 1);

if( !isset($_REQUEST['onlyFilter']) ) $Catalog->onlyFilter=0;
else $Catalog->onlyFilter =  $Catalog->Form->GetRequestTxtData($_REQUEST['onlyFilter'], 1);

if ( isset($val_categ) AND !empty($val_categ)  ) {
  $val_categ='$'.$val_categ.';';
  eval($val_categ);
}

$Catalog->SetMetaData();
$Catalog->lang_id = _LANG_ID;
$Catalog->sublevels = NULL;

if( !empty($Catalog->id_cat)){
  // get parameters influens on images for current position
  if ( is_array($_REQUEST) ) {
      foreach($_REQUEST as $key=>$value){
          if( strstr( $key,PARAM_VAR_NAME.PARAM_VAR_SEPARATOR) ){
              $par_tmp = explode(PARAM_VAR_SEPARATOR,$key);
              if(isset($par_tmp[1])){
                  // if parameter is multiselect then build array in array
                  if(isset($par_tmp[2])){
                    $Catalog->arr_current_img_params_value[$par_tmp[1]][$par_tmp[2]]=$value;
                  }
                  else {
                    $Catalog->arr_current_img_params_value[$par_tmp[1]]=$value;
                  }
              } //end if
          } //end if
      } // end foreach
  }// end if

//  echo '<br>$Catalog->arr_current_img_params_value='; print_r($Catalog->arr_current_img_params_value);
  if( empty($Catalog->id_img) ) $id_img = $Catalog->GetFirstImgOfProp($Catalog->id);
  else $id_img = $Catalog->id_img;

  // если параметры товара не передавлись через массив $_REQUEST, то
  // получаю значения параметров товара, которые зависят от изображения для текущего изображения
  if ( !isset($Catalog->arr_current_img_params_value) ) $Catalog->arr_current_img_params_value = $Catalog->GetParamsValuesOfPropForImg($id_img);
  //echo '<br>$Catalog->arr_current_img_params_value='; print_r($Catalog->arr_current_img_params_value);

  $Catalog->sublevels = $Catalog->GetSubLevelsLayout($Catalog->id_cat);
  $Catalog->subCategoriesLinks='';
  $Catalog->isParams  = $Catalog->IsParams($Catalog->id_cat);
  if ($Catalog->isParams )
        $Catalog->subCategoriesLinks = $Catalog->GetLinksToParamsNames($Catalog->id_cat,', ',false);
}

$Catalog->script=$_SERVER['REQUEST_URI'];
if(($Catalog->sort=='param' or $Catalog->sort=='price' or $Catalog->sort=='name') and $Catalog->task=='ajax_refresh_sort')
    $Catalog->script=$_SERVER['HTTP_REFERER'];

//$Catalog->script=$_SERVER['PHP_SELF']."?display=$Catalog->display&amp;start=$Catalog->start&amp;sort=$Catalog->sort&amp;task=$Catalog->task&amp;categ=$Catalog->id_cat&amp;curcod=$Catalog->id&amp;id_manufac=$Catalog->id_manufac";


if ( empty($Catalog->title) ) $title = $Page->FrontendPages->GetTitle(); //old version
//if ( empty($Catalog->title) ) $title = 'Купить '.$Catalog->tovarName.' в Украине - Ohrana.ua';
else $title = $Catalog->title;

if ( empty($Catalog->description) ) $Description = $Page->FrontendPages->GetDescription(); //old version
//if ( empty($Catalog->description) ) $Description = 'Купить '.$Catalog->tovarName.' в магазине «Ohrana.ua» с доставкой по Украине. '.$Catalog->tovarName.' - описание, фото, цены. Для заказа звоните ✆(050) 54-69-444!';
else $Description = $Catalog->description;

if ( empty($Catalog->keywords) ) $Keywords = $Page->FrontendPages->GetKeywords(); //old version
//if ( empty($Catalog->keywords) ) $Keywords = 'Купить '.$Catalog->tovarName.','.$Catalog->tovarName;
else $Keywords = $Catalog->keywords;

$Page->SetTitle( $title );
$Page->SetDescription( $Description );
$Page->SetKeywords( $Keywords );


if( strstr($Catalog->task,'ajax_refresh') ) $show_header_footer=false;
else $show_header_footer=true;

//Если таких фильтров не существует, то потом покажем 404-ю страницу.
if(count($Catalog->arr_current_img_params_value)>0){
    $params_exist = $Catalog->CheckExistOfParamsFilter($Catalog->arr_current_img_params_value);
}

//echo '<br>count($Catalog->arr_current_img_params_value)='.count($Catalog->arr_current_img_params_value).' $cnt_arr_items='.$cnt_arr_items.' is_array($arr_items)='.is_array($arr_items);
//=== set error page ====
//var_dump($_REQUEST);
//echo '$Catalog->id_cat='.$Catalog->id_cat;
//echo '$Catalog->treeCatData[$Catalog->id_cat]='.$Catalog->treeCatData[$Catalog->id_cat];
if (empty($Catalog->task)
    AND $show_header_footer == true
    AND (
        isset($_REQUEST['str_cat'])
        AND ( empty($Catalog->id_cat) OR !isset($Catalog->treeCatData[$Catalog->id_cat]) )
        OR ( isset($_REQUEST['str_id']) AND empty($Catalog->id))
        OR ( count($Catalog->arr_current_img_params_value)>0 AND !$params_exist )
        )
    ){
    $Page->Set_404();
    if(!isset($Catalog->treeCatData[$Catalog->id_cat])) $Catalog->id_cat=NULL;
}
//=======================

$Page->Catalog = &$Catalog;

$Catalog->PageUser=$Page;

//echo '<br> $search_keywords='.$search_keywords;
//echo '<br> $Catalog->task = '.$Catalog->task;
ob_start();
switch ($Catalog->task){
    case 'show_search_form':
        $Catalog->ShowSearchForm();
        break;
    case 'quick_search':
        ?>

        <div class="body">

         <?
         $Catalog->ShowListOfContentByPages($Catalog->QuickSearch($search_keywords, 'limit'));

         ?>
        </div>
        <?
    case 'add_to_cart':
            $curr_href = NULL;
            if( is_array($Catalog->arr_current_img_params_value)){
                foreach($Catalog->arr_current_img_params_value as $k=>$v){
                    if( is_array($v) ){
                        foreach($v as $k_tmp=>$v_tmp){
                            $curr_href = $curr_href.'&amp;'.PARAM_VAR_NAME.PARAM_VAR_SEPARATOR.$k.PARAM_VAR_SEPARATOR.$k_tmp.'='.$v_tmp;
                        }
                    }
                    else {
                        $curr_href = $curr_href.'&amp;'.PARAM_VAR_NAME.PARAM_VAR_SEPARATOR.$k.'='.$v;
                    }
                } // end foreach
            }
            $tmp_script=_LINK."order/task=add_to_cart&prod_id=$Catalog->id".$curr_href;
            echo '<br> $tmp_script='.$tmp_script;
            echo "<script>window.location.href='$tmp_script';</script>";
            //}
            break;
    case 'golink':
            $link = $Catalog->GetNumberName($Catalog->id);
            if( strpos($link, "/")==1 ) $link = substr($link, 2, strlen($link) );
            if( strstr($link, "http://") ) $link = substr($link, strpos($link, "http://")+7, strlen($link) );
            if( strstr($link, "www.") ) $link = substr($link, strpos($link, "www.")+4, strlen($link) );
            //echo '<br>$link='.$link;
            echo "<script>window.location.href='http://www.$link';</script>";
            break;
    case 'show_files':
            //$Catalog->ShowErr('Under Construction');
            //echo '$Catalog->Logon->user_id='.$Catalog->Logon->user_id.' $Catalog->id_file='.$Catalog->id_file.' $Catalog->id_cat='.$Catalog->id_cat.' $Catalog->id='.$Catalog->id;
            //if( !empty($Catalog->Logon->user_id) AND !empty($Catalog->id_file) ){
                $tmp = $Catalog->GetFileData($Catalog->id_file);
                $link = Catalog_Upload_Files_Path.'/'.$Catalog->id.'/'.$tmp['path'];
            //}
            //else {$link="#";}
            //echo '<br>$link='.$link;
            echo "<script>window.location.href='$link';</script>";
            break;
    case 'ajax_refresh_print_it':
            $Catalog->ShowPrintVersion();
            break;
    case 'ajax_refresh_sort':
            if($sort=='price' or $sort=='name'){
                $arr = $Catalog->GetListPositionsSortByDate($Catalog->id_cat, 'limit', $sort, $asc_desc);
                //echo'<br>Rows = '.$rows;
            }
            $Catalog->ShowListOfContentByPages($arr, NULL);
            echo '<script>makeRequest(\'/catalog.php\', \'task=ajax_refresh_panel&display='.$display.'&start='.$start.'&categ='.$Catalog->id_cat.'&sort='.$sort.'&id_param='.$Catalog->id_param.'&asc_desc='.$asc_desc.'\', \'param_panel\')</script>';
            break;
    case 'compare':
        $Page->showLeftBody = false;
        $name = $Catalog->multi['TXT_COMPARE_PROP_IN'].' '.$Catalog->categoryName;
        $Page->breadcrumb = $Catalog->ShowPathToLevel($Catalog->id_cat, NULL, 0).
            '&nbsp→&nbsp<span class="spanShareName">'.$name.'</span>';
        $Page->h1 = $name;
        $Page->showContent2Box = '';
        $Catalog->compareProp();
        break;
    case 'new':

        $Catalog->ShowContentCurentLevel('new');

        break;
    case 'hit':

        $Catalog->ShowContentCurentLevel('hit');

        break;
    case 'share':

        $Catalog->ShowContentCurentLevel('share');

        break;
    default:
        $Catalog->initParamFilter();
        //echo '$Catalog->onlyFilter='.$Catalog->onlyFilter;
        if($Catalog->onlyFilter==0){
            //$Catalog->ShowCatalogTree();
            //echo '<br>$Catalog->id='.$Catalog->id;
            if ( !empty($Catalog->id) ) {

                $Catalog->ShowDetailsCurrentPosition($Catalog->id_img);
            }
            else {
                //echo '<br/>$Catalog->id_cat: '.$Catalog->id_cat;
                if ( !empty($Catalog->id_cat) ){
                    $Catalog->ShowMainCategoriesImg(true,$Catalog->id_cat);
                    $Catalog->ShowContentCurentLevel();
                }else{
                   $Catalog->ShowMainCategoriesImg(true);
                }
            }
        }else{
            $Catalog->showFilter();
        }
        break;
}
$Page->content = ob_get_clean();
$Page->out();

//GetSubLevelsLayout
?>

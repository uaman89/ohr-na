<?php

/**
 * catalogcontent.backend.php
 * script for all actions with Catalog content
 * @package Catalog Package of SEOCMS
 * @author Igor Trokhymchuk  <ihor@seotm.com>
 * @version 1.1, 24.02.2011
 * @copyright (c) 2010+ by SEOTM
 */
if (!defined("SITE_PATH"))
    define("SITE_PATH", $_SERVER['DOCUMENT_ROOT']);
include_once(SITE_PATH . '/modules/mod_catalog/catalog.defines.php' );

if (!defined("_LANG_ID")) {
    $pg = check_init('PageAdmin', 'PageAdmin');
}

if (!isset($_REQUEST['module']))
    $module = NULL;
else
    $module = $_REQUEST['module'];

//Blocking to execute a script from outside (not Admin-part)
if (!$pg->logon->isAccessToScript($module))
    exit;

$Catalog = check_init('Catalog_content', 'Catalog_content', "'" . $pg->logon->user_id . "', '$module'");


if (!isset($_REQUEST['task']) || empty($_REQUEST['task']))
    $task = 'show';
else
    $task = $_REQUEST['task'];

if (isset($_REQUEST['saveimg']))
    $task = 'saveimg';
if (isset($_REQUEST['updimg']))
    $task = 'updimg';
if (isset($_REQUEST['delimg']))
    $task = 'delimg';
if (isset($_REQUEST['cancel']))
    $task = 'cancel';

if (isset($_REQUEST['savefiles']))
    $task = 'savefiles';
if (isset($_REQUEST['updfiles']))
    $task = 'updfiles';
if (isset($_REQUEST['delfiles']))
    $task = 'delfiles';

//echo '<br> $task='.$task;

if (!isset($_REQUEST['sort']))
    $sort = NULL;
else
    $sort = $_REQUEST['sort'];

if (!isset($_REQUEST['asc_desc']))
    $asc_desc = 'asc';
else
    $asc_desc = $_REQUEST['asc_desc'];

if (!isset($_REQUEST['start']))
    $start = 0;
else
    $start = $_REQUEST['start'];

if (!isset($_REQUEST['dontUseSizes']))
    $dontUseSizes = 0;
else
    $dontUseSizes = 1;

if (!isset($_REQUEST['dontUseSizes']))
    $dontUseSizesArr = 0;
else
    $dontUseSizesArr = $_REQUEST['dontUseSizes'];


if (!isset($_REQUEST['srch']))
    $srch = NULL;
else
    $srch = htmlspecialchars($_REQUEST['srch']);

if (!isset($_REQUEST['display']) AND empty($srch))
    $display = 50;
else {
    if ($task == 'show_srch_res' OR $task == 'new' OR $task == 'edit')
        $display = 50;
    elseif (!isset($_REQUEST['display']))
        $display = 50;
    else
        $display = $_REQUEST['display'];
}

if (!isset($_REQUEST['fln']))
    $fln = _LANG_ID;
else
    $fln = $_REQUEST['fln'];

if (!isset($_REQUEST['fltr']))
    $fltr = NULL;
else
    $fltr = $_REQUEST['fltr'];

if (!isset($_REQUEST['fltr2']))
    $fltr2 = NULL;
else
    $fltr2 = $_REQUEST['fltr2'];

if (!isset($_REQUEST['fltr3']))
    $fltr3 = NULL;
else
    $fltr3 = $_REQUEST['fltr3'];

if (!empty($fltr2)) { // $ltr2=categ=51 => $flrt2=51
    $arr_fltr2_tmp = explode("=", $fltr2);
    if (isset($arr_fltr2_tmp[1]))
        $fltr2 = $arr_fltr2_tmp[1];
}


// read the parent parameters
if (isset($_REQUEST['parent']))
    $parent = $_REQUEST['parent'];
else
    $parent = NULL;

if (isset($_REQUEST['parent_module']))
    $parent_module = $_REQUEST['parent_module'];
else
    $parent_module = NULL;

if (isset($_REQUEST['parent_id']))
    $parent_id = $_REQUEST['parent_id'];
else
    $parent_id = NULL;

if (isset($_REQUEST['parent_display']))
    $parent_display = $_REQUEST['parent_display'];
else
    $parent_display = NULL;

if (isset($_REQUEST['parent_start']))
    $parent_start = $_REQUEST['parent_start'];
else
    $parent_start = NULL;

if (isset($_REQUEST['parent_sort']))
    $parent_sort = $_REQUEST['parent_sort'];
else
    $parent_sort = NULL;

if (isset($_REQUEST['parent_task']))
    $parent_task = $_REQUEST['parent_task'];
else
    $parent_task = NULL;

if (isset($_REQUEST['parent_level']))
    $parent_level = $_REQUEST['parent_level'];
else
    $parent_level = NULL;

if (isset($_REQUEST['parent_fltr']))
    $parent_fltr = $_REQUEST['parent_fltr'];
else
    $parent_fltr = NULL;

if (isset($_REQUEST['parent_fln']))
    $parent_fln = $_REQUEST['parent_fln'];
else
    $parent_fln = NULL;


// read self parameters
if (!isset($_REQUEST['id']))
    $id = NULL;
else
    $id = $_REQUEST['id'];

if (!isset($_REQUEST['cod_pli']))
    $cod_pli = NULL;
else
    $cod_pli = $_REQUEST['cod_pli'];

if (!isset($_REQUEST['idProp']))
    $idProp = NULL;
else
    $idProp = $_REQUEST['idProp'];


if (!isset($_REQUEST['id_cat']))
    $id_cat = NULL;
else
    $id_cat = $_REQUEST['id_cat'];

if (!empty($id_cat)) { // $ltr2=categ=51 => $flrt2=51
    $arr_fltr2_tmp = explode("=", $id_cat);
    if (isset($arr_fltr2_tmp[1]))
        $id_cat = $arr_fltr2_tmp[1];
}


if (!isset($_REQUEST['new_id_cat']))
    $new_id_cat = NULL;
else
    $new_id_cat = $_REQUEST['new_id_cat'];


if (!empty($new_id_cat)) { // $ltr2=categ=51 => $flrt2=51
    $arr_fltr2_tmp = explode("=", $new_id_cat);
    if (isset($arr_fltr2_tmp[1]))
        $new_id_cat = $arr_fltr2_tmp[1];
}

if (!isset($_REQUEST['old_id_cat']))
    $old_id_cat = NULL;
else
    $old_id_cat = $_REQUEST['old_id_cat'];

if (!isset($_REQUEST['id_manufac']))
    $id_manufac = NULL;
else
    $id_manufac = $_REQUEST['id_manufac'];

if (!isset($_REQUEST['id_group']))
    $id_group = NULL;
else
    $id_group = $_REQUEST['id_group'];

if (!isset($_REQUEST['id_CountryManufac']))
    $id_CountryManufac = NULL;
else
    $id_CountryManufac = $_REQUEST['id_CountryManufac'];

if (!isset($_REQUEST['name']))
    $name = NULL;
else
    $name = $_REQUEST['name'];

if (!isset($_REQUEST['h1']))
    $h1 = NULL;
else
    $h1 = $_REQUEST['h1'];


if (!isset($_REQUEST['imagesOrder']))
    $imagesOrder = NULL;
else
    $imagesOrder = $_REQUEST['imagesOrder'];

if (!isset($_REQUEST['share_id']))
    $share_id = NULL;
else
    $share_id = $_REQUEST['share_id'];

if (!isset($_REQUEST['share']))
    $share = 0;
else
    $share = 1;

if (!isset($_REQUEST['short_descr']))
    $short_descr = NULL;
else
    $short_descr = $_REQUEST['short_descr'];

if (!isset($_REQUEST['full_descr']))
    $full_descr = NULL;
else
    $full_descr = $_REQUEST['full_descr'];

if (!isset($_REQUEST['specif']))
    $specif = NULL;
else
    $specif = $_REQUEST['specif'];

if (!isset($_REQUEST['reviews']))
    $reviews = NULL;
else
    $reviews = $_REQUEST['reviews'];

if (!isset($_REQUEST['support']))
    $support = NULL;
else
    $support = $_REQUEST['support'];

if (isset($_REQUEST['visible']))
    $visible = 2; //видимый
else
    $visible = 0;

if (isset($_REQUEST['exist']))
    $exist = 1;    //в наличии
else
    $exist = 2;

if (isset($_REQUEST['setPriceManually']))
    $setPriceManually = 1;    //в наличии
else
    $setPriceManually = 0;

if (isset($_REQUEST['new']))
    $new = 1;        //новинка
else
    $new = 0;

if (isset($_REQUEST['best']))
    $best = 1;      //лучший товар
else
    $best = 0;

if (isset($_REQUEST['shareprop']))
    $shareprop = 1;      //лучший товар
else
    $shareprop = 0;

if (!isset($_REQUEST['art_num']))
    $art_num = NULL;
else
    $art_num = $_REQUEST['art_num'];

if (!isset($_REQUEST['barcode']))
    $barcode = NULL;
else
    $barcode = $_REQUEST['barcode'];

if (!isset($_REQUEST['number_name']))
    $number_name = NULL;
else
    $number_name = $_REQUEST['number_name'];

if (!isset($_REQUEST['price']))
    $price = NULL;
else
    $price = $_REQUEST['price'];

if (!isset($_REQUEST['old_price']))
    $old_price = NULL;
else
    $old_price = $_REQUEST['old_price'];

if (!isset($_REQUEST['priceSize']))
    $priceSize = NULL;
else
    $priceSize = $_REQUEST['priceSize'];

if (!isset($_REQUEST['old_priceSize']))
    $old_priceSize = NULL;
else
    $old_priceSize = $_REQUEST['old_priceSize'];

if (!isset($_REQUEST['opt_price']))
    $opt_price = NULL;
else
    $opt_price = $_REQUEST['opt_price'];


//--------------------------------------



if (!isset($_REQUEST['oldprice']))
    $oldprice = NULL;
else
    $oldprice = $_REQUEST['oldprice'];


if (!isset($_REQUEST['group_price_currency']))
    $group_price_currency = NULL;
else
    $group_price_currency = $_REQUEST['group_price_currency'];

if (!isset($_REQUEST['priceforgroup']))
    $priceforgroup = NULL;
else
    $priceforgroup = $_REQUEST['priceforgroup'];




//--------------------------------------------



if (!isset($_REQUEST['grnt']))
    $grnt = NULL;
else
    $grnt = $_REQUEST['grnt'];

if (!isset($_REQUEST['dt']))
    $dt = date('Y-m-d');
else
    $dt = $_REQUEST['dt'];

if (!isset($_REQUEST['move']))
    $move = NULL;
else
    $move = $_REQUEST['move'];


if (!isset($_REQUEST['price_currency']))
    $price_currency = NULL;
else
    $price_currency = $_REQUEST['price_currency'];

if (!isset($_REQUEST['price_currencySize']))
    $price_currencySize = NULL;
else
    $price_currencySize = $_REQUEST['price_currencySize'];

if (!isset($_REQUEST['opt_price_currency']))
    $opt_price_currency = NULL;
else
    $opt_price_currency = $_REQUEST['opt_price_currency'];

//for price levels
if (!isset($_REQUEST['id_price_level']))
    $id_price_level = NULL;
else
    $id_price_level = $_REQUEST['id_price_level'];

if (!isset($_REQUEST['qnt_from']))
    $qnt_from = NULL;
else
    $qnt_from = $_REQUEST['qnt_from'];

if (!isset($_REQUEST['qnt_to']))
    $qnt_to = NULL;
else
    $qnt_to = $_REQUEST['qnt_to'];

if (!isset($_REQUEST['price_level']))
    $price_level = NULL;
else
    $price_level = $_REQUEST['price_level'];

if (!isset($_REQUEST['price_levels_currency']))
    $price_levels_currency = NULL;
else
    $price_levels_currency = $_REQUEST['price_levels_currency'];

//--- for images ---
if (!isset($_REQUEST['id_img']))
    $id_img = NULL;
else
    $id_img = $_REQUEST['id_img'];

if (!isset($_FILES["image"]))
    $image = NULL;
else
    $image = $_FILES["image"];

if (!isset($_REQUEST['img_title']))
    $img_title = NULL;
else
    $img_title = $_REQUEST['img_title'];

if (!isset($_REQUEST['img_descr']))
    $img_descr = NULL;
else
    $img_descr = $_REQUEST['img_descr'];

if (!isset($_REQUEST['arr_params']))
    $arr_params = NULL;
else
    $arr_params = $_REQUEST['arr_params'];

if (!isset($_REQUEST['id_img_show']))
    $id_img_show = NULL;
else
    $id_img_show = $_REQUEST['id_img_show'];


if (!isset($_REQUEST['id_prop1']))
    $id_prop1 = NULL;
else
    $id_prop1 = $_REQUEST['id_prop1'];

if (!isset($_REQUEST['arr_relat_prop']))
    $arr_relat_prop = NULL;
else
    $arr_relat_prop = $_REQUEST['arr_relat_prop'];
if (is_array($arr_relat_prop)) {
    for ($i = 0; $i < count($arr_relat_prop); $i++) {
        if (!empty($arr_relat_prop[$i])) { // $ltr2=categ=51 => $flrt2=51
            $arr_fltr2_tmp = explode("=", $arr_relat_prop[$i]);
            //echo  '<br>$arr_fltr2_tmp[0]='.$arr_fltr2_tmp[0].' $arr_fltr2_tmp[1]='.$arr_fltr2_tmp[1];
            if (isset($arr_fltr2_tmp[0]) AND $arr_fltr2_tmp[0] == 'curcod' AND isset($arr_fltr2_tmp[1]))
                $arr_relat_prop[$i] = $arr_fltr2_tmp[1];
            else
                $arr_relat_prop[$i] = NULL;
            //echo '<br>$arr_relat_prop[$i]='.$arr_relat_prop[$i];
        }
    }
}

if (!isset($_REQUEST['multi_categs']))
    $multi_categs = NULL;
else
    $multi_categs = $_REQUEST['multi_categs'];

if (!isset($_REQUEST['files_title']))
    $files_title = NULL;
else
    $files_title = $_REQUEST['files_title'];

if (!isset($_REQUEST['files_descr']))
    $files_descr = NULL;
else
    $files_descr = $_REQUEST['files_descr'];

if (!isset($_REQUEST['id_cat_move_from']))
    $id_cat_move_from = NULL;
else
    $id_cat_move_from = $_REQUEST['id_cat_move_from'];
if (!empty($id_cat_move_from)) { // $ltr2=categ=51 => $flrt2=51
    $arr_fltr2_tmp = explode("=", $id_cat_move_from);
    if (isset($arr_fltr2_tmp[1]))
        $id_cat_move_from = $arr_fltr2_tmp[1];
}

if (!isset($_REQUEST['id_cat_move_to']))
    $id_cat_move_to = NULL;
else
    $id_cat_move_to = $_REQUEST['id_cat_move_to'];
if (!empty($id_cat_move_to)) { // $ltr2=categ=51 => $flrt2=51
    $arr_fltr2_tmp = explode("=", $id_cat_move_to);
    if (isset($arr_fltr2_tmp[1]))
        $id_cat_move_to = $arr_fltr2_tmp[1];
}

if (!isset($_REQUEST['replace_to']))
    $replace_to = NULL;
else
    $replace_to = $_REQUEST['replace_to'];

//------ Meta data START -------
if (!isset($_REQUEST['mtitle']))
    $mtitle = NULL;
else
    $mtitle = $_REQUEST['mtitle'];

if (!isset($_REQUEST['mdescr']))
    $mdescr = NULL;
else
    $mdescr = $_REQUEST['mdescr'];

if (!isset($_REQUEST['mkeywords']))
    $mkeywords = NULL;
else
    $mkeywords = $_REQUEST['mkeywords'];
//------ Meta data END -------

if (!isset($_REQUEST['translit']))
    $translit = NULL;
else
    $translit = $_REQUEST['translit'];

if (!isset($_REQUEST['translit_old']))
    $translit_old = NULL;
else
    $translit_old = $_REQUEST['translit_old'];

if (!isset($_REQUEST['cnt_div']))
    $cnt_div = NULL;
else
    $cnt_div = $_REQUEST['cnt_div'];

if (!isset($_REQUEST['sostav']))
    $sostav = NULL;
else
    $sostav = $_REQUEST['sostav'];

if (!isset($_REQUEST['id_tag']))
    $id_tag = NULL;
else
    $id_tag = $_REQUEST['id_tag'];

if (!isset($_REQUEST['newColor']))
    $newColor = NULL;
else
    $newColor = $_REQUEST['newColor'];

if (!isset($_REQUEST['delColor']))
    $delColor = NULL;
else
    $delColor = $_REQUEST['delColor'];

if (!isset($_REQUEST['id_prop_copy']))
    $id_prop_copy = NULL;
else
    $id_prop_copy = $_REQUEST['id_prop_copy'];

if (!isset($_REQUEST['sizes']))
    $sizes = NULL;
else
    $sizes = $_REQUEST['sizes'];

if (!isset($_REQUEST['modelParam']))
    $modelParam = NULL;
else
    $modelParam = $_REQUEST['modelParam'];

if (!isset($_REQUEST['colorsStr']))
    $colorsStr = NULL;
else
    $colorsStr = $_REQUEST['colorsStr'];

if (!isset($_REQUEST['modelSize']))
    $modelSize = NULL;
else
    $modelSize = $_REQUEST['modelSize'];

if ($task == 'savereturn') {
    $task = 'save';
    $action = 'return';
}
else
    $action = NULL;

if (!isset($_REQUEST['new_name']))
    $new_name = NULL;
else
    $new_name = addslashes($_REQUEST['new_name']);

if (isset($_REQUEST['fltr_visible']))
    $Catalog->fltr_visible = 2; //видимый
else
    $Catalog->fltr_visible = 0;

if (isset($_REQUEST['fltr_exist']))
    $Catalog->fltr_exist = 1; //видимый
else
    $Catalog->fltr_exist = 0;

if (isset($_REQUEST['fltr_new']))
    $Catalog->fltr_new = 1; //видимый
else
    $Catalog->fltr_new = 0;

if (isset($_REQUEST['fltr_best']))
    $Catalog->fltr_best = 1; //видимый
else
    $Catalog->fltr_best = 0;

if (isset($_REQUEST['nameh']))
    $Catalog->nameh = $_REQUEST['nameh']; //видимый
else
    $Catalog->nameh = NULL;



//exit;

if (isset($_REQUEST['descrh']))
    $Catalog->descrh = $_REQUEST['descrh']; //видимый
else
    $Catalog->descrh = NULL;


//var_dump($Catalog->nameh);
//var_dump($Catalog->descrh);
//exit;
if( !isset($_REQUEST['countcomplect']) ) {
    $countcomplect = NULL;
}else{
    $countcomplect = $_REQUEST['countcomplect'];
}

$Catalog->task = $task;
$Catalog->display = $display;
$Catalog->sort = $sort;
$Catalog->asc_desc = $asc_desc;
$Catalog->start = $start;
$Catalog->fln = $fln;
$Catalog->srch = $srch;
$Catalog->fltr = $fltr;
$Catalog->fltr2 = $fltr2;
$Catalog->fltr3 = $fltr3;
$Catalog->share_id = $share_id;
$Catalog->share = $share;
$Catalog->sizes = $sizes;
$Catalog->old_price = $old_price;
$Catalog->dontUseSizes = $dontUseSizes;
$Catalog->dontUseSizesArr = $dontUseSizesArr;
$Catalog->setPriceManually = $setPriceManually;

$Catalog->priceSize = $priceSize;
$Catalog->price_currencySize = $price_currencySize;
$Catalog->old_priceSize = $old_priceSize;


$Catalog->oldprice = $oldprice;



$Catalog->parent = $parent;
$Catalog->sostav = $sostav;
$Catalog->modelParam = $modelParam;
$Catalog->modelSize = $modelSize;
$Catalog->parent_module = $parent_module;
$Catalog->parent_id = $parent_id;
$Catalog->parent_display = $parent_display;
$Catalog->parent_start = $parent_start;
$Catalog->parent_sort = $parent_sort;
$Catalog->id_CountryManufac = $id_CountryManufac;
$Catalog->parent_task = $parent_task;
$Catalog->parent_level = $parent_level;
$Catalog->parent_fltr = $parent_fltr;
$Catalog->parent_fln = $parent_fln;
$Catalog->imagesOrder = $imagesOrder;

//-----------------------






$Catalog->group_price_currency = $group_price_currency;
$Catalog->priceforgroup = $priceforgroup;

$Catalog->countcomplect = $countcomplect;
//-----------------

if ($Catalog->task == 'show')
    $Catalog->id = NULL;
else
    $Catalog->id = $id;

$Catalog->cod_pli = $cod_pli;

if (!empty($Catalog->fltr2))
    $Catalog->id_cat = $Catalog->fltr2;
else
    $Catalog->id_cat = $id_cat;

$Catalog->new_id_cat = $new_id_cat;
$Catalog->old_id_cat = $old_id_cat;

$Catalog->multi_categs = $multi_categs;

$Catalog->id_manufac = $id_manufac;
$Catalog->id_tag = $id_tag;
$Catalog->id_group = $id_group;
$Catalog->name = $name;
$Catalog->h1 = $h1;
$Catalog->new_name = $new_name;
$Catalog->img = $image;
$Catalog->short_descr = $short_descr;
$Catalog->full_descr = $full_descr;
$Catalog->specif = $specif;
$Catalog->reviews = $reviews;
$Catalog->support = $support;
$Catalog->exist = $exist;
$Catalog->new = $new;
$Catalog->best = $best;
$Catalog->shareprop = $shareprop;

$Catalog->number_name = $Catalog->Form->GetRequestTxtData($number_name, 1);
$Catalog->art_num = $Catalog->Form->GetRequestTxtData($art_num, 1);
$Catalog->barcode = $Catalog->Form->GetRequestTxtData($barcode, 1);
$Catalog->price = str_replace(',', '.', $Catalog->Form->GetRequestTxtData($price, 1));
$Catalog->opt_price = str_replace(',', '.', $Catalog->Form->GetRequestTxtData($opt_price, 1));
$Catalog->grnt = $Catalog->Form->GetRequestTxtData($grnt, 1);
$Catalog->dt = $Catalog->Form->GetRequestTxtData($dt, 1);
$Catalog->move = $move;
$Catalog->visible = $visible;
$Catalog->price_currency = $price_currency;
$Catalog->opt_price_currency = $opt_price_currency;

//for price level
$Catalog->id_price_level = $id_price_level; //for delete current line of price level
$Catalog->qnt_from = $qnt_from;
$Catalog->qnt_to = $qnt_to;
$Catalog->price_level = $price_level;
$Catalog->price_levels_currency = $price_levels_currency;

// for relations positions
$Catalog->id_prop1 = $id_prop1;
$Catalog->arr_relat_prop = $arr_relat_prop;

// for images
$Catalog->id_img = $id_img;
$Catalog->img_title = $img_title;
$Catalog->img_descr = $img_descr;
$Catalog->img_show = $id_img_show;

$Catalog->arr_params = $arr_params;
//print_r($Catalog->arr_params);

$Catalog->files_title = $files_title;
$Catalog->files_descr = $files_descr;

$Catalog->colorsStr = $colorsStr;

$Catalog->id_cat_move_from = $id_cat_move_from;
$Catalog->id_cat_move_to = $id_cat_move_to;

$Catalog->mtitle = $mtitle;
$Catalog->mdescr = $mdescr;
$Catalog->mkeywords = $mkeywords;

$Catalog->translit = $translit;
$Catalog->translit_old = $translit_old;

$Catalog->cnt_div = $cnt_div;
$Catalog->id_prop_copy = $id_prop_copy;

$Catalog->script_ajax = "module=$Catalog->module&start=$Catalog->start&sort=$Catalog->sort&fltr=$Catalog->fltr&fltr2=$Catalog->fltr2&srch=$Catalog->srch";
if ($Catalog->task == 'save')
    $Catalog->script_ajax .= "&display=" . ($Catalog->display + 1);
if ($Catalog->task != 'new_by_copy')
    $Catalog->script_ajax .= "&id=" . $Catalog->id;
$Catalog->script_ajax .= "&id_cat=$Catalog->id_cat&parent_module=$Catalog->parent_module&parent_id=$Catalog->parent_id&parent_display=$Catalog->parent_display&parent_start=$Catalog->parent_start&parent_sort=$Catalog->parent_sort&parent_task=$Catalog->parent_task&parent_level=$Catalog->parent_level";
$Catalog->script = "index.php?" . $Catalog->script_ajax;

if (isset($Catalog->settings['imgColors']) AND $Catalog->settings['imgColors'] == '1') {
    if (is_array($colorsStr))
        $colorsStr = implode(',', $colorsStr);
    $CatalogColors = check_init('CatalogColors', 'CatalogColors', "'" . $pg->logon->user_id . "', '$module','$id','$colorsStr','$id_cat'");
    $CatalogColors->newColor = $newColor;
    $CatalogColors->delColor = $delColor;
    $CatalogColors->price_currency = $price_currency;
    $CatalogColors->price = $price;
    $CatalogColors->old_price = $old_price;
    if (empty($Catalog->id) && empty($Catalog->id_prop_copy) && !empty($idProp))
        $Catalog->id = $idProp;
    $CatalogColors->script = $Catalog->script;
    $Catalog->CatalogColors = $CatalogColors;
}
//echo '<br> $Catalog->script='.$Catalog->script;
//echo '<br>$Catalog->id='.$Catalog->id;
//echo '<br> $Catalog->task='.$Catalog->task;

//echo '<h1>$Catalog->task: '.$Catalog->task."</h1>";
switch ($Catalog->task) {
    case 'colorsPositionDialogFinish':
        $CatalogColors->showImageDialog();
        break;
    case 'colorsPositionDialog':
        $CatalogColors->showColorsPositionDialog();
        break;
    case 'delColorsFromProp':
        $CatalogColors->DelColorsFromProp();
        break;
    case 'addColorToProp':
        $CatalogColors->addNewColorsToDatabase();
        break;
    case 'showImageDialog':
        $CatalogColors->ColorsDialog();
        break;
    case 'show':
        if ($Catalog->Right->IsRead()) {
            $Catalog->ShowContent();
        }
        break;
    case 'show_srch_res':
        $Catalog->ShowContentAll();
        break;
    case 'new_by_copy':
    case 'edit':
        //$starttime = microtime();
        if ($Catalog->Right->IsRead()) {
            if (!$Catalog->EditContent($id))
                echo "<script>window.location.href='$Catalog->script';</script>";
            //echo $Catalog->microtime_diff($starttime, microtime());
        }
        break;
    case 'new':
        if ($Catalog->Right->IsRead()) {
            $Catalog->EditContent(NULL);
        }
        break;
    case 'save':
        //phpinfo();

        if ($Catalog->Right->IsWrite() OR $Catalog->Right->IsUpdate()) {
            if ($Catalog->CheckContentFields($id) != NULL) {
                $Catalog->EditContent($id);
                return false;
            }
            $Catalog->SaveOrderImg();
            $res = $Catalog->SaveContent();//exit();

            if ($res) {
                if (isset($Catalog->settings['imgColors']) AND $Catalog->settings['imgColors'] == '1') {
                    //if make new position to catalog based on copy of existing one, then copy all colors from existing position to new one
                    if (!empty($Catalog->id_prop_copy))
                        $Catalog->CopyColorsToNewId($Catalog->id_prop_copy, $Catalog->id);
                }
                if (isset($Catalog->settings['img']) AND $Catalog->settings['img'] == '1') {
                    //if make new position to catalog based on copy of existing one, then copy all images from existing position to new one
                    if (!empty($Catalog->id_prop_copy))
                        $Catalog->CopyImagesToNewId($Catalog->id_prop_copy, $Catalog->id);
                    //upload new images

                    if (isset($_FILES["image"]["name"]) AND count($_FILES["image"]["name"]) > 0) {
                        if ($Catalog->CheckImages() != NULL) {
                            $Catalog->EditContent();
                            return false;
                        }
                        if ($Catalog->SavePicture() != NULL) {
                            $Catalog->EditContent($id);
                            return false;
                        }
                    }
                }
                if (isset($Catalog->settings['sizes']) AND $Catalog->settings['sizes'] == '1') {
                    //if make new position to catalog based on copy of existing one, then copy all sizes from existing position to new one
                    if (!empty($Catalog->id_prop_copy))
                        $Catalog->CopySizesToNewId($Catalog->id_prop_copy, $Catalog->id);
                }
                if (isset($Catalog->settings['files']) AND $Catalog->settings['files'] == '1') {
                    //if make new position to catalog based on copy of existing one, then copy all files from existing position to new one
                    if (!empty($Catalog->id_prop_copy))
                        $Catalog->CopyFilesToNewId($Catalog->id_prop_copy, $Catalog->id);
                    //upload new files
                    if (isset($_FILES["files"]["name"]) AND count($_FILES["files"]["name"]) > 0) {
                        if ($Catalog->SaveFiles() != NULL) {
                            $Catalog->EditContent($id);
                            return false;
                        }
                    }
                }
                if (isset($Catalog->settings['relat_prop']) AND $Catalog->settings['relat_prop'] == '1') {
                    //if make new position to catalog based on copy of existing one, then copy all relat props from existing position to new one
                    if (!empty($Catalog->id_prop_copy))
                        $Catalog->CopyRelatPropToNewId($Catalog->id_prop_copy, $Catalog->id);
                }
                //$Catalog->ShowContent();

                if ($action == 'return'){
                    echo "<script>window.location.href='" . $Catalog->script . "&task=edit&id=$Catalog->id';</script>";
                }
                else{
                    echo "<script>window.location.href='" . $Catalog->script . "';</script>";
                }
            }
            else
                echo '<br>' . $pg->Msg->show_text('MSG_ERR_NOT_SAVE');
        }
        break;
    case 'delete':
        if ($Catalog->Right->IsDelete()) {
            if (!isset($_REQUEST['id_del']))
                $id_del = NULL;
            else
                $id_del = $_REQUEST['id_del'];
            if (!empty($id_del)) {
                $del = $Catalog->DelContent($id_del);
                if (!$del)
                    $pg->Msg->show_msg('_ERROR_DELETE');

                //if ( $del > 0 ) echo "<script>window.alert('".$pg->Msg->get_msg('_SYS_DELETED_OK')." $del');</script>";
                //else $pg->Msg->show_msg('_ERROR_DELETE');
            }
            else
                $pg->Msg->show_msg('_ERROR_SELECT_FOR_DEL');
            //$Catalog->ShowContent();

            echo "<script>window.location.href='$Catalog->script';</script>";
        }
        break;
    case 'cancel':
        echo "<script>window.location.href='$Catalog->script';</script>";
        break;
    case 'show_move_to_category_all':
        $del = $Catalog->ShowMoveToCategoryForm(NULL);
        break;
    case 'show_move_to_category':
        if (!isset($_REQUEST['id_del']))
            $id_del = NULL;
        else
            $id_del = $_REQUEST['id_del'];
        if (!empty($id_del)) {
            $del = $Catalog->ShowMoveToCategoryForm($id_del);
        } else {
            $pg->Msg->show_msg('_ERROR_SELECT_FOR_MOVE');
            $Catalog->ShowContent();
        }
        break;
    case 'move_to_category':
        if (!isset($_REQUEST['id_del']))
            $id_del = NULL;
        else
            $id_del = $_REQUEST['id_del'];
        if (!empty($Catalog->id_cat_move_from) AND !empty($Catalog->id_cat_move_to)) {
            $res = $Catalog->MoveToCategory($id_del);
            if ($res) {
                echo "<script>window.alert('" . $pg->Msg->get_msg('TXT_MOVE_OK', TblModCatalogSprTxt) . " $Catalog->del');</script>";
                $id_del = NULL;
            } else {
                $pg->Msg->show_msg('MSG_ERR_', TblModCatalogSprTxt);
            }
        } else {
            if (empty($Catalog->id_cat_move_from))
                $pg->Msg->show_msg('ERR_EMPTY_CATEGORY_MOVE_FROM', TblModCatalogSprTxt);
            if (empty($Catalog->id_cat_move_to))
                $pg->Msg->show_msg('ERR_EMPTY_CATEGORY_MOVE_TO', TblModCatalogSprTxt);
        }
        $Catalog->ShowMoveToCategoryForm($id_del);
        break;

    case 'up':
        //phpinfo();
        //echo '<br>$Catalog->id_cat='.$Catalog->id_cat;
        $Catalog->up(TblModCatalogProp, 'id_cat', $Catalog->id_cat);
        $Catalog->ShowContentHTML();
        //echo "<script>window.location.href='$Catalog->script';</script>";
        break;
    case 'down':
        $Catalog->down(TblModCatalogProp, 'id_cat', $Catalog->id_cat);
        $Catalog->ShowContentHTML();
        //echo "<script>window.location.href='$Catalog->script';</script>";
        break;
    case 'replace':
        $Catalog->Form->Replace(TblModCatalogProp, 'move', $Catalog->id, $replace_to);
        $Catalog->ShowContentHTML();
        break;

    case 'del_thumbs':
        //echo '<br>$Catalog->id_cat='.$Catalog->id_cat;
        $del = $Catalog->DelThumbs($Catalog->id_cat);
        echo "<script>window.alert('" . $Catalog->Msg->show_text('MSG_THUMBS_DELETED_OK') . " $del');</script>";
        //$Catalog->ShowContent();
        echo "<script>window.location.href='$Catalog->script';</script>";
        break;
    case 'add_html_price_level':
        //phpinfo();
        $Catalog->AddHTMLPriceLevel();
        break;
    case 'del_html_price_level':
        $Catalog->DelCurrentPriceLevel();
        $Catalog->AddHTMLPriceLevel();
        break;
    case 'change_visible':
        if (!isset($_REQUEST['new_visible']))
            $Catalog->new_visible = NULL;
        else
            $Catalog->new_visible = $Catalog->Form->GetRequestNumData($_REQUEST['new_visible']);
        $Catalog->ChangeVisibleProp($Catalog->id, $Catalog->new_visible, 'visible');
        $Catalog->ShowVisibility($Catalog->id, $Catalog->new_visible);
        break;
    case 'changeExist':
        if (!isset($_REQUEST['new_visible']))
            $Catalog->new_visible = NULL;
        else
            $Catalog->new_visible = $Catalog->Form->GetRequestNumData($_REQUEST['new_visible']);
        $Catalog->ChangeNewProp($Catalog->id, $Catalog->new_visible, 'exist');
        $Catalog->ShowStatusPerekluchatel($Catalog->id,"Exist", $Catalog->new_visible,'changeExist');
        break;
        case 'changeNew':
        if (!isset($_REQUEST['new_visible']))
            $Catalog->new_visible = NULL;
        else
            $Catalog->new_visible = $Catalog->Form->GetRequestNumData($_REQUEST['new_visible']);
        $Catalog->ChangeNewProp($Catalog->id, $Catalog->new_visible, 'new');
        $Catalog->ShowStatusPerekluchatel($Catalog->id,"New", $Catalog->new_visible,'changeNew');
        break;
    case 'changeBest':
        if (!isset($_REQUEST['new_visible']))
            $Catalog->new_visible = NULL;
        else
            $Catalog->new_visible = $Catalog->Form->GetRequestNumData($_REQUEST['new_visible']);
        $Catalog->ChangeNewProp($Catalog->id, $Catalog->new_visible, 'best');
        $Catalog->ShowStatusPerekluchatel($Catalog->id,"Best", $Catalog->new_visible,'changeBest');
        break;
        case 'savename':
        //echo '<br>$Catalog->id='.$Catalog->id.' $Catalog->lang_id='.$Catalog->lang_id.' $Catalog->new_name='.$Catalog->new_name;
        $res = $Catalog->Spr->SaveToSpr(TblModCatalogPropSprName, $Catalog->id, $Catalog->lang_id, $Catalog->new_name);
        if ($res)
            echo $Catalog->multi['_OK_SAVE'];
        else
            echo $Catalog->multi['_NOT_SAVE'];
        break;
    case 'change_exist':
        if (!isset($_REQUEST['new_exist']))
            $Catalog->new_exist = NULL;
        else
            $Catalog->new_exist = $Catalog->Form->GetRequestNumData($_REQUEST['new_exist']);
        $Catalog->ChangeExistProp($Catalog->id, $Catalog->new_exist);
        $Catalog->ShowExistProp($Catalog->id, $Catalog->new_exist);
        break;
    case 'saveprice':
        if (!isset($_REQUEST['propprice'][$Catalog->id]))
            $Catalog->price = NULL;
        else
            $Catalog->price = str_replace(',', '.', $Catalog->Form->GetRequestTxtData($_REQUEST['propprice'][$Catalog->id], 1));
        //echo '<br>$Catalog->price=' . $Catalog->price;
        if (!isset($_REQUEST['propprice_currency'][$Catalog->id]))
            $Catalog->price_currency = NULL;
        else
            $Catalog->price_currency = str_replace(',', '.', $Catalog->Form->GetRequestNumData($_REQUEST['propprice_currency'][$Catalog->id], 1));
        $res = $Catalog->SavePrice($Catalog->id, $Catalog->price, $Catalog->price_currency);
        if ($res)
            echo $Catalog->multi['_OK_SAVE'];
        else
            echo $Catalog->multi['_NOT_SAVE'];
        break;
    case 'savepriceopt':
        if (!isset($_REQUEST['proppriceopt'][$Catalog->id]))
            $Catalog->price = NULL;
        else
            $Catalog->price = str_replace(',', '.', $Catalog->Form->GetRequestTxtData($_REQUEST['proppriceopt'][$Catalog->id], 1));
        if (!isset($_REQUEST['proppriceopt_currency'][$Catalog->id]))
            $Catalog->price_currency = NULL;
        else
            $Catalog->price_currency = str_replace(',', '.', $Catalog->Form->GetRequestNumData($_REQUEST['proppriceopt_currency'][$Catalog->id], 1));
        $res = $Catalog->SavePriceOpt($Catalog->id, $Catalog->price, $Catalog->price_currency);
        if ($res)
            echo $Catalog->multi['_OK_SAVE'];
        else
            echo $Catalog->multi['_NOT_SAVE'];
        break;


    //---------- images start -----------------
    case 'showpicture':
        $Catalog->ShowPicture();
        break;
    case 'saveimg':
        if ($Catalog->CheckImages() != NULL) {
            $Catalog->ShowPicture();
            return false;
        }
        if ($Catalog->SavePicture() != NULL) {
            $Catalog->ShowPicture();
            return false;
        }
        else
            echo "<script>window.location.href='$Catalog->script&task=showpicture&colorsStr=$Catalog->colorsStr';</script>";
        break;
    case 'updimg':
        if ($Catalog->UpdatePicture() != NULL) {
            $Catalog->ShowPicture();
            return false;
        } else {
            //$Catalog->ShowPicture();
            echo "<script>window.location.href='$Catalog->script';</script>";
        }
        break;
    case 'delimg':
        if (!isset($_REQUEST['id_img_del']))
            $id_img_del = NULL;
        else
            $id_img_del = $_REQUEST['id_img_del'];
        if (!empty($id_img_del)) {
            $del = $Catalog->DelPicture($id_img_del, $Catalog->id);
            if ($del > 0)
                echo "<script>window.alert('" . $pg->Msg->get_msg('_SYS_DELETED_OK') . " $del');</script>";
            else
                $pg->Msg->show_msg('_ERROR_DELETE');
        }
        else
            $pg->Msg->show_msg('_ERROR_SELECT_FOR_DEL');
        echo "<script>window.location.href='$Catalog->script&task=showpicture&colorsStr=$Catalog->colorsStr';</script>";
        break;
    case 'up_img':
        $Catalog->up(TblModCatalogPropImg, 'id_prop', $Catalog->id);
        echo "<script>window.location.href='$Catalog->script&task=showpicture&colorsStr=$Catalog->colorsStr';</script>";
        break;
    case 'down_img':
        $Catalog->down(TblModCatalogPropImg, 'id_prop', $Catalog->id);
        echo "<script>window.location.href='$Catalog->script&task=showpicture&colorsStr=$Catalog->colorsStr';</script>";
        break;
    //---------- images end -----------------
    //---------- files start -----------------
    case 'showfiles':
        $Catalog->ShowFiles();
        break;
    case 'savefiles':
        if ($Catalog->SaveFiles() != NULL) {
            $Catalog->ShowFiles();
            return false;
        }
        else
            echo "<script>window.location.href='$Catalog->script&task=showfiles';</script>";
        break;
    case 'updfiles':
        if ($Catalog->UpdateFiles() != NULL) {
            $Catalog->ShowFiles();
            return false;
        }
        else
            echo "<script>window.location.href='$Catalog->script';</script>";
        break;
    case 'delfiles':
        if (!isset($_REQUEST['id_img_del']))
            $id_img_del = NULL;
        else
            $id_img_del = $_REQUEST['id_img_del'];
        if (!empty($id_img_del)) {
            $del = $Catalog->DelFiles($id_img_del, $Catalog->id);
            if ($del > 0)
                echo "<script>window.alert('" . $pg->Msg->get_msg('_SYS_DELETED_OK') . " $del');</script>";
            else
                $pg->Msg->show_msg('_ERROR_DELETE');
        }
        else
            $pg->Msg->show_msg('_ERROR_SELECT_FOR_DEL');
        echo "<script>window.location.href='$Catalog->script&task=showfiles';</script>";
        break;
    case 'up_files':
        $Catalog->up(TblModCatalogPropFiles, 'id_prop', $Catalog->id);
        echo "<script>window.location.href='$Catalog->script&task=showfiles';</script>";
        break;
    case 'down_files':
        $Catalog->down(TblModCatalogPropFiles, 'id_prop', $Catalog->id);
        echo "<script>window.location.href='$Catalog->script&task=showfiles';</script>";
        break;
    //---------- files end -----------------
    //============ RELATIONS BETWEEN POSITIONS OF THE CATALOGUE START ===============
    case 'control_relat_prop_form':
        $Catalog->ShowControlRelatPropForm();
        break;
    case 'add_relat_prop':
        $Catalog->AddRelatProp();
        $Catalog->ShowControlRelatPropForm();
        break;
    case 'del_relat_prop':
        if (!isset($_REQUEST['id_del']))
            $id_del = NULL;
        else
            $id_del = $_REQUEST['id_del'];
        //echo '<br>$id_del[1]='.$id_del[1];
        if (!empty($id_del)) {
            $del = $Catalog->DelRelatProp($id_del);
            if ($del == 0)
                $pg->Msg->show_msg('_ERROR_DELETE');
        }
        else
            $pg->Msg->show_msg('_ERROR_SELECT_FOR_DEL');
        $Catalog->ShowControlRelatPropForm();
        break;
    case 'up_relat_prop':
        $Catalog->up_relat_prop(TblModCatalogPropRelat);
        $Catalog->ShowControlRelatPropForm();
        break;
    case 'down_relat_prop':
        $Catalog->down_relat_prop(TblModCatalogPropRelat);
        $Catalog->ShowControlRelatPropForm();
        break;
    //============ RELATIONS BETWEEN POSITIONS OF THE CATALOGUE END ===============
}
?>

<?
/**
* catalog_ImpExp.php
* script for all actions with Catalog Import/Export
* @package Catalog Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 24.02.2011
* @copyright (c) 2010+ by SEOTM
*/
include_once( $_SERVER['DOCUMENT_ROOT'].'/modules/mod_catalog/catalog.defines.php' );

if(!defined("_LANG_ID")) {session_start(); $pg = new PageAdmin();}

if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];

//Blocking to execute a script from outside (not Admin-part)
if ( !$pg->logon->isAccessToScript($module)) exit;

if( !isset($_REQUEST['task']) || empty($_REQUEST['task']) ) $task='show';
else $task=$_REQUEST['task'];

if(!isset($_REQUEST['insert_lang_id'])) $insert_lang_id=3;
else $insert_lang_id=$_REQUEST['insert_lang_id'];

if(!isset($_REQUEST['from_charset'])) $from_charset="cp1251";
else $from_charset=$_REQUEST['from_charset'];

if( !isset( $_REQUEST['id_cat'] ) ) $id_cat = NULL;
else $id_cat = Form::GetRequestTxtData($_REQUEST['id_cat'], 1);

$CatalogImpExp = new CatalogImpExp($pg->logon->user_id, $module);
$CatalogImpExp->module = $module;
$CatalogImpExp->task = $task;
$CatalogImpExp->insert_lang_id = $insert_lang_id;
$CatalogImpExp->id_cat = $id_cat;
$CatalogImpExp->from_charset = $from_charset;
$CatalogImpExp->to_charset = "utf-8";

switch($CatalogImpExp->task)
{
    case 'show':           // Вывод формы управления экспортом/импортом по умолчанию
       $CatalogImpExp->Form();
       break;
    case 'import_csv':     // Создание и обновление категорий, товаров из .csv-файла из программы E-Trade PriceList Importer
          if( !isset($_FILES['filename']) )
             $filename = NULL;
         else $filename = $_FILES['filename']["name"];
         if($filename =='' or $filename == null ) {
           echo "Укажите файл для импорта !<br>";
           $CatalogImpExp->Form();
           return false;
         }
         $CatalogImpExp->path = $filename;
         $ext = explode('.', $filename);
         $ext =  strtolower($ext[count($ext)-1]);
         $filesExt = array('csv');
         if (in_array($ext, $filesExt)) {
             $uploaddir = $_SERVER['DOCUMENT_ROOT'].'/import/catalog/';
              if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777);
             else @chmod($uploaddir,0777);
             $uploaddir = $uploaddir.$CatalogImpExp->path;
             //echo '<Br>$uploaddir='.$uploaddir.' $_FILES["filename"]["tmp_name"]='.$_FILES["filename"]["tmp_name"];
             if ( !copy( $_FILES["filename"]["tmp_name"], $uploaddir) ) {
                 echo "Ошибка копирования файла<br>";
                 echo '<a href="#" onClick="history.go(-1);">Назад</a>';
                 return false;
             }
             else{
                 $old_max_exec_time = get_cfg_var("max_execution_time");
                 ini_set("max_execution_time","9999");
                 $CatalogImpExp->CSVtoArr($uploaddir);
                 ini_set("max_execution_time",$old_max_exec_time);
                  $CatalogImpExp->Form();
                 unlink($uploaddir);
             }
             @chmod($uploaddir,0755);
           }
           else {
               echo 'Неверный тип файла: '.$filename.'<br/> Необходимо выбрать файл формата .csv !<br>';
               $CatalogImpExp->Form();
               return false;
           }
          break;
    case 'import_images':
        $CatalogImpExp->import_images();
        $CatalogImpExp->repairImagesMove();
        break;

    case 'update_price_cnt_csv':  // Обновление цены товаров из .csv-файла
          if( !isset($_FILES['filename2']) ) $filename = NULL;
         else $filename = $_FILES['filename2']["name"];

         $CatalogImpExp->xml_file = $filename;
         $CatalogImpExp->path = $filename;
         $uploaddir = $_SERVER['DOCUMENT_ROOT'].'/import/catalog/';
          if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777);
         else @chmod($uploaddir,0777);
         $uploaddir = $uploaddir.$CatalogImpExp->xml_file;
         //echo '<Br>$uploaddir='.$uploaddir.' $_FILES["filename"]["tmp_name"]='.$_FILES["filename"]["tmp_name"];
         if ( !copy( $_FILES["filename2"]["tmp_name"], $uploaddir) ) {
             echo "Ошибка копирования файла<br>";
             echo '<a href="#" onClick="history.go(-1);">Назад</a>';
             return false;
         }
         else{
             $old_max_exec_time = get_cfg_var("max_execution_time");
             ini_set("max_execution_time","9999");
             $CatalogImpExp->UpdatePriceCount($uploaddir);
             ini_set("max_execution_time",$old_max_exec_time);
             $CatalogImpExp->Form();
             unlink($uploaddir);
         }
         @chmod($uploaddir,0755);
         break;

    case 'set_cod_pli':          // Используется единоразово только при создании COD_PLI, посе запуска можно отключить.
           $CatalogImpExp->Generate_Description();
           $CatalogImpExp->Generate_PLI();
           $CatalogImpExp->Form();
           break;

    case 'export_to_csv':     // Экспорт каталога для программы E-Trade PriceList Importer
       $CatalogImpExp->from_charset = "utf-8";
       $CatalogImpExp->to_charset = "windows-1251";
       $CatalogImpExp->ExportCatalogToCSV();
       break;

   case 'export_to_xml':   // Экспорт каталога  в Excel_XML
       $CatalogImpExp->from_charset = "utf-8";
       $CatalogImpExp->to_charset = "utf-8";
       $CatalogImpExp->ExportCatalogToExcelXML();
       break;

    case 'export_price':       // Экспорт каталога  в Excel  (не используется)
       $CatalogImpExp->ExportPriceToExcel();
       break;
}
?>
<?php
/**
* catalog_pricelist.php  
* script for all actions with Catalog Price List
* @package Catalog Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 24.02.2011
* @copyright (c) 2010+ by SEOTM
*/
include_once( $_SERVER['DOCUMENT_ROOT'].'/modules/mod_catalog/catalog.defines.php' );

if(!defined("_LANG_ID")) {$pg = new PageAdmin();} 

if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];

//Blocking to execute a script from outside (not Admin-part) 
if ( !$pg->logon->isAccessToScript($module)) exit;

if( !isset($_REQUEST['task']) || empty($_REQUEST['task']) ) $task=NULL;
else $task=$_REQUEST['task'];

if( !isset( $_REQUEST['fln'] ) ) $fln = _LANG_ID;
else $fln = $_REQUEST['fln'];

if( !isset( $_REQUEST['fltr'] ) ) $fltr = NULL;
else $fltr = $_REQUEST['fltr'];

if( !isset( $_REQUEST['srch'] ) ) $srch = NULL;

if(!isset($_REQUEST['sort'])) $sort=NULL;
else $sort=$_REQUEST['sort'];

if(!isset($_REQUEST['start'])) $start=0;
else $start=$_REQUEST['start'];

if(!isset($_REQUEST['display']) || empty($srch) ) $display=20;
else $display=$_REQUEST['display'];

if(!isset($_REQUEST['file_n'])) $file_n=NULL;
else $file_n=$_REQUEST['file_n'];


$Pricelist = new Price($pg->logon->user_id, $display, $module, $sort, $start);

$Pricelist->user_id = $pg->logon->user_id;
$Pricelist->module = $module;
$Pricelist->display = $display;
$Pricelist->sort = $sort;
$Pricelist->start = $start;
$Pricelist->fln = $fln;
$Pricelist->srch = $srch;
$Pricelist->fltr = $fltr;
$Pricelist->file_n = $file_n;
 
switch( $task )
{
	case 'show':		$Pricelist	->show();
						break;
						
	case 'save':		//echo "zkjhfdksdjfhdsfkjgh";
					$Pricelist->save();
						break;
						
	case 'del':		$Pricelist->del();
						break;
						
	default:			$Pricelist->show();
						break;
}
?>
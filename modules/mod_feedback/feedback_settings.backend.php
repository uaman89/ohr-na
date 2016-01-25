<?php
/* feedback_settings.backend.php */
/**
 * feedback_settings.backend.php
 * script for all actions with FeedBack Settings
 * @package FeedBack Package of SEOCMS
 * @author Bogdan Iglinsky  <bi@seotm.com>
 * @version 1.0, 17.06.2012
 * @copyright (c) 2013+ by SEOTM
 */
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( $_SERVER['DOCUMENT_ROOT'].'/modules/mod_catalog/catalog.defines.php' );

if(!defined("_LANG_ID")) {$pg = new PageAdmin();}

if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];

//Blocking to execute a script from outside (not Admin-part)
if ( !$pg->logon->isAccessToScript($module)) exit;

$FeedBack = check_init('FeedBack_settings', 'FeedBack_settings', "'".$pg->logon->user_id."', '".$module."'");

if( !isset($_REQUEST['task']) || empty($_REQUEST['task']) ) $FeedBack->task='show';
else $FeedBack->task=$_REQUEST['task'];

if(isset($_REQUEST['is_send_ajax'])) $FeedBack->is_send_ajax = $_REQUEST['is_send_ajax'];
else $FeedBack->is_send_ajax = 0;

if(isset($_REQUEST['is_captcha'])) $FeedBack->is_captcha = $_REQUEST['is_captcha'];
else $FeedBack->is_captcha = 0;

if(isset($_REQUEST['is_files'])) $FeedBack->is_files = $_REQUEST['is_files'];
else $FeedBack->is_files = 0;

if(isset($_REQUEST['files_path'])) $FeedBack->files_path = $_REQUEST['files_path'];
else $FeedBack->files_path = FeedbackUploadFilesPath;

if(isset($_REQUEST['is_phone'])) $FeedBack->is_phone = $_REQUEST['is_phone'];
else $FeedBack->is_phone = 0;

if(isset($_REQUEST['is_email'])) $FeedBack->is_email = $_REQUEST['is_email'];
else $FeedBack->is_email = 0;

if(isset($_REQUEST['is_fax'])) $FeedBack->is_fax = $_REQUEST['is_fax'];
else $FeedBack->is_fax = 0;

if(isset($_REQUEST['is_surname'])) $FeedBack->is_surname = $_REQUEST['is_surname'];
else $FeedBack->is_surname = 0;

if(isset($_REQUEST['is_place_label'])) $FeedBack->is_place_label = $_REQUEST['is_place_label'];
else $FeedBack->is_place_label = 0;

switch( $FeedBack->task ) {
    case 'show':
        $FeedBack->ShowSettings();
        break;
    case 'save':
        if ( $FeedBack->SaveSettings() ) echo 'save ok!';
        else echo 'not save!';
        $FeedBack->ShowSettings();
        break;
}
?>
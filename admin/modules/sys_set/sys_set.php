<?php
/**
* sys_set.php
* script for all actions with system settings
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/admin/include/defines.inc.php' );
include_once( SITE_PATH.'/admin/modules/sys_set/sys_set.class.php' );
include_once( SITE_PATH.'/admin/include/defines.inc.php' );

 $module = AntiHacker::AntiHackRequest('module');
//============================================================================================
// START
// Blocking to execute a script from outside (not Admin-part)
//============================================================================================
$goto = "http://".NAME_SERVER."/admin/index.php?logout=1";
//echo '<br>$goto='.$goto;
if ( ! defined('BASEPATH')) {
	 //$Msg->show_msg( '_NOT_AUTH' );
	 //return false;
	 ?><script>window.location.href="<?=$goto?>";</script><?;
}
$logon =check_init('logon','Authorization');
/*if (!$logon->LoginCheck()) {
	//return false;
	?><script>window.location.href="<?=$goto?>";</script><?;
}*/
if ( ! defined('BASEPATH')) {
	//return false;
	?><script>window.location.href="<?=$goto?>";</script><?;
//    exit('No direct script access allowed');
}
//echo '<br /><br /><br /><br /><br /><br /><br />';
//=============================================================================================
// END
//=============================================================================================

$Obj = new SysSettingsAdm($logon->user_id, $module);

$Obj->task = AntiHacker::AntiHackRequest('task','show');
$Obj->fln = AntiHacker::AntiHackRequest('fln',_LANG_ID);
$Obj->fltr = AntiHacker::AntiHackRequest('fltr');
$Obj->fltr2 = AntiHacker::AntiHackRequest('fltr2');
$Obj->fltr3 = AntiHacker::AntiHackRequest('fltr3');
$Obj->srch = AntiHacker::AntiHackRequest('srch');
$Obj->sort = AntiHacker::AntiHackRequest('sort');
$Obj->asc_desc = AntiHacker::AntiHackRequest('asc_desc','asc');
$Obj->start = AntiHacker::AntiHackRequest('start',0);
$Obj->display = AntiHacker::AntiHackRequest('display',20);

// read self parameters
if( !isset($_REQUEST['mail_smtp_auth']) ) $Obj->mail_smtp_auth=0;
else $Obj->mail_smtp_auth = 1;
if( !isset($_REQUEST['mail_is_html']) ) $Obj->mail_is_html=0;
else $Obj->mail_is_html = 1;

$Obj->id = AntiHacker::AntiHackRequest('id');
$Obj->mail_host = AntiHacker::AntiHackRequest('mail_host');
$Obj->mail_port = AntiHacker::AntiHackRequest('mail_port');
$Obj->mail_mailer = AntiHacker::AntiHackRequest('mail_mailer');
$Obj->mail_username = AntiHacker::AntiHackRequest('mail_username');
$Obj->mail_password = AntiHacker::AntiHackRequestPass('mail_password');
$Obj->mail_from = AntiHacker::AntiHackRequest('mail_from');
$Obj->mail_from_name = AntiHacker::AntiHackArrayRequest('mail_from_name');

$Obj->mail_header = AntiHacker::AntiHackArrayRequest('mail_header');
if (is_array($Obj->mail_header) )
    foreach ( $Obj->mail_header as &$val) $val = addslashes($val);
$Obj->mail_footer = AntiHacker::AntiHackArrayRequest('mail_footer');
if (is_array($Obj->mail_footer) )
    foreach ( $Obj->mail_footer as &$val) $val = addslashes($val);

$Obj->mail_word_wrap = AntiHacker::AntiHackRequest('mail_word_wrap');
$Obj->mail_priority = AntiHacker::AntiHackRequest('mail_priority');
$Obj->mail_charset = AntiHacker::AntiHackRequest('mail_charset');
$Obj->mail_encoding = AntiHacker::AntiHackRequest('mail_encoding');
$Obj->mail_auto_emails = AntiHacker::AntiHackRequest('mail_auto_emails');
$Obj->mail_admin_email = AntiHacker::AntiHackRequest('mail_admin_email');

$Obj->editer = AntiHacker::AntiHackRequest('editer');

$Obj->site_status = AntiHacker::AntiHackRequest('site_status');
$Obj->site_off_message = AntiHacker::AntiHackArrayRequest('site_off_message');
$Obj->site_codes_head = AntiHacker::AntiHackRequest('site_codes_head');
$Obj->site_codes_body = AntiHacker::AntiHackRequest('site_codes_body');
$Obj->robots_txt = AntiHacker::AntiHackRequest('robots_txt');

 //echo '<br> $task='.$task;
 $Obj->script=$_SERVER['PHP_SELF']."?module=$Obj->module";

 switch( $Obj->task ) {
    case 'show':
        $Obj->Show();
        break;
    case 'save':
        $res = $Obj->Save();
        if ( $res ){
                ?><div class="warning"><?=$Obj->Msg['_OK_SAVE'];?></div><?
                $Obj->Show($res);
                //echo "<script>window.location.href='$Obj->script';</script>";
        }
        else echo '<br>'.$Obj->Msg['FLD_ERROR'];
        break;
 }

?>

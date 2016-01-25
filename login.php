<?php                                     /* login.php */
// ================================================================================================
// System : SEOCMS
// Module : login.php
// Version : 1.0.0
// Date : 10.01.2006
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : script for all actions with logon of users on the front-end
//
// ================================================================================================

  //session_start();
  //$_SESSION["start_in_login_file"]=1;
  //echo '<br> -$_SESSION[start_in_login_file]='.$_SESSION['start_in_login_file'].' $_SESSION[session_id]='.$_SESSION['session_id'];

  if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
  include_once( SITE_PATH.'/include/defines.php' );

  $Page = check_init('PageUser', 'PageUser');

  if(!isset($_REQUEST['ajax'])) $ajax= false;
  else $ajax =true;

  if ( !isset($_REQUEST['task']) ) $task = NULL;
  else $task = $_REQUEST['task'];

  if ( !isset($_REQUEST['login']) ) $login = NULL;
  else $login = $_REQUEST['login'];

  if ( !isset($_REQUEST['pass']) ) $pass = NULL;
  else $pass = $_REQUEST['pass'];

  if ( !isset($_REQUEST['whattodo']) ) $whattodo = NULL;
  else $whattodo = $_REQUEST['whattodo'];

  if ( !isset($_REQUEST['logout']) ) $logout= NULL;
  else $logout= $_REQUEST['logout'];

  //echo '<br>login='.$login.' $pass='.$pass.' $logout='.$logout.' $whattodo='.$whattodo;
  //phpinfo();

  $go_to_page = NULL;
  if ( !isset($_REQUEST['referer_page']) ) $referer_page = NULL;
  else $referer_page = $_REQUEST['referer_page'];
  //echo '<br> referer_page='.$referer_page.' $_SERVER["HTTP_REFERER"]='.$_SERVER["HTTP_REFERER"].' $_SERVER[PHP_SELF]='.$_SERVER['PHP_SELF'];

  if ( !empty($referer_page) ) {
      if ( strstr($referer_page,'login.php') ) $go_to_page= NULL;
      else $go_to_page = str_replace('AND', '&', $referer_page);
  }
  else {
      if ( isset($_SERVER["HTTP_REFERER"]) ) $go_to_page = $_SERVER["HTTP_REFERER"];
      if ( $go_to_page==$_SERVER['PHP_SELF'] ) $go_to_page = NULL;
      if ( strstr($go_to_page,'login') ) $go_to_page= NULL;
      //if ( empty($go_to_page) ) $go_to_page='index.php';
  }
  if ( empty($go_to_page) ) $go_to_page=_LINK."myaccount/";
  //echo '<br> $go_to_page='.$go_to_page;


  //------------------ Authorization settings -------------------------
  $logon = check_init('UserAuthorize', 'UserAuthorize');
  if( $login || $pass and $whattodo==2 ) {
        $res=$logon->user_valid( $login, $pass, 1 );
        $User = check_init('User', 'User');
        $tmp_status = $User->GetUserStatus($logon->user_id);
        $username = $logon->login;
        if( ($logon->user_type=='5' OR $logon->user_type=='6' OR $logon->user_type=='7') & $tmp_status!='3' ) {
            $logout=1;
            if ( $tmp_status==1 OR $tmp_status>3 ) $go_to_page = 'login.php?task=not_user';
            if ( $tmp_status==2 ) $go_to_page = 'login.php?task=not_activated';
        }
        else {
            //echo '<br> $logon->Err='.$logon->Err.' $logon->user_id='.$logon->user_id.' $res='.$res.' $go_to_page='.$go_to_page;
            if( !$res ){
                if( empty($logon->Err) ) echo "<script language='JavaScript'>window.location.href='".$_SERVER["HTTP_REFERER"]."';</script>";
            }
            else{
                //======== Save succesfull logon to user statistic ======
                $SysUser = check_init('SysUser', 'SysUser');
                $SysUser->SaveStat($logon->user_id);
                //=======================================================
                $profile = $User->GetUserIdByEmail($logon->login);
                echo "<script language='JavaScript'>window.location.href='$go_to_page';</script>";
            }
        }
  }
  if( isset($logout) ) { $logon->Logout(); echo "<script language='JavaScript'>window.location.href='$go_to_page';</script>"; }
  //-------------------------------------------------------------------

  $multi =check_init_txt('TblFrontMulti',TblFrontMulti);

   switch($task){
      case 'reg':
        $title = $multi['TXT_TITLE_REGISTRATION_PAGE'].' | '.META_TITLE;
        $Description = $multi['TXT_TITLE_REGISTRATION_PAGE'].'. '.META_DESCRIPTION;
        $Keywords = $multi['TXT_TITLE_REGISTRATION_PAGE'].', '.META_KEYWORDS;
        break;
      case 'save_reg_data':
        $title = $multi['TXT_TITLE_REGISTRATION_PAGE'].' | '.META_TITLE;
        $Description = $multi['TXT_TITLE_REGISTRATION_PAGE'].'. '.META_DESCRIPTION;
        $Keywords = $multi['TXT_TITLE_REGISTRATION_PAGE'].', '.META_KEYWORDS;
        break;
      case 'profile':
        $title = $multi['TXT_TITLE_EDIT_PROFILE_PAGE'].' | '.META_TITLE;
        $Description = $multi['TXT_TITLE_EDIT_PROFILE_PAGE'].'. '.META_DESCRIPTION;
        $Keywords = $multi['TXT_TITLE_EDIT_PROFILE_PAGE'].', '.META_KEYWORDS;
        break;
      case 'update':
        $title = $multi['TXT_TITLE_EDIT_PROFILE_PAGE'].' | '.META_TITLE;
        $Description = $multi['TXT_TITLE_EDIT_PROFILE_PAGE'].'. '.META_DESCRIPTION;
        $Keywords = $multi['TXT_TITLE_EDIT_PROFILE_PAGE'].', '.META_KEYWORDS;
        break;
      case 'forgotpass':
        $title = $multi['TXT_TITLE_EDIT_PROFILE_PAGE'].' | '.META_TITLE;
        $Description = $multi['TXT_FORGOT_PASS'].'. '.META_DESCRIPTION;
        $Keywords = $multi['TXT_FORGOT_PASS'].', '.META_KEYWORDS;
        break;
      default:
        $title = $multi['TXT_TITLE_LOGIN_PAGE'].' | '.META_TITLE;
        $Description = $multi['TXT_TITLE_LOGIN_PAGE'].'. '.META_DESCRIPTION;
        $Keywords = $multi['TXT_TITLE_LOGIN_PAGE'].', '.META_KEYWORDS;
        break;
  }

  $Page->SetTitle( $title );
  $Page->SetDescription( $Description );
  $Page->SetKeywords( $Keywords );

ob_start();
$scriptact = '/modules/mod_user/user.php';
include_once(SITE_PATH . $scriptact);
$Page->content = ob_get_clean();
$Page->out();
?>
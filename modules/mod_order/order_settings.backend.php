<?php                              /* order_settings.backend.php */
// ================================================================================================
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] ); 
include_once( $_SERVER['DOCUMENT_ROOT'].'/modules/mod_order/order.defines.php' );

if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];

//============================================================================================
// START
// Blocking to execute a script from outside (not Admin-part) 
//============================================================================================
$Msg = new ShowMsg();
$goto = "http://".NAME_SERVER."/admin/index.php?logout=1";
if ( !isset($_SESSION[ 'session_id']) OR empty($_SESSION[ 'session_id']) OR empty( $module ) ) {
     ?><script>window.location.href="<?=$goto?>";</script><?;
}
$logon = new  Authorization();
if (!$logon->LoginCheck()) {
    ?><script>window.location.href="<?=$goto?>";</script><?; 
}
//=============================================================================================
// END
//=============================================================================================

if( !isset($_REQUEST['task']) || empty($_REQUEST['task']) ) $task='show';
else $task=$_REQUEST['task'];

if(isset($_REQUEST['nds'])) $nds = $_REQUEST['nds'];
else $nds = NULL;

$Order = new Order_settings($logon->user_id, $module);
$Order->nds = strip_tags($nds); 

$scriptact = $_SERVER['PHP_SELF']."?module=$Order->module";

 switch( $task ) {
    case 'show':      
                $Order->ShowSettings();
                break;
    case 'save':      
                if ( $Order->SaveSettings() ) echo "<script>window.location.href='$scriptact';</script>";
                break;
 }
?>

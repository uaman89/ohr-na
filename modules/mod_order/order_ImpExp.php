<?
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );

include_once( $_SERVER['DOCUMENT_ROOT'].'/modules/mod_order/order.defines.php' );

if(!defined("_LANG_ID")) {session_start(); $pg = new PageAdmin();}
if( !isset($_REQUEST['task']) || empty($_REQUEST['task']) ) $task='show';
else $task=$_REQUEST['task'];

if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];
//var_dump($_REQUEST);
//die();
//============================================================================================
// START
// Blocking to execute a script from outside (not Admin-part) 
//============================================================================================
$Msg = new ShowMsg();
$goto = "http://".NAME_SERVER."/admin/index.php?logout=1";
//echo '<br>$goto='.$goto;
if ( !isset($_SESSION[ 'session_id']) OR empty($_SESSION[ 'session_id']) OR empty( $module ) ) {
    //$Msg->show_msg( '_NOT_AUTH' );
    //return false;
    ?><script>window.location.href="<?=$goto?>";</script><?;
}

$logon = new  Authorization();
if (!$logon->LoginCheck()) {
    //return false;
    ?><script>window.location.href="<?=$goto?>";</script><?;
}

//=============================================================================================
// END
//=============================================================================================v
$OrderImpExp = new OrderImpExp($logon->user_id, $module);
$OrderImpExp->task   = ( isset( $_REQUEST['task']) ) ? $_REQUEST['task'] : null;
$OrderImpExp->module = ( isset( $_REQUEST['module'] ) ) ? $_REQUEST['module'] : NULL;

// not using:
//$OrderImpExp->dateFrom = ( isset( $_REQUEST['date_from'] ) ) ? $_REQUEST['date_from'] : NULL;
//$OrderImpExp->dateTo = ( isset( $_REQUEST['date_to'] ) ) ? $_REQUEST['date_to'] : NULL;
//$OrderImpExp->order_status = ( isset( $_REQUEST['order_status'] ) ) ? $_REQUEST['order_status'] : NULL;

$OrderImpExp->arrOrderId = ( isset( $_REQUEST['id_del'] ) ) ? $_REQUEST['id_del'] : NULL;
$OrderImpExp->strOrderId = ( isset( $_REQUEST['orders_to_export'] ) ) ? $_REQUEST['orders_to_export'] : NULL;
$OrderImpExp->id_order = ( isset( $_REQUEST['id_order'] ) ) ? $_REQUEST['id_order'] : NULL;

$OrderImpExp->from_charset = "utf-8";
$OrderImpExp->to_charset = "windows-1251//IGNORE";


//echo '<br>task='.$task;
switch( $OrderImpExp->task )
{

    case 'export_orders_csv':
        $OrderImpExp->exportOrdersToCSV();
        break;

    case 'export_orders':
        $OrderImpExp->exportOrdersToExcelXML();
        break;

    case 'send_ttn':
        $OrderImpExp->sendTtn();
        break;

    case 'check_ttn_sms_status':
        if (!empty($OrderImpExp->id_order)){
            $OrderImpExp::checkTtnSmsStatus( $OrderImpExp->id_order );
        }
        else echo 'wrong data!<br>'.__FILE__.' line: '.__LINE__;
        exit; //because its using for ajax

    default:
        $OrderImpExp->showFrom();
        break;

// not using:
//    case 'export_orders_csv':
//        $OrderImpExp->exportOrdersToCSV();
//        break;

}

?>
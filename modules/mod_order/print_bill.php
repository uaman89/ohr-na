<?
include_once( $_SERVER['DOCUMENT_ROOT'].'/include/defines.php' );
include_once( $_SERVER['DOCUMENT_ROOT'].'/modules/mod_order/order.defines.php' );
$Page = new PageUser();  
$logon = new  Authorization();
if (!defined("_LANG_ID")) define("_LANG_ID", DEBUG_LANG); 

$Msg = new ShowMsg();

if( !isset($_REQUEST['task']) || empty($_REQUEST['task']) ) $task=NULL;
else $task=$_REQUEST['task'];

if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];

if( !isset( $_REQUEST['id_del'] ) ) $id_del = NULL;
else $id_del = $_REQUEST['id_del'];

if( !isset( $_REQUEST['id_order'] ) ) $id_order = NULL;
else $id_order = $_REQUEST['id_order'];

if( !isset( $_REQUEST['waybill'] ) ) $waybill = NULL;
else $waybill = $_REQUEST['waybill'];

$Order = new OrderCtrl($logon->user_id, $module);
$Order->module = $module;
$Order->user_id = $logon->user_id;
$Order->id_order = $id_order;

if ( !empty($waybill) ){
    $Order->PrintOrderBackEnd('waybill');
}
else{
    $Order->PrintOrderBackEnd();
}
       
?>
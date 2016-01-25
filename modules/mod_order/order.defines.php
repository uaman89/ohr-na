<?php
// ================================================================================================
// System : CMS
// Module : order.defines.php
// Date : 05.06.2007
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Purpose : All Definitions for module of Orders
// ==============================================================================================
include_once( SITE_PATH.'/include/defines.php' );
include_once( SITE_PATH.'/modules/mod_order/order.class.php' );
include_once( SITE_PATH.'/modules/mod_order/orderLayout.class.php' );
include_once( SITE_PATH.'/modules/mod_order/backend/orderCtrl.class.php' );
include_once( SITE_PATH.'/modules/mod_order/backend/order_settings.class.php' );
include_once( SITE_PATH.'/modules/mod_catalog/catalog.defines.php' );
include_once( SITE_PATH.'/modules/mod_order/num2str.php' );

define("MOD_ORDER", true);

define("TblModOrder", "mod_order_body");
define("TblModOrderComments", "mod_order_head");
define("TblModTmpOrder", "mod_order_temp");
define("TblModOrderSprTxt","mod_order_spr_txt");
define("TblModOrderSprPayMethod","mod_order_spr_pay_method");
define("TblModOrderSprDelivery","mod_order_spr_delivery");
define("TblModOrderSet","mod_order_set");
define( 'TblModOrderStatuses', 'mod_order_statuses_spr' );

?>
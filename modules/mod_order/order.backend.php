<?php
/**
 * order.backend.php
 * script for all actions with Orders
 * @package Order Package of SEOCMS
 * @author Igor Trokhymchuk  <ihor@seotm.com>
 * @version 1.1, 24.02.2011
 * @copyright (c) 2010+ by SEOTM
 */
if (!defined("SITE_PATH"))
    define("SITE_PATH", $_SERVER['DOCUMENT_ROOT']);
include_once( $_SERVER['DOCUMENT_ROOT'] . '/modules/mod_order/order.defines.php' );

if (!defined("_LANG_ID")) {
    $pg = new PageAdmin();
}

if (!isset($_REQUEST['module']))
    $module = NULL;
else
    $module = $_REQUEST['module'];

//Blocking to execute a script from outside (not Admin-part)
if (!$pg->logon->isAccessToScript($module))
    exit;

$my = new OrderCtrl($pg->logon->user_id, $module);

if (!isset($_REQUEST['task']) || empty($_REQUEST['task']))
    $my->task = 'show';
else
    $my->task = $_REQUEST['task'];

if (!isset($_REQUEST['fln']))
    $my->fln = _LANG_ID;
else
    $my->fln = $_REQUEST['fln'];

if (!isset($_REQUEST['fltr']))
    $my->fltr = NULL;
else
    $my->fltr = $_REQUEST['fltr'];

for ($i=2; $i<6; $i++) {
    $var_name = 'fltr'.$i;
    if (!isset($_REQUEST[$var_name]))

        $my->$var_name = NULL;
    else
        $my->$var_name = $_REQUEST[$var_name];
}
if (!isset($_REQUEST['fltrUserId']))
    $my->fltrUserId = NULL;
else
    $my->fltrUserId = $_REQUEST['fltrUserId'];

if (!isset($_REQUEST['srch']))
    $my->srch = NULL;
else
    $my->srch = $_REQUEST['srch'];

if (!isset($_REQUEST['sort']))
    $my->sort = NULL;
else
    $my->sort = $_REQUEST['sort'];

if (!isset($_REQUEST['start']))
    $my->start = 0;
else
    $my->start = $_REQUEST['start'];

if(!isset($_REQUEST['display'])){
    $my->display = 50;
}else{
    if($my->fltr==0){
        $my->display = 50;
    }else{
        $my->display = $_REQUEST['display'];
    }
}

if (!isset($_REQUEST['id']))
    $my->id = NULL;
else
    $my->id = $_REQUEST['id'];

if (!isset($_REQUEST['name']))
    $my->name = NULL;
else
    $my->name = $my->Form->GetRequestTxtData($_REQUEST['name'], 1);

if (!isset($_REQUEST['phone']))
    $my->phone = NULL;
else
    $my->phone = $my->Form->GetRequestTxtData($_REQUEST['phone'], 1);

if (!isset($_REQUEST['phone_mob']))
    $my->phone_mob = NULL;
else
    $my->phone_mob = $my->Form->GetRequestTxtData($_REQUEST['phone_mob'], 1);

if (!isset($_REQUEST['email']))
    $my->email = NULL;
else
    $my->email = $my->Form->GetRequestTxtData($_REQUEST['email'], 1);

if (!isset($_REQUEST['city']))
    $my->city = NULL;
else
    $my->city = $my->Form->GetRequestTxtData($_REQUEST['city'], 1);

if (!isset($_REQUEST['addr']))
    $my->addr = NULL;
else
    $my->addr = $my->Form->GetRequestTxtData($_REQUEST['addr'], 1);

if (!isset($_REQUEST['firm']))
    $my->firm = NULL;
else
    $my->firm = $my->Form->GetRequestTxtData($_REQUEST['firm'], 1);

if (!isset($_REQUEST['comment']))
    $my->comment = NULL;
else
    $my->comment = $my->Form->GetRequestTxtData($_REQUEST['comment'], 1);

if (!isset($_REQUEST['ttn']))
    $my->ttn = NULL;
else
    $my->ttn = $my->Form->GetRequestTxtData($_REQUEST['ttn'], 1);

if (!isset($_REQUEST['free_delivery']))
    $my->free_delivery = NULL;
else
    $my->free_delivery = $my->Form->GetRequestNumData($_REQUEST['free_delivery'], 1);

if (!isset($_REQUEST['no_commission']))
    $my->no_commission = NULL;
else
    $my->no_commission = $my->Form->GetRequestNumData($_REQUEST['no_commission'], 1);

if (!isset($_REQUEST['discount']))
    $my->discount = NULL;
else
    $my->discount = $my->Form->GetRequestTxtData($_REQUEST['discount'], 1);

if (!isset($_REQUEST['delivery_method']))
    $my->delivery_method = NULL;
else
    $my->delivery_method = $my->Form->GetRequestTxtData($_REQUEST['delivery_method'], 1);

if (!isset($_REQUEST['pay_method']))
    $my->pay_method = NULL;
else
    $my->pay_method = $my->Form->GetRequestTxtData($_REQUEST['pay_method'], 1);

if (!isset($_REQUEST['date']))
    $my->date = date('Y-m-d H:i:s');
else
    $my->date = $my->Form->GetRequestTxtData($_REQUEST['date'], 1);

if (!isset($_REQUEST['buyer_id']))
    $my->buyer_id = NULL;
else
    $my->buyer_id = $my->Form->GetRequestTxtData($_REQUEST['buyer_id'], 1);

if (!isset($_REQUEST['status']))
    $my->status = NULL;
else
    $my->status = $_REQUEST['status'];

if (!isset($_REQUEST['old_status']))
    $my->old_status = NULL;
else
    $my->old_status = $_REQUEST['old_status'];

if (!isset($_REQUEST['id_order']))
    $my->id_order = NULL;
else
    $my->id_order = $my->Form->GetRequestTxtData($_REQUEST['id_order'], 1);


if (!isset($_REQUEST['id_prod']))
    $my->id_prod = NULL;
else
    $my->id_prod = $my->Form->GetRequestTxtData($_REQUEST['id_prod'], 1);

if (!isset($_REQUEST['price']))
    $my->price = NULL;
else {
    $my->price = $_REQUEST['price'];
    foreach ($my->price as $key => $val){
        $my->price[$key] = $my->Currency->ShowPrice($val, false);
    }
}

if (!isset($_REQUEST['quantity']))
    $my->quantity = NULL;
else
    $my->quantity = $_REQUEST['quantity'];

if (!isset($_REQUEST['id_del']))
    $my->id_del = NULL;
else
    $my->id_del = $_REQUEST['id_del'];

if (!isset($_REQUEST['add_prod_item']))
    $my->add_prod_item = NULL;
else {
    $my->add_prod_item = $my->Form->GetRequestTxtData($_REQUEST['add_prod_item'], 1);
    $arr_fltr2_tmp = explode("=", $my->add_prod_item);
    //echo  '<br>$arr_fltr2_tmp[0]='.$arr_fltr2_tmp[0].' $arr_fltr2_tmp[1]='.$arr_fltr2_tmp[1];
    if (isset($arr_fltr2_tmp[0]) AND $arr_fltr2_tmp[0] == 'curcod' AND isset($arr_fltr2_tmp[1]))
        $my->add_prod_item = $arr_fltr2_tmp[1];
    else
        $my->add_prod_item = NULL;
}
//var_dump($_REQUEST);
if ( $my->task == 'add_prod_item' && isset($_REQUEST['add']) &&  !empty($_REQUEST['add'])){
    foreach( $_REQUEST['add'] as $key => $val ){
        $my->arr_add_prod_items[] = $my->Form->GetRequestNumData($key);
    }
    //var_dump($my->arr_add_prod_items);
}

if (!isset($_REQUEST['add_prod_item_cnt']))
    $my->add_prod_item_cnt = 1;
else
    $my->add_prod_item_cnt = $my->Form->GetRequestTxtData($_REQUEST['add_prod_item_cnt'], 1);

if (!isset($_REQUEST['add_prod_item_price']))
    $my->add_prod_item_price = NULL;
else
    $my->add_prod_item_price = $my->Form->GetRequestTxtData($_REQUEST['add_prod_item_price'], 1);

if (!isset($_REQUEST['add_prod_item_currency']))
    $my->add_prod_item_currency = NULL;
else
    $my->add_prod_item_currency = $my->Form->GetRequestTxtData($_REQUEST['add_prod_item_currency'], 1);


if ($my->srch && $my->task == '')
    $my->task = 'show';
if ($my->task == 'savereturn') {
    $my->task = 'save_order_backend';
    $my->action = 'return';
}
else
    $my->action = NULL;

$my->script = $_SERVER['PHP_SELF']."?module=$my->module&display=$my->display&start=$my->start&sort=$my->sort&fltr=$my->fltr&fltr2=$my->fltr2&fltr3=$my->fltr3&fltr4=$my->fltr4&fltr5=$my->fltr5"
              ."&fltrUserId=$my->fltrUserId&id_cat=$my->id_cat&property=$my->property&srch=$my->srch";

if (isset($_REQUEST['price_group']))
    $price_group = $my->Form->GetRequestNumData($_REQUEST['price_group']);

//print_r($_REQUEST);
//echo '<br/>$my->task: '.$my->task;
//exit;

switch ($my->task) {
    case 'showCat':
        //$my->show_cat();
        $my->Show_cat1();
        break;

    case 'show':
        //$my->show_extended();
        $my->show();
        break;

    case 'edit':
        $my->edit();
        break;

    case 'new':
        $my->edit();
        break;

    case 'change_status':
        $my->save();
        $my->show();
        break;

    case 'delete':
        //echo 'id_del = '.$my->id_del;
        //$del = $my->del( $my->id_del );
        if (!empty($my->id_del)) {
            $del = $my->del($my->id_del);
            //if ( $del > 0 ) echo "<script>window.alert('".$pg->Msg->get_msg('_SYS_DELETED_OK')." $del');</script>";
            //else $pg->Msg->show_msg('_ERROR_DELETE');
            if (!$del)
                $pg->Msg->show_msg('_ERROR_DELETE');
                $pg->Msg->show_msg('_ERROR_DELETE');
        }
        else
            $pg->Msg->show_msg('_ERROR_SELECT_FOR_DEL');
        $my->show();
        /* echo "<script>window.location.href='$my->script';</script>"; */
        break;

    case 'save':
        $save = $my->save($my->id_del);
        if (empty($my->id_del)) {
            $pg->Msg->show_msg('_ERROR_SELECT_FOR_SAVE');
        }
        $my->show();
        //echo "<script>window.location.href='$my->script';</script>";
        break;

    case 'save_order_backend':
        $res = $my->save_order_backend();
        //echo '<br>$res=' . $res . ' $my->action=' . $my->action;

        if ($res) {
            if ($my->action == 'return')
                echo "<script>window.location.href='" . $my->script . "&task=edit&id_order=$my->id_order';</script>";
            else
                echo "<script>window.location.href='" . $my->script . "';</script>";
        }
        break;

    case 'cancel':
        echo "<script>window.location.href='$my->script';</script>";
        break;

    case 'del_prod_item':
        $my->DelProdItemFromOrder();
        $my->EditOrdersProd($my->GetOrderCommentInArr($my->id_order), $my->GetProdOrdersByIdOrder($my->id_order));
        break;
    case 'show_add_prod_item':
        $my->AddProdItemForm();
        break;
    case 'add_prod_item':
        $my->AddProdItemToOrder();

        $my->EditOrdersProd($my->GetOrderCommentInArr($my->id_order), $my->GetProdOrdersByIdOrder($my->id_order));
        break;
    case 'load_prod_data':
        $my->LoadProdData();
        break;
    case 'send_order_to_email':
        //var_dump($my->id_order, $my->email);
        $OrderLayout = new OrderLayout;
        $OrderLayout->SendOrderToEmail( $my->id_order, $my->email );

        if ( $OrderLayout->Err ) exit( $OrderLayout->Err);
        else exit( 'Сообщение успешно отправленно' );
        break;
    case 'custom_price_group':

        /* wrong version
        $res = $my->SetCustomPriceGroup( $my->id_order, $price_group );
        if ( $res=='0')exit($res);
        $my->EditOrdersProd( $my->GetOrderCommentInArr($my->id_order), $my->GetProdOrdersByIdOrder($my->id_order) );
        */

        $arr = $my->GetOrderPricesByGroup($my->id_order, $price_group);
        echo json_encode($arr);

        exit;
    case 'change_field':
        //var_dump($_REQUEST);
        $id = $my->Form->GetRequestNumData($_REQUEST['id']);
        $field = $my->Form->GetRequestTxtData($_REQUEST['field']);
        $new_val = $my->Form->GetRequestTxtData($_REQUEST['new_val']);
        //var_dump($my->id_order, $id, $field, $new_val);
        $my->ChangeField($my->id_order, $id, $field, $new_val);

        $new_sum = $my->OrderTotalSum($my->id_order, $price_group);

        exit($new_sum);
}
?>
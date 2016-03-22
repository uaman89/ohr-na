<?php
include_once( $_SERVER['DOCUMENT_ROOT'].'/modules/mod_order/order.defines.php' );
$logon = new  UserAuthorize();



$prod_id = NULL;
$Msg = new ShowMsg();

if( !isset($_REQUEST['task']) || empty($_REQUEST['task']) ) $task='show_cart';
else $task=$_REQUEST['task'];

if( !isset( $_REQUEST['fln'] ) ) $fln = _LANG_ID;
else $fln = $_REQUEST['fln'];

if( !isset( $_REQUEST['fltr'] ) ) $fltr = NULL;
else $fltr = $_REQUEST['fltr'];

if( !isset( $_REQUEST['id_order'] ) ) $id_order = NULL;
else $id_order = $_REQUEST['id_order'];

if( !isset( $_REQUEST['suma'] ) ) $suma = NULL;
else $suma = $_REQUEST['suma'];

if( !isset( $_REQUEST['srch'] ) ) $srch = NULL;

if(!isset($_REQUEST['sort'])) $sort=NULL;
else $sort=$_REQUEST['sort'];

if(!isset($_REQUEST['start'])) $start=0;
else $start=$_REQUEST['start'];

if(!isset($_REQUEST['display']) || empty($srch) ) $display=20;
else $display=$_REQUEST['display'];

if( !isset($_REQUEST['id']) ) $id=NULL;
else $id = $_REQUEST['id'];

if( !isset( $_REQUEST['date'] ) ) $date = NULL;
else $date = $_REQUEST['date'];

if( !isset( $_REQUEST['quantity'] ) ) $quantity = NULL;
else $quantity = $_REQUEST['quantity'];

if( !isset( $_REQUEST['buyer_id'] ) ) $buyer_id = NULL;
else $buyer_id = $_REQUEST['buyer_id'];

if( !isset( $_REQUEST['pages'] ) ) $pages = NULL;
else $pages = $_REQUEST['pages'];

if( !isset( $_REQUEST['comment'] ) ) $comment = NULL;
else $comment = $_REQUEST['comment'];

if( !isset( $_REQUEST['status'] ) ) $status = NULL;
else $status = $_REQUEST['status'];

if( !isset( $_REQUEST['prod_id'] ) ) $prod_id = NULL;
else $prod_id = $_REQUEST['prod_id'];



if( !isset( $_REQUEST['modif'] ) ) $modif = NULL;
else $modif = $_REQUEST['modif'];

if( !isset( $_REQUEST['firm'] ) ) $firm = NULL;
else $firm = $_REQUEST['firm'];

if( !isset( $_REQUEST['param'] ) ) $param = NULL;
else $param = $_REQUEST['param'];

if( !isset( $_REQUEST['img'] ) ) $img = NULL;
else $img = $_REQUEST['img'];

if( !isset( $_REQUEST['name'] ) ) $name = NULL;
else $name = $_REQUEST['name'];

if( !isset( $_REQUEST['phone'] ) ) $phone = NULL;
else $phone = $_REQUEST['phone'];

if( !isset( $_REQUEST['phone_mob'] ) ) $phone_mob = NULL;
else $phone_mob = $_REQUEST['phone_mob'];

if( !isset( $_REQUEST['adr'] ) ) $adr = NULL;
else $adr = $_REQUEST['adr'];

if( !isset( $_REQUEST['alias'] ) ) $alias = NULL;
else $alias = $_REQUEST['alias'];

if( !isset( $_REQUEST['colorId'] ) ) $colorId = NULL;
else $colorId = $_REQUEST['colorId'];

if( !isset( $_REQUEST['sizeOfProp'] ) ) $sizeOfProp = NULL;
else $sizeOfProp = $_REQUEST['sizeOfProp'];

if( !isset( $_REQUEST['city'] ) ) $city = NULL;
else $city = $_REQUEST['city'];

if( !isset( $_REQUEST['o_price'] ) ) $o_price = NULL;
else $o_price = $_REQUEST['o_price'];

if( !isset( $_REQUEST['u_discount'] ) ) $u_discount = NULL;
else $u_discount = $_REQUEST['u_discount'];

if( !isset( $_REQUEST['is_discount'] ) ) $is_discount = 0;
else $is_discount = 1;

if( !isset( $_REQUEST['delivery_method'] ) ) $delivery_method = NULL;
else $delivery_method = $_REQUEST['delivery_method'];

if ( $delivery_method == '1'){ //nova poshta
    if( isset( $_REQUEST['np_adr'] ) )
        $adr = $_REQUEST['np_adr'];
}
//var_dump($delivery_method, $city);

if( !isset( $_REQUEST['pay_method'] ) ) $pay_method = NULL;
else $pay_method = $_REQUEST['pay_method'];

if( !isset( $_REQUEST['ajax_reload'] ) ) $ajax_reload = NULL;
else $ajax_reload = $_REQUEST['ajax_reload'];

if(isset($_REQUEST['productId']) ) {
    $task = 'add_to_cart';
    $prod_id = $_REQUEST['productId'];

}

if(isset($_REQUEST['listid'])) {
    $task = 'add_to_cart_list';
    $prod_id_list = $_REQUEST['listid'];

}

if( isset($_REQUEST['countlist']) ) {
    $count_list_id = $_REQUEST['countlist'];
}

if( isset($_REQUEST['countset']) ) {
    $countset = (int)$_REQUEST['countset'];
}
$my = new OrderLayout($logon->user_id);
if( isset($prod_id_list) ) {
    $my->prod_id_list = $prod_id_list;
}

if( isset($count_list_id) ) {
    $my->count_list_id = $count_list_id;

}
if( isset( $countset ) ) {
    $my->countset = $countset;
}

if(isset($_REQUEST['productIdToRemove'])) { $task = 'del_pos'; $prod_id = $_REQUEST['productIdToRemove'];}


$Catalog = &$Page->Catalog;

if( !isset($logon->user_type) || $logon->user_type<5) {
    $my->group_user_id = 5;
}else{
    $my->group_user_id = $logon->user_type;
}


$my->logon = $logon;
$my->display = $display;
$my->sort = $sort;
$my->start = $start;
$my->fln = $fln;
$my->srch = $srch;
$my->fltr = $fltr;

$my->date = $date;

$my->id_order = $id_order;
$my->quantity = $quantity;
//$my->buyer_id = $buyer_id;
$my->buyer_id = $logon->user_id;
$my->pages = $pages;
$my->status = $status;
$my->is_discount = $is_discount;

$my->prod_id = $prod_id;



$my->colorId = $colorId;
$my->sizeOfProp = $sizeOfProp;
$my->name = addslashes(strip_tags(trim($name)));
$my->phone = addslashes(strip_tags(trim($phone)));
$my->phone_mob = addslashes(strip_tags(trim($phone_mob)));
$my->adr = addslashes(strip_tags(trim($adr)));
$my->city = addslashes(strip_tags(trim($city)));
$my->firm = addslashes(strip_tags(trim($firm)));
$my->alias = addslashes(strip_tags(trim($alias)));
$my->o_price = $o_price;
$my->u_discount = $u_discount;
$my->comment = addslashes(strip_tags(trim($comment)));
$my->modif = $modif;
$my->img = $img;

$my->arr_current_img_params_value = NULL;
$my->parameters = NULL;
$my->pay_method = $pay_method;
$my->delivery_method = $delivery_method;

if ( is_array($_REQUEST) ) {
  foreach($_REQUEST as $key=>$value){
      if( strstr( $key,PARAM_VAR_NAME.PARAM_VAR_SEPARATOR) ){
          $par_tmp = explode(PARAM_VAR_SEPARATOR,$key);
          if(isset($par_tmp[1])){
              if( !empty($my->parameters) ) $my->parameters = $my->parameters.'AND';
              // if parameter is multiselect then build array in array
              if(isset($par_tmp[2])){
                $my->arr_current_img_params_value[$par_tmp[1]][$par_tmp[2]]=$value;
                $my->parameters = $my->parameters.PARAM_VAR_NAME.PARAM_VAR_SEPARATOR.$par_tmp[1].PARAM_VAR_SEPARATOR.$par_tmp[2].'='.$value;
              }
              else {
                $my->arr_current_img_params_value[$par_tmp[1]]=$value;
                $my->parameters = $my->parameters.PARAM_VAR_NAME.PARAM_VAR_SEPARATOR.$par_tmp[1].'='.$value;
              }

          } //end if
      } //end if
  } // end foreach
}// end if
//echo '<br>$my->arr_current_img_params_value='; print_r($my->arr_current_img_params_value);
//echo '<br>$my->parameters='.$my->parameters;

$script = $_SERVER['PHP_SELF']."?task=cart";
//echo '<br>$task: '.$task;

switch( $task )
{
    case 'cart':
        $my->Cart();
        break;
    case 'onlysum':
        $my->FullCart(true);
        exit;
    case 'show_cart':
        ?><div id="mybasket"><?
        $my->FullCart();
        ?></div><?
        if( isset($my->arrData) && !empty($my->arrData) ):
        ?><div class='order-form'><?
        echo $my->arrData;
        ?></div><?
        endif;
        break;
    case 'add_to_cart':
        $my->add_to_cart();
        ?><div><div id="CartSmallAjaxBlock"> <?
        $my->cart();
        ?></div></div><?
        break;

    case 'add_to_cart_list':
        $my->add_to_cart_set();
        ?><div><div id="CartSmallAjaxBlock"> <?
            $my->cart();
            ?></div></div><?
        break;
        break;

    case 'add_to_cart_noajax':
        $my->add_to_cart();
        ?><div id="my_d_basket"><?
        $my->FullCart();
        ?></div><?
        break;
     case 'make_order_step4':
         $cnt = $my->GetCountOfOrdersBySessionId( $logon->session_id );
         //if ( !empty($logon->user_id) ) {
         $my->CheckUserData();
         if( !empty($my->Err)){
             ?><div id="mybasket"><?
             $my->FullCart();
             ?></div><?

             if( isset($my->arrData) && !empty($my->arrData) ):

                 ?><div class='order-form'><?
                 echo $my->arrData;
                 ?></div><?

             endif;
         }
         else{

             $my->id_order = $my->make_order();
             if( empty($logon->user_id)){
                 setcookie('kor_order_id['.$my->id_order.']', $my->id_order, time()+60*60*24*30, '/');
             }

             //$Page->WriteHeader();
             if( !empty($my->id_order) ){

                 $arr = $my->GetOrderCommentInArr($my->id_order);
                 if(is_array($arr))
                     $my->user_name = $arr['name'];
                 else
                     $my->user_name =  $my->Logon->GetUserAlias();

                 $my->no_need_mail = 1; //kostyl
                 $res = $my->SendOrderToEmail($my->id_order);

                 $my->ShowOrderResult($my->id_order);

             }
             else {
                 $my->Err = $my->Msg->show_text('MSG_ORDER_NOT_SAVE').'<br/>';
                 ?><div id="my_d_basket"><?
                 $my->FullCart();
                 ?></div><?

             }

             // $Page->WriteFooter();
         }
                 //$my->Step4_OrderDetails($is_discount);
         /*}
         else
           $my->AskPassword();*/

         break;


	case 'del_pos':
        $my->del_pos($id);
        //echo '<script>makeRequest(\''._LINK.'order/\', \'task=cart&prod_id=0&quantity=0\', \'cart\')</script>';
        //echo "<script>window.location.href='$script';</script>";
		break;

    case 'del_all':
        $my->del_all();
        break;

     case 'make_order_finish':

            //if ( !empty($logon->user_id) ) {
                $my->id_order = $my->make_order();
                if( empty($logon->user_id)){
                    setcookie('kor_order_id['.$my->id_order.']', $my->id_order, time()+60*60*24*30, '/');
                }
                $Page->WriteHeader();
			    if( !empty($my->id_order) ){
                    $arr = $my->GetOrderCommentInArr($my->id_order);
                    if(is_array($arr))
                        $my->user_name = $arr['name'];
                    else
                        $my->user_name =  $my->Logon->GetUserAlias();

                    if( isset($my->alias) && !empty($my->alias) ) {
                        $res = $my->SendOrderToEmail($my->id_order);
                    }

                    $my->ShowOrderResult($my->id_order);
                }
                else {
                    $my->Err = $my->Msg->show_text('MSG_ORDER_NOT_SAVE').'<br/>';
                    ?><div id="my_d_basket"><?
                    $my->FullCart();
                    ?></div><?
                }
                $Page->WriteFooter();
            /*}
		    else $my->AskPassword();*/


	break;


	case 'full_cart':
        $my->FullCart();
	break;

    case 'save_order':
        $my->save_order();
        $my->showSummOrder();
        break;

    case 'save_order_discount':
        if ( !empty($logon->user_id) ) {
           $my->save_order();
           $my->FullCart(true);
           $my->SendTempOrderToManagers();
        }
	    else $my->AskPassword(true);
        echo '<script>makeRequest(\''._LINK.'order/\', \'task=cart&prod_id=0&quantity=0\', \'cart\')</script>';
        break;

    case 'order_discount':
		if ( !empty($logon->user_id) ) {
		//$my->save_order();
		$my->FullCart(true);
		$my->SendTempOrderToManagers();
		}
		else $my->AskPassword(true);
        break;
    case 'save_order_hide':
        $my->save_order();
        break;

    case 'checkuserdata':
        $my->CheckUserData();
    break;

    case 'print_order':
        $my->skipUserCheck = ( isset( $_REQUEST['skip_user_check'] ) ) ? $_REQUEST['skip_user_check'] : null;
        $my->PrintOrder();
        exit();
    break;

    /*case 'print_invoice':  // Счет-фактура
        $my->PrintInvoice();
    break;*/

    case 'history':
        if(!$my->id_order) {
            if($ajax_reload==0) {
                ?><div id="my_d_basket"><?
            }
            $my->UserOrderHistory($logon->user_id, $my->id_order);
            if($ajax_reload==0){
                ?></div><?
            }
        }
        else
            $my->UserOrderHistory($logon->user_id, $my->id_order);
        break;

    case 'print_clients_order_page':
        $Page->breadcrumb = FrontendPages::getMicroFormPathItem('Главная', '/', false)
            .'&nbsp;&nbsp;→&nbsp;&nbsp'
            .FrontendPages::getMicroFormPathItem('служебный раздел', null, false);
        $Page->h1 = 'Служебный раздел';

        //maybe delete commented
//        if ( isset($_REQUEST['magic_password']) && $_REQUEST['magic_password']=='printorder2016')
//            $res = setcookie('print_client_order','printmebaby', time() + 60*60, $_SERVER['REQUEST_URI'], $_SERVER['SERVER_NAME']);
//
//        if ( isset($_COOKIE['print_client_order']) && $_COOKIE['print_client_order']=='printmebaby' ){

        if ( isset($_REQUEST['magic_password']) && $_REQUEST['magic_password']=='printorder2016'){
            $my->showPrintClientOrderPage();
        }
        else{
            $my->showAuthFormToPrintClientOrderBlank();
        }
        break;
}
?>
<?php
/** ================================================================================================
* System : SEOCMS
* Module : order.class.php
* Version : 1.0.0
* Date : 05.06.2007
* Licensed To:
* Igor Trokhymchuk ihoru@mail.ru
* Andriy Lykhodid las_zt@mail.ru
*
* Purpose : Class definition for all actions with managment of orders
*
* ================================================================================================
*/

include_once( $_SERVER['DOCUMENT_ROOT'].'/modules/mod_order/order.defines.php' );

/** ================================================================================================
*    Class             : Order
*    Version           : 1.0.0
*    Date              : 05.06.2007
*
*    Constructor       : Yes
*    Parms             : session_id / session id
*                        usre_id    / UserID
*                        user_      /
*                        user_type  / id of group of user
*    Returns           : None
*    Description       : Class definition for all actions with managment of orders
* ================================================================================================
*    Programmer        :  Igor Trokhymchuk
*    Date              :  05.06.2007
*    Reason for change :  Creation
*    Change Request Nbr:  N/A
* ================================================================================================
* @property FrontSpr $Spr
* @property FrontForm $Form
* @property db $db
* @property UserAuthorize $Logon
*/
class Order
{
    var $user_id = NULL;
    var $login = NULL;
    var $module = NULL;
    var $Err=NULL;
    var $Logon = NULL;
    var $lang_id = NULL;

    var $sort = NULL;
    var $display = 20;
    var $start = 0;
    var $fln = NULL;
    var $width = 500;
    var $srch = NULL;
    var $fltr = NULL;
    var $fltr2 = NULL;
    var $script = NULL;
    var $db = NULL;

    var $Msg = NULL;
    var $Right = NULL;
    var $Form = NULL;
    var $Spr = NULL;
    var $currency = NULL;

    var $date = NULL;
    var $quantity = NULL;
    var $buyer_is = NULL;
    var $status = NULL;
    var $prod_id = NULL;
    var $from = NULL;
    var $to = NULL;
    var $comment = NULL;
    var $sessid = NULL;

	/** ================================================================================================
       *    Function          : Order (Constructor)
       *    Version           : 1.0.0
       *    Date              : 21.03.2006
       *    Parms             : usre_id   / User ID
       *                        module    / module ID
       *                        sort      / field by whith data will be sorted
       *                        display   / count of records for show
       *                        start     / first records for show
       *                        width     / width of the table in with all data show
       *    Returns           : Error Indicator
       *
       *    Description       : Opens and selects a dabase
       * ================================================================================================
       */
       function __construct ($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL, $fltr=NULL)
       {
        //Check if Constants are overrulled
        ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
        ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
        ( $display  !="" ? $this->display = $display  : $this->display = 20   );
        ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
        ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
        ( $width    !="" ? $this->width   = $width    : $this->width   = 750  );

        $this->lang_id = _LANG_ID;

		if (empty($this->db)) $this->db = DBs::getInstance();
        if (empty($this->Msg)) $this->Msg= check_init('ShowMsg', 'ShowMsg');
        if (empty($this->Form)) $this->Form =  check_init('form_mod_order', 'Form', '"form_mod_order"');
		if (empty($this->Right)) $this->Right  = check_init('Rights', 'Rights', '"'.$this->user_id.'", "'.$this->module.'"');
		if (empty($this->Spr)) $this->Spr = check_init('SysSpr', 'SysSpr', '"'.$this->user_id.'", "'.$this->module.'"');
        if (empty($this->Logon)) $this->Logon = check_init('UserAuthorize', 'UserAuthorize');
        if (empty($this->currency)) $this->currency = check_init('SystemCurrencies', 'SystemCurrencies');

        //$this->AddTbl();

       } // end of constructor

       // ================================================================================================
       // Function : AddTbl()
       // Version : 1.0.0
       // Date : 17.04.2007
       //
       // Parms :
       // Returns :      true,false / Void
       // Description :  Add tables and fields
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 17.04.2007
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function AddTbl()
       {
           // add field `parameters` to temporary order table
           if ( !$this->db->IsFieldExist(TblModTmpOrder, 'parameters') ) {
               $q = "ALTER TABLE `".TblModTmpOrder."` ADD `parameters` VARCHAR( 255 ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }

           // add field `parameters` to main order table
           if ( !$this->db->IsFieldExist(TblModOrder, 'parameters') ) {
               $q = "ALTER TABLE `".TblModOrder."` ADD `parameters` VARCHAR( 255 ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }
       }// end of function AddTbl()




    function add_to_cart_set() {

        if(!isset($this->count_list_id) ) {
           return false;
        }

        if(!isset($this->countset) ) {
            $this->countset =1;
        }


        if( isset($this->prod_id_list) ) {

            $list = explode(',', $this->prod_id_list);

            $count= explode(',', $this->count_list_id);

           if( count($list) != count($count)){
               return false;
           }

            foreach( $list as $k=>$v ){
                $listArr[$v] = $count[$k]*$this->countset;
            }


            $this->prod_id = $listArr;
            $this->add_to_cart();

        }

    }
/**
 * Order::add_to_cart()
 * Adds products to the shopping cart
 * @return true / false
 */
function add_to_cart()
{
    if($this->prod_id and is_array($this->prod_id)){

      foreach($this->prod_id as $key=>$value){
	    $date = date("Y-m-d\ H:i:s");
        $q = "SELECT * FROM `".TblModTmpOrder."` WHERE `prod_id`='".$key."'
                        AND `modif`='".$this->modif."'
                        AND `parameters`='".$this->parameters."'
                        AND `sessid`='".$this->Logon->session_id."'";
        if(isset($this->colorId)) $q.=" AND `colorId`='".$this->colorId."'";
	    $res = $this->db->db_Query($q);
	    $check = $this->db->db_GetNumRows();
        //echo '<br>$q='.$q.' $res='.$res.' $check='.$check;
        if($check)
        {
         $row = $this->db->db_FetchAssoc();
         $pos = strpos($value, ',');
         if($pos===false)
         {
          $quantity = $value;
         }
         else
         {
          $quantity = str_replace(',', '.',$value);
         }
         $new_col = $row['quantity']+$quantity;
         $new_col = round($new_col,3);
         $q = "update `".TblModTmpOrder."` set `quantity`='".$new_col."' where `prod_id`='".$key."' and `modif`='".$this->modif."' and `sessid`='".$this->Logon->session_id."'";
         $res1 = $this->db->db_Query($q);
        //echo '<br>$q='.$q.' $res1='.$res1.' $this->db->result='.$this->db->result;
        }
        else
        {
         $pos = strpos($value, ',');
         if($pos===false)
         {
		  $quantity = $value;
         }
         else
         {
          $quantity = str_replace(',', '.',$value);
         }

        $q = "INSERT into `".TblModTmpOrder."` values(NULL, '".$date."', '".$quantity."', '".$this->Logon->user_id."', '".$key."', '".$this->modif."', '".$this->from."', '".$this->to."', '".$this->comment."', 1, '".$this->Logon->session_id."', '".$this->parameters."','".$this->colorId."' ,0)";
        $res = $this->db->db_Query( $q );
	    //echo '<br>'.$q.' <br/>$res='.$res.' $db->result='.$db->result;
        if( !$res OR !$this->db->result ) {
        return false;
        }
        }
      }
     }

    return true;
} // end of function add_to_cart()


// ================================================================================================
// Function : save_order()
// Version : 1.0.0
// Date : 21.04.2006
// save order in cart
// ================================================================================================
function save_order()
{
    foreach($this->quantity as $key=>$value)     {
     //echo '<br>key = '.$key.', value = '.$value;
     $pos = strpos($value, ',');
     if($pos===false)
        $quantity = $value;
     else
        $quantity = str_replace(',', '.',$value);

     $q = "UPDATE `".TblModTmpOrder."` SET
            `quantity`='".$quantity."'
           ";
     if( isset($this->comment[$key]) ) $q .= ", `comment`='".$this->comment[$key]."'";

     $q .= "WHERE `id`='".$key."'";
     $res = $this->db->db_Query($q);
    // echo "<br>q=".$q.' res = '.$res. ' comment = '.$this->comment[$key];
    }
    return true;
} //end of function save_order()


// ================================================================================================
// Function : make_order()
// Version : 1.0.0
// Date : 21.04.2006
// Parms :
// Returns :      true,false / Void
// Description :  show links category
// ================================================================================================
// Programmer :  Dmitriy Kerest
// Date : 21.04.2006
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
function make_order()
{
	$this->Err = NULL;
	$logon = new UserAuthorize();
    $tmb_db = new DB();
    $tmb_db1 = new DB();
    /*
    //====== set next max value for id_order START ============
    $q = "select MAX(id_order) from `".TblModOrder."`";
    $id_order = NULL;
    $res = $db->db_Query($q);
    $row = $db->db_FetchAssoc();
    $id_order = $row['MAX(id_order)']+1;
    //====== set next max value for id_order END ============
    */

	 $id_order = $this->GetNewOrderId();

     $date = date("Y-m-d H:i:s");

	 $q = "SELECT `".TblModTmpOrder."`.*, `".TblModPropGroupPrice."`.`price` as `price`, `".TblModPropGroupPrice."`.`group_currency`
           FROM `".TblModTmpOrder."`, `".TblModPropGroupPrice."`
           WHERE `sessid`='".$logon->session_id."'
           AND `".TblModTmpOrder."`.`prod_id`=`".TblModPropGroupPrice."`.`prod_id`
           AND `".TblModPropGroupPrice."`.`group_id`=".$this->group_user_id."
          ";
	 $res = $this->db->db_Query($q);
     //echo '<br>q='.$q.' $res='.$res.' $db->result='.$db->result;
     if( !$res OR ! $this->db->result ) return false;
	 $rows = $this->db->db_GetNumRows($res);
     if ($rows==0) {
         $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_EMPTY_CARD')."<br/>";
         return false;
     }

    $qnt_all = 0;
    $sum_all = 0;
    $arr_row = array();
    for($i=0;$i<$rows;$i++){

        $arr_row[] = $this->db->db_FetchAssoc($res);
    }

    for($i=0;$i<$rows;$i++){

        $row = $arr_row[$i];
        //  var_dump($row);
        //echo '<br>$rows'.$rows;
        //echo '<br>$row[price]='.$row['price'];
        if( $row['quantity']==0 || $row['quantity']=='' ){
            $quantity[$i] = 1;
        }
        else {
            $quantity[$i]=$row['quantity'];
        }

        $price0 = $row['price'];
        $curr = $row['group_currency'];

        $price = $this->currency->Converting($curr, _CURR_ID, $price0);

        // echo '<br>price='.$price;
        //$price = $Currency->Converting($row['price_currency'], _CURR_ID, $row['price'], 2);

        if($curr==1 && $this->group_user_id==5 ) {
            $price = round($price);
        }

        //echo '<br>price='.$price;
        $sum = $this->currency->Converting(_CURR_ID, _CURR_ID, ($price*$quantity[$i]), 2);

        //echo '<br>$sum='.$sum;

        $q = "INSERT INTO `".TblModOrder."` SET
             `id_order`='".$id_order."',
             `quantity`='".$quantity[$i]."',
             `price`='".$price."',
             `sum`='".$sum."',
             `currency`='"._CURR_ID."',
             `prod_id`='".$row['prod_id']."',
             `comment`='".$row['comment']."',
             `parameters`='".$row['parameters']."'
             ";
        $tmp_res = $tmb_db->db_Query($q);
       //echo '<br>q='.$q.' $tmp_res='.$tmp_res.' $tmb_db->result='.$tmb_db->result;
        if( !$tmp_res OR ! $tmb_db->result ) {
			$this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_ORDER_NOT_ISSUED')."<br/>";
            return false;
        }
        $q = "DELETE FROM `".TblModTmpOrder."` WHERE `id`='".$row['id']."'";
		$tmp_res = $tmb_db1->db_Query($q);
		//echo '<br>q='.$q.' $tmp_res='.$tmp_res.' $tmb_db->result='.$tmb_db->result;
        $qnt_all += $quantity[$i];
        $sum_all += $sum;
    }

    if ( !empty($this->phone_mob) ){
        $User = check_init("User", "UserShow");
        $user_data = $User->GetUserDataByUserLogin($this->phone_mob);
        $buyer_id = $user_data['id'];
    }
    else if ( !empty($logon->user_id) )
        $buyer_id = $logon->user_id;
    else
        $buyer_id = NULL;


    $q_com = "INSERT INTO `".TblModOrderComments."` SET
               `id_order`='".$id_order."',
               `name`='".$this->name."',
               `phone`='".$this->phone."',
               `phone_mob`='".$this->phone_mob."',
               `email`='".$this->alias."',
               `city`='".$this->city."',
               `addr`='".$this->adr."',
               `firm`='".$this->firm."',
               `comment`='".$this->comment."',
               `qnt_all`='".$qnt_all."',
               `sum`='".$sum_all."',
               `currency`='"._CURR_ID."',
               `discount`='".$this->u_discount."',
               `delivery_method`='".$this->delivery_method."',
               `pay_method`='".$this->pay_method."',
               `date`='".$date."',
               `buyer_id`='".$buyer_id."',
               `status`='1',
               `isread`='0'
    ";

    $tmp_res = $tmb_db->db_Query($q_com);
    //echo '<br>q_com='.$q_com.' $tmp_res='.$tmp_res.' $tmb_db->result='.$tmb_db->result;
    if( !$tmp_res ) {
        $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_ORDER_NOT_ISSUED')."<br/>";
        return false;
    }

    return $id_order;
}
//--- end of function make_order ---------------------------------------------------------------------------------------


// ================================================================================================
// Function : GetNewOrderId()
// Version : 1.0.0
// Date : 05.05.2010
  // Returns :      true,false / Void
// Description :  return new order Id
// ================================================================================================
// Programmer :   Igor Trokhymchuk
// Date : 05.05.2010
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
function GetNewOrderId()
{
    //====== set next max value for id_order START ============
    //$mask = date("ymd"); //old code
    $mask = substr( date("ym"), 1 );
    $q = "SELECT `id_order` FROM `".TblModOrderComments."` WHERE `id_order` LIKE '".$mask."%' ORDER BY `id` desc LIMIT 1";
    //echo '<br>$q='.$q;
    $id_order = NULL;
    $res = $this->db->db_Query($q);
    $rows = $this->db->db_GetNumRows();
    //echo '<br>$rows='.$rows;
    if($rows>0){
        $row = $this->db->db_FetchAssoc($res);
        //формирую номер нового заказа, как самый больший за этот день + 1
        $tmp = explode("-", $row['id_order']);
        $id_order = $tmp[0].'-'.($tmp[1]+1);
    }
    else{ $id_order = $mask.'-1';}
    //====== set next max value for id_order END ============
    return $id_order;
}//end of function GetNewOrderId()



// ================================================================================================
// Function : GetCountOfOrdersByUserId()
// Version : 1.0.0
// Date : 05.06.2007
// Parms : id_user - id of the user
// Returns :      true,false / Void
// Description :  show links category
// ================================================================================================
// Programmer :   Igor Trokhymchuk
// Date : 05.06.2007
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
function GetCountOfOrdersByUserId( $id_user )
{
  $q = "select * from `".TblModOrderComments."` where `buyer_id`='".$id_user."'";
  $res = $this->db->db_Query($q);
  //echo '<br>$q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;
  if( !$res OR !$this->db->result ) return false;
  $rows = $this->db->db_GetNumRows($res);
  return $rows;
}//end of function GetCountOfOrdersByUserId()

// ================================================================================================
// Function : GetOrdersByUserId()
// Version : 1.0.0
// Date : 05.06.2007
// Parms : id_user - id of the user
// Returns :      true,false / Void
// Description :  show links category
// ================================================================================================
// Programmer :   Igor Trokhymchuk
// Date : 05.06.2007
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
function GetOrdersByUserId( $id_user )
{
  $q = "SELECT `".TblModOrderComments."`.*
        FROM `".TblModOrderComments."`
        WHERE `".TblModOrderComments."`.`buyer_id`='".$id_user."'
        AND `".TblModOrderComments."`.`status` IN (1,2,3,4)
        ORDER BY `".TblModOrderComments."`.`id` desc
        ";
  $res = $this->db->db_Query($q);
  //echo '<br>$q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;
  if( !$res OR !$this->db->result ) return false;
  $rows = $this->db->db_GetNumRows($res);
  $arr = array();
  for($i=0;$i<$rows;$i++){
      $row = $this->db->db_FetchAssoc();
      $arr[] = $row;
  }
  return $arr;
}//end of function GetOrdersByUserId()

// ================================================================================================
// Function : GetParametersToArray()
// Version : 1.0.0
// Date : 06.06.2007
// Parms : id_user - id of the user
// Returns :      true,false / Void
// Description :  show links category
// ================================================================================================
// Programmer :   Igor Trokhymchuk
// Date : 05.06.2007
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
function GetParametersToArray( $parameters_field )
{
       $arr = NULL;
       $tmp_arr = explode("AND", $parameters_field);
       if( is_array($tmp_arr) ){
           foreach($tmp_arr as $k=>$v){
              //echo '<br>$k='.$k.' $v='.$v;
              $par_tmp = explode(PARAM_VAR_SEPARATOR,$v);
              if(isset($par_tmp[1])){
                  $par_tmp2 = explode("=",$par_tmp[1]);
                  // if parameter is multiselect then build array in array
                  if(isset($par_tmp[2])){
                    $par_tmp2 = explode("=",$par_tmp[2]);
                    $arr[$par_tmp[1]][$par_tmp2[0]]=$par_tmp2[1];
                  }
                  else {
                    $arr[$par_tmp2[0]]=$par_tmp2[1];
                  }

              } //end if
           } //end foreach
       } //end if
       //echo '<br>$arr='; print_r($arr);
       return $arr;
}//end of function GetParametersToArray()


// ================================================================================================
// Function : GetCountOfOrdersBySessionId()
// Version : 1.0.0
// Date : 21.04.2006
// returns number of books in cart
// ================================================================================================
function GetCountOfOrdersBySessionId( $session_id )
{

  // delete old orders
  $this->DelOldOrders();

  $q = "SELECT * FROM `".TblModTmpOrder."` WHERE `sessid`='".$session_id."'";
  $res = $this->db->db_Query($q);
  //echo '<br>$q='.$q.' $res='.$res.' $db->result='.$db->result;
  if( !$res OR !$this->db->result ) return false;
  $rows = $this->db->db_GetNumRows($res);
  //echo $rows;
  return $rows;
}//end of function GetCountOfOrdersBySessionId()


// ================================================================================================
// Function : DelOldOrders()
// Version : 1.0.0
// Date : 21.04.2006
// deletes delete old orders from temporary order table
// ================================================================================================
function DelOldOrders()
{
  //=========== delete old orders START =================
  $yesturday  = mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"));
  $yesturday = date("Y-m-d", $yesturday);
  $query = "DELETE FROM `".TblModTmpOrder."` WHERE `date`<'".$yesturday."'";
  $res = $this->db->db_Query($query);
  if( !$res OR !$this->db->result ) return false;
  $query1 = "DELETE FROM `".TblModTmpOrder."` WHERE `sessid`=''";
  $res = $this->db->db_Query($query1);
  if( !$res OR !$this->db->result ) return false;
  //=========== delete old orders END =================
  return true;
}// end of function DelOldOrders()


// ================================================================================================
// Function : del_pos()
// Version : 1.0.0
// Date : 23.01.2014
//deletes position in cart
// Programmer :   Bogdan Iglinsky
// ================================================================================================
    function del_pos($curcod)
    {
        $q = "delete from `".TblModTmpOrder."` where `id`='$curcod'";
        $res = $this->db->db_Query($q);
        //if($res){$this->cart();}
        //else{echo "Удалить позицию не удалось!!!";}
        if($res) {
            $this->FullCart();
            return true;
        }
        else return false;
    }// end of function del_pos

    // ================================================================================================
// Function : del_all()
// Version : 1.0.0
// Date : 23.01.2014
//deletes all position in cart
// Programmer :   Bogdan Iglinsky
// ================================================================================================
    function del_all()
    {
        $q = "delete from `".TblModTmpOrder."` where `sessid`='".$this->Logon->session_id."'";
        $res = $this->db->db_Query($q);
        //if($res){$this->cart();}
        //else{echo "Удалить позицию не удалось!!!";}
        if($res) {
            $this->FullCart();
            return true;
        }
        else return false;
    }// end of function del_pos

    // ================================================================================================
    // Function : showSummOrder()
    // Version : 1.0.0
    // Date : 23.01.2014
    //show all summ in cart
    // Programmer :   Bogdan Iglinsky
    // ================================================================================================
    function showSummOrder(){
        $logon = check_init("UserAuthorize","UserAuthorize");

        $q = "SELECT `".TblModTmpOrder."`.*, `".TblModCatalogProp."`.`price`, `".TblModCatalogProp."`.`price_currency`
           FROM `".TblModTmpOrder."`, `".TblModCatalogProp."`
           WHERE `sessid`='".$logon->session_id."'
           AND `".TblModTmpOrder."`.`prod_id`=`".TblModCatalogProp."`.`id`
          ";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' $res='.$res.' $db->result='.$db->result;
        if( !$res OR ! $this->db->result ) return false;
        $rows = $this->db->db_GetNumRows($res);
        if ($rows==0) {
            $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_EMPTY_CARD')."<br/>";
            return false;
        }

        $sum_all = 0;
        for($i=0;$i<$rows;$i++){
            $row = $this->db->db_FetchAssoc($res);
            $price = $this->currency->Converting($row['price_currency'], _CURR_ID, $row['price']);
            $sum = $this->currency->Converting(_CURR_ID, _CURR_ID, ($price*$row['quantity']), 2);
            $sum_all = $sum_all + $sum;
        }
        echo $this->currency->ShowPrice($sum_all);
    }

// ================================================================================================
// Function : BookOrderPrice()
// Version : 1.0.0
// Date : 21.04.2006
//returns Price for each book in shopping cart
// ================================================================================================

function BookOrderPrice($prod, $id_order, $param, $def_currency=null)
{
    //==================================CATALOGUE============================
	$Currency = new SystemCurrencies();
	$logon = new UserAuthorize();
	$Layout = new CatalogLayout();
	$q = "select * from `".TblModCatalogProp."` where `id`='$prod'";

	$res = $this->db->db_Query($q);
	$row = $this->db->db_FetchAssoc();
	if(empty($def_currency)){
		if($param ==2){
		if(!defined("_CURR_ID")) $def_currency = $Currency->GetDefaultCurrency();
		else $def_currency = _CURR_ID;
		} else {
		$def_currency = _CURR_ID;
		}
        }
    //    echo "<br >def_currency = ".$def_currency;
      //  $curr_str = " ".$this->Spr->GetNameByCod( TblSysCurrenciesSprShort, $def_currency, $this->lang_id, 1 );

	$book_price= $Currency->Converting($row['price_currency'], $def_currency,  $row['price']);

	$idd = $row['id'];
        $page_price='';

    //==================================ORDER=================================
    if($param ==1)
	{
		$q1 = "select * from `".TblModTmpOrder."` where `sessid`='$logon->session_id' and `id`='$id_order'";
	}
    if($param ==2)
	{
		$q1 = "select * from `".TblModOrder."` where `id`='$id_order'";
	}
	$res = $this->db->db_Query($q1);
	while($pagess = $this->db->db_FetchAssoc()){
	$kol = $pagess['quantity'];
	//$num_pages = $pagess['to'] - $pagess['from'];
	if(empty($kol) || $kol==0)
		{
			if(empty($page_price) || $page_price==0){return $price =  "Не установлена";}
			$price = $num_pages*$page_price." ".$this->Spr->GetNameByCod( TblSysCurrenciesSprShort, $def_currency, $this->lang_id, 1 );
			return $price;
		}
	else
		{
			if(empty($book_price) || $book_price==0){return $price =  "Не установлена";}
			$price = $kol*$book_price." ".$this->Spr->GetNameByCod( TblSysCurrenciesSprShort, $def_currency, $this->lang_id, 1 );
			return $price;
		}

	}
}// end of function BookOrderPrice

// ================================================================================================
// Function : suma()
// Version : 1.0.0
// Date : 21.04.2006
//returns Price for each book in shopping cart
// ================================================================================================
function suma($mass, $quantity, $is_discount=false, $use_curr_cod=true)
{

	$k = count($mass);
	$sum = NULL;
	for($i = 0;$i<$k;$i++){
		$mass[$i] = str_replace(',', '.',$mass[$i]);
		$sum = $sum+$mass[$i]*$quantity[$i];
        //var_dump($sum);
        //echo '<br>$sum='.$sum.' $mass['.$i.']='.$mass[$i].' $quantity['.$i.']='.$quantity[$i];
	}
	if($is_discount) {
	    $U = new User();

	    if (empty($this->Logon)) $this->Logon = new  UserAuthorize();
	    $id_user = $U->GetUserIdByEmail($this->Logon->login);
        $discount = $U->GetUserDiscount($id_user);
        $discount = $discount/100;
        //   echo "<br> discount = ".$discount;
        //  echo "<br> sum = ".$sum;
        $discount =  round($sum*$discount, 2);
        $sum = $sum-$discount;
        $tmpret = explode(".", $sum);
        if( !isset($tmpret[1]) ) $sum = $sum.".00";
        elseif( empty($tmpret[1]) ) $sum = $sum."00";
        elseif( strlen($tmpret[1])==1) $sum = $sum."0";
        if($use_curr_cod) return $this->Currency->ShowPrice($sum);
        else return $sum;
	}
    else {
	    $tmpret = explode(".", $sum);
        if( !isset($tmpret[1]) ) $sum = $sum.".00";
        elseif( empty($tmpret[1]) ) $sum = $sum."00";
        elseif( strlen($tmpret[1])==1) $sum = $sum."0";
        if($use_curr_cod) return $this->Currency->ShowPrice($sum);
	    else return $sum;
	}
} // end of function suma

// ================================================================================================
// Function : GetProdOrdersCountByIdOrder()
// Version : 1.0.0
// Date : 12.06.2007
// Parms : id_order - number of the order
// Returns :      true,false / Void
// Description :  return count of ordered products
// ================================================================================================
// Programmer :   Igor Trokhymchuk
// Date : 05.06.2007
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
function GetProdOrdersCountByIdOrder( $id_order )
{
  $q = "Select * from `".TblModOrder."` where `id_order`='".$id_order."'";
  $res = $this->db->db_Query($q);
  //echo '<br>$q='.$q.' $res='.$res.' $db->result='.$db->result;
  if( !$res OR !$this->db->result ) return false;
  $rows = $this->db->db_GetNumRows($res);
  return $rows;
}//end of function GetProdOrdersCountByIdOrder()


// ================================================================================================
// Function : GetProdOrdersByIdOrder()
// Version : 1.0.0
// Date : 12.06.2007
// Parms : id_order - number of the order
//         status   - status of the order
// Returns :      true,false / Void
// Description :  return array with ordered products
// ================================================================================================
// Programmer :   Igor Trokhymchuk
// Date : 05.06.2007
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
function GetProdOrdersByIdOrder( $id_order )
{
  $q = "Select * from `".TblModOrder."` where `id_order`='".$id_order."' ORDER BY `id`";
  $res = $this->db->db_Query($q);
  //echo '<br>$q='.$q.' $res='.$res.' $db->result='.$db->result;
  if( !$res OR !$this->db->result ) return false;
  $rows = $this->db->db_GetNumRows($res);
  $arr = array();
  for($i=0;$i<$rows;$i++){
      $row = $this->db->db_FetchAssoc();
      $arr[$i]['id'] = $row['id'];
      $arr[$i]['quantity'] = $row['quantity'];
      $arr[$i]['price'] = $row['price'];
      $arr[$i]['sum'] = $row['sum'];
      $arr[$i]['prod_id'] = $row['prod_id'];
      $arr[$i]['currency'] = $row['currency'];
      $arr[$i]['comment'] = $row['comment'];
      $arr[$i]['parameters'] = $row['parameters'];
      $arr[$i]['colorId'] = $row['colorId'];
      $arr[$i]['sizeId'] = $row['sizeId'];
  }
  return $arr;
}//end of function GetProdOrdersByIdOrder()


// ================================================================================================
// Function : GetOrderCommentInArr()
// Version : 1.0.0
// Date : 29.08.2007
// Parms :
// Returns :      true,false / Void
// Description :  Get All order date in array for current id
// ================================================================================================
// Programmer : Alex Kerest
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
function GetOrderCommentInArr($id_order)
{
 $arr = array();

 $q = "SELECT * FROM `".TblModOrderComments."` WHERE `id_order`='".$id_order."'";
 $res = $this->db->db_Query( $q );
 $rows = $this->db->db_GetNumRows($res);
 if($rows==0) return false;
 $row = $this->db->db_FetchAssoc();
 return $row;
} // end of funtion  GetOrderCommentInArr


// ================================================================================================
// Function : GetUserData()
// Version : 1.0.0
// Date :  5.03.2008
// returns
// ================================================================================================
function GetUserData($buyer_id){
$us = "select * from `".TblSysUser."` where `id`='".$buyer_id."'";
        $res1 = $this->Right->Query( $us, $this->user_id, $this->module );
        $user = $this->Right->db_FetchAssoc($res1);

        $User = new User();
        $user_data = $User->GetUserDataByUserEmail($user['login']);
     //   echo $user_data['name']."<br>Адр.: ".$user_data['adr']."<br>Тел.: ".$user_data['phone']."<br>Моб.: ".$user_data['phone_mob']."<br><a href=mailto:".$user_data['email'].">".$user_data['email']."</a>";
  return $user_data;
} // end of function GetUserData




// ================================================================================================
// Function : AllSumm()
// Version : 1.0.0
// Date : 5.03.2008
// returns
// ================================================================================================
function AllSumm($id_order){
    $Catalog = new Catalog;
    $summ = 0;
    $q = "SELECT * FROM `".TblModOrder."` WHERE `id_order`='".$id_order."'";
    //echo $q;
    $res = $this->db->db_Query( $q );
    $rows = $this->db->db_GetNumRows($res);
    if($rows>0)
    {
        for($i = 0 ;$i<$rows; $i++)
        {
            $row = $this->db->db_FetchAssoc();
            $result = $this->db->result;
            $mass[$i] = $Catalog->GetPrice($row['prod_id']);
            $this->db->result = $result;
            $quantity[$i] = $row['quantity'];
        }
        //var_dump( $mass, $quantity );
        $summ = $this->suma( $mass, $quantity );
    }
    return $summ;
} // end of function AllSumm


// ================================================================================================
// Function : GetNDS()
// Version : 1.0.0
// Returns :      true,false / Void
// Description :  GetNDS
// Programmer :  Yaroslav Gyryn
// Date : 28.01.2011
// ================================================================================================
function GetNDS () {
    $q="select * from `".TblModOrderSet."` where 1";
     $res = $this->db->db_Query( $q );
     if( !$res )
        return false;
     $row = $this->db->db_FetchAssoc();
     //echo '<br/ >$q ='.$q;
     //echo '<br/ >$res ='.$res;
     return $row['nds'];
}    //end of function GetNDS()


// ================================================================================================
// Function : GetSysSprTableData()
// Version : 1.0.0
// Returns :      true,false / Void
// Description :  GetSysSprTableData
// Programmer :  Yaroslav Gyryn
// Date : 28.01.2011
// ================================================================================================
function GetSysSprTableData($table, $getFullData = false)
   {
       $q = "SELECT `".$table."`.*
             FROM `".$table."`
             WHERE `".$table."`.`name`!=''
             AND `".$table."`.`lang_id`='".$this->lang_id."'
             ORDER BY `".$table."`.`move` ASC
            ";
       $res = $this->db->db_Query( $q );
       //echo '<br>$q='.$q.' $res='.$res.' $dbr->result='.$dbr->result;
       if ( !$res or !$this->db->result) return false;
       $rows = $this->db->db_GetNumRows();
       //echo '<br>rows='.$rows;
       $arr = array();
       for( $i = 0; $i < $rows; $i++ ){
           $row=$this->db->db_FetchAssoc();
           if ($getFullData) {
               $arr[$row['cod']] = $row;
           }
           else{
               $arr[$row['cod']] = stripslashes($row['name']);
           }

       }
       return $arr;
   }
} //end of class order

?>
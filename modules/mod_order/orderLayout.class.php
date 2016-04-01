    <?php
    // ================================================================================================
    // System : SEOCMS
    // Module : orderLayout.class.php
    // Version : 1.0.0
    // Date : 06.06.2007
    // Licensed To:
    // Igor Trokhymchuk ihoru@mail.ru
    // Purpose : Class definition for all actions with managment of Layout for orders
    //
    // ================================================================================================

    include_once( $_SERVER['DOCUMENT_ROOT'].'/modules/mod_order/order.defines.php' );
    /** ================================================================================================
    *    Class             : OrderLayout
    *    Version           : 1.0.0
    *    Date              : 06.06.2007
    *
    *    Constructor       : Yes
    *    Parms             : session_id / session id
    *                        usre_id    / UserID
    *                        user_      /
    *                        user_type  / id of group of user
    *    Returns           : None
    *    Description       : Class definition for all actions with managment of Layout for orders
    * ================================================================================================
    *    Programmer        :  Igor Trokhymchuk
    *    Date              :  06.06.2007
    *    Reason for change :  Creation
    * ================================================================================================
    * @property FrontSpr $Spr
    * @property FrontForm $Form
    * @property db $db
    * @property Right $Right
    * @property SystemCurrencies $currency
    * @property TblFrontMulti $multi
    * @property CatalogLayout $Catalog
    * @property UserAuthorize $Logon
    */
     class OrderLayout extends Order {

            var $user_id = NULL;
            var $module = NULL;
            var $Err=NULL;
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
            var $currency = NULL;

            var $Msg = NULL;
            var $Right = NULL;
            var $Form = NULL;
            var $Spr = NULL;
            var $Catalog = NULL;
            var $Logon = NULL;

            var $date = NULL;
            var $quantity = NULL;
            var $buyer_is = NULL;
            var $status = NULL;
            var $prod_id = NULL;
            var $from = NULL;
            var $to = NULL;
            var $comment = NULL;
            var $sessid = NULL;

            var $TextMessages = NULL;

           // ================================================================================================
           //    Function          : OrderLayout (Constructor)
           //    Version           : 1.0.0
           //    Date              : 21.03.2006
           //    Parms             : usre_id   / User ID
           //                        module    / module ID
           //                        sort      / field by whith data will be sorted
           //                        display   / count of records for show
           //                        start     / first records for show
           //                        width     / width of the table in with all data show
           //    Returns           : Error Indicator
           //
           //    Description       : Opens and selects a dabase
           // ================================================================================================
           function OrderLayout ($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL, $fltr=NULL)
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
            if (empty($this->Msg)) $this->Msg = check_init('ShowMsg', 'ShowMsg');
            $this->Msg->SetShowTable(TblModOrderSprTxt);
            if (empty($this->Form)) $this->Form = check_init('FormOrder', 'FrontForm', '"form_mod_order"');
            //if (empty($this->Right)) $this->Right = new  Rights($this->user_id, $this->module);
            if (empty($this->Right)) $this->Right = check_init('Rights', 'Rights', '$this->user_id, $this->module');
            if (empty($this->Spr)) $this->Spr = check_init('FrontSpr', 'FrontSpr', "'$this->user_id', '$this->module'");
            if (empty($this->Logon)) $this->Logon = new  UserAuthorize();

            $this->multi = $this->multi = check_init_txt('TblFrontMulti', TblFrontMulti);
            $this->multiUser = $this->multi;

            $this->AddTbl();
            $this->currency = new SystemCurrencies();
            $this->delivery_price_currency = 5; //стоимость доставки указана в гривне.
            if(empty($this->Catalog)) $this->Catalog = check_init("CatalogLayout","CatalogLayout");

           } // end of constructor OrderLayout


        // ================================================================================================
        // Function : ShowSmallForm()
        // Version : 1.0.0
        // Date : 06.06.2007
        // Parms :
        // Returns :      true,false / Void
        // Description :  show short information about orders
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 06.06.2007
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function ShowSmallForm()
        {
            ?>
            <table class="table_inbox" border=0 cellpadding="0" cellspacing="0" width="110">
            <tr>
            <td><a href="cart_<?=$this->lang_id;?>.html" title="<?=$this->multi['TXT_CART'];?>"><img src="/images/design/cart.gif" border=0 alt="<?=$this->multi['TXT_CART'];?>" title="<?=$this->multi['TXT_CART'];?>"></a></td>
            <td width=5></td>
            <td><a href="cart_<?=$this->lang_id;?>.html"><b><?=$this->multi['TXT_CART'];?></b></a></td>
            </tr>
            <tr>
            <td colspan=3>
                <?=$this->multi['TXT_IN_CART_NOW'];?>
                <br><a href="cart_<?=$this->lang_id;?>.html"><?=$this->GetCountOfOrdersBySessionId($this->Logon->session_id);?> <?=$this->multi['TXT_IN_CART_PRODUCTS'];?></a>
            </td>
            </tr>
            </table>
            <?
        } //end of function ShowSmallForm()

         function showValuta($arr){
             $url = $_SERVER['REQUEST_URI'];
             $keys=array_keys($arr);
             if(isset($this->c_link))  $url=$this->c_link;
             if(substr_count($url,"?")==0)  $url.='?';
             else $url.='&';

             return View::factory('/modules/mod_order/templates/tpl_valuta_choose.php')
                 ->bind('url',$url)
                 ->bind('arr',$arr);
         }


         /**
          * OrderLayout::Cart()
          * Show Cart
          * @copyright Yaroslav Gyryn 24.04.2012
          * @param mixed $Catalog
          * @return void
          */
         function Cart($Catalog = null)
         {
             $from = NULL;
             $to = NULL;
             $comment = NULL;
             $mass = NULL;
             $tow = 0;
             $arr_curr = $this->currency->GetShortNamesInArray('front');
             $url = $_SERVER['REQUEST_URI'];
             if(isset($this->c_link))  $url=$this->c_link;
             if(substr_count($url,"?")==0)  $url.='?';
             else $url.='&';
             $scriptlink = "onchange=\"location='".$url."curr_ch='+this.value\"";

             $this->DelOldOrders();

             $q = "SELECT * FROM `".TblModTmpOrder."` WHERE `sessid`='".$this->Logon->session_id."' ORDER BY `id`";
             $res = $this->db->db_Query( $q );
             $rows = $this->db->db_GetNumRows($res);
             $arr=array();
             for($i = 0 ;$i<$rows; $i++) {
                 $row = $this->db->db_FetchAssoc();
                 $arr[$i]=$row;
             }
             //echo "<br> q = ".$q." res = ".$res." rows = ".$rows;
             if($rows>0){
                 $this->Catalog = $Catalog;
                 if (empty($this->Catalog))
                     $this->Catalog = check_init('CatalogLayout', 'CatalogLayout');

                 $quantity = null;
                 $sum = 0;

                 if( !isset($this->logon->user_type) || $this->logon->user_type<5) {
                     $this->group_user_id = 5;
                 }else{
                     $this->group_user_id = $this->logon->user_type;
                 }

                 for($i = 0 ;$i<$rows; $i++) {
                     $row = $arr[$i];

                     $group_price = $this->Catalog->GetPrice($row['prod_id']);

                     $curr = $this->Catalog->GetPriceCurrency($row['prod_id']);

                     $p = $this->currency->Converting($curr, _CURR_ID, $group_price);

                     //округляем цены при конвертации с валюты и только для розницы
                     if($curr!=5 && $this->group_user_id == 5){
                         $p = round($p);
                     }

                     $mass[$i] = $p;
                     $quantity[$i] = $row['quantity'];
                     $tow += $quantity[$i];
                 }
                 if($quantity != null) $sum = $this->suma( $mass, $quantity,false,false);
                 //$sum = $this->currency->ShowPrice($sum, 0);
             }
             else{
                 $tow=0;
                 $sum=0;
             }
             $curr=$this->showValuta($arr_curr);

             echo View::factory('/modules/mod_order/templates/tpl_cart.php')
                 ->bind('tow',$tow)
                 ->bind('cart_text',$this->multi['TXT_YOUR_CART'])
                 ->bind('sum',$sum)
                 ->bind('curr',$curr);
             /*
              if($rows>0){
                  /*<a href="<?=_LINK?>order/" title="<?=$this->multi['TXT_ALL_LIST'];?>" ><?=$this->multi['TXT_ALL_LIST'];?></a><br/>?>
                  <a href="<?=_LINK?>order/" title="<?=$this->multi['TXT_MAKE_ORDER'];?>" ><?=$this->multi['TXT_MAKE_ORDER'];?></a>
                  <?
              }*/
         } //end of function Cart()



         // ================================================================================================
         // Function : FullCart()
         // Date : 23.01.2014
         // Returns :      true,false / Void
         // Description :  show links category
         // Programmer :  Bogdan Iglinsky
         // ================================================================================================
         function FullCart($onlysum=false, $id_order=false){
             ?>
             <script>
                 document.title="<?=$this->multi['TXT_YOUR_CART'];?>. <?=META_TITLE?>";
             </script>
             <?


             $q = "select * from `".TblModTmpOrder."` where `sessid`='".$this->Logon->session_id."' order by id";
             if ( $id_order )
                 $q = "Select * from `".TblModOrder."` where `id_order`='".$id_order."' ORDER BY `id`";
             $res = $this->db->db_Query($q);
    //        echo '$q = '.$q.' $res='.$res;
             if(!$res) return false;
             $rows = $this->db->db_GetNumRows();
             if($rows==0) {
                 $Err = $this->showErr();
                 echo View::factory('/modules/mod_order/templates/tpl_full_cart_empty.php')
                     ->bind('multi', $this->multi)
                     ->bind('Err', $Err);

                 return false;
             }
             else{
                 $str_prop = '';
                 for($i=0;$i<$rows;$i++){
                     $row = $this->db->db_FetchAssoc();
                     $arrOrder[$row['prod_id']] = $row;
                     if($i>0) $str_prop .= ',';
                     $str_prop .= $row['prod_id'];
                     $row_order_data[ $row['prod_id'] ] = $row;
                 }
                 $settings = $this->Catalog->settings;
                 //var_dump($settings);
                 if (isset($settings['short_descr']) AND $settings['short_descr'] == '1') {
                     $filed_list = ", `" . TblModCatalogPropSprShort . "`.`name` AS `short`";
                     $left_join = "\n LEFT JOIN `" . TblModCatalogPropSprShort . "` ON (`" . TblModCatalogProp . "`.`id`=`" . TblModCatalogPropSprShort . "`.`cod` AND `" . TblModCatalogPropSprShort . "`.`lang_id`='" . $this->lang_id . "')";
                 }

                 $q = "SELECT
                    `".TblModCatalogProp."`.*,
                     `".TblModPropGroupPrice."`.`price` as group_price,
                    `".TblModPropGroupPrice."`.`group_currency` as currency_group_price,
                    `".TblModCatalogPropSprName."`.name,
                    `".TblModCatalogPropImg."`.`path` AS `first_img`,
                    `".TblModCatalogPropImgTxt."`.`name` AS `first_img_alt`,
                    `".TblModCatalogPropImgTxt."`.`text` AS `first_img_title`,
                    `".TblModCatalogSprManufac."`.`name` as `name_brend`
                    ".$filed_list."
                      FROM `".TblModCatalogProp."`
                        LEFT JOIN `".TblModCatalogPropImg."` ON (`".TblModCatalogProp."`.`id`=`".TblModCatalogPropImg."`.`id_prop` AND `".TblModCatalogPropImg."`.`move`='1' AND `".TblModCatalogPropImg."`.`show`='1')
                        LEFT JOIN `".TblModCatalogPropImgTxt."` ON (`".TblModCatalogPropImg."`.`id`=`".TblModCatalogPropImgTxt."`.`cod` AND `".TblModCatalogPropImgTxt."`.lang_id='".$this->lang_id."')
                        LEFT JOIN `".TblModCatalogSprManufac."` ON (`".TblModCatalogProp."`.`id_manufac`=`".TblModCatalogSprManufac."`.`cod` AND `".TblModCatalogSprManufac."`.lang_id='".$this->lang_id."')
                        ".$left_join."
                    ,`".TblModCatalogPropSprName."`,`".TblModPropGroupPrice."`
                      WHERE `".TblModCatalogProp."`.id=`".TblModCatalogPropSprName."`.cod
                      AND `".TblModCatalogProp."`.id  in(".$str_prop.")
                      AND `".TblModCatalogProp."`.visible='2'
                      AND `".TblModCatalogPropSprName."`.lang_id='"._LANG_ID."'
                      AND
                    `" . TblModCatalogProp . "`.id  =`".TblModPropGroupPrice."`.prod_id
                  AND
                    `".TblModPropGroupPrice."`.group_id = ".$this->group_user_id."
                      ORDER BY id desc ";


                 $res = $this->db->db_Query($q);
                 //echo '$q = '.$q.' $res='.$res;
                 if(!$res) return false;
                 $rows = $this->db->db_GetNumRows();
                 if($rows>0){
                     for($i=0;$i<$rows;$i++){
                         $arrProp[$i] = $this->db->db_FetchAssoc();
                     }
                     $arrShow = array();
                     $quantityArr = array();
                     $massPrice = array();
                     for($i=0;$i<$rows;$i++){
                         $row = $arrProp[$i];
                         $rowOrder = $arrOrder[$row['id']];
                         $rowShow['id'] = $rowOrder['id'];
                         $name = stripslashes($row['name']);
                         $rowShow['name'] = $name;
                         $rowShow['name_brend'] = stripslashes($row['name_brend']);
                         $rowShow['number_name'] = stripslashes($row['number_name']);
                         $rowShow['art_num'] = stripslashes($row['art_num']);
                         $rowShow['link'] = $this->Catalog->Link($row['id_cat'], $row['id']);
                         $rowShow['short'] = strip_tags( stripslashes($row['short']), '<br><br/><p><a><div>');
                         //$rowShow['short'] = stripslashes($row['short']);

                         /*доние по картинке товара*/
                         $row_img = $row['first_img'];
                         if(!empty($row_img)){
                             $path = $this->Catalog->ShowCurrentImage($row_img,'size_auto=150',85,NULL,NULL,$row['id'],true);
                             if(empty($row['first_img_alt'])) $row['first_img_alt'] = $name;
                             $rowShow['img_alt'] = $row['first_img_alt'];
                             if(empty($row['first_img_title'])) $row['first_img_title'] = $name;
                             $rowShow['img_title'] = $row['first_img_title'];
                         }else{
                             $path = '/images/design/no-image.png';
                             $rowShow['img_alt'] = 'Нет фото';
                             $rowShow['img_title'] = 'Нет фото';
                         }
                         $rowShow['src'] = $this->Catalog->ShowCurrentImage($path,'size_width=110',85,NULL,NULL,$row['id'],true);

                         /*дание по цене, количеству, сумме товара*/
                         $curr = $row['currency_group_price'];

                        if (!$id_order){
                            $curr = $row['currency_group_price'];
                            $price = $row['group_price'];
                        }
                        else{
                            $curr =  $row_order_data[ $row['id'] ]['currency'];
                            $price = $row_order_data[ $row['id'] ]['price'];
                        }

                        $group_price = $this->currency->Converting($curr, _CURR_ID, $price);


                        if($curr==1 && $this->group_user_id==5){
                            $group_price = round($group_price);
                        }


    //                    echo '<br>$price='.$price;
                         $quantityProp = $rowOrder['quantity'];
                         $summa = $this->currency->Converting(_CURR_ID, _CURR_ID, $group_price * $quantityProp, 2, 'max');
                         $rowShow['group_price'] = $this->currency->ShowPrice($group_price);
    //                    echo '<br>$price='.$rowShow['price'];
                         $rowShow['quantity'] = $quantityProp;
                         $rowShow['summa'] = $this->currency->ShowPrice($summa);
                         $massPrice[$i] = $group_price;
                         $quantityArr[$i] = $quantityProp;

                         /*формирование фронтенд массива корзинни*/
                         $arrShow[$i] = $rowShow;
                     }

                     $summ_all_price = $this->suma( $massPrice, $quantityArr, $this->is_discount, false );
    //        echo '$summ_all_price='.$summ_all_price;
                     $summ_all = $this->currency->ShowPrice($summ_all_price);

                     $this->sumAllFromData = $summ_all;


                     if($onlysum){
                         exit($this->sumAllFromData);

                     }

                     $this->arrData = $this->Step3_OrderUserDetails();

                     $this->includeJS[] = SITE_PATH.'/js/clamp.js/clamp.js.min';

                     if ( $id_order ){
                         //show view for order histoty
                         echo View::factory('/modules/mod_order/templates/tpl_history_prop_list.php')
                             ->bind('arr', $arrShow)
                             ->bind('rows', $rows)
                             ->bind('multi', $this->multi)
                             ->bind('summ_all', $summ_all);
                     }
                     else {
                         // show full cart view
                         echo View::factory('/modules/mod_order/templates/tpl_full_cart.php')
                             ->bind('arr', $arrShow)
                             ->bind('rows', $rows)
                             ->bind('multi', $this->multi)
                             ->bind('summ_all', $summ_all);
                     }
                 }

             }
         } // end of  function full_cart


        // ================================================================================================
        // Function : AskPassword()
        // Date : 19.07.2006
        // Returns :      true,false / Void
        // Description :  show links category
        // Programmer :   Igor Trokhymchuk
        // ================================================================================================
        function AskPassword()
        {
            $UserShow = new UserShow();
            $UserShow->LoginPageOrder( _LINK.'order/step3/' );
            return true;
        } //end of function AskPassword




         function Step3_OrderUserDetails() {
             ob_start();

             if (!empty($this->user_id)) {
                 $User = new User();
                 $user_data = $User->GetUserDataByUserId( $this->user_id );
                 //var_dump($user_data);
             }

             if( empty($this->Err )AND isset($user_data['name']) and !empty($user_data['name']))
                 $name = stripslashes($user_data['name']);
             else
                 $name = stripslashes($this->name);

             if( empty($this->Err) AND isset($user_data['phone_mob']) and !empty($user_data['phone_mob']))
                 $phone_mob = stripslashes($user_data['phone_mob']);
             else
                 $phone_mob = stripslashes($this->phone_mob);


             if( empty($this->Err) AND isset($user_data['city']) and !empty($user_data['city']))
                 $city = stripslashes($user_data['city']);
             else
                 $city = stripslashes($this->city);


             if( empty($this->Err )AND isset($user_data['email']) and !empty($user_data['email']))
                 $email = stripslashes($user_data['email']);
             else
                 $email = stripslashes($this->alias);
                 //var_dump($email);
             ?>

             <div class="subBody">

                 <div class="data-user-order">Оформить заказ</div>

                 <div align="center"><? $this->showErr(); ?></div>

                 <form name="order_comment" method="post" id="order_comments" action="/order/step4/" onclick="yaCounter25263488.reachGoal('zakazohrana'); return true;">
                     <div class='item-form'>
                         <div>
                         <span class="title-order">ФИО</span><span>*</span><br>
                         <input type="text" name="name" class="validate[required]" size="40" value="<?=$name?>">
                         </div>
                         <div>
                             <div>
                                 <span class="title-order">Телефон</span><span>*</span><br>
                                 <input id="phoneMob" type="text" name="phone_mob" class="" size="40" value="<?=$phone_mob?>">
                             </div>
                         <span class="title-order">Email</span><br>
                         <input type="text" name="alias" class="validate[custom[email]]" size="40" value="<?=$email?>">
                         </div>

                         <div>
                             <span class="title-order">Комментарий к заказу</span><br>
                             <input type="text" name="comment"></textarea>
                         </div>

                     </div>
                     <div class='item-form'>

                         <?
                         $selectItem2 = "<option></option>";
                         $this->db = new DB();
                         $q = "Select * from `" . TblModOrderSprPayMethod . "` where `lang_id`='" . _LANG_ID . "' ORDER BY `move`";
                         $res = $this->db->db_Query($q);
                         if (!$res)
                             return false;
                         $rows = $this->db->db_GetNumRows();
                         $k = 1;


                         for ($i = 0; $i < $rows; $i++) {
                             $row = $this->db->db_FetchAssoc();

                             if (!empty($this->pay_method))
                                 $val = $this->pay_method;
                             else
                                 $val = 1;
                             $selectItem2 .= "<option value='" . $row['cod'] . "'>" . $row['name'] . "</option>";
                         }
                         ?>
                         <div>
                             <span class="title-order">Вариант оплаты</span><br>
                             <label class="pay_method" for="pay_method">
                                <select data-placeholder="Вариант оплаты" name="pay_method" class="chosen-select " ><?= $selectItem2 ?></select>
                             </label>
                         </div>
                         <!------------------------------------------------------------------------------------------------------------------>

                         <?
                         $q = "Select * from `" . TblModOrderSprDelivery . "` where `lang_id`='" . _LANG_ID . "' ORDER BY `move`";
                         $res = $this->db->db_Query($q);
                         if (!$res)
                             return false;
                         $rows = $this->db->db_GetNumRows();
                         $selectItem = "<option></option>";


                         for ($i = 0; $i < $rows; $i++) {
                             $row = $this->db->db_FetchAssoc();
                             $short = stripslashes($row['short']);
                             if (!empty($this->delivery_method))
                                 $val = $this->delivery_method;
                             else
                                 $val = 1;
                             $delivery_price = '';
                             if (($short == 0) or empty($short))
                                 $delivery_price = ''; //$this->multi['TXT_FRONT_DELIVERY_COST_FREE'];
                             else {
                                 $delivery_price = $this->currency->Converting($this->delivery_price_currency, _CURR_ID, $short, 2);
                                 $delivery_price = '(' . $this->currency->ShowPrice($delivery_price) . ')';
                             }
                             ?>

                             <?
                             $selectItem .= '<option value="' . $row['cod'] . '">' . $row['name'] . '</option>';
                         }
                         ?>
                         <div>
                             <span class="title-order">Вариант доставки</span><br>
                         <label class="delivery_method" for="delivery_method">
                             <select id="deliveryMethod" data-placeholder="Вариант доставки" name="delivery_method" class="chosen-select ">
                                 <?= $selectItem ?>
                             </select>
                         </label>

                         <? if (isset($_GET['test'])) $class='class="test"'; else $class = ''; ?>
                         <div <?=$class?>style="position:relative">
                             <!-- nova poshta -->
                             <div id="novaPoshta" style="display:none;">
                                 <span class="title-order">Адрес доставки</span>
                                 <input type='button' class='button_np gmap-btn' title='Подобрать на карте' value='' href='/np/index.php' />
                                 <a href="/np/index.php" class='button_np' style="margin-right: 10px">Подобрать на карте</a>

                                 <link rel="stylesheet" href="/np/js/colorbox.css"/>
                                 <link rel="stylesheet" href="/np/js/button.css"/>
                                 <script type="text/javascript" src="/np/js/jquery.colorbox-min.js"></script>
                                 <script type="text/javascript" src="/np/script.js"></script>
                                 <textarea id="npAdress" class="npochta" name="np_adr"
    placeholder="Для поиска введите ключевые слова,
    например: Киев Набережная" style="margin-bottom: 2px;">
                                     <?//=$city?>
                                 </textarea>
                             </div>
                             <!-- end nova poshta -->
                             <div id="deliveryAddress">
                                 <span class="title-order">Адрес доставки</span>
                                 <textarea id="otherAdress" name="adr"><?=$city?></textarea>
                             </div>
                             <script>
                                 $(document).ready(function(){
                                     $(".npochta").val("");
                                     $( ".button_np" ).hide();
                                     $("#novaPoshta").hide();

                                     $("#deliveryMethod").change( function(){
                                         val = $(this).val();
                                         console.log('val' + val);
                                         if ( val == 1 ) { //nova poshta

                                             $("#deliveryAddress").hide();
                                             $("#novaPoshta").show();
                                             $( ".button_np" ).show(300, 'linear');
                                         }
                                         else{
                                             $( ".button_np" ).hide(300, 'linear');
                                             $("#novaPoshta").hide();
                                             $("#deliveryAddress").show();
                                         }
                                     });
                                 });
                             </script>
                         </div>

                     </div>
                     </div>
                         <div class="item-form margint20">
                             <div class="sum-order display-none-mob">
                                 <span>Итого к оплате: </span><div id="sum"><?echo $this->sumAllFromData;?></div>
                             </div>
                             <div class="orderBtn" align="right">
                                 <div class="msg" align="right" style="width:80px;"></div>
                                 <input class="buy-btn" type="button"  onclick="validForm()" value="Оформить заказ→">
                             </div>
                         </div>
                 </form>
             </div>









         <?
             return ob_get_clean();
         }
        // ================================================================================================
        // Function : Step3_OrderUserDetails()
        // Date : 06.11.2009
        // Returns :      true,false / Void
        // Programmer : Yaroslav Gyryn
        // ================================================================================================
        function Step3_OrderUserDetails8(){

         if(!empty($this->user_id)) {
             $q = "SELECT * FROM `".TblSysUser."` WHERE `id`='".$this->user_id."'";
             $res1 = $this->db->db_Query( $q );
             $row = $this->db->db_FetchAssoc($res1);

             $User = new User();
             $user_data = $User->GetUserDataByUserEmail($row['login']);

             //print_r($user_data);
         }
         if( empty($this->Err )AND isset($user_data['name']) and !empty($user_data['name']))
            $name = stripslashes($user_data['name']);
         else
            $name = stripslashes($this->name);

         if( empty($this->Err) AND isset($user_data['phone_mob']) and !empty($user_data['phone_mob']))
            $phone_mob = stripslashes($user_data['phone_mob']);
         else
            $phone_mob = stripslashes($this->phone_mob);

         if( empty($this->Err) AND isset($user_data['phone']) and !empty($user_data['phone']))
            $phone = stripslashes($user_data['phone']);
         else
            $phone = stripslashes($this->phone);

         if( empty($this->Err) AND isset($user_data['adr']) and !empty($user_data['adr']))
            $adr = stripslashes($user_data['adr']);
         else
            $adr = stripslashes($this->adr);

         if( empty($this->Err) AND isset($user_data['city']) and !empty($user_data['city']))
            $city = stripslashes($user_data['city']);
         else
            $city = stripslashes($this->city);

         if( empty($this->Err) AND isset($user_data['comment']) and !empty($user_data['comment']))
            $comment = stripslashes($user_data['comment']);
         else
            $comment = stripslashes($this->comment);

        if( empty($this->Err )AND isset($row['alias']) and !empty($row['alias']))
            $alias = stripslashes($row['alias']);
         else
            $alias = stripslashes($this->alias);

         ?>
        <div class="chapterCaption "><div class="star">Оформление заказа</div></div>
        <div class="subBody">
             <div align="center"><?$this->showErr();?></div>

           <form name="order_comment" method="post" id="order_comments" action="/order/step4/" onclick="yaCounter25263488.reachGoal('zakazohrana'); return true;">
            <table border="0" cellpadding="2" cellspacing="1" align="center" class="tblRegister">
              <tr>
                  <td valign="top" width="100">
                    ФИО
                    <span style="color:#FF0000"> *</span>
                  </td>
                  <td>
                    <input type="text" name="name" size="40" value="<?=$name?>">
                  </td>
              </tr>
                <tr>
                    <td valign="top">
                        Телефон
                        <span style="color:#FF0000"> *</span>
                    </td>
                    <td>
                        <input type="text" name="phone_mob" size="40" value="<?=$phone_mob?>">

                    </td>
                </tr>

              <tr>
                  <td valign="top">
                    <?=$this->multiUser['FLD_EMAIL'];?>
                    <span style="color:#FF0000"> </span>
                  </td>
                   <td>
                    <input type="text" name="alias" size="40" value="<?=$alias?>">
                  </td>
              </tr>


              <tr>
                  <td valign="top">
                    Адрес доставки
                    <span style="color:#FF0000"> *</span>
                  </td>
                   <td>
                    <input type="text" name="city" size="40" value="<?=$city?>">
                  </td>
              </tr>

              <tr>
               <td valign="top"><?=$this->multi['TXT_FRONT_DELIVERY_METHOD'];?>:</td>
               <td>
               <?
                $q = "Select * from `".TblModOrderSprDelivery."` where `lang_id`='"._LANG_ID."' ORDER BY `move`";
                $res = $this->db->db_Query( $q );
                if( !$res ) return false;
                $rows = $this->db->db_GetNumRows();
                $k=1;

                for($i=0; $i<$rows; $i++){
                    $row = $this->db->db_FetchAssoc();
                    $short = stripslashes($row['short']);
                    if(!empty($this->delivery_method)) $val = $this->delivery_method;
                    else $val = 1;
                    $delivery_price = '';
                    if( ($short==0) or empty($short) )
                        $delivery_price = '';//$this->multi['TXT_FRONT_DELIVERY_COST_FREE'];
                    else {
                        $delivery_price = $this->currency->Converting($this->delivery_price_currency, _CURR_ID, $short, 2);
                        $delivery_price = '('.$this->currency->ShowPrice($delivery_price).')';
                    }
                    ?>
                    <div class="radioBtn">
                        <div style="float:left; margin:0px 4px 0px 0px;"><?$this->Form->Select('delivery_method', $row['cod'], $val);?></div>
                        <div><strong><?=$i+1;?>. <?=$row['name']?></strong>&nbsp;<?=$delivery_price;?><br /><?=strip_tags($row['descr'], "<b><a><strong>")?></div>
                    </div>
                    <?
                    $k++;
                }
               ?>
               </td>
              </tr>
              <tr>
               <td valign="top"><?=$this->multi['TXT_FRONT_PAY_METHOD'];?>:</td>
               <td>
               <?
                $this->db = new DB();
                $q = "Select * from `".TblModOrderSprPayMethod."` where `lang_id`='"._LANG_ID."' ORDER BY `move`";
                $res = $this->db->db_Query( $q );
                if( !$res ) return false;
                $rows = $this->db->db_GetNumRows();
                $k=1;

                for($i=0; $i<$rows; $i++){
                    $row = $this->db->db_FetchAssoc();
                    if(!empty($this->pay_method)) $val = $this->pay_method;
                    else $val = 1;
                    ?>
                    <div class="radioBtn">
                        <div style="float:left; margin:0px 4px 0px 0px;"><?$this->Form->Radio('pay_method', $row['cod'], $val);?></div>
                        <div><strong><?=$i+1;?>. <?=$row['name']?></strong><br /><?=strip_tags($row['descr'], "<b><a><strong>")?></div>
                    </div>
                    <?
                    $k++;
                }
               ?>
               </td>
              </tr>
              <tr>
                  <td valign="top">
                    <?=$this->multiUser['FLD_COMMENT'];?>:
                  </td>
                   <td>
                    <textarea name="comment" ROWS="5" COLS="37"><?=$comment?></textarea>
                  </td>
              </tr>
              <tr>
                  <td valign="top" colspan="2">
                  <ins><?=$this->multiUser['TXT_NEED_FIELDS'];?></ins>
                  </td>
              </tr>
            </table>


            <div class="orderBtn" align="center">
                <?/*<input type="button" value="<?=$this->multiUser['FLD_BACK']?>" onclick="makeRequest('<?=_LINK;?>order/', 'task=full_cart', 'my_d_basket'); return false;" />*/?>
                <?/*<input type="button" value="<?=$this->multiUser['FLD_BACK']?>" onclick="javascript:history.back(); return false;" /> */?>
                <input type="image" title="<?=$this->multiUser['FLD_BACK']?>" alt="<?=$this->multiUser['FLD_BACK']?>" src="/images/design/btnBack.gif" onclick="javascript:history.back(); return false;">
                <?/*<input type="submit" value="<?=$this->multi['TXT_NEXT_STEP'];?>" />*/?>
                <input type="image" title="<?=$this->multi['TXT_NEXT_STEP'];?>" alt="<?=$this->multi['TXT_NEXT_STEP'];?>" src="/images/design/btnSubmit.gif">
            </div>
            <?
            $this->Form->Hidden("is_discount", $this->is_discount);
            ?>
           </form>
          </div>
         <?
         } // end of OrderUserDetails


         // ================================================================================================
        // Function : CheckUserData()
        // Date : 06.11.2009
        // Returns :      true,false / Void
        // Description :  check Users contect data for order
        // Programmer : Yaroslav Gyryn
        // ================================================================================================
         function CheckUserData(){
             $this->Err = '';
             if(empty($this->name))
                $this->Err .= $this->multiUser['MSG_FLD_NAME_EMPTY'].'<br/>';

             if (!empty( $this->phone_mob )) {
                if( !preg_match("#^38\([0-9]{3}\)[0-9]{7}$#", $this->phone_mob) )
                    $this->Err .= $this->multiUser['MSG_NOT_VALID_PHONE_MOB'].'<br/>';
                else{
                    $user_id = NULL;

                    $User = check_init('UserShow', 'UserShow');

                    $User->phone_mob = $this->phone_mob;
                    $res = $User->CheckFields();

                    //if no such user - regiter him
                    if (empty($res)) {
                        $User->email = $this->alias;
                        $User->login = $this->phone_mob;
                        $User->name = $this->name;

                        $User->user_id = $User->SaveToSysUser();
                        $user_id = $User->user_id;
                        if ($User->user_id) {
                            $User->user_status = 3; //зарегистрирован
                            $res = $User->SaveUser();
                        }
                    }
                    //check email

                    if ( !empty($this->alias) ){
                        $user_data = $User->GetUserDataByUserLogin($this->phone_mob);
                        if ( $user_data != false) {
                            $_email = $user_data['email'];
                            //var_dump( $_email, $this->alias );
                            if ($_email !== $this->alias) {
                                //update email
                                $q = "UPDATE `" . TblSysUser . "` SET `email`='" . $this->alias . "' WHERE `id`='" . $user_data['id'] . "'";
                                //echo '<br/>'.$q;
                                $res = $this->db->db_Query($q);
                            }
                        }
                    }
                }
             }
             else
                $this->Err .= $this->multiUser ['MSG_PHONE_MOB_FLD_EMPTY'].'<br/>';

    //         if (empty( $this->alias )) {
    //            $this->Err .= $this->multiUser['MSG_FLD_EMAIL_EMPTY'].'<br/>';
    //         }
    //         else {
    //            if (!preg_match("/^[a-zA-Z0-9_.\-]+@[a-zA-Z0-9.\-].[a-zA-Z0-9.\-]+$/", $this->alias))
    //            //if (!preg_match("/^[a-zA-Z0-9_./\-]+@[a-zA-Z0-9./\-].[a-zA-Z0-9./\-]+$^", $this->alias))
    //                    $this->Err .=  $this->multiUser['MSG_NOT_VALID_EMAIL'].'<br/>';
    //         }
             /*
             if (!empty( $this->phone )) {
                if ( !ereg("^[+0-9]", $this->phone) )
                    $this->Err .= $this->multiUser['MSG_NOT_VALID_PHONE'].'<br/>';
             }
             else
                    $this->Err .= $this->multiUser['MSG_PHONE_FLD_EMPTY'].'<br/>';
             */

    //         if(empty($this->city))
    //            $this->Err .= $this->multiUser['MSG_CITY_FLD_EMPTY'].'<br />';
    //
    //         if(empty($this->delivery_method)) $this->Err .= $this->multi['MSG_ERR_SELECT_DELIVERY_METHOD'].'<br />';
    //
    //         //echo '<br>$this->pay_method='.$this->pay_method;
    //         if(empty($this->pay_method)) $this->Err .= $this->multi['MSG_ERR_SELECT_PAY_METHOD'].'<br />';

             return $this->Err;
         } // end of CheckUserData



         // ================================================================================================
        // Function : Step4_OrderDetails()
        // Date : 14.10.2009
        // Returns :      true,false / Void
        // Description :  Final step of order checkout
        // Programmer : Yaroslav Gyryn
        // ================================================================================================
         function Step4_OrderDetails($is_discount=false)
         {
          $db1 = new DB();
         ?>
         <script type="text/javascript" src="/include/js/highslide/highslide.js"></script>
          <script type="text/javascript">
                hs.graphicsDir = '/include/js/highslide/graphics/';
                hs.outlineType = 'rounded-white';
          </script>
         <div class="chapterCaption "><div class="star"><?=$this->multi['TXT_ORDER_CONFIRMATION'];?></div></div>
         <div class="subBody">
            <?/*<h1><?=$this->multi['TXT_ORDER_CONFIRMATION'];?></h1>*/?>
           <div class="orderFirstStepTxt">
            <?=$this->multi['TXT_FOURTH_STEP'];?>
           </div>
                      <?
                     $db = new DB();
                     $catalog = new CatalogLayout();
                     $settings = $catalog->GetSettings(1);
                     $q = "select * from `".TblModTmpOrder."` where 1 and `sessid`='".$this->Logon->session_id."' order by id";
                     $res = $db->db_Query($q);
                     //echo 'Query = '.$q. '  $res= '.$res;
                     $rows = $db->db_GetNumRows();

                     if($rows==0)  {
                        ?><div class="submit" align="center"><h3><?=$this->multi['TXT_EMPTY_CART2'];?><h3></div><?
                     }
                     else  {
                     $currentValuta = $this->Spr->GetNameByCod( TblSysCurrenciesSprShort, _CURR_ID, $this->lang_id, 1 );
                     ?>
                     <table cellspacing="0" cellpadding="6" border="0" width="100%" class="tblOrder">
                        <tr align="center">
                        <?/*<th>
                            <?=$this->multi['TXT_COD'];?>
                        </th>*/?>
                        <th  align="left">
                            <?=$this->multi['FLD_PROD_ID'];?>
                        </th>
                        <th width="60">
                            <?=$this->multi['TXT_PHOTO'];?>
                        </th>
                        <th width="60">
                            <?=$this->multi['FLD_QUANTITY'];?>
                        </th>
                        <th width="70">
                            <?=$this->multi['TXT_PRICE'];?>
                        </th>
                        <th width="70">
                            <?=$this->multi['FLD_SUMA']?>
                        </th>
                     </tr>
                     <?

                     $i = 0;
                     $mass = NULL;
                     $quantity = NULL;
                     $styleRow = 'styleRow1';

                     while($row=$db->db_FetchAssoc())
                     {
                         $q = "SELECT `".TblModCatalogProp."`.*,
                                 `".TblModCatalogPropSprName."`.name
                          FROM `".TblModCatalogProp."`,`".TblModCatalogPropSprName."`
                          WHERE `".TblModCatalogProp."`.id=`".TblModCatalogPropSprName."`.cod
                          AND `".TblModCatalogProp."`.id ='".$row['prod_id']."'
                          AND `".TblModCatalogProp."`.visible='2'
                          AND `".TblModCatalogPropSprName."`.lang_id='"._LANG_ID."' ";
                     $q = $q." ORDER BY dt desc, id desc ";
                     $res = $db1->db_Query( $q );
                     //echo '<br/>$q = '.$q.'<br/>';
                     $row1 = $db1->db_FetchAssoc();

                     $row_img = $catalog->GetPicture($row['prod_id']);
                     $prod_id_cat = $catalog->GetCategory($row['prod_id']);

                      //====================== Get parameters and image by parameters START =======================
                       $this->arr_current_img_params_value = NULL;
                       $catalog->id = $row['prod_id'];
                       /*if( !empty($row['parameters']) ){
                           $this->arr_current_img_params_value = $this->GetParametersToArray( $row['parameters'] );
                           $catalog->arr_current_img_params_value = $this->arr_current_img_params_value;
                           $this->img = $catalog->GetImageToShowByParams();
                       }//end if
                       else*/ {
                         $this->img = $catalog->GetFirstImgOfProp($row['prod_id']);
                       }
                      // echo '<br> $this->img='.$this->img;
                       //====================== Get parameters and image by parameters END =======================


                     if( $catalog->mod_rewrite==1 )
                        $link = $catalog->Link($prod_id_cat, $row['prod_id']);
                     else
                        $link = "catalog_".$prod_id_cat."_".$row['prod_id']."_".$catalog->lang_id.".html";

                      $name = $row1['name'];
                      //$name = $catalog->Spr->GetNameByCod( TblModCatalogPropSprName, $row['prod_id'], $catalog->lang_id, 1 );
                      //$category_name = $catalog->Spr->GetNameByCod( TblModCatalogSprName, $prod_id_cat, $catalog->lang_id, 1 );

                      $price0 = $row1['price'];
                      //$price0 = $catalog->GetPrice($row['prod_id']);
                      $curr = $catalog->GetPriceCurrency($row['prod_id']);
                      $price = $this->currency->Converting($curr, _CURR_ID, $price0);
                      $price = $this->currency->ShowPrice($price, true);

                     if( !isset($this->logon->user_type) || $this->logon->user_type<5) {
                         $this->group_user_id = 5;
                         }else{
                         $this->group_user_id = $this->logon->user_type;
                         }

                      if($curr==1 AND $this->group_user_id==5) {
                          $price = round($price);
                      }

                      $summa = $this->currency->Converting(_CURR_ID, _CURR_ID, $price * $row['quantity'], 2, 'max');
                      $mass[$i] = $price;
                      $quantity[$i] = $row['quantity'];

                      if($styleRow =='styleRow1')
                        $styleRow  = 'styleRow2';
                     else
                        $styleRow  = 'styleRow1';
                      ?>
                     <tr class="<?=$styleRow;?>" align="center">
                        <?/*<td><?=$row1['number_name']?></td>*/?>
                        <td align="left">
                        <a href="<?=$link?>"><?=$name;?></a></td>
                        <td align="center"><?
                          if ( isset($row_img['0']['id']) ) {
                            /*?><a href="<?=$link?>" title="<?=$name?>"><?=$catalog->ShowCurrentImage($this->img, 'size_auto=120', 85, NULL, 'border=0')?></a><?*/
                            $path = "http://".NAME_SERVER.$settings['img_path']."/".$row['prod_id']."/".$row_img['0']['path'];
                               ?>
                               <a href="<?=$path;?>" class="highslide" onclick="return hs.expand(this, {captionId: 'caption<?=$row['prod_id'];?>'});"  title="Увеличить..." target="_blank">
                                 <img src="/images/design/icoPhotoCamera.gif"/ alt="<?=htmlspecialchars($name);?>" title="<?=htmlspecialchars($name);?>">
                               </a><?
                          }
                          ?>
                        </td>
                        <td><span class="price"><?=$row['quantity']?></span></td>
                        <td><span class="price"><?=$price;?></span></td>
                        <td><span class="price"><?=$summa;?></span></td>
                     </tr>
                     <?
                     //   <td class="prod_in_cart" width="110"><textarea name="comment['.$row['id'].']" class="comm_order" rows="4" cols="10">'.$row['comment'].'</textarea></td>
                     $i++;
                     }
                     ?>
                    </table>

                      <div style="padding-top: 10px;"><b><?=$this->multi['TXT_FRONT_DELIVERY_METHOD'];?>:</b>&nbsp;
                       <?
                       $delivery_data = $this->Spr->GetDataByCod(TblModOrderSprDelivery, $this->delivery_method, $this->lang_id, 1 );
                       $delivery_price = $this->currency->Converting($this->delivery_price_currency, _CURR_ID, stripslashes($delivery_data['short']), 2);
                       echo stripslashes($delivery_data['name']);
                       ?>
                        <div class="priceBack" style="float: right;">
                            <span><?=$delivery_price;?></span>.
                        </div>
                      </div>
                     <?
                    $o_price = $this->suma( $mass, $quantity, $this->is_discount);
                    $o_price += $delivery_price;
                    ?>
                    <div class="clearing"></div>
                    <div class="priceBack" style="float: right;">
                                <span><?=$o_price;?></span>.
                            </div>
                            <div class="totalSum"><?=$this->multi['TXT_TOTAL_COST'];?></div>
                            <input type="hidden" name="is_discount" value="<?=$this->is_discount;?>">

                      <?/*<tr class="orderTotal">
                          <td colspan="4">
                                <span><?=$this->multi['TXT_TOTAL_COST'];?></span>
                          </td>
                          <td colspan="2" align="center">
                            <span><?=$this->currency->ShowPrice($o_price);?></span>
                          </td>
                      </tr> */?>
                      <?
                     }
                      ?>
                      <div class="clearing"></div
                      <table cellspacing="1" cellpadding="2" border="0" width="100%" class="tblRegister" style="font-weight: normal;">
                          <tr class="styleRowName">
                              <td colspan="2">
                                <span><?=$this->multiUser['TXT_BUYER'];?></span>
                              </td>
                          </tr>

                          <tr class="styleRow1">
                              <td width="150">
                                <?=$this->multiUser['FLD_NAME'];?>
                              </td>
                              <td>
                                <?=stripslashes($this->name);?>
                              </td>
                          </tr>

                          <tr class="styleRow1" >
                              <td>
                                <?=$this->multiUser['FLD_PHONE_MOB'];?>
                              </td>
                              <td>
                                <?=stripslashes($this->phone_mob);?>
                              </td>
                          </tr>

                          <tr class="styleRow1">
                              <td>
                                <?=$this->multiUser['FLD_PHONE'];?>
                              </td>
                              <td>
                                <?=stripslashes($this->phone);?>
                              </td>
                          </tr>

                          <tr class="styleRow1">
                              <td>
                                <?=$this->multiUser['FLD_EMAIL'];?>
                              </td>
                               <td>
                                <?=stripslashes($this->alias);?>
                              </td>
                          </tr>

                          <tr class="styleRowName">
                              <td colspan="2">
                                <span><?=$this->multiUser['TXT_DOSTAVKA'];?></span>
                              </td>
                          </tr>

                          <tr class="styleRow1">
                              <td>
                                <?=$this->multiUser['FLD_CITY'];?>
                              </td>
                              <td>
                                <?=stripslashes($this->city);?>
                              </td>
                          </tr>

                          <tr class="styleRow1">
                              <td>
                                <?=$this->multiUser['FLD_ADR'];?>
                              </td>
                              <td>
                                <?=stripslashes($this->adr);?>
                              </td>
                          </tr>

                          <tr  class="styleRow1">
                              <td>
                                <?=$this->multiUser['FLD_COMMENT'];?>
                              </td>
                               <td>
                                <?=str_replace("\n", "<br>", stripslashes($this->comment));?>
                              </td>
                          </tr>

                          <tr  class="styleRow1">
                              <td>
                                <?=$this->multi['TXT_FRONT_PAY_METHOD'];?>
                              </td>
                               <td>
                                <?=$this->Spr->GetNameByCod(TblModOrderSprPayMethod, $this->pay_method, $this->lang_id, 1 );?>
                              </td>
                          </tr>
                      </table>

                      <form name="order_comment" method="post" action="<?=_LINK?>order/result/">
                        <?
                        $this->Form->Hidden("name", stripslashes($this->name) );
                        $this->Form->Hidden("phone_mob", stripslashes($this->phone_mob) );
                        $this->Form->Hidden("phone", stripslashes($this->phone) );
                        $this->Form->Hidden("alias", stripslashes($this->alias) );
                        $this->Form->Hidden("city", stripslashes($this->city) );
                        $this->Form->Hidden("adr", stripslashes($this->adr) );
                        $this->Form->Hidden("comment", stripslashes($this->comment) );
                        $this->Form->Hidden("delivery_method", stripslashes($this->delivery_method) );
                        $this->Form->Hidden("pay_method", stripslashes($this->pay_method) );

                        $o_price = $this->suma( $mass, $quantity, $is_discount, false );
                        $this->Form->Hidden("o_price", $o_price);
                        $U = new User();
                        $id_user = $U->GetUserIdByEmail($this->Logon->login);
                        $discount = $U->GetUserDiscount($id_user);
                        $this->Form->Hidden("u_discount", $discount);
                        $this->Form->Hidden("task", "make_order_finish");

                        ?>
                        <div class="orderBtn" align="center">
                            <?/*<input type="button" value="<?=$this->multiUser['FLD_BACK']?>" onclick="makeRequest('<?=_LINK;?>order/', 'task=full_cart', 'my_d_basket'); return false;" />
                            <input type="button" value="<?=$this->multiUser['FLD_BACK']?>" onclick="javascript:history.back();; return false;" />*/?>
                            <input type="image" title="<?=$this->multiUser['FLD_BACK']?>" alt="<?=$this->multiUser['FLD_BACK']?>" src="/images/design/btnBack.gif" onclick="javascript:history.back(); return false;">
                            &nbsp;
                            <input type="image" title="<?=$this->multiUser['BTN_SUBMIT']?>" alt="<?=$this->multiUser['BTN_SUBMIT']?>" src="/images/design/btnSubmit.gif">
                            <?/*<input type="submit" value="<?=$this->multiUser['BTN_SUBMIT']?>" />*/?>
                        </div>
                        <!--span class="txtCheckOrder">
                        </span-->
                      </form>


           <?/*<div class="orderHelpInfo">
              <?=$this->multi['TXT_HELP_INFO'];?>:
              <div class="orderHelpText">
                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$this->multi['TXT_HELP_CHECK_ORDER'];?>
                 <?//=$this->multi['TXT_HELP_AMOUNT'];?>
                 <?//=$this->multi['TXT_HELP_DELETE'];?>
              </div>
          </div>*/?>

          </div>
          <?
         } // end of Step4_OrderDetails


        // ================================================================================================
        // Function : SendOrderToEmail()
        // Date : 17.11.2009
        // returns true,false / Void
        // Description : send email with order to the user and on the admin-email
        // Programmer :   Yaroslav Gyryn
        // ================================================================================================
        function SendOrderToEmail( $id_order )
        {
            $this->Err = NULL;
            $mail_user = new Mail();
            $mail_admin = new Mail();
            $body = '';

            $o_comm = $this->GetOrderCommentInArr($id_order);
            $user_name = stripslashes($o_comm['name']);
            //-------- build body of email message START ----------
            //вступительная речь
    //        $body_0 = "
    //          <div>
    //	           ".$this->multi['_TXT_DEAR']."  ".$user_name.", ".$this->multi['_TXT_M_ORDER']."  <a href='http://".$_SERVER['SERVER_NAME']."'>".$_SERVER['SERVER_NAME']."</a>
    //               <br/>
    //          </div>
    //          ";

            /*
            <table style="font-family: 'PT Sans', sans-serif; width: 100%;" border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
            <td style="padding-left: 25px;" width="50%"><a href="ohrana.ua"><img title="ohrana.ua" src="http://ohrana.ua/images/design/ohrana_ua.jpg" alt="ohrana.ua" /></a></td>
            <td width="50%">
            <div style="display: inline-block; float: left; width: 50%; min-width: 18ex; text-align: center; white-space: nowrap;">
                <span style="display: table-cell; vertical-align: middle; font-size: 16px; letter-spacing: nowrap;"><img title="MTC" src="http://ohrana.ua/images/design/mts-phone-ico.jpg" alt="MTC" />&nbsp;(050)&nbsp;54-69-444</span>
            </div>
            <div style="display: inline-block; float: left; width: 50%; min-width: 18ex; text-align: center; white-space: nowrap;">
                <span style="display: table-cell; vertical-align: middle; font-size: 16px; letter-spacing: nowrap;"><img title="kyivstar" src="http://ohrana.ua/images/design/kievstar-phone-ico.jpg" alt="kyivstar"/>&nbsp;(098)&nbsp;54-38-444</span>
            </div>
            </td>
            </tr>
            </tbody>
            </table>
            */

            $shapka = '
            <table cellspacing="0" cellpadding="0" border="0" width="100%" style="font-family: \'PT Sans\', sans-serif;">
                <tr>
                    <td colspan="3" style="padding: 30px 0 15px 25px; font-size: 13px;">'.$this->multi['_TXT_DEAR'].' '.$user_name.', Вы сделали заказ товаров в интернет-магазине <a href="http://'.$_SERVER['SERVER_NAME'].'" style="color: #039a9a;">'.$_SERVER['SERVER_NAME'].'</a></td>
                </tr>';

            //дата и номер заказа
            $shapka .='
                <tr>
                    <td colspan="2" valign="middle" style="vertical-align: middle; height: 14px; padding: 8px 0 8px 25px; background: #585858; color: white; font-size: 15px;">'.$this->multi['FLD_ORDER_ID'].' '.$o_comm['id_order'].' от '.$o_comm['date'].'</td>
                </tr>
            </table>
            ';

            /* old code
               <br/> ".$this->multi['_TXT_S_ADDED']."
               <br/> ".$this->multi['_TXT_MANAGER']."
            */
            /* old code
                if(empty($this->FrontendPages))
                    $this->FrontendPages = new FrontendPages();
                $mail_shapka = $this->FrontendPages->GetPageData(11);
                $shapka =  stripslashes($mail_shapka ['content']);
                if(!empty($shapka))
                    $body .=$shapka;
            */

            //Всего по товарам
            $body .= '
                <table cellspacing="0" cellpadding="6" border="0" width="100%" style="font-family: \'PT Sans\', sans-serif;">
                    <tr style="text-align: center; font-weight: bold; font-size: 13px; background-color: #f5f8f7; border: 1px solid #e6e6e6; background:linear-gradient(to bottom,rgba(254,254,254,1) 0%,rgba(236,242,241,1) 100%);">
                        <td colspan="2" style="width: 60%; border-radius: 0 0 0 3px; text-align: center; border-left: 1px solid #e6e6e6; border-bottom: 1px solid #e6e6e6; border-top:1px solid #e6e6e6">Наименование товара</td>
                        <td style="border-top: 1px solid #e6e6e6; border-bottom: 1px solid #e6e6e6;">'.$this->multi['FLD_QUANTITY'].'</td>
                        <td style="border-top: 1px solid #e6e6e6; border-bottom: 1px solid #e6e6e6;">Цена</td>
                        <td style="border-radius: 0 0 3px 0; border-top: 1px solid #e6e6e6; border-bottom: 1px solid #e6e6e6; border-right: 1px solid #e6e6e6;">Сумма</td>
                    </tr>
            ';
            //$body .="<div><strong>".$this->multi['FLD_QUANTITY'].' '.$o_comm['qnt_all'].' '.$this->multi['TXT_FRONT_PCS'].' '.$this->multi['TXT_COST'].' '.$this->currency->ShowPrice($o_comm['sum']).'</strong></div>';

            //детально по товарам
            $order_arr = $this->GetProdOrdersByIdOrder($id_order);
            $cnt = sizeof($order_arr);
            for($i=0;$i<$cnt;$i++){
                $order_data = $order_arr[$i];
                $q_prod = "
                    SELECT
                        `".TblModCatalogProp."`.`number_name`,
                         `".TblModCatalogPropSprName. "`.`name`,
                         `" . TblModCatalogTranslit . "`.`translit`,
                         `" . TblModCatalogTranslit . "`.`id_cat`,
                         `" . TblModCatalogPropSprShort . "`.`name` AS `short`
                    FROM
                       `".TblModCatalogPropSprName."`,
                        `".TblModCatalogTranslit."`,
                        `".TblModCatalogProp."`
                            LEFT JOIN `" . TblModCatalogPropSprShort . "` ON (`" . TblModCatalogProp . "`.`id`=`" . TblModCatalogPropSprShort . "`.`cod` AND `" . TblModCatalogPropSprShort . "`.`lang_id`='" . $this->lang_id . "')
                    WHERE `".TblModCatalogProp."`.id = '".$order_data['prod_id']."'
                        AND `".TblModCatalogProp."`.id = `".TblModCatalogPropSprName."`.`cod`
                        AND `".TblModCatalogPropSprName."`.lang_id = '"._LANG_ID."'
                        AND `".TblModCatalogTranslit."`.`id_prop` = '".$order_data['prod_id']."'
                ";

                $res_prod = $this->db->db_Query($q_prod);
                //echo '<br>$q_prod='.$q_prod.' $res_prod='.$res_prod;
                $row_prod = $this->db->db_FetchAssoc();
                $name = stripslashes($row_prod['name']);
                $short = stripslashes($row_prod['short']);
                $short = str_replace('</p>', '<br/>',$short);
                $short = preg_replace('#<p.*?>#', '', $short);

                    //$name = $this->Spr->GetDataByCod(TblModCatalogPropSprName, $order_data['prod_id'], $this->lang_id, 1 );

                $row_img = $this->Catalog->GetPicture($order_data['prod_id']);
                $img_path = 'http://'.$_SERVER['SERVER_NAME'].'/'.$this->Catalog->ShowCurrentImage($row_img[0]['path'], 'size_square=99x77', 95, NULL, "alt='' title=''", $order_data['prod_id'], true);
                $img = '<img src="'.$img_path.'" alt="'.$name.'" title="'.$name.'"/>';
                $link = 'http://'.$_SERVER['SERVER_NAME'].$this->Catalog->Link($row_prod['id_cat'],$row_prod['translit']);
                //var_dump($link);

                //в письме отображаем стоисомть и сумму в той валюте, в которой пользователь делал заказ
                $price = $this->currency->ShowPrice($order_data['price']);
                $sum = $this->currency->ShowPrice($order_data['sum']);
                //$body .= ($i+1).$img.". <strong>".$name."</strong> / ".$order_data['quantity']." шт. / <br>";
                $body .= '
                <tr>
                   <td colspan="5"><a style="text-decoration: none; color: #039a9a; font-size: 17px; font-weight: bold;" href="'.$link.'">'.$name.'</a></td>
                </tr>
                <tr>
                    <td>
                        <a href="'.$link.'">'.$img.'</a>
                    </td>
                    <td align="left" valign="top" style="font-size: 11px;">'.$short.'</td>
                    <td style="color: #000000; font-size: 12px;font-weight: 600; text-align: center;">'.$order_data['quantity'].'</td>
                    <td style="color: #000000; font-size: 12px;font-weight: 600; text-align: center;">'.$price.'</td>
                    <td style="color: #000000; font-size: 12px;font-weight: 600; text-align: center;">'.$sum.'</td>
                </tr>
                ';

                //$mail->AddAttachment($img_path);
            } //end for

            $total_sum = $this->currency->ShowPrice($o_comm['sum']);

            $body .='
                <tr style="text-align: center; color: #039a9a; font-weight: bold; font-size: 19px; border: 1px solid #e6e6e6; background-color: #f5f8f7; background:linear-gradient(to bottom,rgba(254,254,254,1) 0%,rgba(236,242,241,1) 100%);">
                    <td colspan="2" style="width: 60%; text-align: left; padding: 11px 0 11px 15px; border-radius: 0 0 0 3px; border-left: 1px solid #e6e6e6; border-bottom: 1px solid #e6e6e6; border-top:1px solid #e6e6e6">Итого к оплате за:</td>
                    <td style="padding: 11px 0; border-top: 1px solid #e6e6e6; border-bottom: 1px solid #e6e6e6;">'.$o_comm['qnt_all'].' шт.</td>
                    <td colspan="2" style="text-align: right; padding: 11px 15px 11px 0; border-radius: 0 0 3px 0; border-top: 1px solid #e6e6e6; border-bottom: 1px solid #e6e6e6; border-right: 1px solid #e6e6e6;">'.$total_sum.'</td>
                </tr>
                </table>
            ';

            //способ доставки заказа
            $delivery_data = $this->Spr->GetDataByCod(TblModOrderSprDelivery, $o_comm['delivery_method'], $this->lang_id, 1 );
            //var_dump($delivery_data);
            if (!isset($delivery_data['name']) ) $delivery = '';
            else $delivery = stripslashes($delivery_data['name']);

            $body .= '
                <table border=0 cellspacing=2 cellpadding=0 style="margin: 25px 0 15px 30px; font-family: \'PT Sans\', sans-serif;">
                    <tr>
                        <td width="100"><b>'.$this->multi['_TXT_COSTOMER'].':</b></td>
                        <td>'.$user_name.'</td>
                    </tr>
                    <tr>
                        <td><b>'.$this->multiUser['FLD_PHONE_MOB'].':</b></td>
                        <td>'.stripslashes($o_comm['phone_mob']).'</td>
                    </tr>
                    <tr>
                        <td><b>'.$this->multiUser['FLD_EMAIL'].':</b></td>
                        <td>'.stripslashes($o_comm['email']).'</td>
                    </tr>
                    <tr>
                        <td style="width: 14em;"><b>Вариант оплаты:</b></td>
                        <td>' . $this->Spr->GetNameByCod(TblModOrderSprPayMethod, $o_comm['pay_method'], _LANG_ID, 1) . '</td>
                    </tr>
                    <tr>
                        <td><b>Вариант доставки:</b></td>
                        <td>'.$delivery.'</td>
                    </tr>
                    <tr>
                        <td><b>' . $this->multiUser['FLD_ADR'] . ':</b></td>
                        <td>' . stripslashes($o_comm['addr']) . '</td>
                    </tr>
                    <tr>
                        <td><b>'.$this->multiUser['FLD_COMMENT'].':</b></td>
                        <td>'.stripslashes($o_comm['comment']).'</td>
                    </tr>
            ';

            //даные личного кабинетаmodi
            if ( !empty($this->phone_mob) ) {
                $User = check_init('UserShow', 'UserShow');
                $pass = $User->GetUserPassword($this->phone_mob);
                $body .= '
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr><td colspan="2" nowrap>Для просмотра истории заказов зайдите в <a style="color: #039a9a;" href="http://ohrana.ua/myaccount/">личный кабинет</a></td>
                    <tr><td><b>Логин:</b></td><td>'.$this->phone_mob.'</td></tr>
                    <tr><td><b>Пароль:</b></td><td>'.$pass.'</td></tr>
                ';
            }

            $body .= '
                </table>

            ';

            /*
                $body .= '
                    <table style="font-family: 'PT Sans', sans-serif; width: 100%;" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                    <tr style="background-color: #585858;">
                    <td style="width: 14%;" width="14%">&nbsp;</td>
                    <td style="padding: 8px 0; font-size: 15px; color: white;" align="left">тел.: (050)&nbsp;54-69-444; (098)&nbsp;54-38-444.</td>
                    <td style="font-size: 15px; color: white;" align="right">e-mail: <a style="color: white; text-decoration: none;" href="mailto:info@ohrana.ua">info@ohrana.ua</a></td>
                    <td style="width: 14%;" width="14%">&nbsp;</td>
                    </tr>
                    <tr style="background-color: #28b498; background: linear-gradient(to bottom, rgba(68,198,172,1) 0%,rgba(4,157,126,1) 100%); filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#44c6ac', endColorstr='#049d7e',GradientType=0 );">
                    <td style="height: 72px; padding: 4px; font-size: 21px; color: white; text-align: center; vertical-align: middle;" colspan="4" valign="middle"><strong>C УВАЖЕНИЕМ, АДМИНИСТРАЦИЯ САЙТА.</strong></td>
                    </tr>
                    </tbody>
                    </table>
                ';
             */

            //способ оплаты заказа
            //$body .= "<div><strong>".$this->multi['TXT_FRONT_PAY_METHOD'].":</strong> ".$this->Spr->GetNameByCod( TblModOrderSprPayMethod, $o_comm['pay_method'], _LANG_ID, 1 )."</div>";

    //        if($o_comm['pay_method']==2){
    //            $oplata = $this->Spr->GetNameByCod( TblModPagesSprContent, 79, _LANG_ID );
    //        }



            //письмо целеком
            $body = $shapka.$body;
            //-------- build body of email message END ----------
            //echo '<br>$body='.$body;
            //$mail_user->insert_header = $mail_user->insert_footer = false;
            $subject = $this->multi['_TXT_YOUR_ORDER2']." ".$_SERVER['SERVER_NAME'];

            //var_dump($this->alias , $o_comm['email']);
            
            if ( empty($this->alias) && !empty($o_comm['email']) )
                $this->alias = $o_comm['email'];

            if ( empty($this->alias) && !isset($this->no_need_mail) ) $this->Err .= ' не указан емейл покупателя.<br/>';

            if( isset($this->alias) && !empty($this->alias) ) {
                $mail_user->AddAddress($this->alias);

                $mail_user->WordWrap = 500;
                $mail_user->IsHTML( true );
                $mail_user->Subject = $subject;
                $mail_user->Body = $body;
                $res_user = $mail_user->SendMail();
                //echo '<br>$res_user='.$res_useres;
                if( !$res_user ) {
                    $this->Err = $this->Err.' '.$this->multi['_ERR1']." <a href='mailto:".$this->alias."'>".$this->alias."</a> ".$this->multi['_ERR2']."<br>";
                }
            }

            $SysSet = new SysSettings();
            $sett = $SysSet->GetGlobalSettings();
            //$mail_admin->insert_header = $mail_admin->insert_footer = false;
            $mail_admin->WordWrap = 500;
            $mail_admin->IsHTML( true );
            $mail_admin->Subject = $subject;
            $mail_admin->Body = 'копия админу: <br/>'.$body;

            if ( !empty($this->Err) ) $mail_admin->Body = 'Ошибки:<br/>'.$this->Err.$mail_admin->Body;

            if( !empty($sett['mail_auto_emails'])){
                $hosts = explode(";", $sett['mail_auto_emails']);
                for($i=0;$i<count($hosts);$i++){
                    //$arr_emails[$i]=$hosts[$i];
                    $mail_admin->AddAddress($hosts[$i]);
                }//end for
            }
            $res_admin = $mail_admin->SendMail();
            if( isset($this->alias) && !empty($this->alias) ) {
            return $res_user;
            }
        } //end of function SendOrderToEmail()


        // ================================================================================================
        // Function : ShowOrderResult()
        // Date : 21.04.2006
        // Returns :      true,false / Void
        // Description :  Show Order Result
        // Programmer :  Ihor Trokhymchuk
        // ================================================================================================
        function ShowOrderResult($id_order)
        {
            ?>

            <div class="h1main"><div class="line2"></div><span>Вы успешно оформили заказ</span></div>
            <div class="subBody">
            <?/*<h1 ><?=$this->multi['_TXT_ORDERING']?> <?=$_SERVER['SERVER_NAME']?></h1>*/?>
            <?

            if ( !empty($this->Err)) $this->showErr();
            else {
                $body3 = "
                  <div class='orderTxt'>
                      Вы успешно сделали заказ на сайте ohrana.ua<br>
                      В ближайшее время с Вами свяжется менеджер для подтверждения заказа.
                  </div>
                  ";
                echo $body3;
                ?>
                <div><?=$this->multi['FLD_ORDER_ID'].' '.$id_order;?></div>
                <?
            }
            $link = _LINK.'order/print/'.$id_order.'/';
            $width = '750px';
            $height = '800px';
            $params = "OnClick='window.open(\"".$link."\", \"\", \"width=".$width.", height=".$height.", status=0, toolbar=0, location=0, menubar=0, resizable=0, scrollbars=1\"); return false;'";
            ?>
            <br/>
            <a class="print-order-link" href="javascript:void(0);" target="_blank" <?=$params;?>><?=$this->multi['TXT_PRINT_ORDER']?></a>
            </div>
            <?

        } //end of function ShowOrderResult()


        // ================================================================================================
        // Function : UserOrderHistory()
        // Date : 21.04.2006
        // Returns :      true,false / Void
        // Description :  show links category
        // Programmer :  Dmitriy Kerest
        // ================================================================================================
        function UserOrderHistory($user_id, $id_order)
        {
            $catalog = check_init('CatalogLayout', 'CatalogLayout');
            if(!$id_order)
            {
                ?>
                <div class="subBody" style="position: relative; z-index: 3;">
                <?
                if( empty($user_id) AND isset($_COOKIE['kor_order_id']))
                {
                    $keys = array_keys($_COOKIE['kor_order_id']);
                    $cnt = count($keys);
                    $str='';
                    for($i=0;$i<$cnt;$i++){
                        if(empty($str)) $str = "'". $_COOKIE['kor_order_id'][$keys[$i]] ."'";
                        else $str .= ",'". $_COOKIE['kor_order_id'][$keys[$i]] ."'";
                    }
                    $q = "SELECT `id`, `id_order`, `date` FROM `".TblModOrderComments."` WHERE `id_order` IN(".$str.") ORDER BY id_order desc";
                }
                elseif( $user_id>0 ){
                    $q = "SELECT `id`, `id_order`, `date` FROM `".TblModOrderComments."` WHERE `buyer_id`='".$user_id."' ORDER BY id_order desc";
                }
                if(isset($q)){
                    $res = $this->db->db_Query($q);
                    $rows = $this->db->db_GetNumRows($res);
                    //echo '<br>$q='.$q.' $res='.$res.' $rows='.$rows;
                    if($rows==0){
                        ?><div style="padding-top:15px;"><?
                        echo $this->multi['_TXT_EMPTY_O_HIS'];
                        ?></div><?
                    }
                    else {
                        ?><div><?
                        for($i=$rows-1;$i>=0;$i--)
                        {
                           $row = $this->db->db_FetchAssoc($res);
                           $j = $i+1;
                           //echo '<br/><strong><a href="#" class="toCartUrl"  onclick="makeRequest(\''._LINK.'order.php/\', \'task=history&amp;user_id='.$this->Logon->user_id.'&id_order='.$row['id_order'].'\', \'my_d_basket\'); return false;">
                           echo '<br/><strong><a href="#" class="toCartUrl main-tr" data-show="'.$row['id'].'"  onclick="makeHistoryRequest(\''._LINK.'order/history/\', \'?user_id='.$this->Logon->user_id.'&width=630&height=800&id_order='.$row['id_order'].'\', \'hist'.$row['id'].'\'); return false;">
                          '.$this->multi['FLD_ORDER_ID'].$row['id_order'].' '.$this->multi['FLD_FROM'].' '.$row['date'].'</a></strong>';
                          ?><div class="hist" id="hist<?=$row['id'];?>"></div><?
                        }
                        ?></div><?
                    }
                }
                ?>
                </div>
                <?
            }   // end if for orders layuot

            else
            {
                $q = "SELECT * FROM `".TblModOrderComments."` WHERE `buyer_id`='".$user_id."' AND `id_order`='".$id_order."'";
                $res = $this->db->db_Query($q);
                $rows = $this->db->db_GetNumRows($res);
                $row = $this->db->db_FetchAssoc($res);
                $statusesData = $this->GetSysSprTableData(TblModOrderStatuses, true);
                foreach( $statusesData as $key => $statusData ){
                    if ( $key == $row['status'] ){
                        $status = stripslashes($statusData['short']);
                    }
                }

    /*
                $order_prod = $this->GetProdOrdersByIdOrder($id_order);
                $cnt = count($order_prod);
                for($i=0;$i<$cnt;$i++){
                    $q_prod = "SELECT `".TblModCatalogProp."`.`number_name`, `".TblModCatalogPropSprName."`.`name`
                                FROM `".TblModCatalogProp."`, `".TblModCatalogPropSprName."`
                                WHERE `".TblModCatalogProp."`.id = '".$order_prod[$i]['prod_id']."'
                                AND `".TblModCatalogProp."`.id = `".TblModCatalogPropSprName."`.`cod`
                                AND `".TblModCatalogPropSprName."`.lang_id = '"._LANG_ID."'
                                ";

                    $res_prod = $this->db->db_Query($q_prod);
                    //echo '<br>$q_prod='.$q_prod.' $res_prod='.$res_prod;
                    $row_prod = $this->db->db_FetchAssoc();
                    $name = stripslashes($row_prod['name']);
                    echo '<table cellpadding="0" cellspacing="2" border="0">
                              <tr>
                                 <td width="170" style="border-bottom:1px solid #f0f0f0;">'.$this->multi['_TXT_PRODUCT'].':</td>
                                 <td style="border-bottom:1px solid #f0f0f0;"> ('.$row_prod['number_name'].') '.$name.' / '.$order_prod[$i]['quantity'].' шт. / '.$this->currency->ShowPrice($order_prod[$i]['sum']).' </td>
                              </tr>';

                }
    */
                //способ доставки заказа
                echo '<table cellpadding="0" cellspacing="2" border="0" style="margin-top: 10px;" class="display-none-mob">';

                $delivery_data = $this->Spr->GetDataByCod(TblModOrderSprDelivery, $row['delivery_method'], $this->lang_id, 1 );
                if ( is_array($delivery_data) ) {
                    if ($delivery_data['short'] == 0) $delivery_price = $this->multi['TXT_FRONT_DELIVERY_COST_FREE'];
                    else $delivery_price = $this->currency->Converting($this->delivery_price_currency, _CURR_ID, stripslashes($delivery_data['short']), 2);
                    echo '<tr><td style="border-bottom:1px solid #f0f0f0;">' . $this->multi['TXT_FRONT_DELIVERY_METHOD'] . ':</td><td style="border-bottom:1px solid #f0f0f0;">' . stripslashes($delivery_data['name']) . " (" . $this->currency->ShowPrice($delivery_price) . ")</td>";
                }
                else echo $delivery_data;

                if($row['comment']!=''){
                    echo '<tr>
                             <td width="170" style="border-bottom:1px solid #f0f0f0;">'.$this->multi['_TXT_YOUR_COMM'].':</td>
                             <td style="border-bottom:1px solid #f0f0f0;">'.stripslashes($row['comment']).'</td>
                          </tr>';
                }

                $width = 700;
                $height = 600;
                $params = "OnClick='window.open(\""._LINK."order/print/".$id_order."/\", \"print_order\", \"width=".$width.", height=".$height.", status=0, toolbar=0, location=0, menubar=0, resizable=1, scrollbars=1\"); return false;'";

                ?>
                    <tr>
                     <td width="170" style="border-bottom:1px solid #f0f0f0;"><?=$this->multi['_TXT_O_DATE'];?>:</td>
                     <td style="border-bottom:1px solid #f0f0f0;"><?=$row['date']?></td>
                  </tr>
                  <tr>
                     <td width="170" style="border-bottom:1px solid #f0f0f0;"><?=$this->multi['_TXT_O_STATUS']?>:</td>
                     <td style="border-bottom:1px solid #f0f0f0;"><?=$status?></td>
                  </tr>
                  <?if ( !empty($row['ttn']) ): ?>
                  <tr>
                     <td width="170" style="border-bottom:1px solid #f0f0f0;">ТТН:</td>
                     <td style="border-bottom:1px solid #f0f0f0;"><a href="http://novaposhta.ua/tracking/?cargo_number=<?=$row['ttn']?>" target="_blank"><?=$row['ttn']?></a></td>
                  </tr>
                  <? endif; ?>
                  <tr>
                     <td width="170" style="border-bottom:1px solid #f0f0f0;">
                     <?/*<a href="<?=_LINK?>order/print/<?=$id_order?>/" target="_blank">Печать заказа</a>*/?>
                     <a  href="javascript:void(0)" <?=$params;?>><?=$this->multi['TXT_PRINT_ORDER'];?></a>
                     </td>
                     <td style="border-bottom:1px solid #f0f0f0;"></td>
                  </tr>
                  </table><br>
                <? //способ доставки заказа моб ?>
                <div class="table_delivery display-none-desktop">
                <?php
                    if ( is_array($delivery_data) ) {
                        if ($delivery_data['short'] == 0)
                            $delivery_price = $this->multi['TXT_FRONT_DELIVERY_COST_FREE'];
                        else
                            $delivery_price = $this->currency->Converting($this->delivery_price_currency, _CURR_ID, stripslashes($delivery_data['short']), 2); ?>
                        <div class="delivery_title"><?=$this->multi['TXT_FRONT_DELIVERY_METHOD'];?>:</div>
                        <div class="delivery_content"><?=stripslashes($delivery_data['name']);?> (<?=$this->currency->ShowPrice($delivery_price);?>)</div>
                <?php }
                else echo $delivery_data;

                if($row['comment']!=''){
                    echo '<div class="delivery_title">'.$this->multi['_TXT_YOUR_COMM'].':</div>
                          <div class="delivery_content">'.stripslashes($row['comment']).'</div>';
                }

                $width = 700;
                $height = 600;
                $params = "OnClick='window.open(\""._LINK."order/print/".$id_order."/\", \"print_order\", \"width=".$width.", height=".$height.", status=0, toolbar=0, location=0, menubar=0, resizable=1, scrollbars=1\"); return false;'";

                ?>
                     <div class="delivery_title"><?=$this->multi['_TXT_O_DATE'];?>:</div>
                     <div class="delivery_content"><?=$row['date']?></div>
                     <div class="delivery_title"><?=$this->multi['_TXT_O_STATUS']?>:</div>
                     <div class="delivery_content border-bottom-none"><?=$status?></div>
                      <?if ( !empty($row['ttn']) ): ?>
                         <div class="delivery_title">ТТН:</div>
                         <div class="delivery_content">
                            <a href="http://novaposhta.ua/tracking/?cargo_number=<?=$row['ttn']?>" target="_blank"><?=$row['ttn']?></a>
                        </div>
                      <? endif; ?>
                      <div class="delivery_print">
                         <?/*<a href="<?=_LINK?>order/print/<?=$id_order?>/" target="_blank">Печать заказа</a>*/?>
                         <a  href="javascript:void(0)" <?=$params;?>><?=$this->multi['TXT_PRINT_ORDER'];?></a>
                      </div>
                  </div>
                <?

                $this->FullCart( false, $id_order );

                /*
                <a href="#" class="toCartUrl"  onclick="makeRequest('<?=_LINK;?>order/', 'task=history&amp;user_id=<?=$this->Logon->user_id?>', 'my_d_basket'); return false;">
                <?=$txtBack;?></a><br />
                */

                /*
                ?>
                <a href="<?=_LINK;?>order/history/" class="toCartUrl" onclick="makeRequest('<?=_LINK;?>order/history/', 'user_id=<?=$this->Logon->user_id;?>&ajax_reload=1', 'my_d_basket'); return false;";><img src="/images/design/backBtn<?=_LANG_ID;?>.gif" border="0" alt="<?=$this->multi['_TXT_BACK'];?>" title="<?=$this->multi['_TXT_BACK'];?>"></a>
                <?
                */
            }

        }//end of function UserOrderHistory()


        // ================================================================================================
        // Function : ShowErr()
        // Version : 1.0.0
        // Date : 10.01.2006
        //
        // Parms :
        // Returns :      true,false / Void
        // Description :  Show errors
        // ================================================================================================
        // Programmer :  Igor Trokhymchuk
        // Date : 10.01.2006
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function showErr()
        {
            $this->Form->showErr($this->Err);
        } //end of fuinction ShowErr()


        // ================================================================================================
        // Function : ShowTextMessages()
        // Version : 1.0.0
        // Date : 06.06.2007
        //
        // Parms :
        // Returns :      true,false / Void
        // Description :  Show text messages
        // ================================================================================================
        // Programmer :  Igor Trokhymchuk
        // Date : 06.06.2007
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function ShowTextMessages($txt)
        {
            if( !empty($txt) ) $this->TextMessages = $txt;
            if ($this->TextMessages){
                ?>
                <table border="0" cellspacing="0" cellpadding="0" class="messages" width="98%" align"=center">
                 <tr><td><h3><?=$this->TextMessages;?></h3></td></tr>
                </table>
                <?
            }
        } //end of fuinction ShowTextMessages()


        // ================================================================================================
        // Function : PrintOrderBlank()
        // Date : 27.01.2011
        // Returns :      true,false / Void
        // Description :  Show Order for Print
        // Programmer :  Yaroslav Gyryn
        // ================================================================================================
         function PrintOrderBlank($o_comm) {
             $multi_lang_catalog = array( "'FLD_NUMBER_NAME'");
             $multiCatalog = $this->Spr->GetArrNameByArrayCod(TblModCatalogSprTxt, $multi_lang_catalog);
             $order_arr = $this->GetProdOrdersByIdOrder($this->id_order);

             ?>
             <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
             <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
             <head>
                 <META http-equiv="Content-Type" content="text/html; charset=utf-8">
                 <META http-equiv="content-language" content="ru">
                 <META name="description" content="">
                 <META name="keywords" content="">
                 <title><?=$this->multi['TXT_STATEMENT_ACCOUNT']?></title>
                 <style>
                     p {margin: 0; padding: 0;}
                     td, div {font-size: 12px;}
                 </style>
             </head>
             <body style="background:white;">
             <div style="float:left;"><img src="/images/logo.gif"/></div>
             <div style="height:109px; padding: 10px 20px 20px 160px; font-size:18px;">
                 <?=$this->multi['FLD_ORDER_ID']?><?=$this->id_order;?> <?=$this->multi['TXT_IN_INTERNET_SHOP']?> <?=$_SERVER['SERVER_NAME'];?>
                 <div style="font-size: 11px; margin: 5px 0px 0px 0px;"><?=$this->multi['_TXT_O_DATE']?>: <?=$o_comm['date'];?></div>
             </div>

             <div style="width:600px;">
                 <table border="1" cellspacing="0" cellpadding="5" width="100%" align="left" >
                     <tr>
                         <td class="td_border" colspan="2"><b><?=$this->multiUser['TXT_BUYER']?></b></td>
                     </tr>
                     <tr>
                         <td class="td_border"><?=$this->multiUser['FLD_NAME'];?></td>
                         <td class="td_border" width="80%"><?=stripslashes($o_comm['name']);?></td>
                     </tr>
                     <tr>
                         <td class="td_border"><?=$this->multiUser['FLD_PHONE_MOB'];?></td>
                         <td class="td_border"><?=stripslashes($o_comm['phone_mob']);?></td>
                     </tr>
                     <?if(!empty($o_comm['phone'])){?>
                         <tr>
                             <td class="td_border"><?=$this->multiUser['FLD_PHONE'];?></td>
                             <td class="td_border"><?=stripslashes($o_comm['phone']);?></td>
                         </tr>
                     <?}?>
                     <tr>
                         <td class="td_border"><?=$this->multiUser['FLD_EMAIL'];?></td>
                         <td class="td_border"><?=stripslashes($o_comm['email']);?></td>
                     </tr>
                     <tr>
                         <td class="td_border"><?=$this->multiUser['FLD_CITY'];?></td>
                         <td class="td_border"><?=stripslashes($o_comm['city']);?></td>
                     </tr>
                     <tr>
                         <td class="td_border"><?=$this->multiUser['FLD_ADR'];?></td>
                         <td class="td_border"><?=stripslashes($o_comm['addr']);?></td>
                     </tr>
                     <? if($o_comm['comment']!=''){?>
                         <tr>
                             <td class="td_border"><em><?=$this->multiUser['FLD_COMMENT'];?></em></td>
                             <td class="td_border"><em><?=str_replace("\n", "<br/>", stripslashes($o_comm['comment']));?></em></td>
                         </tr>
                     <?
                     } ?>
                 </table>
                 <br/><br/>
                 <table border="1" cellspacing="0" cellpadding="5" width="100%" align="left">
                     <tr style="font-weight:bold;">
                         <td align="left"> <?=$multiCatalog['FLD_NUMBER_NAME'];?></td>
                         <td align="left"> <?=$this->multi['_TXT_PRODUCT'];?></td>
                         <td align="center" width="75" class="td_border"><?=$this->multi['TXT_PRICE'];?></td>
                         <td align="center" width="75" class="td_border"><?=$this->multi['FLD_QUANTITY'];?></td>
                         <td align="center" width="150" class="td_border"><?=$this->multi['FLD_SUMA'];?></td>
                     </tr>
                     <?
                     $cnt = sizeof($order_arr);
                     for($i=0; $i<$cnt; $i++){
                         $order_data = $order_arr[$i];
                         //на бланке для печати все стоимости и суммы отображаються в валюте, выбранной по-умолчанию для сайта,
                         //независимо от того, в какой валюте пользователь делал заказ
                         $price= $this->currency->Converting($order_data['currency'], _CURR_ID, $order_data['price'], 2);
                         $sum = $this->currency->Converting($order_data['currency'], _CURR_ID, $order_data['sum'], 2);

                         $q_prod = "SELECT `".TblModCatalogProp."`.`number_name`, `".TblModCatalogPropSprName."`.`name`
                                FROM `".TblModCatalogProp."`, `".TblModCatalogPropSprName."`
                                WHERE `".TblModCatalogProp."`.id = '".$order_data['prod_id']."'
                                AND `".TblModCatalogProp."`.id = `".TblModCatalogPropSprName."`.`cod`
                                AND `".TblModCatalogPropSprName."`.lang_id = '"._LANG_ID."'
                                ";

                         $res_prod = $this->db->db_Query($q_prod);
                         $row_prod = $this->db->db_FetchAssoc();
                         $name = stripslashes($row_prod['name']);
                         ?>
                         <tr align="center">
                             <td align="left"><?=$row_prod['number_name'];?></td>
                             <td align="left"><?=$name;?></td>
                             <td><?=$this->currency->ShowPrice($price);?></td>
                             <td><?=$order_data['quantity'];?></td>
                             <td><?=$this->currency->ShowPrice($sum);?></td>
                         </tr>
                     <?
                     } // end for prod
                     ?>
                     <tr>
                         <td><?=$this->multi['TXT_FRONT_DELIVERY_METHOD'];?></td>
                         <td colspan="3">
                             <?
                             $delivery_data = $this->Spr->GetDataByCod(TblModOrderSprDelivery, $o_comm['delivery_method'], $this->lang_id, 1 );
                             $delivery_price = $this->currency->Converting($this->delivery_price_currency, _CURR_ID, stripslashes($delivery_data['short']), 2);
                             echo stripslashes($delivery_data['name']);
                             ?>
                         </td>
                         <td align="center"><?=$this->currency->ShowPrice($delivery_price);?></td>
                     </tr>
                     <?$o_price = $o_comm['sum'] + $delivery_price;?>
                     <tr>
                         <td colspan="4" style="height:45px; margin-top:15px; padding-right:28px;" align="right">
                             <strong><?=$this->multi['TXT_TOTAL_COST']?>:</strong>
                         </td>
                         <td align="center"><strong><?=$this->currency->ShowPrice($o_price);?></strong></td>
                     </tr>
                 </table>
             </div>
             <br/><br/><br/><br/>
             <div align="center">
                 <div style="width:200px;" align="center" onclick="this.style.visibility='hidden';">
                     <?/*<input type="button" value="Закрыть" onclick="window.close();"/>&nbsp;&nbsp;*/?>
                     <input type="submit" name="submit" value="<?=$this->multi['TXT_PRINT_ORDER']?>" onclick="this.style.visibility='hidden'; window.print();" />
                 </div>
             </div>
             </body>
             </html>
         <?
         }

        // ================================================================================================
        // Function : PrintInvoice()
        // Date : 27.01.2011
        // Returns :      true,false / Void
        // Description :  Show Order for Print
        // Programmer :  Yaroslav Gyryn
        // ================================================================================================
         function PrintInvoice($o_comm) {
             $order_arr = $this->GetProdOrdersByIdOrder($this->id_order);
             if(empty($this->FrontendPages))
                 $this->FrontendPages = new FrontendPages();

             ?>
             <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
             <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
             <head>
                 <META http-equiv="Content-Type" content="text/html; charset=utf-8">
                 <META http-equiv="content-language" content="ru">
                 <META name="description" content="">
                 <META name="keywords" content="">
                 <link href="/include/css/screen.css" rel="stylesheet" type="text/css" media="screen" />
                 <title><?//=$this->multi['TXT_STATEMENT_ACCOUNT']?></title>
             </head>
             <body style="background:white;">
             <style>
                 p {margin: 0; padding: 0;}
                 td, div {font-size: 12px;}
                 .tblFacturaHead tr td {vertical-align: top;}
                 .tblFacturaHead tr td.first { width: 33px;}
                 .tblFacturaHead tr td.second { width: 125px; text-decoration: underline;}
                 .tblFacturaHead tr td.third {}
                 .all-sum:first-letter {text-transform:uppercase;}
             </style>
             <div style="width:600px;">
             <table cellpadding="2" cellspacing="2" class="tblFacturaHead" width="100%">
                 <tr>
                     <td class="first"></td>
                     <td class="second"> Постачальник</td>
                     <td class="third">
                         <?$header = $this->FrontendPages->GetPageData(10);
                         echo stripslashes($header ['content']);
                         //echo strip_tags(stripslashes($header ['content']),'<div><br/><br/><br /><span>');?>
                     </td>
                 </tr>
                 <tr>
                     <td></td>
                     <td class="second">Одержувач</td>
                     <td><?=stripslashes($o_comm['name']);?></td>
                 </tr>
                 <tr>
                     <td></td>
                     <td class="second"> Платник</td>
                     <td> той самий</td>
                 </tr>
             </table>

             <div style=" padding: 10px 20px 20px 20px;  font-weight: bold; text-align: center;">
                 <?
                 //echo 'Дата замовлення: '.$o_comm['date'].'<br/>';
                 $tmp = explode("-", $o_comm['date']);
                 $tmp2 = explode(" ", $tmp[2]);

                 $year = $tmp[0];
                 $month = intval($tmp[1]);
                 $day = intval($tmp2[0]);

                 $time = $tmp2[1];
                 $tmp3 = explode(":", $time);   // час

                 $hours = $tmp3[0];
                 $minutes = $tmp3[1];
                 $seconds = $tmp3[2];

                 /*$date_time_array = getdate(time());
                 $hours = $date_time_array['hours'];
                 $minutes = $date_time_array['minutes'];
                 $seconds = $date_time_array['seconds'];
                 $month = $date_time_array['mon'];
                 $day = $date_time_array['mday'];
                 $year = $date_time_array['year'];*/

                 $activeDate = mktime($hours,$minutes,$seconds,$month,$day,$year);
                 ?>
                 Рахунок-фактура № <?=$this->id_order;?> <br/>
                 від <?=strftime('%d.%m.%Y',$activeDate);?>
             </div>

             <div style="overflow: hidden;">
                 <table  cellspacing="0" cellpadding="5" width="100%" border="1" align="left" class="tblSum" >
                     <tr style="font-weight:bold;">
                         <td align="center" width="30">№</td>
                         <td align="center" width="290">Назва</td>
                         <td align="center" width="30" >Од.</td>
                         <td align="center" width="80">Кількість</td>
                         <td align="center" width="80" class="td_border">Ціна без ПДВ</td>
                         <td align="center" width="80" class="td_border">Сума без ПДВ</td>
                     </tr>
                     <?
                     $cnt = sizeof($order_arr);

                     // Податок на додану вартість 20%
                     $this->nds = $this->GetNDS();
                     if(!empty($this->nds))
                         $this->nds = floatval ( ($this->nds+100)/100);
                     else
                         $this->nds = 1;
                     //echo '$nds ='.$this->nds;

                     for($i=0; $i<$cnt; $i++){
                         $order_data = $order_arr[$i];
                         //на бланке для печати все стоимости и суммы отображаються в валюте, выбранной по-умолчанию для сайта,
                         //независимо от того, в какой валюте пользователь делал заказ
                         $price= $this->currency->Converting($order_data['currency'], _CURR_ID, $order_data['price'], 2);
                         $sum = $this->currency->Converting($order_data['currency'], _CURR_ID, $order_data['sum'], 2);

                         $q_prod = "SELECT `".TblModCatalogProp."`.`number_name`, `".TblModCatalogPropSprName."`.`name`
                                    FROM `".TblModCatalogProp."`, `".TblModCatalogPropSprName."`
                                    WHERE `".TblModCatalogProp."`.id = '".$order_data['prod_id']."'
                                    AND `".TblModCatalogProp."`.id = `".TblModCatalogPropSprName."`.`cod`
                                    AND `".TblModCatalogPropSprName."`.lang_id = '"._LANG_ID."'
                                    ";

                         $res_prod = $this->db->db_Query($q_prod);
                         $row_prod = $this->db->db_FetchAssoc();
                         $name = stripslashes($row_prod['name']);
                         ?>
                         <tr align="center">
                             <td align="center"><?=$i+1;?></td>
                             <td align="left"><?=$name;?></td>
                             <td align="center">шт.</td>
                             <td align="right"><?=sprintf("%.3f",$order_data['quantity']);?></td>
                             <td align="right"><?=$this->currency->ShowPrice($price/$this->nds, false);?></td>
                             <td align="right"><?=$this->currency->ShowPrice($sum/$this->nds, false);?></td>
                         </tr>
                     <?
                     } // end for prod
                     /*<tr>
                        <td align="left"><?=$i+1;?></td>
                      <td><i><?=$this->multi['TXT_FRONT_DELIVERY_METHOD'];?>:<br/></i>
                      <?
                       $delivery_data = $this->Spr->GetDataByCod(TblModOrderSprDelivery, $o_comm['delivery_method'], $this->lang_id, 1 );
                       $delivery_price = $this->currency->Converting($this->delivery_price_currency, _CURR_ID, stripslashes($delivery_data['short']), 2);
                       echo stripslashes($delivery_data['name']);
                       $o_price = $o_comm['sum'] + $delivery_price;
                       ?>
                      </td>
                      <td colspan="3"></td>
                      <td align="center"><?=$this->currency->ShowPrice($delivery_price);?></td>
                     </tr>*/?>
                 </table>
                 <table  cellspacing="0" cellpadding="5" width="100%" border="0" align="left">
                     <tr>
                         <td  colspan="5" style="padding-right:8px; border-left: 0px; border-bottom: 0px;" align="right">
                             <strong>Разом без ПДВ:</strong>
                         </td>
                         <td class="pdvCell" width="80" align="right"><strong>
                                 <?=$this->currency->ShowPrice($o_comm['sum']/$this->nds, false);?>
                             </strong></td>
                     </tr>
                     <tr>

                     <tr>
                         <td colspan="5" style=" padding-right:8px; border-left: 0px; border-bottom: 0px; border-top: 0px;" align="right">
                             <strong>ПДВ:</strong>
                         </td>
                         <td class="pdvCell" align="right">
                             <strong><?=$sumPDV = $this->currency->ShowPrice($o_comm['sum'] - ($o_comm['sum']/$this->nds),false); // Сумма ПДВ
                                 ?></strong></td>
                     </tr>
                     <tr>

                         <td colspan="5"  style="padding-right:8px;  border-left: 0px; border-bottom: 0px; border-top: 0px;" align="right">
                             <strong>Всього з ПДВ:</strong>
                         </td>
                         <td class="pdvCell" align="right"><strong><?
                                 echo $sum = $this->currency->ShowPrice($o_comm['sum'], false);
                                 //echo sprintf("%.2f", $sum);
                                 ?></strong></td>
                     </tr>
                 </table>
             </div>
             <br/><br/>
             <div class="itogo">
                 Всього на суму:
                 <div class="all-sum"><b><?=num2str($sum);?></b></div>
                 ПДВ: <?=$this->currency->ShowPrice($sumPDV,true);?>
                 <br/><br/>
                 <div align="right">Виписав(ла): __________________________________</div><br/>
                 <br/>
             </div>
             <div class="dateActual" align="right"><b>Рахунок дійсний до сплати до <?=strftime('%d.%m.%Y',$activeDate);?></b></div><br/>
             <?/*
                         <table border="1" cellspacing="0" cellpadding="5" width="100%" align="left" >
                           <tr>
                            <td class="td_border" colspan="2"><b><?=$this->multiUser['TXT_BUYER']?></b></td>
                           </tr>
                           <tr>
                            <td class="td_border"><?=$this->multiUser['FLD_NAME'];?></td>
                            <td class="td_border" width="80%"><img src="/images/design/spacer.gif"><?=stripslashes($o_comm['name']);?></td>
                           </tr>
                           <tr>
                            <td class="td_border"><?=$this->multiUser['FLD_PHONE_MOB'];?></td>
                            <td class="td_border"><img src="/images/design/spacer.gif"><?=stripslashes($o_comm['phone_mob']);?></td>
                           </tr>
                           <?if(!empty($o_comm['phone'])){?>
                               <tr>
                                <td class="td_border"><?=$this->multiUser['FLD_PHONE'];?></td>
                                <td class="td_border"><img src="/images/design/spacer.gif"><?=stripslashes($o_comm['phone']);?></td>
                               </tr>
                           <?}?>
                           <tr>
                            <td class="td_border"><?=$this->multiUser['FLD_EMAIL'];?></td>
                            <td class="td_border"><img src="/images/design/spacer.gif"><?=stripslashes($o_comm['email']);?></td>
                           </tr>
                           <tr>
                            <td class="td_border"><?=$this->multiUser['FLD_CITY'];?></td>
                            <td class="td_border"><img src="/images/design/spacer.gif"><?=stripslashes($o_comm['city']);?></td>
                           </tr>
                           <tr>
                            <td class="td_border"><?=$this->multiUser['FLD_ADR'];?></td>
                            <td class="td_border"><img src="/images/design/spacer.gif"><?=stripslashes($o_comm['addr']);?></td>
                           </tr>
                          <? if($o_comm['comment']!=''){?>
                               <tr>
                                <td class="td_border"><em><?=$this->multiUser['FLD_COMMENT'];?></em></td>
                                <td class="td_border"><em><?=str_replace("\n", "<br/>", stripslashes($o_comm['comment']));?></em></td>
                               </tr>
                           <?
                           } ?>
                         </table>*/?>
             <br/><br/><br/><br/>
             <div align="center">
                 <div style="width:200px;" align="center" onclick="this.style.visibility='hidden';">
                     <?/*<input type="button" value="Закрыть" onclick="window.close();"/>&nbsp;&nbsp;*/?>
                     <input type="submit" name="submit" value="<?=$this->multi['TXT_PRINT_ORDER']?>" onclick="this.style.visibility='hidden'; window.print();" />
                 </div>
             </div>
             </div>
             </body>
             </html>
         <?
         }
        // ================================================================================================
        // Function : PrintOrder()
        // Date : 27.01.2011
        // Returns :      true,false / Void
        // Description :  Show Order for Print
        // Programmer :  Yaroslav Gyryn
        // ================================================================================================
        function PrintOrder()
        {
            $o_comm = $this->GetOrderCommentInArr($this->id_order);

            if (!isset($logon)) $logon = new  UserAuthorize();
            if ( $logon->user_type == 1 || isset($_COOKIE['kor_order_id'][$this->id_order]) ) $this->skipUserCheck=1;

            if (!isset($this->skipUserCheck)){
                //check user access rights
                if( ( (intval($logon->user_id)!=intval($o_comm['buyer_id'])) OR empty($o_comm['buyer_id'])) ){
                     ?>
                     <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
                     <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
                         <head>
                            <title><?=$this->multi['TXT_STATEMENT_ACCOUNT']?> <?=$this->multi['TXT_ACCESS_DENIED']?>!</title>
                         </head>
                         <body>
                            <h1><?=$this->multi['TXT_ACCESS_DENIED']?></h1>
                         </body>
                     </html>
                     <?
                     return true;
                }
            }
            $this->PrintWaybill();
            /*old code
             //если оплата через банковский платеж, то отображаем счет-фактуру для оплаты заказа
             //$this->PrintInvoice($o_comm);
             if($o_comm['pay_method']==2)
                 $this->PrintInvoice($o_comm);
             else
                $this->PrintOrderBlank($o_comm);*/

        }
    //---- end of PrintOrder -----------------------------------------------------------------------------------------------


        function PrintWaybill( $waybillType = 'client' ){
            if ( !isset($this->id_order) || empty($this->id_order) ) echo 'пустой заказ';
            $o_comm = $this->GetOrderCommentInArr($this->id_order);

            //var_dump($o_comm);
            $user_name = stripslashes($o_comm['name']);
            $phone_mob = stripslashes($o_comm['phone_mob']);
            $address = stripslashes($o_comm['addr']);

            $user_pass = '';
            $q = "SELECT `pass` FROM `".TblSysUser."` WHERE `id`='".$o_comm['buyer_id']."'";
            $res = $this->db->db_Query($q);
            if ($res){
                $row = $this->db->db_FetchAssoc();
                if ( isset($row['pass']) )
                    $user_pass = $row['pass'];
            }
            //echo $q;

            $order_arr = $this->GetProdOrdersByIdOrder($this->id_order);
            $cnt = sizeof($order_arr);

            $split_page = false;
            if ( $cnt > 10 ) $split_page = true;
            else $pt_height = '15.5';

            for($i=0; $i<2; $i++):
                ob_start();
            ?>
                <div class="prods-table">
                    <table border="0" cellspacing="0" cellpadding="0" width="100%">
                        <tr>
                            <td style="width: 30%">
                               <?php if($i==1): ?><img style="width: 100%" src="/images/design/ohrana_ua.png"/><? endif; ?>
                            </td>
            <?php
                if ($i==0) $prods_table_top_header = ob_get_clean();
                else $prods_table_bottom_header = ob_get_clean();
            endfor;
            ?>

            <?php ob_start(); ?>
                            <td style="width: 40%; font-size: 0.4cm; text-align: center">
                            <b>Накладная № <?=$this->id_order;?></b>
                                <div style="font-size: 0.3cm; margin-top: 0.05cm;">от <?=$o_comm['date'];?></div>
                          </td>
                          <td style="width:30%">
                               Заказчик: <?=$user_name?><br/>
                               Тел.: <?=$phone_mob?><br/>
                               <?=$address?>
                               <br/>
                          </td>
                      </tr>
                    </table>

                    <div style="height: 10px"></div>

                    <table border="1" cellspacing="0" cellpadding="0" width="100%">
                        <tr style="font-weight:bold;">
                            <td align="center"> № </td>
                            <td align="center" width="50%"> <?=$this->multi['_TXT_PRODUCT'];?></td>
                            <td align="center"> Ед. </td>
                            <td align="center"><?=$this->multi['FLD_QUANTITY'];?></td>
                            <td align="center"><?=$this->multi['TXT_PRICE'];?></td>
                            <td align="center"><?=$this->multi['FLD_SUMA'];?></td>
                        </tr>
                    <?
                    for($i=0; $i<$cnt; $i++){
                        $order_data = $order_arr[$i];
                        //на бланке для печати все стоимости и суммы отображаються в валюте, выбранной по-умолчанию для сайта,
                        //независимо от того, в какой валюте пользователь делал заказ
                        $price= $this->currency->Converting($order_data['currency'], _CURR_ID, $order_data['price'], 2);
                        $sum = $this->currency->Converting($order_data['currency'], _CURR_ID, $order_data['sum'], 2);

                        $q_prod = "SELECT `".TblModCatalogProp."`.`number_name`, `".TblModCatalogPropSprName."`.`name`
                                   FROM `".TblModCatalogProp."`, `".TblModCatalogPropSprName."`
                                   WHERE `".TblModCatalogProp."`.id = '".$order_data['prod_id']."'
                                   AND `".TblModCatalogProp."`.id = `".TblModCatalogPropSprName."`.`cod`
                                   AND `".TblModCatalogPropSprName."`.lang_id = '"._LANG_ID."'
                        ";

                        $res_prod = $this->db->db_Query($q_prod);
                        $row_prod = $this->db->db_FetchAssoc();
                        $name = stripslashes($row_prod['name']);
                        ?>
                        <tr align="center">
                            <td align="left"><?=($i+1)?></td>
                            <td align="left"><?=$name;?></td>
                            <td align="left">шт.</td>
                            <td><?=$order_data['quantity'];?></td>
                            <td><?=$this->currency->ShowPrice($price);?></td>
                            <td><?=$this->currency->ShowPrice($sum);?></td>
                        </tr>
                    <?
                    } // end for prod
                    ?>
                    </table>

            <?php $prods_table_body = ob_get_clean(); ?>

            <?php
            $total_sum = $this->currency->ShowPrice($o_comm['sum']);
            for ($i=0; $i<2; $i++):
                ob_start(); ?>

                    <div class="order-footer">
                        <? if ($i==0): ?>
                            <span class="order-comment"><?=$o_comm['comment']?></span>
                        <? endif?>
                        <span class="total-sum">
                            Итого к оплате: <?=$total_sum?><br/>
                        </span>
                    </div>
                </div>

                 <?php
                if ($i==0) $prods_table_top_footer = ob_get_clean();
                else $prods_table_bottom_footer = ob_get_clean();
            endfor;

            $prods_table_top = $prods_table_top_header.$prods_table_body.$prods_table_top_footer;

            if ( $waybillType == 'client' ){
                echo View::factory('/modules/mod_order/templates/tpl_waybill.php')
                    ->bind('waybillType', $waybillType)
                    ->bind('multi', $this->multi)
                    ->bind('prods_table_top', $prods_table_top)
                    ;
                exit;
            }

            $prods_table_bottom = $prods_table_bottom_header.$prods_table_body.$prods_table_bottom_footer;

            //delivery info
            if ($o_comm['pay_method']=='1'){
                $valuation = 300;
                $reverse_delivery = 'Ничего';
            }
            else{
                $valuation = $total_sum;
                $reverse_delivery = $total_sum;
            }

            if ($o_comm['free_delivery']==1) $delivery_payer = "отправитель";
            else $delivery_payer = "получатель";

            if ($o_comm['pay_method']=='1'){
                $reverse_delivery_payer = '';
            }
            else{
                if ($o_comm['no_commission']==1)
                    $reverse_delivery_payer = 'Обратную доставку оплачивает отправитель';
                else
                    $reverse_delivery_payer = 'Обратную доставку оплачивает получатель';
            }

            //delivery table headers
            for ($i=0; $i<2; $i++):
                if ( $i==0 )
                    $deliv_tbl_top_header = '
            <table class="delivery-table delivery-table-top" border="1" cellspacing="0" cellpadding="0">
                <tr><td colspan="2" align="center">Информация об отправке и оплате</td></tr>';
                else
                    $deliv_tbl_bottom_header = '
            <table class="delivery-table delivery-table-bottom" border="1" cellspacing="0" cellpadding="0">
                <tr><td>Отправитель:</td><td>Магазин «Ohrana.ua»  через Пташкину И.В. тел. 050-2021333</td></tr>';
            endfor;

            //delivery table "body"
            ob_start();
            ?>
                <tr><td>Получатель:</td><td><?=$user_name?></td></tr>
                <tr><td>Телефон:</td><td><?=$phone_mob?></td></tr>
                <tr><td>Адресс:</td><td><?=$address?></td></tr>
                <tr><td>Описание:</td><td>«Электрическое оборудование»</td></tr>
                <tr><td>Оценочная&nbsp;стоимость:</td><td><?=$valuation?></td></tr>
                <tr><td>Отправку&nbsp;оплачивает:</td><td><?=$delivery_payer?></td></tr>
                <tr>
                    <td>Обратная&nbsp;доставка:</td><td><?=$reverse_delivery?>
                    <? if (!empty($reverse_delivery_payer)):?>
                    &nbsp;&nbsp;&nbsp;<?=$reverse_delivery_payer?>
                    <? endif; ?>
                    </td>
                </tr>
            </table>
            <?
            $deliv_tbl_body = ob_get_clean();
            $delivery_table_top = $deliv_tbl_top_header.$deliv_tbl_body;
            $delivery_table_bottom = $deliv_tbl_bottom_header.$deliv_tbl_body;

            //out waybill:

            echo View::factory('/modules/mod_order/templates/tpl_waybill.php')
                    ->bind('waybillType', $waybillType)
                    ->bind('multi', $this->multi)
                    ->bind('prods_table_top', $prods_table_top)
                    ->bind('delivery_table_top', $delivery_table_top)
                    ->bind('split_page', $split_page)
                    ->bind('phone_mob', $phone_mob)
                    ->bind('user_pass', $user_pass)
                    ->bind('delivery_table_bottom', $delivery_table_bottom)
                    ->bind('prods_table_bottom', $prods_table_bottom);
        }
//---- END of PrintWaybill() -------------------------------------------------------------------------------------------

        function showAuthFormToPrintClientOrderBlank(){
            ?>
            <div class="cool-bg">
                <form method="post">
                    <input type="hidden" name="task" value="print_clients_order_page">
                    <input type="text" name="service" value="service_page_ohrana.ua" style="display:none;">
                    <label for="magicPassword">Введите пароль:</label>
                    <input id="magicPassword" type="password" name="magic_password">
                    <input type="submit" value="Вход">
                </form>
            </div>
            <?
        }
//---- end showAuthFormToPrintClientOrderBlank() ----------------------------------------------------------------------------------------------


    function showPrintClientOrderPage(){
        ?>
        <div class="cool-bg">
            <input id="idOrder" type="text" name="id_order" placeholder="ID заказа" autofocus>
            <input id="printClientOrder" type="button" value="<?=$this->multi['TXT_PRINT_ORDER']?>">
            <br/>
        </div>
        <br>
        <script>
                var idOrderInput = $('#idOrder');
                $('#printClientOrder').on('click', function(){
                    openPrintWindow();
                });
                idOrderInput.keypress(function(e) {
                    if(e.which == 13) {
                        openPrintWindow();
                    }
                });

                function openPrintWindow(){
                    var id_order = idOrderInput.val();
                    var link = 'order/print-client-waybill/'+id_order+'/';
                    window.open( link, 'Print ' + id_order, 'left="25%" width="", height="90%", status=0, toolbar=0, menubar=0, resizable=0, scrollbars=1');
                }
        </script>
        <?
    }
//---- end showPrintClientOrderPage() ----------------------------------------------------------------------------------------------


}//end of class OrderLayout
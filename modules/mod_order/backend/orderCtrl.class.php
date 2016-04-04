<?php
// ================================================================================================
// System : CMS
// Module : orderCtrl.class.php
// Date : 06.06.2007
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Purpose : Class definition for all actions with managment of orders
// ================================================================================================

include_once( $_SERVER['DOCUMENT_ROOT'].'/modules/mod_order/order.defines.php' );

// ================================================================================================
//    Class             : OrderCtrl
//    Date              : 06.06.2007
//    Constructor       : Yes
//    Parms             : session_id / session id
//                        usre_id    / UserID
//                        user_      /
//                        user_type  / id of group of user
//    Returns           : None
//    Description       : Class definition for all actions with managment of orders
//    Programmer        :  Igor Trokhymchuk
// ================================================================================================
 class OrderCtrl extends Order {

    public $user_id = NULL;
    public $module = NULL;
    public $Err=NULL;
    public $lang_id = NULL;

    public $sort = NULL;
    public $display = 20;
    public $start = 0;
    public $fln = NULL;
    public $width = 500;
    public $srch = NULL;
    public $fltr = NULL;
    public $fltr2 = NULL;
    public $fltrUserId = NULL;
    public $script = NULL;

    public $db = NULL;
    public $Msg = NULL;
    public $Right = NULL;
    public $Form = NULL;
    public $Spr = NULL;
    public $Currency;

    public $date = NULL;
    public $quantity = NULL;
    public $buyer_is = NULL;
    public $status = NULL;
    public $prod_id = NULL;
    public $from = NULL;
    public $to = NULL;
    public $comment = NULL;
    public $sessid = NULL;
    public $property = NULL;
    public $id_cat = NULL;
    public $sum = NULL;
    public $cost_comment = NULL;
    public $Catalog = NULL;
    public $sysUser = NULL;
    public $user_fltr1 = NULL;
    public $user_fltr2 = NULL;
    public $user_fltr3 = NULL;


   // ================================================================================================
    //    Function          : OrderCtrl (Constructor)
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
    function __construct($user_id = NULL, $module = NULL, $display = NULL, $sort = NULL, $start = NULL, $width = NULL, $fltr = NULL) {
        //Check if Constants are overrulled
        ( $user_id != "" ? $this->user_id = $user_id : $this->user_id = NULL );
        ( $module != "" ? $this->module = $module : $this->module = NULL );
        ( $display != "" ? $this->display = $display : $this->display = 20 );
        ( $sort != "" ? $this->sort = $sort : $this->sort = NULL );
        ( $start != "" ? $this->start = $start : $this->start = 0 );
        ( $width != "" ? $this->width = $width : $this->width = 750 );

        $this->lang_id = _LANG_ID;

        if (empty($this->db))
            $this->db = DBs::getInstance();
        if (empty($this->Msg))
            $this->Msg = check_init('ShowMsg', 'ShowMsg');
        //$this->Msg->SetShowTable(TblModOrderSprTxt);
        if (empty($this->Form))
            $this->Form = check_init('form_mod_order', 'Form', '"form_mod_order"');
        if (empty($this->Right))
            $this->Right = check_init('RightsOrder', 'Rights', '"' . $this->user_id . '", "' . $this->module . '"');
        if (empty($this->Spr))
            $this->Spr = check_init('SysSpr', 'SysSpr', '"' . $this->user_id . '", "' . $this->module . '"');

        if (empty($this->Catalog))
            $this->Catalog = check_init('Catalog', 'Catalog');
        if (empty($this->User))
            $this->User = check_init('User', 'User');
        if (empty($this->sysUser))
            $this->sysUser = check_init('sysUser', 'sysUser');

        $this->AddTbl();
        if (empty($this->multi))
            $this->multi = check_init_txt('TblBackMulti', TblBackMulti);
        if (empty($this->Currency))
            $this->Currency = check_init('SystemCurrencies', 'SystemCurrencies');
        $this->Currency->defCurrencyData = $this->Currency->GetDefaultData();
        $this->Currency->GetShortNamesInArray('back');

        $this->statuses['0'] = $this->multi['FLD_TXT_ALL_ORDERS'];

        /*
        $this->statuses['1'] = $this->multi['FLD_TXT_POLUCHENO'];
        $this->statuses['2'] = $this->multi['FLD_TXT_OFORMLENNO'];  // "Ожидает оплаты"
        $this->statuses['4'] = $this->multi['FLD_TXT_OPLACHENO'];   // "Ожидает отправки"
        $this->statuses['3'] = $this->multi['FLD_TXT_OTPRAVLENO'];
        $this->statuses['5'] = $this->multi['FLD_TXT_OTMENENO'];    // "ожидает подтверждения"
        $this->statuses['6'] = $this->multi['FLD_TXT_DELETED'];
        */

        $statusesData = $this->GetSysSprTableData(TblModOrderStatuses, true);
        //var_dump($statusesData);
        foreach( $statusesData as $cod => $row ){
            $this->statuses[$cod] = $row['name'];
        }
        //var_dump($this->statuses);

    }

// end of constructor OrderCtrl


    // ================================================================================================
    // Function : ShowContentFilters
    // Date : 27.03.2006
    // Returns : true,false / Void
    // Description : Show content of the catalogue
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function ShowContentFilters()
    {
        /* Write Table Part */
        AdminHTML::PanelSimpleH();
        ?>
        <tr valign="top">
            <td>
                <div><h3 style="padding:0px; margin:0px;"><?=$this->multi['TXT_SEARCH_PANEL'];?></h3></div>
                <table class="search-form" border="0" cellpadding="2" cellspacing="1" width="400">
                    <tr class="tr2">
                        <td align="right" width="100"><?=$this->multi['FLD_STATUS'];?>:</td>
                        <td align="left">
                            <div align="left"><?$this->Form->Select($this->statuses, 'fltr', $this->fltr);?></div>
                        </td>

                        <td align="right" nowrap="nowrap">
                            <?=$this->multi['FLD_ORDER_ID'];?>:
                        </td>
                        <td>
                            <?$this->Form->TextBox('srch', $this->srch, 10, 'style="width:200px;"');?></h4>

                        </td>
                    </tr>
                    <tr class="tr2">
                        <td align="right" width="100">Дата:</td>
                        <td style="min-width: 215px;" align="left">
                            <?
                                if ( $this->fltr3 == NULL ) $this->fltr3 = '2014-01-01';
                                if ( !$this->fltr4 ) $this->fltr4 = date('Y-m-d');
                            ?>
                            с&nbsp;<?$this->Form->TextBox('fltr3', $this->fltr3, 10, 'id="fltr3" maxlength="10" placeholder="2015-01-01"');?>&nbsp;-&nbsp;по
                            &nbsp;<?$this->Form->TextBox('fltr4', $this->fltr4, 10, 'id="fltr4" maxlength="10" placeholder="2015-02-01"');?>
                        </td>

                        <td align="right" nowrap="nowrap">
                            товар:
                        </td>
                        <td >
                            <?$this->Form->TextBox('fltr5', $this->fltr5, 40, 'style="width:200px;"');?>
                        </td>
                    </tr>
                    <tr class="tr2">
                        <td align="right" nowrap="nowrap">
                            <?=$this->multi['FLD_USER_ID'];?>:
                        </td>
                        <td >
                            <?$this->Form->TextBox('fltr2', $this->fltr2, 40, 'style="width:200px;"');?>
                            <div style="color: #ACACAC;font-size:10px; margin: 0px 0px 5px 0px;">
                                Укажите ФИО, телефон, емейл или ID
                            </div>
                        </td>

                        <td align="left" colspan="2" style="text-align: center; vertical-align: middle">
                            <input  type="submit" value="<?=$this->multi['TXT_BUTTON_SEARCH'];?>"/>
                        </td>
                    <tr>
                </table>
            </td>
        </tr>
        <?
        AdminHTML::PanelSimpleF();
    } //end of fuinction ShowContentFilters()

    // ================================================================================================
    //    Function          : show
    //    Date              : 28.01.2011
    //    Returns           : Error Indicator
    //    Description       : Show orders
    // ================================================================================================
    function show()
    {
        //echo 'fltr:'.$this->fltr;
        $CatalogLayout = check_init('CatalogLayout', 'CatalogLayout');
      if( !$this->sort ) $this->sort='id';
        //var_dump($this->sort);

      $skip_search = false;
      $left_join = '';
      if(!empty($this->fltr5)){
          $fltr5_str = '';
          //search by names
          $q_temp = "SELECT `cod` FROM `".TblModCatalogPropSprName."` WHERE `name` LIKE '%$this->fltr5%'";
          $this->db->db_Query($q_temp);
          if ( $this->db->db_GetNumRows() > 0 ){
              while ( $row = $this->db->db_FetchAssoc() ) $arr_prod_id[ $row['cod'] ] = '';
          }
          //search by art_num
          $q_temp = "SELECT `id` FROM `".TblModCatalogProp."` WHERE `".TblModCatalogProp."`.`art_num` LIKE '%$this->fltr5%'";
          $this->db->db_Query($q_temp);
          if ( $this->db->db_GetNumRows() > 0 ){
              while ( $row = $this->db->db_FetchAssoc() ) $arr_prod_id[ $row['id'] ] = '';
          }
          if ( !empty($arr_prod_id) ) {
              $arr_prod_id = array_keys($arr_prod_id);
              $str_prod_id = implode(',', $arr_prod_id);
              $left_join = " INNER JOIN `" . TblModOrder . "` ON `" . TblModOrderComments . "`.`id_order`=`" . TblModOrder . "`.`id_order`";
              $fltr5_str = " AND `prod_id` IN ($str_prod_id)";
          }
          else $skip_search = true;
      }

      if ( !$skip_search ) {
          $q = "SELECT `" . TblModOrderComments . "`.* FROM `" . TblModOrderComments . "` " . $left_join . " WHERE 1";
          if ($this->fltr) $q = $q . " AND `" . TblModOrderComments . "`.`status`='" . $this->fltr . "'";
          if (!empty($this->srch)) $q .= " AND `" . TblModOrderComments . "`.`id_order` LIKE '%" . $this->srch . "%'";
          if (!empty($this->fltr2)) $q .= " AND (`" . TblModOrderComments . "`.`name` LIKE '%" . $this->fltr2 . "%' OR `" . TblModOrderComments . "`.`email` LIKE '%" . $this->fltr2 . "%' OR `" . TblModOrderComments . "`.`phone_mob` LIKE '%" . $this->fltr2 . "%')";
          if (!empty($this->fltr3)) $q .= " AND `" . TblModOrderComments . "`.`date` >= '" . $this->fltr3 . "'";
          if (!empty($this->fltr4)) $q .= " AND `" . TblModOrderComments . "`.`date` <= '" . $this->fltr4 . "23:59:59'";
          if (!empty($this->fltr5)) $q .= $fltr5_str;

          if (!empty($this->fltrUserId)) $q .= " AND `" . TblModOrderComments . "`.`buyer_id`='" . $this->fltrUserId . "'";
          $q = $q . " ORDER BY `" . TblModOrderComments . "`.`" . $this->sort . "` desc";
          $res = $this->Right->Query($q, $this->user_id, $this->module);
          //echo '<br>' . $q;
          //echo '<br/>$res=' . $res . '$this->Right->result=' . $this->Right->result . ' $this->user_id=' . $this->user_id;
          if (!$res) return false;
          $rows = $this->Right->db_GetNumRows();
      }
      else $rows = 0;

      $a = $rows;
      $j = 0;
      $row_arr = NULL;
      for( $i = 0; $i < $rows; $i++ ){
          $row = $this->Right->db_FetchAssoc();
          if( $i >= $this->start && $i < ( $this->start+$this->display ) )
          {
            $row_arr[$j] = $row;
            $j = $j + 1;
          }
      }

      /* Write Form Header */
      $this->Form->WriteHeader( $this->script );

      //========= Show Search Panel START ===========
      $this->ShowContentFilters();
      //========= Show Search Panel END ===========

      /* Write Table Part */
      AdminHTML::TablePartH();

      ?><tr><td colspan="10"><?
        $this->Form->WriteLinkPages( $this->script, $rows, $this->display, $this->start, $this->sort );

    ?>
    <tr><td colspan="4">
        <?
        if($this->Right->IsUpdate()) $this->Form->WriteSavePanel( $this->script );
        if($this->Right->IsDelete()) {
            if($this->fltr==6) $this->Form->WriteTopPanel( $this->script,2);
            else $this->Form->WriteTopPanel( $this->script,1);

        }
        ?>
        <a class="r-button" href="#" id="orderExportBtn">
            <span>
                <span>
                    <img src="images/icons/document-export.png" height="23" alt="Экпорт" title="Экпорт">
                    Экпорт
                </span>
            </span>
        </a>
        <?


        $script2 = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&task=show&fltr='.$this->fltr;
        $script2 = $_SERVER['PHP_SELF']."?$script2";
        ?>
    <tr>
        <th class="THead" width="30">
            <input type="button" value="BCE" id="selecAllCheckboxes">
        </th>
        <?/*
        <th class="THead" width="80"><A HREF=<?=$script2?>&sort=id_order><?=$this->multi['FLD_NUM_ORDER'];?></A></th>
        <th class="THead" width="100"><A HREF=<?=$script2?>&sort=date><?=$this->multi['FLD_DATE'];?></A></th>
        <th class="THead" width="80"><?=$this->multi['FLD_ORDER_STATUS'];?></th>
        */?>
        <th class="THead" width="400"><?=$this->multi['_TXT_COSTOMER'];?></th>
        <th class="THead"><?=$this->multi['_TXT_PRODUCT'];?></th>
        <?

        $style1 = 'TR1';
        $style2 = 'TR2';
        $style_prod1 = 'line1';
        $style_prod2 = 'line2';
        $old_id_order = NULL;

        $mass = $this->statuses;

        // Метод доставки
        $this->deliveryMethod = $this->GetSysSprTableData(TblModOrderSprDelivery);
        //Метод оплаты
        $this->payMethod = $this->GetSysSprTableData(TblModOrderSprPayMethod);

        ?>
        <script type="text/javascript">
            $(document).ready(function(){
                $('a.user-edit, a.user-orders').fancybox({
                    'hideOnContentClick' : true,
                    'width' : '90%',
                    'type' : 'iframe',
                    'padding' : 35,
                    'onComplete' : function() {
                        $('#fancybox-frame').load(function() { // wait for frame to load and then gets it's height
                            $('#fancybox-content').height($(this).contents().find('body').height()+30);
                        });
                    }
                });

                $('#orderExportBtn').click(function(){
                    $('#task').val('default');
                    $.fancybox({
                      type: 'ajax',
                      href: '/modules/mod_order/order_ImpExp.php?module=<?=$this->module?>',
                      ajax:{
                        data: $('#form_mod_order').serialize()
                      }
                    });
                });
                
                $('#selecAllCheckboxes').click(function(){
                     var checkboxes = document.getElementsByName('id_del[]');
//                     console.log('checkboxes',checkboxes);
                     $(checkboxes).attr('checked','checked');
                });
            })
        </script>
        <?

        $SysGroup = check_init('SysGroup', 'SysGroup');
        $arr_grp = $SysGroup->GetGrpNameToArr('front');

        $n = count( $row_arr );
        for( $i = 0; $i < $n; $i++ ){
            $row = $row_arr[$i];
            if ( (float)$i/2 == round( $i/2 ) ) { $class=$style1; }
            else { $class=$style2; }

            if($row['isread']==0) $new = '<sup class="newItemSup">NEW</sup>';
            else $new = '';
            ?>
            <tr class="<?=$class;?>">
                 <td><?$this->Form->CheckBox( "id_del[]", $row['id_order'], NULL, 'orderId'.$row['id'] );?></td>
                 <td colspan="6" align="left" ><a href="<?=$this->script?>&task=edit&id_order=<?=$row['id_order'];?>" style="font-size:14px;"><?=$this->multi['FLD_ORDER_ID'];?><strong><?=$row['id_order'];?></strong></a><?=$new;?>
                 &nbsp;<?=$this->multi['FLD_FROM'].' '.$row['date'];?>
                 <?$status = stripslashes($row['status']);?>
                 &nbsp;<?$this->Form->Select( $mass, 'status['.$row['id_order'].']', $status, 10,'onChange="$(\'#orderId'.$row['id'].'\').attr(\'checked\',true);"');?>
                 <?
                 $link = '/modules/mod_order/print_bill.php?module='.$this->module.'&id_order='.$row['id_order'];
                 $link2 = $link.'&waybill=service_waybill';
                 $link3 = $link.'&waybill=1';

                 $width = '700px';
                 $height = '800px';

                 $params = "OnClick='window.open(\"".$link."\", \"\", \"width=".$width.", height=".$height.", status=0, toolbar=0, location=0, menubar=0, resizable=0, scrollbars=1\"); return false;'";
                 $params2 = "OnClick='window.open(\"".$link2."\", \"\", \"width=".$width.", height=".$height.", status=0, toolbar=0, location=0, menubar=0, resizable=0, scrollbars=1\"); return false;'";
                 $params3 = "OnClick='window.open(\"".$link3."\", \"\", \"width=".$width.", height=".$height.", status=0, toolbar=0, location=0, menubar=0, resizable=0, scrollbars=1\"); return false;'";
                 ?>
                 &nbsp;Распечатать:
                 <a href="<?=$link;?>" <?=$params;?>>Счет</a>&nbsp;&nbsp;&nbsp;
                 <a href="<?=$link2?>" <?=$params2;?>>Накладная</a>&nbsp;&nbsp;&nbsp;
                 <a href="<?=$link2?>" <?=$params3;?>>Накладная-Клиент</a>
                 <?/*&nbsp;<span style="color:#087D2B" align="center"><?=$this->multi['FLD_ORDER_SUM']?>: <?=$row['sum'].' '.$this->Spr->GetNameByCod( TblSysCurrenciesSprSufix, $row['currency'], $this->lang_id, 1 );?></span>*/?>
                 </td>
            </tr>
            <tr class="<?=$class;?>">
            <?
            if( $row['buyer_id']!=0 ) {  // Существующий пользователь
                $user_data = $this->User->GetUserDataByUserId($row['buyer_id']);

            }
            if ( empty($user_data['aboutMe']) ) $class = 'empty';
            else $class = '';
            ?>


            <td valign="top" align="center">

                <!-- notes icon-->
                <? if (isset($user_data)): ?>
                <div class="about-user-wrapper">
                    <div class="widget-btn about-user-btn <?=$class?>"></div>
                    <div class="about-user-inner-wrapper <?=$class?>">
                        <div contenteditable class="about-user-text"></div>
                        <div class="info"></div>
                        <input class="btn save-btn" data-user-id="<?=$user_data['sys_user_id']?>" type="button" value="сохранить"/>
                        <input class="btn cancel-btn" type="button" value="закрыть"/>
                    </div>
                </div>
                <? endif; ?>

                <!-- email icon-->
                <? if ( !empty($row['email']) ): ?>
                <div class="send-to-email-wrapper">
                    <div class="widget-btn ste-btn" data-id-order="<?=$row['id_order']?>" data-email="<?=$row['email']?>" title="отправить на емейл"></div>
                </div>
                <? endif; ?>

                <!-- orders count icon-->
                <?
                if ( !empty($row['buyer_id']) ) {
                    if ($this->fltrUserId != $row['buyer_id']) {
                        $cntUsrOrders = $this->GetCountOfOrdersByUserId($row['buyer_id']);
                        if ($cntUsrOrders > 1) {
                            ?>
                            <a class="user-orders" data-fancybox-type="iframe"
                               href="<?php echo $_SERVER['PHP_SELF'] . "?module=106&fltrUserId=" . $row['buyer_id']; ?>"
                               title="количество заказов которые сделал пользователь"><?= $cntUsrOrders; ?></a>
                        <?
                        }
                    }

                    //var_dump($user_data['group_id'],$arr_grp );

                    if ($user_data['group_id'] != 5 && isset($arr_grp[$user_data['group_id']])):?>

                        <div class="vip-icon">
                            <div class="vip-icon-popup"><?= $arr_grp[$user_data['group_id']] ?></div>
                        </div>
                    <?endif;
                }

                //var_dump( $row['sms_send_status'], $row['id_order']);

                self::showTtnSmsStatusBadge( $row['sms_send_status'], $row['id_order'] );

                // if it was sended, or has other status
                if ( $row['sms_send_status'] == 1  || strlen($row['sms_send_status']) > 1){
                    ?>
                    <script>
                        checkTtnSmsStatus('<?=$row['id_order']?>');
                    </script>
                    <?
                }
                ?>
            </td>
            <?

            // -----------   start user -------------------
            ?>
            <td align="left" valign="top">
            <?/* echo $this->multi['FLD_FIO_FOR_ORDER']*/?>
            <?
            if( $row['buyer_id']!=0 ) {  // Существующий пользователь

                //var_dump($user_data);
                if(isset($user_data['sys_user_id']) AND !empty($user_data['sys_user_id'])){
                ?>
                    <a class="user-edit" data-fancybox-type="iframe" href="/admin/index.php?module=35&display=20&start=0&sort=&id=<?=$user_data['sys_user_id'];?>&task=edit"><?=stripslashes($row['name']);?></a><br>

                <?
                }else{
                    echo stripslashes($row['name']);?><br><?
                }
            }
            else {  // Пользователь без регистрации на сайте
                echo stripslashes($row['name']);?><br><?
            }

            ?>
            <br/>
            <b><?=$this->multi['FLD_EMAIL']?>:</b> <a href="mailto:<?=stripslashes($row['email']);?>"><?=stripslashes($row['email']);?></a><br/><br/>
            <b style="vertical-align: top"><?=$this->multi['FLD_PHONE_MOB']?>:</b> <?=stripslashes($row['phone_mob']);?>

            <br/>
            <br/>
            <?/*<?=$this->multi['FLD_PHONE']?>: <?=stripslashes($row['phone']);?> <br/> */?>
            <?/*<?=$this->multi['FLD_CITY']?>: <?=stripslashes($row['city']);?> <br/>*/?>
            <b><?=$this->multi['FLD_ADR']?>:</b> <?=stripslashes($row['city']);?><?=stripslashes($row['addr']);?> <br/>
            <? if(!empty($row['firm'])){?> Фирма: <?=stripslashes($row['firm']);?> <br /><? }

            ?>
            <br/>
            <b><?=$this->multi['TXT_FRONT_DELIVERY_METHOD'];?>:</b>
              <?php if(isset($row['delivery_method']) and !empty($row['delivery_method']) ):?>
              <?=$this->deliveryMethod[$row['delivery_method']];?>
              <br/>
              <?php endif;?>
            <br/>
            <b><?=$this->multi['TXT_FRONT_PAY_METHOD'];?>:</b>
            <?php if(isset($row['pay_method']) and !empty($row['pay_method']) ):?>
              <?=$this->payMethod[$row['pay_method']];?>
              <br/>
            <?php endif;?>
            <br/><b><?=$this->multi['FLD_COMMENT'];?>:</b><br/>
            <?
            if(!empty($row['comment'])){
                ?>
                <?=stripslashes(str_replace("\n", "<br>", stripslashes($row['comment'])));
            }
            ?>
            <br/>
            <? if(!empty($row['ttn'])): ?>
                <b>ТТН.:</b>&nbsp;<a class="nofancybox" href="http://novaposhta.ua/tracking/?cargo_number=<?=$row['ttn']?>" target="_blank"><?=stripslashes(str_replace("\n", "<br>", stripslashes($row['ttn'])));?></a>
            <? endif; ?>
            </td>
            <?
            // ----------- end user -------------------

            $order_prod = $this->GetProdOrdersByIdOrder($row['id_order']);



            $cnt_o = count($order_prod);
            ?>
            <td align="left" valign="top">
             <table border="0" cellpadding="2" cellspacing="1" class="EditTable" width="100%">
                 <tr valign="top" class="line0">
                  <td width="40"><?=$this->multi['FLD_IMG'];?></td>
                  <?/*<td width="40"><?=$this->multi['FLD_NUMBER_NAME'];?></td>*/?>
                  <td width="150" align="left"><?=$this->multi['FLD_PROD_ID'];?></td>
                  <?
                  if(isset($this->Catalog->settings['imgColors']) AND $this->Catalog->settings['imgColors']==1){
                      ?><td width="150" align="left"><?=$this->multi['TXT_COLOR_SELECTED'];?></td><?
                  }
                  if(isset($this->Catalog->settings['sizes']) AND $this->Catalog->settings['sizes']==1){
                      ?><td width="150" align="left"><?=$this->multi['TXT_SIZE_SELECTED'];?></td><?
                  }
                  ?>
                  <td width="100" align="center"><?=$this->multi['FLD_PRICE'];?></td>
                  <td width="40" align="center"><?=$this->multi['FLD_QUANTITY'];?></td>
                  <td width="120" align="center"><?=$this->multi['FLD_SUMA'];?></td>
                  <?/*
                  <td></td>
                  <td></td>
                  */?>
                 </tr>
             <?
             $str_prod = '';
             for($j=0; $j<$cnt_o; $j++){
                 if($j==0)
                     $str_prod = $order_prod[$j]['prod_id'];
                 else
                     $str_prod .= ', '.$order_prod[$j]['prod_id'];

             }

             $q_prod = "SELECT DISTINCT
                            `".TblModCatalogProp."`.*,
                            `".TblModCatalogPropSprName."`.name,
                            `".TblModCatalogSprName."`.name as cat_name,
                            `".TblModCatalogTranslit."`.`translit`,
                            `".TblModCatalogPropImg."`.`path` AS `first_img`,
                            `".TblModCatalogPropImgTxt."`.`name` AS `first_img_alt`,
                            `".TblModCatalogPropImgTxt."`.`text` AS `first_img_title`
                        FROM `".TblModCatalogProp."`
                            LEFT JOIN `".TblModCatalogPropImg."` ON (`".TblModCatalogProp."`.`id`=`".TblModCatalogPropImg."`.`id_prop` AND `".TblModCatalogPropImg."`.`move`='1' AND `".TblModCatalogPropImg."`.`show`='1')
                            LEFT JOIN `".TblModCatalogPropImgTxt."` ON (`".TblModCatalogPropImg."`.`id`=`".TblModCatalogPropImgTxt."`.`cod` AND `".TblModCatalogPropImgTxt."`.lang_id='".$this->lang_id."'),
                            `".TblModCatalogPropSprName."`,`".TblModCatalogSprName."`, `".TblModCatalog."`, `".TblModCatalogTranslit."`
                        WHERE `".TblModCatalogProp."`.`id` IN (".$str_prod.")
                        AND `".TblModCatalogProp."`.`id_cat`=`".TblModCatalog."`.`id`
                        AND `".TblModCatalogProp."`.visible='2'
                        AND `".TblModCatalog."`.`visible`='2'
                        AND `".TblModCatalogProp."`.id=`".TblModCatalogPropSprName."`.cod
                        AND `".TblModCatalogProp."`.id_cat=`".TblModCatalogSprName."`.cod
                        AND `".TblModCatalogPropSprName."`.lang_id='".$this->lang_id."'
                        AND `".TblModCatalogSprName."`.lang_id='".$this->lang_id."'
                        AND `".TblModCatalogProp."`.id=`".TblModCatalogTranslit."`.`id_prop`
                        AND `".TblModCatalogTranslit."`.`lang_id`='".$this->lang_id."'
                        ";
             $arr_prod = array();
             $res_prod = $this->db->db_Query($q_prod);
             if ($res_prod) {
                 //echo '<br>$q_prod='.$q_prod.' $res_prod='.$res_prod;
                 $rows_prod = $this->db->db_GetNumRows();
                 for ($j = 0; $j < $rows_prod; $j++) {
                     $row_tmp = $this->db->db_FetchAssoc();
                     $arr_prod[$row_tmp['id']] = $row_tmp;
                 }
             }

             for($j=0; $j<$cnt_o; $j++){
                 if ( (float)$j/2 == round( $j/2 ) ) { $class=$style_prod1; }
                 else { $class=$style_prod2; }
                 $prod_id = $order_prod[$j]['prod_id'];
                 if(!isset($arr_prod[$prod_id])){
                     $row_prod = 0;
                 }else{
                     $row_prod = $arr_prod[$prod_id];
                 }


                 $href = $CatalogLayout->Link($row_prod['id_cat'], $row_prod['id']);
                 /*
                 $q_prod = "SELECT `".TblModCatalogProp."`.`number_name`, `".TblModCatalogPropSprName."`.`name`
                            FROM `".TblModCatalogProp."`, `".TblModCatalogPropSprName."`
                            WHERE `".TblModCatalogProp."`.id = '".$order_prod[$j]['prod_id']."'
                            AND `".TblModCatalogProp."`.id = `".TblModCatalogPropSprName."`.`cod`
                            AND `".TblModCatalogPropSprName."`.lang_id = '"._LANG_ID."'
                            ";
                 $res_prod = $this->db->db_Query($q_prod);
                 echo '<br>$q_prod='.$q_prod.' $res_prod='.$res_prod;
                 $row_prod = $this->db->db_FetchAssoc();
                  *
                  */
                 ?>
                 <tr class="<?=$class;?>">
                  <td valign="top"><?=$this->Catalog->ShowCurrentImage($row_prod['first_img'], 'size_auto=75', 85, NULL, NULL, $row_prod['id'], false);?></td>
                  <?/*<td valign="top"><?=$row_prod['number_name'];?></td>*/?>
                  <td align="left" valign="top">
                      <?
                      if(isset($row_prod['name']) AND !empty($row_prod['name'])){
                          ?><a href="<?=$href;?>"><?=stripslashes($row_prod['name']);?></a><?
                          if (!empty($row_prod['art_num'])):?>
                              <br/>артикул: <?=$row_prod['art_num']?>
                          <?endif;
                      }else{
                          echo 'Товар удален из каталога';
                      }
                      ?>
                  </td>
                  <?
                  if(isset($this->Catalog->settings['imgColors']) AND $this->Catalog->settings['imgColors']==1){
                      ?><td width="150" align="left"><?=$order_prod[$j]['colorId'];?></td><?
                  }
                  if(isset($this->Catalog->settings['sizes']) AND $this->Catalog->settings['sizes']==1){
                      ?><td width="150" align="left"><?=$order_prod[$j]['sizeId'];?></td><?
                  }
                  ?>
                  <td align="center" valign="top"><?=$this->Currency->ShowPrice($order_prod[$j]['price'])?></td>
                  <td valign="top"><?=$order_prod[$j]['quantity'];?></td>
                  <td align="center" valign="top"><?=$this->Currency->ShowPrice($order_prod[$j]['sum'])?></td>
                  <?/*
                  <td><?=stripslashes($order_prod[$j]['comment']);?></td>
                  <td><?=$order_prod[$j]['parameters'];?></td>
                  */?>
                 </tr>
                 <?
             }
             ?>
                 <tr  class="line0" style="font-weight:bold;">
                     <?
                     $colspan=3;
                     if(isset($this->Catalog->settings['imgColors']) AND $this->Catalog->settings['imgColors']==1){
                         $colspan++;
                     }
                     if(isset($this->Catalog->settings['sizes']) AND $this->Catalog->settings['sizes']==1){
                         $colspan++;
                     }
                     ?>
                  <td colspan="<?=$colspan;?>" align="right">ИТОГО: </td>
                  <td align="center"><?=$row['qnt_all']?></td>
                  <td align="right"><?=$this->Currency->ShowPrice($row['sum'])?></td>
                 </tr>
             </table>
            </td>
           </tr>
           <tr><td height="20"></td></tr>
            <?
          $a=$a-1;
        } //-- end for
        ?>
        <script>
            init_widget_buttons();
        </script>
        <?

        AdminHTML::TablePartF();
        if($this->Right->IsUpdate()) $this->Form->WriteSavePanel( $this->script );

        $this->Form->WriteFooter();

    } //end of function show


    // ================================================================================================
    // Function : save()
    // Date : 03.04.2006
    // Parms : $id_order
    // Returns : true,false / Void
    // Description : Store status of the order
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function save($id_order)
    {
        $cnt  = count($id_order);
        for($j=0;$j<$cnt;$j++){
            //$res = $this->SendNotificationToUserEmail($id_order[$j], $this->status[$id_order[$j]]);
            //if($res) echo 'Уведомление о смене статуса заказа №'.$id_order[$j].' успешно отправлено заказчику<br/>';
            $q = "UPDATE `".TblModOrderComments."` SET `status` = '".$this->status[$id_order[$j]]."' WHERE `id_order` = '".$id_order[$j]."' ";
            //echo "<br> q = ".$q;
            $res = $this->Right->Query( $q, $this->user_id, $this->module );
        }
        return true;
    } //end of fuinction save()

    /**
    * Class method SendNotificationToUserEmail
    * send notification to user_email with new status of order
    * @return true/false or arrays:
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 30.07.2012
    */
    function SendNotificationToUserEmail( $id_order, $status )
    {
        $this->Err = NULL;
        $mail_user = new Mail();
        $body = '';

        $o_comm = $this->GetOrderCommentInArr($id_order);
        $user_name = stripslashes($o_comm['name']);
        $user_email = stripslashes($o_comm['email']);
        $dt = explode(' ', $o_comm['date']);

        //-------- build body of email message START ----------
        //если статус заказа изменен на "Отправлен", то отправлется специальное уведомление с номером ТТН (ели он есть)
        if($status==3 AND !empty($o_comm['ttn'])){
            $carrier_data = $this->Spr->GetDataByCod( TblModOrderSprDelivery, $o_comm['delivery_method'], $this->lang_id);
            $carrier_name = stripslashes($carrier_data['name']);
            $carrier_link = stripslashes($carrier_data['href']);
            $body = '
            <div>
            '.$this->multi['TXT_ORDER_DEAR'].' '.$user_name.',
            <br/>'.$this->multi['TXT_ORDER_SENDING_FOR_YOU'].$id_order.' '.$this->multi['TXT_ORDER_FROM'].' '.$dt[0].' '.$this->multi['TXT_COST'].' '.$this->Currency->ShowPrice($o_comm['sum']).'
            <br/>'.$this->multi['TXT_ORDER_CARRIER'].': '.$carrier_name.'
            <br/>'.$this->multi['TXT_ORDER_DELIVERY_ADDRESS'].': '.stripslashes($o_comm['addr']).'
            <br/>'.$this->multi['TXT_ORDER_RECIPIENT'].': '.$user_name.'
            <br/>'.$this->multi['FLD_ORDER_TTN_NUMBER'].': '.stripslashes($o_comm['ttn']).'
            <br/>'.$this->multi['TXT_ORDER_CARRIER_TRACKING_LNK'].': '.$carrier_link.'
            <br/>'.$this->multi['TXT_ORDER_TRACKING'].' <a href="http://'.$_SERVER['SERVER_NAME'].'/order/history/">'.$this->multi['TXT_ORDER_TRACKING_2'].'</a>
            <br/>
            </div>
            ';
        }else{
            $body = '
            <div>
            '.$this->multi['TXT_ORDER_DEAR'].' '.$user_name.',
            <br/>'.$this->multi['TXT_ORDER_STATUS_CHANGE_TO'].$id_order.' '.$this->multi['TXT_ORDER_FROM'].' '.$dt[0].' '.$this->multi['TXT_COST'].' '.$this->Currency->ShowPrice($o_comm['sum']).' '.$this->multi['TXT_ORDER_STATUS_CHANGE_TO_2'].' "'.$this->statuses[$status].'".
            <br/>'.$this->multi['TXT_ORDER_TRACKING'].' <a href="http://'.$_SERVER['SERVER_NAME'].'/order/history/">'.$this->multi['TXT_ORDER_TRACKING_2'].'</a>
            <br/>
            </div>
            ';
        }
        //-------- build body of email message END ----------
        //echo '<br>$body='.$body;

        $subject = $this->multi['TXT_ORDER_NOTIFICATION_SBJ'].' '.$id_order.' '.$this->multi['TXT_ORDER_NOTIFICATION_SBJ_2'].' '.$_SERVER['SERVER_NAME'];
        //echo '<br>$subject='.$subject;

        $mail_user->AddAddress($user_email);
        $mail_user->WordWrap = 500;
        $mail_user->IsHTML( true );
        $mail_user->Subject = $subject;
        $mail_user->Body = $body;
        $res_user = $mail_user->SendMail();
        //echo '<br>$res_user='.$r$res_useres;
        return $res_user;

    } //end of function SendNotificationToUserEmail()



    // ================================================================================================
    // Function : edit
    // Date : 03.04.2006
    // Returns : true,false / Void
    // Description : Show data from $module table
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function edit( )
    {
        $order_arr = array();
        if( $this->id_order!=NULL  ){
            $mas = $this->GetOrderCommentInArr($this->id_order);

            $order_arr = $this->GetProdOrdersByIdOrder($this->id_order);

            if($mas['isread']==0){
                $q = "UPDATE `".TblModOrderComments."` SET `isread`='1' WHERE `id_order`='".$this->id_order."'";
                $res = $this->Right->query($q, $this->user_id, $this->module);
            }
        }
        else $mas = NULL;
        //$curr_id = $mas['currency'];

        // Write Form Header
        $this->Form->WriteHeaderFormImg( $this->script );
        $this->Form->Hidden( 'srch', $this->srch );
        $this->Form->Hidden( 'fltr', $this->fltr );
        $this->Form->Hidden( 'fltr2', $this->fltr2 );
        $this->Form->Hidden( 'fltrUserId', $this->fltrUserId );

        if( $this->id_order!=NULL )
            $txt = $this->multi['_TXT_EDIT_DATA'];
        else
            $txt = $this->multi['_TXT_ADD_DATA'];

        AdminHTML::PanelSubH( $txt );

        //-------- Show Error text for validation fields --------------
        $this->showErr();
        //-------------------------------------------------------------

        AdminHTML::PanelSimpleH();
        ?>
        <table border="0" class="EditTable"  width="100%">
         <tr>
          <td width="20%">
           <b><?=$this->multi['FLD_ID']?>:</b>
           <?
           if( $this->id_order!=NULL ){
               echo $mas['id'];
           }
           ?>
          </td>
         </tr>
         <tr>
          <td width="20%">
           <b><?=$this->multi['FLD_ORDER_ID']?>:</b>
          </td>
          <td>

           <?
           if( $this->id_order!=NULL ) $this->Err!=NULL ? $date=$this->date : $date=$mas['date'];
           else $date=$this->date;
           $this->Form->Hidden( 'date', $date );
           if( $this->id_order!=NULL ){
               echo '<b>'.$mas['id_order'].'</b> '.$this->multi['FLD_FROM'].' '.$date;
               $this->Form->Hidden( 'id_order', $mas['id_order'] );
           }
           else $this->Form->Hidden( 'id_order', '' );

           ?>
          </td>
         </tr>
         <tr>
             <td>
                 <b><?=$this->multi['FLD_STATUS']?>:</b>
             </td>
             <td>
                 <?
                 if( $this->id_order!=NULL ) $this->Err!=NULL ? $val=$this->status : $val=$mas['status'];
                  else $val=$this->status;
                 $this->Form->Select($this->statuses, 'status', $val);
                 $this->Form->Hidden('old_status', $mas['status']);
                 ?>
             </td>
         </tr>
         <tr>
          <td><b><?=$this->multi['FLD_FIO_FOR_ORDER']?>:</b></td>
          <td>
           <?
           if( $this->id_order!=NULL ) $this->Err!=NULL ? $val=$this->name : $val=$mas['name'];
           else $val=$this->name;
           $this->Form->TextBox( 'name', stripslashes($val), 50 );
           ?>
          </td>
         </tr>
         <tr>
          <td><b><?=$this->multi['FLD_PHONE_MOB']?>:</b></td>
          <td>
           <?
           if( $this->id_order!=NULL ) $this->Err!=NULL ? $val=$this->phone_mob : $val=$mas['phone_mob'];
           else $val=$this->phone_mob;
           $this->Form->TextBox( 'phone_mob', stripslashes($val), 50 );
           ?>
          </td>
         </tr>
         <tr>
          <td><b><?=$this->multi['FLD_PHONE']?>:</b></td>
          <td>
           <?
           if( $this->id_order!=NULL ) $this->Err!=NULL ? $val=$this->phone : $val=$mas['phone'];
           else $val=$this->phone;
           $this->Form->TextBox( 'phone', stripslashes($val), 50 );
           ?>
          </td>
         </tr>
         <tr>
          <td><b><?=$this->multi['FLD_EMAIL']?>:</b></td>
          <td>
           <?
           if( $this->id_order!=NULL ) $this->Err!=NULL ? $val=$this->email : $val=$mas['email'];
           else $val=$this->email;
           $this->Form->TextBox( 'email', stripslashes($val), 50 );
           ?>
          </td>
         </tr>
         <?/*<tr>
          <td><b><?=$this->multi['FLD_CITY']?>:</b></td>
          <td>
           <?
           if( $this->id_order!=NULL ) $this->Err!=NULL ? $val=$this->city : $val=$mas['city'];
           else $val=$this->city;
           $this->Form->TextBox( 'city', stripslashes($val), 50 );
           ?>
          </td>
         </tr>*/?>
         <tr>
          <td><b><?=$this->multi['FLD_ADR']?>:</b></td>
          <td>
           <?
           //if( $this->id_order!=NULL ) $this->Err!=NULL ? $city=$this->city : $city=$mas['city'];
           if( $this->id_order!=NULL ) $this->Err!=NULL ? $val=$this->addr : $val=$mas['addr'];
           else $val=$this->addr;
           //$this->Form->Textarea( 'addr', stripslashes($val), 3, 90 );

           ?>
            <!-- nova poshta -->
            <div id="novaPoshta">
                <div>
                    <input style="float: none" type='button' class='button_np gmap-btn' title='Подобрать на карте' value='' href='/np/index.php' />
                    <a style="float: none" href="/np/index.php" class='button_np' style="display: block; margin-right: 10px">Подобрать на карте</a>
                </div>

                <link rel="stylesheet" href="/np/js/colorbox.css"/>
                <link rel="stylesheet" href="/np/js/button.css"/>
                <script type="text/javascript" src="/np/js/jquery.colorbox-min.js"></script>
                <script type="text/javascript" src="/np/script.js"></script>
                <textarea id="npAdress" class="npochta area0" name="addr" placeholder="Для поиска введите ключевые слова,
например: Киев Набережная" ><?=$val?></textarea>
            </div>
            <!-- end nova poshta -->
          </td>
         </tr>
         <tr>
          <td><b><?=$this->multi['FLD_COMMENT']?>:</b></td>
          <td>
           <?
           if( $this->id_order!=NULL ) $this->Err!=NULL ? $val=$this->comment : $val=$mas['comment'];
           else $val=$this->comment;
           $this->Form->Textarea( 'comment', stripslashes($val), 3, 50 );
           ?>
          </td>
         </tr>
         <tr>
            <td><b><?=$this->multi['FLD_ORDER_TTN_NUMBER']?>:</b></td>
            <td>
                <?
                if( $this->id_order!=NULL ) $this->Err!=NULL ? $val=$this->ttn : $val=$mas['ttn'];
                else $val=$this->ttn;
                $this->Form->TextBox( 'ttn', stripslashes($val), 50 );
                ?>
            </td>
         </tr>
         <tr><td colspan="2"><hr /></td></tr>
         <tr>
          <td>
              <b><?=$this->multi['TXT_FRONT_PAY_METHOD']?>:</b>
              <br/><br/>
              <?
              $mas['no_commission']==1 ? $nc_checked = 'checked' : $nc_checked='';
              $this->Form->CheckBox( "no_commission", '1', $mas['no_commission'], NULL, $nc_checked, 'оплата без комиссии' );
              ?>
          </td>
          <td>
           <?
           if( $this->id_order!=NULL ) $this->Err!=NULL ? $val=$this->pay_method : $val=$mas['pay_method'];
           else $val=$this->pay_method;
           $q = "Select * from `".TblModOrderSprPayMethod."` where `lang_id`='"._LANG_ID."'";
           $res = $this->db->db_Query( $q );
           if( !$res ) return false;
           $rows = $this->db->db_GetNumRows();
           $k=1;

           for($i=0; $i<$rows; $i++){
               $row = $this->db->db_FetchAssoc();
               ?>
               <div style="overflow:hidden; padding-top:8px;">
                   <div style="float:left; margin:0px 4px 0px 0px;"><?$this->Form->Radio('pay_method', '', $row['cod'], $val);?></div>
                   <div><strong><?=$row['cod'];?>. <?=$row['name'];?></strong><br /><?=strip_tags($row['descr'], "<b><a><strong>")?></div>
               </div>
               <?
               $k++;
           }
           ?>
          </td>
         </tr>
         <tr><td colspan="2"><hr /></td></tr>
         <tr>
          <td>
              <b><?=$this->multi['TXT_FRONT_DELIVERY_METHOD']?></b>
              <br/><br/>
              <?php
              $mas['free_delivery']==1 ? $fd_checked = 'checked' : $fd_checked='';
              $this->Form->CheckBox( "free_delivery", '1', $mas['free_delivery'], NULL, $fd_checked, 'безплатная доставка');
              ?>
          </td>
          <td>
           <?

           if( $this->id_order!=NULL ) $this->Err!=NULL ? $val=$this->delivery_method : $val=$mas['delivery_method'];
           else $val=$this->delivery_method;
           $q = "Select * from `".TblModOrderSprDelivery."` where `lang_id`='"._LANG_ID."'";
           $res = $this->db->db_Query( $q );
           if( !$res ) return false;
           $rows = $this->db->db_GetNumRows();
           $k=1;

           for($i=0; $i<$rows; $i++){
               $row = $this->db->db_FetchAssoc();
               ?>
               <div style="overflow:hidden; padding-top:8px;">
                   <div style="float:left; margin:0px 4px 0px 0px;"><?$this->Form->Radio('delivery_method', '', $row['cod'], $val);?></div>
                   <div><strong><?=$row['cod'];?>. <?=$row['name'];?></strong><br /><?=strip_tags($row['descr'], "<b><a><strong>")?></div>
               </div>
               <?
               $k++;
           }
           ?>
          </td>
         </tr>
         <tr><td colspan="2"><hr></td></tr>
         <tr>
             <td align="left">
                 <br/>
                 <?php
                 if( $this->id_order!=NULL ):
                     $link = '/modules/mod_order/print_bill.php?module=' . $this->module . '&id_order=' . $this->id_order;
                     $link2 = $link.'&waybill=1';
                     $width = '770px';
                     $height = '1440px';
                     $params = "OnClick='window.open(\"" . $link . "\", \"\", \"width=" . $width . ", height=" . $height . ", status=0, toolbar=0, location=0, menubar=0, resizable=0, scrollbars=1\"); return false;'";
                     $params2 = "OnClick='window.open(\"" . $link2 . "\", \"\", \"width=" . $width . ", height=" . $height . ", status=0, toolbar=0, location=0, menubar=0, resizable=0, scrollbars=1\"); return false;'";?>
                     Распечатать: <a href="<?=$link?>" <?=$params?> >Счет</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo $link2; ?>" <?=$params2?>>Накладная</a>
                 <? else: ?>
                     ( накладная не доступна - сохраните заказ )
                 <? endif;?>
             </td>

             <td align="right">
             <?
                 $q = "
                     SELECT `".TblSysUser."`.`group_id`
                     FROM `".TblModOrderComments."` LEFT JOIN `".TblSysUser."` ON `".TblModOrderComments."`.`buyer_id`=`".TblSysUser."`.`id`
                     WHERE `".TblModOrderComments."`.`id_order`='" .$this->id_order. "'
                 ";
                 //echo $q;
                 $res = $this->db->db_Query($q);
                 if($res) {
                     if ($this->db->db_GetNumRows() > 0) {
                         $row = $this->db->db_FetchAssoc();
                         $user_id_group = $row['group_id'];
                     }
                 }
                 if (empty($user_id_group)) $user_id_group = 5;

                 if ( !empty($mas['custom_price_group']) ) $cur_price_group = $mas['custom_price_group'];
                 else $cur_price_group = $user_id_group;

                 $SysGroup = new SysGroup();
                 $arr_grp = $SysGroup->GetGrpNameToArr('front');
                 ?>
                 ( группа пользователя: <span id="curPriceGroup"><?=$arr_grp[$user_id_group]?></span> )<br/>
                 <b><?=$this->Msg->show_text('_FLD_GROUP');?> цен:</b><?

                 $this->Form->Select($arr_grp, 'group_id', $cur_price_group, NULL, 'id="customPriceGroup"');
                 ?>
                 <input type="button" value="пересчитать" onclick="setCustomPriceGroup('<?=$this->id_order?>')"/>
             </td>
         </tr>
         <tr><td colspan="2" height="15"></td></tr>
         <tr>
          <td colspan="2">
             <div id="tableprod">
             <?$this->EditOrdersProd($mas, $order_arr);?>
             </div>
          </td>
         </tr>
        </table>
        <?
        AdminHTML::PanelSimpleF();

        if($this->Right->IsUpdate()) $this->Form->WriteSaveAndReturnPanel( $this->script );?>&nbsp;<?
        if($this->Right->IsUpdate()) $this->Form->WriteSavePanel( $this->script, 'save_order_backend' );?>&nbsp;<?
        $this->Form->WriteCancelPanel( $this->script );?>&nbsp;<?
        AdminHTML::PanelSubF();

        $this->Form->WriteFooter();

        ?>
        <script language="JavaScript">

            function calc_total_sum(){
                total_sum = 0;
                $('.prod-price input').each(function(){
                    row_id = $(this).data('prod-id');
                    price = $(this).val();
                    qnt = $('#quantity'+row_id).val();
                    sum = price * qnt;

                    $('#prodSum'+row_id).html( sum + ' грн.' );
                    total_sum += sum;

                    console.log('price: ' + price + ' qnt: ' + qnt);
                });

                $('#totalSum').html( total_sum + ' грн.');
                console.log('total_sum: '+ total_sum);
            }

            function changeField( field, id ){
                /*
                k = (document.all)?event.keyCode : arguments.callee.caller.arguments[0].which;
                //alert('k='+k);
                if(k==13){
                */

                //console.log(field + ' change');
                setTimeout(function(){
                    //new_val = $('#'+field+id).val();
                    calc_total_sum();
                },300);

                /*
                $.ajax({
                    type: "POST",
                    url: '/modules/mod_order/order.backend.php',
                    data: {
                        task     : 'change_field',
                        module   : 106,
                        id_order : '<?//=$this->id_order?>',
                        id       : id,
                        field    : field,
                        new_val  : new_val,
                        price_group : <?//=$user_id_group?>
                    },
                    success: function (html) {
                        //console.log('ok');
                        $('#totalSum').html( html );

                    }
                });
                */

                /*
                }
                */
            }

            function setCustomPriceGroup( order_id ){
                //  console.log('val:' + $("#customPriceGroup option:selected").val());
                $.ajax({
                    url: '/modules/mod_order/order.backend.php',
                    dataType: 'json',
                    data: {
                        task: 'custom_price_group',
                        module: '106',
                        id_order: order_id,
                        price_group: $("#customPriceGroup option:selected").val()
                    },
                    success: function( res ){
                        if ( res=='0') {
                            alert('ошибка');
                            return;
                        }
                        else{
                            //$('#tableprod').html( res );
                            $.each( res, function( id, price ){
                                $('#price'+id).val( price );
                                //console.log('#price'+id+': ' + price);
                            });
                            calc_total_sum();
                        }

                    }
                })
            }// end of function setCustomPriceGroup

        function DelProd(div_id, url){
            if( !window.confirm('<?=$this->Msg->get_msg('_SYS_QUESTION_IS_DELETE');?>')) return false;
            else{
              did = "#"+div_id;
              $.ajax({
                  type: "POST",
                  url: url,
                  data: "",
                  success: function(html){
                      $(did).empty();
                      $(did).append(html);
                  },
                  beforeSend: function(html){
                      $(did).html('<img src="images/icons/loading_animation_liferay.gif"/>');
                  }
              });
            }
        } // end of function DelProd



        function ShowAddProdItem(div_id, url){
          did = "#"+div_id;
          $.ajax({
              type: "POST",
              url: url,
              data: "",
              success: function(html){
                  $(did).empty();
                  $(did).append(html);
              },
              beforeSend: function(html){
                  $(did).html('<img src="images/icons/loading_animation_liferay.gif"/>');
              }
          });
        } // end of function ShowAddProdItem

        function AddProdItem(div_id, url, form_name){
          did = "#"+div_id;
          dta = $('#'+form_name).formSerialize();
          $.ajax({
              type: "POST",
              url: url,
              data: dta,
              success: function(html){
                  $(did).empty();
                  $(did).append(html);
              },
              beforeSend: function(html){
                  $(did).html('<img src="images/icons/loading_animation_liferay.gif"/>');
              }
          });
        } // end of function ShowAddProdItem

        function LoadProdData(div_id, url, form_name){
          did = "#"+div_id;
          dta = $('#'+form_name).formSerialize();
          $.ajax({
              type: "POST",
              url: url,
              data: dta,
              success: function(html){
                  $(did).empty();
                  $(did).append(html);
              },
              beforeSend: function(html){
                  $(did).html('<img src="images/icons/loading_animation_liferay.gif"/>');
              }
          });
        } // end of function ShowAddProdItem

        function sellAllRelatProps(){
            $('.for-sel-all input[type="checkbox"]').attr('checked','checked');
            $('.for-sel-all').addClass('propSelected');
        }

        function removeAllRelatProps(){
            $('.for-sel-all input[type="checkbox"]').attr('checked',false);
            $('.for-sel-all').removeClass('propSelected');
        }
        </script>
        <?
    } //end of fuinction edit()

    // ================================================================================================
    // Function : EditOrdersProd()
    // Date : 12.05.2010
    // Parms : $id_order
    // Returns : true,false / Void
    // Description : recalculate order
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function EditOrdersProd($mas, $order_arr)
    {
        $curr_id = $mas['currency'];
        if(empty($curr_id)) $curr_id = $this->Currency->defCurrencyData['id'];
        ?>
           <table border="1" cellspacing="0" cellpadding="5" width="100%" align="left" class="EditTable">
              <tr style="font-weight:bold;">
               <?/*<td align="left" width="130"> <?=$this->multi['FLD_NUMBER_NAME'];?></td>*/?>
               <td align="center" width="30">№</td>
               <td align="left"> <?=$this->multi['_TXT_PRODUCT'];?></td>
               <td align="center" width="110" class="td_border"><?=$this->multi['FLD_PRICE'];?> <??></td>
               <td align="center" width="40" class="td_border"><?=$this->multi['FLD_QUANTITY'];?></td>
               <td align="center" width="100" class="td_border"><?=$this->multi['FLD_SUMA'];?></td>
               <td align="center" width="75" class="td_border"><?=$this->multi['TXT_DELETE'];?></td>
              </tr>
              <?

               $cnt = sizeof($order_arr);
               $summ_all = 0;
               for($i=0;$i<$cnt;$i++){
                   $order_data = $order_arr[$i];
                   //при редактировании заказа все стоимости и суммы отображаются в валюте, в какой валюте пользователь делал заказ

                   $price = $this->Currency->Converting($order_data['currency'], $curr_id, $order_data['price'], 2);
                   $sum = $this->Currency->Converting($order_data['currency'], $curr_id, $order_data['sum'], 2);
                   $summ_all += $sum;
                   //`".TblModCatalogProp."`.`number_name`,
                   $q_prod = "SELECT  `".TblModCatalogPropSprName."`.`name`
                        FROM `".TblModCatalogProp."`, `".TblModCatalogPropSprName."`
                        WHERE `".TblModCatalogProp."`.id = '".$order_data['prod_id']."'
                        AND `".TblModCatalogProp."`.id = `".TblModCatalogPropSprName."`.`cod`
                        AND `".TblModCatalogPropSprName."`.lang_id = '"._LANG_ID."'
                        ";

                    $res_prod = $this->db->db_Query($q_prod);
                    //echo '<br>$q_prod='.$q_prod.' $res_prod='.$res_prod;
                    $row_prod = $this->db->db_FetchAssoc();
                    $name = stripslashes($row_prod['name']);
                    $id = $order_data['id'];
                    $prod_id = $order_data['prod_id']
                    ?>
                    <tr align="center">
                    <?/*<td align="left"><?=$row_prod['number_name'];?></td>*/?>
                    <td align="center"><?=$i+1?></td>
                    <td align="left"><?=$name;?></td>
                    <td>
                        <div class="prod-price">
                        <?
                        $currency_data = $this->Currency->def_curr;
                        $sufix = stripslashes($currency_data['suf']);
                        $this->Form->TextBox(
                            'price['.$id.']',
                            $this->Currency->ShowPrice($price, false), 9,
                            ' id="price'.$prod_id.'" data-prod-id='.$prod_id.' onkeydown="changeField(\'price\', \''.$prod_id.'\')" '
                        );
                        echo ' '.$sufix;
                        ?>
                        </div>
                    </td>
                    <td>
                        <?
                        //echo '<br />$this->id='.$this->id.' $this->Err='.$this->Err;
                        if( $this->id_order!=NULL ) {
                            $this->Err!=NULL ? $val = $this->quantity[$id] : $val = $order_data['quantity'];
                        }
                        else $val = $order_data['quantity'];
                        $val = stripslashes($val);
                        $this->Form->TextBox( 'quantity['.$id.']', $val, 4, ' id="quantity'.$prod_id.'" onkeydown="changeField(\'quantity\', \''.$prod_id.'\')" ' );
                        ?>
                        шт.
                    </td>
                    <td><span id="prodSum<?=$prod_id    ?>"><?=$this->Currency->ShowPrice($sum);?></span></td>
                    <td><a href="" onclick="DelProd('tableprod', '/modules/mod_order/order.backend.php?module=<?=$this->module;?>&task=del_prod_item&id_order=<?=$mas['id_order'];?>&id=<?=$id;?>'); return false;"><?=$this->multi['TXT_DELETE'];?></a></td>
                    </tr>
                    <?
              } // end for prod

              if($this->Right->IsUpdate()){
              ?>
              <tr>
                  <td colspan="6" align="left">
                      <div id="addproditem">
                          <?php
                          $prop_str = '';
                          foreach ($order_arr as $row){
                              if ( empty($prop_str) ) $prop_str = $row['prod_id'];
                              else $prop_str .= ', '.$row['prod_id'];
                          }
                          ?>

                          <?/*<input type="button" value="+" onclick="ShowAddProdItem('addproditem', '/modules/mod_order/order.backend.php?module=<?=$this->module;?>&task=show_add_prod_item&id_order=<?=$mas['id_order'];?>'); return false;">*/?>
                          <input type="button" value="+" onclick="showCatalog()">
                          <input type="hidden" name="propStr" value="<?=$prop_str?>"/>
                          <script>



                              /* code below grabbed from mod_catalog/RelatProp.class.php, and modified with no optimization */
                              function showCatalog(){
                                  $.fancybox({
                                      href : "/admin/index.php?module=96&task=CatalogShow&ajax=1&propStr="+ $('input[name=propStr]').val(),
                                      onComplete:function(){
                                          //makeTree();
                                      }
                                  });
                              }

                              function makeTree(){
                                  $("#tree").treeview({
                                      collapsed: true,
                                      animated: "medium",
                                      control:"#sidetreecontrol",
                                      persist: "cookie",
                                      cookieId: "catalogTreeView"
                                  });
                              }

                              function reloadCatalogInner($catId,$url){
                                  if($catId=='') $catId=0;
                                  if(!$url) $url="<?="index.php?module=96&task=CatalogInnerShow&ajax=1&propStr="?>"+$("#propStr").val()+"&id_cat="+$catId+"&propStronThisPropPanel="+$("#propStr").val();
                                  $.ajax({
                                      url : $url,
                                      type : "POST",
                                      beforeSend : function(){
                                          $("#loader").show();
                                      },
                                      success : function(data){
                                          $("#loader").hide();
                                          $("#relatPropCatalog").html(data);
                                      }
                                  });
                              }

                              function SelectDeselectProp($id){
                                  $checkObj=$($id);
                                  $checkObj.attr('checked',!$checkObj.attr('checked'));
                                  if($checkObj.attr('checked')=='checked') $checkObj.parent().addClass("propSelected");
                                  else $checkObj.parent().removeClass("propSelected");
                              }

                              function AddRelatPropsTo(){
                                  //kostyl
                                  $('#addPropTo').find('input[type=hidden]').val('add_prod_item');

                                  did = "#tableprod";
                                  dta = $("#addPropTo").serialize();
                                  $.ajax({
                                      type: "POST",
                                      url: '/modules/mod_order/order.backend.php?task=add_prod_item&module=106&id_order=<?=$this->id_order?>',
                                      data: dta,
                                      success: function (html) {
                                          $(did).empty();
                                          $(did).append(html);
                                          showCatalog();
                                      },
                                      beforeSend: function (html) {
                                          $(did).html('<img src="images/icons/loading_animation_liferay.gif"/>');
                                      }
                                  });
                              }
                          </script>
                      </div>
                  </td>
              </tr>
              <?
              }
              ?>
              <tr>
               <td colspan="4" style="height:45px; margin-top:15px; padding-right:28px;" align="right">
                <b><?=$this->multi['TXT_TOTAL_COST']?>:</b>
               </td>
               <td>
                   <b><span id="totalSum">
                   <?
                    if($this->id_order) echo $this->Currency->ShowPrice($mas['sum']);
                    else echo $this->Currency->ShowPrice($summ_all);
                    ?>
                   </span>
                   </b>
               </td>
              </tr>
           </table>
        <?
    }//end of function EditOrdersProd()

    // ================================================================================================
    // Function : save_order_backend()
    // Date : 03.04.2006
    // Parms : $id_order
    // Returns : true,false / Void
    // Description : Store status of the order
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function save_order_backend()
    {
        $sum_all=0;
        $qnt_all=0;
        if($this->id_order){

            $q = "UPDATE `".TblModOrderComments."` SET
               `name`='".$this->name."',
               `phone`='".$this->phone."',
               `phone_mob`='".$this->phone_mob."',
               `email`='".$this->email."',
               `city`='".$this->city."',
               `addr`='".$this->addr."',
               `firm`='".$this->firm."',
               `comment`='".$this->comment."',
               `discount`='".$this->discount."',
               `pay_method`='".$this->pay_method."',
               `delivery_method`='".$this->delivery_method."',
               `date`='".$this->date."',
               `status`='".$this->status."',
               `ttn`='".$this->ttn."',
               `free_delivery`='".$this->free_delivery."',
               `no_commission`='".$this->no_commission."'
               WHERE `id_order`='".$this->id_order."'
            ";
            $res = $this->Right->Query($q);
            //echo '<br />$q='.$q.' $res='.$res;
            if(!$res OR ! $this->Right->result) return false;

            $q = "SELECT * FROM `".TblModOrder."` WHERE `id_order`='".$this->id_order."'";
            $res = $this->db->db_Query($q);
            //echo '<br />$q='.$q.' $res='.$res;
            if(!$res OR ! $this->db->result) return false;
            $rows = $this->db->db_GetNumRows();

            for($i=0;$i<$rows;$i++){
                $arr[] = $this->db->db_FetchAssoc();
            }

            for($i=0;$i<$rows;$i++){
                $row = $arr[$i];
                //var_dump($this->price[$row['id']], $row['price']);
                //var_dump($this->quantity[$row['id']], $row['quantity']);
                //var_dump($row, $this->price[$row['id']]);

                if( (isset($this->quantity[$row['id']]) AND $row['quantity']!=$this->quantity[$row['id']]) ||
                    (isset($this->price[$row['id']]) AND $row['price']!=$this->price[$row['id']]) ){

                    $sum = $this->quantity[$row['id']] * $this->price[$row['id']];
                    $sum_all += $sum;
                    $qnt_all += $this->quantity[$row['id']];
                    $q = "UPDATE `".TblModOrder."` SET
                          `quantity`='".$this->quantity[$row['id']]."',
                          `price`='".$this->price[$row['id']]."',
                          `currency`='".$this->Currency->defCurrencyData['id']."',
                          `sum`='".$sum."'
                          WHERE `id`='".$row['id']."' AND `id_order`='".$this->id_order."'
                         ";
                    $res = $this->Right->Query($q, $this->user_id, $this->module);
                    //echo '<br />$q='.$q.' $res='.$res.' $this->Right->result='.$this->Right->result;
                    if(!$res OR ! $this->Right->result) return false;

                }
                else{
                    $qnt_all +=$row['quantity'];
                    $sum_all +=$row['sum'];
                }
            }
            if($this->old_status!=$this->status){
               // $res = $this->SendNotificationToUserEmail($this->id_order, $this->status);
            }
        }
        else{
            //перевіряємо чи покупець зареєстрований на сайті
            $User = check_init('UserShow', 'UserShow');

            $User->phone_mob = $this->phone_mob;
            $res = $User->CheckFields();
            //if no such user - regiter him
            if (empty($res)) {
                $User->email = $this->email;
                $User->login = $this->phone_mob;
                $User->name = $this->name;

                $User->user_id = $User->SaveToSysUser();
                if ($User->user_id) {
                    $User->user_status = 3; //зарегистрирован
                    $res = $User->SaveUser();
                }
            }
            $user_data = $User->GetUserDataByUserLogin($this->phone_mob);
            //var_dump($user_data);exit;

            $id_order = $this->GetNewOrderId();
            $q = "INSERT INTO `".TblModOrderComments."` SET
               `id_order` = '".$id_order."',
               `buyer_id` = '".$user_data['id']."',
               `name`='".$this->name."',
               `phone`='".$this->phone."',
               `phone_mob`='".$this->phone_mob."',
               `email`='".$this->email."',
               `city`='".$this->city."',
               `addr`='".$this->addr."',
               `firm`='".$this->firm."',
               `comment`='".$this->comment."',
               `discount`='".$this->discount."',
               `pay_method`='".$this->pay_method."',
               `delivery_method`='".$this->delivery_method."',
               `date`='".$this->date."',
               `status`='".$this->status."',
               `isread`='1',
               `currency`='".$this->Currency->defCurrencyData['id']."',
               `ttn`='".$this->ttn."',
               `free_delivery`='".$this->free_delivery."',
               `no_commission`='".$this->no_commission."'

               ";
            $res = $this->Right->Query($q, $this->user_id, $this->module);
            //echo '<br />$q='.$q.' $res='.$res;
            if(!$res OR !$this->Right->result) return false;

            $q = "SELECT * FROM `".TblModOrder."` WHERE `id_order`=''";
            $res = $this->db->db_Query($q);
            //echo '<br />$q='.$q.' $res='.$res;
            if(!$res OR ! $this->db->result) return false;
            $rows = $this->db->db_GetNumRows();
            for($i=0;$i<$rows;$i++){
                $arr_new_order_data[$i] = $this->db->db_FetchAssoc();
            }
            for($i=0;$i<$rows;$i++){
                $row =$arr_new_order_data[$i];
                //echo '<br />$row[prod_id]='.$row['prod_id'];
                if( isset($this->quantity[$row['id']]) || isset($this->price[$row['id']]) ){
                    if ( !$this->id_order OR ($row['quantity']!=$this->quantity[$row['id']] AND !empty($this->id_order))
                                          OR ($row['price']!=$this->price[$row['id']] AND !empty($this->id_order)) ) {
                        $sum = $this->quantity[$row['id']] * $this->price[$row['id']];
                        $sum_all += $sum;
                        $qnt_all += $this->quantity[$row['id']];
                        $q = "UPDATE `" . TblModOrder . "` SET
                          `id_order` = '" . $id_order . "',
                          `quantity`='" . $this->quantity[$row['id']] . "',
                          `price`='" . $this->price[$row['id']] . "',
                          `sum`='" . $sum . "'
                          WHERE `id`='" . $row['id'] . "' AND `id_order`=''
                         ";
                        $res = $this->Right->Query($q, $this->user_id, $this->module);
                        //echo '<br />2$q='.$q.' $res='.$res.' $this->Right->result='.$this->Right->result;
                        if (!$res OR !$this->Right->result) return false;
                    }
                }//end if
            }//end for
            $this->id_order = $id_order;
        }//end if
        $res = $this->ReCalculateOrder($qnt_all, $sum_all);
        if(!$res) return false;
        return true;
    }//end of function save_order_backend()

    // ================================================================================================
    // Function : ReCalculateOrder()
    // Date : 12.05.2010
    // Parms :  $qnt_all
    //          $sum_all
    // Returns : true,false / Void
    // Description : recalculate order
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function ReCalculateOrder($qnt_all=0, $sum_all=0)
    {
        $q = "SELECT * FROM `".TblModOrderComments."` WHERE `id_order`='".$this->id_order."'";
        $res = $this->db->db_Query($q);
        //echo '<br />$q='.$q.' $res='.$res;
        if(!$res OR ! $this->db->result) return false;
        $row = $this->db->db_FetchAssoc();
        if($row['sum']!=$sum_all OR $row['qnt_all']!=$qnt_all){
            $q = "UPDATE `".TblModOrderComments."` SET
                  `qnt_all`='".$qnt_all."',
                  `sum`='".$sum_all."'
                  WHERE `id_order`='".$this->id_order."'
                 ";
            $res = $this->Right->Query($q, $this->user_id, $this->module);
            //echo '<br />$q='.$q.' $res='.$res.' $this->Right->result='.$this->Right->result;
            if(!$res OR ! $this->Right->result) return false;

        }
        return true;
    }//end of function ReCalculateOrder()

    // ================================================================================================
    // Function : del()
    // Date : 06.01.2006
    // Returns :      true,false / Void
    // Description :  Remove data from the table
    // Programmer :  Igor Trokhymchuk
    // ================================================================================================
    function del( $id_del )
    {
        $kol = count( $id_del );
        $del = 0;
        for( $i=0; $i<$kol; $i++ ){
            $u = $id_del[$i];
            $q = "DELETE
                  FROM `".TblModOrderComments."`, `".TblModOrder."`
                  USING  `".TblModOrderComments."` INNER JOIN `".TblModOrder."`
                  WHERE `".TblModOrderComments."`.id_order='".$u."'
                  AND `".TblModOrderComments."`.id_order=`".TblModOrder."`.id_order
                 ";
            $res = $this->Right->Query( $q, $this->user_id, $this->module );
            //echo '<br>$q='.$q.' $res='.$res;
            if ( $res )
                $del=$del+1;
            else
                return false;
        }
        return $del;
    } //end of fuinction del()

    // ================================================================================================
    // Function : DelProdItemFromOrder()
    // Date : 05.05.2010
    // Returns :      true,false / Void
    // Description :  Remove data from the table
    // Programmer :  Igor Trokhymchuk
    // ================================================================================================
    function DelProdItemFromOrder()
    {
        $q = "DELETE FROM `".TblModOrder."` WHERE `".TblModOrder."`.`id_order`='".$this->id_order."' AND `".TblModOrder."`.`id`='".$this->id."'";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        if( !$res) return false;

        $q = "SELECT * FROM `".TblModOrder."` WHERE `id_order`='".$this->id_order."'";
        $res = $this->db->db_Query($q);
        //echo '<br />$q='.$q.' $res='.$res;
        if(!$res OR ! $this->db->result) return false;
        $rows = $this->db->db_GetNumRows();
        $qnt_all = 0;
        $sum_all = 0;
        for($i=0;$i<$rows;$i++){
            $row = $this->db->db_FetchAssoc();
            $qnt_all += $row['quantity'];
            $sum_all += $row['sum'];
        }
        $this->ReCalculateOrder($qnt_all, $sum_all);
        return true;
    } //end of fuinction DelProdItemFromOrder()

    // ================================================================================================
    // Function : AddProdItemForm()
    // Date : 13.05.2010
    // Parms :
    // Description :  show form for add product to order
    // Programmer :  Igor Trokhymchuk
    // ================================================================================================
    function AddProdItemForm()
    {
        ?>
        <form action="" method="post" name="add_prod_item_form" id="add_prod_item_form">
        <input type="hidden" name="id_order" value="<?=$this->id_order;?>" />
        <div style="float:left;">
        <?
        $params = "style='width:300px;' onchange='LoadProdData(\"prod_dtl\", \"/modules/mod_order/order.backend.php?module=".$this->module."&task=load_prod_data\", \"add_prod_item_form\"); return false';";

        $arr_categs = $this->Catalog->PrepareCatalogForSelect(0, NULL, NULL, 'back', true, true, false, false, NULL, NULL);
        $arr_props = $this->Catalog->PreparePositionsTreeForSelect('all', 'back', 'move', 'asc', NULL);
        echo $this->multi['_TXT_PRODUCT'];
        $this->Catalog->ShowCatalogInSelect($arr_categs, $arr_props, '--- '.$this->multi['TXT_SELECT_PRODUCT'].' ---', 'add_prod_item', '', $params);
        //$this->Catalog->ShowCatalogInSelect(NULL, '--- '.$this->multi['TXT_SELECT_PRODUCT'].' ---', NULL, NULL, true, 'back', true, false, false, 'add_prod_item', NULL, NULL, NULL, $params);
        ?>
        </div>
        <div id="prod_dtl"></div>
        </form>
        <?
        return true;
    } //end of fuinction AddProdItemForm()

    // ================================================================================================
    // Function : LoadProdData()
    // Date : 13.05.2010
    // Returns :      true,false / Void
    // Description :  load product data to order
    // Programmer :  Igor Trokhymchuk
    // ================================================================================================
    function LoadProdData()
    {
        if($this->id_order){
            $q0 = "SELECT `".TblModOrderComments."`.`currency`
                FROM `".TblModOrderComments."`
                WHERE `".TblModOrderComments."`.`id_order`='".$this->id_order."'
                ";
            $res0 = $this->db->db_Query($q0);
            //echo '<br />$q0='.$q0.' $res0='.$res0.' $this->db->result='.$this->db->result;
            if( !$res0 OR !$this->db->result) return false;
            $row0 = $this->db->db_FetchAssoc();
            //print_r($row0);
            $order_curr = $this->Currency->GetCurrencyData($row0['currency']);
            //var_dump($order_curr);
        }
        else $order_curr = '';

        $q = "SELECT `".TblModPropGroupPrice."`.`price`, `".TblModCatalogProp."`.`price_currency`
              FROM `".TblModCatalogProp."`, `".TblModPropGroupPrice."`
              WHERE `".TblModCatalogProp."`.`id`='".$this->add_prod_item."'
              AND `".TblModPropGroupPrice."`.`prod_id`='".$this->add_prod_item."'
              AND `".TblModPropGroupPrice."`.`group_id`=`".TblModCatalogProp."`.`price_currency`
             ";
//        echo '<br/>'.$q;
        $res = $this->db->db_Query($q);
        //echo '<br />$q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;
        if( !$res OR !$this->db->result) return false;
        $row_prod = $this->db->db_FetchAssoc();
        //echo '<br>$row_prod[price]='.$row_prod['price'].' $row_prod[price_currency]='.$row_prod['price_currency'].' $row0[currency]='.$row0['currency'];

        ?>
        &nbsp;<?=$this->multi['FLD_PRICE'];
        if($this->id_order){
            $price = $this->Currency->Converting($row_prod['price_currency'], $row0['currency'], $row_prod['price']);
            ?>&nbsp;<?=stripslashes($order_curr['pref']);?><input type="text" size="5" name="add_prod_item_price" value="<?=$price;?>" />&nbsp;<?=stripslashes($order_curr['suf']);?>
            <input type="hidden" name="add_prod_item_currency" value="<?=$row0['currency'];?>" />
            <?
        }
        else{
            $price = $row_prod['price'];
            ?>&nbsp;<input type="text" size="5" name="add_prod_item_price" value="<?=$price;?>" /><?
            if(isset($this->Catalog->settings['price_currency']) AND $this->Catalog->settings['price_currency']=='1' ){
                ?>&nbsp;<?=$this->Form->Select($this->Currency->listShortNames, 'add_prod_item_currency', $row_prod['price_currency']);
            }
        }
        ?>&nbsp;<?=$this->multi['FLD_QUANTITY'];?>&nbsp;<input type="text" size="5" name="add_prod_item_cnt" value="<?=$this->add_prod_item_cnt;?>" />

        <input type="button" value="<?=$this->multi['TXT_SAVE'];?>" onclick="AddProdItem('tableprod', '/modules/mod_order/order.backend.php?module=<?=$this->module;?>&task=add_prod_item', 'add_prod_item_form');return false;" />
        <?
    } //end of function LoadProdData()


    // ================================================================================================
    // Function : AddProdItemToOrder()
    // Date : 05.05.2010
    // Returns :      true,false / Void
    // Description :  Remove data from the table
    // Programmer :  Igor Trokhymchuk
    // ================================================================================================
    function AddProdItemToOrder()
    {
        if( (!empty($this->add_prod_item) AND $this->add_prod_item_cnt>0) || isset($this->arr_add_prod_items)) {

            $q = "SELECT * FROM `" . TblModOrder . "` WHERE `id_order`='" . $this->id_order . "'";
            $res = $this->db->db_Query($q);
            //echo '<br />$q='.$q.' $res='.$res;
            if (!$res OR !$this->db->result) return false;
            $rows = $this->db->db_GetNumRows();
            $qnt_all = 0;
            $sum_all = 0;
            for ($i = 0; $i < $rows; $i++) {
                $row = $this->db->db_FetchAssoc();
                $arr[$row['prod_id']] = $row;
                $qnt_all += $row['quantity'];
                $sum_all += $row['sum'];
            }
            $sum = $this->add_prod_item_price * $this->add_prod_item_cnt;


            if ( !empty($this->add_prod_item) ){
                //if( !isset($arr[$this->add_prod_item]) ){
                $q = "INSERT INTO `" . TblModOrder . "` SET
                          `id_order`='" . $this->id_order . "',
                          `quantity`='" . $this->add_prod_item_cnt . "',
                          `price`='" . $this->add_prod_item_price . "',
                          `sum`='" . $sum . "',
                          `currency`='" . $this->add_prod_item_currency . "',
                          `prod_id` = '" . $this->add_prod_item . "'
                ";
                $res = $this->Right->Query($q, $this->user_id, $this->module);
                //echo '<br />$q='.$q.' $res='.$res.' $this->Right->result='.$this->Right->result;
                if (!$res OR !$this->Right->result) return false;
            }
            else if ( isset($this->arr_add_prod_items) ){
                //var_dump($this->arr_add_prod_items);
                //`".TblModOrderComments."`.`custom_price_group`
                    $q = "
                        SELECT
                            `".TblSysUser."`.`group_id`
                        FROM `".TblModOrderComments."` LEFT JOIN `".TblSysUser."` ON `".TblModOrderComments."`.`buyer_id`=`".TblSysUser."`.`id`
                        WHERE `".TblModOrderComments."`.`id_order`='" .$this->id_order. "'
                    ";
                    //echo $q;
                    $res = $this->db->db_Query($q);
                    //var_dump($res, $this->db->db_GetNumRows());
                    if($res) {
                        if ($this->db->db_GetNumRows() > 0) {
                            $row = $this->db->db_FetchAssoc();
                            /*if ( !empty($row['custom_price_group']) )
                                $id_group = $row['custom_price_group'];
                            else*/
                                $id_group = $row['group_id'];
                        }else $id_group = 5; //це може бути замовлення створене вручну...
                    }
                    else return false;

                    foreach ( $this->arr_add_prod_items as $id_prod ){
                        $q = "
                          SELECT `price`, `group_currency`
                          FROM  `".TblModPropGroupPrice."`
                            WHERE `group_id`='".$id_group."' AND `prod_id` = '". $id_prod."'
                        ";
                        //echo $q.'<br/>';
                        $res = $this->db->db_Query($q);
                        //var_dump($res);
                        if($res){
                            if ( $this->db->db_GetNumRows() > 0 ){
                                $prop = $this->db->db_FetchAssoc();
                                //var_dump($prop);
                                $price = $this->Currency->Converting($prop['group_currency'], 5, $prop['price'], 2);
                                //var_dump($price, $prop['group_currency'],'..');

                                //var_dump($prop);
                                $q = "INSERT INTO `".TblModOrder."` SET
                                `id_order`='".$this->id_order."',
                                `quantity`='1',
                                `price`='".$price."',
                                `sum`='".$price."',
                                `currency`='5',
                                `prod_id` = '".$id_prod."'
                                ";
                                $res = $this->Right->Query($q, $this->user_id, $this->module);
                                //echo '<br />$q='.$q.' $res='.$res.' $this->Right->result='.$this->Right->result;
                                if(!$res OR ! $this->Right->result) continue;
                                $qnt_all++;
                                $sum_all += $price;
                            }
                            else continue;
                        }
                        else return false;
                    }
                }

                $mas = $this->GetOrderCommentInArr($this->id_order);
                $qnt_all += $this->add_prod_item_cnt;
                $sum_all += $sum;
                $res = $this->ReCalculateOrder($qnt_all, $sum_all);
                if(!$res) return false;
                return true;
            //}
        }
    } //end of fuinction AddProdItemToOrder()

    // ================================================================================================
    // Function : PrintOrderBackEnd()
    // Date : 28.01.2011
    // Returns :      true,false / Void
    // Description :  Show Order for Print
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function PrintOrderBackEnd( $waybillType=NULL )
    {
        if(!$this->user_id){
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

        $OrderLayout = new OrderLayout($this->user_id);
        $OrderLayout->id_order=$this->id_order;

        $o_comm = $this->GetOrderCommentInArr($this->id_order);
        if ( !empty($waybillType) )
            $OrderLayout->PrintWaybill($waybillType);
        else {
            if ($o_comm['pay_method'] == 2)
                $OrderLayout->PrintInvoice($o_comm);
            else
                $OrderLayout->PrintOrderBlank($o_comm);
        }
    } // end of PrintOrderBackEnd

   // ================================================================================================
   // Function : ShowErr()
   // Date : 10.01.2006
   // Returns :      true,false / Void
   // Description :  Show errors
   // Programmer :  Igor Trokhymchuk
   // ================================================================================================
   function showErr()
   {
     if ($this->Err){
       echo '
        <fieldset class="err" title="'.$this->Msg->show_text('MSG_ERRORS').'"> <legend>'.$this->Msg->show_text('MSG_ERRORS').'</legend>
        <div class="err_text">'.$this->Err.'</div>
        </fieldset>';
     }
   } //end of function ShowErr()

    function SetCustomPriceGroup( $id_order, $price_group ){
        if ( empty($id_order) || empty($price_group) ) return false;

        $sum_all = 0;
        $qnt_all = 0;

        $q = "UPDATE `".TblModOrderComments."` SET `custom_price_group`='".$price_group."'";
        $res = $this->db->db_Query($q);
        if (!$res) return '0';

        //get prod from order
        $q = "SELECT `prod_id`, `quantity` FROM `".TblModOrder."` WHERE id_order='".$id_order."' ";
        //echo '<br/>'.$q;
        $res = $this->db->db_Query($q);
        if (!$res) return false;

        while ( $row = $this->db->db_FetchAssoc() ){
            $prods[] = $row;
        }

        //var_dump($prods);

        foreach ( $prods as $key => $prod ){
            //get group price and currency for each prod
            $q = "
              SELECT `price`, `group_currency`
              FROM  `".TblModPropGroupPrice."`
                WHERE `group_id`='".$price_group."' AND `prod_id` = '". $prod['prod_id']."'
            ";
            //echo $q.'<br/>';
            $res = $this->db->db_Query($q);
            if($res){
                if ( $row = $this->db->db_FetchAssoc() ){
                    //var_dump($row);

                    // if user group
                    if ($price_group == 5) $floatvar = 0;
                    else $floatvar = 2;

                    $price = $this->Currency->Converting($row['group_currency'], 5, $row['price'], $floatvar);
                    $sum = $price * $prod['quantity'];
                    //var_dump($prod['prod_id'], $row['price'], $price,$prod['quantity'], $sum);
                    //var_dump($prod);


                    //update price & sum
                    $q = "UPDATE `".TblModOrder."`
                        SET
                            `price`='".$price."',
                            `sum`='".$sum."',
                            `currency`='5'
                        WHERE
                            `id_order`='".$id_order."' AND `prod_id`='".$prod['prod_id']."'";
                    $res = $this->Right->Query($q, $this->user_id, $this->module);
                    //echo '<br />$q='.$q.' $res='.$res.' $this->Right->result='.$this->Right->result;
                    if (!$res OR !$this->Right->result) continue;


                    $sum_all += $sum;
                    $qnt_all += $prod['quantity'];
                }
                else continue;
            }
            else return false;
        }

        //var_dump('qnt/sum',$qnt_all, $sum_all);


        $q = "UPDATE `".TblModOrderComments."` SET
                  `qnt_all`='".$qnt_all."',
                  `sum`='".$sum_all."'
                  WHERE `id_order`='".$id_order."'
                 ";
        $res = $this->db->db_Query($q);


        return '1';
    }

    function ChangeField($id_order, $id, $field, $new_val){
        if ( empty($id_order) || empty($id) || empty($field) || empty($new_val)) return false;
        $q = "UPDATE `".TblModOrder."` SET `".$field."`='".$new_val."' WHERE `id_order`='".$id_order."' AND `id`='".$id."'";
        //echo $q;

        $res = $this->db->db_Query($q);
        return $res;

    }

    function OrderTotalSum($id_order, $user_group){
        $q = "SELECT `price`, `currency`, `quantity` FROM `".TblModOrder."` WHERE `id_order`='".$id_order."'";
        //echo $q;
        $res = $this->db->db_Query( $q );
        $rows = $this->db->db_GetNumRows($res);
        if($rows>0) {
            $total_sum = 0;
            $total_qnt = 0;

            for ($i = 0; $i < $rows; $i++) {
                $row = $this->db->db_FetchAssoc();
                $price = $this->Currency->Converting($row['currency'], 5, $row['price'], 2);
                $total_sum += $price * $row['quantity'];
                $total_qnt += $row['quantity'];
            }
            //save changes
            //$res = $this->ReCalculateOrder($total_qnt, $total_sum);

            //show total sum
            return $this->Currency->ShowPrice($total_sum);

        }
    }
//----------------------------------------------------------------------------------------------------------------------

    function GetOrderPricesByGroup($id_order, $price_group){
        if ( empty($id_order) || empty($price_group) ) return false;

        //get prod from order
        $q = "SELECT `prod_id` FROM `".TblModOrder."` WHERE id_order='".$id_order."' ";
        //echo '<br/>'.$q;
        $res = $this->db->db_Query($q);
        if (!$res) return false;

        while ( $row = $this->db->db_FetchAssoc() ){
            $prods[] = $row;
        }

        //var_dump($prods);

        foreach ( $prods as $key => $prod ){
            //get group price and currency for each prod
            $q = "
              SELECT `price`, `group_currency`
              FROM  `".TblModPropGroupPrice."`
                WHERE `group_id`='".$price_group."' AND `prod_id` = '". $prod['prod_id']."'
            ";
            //echo $q.'<br/>';
            $res = $this->db->db_Query($q);
            if($res){
                if ( $row = $this->db->db_FetchAssoc() ){
                    //var_dump($row);

                    // if user group
                    if ($price_group == 5) $floatvar = 0;
                    else $floatvar = 2;

                    $price = $this->Currency->Converting($row['group_currency'], 5, $row['price'], $floatvar);
                    $arr_prices[ $prod['prod_id'] ] = $price;

                }
                else continue;
            }
            else return false;
        }
        return $arr_prices;
    }
//----------------------------------------------------------------------------------------------------------------------


    public static function showTtnSmsStatusBadge( $type, $id_order ){
        switch( $type ){
            case '':
            case '0':
                $badgeClass = 'not-sended-badge';
                $title = 'не отправлено';
                break;
            case '1':
                $badgeClass = 'sended-badge';
                $title = 'отправлено';
                break;
            case '2':
                $badgeClass = 'delivered-badge';
                $title = 'доставленно';
                break;
            default:
                $badgeClass = 'other-badge';
                $title = $type;
        }
        ?>

        <div id="ttnSmsStatus_<?=$id_order?>" class="ttn-sms-status">
            <div class="<?=$badgeClass?> badge" title="<?=$title?>">TTH</div>
        </div>
        <?
    }

//--- end getTtnSmsStatusBadge -----------------------------------------------------------------------------------------


} // end of class
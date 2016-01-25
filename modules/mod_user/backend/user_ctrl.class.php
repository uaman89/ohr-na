<?php
// ================================================================================================
// System : SEOCMS
// Module : user_ctrl.class.php
// Version : 1.0.0
// Date : 06.01.2006
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : Class definition for all actions with managment on the back-end of External users
//
// ================================================================================================

include_once( SITE_PATH.'/modules/mod_user/user.defines.php' );

// ================================================================================================
//    Class             : UserCtrl
//    Version           : 1.0.0
//    Date              : 06.01.2006
//
//    Constructor       : Yes
//    Parms             : session_id / session id
//                        usre_id    / UserID
//                        user_      /
//                        user_type  / id of group of user
//    Returns           : None
//    Description       : Class definition for all actions with managment  on the back-end  of External users
// ================================================================================================
//    Programmer        :  Igor Trokhymchuk
//    Date              :  06.01.2006
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================
 class UserCtrl extends User {

       var $user_id = NULL;
       var $module = NULL;
       var $Err=NULL;
       var $lang_id = NULL;

       var $sort = NULL;
       var $display = 20;
       var $start = 0;
       var $fln = NULL;
       var $width = 500;
       var $spr = NULL;
       var $srch = NULL;
       var $script = NULL;
       var $srch_dtfrom = NULL;
       var $srch_dtto = NULL;
       var $group_id = NULL;
       var $status = NULL;

       var $db = NULL;
       var $Msg = NULL;
       var $Right = NULL;
       var $Form = NULL;
       var $Spr = NULL;

       // ================================================================================================
       //    Function          : UserCtrl (Constructor)
       //    Version           : 1.0.0
       //    Date              : 06.01.2006
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
       function UserCtrl($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL) {
                //Check if Constants are overrulled
                ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
                ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
                ( $display  !="" ? $this->display = $display  : $this->display = 20   );
                ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
                ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
                ( $width    !="" ? $this->width   = $width    : $this->width   = 750  );

                if( defined("_LANG_ID") ) $this->lang_id = _LANG_ID;

                if( defined("DATING_DBHOST")) $this->db_host = DATING_DBHOST;
                if( defined("DATING_DBNAME")) $this->db_name = DATING_DBNAME;
                if( defined("DATING_DBUSER")) $this->db_user = DATING_DBUSER;
                if( defined("DATING_DBPASS")) $this->db_pass = DATING_DBPASS;
                if( defined("DATING_DBOPEN")) $this->db_open = DATING_DBOPEN;

                if (empty($this->db)) $this->db = DBs::getInstance();
                if (empty($this->db2)) $this->db2= new DB();
                if (empty($this->Right)) $this->Right = check_init('RightsUser', 'Rights', "'".$this->user_id."', '".$this->module."'");
                if (empty($this->Msg)) $this->Msg = check_init('ShowMsg', 'ShowMsg');
                if (empty($this->multi)) $this->multi = check_init_txt('TblBackMulti',TblBackMulti);
                if (empty($this->Form)) $this->Form = check_init('FormCatalog', 'Form', "'form_mod_catalog'");
                if (empty($this->Spr)) $this->Spr = check_init('SysSpr', 'SysSpr', "'$this->user_id', '$this->module'");
       } // End of UserCtrl Constructor

       // ================================================================================================
       // Function : show
       // Version : 1.0.0
       // Date : 06.01.2006
       //
       // Parms :
       // Returns : true,false / Void
       // Description : Show data from $module table
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 06.01.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function show()
       {

        //$script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr.'&fln='.$this->fln;
        //$script = $_SERVER['PHP_SELF']."?$script";
        $logon = check_init('logon','Authorization');
        if( !$this->sort ) $this->sort='id';
        $q = "SELECT `".TblModUser."`.*,
              `".TblSysUser."`.*
              FROM `".TblModUser."`,
                   `".TblSysUser."`,
                   `".TblSysGroupUsers."`
              WHERE 1 ";
        if( $this->srch ) $q = $q." AND (`email` LIKE '%".$this->srch."%' OR `".TblModUser."`.`name` LIKE '%".$this->srch."%' OR (`".TblSysUser."`.login LIKE '%".$this->srch."%') )";
        if( $this->fltr ) $q = $q." AND `".TblSysUser."`.group_id='".$this->fltr."'";
        if( $this->fltr2 ) $q = $q." AND `user_status`='".$this->fltr2."'";
        if( $this->srch_dtfrom ) $q = $q." AND `".TblSysUser."`.enrol_date>='".$this->srch_dtfrom."'";
        if( $this->srch_dtto ) $q = $q." AND `".TblSysUser."`.enrol_date<='".$this->srch_dtto."'";
        if( $this->srch_country ) $q = $q." AND `country`='".$this->srch_country."'";
        if( $this->srch_alias ) $q = $q." AND `".TblSysUser."`.alias LIKE '%".$this->srch_alias."%'";
        $q .= " AND `".TblModUser."`.sys_user_id=`".TblSysUser."`.id
                AND `".TblSysUser."`.group_id = `".TblSysGroupUsers."`.`id`
                AND `".TblSysGroupUsers."`.`adm_menu`='0'
              ORDER BY `".TblModUser."`.`$this->sort` desc";

        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        //echo '<br>$q='.$q.' $res='.$res.'$this->Right->result='.$this->Right->result.' $this->user_id='.$this->user_id;

        if( !$res )return false;

        $rows = $this->Right->db_GetNumRows();

        $a = $rows;
        $j = 0;
        $row_arr = NULL;
        for( $i = 0; $i < $rows; $i++ )
        {
          $row = $this->Right->db_FetchAssoc();
          //--------------- check by search criteria START -----------------
          //if(!empty())
          //--------------- check by search criteria END  ------------------

          if( $i >= $this->start && $i < ( $this->start+$this->display ) )
          {
            $row_arr[$j] = $row;
            $j = $j + 1;
          }
        }

        // echo '<br> this->srch ='.$this->srch.' $script='.$script;
        $this->ShowSearchForm();

        /* Write Form Header */
        $this->Form->WriteHeader( $this->script );

        /* Write Table Part */
        AdminHTML::TablePartH();
        /* Write Links on Pages */
        ?>
        <tr>
         <td colspan="17">
          <?
          //$script1 = 'module='.$this->module.'&fltr='.$this->fltr;
          //$script1 = $_SERVER['PHP_SELF']."?$script1";
          $this->Form->WriteLinkPages( $this->script, $rows, $this->display, $this->start, $this->sort );
          ?>
         </td>
        </tr>
        <tr>
         <td colspan="10">
          <?
          if($this->Right->IsWrite()) $this->Form->WriteTopPanel( $this->script.'&id=', 1 );
          if($this->Right->IsDelete()) $this->Form->WriteTopPanel( $this->script.'&id=', 2 );
          ?>
         </td>
        </tr>
        <?

        /*
        echo '<td colspan=7>';
        echo $this->Form->TextBox('srch', $this->srch, 25);
        echo '<input type=submit value='.$this->Msg->show_text('_BUTTON_SEARCH',TblSysTxt).'>';
        */

        //$script2 = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&task=show&fltr='.$this->fltr;
        //$script2 = $_SERVER['PHP_SELF']."?$script2";
       ?>
        <tr>
        <td class="THead">*</td>
        <td class="THead"><A HREF=<?=$this->script?>&sort=id class="aTHead"><?=$this->Msg->show_text('FLD_ID')?></A></td>
        <td class="THead"><A HREF=<?=$this->script?>&sort=email class="aTHead"><?=$this->Msg->show_text('FLD_LOGIN')?></A></td>
        <td class="THead"><A HREF=<?=$this->script?>&sort=name class="aTHead"><?=$this->Msg->show_text('FLD_NAME')?></A></td>
        <td class="THead"><A HREF=<?=$this->script?>&sort=country class="aTHead"><?=$this->Msg->show_text('TXT_REG_STEP1')?></A></td>
        <?/*<td class="THead"><A HREF=<?=$this->script?>&sort=country><?=$this->Msg->show_text('FLD_COUNTRY')?></A></td>
        <td class="THead"><A HREF=<?=$this->script?>&sort=adr><?=$this->Msg->show_text('FLD_ADR')?></A></td>
        <td class="THead"><A HREF=<?=$this->script?>&sort=phone><?=$this->Msg->show_text('FLD_PHONE')?></A></td>
        <td class="THead"><A HREF=<?=$this->script?>&sort=phone_mob><?=$this->Msg->show_text('FLD_PHONE_MOB')?></A></td>
        <?/*<td class="THead"><A HREF=<?=$this->script?>&sort=fax><?=$this->Msg->show_text('FLD_FAX')?></A></td>*/?>
        <td class="THead"><A HREF=<?=$this->script?>&sort=bonuses class="aTHead"><?=$this->Msg->show_text('FLD_BONUSES')?></A></td>
        <td class="THead"><A HREF=<?=$this->script?>&sort=discount class="aTHead"><?=$this->Msg->show_text('FLD_DISCOUNT')?></A></td>
        <td class="THead"><A HREF=<?=$this->script?>&sort=user_status class="aTHead"><?=$this->Msg->show_text('FLD_STATUS')?></A></td>
        <td class="THead"><?=$this->Msg->show_text('_FLD_ALIAS')?></td>
        <?
        if(defined("MOD_ORDER")){
            ?><td class="THead">Заказы пользователя</td><?
        }
        ?>
        <td class="THead"><?=$this->Msg->show_text('_FLD_GROUP')?></td>
        <td class="THead"><?=$this->Msg->show_text('FLD_ACTIVE')?></td>
        <td class="THead"><?=$this->Msg->show_text('_FLD_ENROL_DATE')?></td>
        <td class="THead"><?=$this->Msg->show_text('_FLD_LAST_ACT_COUNTER')?></td>
        <td class="THead"><?=$this->Msg->show_text('_FLD_USED_COUNTER')?></td>
        <td class="THead"><?=$this->Msg->show_text('_FLD_IP_USER');?></td>
        <?

        $style1 = 'TR1';
        $style2 = 'TR2';

        for( $i = 0; $i < count( $row_arr ); $i++ )
        {
          $row = $row_arr[$i];




          if ( (float)$i/2 == round( $i/2 ) ) $class="TR1";
          else $class="TR2";
          ?><tr class="<?=$class;?>"><?

          ?><td><?$this->Form->CheckBox( "id_del[]", $row['sys_user_id'] );?></td><?
          ?><td><?$this->Form->Link( $this->script."&task=edit&id=".$row['sys_user_id'], $row['sys_user_id'] , $this->Msg->show_text('_TXT_EDIT_DATA') );?></td><?

          $ModulesPlug = new ModulesPlug();
          $id_mod_sys_user = $ModulesPlug->GetModuleIdByPath( 'modules/sys_user/sys_user.php' );
          $id_mod_stat = $ModulesPlug->GetModuleIdByPath( 'modules/sys_stat/stat.php' );
          $id_modBlog=$ModulesPlug->GetModuleIdByPath( 'mod_user/userBlog.backend.php' );
          ?>
          <td align="left" style="padding:3px;"><span style="font-size: 11px; margin:3px;"><?=stripslashes($row['login']);?></span>
            <hr/><a href="<?=$_SERVER['PHP_SELF']?>?module=<?=$id_mod_sys_user;?>&amp;task=show_stat&amp;fltr_user=<?=$row['sys_user_id'];?>" target="_blank"><?=$this->Msg->show_text('FLD_IP_STATISTIC');?></a>
            <?/*
            <hr/>
            <a href="<?=$_SERVER['PHP_SELF'].'?module='.$id_mod_stat.'&amp;fltr_dtfrom=2000-01-01&amp;fltr_dtto=2100-01-01&amp;fltr_user='.$row['sys_user_id'];?>" target="_blank"><?=$this->Msg->Show_text('TXT_DETAIL_STATISTIC');?></a>
             *
             */
            ?>
            <hr/>
            <a href="<?=$_SERVER['PHP_SELF']?>?module=<?=$id_modBlog?>&task=showblogs&sys_user_id=<?=$row['sys_user_id'];?>">Блог</a>

          </td>
          <?

          ?><td><?=$row['name'];?></td><?

          ?><td align="center"><?
          if( !empty( $row['country'] ) ){
              echo stripslashes($row['country']).'<br/>';
              //echo $this->Spr->GetNameByCod(TblSysSprCountry, $row['country']);
          }
          if( trim( $row['city'] )!='' ) {
               echo $this->Msg->show_text('FLD_CITY').': '.stripslashes($row['city']);
          }

          ?><br/><span style="font-weight:normal;"><?=stripslashes($row['adr']);
          ?><br/><?=stripslashes($row['phone']);
          ?><br/><?=stripslashes($row['phone_mob']);?></span><?
          /*?><td><?=stripslashes($row['fax']);?></td><?*/
          ?><td><?=stripslashes($row['bonuses']);?></td><?
          ?><td><?=stripslashes($row['discount']);?></td><?
          ?><td><?=$this->Spr->GetNameByCod(TblModUserSprStatus, $row['user_status']);?></td><?
          ?><td style="padding:3px;"><?=$row['email'];?></td><?
          if(defined("MOD_ORDER")){
              ?>
              <td>
                  <?
                  $Order = check_init('Order', 'Order');
                  $usr_orders = $Order->GetOrdersByUserId($row['sys_user_id']);

                  
                  $cnt = count($usr_orders);
                  if($cnt>0){
                      $sum_order = 0;
                      $cnt_items = 0;
                      for($j=0;$j<$cnt;$j++){


                          $sum_order += $usr_orders[$j]['sum'];
                          $cnt_items += $usr_orders[$j]['qnt_all'];
                      }
                      $href = 'index.php?module=106&fltrUserId='.$row['sys_user_id'];
                      echo '<a href="'.$href.'">'.$cnt.' '.$this->multi['TXT_USERS_ORDERS_SUMMARY'].' '.$Order->currency->ShowPrice($sum_order).'</a>';
                  }
                  ?>
              </td>
              <?
          }
          ?><td align="center"><?=$this->GetNameUserGrp($row['group_id']);?></td><?
          ?><td align="center"><?
          $status = $row['active'];
          if ($status==1) echo $this->Msg->show_text('TXT_ONLINE');
          else echo '-';//$this->Msg->show_text('TXT_OFFLINE');
          ?></td><?

          ?><td align="center"><?=$row['enrol_date'];?></td><?
          ?><td align="center"><?=$row['last_active_counter'];?></td><?

          $ModulesPlug = new ModulesPlug();
          $id_mod_sys_user = $ModulesPlug->GetModuleIdByPath( '/admin/modules/sys_user/sys_user.php' );
          $id_mod_stat = $ModulesPlug->GetModuleIdByPath( '/admin/modules/sys_stat/stat.php' );
          ?><td align="center"><?=$row['used_counter'];?></td><?
          ?><td align="center"><?=$row['ip'];?></td><?

        } //-- end for

        AdminHTML::TablePartF();
        $this->Form->WriteFooter();
        return true;

       } //end of fuinction show



        // ================================================================================================
        // Function : ShowSearchForm
        // Version : 1.0.0
        // Date : 07.06.2007
        //
        // Parms :
        // Returns : true,false / Void
        // Description : Show search form
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 07.06.2007
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function ShowSearchForm()
        {
            ?>
            <form name="search_form" action="<?=$this->script;?>" method="post"  title="<?=$this->Msg->show_text('TXT_SEARCH_FORM');?>">
                <?
                if(empty($this->srch_dtfrom)) {
                 //$DateCalc = new Date_Calc();
                 //$tmp_dt = explode("-",date("Y-m-d"));
                 //$this->srch_dtfrom = $DateCalc->beginOfPrevMonth($tmp_dt[2], $tmp_dt[1], $tmp_dt[0],"%Y-%m-%d");
                 $this->srch_dtfrom = '2014-01-01';
                }
                if(empty($this->srch_dtto)) {
                $this->srch_dtto = date("Y-m-d");
                }

                ?>
                <table border="0" cellpadding="2" cellspacing="1" style="float:left; margin: 0 10px 0 0; ">
                <tr>
                    <td><h4><?=$this->Msg->show_text('TXT_SEARCH_FORM');?></h4></td>
                </tr>
                <tr class="tr1">
                    <td align="right"><?=$this->Msg->show_text('FLD_USER_LOGIN').', '.$this->Msg->show_text('FLD_NAME').', '.$this->Msg->show_text('FLD_PHONE');?></td>
                    <td align="left"><?$this->Form->TextBox('srch', $this->srch, 30);?></td>
                    <td align="right"><?=$this->Msg->show_text('_FLD_GROUP');?></td>
                    <td align="left">
                        <?
                        $SysGroup = new SysGroup();
                        $arr_grp = $SysGroup->GetGrpNameToArr('front');
                        //$arr_grp[0]='';
                        //ksort($arr_grp);
                        $arr_grp = array( 0=>'') + $arr_grp ;
                        $this->Form->Select($arr_grp, 'fltr', $this->fltr);
                        ?>
                    </td>
                </tr>
                <tr class="tr2">
                    <td align="right"><?=$this->Msg->show_text('_FLD_ENROL_DATE')?></td>
                    <td align="left"><?=$this->Msg->show_text('TXT_ENROLE_DATE_FROM'); $this->Form->TextBox('srch_dtfrom', $this->srch_dtfrom, 10);?>- <?=$this->Msg->show_text('TXT_ENROLE_DATE_TO'); $this->Form->TextBox('srch_dtto', $this->srch_dtto, 10);?></td>
                    <td align="right"><?=$this->Msg->show_text('FLD_STATUS');?></td>
                    <td align="left">
                        <?
                        $this->Spr->ShowInComboBox( TblModUserSprStatus, 'fltr2', $this->fltr2, 10 );
                        ?>
                    </td>
                </tr>
                <tr class="tr1">
                    <td colspan="4" align="center"><?$this->Form->Button( '', $this->Msg->show_text('TXT_BUTTON_SEARCH'), 50 );?></td>
                <tr>
                <?/*
                <tr class="tr1">
                <td align="right"><?=$this->Msg->show_text('FLD_COUNTRY');?></td>
                <td align="left"><?$this->Spr->ShowInComboBox( TblSysSprCountry, 'srch_country', $this->srch_country, 10 );?></td>
                </tr>
                *
                */
                ?>
                </table>
            </form>
            <?
            $script = 'module=' . $this->module . '&display=' . $this->display . '&start=' . $this->start . '&sort=' . $this->sort . '&fltr=' . $this->fltr;
            $script = $_SERVER['PHP_SELF'] . "?$script";
            ?>
            <div style="margin: 0px 0px 0px 10px;">
                <h4><?=$this->Msg->show_text('TXT_USER_EXPORT_FORM');?></h4>
                <form enctype="multipart/form-data" id="ExportPrice" name="ExportPrice" method="post" action="<?=$script;?>">
                    <input type="submit" name="export_users_to_csv" value="Экспорт всех пользователей в .csv-файл" onclick="Export('<?=$this->module;?>', 'export_users_to_csv', 'res_export_users_to_csv'); return false;" />
                    <div id="res_export_users_to_csv" style="margin-bottom: 5px;"></div>
                    <input type="submit" name="export_users_email" value="Экспорт емейлов всех пользователей в .txt-файл" onclick="Export('<?=$this->module;?>', 'export_users_email', 'res_export_users_email'); return false;" />
                    <div id="res_export_users_email" style="margin-bottom: 5px;"></div>
                </form>
               </div>

            <script type="text/javascript">
                function Export(module, task, div_id) {
                    Did = "#" + div_id;
                    $.ajax({
                        type: "POST",
                        url: "/modules/mod_user/user.backend.php",
                        data: "module=" + module + "&task=" + task,
                        beforeSend: function () {
                            $(Did).html('<img src="/admin/images/ajax-loader.gif"/>');
                        },
                        success: function (html) {
                            $(Did).html(html);
                            $(Did).show("slow");
                        }
                    });
                }
            </script>
            <div class="clear"></div>

            <?
        } //end of fuinction ShowSearchForm()



       // ================================================================================================
       // Function : edit()
       // Version : 1.0.0
       // Date : 06.01.2006
       //
       // Parms :
       // Returns : true,false / Void
       // Description : edit/add records in Products module
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 06.01.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function edit()
       {
           $this->show_TinyMCE();
           $this->show_JS();
        $tmp_db = new DB();
        $logon = new  Authorization();
        $mas = NULL;
        if( $this->id!=NULL  )
        {
          $q = "SELECT * FROM `".TblModUser."`,`".TblSysUser."` where `".TblModUser."`.sys_user_id='$this->id' AND `".TblModUser."`.sys_user_id=`".TblSysUser."`.id" ;
          $res = $this->Right->Query( $q, $this->user_id, $this->module, $this->db_host, $this->db_user, $this->db_pass, $this->db_name, $this->db_open );
          //echo '<br>$q='.$q.' $res='.$res;
          if( !$res ) return false;
          $mas = $this->Right->db_FetchAssoc();
        }
        $Panel = new Panel();
        $ln_sys = new SysLang();
        $Spr = new SysSpr();


        $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr;
        $script = $_SERVER['PHP_SELF']."?$script";

        /* Write Form Header */


        if( $this->id!=NULL ) $txt = $this->Msg->show_text('_TXT_EDIT_DATA');
        else $txt = $this->Msg->show_text('_TXT_ADD_DATA');
        AdminHTML::PanelSubH( $txt );

        //-------- Show Error text for validation fields --------------
        $this->ShowErrBackEnd();
        //-------------------------------------------------------------

        AdminHTML::PanelSimpleH();
$this->Form->WriteHeaderFormImg( $script );
       ?>
   <table class="EditTable" border="0" width="100%">
    <tr>
     <td width="100%">
      <table border="0" class="EditTable">
       <tr>
        <td colspan="3"><h2>1. <?=$this->Msg->show_text('TXT_REG_STEP0');?></h2></td>
       </tr>
       <?
       if(!empty($mas['avatar'])){
           ?>
       <tr>
           <td>
               <img width="50" height="50" src="/images/mod_blog/<?=$this->id?>/<?=$mas['avatar']?>"/>
           </td>
       </tr>
       <?}?>
       <tr>
        <td align="right"><b><?=$this->Msg->show_text('FLD_STATUS');?>:<span class="red">*</span></b></td>
        <td>
         <?
         if( $this->id!=NULL ) $this->Err!=NULL ? $user_status=$this->user_status : $user_status=$mas['user_status'];
         else $user_status=$this->user_status;
         $this->Form->Hidden( 'old_user_status', $user_status);
         //$this->Form->TextBox( 'user_status', $user_status, 40  );
         $Spr->ShowInComboBox( TblModUserSprStatus, 'user_status', $user_status, 10 );
         ?>
         &nbsp;
         <b><?=$this->Msg->show_text('_FLD_GROUP');?>:<span class="red">*</span></b><?
         if( $this->id!=NULL ) $this->Err!=NULL ? $val = $this->group_id : $val = $mas['group_id'];
         else $val = $this->group_id;

         $SysGroup = new SysGroup();
         $arr_grp = $SysGroup->GetGrpNameToArr('front');

         $arr_grp = array( 0=>'') + $arr_grp ;
         //ksort($arr_grp);
         $this->Form->Select($arr_grp, 'group_id', $val);
         $this->Form->Hidden( 'old_group_id', $val );
         ?>
        </td>
       </tr>
       <tr>
        <td align="right"><b><?=$this->Msg->show_text('_FLD_MULTI_USE');?>:<span class="red">*</span></b></td>
        <td>
           <?
           $mas_tmp1[0] = $this->Msg->show_text('TXT_UNIQUE_USE');
           $mas_tmp1[1] = $this->Msg->show_text('TXT_MULTIPLE_USE');
           if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->login_multi_use : $val=$this->IsLoginMultiUse($mas['email']);
           else $val=$this->login_multi_use;
           $this->Form->Select( $mas_tmp1, 'login_multi_use', stripslashes($val) );
           ?>
          </td>
         </tr>
         <tr>
          <td align="right"><b><?=$this->Msg->show_text('_FLD_ENROL_DATE');?></b></td>
          <td>
           <?
           if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->enrol_date : $val = $mas['enrol_date'];
           else $val = date('Y-m-d');echo stripslashes($val);

           ?>
        </td>
       </tr>
       <tr>
        <td align="right"><b><?=$this->Msg->show_text('FLD_ID');?>:&nbsp;</b></td>
        <td>
         <?
          if( $this->id!=NULL )
          {
           echo $mas['id'];
           $this->Form->Hidden( 'id', $mas['id'] );
          }
          else $this->Form->Hidden( 'id', '' );
         ?>
        </td>
       </tr>
       <tr>
        <td align="right"><b><?=$this->Msg->show_text('FLD_USER_LOGIN');?>:<span class="red">*</span></b></td>
        <td>
         <?
         if( $this->id!=NULL ) $this->Err!=NULL ? $login=$this->login : $login=$mas['login'];
         else $login=$this->login;
         $this->Form->Hidden( 'old_login', $login);
         $url = '/modules/mod_user/user.backend.php?'.$this->script_ajax.'&task=login_checkup';
         $formname=$this->Form->name;
         $params = " onBlur=\"if(document.$formname.old_login.value!=document.$formname.login.value) isLoginAlias('".$url."', 'islogin', 'login_checkup')\" ";
         $this->Form->TextBox('login', stripslashes($login), 50, $params ); ?>&nbsp;<? $this->Form->ButtonSimple( 'check_login', $this->Msg->show_text('_BTN_CHECK_UP'), NULL, "onClick=\"if(document.$formname.login.value!='') isLoginAlias('".$url."check_up=1', 'islogin', 'login_checkup')\"" );
         ?>
        </td>
        <td align="left">
         <div id="islogin"></div>
        </td>
       </tr>
       <tr>
        <td></td>
        <td><?=$this->Msg->show_text('FLD_LOGIN_HELP');?></td>
       </tr>
       <tr>
        <td align="right"><b><?=$this->Msg->show_text('_FLD_ALIAS');?>:</b></td>
        <td>
         <?
         $this->Form->Hidden('old_email', $mas['email']);

         //$params = " onBlur=\"if(document.$formname.alias.value!='' AND document.$formname.old_alias.value!=document.$formname.alias.value) isLoginAlias('".$url."', 'isalias', 'alias_checkup')\" ";
         if( $this->id!=NULL ) $this->Err!=NULL ? $val = $this->email : $val = $mas['email'];
         else $val = $this->email;
         $this->Form->TextBox('email', $val, 50, ' autocomplete="off"' ); ?>&nbsp;<? //$this->Form->ButtonSimple( 'check_alias', $this->Msg->show_text('_BTN_CHECK_UP'), NULL, "onClick=\"if(document.$formname.alias.value!='') isLoginAlias('".$url."check_up=1', 'isalias', 'alias_checkup')\"" );
         ?>
        </td>
        <td>
         <div id="isalias"></div>
        </td>
       </tr>
       <?
       if( $this->id!=NULL ) {
       ?>
       <tr>
        <td align="right"><b><?=$this->Msg->show_text('_FLD_CURRENT_PASSWORD');?>:</b></td>
        <td>
         <?
         $curr_password = $this->GetUserPassword($mas['login']);
         if( $this->IsEncodePass($mas['login']) ) echo '*** PASSWORD ENCODE ***';
         else echo $curr_password;
         $this->Form->Hidden( 'oldpass', $curr_password);
         ?>
        </td>
       </tr>
       <?
       }
       ?>
       <tr>
        <td align="right"><b><?=$this->Msg->show_text('FLD_NEW_PASSWORD');?>:</b></td>
        <td>
         <?
         if( $this->id!=NULL ) $this->Err!=NULL ? $val = $this->password : $val = '';
         else $val = $this->password;
         //$this->Form->Password( 'password', $val, $size=40, );
         ?>
            <input type="password" name="password" value="<?=$val?>" size="40" autocomplete="off"/>

        </td>
       </tr>
       <tr>
        <td align="right"><b><?=$this->Msg->show_text('FLD_CONFIRM_PASSWORD');?>:</b></td>
        <td>
         <?
         if( $this->id!=NULL ) $this->Err!=NULL ? $val = $this->password2 : $val = '';
         else $val = $this->password2;
         //$this->Form->Password( 'password2', $val, $size=40, ' autocomplete="off"' );
         ?>
            <input type="password" name="password2" value="<?=$val?>" size="40" autocomplete="off"/>
        </td>
       </tr>
       <tr>
        <td></td>
        <td><?=$this->Msg->show_text('FLD_PASSWORD_HELP');?></td>
       </tr>
       <tr><td height="20"></td></tr>
       <tr><td colspan="3"><hr></td></tr>
       <tr>
        <td colspan="3"><h2>2. <?=$this->Msg->show_text('TXT_REG_STEP1');?></h2></td>
       </tr>
       <tr>
           <td align="right"></td>
           <td>
               <?
               if( $this->id!=NULL ) $this->Err!=NULL ? $val = $this->subscr : $val = $mas['subscr'];
               else $val = $this->subscr;
               $this->Form->CheckBox( 'subscr', 1, $val )?>
               <?=$this->multi['FLD_SUBSCR'];?>
           </td>
       </tr>
       <tr>
        <td align="right"><b><?=$this->Msg->show_text('FLD_FIO_FOR_USERS');?>:<span class="red">*</span></b></td>
        <td>
         <?
         if( $this->id!=NULL ) $this->Err!=NULL ? $val = $this->name : $val = $mas['name'];
         else $val = $this->name;
         $this->Form->TextBox( 'name', stripslashes($val), 40);
         ?>
        </td>
       </tr>
       <?if($mas['group_id']==7){?>
       <tr>
        <td align="right"><b>Підпис експерта:</b></td>
        <td>
         <?
         if( $this->id!=NULL ) $this->Err!=NULL ? $val = $this->expertTitle : $val = $mas['expertTitle'];
         else $val = $this->expertTitle;
         $this->Form->TextBox( 'expertTitle', stripslashes($val), 40);
         ?>
        </td>
       </tr>
       <tr>
        <td align="right"><b>Відображати в ХЕАДЕРЕ:</b></td>
        <td>
         <?
         $checked="";
         if( $this->id!=NULL ) $this->Err!=NULL ? $visible=$this->ShowInTop : $visible=$mas['ShowInTop'];
             else $this->Err!=NULL ? $visible=$this->ShowInTop : $visible=2;
             ($visible==1)? $checked = 'checked="checked"': $checked = NULL;
             ?>
             <input type="checkbox" name="ShowInTop" <?=$checked?> value="1" onclick=""/>
        </td>
       </tr>
       <?}?>
       <?/*
       <tr>
        <td align="right"><b><?=$this->Msg->show_text('FLD_COUNTRY');?>:</b></td>
        <td nowrap>
         <?
         if( $this->id!=NULL ) $this->Err!=NULL ? $country=$this->country : $country=$mas['country'];
         else $country=$this->country;
         $this->Form->TextBox( 'country', stripslashes($country), 40);
         //$this->Spr->ShowInComboBox( TblSysSprCountry, 'country', $country, 40 )
         ?>
         &nbsp;<b><?=$this->Msg->show_text('FLD_CITY');?>: </b><?
         if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->city : $val=$mas['city'];
         else $val=$this->city;
         $this->Form->TextBox( 'city', stripslashes($val), 20  );
         ?>
        </td>
       </tr>
       */?>
       <tr>
        <td align="right"><b><?=$this->Msg->show_text('FLD_ADR');?>:</b></td>
        <td>
         <?
         if( $this->id!=NULL ) $this->Err!=NULL ? $val = $this->adr : $val = $mas['adr'];
         else $val = $this->adr;
         $this->Form->TextArea( 'adr', stripslashes($val), 4, 70);
         ?>
        </td>
       </tr>
       <tr>

        <td align="right">
            <?/*<b><?=$this->Msg->show_text('FLD_PHONE');?>:</b></td>
             <td>
             <?
             if( $this->id!=NULL ) $this->Err!=NULL ? $val = $this->phone : $val = $mas['phone'];
             else $val = $this->phone;
             $this->Form->TextBox( 'phone', stripslashes($val), 20);
            */?>
         &nbsp;<b><?=$this->Msg->show_text('FLD_PHONE_MOB').':</b>';?>
         </td>
         <td>
         <?
         if( $this->id!=NULL ) $this->Err!=NULL ? $val = $this->login : $val = $mas['login'];
         else $val = $this->login;
         $this->Form->TextBox( 'phone_mob', stripslashes($val), 20);
         ?>
        </td>
       </tr>
       <?/*
       <tr>
        <td align="right"><b><?=$this->Msg->show_text('FLD_FAX');?>:</b></td>
        <td>
         <?
         if( $this->id!=NULL ) $this->Err!=NULL ? $val = $this->fax : $val = $mas['fax'];
         else $val = $this->fax;
         $this->Form->TextBox( 'fax', stripslashes($val), 20);
         ?>
        </td>
       </tr>
       <tr>
        <td align="right"><b><?=$this->Msg->show_text('FLD_URL');?></b></td>
        <td>
         <?
         if( $this->id!=NULL ) $this->Err!=NULL ? $val = $this->www : $val = $mas['www'];
         else $val = $this->www;
         $this->Form->TextBox( 'www', stripslashes($val), 40);
         ?>
        </td>
       </tr>
       <tr>
        <td align="right"><b><?=$this->Msg->show_text('FLD_BONUSES');?>:</b></td>
        <td>
         <?
         if( $this->id!=NULL ) $this->Err!=NULL ? $val = $this->bonuses : $val = $mas['bonuses'];
         else $val = $this->bonuses;
         $this->Form->TextBox( 'bonuses', stripslashes($val), 40);
         ?>
        </td>
       </tr>
       <tr>
        <td align="right"><b><?=$this->Msg->show_text('FLD_DISCOUNT');?>:</b></td>
        <td>
         <?
         if( $this->id!=NULL ) $this->Err!=NULL ? $val = $this->discount : $val = $mas['discount'];
         else $val = $this->discount;
         $this->Form->TextBox( 'discount', stripslashes($val), 40);
         ?>
        </td>
       </tr>
        */?>
       <tr><td colspan="2"><hr></td></tr>
       <tr>
           <td align="right">
               Служебный комментарий:
           </td>
           <td>
               <?
               if( $this->id!=NULL ) $this->Err!=NULL ? $val = $this->aboutMe : $val = $mas['aboutMe'];
               else $val = $this->aboutMe;
               ?>
               <textarea id="service_comment" name="aboutMe" style="width: 500px;height: 200px;"><?=$val;?></textarea>
           </td>
       </tr>
<!--       <tr><td height="20"></td></tr>-->
<!--       <input type="hidden" id="UserImageFilePath" value="--><?//=$mas['avatar']?><!--" name="avatar" />-->
<!--       <input type="hidden" id="UserImageFilePathExpert" value="--><?//=$mas['expertImg']?><!--" name="expertImg" />-->
<!--       <input type="hidden" id="UserImageFilePathExpertHeader" value="--><?//=$mas['expertImgHeader']?><!--" name="expertImgHeader" />-->
      </table>
     </td>
    </tr>
   </table>

   <?
   $this->Form->WriteFooter();
   ?>
   <?/*     <table>
            <tr>
                <td>
                   Аватар:
                </td>
                <td>
                    Аватар2:
                </td>
                <td>
                    Аватар в хеадере:
                </td>
            </tr>
            <tr>
                 <td>
                        <ul class="CatFormUl" id="imgLoaderConteiner1" style="list-style:none;">
                            <li id="imgLoaderConteiner1" style="height: auto;">
                                <div id="catImgAjaxLoader1" class="catImgAjaxLoader"></div>
                        <div id="CatImageUploadBox1">
                           <? if(is_file($_SERVER['DOCUMENT_ROOT']."/images/mod_blog/".$this->id."/".$mas['avatar'])){?>
                            <img class="avatarImage" width="120" src="<?="/images/mod_blog/".$this->id."/".$mas['avatar']?>"/><form id="catLoadImageForm1" name="catLoadImageForm2" action="/login.html" enctype="multipart/form-data" method="post" target="hiddenframe"><input type="hidden" value="deleteImage" name="task"/><input type="hidden" value="true" name="ajax"/><input type="hidden" value="2" name="wichForm"/><input type="hidden" value="/images/mod_blog/<?=$this->id."/".$mas['avatar']?>" name="fileDel"/><input type="button" class="btnCatalogFormDel" onclick="loadImage(1);" value="Видалити"/></form>
                           <?}else{?>
                            <form id="catLoadImageForm1" name="catLoadImageForm2" action="/login.html" enctype="multipart/form-data" method="post" target="hiddenframe">
                                         <input type="hidden" value="addImage" name="task"/>
                                         <input type="hidden" value="true" name="ajax"/>
                                         <input type="hidden" value="1" name="wichForm"/>
                                      Виберіть зображення для аватари:<br/>
                                      <input id="catUserFileUploader" type="file" name="image" size="18"/>
                                      <input class="btnCatalogImgUpload" type="button" onclick="loadImage('1');" value="Завантажити"/>
                            </form>
                           <?}?>
                        </div>
                            <iframe id="hiddenframe" name="hiddenframe" style="width:0px; height:0px; border:0px"></iframe>
                            </li>
                            <li>

                            </li>
                        </ul>
                </td>
                <td>
                     <ul class="CatFormUl" id="imgLoaderConteiner2" style="list-style:none;">
                            <li id="imgLoaderConteiner2" style="height: auto;">
                                <div id="catImgAjaxLoader2" class="catImgAjaxLoader"></div>
                        <div id="CatImageUploadBox2">
                           <? if(is_file($_SERVER['DOCUMENT_ROOT']."/images/mod_blog/".$this->id."/".$mas['expertImg'])){?>
                            <img class="avatarImage" width="120" src="<?="/images/mod_blog/".$this->id."/".$mas['expertImg']?>"/><form id="catLoadImageForm2" name="catLoadImageForm2" action="/login.html" enctype="multipart/form-data" method="post" target="hiddenframe"><input type="hidden" value="deleteImage" name="task"/><input type="hidden" value="true" name="ajax"/><input type="hidden" value="2" name="wichForm"/><input type="hidden" value="/images/mod_blog/<?=$this->id."/".$mas['expertImg']?>" name="fileDel"/><input type="button" class="btnCatalogFormDel" onclick="loadImage(2);" value="Видалити"/></form>
                           <?}else{?>
                            <form id="catLoadImageForm2" name="catLoadImageForm2" action="/login.html" enctype="multipart/form-data" method="post" target="hiddenframe">
                                         <input type="hidden" value="addImage" name="task"/>
                                         <input type="hidden" value="true" name="ajax"/>
                                         <input type="hidden" value="2" name="wichForm"/>
                                      Виберіть зображення для аватари2:<br/>
                                      <input id="catUserFileUploader" type="file" name="image" size="18"/>
                                      <input class="btnCatalogImgUpload" type="button" onclick="loadImage('2');" value="Завантажити"/>
                            </form>
                           <?}?>
                        </div>
                            <iframe id="hiddenframe" name="hiddenframe" style="width:0px; height:0px; border:0px"></iframe>
                            </li>
                            <li>

                            </li>
                        </ul>
                </td>
                 <td>
               <ul class="CatFormUl" id="imgLoaderConteiner3" style="list-style:none;">
                            <li id="imgLoaderConteiner3" style="height: auto;">
                                <div id="catImgAjaxLoader3" class="catImgAjaxLoader"></div>
                        <div id="CatImageUploadBox3">
                           <? if(is_file($_SERVER['DOCUMENT_ROOT']."/images/mod_blog/".$this->id."/".$mas['expertImgHeader'])){?>
                            <img class="avatarImage" width="120" src="<?="/images/mod_blog/".$this->id."/".$mas['expertImgHeader']?>"/><form id="catLoadImageForm3" name="catLoadImageForm3" action="/login.html" enctype="multipart/form-data" method="post" target="hiddenframe"><input type="hidden" value="deleteImage" name="task"/><input type="hidden" value="true" name="ajax"/><input type="hidden" value="3" name="wichForm"/><input type="hidden" value="/images/mod_blog/<?=$this->id."/".$mas['expertImgHeader']?>" name="fileDel"/><input type="button" class="btnCatalogFormDel" onclick="loadImage(3);" value="Видалити"/></form>
                           <?}else{?>
                            <form id="catLoadImageForm3" name="catLoadImageForm3" action="/login.html" enctype="multipart/form-data" method="post" target="hiddenframe">
                                         <input type="hidden" value="addImage" name="task"/>
                                         <input type="hidden" value="true" name="ajax"/>
                                         <input type="hidden" value="3" name="wichForm"/>
                                      Виберіть зображення для аватари в хеадере:<br/>
                                      <input id="catUserFileUploader" type="file" name="image" size="18"/>
                                      <input class="btnCatalogImgUpload" type="button" onclick="loadImage('3');" value="Завантажити"/>
                            </form>
                           <?}?>
                        </div>
                            <iframe id="hiddenframe" name="hiddenframe" style="width:0px; height:0px; border:0px"></iframe>
                            </li>
                            <li>

                            </li>
                        </ul>
                </td>
        </tr>
          </table>
        */?>
        <table><tr><td><img src="images/icons/warning.png" alt="" title="" border="0"></td><td class="warning"><?=$this->Msg->show_text('_MSG_OBLIGATORY_FOR_FILLING');?></td></tr></table>
         <?
        AdminHTML::PanelSimpleF();
        if($this->Right->IsUpdate()) $this->Form->WriteSaveAndReturnPanel( $script );?>&nbsp;<?
        if($this->Right->IsUpdate()) $this->Form->WriteSavePanel( $script );
        $this->Form->WriteCancelPanel( $script );
        AdminHTML::PanelSubF();


        ?>
        <script language="JavaScript">
         function isLoginAlias(url, div_id, $task){
              document.<?=$this->Form->name?>.task.value=$task;
              parm = $('#<?=$this->Form->name?>').formSerialize();
              $.post(url, parm,
               function(data){
                  $("#"+div_id).empty();
                  $("#"+div_id).append(data);
               }
              );
         }
        </script>
        <?
        return true;
       } //end of fuinction edit()


        function show_TinyMCE(){
           $this->Form->IncludeTinyMCE();
        ?>
        <script type="text/javascript">
        	$(document).ready(function() {
        		$('#aboutMe').tinymce({
        			// Location of TinyMCE script
        			//script_url : '/sys/js/tinymce/tiny_mce.js',
                                 mode : "textareas",
                                language:"<?if(_LANG_SHORT=="ua") echo "uk"; else echo _LANG_SHORT;?>",
                                plugins : "pagebreak,style,contextmenu,table,advhr,advimage,advlink,inlinepopups,media,searchreplace,paste,fullscreen,noneditable,visualchars,nonbreaking,template,imagemanager,filemanager",
                              theme : "advanced",
                                theme_advanced_buttons1 : "mylistbox,mysplitbutton,bold,italic,underline,separator,strikethrough,justifyleft,justifycenter,justifyright,justifyfull,bullist,numlist,undo,redo,link,unlink",
                                theme_advanced_buttons2 : "",
                                theme_advanced_buttons3 : "",
                                theme_advanced_toolbar_location : "top",
                                theme_advanced_toolbar_align : "left",
                                theme_advanced_statusbar_location : "bottom",
                                theme_advanced_toolbar_location : "top",
                                theme_advanced_toolbar_align : "left",
                                theme_advanced_statusbar_location : "bottom",
                                theme_advanced_resizing : true,
                                skin : "o2k7",
                                skin_variant : "silver",
                                convert_urls : false,
                                content_css : "/include/css/TinyMCE.css"


        		});
        	});
        </script>

        <?
    }

    function show_JS(){
        ?>
         <script type="text/javascript">
         function loadImage(form){
                if($('#catUserFileUploader'+form).val()!=""){
                    loader=$("#imgLoaderConteiner"+form);
                    $("#catImgAjaxLoader"+form).width(loader.width()+10).height(loader.height()).fadeTo("fast", 0.4);
                    $('#catLoadImageForm'+form).submit();
                }else $.fancybox('Виберіть зображення для завантаження');

            }
            function del(form){
                $("#catImgAjaxLoader"+form).fadeOut("fast", function(){
                    $('#CatImageUploadBox'+form).html('<form id="catLoadImageForm'+form+'" name="catLoadImageForm'+form+'" action="/login.html" enctype="multipart/form-data" method="post" target="hiddenframe"><input type="hidden" value="'+form+'" name="wichForm"/><input type="hidden" value="addImage" name="task"/><input type="hidden" value="true" name="ajax"/>Виберіть зображення:<br/><input id="catUserFileUploader" type="file" name="image" size="18"/><input class="btnCatalogImgUpload'+form+'" type="button" onclick="loadImage('+form+');" value="Завантажити"/></form>');
                });
            }
            function response(err,filePath,file,form){

              $("#catImgAjaxLoader"+form).fadeOut("fast", function(){
                if(err==''){
                    if(form==1){$("#UserImageFilePath").val(file);}
                    if(form==2){$("#UserImageFilePathExpert").val(file);}
                    if(form==3){$("#UserImageFilePathExpertHeader").val(file);}
                    $('#CatImageUploadBox'+form).html('<img class="avatarImage" style="border:white solid 3px;" width="120" src="'+filePath+'"/><form id="catLoadImageForm'+form+'" name="catLoadImageForm'+form+'" action="/login.html" enctype="multipart/form-data" method="post" target="hiddenframe"><input type="hidden" value="deleteImage" name="task"/><input type="hidden" value="true" name="ajax"/><input type="hidden" value="'+form+'" name="wichForm"/><input type="hidden" value="'+filePath+'" name="fileDel"/><input type="button" class="btnCatalogFormDel" onclick="loadImage('+form+');" value="Видалити"/></form>');
                }else{
                    $.fancybox(err);
                }
              });
            }
         </script>
         <?
    }
       // ================================================================================================
       // Function : CheckFields()
       // Version : 1.0.0
       // Date : 10.01.2006
       //
       // Parms :        $id - id of the record in the table
       // Returns :      true,false / Void
       // Description :  Checking all fields for filling and validation
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 10.01.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function CheckFields($id = NULL)
       {
        $this->Err=NULL;
        $this->Err_characters=NULL;
        $this->user_status=trim($this->user_status);

        if( empty($this->login) ) {
            $this->Err = $this->Err.$this->Msg->show_text('MSG_LOGIN_EMPTY').'<br>';
        }
        else{
            if ( !$this->unique_login($this->email) AND stripslashes($this->old_email)!=stripslashes($this->email) AND !empty($this->email) ) {
                //$this->Err=$this->Err.$this->Msg->show_text('MSG_NOT_UNIQUE_LOGIN_1')." ".stripslashes($this->email)." ".$this->Msg->show_text('MSG_NOT_UNIQUE_LOGIN_2').'<br>';
                $this->Err=$this->Err.$this->Msg->show_text('MSG_NOT_UNIQUE_LOGIN').' "'.stripslashes($this->email).'" '.$this->Msg->show_text('MSG_NOT_UNIQUE_LOGIN2').'<br>';
                if ( !empty($this->old_email)) $this->email = $this->old_email;
                else {
                    $this->email=NULL;
                    $this->email2=NULL;
                }
            }
            /*
            else {
                if ( $this->email!=$this->email2 ) $this->Err=$this->Err.$this->Msg->show_text('MSG_NOT_MATCH_REENTER_EMAIL').'<br>';
                if (!ereg("^[a-zA-Z0-9_.\-]+@[a-zA-Z0-9.\-].[a-zA-Z0-9.\-]+$", $this->email)) $this->Err=$this->Err.$this->Msg->show_text('MSG_NOT_VALID_EMAIL').'<br>';
            }
            */
            if( $this->old_group_id!=$this->group_id ){
                $is_encode1 = $this->IsEncodePass($this->login, $this->old_group_id );
                $is_encode2 = $this->IsEncodePass($this->login, $this->group_id );
                //echo '<br>$is_encode1='.$is_encode1.' $is_encode2='.$is_encode2;
                if($is_encode1==1 AND $is_encode2==NULL) $this->Err = $this->Err.$this->Msg->show_text('MSG_CHANGE_PASSWORD').'<br>';
                if($is_encode1==NULL AND $is_encode2==1) {
                    $this->password = $this->EncodePass($this->login, $this->password, $this->group_id);
                    $this->password2 = $this->password;
                }
            }
        }
        if ( !empty( $this->password ) ) {
            if ( empty( $this->password2 ) ) $this->Err = $this->Err.$this->Msg->show_text('MSG_FLD_CONFIRM_PASSWORD_EMPTY').'<br>';
            else {
                if ( $this->password!=$this->password2 ) $this->Err = $this->Err.$this->Msg->show_text('MSG_NOT_MATCH_CONFIRM_PASSWORD').'<br>';
            }
        }

        //if (empty( $this->name )) $this->Err=$this->Err.$this->Msg->show_text('MSG_FLD_NAME_EMPTY').'<br>';
        //if (empty( $this->last_name )) $this->Err=$this->Err.$this->Msg->show_text('MSG_FLD_LAST_NAME_EMPTY').'<br>';
        //if (empty( $this->position )) $this->Err=$this->Err.$this->Msg->show_text('MSG_FLD_POSITION_EMPTY').'<br>';
        //if (empty( $this->country )) $this->Err=$this->Err.$this->Msg->show_text('MSG_FLD_COUNTRY_EMPTY').'<br>';

        if ($this->user_status==NULL) $this->user_status=3;

        //return $this->Err.$this->Err_characters;
        return $this->Err;
       } //end of fuinction CheckFields()


    // ================================================================================================
    // Function : del()
    // Version : 1.0.0
    // Date : 06.01.2006
    //
    // Parms :
    // Returns :      true,false / Void
    // Description :  Remove data from the table
    // ================================================================================================
    // Programmer :  Igor Trokhymchuk
    // Date : 06.01.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function del( $id_del )
    {
       $kol = count( $id_del );
       $del = 0;
       for( $i=0; $i<$kol; $i++ )
       {
        $u = $id_del[$i];

        // delete user from main Exsternal users table
        $q = "DELETE FROM `".TblModUser."` WHERE `sys_user_id`='$u'";
        $res = $this->Right->Query( $q, $this->user_id, $this->module, $this->db_host, $this->db_user, $this->db_pass, $this->db_name, $this->db_open );
        //echo '<br>7$q='.$q.' $res='.$res.' $this->Right->result'.$this->Right->result;
        if (!$res) return false;

        // delete user from system users
        $q = "DELETE FROM `".TblSysUser."` WHERE `id`='".$u."'";
        $res = $this->Right->Query( $q, $this->user_id, $this->module, $this->db_host, $this->db_user, $this->db_pass, $this->db_name, $this->db_open );
        //echo '<br>8$q='.$q.' $res='.$res.' $this->Right->result'.$this->Right->result;

        if ( $res )
         $del=$del+1;
        else
         return false;
       }
     return $del;
    } //end of fuinction del()


    // ================================================================================================
    // Function : ShowErrBackEnd()
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
    function ShowErrBackEnd( $err = NULL)
    {
       if ($this->Err) $err = $this->Err;
       if( !empty($err) ) {
           ?>
           <table border="0" cellspacing="0" cellpadding="0" class="err" align="center" width="100%">
            <tr><td align="center"><?=$err;?></td></tr>
           </table>
           <?
       }
    } //end of fuinction ShowErrBackEnd()

    // ================================================================================================
    // Function : SendHTMLActivation
    // Version : 1.0.0
    // Date : 04.03.2005
    //
    // Parms :
    // Returns : true,false / Void
    // Description : Send the registration mail with profile of the user
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 04.03.2005
    // Reason for change : Remake desing of the login form.
    // Change Request Nbr:
    // ================================================================================================
    function SendHTMLActivation()
    {
     $old_lang_id = $this->Msg->lang_id;
     //$this->Msg->lang_id = $this->lang_id_for_send_emails;
     $txt = "<br>Уважаемый, ".$this->name.",";
     $info = NULL;

     $info = "<br> Ваш аккаунт был активирован";

     $password = $this->GetUserPassword($this->email);
     $info = $info."
     <br>Ваш логін : ".$this->login."
     <br>".$this->Msg->show_text('FLD_PASSWORD').": ".$password.
             "<br/> Ви можете зайти на сайт використовуючи форму входу: '".NAME_SERVER."/login.html'";
     //echo '<br> info='.$info;

     //-------------Send to User START ---------------
     $subject = "Ваш аккаунт был активирован";
     $body = $txt.$info;
     //echo $body;
     $arr_emails[0]=$this->email;
     $res = $this->SendSysEmail($subject, $body, $arr_emails);
     //echo '<br>$res='.$res;
     $this->Msg->lang_id = $old_lang_id;
     if( !$res ) {return false;}
     //-------------Send to User END ---------------

     return true;
    } //end of function SendHTMLActivation()


    /**
    * Class method ExportUsersEmail
    * export users e-mail to .txt-file
    * @return true/false or arrays:
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 09.03.2014
    */
    function ExportUsersEmail(){
        $q = "SELECT
                `".TblSysUser."`.`email`
              FROM
                `".TblSysUser."`, `".TblModUser."`
              WHERE
                `".TblSysUser."`.`group_id` = '5'
                AND `".TblSysUser."`.`id`=`".TblModUser."`.`sys_user_id`
                AND `".TblModUser."`.`user_status`='3'
             ";
        $email = "";
        $filename = "user-emails.txt";
        $res = $this->db->db_Query($q);
        //echo '<br>$q='.$q.' $res='.$res;
        if($res){
            $row = $this->db->db_GetNumRows();
            for($i=0; $i<$row ; $i++){
               $row = $this->db->db_FetchAssoc();
               $email .= $row['email']."\n";
            }
            $path = '/export/'.$filename;
            $path_show = 'http://'.NAME_SERVER.$path;

            $uploaddir = SITE_PATH.'/export';
            $fullpath = $uploaddir.'/'.$filename;
            if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777);
            else @chmod($uploaddir,0777);

            if (!$handle = fopen($fullpath, 'w')) {
               echo "Не могу открыть файл ()";
               return;
            }

            if (fwrite($handle, $email) === FALSE) {
               echo "Не могу произвести запись в файл ()";
               return;
            }
            //readfile($fullpath);
            fclose($handle);



            if($path){
               echo 'Скачать список емелйов пользователей <a href="http://'.NAME_SERVER.'/sys/report_download.php?path='.$path.'&module='.$this->module.'&task=export_to_txt">'.$path_show.'</a>';

               return true;
            }
            else{
               echo 'Ошибка. Емейлы не экспортировались.';
               return false;
            }

        }

    }

    /**
    * Class method ExportUsersToCSV
    * export users data to .csv-file
    * @return true/false or arrays:
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 09.03.2014
    */
    function ExportUsersToCSV()
    {
        $arruser = array();

        $csv_separator = ";";
        $csv_terminated = "\n";

        $filename = 'export_users.csv';
        $outPutArray = '';


        $q = "SELECT
                `".TblModUser."`.*,
                `".TblSysUser."`.*
              FROM
                `".TblModUser."`,
                `".TblSysUser."`
              WHERE
                `".TblSysUser."`.`group_id` = '5'
                AND `".TblSysUser."`.`id`=`".TblModUser."`.`sys_user_id`
                AND `".TblModUser."`.`user_status`='3'
             ";

        $res = $this->db->db_Query($q);

        if( !$res OR !$this->db->result) return false;
        $rows = $this->db->db_GetNumRows();

        for($i=0; $i<$rows; $i++){
           $row = $this->db->db_FetchAssoc();

           $index = stripslashes($row['sys_user_id']);
           $arruser[$index]=$row;
        }

        $outPutArray .= "Ид. Пользователя".$csv_separator.
                       "Фамилия, Имя пользователя".$csv_separator.
                       "E-mail".$csv_separator.
                       "Тел".$csv_separator.
                       "Моб. тел".$csv_separator.
                       "Адрес".$csv_separator.
                       "Дата регистрации".$csv_separator.
                       $csv_terminated;

        foreach($arruser as $id=>$arr_user){
            $outPutArray .=
               $arr_user['sys_user_id'].$csv_separator.
               $arr_user['name'].$csv_separator.
               $arr_user['email'].$csv_separator.
               $arr_user['phone'].$csv_separator.
               $arr_user['phone_mob'].$csv_separator.
               $arr_user['adr'].$csv_separator.
               $arr_user['enrol_date'].$csv_separator.
               $csv_terminated;
        }
        $this->from_charset = "utf-8";
        $this->to_charset = "windows-1251";
        $out = $this->Conv($outPutArray);

        $uploaddir = SITE_PATH.'/export';
        $fullpath = $uploaddir.'/'.$filename;
        if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777);
        else @chmod($uploaddir,0777);

        if (!$handle = fopen($fullpath, 'w')) {
           echo "Не могу открыть файл ()";
           return;
        }

        if (fwrite($handle, $out) === FALSE) {
           echo "Не могу произвести запись в файл ()";
           return;
        }
        //readfile($fullpath);
        fclose($handle);

        $path = '/export/'.$filename;
        $path_show = 'http://'.NAME_SERVER.$path;

        if($path){
           echo 'Скачать пользователей <a href="http://'.NAME_SERVER.'/sys/report_download.php?path='.$path.'&module='.$this->module.'&task=export_to_csv">'.$path_show.'</a>';
           //
           return true;
        }
        else{
           echo 'Ошибка. Пользователи не экспортировались.';
           return false;
        }

    }//end of function ExportCatalogToCSV()

    // ================================================================================================
    // Function : Conv()
    // Version : 1.0.0
    // Date : 29.11.2009
    // Parms : $str - string to conv
    // Returns : true,false / Void
    // Description :  convert string from one charset to another
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function Conv($str)
    {
       //echo '<br/>$this->from_charset ='.$this->from_charset;
       //echo '<br/>$this->to_charset ='.$this->to_charset;
       if ($this->to_charset != $this->from_charset) {
           $str = iconv($this->from_charset, $this->to_charset, $str);
       }
       return $str;
    }


//============================================================================================================
//========================================= USER BOX SECTION =================================================
//=========================================     START  =======================================================
//============================================================================================================

       // ================================================================================================
       // Function : ShowCtrlDatingBox()
       // Version : 1.0.0
       // Date : 11.05.2007
       //
       // Parms :
       // Returns :      true,false / Void
       // Description :  show control form for User DatingDox
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 11.05.2007
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function ShowCtrlDatingBox()
       {
?>
<style type="text/css">
#box_border{
 margin-top:0px;
 margin-left:10px;
 margin-right:10px;
 padding-top:2px;
 padding-bottom:2px;
 width:98%;
 border: solid 1px #D0D0D0;
}

#box_header{
 background-color:#FFF4F4;
 margin-top:0px;
 margin-left:10px;
 margin-right:10px;
 padding-top:2px;
 padding-bottom:2px;
 width:100%;
 text-align:center;
 text-transform: uppercase;
 font-weight: bold;
 border: solid 1px #D0D0D0;
}
.box_header{
 background-color:#C0C0FF;
 margin-top:0px;
 margin-left:10px;
 margin-right:10px;
 padding-top:2px;
 padding-bottom:2px;
 width:100%;
 text-align:center;
 text-transform: uppercase;
 font-weight: bold;
 border: solid 1px #D0D0D0;
}

.box_content_unread{
 margin-top:2px;
 margin-left:10px;
 margin-right:10px;
 padding-top:2px;
 text-align:center;
 vertical-align:middle;
 width:100%;
 text-align:center;
 font-weight: bold;
 border: solid 1px #D0D0D0;
}

.box_content_unread a{
 color: #000000;
}

.box_content{
 margin-top:2px;
 margin-left:10px;
 margin-right:10px;
 padding-top:2px;
 text-align:center;
 vertical-align:middle;
 width:100%;
 text-align:center;
 border: solid 1px #D0D0D0;
}

.box_content a{
 font-weight: normal;
}

.box_check{
 width:30px;
 text-align:left;
 border: solid 0px #000000;
}

.box_img{
 width:50px;
 text-align:left;
}

.box_text{
 width:250px;
 text-align:left;
}

.box_from{
 width:150px;
 text-align:left;
}

.box_date{
 width:150px;
 text-align:left;
}
#box_detail{
 margin-left:10px;
 margin-right:10px;
 padding:2px;
}

#box_detail .c03{
 text-align:justify;
 margin-top: 5px;
 padding:10px;
 border: solid 1px #F0F0F0;
}
</style>
<?

         AdminHTML::PanelSubH( $this->GetUserNickNameByUserId($this->id).' ('.$this->GetUserEmailByUserId($this->id).') :: '.$this->Msg->Show_text('TXT_USER_DATING_BOX') );
         AdminHTML::PanelSimpleH();
         ?>
         <table border="0" cellpadding="10" cellspacing="10">
          <tr>
          <?$count_new_letters = $this->GetBoxDataCountNew($this->id, 1, 1);
            if ($count_new_letters>0) $str_show_count_new_letters = "<b>$count_new_letters</b> (".$this->GetBoxDataCount($this->id, 1, 1).")";
            else $str_show_count_new_letters = $this->GetBoxDataCount($this->id, 1, 1);
          ?>
           <td align="center"><a href="<?=$this->script;?>&amp;task=show_box_letters&amp;box_type=1"><?=$this->Msg->show_img('IMG_MY_LETTERS');?></a>
            <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
             <tr>
              <td align="center"><a href="<?=$this->script;;?>&amp;task=show_box_letters&amp;box_type=1"><?=$this->Msg->Show_text('TXT_FRONT_BOX_INBOX');?></a>...<?=$str_show_count_new_letters;?></td>
             </tr>
             <tr>
              <td align="center"><a href="<?=$this->script;?>&amp;task=show_box_letters&amp;box_type=2"><?=$this->Msg->Show_text('TXT_FRONT_BOX_OUTBOX');?></a>...<?=$this->GetBoxDataCount($this->id, 1, 2);?></td>
             </tr>
             <?/*
             <tr>
              <td align="center"><a href="<?=$this->script;?>&amp;task=show_box_letters&amp;box_type=3"><?=$this->Msg->Show_text('TXT_FRONT_BOX_TRASH');?></a>...<?=$this->GetBoxDataCount($this->id, 1, 3);?></td>
             </tr>
             */?>
            </table>
           </td>
          <?$count_new_letters = $this->GetBoxDataCountNew($this->id, 2, 1);
            if ($count_new_letters>0) $str_show_count_new_letters = "<b>$count_new_letters</b> (".$this->GetBoxDataCount($this->id, 2, 1).")";
            else $str_show_count_new_letters = $this->GetBoxDataCount($this->id, 2, 1);
          ?>
           <td align="center"><a href="<?=$this->script;?>&amp;task=show_box_kisses&amp;box_type=1"><?=$this->Msg->show_img('IMG_MY_KISSES');?></a>
            <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
             <tr>
              <td align="center"><a href="<?=$this->script;?>&amp;task=show_box_kisses&amp;box_type=1"><?=$this->Msg->Show_text('TXT_FRONT_BOX_INBOX');?></a>...<?=$str_show_count_new_letters;?></td>
             </tr>
             <tr>
              <td align="center"><a href="<?=$this->script;?>&amp;task=show_box_kisses&amp;box_type=2"><?=$this->Msg->Show_text('TXT_FRONT_BOX_OUTBOX');?></a>...<?=$this->GetBoxDataCount($this->id, 2, 2);?></td>
             </tr>
             <?/*
             <tr>
              <td align="center"><a href="<?=$this->script;?>&amp;task=show_box_kisses&amp;box_type=3"><?=$this->Msg->Show_text('TXT_FRONT_BOX_TRASH');?></a>...<?=$this->GetBoxDataCount($this->id, 2, 3);?></td>
             </tr>
             */?>
            </table>
           </td>
          <?$count_new_letters = $this->GetBoxDataCountNew($this->id, 3, 1);
            if ($count_new_letters>0) $str_show_count_new_letters = "<b>$count_new_letters</b> (".$this->GetBoxDataCount($this->id, 3, 1).")";
            else $str_show_count_new_letters = $this->GetBoxDataCount($this->id, 3, 1);
          ?>
           <td align="center"><a href="<?=$this->script;?>&amp;task=show_box_photos&amp;box_type=1"><?=$this->Msg->show_img('IMG_MY_PHOTOS');?></a>
            <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
             <tr>
              <td align="center"><a href="<?=$this->script;?>&amp;task=show_box_photos&amp;box_type=1"><?=$this->Msg->Show_text('TXT_FRONT_BOX_INBOX');?></a>...<?=$str_show_count_new_letters;?></td>
             </tr>
             <tr>
              <td align="center"><a href="<?=$this->script;?>&amp;task=show_box_photos&amp;box_type=2"><?=$this->Msg->Show_text('TXT_FRONT_BOX_OUTBOX');?></a>...<?=$this->GetBoxDataCount($this->id, 3, 2);?></td>
             </tr>
             <?/*
             <tr>
              <td align="center"><a href="<?=$this->script;?>&amp;task=show_box_photos&amp;box_type=3"><?=$this->Msg->Show_text('TXT_FRONT_BOX_TRASH');?></a>...<?=$this->GetBoxDataCount($this->id, 3, 3);?></td>
             </tr>
             */?>
            </table>
           </td>
          </tr>
         </table>
         <div class="line1"></div>
         <table width="100%" border="0" align="left" cellpadding="0" cellspacing="10">
          <tr>
           <td>
           <?
           if( !empty($this->item_letter) ) $this->ShowLettersDetail($this->box_group, $this->box_type, $this->GetBoxDataDetail($this->item_letter));
           else {
            if( !empty($this->box_group) ) $this->ShowLetters($this->box_group, $this->box_type, $this->GetBoxData($this->id, $this->box_group, $this->box_type));
           }
           ?>
           </td>
          </tr>
        </table>
        <?
        AdminHTML::PanelSimpleF();
        ?>
         <a CLASS="toolbar" href=<?=$this->script."&task=show";?> onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage('restore','','../admin/images/icons/restore_f2.png',1);">
         <IMG src='../admin/images/icons/restore.png' alt="<?=$this->Msg->show_text('TXT_RETUTN');?>" align="middle" border="0" name="restore">&nbsp;<?=$this->Msg->show_text('TXT_RETUTN');?></a>
        <?
        AdminHTML::PanelSubF();
       } //end of fuinction ShowCtrlDatingBox()


        // ================================================================================================
       // Function : ShowLetters()
       // Version : 1.0.0
       // Date : 29.01.2007
       //
       // Parms :   group   - group of the letter (1-letter, 2-kiss, 3-photo)
       //           type    - type of letter (1-inbox, 2-outbox)
       // Returns :      true,false / Void
       // Description :  Show form with all letters (inbox or outbox)
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 29.01.2007
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function ShowLetters($group, $type, $arr)
       {
           if( empty($type)){
               $type = $this->GetTypeOfLetter($this->item_letter);
           }
           ?>
        <script type="text/javascript">
            function CheckAll (number)
            {
                which = 0;
                current = number+which;
                var forma = window.document.selector;
                if (forma.elements[current].checked == true)
                {
                    for (i=0; i<number; i++)
                    {
                        forma.elements[i].checked = true;
                    }
                }
                else
                {
                    for (i=0; i<number; i++)
                    {
                        forma.elements[i].checked = false;
                    }
                }
            }


            function UncheckAll (which)
            {
                var forma = window.document.selector;
                number = 0;

                if (forma.elements[which].checked != true)
                {
                    forma.elements[number].checked = false;
                }

            }
        </script>
          <?
          $this->Form->WriteHeader( $this->script );
          $this->Form->Hidden('task', 'set_deleted_box_letters_arr' );
          $this->Form->Hidden('box_group', $this->box_group);
          $this->Form->Hidden('box_type', $this->box_type);
           switch($group){
             case '1':
                if ($type==1) $title = $this->Msg->Show_text('TXT_FRONT_BOX_INBOX').' '.$this->Msg->Show_text('TXT_BOX_LETTER_TITLE');
                else $title = $this->Msg->Show_text('TXT_FRONT_BOX_OUTBOX').' '.$this->Msg->Show_text('TXT_BOX_LETTER_TITLE');
                break;
             case '2':
                if ($type==1) $title = $this->Msg->Show_text('TXT_FRONT_BOX_INBOX').' '.$this->Msg->Show_text('TXT_BOX_KISSES_TITLE');
                else $title2 = $title = $this->Msg->Show_text('TXT_FRONT_BOX_OUTBOX').' '.$this->Msg->Show_text('TXT_BOX_KISSES_TITLE');
                break;
             case '3':
                $title = $this->Msg->Show_text('TXT_BOX_PHOTO_TITLE');
                if ($type==1) $title = $this->Msg->Show_text('TXT_FRONT_BOX_INBOX').' '.$this->Msg->Show_text('TXT_BOX_PHOTO_TITLE');
                else $title = $this->Msg->Show_text('TXT_FRONT_BOX_OUTBOX').' '.$this->Msg->Show_text('TXT_BOX_PHOTO_TITLE');
                break;
             default:
                $title = $this->Msg->Show_text('TXT_FRONT_BOX_TITLE');

         }
         ?>
         <h3><?=$title;?></h3>
        <?
        $this->ShowErrBackEnd();

         ?>
         <div id="box_border">
         <?
         if ( !is_array($arr) OR count($arr)==0){
             if ($group==1) $this->ShowErrBackEnd( $this->Msg->Show_text('MSG_FRONT_BOX_NO_LETTERS') );
             if ($group==2) $this->ShowErrBackEnd( $this->Msg->Show_text('MSG_FRONT_BOX_NO_KISSES') );
             if ($group==3) $this->ShowErrBackEnd( $this->Msg->Show_text('MSG_FRONT_BOX_NO_PHOTOS') );
             return true;
         }

         ?>
         <table border="1" width="100%">
          <tr>
           <td class="box_header">
            <table border="0">
             <tr>
              <td class="box_check">&nbsp;</td>
              <td class="box_img">&nbsp;</td>
              <td class="box_text"><?($group==2 ? $text=$this->Msg->Show_text('TXT_FRONT_BOX_KISSES') : $text=$this->Msg->Show_text('TXT_FRONT_BOX_TEXT')); echo $text;?></td>
              <td class="box_from"><?
                if($type==2) $text=$this->Msg->Show_text('TXT_FRONT_BOX_TO');
                else {
                    if ($group==2) $text=NULL;
                    else $text=$this->Msg->Show_text('TXT_FRONT_BOX_FROM');
                }
                echo $text;?></td>
              <td class="box_date"><?=$this->Msg->Show_text('TXT_FRONT_BOX_DATE');?></td>
             </tr>
            </table>
           </td>
          </tr>
         <?
         foreach($arr as $key=>$value){
             if($value['status']==0 and $value['type']==1) $id="box_content_unread";
             else $id="box_content";
             ?>
             <tr>
              <td class="<?=$id;?>">
               <table border="0">
                <tr>
                 <td class="box_check"><input type="checkbox"  name="id_del_box[]" value="<?=$value['id'];?>" /></td>
                 <?
                 if($this->box_group==1){
                  ?>
                  <td class="box_img"></td>
                  <td class="box_text"><a href="<?=$this->script;?>&amp;task=show_box_letter_detail&amp;item_letter=<?=$value['id'];?>" class="a01" title="<?=$this->Msg->Show_text('TXT_FRONT_BOX_DETAILS');?>"><?=$this->GetSubStrCutByWorld($value['text'], 0, 30);?>...</a></td><?
                 }
                 if($this->box_group==2) {
                  ?>
                  <td class="box_img"></td>
                  <td class="box_text"><a href="<?=$this->script;?>&amp;&task=edit&id=<?=$value['from'];?>" title="<?=$this->Msg->Show_text('TXT_FRONT_SEE_PROFILE');?>" target="_blank"><?=$this->GetUserNickNameByUserId($value['from']);?></a></td><?
                 }
                 if($this->box_group==3){
                  ?>
                  <td class="box_img"><a href="<?=$this->script;?>&amp;task=show_box_photo_detail&amp;item_letter=<?=$value['id'];?>" class="a01" title="<?=$this->Msg->Show_text('TXT_FRONT_BOX_DETAILS');?>"><?=$this->ShowImageBox($value['img'], $this->id, 'size_auto=50', '75', NULL, 'border="0"');?></a></td>
                  <td class="box_text"><a href="<?=$this->script;?>&amp;task=show_box_photo_detail&amp;item_letter=<?=$value['id'];?>" class="a01" title="<?=$this->Msg->Show_text('TXT_FRONT_BOX_DETAILS');?>"><?=$this->GetSubStrCutByWorld($value['text'], 0, 30);?>...</a></td><?
                 }
                 ?>
                 <td class="box_from">
                  <?
                  if($this->box_group==1 OR $this->box_group==3)  {echo $this->GetUserNickNameByUserId($value['from']);}
                  else {
                      if ($this->box_type==2) echo $this->GetUserNickNameByUserId($value['from']);
                      /* ?><a href="<?=$href;?>" class="a01" title="<?=$this->Msg->Show_text('TXT_FRONT_SEE_PROFILE');?>"><?=$this->GetUserNickNameByUserId($value['from']);?></a><? */
                  }
                  ?>
                 </td>
                 <td class="box_date"><?=$value['date'];?></td>
                </tr>
               </table>
              </td>
             </tr>
             <?
         }// end foreach
         ?>
         </table>
          <div class="otstup_padding">
          <?/*
           <div class="box_check"><input onclick="javascript: CheckAll(<?=count($arr);?>);" type="checkbox" value="1" name="all" class="in"></div>
          */?>
           <?=$this->Form->Button("submit",  $this->Msg->show_text('BTN_BOX_DELETE_LETTERS') );?>
          </div>
         </div>
        <?
        $this->Form->WriteFooter();
       } //end of fuinction ShowLetters()


       // ================================================================================================
       // Function : ShowLettersDetail()
       // Version : 1.0.0
       // Date : 29.01.2007
       //
       // Parms :   group   - group of the letter (1-letter, 2-kiss, 3-photo)
       //           type    - type of letter (1-inbox, 2-outbox)
       // Returns :      true,false / Void
       // Description :  Show form with all letters (inbox or outbox)
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 29.01.2007
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function ShowLettersDetail($group, $type, $arr)
       {
           //echo '<br>$group='.$group.' $type='.$type.'$arr='.$arr;
           if( empty($type)){
               $type = $this->GetTypeOfLetter($this->item_letter);
           }
           switch($group){
             case '1':
                if ($type==1) $title = $this->Msg->Show_text('TXT_FRONT_BOX_INBOX').' '.$this->Msg->Show_text('TXT_BOX_LETTER_TITLE');
                else $title = $this->Msg->Show_text('TXT_FRONT_BOX_OUTBOX').' '.$this->Msg->Show_text('TXT_BOX_LETTER_TITLE');
                break;
             case '2':
                if ($type==1) $title = $this->Msg->Show_text('TXT_FRONT_BOX_INBOX').' '.$this->Msg->Show_text('TXT_BOX_KISSES_TITLE');
                else $title2 = $title = $this->Msg->Show_text('TXT_FRONT_BOX_OUTBOX').' '.$this->Msg->Show_text('TXT_BOX_KISSES_TITLE');
                break;
             case '3':
                $title = $this->Msg->Show_text('TXT_BOX_PHOTO_TITLE');
                if ($type==1) $title = $this->Msg->Show_text('TXT_FRONT_BOX_INBOX').' '.$this->Msg->Show_text('TXT_BOX_PHOTO_TITLE');
                else $title = $this->Msg->Show_text('TXT_FRONT_BOX_OUTBOX').' '.$this->Msg->Show_text('TXT_BOX_PHOTO_TITLE');
                break;
             default:
                $title = $this->Msg->Show_text('TXT_FRONT_BOX_TITLE');

         }
         ?>
         <h3><?=$title;?></h3>

         <div id="box_border">
         <?
         if ( !is_array($arr) OR count($arr)==0){
             $this->ShowTextMessages($this->Msg->Show_text('MSG_FRONT_BOX_NO_LETTERS'));
             return true;
         }
         foreach($arr as $key=>$value){
         ?>
          <div id="box_detail">
           <?
           if (empty($value['from'])){
               $this->ShowTextMessages($this->Msg->Show_text('MSG_FRONT_BOX_ADMIN_LETTER'));
               ?>
               <br/><?=$value['date'];?>
               <?
           }
           else{
           ?>
            <table border="0" cellpadding="0" cellspacing="0">
             <tr>
              <td clospan="2"><strong>
               <?
               if ($type==1) echo $this->Msg->Show_text('TXT_FRONT_BOX_FROM');
               else echo $this->Msg->Show_text('TXT_FRONT_BOX_TO');
               ?>:
               </strong>
              </td>
             </tr>
             <tr>
              <?
              $href = $this->UserValidSeeProfile($value['from']);
              //$href = $this->UserValidSendLetterKissPhoto( $value['from'], $group );

              $user_arr = $this->CotvertDataToOutputArray($this->GetUserData($value['from']),'id', 'asc', 'short');
              //print_r($user_arr);
              $user_arr[$value['from']]['img']['path'] = $this->GetMainImage($value['from'], 'front');
              if( !empty($user_arr[$value['from']]['img']['path'])) {
                  ?><td valign="top" align="center" ><table><tr><td class="img_others"><a href="<?=$href?>" title=""><?$this->ShowImage($user_arr[$value['from']]['img']['path'], $value['from'], 'size_auto=100', '85', NULL, 'border="0" class="img1"');?></a></td></tr></table></td><?
              }
              else {?><td valign="top" align="center" ><table><tr><td class="img_others"><img src="images/design/no_photo.jpg" border="0" alt="<?=$user_arr[$value['from']]['nickname'];?>" title="<?=$user_arr[$value['from']]['nickname'];?>"/></td></tr></table></td><?}
              ?>
              <td>
              <?
              if( isset($user_arr[$value['from']]['nickname']) AND !empty($user_arr[$value['from']]['nickname']) ){
               ?>
               <a href="<?=$href;?>" class="title"><?=$user_arr[$value['from']]['nickname'];?></a>
               <br/><?=$this->Msg->show_text('FLD_COUNTRY').': <b>'.$user_arr[$value['from']]['country'];?></b>
               <?if ( !empty($value['city'])) {
                  ?><br><?=$this->Msg->show_text('FLD_CITY').': <b>'.$user_arr[$value['from']]['city'].'</b>';
                 }
               ?>
               <br/><?=$this->Msg->show_text('TXT_AGE').': <b>'.$user_arr[$value['from']]['age'];?></b>
               <br/><?=$this->Msg->show_text('FLD_STATUS').': <b>'.$user_arr[$value['from']]['status'];?></b>
               <br/>
               <br/><?=$value['date'];?>
               <?
              }
              else echo $this->Msg->show_text('TXT_UNKNOWN_MEMBER');
              ?>
              </td>
             </tr>
            </table>
           <?}?>
           <div class="c03">
           <table>
           <?
           if ($group==3){
            ?>
            <tr>
             <td><?=$this->ShowImageBox($value['img'], $this->id, 'size_auto=600', '75', NULL, 'border="0"');?></td>
            </tr>
            <?
           }
           ?>
            <tr>
             <td><?=$value['text']?></td>
            </tr>
           </table>
           </div>
          </div>
         <?
         }// end foreach
         ?></div>
           <table border="0" cellpadding="0" cellspacing="10">
            <tr>
            <?/*
             <td><?=$this->Form->ShowButton($this->Msg->show_text('BTN_BOX_REPLY'), "send.php?task=show_send_letter_form&amp;profile=".$value['from'], 'width="100"', NULL);?></td>
             <td><?=$this->Form->ShowButton($this->Msg->show_text('TXT_FRONT_SEND_KISS'), "send.php?task=send_kiss_to&amp;profile=".$value['from'], 'width="200"', NULL);?></td>
             <td width="50%"></td>
             */?>
             <td align="right"><a href="<?=$this->script?>&amp;task=set_deleted_box_letter&amp;item_letter=<?$value['id'];?>"><?=$this->Msg->show_text('BTN_BOX_DELETE_LETTER');?></a></td>
            </tr>
           </table>
           </div>
         <?
       } //end of fuinction ShowLettersDetail()


//============================================================================================================
//========================================= USER BOX SECTION =================================================
//=========================================     END    =======================================================
//============================================================================================================

    function GetAboutUser(){
        if ( isset($_REQUEST['uid']) && !empty($_REQUEST['uid']) ){
            $id_user = (int)$_REQUEST['uid'];
            $id_user = addslashes( strip_tags( $id_user ));
            $q = "SELECT `aboutMe` FROM `".TblModUser."` WHERE sys_user_id='".$id_user."'";
            $res = $this->db->db_Query($q);
            if ( $res && $this->db->db_GetNumRows() > 0 ){
                $fetch = $this->db->db_FetchAssoc();
                echo stripslashes($fetch['aboutMe']);
            }
            else echo -1;
        }
        else echo 0;
    }

    function SaveAboutUser(){
        if ( isset($_REQUEST['about_user']) && isset($_REQUEST['uid']) && !empty($_REQUEST['uid']) ){
            $id_user = (int)$_REQUEST['uid'];
            $id_user = addslashes( strip_tags( $id_user ));
            $about_user = addslashes( strip_tags( trim( $_REQUEST['about_user'] ) ) );
            $q = "SELECT `id` FROM `".TblModUser."` WHERE sys_user_id='" . $id_user . "' ";
            $res = $this->db->db_Query($q);

            if ( $res && $this->db->db_GetNumRows() > 0 )
                $q = "UPDATE `".TblModUser."` SET `aboutMe`='" . $about_user ."' WHERE sys_user_id='" . $id_user . "' ";
            else
                $q = "INSERT INTO `".TblModUser."` SET `aboutMe`='" . $about_user ."' WHERE sys_user_id='" . $id_user . "' ";

            $res = $this->db->db_Query($q);
            if ( $res ) echo 1;
            else echo -1;
            //echo 'update about user / test / user id: '. $id_user;
        }
        else echo 0;
    }
 } // end of class UserCtrl
?>

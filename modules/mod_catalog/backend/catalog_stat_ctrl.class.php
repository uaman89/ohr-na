<?php
// ================================================================================================
// System : SEOCMS
// Module : catalog_stat_ctrl.class.php
// Version : 1.0.0
// Date : 06.08.2007
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : Class definition for all actions with control of catalog  statictic
//
// ================================================================================================

include_once( SITE_PATH.'/modules/mod_catalog/catalog.defines.php' );

// ================================================================================================
//    Class             : Catalog_Stat_Ctrl
//    Version           : 1.0.0
//    Date              : 06.08.2007
//
//    Constructor       : Yes
//    Parms             : session_id / session id
//                        usre_id    / UserID
//                        user_      /
//                        user_type  / id of group of user
//    Returns           : None                                               
//    Description       : Class definition for all actions with control of catalog  statictic
// ================================================================================================
//    Programmer        :  Igor Trokhymchuk
//    Date              :  06.08.2007
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================
 class Catalog_Stat_Ctrl extends Catalog_Stat {
          
       // ================================================================================================
       //    Function          : Catalog_Stat_Ctrl (Constructor)
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
       function Catalog_Stat_Ctrl ($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL) {
                //Check if Constants are overrulled
                ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
                ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
                ( $display  !="" ? $this->display = $display  : $this->display = 100   );
                ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
                ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
                ( $width    !="" ? $this->width   = $width    : $this->width   = 750  );

                $this-> lang_id = _LANG_ID;
                
                if (empty($this->db)) $this->db = new DB();
                if (empty($this->Right)) $this->Right = new Rights($this->user_id, $this->module);
                if (empty($this->Msg)) $this->Msg = new ShowMsg();
                $this->Msg->SetShowTable(TblModCatalogSprTxt);
                if (empty($this->Form)) $this->Form = new Form('form_mod_catalog_stat_ctrl');
                if (empty($this->Spr)) $this->Spr = new  SysSpr($this->user_id, $this->module);
                // add new tables
                $Catalog_category = new Catalog_category($this->user_id, $this->module);
                $Catalog_category->AddTbl();
       } // End of Catalog_Stat_Ctrl Constructor
       
       
       // ================================================================================================
       // Function : show
       // Date : 06.08.2007
       // Returns : true,false / Void
       // Description : Show data from $module table
       // Programmer : Igor Trokhymchuk
       // ================================================================================================
       function show()
       {
        //$script = $_SERVER['PHP_SELF'].'?module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr.'&fltr2='.$this->fltr2.'&fln='.$this->fln.'&id_cat='.$this->id_cat;
        $tmp_db = new DB();
        
        $settings = $this->GetSettings(); 
         
        if( !$this->sort ) $this->sort='id';
        $q = "SELECT * FROM `".TblModCatalogStatLog."` where 1";
        if( !empty($this->show_dt_from)) $q = $q." AND `dt`>='$this->show_dt_from'";
        if( !empty($this->show_dt_to)) $q = $q." AND `dt`<='$this->show_dt_to'";
        if( $this->srch ){
            $q = $q." AND `number_name` LIKE '%$this->srch%'";
            if( !empty($srch_str) ) $q = $q." AND `id` IN ($srch_str)";
        }
        if( $this->id_cat ) $q = $q." AND `id_cat`='$this->id_cat'";
        if( $this->fltr ) $q = $q." AND `id_manufac`='$this->fltr'"; 
        if( $this->fltr2 ) $q = $q." AND `id_cat`='$this->fltr2'";
        if( $this->fltr3 ) $q = $q." AND `id_group`='$this->fltr3'";
        $q = $q." order by `$this->sort`";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        //echo '<br>$q='.$q.' $res='.$res.'$this->Right->result='.$this->Right->result.' $this->user_id='.$this->user_id;
        if( !$res )return false;

        $rows = $this->Right->db_GetNumRows();

        /* Write Form Header */
        $this->Form->WriteHeader( $this->script );

   ?>
 <table border="0" cellpadding="5" cellspacing="0" width="100%">
  <tr>
   <td valign="top">
   <?

        $this->ShowContentFilters();

        /* Write Table Part */
        AdminHTML::TablePartH();

        
        /* Write Links on Pages */
        echo '<TR><TD COLSPAN="16">';
        $script1 = 'module='.$this->module.'&fltr='.$this->fltr;
        $script1 = $_SERVER['PHP_SELF']."?$script1";
        $this->Form->WriteLinkPages( $this->script, $rows, $this->display, $this->start, $this->sort );

        echo '<TR><TD COLSPAN="4">';
        $this->Form->WriteTopPanel( $this->script );

        //echo '<TD>';   $this->Spr->ShowActSprInCombo(TblModUserSprGroup, 'fltr2', $this->fltr2, "module=$this->module&task=showcontent"); 
        
        $script2 = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&task=show&fltr='.$this->fltr;
        $script2 = $_SERVER['PHP_SELF']."?$script2";
       ?>
        <TR>
        <Th class="THead">*</Th>
        <Th class="THead"><a href="<?=$script2?>&sort=id"><?=$this->Msg->show_text('FLD_ID')?></a></Th>
        <Th class="THead"><a href="<?=$script2?>&sort=id_cat"><?=$this->Msg->show_text('FLD_CATEGORY')?></a></Th>
        <?if ( isset($settings['name']) AND $settings['name']=='1' ) {?>
        <Th class="THead"><a href="<?=$script2?>&sort=id_prop"><?=$this->Msg->show_text('FLD_NAME')?></a></Th>
        <?}
        else{?>
        <Th class="THead"><a href="<?=$script2?>&sort=id_prop"><?=$this->Msg->show_text('FLD_NUMBER_NAME')?></a></Th> 
        <?}        
        if ( isset($settings['img']) AND $settings['img']=='1' ) {?>
        <Th class="THead"><a href="<?=$script2?>&sort=id_img"><?=$this->Msg->show_text('FLD_IMG')?></a></Th>
        <?}
        if ( isset($settings['files']) AND $settings['files']=='1' ) {?>
        <Th class="THead"><a href="<?=$script2?>&sort=id_file"><?=$this->Msg->show_text('FLD_FILE')?></a></Th>
        <?} 
        if ( isset($settings['manufac']) AND $settings['manufac']=='1' ) {?>
        <Th class="THead"><a href="<?=$script2?>&sort=id_manufac"><?=$this->Msg->show_text('FLD_MANUFAC')?></a></Th>
        <?}
        if ( isset($settings['id_group']) AND $settings['id_group']=='1' ) { ?>
        <Th class="THead"><a href="<?=$script2?>&sort=id_group"><?=$this->Msg->show_text('FLD_GROUP')?></a></Th>
        <?}?>
        <Th class="THead"><a href="<?=$script2?>&sort=time_gen"><?=$this->Msg->show_text('SYS_STAT_TIME_GEN', TblSysTxt)?></a></Th>         
        <Th class="THead"><a href="<?=$script2?>&sort=page"><?=$this->Msg->show_text('SYS_STAT_PAGE', TblSysTxt)?></a></Th>
        <Th class="THead"><a href="<?=$script2?>&sort=refer"><?=$this->Msg->show_text('SYS_STAT_REFER', TblSysTxt)?></a></Th>
        <Th class="THead"><a href="<?=$script2?>&sort=dt"><?=$this->Msg->show_text('SYS_STAT_DT', TblSysTxt)?></a></Th>
        <Th class="THead"><a href="<?=$script2?>&sort=tm"><?=$this->Msg->show_text('SYS_STAT_TM', TblSysTxt)?></a></Th>
        <Th class="THead"><a href="<?=$script2?>&sort=ip"><?=$this->Msg->show_text('SYS_STAT_IP', TblSysTxt)?></a></Th>
        <Th class="THead"><a href="<?=$script2?>&sort=host"><?=$this->Msg->show_text('SYS_STAT_HOST', TblSysTxt)?></a></Th>
        <Th class="THead"><a href="<?=$script2?>&sort=proxy"><?=$this->Msg->show_text('SYS_STAT_PROXY', TblSysTxt)?></a></Th>
        <Th class="THead"><a href="<?=$script2?>&sort=agent"><?=$this->Msg->show_text('SYS_STAT_USER_AGENT', TblSysTxt)?></a></Th>
        <Th class="THead"><a href="<?=$script2?>&sort=screen_res"><?=$this->Msg->show_text('SYS_STAT_SCREEN_RES', TblSysTxt)?></a></Th> 
        <Th class="THead"><a href="<?=$script2?>&sort=lang"><?=$this->Msg->show_text('SYS_STAT_TXT_LANG', TblSysTxt)?></a></Th> 
        <Th class="THead"><a href="<?=$script2?>&sort=country"><?=$this->Msg->show_text('SYS_STAT_TXT_CNTR', TblSysTxt)?></a></Th>
        <Th class="THead"><a href="<?=$script2?>&sort=cnt"><?=$this->Msg->show_text('SYS_STAT_COUNT', TblSysTxt)?></a></Th>
        <Th class="THead"><a href="<?=$script2?>&sort=id_user"><?=$this->Msg->show_text('SYS_STAT_USER', TblSysTxt)?></a></Th>
       <?

        $a = $rows;
        $j = 0;
        $up = 0;
        $down = 0;
        $row_arr = NULL;
        for( $i = 0; $i < $rows; $i++ )
        {
          $row = $this->Right->db_FetchAssoc();
          if( $i >= $this->start && $i < ( $this->start+$this->display ) )
          {
            $row_arr[$j] = $row;
            $j = $j + 1;
          }
        }

        $style1 = 'TR1';
        $style2 = 'TR2';
        for( $i = 0; $i < count( $row_arr ); $i++ )
        {
          $row = $row_arr[$i];

          if ( (float)$i/2 == round( $i/2 ) ) {?><tr class="<?=$style1;?>"><?}
          else {?><tr class="<?=$style2;?>"><? }

          ?><td><?
          $this->Form->CheckBox( "id_del[]", $row['id'] );

          ?><td><?
          $this->Form->Link( $this->script."&task=edit&id=".$row['id'], stripslashes( $row['id'] ), $this->Msg->show_text('_TXT_EDIT_DATA', TblSysTxt) );
          
          ?><td align="left" style="padding:5px;"><?
          echo $this->GetPathToLevel($row['id_cat']);
          //echo $this->Spr->GetNameByCod( TblModCatalogSprName, $row['id_cat'], $this->lang_id, 1 );          
          
          if ( isset($settings['name']) AND $settings['name']=='1' ) {
          ?><td style="padding:5px;"><?=$this->Spr->GetNameByCod( TblModCatalogPropSprName, $row['id_prop'], $this->lang_id, 1 );
          }
          else{
          ?><td style="padding:5px;"><?=$this->GetNumberName($row['id_prop']);
          }
          
          if ( isset($settings['img']) AND $settings['img']=='1' ) {   
          ?><td style="padding:5px;"><?
              if( $row['id_img']>0){
                $img_arr = $this->GetPictureData($row['id_img']);
                echo '/'.$img_arr['id_prop'].'/'.$img_arr['path'];
              }
          }
          
          if ( isset($settings['files']) AND $settings['files']=='1' ) {
          ?><td style="padding:5px;"><?
              if( $row['id_file']>0){
                $img_arr = $this->GetFileData($row['id_file']);
                echo '/'.$img_arr['id_prop'].'/'.$img_arr['path'];
              }          
          }
          
          if ( isset($settings['manufac']) AND $settings['manufac']=='1' ) {
          ?><td style="padding:5px;"><?=$this->Spr->GetNameByCod( TblModCatalogSprManufac, $row['id_manufac'], $this->lang_id, 1 );
          }
          
          if ( isset($settings['id_group']) AND $settings['id_group']=='1' ) {
          ?><td style="padding:5px;"><?if( $row['id_group']>0 ) echo $row['id_group'];
          }
          ?><td style="padding:5px;"><?=$row['time_gen'];
          ?><td align="left" style="padding:5px;"><?=$row['page'];
          ?><td align="left" style="padding:5px;"><?=$row['refer'];
          ?><td align="left" style="padding:2px;"><?=$row['dt'];
          ?><td align="left" style="padding:2px;"><?=$row['tm'];
          ?><td align="left" style="padding:2px;"><?=long2ip($row['ip']);
          ?><td style="padding:2px;"><?=$row['host'];
          ?><td style="padding:2px;"><?=$row['proxy'];
          
          ?><td><?
          $db = new DB();
          $q = "SELECT name FROM `".TblModStatAgent."` WHERE `id`='".$row['agent']."'";
          $tmp_res = $db->db_Query( $q );
          $tmp_row = $db->db_FetchAssoc( $tmp_res );
          echo $tmp_row['name'];
          
          ?><td><?=$row['screen_res'];
          ?><td><?=$row['lang']; 
          ?><td><?=$row['country'];
          
          ?><td><?=$row['cnt'];
          
          ?><td><?=$row['id_user'];
          $SysUser = new SysUser();
          $user_name = $SysUser->GetUserLoginByUserId($row['id_user']);
          if( !empty($user_name) ) echo '<br>'.$user_name;          
                   
        } //-- end for

        AdminHTML::TablePartF();
 ?>
   </td>
  </tr>
 </table>
 <?        
        $this->Form->WriteFooter();
        return true;

       } //end of fuinction show()  
       
       
       // ================================================================================================
       // Function : ShowContentFilters
       // Version : 1.0.0
       // Date : 27.03.2006
       //
       // Parms :
       // Returns : true,false / Void
       // Description : Show content of the catalogue
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 27.03.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function ShowContentFilters()
       { 
         $Date = new Date_Calc();
         $date_now = date("Y-m-d");
         $expole_date_now = explode("-", date("Y-m-d"));
         $one_month_prev = $Date->daysToDate( $Date->dateToDays($expole_date_now[2],$expole_date_now[1],$expole_date_now[0]) - $Date->daysInMonth($expole_date_now[1],$expole_date_now[0]) ,$format="%Y-%m-%d" );
         if( empty($this->show_dt_from) ) $this->show_dt_from = $one_month_prev; 
         //if( empty($this->show_dt_from) ) $this->show_dt_from = $Date->beginOfPrevMonth($expole_date_now[2],$expole_date_now[1],$expole_date_now[0],$format="%Y-%m-%d");
         if( empty($this->show_dt_to) ) $this->show_dt_to = $date_now; 
         
         if( empty($this->del_dt_from) ) $this->del_dt_from = '0000-00-00';
         if( empty($this->del_dt_to) ) $this->del_dt_to = '0000-00-00';
           
         /* Write Table Part */
         AdminHTML::TablePartH();
           //phpinfo();
         ?>
         <table border="0" cellpadding="0" cellspacing="0">
          <tr valign=top>
           <td>
             <table border=0 cellpadding=2 cellspacing=1>
              <tr><td><h4 style="margin:0px;"><?=$this->Msg->show_text('TXT_STAT_DEL_FOR_PERIOD');?></h4></td></tr>
              <tr class="tr1">
               <td align=left>
                <?echo $this->Msg->show_text('FLD_DATE').' '.$this->Msg->show_text('FLD_STAT_DATE_FROM').' '; $this->Form->Textbox('del_dt_from', $this->del_dt_from, 10); echo ' '.$this->Msg->show_text('FLD_STAT_DATE_TO').' '; $this->Form->Textbox('del_dt_to', $this->del_dt_to, 10);?></td>
               </td>
              </tr>
              <tr class="tr2">
               <td align="right"><?$this->Form->Button( 'del_for_period', $this->Msg->show_text('TXT_DELETE'), 50 );?></td>
              </tr>
             </table>
           </td>
           <td width=30></td>
           <td>
             <table border=0 cellpadding=2 cellspacing=1>
              <tr><td><h4 style="margin:0px;"><?=$this->Msg->show_text('TXT_STAT_SHOW_FOR_PERIOD');?></h4></td></tr>
              <tr class="tr1">
               <td align=left>
                <?echo $this->Msg->show_text('FLD_DATE').' '.$this->Msg->show_text('FLD_STAT_DATE_FROM').' '; $this->Form->Textbox('show_dt_from', $this->show_dt_from, 10); echo ' '.$this->Msg->show_text('FLD_STAT_DATE_TO').' '; $this->Form->Textbox('show_dt_to', $this->show_dt_to, 10);?></td>
               </td>
              </tr>
              <tr class="tr2">
               <td align="right"><?$this->Form->Button( '', $this->Msg->show_text('FLD_SHOW'), 50 );?></td>
              </tr>
             </table>
           </td>
          </tr>
         </table>
         <?
         AdminHTML::TablePartF();
       } //end of fuinction ShowContentFilters()       
       

       // ================================================================================================
       // Function : del()
       // Date : 22.03.2006
       // Parms : $id_del
       // Returns : true,false / Void
       // Description :  Remove data from the table
       // Programmer : Igor Trokhymchuk
       // ================================================================================================
       function del( $id_del )
       {
        $del = 0;
        $kol = count( $id_del );
        if($kol>0){
            for( $i=0; $i<$kol; $i++ ){
                $u=$id_del[$i];
                $q = "DELETE FROM `".TblModCatalogStatLog."` WHERE `id`='$u'";
                $res = $this->Right->Query( $q, $this->user_id, $this->module );
                //echo '<br>$q='.$q.' $res='.$res.' $this->Right->result='.$this->Right->result;
                if ( $res ) $del=$del+1;
                else return false;
            }
        }
        return $del;
       } //end of function del()
       
       // ================================================================================================
       // Function : DelForPeriod()
       // Version : 1.0.0
       // Date : 11.12.2007
       // Returns : true,false / Void
       // Description :  Remove data from the table for period
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 22.03.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function DelForPeriod()
       {
           $del = 0;
           if($this->del_dt_from=='0000-00-00' AND $this->del_dt_to=='0000-00-00') return false;
            
           $q = "SELECT `id` FROM `".TblModCatalogStatLog."` WHERE 1";
           $q = $q." AND `dt`>='$this->del_dt_from'";
           $q = $q." AND `dt`<='$this->del_dt_to'";
           //if( !empty($this->del_dt_from) AND $this->del_dt_from!='0000-00-00' ) $q = $q." AND `dt`>='$this->del_dt_from'";
           //if( !empty($this->del_dt_to) AND $this->del_dt_to!='0000-00-00' ) $q = $q." AND `dt`<='$this->del_dt_to'";
           $res = $this->Right->Query( $q, $this->user_id, $this->module );
           //echo '<br>$q='.$q.' $res='.$res.' $this->Right->result='.$this->Right->result;
           $rows = $this->Right->db_GetNumRows();
           
           $q = "DELETE FROM `".TblModCatalogStatLog."` WHERE 1";
           $q = $q." AND `dt`>='$this->del_dt_from'";
           $q = $q." AND `dt`<='$this->del_dt_to'";
           //if( !empty($this->del_dt_from) AND $this->del_dt_from!='0000-00-00' ) $q = $q." AND `dt`>='$this->del_dt_from'";
           //if( !empty($this->del_dt_to) AND $this->del_dt_to!='0000-00-00' ) $q = $q." AND `dt`<='$this->del_dt_to'";
           $res = $this->Right->Query( $q, $this->user_id, $this->module );
           //echo '<br>$q='.$q.' $res='.$res.' $this->Right->result='.$this->Right->result;
           if ( $res ) $del=$rows;
           else return false;            
           return $del;
       } //end of function DelForPeriod()       
             
 } //end of class Catalog_category     
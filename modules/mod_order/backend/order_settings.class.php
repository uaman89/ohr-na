<?php
include_once( SITE_PATH.'/modules/mod_order/order.defines.php' );

class Order_settings extends Order {
   // ================================================================================================
   //    Function          : Order_settings (Constructor)
   //    Description       : Opens and selects a dabase
   // ================================================================================================
   function Order_settings ($user_id=NULL, $module=NULL) {
            //Check if Constants are overrulled
            ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
            ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );

            if (empty($this->db)) $this->db = DBs::getInstance();
            if (empty($this->Right)) $this->Right = new Rights($this->user_id, $this->module);
            if (empty($this->Msg)) $this->Msg = new ShowMsg();
            $this->Msg->SetShowTable(TblModOrderSprTxt);
            if (empty($this->Form)) $this->Form = new Form('form_mod_order');
            if (empty($this->Spr)) $this->Spr = new  SysSpr($this->user_id, $this->module);

            $this->AddTable();

   } // End of Order_settings Constructor


   // ================================================================================================
   // Function : AddTable()
   // Version : 1.0.0
   // Date : 22.02.2010
   // Returns : true,false / Void
   // Description : show setting of Orderue
   // Programmer : Yaroslav Gyryn
   // ================================================================================================
   function AddTable()
   {
       // Add field nds to the table settings
       if ( !$this->db->IsFieldExist(TblModOrderSet, "nds") ) {
           $q = "ALTER TABLE `".TblModOrderSet."` ADD `nds` DEFAULT NULL;";
           $res = $this->db->db_Query( $q );
           //echo '<br>$q='.$q.' $res='.$res;
           if( !$res )return false;
       }
       return true;
       // CREATE TABLE `mod_order_set` (`nds` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL);
   }// end of function AddTable()


   // ================================================================================================
   // Function : ShowSettings()
   // Version : 1.0.0
   // Date : 22.02.2010
   // Parms :
   // Returns : true,false / Void
   // Description : show setting of Orderue
   // Programmer : Yaroslav Gyryn
   // ================================================================================================
   function ShowSettings()
   {
     $Panel = new Panel();
     $ln_sys = new SysLang();

     $script = $_SERVER['PHP_SELF'].'?module='.$this->module;

     $q="select * from `".TblModOrderSet."` where 1";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     //echo '<br>$q='.$q.' $res='.$res;
     if( !$this->Right->result ) return false;
     $row = $this->Right->db_FetchAssoc();

    /* Write Form Header */
    $this->Form->WriteHeader( $script );
    AdminHTML::PanelSimpleH();
    AdminHTML::PanelSimpleH();
    ?>
       <table border=0 cellspacing=1 cellpading=0 class="EditTable">
        <tr>
         <td><b><?=$this->Msg->show_text('TXT_NDS')?>:</b></td>
         <?$this->Err!=NULL ? $val=$this->nds : $val=$row['nds'];
           if ( trim($val)=='' ) $val = 0;?>
         <td align="left"><?=$this->Form->TextBox( 'nds', $val, 20 )?></td>
        </tr>
       </table>
       <?
       AdminHTML::PanelSimpleF();
       AdminHTML::PanelSimpleF();

    $this->Form->WriteSavePanel( $script );
    //$this->Form->WriteCancelPanel( $script );
    AdminHTML::PanelSimpleF();
    //AdminHTML::PanelSubF();

    $this->Form->WriteFooter();
    return true;
   } //end of function ShowSettings()

   // ================================================================================================
   // Function : SaveSettings()
   // Version : 1.0.0
   // Date : 22.02.2010
   // Returns : true,false / Void
   // Description : show setting of Orderue
   // Programmer : Yaroslav Gyryn
   // ================================================================================================
   function SaveSettings()
   {
    $q="select * from `".TblModOrderSet."` where 1";
    $res = $this->Right->Query( $q, $this->user_id, $this->module );
    if( !$this->Right->result ) return false;
    $rows = $this->Right->db_GetNumRows();

    if($rows>0)
    {
      $q="update `".TblModOrderSet."` set
          `nds`='$this->nds'
          ";
      $res = $this->Right->Query( $q, $this->user_id, $this->module );
      //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
      if( !$res  or  !$this->Right->result )
        return false;
    }
    else
    {
      $q="select * from `".TblModOrderSet."` where 1";
      $res = $this->Right->Query( $q, $this->user_id, $this->module );
      $rows = $this->Right->db_GetNumRows();
      if($rows>0) return false;

      $q="INSERT INTO `".TblModOrderSet."` SET
          `nds`='$this->nds'
           ";
      $res = $this->Right->Query( $q, $this->user_id, $this->module );
      if( !$this->Right->result)
        return false;
    }
    return true;
   } // end of function SaveSettings()

 } //end of class Order_settings
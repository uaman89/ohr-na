<?php
// ================================================================================================
// System : SEOCMS
// Module : catalog_response.class.php
// Version : 1.0.0
// Date : 21.03.2006
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : Class definition for all actions with managment of responsesof the goods from catalog
//
// ================================================================================================

include_once( SITE_PATH.'/modules/mod_catalog/catalog.defines.php' );

// ================================================================================================
//    Class             : CatalogResponse
//    Version           : 1.0.0
//    Date              : 21.03.2006
//
//    Constructor       : Yes
//    Parms             : session_id / session id
//                        usre_id    / UserID
//                        user_      /
//                        user_type  / id of group of user
//    Returns           : None                                               
//    Description       : Class definition for all actions with managment of responsesof the goods from catalog
// ================================================================================================
//    Programmer        :  Igor Trokhymchuk
//    Date              :  21.03.2006
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================
 class CatalogResponse extends Catalog {

     var $id = NULL;
     var $id_prop = NULL;  
     var $name = NULL;
     var $email = NULL;
     var $response = NULL;
     var $rating = NULL;
     var $dt = NULL;
     var $status = NULL;
     var $move = NULL; 
       
       // ================================================================================================
       //    Function          : CatalogResponse (Constructor)
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
       function CatalogResponse ($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL) {
                //Check if Constants are overrulled
                ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
                ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
                ( $display  !="" ? $this->display = $display  : $this->display = 20   );
                ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
                ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
                ( $width    !="" ? $this->width   = $width    : $this->width   = 750  );

                if (empty($this->db)) $this->db = new DB();
                if (empty($this->Right)) $this->Right = new Rights($this->user_id, $this->module);
                if (empty($this->Msg)) $this->Msg = new ShowMsg();
                $this->Msg->SetShowTable(TblModCatalogSprTxt);
                if (empty($this->Form)) $this->Form = new Form('form_mod_catalog_response');
                if (empty($this->Spr)) $this->Spr = new  SysSpr($this->user_id, $this->module);
       } // End of CatalogResponse Constructor
       
       // ================================================================================================
       // Function : show
       // Version : 1.0.0
       // Date : 21.03.2006
       //
       // Parms :
       // Returns : true,false / Void
       // Description : Show data from $module table
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 21.03.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function show()
       {
        $ModulesPlug = new ModulesPlug();
        $settings = $this->GetSettings();
        
        //echo '<br>$this->ajax_reload='.$this->ajax_reload;
        if($this->ajax_reload==1){
            $this->Msg->make_encoding = 1;
            $this->Msg->encoding_from = 'utf-8';
            $this->Msg->encoding_to = 'windows-1251';
            
            $this->Spr->make_encoding = 1;
            $this->Spr->encoding_from = 'utf-8';
            $this->Spr->encoding_to = 'windows-1251';
        }
        
        if( !$this->sort ) $this->sort='dt';
        $q = "SELECT * FROM ".TblModCatalogResponse." where 1";
        if ( !empty($this->fltr) ) $q = $q." AND `status`='$this->fltr'";
        if ( !empty($this->fltr2) ) $q = $q." AND `id_prop`='$this->fltr2'";
        $q = $q." order by $this->sort";
        if( $this->sort == 'dt' ) $q = $q.' desc';
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        //echo '<br>$q='.$q.' $res='.$res.'$this->Right->result='.$this->Right->result.' $this->user_id='.$this->user_id;
        if( !$res )return false;

        $rows = $this->Right->db_GetNumRows();

        ?><div id="content_response"><?  
        /* Write Form Header */
        $this->Form->WriteHeader( $this->script ); 
        
        //$txt = $this->Spr->GetNameByCod( TblModCatalogSprName, $this->level );
        //if ( !empty($txt) ) echo $txt ;
        
        if ( $this->level>0 ) $this->ShowPathToLevel($this->level, NULL, $this->script );

        /* Write Table Part */
        AdminHTML::TablePartH();

       
        /* Write Links on Pages */
        echo '<TR><TD COLSPAN=9>';
        $script1 = 'module='.$this->module.'&fltr='.$this->fltr;
        $script1 = $_SERVER['PHP_SELF']."?$script1";
        $this->Form->WriteLinkPages( $this->script, $rows, $this->display, $this->start, $this->sort );

        echo '<TR><TD COLSPAN=9>';
        $this->Form->WriteTopPanel( $this->script );        
        
        //$script2 = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&task=show&fltr='.$this->fltr;
        //$script2 = $_SERVER['PHP_SELF']."?$script2";
        //echo '<br>$this->ajax_reload='.$this->ajax_reload;
         if($rows>$this->display) $ch = $this->display;
        else $ch = $rows;
       ?>
        <TR>
        <Th class="THead"><input value="0" id="cAll" onclick="if (this.value == '1') {unCheckAll(<?=$ch;?>); this.value = '0';} else {checkAll(<?=$ch;?>); this.value = '1';}" type="checkbox"></Th>
        <Th class="THead"><A HREF=<?=$this->script?>&sort=id><?=$this->Msg->show_text('FLD_ID', NULL, 1)?></A></Th>
        <Th class="THead"><A HREF=<?=$this->script?>&sort=id_prop><?=$this->Msg->show_text('FLD_NAME')?></A></Th>
        <Th class="THead"><A HREF=<?=$this->script?>&sort=name><?=$this->Msg->show_text('FLD_USERNAME')?></A></Th>
        <?
        if ( isset($settings['responses']) AND $settings['responses']=='1' ){?>
        <Th class="THead"><A HREF=<?=$this->script?>&sort=response><?=$this->Msg->show_text('FLD_RESPONSE')?></A></Th> 
        <?}
        if ( isset($settings['rating']) AND $settings['rating']=='1' ){?>
        <Th class="THead"><A HREF=<?=$this->script?>&sort=rating><?=$this->Msg->show_text('FLD_RATING')?></A></Th> 
        <?}?>
        <Th class="THead"><A HREF=<?=$this->script?>&sort=status><?=$this->Msg->show_text('FLD_STATUS')?></A></Th> 
        <Th class="THead"><A HREF=<?=$this->script?>&sort=dt><?=$this->Msg->show_text('FLD_DATE')?></A></Th>                
        <Th class="THead"><A HREF=<?=$this->script?>&sort=move><?=$this->Msg->show_text('FLD_DISPLAY')?></A></Th>         
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

          if ( (float)$i/2 == round( $i/2 ) )
          {
           echo '<TR CLASS="'.$style1.'">';
          }
          else echo '<TR CLASS="'.$style2.'">';

          echo '<TD>';
          $this->Form->CheckBox( "id_del[]", $row['id'], null, "check".$i );   

          echo '<TD>';
          $this->Form->Link( $this->script."&task=edit&id=".$row['id'], stripslashes( $row['id'] ), $this->Msg->show_text('_TXT_EDIT_DATA', TblSysTxt) );

          ?><td><?
          $id_module = $ModulesPlug->GetModuleIdByPath('/modules/mod_catalog/catalog.backend.php');
          echo $this->GetPathToLevel( $this->GetCategory($row['id_prop']) );
          ?><br/><?
          $id_module = $ModulesPlug->GetModuleIdByPath('/modules/mod_catalog/catalogcontent.backend.php');
          if ( isset($settings['name']) AND $settings['name']=='1' ) $tmpname = stripslashes($this->Spr->GetNameByCod( TblModCatalogPropSprName, $row['id_prop'] ));
          else $tmpname = $this->GetNumberName($row['id_prop']);
            $this->Form->Link( $_SERVER['PHP_SELF'].'?module='.$id_module."&task=edit&id=".$row['id_prop'], $tmpname, $this->Msg->show_text('LNK_GO_TO_POSITION') );
          ?></td><?

          ?><td align="center"><?
          $name = stripslashes($row['name']);
          if($this->ajax_reload==1){
            $name = iconv($name, 'utf-8', 'windows-1251');
          }          
          echo $name;
          
          if( file_exists(SITE_PATH.'/modules/mod_user/user.defines.php') ){
              $User = new User();
              $id_user = $User->GetUserIdByEmail( stripslashes($row['email']) );
              if( !empty($id_user) ) {
                  $id_module = $ModulesPlug->GetModuleIdByPath( '/modules/mod_user/user.backend.php' );
                  ?><br/><?$this->Form->Link( $_SERVER['PHP_SELF'].'?module='.$id_module."&task=edit&id=".$id_user, stripslashes($row['email']), $this->Msg->show_text('LNK_GO_TO_USER') );
              }
          }
          else echo '<br>'.stripslashes($row['email']);
          
          if ( isset($settings['responses']) AND $settings['responses']=='1' ){
            ?><td align="center"><?  
            $val = stripslashes($row['response']);
            if($this->ajax_reload==1){
                $val = iconv($val, 'utf-8', 'windows-1251');
            }
            $this->GetSubStrCutByWorld($val, 0, 200);
            echo $val;
            if ( strlen($val)>200 ) echo '...';
          }
          
          if ( isset($settings['rating']) AND $settings['rating']=='1' ){
            echo '<TD>';  echo $row['rating'];
          }
          
          echo '<TD align="center">';
          $Page = new Page();
          $url = $Page->GetFunction( $this->module );
          if( !strstr($url,"?") ) $url = $url.'?module='.$this->module;
          else $url = $url.'&amp;module='.$this->module;

          //echo '<br>$url='.$url;
          
          if ( $row['status']==1 ) {
              echo $this->Msg->show_text('TXT_NOT_CHECKED');
              ?><br/><?
              $scriptlink = "&amp;task=moderate&amp;id=".$row['id']."&amp;status=2";
              $this->Form->Img( 'http://'.NAME_SERVER.'/admin/images/icons/publish_x.png', $this->Msg->show_text('TXT_UNVISIBLE'), 'border="0" class="href" onclick="makeRequest( '."'".$url."'".', '."'".$scriptlink."'".', '."'content_response'".' )"  onmouseover="return overlib('."'".$this->Msg->show_text('LNK_CHANGE_STATUS')."'".',WRAP);" onmouseout="nd();" ' );
              ?>&nbsp;&nbsp;<? 
              $scriptlink = "&amp;task=moderate&amp;id=".$row['id']."&amp;status=3"; 
              $this->Form->Img( 'http://'.NAME_SERVER.'/admin/images/icons/publish_g.png', $this->Msg->show_text('TXT_VISIBLE') , 'border="0" class="href" onclick="makeRequest( '."'".$url."'".', '."'".$scriptlink."'".', '."'content_response'".' )"  onmouseover="return overlib('."'".$this->Msg->show_text('LNK_CHANGE_STATUS')."'".',WRAP);" onmouseout="nd();" ' );
          }
          if ( $row['status']==2 ) {
              echo $this->Msg->show_text('TXT_UNVISIBLE').'<br/>';
              $scriptlink = "&amp;task=moderate&amp;id=".$row['id']."&amp;status=3";
              $this->Form->Img( 'http://'.NAME_SERVER.'/admin/images/icons/publish_x.png', $this->Msg->show_text('TXT_UNVISIBLE'), 'border="0" class="href" onclick="makeRequest( '."'".$url."'".', '."'".$scriptlink."'".', '."'content_response'".' )"  onmouseover="return overlib('."'".$this->Msg->show_text('LNK_CHANGE_STATUS')."'".',WRAP);" onmouseout="nd();" ' );

          }
          if ( $row['status']==3 ) {
              echo $this->Msg->show_text('TXT_VISIBLE').'<br/>';
              $scriptlink = "&amp;task=moderate&amp;id=".$row['id']."&amp;status=2"; 
              $this->Form->Img( 'http://'.NAME_SERVER.'/admin/images/icons/publish_g.png', $this->Msg->show_text('TXT_VISIBLE') , 'border="0" class="href" onclick="makeRequest( '."'".$url."'".', '."'".$scriptlink."'".', '."'content_response'".' )"  onmouseover="return overlib('."'".$this->Msg->show_text('LNK_CHANGE_STATUS')."'".',WRAP);" onmouseout="nd();" ' );
          }
          //$arr_v[0]=$this->Msg->show_text('TXT_NOT_CHECKED');
          //$arr_v[1]=$this->Msg->show_text('TXT_UNVISIBLE');
          //$arr_v[2]=$this->Msg->show_text('TXT_VISIBLE');
          //$this->Form->Select( $arr_v, 'status', $row['status'] );          
          
          echo '<TD align="center">'; echo $row['dt'];
                
           echo '<TD align=center>';
           if( $up!=0 )
           {
           ?>
            <a href=<?=$this->script?>&task=up&move=<?=$row['move']?>>
            <?=$this->Form->ButtonUp( $row['id'] );?>
            </a>
           <?
           }

           if( $i!=($rows-1) )
           {
           ?>
             <a href=<?=$this->script?>&task=down&move=<?=$row['move']?>>
             <?=$this->Form->ButtonDown( $row['id'] );?>
             </a>
           <?
           }
           
           $up=$row['id'];
           $a=$a-1;                    
        } //-- end for

        AdminHTML::TablePartF();
        $this->Form->WriteFooter();
        ?></div><?
        
        return true;

       } //end of fuinction show()  
       
       // ================================================================================================
       // Function : edit()
       // Version : 1.0.0
       // Date : 21.03.2006
       //
       // Parms : id/id of the record
       // Returns : true,false / Void
       // Description : Show data from $spr table for editing
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 21.03.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================

       function edit( $id=NULL )
       {
        $Panel = new Panel();
        $ln_sys = new SysLang();
        $mas=NULL;

        $fl = NULL;

        if( $id!=NULL and ( $mas==NULL ) )
        {
         $q="select * from `".TblModCatalogResponse."` where id='$id'";
         $res = $this->Right->Query( $q, $this->user_id, $this->module );
         if( !$this->Right->result ) return false;
         $mas = $this->Right->db_FetchAssoc();
        }

        /* Write Form Header */
        $this->Form->WriteHeaderFormImg( $this->script );
        
        if( $id!=NULL ) $txt = $this->Msg->show_text('_TXT_EDIT_DATA',TblSysTxt);
        else $txt = $this->Msg->show_text('_TXT_ADD_DATA',TblSysTxt);

        AdminHTML::PanelSubH( $txt );
        
        //-------- Show Error text for validation fields --------------
        $this->ShowErrBackEnd();
        //-------------------------------------------------------------        
        
        AdminHTML::PanelSimpleH();
       ?>
        <TABLE BORDER=0 class="EditTable">
        <TR><TD><b><?echo $this->Msg->show_text('FLD_ID')?>:</b>
        <TD width="80%">
       <?
          if( $id!=NULL )
          {
           echo $mas['id'];
           $this->Form->Hidden( 'id', $mas['id'] );
          }
          else $this->Form->Hidden( 'id', '' );

       ?>
        <TR><TD><b><?echo $this->Msg->show_text('FLD_NAME')?>:</b>
            <TD><b><?
             //$arr_prod = $this->GetArrModelsOfManufacForCategory (NULL, NULL, "move", "asc");
             $arr_prod = $this->GetCatalogInArray(NULL, '--- '.$this->Msg->show_text('TXT_SELECT_POSITIONS').' ---', NULL, NULL, 1, 'back');
             
             if( $id!=NULL ) $this->Err!=NULL ? $val=$this->id_prop : $val=$mas['id_prop'];
             else $val=$this->id_prop;             
             //$this->Form->Select( $arr_prod, 'id_prop', $val );
             $this->Form->Select( $arr_prod, "id_prop", "curcod=".$val,  NULL, 'onchange="CheckCatalogPosition(this, this.value, '."'".$this->Msg->show_text('ERR_SELECT_POSITION')."'".')"' );
             ?></b>
                     
        <TR><TD><b><?echo $this->Msg->show_text('FLD_USERNAME')?>:</b>
            <TD><b><?
             if( $id!=NULL ) $this->Err!=NULL ? $val=$this->name : $val=$mas['name'];
             else $val=$this->name;
             $this->Form->TextBox( 'name', stripslashes($val), 40 );             
             ?></b>

        <TR><TD><b><?echo $this->Msg->show_text('_FLD_EMAIL', TblSysTxt)?>:</b>
            <TD><b><?
             if( $id!=NULL ) $this->Err!=NULL ? $val=$this->email : $val=$mas['email'];
             else $val=$this->email;             
             $this->Form->TextBox( 'email', stripslashes($val), 40 ); 
             ?></b>            
            
        <TR><TD><b><?echo $this->Msg->show_text('FLD_RESPONSE')?>:</b>
            <TD><?
             if( $id!=NULL ) $this->Err!=NULL ? $val=$this->response : $val=$mas['response'];
             else $val=$this->response;              
             $this->Form->TextArea( 'response', stripslashes($val), 9, 70 );            
            ?>
            
        <TR><TD><b><?echo $this->Msg->show_text('FLD_RATING')?>:</b>
            <TD><?
             if( $id!=NULL ) $this->Err!=NULL ? $val=$this->rating : $val=$mas['rating'];
             else $val=$this->rating;              
             $this->Form->Radio( 'rating', '1', '1', $val ).'&nbsp;'; 
             $this->Form->Radio( 'rating', '2', '2', $val ).'&nbsp;';
             $this->Form->Radio( 'rating', '3', '3', $val ).'&nbsp;';
             $this->Form->Radio( 'rating', '4', '4', $val ).'&nbsp;';
             $this->Form->Radio( 'rating', '5', '5', $val );
             ?>
             
        <TR><TD><b><?echo $this->Msg->show_text('FLD_STATUS')?>:</b>
            <TD><?             
             $arr_v[1]=$this->Msg->show_text('TXT_NOT_CHECKED');
             $arr_v[2]=$this->Msg->show_text('TXT_UNVISIBLE');
             $arr_v[3]=$this->Msg->show_text('TXT_VISIBLE');
             if( $id!=NULL ) $this->Err!=NULL ? $val=$this->status : $val=$mas['status'];
             else $val=$this->status;           
             $this->Form->Select( $arr_v, 'status', $val );
             ?>
             
        <TR><TD><b><?echo $this->Msg->show_text('FLD_DATE')?>:</b>
            <TD><b><?
             if( $id!=NULL ) $this->Err!=NULL ? $val=$this->dt : $val=$mas['dt'];
             else $val=$this->dt;       
             $this->Form->TextBox( 'dt', stripslashes($val), 10 ); 
             ?></b>              
        <?                
        if ($id==NULL) {
         $arr = NULL;
         $arr['']='';
         $tmp_db = new DB();
         $tmp_q = "select * from `".TblModCatalog."` order by move desc";
         $res = $tmp_db->db_Query( $tmp_q );
         if( !$res )return false;
         $tmp_row = $tmp_db->db_FetchAssoc();
         $move = $tmp_row['move'];
         $move=$move+1;
         $this->Form->Hidden( 'move', $move );
        }
        else $move=$mas['move'];
        $this->Form->Hidden( 'move', $move );

        echo '<TR><TD COLSPAN=2 ALIGN=left>';
        $this->Form->WriteSavePanel( $this->script );
        $this->Form->WriteCancelPanel( $this->script );
        echo '</table>';
        AdminHTML::PanelSimpleF();
        AdminHTML::PanelSubF();

        $this->Form->WriteFooter();
        return true;
       } // end of function edit()
       
       // ================================================================================================
       // Function : ChecResponseFields()
       // Version : 1.0.0
       // Date : 21.03.2006
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
       function ChecResponseFields($id = NULL)
       {
        $settings = $this->GetSettings(); 
        $this->Err=NULL;

        if (empty( $this->id_prop )) {
            $this->Err=$this->Err.$this->Msg->show_text('MSG_FLD_NAME_EMPTY').'<br>';
        }          
        
        if (empty( $this->name )) {
            $this->Err=$this->Err.$this->Msg->show_text('MSG_FLD_USERNAME_EMPTY').'<br>';
        }        

        if (empty( $this->email )) {
            $this->Err=$this->Err.$this->Msg->show_text('MSG_FLD_EMAIL_EMPTY').'<br>';
        }
          
        if ( isset($settings['responses']) AND $settings['responses']=='1' ){
            if (empty( $this->response )) {
                $this->Err=$this->Err.$this->Msg->show_text('MSG_FLD_RESPONSE_EMPTY').'<br>';
            }          
        }
        if ( isset($settings['rating']) AND $settings['rating']=='1' ){
            if ( $this->rating==0 ) {
                $this->Err=$this->Err.$this->Msg->show_text('MSG_FLD_RATING_EMPTY').'<br>';
            }          
        }
        
        if (empty( $this->status )) {
            $this->Err=$this->Err.$this->Msg->show_text('MSG_FLD_STATUS_EMPTY').'<br>';
        }        
        
        //echo '<br>$this->Err='.$this->Err.' $this->Msg->table='.$this->Msg->table;
        return $this->Err;
       } //end of fuinction ChecResponseFields()        
       
       // ================================================================================================
       // Function : save()
       // Version : 1.0.0
       // Date : 22.03.2006
       //
       // Parms :   $user_id, $module, $id, $group_menu, $level, $description, $function, $move
       // Returns : true,false / Void
       // Description : Store data to the table
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 22.03.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================

       function save( $id )
       {

        $q="select * from `".TblModCatalogResponse."` where id='$id'";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        if( !$this->Right->result ) return false;
        $rows = $this->Right->db_GetNumRows();
        $row = $this->Right->db_FetchAssoc();
        //phpinfo();
        
        if($rows>0)
        {
          $q="update `".TblModCatalogResponse."` set
              `id_prop`='".$this->id_prop."',
              `name`='".$this->name."',
              `email`='".$this->email."',
              `response`='".$this->response."',
              `rating`='".$this->rating."',
              `dt`='".$this->dt."',
              `status`='".$this->status."'";
          $q=$q." where id='".$id."'";
          $res = $this->Right->Query( $q, $this->user_id, $this->module );
          echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result; 
          if( !$res ) return false; 
          if( !$this->Right->result ) return false;
        }
        else
        {
          $q="SELECT MAX(`move`) FROM `".TblModCatalogResponse."` WHERE 1";
          $res = $this->db->db_Query( $q );
          //$rows = $this->db->db_GetNumRows();
          $row = $this->db->db_FetchAssoc();
          $maxx = $row['MAX(`move`)'];
          $maxx=$maxx+1; 

           $q = "INSERT INTO `".TblModCatalogResponse."` SET
                `id_prop`='".$this->id_prop."',
                `name`='".$this->name."',
                `email`='".$this->email."',
                `response`='".$this->response."',
                `rating`='".$this->rating."',
                `dt`='".$this->dt."',
                `status`='".$this->status."',
                `move`='".$maxx."',
                `ip`='".$this->ip."'";
          $res = $this->Right->Query( $q, $this->user_id, $this->module );
          echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
          if( !$this->Right->result) return false;
        }

        return true;
       } // end of function save()       

       // ================================================================================================
       // Function : del()
       // Version : 1.0.0
       // Date : 22.03.2006
       // Parms :   $user_id, $module_id, $id_del
       // Returns : true,false / Void
       // Description :  Remove data from the table
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 22.03.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================

       function del( $id_del )
       {
        $del = 0;
        $kol = count( $id_del );
        for( $i=0; $i<$kol; $i++ )
        {
         $u=$id_del[$i];
         
          $q = "delete from ".TblModCatalogResponse." where id='".$u."'";
          $res = $this->Right->Query( $q, $this->user_id, $this->module );
          if( !$res ) return false;
          if ( $res )
           $del=$del+1;
          else
           return false;
        }         
         return $del;           
       } //end of function del()
        
       // ================================================================================================
       // Function : Moderate()
       // Version : 1.0.0
       // Date : 09.08.2007
       //
       // Parms :
       // Returns : true,false / Void
       // Description : moderate responses
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 09.08.2007 
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function Moderate()
       {
        $q="select * from `".TblModCatalogResponse."` where id='".$this->id."'";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result; 
        if( !$this->Right->result ) return false;
        $rows = $this->Right->db_GetNumRows();
        $row = $this->Right->db_FetchAssoc();
        //phpinfo();
        if($rows>0)
        {
          $q="UPDATE `".TblModCatalogResponse."` SET `status`='".$this->status."' WHERE id='".$row['id']."'";
          $res = $this->Right->Query( $q, $this->user_id, $this->module );
          //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result; 
          if( !$res ) return false; 
          if( !$this->Right->result ) return false;
        }

        return true;
       } // end of function Moderate()            
        
        
        
       // ================================================================================================
       // Function : SaveResponse()
       // Version : 1.0.0
       // Date : 02.06.2006
       //
       // Parms :       $name - name of the user
       //               $email - email of the user
       //               $response - response of the goods
       //               $rating - rating of the goods
       // Returns :      true/false
       // Description :  Return as links path of the categories to selected level of catalogue
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 10.01.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function SaveResponse()
       {
          $q="SELECT MAX(`move`) FROM `".TblModCatalogResponse."` WHERE 1";
          $res = $this->db->db_Query( $q );
          //$rows = $this->db->db_GetNumRows();
          $row = $this->db->db_FetchAssoc();
          $maxx = $row['MAX(`move`)'];
          $maxx=$maxx+1;           
           
          $q = "INSERT INTO `".TblModCatalogResponse."` SET
                `id_prop`='".$this->id_prop."',
                `name`='".$this->name."',
                `email`='".$this->email."',
                `response`='".$this->response."',
                `rating`='".$this->rating."',
                `dt`='".$this->dt."',
                `status`='".$this->status."',
                `move`='".$maxx."',
                `ip`='".$this->ip."'";
          $res = $this->db->db_Query( $q );
          //echo '<br>q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
          if( !$this->db->result) return false;            
          return true;
       }  //end of function SaveResponse()     
        
       
 } // end of class CatalogResponse 

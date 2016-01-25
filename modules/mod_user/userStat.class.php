<?php
// ================================================================================================
// System : SEOCMS
// Module : userStat.class.php
// Version : 1.0.0
// Date : 10.01.2006
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : Class definition For all actions with first step of registration. This is the statistic part.
//
// ================================================================================================

include_once( SITE_PATH.'/modules/mod_user/user.defines.php' );

// ================================================================================================
//    Class             : User
//    Version           : 1.0.0
//    Date              : 10.01.2006
//
//    Constructor       : Yes
//    Parms             :
//    Returns           : None
//    Description       : Class definition For all actions with first step of registration. This is the statistic part.
// ================================================================================================
//    Programmer        :  Igor Trokhymchuk
//    Date              :  10.01.2006
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================
 class UserStat {
       var $Err = NULL;
       var $module = NULL;
       var $session_id = NULL;
                        
       var $sort = NULL;
       var $display = 20;
       var $start = 0;
       var $fln = NULL;
       var $width = '100%';
       var $spr = NULL;
       var $srch = NULL;

       var $db = NULL;
       var $Msg = NULL;
       var $Right = NULL;
       var $Form = NULL;
                        
       var $pr1 = NULL;
       var $pr2 = NULL;
       var $pr3 = NULL;
       var $pr4 = NULL;
       var $pr5 = NULL;
       var $pr6 = NULL;
       var $pr7 = NULL;
       var $pr8 = NULL;
       var $pr9 = NULL;
       var $pr10 = NULL;
       var $pr11 = NULL;
       var $pr12 = NULL;
       var $pr13 = NULL;
       var $pr14 = NULL;
       var $subscr = NULL;
       var $subscr1 = NULL;

       // ================================================================================================
       //    Function          : UserStat (Constructor)
       //    Version           : 1.0.0
       //    Date              : 10.01.2006
       //    Parms             :
       //    Returns           : Error Indicator
       //
       //    Description       : Init variables
       // ================================================================================================
       function UserStat( $user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL, $session_id = NULL ) {
                ( $session_id   !="" ? $this->session_id  = $session_id   : $this->session_id  = NULL );
                ( $user_id      !="" ? $this->user_id     = $user_id      : $this->user_id     = NULL );

                if (empty($this->db)) $this->db = new db();
                if (empty($this->Msg)) $this->Msg = new ShowMsg();
                $this->Msg->SetShowTable(TblModUserSprTxt);
                if (empty($this->Right)) $this->Right = new Rights($this->user_id, $this->module);
                if (empty($this->Form)) $this->Form = new Form('form_mod_userstat');
       } // End of UserStat Constructor

       
       // ================================================================================================
       // Function : show
       // Version : 1.0.0
       // Date : 30.01.2006
       //
       // Parms :
       // Returns : true,false / Void
       // Description : Show data from $module table
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 30.01.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function show()
       {
        $User = new User();
        $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr.'&fln='.$this->fln;
        $script = $_SERVER['PHP_SELF']."?$script";

        if ( !empty($this->srch) ) {
           $q = "SELECT id FROM ".TblSysUser." WHERE login='$this->srch' ";
           $res = $this->Right->Query( $q, $this->user_id, $this->module );
           //echo '<br>$q='.$q.' $res='.$res.'$this->Right->result='.$this->Right->result.' $this->user_id='.$this->user_id;
           if ( !$res ) return false;
           if ( !$this->Right->result ) return false;
           $row = $this->Right->db_FetchAssoc();
           $id_user=$row['id'];
        }
        
        if( !$this->sort ) $this->sort='dt';
        //if( strstr( $this->sort, 'seria' ) )$this->sort = $this->sort.' desc';
        $q = "SELECT * FROM ".TableStatReg." where 1 ";
        if( $this->srch ) $q = $q." and id_user=$id_user ";
        if( $this->fltr ) $q = $q." and $this->fltr";
        $q = $q." order by $this->sort desc";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        //echo '<br>$q='.$q.' $res='.$res.'$this->Right->result='.$this->Right->result.' $this->user_id='.$this->user_id;
        if( !$res ) return false;

        $rows = $this->Right->db_GetNumRows();

        //echo '<br> this->srch ='.$this->srch.' $script='.$script;

        /* Write Form Header */
        $this->Form->WriteHeader( $script );

        /* Write Table Part */
        AdminHTML::TablePartH();

        /* Write Links on Pages */
        echo '<TR><TD COLSPAN=17>';
        $script1 = 'module='.$this->module.'&fltr='.$this->fltr;
        $script1 = $_SERVER['PHP_SELF']."?$script1";
        $this->Form->WriteLinkPages( $script1, $rows, $this->display, $this->start, $this->sort );

        echo '<TR><TD COLSPAN=3>';
        $this->Form->WriteTopPanel( $script, 2 );

        echo '<td colspan=5>';
        echo $this->Form->TextBox('srch', $this->srch, 25);
        echo '<input type=submit value='.$this->Msg->show_text('_BUTTON_SEARCH',TblSysTxt).'>';

        /*
        echo '<td><td><td><td><td colspan=2>';
        $this->Form->WriteSelectLangChange( $script, $this->fln);
        */

        $script2 = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&task=show&fltr='.$this->fltr;
        $script2 = $_SERVER['PHP_SELF']."?$script2";
       ?>
        <TR>
        <td class="THead">*</Th>
        <td class="THead"><A HREF=<?=$script2?>&sort=id><?=$this->Msg->show_text('FLD_ID')?></A></Th>
        <td class="THead"><A HREF=<?=$script2?>&sort=dt><?=$this->Msg->show_text('FLD_STAT_DATE')?></A></Th>
        <td class="THead"><A HREF=<?=$script2?>&sort=id_user><?=$this->Msg->show_text('FLD_STAT_USER_LOGIN')?></A></Th>
        <td class="THead"><A HREF=<?=$script2?>&sort=pr1><?=$this->Msg->show_text('TXT_STAT_QUESTION1_ANSWER1')?></A></Th>
        <td class="THead"><A HREF=<?=$script2?>&sort=pr2><?=$this->Msg->show_text('TXT_STAT_QUESTION1_ANSWER2')?></A></Th>
        <td class="THead"><A HREF=<?=$script2?>&sort=pr3><?=$this->Msg->show_text('TXT_STAT_QUESTION1_ANSWER3')?></A></Th>
        <td class="THead"><A HREF=<?=$script2?>&sort=pr4><?=$this->Msg->show_text('TXT_STAT_QUESTION1_ANSWER4')?></A></Th>
        <td class="THead"><A HREF=<?=$script2?>&sort=pr5><?=$this->Msg->show_text('TXT_STAT_QUESTION1_ANSWER5')?></A></Th>
        <td class="THead"><A HREF=<?=$script2?>&sort=pr6><?=$this->Msg->show_text('TXT_STAT_QUESTION1_ANSWER6')?></A></Th>
        <td class="THead"><A HREF=<?=$script2?>&sort=pr7><?=$this->Msg->show_text('TXT_STAT_QUESTION1_ANSWER7')?></A></Th>
        <td class="THead"><A HREF=<?=$script2?>&sort=pr8><?=$this->Msg->show_text('TXT_STAT_QUESTION1_ANSWER8')?></A></Th>
        <td class="THead"><A HREF=<?=$script2?>&sort=pr9><?=$this->Msg->show_text('TXT_STAT_QUESTION2_ANSWER1')?></A></Th>
        <td class="THead"><A HREF=<?=$script2?>&sort=pr10><?=$this->Msg->show_text('TXT_STAT_QUESTION2_ANSWER2')?></A></Th>
        <td class="THead"><A HREF=<?=$script2?>&sort=pr11><?=$this->Msg->show_text('TXT_STAT_QUESTION2_ANSWER3')?></A></Th>
        <?


        $a = $rows;
        $j = 0;
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
          $this->Form->CheckBox( "id_del[]", $row['id'] );

          echo '<TD>'.$row['id'];
          
          //$this->Form->Link( $script."&task=edit&id=".$row['id'], stripslashes( $row['id'] ), $this->Msg->show_text('_TXT_EDIT_DATA', TblSysTxt) );

          echo '<TD align=center>'.$row['dt'].'</TD>';

          echo '<TD align=center>';
          if( trim( $row['id_user'] )!='' ) echo $User->GetUserLoginByUserId($row['id_user']);

          echo '<TD align=center>';
          if( trim( $row['pr1'] )!='' ) echo $row['pr1'];

          echo '<TD align=center>';
          if( trim( $row['pr2'] )!='' ) echo $row['pr2'];

          echo '<TD align=center>';
          if( trim( $row['pr3'] )!='' ) echo $row['pr3'];

          echo '<TD align=center>';
          if( trim( $row['pr4'] )!='' ) echo $row['pr4'];

          echo '<TD align=center>';
          if( trim( $row['pr5'] )!='' ) echo $row['pr5'];

          echo '<TD align=center>';
          if( trim( $row['pr6'] )!='' ) echo $row['pr6'];

          echo '<TD align=center>';
          if( trim( $row['pr7'] )!='' ) echo $row['pr7'];

          echo '<TD align=center>';
          if( trim( $row['pr8'] )!='' ) echo $row['pr8'];

          echo '<TD align=center>';
          if( trim( $row['pr9'] )!='' ) echo $row['pr9'];

          echo '<TD align=center>';
          if( trim( $row['pr10'] )!='' ) echo $row['pr10'];

          echo '<TD align=center>';
          if( trim( $row['pr11'] )!='' ) echo $row['pr11'];

        } //-- end for

        AdminHTML::TablePartF();
        $this->Form->WriteFooter();
        return true;

       } //end of fuinction show
       
       // ================================================================================================
       // Function : del()
       // Version : 1.0.0
       // Date : 30.01.2006
       //
       // Parms :
       // Returns :      true,false / Void
       // Description :  Remove data from the table
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 30.01.2006
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
            $q = "select * from ".TableStatReg." where id='$u'";
            $res = $this->Right->Query( $q, $this->user_id, $this->module );
            $row = $this->Right->db_FetchAssoc();

            $q = "DELETE FROM `".TableStatReg."` WHERE id='$u'";
            $res = $this->Right->Query( $q, $this->user_id, $this->module );

            if ( $res )
             $del=$del+1;
            else
             return false;
           }
         return $del;
       } //end of fuinction del()
       
              
       
/*====================================================================================================================================
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
- - - - - - - - - - - - - - - - -  FRONT-END Part Fuctions - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
====================================================================================================================================*/ 
              
       
       // ================================================================================================
       // Function : ShowStat
       // Version : 1.0.0
       // Date : 13.05.2005
       //
       // Parms :
       // Returns : true,false / Void
       // Description : Show the first step of registration. This is the statistic part.
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 13.05.2005
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function ShowStat()
       {
        echo '<table width="100%" align=center border=0 cellpading=0 cellspacing=0>
               <tr>
                <td colspan=3><H3>'.$this->Msg->show_text('TXT_STEP1_REG').'</H3></td>
               </tr>
               <tr><td></td></tr>
               <tr>
                <td><br>'.$this->Msg->show_text('TXT_STAT_QUESTION1').'<span style="color:#FF0000">*</span></td>
               </tr>
               <tr>
                <td>
                <table width="100%" border=0>
                 <tr><td><input type="checkbox" align="left" name="pr1" '; if ($this->pr1=='on') echo ' CHECKED ';
                 echo '></td><td width="100%">'.$this->Msg->show_text('TXT_STAT_QUESTION1_ANSWER1').'</td></tr>
                 <tr><td><input type="checkbox" align="left" name="pr2" '; if ($this->pr2=='on') echo ' CHECKED ';
                 echo '></td><td>'.$this->Msg->show_text('TXT_STAT_QUESTION1_ANSWER2').'</td></tr>
                 <tr><td><input type="checkbox" align="left" name="pr3" '; if ($this->pr3=='on') echo ' CHECKED ';
                 echo '></td><td>'.$this->Msg->show_text('TXT_STAT_QUESTION1_ANSWER3').'</td></tr>
                 <tr><td><input type="checkbox" align="left" name="pr4" '; if ($this->pr4=='on') echo ' CHECKED ';
                 echo '></td><td>'.$this->Msg->show_text('TXT_STAT_QUESTION1_ANSWER4').'</td></tr>

                 <tr><td><input type="checkbox" align="left" name="pr5" '; if ($this->pr5=='on') echo ' CHECKED ';
                 echo '></td><td >'.$this->Msg->show_text('TXT_STAT_QUESTION1_ANSWER5').'</td><td align=left width=100%><input type="text" name="pr6" value="'.$this->pr6.'" size="35"></td></tr>

                 <tr><td><input type="checkbox" align="left" name="pr7" '; if ($this->pr7=='on') echo ' CHECKED ';
                 echo '></td><td>'.$this->Msg->show_text('TXT_STAT_QUESTION1_ANSWER6').'</td></tr>

                 <tr><td><input type="checkbox" align="left" name="pr8" '; if ($this->pr8=='on') echo ' CHECKED ';
                 echo '></td><td>'.$this->Msg->show_text('TXT_STAT_QUESTION1_ANSWER7').'</td><td align=left width=100%><input type="text" name="pr14" value="'.$this->pr14.'" class="textbox_user" size="35"></td></tr>

                 <tr><td><input type="checkbox" align="left" name="pr9" '; if ($this->pr9=='on') echo ' CHECKED ';
                 echo '></td><td>'.$this->Msg->show_text('TXT_STAT_QUESTION1_ANSWER8').'</td><td width="100%"><input type="text" name="pr10" value="'.$this->pr10.'" size="35"></td></tr>
                </table>
                </td>
               </tr>
               <tr>
                <td><br>'.$this->Msg->show_text('TXT_STAT_QUESTION2').'<span style="color:#FF0000">*</span></td>
               </tr>
               <tr><td></td></tr>
               <tr>
                <td><div>1. <input type=text name=pr11 value="'.$this->pr11.'"></div></td>
               </tr>
               <tr>
                <td><div>2. <input type=text name=pr12 value="'.$this->pr12.'"></div></td>
               </tr>
                              <tr>
                <td><div>3. <input type=text name=pr13 value="'.$this->pr13.'"></div></td>
               </tr>
               <tr>
                <td><br>'.$this->Msg->show_text('TXT_STAT_QUESTION3').'<span style="color:#FF0000">*</span></td>
               </tr>
               <tr>
                <td>
                <table width="100%" border=0>
                 <tr><td><input type="checkbox" align="left" name="subscr" '; if ($this->subscr==1) echo ' CHECKED ';
                 echo '></td><td width="100%">'.$this->Msg->show_text('TXT_STAT_QUESTION3_ANSWER1').'</td></tr>
                 <tr><td><input type="checkbox" align="left" name="subscr1" '; if ($this->subscr1==1) echo ' CHECKED ';
                 echo '></td><td>'.$this->Msg->show_text('TXT_STAT_QUESTION3_ANSWER2').'</td></tr>
                </table>
                </td>
               </tr>
               <tr><td colspan=3 align=center><input type=submit name=save_stat value="'.$this->Msg->show_text('BTN_STAT_CONTINUE').'">  <input type=reset name=reset value="'.$this->Msg->show_text('BTN_RESET').'">
              </table>';
         return true;
       } //end of function ShowStat()


       // ================================================================================================
       // Function : CheckFields
       // Version : 1.0.0
       // Date : 11.01.2006
       //
       // Parms :
       // Returns : true,false / Void
       // Description : hecking all fields for filling and validation
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 1.01.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function CheckFields()
       {
         $this->Err = NULL;
                if (!$this->pr1 & !$this->pr2 & !$this->pr3 & !$this->pr4 & !$this->pr5 & !$this->pr6 & !$this->pr14 & !$this->pr8 & !$this->pr9 & !$this->pr10) $this->Err = $this->Err.$this->Msg->show_text('MSG_STAT_NOT_ANSWER_FIRST_QUESTION')."<br>";
                if (!empty($this->pr5) & empty($this->pr6)) $this->Err = $this->Err.$this->Msg->show_text('MSG_STAT_ANSWER_FIRST_QUESTION_MORE_DETAIL')."<br>";
                if (!empty($this->pr8) & empty($this->pr14)) $this->Err = $this->Err.$this->Msg->show_text('MSG_STAT_ANSWER_FIRST_QUESTION_MORE_DETAIL')."<br>";
                if (!empty($this->pr9) & empty($this->pr10)) $this->Err = $this->Err.$this->Msg->show_text('MSG_STAT_ANSWER_FIRST_QUESTION_MORE_DETAIL')."<br>";
                if (!$this->pr11 & !$this->pr12 & !$this->pr13) $this->Err = $this->Err.$this->Msg->show_text('MSG_STAT_NOT_ANSWER_SECOND_QUESTION')."<br>";
                if (!$this->user_subscr & !$this->user_subscr1) $this->Err = $this->Err.$this->Msg->show_text('MSG_STAT_NOT_ANSWER_THIRD_QUESTION')."<br>";
         return $this->Err;
       } //end of function CheckFields()

       // ================================================================================================
       // Function : SaveStat
       // Version : 1.0.0
       // Date : 13.05.2005
       //
       // Parms :
       // Returns : true,false / Void
       // Description : Save first step of registraion to the database to the table ltw_strat_reg.
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 13.05.2005
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function SaveStat()
       {
         $date = date("Y-m-d");
         if ( empty($this->pr6) ) $pr5=$this->pr5;
         else $pr5=$this->pr6;
         if ( empty($this->pr14) ) $pr8=$this->pr8;
         else $pr8=$this->pr14;
         if ( empty($this->pr10) ) $pr9=$this->pr9;
         else $pr9=$this->pr10;
         $q="Insert into `".TableStatReg."` values(NULL,'0','$date',NULL,'$this->pr1','$this->pr2','$this->pr3','$this->pr4','$pr5','$this->pr7',
            '$pr8','$pr9','$this->pr11','$this->pr12','$this->pr13')";
         $res = $this->db->db_Query($q);
         //echo '<br> q='.$q.' $this->db->result='.$this->db->result;
         if( !$res ) return false;
         if (!$this->db->result) return false;
         $new_stat_id = $this->db->db_GetInsertID();
         return $new_stat_id;
       } //end of function SaveStat

       // ================================================================================================
       // Function : UpdateStat
       // Version : 1.0.0
       // Date : 11.01.2006
       //
       // Parms :
       // Returns : true,false / Void
       // Description : Save first step of registraion to the database to the table ltw_strat_reg.
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 13.05.2005
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function UpdateStat($new_stat_id = NULL, $sys_user_id = NULL)
       {
          $q="UPDATE `".TableStatReg."` set id_user='".$sys_user_id."' where id='".$new_stat_id."'";
          $res = $this->db->db_Query($q);
          //echo '<br> q='.$q.' $this->db->result='.$this->db->result;
          if( !$res ) return false;
          if( !$this->db->result ) return false;
          return true;
       } //end of function UpdateStat
 } //end of class UserStat
?>
<?php
include_once( SITE_PATH.'/admin/include/defines.inc.php' );

/**
* Class SysSettingsAdm
* Class definition for all actions with system settings on the back-end
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/
class SysSettingsAdm extends SysSettings {

       /**
       * SysSettingsAdm::__construct()
       *
       * @param integer $user_id
       * @param integer $module_id
       * @param integer $display
       * @param string $sort
       * @param integer $start
       * @param integer $width
       * @param integer $spr
       * @return void
       */
       function __construct($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL, $spr=NULL) {
                //Check if Constants are overrulled
                ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
                ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
                ( $display  !="" ? $this->display = $display  : $this->display = 20   );
                ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
                ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
                ( $width    !="" ? $this->width   = $width    : $this->width   = 750  );
                ( $spr      !="" ? $this->spr     = $spr      : $this->spr     = NULL  );

                if ( defined("_LANG_ID") ) $this->lang_id = _LANG_ID;

                if (empty($this->db)) $this->db = DBs::getInstance();
                if (empty($this->Right)) $this->Right = new Rights($this->user_id, $this->module);
                if (empty($this->Msg)) $this->Msg = check_init_txt('TblBackMulti',TblBackMulti);
                if (empty($this->Form)) $this->Form = new Form('form_sys_set');
                if (empty($this->Spr)) $this->Spr = new SysSpr($this->user_id, $this->module);

       } // End of SysSettingsAdm Constructor

  // ================================================================================================
  //    Function          : Show()
  //    Version           : 1.0.0
  //    Date              : 14.03.2005
  //    Parms             :
  //    Returns           : true/false
  //    Description       : Show Statistic Log
  // ================================================================================================

  function Show($res = '' )
  {
   //$this->AddTbl();
   $maildata= $this->GetMailTxtData();
   $Panel = new Panel();
   $script = 'module='.$this->module;
   $script = $_SERVER['PHP_SELF']."?$script";
   $q = "SELECT * FROM ".TblSysSetGlobal." WHERE `id`='1'";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   if( !$res ) return false;
   $mas = $this->Right->db_FetchAssoc();
   $txt = $this->Msg['TXT_EDIT'];
   AdminHTML::PanelSubH( $txt );
   // Write Form Header
   $this->Form->WriteHeader( $script );

   $settings=SysSettings::GetGlobalSettings();
   $this->Form->textarea_editor = $settings['editer']; //'tinyMCE';
   $this->Form->IncludeSpecialTextArea( $this->Form->textarea_editor );

   $Panel->WritePanelHead( "SubPanel_" );

   /*========================== Tab 1 START ================================*/
   //$Panel->WritePanelHead( "SubPanel_" );
   $Panel->WriteItemHeader( $this->Msg['SYS_SET_FRONT_SETTINGS'] );
   ?>
   <table class="ContentTable" border="0" width="100%">
       <tr class="tr1">
           <td valign="top">
               <b><?=$this->Msg['SYS_SET_SITE_STATUS'];?>:</b><br/>
               <?
               ( !empty($this->Err) ? $val = $this->site_status : $val = $mas['site_status'] );
               ?>
               <input type="radio" id="site_status_1" name="site_status" value="1" <?if($val){echo 'checked';}?>/> <label for="site_status_1"><?=$this->Msg['TXT_ONLINE'];?></label><br/>
               <input type="radio" id="site_status_2" name="site_status" value="0" <?if(!$val){echo 'checked';}?>/> <label for="site_status_2"><?=$this->Msg['TXT_OFFLINE'];?></label><br/><br/>
               <?=$this->Msg['SYS_SET_SITE_STATUS_HELP'];?>
           </td>
           <td>
               <?
                $Panel->WritePanelHead( 'site_off_message' );
                $ln_arr = SysLang::$LangArray;
                while( $el = each( $ln_arr ) ){
                    $lang_id = $el['key'];
                    $lang = $el['value'];
                    $mas_s[$lang_id] = $lang;
                    $Panel->WriteItemHeader( $lang );
                    ?>
                    <table border="0" class="EditTable">
                        <tr>
                            <td><b><?=$this->Msg['SYS_SET_SITE_OFFLINE_MESSAGE'];?>:</b></td>
                        </tr>
                        <tr>
                            <td>
                                <?
                                ( !empty($this->Err) ? $val = $this->site_off_message[$lang_id] : $val = $maildata[$lang_id]['site_off_message'] );
                                //$this->Form->HTMLTextArea( 'mail_header['.$lang_id.']', $val, 7, 80 );
                                $this->Form->SpecialTextArea( $this->Form->textarea_editor, 'site_off_message['.$lang_id.']', stripslashes($val), 20, 80, 'style="width:100%;"', $lang_id, 'site_off_message');
                                ?>
                            </td>
                        </tr>
                    </table>
                    <?
                    $Panel->WriteItemFooter();
                }
                $Panel->WritePanelFooter();
                ?>
           </td>
       </tr>
       <tr class="tr2">
           <td align="left" width="150" height="25"><?=$this->Msg['SYS_SET_CODES_BEFORE_END_OF_HEAD'];?>:</td>
           <?( !empty($this->Err) ? $val = $this->site_codes_head : $val = $mas['site_codes_head'] );?>
           <td align="left"><?=$this->Form->Textarea( 'site_codes_head', stripslashes($val), 20, 30);?></td>
       </tr>
       <tr class="tr1">
           <td align="left" width="150" height="25"><?=$this->Msg['SYS_SET_CODES_BEFORE_END_OF_BODY'];?>:</td>
           <?( !empty($this->Err) ? $val = $this->site_codes_body : $val = $mas['site_codes_body'] );?>
           <td align="left"><?=$this->Form->Textarea( 'site_codes_body', stripslashes($val), 20, 30);?></td>
       </tr>
   </table>
   <?
   $Panel->WriteItemFooter();
   /*========================== Tab 1 END ================================*/

   /*========================== Tab 2 START ================================*/
   //$Panel->WritePanelHead( "SubPanel_" );
   $Panel->WriteItemHeader( $this->Msg['SYS_SET_REDACTORS'] );
   ?>
    <table class="ContentTable" border="0" width="100%">
      <tr class="tr1">
         <td align="left" width="150" height="25"><?=$this->Msg['SYS_SET_EDITER_SELECT'];?>:</td>
         <td align="left"><select name="editer">
                 <option <?if($mas['editer']=="TinyMCE") echo "selected "?> value="TinyMCE">TinyMCE</option>
                 <option <?if($mas['editer']=="FCK") echo "selected "?> value="FCK">FCK</option>
                 <option <?if($mas['editer']=="elrte") echo "selected "?> value="elrte">elrte</option>
             </select></td>
      </tr>
   </table>
   <?
   $Panel->WriteItemFooter();
  /*========================== Tab 2 END ================================*/

   /*========================== Tab 3 START ================================*/
   $Panel->WriteItemHeader( $this->Msg['SYS_SET_MAIL'] );
   // Write Simple Panel
   AdminHTML::PanelSimpleH();

   $this->Form->Hidden( 'id', $mas['id'] );

   ?>
   <table class="ContentTable" border="0" width="100%">
   <?/*
    <tr>
     <td><?=$this->Msg['FLD_ID'];?></td>
     <td><?=$mas['id']; ?></td>
    </tr>
    */?>
    <tr class="tr1">
     <td align="left" height="25"><?=$this->Msg['SYS_SET_MAIL_MAILER'];?>:</td>
     <?( !empty($this->Err) ? $val = $this->mail_mailer : ( !empty($mas['mail_mailer']) ? $val = $mas['mail_mailer'] : $val = 'smtp' ) );?>
     <td align="left"><?=$this->Form->TextBox( 'mail_mailer', stripslashes($val), 30 );?></td>
     <td align="left"><?=$this->Msg['SYS_SET_MAIL_MAILER_HELP'];?></td>
    </tr>
    <tr class="tr2">
     <td align="left" width="150" height="25"><?=$this->Msg['SYS_SET_MAIL_HOST'];?>:</td>
     <?( !empty($this->Err) ? $val = $this->mail_host : $val = $mas['mail_host'] );?>
     <td align="left" width="300"><?=$this->Form->TextBox( 'mail_host', stripslashes($val), 30 );?></td>
     <td align="left"><?=$this->Msg['SYS_SET_MAIL_HOST_HELP'];?></td>
    </tr>
    <tr class="tr1">
     <td align="left" height="25"><?=$this->Msg['SYS_SET_MAIL_PORT'];?>:</td>
     <?( !empty($this->Err) ? $val = $this->mail_port : ( !empty($mas['mail_port']) ? $val = $mas['mail_port'] : $val = 25 ) );?>
     <td align="left"><?=$this->Form->TextBox( 'mail_port', stripslashes($val), 3 );?></td>
     <td align="left"></td>
    </tr>
    <tr class="tr2">
     <td align="left" height="25"><?=$this->Msg['SYS_SET_MAIL_SMTP_AUTH'];?>:</td>
     <?( !empty($this->Err) ? $val = $this->mail_smtp_auth : $val = $mas['mail_smtp_auth'] );?>
     <td align="left"><?=$this->Form->CheckBox( "mail_smtp_auth", '1', $val );?></td>
     <td align="left"></td>
    </tr>
    <tr class="tr1">
     <td align="left" height="25"><?=$this->Msg['SYS_SET_MAIL_USERNAME'];?>:</td>
     <?( !empty($this->Err) ? $val = $this->mail_username : $val = $mas['mail_username'] );?>
     <td align="left"><?=$this->Form->TextBox( 'mail_username', stripslashes($val), 30 );?></td>
     <td align="left"></td>
    </tr>
    <tr class="tr2">
     <td align="left" height="25"><?=$this->Msg['SYS_SET_MAIL_PASSWORD'];?>:</td>
     <?( !empty($this->Err) ? $val = $this->mail_password : $val = $mas['mail_password'] );?>
     <td align="left"><?=$this->Form->TextBox( 'mail_password', stripslashes($val), 30 );?></td>
     <td align="left"></td>
    </tr>
    <tr class="tr1">
     <td align="left" height="25"><?=$this->Msg['SYS_SET_MAIL_FROM'];?>:</td>
     <?( !empty($this->Err) ? $val = $this->mail_from : $val = $mas['mail_from'] );?>
     <td align="left"><?=$this->Form->TextBox( 'mail_from', stripslashes($val), 30 );?></td>
     <td align="left"></td>
    </tr>
    <tr class="tr1">
     <td align="left" colspan="3">
      <?
      $Panel->WritePanelHead( 'mail_data' );
      $ln_arr = SysLang::$LangArray;
      while( $el = each( $ln_arr ) ){
        $lang_id = $el['key'];
        $lang = $el['value'];
        $mas_s[$lang_id] = $lang;
        $Panel->WriteItemHeader( $lang );
        ?>
        <table border="0" class="EditTable">
         <tr>
          <td><b><?=$this->Msg['SYS_SET_MAIL_FROM_NAME'];?>:</b></td>
         </tr>
         <tr>
          <td>
           <?
           ( !empty($this->Err) ? $val = $this->mail_from_name[$lang_id] : $val = $maildata[$lang_id]['from'] );
           $this->Form->TextBox( 'mail_from_name['.$lang_id.']', $val, 80 );
           ?>
          </td>
         </tr>
         <tr>
         <tr>
          <td><b><?=$this->Msg['SYS_SET_MAIL_HEADER'];?>:</b></td>
         </tr>
         <tr>
          <td>
           <?
           ( !empty($this->Err) ? $val = $this->mail_header[$lang_id] : $val = $maildata[$lang_id]['head'] );
           //$this->Form->HTMLTextArea( 'mail_header['.$lang_id.']', $val, 7, 80 );
           $this->Form->SpecialTextArea( $this->Form->textarea_editor, 'mail_header['.$lang_id.']', stripslashes($val), 20, 70, 'style="width:100%;"', $lang_id, 'mail_header');
           ?>
          </td>
         </tr>
         <tr>
          <td><b><?=$this->Msg['SYS_SET_MAIL_FOOTER'];?>:</b></td>
         </tr>
         <tr>
          <td>
           <?
           ( !empty($this->Err) ? $val = $this->mail_footer[$lang_id] : $val = $maildata[$lang_id]['foot'] );
           //$this->Form->HTMLTextArea( 'mail_footer['.$lang_id.']', $val, 7, 80 );
           $this->Form->SpecialTextArea( $this->Form->textarea_editor, 'mail_footer['.$lang_id.']', stripslashes($val), 20, 70, 'style="width:100%;"', $lang_id, 'mail_footer');
           ?>
          </td>
         </tr>
        </table>
        <?
        $Panel->WriteItemFooter();
      }
      $Panel->WritePanelFooter();
      ?>
     </td>
    </tr>
    <tr class="tr1">
     <td align="left" height="25"><?=$this->Msg['SYS_SET_MAIL_WORD_WRAP'];?>:</td>
     <?( !empty($this->Err) ? $val = $this->mail_word_wrap : ( !empty($mas['mail_word_wrap']) ? $val = $mas['mail_word_wrap'] : $val = 50 ) );?>
     <td align="left"><?=$this->Form->TextBox( 'mail_word_wrap', stripslashes($val), 3 );?></td>
     <td align="left"><?=$this->Msg['SYS_SET_MAIL_WORD_WRAP_HELP'];?></td>
    </tr>
    <tr class="tr2">
     <td align="left" height="25"><?=$this->Msg['SYS_SET_MAIL_IS_HTML'];?>:</td>
     <?( !empty($this->Err) ? $val = $this->mail_is_html : $val = $mas['mail_is_html'] );?>
     <td align="left"><?=$this->Form->CheckBox( "mail_is_html", '1', $val );?></td>
     <td align="left"></td>
    </tr>
    <tr class="tr1">
     <td align="left" height="25"><?=$this->Msg['SYS_SET_MAIL_PRIORITY'];?>:</td>
     <?( !empty($this->Err) ? $val = $this->mail_priority : ( !empty($mas['mail_priority']) ? $val = $mas['mail_priority'] : $val = 3 ) );?>
     <td align="left"><?=$this->Form->TextBox( 'mail_priority', stripslashes($val), 3 );?></td>
     <td align="left"><?=$this->Msg['SYS_SET_MAIL_PRIORITY_HELP'];?></td>
    </tr>
    <tr class="tr2">
     <td align="left" height="25"><?=$this->Msg['SYS_SET_MAIL_CHARSET'];?>:</td>
     <?( !empty($this->Err) ? $val = $this->mail_charset : ( !empty($mas['mail_charset']) ? $val = $mas['mail_charset'] : $val = 'utf-8' ) );?>
     <td align="left"><?=$this->Form->TextBox( 'mail_charset', stripslashes($val), 30 );?></td>
     <td align="left"></td>
    </tr>
    <tr class="tr1">
     <td align="left" height="25"><?=$this->Msg['SYS_SET_MAIL_ENCODING'];?>:</td>
     <?( !empty($this->Err) ? $val = $this->mail_encoding : ( !empty($mas['mail_encoding']) ? $val = $mas['mail_encoding'] : $val = '8bit' ) );?>
     <td align="left"><?=$this->Form->TextBox( 'mail_encoding', stripslashes($val), 30 );?></td>
     <td align="left"><?=$this->Msg['SYS_SET_MAIL_ENCODING_HELP'];?></td>
    </tr>
    <tr class="tr2">
     <td align="left" height="25"><?=$this->Msg['SYS_SET_AUTO_EMAILS'];?></td>
     <?( !empty($this->Err) ? $val = $this->mail_auto_emails : $val = $mas['mail_auto_emails'] );?>
     <td align="left"><?=$this->Form->Textarea( 'mail_auto_emails', stripslashes($val), 6, 30, 'style="width:100%;"' );?></td>
     <td align="left"><?=$this->Msg['SYS_SET_AUTO_EMAILS_HELP'];?></td>
    </tr>
    <tr class="tr1">
     <td align="left" height="25"><?=$this->Msg['SYS_SET_ADMIN_EMAIL'];?>:</td>
     <?( !empty($this->Err) ? $val = $this->mail_admin_email : $val = $mas['mail_admin_email'] );?>
     <td align="left"><?=$this->Form->TextBox( 'mail_admin_email', stripslashes($val), 30 );?></td>
     <td align="left"><?=$this->Msg['SYS_SET_ADMIN_EMAIL_HELP'];?></td>
    </tr>
   </table>
   <?
   AdminHTML::PanelSimpleF();

   $Panel->WriteItemFooter();
   /*========================== Tab 3 END ================================*/

   /*========================== Tab 4 START ================================*/
   //$Panel->WritePanelHead( "SubPanel_" );
   $Panel->WriteItemHeader( 'robots.txt' );
   ?>
   <table class="ContentTable" border="0" width="100%">
       <tr class="tr1">
           <td align="left" width="150" height="25">robots.txt:</td>
           <?
           $fpath = SITE_PATH.'/robots.txt';
           if(!file_exists($fpath)){
               $val = '';
           }
           else{
               $handle = fopen($fpath, 'r');
               $val = fread($handle, filesize($fpath));
               fclose($handle);
           }
           ?>
           <td align="left"><?=$this->Form->Textarea( 'robots_txt', stripslashes($val), 40, 30 );?></td>
       </tr>
   </table>
   <?
   $Panel->WriteItemFooter();
  /*========================== Tab 4 END ================================*/

   $Panel->WritePanelFooter();
    ?><div class="space"></div><?
   if($this->Right->IsUpdate($this->module)) $this->Form->WriteSavePanel( $script );
   $this->Form->WriteFooter();
   AdminHTML::PanelSubF();
  } //--- end of Show()


// ================================================================================================
// Function : Save
// Version : 1.0.0
// Date : 19.12.2007
//
// Parms :
// Returns : true,false / Void
// Description : Store data to the table
// ================================================================================================
// Programmer : Igor Trokhymchuk
// Date : 19.12.2007
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
function Save()
{
   $q = "SELECT * FROM ".TblSysSetGlobal." WHERE `id`='1'";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   if( !$res OR !$this->Right->result ) return false;
   $rows = $this->Right->db_GetNumRows();
   if( $rows > 0 ){
    $q = "UPDATE ".TblSysSetGlobal." SET
          `mail_host`='".$this->mail_host."',
          `mail_port`='".$this->mail_port."',
          `mail_mailer`='".$this->mail_mailer."',
          `mail_smtp_auth`='".$this->mail_smtp_auth."',
          `mail_username`='".$this->mail_username."',
          `mail_password`='".$this->mail_password."',
          `mail_from`='".$this->mail_from."',
          `mail_word_wrap`='".$this->mail_word_wrap."',
          `mail_is_html`='".$this->mail_is_html."',
          `mail_priority`='".$this->mail_priority."',
          `mail_charset`='".$this->mail_charset."',
          `mail_encoding`='".$this->mail_encoding."',
          `mail_auto_emails`='".$this->mail_auto_emails."',
          `mail_admin_email`='".$this->mail_admin_email."',
          `editer`='".$this->editer."',
          `site_codes_head`='".addslashes($this->site_codes_head)."',
          `site_codes_body`='".addslashes($this->site_codes_body)."',
          `site_status`='".$this->site_status."'
          WHERE `id`='1'";
    $res = $this->Right->Query( $q, $this->user_id, $this->module );
    //echo '<br>$q='.$q.' <br/>$res='.$res.' $this->Right->result='.$this->Right->result;
    if( !$res OR !$this->Right->result ) return false;


   }else{
    $q = "INSERT INTO ".TblSysSetGlobal." SET
          `id`='1',
          `mail_host`='".$this->mail_host."',
          `mail_port`='".$this->mail_port."',
          `mail_mailer`='".$this->mail_mailer."',
          `mail_smtp_auth`='".$this->mail_smtp_auth."',
          `mail_username`='".$this->mail_username."',
          `mail_password`='".$this->mail_password."',
          `mail_from`='".$this->mail_from."',
          `mail_word_wrap`='".$this->mail_word_wrap."',
          `mail_is_html`='".$this->mail_is_html."',
          `mail_priority`='".$this->mail_priority."',
          `mail_charset`='".$this->mail_charset."',
          `mail_encoding`='".$this->mail_encoding."',
          `mail_auto_emails`='".$this->mail_auto_emails."',
          `mail_admin_email`='".$this->mail_admin_email."',
          `editer`='".$this->editer."',
          `site_codes_head`='".$this->site_codes_head."',
          `site_codes_body`='".$this->site_codes_body."',
          `site_status`='".$this->site_status."'
         ";
    $res = $this->Right->Query( $q, $this->user_id, $this->module );
    //echo '<br>$q='.$q.' $res='.$res.' $this->Right->result='.$this->Right->result;

    if( !$res OR !$this->Right->result ) return false;
   }


   $ln_sys = check_init('LangSys','SysLang');
    //print_r($description_arr);
   $ln_arr = $ln_sys->LangArray( _LANG_ID );
   while( $el = each( $ln_arr ) )
   {
       $lang_id = $el['key'];
       $q = "SELECT * FROM ".TblSysSetGlobalSprMail." WHERE `lang_id`='".$lang_id."'";
       $res = $this->Right->Query( $q, $this->user_id, $this->module );
       if( !$res OR !$this->Right->result ) return false;
       $rows = $this->Right->db_GetNumRows();
       if( $rows > 0 ){
           $q="UPDATE `".TblSysSetGlobalSprMail."` SET
                `from`='".$this->mail_from_name[$lang_id]."',
                `head`='".$this->mail_header[$lang_id]."',
                `foot`='".$this->mail_footer[$lang_id]."',
                `site_off_message`='".$this->site_off_message[$lang_id]."'
                WHERE `lang_id`='".$lang_id."'";
       }else{
           $q="INSERT INTO `".TblSysSetGlobalSprMail."` SET
                `lang_id`='".$lang_id."',
                `from`='".$this->mail_from_name[$lang_id]."',
                `head`='".$this->mail_header[$lang_id]."',
                `foot`='".$this->mail_footer[$lang_id]."',
                `site_off_message`='".$this->site_off_message[$lang_id]."'
               ";
       }
      $res = $this->db->db_Query($q);
      //echo '<br>$q='.$q.' $res='.$res.' $$this->Rights->result='.$this->db->result;
      if( !$this->db->result ) return false;
   } //--- end while

    $filename = SITE_PATH.'/robots.txt';

    //return true;
    if (is_writable($filename)) {
        if (!$handle = fopen($filename, 'w')) {
             echo "Не могу открыть файл ($filename)";
             exit;
        }
        //echo '<br>$this->robots_txt='.$this->robots_txt;
        $this->robots_txt = str_replace('\r\n', "\n", $this->robots_txt);
        //echo '<br>$this->robots_txt='.$this->robots_txt;
        // Записываем $somecontent в наш открытый файл.
        if (fwrite($handle, $this->robots_txt) === FALSE) {
            echo "Не могу произвести запись в файл ($filename)";
            exit;
        }
        fclose($handle);

    }else {
        echo "Файл $filename недоступен для записи";
    }

    return true;
} //end of fuinction Save()


 }  //end of class SysSettingsAdm
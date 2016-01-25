<?php
/* ================================================================================================
* System : SEOCMS
* Module : userBlog_ctrl.class.php
* @Version : 1.0.0
* @Date : 13.05.2011
* Licensed To:
* Sergey Panarin sp@seotm.com
*
* Purpose : Class definition for all actions with managment of content of the blog
*
* ================================================================================================
*/
include_once( SITE_PATH.'/modules/mod_user/user.defines.php' );

/* ================================================================================================
 *   Class             : UserBlogCtrl
 *   Version           : 1.0.0
 *   Date              : 13.05.2011
*
 *   Constructor       : Yes
 *   Parms             : session_id / session id
 *                       usre_id    / UserID
 *                       user_      /
 *                       user_type  / id of group of user
 *   Returns           : None                                               
 *   Description       : Class definition for all actions with managment of content of the user blog 
 *================================================================================================
 *   Programmer        :  Sergey Panarin
 *   Date              :  13.05.2011
 *   Reason for change :  Creation
 *   Change Request Nbr:  N/A
 *================================================================================================
 */
 class UserBlogCtrl extends User{

       /* ================================================================================================
       *    Function          : UserBlogCtrl (Constructor)
       *    Version           : 1.0.0
       *    Date              : 13.05.2011
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
       function UserBlogCtrl ($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL) {
                //Check if Constants are overrulled
                ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
                ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
                ( $display  !="" ? $this->display = $display  : $this->display = 20   );
                ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
                ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
                ( $width    !="" ? $this->width   = $width    : $this->width   = 750  );

                $this-> lang_id = _LANG_ID;
                
                if (empty($this->db)) $this->db = new DB();
                if (empty($this->Right)) $this->Right = new Rights($this->user_id, $this->module);
                if (empty($this->Msg)) $this->Msg = new ShowMsg();
                $this->Msg->SetShowTable(TblModUserSprTxt);
                $this->multi = $this->Msg->GetMultiTxtInArr(TblModUserSprTxt);
                if (empty($this->Form)) $this->Form = new Form('form_mod_user_blog_ctrl');
                if (empty($this->Spr)) $this->Spr = new  SysSpr($this->user_id, $this->module);
       } // End of UserBlogCtrl Constructor
        function GetContent($limit='limit')
       {       
            $tmp_db = new DB();
            if( !$this->sort ) $this->sort='dttm';
            $this->asc_desc="";
            if( !$this->asc_desc || empty($this->asc_desc) || !isset($this->asc_desc)) $this->asc_desc='DESC';
            $q = "SELECT * FROM `".TblModUserBlog."` where `id_user`=".$this->sys_user_id;
//            if( $this->srch ){
//                if( !empty($srch_str) ) $q = $q." AND (`number_name` LIKE '%".addslashes(htmlspecialchars_decode($this->srch))."%' OR `id` IN (".$srch_str."))";
//                else $q = $q." AND `number_name` LIKE '%".addslashes(htmlspecialchars_decode($this->srch))."%'";
//            }
            
            $q = $q." ORDER BY `$this->sort` $this->asc_desc";
            if($limit=='limit') $q = $q." LIMIT ".$this->start.", ".$this->display;
           // echo $q;
            $res = $this->Right->Query( $q, $this->user_id, $this->module );
            //echo '<br>$q='.$q.' $res='.$res.' $this->Right->result='.$this->Right->result.' $this->user_id='.$this->user_id;
            if( !$res )return false;
            return true;
       }
       /* ================================================================================================
       * @Function : ShowContent
       * @Version : 1.0.0
       * @Date : 13.05.2011
       //
       * @Parms :
       * @Returns : true,false / Void
       * @Description : Show content of the users blogs
       /* ================================================================================================
       * @Programmer : Sergey Panarin
       * @Date : 13.05.2011
       * @Reason for change : Creation
       * Change Request Nbr:
       * ================================================================================================*/
       
       function ShowContent()
       {
       
        //$script = $_SERVER['PHP_SELF'].'?module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr.'&fltr2='.$this->fltr2.'&fln='.$this->fln.'&id_cat='.$this->id_cat;
        $this->GetContent('nolimit');
       $rowsAll = $this->Right->db_GetNumRows(); 
        $res = $this->GetContent();
        //echo '<br>$q='.$q.' $res='.$res.' $this->Right->result='.$this->Right->result.' $this->user_id='.$this->user_id;
        if( !$res )return false;

        $rows = $this->Right->db_GetNumRows();
        /* Write Form Header */
        $this->Form->WriteHeader( $this->script.'module='.$this->module."&sys_user_id=".$this->sys_user_id."&display=".$this->display."&start=".$this->start);
        
        
   ?>
 <table border="0" cellpadding="5" cellspacing="0" width="100%">
  <tr>
   
   <td valign="top">
   <?      
        //$this->ShowContentFilters();

        /* Write Table Part */
        AdminHTML::TablePartH();
        //echo $this->script;
        /* Write Links on Pages */
        echo '<TR><TD COLSPAN="20">';
        $script1 = $this->script.'module='.$this->module.'&task='.$this->task."&sys_user_id=".$this->sys_user_id;
//        $script1 = $_SERVER['PHP_SELF']."?$script1";
        $this->Form->WriteLinkPages( $script1, $rowsAll, $this->display, $this->start, $this->sort );

        echo '<TR><TD COLSPAN="4">';
        $this->Form->WriteTopPanel( $this->script.'module='.$this->module.'&task='.$this->task."&sys_user_id=".$this->sys_user_id );

        //echo '<TD>';   $this->Spr->ShowActSprInCombo(TblModUserSprGroup, 'fltr2', $this->fltr2, "module=$this->module&task=showcontent"); 
        
       ?>
        <tr>
        <Th class="THead"><input value="0" id="cAll" onclick="if (this.value == '1') {unCheckAll(20); this.value = '0';} else {checkAll(20); this.value = '1';}" type="checkbox"/></Th>
        <Th class="THead">id</Th>
        
        <Th class="THead"><?=$this->Msg->show_text('FLD_TITLE')?></Th>
        <Th class="THead">Відображається</Th>
        <Th class="THead"><?=$this->Msg->show_text('FLD_PUB_DATA')?></Th>
        </tr>
       <?

        $a = $rows;
        $j = 0;
        $up = 0;
        $down = 0;

        $style1 = 'TR1';
        $style2 = 'TR2';
        for( $i = 0; $i < $rows; $i++ )
        {
           $row = $this->Right->db_FetchAssoc(); 
      //$row = $row_arr[$i];

      if ( (float)$i/2 == round( $i/2 ) ) $class=$style1;else $class = $style2;

         ?> <tr class="<?=$class;?>"><td><?
          $this->Form->CheckBox( "id_del[]", $row['id'],NULL,"check".$i);

          ?>
          <td>
          <?$this->Form->Link(  $this->script.'module='.$this->module.'&task=editUserBlog&sys_user_id='.$this->sys_user_id."&blogId=".$row['id'], stripslashes( $row['id'] ), $this->Msg->show_text('_TXT_EDIT_DATA', TblSysTxt) );?>
          </td>
          <?
            ?><td align="center"><?
            echo $row['title'];
          ?></td>
            <td align="center">
                <?if($row['visible']==1){?>
                   <img src="images/icons/tick.png" border="0"/>
                   <?}else{?>
                   <img src="images/icons/publish_x.png" border="0"/>
                   <?}?>
            </td>
           <td align="center">       
               <?=$row['dttm']?>
           </td>
           <?
        } //-- end for

        AdminHTML::TablePartF();
 ?>
   </td>
  </tr>
 </table>
 <?        
        $this->Form->WriteFooter();
        return true;

       } //end of fuinction ShowContent()
       
       
        
       /* ================================================================================================
       * @Function : EditUserBlog()
       * @Version : 1.0.0
       * @Date : 14.05.2011
       //
       * @Parms : id/id of the record
       * @Returns : true,false / Void
       * @Description : Show content of catalogue for editing
       // ================================================================================================
       * @Programmer : Sergey Panarin
       * @Date : 14.05.2011
       * @Reason for change : Creation
       * Change Request Nbr:
       * ================================================================================================*/

       function EditUserBlog( $id=NULL )
       {
        $Panel = new Panel();
        $ln_sys = new SysLang();
        $mas=NULL;

        $fl = NULL;

        /* set action page-adress with parameters */
        //$script = $_SERVER['PHP_SELF'].'?module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr.'&fltr2='.$this->fltr2.'&fln='.$this->fln.'&id_cat='.$this->id_cat;
        
        
         $q="select * from `".TblModUserBlog."` where id='$this->blogId' AND id_user=".$this->sys_user_id;
         $res = $this->Right->Query( $q, $this->user_id, $this->module );
         if( !$this->Right->result ) {
             $mas = $this->Right->db_FetchAssoc();
        
            $this->Form->WriteHeaderFormImg( $this->script.'module='.$this->module.'&sys_user_id='.$this->sys_user_id."&blogId=".$this->blogId);
            $this->Form->IncludeHTMLTextArea(); 

            if( $this->id!=NULL ) $txt = $this->Msg->show_text('_TXT_EDIT_DATA',TblSysTxt);
            else $txt = $this->Msg->show_text('_TXT_ADD_DATA',TblSysTxt);

            AdminHTML::PanelSubH( $txt );

            $_SESSION['sys_user_id']=$this->sys_user_id;
            $this->show_TinyMCE();
            AdminHTML::PanelSimpleH();
           ?>
            <table border="0" class="EditTable">

               <td style="height: 30px;font-weight: bold;">


                   <span style="width: 500px;text-align: right;">
                       Відображення: <input type="checkbox" name="showBlog" value="1" />
                   </span>
              </td>

              </tr>
              <tr style="height: 30px;font-weight: bold;">
                  <td>
                      Дата створення/редагування: 
                  </td>
                  <td><span style="font-weight: normal"></span></td>

              </tr>
              <tr style="height: 30px;font-weight: bold;">
                  <td>
                      Заголовок блогу: 
                  </td>
                  <td><input style="width: 300px;" type="text" name="headerBlog" value=""/></td>
              </tr>

              <tr style="height: 30px;">
                  <td style="vertical-align: text-bottom;font-weight: bold;">
                      Запис:
                  </td>
              </tr>
              <tr style="height: 30px;">
                  <td colspan="2"> 
                      <textarea style="height: 533px;width: 600px;" name="blogContent" style="width:100%" class="tiny" id="tiny"></textarea>
                  </td>
              </tr>
              <tr style="height: 30px;"></tr>
            </table>
             <?
            
         }else{
             
         $mas = $this->Right->db_FetchAssoc();
        
            $this->Form->WriteHeaderFormImg( $this->script.'module='.$this->module.'&sys_user_id='.$this->sys_user_id."&blogId=".$this->blogId);
            $this->Form->IncludeHTMLTextArea(); 

            if( $this->id!=NULL ) $txt = $this->Msg->show_text('_TXT_EDIT_DATA',TblSysTxt);
            else $txt = $this->Msg->show_text('_TXT_ADD_DATA',TblSysTxt);

            AdminHTML::PanelSubH( $txt );

            $_SESSION['sys_user_id']=$this->sys_user_id;
            $this->show_TinyMCE();
            AdminHTML::PanelSimpleH();
           ?>
            <table border="0" class="EditTable">
            <tr><td><b><?echo $this->Msg->show_text('FLD_ID')?>: <?=$mas['id']?></b></td>

               <td style="height: 30px;font-weight: bold;">


                   <span style="width: 500px;text-align: right;">
                       Відображення: <input type="checkbox" name="showBlog" value="1" <?if($mas['visible']) echo "checked";?>/>
                   </span>
              </td>

              </tr>
              <tr style="height: 30px;font-weight: bold;">
                  <td>
                      Дата створення/редагування: 
                  </td>
                  <td><span style="font-weight: normal"><?=$mas['dttm']?></span></td>

              </tr>
              <tr style="height: 30px;font-weight: bold;">
                  <td>
                      Заголовок блогу: 
                  </td>
                  <td><input style="width: 300px;" type="text" name="headerBlog" value="<?=$mas['title']?>"/></td>
              </tr>

              <tr style="height: 30px;">
                  <td style="vertical-align: text-bottom;font-weight: bold;">
                      Запис:
                  </td>
              </tr>
              <tr style="height: 30px;">
                  <td colspan="2"> 
                      <textarea style="height: 533px;width: 600px;" name="blogContent" style="width:100%" class="tiny" id="tiny"><?=$mas['content']?></textarea>
                  </td>
              </tr>
              <tr style="height: 30px;"></tr>
            </table>
             <?
      
         }
             AdminHTML::PanelSimpleF();
            $this->Form->WriteSaveAndReturnPanel( $this->script );?>&nbsp;<?
            $this->Form->WriteSavePanel( $this->script );?>&nbsp;<?
            $this->Form->WriteCancelPanel( $this->script.'module='.$this->module.'&sys_user_id='.$this->sys_user_id);?>&nbsp;<?
            if( !empty($this->blogId) ){
               $this->Form->WritePreviewPanelNewWindow( "http://".NAME_SERVER."/blog/".$this->sys_user_id."/entry/".$this->blogId);
            }  
            AdminHTML::PanelSubF();
            $this->Form->WriteFooter();
        return true;
       } // end of function EditContent() 
       
       
     function  save(){
//         $q="select * from `".TblModUserBlog."` where id='$this->blogId' AND id_user=".$this->sys_user_id;
//         $res = $this->Right->Query( $q, $this->user_id, $this->module );
//         $mas = $this->Right->db_FetchAssoc();
         if( empty($this->blogId)){
             //insert  new blog
             $q="INSERT INTO ".TblModUserBlog." SET
                    `id_user`='".$this->sys_user_id."',
                    `title`='".$this->headerBlog."',
                    `content`='".$this->blogContent."',
                    `dttm`='".date("Y-m-d")." ".date("G:i:s")."',
                `is_comment`=1,
                `visible`=".$this->showBlog."";
             $res = $this->Right->Query( $q, $this->user_id, $this->module );
            if(!$res) return false;
         }else{
             //update current blog
             $q="UPDATE `".TblModUserBlog."` SET
                `title`='".$this->headerBlog."',
                `content`='".$this->blogContent."',
                `is_comment`=1,
                 `visible`=".$this->showBlog."
                WHERE `id`='".$this->blogId."' AND `id_user`='".$this->sys_user_id."'";
            $res = $this->db->db_Query($q);
            if(!$res) return false;
         }
         //$mas = $this->Right->db_FetchAssoc();
     }// end of function save() 
     
     
     function show_TinyMCE(){
        ?>
        <script type="text/javascript" src="/include/js/tinymce/tiny_mce.js"></script>
        <script type="text/javascript">
            function tinyMceInit(){
                tinyMCE.init({
                        // General options
                        mode : "textareas",
                        theme : "advanced",
                        editor_selector : "tiny",
                        plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,images,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

                        // Theme options
                        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect,link,pasteword,pastetext,table",
                        theme_advanced_buttons2 : "emotions,|,search,replace,|,hr,removeformat,visualaid,|,media,fullscreen,tcut,|,bullist,numlist,|,undo,redo,|,code,fullscreen,|,forecolor,backcolor,pagebreak,images",
                        theme_advanced_buttons3 : "",
                        theme_advanced_toolbar_location : "top",
                        theme_advanced_toolbar_align : "left",
                        theme_advanced_statusbar_location : "bottom",
                        theme_advanced_resizing : true,

                        // Example content CSS (should be your site CSS)
                       // content_css : "/default.css",
                       skin : "o2k7",
                       skin_variant : "silver",
                        //Path
                        relative_urls : false,
                        remove_script_host : true,

                        extended_valid_elements : "tcut",

                        language : "ru"
                });
            }
            tinyMceInit();
        </script>
        
        <?
    }
       /* ================================================================================================
       * @Function : DelContent()
       * @Version : 1.0.0
       * @Date : 27.03.2006
       * @Parms :   $user_id, $module_id, $id_del
       * @Returns : true,false / Void
       * @Description :  Remove data from the table
       // ================================================================================================
       * @Programmer : Sergey Panarin
       * @Date : 27.03.2006
       * @Reason for change : Creation
       * Change Request Nbr:
       * ================================================================================================*/
       function DelUserBlogs( $id_del )
       {
        $del = 0; 
        $kol = count( $id_del );echo $kol;
        for( $i=0; $i<$kol; $i++ )
        {
         $u=$id_del[$i];
          $q = "delete from ".TblModUserBlog." where id='$u'";
          $res = $this->Right->Query( $q, $this->user_id, $this->module );
                    
          if ( $res )
           $del=$del+1;
          else
           return false;
        }         
         return $del;
       } //end of function DelContent() 
       
       
       
                
      
       
 } // end of class Catalog_content   

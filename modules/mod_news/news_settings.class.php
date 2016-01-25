<?php
// ================================================================================================
// System : SEOCMS
// Module : news_settings.class.php
// Version : 1.0.0
// Date : 23.05.2007
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : Class definition for all actions with settings of News
//
// ================================================================================================

include_once( $_SERVER['DOCUMENT_ROOT'].'/modules/mod_news/news.defines.php' );

// ================================================================================================
//    Class             : News_settings
//    Version           : 1.0.0
//    Date              : 21.03.2006
//
//    Constructor       : Yes
//    Parms             : session_id / session id
//                        usre_id    / UserID
//                        user_      /
//                        user_type  / id of group of user
//    Returns           : None
//    Description       : Class definition for all actions with managment of News
// ================================================================================================
//    Programmer        :  ALex Kerest
//    Date              :  23.05.2007
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================
 class News_settings extends News {

     var $rss_id = null;
     var $rss_path = null;
     var $rss_descr = null;
     var $rss_status = null;

       // ================================================================================================
       //    Function          : News_settings (Constructor)
       //    Version           : 1.0.0
       //    Date              : 23.05.2007
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
       function News_settings ($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL) {
                //Check if Constants are overrulled
                ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
                ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
                ( $display  !="" ? $this->display = $display  : $this->display = 20   );
                ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
                ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
                ( $width    !="" ? $this->width   = $width    : $this->width   = 750  );

                if (empty($this->db)) $this->db =  DBs::getInstance();
                if (empty($this->Right)) $this->Right =  check_init('RightsNews', 'Rights', "'".$this->user_id."','".$this->module."'");
                $this->multi = check_init_txt('TblBackMulti',TblBackMulti);
                if (empty($this->Form)) $this->Form = check_init('FormNews', 'Form', "'form_mod_news'");
                if (empty($this->Spr)) $this->Spr = check_init('SysSpr', 'SysSpr');

                $this->AddTable();

       } // End of News_settings Constructor


       // ================================================================================================
       // Function : AddTable()
       // Version : 1.0.0
       // Date : 02.08.2007
       // Parms :
       // Returns : true,false / Void
       // Description : show setting of Catalogue
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 02.08.2007
       // Reason for change : Reason Description / Creation
       // Change Request Nbr:
       // ================================================================================================
       function AddTable()
       {
           // add field id_group to the table settings
           if ( !$this->db->IsFieldExist(TblModNewsSet, "rss_import") ) {
               $q = "ALTER TABLE `".TblModNewsSet."` ADD `rss_import` SET( '0', '1' ) DEFAULT NULL ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
	       }


           // create table for strore RSS chanels
           if ( !$this->db->IsTableExist(TblModNewsRss) ) {
               $q = "
                CREATE TABLE `".TblModNewsRss."` (
                  `id` int(10) unsigned NOT NULL auto_increment,
                  `path` char(255) NOT NULL default '',
	          `status` set('0','1') default NULL,
                  PRIMARY KEY  (`id`),
                  KEY `path` (`path`)
                ) ENGINE=MyISAM;
                ";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }

           // create table for strore RSS description of RSS chanel
           if ( !$this->db->IsTableExist(TblModNewsRssSprDescr) ) {
               $q = "
                CREATE TABLE `".TblModNewsRssSprDescr."` (
                  `id` int(10) unsigned NOT NULL auto_increment,
                  `cod` int(10) unsigned NOT NULL default '0',
                  `lang_id` int(10) unsigned NOT NULL default '0',
                  `name` char(255) NOT NULL default '',
                  PRIMARY KEY  (`id`),
                  KEY `cod` (`cod`,`lang_id`)
                ) ENGINE=MyISAM;
                ";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }

           // add field id_group to the table settings
           if ( !$this->db->IsFieldExist(TblModNewsSet, "relat_prod") ) {
               $q = "ALTER TABLE `".TblModNewsSet."` ADD `relat_prod` set('0','1') default '0';";
               $res = $this->db->db_Query( $q );
             //  echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }
        } // end  of function AddTable

      function showField($name = NULL,$sel = 0,$Label = '',$class = 'tr1'){
          ?><tr class=<?=$class?>>
          <td align="left"><label style="cursor: pointer" for="<?=$name?>"><?=$Label;?></label></td>
          <td><?$this->Form->CheckBox( $name, '1', $sel,$name);?></td>
          </tr><?
      }

       // ================================================================================================
       // Function : ShowSettings()
       // Version : 1.0.0
       // Date : 27.03.2006
       // Parms :
       // Returns : true,false / Void
       // Description : show setting of Newsue
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 27.03.2006
       // Reason for change : Reason Description / Creation
       // Change Request Nbr:
       // ================================================================================================
       function ShowSettings()
       {
         $Panel = new Panel();
         $ln_sys = new SysLang();
        $tmp_bd = new DB();
        //echo '?module='.$this->module;
        $script = $_SERVER['PHP_SELF'].'?module='.$this->module;

         $q="select * from `".TblModNewsSet."` where 1";
         $res = $this->Right->Query( $q, $this->user_id, $this->module );
         if( !$this->Right->result ) return false;
         $row = $this->Right->db_FetchAssoc();

        /* Write Form Header */
        $this->Form->WriteHeader( $script );
        AdminHTML::PanelSimpleH();

        ?>
        <TABLE BORDER=0 class="EditTable">
         <TR valign="top">
          <TD>
           <?=AdminHTML::PanelSimpleH();?>
           <table border=0 cellspacing=1 cellpading=0 width="200" class="EditTable">
            <tr>
             <td colspan=2><b><?=$this->multi['TXT_USED_PROPS']?>:</b></td>
            </tr>
               <?$this->showField('top_news',$row['top_news'],$this->multi['TXT_TOP']);
               $this->showField('newsline',$row['newsline'],$this->multi['TXT_NEWS_LINE'],'tr2');
               $this->showField('img',$row['img'],$this->multi['FLD_IMG2']);
               $this->showField('dt',$row['dt'],$this->multi['FLD_DATE'],'tr2');
               $this->showField('short_descr',$row['short_descr'],$this->multi['FLD_SHORT_DESCR']);
               $this->showField('full_descr',$row['full_descr'],$this->multi['FLD_FULL_DESCR'],'tr2');
               $this->showField('relat_prod',$row['relat_prod'],$this->multi['TXT_PRODUCTS_TO_NEWS']);
               $this->showField('subscr',$row['subscr'],$this->multi['TXT_SUBSCRIBE'],'tr2');
               $this->showField('rss_import',$row['rss_import'],$this->multi['TXT_RSS_IMPORT']);?>
           </table>
           <?=AdminHTML::PanelSimpleF();?>
          </TD>
          <TD></TD>
          <TD>
          <?=AdminHTML::PanelSimpleH();?>
           <table border=0 cellspacing=1 cellpading=0 width="200" class="EditTable">
            <tr>
             <td colspan=2><b><?=$this->multi['TXT_META_DATA']?>:</b></td>
            </tr>
            <tr>
             <td>
              <?
                $Panel->WritePanelHead( "SubPanel_" );
                $ln_arr = $ln_sys->LangArray( _LANG_ID );
                while( $el = each( $ln_arr ) )
                {
                  $lang_id = $el['key'];
                  $lang = $el['value'];
                  $mas_s[$lang_id] = $lang;

                  $Panel->WriteItemHeader( $lang );
                  echo "\n <table border=0 class='EditTable'>";

                  echo "\n<tr><td><b>".$this->multi['FLD_TITLE'].":</b></td>";
                  echo "\n<td>";
                  $name = $this->Spr->GetByCod( TblModNewsSetSprTitle, 1, $lang_id );
                  $this->Err!=NULL ? $val=$this->title[$lang_id] : $val=$name[$lang_id];
                  //else $val=$this->title[$lang_id];
                   $this->Form->TextBox( 'title['.$lang_id.']', stripslashes($val),58 );

                  echo "\n<tr><td><b>".$this->multi['_TXT_META_DESCRIPTION'].":</b></td>";
                  echo "\n<td>";
                  $name = $this->Spr->GetByCod( TblModNewsSetSprDescription, 1, $lang_id );
                  $this->Err!=NULL ? $val=$this->description[$lang_id] : $val=$name[$lang_id];
                  //else $val=$this->description[$lang_id];
                  $this->Form->TextArea( 'description['.$lang_id.']', stripslashes($val), 4, 50 );

                  echo "\n<tr><td><b>".$this->multi['_TXT_META_KEYWORDS'].":</b></td>";
                  echo "\n<td>";
                  $name = $this->Spr->GetByCod( TblModNewsSetSprKeywords, 1, $lang_id );
                  $this->Err!=NULL ? $val=$this->keywords[$lang_id] : $val=$name[$lang_id];
                  //else $val=$this->keywords[$lang_id];
                  $this->Form->TextArea( 'keywords['.$lang_id.']', stripslashes($val), 4, 50 );
                  echo "\n</table>";
                  $Panel->WriteItemFooter();
                }
                $Panel->WritePanelFooter();
                ?>
             </td>
            </tr>
            <?if($row['rss_import']=='1'){ ?>
       	         <tr>
                  <TD>
                   <center>
                    <h4><?=$this->multi['TXT_RSS_SET'];?></h4>
                   </center>
                 <?
                 $style1 = 'tr1';
                 $style2 = 'tr2';
                 $i = 0;
                 $q="select * from `".TblModNewsRss."` where 1";
                $res = $this->Right->Query( $q, $this->user_id, $this->module );
                if( !$this->Right->result ) return false;
                $rows = $this->Right->db_GetNumRows();
                if($rows>0){
                   ?>
                    <table border="0" cellpadding="2" cellspacing="1" width="100%" class="EditTable">
                      <tr>
                        <td class="THead"><b><?=$this->multi['FLD_ID'];?></b></td>
                        <td class="THead"><b><?=$this->multi['TXT_PATH_RSS'];?></b></td>
                        <td class="THead"><b><?=$this->multi['TXT_RSS_DESCR'];?></b></td>
                        <td class="THead"><b><?=$this->multi['_FLD_NEWS_STATUS'];?></b></td>
                      </tr>
                   <?
                     for($i=0;$i<$rows;$i++){
                       $row_arr[$i] = $this->Right->db_FetchAssoc();
                     }

                     for($i=0;$i<$rows;$i++){
                       $row = $row_arr[$i];

                       if ( (float)$i/2 == round( $i/2 ) )
                           {
                            echo '<TR CLASS="'.$style1.'">';
                           }
                           else echo '<TR CLASS="'.$style2.'">';
                       ?>
                        <td><?=$row['id'];?><?=$this->Form->Hidden( 'rss_id['.$i.']', $row['id']);?></td>
                        <td><?=$this->Form->TextBox( 'rss_path['.$i.']', $row['path'], 30 );?></td>
                        <td>
                            <?
                            $Panel->WritePanelHead( "SubPanel_".$i );
                            $ln_arr = $ln_sys->LangArray( _LANG_ID );
                            while( $el = each( $ln_arr ) )
                            {
                              $lang_id = $el['key'];
                              $lang = $el['value'];
                              $mas_s[$lang_id] = $lang;

                              $Panel->WriteItemHeader( $lang );
                              echo "\n <table border=0 class='EditTable'>";

                              echo "\n<tr>";
                              echo "\n<td>";
                              $descr_rss = $this->Spr->GetByCod( TblModNewsRssSprDescr, $row['id'], $lang_id );
                              $this->Err!=NULL ? $val=$this->descr_rss[$lang_id] : $val=$descr_rss[$lang_id];
                              //else $val=$this->keywords[$lang_id];
                              $this->Form->TextArea( 'rss_descr['.$row['id'].']['.$lang_id.']', stripslashes($val), 4, 30 );
                              echo "\n</table>";
                              $Panel->WriteItemFooter();
                            }
                            $Panel->WritePanelFooter();
                            ?>
                        </td>
                        <td>
                           <?
                           $arr = NULL;
                           $arr['1'] = 'Active';
                           $arr['0'] = 'Off';
                           $arr['3'] = 'Delete';
                           if( !$row['status'] ) $row['status'] = 'i';
                           $this->Form->Select( $arr, 'rss_status['.$row['id'].']', $row['status'], NULL );
                           ?>
                        </td>
                       </tr>
                       <?
                     }//end for
                   ?></table><?
                } // end if
                ?>
        		<table border="0" cellpadding="2" cellspacing="1" width="100%" class="EditTable">
                  <tr>
                   <td colspan="2" class="THead"><?=$this->multi['TXT_ADD_NEW_RSS'];?></td>
                  </tr>
                  <?

                  for($k=0;$k<ADD_COUNT_CHANEL;$k++){

                           if ( (float)$k/2 == round( $k/2 ) )
                           {
                            echo '<TR CLASS="'.$style1.'">';
                           }
                           else echo '<TR CLASS="'.$style2.'">';
                           ?>
                            <td><b><?=$this->multi['TXT_RSS_PATH']?>:</b></td>
                            <td align="left" width="80%"><?=$this->Form->TextBox( 'rss_path['.($i+$k).']', '', 40 )?></td>
                  </tr>
                  <?
                  }
                  ?>
                </table>
    		   </TD>
              </tr>
            <?}?>
           </table>
           <?=AdminHTML::PanelSimpleF();?>
          </TD>
         </TR>
        </TABLE>
        <?

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
       // Date : 27.03.2006
       // Parms :
       // Returns : true,false / Void
       // Description : show setting of Newsue
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 27.03.2006
       // Reason for change : Reason Description / Creation
       // Change Request Nbr:
       // ================================================================================================
       function SaveSettings()
       {
        $q="select * from `".TblModNewsSet."` where 1";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        if( !$this->Right->result ) return false;
        $rows = $this->Right->db_GetNumRows();

        $uploaddir = SITE_PATH.$this->img_path;
        if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0755);
        else @chmod($uploaddir,0755);

        if($rows>0)
        {
          $q="UPDATE `".TblModNewsSet."` set
              `img`='".$this->img."',
              `short_descr`='".$this->short_descr."',
              `full_descr`='".$this->full_descr."',
              `subscr`='".$this->subscr."',
              `dt`='".$this->dt."',
	          `rss_import` = '".$this->rss_import."',
              `top_news`='".$this->top_news."',
              `newsline`='".$this->newsline."',
              `relat_prod`='".$this->relat_prod."'
           ";
        }
        else
        {
          $q="INSERT INTO `".TblModNewsSet."` SET
              `img`='".$this->img."',
              `short_descr`='".$this->short_descr."',
              `full_descr`='".$this->full_descr."',
              `subscr`='".$this->subscr."',
              `dt`='".$this->dt."',
	          `rss_import` = '".$this->rss_import."',
              `top_news`='".$this->top_news."',
              `newsline`='".$this->newsline."',
              `relat_prod`='".$this->relat_prod."'
             ";
        }
           $res = $this->Right->Query( $q, $this->user_id, $this->module );
           //  echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
           if( !$res || !$this->Right->result) return false;
        $id = 1;

        //---- Save fields on different languages ----
        $res=$this->Spr->SaveNameArr( $id, $this->title, TblModNewsSetSprTitle );
        if( !$res ) return false;

        $res=$this->Spr->SaveNameArr( $id, $this->description, TblModNewsSetSprDescription );
        if( !$res ) return false;

        $res=$this->Spr->SaveNameArr( $id, $this->keywords, TblModNewsSetSprKeywords );
        if( !$res ) return false;


       // print_r($this->rss_descr);
       // print_r($this->rss_path);
        for($i=0;$i<sizeof($this->rss_path);$i++){
            $val = $this->rss_path[$i];
            if(empty($val)) continue;

             $q="select * from `".TblModNewsRss."` where `path`='".$val."'";
             $res = $this->Right->Query( $q, $this->user_id, $this->module );
             if( !$this->Right->result ) return false;
             $rows = $this->Right->db_GetNumRows();
             if($rows>0){
                 // print_r($this->rss_id);
                  $rss_id = $this->rss_id[$i];
                  $status  = $this->rss_status[$rss_id];
                  $descr  = $this->rss_descr[$rss_id];

                 if($status==3) {
                   $q="delete from `".TblModNewsRss."` where `path`='".$val."'";
                 } else{

                     $q="update `".TblModNewsRss."` set
                          `status`='".$status."'
                           where `id`='".$rss_id."'";
                     $res=$this->Spr->SaveNameArr( $rss_id, $descr, TblModNewsRssSprDescr );
                     if( !$res ) return false;
                 }
             } else{
                $q="insert into `".TblModNewsRss."` values(NULL, '$val', '1')";
             }
             $res = $this->Right->Query( $q, $this->user_id, $this->module );
             //   echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
                if( !$res ) return false;
                if( !$this->Right->result) return false;

        }


        return true;
       } // end of function SaveSettings()
 } //end of class News_settings

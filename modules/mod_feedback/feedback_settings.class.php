<?php
// ================================================================================================
// System : CMS
// Module : feedback_settings.class.php
// Date : 17.06.2013
// Licensed To:   Bogdan Iglinsky
// Purpose : Class definition for all actions with settings of feedback
// ================================================================================================

include_once( SITE_PATH.'/modules/mod_feedback/feedback.defines.php' );

// ================================================================================================
//    Class             : feedback_settings
//    Date              : 17.06.2013
//    Constructor       : Yes
//    Parms             : usre_id    / UserID
//                        user_      /
//                        user_type  / id of group of user
//    Returns           : None
//    Description       : Class definition for all actions with managment of feedback
//    Programmer        :  Bogdan Iglinsky
// ================================================================================================
class FeedBack_settings{

    // ================================================================================================
    //    Function          : feedback_settings (Constructor)
    //    Date              : 17.06.2013
    //    Parms             : usre_id   / User ID
    //                        module    / module ID
    //    Returns           : Error Indicator
    //    Description       : Opens and selects a dabase
    // ================================================================================================
    function FeedBack_settings ($user_id=NULL, $module=NULL) {
        //Check if Constants are overrulled
        ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
        ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );

        if (empty($this->db)) $this->db = DBs::getInstance();
        if (empty($this->Right)) $this->Right = check_init('RightsFeedBack', 'Rights', "'".$this->user_id."', '".$this->module."'");
        if (empty($this->Form)) $this->Form = check_init('FormFeedBack', 'Form', '"form_mod_feedback"');
        if (empty($this->Spr)) $this->Spr = check_init('SysSpr', 'SysSpr', "'".$this->user_id."', '".$this->module."'");
        if (empty($this->multi)) $this->multi = check_init_txt('TblBackMulti',TblBackMulti);

        $this->AddTable();

    } // End of Catalog_settings Constructor


    // ================================================================================================
    // Function : AddTable()
    // Date : 17.06.2013
    // Returns : true,false / Void
    // Description : Adding fields setting to table TblModFeedbackSet
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function AddTable()
    {
        // add field is_files to the table settings
        if ( !$this->db->IsFieldExist(TblModFeedbackSet, "is_files") ) {
            $q = "ALTER table `".TblModFeedbackSet."` ADD `is_files` SET( '0', '1' ) DEFAULT NULL ;";
            $res = $this->db->db_Query( $q );
//            echo '<br>$q='.$q.' $res='.$res;
            if( !$res )return false;

            $q = "ALTER table `".TblModFeedbackSet."` ADD INDEX ( `is_files` ) ;";
            $res = $this->db->db_Query( $q );
            //echo '<br>$q='.$q.' $res='.$res;
            if( !$res )return false;
        }

        // add field is_captcha to the table settings
        if ( !$this->db->IsFieldExist(TblModFeedbackSet, "is_captcha") ) {
            $q = "ALTER table `".TblModFeedbackSet."` ADD `is_captcha` SET( '0', '1' ) DEFAULT NULL ;";
            $res = $this->db->db_Query( $q );
            //echo '<br>$q='.$q.' $res='.$res;
            if( !$res )return false;

            $q = "ALTER table `".TblModFeedbackSet."` ADD INDEX ( `is_captcha` ) ;";
            $res = $this->db->db_Query( $q );
            //echo '<br>$q='.$q.' $res='.$res;
            if( !$res )return false;
        }

        // add field is_send_ajax to the table settings
        if ( !$this->db->IsFieldExist(TblModFeedbackSet, "is_send_ajax") ) {
            $q = "ALTER table `".TblModFeedbackSet."` ADD `is_send_ajax` SET( '0', '1' ) DEFAULT NULL ;";
            $res = $this->db->db_Query( $q );
            //echo '<br>$q='.$q.' $res='.$res;
            if( !$res )return false;

            $q = "ALTER table `".TblModFeedbackSet."` ADD INDEX ( `is_send_ajax` ) ;";
            $res = $this->db->db_Query( $q );
            //echo '<br>$q='.$q.' $res='.$res;
            if( !$res )return false;
        }

        // add field files to the table settings
        if ( !$this->db->IsFieldExist(TblModFeedbackSet, "files_path") ) {
            $q = "ALTER table `".TblModFeedbackSet."` ADD `files_path` VARCHAR( 255 );";
            $res = $this->db->db_Query( $q );
            //echo '<br>$q='.$q.' $res='.$res;
            if( !$res )return false;
        }

        // add field is_send_ajax to the table settings
        if ( !$this->db->IsFieldExist(TblModFeedbackSet, "is_phone") ) {
            $q = "ALTER table `".TblModFeedbackSet."` ADD `is_phone` SET( '0', '1' ) DEFAULT NULL ;";
            $res = $this->db->db_Query( $q );
            //echo '<br>$q='.$q.' $res='.$res;
            if( !$res )return false;

            $q = "ALTER table `".TblModFeedbackSet."` ADD INDEX ( `is_phone` ) ;";
            $res = $this->db->db_Query( $q );
            //echo '<br>$q='.$q.' $res='.$res;
            if( !$res )return false;
        }

        // add field is_send_ajax to the table settings
        if ( !$this->db->IsFieldExist(TblModFeedbackSet, "is_email") ) {
            $q = "ALTER table `".TblModFeedbackSet."` ADD `is_email` SET( '0', '1' ) DEFAULT NULL ;";
            $res = $this->db->db_Query( $q );
            //echo '<br>$q='.$q.' $res='.$res;
            if( !$res )return false;

            $q = "ALTER table `".TblModFeedbackSet."` ADD INDEX ( `is_email` ) ;";
            $res = $this->db->db_Query( $q );
            //echo '<br>$q='.$q.' $res='.$res;
            if( !$res )return false;
        }

        // add field is_send_ajax to the table settings
        if ( !$this->db->IsFieldExist(TblModFeedbackSet, "is_fax") ) {
            $q = "ALTER table `".TblModFeedbackSet."` ADD `is_fax` SET( '0', '1' ) DEFAULT NULL ;";
            $res = $this->db->db_Query( $q );
            //echo '<br>$q='.$q.' $res='.$res;
            if( !$res )return false;

            $q = "ALTER table `".TblModFeedbackSet."` ADD INDEX ( `is_fax` ) ;";
            $res = $this->db->db_Query( $q );
            //echo '<br>$q='.$q.' $res='.$res;
            if( !$res )return false;
        }

        // add field is_send_ajax to the table settings
        if ( !$this->db->IsFieldExist(TblModFeedbackSet, "is_surname") ) {
            $q = "ALTER table `".TblModFeedbackSet."` ADD `is_surname` SET( '0', '1' ) DEFAULT NULL ;";
            $res = $this->db->db_Query( $q );
            //echo '<br>$q='.$q.' $res='.$res;
            if( !$res )return false;

            $q = "ALTER table `".TblModFeedbackSet."` ADD INDEX ( `is_surname` ) ;";
            $res = $this->db->db_Query( $q );
            //echo '<br>$q='.$q.' $res='.$res;
            if( !$res )return false;
        }

        if ( !$this->db->IsFieldExist(TblModFeedbackSet, "is_place_label") ) {
            $q = "ALTER table `".TblModFeedbackSet."` ADD `is_place_label` SET( '0', '1' ) DEFAULT NULL ;";
            $res = $this->db->db_Query( $q );
            //echo '<br>$q='.$q.' $res='.$res;
            if( !$res )return false;

            $q = "ALTER table `".TblModFeedbackSet."` ADD INDEX ( `is_place_label` ) ;";
            $res = $this->db->db_Query( $q );
            //echo '<br>$q='.$q.' $res='.$res;
            if( !$res )return false;
        }

    }// end of function AddTable()


    // ================================================================================================
    // Function : ShowSettings()
    // Date : 17.06.2013
    // Returns : true,false / Void
    // Description : Show setting of Feedback
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function ShowSettings()
    {
        $Panel = new Panel();
        $ln_sys = new SysLang();

        $script = $_SERVER['PHP_SELF'].'?module='.$this->module;

        $q="select * from `".TblModFeedbackSet."` where 1";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        if( !$this->Right->result ) return false;
        $row = $this->Right->db_FetchAssoc();

        /* Write Form Header */
        $this->Form->WriteHeader( $script );
        AdminHTML::PanelSimpleH();

        ?><div class="floatToLeft" style="margin: 0px 20px 0px 0px;">
            <?=AdminHTML::PanelSimpleH();?>
            <table border="0" cellspacing="1" cellpading="0" width="150" class="EditTable">
                <?
                $this->showCheckbox($this->multi['TXT_PLACEHOLDER_LABEL'],"is_place_label",$row['is_place_label']);
                $this->showCheckbox($this->multi['TXT_SURNAME'],"is_surname",$row['is_surname'],'TR2');
                $this->showCheckbox($this->multi['TXT_USE_PHONE'],"is_phone",$row['is_phone']);
                $this->showCheckbox($this->multi['TXT_USE_EMAIL'],"is_email",$row['is_email'],'TR2');
                $this->showCheckbox($this->multi['TXT_USE_FAX'],"is_fax",$row['is_fax']);
                $this->showCheckbox($this->multi['TXT_ADDITING_FILES'],"is_files",$row['is_files'],'TR2');
                $this->showCheckbox($this->multi['TXT_USE_KAPTCH'],"is_captcha",$row['is_captcha']);
                $this->showCheckbox($this->multi['TXT_IS_SEND_AJAX'],"is_send_ajax",$row['is_send_ajax'],'TR2');
                ?>
            </table><br>
            <table border="0" cellspacing="1" cellpading="0" class="EditTable">
                <tr>
                    <td><b><?=$this->multi['TXT_FILES_PATH']?>:</b>
                        <?
                        $val=$row['files_path'];
                        if ( trim($val)=='' ) $val = FeedbackUploadFilesPath;?>
                        <br/>
                        <?echo SITE_PATH; echo $this->Form->TextBox( 'files_path', $val, 40 )?>
                    </td>
                </tr>
            </table>
            <?=AdminHTML::PanelSimpleF();?>
        </div>
        <?

        $this->Form->WriteSavePanel( $script );
        //$this->Form->WriteCancelPanel( $script );
        AdminHTML::PanelSimpleF();
        //AdminHTML::PanelSubF();

        $this->Form->WriteFooter();
        return true;
    } //end of function ShowSettings()

    // ================================================================================================
    // Function : showCheckbox()
    // Date : 17.06.2013
    // Returns : true,false / Void
    // Description : Show checkbox in Feedback seting
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function showCheckbox($label = '',$name = '',$val = 0,$class = 'TR1'){
        if($val) $sel = 1;
        else $sel = 0;
        ?><tr class="<?=$class?>">
        <td align="left">
            <label for="<?=$name?>" style="display: block;cursor: pointer"><?=$label;?></label>
        </td>
        <td>
            <?$this->Form->CheckBox($name ,1 ,$sel ,$name );?>
        </td>
        </tr><?
    }

    // ================================================================================================
    // Function : SaveSettings()
    // Date : 17.06.2013
    // Returns : true,false / Void
    // Description : save setting of Feedback
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function SaveSettings()
    {
        $q="select * from `".TblModFeedbackSet."` where 1";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        if( !$this->Right->result ) return false;
        $rows = $this->Right->db_GetNumRows();

        $uploaddir = SITE_PATH.$this->files_path;
        if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0755);
        else @chmod($uploaddir,0755);

        if($rows>0)
        {
            $q="update `".TblModFeedbackSet."` set
              `files_path`='$this->files_path',
              `is_send_ajax`='$this->is_send_ajax',
              `is_files`='$this->is_files',
              `is_captcha`='".$this->is_captcha."',
              `is_phone`='".$this->is_phone."',
              `is_email`='".$this->is_email."',
              `is_fax`='".$this->is_fax."',
              `is_surname`='".$this->is_surname."',
              `is_place_label`='".$this->is_place_label."'  ";
        }
        else
        {

            $q="INSERT INTO `".TblModFeedbackSet."` set
              `files_path`='$this->files_path',
              `is_send_ajax`='$this->is_send_ajax',
              `is_files`='$this->is_files',
              `is_captcha`='".$this->is_captcha."',
              `is_phone`='".$this->is_phone."',
              `is_email`='".$this->is_email."',
              `is_fax`='".$this->is_fax."',
              `is_surname`='".$this->is_surname."',
              `is_place_label`='".$this->is_place_label."' ";
        }
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
//        echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
        if( !$res || !$this->Right->result ) return false;
        return true;
    } // end of function SaveSettings()

} //end of class Catalog_settings
<?php
// ================================================================================================
// System : SEOCMS
// Module : user.class.php
// Version : 1.0.0
// Date : 06.01.2006
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : Class definition for all actions with managment of External users
//
// ================================================================================================

include_once( SITE_PATH.'/modules/mod_user/user.defines.php' );

// ================================================================================================
//    Class             : User
//    Version           : 1.0.0
//    Date              : 06.01.2006
//
//    Constructor       : Yes
//    Parms             : session_id / session id
//                        usre_id    / UserID
//                        user_      /
//                        user_type  / id of group of user
//    Returns           : None
//    Description       : Class definition for all actions with managment of External users
// ================================================================================================
//    Programmer        :  Igor Trokhymchuk
//    Date              :  06.01.2006
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================
 class User extends SysUser {

       var $user_id = NULL;
       var $module = NULL;
       var $Err=NULL;
       var $lang_id;

       var $sort = NULL;
       var $display = 20;
       var $start = 0;
       var $fln = NULL;
       var $width = 500;
       var $spr = NULL;
       var $srch = NULL;

       var $db = NULL;
       var $Msg = NULL;
       var $Right = NULL;
       var $Form = NULL;
       var $Spr = NULL;

       var $id = NULL;
       var $name = NULL;
       var $position = NULL;
       var $firm = NULL;
       var $country = NULL;
       var $regnumber = NULL;
       var $emplnumber = NULL;
       var $adr = NULL;
       var $city = NULL;
       var $phone = NULL;
       var $phone_mob = NULL;
       var $fax = NULL;
       var $email = NULL;
       var $www = NULL;
       var $subscr = NULL;
       var $user_status = NULL;
       var $login_multi_use = NULL;

       // ================================================================================================
       //    Function          : User (Constructor)
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
       function __construct($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL) {
                //Check if Constants are overrulled
                ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
                ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
                ( $display  !="" ? $this->display = $display  : $this->display = 20   );
                ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
                ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
                ( $width    !="" ? $this->width   = $width    : $this->width   = 750  );

                 if (empty($this->db)) $this->db = DBs::getInstance();
                 if (empty($this->Right)) $this->Right = check_init('Rights', 'Rights', '"'.$this->user_id.'", "'.$this->module.'"');
                 if (empty($this->Msg)) $this->Msg = check_init('ShowMsg', 'ShowMsg');
                 if (empty($this->Form)) $this->Form = check_init('form_mod_user', 'Form', '"form_mod_user"');
                if (empty($this->Spr)) $this->Spr = check_init('SysSpr', 'SysSpr', '"'.$this->user_id.'", "'.$this->module.'"');
       } // End of Prod Constructor

       // ================================================================================================
       // Function : GetUserIdByEmail()
       // Version : 1.0.0
       // Date : 06.01.2006
       //
       // Parms : $email - email (login)of the user
       // Returns :      true,false / Void
       // Description :  Return User id by user email (login)
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 06.01.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function GetUserIdByEmail( $email = NULL )
       {
         $tmp_db = new DB();
         $q = "SELECT `id` FROM `".TblModUser."` WHERE `email`='".$email."'";
         $res = $tmp_db->db_Query( $q );
         //echo '<br>'.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result;
         if ( !$res OR !$tmp_db->result ) {$tmp_db->db_Close(); return false;}
         $row = $tmp_db->db_FetchAssoc();
         $email = $row['id'];
         //echo '<br> $email='.$email;
         $tmp_db->db_Close();
         return $email;
       } //end of fuinction GetUserIdByEmail()

       // ================================================================================================
       // Function : GetUserEmailByUserId()
       // Version : 1.0.0
       // Date : 06.01.2006
       //
       // Parms :
       // Returns :      true,false / Void
       // Description :  Return User name
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 06.01.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function GetUserEmailByUserId( $user_id = NULL )
       {
         if ( !$user_id ) $user_id = $this->user_id;
         $q = "SELECT `email` FROM ".TblSysUser." WHERE `id`='".$user_id."'";
         $res = $this->db->db_Query( $q );
         //echo '<br>'.$q.' $res='.$res.' $this->db->result='.$this->db->result;
         if ( !$res ) return false;
         $row = $this->db->db_FetchAssoc();
         $email = $row['email'];
         //echo '<br> $email='.$email;
         return $email;
       } //end of fuinction GetUserEmailByUserId()

       // ================================================================================================
       // Function : GetUserNameByUserId()
       // Version : 1.0.0
       // Date : 06.01.2006
       //
       // Parms :
       // Returns :      true,false / Void
       // Description :  Return User email
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 06.01.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function GetUserNameByUserId( $user_id = NULL )
       {
         if ( !$user_id ) $user_id = $this->user_id;
         $q = "SELECT `name` FROM ".TblModUser." WHERE `sys_user_id`='".$user_id."'";
         $res = $this->db->db_Query( $q );
         //echo '<br>'.$q.' $res='.$res.' $this->db->result='.$this->db->result;
         if ( !$res ) return false;
         $row = $this->db->db_FetchAssoc();
         $name = $row['name'];
         //echo '<br> $name='.$name;
         return $name;
       } //end of fuinction GetUserNameByUserId()

// ================================================================================================
// Function : GetUserDataByUserEmail()
// Version : 1.0.0
// Date : 07.09.2006
//
// Parms :
// Returns :      true,false / Void
// Description :  Return all User data by user email
// ================================================================================================
// Programmer :  Igor Trokhymchuk
// Date : 7.09.2006
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
function GetUserDataByUserEmail( $user_email = NULL )
{
 $q = "SELECT * FROM ".TblSysUser." WHERE `email`='".$user_email."'";
 $res = $this->db->db_Query( $q );
 //echo '<br>'.$q.' $res='.$res.' $this->db->result='.$this->db->result;
 if ( !$res ) return false;
 $row = $this->db->db_FetchAssoc();
 return $row;
} //end of fuinction GetUserDataByUserEmail()

//----------------------------------------------------------------------------------------------------------------------


function GetUserDataByUserLogin( $user_login = NULL )
{
 $q = "SELECT * FROM ".TblSysUser." WHERE `login`='".$user_login."'";
 $res = $this->db->db_Query( $q );
 //echo '<br>'.$q.' $res='.$res.' $this->db->result='.$this->db->result;
 if ( !$res ) return false;
 $row = $this->db->db_FetchAssoc();
 return $row;
} //end of fuinction GetUserDataByUserEmail()

       // ================================================================================================
       // Function : GetUserDataByUserId()
       // Version : 1.0.0
       // Date : 07.09.2006
       //
       // Parms :
       // Returns :      true,false / Void
       // Description :  Return all User data by user id
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 7.09.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function GetUserDataByUserId( $user_id = NULL )
       {
         if ( !$user_id ) $user_id = $this->user_id;
         $q = "
            SELECT
                `" . TblModUser . "`.*,
                `" . TblSysUser . "`.`email`,
                `" . TblSysUser . "`.`group_id`
            FROM " . TblModUser . "," . TblSysUser . "
            WHERE `" . TblModUser . "`.`sys_user_id`='".$user_id."'
                AND `" . TblSysUser . "`.`id`='".$user_id."'
         ";
         $res = $this->db->db_Query( $q );
         //echo '<br>'.$q.' $res='.$res.' $this->db->result='.$this->db->result;
         if ( !$res ) return false;
         $row = $this->db->db_FetchAssoc();
         return $row;
       } //end of fuinction GetUserDataByUserId()


/*====================================================================================================================================
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
- - - - - - - - - - - - - - - - -  FRONT-END Part Fuctions - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
====================================================================================================================================*/


       // ================================================================================================
       // Function : SaveUser()
       // Version : 1.0.0
       // Date : 10.01.2006
       //
       // Parms :
       // Returns : true,false / Void
       // Description : Store data to the table from front-end
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 10.01.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function SaveUser()
       {
        $tmp_db = new DB();
        $q = "SELECT * FROM ".TblModUser." WHERE `sys_user_id`='".$this->user_id."'";
        $res = $this->db->db_Query( $q );
        if( !$res ) return false;
        $rows = $this->db->db_GetNumRows();
        //echo '<br>$q='.$q.'$rows='.$rows;

        $ip = $_SERVER['REMOTE_ADDR'];

        $long_ip = ip2long($ip);
        $q = "SELECT * from `mod_stat_ip` WHERE `ip_from`<".$long_ip." AND `ip_to`>".$long_ip."";
        $restmp = $tmp_db->db_Query( $q );
        //if( !$restmp ) return false;
        $rows_tmp = $tmp_db->db_GetNumRows();
        if($rows_tmp>0){
            $row_tmp = $tmp_db->db_FetchAssoc();
            //echo '<br>$q='.$q.' $rows_tmp='.$rows_tmp;
            $ip_ctry = $row_tmp['ctry'];
        }
        else $ip_ctry=NULL;


        if( $rows>0 )   //--- update
        {
           // echo $this->subscr;
            $Logon = new UserAuthorize();
            $q = "UPDATE `".TblModUser."` SET
                      `name`='".$this->name."',
                      `phone_mob`='".$this->phone_mob."'
                  ";
            //if update make User (not adminitsrator), then save IP address of this user.
            if( $Logon->user_id==$this->user_id){
                $q = $q.", `ip`='".$ip."'";
                if( !empty($ip_ctry) ) $q = $q.", `ip_ctry`='".$ip_ctry."'";
            }
            if(isset($this->expertTitle)){
                $q.=", `expertTitle`='".$this->expertTitle."'";
            }
            if(isset($this->ShowInTop) && $this->ShowInTop==1){
                $q.=", `ShowInTop`='1'";
            }else{
                $q.=", `ShowInTop`='0'";
            }
            if( isset($this->bonuses) ){
                $q = $q.", `bonuses`='".$this->bonuses."'";
            }
            if(isset($this->aboutMe)){ $q.=", `aboutMe`='".$this->aboutMe."'"; }
            if(isset($this->adr)){ $q.=", `adr`='".$this->adr."'"; }

            $q = $q." WHERE `sys_user_id`='".$this->user_id."'";
            //echo $q;die();
        }
        else   //--- insert
        {
            $q = "INSERT INTO `".TblModUser."` SET
                  `sys_user_id`='".$this->user_id."',
                  `name`='".$this->name."',


                  `phone_mob`='".$this->phone_mob."',

                  `user_status`='".$this->user_status."',
                  `ip`='".$ip."'

                  ";
            if( !empty($ip_ctry) ) $q = $q.", `ip_ctry`='".$ip_ctry."'";
            if( isset($this->bonuses) ){
                $q = $q.", `bonuses`='".$this->bonuses."'";
            }
            if(isset($this->ShowInTop) && $this->ShowInTop==1){
                $q.=", `ShowInTop`='1'";
            }else{
                $q.=", `ShowInTop`='0'";
            }
            if(isset($this->expertTitle)){
                $q.=", `expertTitle`='".$this->expertTitle."'";
            }

            if(isset($this->aboutMe)){ $q.=", `aboutMe`='".$this->aboutMe."'"; }
            if(isset($this->adr)){ $q.=", `adr`='".$this->adr."'"; }
        }
        $res = $this->db->db_Query( $q );
        //echo '<br>$q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;echo "rows=".$rows;
        if( !$res OR !$this->db->result ) return false;
        return true;
       } //end of fuinction SaveUser()


        function getExtension($fileName) {
                return substr(strrchr($fileName, '.'), 1);
        }

    function saveUserAvatar(){
        $this->Err = NULL;//$this->userAvatar
        $max_image_width= 1024;
        $max_image_height= 1024;
        $max_image_size= MAX_IMAGE_SIZE;
        $max_image_quantity = MAX_IMAGES_QUANTITY;
        $SITE_PATH=$_SERVER['DOCUMENT_ROOT'];
        //print_r($_FILES["image"]);
        $filepath=$SITE_PATH."/uploads/tmpAva/".$this->userAvatar;

        if ( file_exists ($filepath) && $this->userAvatar!=NULL && !empty($this->userAvatar)){
            $size = GetImageSize($filepath);
            $alias = $this->user_id;
            $folder = $SITE_PATH."/images/mod_blog/";
            if ( !file_exists ($folder) ) mkdir($folder,0777);
            else @chmod($folder,0777);
            $uploaddir = $folder.$alias;
            $uploaddir_0 =$uploaddir;
            $ext=$this->getExtension($this->userAvatar);
            if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777);
            else @chmod($uploaddir,0777);
            $uploaddir2 = time().'e.'.$ext;
            $uploaddir = $uploaddir."/".$uploaddir2;

            if ( @copy($filepath,$uploaddir) ) {
                $q="UPDATE `".TblModUser."` SET `avatar`='".$uploaddir2."' WHERE `sys_user_id`=".$alias."";
                $res = $this->db->db_Query( $q );
                if( !$this->db->result ) $this->Err = $this->Err.'Помилка додавання файлу аватара до бази данних.<br>';
                //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
                unlink($filepath);
                if (($size) AND (($size[0] > $max_image_width) OR ($size[1] > $max_image_height)) ){
                    //============= resize original image to size from settings =============
                    $thumb = new Thumbnail($uploaddir);
                    if($max_image_width==$max_image_height) $thumb->size_auto($max_image_width);
                    else{
                           $thumb->size_width($max_image_width);
                           $thumb->size_height($max_image_height);
                    }
                    $thumb->quality = $max_image_quantity;
                    $thumb->process();       // generate image
                    $thumb->save($uploaddir); //make new image
                    //=======================================================================
                }
            }else{
                $this->Err = $this->Err.'Помилка переміщення файлу аватара<br>';
            }
            @chmod($uploaddir_0,0755);
            @chmod($folder,0755);
        }
        return $this->Err;
    }

       function saveUserAvatarExpert(){
           $this->Err = NULL;//$this->userAvatar
         $max_image_width= 1024;
         $max_image_height= 1024;
         $max_image_size= MAX_IMAGE_SIZE;
         $max_image_quantity = MAX_IMAGES_QUANTITY;
         $SITE_PATH=$_SERVER['DOCUMENT_ROOT'];
         //print_r($_FILES["image"]);
         $filepath=$SITE_PATH."/uploads/tmpAva/".$this->expertImg;
        //$image=open_image($filepath);

               if ( file_exists ($filepath) && $this->expertImg!=NULL && !empty($this->expertImg)){
                  $size = GetImageSize($filepath);
                     $alias = $this->user_id;
                     $uploaddir = $SITE_PATH."/images/mod_blog/".$alias;
                     $uploaddir_0 =$uploaddir;
                     $ext=$this->getExtension($this->expertImg);
                     if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777);
                     else @chmod($uploaddir,0777);
                                     $uploaddir2 = time().'e.'.$ext;
                                     $uploaddir = $uploaddir."/".$uploaddir2;

                     if ( @copy($filepath,$uploaddir) ) {
                         $q="UPDATE `".TblModUser."` SET `expertImg`='".$uploaddir2."' WHERE `sys_user_id`=".$alias."";
                         $res = $this->db->db_Query( $q );
                         if( !$this->db->result ) $this->Err = $this->Err.'Помилка додавання файлу аватара до бази данних.<br>';
                         //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
                         unlink($filepath);
                                             if (($size) AND (($size[0] > $max_image_width) OR ($size[1] > $max_image_height)) ){
                                                     //============= resize original image to size from settings =============
                                                     $thumb = new Thumbnail($uploaddir);
                                                     if($max_image_width==$max_image_height) $thumb->size_auto($max_image_width);
                                                     else{
                                                            $thumb->size_width($max_image_width);
                                                            $thumb->size_height($max_image_height);
                                                     }
                                                     $thumb->quality = $max_image_quantity;
                                                     $thumb->process();       // generate image
                                                     $thumb->save($uploaddir); //make new image
                                                     //=======================================================================
                                             }
                                     }
                     else {
                         $this->Err = $this->Err.'Помилка переміщення файлу аватара<br>';
                     }
                     @chmod($uploaddir_0,0755);

                  //}
                  //else {
                  //   $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_PROPERTIES').' ['.$max_image_width.'x'.$max_image_height.']px ('.$_FILES['image']['name']["$i"].')<br>';
                  //}
               }
               //else $this->Err = $this->Err.'Файл відсутній на сервері';
             //echo '<br>$i='.$i;

         return $this->Err;
       }


        function saveUserAvatarHeader(){
           $this->Err = NULL;//$this->userAvatar
         $max_image_width= 200;
         $max_image_height= 200;
         $max_image_size= MAX_IMAGE_SIZE;
         $max_image_quantity = MAX_IMAGES_QUANTITY;
         $SITE_PATH=$_SERVER['DOCUMENT_ROOT'];
         //print_r($_FILES["image"]);
         $filepath=$SITE_PATH."/uploads/tmpAva/".$this->expertImgHeader;
        //$image=open_image($filepath);

               if ( file_exists ($filepath) && $this->expertImgHeader!=NULL && !empty($this->expertImgHeader)){
                  $size = GetImageSize($filepath);
                     $alias = $this->user_id;
                     $uploaddir = $SITE_PATH."/images/mod_blog/".$alias;
                     $uploaddir_0 =$uploaddir;
                     $ext=$this->getExtension($this->expertImgHeader);
                     if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777);
                     else @chmod($uploaddir,0777);
                                     $uploaddir2 = time().'h.'.$ext;
                                     $uploaddir = $uploaddir."/".$uploaddir2;
                     //$uploaddir = $uploaddir."/".$_FILES['image']['name']["$i"];
                     //$uploaddir2 = $_FILES['image']['name']["$i"];

                     //echo '<br>$filename='.$filename.'<br> $uploaddir='.$uploaddir.'<br> $uploaddir2='.$uploaddir2;
                     //if (@move_uploaded_file($filename, $uploaddir)) {
                     if ( @copy($filepath,$uploaddir) ) {
                         $q="UPDATE `".TblModUser."` SET `expertImgHeader`='".$uploaddir2."' WHERE `sys_user_id`=".$alias."";
                         $res = $this->db->db_Query( $q );
                         if( !$this->db->result ) $this->Err = $this->Err.'Помилка додавання файлу аватара до бази данних.<br>';
                         //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
                         unlink($filepath);
                                             if (($size) AND (($size[0] > $max_image_width) OR ($size[1] > $max_image_height)) ){
                                                     //============= resize original image to size from settings =============
                                                     $thumb = new Thumbnail($uploaddir);
                                                     if($max_image_width==$max_image_height) $thumb->size_auto($max_image_width);
                                                     else{
                                                            $thumb->size_width($max_image_width);
                                                            $thumb->size_height($max_image_height);
                                                     }
                                                     $thumb->quality = $max_image_quantity;
                                                     $thumb->process();       // generate image
                                                     $thumb->save($uploaddir); //make new image
                                                     //=======================================================================
                                             }
                                     }
                     else {
                         $this->Err = $this->Err.'Помилка переміщення файлу аватара<br>';
                     }
                     @chmod($uploaddir_0,0755);

                  //}
                  //else {
                  //   $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_PROPERTIES').' ['.$max_image_width.'x'.$max_image_height.']px ('.$_FILES['image']['name']["$i"].')<br>';
                  //}
               }
               //else $this->Err = $this->Err.'Файл відсутній на сервері';
             //echo '<br>$i='.$i;

         return $this->Err;
       }
       // ================================================================================================
       // Function : SaveToSysUser()
       // Version : 1.0.0
       // Date : 09.01.2006
       //
       // Parms :
       // Returns :      true,false / Void
       // Description :  Add External user to table sys_user
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 09.01.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function SaveToSysUser($group_id=NULL)
       {
           $Crypt = new Crypt();
           if(empty($group_id)) $group_id=5;
           //if(empty($this->password)) $this->password = $Crypt->GetRandLetterStr(6,2);
           if(empty($this->password)) $this->password = $Crypt->GetRandNumStr(7);
           $pass = $this->EncodePass($this->login, $this->password, $group_id);

           if( empty($this->enrol_date) ) $enrol_date = date("Y-m-d");
           else $enrol_date = $this->enrol_date;

           if( empty($this->enrol_date) ) $login_multi_use = $this->login_multi_use;
           else $login_multi_use=NULL;
          // echo $group_id." ".$this->login." ".$pass." ".$enrol_date." ".$login_multi_use." ".$this->alias;
           $res = $this->save_sys_user($group_id, $this->login, $pass, $enrol_date, $login_multi_use, NULL, NULL, NULL, $this->email);

           if (empty($res)) return false;
           return $res;
       } //end of fuinction SaveToSysUser()

       // ================================================================================================
       // Function : ActivateUser()
       // Version : 1.0.0
       // Date : 11.01.2006
       //
       // Parms :
       // Returns : true,false / Void
       // Description : Set status of user as Registrated
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 11.01.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function ActivateUser( $activate_user )
       {
        /*
        $q = "SELECT * FROM ".TblModUser." WHERE `email`='$activate_user'";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        if( !$res ) return false;
        $rows = $this->Right->db_GetNumRows();
        //echo '<br>$q='.$q.'$rows='.$rows;
        if( $rows==0 ) return false;
        */
        $q = "UPDATE `".TblModUser."` SET `user_status`=3, `activate_code`='' WHERE `activate_code` = '".$activate_user."'";
        $res = $this->db->db_Query( $q );
        //echo '<br>$q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;

        if( !$res ) {return false;}
        return true;
       } //end of fuinction save()

       // ================================================================================================
       // Function : SendHTML
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
        function SendHTML( $auto = NULL )
        {
            $code = $this->GetActivateCode($this->email);
            if ( empty($auto) ){
                $txt = '
                <H1>Вы успешно зарегистрировались на сайте ' . $_SERVER['SERVER_NAME'] . '</H1><br>
                ';
            }
            else{
                $txt = '
                <H1>Для вас создан аккаунт на сайте ' . $_SERVER['SERVER_NAME'] . '</H1><br>
                ';
            }
            //.$this->Msg->show_text('TXT_ACTIVATE_PROFILE').' <a href="http://'.$_SERVER['SERVER_NAME'].'/login.php?task=activate_user&activate_user='.$code.'">http://'.$_SERVER['SERVER_NAME'].'/login.php?task=activate_user&activate_user='.$code.'</a><br><br>

            $info = "
            <table border=0 cellspacing=1 cellpadding=2 align=center class='email_table2'>
             <tr class='email_td'><td colspan=2 align=center><b>Данные вашего аккаунта:</b>";

            if ( isset($this->phone_mob) ){
                $info .= "
                     <tr class='email_td'>
                      <td>Номер телефона:</td>
                      <td>".$this->phone_mob."</td>
                     </tr>
                ";
            }

            $info .= "
             <tr class='email_td'>
              <td>E-mail:</td>
              <td>".$this->email."</td>
             </tr>
             <tr class='email_td'>
              <td>Пароль:</td>
              <td>".$this->password."</td>
             </tr>

            </table>
            ";
             //echo '<br> info='.$info;

            //-------------Send to User ---------------
            $subject = "Регистрация на сайте ".$_SERVER['SERVER_NAME'];
            $body = $txt.$info;
            //echo $body;
            $mail = new Mail();
            $mail->AddAddress($this->email);
            //$mail->AddReplyTo("ihor@seotm.com");
            $mail->WordWrap = 500;
            $mail->IsHTML( true );
            //$mail->IsHTML( true );
            $mail->Subject = $subject;
            $mail->Body = $body;
            $send_res = $mail->SendMail();
            //var_dump($send_res);
            if( !$send_res ) {return false;}
            //echo '<br>$send_res='.$send_res;
            //-------------Send to Admin ---------------
            $body = "<b>копия админу:</b><br/>".$txt.$info;
            $mail2 = new Mail();
            $SysSet = new SysSettings();
            $sett = $SysSet->GetGlobalSettings();
            if( !empty($sett['mail_auto_emails'])){
                $hosts = explode(";", $sett['mail_auto_emails']);
                for($i=0;$i<count($hosts);$i++){
                    //$arr_emails[$i]=$hosts[$i];
                    if( isset($hosts[$i]) AND !empty($hosts[$i]) ) $mail2->AddAddress($hosts[$i]);
                }//end for
            }
            $mail2->WordWrap = 500;
            $mail2->IsHTML( true );
            $mail2->Subject = $subject;
            $mail2->Body = $body;
            $res2 = $mail2->SendMail();
            //var_dump($res2);
            if( !$res2 ) {return false;}
            return true;
        } //end of function SendHTML()

       // ================================================================================================
       // Function : SendSysEmail
       // Version : 1.0.0
       // Date : 18.01.2007
       //
       // Parms :   $sbj        - subject of email
       //           $body       - body of email
       //           $arr_emails - array with emails whrere to send ($arr_emails[0]='iii@ii.i'
       //                                                           $arr_emails[1]='aaa@aa.a')
       // Returns : $res / Void
       // Description : Function for send emails
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 18.01.2007
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function SendSysEmail($sbj=NULL, $body=NULL, $arr_emails=NULL, $IsHTML=true, $insert_header=true, $insert_footer=true)
       {
           if( empty($sbj) ) $sbj=NULL;
           $mail = new Mail(_LANG_ID);
           $arr_html_img = $mail->ConvertHtmlWithImagesForSend($body);
           foreach($arr_html_img as $key=>$value){
              //echo '<br>$key='.$key.' $value='.$value;
              if( $key!='content') $mail->AddAttachment($key);
              else $body = $value;
           }
           //$mail = new Mail($this->lang_id_for_send_emails);
           if (is_array($arr_emails) AND count($arr_emails)>0){
               foreach($arr_emails as $k=>$v){
                   //echo '<br>$v='.$v;

                   $mail->AddAddress($v);
                   //if (empty($headers)) $headers = "To: $v \r\n";
                   //else $headers .= ",$v \r\n";
               }
               //$mail->AddReplyTo("info@biletik.ua",$this->Msg->show_text('_TITLE', TblSysTxt));
               $mail->WordWrap = 500;
               $mail->IsHTML( $IsHTML );
               $mail->Subject = $sbj;
               $mail->insert_header = $insert_header;
               $mail->insert_footer = $insert_footer;
               $mail->Body = $body;
               $res = $mail->SendMail();
               //$res = true;
               //echo '<br>$res='.$res.' $sbj='.$sbj.'<br>$body='.$body.'<br>$arr_emails='.$arr_emails.' $IsHTML='.$IsHTML;
               if( !$res ) {return false;}

               //$headers .= "From: info@{$_SERVER['SERVER_NAME']}";
               //if ( !mail("info@art-dating.com", $sbj, $body, $headers) ) return false;
           }
           else return false;
           return true;
       } //End of function SendSysEmail()

       // ================================================================================================
       // Function : CheckEmailFields()
       // Version : 1.0.0
       // Date : 30.01.2006
       //
       // Parms :
       // Returns :      true,false / Void
       // Description :  Check fields of email for validation
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 30.01.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function CheckEmailFields($param=NULL)
       {
         $this->Err=NULL;

         if (empty( $this->email )) $this->Err = $this->Err.$this->Msg->show_text('MSG_FLD_EMAIL_EMPTY').'<br>';
         elseif($param!='forgotpass'){
            if ( $this->email!=$this->email2 ) $this->Err = $this->Err.$this->Msg->show_text('MSG_NOT_MATCH_REENTER_EMAIL').'<br>';
            if (!ereg("^[a-zA-Z0-9_.\-]+@[a-zA-Z0-9.\-].[a-zA-Z0-9.\-]+$", $this->email)) $this->Err = $this->Err.$this->Msg->show_text('MSG_NOT_VALID_EMAIL').'<br>';
         }
         return $this->Err;
       } //end of fuinction CheckEmailFields()


       // ================================================================================================
       // Function : SetNewPass()
       // Version : 1.0.0
       // Date : 30.01.2006
       //
       // Parms :        $login - login of the user
       // Returns :      true,false / Void
       // Description :  Set new password of the user with login = $this->email
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 30.01.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function SetNewPass($login = NULL)
       {
         $Crypt = new Crypt();
         $this->password = $Crypt->GetRandLetterStr(6,2);
         if ( !$this->change_pass($login, $this->password) ) return false;
         return true;
       } //end of fuinction SetNewPass()


       // ================================================================================================
       // Function : SendNewPass
       // Version : 1.0.0
       // Date : 30.01.2006
       //
       // Parms :
       // Returns : true,false / Void
       // Description : Send new Password to the user.
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 30.01.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function SendNewPass()
       {
         $user_name = $this->GetUserName($this->email);
         $user_data = $this->GetUserDataByUserLogin($this->phone_mob);
         //var_dump($user_data);

         $subject = "Ваш пароль на ".NAME_SERVER;
         $body = "Уважаемый(ая) ".$user_name.",
         <br/>Ваш логин: <b>".$user_data['login']."</b>
         <br/>Ваш пароль: <b>".$this->password."</b>";
         $body .= '<br/>
         Для авторизации на сайте используйте страницу <a href="http://'.$_SERVER['SERVER_NAME'].'/login.html">http://'.$_SERVER['SERVER_NAME'].'/login.html</a>';
         $arr_emails[0]=$this->email;
         echo '<br>$this->email='.$this->email;
         $res = $this->SendSysEmail($subject, $body, $arr_emails);
         if( !$res ) return false;
         return true;
       } //end of function SendNewPass()


       // ================================================================================================
       // Function : CheckPassFields()
       // Version : 1.0.0
       // Date : 30.01.2006
       //
       // Parms :
       // Returns :      true,false / Void
       // Description :  Check fields of password for validation
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 30.01.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function CheckPassFields($login)
       {
         $this->Err=NULL;
         if(!empty($this->id)){
             //for existing users check current password to change it to new one.
             if ( empty( $this->oldpass ) ) $this->Err = $this->Err.$this->Msg->show_text('MSG_FLD_OLD_PASSWORD_EMPTY').'<br>';
             else{
                if ( !$this->CheckPassword($login, $this->oldpass) ) $this->Err = $this->Err.$this->Msg->show_text('MSG_INCORRECT_OLD_PASSWORD').'<br>';
             }
         }
         if ( empty( $this->password ) ) $this->Err = $this->Err.$this->Msg->show_text('MSG_FLD_NEW_PASSWORD_EMPTY').'<br>';
         if ( empty( $this->password2 ) ) $this->Err = $this->Err.$this->Msg->show_text('MSG_FLD_CONFIRM_PASSWORD_EMPTY').'<br>';
         else {
             if ( $this->password!=$this->password2 ) $this->Err = $this->Err.$this->Msg->show_text('MSG_NOT_MATCH_CONFIRM_PASSWORD').'<br>';
         }
         if (strlen($this->password)<4) $this->Err = $this->Err.$this->Msg->show_text('MSG_PASS_TOO_SHORT').'<br>';
         return $this->Err;
       } //end of fuinction CheckPassFields()



       // ================================================================================================
       // Function : GetUserStatus()
       // Version : 1.0.0
       // Date : 30.01.2006
       //
       // Parms :        $login / email of the user
       // Returns :      satus of the user
       // Description :  return status of the user
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 30.01.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function GetUserStatus($sys_user_id)
       {
           $q = "SELECT `user_status` from ".TblModUser." where sys_user_id='".$sys_user_id."'";
           $res = $this->db->db_Query( $q );
           //echo '<br>'.$q.' $res='.$res.' $this->db->result='.$this->db->result;
           if ( !$res OR !$this->db->result ) return false;
           $row = $this->db->db_FetchAssoc();
           return $row['user_status'];
       } //end of fuinction GetUserStatus()

       function GetUserStatusByLogin($login)
       {
           $q = "SELECT `user_status`
                    from ".TblModUser." , ".TblSysUser."
                    where
               ".TblModUser.".sys_user_id= ".TblSysUser.".id
                   AND ".TblSysUser.".login = '".$login."'";
           $res = $this->db->db_Query( $q );
           //echo '<br>'.$q.' $res='.$res.' $this->db->result='.$this->db->result;
           if ( !$res OR !$this->db->result ) return false;
           $row = $this->db->db_FetchAssoc();
           return $row['user_status'];
       } //end of function GetUserStatus()

       // ================================================================================================
       // Function : SetActivateCode()
       // Version : 1.0.0
       // Date : 12.05.2008
       //
       // Parms :        $login / email of the user
       // Returns :      true/false
       // Description :  save to database activation code for user with login $login
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 12.05.2008
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function SetActivateCode($login)
       {
           $tmp_db = new DB();
           $Crypt = new Crypt();
           $code = $Crypt->GetRandCharStr(20, 1);
           $q = "UPDATE `".TblModUser."` SET `activate_code`='".$code."' WHERE `email`='".$login."'";
           $res = $tmp_db->db_Query( $q );
           //echo '<br>'.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result;
           if ( !$res OR !$tmp_db->result ) {$tmp_db->db_Close(); return false;}
           $tmp_db->db_Close();
           return true;
       }//end of fuinction SetActivateCode()

       // ================================================================================================
       // Function : GetActivateCode()
       // Version : 1.0.0
       // Date : 12.05.2008
       //
       // Parms :        $login / email of the user
       // Returns :      activation code of the user
       // Description :  return activation code for user with login $login
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 12.05.2008
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function GetActivateCode($login)
       {
           $tmp_db = new DB();
           $q = "SELECT `activate_code` FROM ".TblModUser." WHERE `email`='".$login."'";
           $res = $tmp_db->db_Query( $q );
           //echo '<br>'.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result;
           if ( !$res OR !$tmp_db->result ) {$tmp_db->db_Close(); return false;}
           $row = $tmp_db->db_FetchAssoc();
           $tmp_db->db_Close();
           return $row['activate_code'];
       }//end of fuinction GetActivateCode()

       // ================================================================================================
       // Function : GetLoginByActivateCode()
       // Version : 1.0.0
       // Date : 12.05.2008
       //
       // Parms :        $login / email of the user
       // Returns :      login of the user
       // Description :  return logni code for user by activate code
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 12.05.2008
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function GetLoginByActivateCode($code=NULL)
       {
           if( empty($code) ) return false;
           $tmp_db = new DB();
           $q = "SELECT `email` FROM ".TblModUser." WHERE `activate_code`='".$code."'";
           $res = $tmp_db->db_Query( $q );
           //echo '<br>'.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result;
           if ( !$res OR !$tmp_db->result ) {$tmp_db->db_Close(); return false;}
           $row = $tmp_db->db_FetchAssoc();
           $tmp_db->db_Close();
           return $row['email'];
       }//end of fuinction GetLoginByActivateCode()




       function GetUserLoginByUserAlias($alias){
           $tmp_db = new DB();
           $q = "SELECT `login` FROM ".TblSysUser." WHERE `alias`='".$alias."'";
           $res = $tmp_db->db_Query( $q );
        //   echo '<br>'.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result;
           if ( !$res OR !$tmp_db->result ) {$tmp_db->db_Close(); return false;}
           $row = $tmp_db->db_FetchAssoc();
           $tmp_db->db_Close();
           return $row['login'];
       }



      function GetUserDiscount($user_id){
         if( empty($user_id) ) return false;
           $tmp_db = new DB();
           $q = "SELECT `discount` FROM ".TblModUser." WHERE `id`='".$user_id."'";
           $res = $tmp_db->db_Query( $q );
        //   echo '<br>'.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result;
           if ( !$res OR !$tmp_db->result ) {$tmp_db->db_Close(); return false;}
           $row = $tmp_db->db_FetchAssoc();
           $tmp_db->db_Close();
           return $row['discount'];
       } // end of GetUserDiscount


// ================================================================================================
// Function : GetUserStatusByActivateCode()
// Version : 1.0.0
// Date : 04.11.2009
//
// Parms :        $code / activate code of the user
// Returns :      satus of the user
// Description :  return status of the user
// ================================================================================================
// Programmer :  Igor Trokhymchuk
// Date : 04.11.2009
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
function GetUserStatusByActivateCode($code)
{
   $q = "SELECT `user_status` from ".TblModUser." where `activate_code`='".$code."'";
   $res = $this->db->db_Query( $q );
   //echo '<br>'.$q.' $res='.$res.' $this->db->result='.$this->db->result;
   if ( !$res OR !$this->db->result ) return false;
   $row = $this->db->db_FetchAssoc();
   return $row['user_status'];
} //end of fuinction GetUserStatusByActivateCode()

 } // End of class User

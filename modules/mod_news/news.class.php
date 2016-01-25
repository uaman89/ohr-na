<?
// ================================================================================================
//    System     : CMS
//    Module     : News
//    Date       : 04.02.2007
//    Licensed To:   Yaroslav Gyryn
//    Purpose    : Class definition for News - moule
// ================================================================================================

 include_once( SITE_PATH.'/modules/mod_news/news.defines.php' );

// ================================================================================================
//    Class             : News
//    Date              : 23.05.2007
//    Constructor       : Yes
//    Returns           : None
//    Description       : News Module
//    Programmer      :  Yaroslav Gyryn
// ================================================================================================
class News {

    var $Right;
    var $Form;
    var $Msg;
    var $Spr;
    var $db;

    var $page;
    var $display;
    var $sort;
    var $start;
    var $user_id;
    var $module;
    var $fltr;    // filter of group news
    var $id_news = NULL;
    var $width = NULL;
    var $id = NULL;
    var $img = NULL;
    var $search_keywords =NULL;
    var $category = NULL;
    var $sel = NULL;
    var $Err = NULL;
    var $script = NULL;
    var $title = NULL;
    var $source;

    var $keywords = NULL;
    var $description = NULL;
    var $lang_id = NULL;

    var $str_cat = NULL;
    var $str_news = NULL;

    var $subscriber = NULL;
    var $subscr_pass = NULL;
    var $categories = NULL;

    var $subscr = NULL;
    var $full_descr = NULL;
    var $rewrite = NULL;
    var $dt = NULL;
    var $img_path = NULL;
    var $task = NULL;
    var $rss = NULL;
    var $rss_impor = NULL;
    var $treeNewsCat = NULL;

    // ================================================================================================
    //    Function          : News (Constructor)
    //    Date              : 04.02.2005
    //    Description       : News
    // ================================================================================================
    function News()
    {
        $this->db =  DBs::getInstance();


        if (empty($this->Spr)) $this->Spr = check_init('SysSpr', 'SysSpr');
        $this->width = '750';
        if( defined("_LANG_ID") ) $this->lang_id = _LANG_ID;
        if (empty($this->Crypt)) $this->Crypt = check_init('Crypt', 'Crypt');
        if(empty($this->settings)) $this->settings = $this->GetSettings();

        $this->updateStatus();
        $this->loadTreeNewsCat();
    }

    // ================================================================================================
    // Function : GetNewsCategory()
    // Date :    25.09.2006
    // Parms :   $id - news id
    // Returns : true/false
    // Description : get categ of news
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GetNewsCategory($id){
      $q = "select id_category from `".TblModNews."` where 1 and `status`='a' and `id`='".$id."'";
      $res = $this->db->db_Query($q);
      $row = $this->db->db_FetchAssoc($res);
      return $row['id_category'];
    } //end of function GetNewsCategory

    // ================================================================================================
    // Function : isNews()
    // Date :    10.06.2013
    // Returns : count of news
    // Description : get count of news
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function isNews(){
        $q = "select *
        from `".TblModNews."`,`".TblModNewsNames."`
        where `".TblModNews."`.`status`='a'
        and `".TblModNews."`.`id` = `".TblModNewsNames."`.`id_news`
        and `".TblModNewsNames."`.`lang_id` = '".$this->lang_id."'
        and `".TblModNewsNames."`.`name` !='' ";
        $res = $this->db->db_Query($q);
//        echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
        if( !$res OR !$this->db->result ) return 0;
        return $this->db->db_GetNumRows();
    } //end of function GetNewsCategory

    // ================================================================================================
    // Function : updateStatus()
    // Date :    07.11.2013
    // Parms :   $id - poll id
    // Returns : true/false
    // Description : Update News Status
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function updateStatus( $id = NULL ){
        $q = "select * from ".TblModNews."  where ";
        if(empty($id))$q .= " `status` = 'a' or `status`='n' or `status`='i'";
        else $q .= " `id` = '".$id."' ";
        $res = $this->db->db_Query( $q );
//          echo '<br/> $q='.$q.' $res='.$res;
        if(!$res) return false;
        $rows = $this->db->db_GetNumRows();
        $arr = array();
        for( $i = 0; $i < $rows; $i++ ){
            $arr[] = $this->db->db_FetchAssoc();
        }

        $dt_now = strftime('%Y-%m-%d %H:%M', strtotime('now'));
        for( $i = 0; $i < $rows; $i++ ){
            $tmp = $arr[$i];
            $status = '';
//              echo '<br>start='.$tmp['start_date'].' end='.$tmp['end_date'].' now='.$dt_now;
            if($tmp['end_date'] > $dt_now && $tmp['start_date'] < $dt_now){
                $status = 'a';
            }elseif($tmp['end_date'] < $dt_now){
                $status = 'e';
            }elseif($tmp['start_date'] > $dt_now){
                $status = 'n';
            }
            if($tmp['status']!=$status){
                $q = "update ".TblModNews." set `status`='".$status."' where `id`='".$tmp['id']."'";
                $res = $this->db->db_Query( $q );
//                  echo '<br/> $q='.$q.' $res='.$res;
            }
        }
        return  true;
    } //--- end of CheckStatus

    // ================================================================================================
    // Function : loadTreeNewsCat()
    // Date :    18.06.2013
    // Returns : treeNewsCat
    // Description : load Tree News Category
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function loadTreeNewsCat(){
        $q = "select * from ".TblModNewsCat." where `lang_id`='".$this->lang_id."'";
        $res = $this->db->db_Query( $q );
//        echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
        if( !$res OR !$this->db->result ) return 0;
        $rows = $this->db->db_GetNumRows();
        for( $i = 0; $i < $rows; $i++ )
        {
            $row = $this->db->db_FetchAssoc();
            $this->treeNewsCat[$row['cod']] = $row;
        }
    }

     // ================================================================================================
     // Function : ConvertDate()
     // Date : 12.05.2011
     // Returns :      true,false / Void
     // Description :  Convert Date Time
     // Programmer :  Yaroslav Gyryn
     // ================================================================================================
     function ConvertDate($date_to_convert, $showTimeOnly = false, $showMonth = false){
        $tmp = explode("-", $date_to_convert);
        $tmp2 = explode(" ", $tmp[2]);
        $month = NULL;
        $day = NULL;
        $year = NULL;
        $month =  $tmp[1];
        $day = intval($tmp2[0]);
        $year = $tmp[0];
        if($showMonth) {
            $month = intval($month);
            if(!isset($this->month[$month]))
                $this->month[$month] = $this->Spr->GetShortNameByCod(TblSysSprMonth, $month, $this->lang_id, 1);
            $month =  $this->month[$month];
            return $day." ".$month;
        }
        if($showTimeOnly) {
            $time = $tmp2[1];
            $tmp3 = explode(":", $time);
            return $tmp3[0].':'.$tmp3[1];      //18:30
        }
        return $day.".".$month.".".$year;
    } // end of function ConvertDate()



    // ===========================================================================================================
    // Function    : GetStartDate()
    // Date        : 02.04.2007
    // Parms       : $start_date - date to convert
    // Returns     : true,false / Void
    // Description : Return start date of news
    // Programmer :  Yaroslav Gyryn
    // ===========================================================================================================
    function GetStartDate($start_date)
    {
           $tmp = explode( '-', $start_date );
           $tmp1 = explode( ' ', $tmp[2] );
           $tmp2 = explode( ':', $tmp1[1] );
           $start_date = $tmp[0].$tmp[1].$tmp1[0].$tmp2[0].$tmp2[1].$tmp2[2];
           return $start_date;
    } // end of function GetStartDate

    // ===========================================================================================================
    // Function    : GetEndDate()
    // Date        : 02.04.2007
    // Parms       : $end_date - date to convert
    // Returns     : true,false / Void
    // Description : Return end date of news
    // Programmer :  Yaroslav Gyryn
    // ===========================================================================================================
    function GetEndDate($end_date)
    {
           $tmp = explode( '-', $end_date );
           $tmp1 = explode( ' ', $tmp[2] );
           $tmp2 = explode( ':', $tmp1[1] );
           $end_date = $tmp[0].$tmp[1].$tmp1[0].$tmp2[0].$tmp2[1].$tmp2[2];
           return $end_date;
    } // end of function GetEndDate

    // ===========================================================================================================
    // Function    : GetCurrentDate()
    // Date        : 02.04.2007
    // Returns     : true,false / Void
    // Description : Return Current Date for news
    // Programmer :  Yaroslav Gyryn
    // ===========================================================================================================
    function GetCurrentDate()
    {
           $date = date('Y-m-d H:i:s');
           $tmp = explode( '-', $date );
           $tmp1 = explode( ' ', $tmp[2] );
           $tmp2 = explode( ':', $tmp1[1] );
           $date = $tmp[0].$tmp[1].$tmp1[0].$tmp2[0].$tmp2[1].$tmp2[2];
           return $date;
    } // end of function GetCurrentDate

    // ================================================================================================
    // Function : SavePictureTop
    // Date : 03.04.2011
    // Returns : $res / Void
    // Description : Save the file (image) to the folder  and save path in the database (table user_images)
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function SavePictureTop()
    {
         $this->Err = NULL;
         $max_image_width= NEWS_MAX_IMAGE_WIDTH;
         $max_image_height= NEWS_MAX_IMAGE_HEIGHT;
         $max_image_size= NEWS_MAX_IMAGE_SIZE;
         $valid_types =  array("gif", "GIF", "jpg", "JPG", "png", "PNG", "jpeg", "JPEG");
         //$ln_arr = $ln_sys->LangArray( _LANG_ID );
         //print_r($_FILES["topImage"]);
         if (!isset($_FILES["topImage"])) return false;
         $cols = count($_FILES["topImage"]["name"]);
         for ($i=0; $i<$cols; $i++) {
             //echo '<br>$_FILES["topImage"]='.$_FILES["topImage"].' $_FILES["topImage"]["tmp_name"]["'.$i.'"]='.$_FILES["topImage"]["tmp_name"]["$i"].' $_FILES["topImage"]["size"]["'.$i.'"]='.$_FILES["topImage"]["size"]["$i"];
             //echo '<br>$_FILES["topImage"]["name"][$i]='.$_FILES["topImage"]["name"][$i];
             if ( !empty($_FILES["topImage"]["name"][$i]) ) {
               if ( isset($_FILES["topImage"]) && is_uploaded_file($_FILES["topImage"]["tmp_name"][$i]) && $_FILES["topImage"]["size"][$i] ){
                $filename = $_FILES['topImage']['tmp_name'][$i];
                $ext = substr($_FILES['topImage']['name'][$i],1 + strrpos($_FILES['topImage']['name'][$i], "."));
                //echo '<br>filesize($filename)='.filesize($filename).' $max_image_size='.$max_image_size;
                if (filesize($filename) > $max_image_size) {
                    $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_SIZE', TblSysTxt).' ('.$_FILES['topImage']['name']["$i"].')<br>';
                    continue;
                }
                if (!in_array($ext, $valid_types)) {
                    $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_TYPE', TblSysTxt).' ('.$_FILES['topImage']['name']["$i"].')<br>';
                }
                else {
                  $size = GetImageSize($filename);
                  //echo '<br>$size='.$size.'$size[0]='.$size[0].' $max_image_width='.$max_image_width.' $size[1]='.$size[1].' $max_image_height='.$max_image_height;
                  if (($size) && ($size[0] < $max_image_width) && ($size[1] < $max_image_height)) {

                      // Удаление предыдущего изображения для Топ Новости
                      $res = $this->DelTopPicture($this->id);

                     //$alias = $this->Spr->GetNameByCod( TblModCatalogPropSprName, $this->id );
                     if ( !file_exists (NewsImg_Full_Path) ) mkdir(NewsImg_Full_Path,0777);
                     $uploaddir = NewsImg_Full_Path.$this->id;
                     if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777);
                     else @chmod($uploaddir,0777);

                     $uploaddir2 = time().'_'.$i.'.'.$ext;
                     $uploaddir1 = $uploaddir."/".$uploaddir2;

                     //echo '<br>$filename='.$filename.'<br> $uploaddir='.$uploaddir.'<br> $uploaddir2='.$uploaddir2;
                     //if (@move_uploaded_file($filename, $uploaddir)) {
                     if ( copy($filename,$uploaddir1) ) {
                         //$q="INSERT into `".TblModNewsT."` values(NULL,'".$this->id."','".$uploaddir2."','1', '$maxx', NULL)";
                         $q = "UPDATE
                                    `".TblModNewsTop."`
                                SET
                                    `image`='".$uploaddir2."'
                                WHERE
                                    cod ='".$this->id."'
                                AND
                                    lang_id ='2'
                         ";
                         $res = $this->db->db_Query( $q );
                         if( !$res OR !$this->db->result )
                            $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_SAVE_FILE_TO_DB', TblSysTxt).' ('.$_FILES['topImage']['name']["$i"].')<br>';
                         //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
                     }
                     else {
                         $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_MOVE', TblSysTxt).' ('.$_FILES['topImage']['name']["$i"].')<br>';
                     }
                     @chmod($uploaddir,0755);
                     @chmod(NewsImg_Full_Path,0755);
                  }
                  else {
                     $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_PROPERTIES', TblSysTxt).' ['.$max_image_width.'x'.$max_image_height.'] ('.$_FILES['topImage']['name']["$i"].')<br>';
                  }
                }
               }
               else $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE', TblSysTxt).' ('.$_FILES['topImage']['name']["$i"].')<br>';
             }
             //echo '<br>$i='.$i;
         } // end for       */
         return $this->Err;
    }  // end of function SavePictureTop()


    // ================================================================================================
    // Function : DelTopPicture
    // Date : 07.04.2011
    // Parms :  $id - id news
    // Returns : $res / Void
    // Description : Remove Top images from table and disk
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function DelTopPicture($id)
    {
    $q = "SELECT image  FROM `".TblModNewsTop."` WHERE cod= '".$id."' ";
    $res = $this->db->db_Query( $q );
    //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
    if( !$res) return false;
    if( !$this->db->result ) return false;
    $rows = $this->db->db_GetNumRows();
    $arr = array();
    for($i=0; $i<$rows; $i++) {
        $arr[] = $this->db->db_FetchAssoc();
    }
    $del=0;
    $path='';
    for($i=0; $i<$rows; $i++){
        $row = $arr[$i];
        $path = NewsImg_Full_Path.'/'.$id.'/'.$row['image'];
        // delete file which store in the database
        if (file_exists($path)) {
            $res = unlink ($path);
            if( !$res ) return false;
        }
        $del=$del+1;
        $path = NewsImg_Full_Path.$id;
        if( is_dir($path) ){
        $handle = @opendir($path);
        //echo '<br> $handle='.$handle;
        $cols_files = 0;
        while ( ($file = readdir($handle)) !==false ) {
           //echo '<br> $file='.$file;
           $mas_file=explode(".",$file);
           $mas_img_name=explode(".",$row['image']);
           if ( strstr($mas_file[0], $mas_img_name[0].NEWS_ADDITIONAL_FILES_TEXT) and $mas_file[1]==$mas_img_name[1] ) {
              $res = @unlink ($path.'/'.$file);
              if( !$res ) return false;
           }
           if ($file == "." || $file == ".." ) {
               $cols_files++;
           }
        }
           closedir($handle);
       }
     }
     $n = $this->getImagesCount($id);
     if( $n==0 AND is_dir($path) ) $this->full_rmdir($path);

     return $del;
    } // end of function DelTopPicture()


     // ================================================================================================
     // Function : full_rmdir
     // Date : 07.04.2011
     // Parms :  $dirname - directory for full del
     // Returns : $res / Void
     // Description : Full remove directory from disk (all files and subdirectory)
     // Programmer : Yaroslav Gyryn
     // ================================================================================================
     function full_rmdir($dirname)
     {
        if ($dirHandle = opendir($dirname)){
            $old_cwd = getcwd();
            chdir($dirname);

            while ($file = readdir($dirHandle)){
                if ($file == '.' || $file == '..') continue;

                if (is_dir($file)){
                    if (!full_rmdir($file)) return false;
                }else{
                    if (!unlink($file)) return false;
                }
            }

            closedir($dirHandle);
            chdir($old_cwd);
            if (!rmdir($dirname)) return false;

            return true;
        }else{
            return false;
        }
     }

    // ================================================================================================
    // Function : GetImagesCount
    // Date : 28.11.2006
    // Parms : $id_news  / id of the user
    // Description : return count of images for current user with $id_news
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function getImagesCount($id_news)
    {
        $image = NULL;
        $q = "SELECT * FROM `".TblModNewsImg."` WHERE 1 AND `id_news`='$id_news' order by `move`";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if ( !$res or !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        return $rows;
    } //end of function GetImagesCount()

    // ================================================================================================
    // Function : GetImages
    // Date : 13.10.2006
    // Parms : $id_news  / id of the user
    // Returns : return $image for current value with cod=$cod
    // Description : return image for current value with cod=$cod, if it is exist
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetImages($id_news)
    {
        $image = NULL;

        $q = "SELECT * FROM `".TblModNewsImg."` WHERE 1 AND `id_news`='$id_news' order by `move`";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if ( !$res or !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        //echo '<br>$rows='.$rows;
        $arr = NULL;
        for($i=0; $i<$rows; $i++){
        $row = $this->db->db_FetchAssoc();
        //echo '<br>$row[id_val]'.$row['id_val'];
        $arr[$i] = $row['path'];
        }
        return $arr;
    } //end of function GetImages()


    // ================================================================================================
    // Function : GetImagesToShow
    // Date : 01.04.2011
    // Parms : $id_news  / id of the news
    // Returns : return all image data
    // Description : return image for current value
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GetImagesToShow($id_news)
    {
        $image = NULL;

        $q = "SELECT
                    `".TblModNewsImg."`.`id`,
                    `".TblModNewsImg."`.`path`,
                    `".TblModNewsImg."`.`show`,
                    `".TblModNewsImg."`.`move`,
                    `".TblModNewsImg."`.`path`,
                    `".TblModNewsImgSprName."`.name,
                    `".TblModNewsImgSprDescr."`.name as descr
                    FROM `".TblModNewsImg."`
                    LEFT JOIN (`".TblModNewsImgSprName."`, `".TblModNewsImgSprDescr."`)
                    ON
                     (
                     `".TblModNewsImg."`.`id`=`".TblModNewsImgSprName."`.`cod`
                        AND
                     `".TblModNewsImg."`.`id`=`".TblModNewsImgSprDescr."`.`cod`
                        AND
                        `".TblModNewsImgSprDescr."`.`lang_id` ='".$this->lang_id."'
                        AND
                        `".TblModNewsImgSprName."`.`lang_id` ='".$this->lang_id."'
                     )
                WHERE
                    `".TblModNewsImg."`.`id_news`='".$id_news."' AND `show`=1
                ORDER BY
                `".TblModNewsImg."`.`move`";

        $res = $this->db->db_Query($q);
        //echo '<br>'.$q.'<br/> res='.$res.' $tmp_db->result='.$tmp_db->result;
        if ( !$res or !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        $arr = NULL;
        for($i=0; $i<$rows; $i++){
            $row = $this->db->db_FetchAssoc();
            $arr[$i] = $row;
        }
        return $arr;
    } //end of function GetImagesToShow()

    // ================================================================================================
    // Function : GetMainImage
    // Date : 13.10.2006
    // Parms :   $id_news    / id of the user
    //           $part       /  for front-end or for back-end
    // Returns : return $image for current value with cod=$cod
    // Description : return image for current value with cod=$cod, if it is exist
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetMainImage($id_news, $part = 'front')
    {
        $image = NULL;
        $q = "SELECT * FROM `".TblModNewsImg."` WHERE 1 AND `id_news`='".$id_news."'";
        if ($part=='front') $q = $q." AND `show`=1";
        $q = $q." order by `move`";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if ( !$res or !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        //echo '<br>$rows='.$rows;
        $row = $this->db->db_FetchAssoc();
        return $row['path'];
    } //end of function GetMainImage()


    // ================================================================================================
    // Function : GetTopImage
    // Date : 13.10.2006
    // Parms :   $id_news    / id of the user
    // Returns : return $image for current value with cod=$cod
    // Description : return image for current value with cod=$cod, if it is exist
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetTopImage($id_news)
    {
        $q = "SELECT * FROM `".TblModNewsTop."` WHERE 1 AND `cod`='".$id_news."'";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if ( !$res or !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        //echo '<br>$rows='.$rows;
        $row = $this->db->db_FetchAssoc();
        return $row['image'];
    } //end of function GetTopImage()
    // ================================================================================================
    // Function : GetMainImageData
    // Date : 13.10.2006
    // Parms :   $id_news    / id of the user
    //           $part       /  for front-end or for back-end
    // Returns : return $image for current value with cod=$cod
    // Description : return image for current value with cod=$cod, if it is exist
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetMainImageData($id_news, $part = 'front')
    {
        $image = NULL;

        $q = "SELECT * FROM `".TblModNewsImg."` WHERE 1 AND `id_news`='".$id_news."'";
        if ($part=='front') $q = $q." AND `show`=1";
        $q = $q." order by `move`";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if ( !$res or !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        //echo '<br>$rows='.$rows;
        $row = $this->db->db_FetchAssoc();
        return $row;
    } //end of function GetMainImageData()


    /**
    * Class method getPictureAbsPath
    *
    * @param integer $id_prop - id of the item position
    * @param string $imgName - name of image file
    * @return Absolute path to the image
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 04.12.2012
    */
    function getPictureAbsPath($id_prop, $imgName){
        return SITE_PATH.$this->settings['img_path'].'/'.$id_prop.'/'.$imgName;
    }

        /**
    * Class method getPictureRelPath
    *
    * @param integer $id_prop - id of the item position
    * @param string $imgName - name of image file
    * @return Relative path to the image
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 04.12.2012
    */
    function getPictureRelPath($id_prop, $imgName){
        return $this->settings['img_path'].'/'.$id_prop.'/'.$imgName;
    }

    /**
    * Class method ShowImage
    * function for import data from old Edifier News to new
    * @param $img - id of the picture, or relative path of the picture /images/mod_news/24094/12984541610.jpg or name of the picture 12984541610.jpg
    * @param $id_news - id of the news
    * @param $size - Can be "size_auto" or  "size_width" or "size_height"
    * @param $quality - quality of the image from 0 to 100
    * @param $wtm - make watermark or not. Can be "txt" or "img"
    * @param $parameters - other parameters for TAG <img> like border
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 08.10.2011
    * @return true or false
    */
    function ShowImage($img = NULL, $id_news, $size = NULL, $quality = NULL, $wtm = NULL, $parameters = NULL, $return_src=false)
    {
//        echo '<br><br>$img='.$img;
        if (!strstr($img, '.') AND !strstr($img, '/')) {
            $img_data = $this->GetPictureData($img);
            if (!isset($img_data['id_prop'])) {
                return false;
            }
            $img_with_path = $this->getPictureRelPath($img_data['id_prop'], $img_data['path']);
        }
        else {
            //$settings_img_path = $settings['img_path'].'/categories';
            $rpos = strrpos($img, '/');
            if ($rpos > 0) {
                $img_with_path = $img;
            }else {
                if (!$id_news){
                    return false;
                }
                $img_with_path = $this->getPictureRelPath($id_news, $img);
            }
            $alt = '';
            $title = '';
        }
//        echo '<br>$img_with_path='.$img_with_path;
        $imgSmall = ImageK::getResizedImg($img_with_path, $size, $quality, $wtm);
        if($return_src){
            return $imgSmall;
        }else{
            return '<img src="'.$imgSmall.'" '.$parameters.' />';
        }
        /*
        $size_auto = NULL;
        $size_width = NULL;
        $size_height = NULL;
        $alt = NULL;
        $title = NULL;
        $settings_img_path = $this->settings['img_path'];
        //echo "<br>img=".$img;

        if( !strstr($img, '.') AND !strstr($img, '/') ){
            $img_data = $this->GetPictureData($img);
            if(!isset($img_data['id_news'])) {return false;}
            $settings_img_path = $this->settings['img_path'].$img_data['id_news']; // like /uploads/45
            $img_name = $img_data['path'];  // like R1800TII_big.jpg
            $img_with_path = $settings_img_path.$img_name; // like /uploads/45/R1800TII_big.jpg
            if ( !strstr($parameters, 'alt') ) $alt = $this->GetPictureAlt($img);
            if ( !strstr($parameters, 'title') ) $title = $this->GetPictureTitle($img);
        }
        else {
            $rpos = strrpos($img,'/');
            if($rpos>0){
                $settings_img_path = substr($img, 0, $rpos);
                $img_name = substr($img, $rpos+1, strlen($img)-$rpos );
                $img_with_path = $img;
            }
            else{
                if(!$id_news) return false;
                $settings_img_path = $this->settings['img_path'].'/'.$id_news; // like /uploads/45
                $img_name = $img;
                $img_with_path = $settings_img_path.'/'.$img;
            }
            $alt ='';
            $title= '';
        }
        //echo '<br>$img_name='.$img_name.'<br>$img_with_path='.$img_with_path;
        $mas_img_name=explode(".",$img_with_path);

        if ( strstr($size,'size_width') ){
            $size_width = substr( $size, strrpos($size,'=')+1, strlen($size) );
            $img_name_new = $mas_img_name[0].NEWS_ADDITIONAL_FILES_TEXT.'width_'.$size_width.'.'.$mas_img_name[1];
        }
        elseif ( strstr($size,'size_auto') ) {
            $size_auto = substr( $size, strrpos($size,'=')+1, strlen($size) );
            $img_name_new = $mas_img_name[0].NEWS_ADDITIONAL_FILES_TEXT.'auto_'.$size_auto.'.'.$mas_img_name[1];
        }
        elseif ( strstr($size,'size_height') ) {
            $size_height = substr( $size, strrpos($size,'=')+1, strlen($size) );
            $img_name_new = $mas_img_name[0].NEWS_ADDITIONAL_FILES_TEXT.'height_'.$size_height.'.'.$mas_img_name[1];
        }
        elseif(empty($size)) $img_name_new = $mas_img_name[0].'.'.$mas_img_name[1];
        //echo '$img_name_new='.$img_name_new;
        $img_full_path_new = SITE_PATH.$img_name_new;
        //if exist local small version of the image then use it
        if( file_exists($img_full_path_new)){
            //echo 'exist';
            //echo '<br>$settings_img_path='.$settings_img_path.' $img_full_path='.$img_full_path;
            if ( !strstr($parameters, 'alt') ) $alt = $this->GetPictureAlt($img);
            if ( !strstr($parameters, 'title') ) $title = $this->GetPictureTitle($img);
            if ( !strstr($parameters, 'alt') )  $parameters = $parameters.' alt="'.$alt.'"';
            if ( !strstr($parameters, 'title') ) $parameters = $parameters.' title=" '.$title.' "';
            if($return_src) $str = $img_name_new;
            else $str = '<img src="'.$img_name_new.'" '.$parameters.' />';
        }
        //else use original image on the server SITE_PATH and make small version on local server
        else {
            //echo 'Not  exist';
            $img_full_path = SITE_PATH.$img_with_path; // like z:/home/speakers/www/uploads/45/R1800TII_big.jpg
            //echo '<br> $img_full_path='.$img_full_path;
            if ( !file_exists($img_full_path) ) return false;

            $thumb = new Thumbnail($img_full_path);
            //echo '<br>$thumb->img[x_thumb]='.$thumb->img['x_thumb'].' $thumb->img[y_thumb]='.$thumb->img['y_thumb'];
            $src_x = $thumb->img['x_thumb'];
            $src_y = $thumb->img['y_thumb'];
            if ( !empty($size_width ) and empty($size_height) ) $thumb->size_width($size_width);
            if ( !empty($size_height) and empty($size_width) ) $thumb->size_height($size_height);
            if ( !empty($size_width) and !empty($size_height) ) $thumb->size($size_width,$size_height);
            if ( !$size_width and !$size_height and $size_auto ) $thumb->size_auto($size_auto); // [OPTIONAL] set the biggest width and height for thumbnail
            //echo '<br>$thumb->img[x_thumb]='.$thumb->img['x_thumb'].' $thumb->img[y_thumb]='.$thumb->img['y_thumb'];

            //if original image smaller than thumbnail then use original image and don't create thumbnail
            if($thumb->img['x_thumb']>=$src_x OR $thumb->img['y_thumb']>=$src_y){
                $img_full_path = $settings_img_path.'/'.$img_name;
                //echo '<br>$settings_img_path='.$settings_img_path.' $img_full_path='.$img_full_path;
                if ( !strstr($parameters, 'alt') ) $alt = $this->GetPictureAlt($img);
                if ( !strstr($parameters, 'title') ) $title = $this->GetPictureTitle($img);
                if ( !strstr($parameters, 'alt') )  $parameters = $parameters.' alt="'.$alt.'"';
                if ( !strstr($parameters, 'title') ) $parameters = $parameters.' title=" '.$title.' "';
                if($return_src) $str = $img_with_path;
                else $str = '<img src="'.$img_with_path.'" '.$parameters.' />';
            }
            else{
                $thumb->quality=$quality;                  //default 75 , only for JPG format
                //echo '<br>$wtm='.$wtm;
                if ( $wtm == 'img' ) {
                    $thumb->img_watermark = NULL; //SITE_PATH.'/images/design/m01.png';        // [OPTIONAL] set watermark source file, only PNG format [RECOMENDED ONLY WITH GD 2 ]
                    $thumb->img_watermark_Valing='CENTER';           // [OPTIONAL] set watermark vertical position, TOP | CENTER | BOTTOM
                    $thumb->img_watermark_Haling='CENTER';           // [OPTIONAL] set watermark horizonatal position, LEFT | CENTER | RIGHT
                }
                if ( $wtm == 'txt' ) {
                    if ( defined('WATERMARK_TEXT') ) $thumb->txt_watermark=NEWS_WATERMARK_TEXT;        // [OPTIONAL] set watermark text [RECOMENDED ONLY WITH GD 2 ]
                    else $thumb->txt_watermark='';
                    $thumb->txt_watermark_color='000000';        // [OPTIONAL] set watermark text color , RGB Hexadecimal[RECOMENDED ONLY WITH GD 2 ]
                    $thumb->txt_watermark_font=5;                // [OPTIONAL] set watermark text font: 1,2,3,4,5
                    $thumb->txt_watermark_Valing='TOP';           // [OPTIONAL] set watermark text vertical position, TOP | CENTER | BOTTOM
                    $thumb->txt_watermark_Haling='LEFT';       // [OPTIONAL] set watermark text horizonatal position, LEFT | CENTER | RIGHT
                    $thumb->txt_watermark_Hmargin=10;          // [OPTIONAL] set watermark text horizonatal margin in pixels
                    $thumb->txt_watermark_Vmargin=10;           // [OPTIONAL] set watermark text vertical margin in pixels
                }

                if( !strstr($img, '.') AND !strstr($img, '/') ){
                    $mas_img_name=explode(".",$img_name);
                    //$img_name_new = $mas_img_name[0].NEWS_NEWS_ADDITIONAL_FILES_TEXT.intval($thumb->img['x_thumb']).'x'.intval($thumb->img['y_thumb']).'.'.$mas_img_name[1];
                    if(!empty($size_width ))
                        $img_name_new = $mas_img_name[0].NEWS_ADDITIONAL_FILES_TEXT.'width_'.$size_width.'.'.$mas_img_name[1];
                    elseif(!empty($size_auto ))
                        $img_name_new = $mas_img_name[0].NEWS_ADDITIONAL_FILES_TEXT.'auto_'.$size_auto.'.'.$mas_img_name[1];
                    elseif(!empty($size_height ))
                        $img_name_new = $mas_img_name[0].NEWS_ADDITIONAL_FILES_TEXT.'height_'.$size_height.'.'.$mas_img_name[1];
                    $img_full_path_new = SITE_PATH.$settings_img_path.'/'.$img_name_new;
                    $img_src = $settings_img_path.'/'.$img_name_new;
                    $uploaddir = SITE_PATH.$settings_img_path;
                }
                else {
                    $mas_img_name=explode(".",$img_with_path);
                    //$img_name_new = $mas_img_name[0].NEWS_NEWS_ADDITIONAL_FILES_TEXT.intval($thumb->img['x_thumb']).'x'.intval($thumb->img['y_thumb']).'.'.$mas_img_name[1];
                    if(!empty($size_width ))
                        $img_name_new = $mas_img_name[0].NEWS_ADDITIONAL_FILES_TEXT.'width_'.$size_width.'.'.$mas_img_name[1];
                    elseif(!empty($size_auto ))
                        $img_name_new = $mas_img_name[0].NEWS_ADDITIONAL_FILES_TEXT.'auto_'.$size_auto.'.'.$mas_img_name[1];
                    elseif(!empty($size_height ))
                        $img_name_new = $mas_img_name[0].NEWS_ADDITIONAL_FILES_TEXT.'height_'.$size_height.'.'.$mas_img_name[1];
                    $img_full_path_new = SITE_PATH.$img_name_new;
                    $img_src = $img_name_new;
                    $rpos = strrpos($img_with_path,'/');
                    //echo '<br />$img_with_path='.$img_with_path.' $rpos='.$rpos;
                    if($rpos>0){
                        $uploaddir = SITE_PATH.substr($img_with_path, 0, $rpos);
                    }
                    else $uploaddir = SITE_PATH.$settings_img_path;
                }
                if ( !strstr($parameters, 'alt') ) $alt = $this->GetPictureAlt($img);
                if ( !strstr($parameters, 'title') ) $title = $this->GetPictureTitle($img);

                //echo '<br>$img_name_new='.$img_name_new;
                //echo '<br>$img_full_path_new='.$img_full_path_new;
                //echo '<br>$img_src='.$img_src;
                //echo '<br>$uploaddir='.$uploaddir;

                if ( !strstr($parameters, 'alt') )  $parameters = $parameters.' alt="'.htmlspecialchars($alt).'"';
                if ( !strstr($parameters, 'title') ) $parameters = $parameters.' title=" '.htmlspecialchars($title).' "';

                //echo '<br>$uploaddir='.$uploaddir;
                if ( !file_exists($img_full_path_new) ) {
                    if( !file_exists ($uploaddir) ) mkdir($uploaddir,0777);
                    if( file_exists($uploaddir) ) @chmod($uploaddir,0777);
                    $thumb->process();       // generate image
                    //make new image like R1800TII_big.jpg -> R1800TII_big_autozoom_100x84.jpg
                    $thumb->save($img_full_path_new);
                    @chmod($uploaddir,0755);
                    $params = "img=".$img."&".$size;
                }
                if($return_src) $str = $img_src;
                else $str = '<img src="'.$img_src.'" '.$parameters.' />';
            }//end else
        }//end else
        return $str;
         *
         */
    } // end of function ShowImage()

    // ================================================================================================
    // Function : GetExtationOfFile
    // Date : 31.08.2009
    // Parms :  $filename - name of the image
    // Returns : $res / Void
    // Description : return extenation of file
    // Programmer : Oleg Morgalyuk
    // ================================================================================================
    function GetExtationOfFile($filename)
    {
        return $ext = substr($filename,1 + strrpos($filename, "."));
    }// end of function GetExtationOfFile()

    // ================================================================================================
    // Function : GetImgFullPath
    // Date : 06.11.2006
    // Parms :  $img - name of the image
    //          $id_news - id of the user
    // Returns : $res / Void
    // Description : return path to the image like /images/mod_user/120/1162648375_0.jpg
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetImgFullPath($img = NULL, $id_news = NULL )
    {
        return SITE_PATH.$this->settings['img_path'].'/'.$id_news.'/'.$img;
    } //end of function GetImgFullPath()

    // ================================================================================================
    // Function : GetImgPath
    // Date : 06.11.2006
    // Parms :  $img - name of the image
    //          $id_news - id of the user
    // Returns : $res / Void
    // Description : return path to the image like /images/mod_user/120/1162648375_0.jpg
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetImgPath($img = NULL, $id_news = NULL )
    {
        return $this->settings['img_path'].'/'.$id_news.'/'.$img;
    } //end of function GetImgPath()



    // ================================================================================================
    // Function : GetPictureData
    // Date : 03.04.2006
    // Parms :  $id_img - id of the image
    // Returns : $res / Void
    // Description : return array with path to the pictures of current product
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetPictureData($id_img)
    {
        $tmp_db = DBs::getInstance();

        $q="SELECT `".TblModNewsImg."`.*,
            `".TblModNewsImgSprName."`.`name`,
            `".TblModNewsImgSprDescr."`.`name` AS `descr`
            FROM `".TblModNewsImg."`
            LEFT JOIN `".TblModNewsImgSprName."` ON (`".TblModNewsImg."`.`id`=`".TblModNewsImgSprName."`.`cod` AND `".TblModNewsImgSprName."`.`lang_id`='".$this->lang_id."')
            LEFT JOIN `".TblModNewsImgSprDescr."` ON (`".TblModNewsImg."`.`id`=`".TblModNewsImgSprDescr."`.`cod` AND `".TblModNewsImgSprDescr."`.`lang_id`='".$this->lang_id."')
            WHERE `".TblModNewsImg."`.`id`='".$id_img."'";
        $res = $tmp_db->db_Query( $q );
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if( !$res OR !$tmp_db->result ) return false;
        $row = $tmp_db->db_FetchAssoc();
        return $row;

    } // end of function GetPictureData()


    // ================================================================================================
    // Function : GetPictureAlt
    // Date : 19.05.2006
    // Parms :  $id_img - id of the image
    // Returns : $res / Void
    // Description : return alt for this image
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetPictureAlt($img, $show_name = true)
    {

        if ( strstr($img, '.') ) {
        $id_img = $this->GetImgIdByPath($img);
        } else {
        $id_img = $img;
        }

        // echo "<br>id_img=".$id_img;
        $alt = $this->Spr->GetNameByCod(TblModNewsImgSprName, $id_img, $this->lang_id, 1);
        // echo '<br>$alt='.$alt;
        if ( empty($alt) and $show_name ) {
        $q="SELECT `id_news` FROM `".TblModNewsImg."` WHERE `id`='".$id_img."'";
        $res = $this->db->db_Query( $q );
        // echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if( !$res OR !$this->db->result ) return false;
        $row = $this->db->db_FetchAssoc();

        $alt = $this->Spr->GetNameByCod(TblModNewsSprSbj, $row['id_news'], $this->lang_id, 1);
        //$id_cat = $this->GetCategory($row['id_prop']);
        //echo '<br>$id_cat='.$id_cat;
        //$name_ind = $this->Spr->GetNameByCod(TblModCatalogSprNameInd, $id_cat, $this->lang_id, 1 );
        // $alt = $name_ind.' '.$alt;
        }

        //  echo '<br> $alt='.$alt;
        return htmlspecialchars($alt);

    } // end of function GetPictureAlt()

    // ================================================================================================
    // Function : GetPictureTitle
    // Date : 19.05.2006
    // Parms :  $id_img - id of the image
    // Returns : $res / Void
    // Description : return title for this image
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetPictureTitle($img)
    {
        if ( strstr($img, '.') ) {
        $id_img = $this->GetImgIdByPath($img);
        } else {
        $id_img = $img;
        }

        $alt = htmlspecialchars($this->Spr->GetNameByCod(TblModNewsImgSprDescr, $id_img, $this->lang_id, 1));
        //echo '<br>$alt='.$alt;
        if ( empty($alt) ) {
        $alt = $this->GetPictureAlt($id_img);
        }
        // echo '<br> $title='.$alt;
        return $alt;

    } // end of function GetPictureTitle()
    // ================================================================================================
    // Function : GetImgTitleByPath
    // Date : 06.11.2006
    // Parms :  $img - name of the picture
    // Returns : $res / Void
    // Description : return title for image
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetImgIdByPath( $img )
    {

        $q = "SELECT * FROM `".TblModNewsImg."` WHERE 1 AND `path`='$img'";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if ( !$res or !$this->db->result ) return false;
        //$rows = $tmp_db->db_GetNumRows();
        // echo '<br>$rows='.$rows;
        $row = $this->db->db_FetchAssoc();
        $id = $row['id'];
        return $id;
    } //end of function GetImgTitleByPath()

    // ================================================================================================
    // Function : GetNewsIdByImgId
    // Date : 22.06.2007
    // Parms :  $img - name of the picture
    // Returns : $res / Void
    // Description : return title for image
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GetNewsIdByImgId( $img )
    {
        $tmp_db = new DB();
        $q = "SELECT * FROM `".TblModNewsImg."` WHERE 1 AND `id`='$img'";
        $res = $tmp_db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if ( !$res or !$tmp_db->result ) return false;
        //$rows = $tmp_db->db_GetNumRows();
        // echo '<br>$rows='.$rows;
        $row = $tmp_db->db_FetchAssoc();
        $id = $row['id_news'];
        return $id;
    } //end of function GetNewsIdByImgId()

    // ================================================================================================
    // Function : GetNewsData()
    // Date : 06.04.2007
    // Returns :      true,false / Void
    // Description :  Return news data
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function GetNewsData( $news_id = NULL )
    {
        if(!$news_id) return true;
        $q = "SELECT *
              FROM `".TblModNews."`, `".TblModNewsNames."`, `".TblModNewsFull."`
              WHERE `".TblModNews."`.`id` = `".TblModNewsNames."`.`id_news`
              AND `".TblModNewsNames."`.lang_id='".$this->lang_id."'
              AND `".TblModNews."`.id=`".TblModNewsFull."`.`id_news`
              AND `".TblModNewsFull."`.`lang_id` = '".$this->lang_id."'
             ";
        if( !empty($this->fltr)){
            $q .= $this->fltr;
        }
        $res = $this->db->db_Query( $q );
        //echo '<br>'.$q.' $res='.$res.' $this->db->result='.$this->db->result;
        if ( !$res OR !$this->db->result ) return false;
        //$rows = $this->db->db_GetNumRows();
        $row = $this->db->db_FetchAssoc();
//        var_dump($row);
        return $row;
    } //end of fuinction GetNewsData()


    // ================================================================================================
    function GetNewsCatLast( $id_cat=1, $limit=3, $active=true)
    {
        $q = "SELECT
             `".TblModNews."`.id,
            `".TblModNews."`.start_date,
            `".TblModNews."`.id_category,
            `".TblModNewsSprSbj."`.lang_id,
            `".TblModNewsSprSbj."`.name,
            `".TblModNewsSprShrt."`.name as shrt
        FROM
            `".TblModNews."`, `".TblModNewsSprSbj."`, `".TblModNewsSprShrt."`
        WHERE
            `".TblModNews."`.id = `".TblModNewsSprSbj."`.cod and
            `".TblModNews."`.id = `".TblModNewsSprShrt."`.cod and
            `".TblModNews."`.id_category ='".$id_cat."' and
            `".TblModNewsSprSbj."`.lang_id ='".$this->lang_id."' and
            `".TblModNewsSprSbj."`.name !=''  AND
            `".TblModNewsSprShrt."`.lang_id='".$this->lang_id."' ";
        if($active==true)
           $q .= " and `".TblModNews."`.status='a' ";
        if(isset($this->id))
            $q .= " and `".TblModNews."`.id!= '".$this->id."' ";

        $q .="ORDER BY
            `display` desc LIMIT ".$limit;

        $res = $this->db->db_Query( $q );
        //echo '<br>'.$q.' $res='.$res.' $this->db->result='.$this->db->result;
        if ( !$res OR !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        $array = array();
        for( $i=0; $i<$rows; $i++ )  {
            $array[$i] =  $this->db->db_FetchAssoc($res);
        }
        return $array;
    } //end of function GetNewsCatLast()


    // ================================================================================================
    // Function : GetNewsIdByQuickSearch()
    // Date :    26.05.2011
    // Returns : true/false
    // Description : Get all Id news for $search_keywords
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function GetNewsIdByQuickSearch( $search_keywords = null, $idModule = null )
    {
        $search_keywords = stripslashes($search_keywords);
        $sel_table = NULL;
        $str_like = NULL;
        $filter_cr = ' OR ';
        $str_like = $this->build_str_like(TblModNewsNames.'.name', $search_keywords);
        $str_like .= $filter_cr.$this->build_str_like(TblModNewsShort.'.short', $search_keywords);
        $str_like .= $filter_cr.$this->build_str_like(TblModNewsFull.'.full', $search_keywords);
        $sel_table = "`".TblModNews."`, `".TblModNewsCat."`, `".TblModNewsNames."`, `".TblModNewsShort."`, `".TblModNewsFull."` ";

        $q ="SELECT
                `".TblModNews."`.id,
                `".TblModNews."`.start_date,
                `".TblModNews."`.display
             FROM ".$sel_table."
             WHERE (".$str_like.")
             AND `".TblModNewsNames."`.lang_id = '".$this->lang_id."'
             AND `".TblModNews."`.id = `".TblModNewsNames."`.id_news
             AND `".TblModNewsShort."`.lang_id = '".$this->lang_id."'
             AND `".TblModNews."`.id = `".TblModNewsShort."`.id_news
             AND `".TblModNewsFull."`.lang_id = '".$this->lang_id."'
             AND `".TblModNews."`.id = `".TblModNewsFull."`.id_news
             AND `".TblModNews."`.`id_category` = `".TblModNewsCat."`.`cod`
             AND `".TblModNewsCat."`.lang_id = '".$this->lang_id."'
             AND `".TblModNews."`.status != 'n'
             AND `".TblModNews."`.status != 'i'
             AND `".TblModNews."`.`visible` = '1'
             ORDER BY `".TblModNews."`.`display` desc
            ";

        $res = $this->db->db_Query( $q );
//        echo '<br>'.$q.' <br/>$res='.$res.' $this->db->result='.$this->db->result;
        if ( !$res OR !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        $array = array();
        for( $i=0; $i<$rows; $i++ )  {
            $row =  $this->db->db_FetchAssoc($res);
            $dateId = strtotime ($row['start_date']);
            $array[$dateId]['id'] = $row['id'];
            //$array[$dateId]['start_date'] = $row['start_date'];
            $array[$dateId]['id_module'] = $idModule;
        }
        //print_r($array);
        return $array;
    } //end of function GetNewsIdByQuickSearch()

    // ================================================================================================
    // Function : CotvertDataToOutputArray
    // Date : 19.05.2006
    // Parms :  $rows - count if founded records stored in object $this->db
    //          $sort - type of sortaion returned array
    //                  (move - default value, name)
    //          $asc_desc - sortation Asc or Desc
    //          $data - count of returned data (full or short)
    // Returns : $arr
    // Description : return arr of content for selected category
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function ConvertDataToOutputArray ($rows, $sort = "id", $asc_desc = "asc", $data = "full")
    {
        // echo '<br> $sort='.$sort.' $rows='.$rows;
        $arr0 = NULL;
        if(!$rows) return true;

        $settings = $this->GetSettings();

        for ($i=0;$i<$rows;$i++){
        $row = $this->db->db_FetchAssoc();
        $main_img_data = $this->GetMainImageData($row['id'], 'front');
        switch($sort){
        case 'id':
            $index_sort = $row['id'];
            break;
        case 'display ':
            $index_sort = $row['display'];
            break;
        default:
            $str_to_eval = '$index_sort = "_".$row['."'".$sort."'".']."_".$row['."'id'".'];';
            //echo '<br> $str_to_eval='.$str_to_eval;
            eval($str_to_eval);
            break;
        }

        $arr0[$index_sort]["id"] = $row['id'];
        $arr0[$index_sort]['id_category'] = $row['id_category'];

        if ( isset($settings['img']) AND $settings['img']=='1' ) {
        $arr0[$index_sort]["img"]["id"] = $main_img_data['id'];
        $arr0[$index_sort]["img"]["descr"] = $main_img_data['descr'];
        $arr0[$index_sort]["img"]["path"] = $main_img_data['path'];//$this->GetMainImage($row['id'], 'front');
        $arr0[$index_sort]["img"]["img_path"] = $this->GetImgPath( $this->GetMainImage($row['id'], 'front'), $row['id'] );
        $arr0[$index_sort]["img"]["full_img_path"] = $this->GetImgFullPath( $this->GetMainImage($row['id'], 'front'), $row['id'] );
        }

        $arr0[$index_sort]['start_date'] = $this->ConvertDate($row['start_date']);
        $arr0[$index_sort]['category'] = $this->Spr->GetNameByCod( TblModNewsCat, $row['id_category'] );

        $sbj = strip_tags($this->Spr->GetNameByCod( TblModNewsSprSbj, $row['id'], $this->lang_id, 0 ));
        $sbj = str_replace ( '&amp;', '&', $sbj );
        $sbj = str_replace ( '&#039;', '\'', $sbj );
        $sbj = str_replace ( '&quot;', '\"', $sbj );
        $arr0[$index_sort]["sbj"] = $sbj;

        $shrt_news = strip_tags(stripslashes( $this->Spr->GetNameByCod( TblModNewsSprShrt, $row['id'] ) ), "<p><br><strong><u><i><b><ul><li><table><tr><td>");
        $shrt_news = str_replace ( '&amp;', '&', $shrt_news );
        $shrt_news = str_replace ( '&#039;', '\'', $shrt_news );
        $shrt_news = str_replace ( '&quot;', '\"', $shrt_news );
        if($shrt_news=='') $shrt_news = $this->Msg->show_text('TXT_NEWS_EMPTY');
        if ( isset($settings['short_descr']) AND $settings['short_descr']=='1' ) {
        $arr0[$index_sort]["shrt_news"] = $shrt_news;
        }
        if(empty($arr0[$index_sort]["shrt_news"])) $arr0[$index_sort]["shrt_news"] = $this->Msg->show_text('TXT_NEWS_EMPTY');

        if( $data=='full' ){

        if ( isset($settings['full_descr']) AND $settings['full_descr']=='1' ) {
        $full_news = stripslashes($this->Spr->GetNameByCod( TblModNewsSprFull, $row['id'], $this->lang_id, 0 ));
        $full_news = str_replace ( '&amp;', '&', $full_news );
        $full_news = str_replace ( '&#039;', '\'', $full_news );
        $full_news = str_replace ( '&quot;', '\"', $full_news );
        $arr0[$index_sort]["full_news"] = $full_news;
        }
        if(empty($full_news)) $arr0[$index_sort]["full_news"] = $shrt_news;

        $arr0[$index_sort]["source"] = $row['source'];

        //-------- get all photos start ---------
        $img_arr = $this->GetImagesToShow($row['id']);
        for ($ii=0;$ii<count($img_arr);$ii++){
            $arr0[$index_sort]["img_arr"][$ii] = $img_arr[$ii];
            //$arr0[$index_sort]["img_arr"][$ii]['descr'] = $img_arr[$ii];
        }
        //-------- get all photos end ---------
        }

        }//end for

        if (is_array($arr0)) {
        if ( $asc_desc == 'desc' ) krsort($arr0);
        else ksort($arr0);
        reset($arr0);
        }
        // echo '<br>Arr:<br>'; print_r($arr0); echo '<br><br>';
        return $arr0;
    } //end of function CotvertDataToOutputArray()




    // ================================================================================================
    // Function : GetValueOfFieldByNewsId()
    // Date : 06.01.2006
    // Returns :      true,false / Void
    // Description :
    // Programmer :  Igor Trokhymchuk
    // ================================================================================================
    function GetValueOfFieldByNewsId( $news_id = NULL, $field = NULL )
    {
        $tmp_db = new DB();
        if ( empty($field) ) return false;

        $q = "select `".$field."` from ".TblModNews." where id='$news_id'";
        $res = $tmp_db->db_Query( $q );
        //echo '<br>'.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result;
        if ( !$res OR !$tmp_db->result ) return false;
        $row = $tmp_db->db_FetchAssoc();
        $name = $row[$field];
        //echo '<br> $name='.$name;
        return $name;
    } //end of fuinction GetValueOfFieldByNewsId()


       // ================================================================================================
       // Function : GetNewsNameByNewsId()
       // Date : 06.04.2007
       // Returns :      true,false / Void
       // Description :  Return news title
       // Programmer :  Yaroslav Gyryn
       // ================================================================================================
      function GetNewsNameByNewsId( $news_id = NULL )
       {
         $tmp_db = new DB();

         $q = "select * from ".TblModNewsSprSbj." where `cod`='$news_id' and `lang_id`='".$this->lang_id."'";
         $res = $tmp_db->db_Query( $q );
        // echo '<br>'.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result;
         if ( !$res OR !$tmp_db->result ) return false;
         $row = $tmp_db->db_FetchAssoc();
         $name = $row['name'];
        // echo '<br> name='.$name;
         return $name;
       } //end of fuinction GetUserNameByUserId()

    // ================================================================================================
    // Function : Link()
    // Date : 12.01.2011
    // Description : Return Link
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function Link( $cat = NULL, $str_news = NULL)
    {
        if(empty($this->settings))
            $this->settings = $this->GetSettings();
//        echo '$str_news='.$str_news;
        if( !defined("_LINK")) {
            $Lang = new SysLang(NULL, "front");
            $tmp_lang = $Lang->GetDefFrontLangID();
            if( ($Lang->GetCountLang('front')>1 OR isset($_GET['lang_st'])) AND $this->lang_id!=$tmp_lang) {
                define("_LINK", "/".$Lang->GetLangShortName($this->lang_id)."/");
            }
            else {
                define("_LINK", "/");
            }
        }
//        var_dump($this->treeNewsCat);
        if( !empty($cat) ){
            $link_return = $this->treeNewsCat[$cat]['translit'];
        }
        else{
            $link_return = NULL;
        }
//        echo '$link_return='.$link_return;
        if(!empty($str_news)){
//            echo '$str_news='.$str_news;
            if(!is_numeric($str_news)){
//                echo 'no';
                $link_return = $link_return.'/'.$str_news.'.html';
            }else{
//                echo 'yes';
                $link_return = $link_return.'/'.$this->getLinlMewsById($str_news).'.html';
            }
        }

        if(empty($link_return)){
            if( $this->task=='showa') $link_return = 'last/';
            elseif( $this->task=='showall') $link_return = 'all/';
            elseif( $this->task=='arch') $link_return = 'arch/';
        }

        $link_return = _LINK.'news/'.$link_return;

        return $link_return;
    } // end of function Link


    // ================================================================================================
    // Function : GetIdNewsByStrNews()
    // Date : 13.05.2007
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GetIdNewsByStrNews($str_news){
        $q = "SELECT `".TblModNews."`.`id`
              FROM `".TblModNewsNames."`, `".TblModNews."`
              WHERE BINARY `".TblModNewsNames."`.`link` = BINARY '".$str_news."'
              AND `".TblModNewsNames."`.id_news=`".TblModNews."`.`id`
              AND `".TblModNews."`.`id_cat`='".$this->id_cat."'
              AND `".TblModNewsNames."`.`lang_id`='".$this->lang_id."'
             ";
        $res = $this->db->db_Query( $q );
//        echo '<br>'.$q.' $res='.$res.' $this->db->result='.$this->db->result;
        if ( !$res OR !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        if($rows==0) return false;
        //echo "<br>GetIdNewsByStrNews  q=".$q." res=".$res." rows=".$rows;
        $row = $this->db->db_FetchAssoc();
        return $row['id'];
    } // end of function GetIdNewsByStrNews



    //======================================= SubSribe START =================================================

    // ================================================================================================
    // Function : SubscrSave()
    // Date : 21.05.2007
    // Description : save subscribers
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function SubscrSave()
    {
        $q = "SELECT * FROM ".TblModNewsSubscr." WHERE `login`='".$this->subscriber."'";
        $res = $this->db->db_Query( $q );
        //echo "<br>11 q=".$q." res=".$res;
        if( !$res ) return false;
        $rows = $this->db->db_GetNumRows();
        $date = date("Y-m-d");
        if( $rows>0 )   //--- update
        {
            $row = $this->db->db_FetchAssoc();
            $q = "UPDATE `".TblModNewsSubscr."` SET
                  `login`='".$this->subscriber."',
                  `pass`='".$this->subscr_pass."'
                  WHERE `id`='".$row['id']."'
                 ";
            $id = $row['id'];
            $res = $this->db->db_Query( $q );
            if( !$res ) return false;
        }
        else          //--- insert
        {
            $q = "INSERT INTO `".TblModNewsSubscr."` SET
                  `login`='".$this->subscriber."',
                  `pass`='".$this->subscr_pass."',
                  `user_status`='0',
                  `is_send`='0',
                  `dt`='".$date."'
                 ";
            $res = $this->db->db_Query( $q );
            if( !$res ) return false;
        }

        if ( empty($id)) $id = $this->db->db_GetInsertID();

        $q="DELETE FROM `".TblModNewsSubscrCat."` WHERE `subscr_id`='".$id."'";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
        if (!$res OR !$this->db->result) return false;;

        if($this->categories=='all') $this->categories = $this->Spr->GetListName( TblModNewsCat, $this->lang_id, 'array', 'cod', 'asc', 'cod' );
        foreach($this->categories as $k=>$v){
            $q = "INSERT `".TblModNewsSubscrCat."` SET
                  `subscr_id`='".$id."',
                  `cat_id`='".$v."'
                 ";
            $res = $this->db->db_Query( $q );
            //echo "<br>q=".$q." res=".$res;
            if( !$res ) return false;
        }
        return true;
    } // end of function  SubscrSave

    // ================================================================================================
    // Function : SubscrDel()
    // Date : 22.05.2007
    // Description : save subscribers
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function SubscrDel()
    {
        $tmp_db = new DB();
        $q = "DELETE
                 FROM `".TblModNewsSubscr."`, `".TblModNewsSubscrCat."`
                 USING  `".TblModNewsSubscr."` INNER JOIN `".TblModNewsSubscrCat."`
                 WHERE `".TblModNewsSubscr."`.`login`='".$this->subscriber."'
                 AND `".TblModNewsSubscr."`.id=`".TblModNewsSubscrCat."`.subscr_id";
        $res = $tmp_db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res) return false;
        return true;
    }

    // ================================================================================================
    // Function : SaveManyValues()
    // Date : 15.11.2006
    // Returns : true,false / Void
    // Description : Store many data to the table for one user
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function SaveManyValues( $table, $id_user, $arr_val )
    {
       $tmp_db = new DB();
       $q="DELETE FROM `".$table."` WHERE `cod`='$id_user'";
       $res = $tmp_db->db_Query($q);
      // echo '<br>q='.$q.' res='.$res.'<br>'.$tmp_db->result;
       if (!$tmp_db->result) return false;
      // echo '<br>count($arr_val='.count($arr_val);
       for( $i=0; $i<count($arr_val); $i++){
           //echo '<br>char='.$character[$i];
           $q="INSERT into `".$table."` values(NULL,'$id_user','".$arr_val[$i]."')";
           $res = $tmp_db->db_Query($q);
          // echo '<br>q='.$q.' res='.$res.'<br>';
           if (!$tmp_db->result) return false;
       }
       return true;
    } //end of fuinction SaveManyValues()

    // ================================================================================================
    // Function : DelManyValues
    // Date : 17.11.2006
    // Parms :   $table - name of the table from which will gets data
    //           $id_user - id of the user
    // Returns : $arr - if values exist in the $table ; else - false
    // Description : remove array with values of one property of user
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function DelManyValues( $table, $id_user)
    {
        $tmp_db = new DB();
        $q="DELETE FROM `".$table."` WHERE `cod`='$id_user'";
        $res = $tmp_db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res OR !$tmp_db->result) return false;
        return true;
    } // ed of function DelManyValues()

    // ================================================================================================
    // Function : SendHTML
    // Date : 22.05.2007
    // Returns : true,false / Void
    // Description : Send the registration mail with profile of the subscriber
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function SendHTML()
    {
     $info = "
      <H3>".$this->multi['TXT_SUCCESSFULL_REG']." ".$_SERVER['SERVER_NAME']."</H3>
     <div>".$this->multi['TXT_THANK_FOR_REG']."
     <br>".$this->multi['TXT_SUPPORT_FOR_REG']."</div><br>
     <table border=0 cellspacing=1 cellpadding=2 align=center width='100%'>
     <tr><td colspan=2 align=left><b>".$this->multi['TXT_REG_HEADER']."</b>
     <tr><td>".$this->multi['_FLD_EMAIL']." :
         ".$this->subscriber."
     <tr><td>".$this->multi['TXT_ACTIVE_PAGE'].":
         <a href='http://".$_SERVER['SERVER_NAME']."/news/activate/".$this->subscriber."/'>http://".$_SERVER['SERVER_NAME']."/news/activate/</a></td>
     </tr>
     <tr><td colspan='2'>".$this->multi['TXT_WRONG_ADDR']."</td>
     </tr>
    </table>
    ";

     //-------------Send to User ---------------
     $subject = $this->multi['TXT_SUCCESSFULL_REG']." ".$_SERVER['SERVER_NAME'];
     $body = $info;
   //echo $body;
     $arr_emails[0]=$this->subscriber;
     $res = $this->SendSysEmail($subject, $body, $arr_emails);

     if( !$res ) {return false;}
     return true;
    } //end of function SendHTML()



    // ================================================================================================
    // Function : SendSysEmail
    // Date : 18.01.2007
    // Parms :   $sbj        - subject of email
    //           $body       - body of email
    //           $arr_emails - array with emails whrere to send ($arr_emails[0]='iii@ii.i'
    //                                                           $arr_emails[1]='aaa@aa.a')
    // Returns : $res / Void
    // Description : Function for send emails
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function SendSysEmail($sbj=NULL, $body=NULL, $arr_emails=NULL, $headers=NULL)
    {
        if( empty($sbj) ) $sbj=NULL;
        $body .= "
        <p>
        <a href='http://".$_SERVER['SERVER_NAME']."/news/deactivate/".$this->subscriber."/'>".$this->multi['TXT_SUBSCR_DEL']."</a>
        </p>
        ";

        $mail = new Mail();
        for($i=0;$i<count($arr_emails);$i++){
         $mail->AddAddress($arr_emails[$i]);
        }

        $mail->WordWrap = 500;
        $mail->IsHTML( true );
        $mail->Subject = $sbj;
        $mail->Body = $body;

        $res = $mail->SendMail();
        // if(mail($this->subscriber, $sbj, $body, $headers)) return true;
        if(!$res) return false;
        else return true;
    } //End of function SendSysEmail()


    // ================================================================================================
    // Function : ActivateUser()
    // Date : 11.02.2011
    // Returns : true,false / Void
    // Description : Set status of user as Activated
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function ActivateUser( $activate_user )
    {
        $q = "SELECT `".TblModNewsSubscr."`.user_status
                FROM `".TblModNewsSubscr."`
                WHERE `login` = '$activate_user'";
        $res = $this->db->db_Query( $q );
        //echo '<br>$q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;
        $rows = $this->db->db_GetNumRows();
        if( !$rows or !$res)
            $this->ShowTextMessages($this->multi['TXT_ACTIVE_FALSE']);
        else {
            $row = $this->db->db_FetchAssoc();
            if($row['user_status'] == 0) { // Еще не был активирован
                $q = "UPDATE `".TblModNewsSubscr."`
                        SET `user_status`=1
                        WHERE `login` = '$activate_user'";
                $res = $this->db->db_Query( $q );
                //echo '<br>$q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;
                if( !$res)
                    $this->ShowTextMessages($this->multi['TXT_ACTIVE_FALSE']);
                else
                    $this->ShowTextMessages($this->multi['TXT_ACTIVE_OK']);
            }
            else {
                    $this->ShowTextMessages($this->multi['TXT_ALREADY_ACTIVE']);
            }
        }
        return true;
    } //end of function ActivateUser()


    //======================================= SubSribe END ===================================================



    //--------------------------------------------------------------------------------------------------------
    //---------------------------- FUNCTION FOR SETTINGS OF NEWS START ---------------------------------------
    //--------------------------------------------------------------------------------------------------------

    // ================================================================================================
    // Function : GetSettings()
    // Date : 27.03.2006
    // Returns : true,false / Void
    // Description : return all settings of catalog
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetSettings($front = true, $lang_id = NULL )
    {
        if( empty($lang_id)) $lang_id = $this->lang_id;
        $q = "SELECT * from `".TblModNewsSet."` where 1";
        //echo '$q ='.$q;
        $res = $this->db->db_Query( $q );
        if( !$this->db->result ) return false;
        $row = $this->db->db_FetchAssoc();
        if($front) {
            $row['title'] =  $this->Spr->GetNameByCod( TblModNewsSetSprTitle, 1, $lang_id, 1 );
            $row['description'] = $this->Spr->GetNameByCod( TblModNewsSetSprDescription, 1, $lang_id, 1 );
            $row['keywords'] = $this->Spr->GetNameByCod( TblModNewsSetSprKeywords, 1, $lang_id, 1 );
        }
        return $row;
    } // end of function GetSettings()

    // ================================================================================================
    // Function : SetSeoData()
    // Date : 18.06.2013
    // Parms :
    //           $pageTxt - text of DinamicPage
    // Returns : $this->title, $this->description, $this->keywords, $this->h1
    // Description : set title, description, keywords, h1 for this module or for current news or category
    //               of news
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function SetSeoData($pagetxt)
    {
        $this->h1 = '';
        $this->title = '';
        $this->description = '';
        $this->keywords = '';
        $defaultTitle = $pagetxt['pname'];
        if(!empty($this->id_cat)) $defaultTitle = $this->treeNewsCat[$this->id_cat]['name'].' | '.$defaultTitle;
        if(!empty($this->id)) {
            $q="select *
            FROM `".TblModNewsFull."` ,`".TblModNewsNames."`,`".TblModNews."`
            WHERE `".TblModNews."`.`id`='$this->id'
            and `".TblModNews."`.`id` = `".TblModNewsNames."`.`id_news`
            and `".TblModNews."`.`id` = `".TblModNewsFull."`.`id_news`
            and `".TblModNewsNames."`.`lang_id` = '".$this->lang_id."'
            and `".TblModNewsFull."`.`lang_id` = '".$this->lang_id."'";
            $res = $this->db->db_Query($q);
//            echo '<br>q='.$q.' res='.$res.'<br>'.$this->db->result;
            if (!$res || !$this->db->result) return false;
            $rows = $this->db->db_GetNumRows();
//            echo '<br>$rows='.$rows;
            if($rows>0){
                $row = $this->db->db_FetchAssoc();
                $this->name_to_path = $row['name'];
                $this->h1 = $row['h1'];
                if(empty($this->h1)){
                    $this->h1 = $row['name'];
                }
                $this->title = $row['title'];
                if(empty($this->title)){
                    $this->title = $row['name'].' | '.$defaultTitle;
                }
                $this->description = $row['descr'];
                $this->keywords = $row['keywords'];
            }
            return;
        }elseif(!empty($this->id_cat)){
            $row = $this->treeNewsCat[$this->id_cat];
            $this->name_to_path = $row['name'];
            $this->h1 = $row['name'];
            $this->title = $row['mtitle'];
            if(empty($this->title)){
                $this->title = $defaultTitle;
            }
            $this->description = $row['mdescr'];
            $this->keywords = $row['mkeywords'];
        }else{
            $this->name_to_path = $pagetxt['pname'];
            $this->h1 = $pagetxt['h1'];
            if(empty($this->h1)){
                $this->h1 = $pagetxt['pname'];
            }
            $this->FrontendPages = check_init('FrontendPages', 'FrontendPages');
            $this->title = $this->FrontendPages->GetTitle();
            $this->description = $this->FrontendPages->GetDescription();
            $this->keywords = $this->FrontendPages->GetKeywords();
        }

        if(empty($this->title))
            $this->title = $this->Spr->GetNameByCod( TblModNewsSetSprTitle, 1, $this->lang_id, 1 );

        if(empty($this->description))
            $this->description = $this->Spr->GetNameByCod( TblModNewsSetSprDescription, 1, $this->lang_id, 1 );

        if(empty($this->keywords))
            $this->keywords = $this->Spr->GetNameByCod( TblModNewsSetSprKeywords, 1, $this->lang_id, 1 );

        /*
        //echo '<br>$this->title='.$this->title.'<br>$title='.$title;
        if( empty($this->title) ){
            // echo "<br>task=".$this->task;
            switch($this->task){
                case 'showall':  $title = $this->multi['TXT_META_TITLE_ALL'];
                        break;
                case 'showa':  $title = $this->multi['TXT_META_TITLE_LAST'];
                        break;
                case 'arch':  $title = $this->multi['TXT_META_TITLE_ARCH'];
                        break;
                case 'new_subscriber':  $title = $this->multi['TXT_SUBSCRIBE'];
                        break;
            }
            if( !empty($this->title) ) $title = $title.' | '.$this->title;
        }
        */

        if($this->page>1) $this->title .= ' | Page'.$this->page;

    } //end of function  SetMetaData()


    // ================================================================================================
    // Function : GetIdCatByStr()
    // Date : 18.06.2013
    // Parms :
    //           $cat_str - tranlit by Cat
    // Returns : id_cat
    // Description : For transliteration category determines its id
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function GetIdCatByStr($str_cat = NULL){
        $keys = array_keys($this->treeNewsCat);
        $size = sizeof($keys);
        for($i=0;$i<$size;$i++){
            $row = $this->treeNewsCat[$keys[$i]]['translit'];
            if(strcmp($str_cat,$row)==0) return $keys[$i];
        }
    }

    //------------------------------------------------------------------------------------------------------------
    //---------------------------- FUNCTION FOR SETTINGS OF NEWS  END --------------------------------------------
    //------------------------------------------------------------------------------------------------------------



    //------------------------------------------------------------------------------------------------------------
    //----------------------------------- FUNCTION FOR RSS START -------------------------------------------------
    //------------------------------------------------------------------------------------------------------------

    // ================================================================================================
    // Function : GetNewsForRSS()
    // Date :    17.06.2011
    // Returns : true/false
    // Description : Get News for Rss
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GetNewsForRSS($limit = NULL, $lang_id)
    {
        $q = "SELECT
                `".TblModNews."`.*,
                `".TblModNewsSprSbj."`.name as sbj,
                `".TblModNewsSprShrt."`.name as short,
                `".TblModNewsSprFull."`.name as full,
                `".TblModNewsCat."`.name as category_name
            FROM
                `".TblModNews."` LEFT JOIN `".TblModNewsCat."` ON (`".TblModNews."`.`id_category`=`".TblModNewsCat."`.`cod` AND `".TblModNewsCat."`.`lang_id`='".$lang_id."'),
                 `".TblModNewsSprSbj."`,`".TblModNewsSprShrt."`, `".TblModNewsSprFull."`
            WHERE
                `".TblModNews."`.status = 'a'
                AND
                `".TblModNews."`.id = `".TblModNewsSprSbj."`.cod
                AND
                `".TblModNewsSprSbj."`.lang_id = '".$lang_id."'
                AND
                `".TblModNewsSprSbj."`.`name` != ''
                AND
                `".TblModNews."`.id = `".TblModNewsSprShrt."`.cod
                AND
                `".TblModNewsSprShrt."`.lang_id = '".$lang_id."'
                AND
                `".TblModNews."`.id = `".TblModNewsSprFull."`.cod
                AND
                `".TblModNewsSprFull."`.lang_id = '".$lang_id."'
            ORDER BY
                `".TblModNews."`.`display` desc
        ";
        if(!empty($limit)) $q .= " LIMIT ".$limit;
        $res = $this->db->db_Query($q);
        $rows = $this->db->db_GetNumRows($res);
        $array = array();
         for( $i = 0; $i <$rows; $i++ ){
             $row = $this->db->db_FetchAssoc();
             $array[$i] = $row;
         }
         return $array;
    }
    // ================================================================================================
    // Function : GenerateRSSNews()
    // Date :    17.06.2011
    // Returns : true/false
    // Description : Generate Rss Feed
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GenerateRSSNews()
    {
        if ( isset($settings['rss']) AND $settings['rss']=='0' ) {
            if (file_exists(SITE_PATH."/rss/news/export.xml")) $res = unlink (SITE_PATH."/rss/news/export.xml");
            return true;
        }

        if(empty($this->Crypt)) $this->Crypt = check_init('Crypt', 'Crypt');
        $ln_sys = new SysLang();
        $ln_arr = $ln_sys->LangArray( _LANG_ID );
        while( $el = each( $ln_arr ) ){
            $lang_id = $el['key'];
            $lang = $el['value'];
            $settings = $this->GetSettings(true, $lang_id);
            $outputArray =$this->GetNewsForRSS(NULL, $lang_id);
            $outputArrayCount  = count($outputArray);

            $title = stripslashes($settings['title']);
            $descr = stripslashes($settings['description']);
            $data = '<?xml version="1.0" encoding="utf-8" ?>
                <rss version="2.0">
                <channel>
                <image>
                <url>http://'.NAME_SERVER.'/images/design/logo_rss.gif</url>
                <title>'.$title.'</title>
                <link>http://'.NAME_SERVER.'/</link>
                </image>
                <title>'.$title.'</title>
                <link>http://'.NAME_SERVER.'/</link>
                <description>'.$descr.'</description>
                ';
                for($i = 0; $i<$outputArrayCount; $i++){
                    $row = $outputArray[$i];
                    $img_path = $this->GetMainImage($row['id'], 'front');
                    if(!empty($img_path)){
                        $enclosure = 'http://'.NAME_SERVER.'/'.$settings['img_path'].$row['id'].'/'.$img_path;
                    }
                    else
                        $enclosure = NULL;

                    $sbj = strip_tags(htmlspecialchars(stripslashes($row['sbj'])));
                    $link = $this->Link($row['id_category'],$row['id']);
                    $short = htmlspecialchars(strip_tags(stripslashes($row['short'] )));
                    $category = stripslashes($row['category_name']);
                    $full = trim(htmlspecialchars(strip_tags(stripslashes($row['full']))));
                    $full = $this->Crypt->TruncateStr($full,1000);
                    $date = date("D, d M Y H:i:s", strtotime($row['start_date']));

                    $data = $data.'
                    <item>
                    <title>'.$sbj.'</title>
                    <link>http://'.NAME_SERVER.$link.'</link>
                    <description>'.$short.'</description>
                    <author>http://'.NAME_SERVER.'</author>
                    <category>'.$category.'</category>
                    <enclosure url="'.$enclosure.'" type="image/jpeg"></enclosure>
                    <pubDate>'.$date.' +0300'.'</pubDate>
                    <fulltext>'.$full.'</fulltext>
                    </item>
                    ';
                }

            $data = $data.'</channel></rss>';
            //$_tmp_time = filemtime("export.xml")+43200;
            $path = SITE_PATH."/rss";
            if(!file_exists($path)){
                $res = mkdir($path, 0777);
            }
            $hhh = fopen($path."/export".$lang_id.".xml", "w+");
            fwrite($hhh, $data);
            fclose($hhh);
        }
    } //end of function GenerateRSSNews()

    // ================================================================================================
    // Function : ReadRss()
    // Date :    14.09.2009
    // Parms :   $url - url of rss chanel
    // Returns : true/false
    // Description : read rss news
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function ReadRss()
    {
        include_once($_SERVER['DOCUMENT_ROOT'].'/modules/mod_rss/rss_fetch.inc');
        $ss = 0;
        $insert = 0;
        $q="select * from `".TblModNewsRss."` where 1 and `status`='1'";
        $res = $this->db->db_Query( $q);
        //echo "<br> q = ".$q." res = ".$res;
        if( !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        if($rows>0){
            for($i = 0; $i<$rows; $i++){
                $row = $this->db->db_FetchAssoc($res);
                $descr_rss = $this->Spr->GetNameByCod( TblModNewsRssSprDescr, $row['id'], $this->lang_id );
                //echo "<br /> row['path'] = ".$row['path'];
                //echo "<br /> descr_rss = ".$descr_rss;

                $rss = fetch_rss($row['path']);
                //print_r($rss);
                //echo "<br />";
                foreach($rss as $key=>$val){
                    //echo "<br / > key = ".$key;
                    //echo "<br / > val = ".$val;
                    if(is_array($val) and count($val)>0){
                        foreach($val as $k=>$v){
                            //echo "<br / > k = ".$k;
                            //echo "<br / > v = ".$v;
                            $cn = count($v);
                            $j=0;
                            if(is_array($v) and $cn>0){
                                foreach($v as $k1=>$v1){
                                    if($k1=='title') {
                                        if($this->CheckIfNewsExist($v1)) continue;
                                        else{
                                            $this->subj_[$this->lang_id] = $v1;
                                            $insert=1;
                                        }
                                    }
                                    /*
                                    *form data of news for insert to db
                                    */
                                    if($k1=='category') {
                                        $this->category = $v1;
                                    }

                                    //echo "<br / > k1 = ".$k1;
                                    //echo "<br / > v1 = ".$v1;
                                    if($k1=='description') {
                                        $this->short_[$this->lang_id] = $v1;
                                    }

                                    if($k1=='fulltext') {
                                        $this->full_[$this->lang_id] = $v1;
                                    }

                                    if($k1=='link') {
                                        $this->source = $v1;
                                    }

                                    if($k1=='date_timestamp') {
                                        $this->date_timestamp = $v1;
                                    }
                                    $j++;

                                    if($insert==1 and $cn==($j)){
                                        if(strlen($this->short_[$this->lang_id])>100){
                                        if($this->SaveRssData()) $ss++;
                                        $insert = 0;
                                        $this->category = '';
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        echo "<br />".$this->Msg->show_text('MSG_RSS_IMPORTED_OK')." ".$ss;
    } // end of function ReadRss



    // ================================================================================================
    // Function : SaveRssData()
    // Date : 14.09.2009
    // Returns : true,false / Void
    // Description : Store data to the table
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function SaveRssData()
    {
        $ln_sys = new SysLang();

        $id_relart = NULL;
        if(trim($this->category)=='') $this->category = "Другие";

        $q = "select * from `".TblModNewsCat."` where 1 and `name`='".$this->category."' and `lang_id`='".$this->lang_id."'";
        $res = $this->db->db_Query($q);
        //echo "<br /> q SEL CAT = ".$q." res = ".$res;
        if( !$res ) return false;
        $rows = $this->db->db_GetNumRows();
        if($rows>0) {
            $row = $this->db->db_FetchAssoc();
            $this->id_category = $row['cod'];
        }
        else{
            $this->id_category = $this->GetMaxValueOfField(TblModNewsCat, 'cod')+1;
            $move = $this->GetMaxValueOfField(TblModNewsCat, 'move')+1;
            $q = "insert into `".TblModNewsCat."` values(NULL, '".$this->id_category."', '".$this->lang_id."', '".$this->category."','".$move."', '', '')";
            $res = $this->db->db_Query( $q );
            //echo "<br /> q = ".$q." res = ".$res;
            if( !$res ) return false;
            //$this->id_category =  $this->db->db_GetInsertID();
        }

        //echo "<br /> id_cat = ".$this->id_category ;

        if (empty($this->id_category)) {
            //$this->Msg->show_msg('NEWS_CATEGORY_EMPTY');
            echo "<br />".$this->Msg->show_text('MSG_RSS_IMPORT_NO_CATEGORY_FOR_NEWS').' <u>'.$this->subj_[$this->lang_id].'</u>';
        }
        if (empty( $this->subj_[$this->lang_id] )) {
           // $this->Msg->show_msg('NEWS_SUBJECT_EMPTY');
        }
        if (empty( $this->short_[$this->lang_id] )) {
          //  $this->Msg->show_msg('NEWS_SHORT_EMPTY');
        }

        //3600*24 - 1 day

        $start_d = date("Y-m-d H:i:s", $this->date_timestamp);
        $end_d = date("Y-m-d H:i:s", $this->date_timestamp+(3600*24*7));


        $display = $this->GetMaxValueOfField(TblModNews, 'display')+1;

        $q = "insert into `".TblModNews."` values(NULL,'".$this->id_category."','".$this->id_relart."','a','".$start_d."','".$end_d."','".$display."', '".$this->source."')";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        //echo "<br /> q = ".$q." res = ".$res;
        if( !$res ) return false;

        $id =  $this->db->db_GetInsertID();
        $this->id = $id;
        //else return true;

        $id = $this->id;

        $subject = addslashes(trim($this->subj_[$this->lang_id]));
        $keywords = '';
        $description = '';
        $short = addslashes($this->br2p($this->nltobr(trim($this->short_[$this->lang_id]))));
        $full = addslashes($this->br2p($this->nltobr(trim($this->full_[$this->lang_id ]))));
        if(strlen($full)<10) $full = $short;
        //echo "<br /> short = ".$short;
        $tmp = explode("<br>",$short);
        $n = count($tmp);
        //echo "<br /> n= ".$n;
        // echo   strip_tags(stripslashes(stripslashes( $"<br> tmp = ";
        // print_r($tmp);
        if($n>2){
            if(strlen($tmp[0])>150) $short = $tmp[0]."</p>";
            else $short = $tmp[0]."</p><p>".$tmp[1]."</p>";
        }

        //echo "<br /> short = ".$short;
        $full = strip_tags($full, "<p><img><br><strong><u><i><b><ul><li><table><tr><td>");


        $res = $this->Spr->SaveToSpr( TblModNewsSprSbj, $id, $this->lang_id, $subject );
        if( !$res ) return false;

        $res = $this->Spr->SaveToSpr( TblModNewsSprKeywords, $id, $this->lang_id, $keywords );
        if( !$res ) return false;

        $res = $this->Spr->SaveToSpr( TblModNewsSprDescription, $id, $this->lang_id, $description );
        if( !$res ) return false;

        $res = $this->Spr->SaveToSpr( TblModNewsSprShrt, $id, $this->lang_id, $short );
        if( !$res ) return false;

        $res = $this->Spr->SaveToSpr( TblModNewsSprFull, $id, $this->lang_id, $full );
        if( !$res ) return false;

        $l_link = $this->Link($this->id_category, $id);

        $res = $this->SavePicture();
        // if( !$res ) return false;
        // $res = $this->GenerateRSSNews();

        return true;
    } // end of function SaveRssData


    // ================================================================================================
    // Function : nltobr()
    // Date : 14.09.2009
    // Parms :  $var, $xhtml
    // Returns : true,false / Void
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function nltobr($var, $xhtml = FALSE){
        if($var){
            if($xhtml == FALSE){
                $array = array("\r\n", "\n\r", "\n", "\r");
                $var = str_replace($array, "<br>", $var);
                return $var;
            }
            else{
                $array = array("\r\n", "\n\r", "\n", "\r");
                $var = str_replace($array, "<br />", $var);
                return $var;
            }
        }
        else{
            return FALSE;
        }
    }//end of function nltobr()


    // ================================================================================================
    // Function : br2p()
    // Date : 14.09.2009
    // Parms :  $string
    // Returns : true,false / Void
    // Description :
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function br2p($string)
    {
      return preg_replace('#<p>[\n\r\s]*?</p>#m', '', '<p>'.preg_replace('#(<br\s*?/?>){2,}#m', '</p><p>', $string).'</p>');
    }//end of function br2p()

    //------------------------------------------------------------------------------------------------------------
    //----------------------------------- FUNCTION FOR RSS END ---------------------------------------------------
    //------------------------------------------------------------------------------------------------------------



    // ================================================================================================
    // Function : GetMaxValueOfField
    // Date : 19.05.2006
    // Parms :  $table  - name of the table
    // Returns : value
    // Description : return the biggest value
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetMaxValueOfField( $table = TblModNews, $field='move' )
    {
        $tmp_db = new DB();

        $q = "SELECT `".$field."` FROM `".$table."` WHERE 1  ORDER BY `".$field."` desc LIMIT 1";
        $res = $tmp_db->db_Query( $q );
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if( !$res OR !$tmp_db->result ) return false;
        //$rows = $tmp_db->db_GetNumRows();
        $row = $tmp_db->db_FetchAssoc();
        return $row[$field];
    } // end of function GetMaxValueOfField();

    // ================================================================================================
    //    Function          : CheckIfNewsExist
    //    Date              : 21.03.2006
    //    Parms             : $prod_name
    //    Returns           : Error Indicator
    //    Description       : Check If News Exist
    // ================================================================================================
    function CheckIfNewsExist($prod_name) {
      $q = "select * from `".TblModNewsSprSbj."` where 1 and `name` LIKE '%".$prod_name."%'";
      $res = $this->Right->Query($q, $this->user_id, $this->module);
      $rows = $this->Right->db_GetNumRows();
      //echo "<br> q = ".$q." res = ".$res." rows = ".$rows;
      if($rows==0){
        $tmp = explode(" ", $prod_name);
       // print_r($tmp);

         $str = $this->GetStrForSearch($tmp, 1);
          //echo "<br> str = ".$str;

         $q = "select * from `".TblModNewsSprSbj."` where 1 and `name` LIKE '%".$str."%' and `lang_id`='3'";
         $res = $this->Right->Query($q, $this->user_id, $this->module);
         $rows = $this->Right->db_GetNumRows();
       //  echo "<br> q = ".$q." res = ".$res." rows = ".$rows;
         if($rows!=1){
            $str = $this->GetStrForSearch($tmp, 2);
            //  echo "<br> str = ".$str;
              if($str=='') return false;
             $q = "select * from `".TblModNewsSprSbj."` where 1 and `name` LIKE '%".$str."%' and `lang_id`='3'";
             $res = $this->Right->Query($q, $this->user_id, $this->module);
             $rows = $this->Right->db_GetNumRows();
             //echo "<br> q = ".$q." res = ".$res." rows = ".$rows;
              if($rows!=1){
              $str = $this->GetStrForSearch($tmp, 3);
           //   echo "<br> str = ".$str;
              if($str=='') return false;
             $q = "select * from `".TblModNewsSprSbj."` where 1 and `name` LIKE '%".$str."%' and `lang_id`='3'";
             $res = $this->Right->Query($q, $this->user_id, $this->module);
             $rows = $this->Right->db_GetNumRows();
             //echo "<br> q = ".$q." res = ".$res." rows = ".$rows;

                if($rows!=1){
                  $str = $this->GetStrForSearch($tmp, 4);
              //    echo "<br> str = ".$str;
                 if($str=='') return false;
                 $q = "select * from `".TblModNewsSprSbj."` where 1 and `name` LIKE '%".$str."%' and `lang_id`='3'";
                 $res = $this->Right->Query($q, $this->user_id, $this->module);
                 $rows = $this->Right->db_GetNumRows();
                // echo "<br> q = ".$q." res = ".$res." rows = ".$rows;
                  }
              }
         }
      }
      $row = $this->Right->db_FetchAssoc();
      if($rows==1)
      {
        return $row['cod'];
      }
      else return false;
    } // end of function  CheckIfNewsExist

    // ================================================================================================
    // Function : GetStrForSearch()
    // Date : 19.02.2008
    // Returns : true,false / file
    // Description : build search string
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GetStrForSearch($mas, $start=0)
    {
     $str = '';
     $count = sizeof($mas);
     for($i=$start;$i<$count;$i++){
           $str .= $mas[$i]." ";
         }
         $str = trim($str);
      return $str;
    } // end of function GetStrForSearch

    // ================================================================================================
    // Function : build_str_like
    // Date : 19.01.2005
    // Parms : $find_field_name - name of the field by which we want to do search
    //         $field_value - value of the field
    // Returns : str_like_filter - builded string with special format;
    // Description : create the string for SQL-command SELECT for search in the text field by any word
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function build_str_like($find_field_name, $field_value){
            $str_like_filter=NULL;
            // cut unnormal symbols
            $field_value=preg_replace("/[^\w\x7F-\xFF\s]/", " ", $field_value);
            // delete double spacebars
            $field_value=str_replace(" +", " ", $field_value);
            $wordmas=explode(" ", $field_value);

            for ($i=0; $i<count($wordmas); $i++){
                  $wordmas[$i] = trim($wordmas[$i]);
                  if (EMPTY($wordmas[$i])) continue;
                  if (!EMPTY($str_like_filter)) $str_like_filter=$str_like_filter." OR ".$find_field_name." LIKE '%".$wordmas[$i]."%'";
                  else $str_like_filter=$find_field_name." LIKE '%".$wordmas[$i]."%'";
            }
            if ($i>1) $str_like_filter="(".$str_like_filter.")";
            //echo '<br>$str_like_filter='.$str_like_filter;
     return $str_like_filter;
    } //end of function build_str_like()

    // ================================================================================================
    // Function : QuickSearch()
    // Date : 27.03.2008
    // Parms :  $search_keywords
    // Returns : true,false / Void
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function QuickSearch($search_keywords)
    {
        $search_keywords = stripslashes($search_keywords);

        $sel_table = NULL;
        $str_like = NULL;
        $filter_cr = ' OR ';
        $str_like = $this->build_str_like(TblModNewsNames.'.name', $search_keywords);
        $str_like .= $filter_cr.$this->build_str_like(TblModNewsShort.'.short', $search_keywords);
        $str_like .= $filter_cr.$this->build_str_like(TblModNewsFull.'.full', $search_keywords);
        $sel_table = "`".TblModNews."`, `".TblModNewsCat."`, `".TblModNewsNames."`, `".TblModNewsShort."`, `".TblModNewsFull."` ";

        $q ="SELECT `".TblModNews."`.id,
        `".TblModNews."`.id_cat,
        `".TblModNews."`.status,
        `".TblModNews."`.display,
        `".TblModNewsNames."`.`name` AS `news_name`
             FROM ".$sel_table."
             WHERE (".$str_like.")
             AND `".TblModNewsNames."`.lang_id = '".$this->lang_id."'
             AND `".TblModNews."`.id = `".TblModNewsNames."`.id_news
             AND `".TblModNewsShort."`.lang_id = '".$this->lang_id."'
             AND `".TblModNews."`.id = `".TblModNewsShort."`.id_news
             AND `".TblModNewsFull."`.lang_id = '".$this->lang_id."'
             AND `".TblModNews."`.id = `".TblModNewsFull."`.id_news
             AND `".TblModNews."`.`id_cat` = `".TblModNewsCat."`.`cod`
             AND `".TblModNewsCat."`.lang_id = '".$this->lang_id."'
             AND `".TblModNews."`.status != 'n'
             AND `".TblModNews."`.status != 'i'
             AND `".TblModNews."`.`visible` = '1'
             ORDER BY `".TblModNewsCat."`.`move` asc, `".TblModNews."`.`display` asc
            ";

        $res = $this->db->db_Query( $q );
//        echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$this->db->result;
        if ( !$res) return false;
        if( !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        //echo "<br> rows = ";
        //print_r($rows);
        return $rows;
   } // end of function QuickSearch

     // ================================================================================================
     // Function : GetTopNews()
     // Date : 11.09.2009
     // Returns :      true,false / Void
     // Description :  Get Top News()
     // Programmer : Yaroslav Gyryn
     // ================================================================================================
     function GetTopNews($limit = 5)   {
        $q = "SELECT
            `".TblModNews."`.id,
            `".TblModNews."`.start_date,
            `".TblModNews."`.id_category,
            `".TblModNews."`.top_main,
            `".TblModNewsTop."`.name,
            `".TblModNewsTop."`.short,
            `".TblModNewsTop."`.image,
            `".TblModNewsLinks."`.link
            FROM
                `".TblModNews."`, `".TblModNewsTop."`, `".TblModNewsLinks."`
            WHERE
                `".TblModNews."`.id = `".TblModNewsTop."`.cod   and
                `".TblModNews."`.id = `".TblModNewsLinks."`.cod and
                `".TblModNews."`.top = '1'  and
                `".TblModNewsTop."`.lang_id='".$this->lang_id."' and
                `".TblModNews."`.status='a'
            ORDER BY
                `".TblModNews."`.top_main, `".TblModNews."`.start_date  DESC
            LIMIT ".$limit;

        $res = $this->db->db_Query($q);
        //echo "<br> ".$q." <br/> res = ".$res;
        $rows = $this->db->db_GetNumRows($res);
        $arrNews = array();
        for( $i=0; $i<$rows; $i++ ) {
            $arrNews[] = $this->db->db_FetchAssoc($res);
        }
        for( $i=0; $i<$rows; $i++ ) {
            $arrNews[$i]['link'] = $this->Link($arrNews[$i]['id_category'], $arrNews[$i]['id'], $arrNews[$i]['link']);
            $arrNews[$i]['type'] = 'news';
        }
        return $arrNews;
     }



     // ================================================================================================
     // Function : GetNewsNameLinkForId()
     // Date : 11.09.2009
     // Returns :      true,false / Void
     // Description :  Get News Name Link For Id()
     // Programmer : Yaroslav Gyryn
     // ================================================================================================
     function GetNewsNameLinkForId($str = null) {
          $q = "SELECT
                `".TblModNews."`.id,
                `".TblModNews."`.id_category,
                `".TblModNewsLinks."`.link,
                `".TblModNewsSprSbj."`.name
            FROM
                `".TblModNews."`, `".TblModNewsLinks."`, `".TblModNewsSprSbj."`
            WHERE
                `".TblModNewsSprSbj."`.cod = `".TblModNews."`.id
            AND
                `".TblModNewsLinks."`.cod = `".TblModNews."`.id
            AND
                `".TblModNewsSprSbj."`.lang_id='".$this->lang_id."'
            AND
                `".TblModNews."`.id in (".$str.")
            ";
            $res = $this->db->db_Query( $q );
            //echo "<br> ".$q." <br/> res = ".$res;
            $rows = $this->db->db_GetNumRows($res);

            $arrNews = array();
            for( $i=0; $i<$rows; $i++ ) {
                $row = $this->db->db_FetchAssoc($res);
                $id = $row['id'];
                if(!isset($arrNews[$id])) {
                    $arrNews[$id]['name'] = $row['name'];
                    $arrNews[$id]['link'] = $this->Link($row['id_category'], $id, $row['link']);
                }
            }
            return  $arrNews;
     }

     // ================================================================================================
     // Function : GetRelatProdToNews()
     // Version : 1.0.0
     // Date : 28.11.2008
     // Parms : $id_news - id of the news
     // Returns :      true,false / Void
     // Description :  get realit products tho current news
     // ================================================================================================
     // Programmer :  Igor Trokhymchuk
     // Date : 28.11.2008
     // Reason for change : Creation
     // Change Request Nbr:
     // ================================================================================================
     function GetRelatProdToNews($id_news)
     {
        $db1 = new DB();
        $arr = array();
        $q = "SELECT * FROM `".TblModNewsRelatProd."` WHERE `id_news`='".$id_news."' ORDER BY `id` asc";
        $res = $db1->db_Query($q);
        //echo '<br>$q='.$q.' $res='.$res.' $db1->result='.$db1->result;
        if( !$res OR ! $db1->result) return false;
        $rows = $db1->db_GetNumRows();
        for($i=0;$i<$rows;$i++){
            $tmp_row = $db1->db_FetchAssoc();
            $arr[$i] = $tmp_row['id_prod'];
        }
        //print_r($arr);
        return $arr;
     }//end of GetRelatProdToNews


    // ================================================================================================
    // Function : GetNRows()
    // Date : 01.03.2011
    // Returns :      true,false / Void
    // Description :  Get count all news for PagesFront
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function GetNRows($limit=false)
    {
        $q = "SELECT
            *
        FROM
            `".TblModNews."`, `".TblModNewsShort."`, `".TblModNewsNames."`
        WHERE
            `".TblModNews."`.id = `".TblModNewsShort."`.id_news and
            `".TblModNews."`.id = `".TblModNewsNames."`.id_news and
            `".TblModNewsShort."`.lang_id='".$this->lang_id."' and
            `".TblModNewsNames."`.`lang_id`='".$this->lang_id."' and
            `".TblModNewsNames."`.`name`!='' and
            `".TblModNews."`.`status`!='n' and
            `".TblModNews."`.`visible` = '1'
	ORDER BY  `".TblModNews."`.`start_date` DESC
         ";
       // if( $this->fltr!='' ) $q = $q.$this->fltr;
       // $q = $q." order by `".TblModNews."`.display DESC ";
        if($limit) $q .= " limit ".$this->start.",".$this->display."";
        $res = $this->db->db_Query( $q );
//        echo "<br>q=".$q." res=".$res;
        if( !$res OR !$this->db->result) return false;
        $rows = $this->db->db_GetNumRows();
        //echo "<br>rows=".$rows;
        $array = array();
        for( $i = 0; $i <$rows; $i++ ){
            $array[$i] = $this->db->db_FetchAssoc();
        }
        return $array;
    } // end of  GetNRows

    // ================================================================================================
    // Function : GetUniqueLink()
    // Date : 05.02.2014
    // Returns :      string,false
    // Description :  Build a unique transliteration for news
    // Programmer :  Bogdan Iglinsky
    // ================================================================================================
    function GetUniqueLink($link = 0,$id_news = 0,$cnt=0){
        if(is_numeric($link)) $link = 'news-'.$link;
        $q = "SELECT
            *
        FROM
             `".TblModNewsNames."`
        WHERE
            `".TblModNewsNames."`.`lang_id`='".$this->lang_id."' and
            `".TblModNewsNames."`.`link` = '".$link."' and
            `".TblModNewsNames."`.`id_news` != '".$id_news."'
         ";
        $res = $this->db->db_Query( $q );
//        echo "<br>q=".$q." res=".$res;
        if( !$res OR !$this->db->result) return false;
        $rows = $this->db->db_GetNumRows();
        if($rows>0){
            if($cnt<5){
                return $this->GetUniqueLink($link.$id_news,$id_news,$cnt++);
            }elseif($cnt==5){
                return $this->GetUniqueLink($link.time(),$id_news,$cnt);
            }else{
                return false;
            }
        }else{
            return $link;
        }
    }

    // ================================================================================================
    // Function : getLinlMewsById()
    // Date : 05.02.2014
    // Returns :      string,false
    // Description :  Pulls news link id
    // Programmer :  Bogdan Iglinsky
    // ================================================================================================
    function getLinlMewsById($id_news = ''){
        $q = "SELECT
            *
        FROM
             `".TblModNewsNames."`
        WHERE
            `".TblModNewsNames."`.`lang_id`='".$this->lang_id."' and
            `".TblModNewsNames."`.`id_news` = '".$id_news."'
         ";
        $res = $this->db->db_Query( $q );
//        echo "<br>q=".$q." res=".$res;
        if( !$res OR !$this->db->result) return false;
        $rows = $this->db->db_GetNumRows();
        if($rows>0){
            $row = $this->db->db_FetchAssoc();
            return $row['link'];
        }else{
            return false;
        }
    }

} //--- end of class News

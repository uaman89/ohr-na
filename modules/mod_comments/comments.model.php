<?php
include_once( SITE_PATH.'/modules/mod_comments/comments.controller.class.php' );
/**
 * Created by JetBrains PhpStorm.
 * User: sergey
 * Date: 03.10.12
 * Time: 14:51
 */
class Comments extends SystemComments
{
    var $display = 10;
    var $page = 1;
    var $start = 0;

    function __construct($module = NULL, $id_item = NULL)
    {
        //Check if Constants are overrulled
        ($module != "" ? $this->module = $module : $this->module = NULL);
        ($id_item != "" ? $this->id_item = $id_item : $this->id_item = NULL);

        if (defined("_LANG_ID")) $this->lang_id = _LANG_ID;

        if (empty($this->db)) $this->db = DBs::getInstance();
        if (empty($this->Spr)) $this->Spr = check_init('SystemSpr', 'SystemSpr', "'', '$this->module'");
        if (empty($this->Form)) $this->Form = check_init('FrontFormCommnents', 'FrontForm', "'form_comments'");
        if (empty($this->Logon)) $this->Logon = check_init('UserAuthorize', 'UserAuthorize');
        $this->multi = check_init_txt('TblFrontMulti', TblFrontMulti);
        $this->start = ($this->page - 1) * $this->display;
        $this->uselogon = 1;

    } // End of FrontComments Constructor

    // ================================================================================================
    // Programmer : Yaroslav Gyryn
    // Date : 01.06.2011
    // Reason for change : Reason Description / Delete Comments
    // ================================================================================================
    function deleteComments()
    {
        $commetn=$this->getCommentById($this->idComment);
        if($this->Logon->user_id!=$commetn['id_user']) return false;

        $q = "SELECT
                 COUNT(`" . TblSysModComments . "`.id) as count
            FROM
                `" . TblSysModComments . "`
            WHERE
                `level`=" . $this->idComment . "
            ";
        $res = $this->db->db_Query($q);
        //echo '$q ='.$q.' <br/>$res = '.$res.'<br/>';
        if (!$res OR !$this->db->result)
            return 0;
        $row = $this->db->db_FetchAssoc($res);
        if ($row['count'] == 0) {
            $q = "DELETE FROM `" . TblSysModComments . "` WHERE id=" . $this->idComment . "";
            $res = $this->db->db_Query($q);
            //echo '$q ='.$q.' <br/>$res = '.$res.'<br/>';
            if (!$res OR !$this->db->result)
                return 0;

            $q = "DELETE FROM `" . TblSysModComments . "` WHERE cod=" . $this->idComment . "";
            $res = $this->db->db_Query($q);
            //echo '$q ='.$q.' <br/>$res = '.$res.'<br/>';
            /*if(!$res OR !$this->db->result)
                return 0;*/

            return 1;
        } else
            return -1;
    }

    // ================================================================================================
    // Function : PopularItems()
    // Date : 20.06.2011
    // Returns : true,false / Void
    // Description : Show form to leave Comments
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function popularItems($module = null, $limit = 10)
    {
        if ($module == null)
            $module = $this->module;
        $q = "SELECT
                    `id_item`,
                    count(id_item) as `count`
                FROM
                    `" . TblSysModComments . "`
                WHERE
                    `id_module`='" . $module . "'
                GROUP BY
                    id_item
                ORDER BY
                    `count` DESC
                LIMIT " . $limit;

        $res = $this->db->db_Query($q);
        if (!$res OR !$this->db->result)
            return false;
        //echo '$q ='.$q.' <br/>$res = '.$res.'<br/>';
        $rows = $this->db->db_GetNUmRows($res);
        if ($rows == 0)
            return false;

        $arr = array();
        for ($i = 0; $i < $rows; $i++) {
            $arr[] = $this->db->db_FetchAssoc($res);
        }
        $str = null;
        for ($i = 0; $i < $rows; $i++) {
            if (empty($str))
                $str = $arr[$i]['id_item'];
            else
                $str = $str . ',' . $arr[$i]['id_item'];
        }
        return $str;
    }

    // ================================================================================================
    // Function : GetCommentsCountItem()
    // Date : 08.08.2011
    // Returns : true,false / Void
    // Description :
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function getCommentsCountItem($module, $item)
    {
        $q = "SELECT
                    COUNT(`" . TblSysModComments . "`.id) as count
                FROM
                    `" . TblSysModComments . "`
                WHERE
                    `id_module`=" . $module . "
                AND `id_item`=" . $item . "
                AND `status` = '1'
                AND `level` = '0'
                ";
        $res = $this->db->db_Query($q);
        if (!$res OR !$this->db->result)
            return false;
        //echo '$q ='.$q.' <br/>$res = '.$res.'<br/>';
        $row = $this->db->db_FetchAssoc($res);
        $count = 0;
        $count = $row['count'];
        return $count;
    }
    // ================================================================================================

    function getCommentsCountAndRating($module, $item)
    {
        $q = "SELECT
                    COUNT( `" . TblSysModComments . "`.id ) as count,
                    ( SELECT COUNT(`" . TblSysModComments . "`.id)
                      FROM `" . TblSysModComments . "`
                      WHERE `status` = '1'
                            AND `id_module`=" . $module . "
                            AND `id_item`=" . $item . "
                            AND `vote` != '0') as rated_count,
                    SUM(`" . TblSysModComments . "`.vote) as rating
                FROM
                    `" . TblSysModComments . "`
                WHERE
                    `id_module`=" . $module . "
                AND `id_item`=" . $item . "
                AND `status` = '1'
                AND `level` = '0'
                ";
        $res = $this->db->db_Query($q);
        if (!$res OR !$this->db->result)
            return false;
        //echo '$q ='.$q.' <br/>$res = '.$res.'<br/>';
        $row = $this->db->db_FetchAssoc($res);
        //var_dump($row);
        if ( $row['rating'] > 0 )
            $row['rating'] = round($row['rating'] / $row['rated_count']);
        return $row;
    }


    // ================================================================================================
    // Function : GetCommentsCountItem()
    // Date : 08.08.2011
    // Returns : true,false / Void
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function getCommentsCount($str = null)
    {
        $q = "SELECT
                id_item,
            COUNT(`" . TblSysModComments . "`.id) as count
            FROM
                `" . TblSysModComments . "`
            WHERE
                `id_module`=" . $this->module . "
            AND
                `status` = '1'
                ";

        if ($str != null) {
            $q .= " AND id_item IN (" . $str . ") ";
        }
        $q .= "GROUP BY
                `id_item` DESC
        ";
        $res = $this->db->db_Query($q);
        //echo $q.' <br/>$res = '.$res.'<br/>';

        if (!$res or !$this->db->result)
            return false;

        $rows = $this->db->db_GetNumRows();
        $arr = array();
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc($res);
            $arr[$row['id_item']] = $row['count'];
        }
        return $arr;
    }
    // ================================================================================================
    // Function : GetCommentsCountItem()
    // Date : 08.08.2011
    // Returns : true,false / Void
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function getUserCommentsCount($user_id)
    {
        $q = "SELECT
            COUNT(`" . TblSysModComments . "`.id) as `count`
            FROM
                `" . TblSysModComments . "`
            WHERE
                `id_user`=" . $user_id . "
            AND
                `status` = '1'
                ";

        $res = $this->db->db_Query($q);
        //echo $q.' <br/>$res = '.$res.'<br/>';

        if (!$res or !$this->db->result)
            return 0;

        $row = $this->db->db_FetchAssoc($res);
        return $row['count'];
    }
    //-------------------------------------------------------------------------------------------------

    function getAnswersCount( $comment_id ){
        $q = "SELECT
            COUNT(`" . TblSysModComments . "`.id) as `count`
            FROM
                `" . TblSysModComments . "`
            WHERE
                `level`='" . $comment_id . "'
                AND `status` = '1'
                ";

        $res = $this->db->db_Query($q);
        //echo $q.' <br/>$res = '.$res.'<br/>';

        if (!$res or !$this->db->result)
            return 0;

        $row = $this->db->db_FetchAssoc($res);
        return $row['count'];
    }

    // ================================================================================================
    // Function : AddVote()
    // Date : 08.08.2011
    // Returns : true,false / Void
    // Description : Add Vote
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function addVote()
    {
        // Вибірка  к-сті голосів для коментаря
        $q = "SELECT
                `" . TblSysModComments . "`.rating
            FROM
                `" . TblSysModComments . "`
            WHERE
                `id`=" . $this->idComment . "
        ";
        $res = $this->db->db_Query($q);
        //echo '<br>'.$q.' <br>$res='.$res.' $this->db->result='.$this->db->result;
        if (!$res OR !$this->db->result) return false;
        $row = $this->db->db_FetchAssoc($res);
        $rating = intval($row['rating']);

        // Перевірка чи користувач ще не голосував
        $q = "SELECT
                    COUNT(`" . TblSysModCommentsRating . "`.id) as count
                FROM
                    `" . TblSysModCommentsRating . "`
                WHERE
                    `sys_user_id`=" . $this->idUser . "
                AND
                    `cod`=" . $this->idComment . "
            ";
        $res = $this->db->db_Query($q);
        //echo '<br>'.$q.' <br>$res='.$res.' $this->db->result='.$this->db->result;
        if (!$res OR !$this->db->result)
            return false;
        //$rows = $this->db->db_GetNUmRows($res);
        $row = $this->db->db_FetchAssoc($res);
        if ($row['count'] == 0) {
            // Запис голоса в таблицю для користувача
            //echo '$this->vote = '.$this->vote;
            $q = "INSERT INTO
                        `" . TblSysModCommentsRating . "`
                    SET
                      `cod`='" . $this->idComment . "',
                      `sys_user_id`='" . $this->idUser . "',
                      `vote`='" . $this->vote . "'
            ";
            $res = $this->db->db_Query($q);
            //echo '<br>'.$q.' <br>$res='.$res.' $this->db->result='.$this->db->result;
            if (!$res OR !$this->db->result) return false;

            $rating += $this->vote;

            // Оновлення рейтингу для коментаря
            $q = "UPDATE
                        `" . TblSysModComments . "`
                    SET
                        `rating` = '" . $rating . "'
                    WHERE
                        `id` = '" . $this->idComment . "'
            ";
            $res = $this->db->db_Query($q);
            //echo '<br>'.$q.' <br>$res='.$res.' $this->db->result='.$this->db->result;
            if (!$res OR !$this->db->result) return false;
        }

        return $rating;
    }

    // ================================================================================================
    // Function : CheckFields()
    // Date : 08.08.2011
    // Returns : true,false / Void
    // Description : show form with rating from users about goods
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function checkFields()
    {
        if (empty($this->name))
            die(json_encode(array('err' => 'msg', 'div_id' => 'commentsNameId', 'err_cont' => 'Нужно обязательно заполнить поле "Имя"')));
        /*
        if (empty($this->password))
            die(json_encode(array('err' => 'msg', 'div_id' => 'commentsPassId', 'err_cont' => 'Нужно обязательно заполнить поле "Пароль"')));

        $this->Logon->user_valid($this->login, $this->password, 1);
        if (empty($this->Logon->user_id))
            die(json_encode(array('err' => 'msg', 'div_id' => 'commentsLoginId', 'err_cont' => 'Неправильный логин или пароль')));
        else {
            $User = check_init('User', 'User');
            if (($this->Logon->user_type == '5' OR $this->Logon->user_type == '6' OR $this->Logon-
                $tmp_status = $User->GetUserStatus($this->Logon->user_id);>user_type == '7') & $tmp_status != '3') {
                $this->Logon->Logout();
                die(json_encode(array('err' => 'msg', 'div_id' => 'commentsLoginId', 'err_cont' => 'Даный пользователь не активирован или заблокирован.')));
            }
        }
        */
        if (empty($this->text))
            die(json_encode(array('err' => 'msg', 'div_id' => 'commentsTextId', 'err_cont' => 'Нужно обязательно написать комментарий')));

        return true;
    }

    function saveComment($id,$text){
        $commetn=$this->getCommentById($id);
        if($this->Logon->user_id!=$commetn['id_user']) return false;
        $q="UPDATE  `" . TblSysModComments . "` SET
            `text`='$text'
            WHERE `id`=$id
            ";
        $res = $this->db->db_Query($q);
    }

    // ================================================================================================
    // Function : SaveComments()
    // Date : 26.08.2011
    // Returns :      true,false / Void
    // Description :  Save data to database
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function addComment()
    {
        $this->dt = time();
        $this->status = 2;
        $q = "INSERT INTO `" . TblSysModComments . "` SET
              `id_module`='" . $this->module . "',
              `id_item`='" . $this->id_item . "',
              `level`='" . $this->level . "',
              `dt`='" . date("Y-m-d") . " " . date("G:i:s") . "',
              `status`='" . $this->status . "',
              `text`='" . $this->text . "',
              `id_user`='" . $this->Logon->user_id . "',
              `name`='" . $this->name . "',
              `email`='" . $this->email . "',
              `vote`='".$this->vote."'
             ";
        $res = $this->db->db_Query($q);
        //echo '<br>$q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;
        if (!$res OR !$this->db->result){
            die( json_encode( array('err' => 'msg', 'err_cont' => 'Не удалось добавить отзыв :(') ) );
        }
        $this->id = $this->db->db_GetInsertID();
        return true;
    }


    // ================================================================================================
    // Programmer : Yaroslav Gyryn
    // Date : 01.06.2011
    // Reason for change : Reason Description / Creation
    // ================================================================================================
    function GetUserCommentsTree($count=false, $idUser = null)
    {
        $this->user_id = $idUser;
        $tree = $this->LoadCommentsUserTreeList($count);
        if($count) return $tree;

        if (isset($this->moduleId['24'])) {
            if (empty($this->News)) $this->News = check_init('NewsLayout', 'NewsLayout');
            $this->arrNews = $this->News->GetNewsNameLinkForId($this->moduleId['24']);
            $tree['modules']['24']=$this->arrNews ;
        }

        if (isset($this->moduleId['32'])) {
            if (empty($this->Article)) $this->Article = check_init('ArticleLayout', 'ArticleLayout');
            $this->arrArticles = $this->Article->GetArticlesNameLinkForId($this->moduleId['32']);
            $tree['modules']['32']=$this->arrArticles ;
        }
        if (isset($this->moduleId['37'])) {
            if (empty($this->FrontendPages)) $this->FrontendPages = check_init('FrontendPages', 'FrontendPages');
            $this->arrPages = $this->Article->GetSharesNameLinkForId($this->moduleId['37']);
            $tree['modules']['37']=$this->arrPages ;
        }
        if (isset($this->moduleId['35'])) {
            if (empty($this->Blog)) $this->Blog = check_init('userBlogLayout', 'userBlogLayout');
            $this->arrBlogs = $this->Blog->GetBlogNameLinkForId($this->moduleId['35']);
            $tree['modules']['35']=$this->arrBlogs ;
        }

        return $tree;


        /*
        if(isset($this->moduleId['153'])) {
            if(empty($this->Video)) $this->Video = Singleton::getInstance('VideoLayout');
            $this->arrVideos = $this->Video->GetVideosNameLinkForId($this->moduleId['153']);
        }

        if(isset($this->moduleId['156'])) {
            if(empty($this->Gallery)) $this->Gallery = Singleton::getInstance('GalleryLayout');
            $this->arrGallerys = $this->Gallery->GetGallerysNameLinkForId($this->moduleId['156']);
        }
        */

    }

    //end of function SaveComments()

    // ================================================================================================
    // Function : SendEmailNotification()
    // Date : 26.06.2012
    // Returns : true,false / Void
    // Description : Send email notification t oadmin
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function sendEmailNotification()
    {
        $link = $this->referer_page;
        $subject = 'Добавлен новый комментарий к ' . $this->referer_page;
        $body = 'Новый комментарий можете отмодерировать по ссылке <a href="/admin/index.php?module=71#id' . $this->id . '"></a>';
        $SysSet = new SysSettings();
        $sett = $SysSet->GetGlobalSettings();
        $mail_admin = new Mail();
        $mail_admin->WordWrap = 500;
        $mail_admin->IsHTML(true);
        $mail_admin->Subject = $subject;
        $mail_admin->Body = $body;
        if (!empty($sett['mail_auto_emails'])) {
            $hosts = explode(";", $sett['mail_auto_emails']);
            for ($i = 0; $i < count($hosts); $i++) {
                //$arr_emails[$i]=$hosts[$i];
                $mail_admin->AddAddress($hosts[$i]);
            }
            //end for
        }
        $res_admin = $mail_admin->SendMail();
        return $res_admin;
    } //end of function SendEmailNotification()


    // ================================================================================================
    // Function : SendResponseEmail()
    // Date : 13.06.2011
    // Returns : true,false / Void
    // Description : Send Response to Email
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function sendResponseEmail()
    {
        $q = "SELECT
                        `" . TblModUser . "`.email,
                        `" . TblModUser . "`.sys_user_id,
                        `" . TblModUser . "`.name as first_name,
                        `" . TblModUser . "`.country as second_name
                    FROM
                        `" . TblModUser . "`, `" . TblSysModComments . "`
                    WHERE
                        `" . TblModUser . "`.sys_user_id = `" . TblSysModComments . "`.id_user
                    AND
                        `" . TblSysModComments . "`.id_module = '" . $this->module . "'
                    AND
                        `" . TblSysModComments . "`.id_item = '" . $this->id_item . "'
                    AND
                        `" . TblSysModComments . "`.id = '" . $this->level . "'
            ";
        $res = $this->db->db_Query($q);
        if (!$res OR !$this->db->result)
            return false;
        //echo '<br/>'.$q.' <br/>$res = '.$res.'<br/>';

        $row = $this->db->db_FetchAssoc();
        $username = '';
        $email = $row['email'];
        $idUser = $row['sys_user_id'];
        if (!empty($row['second_name']) or !empty($row['first_name'])) {
            $username = stripslashes($row['second_name']) . ' ' . stripslashes($row['first_name']);
        } else {
            if (empty($this->User))
                $this->User = Singleton::getInstance('User');
            $username = $this->User->GetUserLoginByUserId($idUser);
        }

        if (empty($email)) {
            if (empty($SysUser))
                $SysUser = Singleton::getInstance('SysUser');
            $email = $SysUser->GetUserEmailByUserId($idUser);
        }


        if (!empty($email)) {
            $link = $this->referer_page;
            $subject = '"Вам відповіли на залишений коментар"';
            $body = 'Доброго дня, ' . $username . '! <br/>Вам відповіли на залишений коментар: <br/><a href=' . $link . '>' . $link . '</a>';

            //================ send by class Mail START =========================
            $mail = new Mail();
            $mail->AddAddress($email);
            $mail->WordWrap = 500;
            $mail->IsHTML(true);
            //$mail->FromName = $name;
            $mail->Subject = $subject;
            $mail->Body = $body;
            if (!$mail->SendMail()) {
                echo "<h2 class='err'>Повідомлення не відправлено!</h2>";
                return false;
            }
        } else
            return false;
        return true;
    } //end of function SendResponseEmail()


    // ------------------------------------------------------------------------------------------------
    function LoadCommentsUserTreeList($count=false)
    {
        if($count){
            $q = "SELECT
                count(`" . TblSysModComments . "`.`id`) AS count
            FROM
                `" . TblSysModComments . "`
            LEFT JOIN `" . TblModUser . "` ON
            (
                `" . TblSysModComments . "`.`id_user`=`" . TblModUser . "`.`sys_user_id`
            )
            WHERE
                `" . TblSysModComments . "`.status = '1'
            AND
                `" . TblSysModComments . "`.id_user = " . $this->user_id . "
            ORDER BY
                `" . TblSysModComments . "`.dt desc";
            $res = $this->db->db_Query($q);
            if(!$res) return 0;
            $row = $this->db->db_FetchAssoc($res);
            return $row['count'];
        }

        $q = "SELECT
                `" . TblSysModComments . "`.id,
                `" . TblSysModComments . "`.id_module,
                `" . TblSysModComments . "`.id_item,
                `" . TblSysModComments . "`.id_user,
                `" . TblSysModComments . "`.dt,
                `" . TblSysModComments . "`.level,
                `" . TblSysModComments . "`.text,
                `" . TblModUser . "`.discount as img,
                `" . TblModUser . "`.name as first_name,
                `" . TblModUser . "`.country as second_name
            FROM
                `" . TblSysModComments . "`
            LEFT JOIN `" . TblModUser . "` ON
            (
                `" . TblSysModComments . "`.`id_user`=`" . TblModUser . "`.`sys_user_id`
            )
            WHERE
                `" . TblSysModComments . "`.status = '1'
            AND
                `" . TblSysModComments . "`.id_user = " . $this->user_id . "
            ORDER BY
                `" . TblSysModComments . "`.dt desc
            LIMIT $this->start, $this->display
                ";

        // $q = $q." limit ".$limit;
        // $q = $q."  limit ".$this->start.",".$this->display."";
        $res = $this->db->db_Query($q);
//        echo '<br/>'.$q.' <br/><br/>$res = '.$res.'<br/>';
        if (!$res OR !$this->db->result)
            return false;

        $rows = $this->db->db_GetNUmRows($res);
        if ($rows == 0)
            return false;

        $tree = array();
        $this->moduleId = array();
        $idArray = array();

        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc($res);

            //Список всіх ID-шок
            if (empty($idArray))
                $idArray = $row['id'];
            else
                $idArray .= ',' . $row['id'];

            if (empty($this->moduleId[$row['id_module']]))
                $this->moduleId[$row['id_module']] = $row['id_item'];
            else
                $this->moduleId[$row['id_module']] .= ',' . $row['id_item'];

            if($this->Logon->user_id==$row['id_user'])
                $row['edit']=true;
            else
                $row['edit']=false;

            $row['href']='';

            $row['dt']=$this->reparseDate($row['dt']);

            $tree[$row['id']] = $row;

        }

        /*$q = "SELECT
                `" . TblSysModComments . "`.id,
                `" . TblSysModComments . "`.id_module,
                `" . TblSysModComments . "`.id_item,
                `" . TblSysModComments . "`.id_user,
                `" . TblSysModComments . "`.dt,
                `" . TblSysModComments . "`.level,
                `" . TblSysModComments . "`.text,
                `" . TblModUser . "`.discount as img,
                `" . TblModUser . "`.name as first_name,
                `" . TblModUser . "`.country as second_name
            FROM
                `" . TblSysModComments . "`
            LEFT JOIN `" . TblModUser . "` ON
            (
                `" . TblSysModComments . "`.`id_user`=`" . TblModUser . "`.`sys_user_id`
            )
            WHERE
                `" . TblSysModComments . "`.status = '1'
            AND
                `" . TblSysModComments . "`.level IN ( " . $idArray . ")
            AND
                `" . TblSysModComments . "`.id NOT IN ( " . $idArray . ")
            ORDER BY
                `" . TblSysModComments . "`.dt desc";

        $res = $this->db->db_Query($q);
        //echo '<br/>'.$q.' <br/><br/>$res = '.$res.'<br/>';
        if (!$res OR !$this->db->result)
            return false;

        $rows = $this->db->db_GetNUmRows($res);
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc($res);

            if (empty($this->moduleId[$row['id_module']]))
                $this->moduleId[$row['id_module']] = $row['id_item'];
            else
                $this->moduleId[$row['id_module']] .= ',' . $row['id_item'];



            $tree[$row['id']] = $row;
        }*/

        krsort($tree);

        return $tree;
    }

    function getCommentById($id){
        $q = "SELECT
                `" . TblSysModComments . "`.id,
                `" . TblSysModComments . "`.id_user,
                `" . TblSysModComments . "`.dt,
                `" . TblSysModComments . "`.level,
                `" . TblSysModComments . "`.text,
                `" . TblSysModComments . "`.name
            FROM
                `" . TblSysModComments . "`
            WHERE
                `status`='1'
            AND
                `sys_modules_comments`.`id`='$id'
            ORDER BY
                `dt` desc";
        $res = $this->db->db_Query($q);
        if (!$res OR !$this->db->result)
            return false;
        //echo '$q ='.$q.' <br/>$res = '.$res.'<br/>';
        $rows = $this->db->db_GetNUmRows($res);
        if ($rows == 0) {
            return false;
        }
        $row = $this->db->db_FetchAssoc($res);
        if ( !empty($row['name']) )
            $row['show_name'] = $row['name'];
        else
            $row['show_name'] = 'Аноним';

        return $row;
    }

    function getComments()
    {
        $q = "SELECT
                `" . TblSysModComments . "`.id,
                `" . TblSysModComments . "`.id_user,
                `" . TblSysModComments . "`.dt,
                `" . TblSysModComments . "`.level,
                `" . TblSysModComments . "`.text,
                `" . TblSysModComments . "`.vote,
                `" . TblSysModComments . "`.name
            FROM `" . TblSysModComments . "`
            WHERE
                `id_module`=" . $this->module . "
            AND
                `id_item`=" . $this->id_item . "
            AND
                `status`='1'
            ORDER BY
                `dt` desc";

        $res = $this->db->db_Query($q);
        //echo '$q ='.$q.' <br/>$res = '.$res.'<br/>';
        if (!$res OR !$this->db->result)
            return false;

        $rows = $this->db->db_GetNUmRows($res);
        if ($rows == 0) {
            return false;
        }

        $tree = array();
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc($res);

            $row['dt'] = $this->reparseDate($row['dt']);
            $row['date_past'] = $this->timePast($row['dt']);
            if ( !empty($row['name']) )
                $row['show_name'] = $row['name'];
            else
                $row['show_name'] = 'Аноним';
            $tree[$row['level']][] = $row;
        }

        $arrShow = array();
        $this->makeTree($tree, 0, $arrShow);
        return $arrShow;
    }
    //------------------------------------------------------------------------------------------------------------------
    function timePast($datePublic)
    {
        $timestampPublic = strtotime($datePublic);
        $arr = Date::span($timestampPublic);
        $str = '';
        if ($arr['years'] != 0) {
            $str = $this->endings($arr['years'], 'years');
        } elseif ($arr['months'] != 0) {
            $str = $this->endings($arr['months'], 'months');
        } elseif ($arr['days'] != 0) {
            $str = $this->endings($arr['days'], 'days');
        } elseif ($arr['hours'] != 0) {
            $str = $this->endings($arr['hours'], 'hours');
        } elseif ($arr['minutes'] != 0) {
            $str = $this->endings($arr['minutes'], 'minutes');
        } else {
            $str = 'Меньше минуты';
        }
        return $str;
    }

    function endings($digit, $what)
    {
        $lastDigit = $this->getLastDigit($digit);
        if ($lastDigit == 1) {
            if ($what == 'years')
                $text = ' год';
            elseif ($what == 'months')
                $text = ' месяц';
            elseif ($what == 'days')
                $text = ' день';
            elseif ($what == 'hours')
                $text = ' час';
            elseif ($what == 'minutes')
                $text = ' минута';
        } elseif ($lastDigit > 1 AND $lastDigit < 5) {
            if ($what == 'years')
                $text = ' года';
            elseif ($what == 'months')
                $text = ' месяца'; elseif ($what == 'days')
                $text = ' деня'; elseif ($what == 'hours')
                $text = ' часа'; elseif ($what == 'minutes')
                $text = ' минуты';
        } elseif ($lastDigit > 4 AND $lastDigit < 21) {
            if ($what == 'years')
                $text = ' лет';
            elseif ($what == 'months')
                $text = ' месяцев'; elseif ($what == 'days')
                $text = ' дней'; elseif ($what == 'hours')
                $text = ' часов'; elseif ($what == 'minutes')
                $text = ' минут';
        }
        return $digit . $text;
    }

    function getLastDigit($digits)
    {
        if ($digits > 20) {
            $last = (string)$digits;
            return $last[count($last) - 1];
        } else {
            return $digits;
        }
    }

    // ------------------------------------------------------------------------------------------------

    function reparseDate($date, $justDate = false)
    {
        $dateArr = date_parse_from_format('Y-m-d G:i:s', $date);

        switch ($dateArr['month']){
            case 1: $dateArr['month']='января'; break;
            case 2: $dateArr['month']='февраля'; break;
            case 3: $dateArr['month']='марта'; break;
            case 4: $dateArr['month']='апреля'; break;
            case 5: $dateArr['month']='мая'; break;
            case 6: $dateArr['month']='июня'; break;
            case 7: $dateArr['month']='июля'; break;
            case 8: $dateArr['month']='августа'; break;
            case 9: $dateArr['month']='сентября'; break;
            case 10: $dateArr['month']='октября'; break;
            case 11: $dateArr['month']='ноября'; break;
            case 12: $dateArr['month']='декабря'; break;
        }
        //if ( strlen($dateArr['hour']) == 1 )
        //    $dateArr['hour'] = '0'.$dateArr['hour'];

        if ( strlen($dateArr['minute']) == 1 )
            $dateArr['minute'] = '0'.$dateArr['minute'];

        if ($justDate)
            return $dateArr['day'] . '.' . $dateArr['month'] . '.' . $dateArr['year'];
        else
            return $dateArr['day'] . ' ' . $dateArr['month'] . ' ' . $dateArr['year'] .' '. $dateArr['hour'] . ':' . $dateArr['minute'];
    }
    // ------------------------------------------------------------------------------------------------

    function makeTree(&$tree, $k_item = 0, &$arrShow, $level = 0)
    {
        if (empty($tree[$k_item])) return false;
        if($level>0) $tree[$k_item]=array_reverse($tree[$k_item]);
        $n = count($tree[$k_item]);
        for ($i = 0; $i < $n; $i++) {
            $row = $tree[$k_item][$i];
            $row['level_show'] = $level;
            $arrShow[] = $row;
            $this->makeTree($tree, $tree[$k_item][$i]['id'], $arrShow, $level + 1);
        }
    }

}

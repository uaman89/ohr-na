<?php
// ================================================================================================
// System : SEOCMS
// Module : comments.controller.class.php
// Version : 1.0.0
// Date : 20.08.2008
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
// ================================================================================================

// ================================================================================================
//    Class             : FrontComments
//    Version           : 1.0.0
//    Date              : 20.08.2008
//    Constructor       : Yes
//    Parms             :
//    Returns           : None
//    Description       : Class definition for describe input fields on front-end
// ================================================================================================
//    Programmer        :  Igor Trokhymchuk, Andriy Lykhodid
//    Date              :  20.08.2008
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================

class CommentsLayout extends Comments
{
    var $uselogon = NULL;
    var $login = NULL;
    var $password = NULL;
    var $script = NULL;
    var $task = NULL;
    var $Err = NULL;

    public $db = NULL;
    public $Spr = NULL;
    public $Form = NULL;
    public $Logon = NULL;

    // ================================================================================================
    //    Function          : FrontComments (Constructor)
    //    Version           : 1.0.0
    //    Date              : 20.08.2008
    //    Parms             : usre_id   / User ID
    //                        module    / module ID
    //    Returns           : Error Indicator
    //
    //    Description       : Opens and selects a dabase
    // ================================================================================================
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

        $this->uselogon = 1;

    } // End of FrontComments Constructor


    // ================================================================================================
    // Function : FacebookComments()
    // Date : 20.05.2011
    // Returns : true,false / Void
    // Description : show list of Facebook comments
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function FacebookComments($n = 7, $width = 686)
    {
        if (isset($_SERVER['REQUEST_URI']))
            $uri = 'http://' . NAME_SERVER . $_SERVER['REQUEST_URI'];
        else
            $uri = 'http://' . NAME_SERVER;
        echo View::factory('/modules/mod_comments/templates/tpl_facebook_comments.php')
            ->bind('uri', $uri);
    } //end of function FacebookComments()


    // ================================================================================================
    // Function : VkontakteComments()
    // Date : 20.06.2011
    // Returns : true,false / Void
    // Description : show list of Vkontakte comments
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function VkontakteComments()
    {
       echo View::factory('/modules/mod_comments/templates/tpl_vk_comments.php');
    } //end of function VkontakteComments()


    function showCommentsForm(){
        $commentsData=NULL;
        if(!empty($this->level)){
            $popup_title='Ответить на отзыв';
            //$commentsData=$this->getCommentById($this->level);
            $is_new_comment = false;
        }else {
            $popup_title='Оставить отзыв';
            $is_new_comment = true;
        }

        $user = check_init('User', 'User');
        $name = $user->GetUserNameByUserId($this->Logon->user_id);
        $email = $user->GetUserEmailByUserId($this->Logon->user_id);

        $commentForm=View::factory('/modules/mod_comments/templates/tpl_comments_form.php')
        ->bind('user_id',$this->Logon->user_id)
        ->bind('name',$name)
        ->bind('email',$email)
        ->bind('module',$this->module)
        ->bind('id_item',$this->id_item)
        ->bind('commentsData',$commentsData)
        ->bind('level',$this->level)
        ->bind('page',$this->page)
        ->bind('user_id',$this->Logon->user_id)
        ->bind('is_new_comment',$is_new_comment)
        ->bind('popup_title',$popup_title);

        die(json_encode(array('ok'=>'return_html','return_html'=>$commentForm->render())));
    }

    // ================================================================================================
    // Function : ShowCommentsByModuleAndItem()
    // Date : 01.06.2011
    // Returns : true,false / Void
    // Description : Show Comments By Module And Item
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function ShowComments($return=false)
    {
        $commentsByPages=$this->showCommentsTree();
        //$this->commentsCount = $this->getCommentsCountItem($this->module, $this->id_item); // got it already in showCommentsTree

        if($return){
            return View::factory('/modules/mod_comments/templates/tpl_comments_box_ajax.php')
                ->bind('commentsCount',$this->commentsCount)
                ->bind('module',$this->module)
                ->bind('id_item',$this->id_item)
                ->bind('commentsByPages',$commentsByPages);
        }else{
            echo View::factory('/modules/mod_comments/templates/tpl_comments_box.php')
                ->bind('module',$this->module)
                ->bind('id_item',$this->id_item)
                ->bind('commentsByPages',$commentsByPages)->render();
        }

    } //end of function ShowCommentsByModuleAndItem()

    function showCommentsTree(){
        $this->commentsCount = 0;

        $arr=$this->getComments();
        if(!$arr) return false;

        foreach ( $arr as &$val ){
            if ( $val['level'] == 0 ){
                $responses[] = $val;
                $this->commentsCount++; //count of comments without answers
            }
            else{
                $answers[ $val['level'] ][] = $val;
            }
        }

        if ( $this->commentsCount < ($this->start + $this->display) )
            $end = $this->commentsCount;
        else
            $end = $this->start + $this->display;

        $pagination = $this->Form->WriteLinkPagesStatic('/comments/', $this->commentsCount, $this->display, $this->start, NULL, $this->page);

        /*last comments start*/
        $limit = 2;

        if ( $limit > $this->commentsCount )
            $limit = $this->commentsCount;
        $c = 0;
        $last = array();
        foreach ($arr as $comment){
            //var_dump($comment);
            if ( $comment['level'] == 0 ) {
                $last[] = $comment;
                $c++;
            }
            if ( $c == $limit ) break;

        }
        //var_dump($last);

        $this->last_comments = View::factory('/modules/mod_comments/templates/tpl_comments_last_comments.php')
            ->bind('limit', $limit)
            ->bind('arr', $last);
        /* last comments end */

        $commentsByPages=View::factory('/modules/mod_comments/templates/tpl_comments_by_pages.php')
            ->bind('display',$this->display)
            ->bind('page',$this->page)
            ->bind('start',$this->start)
            ->bind('end',$end)
            ->bind('pagination',$pagination)
            ->bind('responses',$responses)
            ->bind('answers',$answers);
        return $commentsByPages;
    }

    function showCommentsTreeAjax(){
        //$commentsByPages=$this->showCommentsTree();
        $commentsByPages = $this->ShowComments(true);

        $params = array( 'div_id'=>'commentsBlock','ok_cont'=>$commentsByPages->render() );
        if ( isset($this->add_success) )
            $params['ok'] = 'add_success';
        else
            $params['ok'] = 'msg_div';
        die(json_encode($params));
    }

}//end of class FrontComments
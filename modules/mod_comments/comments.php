<?php
/**
* add_comment.php
* script for all actions with system comments on front-end
* @package News Package of SEOCMS
* @author Yaroslav Gyryn  <yaroslav@seotm.com>
* @version 1.1, 29.07.2011
* @copyright (c) 2010+ by SEOTM
*/
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/include/defines.php' );

//========= FIRST DEFINE PAGE LANGUAGE  BEGIN ===========
$Page = check_init('PageUser', 'PageUser');
//========= FIRST DEFINE PAGE LANGUAGE BEGIN  ===========

if (!isset($_REQUEST['module'])) $module = NULL;
else{
    $FrontForm = new FrontForm();
    $module = $FrontForm->GetRequestNumData($_REQUEST['module']);
}

$Obj = new CommentsLayout(NULL, $module);

if (!isset($_REQUEST['task'])) $task = NULL;
else $task = $Obj->Form->GetRequestTxtData($_REQUEST['task'], 1);

if (!isset($_REQUEST['id_item'])) $id_item = NULL;
else $id_item = $Obj->Form->GetRequestNumData($_REQUEST['id_item']);

if (!isset($_REQUEST['idUser'])) $idUser = NULL;
else $idUser = $Obj->Form->GetRequestNumData($_REQUEST['idUser']);

if (!isset($_REQUEST['level'])) $level = 0;
else $level = $Obj->Form->GetRequestNumData($_REQUEST['level']);

if (!isset($_REQUEST['vote'])) $vote = null;
else $vote = $_REQUEST['vote'];

if (!isset($_REQUEST['idComment'])) $idComment = Null;
else $idComment = $Obj->Form->GetRequestNumData($_REQUEST['idComment']);

if (!isset($_REQUEST['login'])) $login = NULL;
else $login = $Obj->Form->GetRequestTxtData($_REQUEST['login'], 1);

if (!isset($_REQUEST['password'])) $password = NULL;
else $password = $_REQUEST['password'];

if (!isset($_REQUEST['name'])) $name = NULL;
else $name = $Obj->Form->GetRequestTxtData($_REQUEST['name'], 1);

if (!isset($_REQUEST['email'])) $email = NULL;
else $email = $Obj->Form->GetRequestTxtData($_REQUEST['email'], 1);

if (!isset($_REQUEST['text'])) $text = NULL;
else $text = $Obj->Form->GetRequestTxtData($_REQUEST['text'], 1);


if (isset($_REQUEST['referer_page'])) $referer_page = $_REQUEST['referer_page'];
else {
    if(strstr($_SERVER['REQUEST_URI'], 'referer_page=')){
         $start = strpos($_SERVER['REQUEST_URI'], 'referer_page=');
         $referer_page = substr($_SERVER['REQUEST_URI'], ($start+strlen('referer_page=')) );
    }
    elseif( isset($_SERVER["HTTP_REFERER"])) $referer_page = $_SERVER["HTTP_REFERER"];
    else $referer_page = NULL;
}
$referer_page = str_replace('AND', '&', $referer_page);


$Obj = new CommentsLayout(NULL, $module);

$Obj->display = 10;
$Obj->page = Arr::get($_REQUEST,'page',1);
$Obj->start = ($Obj->page-1)*$Obj->display;

$Obj->task = addslashes(strip_tags(trim($task)));
$Obj->module = intval($module);
$Obj->id_item = intval($id_item);
$Obj->idComment = intval($idComment);
$Obj->idUser = intval($idUser);
$Obj->vote = $vote;
$Obj->level = intval($level);
$Obj->login = addslashes(strip_tags(trim($login)));
$Obj->password = addslashes(strip_tags(trim($password)));
$Obj->name = addslashes(strip_tags(trim($name)));
$Obj->email = addslashes(strip_tags(trim($email)));
$Obj->text = addslashes(strip_tags(trim($text)));
$Obj->referer_page = $referer_page;

$Obj->script = '/comments.php?id_module='.$Obj->module.'&id_item='.$Obj->id_item;
//echo '<br>$Obj->task='.$Obj->task.' $Obj->script='.$Obj->script;

switch($Obj->task){
    case 'getCommentsForm':
        $Obj->showCommentsForm();
        break;
    case 'del_comments':

       $res = $Obj->deleteComments();
       if($res== 1) {
           $Obj->Form->ShowTextMessages('Коментар успішно видалено.');
       }
       elseif($res ==-1) {
            $Obj->Err = 'Коментар не можна видалити, так як на нього існують відповіді.';
           ?><div id="err" class="err"><?
            $Obj->Form->showErr($Obj->Err);
            ?></div><?
       }
       elseif($res == 0) {
            $Obj->Err = 'Помилка видалення коментаря.';
           ?><div id="err" class="err"><?
            $Obj->Form->showErr($Obj->Err);
            ?></div><?
       }
        break;

    case 'add_comment':
        $Obj->checkFields();
        if ( $Obj->addComment() );
            $Obj->add_success = true;
        $Obj->showCommentsTreeAjax();

        break;
    case 'show_comments':
        $Obj->showCommentsTreeAjax();
        break;
    case 'show_user_comments':
        break;

    case 'vote':
        /*echo '<br/>User = '.$idUser;
        echo '<br/>vote = '.$vote;
        echo '<br/>idComment = '.$idComment;*/
        if($Obj->idUser != null) {
            $rating = $Obj->addVote();
            $class='gray';
            if($rating>0)
                $class='blue';
            elseif($rating<0)
                $class='red';
            ?><div class="rating left <?=$class?>"><?=$rating;?></div>
            <img src="/images/design/icoVote.png">
            <?
        }
        else {
            echo 'Користувач незареєстрований';
        }
        break;
}
?>


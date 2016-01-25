<?php
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/include/defines.php' );

//========= FIRST DEFINE PAGE LANGUAGE  BEGIN ===========
$Page = new PageUser();
//========= FIRST DEFINE PAGE LANGUAGE BEGIN  ===========

if (!isset($task)) $task = 'all';
if (!isset($item)) $item = NULL;

$Price = new Price();

$title = '�����-���� | '.META_TITLE;
$Description = '�����-����. '.META_DESCRIPTION;
$Keywords = '�����-����, '.META_KEYWORDS;


$Page->SetTitle( $title );
$Page->SetDescription( $Description );
$Page->SetKeywords( $Keywords );

ob_start();

$goto = $Price->GetPathPrice();
  //$goto='/modules/mod_user/user.php';
  //require(SITE_PATH.$goto);

 ?><script>window.location.href="<?=$goto?>";</script><?;
$Page->content = ob_get_clean();
$Page->out();
?>


<?php
include_once( $_SERVER['DOCUMENT_ROOT'].'/include/defines.php' );
include_once( SITE_PATH.'/modules/mod_search/search.defines.php' );

//========= FIRST DEFINE PAGE LANGUAGE  BEGIN ===========
$Page = check_init('PageUser', 'PageUser');
//========= FIRST DEFINE PAGE LANGUAGE BEGIN  ===========

if(defined("MOD_NEWS") AND MOD_NEWS AND empty($Page->News) )
    $Page->News = check_init('NewsLayout', 'NewsLayout');

if(defined("MOD_PAGES") AND MOD_PAGES AND empty($Page->FrontendPages) )
    $Page->FrontendPages = check_init('FrontendPages', 'FrontendPages');

if(defined("MOD_CATALOG") AND MOD_CATALOG AND empty($Page->Catalog) )
    $Page->Catalog = check_init('CatalogLayout', 'CatalogLayout');

if(defined("MOD_ARTICLE") AND MOD_ARTICLE  AND empty($Page->Article) )
    $Page->Article = check_init('ArticleLayout', 'ArticleLayout');

if (defined("MOD_PUBLIC") AND MOD_PUBLIC AND empty($Page->Public))
$Page->Public = check_init('PublicLayout', 'PublicLayout');

if (defined("MOD_GALLERY") AND MOD_GALLERY AND empty($Page->Gallery))
$Page->Gallery = check_init('GalleryLayout', 'GalleryLayout');

$Search = check_init('Search', 'Search');

if( !isset ( $_REQUEST['task'] ) ) $task = NULL;
else $task = $_REQUEST['task'];

if( !isset ( $_REQUEST['query'] ) ) $query = '';
else {
    $query = addslashes(substr(strip_tags(trim($_REQUEST['query'])), 0,64));
    // cut unnormal symbols
    $query=preg_replace("/[^\w\x7F-\xFF\s\-]/", " ", $query);
    // delete double spacebars
    $query=str_replace(" +", " ", $query);
}

if( !isset ( $_REQUEST['modname'] ) ) $modname = 'all';
else $modname = $_REQUEST['modname'];


if($task==Null){
    $Title = $Page->multi['TXT_FRONT_MOD_SEARCH_ON_SITE'];
}
else{
    $Title = $Page->multi['TXT_FRONT_MOD_SEARCH_RESULT'];
}

$Description = $Page->multi['TXT_FRONT_MOD_SEARCH_ON_SITE'];
$Keywords = $Page->multi['TXT_FRONT_MOD_SEARCH_ON_SITE'];


$Page->SetTitle( $Title );
$Page->SetDescription( $Description );
$Page->SetKeywords( $Keywords );

$Page->searchKeywords = $query;
$Page->h1 = $Page->multi['TXT_FRONT_MOD_SEARCH_RESULT'].' "'.$query.'"';
$Search->Page = $Page;
ob_start();
//echo '<br>$task='.$task.' $query='.$query.' $modname='.$modname;
$flag = TRUE;
if($task=='search' and strlen($query)>=3){
    $flag = FALSE;
    $Search->ip = $_SERVER['REMOTE_ADDR'];
    $Search->query = $query;
    if($modname!='all') $flagRes = $Search->searchSwitch($modname);
    else{
        $flagRes = 0;
        if (defined("MOD_PAGES") AND MOD_PAGES){
            $rows = $Search->searchSwitch('pages');
            $flagRes = $flagRes + $rows;
        }
        if (defined("MOD_NEWS") AND MOD_NEWS){
            $rows = $Search->searchSwitch('news');
            $flagRes = $flagRes + $rows;
        }
        if (defined("MOD_PUBLIC") AND MOD_PUBLIC){
            $rows = $Search->searchSwitch('public');
            $flagRes = $flagRes + $rows;
        }
        if (defined("MOD_ARTICLE") AND MOD_ARTICLE){
            $rows = $Search->searchSwitch('articles');
            $flagRes = $flagRes + $rows;
        }
        if (defined("MOD_CATALOG") AND MOD_CATALOG){
            $rows = $Search->searchSwitch('catalog');
            $flagRes = $flagRes + $rows;
        }
        if (defined("MOD_GALLERY") AND MOD_GALLERY){
            $rows = $Search->searchSwitch('gallery');
            $flagRes = $flagRes + $rows;
        }
    }
    if(!$flagRes){
        $Page->FrontendPages->Form->ShowTextMessages($Page->multi['SEARCH_NO_RES']);
    }
    /*?>
    <p>
      <a href="javascript:history.back()"><u>← <?=$Page->multi['TXT_FRONT_GO_BACK'];?></u></a>
    </p>
    <?*/
    $Search->result = $flagRes;
    $Search->save_search();
}
$Page->content = ob_get_clean();
if(!is_ajax){
$Page->content =$Page->content;
}
$Page->out();
?>
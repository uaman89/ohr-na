<?php
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] ); 
include_once( SITE_PATH.'/include/defines.php' );
 
 $Page = new PageUser();
 $Catalog = new CatalogLayout();

//for .htaccess
if( isset($_REQUEST['str_cat'])){
  $categ_parent = NULL;
  $categ = NULL;
  foreach($_REQUEST['str_cat'] as $key=>$value){
    $categ_parent = $categ;
    $categ = $Catalog->GetIdCategByTranslit($key, $categ_parent);
  }
}
else {
    if( !isset($_REQUEST['categ']) ) $categ=NULL;
    else $categ = $_REQUEST['categ'];
}
//for .htaccess
if( isset($_REQUEST['str_id'])){
  $curcod = $Catalog->GetIdPropByTranslit($_REQUEST['str_id'], $categ);
}
else {  
if( !isset($_REQUEST['curcod']) ) $curcod=NULL;
else $curcod = $_REQUEST['curcod'];
}

if( !isset($_REQUEST['task']) ) $task=NULL;
else $task = $_REQUEST['task'];   
  
if( !isset($_REQUEST['file']) ) $file=NULL;
else $file = $_REQUEST['file']; 
  
if ( !empty($val_categ)  ) {
  $val_categ='$'.$val_categ.';';
  eval($val_categ);
}
 
$Catalog->id_cat = $categ;
$Catalog->id = $curcod;
$Catalog->task = $task;
$Catalog->id_file = $file;
$Catalog->lang_id = _LANG_ID;
switch ($Catalog->task){
    case 'show_files':
            set_time_limit (0);
             
            $tmp = $Catalog->GetFileData($Catalog->id_file);
            $link = SITE_PATH.Catalog_Upload_Files_Path.'/'.$Catalog->id.'/'.$tmp['path'];
            $workDir = SITE_PATH.Catalog_Upload_Files_Path.'/'.$Catalog->id.'/';
             
            if (isset($_SERVER))
            {
                $server = &$_SERVER;
            }
            else
            {
                $server = &$GLOBALS["HTTP_SERVER_VARS"];
            }
            if ( isset($Catalog->id_file) && $Catalog->id_file!="" && file_exists($link) )
            {
                $workFileName = $tmp['path'];
                $fd = fopen ($link, "rb");
                $workFileSize = filesize ($link);
                if (isset($server["HTTP_RANGE"]))
                {
                    preg_match ("/bytes=(\d+)-/", $server["HTTP_RANGE"], $m);
                    $contentSize = $workFileSize - intval($m[1]);
                    $p1 = $workFileSize-$contentSize;
                    $p2 = $workFileSize-1;
                    $p3 = $workFileSize;
                    fseek ($fd, $p1);
                     
                    header ("HTTP/1.1 206 Partial Content");
                    header ("Date: " . getGMTDateTime ());
                    header ("X-Powered-By: PHP/" . phpversion());
                    header ("X-Script: No Direct Links! v0.1 hamshen@mail.ru");
                    header ("Expires: Thu, 19 Nov 1981 08:52:00 GMT");
                    header ("Cache-Control: None");
                    header ("Pragma: no-cache");
                    header ("Accept-Ranges: bytes");
                    header ("Content-Disposition: inline; filename=\"" . $workFileName . "\"");
                    header ("Content-Range: bytes " . $p1 . "-" . $p2 . "/" . $p3);
                    header ("Content-Length: " . $contentSize);
                    header ("Content-Type: application/octet-stream");
                    header ("Proxy-Connection: close");
                    header ("");
                }
                else
                {
                    $contentSize = $workFileSize;
                    header ("HTTP/1.1 200 OK");
                    header ("Date: " . getGMTDateTime ());
                    header ("X-Powered-By: PHP/" . phpversion());
                    header ("X-Script: No Direct Links! v0.1 info@seotm.com");
                    header ("Expires: Thu, 23 July 2009 08:52:00 GMT");
                    header ("Cache-Control: None");
                    header ("Pragma: no-cache");
                    header ("Accept-Ranges: bytes");
                    header ("Content-Disposition: inline; filename=\"" . $workFileName . "\"");
                    header ("Content-Length: " . $contentSize);
                    header ("Age: 0");
                    header ("Content-Type: application/octet-stream");
                    header ("Proxy-Connection: close");
                    header ("");
                    
                }
                $contents = fread ($fd, $contentSize);
                echo $contents;
                fclose ($fd);
                exit();
            }
            else
            {
                header ("HTTP/1.1 404 Object Not Found");
            }             
}

function getGMTDateTime ()
{
    $offset = date("O");
    $roffset = "";
    if ($offset[0] == "+")
    {
        $roffset = "-";
    }
    else
    {
        $roffset = "+";
    }
    $roffset .= $offset[1].$offset[2];
    return (date ("D, j M Y H:i:s", strtotime ($roffset . " hours ")) . " GMT");
}
?>

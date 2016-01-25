<?
// ================================================================================================
//    System     : SEOCMS
//    Module     : Catalog Download reports
//    Version    : 1.0.0
//    Date       : 08.07.2009
//    Licensed To:
//                 Igor  Trokhymchuk  ihoru@mail.ru
//                 Andriy Lykhodid    las_zt@mail.ru
// ================================================================================================
include_once( $_SERVER['DOCUMENT_ROOT'].'/admin/include/defines.inc.php' );

if(!defined("_LANG_ID")){
    $pg = new PageAdmin();
}

if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];

//Blocking to execute a script from outside (not Admin-part) 
if ( !$pg->logon->isAccessToScript($module)) exit;
 
if( !isset($_REQUEST['path']) ) $path=NULL;
else $path = $_REQUEST['path'];
 
if( !isset($_REQUEST['task']) ) $task='export_to_xml';
else $task = $_REQUEST['task'];   

switch ($task){
    case 'export_to_xml': case 'export_price': case 'export_to_csv': case 'export_to_txt':
            set_time_limit (0);
             
            $link = SITE_PATH.$path;
            //echo '<br>$link='.$link;
            if (isset($_SERVER))
            {
                $server = &$_SERVER;
            }
            else
            {
                $server = &$GLOBALS["HTTP_SERVER_VARS"];
            }
            if ( file_exists($link) )
            {
                $workFileName = substr($path, strrpos($path, '/')+1);
                //echo '<br>$workFileName='.$workFileName;
                //$workFileName = '111.xml';
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
                    header ("Expires: Thu, 08 July 2009 08:52:00 GMT");
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

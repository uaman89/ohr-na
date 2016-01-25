<?php
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] ); 
include_once( SITE_PATH.'/include/defines.php' );
  
  $Page = new PageUser();
  //phpinfo();

  $Catalog = new CatalogLayout();
  //for .htaccess
  if( isset($_REQUEST['str_cat'])){
      $categ_parent = NULL;
      $categ = NULL;
      foreach($_REQUEST['str_cat'] as $key=>$value){
        //echo '<br>$key='.$key;
        $categ_parent = $categ;
        $categ = $Catalog->GetIdCategByTranslit($key, $categ_parent);
        //echo '<br>$categ='.$categ;
      }
  }
  else {
    if( !isset($_REQUEST['categ']) ) $categ=NULL;
    else $categ = $_REQUEST['categ'];
  }
  //for .htaccess
  if( isset($_REQUEST['str_id'])){
      $curcod = $Catalog->GetIdPropByTranslit($_REQUEST['str_id'], $categ);
      //echo '<br>$curcod='.$curcod;
  }
  else {  
    if( !isset($_REQUEST['curcod']) ) $curcod=NULL;
    else $curcod = $_REQUEST['curcod'];
  }

  if( !isset($_REQUEST['start']) ) $start=NULL;
  else $start = $_REQUEST['start'];  
  
  if( !isset($_REQUEST['task']) ) $task='show_responses';
  else $task = $_REQUEST['task'];   
  
  if( !isset($_REQUEST['val']) ) $val=NULL;
  else $val = $_REQUEST['val']; 
  
  if( !isset($_REQUEST['name']) ) $name=NULL;
  else $name = $_REQUEST['name'];     
  
  if( !isset($_REQUEST['email']) ) $email=NULL;
  else $email = $_REQUEST['email'];
  
  if( !isset($_REQUEST['response']) ) $response=NULL;
  else $response = $_REQUEST['response'];

  if( !isset($_REQUEST['rating']) ) $rating=NULL;
  else $rating = $_REQUEST['rating'];  
  
  if ( !empty($val)  ) {
      $val='$'.$val.';';
      eval($val);
  }
  //echo '<br> $curcod='.$curcod;
  
//=========== ANTISPAM =================
if ( !isset( $_REQUEST['my_gen_v'] ) ) $my_gen_v = NULL;
else $my_gen_v = $_REQUEST['my_gen_v'];

if ( !isset( $_REQUEST['usr_v'] ) ) $usr_v = NULL;
else $usr_v = $_REQUEST['usr_v'];
//======================================  
  
  $Catalog->id_cat = $categ;
  $Catalog->start = $start;
  $Catalog->id = $curcod;
  $Catalog->task = $task;
  $Catalog->SetMetaData();
  
  $CatalogResponse = new CatalogResponse();
  $CatalogResponse->id_prop=$curcod;
  $CatalogResponse->name=addslashes($name); 
  $CatalogResponse->email=addslashes($email);
  $CatalogResponse->response=addslashes($response); 
  $CatalogResponse->rating=$rating;
  $CatalogResponse->dt = date('Y-m-d');
  $CatalogResponse->status = 3; 

  $Page->SetTitle( '������ � �����������, �������, �������� ����� | '.$Catalog->title.' | '.META_TITLE );
  $Page->SetDescription( '������ � �����������, �������, �������� �����, '.$Catalog->description.', '.META_DESCRIPTION );
  $Page->SetKeywords( '������ , �����������, �������, ������, ����������, ������������, '.$Catalog->keywords.', '.META_KEYWORDS );  
  
  //$Page->WriteHeader($Catalog);
 
  //phpinfo();
  //echo '<br> $task='.$task;
  //$Catalog->script=$_SERVER['PHP_SELF'];
    
  switch ($Catalog->task){
    case 'show_responses':
        $Catalog->ShowResponses();
        break;                                
    case 'make_response':
        $Catalog->ShowResponseForm();
        break; 
    case 'save_response':
        $CatalogResponse->SaveResponse(NULL);             
        // for folders links 
        if( $Catalog->mod_rewrite==1 ) $link = $Catalog->Link($Catalog->id_cat, $Catalog->id);
        else $link = "/catalog_".$Catalog->id_cat."_".$Catalog->id."_".$Catalog->lang_id.".html";
        echo "<script>window.location.href='$link';</script>";
        //$Catalog->ShowResponses();
        break;
  }           

 /*========= Save Catalog Statistic START ====================*/
 $Catalog->SetStat();
 /*========= Save Catalog Statistic END ======================*/
    
 //$Page->WriteFooter();

?>

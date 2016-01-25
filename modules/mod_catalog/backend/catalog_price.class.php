<?php
include_once( SITE_PATH.'/modules/mod_catalog/catalog.defines.php' );

class Price
{
		var $user_id = NULL;
       	var $module = NULL;
       	var $Err=NULL;

       	var $sort = NULL;
       	var $display = 20;
       	var $start = 0;
       	var $fln = NULL;
       	var $width = 500;
       	var $srch = NULL;
       	var $fltr = NULL;
       	var $fltr2 = NULL;
       	var $script = NULL;
		var $db = NULL;
        	var $Msg = NULL;
        	var $Right = NULL;
        	var $Form = NULL;
        	var $Spr = NULL;
		var $file_n = NULL;
		
		
		
// ================================================================================================
//    Function          : Price (Constructor)
//    Version           : 1.0.0
//    Date              : 21.03.2006
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
function Price ($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL, $fltr=NULL) {
                //Check if Constants are overrulled
                ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
                ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
                ( $display  !="" ? $this->display = $display  : $this->display = 20   );
                ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
                ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
                ( $width    !="" ? $this->width   = $width    : $this->width   = 750  );
		
                
		    if (empty($this->db)) $this->db = new DB();
            if (empty($this->Msg)) $this->Msg = new ShowMsg();
            $this->Msg->SetShowTable(TblModOrderSprTxt);
            if (empty($this->Form)) $this->Form = new Form();
		    if (empty($this->Right)) $this->Right = new  Rights($this->user_id, $this->module);
		    if (empty($this->Spr)) $this->Spr = new  SysSpr($this->user_id, $this->module);
} // end of constructor



function show()
{
	$logon = new UserAuthorize();
	$q = "SELECT * FROM `".TblModOrder."` where 1";
	  
	/* Write Table Part */
        AdminHTML::TablePartH();
		
	//echo $_SERVER['SERVER_NAME']."/modules/mod_catalog/";
	$q = "select * from `".TblModCatalogFileManager."` where 1";
	$res = mysql_query( $q );
	  $rows = mysql_num_rows($res);
		
	for($i=0;$i<$rows;$i++)
	{
	$row = mysql_fetch_assoc($res);
	if($row['path'] =='')
		{
			$path ="Файл отсутствует";
			$alias = "";
		}
		else
		{
		$path = "<a href=http://".$_SERVER['SERVER_NAME']."/price/".$row['path'].">".$_SERVER['SERVER_NAME']."/price/".$row['path']."</a>";
		$alias = "Удалить";
		}
	//echo $_SERVER['PHP_SELF'].'?module='.$this->module.'&task=save';
	
	?>
	<table border="0" cellpadding="0" cellspacing="0">
	<tr><td valign="top" style="border-bottom:1px solid #616161; " width="200"><img src="/images/icons/info.jpg" width="32" height="32" border="0"><td style="font-size:10px; color:#515151; vertical-align:top; border-bottom:1px solid #616161;" align="left">Желательно удалять предыдущую версию прайс листа перед загрузкой нового во избежание засорения свободного дискового пространства на сервере. В базе данных хранится путь к последнему загруженому файлу.
    <tr><td align="center"><?=$path?><br><a href=<?=$_SERVER['PHP_SELF'].'?module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&task=del';?>><?=$alias;?></a>
	<form action="<?=$_SERVER['PHP_SELF'].'?module='.$this->module.'&task=save'?>" method="POST" enctype="multipart/form-data">
	<td><INPUT TYPE="file" NAME="filename"  size="40"><br>
	<tr><td colspan="2" align="center"><input type="submit" name="submit" value="Копировать">
	</form>
	</table>
	<?
	AdminHTML::TablePartF();
	// $this->Form->WriteFooter();
	
	}
} //end of function show




function save()
{
	//echo ",sdhjdsjhfgdhfjgs";
	$uploaddir = $_SERVER['DOCUMENT_ROOT'].'/price/';
	if ( !file_exists($uploaddir) ) mkdir($uploaddir,0777);
	else chmod($uploaddir,0777);
	
	$my_file = $_FILES['filename']['name'];
	//echo $_FILES['filename']['name'];
	$uploaddir1 = $uploaddir.$_FILES['filename']['name'];
	$filename = $_FILES['filename']['tmp_name'];
	if ( !copy($filename,$uploaddir1) ){
        echo "<br><h1>Ошибка загрузки файла на сервер!!!</h1><br>";
        chmod($uploaddir,0755);
        return false;
    }
	else echo "<h3>Загрузка файла прошла успешно!</h3>";
	chmod($uploaddir,0755);
    
	$q = "update `".TblModCatalogFileManager."` set `path`='".$my_file."' where `id`=1";
	//echo $q;
	$res = $this->Right->Query( $q, $this->user_id, $this->module );
	if(!$res){echo "Ошибка внесения пути файла в базу данных!!!"; return false;}
	else 
	echo "Путь к файлу был успешно внесён в базу данных.";
	return true;
	$this->show();
	
} //end of function save

function del()
{
	$query = "select * from `".TblModCatalogFileManager."` where 1";
	$res1 = $this->Right->Query( $query, $this->user_id, $this->module );
	if(!$res1){echo "Путь к файлу не удалось получить."; return false;}
	while($fil = $this->Right->db_FetchAssoc($res1))
	{
	$file_path=$fil['path'];
	}
	$q = "update `".TblModCatalogFileManager."` set `path`=NULL where `id`=1";
	$res = $this->Right->Query( $q, $this->user_id, $this->module );
	//echo $q;
	$path = $_SERVER['DOCUMENT_ROOT'].'/price/'.$file_path;
	//echo $path;
	if($res)
	{
		if ( file_exists($path) ) 
		{
             $ress = unlink ($path);
             if( !$ress ) {echo "Не удалось удалить файл."; return false;}
		 }
	}
	$this->show();
} //end of function del


}//end of class Price
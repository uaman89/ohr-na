<?php
// ================================================================================================
//    System     : SEOCMS
//    Module     : Pages
//    Date       : 04.02.2005
//    Licensed To:
//                 Ihor Trokhymchuk     ihoru@mail.ru
//    Purpose    : Class definition for dynamic pages
// ================================================================================================

include_once( SITE_PATH.'/modules/mod_pages/pages.defines.php' );

// ================================================================================================
//    Class                      : PagesBackend
//    Date                       : 02.12.2008
//    Constructor                : Yes
//    Returns                    : None
//    Description                : Dynamic Pages Module
//    Programmer                 :  Ihor Trokhymchuk
// ================================================================================================
/**
 * Class PagesBackend
 * Class definition for control dynamic pages
 * @package System Package of SEOCMS
 * @author Igor Trokhymchuk  <ihor@seotm.com>
 * @version 1.1, 02.04.2012
 * @copyright (c) 2005+ by SEOTM
 */
class ClearPages
{
    public  $user_id;
    public  $module;
    public  $lang_id;

    public  $Right;
    public  $Form;
    public  $Msg;
    public  $Spr;

    public  $name;

    /**
     * Rights::__construct()
     *
     * @param integer $user_id
     * @param integer $module_id
     * @return void
     */
    function __construct( $user_id=NULL, $module=NULL )
    {
        $this->user_id = $user_id;
        $this->module = $module;

        if(defined("_LANG_ID")) $this->lang_id = _LANG_ID;
        $this->width = '750';

        $this->db =  DBs::getInstance();
        $this->Right =  new Rights($this->user_id, $this->module);
        $this->Form = check_init('FormPages', 'Form', "'mod_pages'");        /* create Form object as a property of this class */
        $this->ln_sys = check_init('SysLang', 'SysLang');
        $this->ln_arr = $this->ln_sys->LangArray( $this->lang_id );
        $this->Spr = check_init('SysSpr', 'SysSpr'); /* create SysSpr object as a property of this class */

        $this->multi = check_init_txt('TblBackMulti',TblBackMulti);

        $this->creatTable();

    } //end of constructor PagesBackend

    function creatTable(){
        if( defined("DB_TABLE_CHARSET")) $this->tbl_charset = DB_TABLE_CHARSET;
        else $this->tbl_charset = 'utf8';

        // create table for strore individual name of category
        $q = "
            CREATE TABLE IF NOT EXISTS `".TblModClear."` (
              `date` datetime default NULL,
              `module` varchar(255) default NULL,
              `description` text NOT NULL,
              `cnt_replace` int(10) unsigned NOT NULL default '0'
            ) ENGINE=MyISAM DEFAULT CHARSET=".$this->tbl_charset.";
            ";
        $res = $this->db->db_Query( $q );
        //echo '<br>$q='.$q.' $res='.$res;
        if( !$res )return false;
    }

    // ================================================================================================
    // Function : show()
    // Date : 28.05.2013
    // Description : Show Clear form
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function show()
    {
        ?>
        <form action="index.php?module=<?=$this->module;?>" method="post" name="form-clear" id="form-clear" enctype='multipart/form-data'>
            <table border="0" cellpadding="5" cellspacing="0" width="100%">
                <tr>
                    <td style="width: 150px">Домен для чистки:</td>
                    <td><input type="text" value="" name="name" /><br/>Например: <?=str_replace('www.','',NAME_SERVER)?> (БЕЗ www!!!)</td>
                </tr><?/*?>
                <tr>
                    <td>Модуль:</td>
                    <td>
                        <select name="task" id="task">
                            <option selected="">Вибирете модуль</option>
                            <option value="clear_pages">Динамические страници</option>
                        </select>
                    </td>
                </tr><?*/?>
                <tr>
                    <td colspan="2">
                        <input type="hidden" name="send" value="1" />
                        <input type="submit" value="Поехали!!!" name="submit" />
                    </td>
                </tr>
            </table>
        </form>
        <?
    }// end of function show


    // ================================================================================================
    // Function : clearTableOne()
    // Version : 1.0.0
    // Date : 3.06.2013
    // Returns : true,false / Void
    // Description : Clear content of one table
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function clearTableOne($TableClear = NULL,$nameModule = '',$description = '',$clearPole = 'content',$namePole = 'name'){
        if(empty($TableClear)) return false;
        $q="SELECT `".$TableClear."`.`cod`,
         `".$TableClear."`.`lang_id`,
         `".$TableClear."`.`".$clearPole."` as `content`,
         `".$TableClear."`.`".$namePole."` as `name`
        FROM `".$TableClear."`
        WHERE 1";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
//        echo '<br>$q='.$q.' $res='.$res;
        if( !$res OR !$this->Right->result ) return false;
        $rows = $this->Right->db_GetNumRows();
        for($i=0;$i<$rows;$i++){
            $row = $this->Right->db_FetchAssoc();
            $arrUpdate[$i] = $row;
        }

        //echo '$this->name='.$this->name;
        //var_dump($arrUpdate);return false;
        $domen = 'http://'.$this->name.'/';
        $domen_www = 'http://www.'.$this->name.'/';
        $cnt_all = 0;
        for($i=0;$i<$rows;$i++){
            $row = $arrUpdate[$i];
            $content = $row['content'];
            if(strpos($content,$domen)!==false || strpos($content,$domen_www)!==false){
                $content_real = str_replace($domen_www,'/',$content,$count_no_www);
                $content_real = str_replace($domen,'/',$content_real,$count_www);
                $content_real = addslashes($content_real);
                $count = $count_www + $count_no_www;
                $name = $row['name'];
                $cod = $row['cod'];
                $lang_id = $row['lang_id'];
                $q="UPDATE `".$TableClear."` SET
                `".$TableClear."`.`".$clearPole."`='".$content_real."'
                WHERE `".$TableClear."`.`cod`='".$cod."'
                and `".$TableClear."`.`lang_id` = '".$lang_id."' ";
                $res = $this->Right->Query( $q, $this->user_id, $this->module );
                //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
                if( !$res OR !$this->Right->result ) return false;
                echo '<br>Изменено: Имя:'.$name.' cod='.$cod.' $lang_id='.$lang_id;
                $this->insertClearTable($nameModule.' '.$description,'Название страници:'.$name.' cod='.$cod.' id_user='.$this->user_id,$count);
                $cnt_all = $cnt_all + $count;
            }
        }
        $this->insertClearTable($nameModule,'Замена по '.$description,$cnt_all);
        ?><div style="color: green;font-size: 16px"><?=$nameModule.' '.$description?> почищен!</div><?
        return true;
    }

    // ================================================================================================
    // Function : clearTableOne()
    // Version : 1.0.0
    // Date : 3.06.2013
    // Returns : true,false / Void
    // Description : Clear content of table ang get name from ather table
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function clearTableAndTableName($TableClear = NULL,$TableName = NULL,$nameModule = '',$description = ''){
        if(empty($TableClear) || empty($TableName)) return false;
        $q="SELECT `".$TableClear."`.`cod`,
         `".$TableClear."`.`lang_id`,
         `".$TableClear."`.`name` as `content`,
         `".$TableName."`.`name`
        FROM `".$TableClear."`
        LEFT JOIN `".$TableName."`
        ON (`".$TableName."`.`cod` = `".$TableClear."`.`cod`
        and `".$TableName."`.`lang_id` = `".$TableClear."`.`lang_id`)
        WHERE 1";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        //echo '<br>$q='.$q.' $res='.$res;
        if( !$res OR !$this->Right->result ) return false;
        $rows = $this->Right->db_GetNumRows();
        for($i=0;$i<$rows;$i++){
            $row = $this->Right->db_FetchAssoc();
            $arrUpdate[$i] = $row;
        }

        //echo '$this->name='.$this->name;
        //var_dump($arrUpdate);return false;
        $domen = 'http://'.$this->name.'/';
        $domen_www = 'http://www.'.$this->name.'/';
        $cnt_all = 0;
        for($i=0;$i<$rows;$i++){
            $row = $arrUpdate[$i];
            $content = $row['content'];
            if(strpos($content,$domen)!==false || strpos($content,$domen_www)!==false){
                $content_real = str_replace($domen_www,'/',$content,$count_no_www);
                $content_real = str_replace($domen,'/',$content_real,$count_www);
                $content_real = addslashes($content_real);
                $count = $count_www + $count_no_www;
                $name = $row['name'];
                $cod = $row['cod'];
                $lang_id = $row['lang_id'];
                $q="UPDATE `".$TableClear."` SET
                `".$TableClear."`.`name`= '".$content_real."'
                WHERE `".$TableClear."`.`cod`='".$cod."'
                and `".$TableClear."`.`lang_id` = '".$lang_id."' ";
                $res = $this->Right->Query( $q, $this->user_id, $this->module );
//                echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
                if( !$res OR !$this->Right->result ) return false;
                echo '<br>Изменено: Имя:'.$name.' cod='.$cod.' $lang_id='.$lang_id;
                $this->insertClearTable($nameModule.' '.$description,'Название страници:'.$name.' cod='.$cod.' id_user='.$this->user_id,$count);
                $cnt_all = $cnt_all + $count;
            }
        }
        $this->insertClearTable($nameModule,'Замена по '.$description,$cnt_all);
        ?><div style="color: green;font-size: 16px"><?=$nameModule.' '.$description?> почищен!</div><?
        return true;
    }

    // ================================================================================================
    // Function : insertClearTable()
    // Version : 1.0.0
    // Date : 28.05.2013
    // Returns : true,false / Void
    // Description : Save stat for clear
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function insertClearTable($module = NULL,$description = NULL,$cnt = NULL){
        $q="INSERT INTO `".TblModClear."` SET
                `date`='".strftime('%Y-%m-%d %H:%M', strtotime('now'))."',
                `module` = '".$module."',
                `description` = '".$description."',
                `cnt_replace` = '".$cnt."'";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
//        echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
        if( !$res OR !$this->Right->result ) return false;
        return true;
    }


}// end of class PagesBackend
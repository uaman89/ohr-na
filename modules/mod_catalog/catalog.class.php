<?php
/**
 * catalog.class.php
 * Class definition for all actions with managment of catalog
 * @package Catalog Package of SEOCMS
 * @author Igor Trokhymchuk  <ihor@seotm.com>
 * @version 1.1, 22.12.2010
 * @copyright (c) 2010+ by SEOTM
 */
include_once( SITE_PATH . '/modules/mod_catalog/catalog.defines.php' );

// ================================================================================================
//    Class             : Catalog
//    Date              : 21.03.2006
//    Constructor       : Yes
//    Returns           : None
//    Description       : Class definition for all actions with managment of Catalog
//    Programmer        : Igor Trokhymchuk
// ================================================================================================
/**
 * Class Catalog
 * parent class for all actions with managment of Catalog
 * @author Igor Trokhymchuk  <ihor@seotm.com>
 * @version 1.1, 22.12.2010
 */
class Catalog {

    public $user_id = NULL;
    public $module = NULL;
    public $Err = NULL;
    public $lang_id = _LANG_ID;
    public $translit_lang_id = NULL;
    public $translit_prop_lang_id = NULL;
    public $multi = NULL;
    public $sort = NULL;
    public $display = 20;
    public $start = 0;
    public $fln = NULL;
    public $width = 500;
    public $srch = NULL;
    public $fltr = NULL;
    public $fltr2 = NULL;
    public $script = NULL;
    public $parent_script = NULL;
    public $asc_desc = NULL;
    public $settings = NULL;
    public $db = NULL;
    public $Msg = NULL;
    public $Right = NULL;
    public $Form = NULL;
    public $Spr = NULL;
    public $tbl_charset = NULL;
    // variables for Multicategory
    public $id = NULL;
    public $level = NULL;
    public $group = NULL;
    public $description = NULL;
    public $move = NULL;
    public $visible = NULL;
    public $name_ind = NULL;
    // variables for Content of the Category
    public $parent = NULL;
    public $parent_module = NULL;
    public $parent_id = NULL;
    public $parent_display = NULL;
    public $parent_start = NULL;
    public $parent_sort = NULL;
    public $parent_task = NULL;
    public $parent_level = NULL;
    // variables for Settings and for Content of the Catalogue
    public $id_cat = NULL;
    public $id_manufac = NULL;
    public $id_group = NULL;
    public $name = NULL;
    public $img = NULL;
    public $id_img = NULL;
    public $id_file = NULL;
    public $short_descr = NULL;
    public $full_descr = NULL;
    public $specif = NULL;
    public $reviews = NULL;
    public $support = NULL;
    public $exist = NULL;
    public $email = NULL;
    public $number_name = NULL;
    public $price = NULL;
    public $opt_price = NULL;
    public $grnt = NULL;
    public $dt = NULL;
    // variables only for Settings of the Catalogue
    public $img_path = NULL;
    public $title = NULL;
    public $keywords = NULL;
    public $content_func = NULL;
    // variables for parameters
    public $arr_params = NULL;
    public $is_img = NULL;
    public $modify = NULL;
    public $arr_current_img_params_value = NULL;
    // variables for catalog statistic
    public $time_gen = NULL;
    public $page = NULL;
    public $refer = NULL;
    public $tm = NULL;
    public $ip = NULL;
    public $host = NULL;
    public $proxy = NULL;
    public $agent = NULL;
    public $screen_res = NULL;
    public $lang = NULL;
    public $country = NULL;
    public $cnt = NULL;
    public $id_user = NULL;
    // variables for responses and rating
    public $response = NULL;
    public $rating = NULL;

    public $treeCatList = NULL; //array $this->treeCatList[]=$id_cat
    public $treeCatLevels = array(); //array $this->treeCatLevels[level][id_cat]=''
    public $treeCatData = NULL; //array treeCatData[id_cat]=array with category data
    public $treeColorsData = NULL;
    public $treeShareData = NULL;
    public $treeGroupLevels = NULL;
    public $treeGroupData = NULL;

    /**
     * Class Constructor Catalog
     * Init variables for module Catalog.
     * @param usre_id   / User ID
     * @param module    / module ID
     * @param sort      / field by whith data will be sorted
     * @param display   / count of records for show
     * @param start     / first records for show
     * @param width     / width of the table in with all data show
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.1, 22.12.2010
     */
    function __construct($user_id = NULL, $module = NULL, $display = NULL, $sort = NULL, $start = NULL, $width = NULL) {
        //Check if Constants are overrulled
        ( $user_id != "" ? $this->user_id = $user_id : $this->user_id = NULL );
        ( $module != "" ? $this->module = $module : $this->module = NULL );
        ( $display != "" ? $this->display = $display : $this->display = 20 );
        ( $sort != "" ? $this->sort = $sort : $this->sort = NULL );
        ( $start != "" ? $this->start = $start : $this->start = 0 );
        ( $width != "" ? $this->width = $width : $this->width = 750 );


        $lg= check_init('UserAuthorize', 'UserAuthorize');

        if( !isset($lg->user_type) || $lg->user_type<5 && $lg->user_type!=1) {
            $this->group_user_id = 5;
        }else{
            $this->group_user_id = $lg->user_type;
        }

        $this->lang_id = _LANG_ID;

        if (empty($this->db))
            $this->db = DBs::getInstance();
        if (empty($this->Right))
            $this->Right = check_init('Rights', 'Rights', '"' . $this->user_id . '", "' . $this->module . '"');
        if (empty($this->Msg))
            $this->Msg = check_init('ShowMsg', 'ShowMsg');
        if (empty($this->Form))
            $this->Form = check_init('FormCatalog', 'Form', '"form_mod_catalog"');
        if (empty($this->Spr))
            $this->Spr = check_init('SysSpr', 'SysSpr', '"' . $this->user_id . '", "' . $this->module . '"');
        if (empty($this->settings))
            $this->settings = $this->GetSettings();



        //load all data of categories to arrays $this->treeCat, $this->treeCatLevels, $this->treeCatData
        $this->loadTree();
        //echo '<br />treeCatLevels=';print_r($this->treeCatLevels);
        //echo '<br />treeCatData=';print_r($this->treeCatData);
    }

//end of Catalog (Constructor)

    /**
     * Class method loadTree
     * load all data of catalog categories to arrays
     * @return true/false or arrays:
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.1, 05.04.2011
     */
    function loadTree($frontend=false)
    {

        if( is_array($this->GetTreeCatLevelsAll()) AND is_array($this->GetTreeCatDataAll()) ) return true;
        if(defined("USE_CACHE") AND USE_CACHE){
            if($frontend){
                $this->treeCatData =  Cache::instance()->get('Catalog.Categories.treeCatData'.$this->lang_id);
                $this->treeCatLevels = Cache::instance()->get('Catalog.Categories.treeCatLevels'.$this->lang_id);
                $this->treeCatList =  Cache::instance()->get('Catalog.Categories.treeCatList'.$this->lang_id);
            }else{
                $this->treeCatData =  Cache::instance()->get('Catalog.Categories.treeCatData.backend'.$this->lang_id);
                $this->treeCatLevels = Cache::instance()->get('Catalog.Categories.treeCatLevels.backend'.$this->lang_id);
                $this->treeCatList =  Cache::instance()->get('Catalog.Categories.treeCatList.backend'.$this->lang_id);
            }
        }
        if(empty($this->treeCatLevels) OR empty($this->treeCatList) OR empty($this->treeCatData)){
            $q = "SELECT
                    `".TblModCatalog."`.`id`,
                    `".TblModCatalog."`.`level`,
                    `".TblModCatalog."`.`img_cat`,
                    `".TblModCatalog."`.`move`,
                    `".TblModCatalog."`.`show_in_menu`,
                    `".TblModCatalogSprName."`.`name`,
                    `".TblModCatalogTranslit."`.`translit`";
            $q = $q.",`".TblModCatalogSprH1."`.`name` AS `h1`";

            if ( isset($this->settings['cat_name_ind']) AND $this->settings['cat_name_ind']=='1' ){
                $q = $q.",`".TblModCatalogSprNameInd."`.`name` AS `nameind`";
            }
            if ( isset($this->settings['cat_descr']) AND $this->settings['cat_descr']=='1' ){
                $q = $q.",`".TblModCatalogSprDescr."`.`name` AS `descr`";
            }
            if ( isset($this->settings['cat_descr2']) AND $this->settings['cat_descr2']=='1' ){
                $q = $q.",`".TblModCatalogSprDescr2."`.`name` AS `descr2`";
            }

            $q = $q."FROM `".TblModCatalog."`
                  LEFT JOIN `".TblModCatalogSprName."` ON (
                        `".TblModCatalog."`.`id`=`".TblModCatalogSprName."`.`cod`
                    AND
                        `".TblModCatalogSprName."`.`lang_id`='".$this->lang_id."')
                  LEFT JOIN `".TblModCatalogSprH1."` ON (
                        `".TblModCatalog."`.`id`=`".TblModCatalogSprH1."`.`cod`
                    AND
                        `".TblModCatalogSprH1."`.`lang_id`='".$this->lang_id."')";

            if ( isset($this->settings['cat_name_ind']) AND $this->settings['cat_name_ind']=='1' ){
                $q = $q."
                  LEFT JOIN `".TblModCatalogSprNameInd."` ON (
                        `".TblModCatalog."`.`id`=`".TblModCatalogSprNameInd."`.`cod`
                    AND
                        `".TblModCatalogSprNameInd."`.`lang_id`='".$this->lang_id."')";
            }
            if ( isset($this->settings['cat_descr']) AND $this->settings['cat_descr']=='1' ){
                $q = $q."
                  LEFT JOIN `".TblModCatalogSprDescr."` ON (
                        `".TblModCatalog."`.`id`=`".TblModCatalogSprDescr."`.`cod`
                    AND
                        `".TblModCatalogSprDescr."`.`lang_id`='".$this->lang_id."')";
            }
            if ( isset($this->settings['cat_descr2']) AND $this->settings['cat_descr2']=='1' ){
                $q = $q."
                  LEFT JOIN `".TblModCatalogSprDescr2."` ON (
                        `".TblModCatalog."`.`id`=`".TblModCatalogSprDescr2."`.`cod`
                    AND
                        `".TblModCatalogSprDescr2."`.`lang_id`='".$this->lang_id."')";
            }

            $q = $q.",
                  `".TblModCatalogTranslit."`
                  WHERE
                    `".TblModCatalogSprName."`.`name`!=''
                  AND
                    `".TblModCatalog."`.id = `".TblModCatalogTranslit."`.id_cat
                  AND
                    `".TblModCatalogTranslit."`.`lang_id`='".$this->lang_id."'
                  AND
                    `".TblModCatalogTranslit."`.`id_prop` IS NULL
                 ";
            if($frontend){
                $q .= "AND `".TblModCatalog."`.visible ='2'
                  AND `".TblModCatalog."`.`id`!='0' ";
            }
            $q .= " ORDER BY `".TblModCatalog."`.`level` ASC, `".TblModCatalog."`.`move` ASC";

            $res = $this->db->db_Query($q);
            //echo $q.' <br/>$res = '.$res.' $this->db->result='.$this->db->result;
            if(!$res OR !$this->db->result) return false;
            $rows = $this->db->db_GetNUmRows($res);
            if($rows==0)
                return false;

            for($i = 0; $i < $rows; $i++){
                $row = $this->db->db_FetchAssoc($res);
                $this->SetTreeCatLevel($row['level'], $row['id']);
                //$this->treeCatLevels[$row['level']][$row['id']]='';
                $this->SetTreeCatData($row);
                //$this->treeCatData[$row['id']]=$row;
            }
            //build category translit path for all categories and subcategories
            $this->makeCatPath();
            if(defined("USE_CACHE") AND USE_CACHE){
                if($frontend){
                    Cache::instance()->set('Catalog.Categories.treeCatData'.$this->lang_id, $this->treeCatData);
                    Cache::instance()->set('Catalog.Categories.treeCatLevels'.$this->lang_id, $this->treeCatLevels);
                    Cache::instance()->set('Catalog.Categories.treeCatList'.$this->lang_id, $this->treeCatList);
                }else{
                    Cache::instance()->set('Catalog.Categories.treeCatData.backend'.$this->lang_id, $this->treeCatData);
                    Cache::instance()->set('Catalog.Categories.treeCatLevels.backend'.$this->lang_id, $this->treeCatLevels);
                    Cache::instance()->set('Catalog.Categories.treeCatList.backend'.$this->lang_id, $this->treeCatList);
                }
            }
        }
        return true;
    } //end of function loadTree()

    /**
    * Class method SetTreeCatLevel
    * set new vlaue to property $this->treeCatLevels. It build array $this->treeCatLevels[level][id_cat]=''
    * @param integer $level - id of the parent category
    * @param integer $id - id of the category
    * @return none
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 17.05.2011
    */
    function SetTreeCatLevel($level, $id)
    {
        $this->treeCatLevels[$level][$id]='';
    } //end of function SetTreeCatLevel()

    /**
    * Class method GetTreeCatLevelsAll
    * get array $this->treeCatLevels
    * @return array $this->treeCatLevels
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 17.05.2011
    */
    function GetTreeCatLevelsAll()
    {
        return $this->treeCatLevels;
    } //end of function GetTreeCatLevelsAll()

    /**
    * Class method GetTreeCatLevel
    * get node of array $this->treeCatLevels where store array with sublevels
    * @param integer $item - id of the category as node in array
    * @return node of array $this->treeCatLevels[$item]
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 17.05.2011
    */
    function GetTreeCatLevel($item=0)
    {
        if(!isset($this->treeCatLevels[$item])) return false;
        return $this->treeCatLevels[$item];
    } //end of function GetTreeCatLevel()

    /**
    * Class method SetTreeCatData
    * set new vlaue to property $this->treeCatData. It build array $this->treeCatData[id_cat]=array with category data
    * @param array $row - assoc array with data of category
    * @return true
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 17.05.2011
    */
    function SetTreeCatData($row)
    {
        $this->treeCatData[$row['id']]=$row;
        return true;
    } //end of function SetTreeCatData()

    /**
    * Class method SetTreeCatDataAddNew
    * set new vlaue to property $this->treeCatData. It build array $this->treeCatData[id_cat]=array with category data
    * @param integer $id_cat - id of the category
    * @param varchar $key - name of new key
    * @param varchar $val - value for key $key
    * @return true
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 17.05.2011
    */
    function SetTreeCatDataAddNew($id_cat, $key, $val)
    {
        $this->treeCatData[$id_cat][$key]=$val;
        return true;
    } //end of function SetTreeCatDataAddNew()

    /**
    * Class method GetTreeCatDataAll
    * get array $this->treeCatData
    * @return array $this->treeCatData
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 17.05.2011
    */
    function GetTreeCatDataAll()
    {
        return $this->treeCatData;
    } //end of function GetTreeCatDataAll()

    /**
    * Class method GetTreeCatData
    * get node of array $this->treeCatData where store array with data about category
    * @param integer $item - id of the category as node in array
    * @param string $index - index of array to return
    * @return node of array $this->treeCatData[$item]
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 17.05.2011
    */
    function GetTreeCatData($item, $index=NULL)
    {
        if(!isset($this->treeCatData[$item])) return false;
        if(!empty($index)) return $this->treeCatData[$item][$index];
        return $this->treeCatData[$item];
    } //end of function GetTreeCatData()

    /**
    * Class method SetTreeCatList
    * set new vlaue to property $this->treeCatList. It build array $this->treeCatList[counter]=id of the category
    * @param integer $counter - counter for array
    * @param integer $id_cat - id of the category
    * @return true
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 17.05.2011
    */
    function SetTreeCatList($counter, $id_cat)
    {
        $this->treeCatList[$counter] = $id_cat;
        return true;
    } //end of function SetTreeCatList()

    /**
    * Class method GetTreeListAll
    * get array $this->treeCatList
    * @return array $this->treeCatList
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 17.05.2011
    */
    function GetTreeListAll()
    {
        return $this->treeCatList;
    } //end of function GetTreeListAll()

    /**
    * Class method makeCatPath
    * build relative url to category using category translit for all categories and subcategories
    * @param integer $level - id of the category
    * @param string $path - path to category
    * @return true/false
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 14.01.2011
    */
    function makeCatPath($level = 0, $path = NULL)
    {
        if( !$this->GetTreeCatLevel($level) ) return;
        $keys = array_keys($this->GetTreeCatLevel($level));
        $n = count($keys);
        for($i = 0; $i < $n; $i++) {
            //echo '<br />$keys[$i]='.$keys[$i];
            $row = $this->GetTreeCatData($keys[$i]);
            if(!$path) $full_path = $row['translit'].'/';
            else $full_path  = $path.$row['translit'].'/';
            //$this->treeCatData[$keys[$i]]['path'] = $full_path;
            $this->SetTreeCatDataAddNew($keys[$i], 'path', $full_path);
            //$this->treeCatList[]=$row['id'];
            $i2 = count($this->treeCatList);
            $this->SetTreeCatList($i2, $row['id']);
            if($row['id']!=0)$this->makeCatPath($row['id'], $full_path);
        }
    }//end of function makeCatPath()


    /**
    * Class method loadTreeList
    * Checking load tree list of catalog
    * @return array with index as counter
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 05.01.2011
    */
    function loadTreeList()
    {
        return $this->makeTreeList($this->treeCat);
    } //end of function loadTreeList()

    /**
    * Class method makeTreeList
    * make list tree of catalog category
    * @param array $tree - pointer to array with index as id_cat
    * @param integer $k_item - counter
    * @return array with index as counter
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 05.01.2011
    */
    function makeTreeList(&$tree, $k_item = 0, $path = NULL)
    {
        if( !isset($tree[$k_item]) OR empty($tree[$k_item])) return array();
        //echo '<br />tree[$k_item]='; print_r($tree[$k_item]);
        $a_tree = array();
        $n = count($tree[$k_item]);
        for($i = 0; $i < $n; $i++) {
            $row = $tree[$k_item][$i];
            if(!$path) $full_path = $tree[$k_item][$i]['translit'].'/';
            else $full_path  = $path.$tree[$k_item][$i]['translit'].'/';
            $row['a_tree'] = $this->makeTreeList($tree, $tree[$k_item][$i]['id'], $full_path);
            $row['path'] = $full_path;
            $a_tree[] = $row;

        }
        //print_r($a_tree);
        return $a_tree;
    }//end of function makeTreeList()

    /**
    * Class method isCatASubcatOfLevel
    * Checking if the category $id_cat is a subcategory of $item at any dept start from $arr[$item]
    * @param integer $id_cat - id of the category
    * @param array $arr - pointer to array with index as id_cat
    * @param integer $item - as index for array $arr
    * @return array with index as counter
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 05.01.2011
    */
    function isCatASubcatOfLevel($id_cat, $item)
    {
       if($id_cat==$item) return true;
       $a_tree = $this->GetTreeCatLevel($item);
       if( !$a_tree ) return false;
       $keys = array_keys($a_tree);
       $rows = count($keys);
       if(array_key_exists($id_cat, $a_tree)) return true;
       for ($i=0;$i<$rows;$i++) {
            $id = $keys[$i];
            //echo '<br />$id='.$id;
            if( $this->GetTreeCatLevel($id) AND is_array($this->GetTreeCatLevel($id)) ) {
                $res = $this->isCatASubcatOfLevel($id_cat, $id);
                if($res) return true;
            }
        }
        return false;
    } // end of function isCatASubcatOfLevel()

    /**
    * Class method isSubLevels()
    * Checking exist or not sublevels for category $id_cat
    * @param integer $id_cat - id of the category
    * @param array $arr - pointer to array with index as id_cat
    * @return true or false
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 17.05.2011
    */
    function isSubLevels($id_cat)
    {
       if( !$this->GetTreeCatLevel($id_cat) ) return false;
       $count = count($this->GetTreeCatLevel($id_cat));
       return $count;
    } // end of function isSubLevels()

    /**
    * Class method getSubLevels
    * return string with sublevels for category $id_cat
    * @param integer $level - id of the category
    * @return sting with id of categories like (1,13,15,164,222)
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 18.01.2011
    */
    function getSubLevels( $id_cat )
    {
       if( !$this->GetTreeCatLevel($id_cat) ) return false;
       $a_tree = $this->GetTreeCatLevel($id_cat);
       $keys = array_keys($a_tree);
       $rows = count($keys);
       for ($i=0;$i<$rows;$i++) {
            $id = $keys[$i];
            //echo '<br />$id='.$id;
            if( empty($arr_row)) $arr_row = $id;
            else $arr_row = $arr_row.','.$id;
            if(  $this->GetTreeCatLevel($id) AND is_array($this->GetTreeCatLevel($id)) ) {
                //if($id_cat!=0)$arr_row .= ','.$this->getSubLevels($id);
            }
        }
        return $arr_row;
    } // end of function getSubLevels()

    /**
    * Class method getTopLevel
    * get the top level of catalog for categary $id_cat
    * @param integer $id_cat - id_cat as index for array $arr
    * @param array $arr - pointer to array with indexes as $id_cat
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 13.01.2011
    */
    function getTopLevel($id_cat)
    {
        $cat_data = $this->GetTreeCatData($id_cat);
        if(!$cat_data) return false;
        if($cat_data['level']==0) return $id_cat;
        return $this->getTopLevel($cat_data['level']);
    } // end of function getTopLevel()

    /**
    * Class method getNumLevel
    * get number of sublevel
    * @param integer $id_cat - id  of category
    * @param integer $num - current number of sublevel
    * @return integer number of sublevel
    * @author Gyryn Yaroslav <yaroslav@seotm.com>
    * @version 1.0, 29.05.2013
    */
    function getNumLevel($id_cat, $num = 0)
    {
        $cat_data = $this->GetTreeCatData($id_cat);
        if(!$cat_data)
                return $num;
        return $this->getNumLevel($cat_data['level'], $num+1);
    } // end of function getNumLevel()

    /**
    * Class method getUrlByTranslit
    * build reletive URL link to category or to postion $id_prop
    * @param string $catTranslit - string with tranlsit to the category
    * @param string $prodTraslit - tranlsit ofthe position of catalog
    * @return string $link with reletive URL link to category $id_cat or to postion $id_prop
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 13.01.2011
    */
    function getUrlByTranslit($catTranslit, $prodTraslit=NULL)
    {


        if( !defined("_LINK")) {
            $Lang = check_init('SysLang', 'SysLang', 'NULL, "front"');
            if( _LANG_ID!=$Lang->GetDefFrontLangID() ) define("_LINK", "/".$Lang->GetLangShortName(_LANG_ID)."/");
            else define("_LINK", "/");
        }

        if(!empty($prodTraslit)){
            if(CATALOG_TRASLIT){
                $link = _LINK.$prodTraslit.'.html';
            }else{
                $link = _LINK.'catalog/'.$catTranslit.$prodTraslit.'.html';
            }
        }elseif(CATALOG_TRASLIT){
            $link = _LINK.$catTranslit;
        }else{
            $link = _LINK.'catalog/'.$catTranslit;
        }

        return $link;
    } //end of function getUrlByTranslit()




    /**
     * Class method CheckCateg
     * Checking the directory transliteration
     * @return true If the truth or false not
     * @author Bogdan Iglinsky  <bi@seotm.com>
     * @version 1.0, 09.26.2012
     */
    function CheckCateg(){
        if(!isset($_REQUEST['q'])) return false;
        $str_arr=  explode('/', $_REQUEST['q']);
//        var_dump($str_arr);
        $count=count($str_arr);
//        echo '$count='.$count;
        if(empty($str_arr[$count-1])){
            unset($str_arr[$count-1]);
            $count=count($str_arr);
        }
        $traslit = $str_arr[0];
        $q = "SELECT id
            FROM `" . TblModCatalogTranslit . "`
            WHERE `translit`='".$traslit."'
            AND `id_cat_parent`='0'
            AND id_prop is NULL";
        $res = $this->db->db_Query($q);
//            echo '<br>'.$q.'<br> res='.$res.'<br> $this->db->result='.$this->db->result;
        if (!$res or !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows($res);
        //echo '<br>$rows='.$rows;
        if($rows>0){
            for($i=0;$i<$count-1;$i++){
                $item_str = $str_arr[$i];
                switch($item_str){
                    case 'alltovar':
                        $_REQUEST['page']='alltovar';
                        break;
                    case 'compare':
                        $_REQUEST['task']='compare';
                        break;
                    default:
                        $_REQUEST['str_cat'][$i] = $item_str;
                        break;
                }
            }
            $item_str = $str_arr[$count-1];
//            var_dump($_REQUEST);
            if(!strstr($item_str,'html')){
                $brand = $str_arr[$count-1];
                $id_manufac = $this->checkBrand($brand);
                if(!empty($id_manufac)){
                    unset($_REQUEST['str_cat'][$count-1]);
                    $_REQUEST['id_manufac'] = $id_manufac;
                }else{
                    if(isset($_REQUEST['str_cat']))$_REQUEST['str_cat'][] = $item_str;
                    else $_REQUEST['str_cat'][0] = $item_str;
                }
            }else{
                $_REQUEST['str_id'] = substr($item_str,0,strlen($item_str)-5);
            }
//            var_dump($_REQUEST);
            return true;
        }
        return false;
    }

    function checkBrand($brand){
        $q="SELECT `".TblModCatalogSprManufac."`.`cod`
        FROM `".TblModCatalogSprManufac."`
        WHERE `".TblModCatalogSprManufac."`.`lang_id` = '".$this->lang_id."'
            AND '".$brand."' = `".TblModCatalogSprManufac."`.`translit` ";
        $res = $this->db->db_Query( $q );
//        echo '<br> $q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;
        if ( !$res or !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        if($rows==0) return false;
        $row = $this->db->db_FetchAssoc();
        return $row['cod'];
    }

    /**
     * Class method show_levels_tree_back_end
     * show tree of catalog on the back-end
     * @param $level   / ID of the category
     * @param $script  / url with parameters
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.1, 22.12.2010
     */
    function show_levels_tree_back_end($level = 0, $script) {
        $q = "select
                    `" . TblModCatalog . "`.id,
                    `" . TblModCatalog . "`.level,
                    `" . TblModCatalogSprName . "`.name
              from `" . TblModCatalog . "` LEFT JOIN `" . TblModCatalogSprName . "`
                    ON ( `" . TblModCatalog . "`.id = `" . TblModCatalogSprName . "`.cod AND `" . TblModCatalogSprName . "`.lang_id = '" . $this->lang_id . "')
              where `" . TblModCatalog . "`.id != '0'
                    order by `level` asc, `move` ";
//        $q = "select * from `".TblModCatalog."` where 1 and `level`='".$level."' order by `move` ";
        $res = $this->db->db_Query($q);
//        echo '<br>$q='.$q.' $res='.$res;
        $rows = $this->db->db_GetNumRows($res);
        $levels = array();
        $names = array();
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc($res);
            $levels [$row['level']][] = $row ['id'];
            $names [$row['id']] = $row['name'];
        }
        $this->countArr = $this->GetArrayContentCount(); // Количество товаров в каждой категории
        ?>
        <script src="/admin/include/js/treeView/jquery.treeview.js" type="text/javascript"></script>
        <script src="/admin/include/js/treeView/jquery.cooki.js" type="text/javascript"></script>
        <link rel="stylesheet" href="/admin/include/js/treeView/jquery.treeview.css" />
        <script type="text/javascript">
            $(document).ready(function() {
                $("#tree").treeview({
                    collapsed: true,
                    animated: "medium",
                    control:"#sidetreecontrol",
                    persist: "cookie",
                    cookieId: "catalogTreeView"
                });
            })

        </script>
        <div id="sidetreecontrol"><a href="?#"><?= $this->multi['TXT_COLLAPSE_ALL'] ?></a> | <a href="?#"><?= $this->multi['TXT_EXPAND_ALL'] ?></a></div>
        <?
        $this->show_levels_tree_back_end_inner(0, $script, $levels, $names);
    }

//end of function show_levels_tree_back_end()
    // ================================================================================================
    // Function : show_levels_tree_back_end_inner()
    // Date : 23.12.2010
    // Returns : true,false / Void
    // Programmer : Oleg Morgalyuk
    // Reason for change : Optimization
    // ================================================================================================
    function show_levels_tree_back_end_inner($level = 0, $script, $levels, $names) {
        //echo '<br>$this->lang_id='.$this->lang_id;
        if (!isset($levels[$level]))
            return;
        $count = count($levels[$level]);
        //echo '<br>$q='.$q.' $res='.$res;
        //echo $this->page;
        ?>

        <ul id="tree" class="filetree treeview"><?
        for ($i = 0; $i < $count; $i++) {
            $id = $levels[$level][$i];
            $link = $script . '&amp;level=' . $id;

            if ($this->level == $id) {
                $li = "lev_act";
                $s = "mactive";
            } else {
                $li = "lev0";
                $s = "mpass";
            }

            $is_sub_level = (isset($levels[$id]) && count($levels[$id]) > 0);
            //$count_content = $this->IsContent($id, NULL, NULL, 'back');
            if (isset($this->countArr[$id]))
                $count_content = $this->countArr[$id];
            else
                $count_content = 0;
            if(!empty($this->parent_module)){
                $parent_module = $this->parent_module;
            }else{
                $parent_module = $this->module;
            }
            $link_content = $_SERVER['PHP_SELF'] . "?module=" . $this->settings['content_func'] . "&task=show&id_cat=" . $id . "&parent=1&parent_id=" . $id . "&parent_module=" . $parent_module . "&parent_display=" . $this->display . "&parent_start=" . $this->start . "&parent_sort=" . $this->sort . "&parent_task=show&parent_level=" . $level;
            ?><li class="<?= $li; ?>"><?
            if ($is_sub_level) {
                ?><a class="folder " href="<?= $link; ?>"><?= $names[$id]; ?></a><?
                if ($count_content > 0) {
                    ?><br/><a href="<?= $link_content; ?>"><?= $this->multi['FLD_CONTENT']; ?></a><span class="not_href">&nbsp;[<?= $count_content; ?>]</span><?
                }
            } else {
                if (isset($this->settings['cat_content']) AND $this->settings['cat_content'] == '1') {
                    if ($count_content > 0) {
                        ?><a class="file " href="<?= $link_content; ?>"><?= $names[$id]; ?></a><span class="not_href">&nbsp;[<?= $count_content; ?>]</span><?
                    } else {
                        ?><a class="file " href="<?= $link_content; ?>"><?= $names[$id]; ?></a><?
                        /* ?><span class="not_href"><?=$row['prodname'];?></span><? */
                    }
                } else {
                    ?><a class="file " href="<?= $script; ?>&task=edit&id=<?= $id; ?>"><?= $names[$id]; ?></a><?
                }
            }
            if ($is_sub_level)
                $this->show_levels_tree_back_end_inner($id, $script, $levels, $names);
            ?></li><?
        } //end for
        ?></ul><?
    }

//end of function show_levels_tree_back_end_inner()
    // ================================================================================================
    // Function : up()
    // Version : 1.0.0
    // Date : 11.02.2005
    // Parms : $table,  $level_name, $level_val
    // Returns :      true,false / Void
    // Description :  Up position
    // Programmer :  Andriy Lykhodid
    // ================================================================================================
    function up($table, $level_name = NULL, $level_val = NULL) {
        $q = "select * from `" . $table . "` where `move`='" . $this->move . "'";
        if (!empty($level_name)) {
            if (empty($level_val) AND $level_name == 'id_cat')
                $level_val = $this->GetIdCatByMove($this->move);
            $q = $q . " AND `" . $level_name . "`='" . $level_val . "'";
        }
        $res = $this->Right->Query($q, $this->user_id, $this->module);
        //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
        if (!$res)
            return false;
        $rows = $this->Right->db_GetNumRows();
        $row = $this->Right->db_FetchAssoc();
        $move_down = $row['move'];
        $id_down = $row['id'];


        $q = "select * from `" . $table . "` where `move`<'" . $this->move . "'";
        if (!empty($level_name))
            $q = $q . " AND `" . $level_name . "`='" . $level_val . "'";
        $q = $q . " order by `move` desc";
        $res = $this->Right->Query($q, $this->user_id, $this->module);
        //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
        if (!$res)
            return false;
        $rows = $this->Right->db_GetNumRows();
        $row = $this->Right->db_FetchAssoc();
        $move_up = $row['move'];
        $id_up = $row['id'];

        //echo '<br> $move_down='.$move_down.' $id_down ='.$id_down.' $move_up ='.$move_up.' $id_up ='.$id_up;
        if ($move_down != 0 AND $move_up != 0) {
            $q = "update `" . $table . "` set
         `move`='" . $move_down . "' where `id`='" . $id_up . "'";
            $res = $this->Right->Query($q, $this->user_id, $this->module);
            //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;

            $q = "update `" . $table . "` set
         `move`='" . $move_up . "' where `id`='" . $id_down . "'";
            $res = $this->Right->Query($q, $this->user_id, $this->module);
            //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
        }
    }

// end of function up()
    // ================================================================================================
    // Function : down()
    // Date : 11.02.2005
    // Parms :  $table,  $level_name, $level_val
    // Returns :      true,false / Void
    // Description :  Down position
    // Programmer :  Andriy Lykhodid
    // ================================================================================================

    function down($table, $level_name = NULL, $level_val = NULL) {
        $q = "select * from `" . $table . "` where `move`='" . $this->move . "'";
        if (!empty($level_name)) {
            if (empty($level_val) AND $level_name == 'id_cat')
                $level_val = $this->GetIdCatByMove($this->move);
            $q = $q . " AND `" . $level_name . "`='" . $level_val . "'";
        }
        $res = $this->Right->Query($q, $this->user_id, $this->module);
        //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
        if (!$res)
            return false;
        $rows = $this->Right->db_GetNumRows();
        $row = $this->Right->db_FetchAssoc();
        $move_up = $row['move'];
        $id_up = $row['id'];


        $q = "select * from `" . $table . "` where `move`>'" . $this->move . "'";
        if (!empty($level_name))
            $q = $q . " AND `" . $level_name . "`='" . $level_val . "'";
        $q = $q . " order by `move` asc";
        $res = $this->Right->Query($q, $this->user_id, $this->module);
        //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
        if (!$res)
            return false;
        $rows = $this->Right->db_GetNumRows();
        $row = $this->Right->db_FetchAssoc();
        $move_down = $row['move'];
        $id_down = $row['id'];

        if ($move_down != 0 AND $move_up != 0) {
            $q = "update `" . $table . "` set
         `move`='" . $move_down . "' where `id`='" . $id_up . "'";
            $res = $this->Right->Query($q, $this->user_id, $this->module);

            $q = "update `" . $table . "` set
         `move`='" . $move_up . "' where `id`='" . $id_down . "'";
            $res = $this->Right->Query($q, $this->user_id, $this->module);
        }
    }

// end of function down()
    // ================================================================================================
    // Function : ShowPathToLevel()
    // Date : 21.03.2006
    // Parms :        $id - id of the record in the table
    // Returns :      $str / string with name of the categoties to current level of catalogue
    // Description :  Return as links path of the categories to selected level of catalogue
    // Programmer :  Igor Trokhymchuk
    // ================================================================================================
    function ShowPathToLevel($level, $str = NULL, $script) {
        //$script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr.'&fln='.$this->fln;
        //$script = $_SERVER['PHP_SELF']."?$script";

        $q = "SELECT * FROM " . TblModCatalog . " WHERE `id`='" . $level . "'";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
        if (!$res)
            return false;
        $row = $this->db->db_FetchAssoc();

        if (!empty($str))
            $str = '<a href="' . $script . '&level=' . $level . '">' . $this->Spr->GetNameByCod(TblModCatalogSprName, $level, $this->lang_id, 1) . '</a>  → ' . $str;
        else
            $str = '<span style="color:#000000; font-size:8pt; font-weight:bold;">' . $this->Spr->GetNameByCod(TblModCatalogSprName, $level, $this->lang_id, 1) . '</span>' . $str;
        if ($row['level'] > 0) {
            $this->ShowPathToLevel($row['level'], $str, $script);
            return true;
        }
        $str = '<a href="' . $script . '&level=0">' . $this->Msg->show_text('TXT_ROOT_CATEGORY') . '</a> → ' . $str;
        echo $str;
        return true;
    }

// end of function ShowPathToLevel()
    // ================================================================================================
    // Function : GetCatalogCatInArr()
    // Date : 13.05.2010
    // Parms :   $level - level of the catalog
    //           $front_back - can be 'front' or 'back'
    // Returns : true,false / Void
    // Description :
    // Programmer : Ihor Trokhumchuk
    // ================================================================================================
    function GetCatalogCatInArr($level = NULL, $front_back = 'back') {
        $arr = array();
        $rrr = $this->GetTreeCatLevel($level);
        //echo '<br>$level='.$level;print_r($rrr);
        if(is_array($rrr)){
            $keys = array_keys($rrr);
            $rows = count($rrr);
            //echo '<br> $rows='.$rows;
            for ($i = 0; $i < $rows; $i++) {
                $data = $this->GetTreeCatData($keys[$i]);
                if($data['id']==0){
                    continue;
                }
                if($front_back=='front' AND $data['visible']!=2){
                    continue;
                }
                $arr[$data['level']][$data['id']] = $data;
                $arr = $arr + $this->GetCatalogCatInArr($data['id'], $front_back);
            }
        }
        return $arr;
    }

//end of function GetCatalogCatInArr()
    // ================================================================================================
    // Function : PrepareCatalogForSelect()
    // Date : 13.05.2010
    // Returns : true,false / Void
    // Description : prepare array of catalog with all parameters for write <option> in Select elevent
    // Programmer : Ihor Trokhumchuk
    // ================================================================================================
    function PrepareCatalogForSelect($level = 0, $arr_result = NULL, $spacer = NULL, $front_back = 'back', $show_sublevels = true, $show_content = false, $show_cnt_pos = false, $show_cnt_params_for_cat = false, $value = NULL, $curr_idcat = NULL, $counter = 0) {

        if (empty($arr_result))
            $arr_result = array();
        //echo '<hr>on start $counter='.$counter.' ';
        //print_r($arr_result);
        $db = new DB();
        //$tmp_db = new DB();
        if (!isset($this->temp_arr_categs) OR !is_array($this->temp_arr_categs)) {
            $this->temp_arr_categs = $this->GetCatalogCatInArr(0, $front_back);
            //echo '<hr/>';
            //print_r($this->temp_arr_categs);
        }
        if (!isset($this->temp_arr_categs[$level]))
            return $arr_result;

        //$rows = count($this->temp_arr_categs);
        //echo '<br> $rows='.$rows;
        if ($show_content)
            $disable = 'disabled="disabled"';
        else
            $disable = '';
        $i = 0;
        $arr_categs = array_keys($this->temp_arr_categs[$level]);
        //echo '<hr/>';
        //print_r($arr_categs);
        $rows = count($arr_categs);
        for ($i = 0; $i < $rows; $i++) {
            //foreach( $this->temp_arr_categs[$level] as $k=>$v){
            $row = $this->temp_arr_categs[$level][$arr_categs[$i]];
            //echo '<br />0000000000$row=';print_r($row);
            if ($curr_idcat == $row['id'] AND empty($disable))
                $disable = 'disabled="disabled"';
            else
                $disable_cat = $disable;

            $output_str = $spacer . '- ' . stripslashes($row['name']);
            //if show count of parameters for current category
            if ($show_cnt_params_for_cat) {
                $cnt_params = $this->IsParams($row['id']);
                if ($cnt_params > 0)
                    $output_str = $output_str . ' {' . $cnt_params . '}';
            }

            //if show count of positions in currect category
            if ($show_cnt_pos) {
                $cnt = $this->IsContent($row['id'], NULL, NULL, $front_back);
                if ($cnt > 0)
                    $output_str = $output_str . ' [' . $cnt . ']';
            }

            $arr_result[$counter]['id'] = $row['id'];
            $arr_result[$counter]['level'] = $row['level'];
            $arr_result[$counter]['name'] = $output_str;
            $arr_result[$counter]['disable'] = $disable;
            $arr_result[$counter]['spacer'] = $spacer;
            $counter++;
            //echo '<hr>after add $counter='.$counter.' ';
            //print_r($arr_result);
            //----------------- show subcategory ----------------------------
            if ($show_sublevels AND isset($this->temp_arr_categs[$row['id']])) {
                $arr_result = $this->PrepareCatalogForSelect($row['id'], $arr_result, $spacer . '&nbsp;&nbsp;&nbsp;', $front_back, $show_sublevels, $show_content, $show_cnt_pos, $show_cnt_params_for_cat, $value, $curr_idcat, $counter);
                $counter = count($arr_result);
                //echo '<hr>after sublevels $counter='.$counter.' ';
                //print_r($arr_result);
            }
            //------------------------------------------------------------------
        }
        return $arr_result;
    }

// end of function PrepareCatalogForSelect()
    // ================================================================================================
    // Function : PreparePositionsTreeForSelect()
    // Date : 04.04.2006
    // Parms :   $levels - can bee 'all'-for all categories; number, as 1 - for category woth id=1; list, as 1,2,4,12,15,22 - all categories with theese ids.
    //           $front_back - can be 'front' or 'back'
    //           $sort - name of fiels for order
    //           $asc_desc - ACS or DESC
    //           $disable_idprops - can be array with index of id_prop for show as disable, or item value
    // Returns : true,false / Void
    // Description : Show structure of catalog in Combo box
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function PreparePositionsTreeForSelect($levels = 'all', $front_back = 'back', $sort = "move", $asc_desc = "asc", $disable_idprops = NULL) {
        $arr = array();
        $q = "SELECT `" . TblModCatalogProp . "`.*, `" . TblModCatalogPropSprName . "`.`name`
             FROM `" . TblModCatalogProp . "`, `" . TblModCatalog . "`, `" . TblModCatalogPropSprName . "`
             WHERE `" . TblModCatalogProp . "`.`id_cat`=`" . TblModCatalog . "`.`id`
             AND `" . TblModCatalogProp . "`.`id`=`" . TblModCatalogPropSprName . "`.`cod`
             AND `" . TblModCatalogPropSprName . "`.`lang_id`='" . $this->lang_id . "'
            ";
        if (strstr($levels, ","))
            $q.=" `" . TblModCatalogProp . "`.`id_cat` IN ('" . $levels . "')";
        elseif ($levels != 'all')
            $q.=" `" . TblModCatalogProp . "`.`id_cat`='" . $levels . "'";
        if ($front_back == 'front')
            $q .= " AND `" . TblModCatalog . "`.`visible`='2' AND `" . TblModCatalogProp . "`.`visible`='2'";
        $q .= " ORDER BY `" . TblModCatalog . "`.`move` asc, `" . TblModCatalogProp . "`.`" . $sort . "` " . $asc_desc;
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
        if (!$res OR !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();

        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc();
            if (is_array($disable_idprops)) {
                if (isset($disable_idprops[$row['id']]))
                    $disable_prop = 'disabled="disabled"';
                else
                    $disable_prop = '';
            }
            elseif ($disable_idprops == $row['id'])
                $disable_prop = 'disabled="disabled"';
            else
                $disable_prop = '';

            $arr[$row['id_cat']][$row['id']] = $row;
            $arr[$row['id_cat']][$row['id']]['disable'] = $disable_prop;
        }
        return $arr;
    }

//end of function PreparePositionsTreeForSelect()
    // ================================================================================================
    // Function : ShowCatalogInSelect()
    // Date : 13.05.2010
    // Returns : true,false / Void
    // Description : Show structure of catalogue in Select
    // Programmer : Ihor Trokhumchuk
    // ================================================================================================
    function ShowCatalogInSelect($arr_categs, $arr_props, $default_val, $select_name = 'arr_prop[]', $value = NULL, $params = '') {
        $categ_class = 'sel_categ_class';
        $prod_class = 'sel_prod_class';
        ?>
        <select name="<?= $select_name; ?>" <?= $params; ?>>
            <? if ($value == '') { ?><option value="" selected disabled="disabled"><?= $default_val; ?></option><? } else {
                ?><option value="" ><?= $default_val; ?></option><?
        }
        $rows = count($arr_categs);
        for ($i = 0; $i < $rows; $i++) {
            $id_cat = $arr_categs[$i]['id'];
            ?><option value="<?= 'categ=' . $id_cat; ?>" class="<?= $categ_class; ?>" <?= $arr_categs[$i]['disable']; ?>><?= $arr_categs[$i]['name']; ?></option><?
            if (isset($arr_props[$id_cat])) {
                $arr = array_keys($arr_props[$id_cat]);
                $rows2 = count($arr);
                for ($j = 0; $j < $rows2; $j++) {
                    $row = $arr_props[$id_cat][$arr[$j]];
                    $current_val = 'curcod=' . $row['id'];
                    if ($value == $current_val)
                        $selected = "selected";
                    else
                        $selected = '';
                        ?><option value="<?= $current_val; ?>" class="<?= $prod_class; ?>" <?= $row['disable']; ?> <?= $selected; ?> ><?= $arr_categs[$i]['spacer'] . '&nbsp;&nbsp;&nbsp;' . $row['name']; ?></option><?
                }
            }
        }
        //$this->WriteCatalogSelectOptions($level, $default_val, $mas, $spacer, $show_content, $front_back, $show_sublevels, $show_cnt_pos, $show_cnt_params_for_cat, $value, $curr_idcat, $curr_idprop );
            ?>
        </select>
        <?
    }

//end of function ShowCatalogInSelect()
    // ================================================================================================
    // Function : ShowCatalogInCheckbox()
    // Date : 13.05.2010
    // Returns : true,false / Void
    // Description : Show structure of catalogue in Select
    // Programmer : Ihor Trokhumchuk
    // ================================================================================================
    function ShowCatalogInCheckbox($arr_categs, $arr_props, $select_name = 'multi_categs', $val = NULL, $params = '') {
        $categ_class = 'sel_categ_class';
        $prod_class = 'sel_prod_class';
        /*
          if( $value=='' ){?><option value="" selected disabled="disabled"><?=$default_val;?></option><?}
          else{?><option value="" disabled="disabled"><?=$default_val;?></option><?}
         */

        $rows = count($arr_categs);
        for ($i = 0; $i < $rows; $i++) {
            $id_cat = $arr_categs[$i]['id'];
            if (isset($val[$id_cat]))
                $checked = "checked";
            else
                $checked = '';
            ?>
            <input type="checkbox" id="multi_categs<?= $id_cat; ?>" name="<?= $select_name; ?>[<?= $id_cat; ?>]" value="<?= $id_cat; ?>" <?= $checked; ?> /><label for="multi_categs<?= $id_cat; ?>"><?= $arr_categs[$i]['name']; ?></label>
            <br />
            <?
        }
    }

//end of function ShowCatalogInCheckbox()
    // ================================================================================================
    // Function : GetCatalogInArray()
    // Date : 20.10.2009
    // Parms :
    // Returns : true,false / Void
    // Description : Show structure of catalog in Combo box
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GetCatalogInArray($level = NULL, $default_val = NULL, $mas = NULL, $spacer = NULL, $show_content = 1, $front_back = 'back', $show_sublevels = 1, $show_cnt_pos = 0, $show_cnt_params_for_cat = 0)
    {
        if(empty($level)) $level = 0;
        if(!empty($default_val))$mas[''] = $default_val;
        $rrr = $this->GetTreeCatLevel($level);
        //echo '<br>$level='.$level;print_r($rrr);
        if(is_array($rrr)){
            $keys = array_keys($rrr);
            $rows = count($rrr);
            //echo '<br> $rows='.$rows;
            for ($i = 0; $i < $rows; $i++) {
                $row = $this->GetTreeCatData($keys[$i]);
                if($front_back=='front' AND $row['visible']!=2) continue;
                $output_str = '';
                if($row['id']!=0)$output_str = $spacer . '- ';
                $output_str .= $row['name'];
                //if show count of parameters for current category
                if ($show_cnt_params_for_cat == 1) {
                    $cnt_params = $this->IsParams($row['id']);
                    if ($cnt_params > 0)
                        $output_str = $output_str . ' {' . $cnt_params . '}';
                }

                //if show count of positions in currect category
                if ($show_cnt_pos == 1) {
                    $cnt = $this->IsContent($row['id'], NULL, NULL, $front_back);
                    if ($cnt > 0)
                        $output_str = $output_str . ' [' . $cnt . ']';
                }

                $mas['categ=' . $row['id']] = $output_str;

                //----------------- show content of the level ----------------------
                if ($show_content == 155) {
                    //if( !isset($arr_prop))
                    $arr_prop = $this->GetArrModelsOfManufacForCategory($row['id'], NULL, "name", "asc", NULL, NULL, $front_back);
                    if (is_array($arr_prop)) {
                        foreach ($arr_prop as $k => $v) {
                            if ($k == '')
                                continue;
                            if (!isset($this->settings['name']) OR empty($this->settings['name'])) {
                                $mas['curcod=' . $v['id']] = $spacer . '&nbsp;&nbsp;&nbsp;' . $v['number_name'];
                            } else {
                                if ($front_back == 'front')
                                    $mas['curcod=' . $v['id']] = $spacer . '&nbsp;&nbsp;&nbsp;' . $v['full_name'];
                                else
                                    $mas['curcod=' . $v['id']] = $spacer . '&nbsp;&nbsp;&nbsp;' . $v['name'];
                            }
                        }
                    }
                }
                //------------------------------------------------------------------
                //----------------- show subcategory ----------------------------
                if ($show_sublevels == 1 AND count($this->GetTreeCatLevel($row['id'])) > 0 AND $row['id']!=0) {
                    $mas = $mas + $this->GetCatalogInArray($row['id'], $default_val, $mas, $spacer . '&nbsp;&nbsp;&nbsp;', $show_content, $front_back, $show_sublevels, $show_cnt_pos, $show_cnt_params_for_cat);
                }
                //------------------------------------------------------------------
            }
        }
        return $mas;
    }

// end of function GetCatalogInArray()

    // ================================================================================================
    // Function : ShowErrBackEnd()
    // Date : 10.01.2006
    // Returns :     void
    // Description :  Show errors
    // Programmer :  Igor Trokhymchuk
    // ================================================================================================
    function ShowErrBackEnd() {
        if ($this->Err) {
            echo '
        <fieldset class="err" title="' . $this->Msg->show_text('MSG_ERRORS') . '"> <legend>' . $this->Msg->show_text('MSG_ERRORS') . '</legend>
        <div class="err_text">' . $this->Err . '</div>
        </fieldset>';
        }
    }

//end of fuinction ShowErrBackEnd()



    // ================================================================================================
    // Function : GetTopLevelsTranslit()
    // Date : 23.10.2009
    // Parms :  $id_cat = 0  - level of menu  (0 - first level)
    // Returns : true,false / Void
    // Description : return all top levels of the curent category in array like $arr_row[12]=''
    //                                                                          $arr_row[13]=''
    //                                                                          $arr_row[17]='',
    //               where 12,13,17 - id of the top levels
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetTopLevelsTranslit($id_cat, $lang_id = NULL, $source_id_cat = NULL) {
        if (empty($source_id_cat))
            $source_id_cat = $id_cat;
        //echo '<br />$this->cat_path=';print_r($this->cat_path);
        if (isset($this->cat_path[$source_id_cat]))
            return $this->cat_path[$source_id_cat];

        //$arr_row[$level]='';
        //$q = "select * from `".TblModCatalog."` where id='".$level."'";
        $q = "SELECT
               `" . TblModCatalog . "`.id,
               `" . TblModCatalog . "`.level,
               `" . TblModCatalogTranslit . "`.translit
             FROM
               `" . TblModCatalog . "`, `" . TblModCatalogTranslit . "`
            WHERE
               `" . TblModCatalog . "`.id ='" . $id_cat . "'
            AND
               `" . TblModCatalog . "`.id = `" . TblModCatalogTranslit . "`.id_cat";
        if (!empty($lang_id))
            $q = $q . " AND `" . TblModCatalogTranslit . "`.lang_id ='" . $lang_id . "'";

        $q = $q . " AND `" . TblModCatalogTranslit . "`.id_prop IS NULL";

        $res = $this->db->db_Query($q);
        //echo '<br> $q='.$q.' $res='.$res.' $db->result='.$db->result;
        if (!$res)
            return false;
        $row = $this->db->db_FetchAssoc();
        $arr_row[$row['id']] = $row['translit'];
        if ($row['level'] > 0) {
            //echo '<br> $arr_row['.$row['level'].']='.$arr_row[$row['level']];
            $arr_row += $this->GetTopLevelsTranslit($row['level'], $lang_id, $id_cat);
        }
        //if ($arr_row==NULL) $arr_row[$level]='';
        //echo '$arr_row=';print_r($arr_row);
        //store results for future
        $this->cat_path[$source_id_cat] = $arr_row;
        return $arr_row;
    }

// end of function GetTopLevelsTranslit()

    // ================================================================================================
    // Function : get_top_levels_in_array()
    // Date : 07.04.2006
    // Parms :
    //           $level = 0  - level of menu  (0 - first level)
    // Returns : true,false / Void
    // Description : return all top levels of the curent category in array like $arr_row[12]=''
    //                                                                          $arr_row[13]=''
    //                                                                          $arr_row[17]='',
    //               where 12,13,17 - id of the top levels
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function get_top_levels_in_array($level, $arr_row = NULL) {
        if(!empty($level))$arr_row[$level] = '';
        $row = $this->GetTreeCatData($level);
        if ($row['level'] > 0) {
            $arr_row[$row['level']] = '';
            $arr_row = $this->get_top_levels_in_array($row['level'], $arr_row);
        }else{
            $arr_row[0] = '';
        }
        //if ($arr_row==NULL) $arr_row[$level]='';
        return $arr_row;
    }

// end of function get_top_levels()


    // ================================================================================================
    // Function : IsContent()
    // Date : 10.10.2009
    // Parms :   $level      -  id of the category
    //           $filter     -  name of the field from TblModCatalogProp for filter
    //           $value      -  value of the field $filter form TblModCatalogProp for filter
    //           $front_back -  can be front ot back
    // Returns : true,false / Void
    // Description : check exist or not content for current category
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function IsContent($level, $filter = NULL, $value = NULL, $front_back = 'front') {
        //$q = "SELECT * FROM `".TblModCatalogProp."` WHERE `id_cat`='".$level."'";
        $lc = false;
        $arr = $this->GetTreeCatLevel($level);

        if($arr){
            $lc = array_keys( $arr  );
        }


        //  var_dump($lc);

        if($lc) {
            $levelCat = implode(", " , $lc ) ;
            // var_dump($levelCat);
            $level = $level.",".$levelCat;
        }

        $this->catList =   $level;


        $q = "SELECT COUNT(*) as count FROM `" . TblModCatalogProp . "` WHERE `id_cat` IN (" . $level . ")";


        if ($filter)
            $q = $q . " and `" . $filter . "`='" . $value . "'";
        if ($front_back == 'front')
            $q = $q . " AND `visible`='2'";
        $res = $this->db->db_Query($q);
        //echo '<br> $q='.$q;
        if (!$res OR !$this->db->result)
            return false;
        $row = $this->db->db_FetchAssoc();

        //если ничего не нашел, но у нас еще включені доп. категории
        if ( $row['count'] == 0 && $this->settings['multi_categs'] == 1 ){
            $q = "SELECT COUNT(id) as count FROM `" . TblModCatalogPropMultiCategs . "` WHERE `id_cat` IN (" . $level . ")";
            $res = $this->db->db_Query($q);
            //echo '<br> $q='.$q;
            if (!$res OR !$this->db->result)
                return false;
            $row = $this->db->db_FetchAssoc();
        }

        return $row['count'];
    }

//end of function  IsContent()
    // ================================================================================================
    // Function : GetArrayContentCount()
    // Date : 05.01.2010
    // Parms :   $front_back -  can be front ot back
    // Returns : $count - array
    // Description : Get content count for each category in array
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GetArrayContentCount($front_back = 'back') {
        $q = "SELECT id_cat, COUNT( * ) as count FROM `" . TblModCatalogProp . "` WHERE 1 ";
        if ($front_back == 'front')
            $q = $q . " AND `visible`='2'";
        if(isset($this->propStronThisPropPanel) and !empty($this->propStronThisPropPanel))
            $q.=" AND `".TblModCatalogProp."`.`id` NOT IN (".$this->propStronThisPropPanel.") AND `".TblModCatalogProp."`.`id`<>'".$this->id_prop."'";
        $q = $q . "GROUP BY id_cat";

        $res = $this->db->db_Query($q);
        //echo '<br> $q='.$q.' <br/>$res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res OR !$this->db->result)
            return false;

        $rows = $this->db->db_GetNumRows($res);
        $count = array();
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc($res);
            $count[$row['id_cat']] = $row['count'];
        }
        return $count;
    }

//end of function GetArrayContentCount()
    // ================================================================================================
    // Function : GetRelatCategs()
    // Date : 01.05.2007
    // Parms :   $level      -  id of the category
    // Returns : true,false / Void
    // Description : return array with relations (similar) categories
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetRelatCategs($level = NULL) {
        $q = "SELECT * FROM `" . TblModCatalogRelat . "` WHERE (`id_cat1`='" . $level . "' OR `id_cat2`='" . $level . "') ORDER BY `move` asc";
        $res = $this->db->db_Query($q);
        //echo '<br> $q='.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res OR !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        $arr_row = NULL;
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc();
            $arr_row[$i]['id'] = $row['id'];
            $arr_row[$i]['id_cat1'] = $row['id_cat1'];
            $arr_row[$i]['id_cat2'] = $row['id_cat2'];
            //echo '<br> $row[id]='.$row['id'].' $arr_row['.$i.']='.$arr_row[$i];
        }
        //echo '<br> $arr_row='.$arr_row;
        return $arr_row;
    }

//end of function GetRelatCategs()
    // ================================================================================================
    // Function : GetRelatProp()
    // Date : 08.05.2007
    // Parms :   $id -  id of the position in catalogue
    // Returns : true,false / Void
    // Description : return array with relations (similar) positions in catalogue
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetRelatProp($id = NULL, $tbl = TblModCatalogPropRelat, $front = false) {

        //похожие товары
        if($tbl!=TblModCatalogPropRelat ){
            $q = "SELECT * FROM `" . $tbl . "` WHERE (`id_prop1`='" . $id . "' AND `id_prop2`!='0') ORDER BY `move` asc";
        }
        //аксессуары для фронта
        elseif($tbl==TblModCatalogPropRelat && $front){
            $q = "SELECT * FROM `" . $tbl . "` WHERE (`id_prop1`='" . $id . "' AND `id_prop2`!='0') ORDER BY `move` asc";
        }//акксесуары для бекенда
        else{
            $q = "SELECT * FROM `" . $tbl . "` WHERE (`id_prop1`='" . $id . "' OR `id_prop2`='" . $id . "') ORDER BY `move` asc";
        }

        $res = $this->db->db_Query($q);

        //echo '<br/>'.__FILE__.' line: '.__LINE__;
        //echo '<br> $q='.$q.' $res='.$res;
        if (!$res OR !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        $arr_row = NULL;
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc();
            $arr_row[$i]['id'] = $row['id'];
            $arr_row[$i]['id_prop1'] = $row['id_prop1'];
            $arr_row[$i]['id_prop2'] = $row['id_prop2'];
            //var_dump($tbl);
            if($tbl!=TblModCatalogPropRelat && $tbl!=TblModCatalogPropRelat2 && $tbl!=TblModCatalogPropRelatForCateg ){
                $arr_row[$i]['count'] = $row['count'];
            }
            //echo '<br> $row[id]='.$row['id'].' $arr_row['.$i.']='.$arr_row[$i];
        }
        //echo '<br> $arr_row='.$arr_row;

        //var_dump($arr_row);
        return $arr_row;
    }

//end of function GetRelatProp()
    // ================================================================================================
    // Function : GetRelatPropAsIndex()
    // Date : 08.05.2007
    // Parms :   $id -  id of the position in catalogue
    // Returns : true,false / Void
    // Description : return array with relations (similar) positions in catalogue
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetRelatPropAsIndex($id = NULL) {
        $q = "SELECT * FROM `" . TblModCatalogPropRelat . "` WHERE (`id_prop1`='" . $id . "' OR `id_prop2`='" . $id . "') ORDER BY `move` asc";
        $res = $this->db->db_Query($q);
        //echo '<br> $q='.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res OR !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        $arr_row = NULL;
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc();
            $arr_row[$row['id_prop1']] = $row;
            $arr_row[$row['id_prop2']] = $row;
            //echo '<br> $row[id]='.$row['id'].' $arr_row['.$i.']='.$arr_row[$i];
        }
        //echo '<br> $arr_row='.$arr_row;
        return $arr_row;
    }

//end of function GetRelatPropAsIndex()
//------------------------------------------------------------------------------------------------------------
//---------------------------------- FUNCTION FOR PARAMETERS -------------------------------------------------
//------------------------------------------------------------------------------------------------------------

    /**
     * Class method GetListNameOfParamVal
     * get list of param values on needed language
     * @param $id_cat - id of the category of catalog
     * @param $id_param - id of the parameter for category $id_cat
     * @param $cod - cod of the parameter
     * @param $lang_id - id of the language
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.0, 10.11.2011
     */
    function GetNameOfParamVal($id_cat, $id_param, $cod, $lang_id = _LANG_ID) {
        $tmp_db = DBs::getInstance();
        $q = "SELECT `name` FROM `" . TblModCatalogParamsVal . "`
             WHERE `lang_id`='" . $lang_id . "'
             AND `id_cat`='" . $id_cat . "'
             AND `id_param`='" . $id_param . "'
             AND `cod`='" . $cod . "'
            ";

        $res = $tmp_db->db_Query($q);
        //echo '<br> $q='.$q.'  $DB_tmp->result='.$DB_tmp->result;
        if (!$res OR !$tmp_db->result)
            return false;
        $row = $tmp_db->db_FetchAssoc();
        return $row['name'];
    }

    /**
     * Class method GetListNameOfParamVal
     * get list of param values on needed language
     * @param $id_cat - id of the category of catalog
     * @param $id_param - id of the parameter for category $id_cat
     * @param $lang_id - id of the language
     * @param $return_type - return data in string or in array. Can be 'str' or 'array';
     * @param $sort - field for sort result
     * @param $asc_desc - type of sort (asc or desc)
     * @param $return_data - what data to return. It can be one specific filea or 'all'
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.0, 10.11.2011
     */
    function GetListNameOfParamVal($id_cat, $id_param, $lang_id = _LANG_ID, $return_type = 'str', $sort = 'move', $asc_desc = 'asc', $return_data = 'all') {
        $tmp_db = DBs::getInstance();
        $q = "SELECT * FROM `" . TblModCatalogParamsVal . "`
             WHERE `lang_id`='" . $lang_id . "'
             AND `id_cat`='" . $id_cat . "'
             ANd `id_param`='" . $id_param . "'
             ORDER BY `" . $sort . "` " . $asc_desc;

        $res = $tmp_db->db_Query($q);
        //echo '<br> $q='.$q.'  $DB_tmp->result='.$DB_tmp->result;
        if (!$res OR !$tmp_db->result)
            return false;
        $rows = $tmp_db->db_GetNumRows();
        //echo '<br> rows='.$rows;
        //if (!$rows) return $this->Msg->show_text('_VALUE_NOT_SET');
        $retstr = NULL;
        for ($i = 0; $i < $rows; $i++) {
            $row = $tmp_db->db_FetchAssoc();
            if ($return_type == 'str') {
                if (!$retstr)
                    $retstr = stripslashes($row['name']) . '<br>';
                else
                    $retstr = $retstr . stripslashes($row['name']) . '<br>';
            }
            else {
                switch ($return_data) {
                    case 'cod':
                        $retstr[$row[$sort]] = stripslashes($row['cod']);
                        break;
                    case 'name':
                        $retstr[$row[$sort]] = stripslashes($row['name']);
                        break;
                    case 'short':
                        $retstr[$row[$sort]] = stripslashes($row['short']);
                        break;
                    case 'img':
                        $retstr[$row[$sort]] = $this->GetImageByCodOnLang($Table, $row['cod'], $lang_id);
                        break;
                    case 'move':
                        $retstr[$row[$sort]] = $row['move'];
                        break;
                    default:
                        $retstr[$row[$sort]]['cod'] = stripslashes($row['cod']);
                        $retstr[$row[$sort]]['name'] = stripslashes($row['name']);
                        $retstr[$row[$sort]]['short'] = stripslashes($row['short']);
                        $retstr[$row[$sort]]['img'] = $this->GetImageByCodOnLang($Table, $row['cod'], $lang_id);
                        break;
                }
            }
        }
        if ($return_type == 'array') {
            if ($asc_desc == 'asc')
                ksort($retstr);
            if ($asc_desc == 'desc')
                krsort($retstr);
        }
        return $retstr;
    }

// end of function GetListNameOfParamVal()

    /**
     * Class method ShowParamsValInComboBoxWithShortName
     * show the list of the records from table to combobox
     * @param $id_cat - id of the category of catalog
     * @param $id_param - id of the parameter for category $id_cat
     * @param $name_fld - name of field
     * @param $val - value seleced by default
     * @param $width - width of SELECT-field
     * @param $default_val - default value
     * @param $sort_name - sortation of a list by which field
     * @param $asc_desc - type of sort (asc or desc)
     * @param $short_name_position - position of short name. It can be: left or right
     * @param $divider - divider between short name and full name
     * @param $params - additional parameters for combobox
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.0, 10.11.2011
     */
    function ShowParamsValInComboBoxWithShortName($id_cat, $id_param, $name_fld, $val, $width = '250', $default_val = '&nbsp;', $sort_name = 'move', $asc_desc = 'asc', $short_name_position = 'left', $divider = ' ', $params = NULL) {
        if (empty($name_fld))
            $name_fld = $Table;

        $tmp_db = DBs::getInstance();
        $q = "SELECT *
            FROM `" . TblModCatalogParamsVal . "`
            WHERE `lang_id`='" . _LANG_ID . "'
            AND `id_cat`='" . $id_cat . "'
            AND `id_param`='" . $id_param . "'
            ORDER BY `" . $sort_name . "` " . $asc_desc;
        $res = $tmp_db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res OR !$tmp_db->result)
            return false;
        $rows = $tmp_db->db_GetNumRows();

        $mas_spr[''] = $default_val;
        for ($i = 0; $i < $rows; $i++) {
            $row_spr = $tmp_db->db_FetchAssoc();
            if (!empty($row_spr['short'])) {
                if ($short_name_position == 'left')
                    $mas_spr[$row_spr['cod']] = stripslashes($row_spr['short']) . $divider . stripslashes($row_spr['name']);
                else
                    $mas_spr[$row_spr['cod']] = stripslashes($row_spr['name']) . $divider . stripslashes($row_spr['short']);
            }
            else
                $mas_spr[$row_spr['cod']] = stripslashes($row_spr['name']);
        }
        $this->Form->Select($mas_spr, $name_fld, $val, $width, $params);
    }

//end of fuinction ShowParamsValInComboBoxWithShortName()

    /**
     * Class method ShowParamValInCheckBox
     * show the list of the records from table to checkbox
     * @param $id_cat - id of the category of catalog
     * @param $id_param - id of the parameter for category $id_cat
     * @param $name_fld - name of field
     * @param $cols - count of checkboxes in one line
     * @param $val - value seleced by default
     * @param $position - position of the combo box ( "right" - right from tite, "left" - left from title)
     * @param $disabled -
     * @param $sort_name - sortation of a list by which field
     * @param $asc_desc - type of sort (asc or desc)
     * @param $show_sublevels - show sublevel or not
     * @param $level - level of position
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.0, 10.11.2011
     */
    function ShowParamValInCheckBox($id_cat, $id_param, $name_fld, $cols, $val, $position = "left", $disabled = NULL, $sort_name = 'move', $asc_desc = 'asc', $show_sublevels = 0, $level = NULL) {
        $row1 = NULL;
        $tmp_db = DBs::getInstance();
        $q = "SELECT *
            FROM `" . TblModCatalogParamsVal . "`
            WHERE `lang_id`='" . _LANG_ID . "'
            AND `id_cat`='" . $id_cat . "'
            AND `id_param`='" . $id_param . "'
           ";
        //echo '<br>$level='.$level;
        if ($tmp_db->IsFieldExist(TblModCatalogParamsVal, 'level'))
            $q = $q . " AND `level`='" . $level . "'";
        $q = $q . " ORDER BY `$sort_name` $asc_desc";
        //echo '<br>$q='.$q;
        $res = $tmp_db->db_Query($q);
        if (!$res)
            return false;
        $rows = $tmp_db->db_GetNumRows();
        $arr_data = array();
        for ($i = 0; $i < $rows; $i++) {
            $row000 = $tmp_db->db_FetchAssoc();
            $arr_data[$i] = $row000;
        }

        $col_check = 1;
        ?>
        <table border="0" cellpadding="1" cellspacing="1" align="left" class="checkbox_tbl">
            <tr>
                                <?
                                for ($i = 0; $i < $rows; $i++) {
                                    $row1 = $arr_data[$i];
                                    if ($col_check > $cols) {
                                        ?></tr><tr><?
                                        $col_check = 1;
                                    }

                                    $checked = '>';
                                    if (is_array($val)) {
                                        if (isset($val))
                                            foreach ($val as $k => $v) {
                                                if (isset($k) and ($v == $row1['cod']))
                                                    $checked = " checked" . $checked;
                                                //echo '<br>$k='.$k.' $v='.$v.' $row1[cod]='.$row1['cod'];
                                            }
                                    }
                                    if ($position == "left")
                                        $align = 'left';
                                    else
                                        $align = 'right';
                                    ?><td align="<?= $align ?>" valign="top" class="checkbox"><?
            if ($position == "left") {
                //echo "<table border='0' cellpadding='1' cellspacing='0'><tr><td><input class='checkbox' type='checkbox' name='".$name_fld."[]' value='".$row1['cod']."' ".$disabled." ".$checked.'</td><td>'.stripslashes($row1['name']).'</td></tr></table>';
                                        ?>
                            <table border="0" cellpadding="1" cellspacing="0">
                                <tr>
                                    <td valign="top"><input class="checkbox" type="checkbox" name="<?= $name_fld; ?>[]" value="<?= $row1['cod']; ?>" <?= $disabled; ?> <?= $checked; ?> </td>
                                    <td class="checkbox_td"><?= stripslashes($row1['name']); ?></td>
                                </tr>
                            </table>
                <?
            } else {
                //echo stripslashes($row1['name'])."<input class='checkbox' type='checkbox' name='".$name_fld."[]' value='".$row1['cod']."' ".$disabled." ".$checked;
                ?>
                            <table border="0" cellpadding="1" cellspacing="0">
                                <tr>
                                    <td valign="top">
                                    <td class="checkbox_td"><?= stripslashes($row1['name']); ?></td>
                                    <td><input class="checkbox" type="checkbox" name="<?= $name_fld; ?>[]" value="<?= $row1['cod']; ?>" <?= $disabled; ?> <?= $checked; ?> </td>
                                </tr>
                            </table>
                <?
            }


            //======= show sublevels START ===========
            if ($show_sublevels == 1) {
                ?>
                            <table border="0" cellpadding="1" cellspacing="0">
                                <tr>
                                    <td style="padding:0px 0px 0px 20px;"><?
                $this->ShowInCheckBox($Table, $name_fld, 1, $val, $position, $disabled, $sort_name, $asc_desc, $show_sublevels, $row1['cod']);
                ?>
                                    </td>
                                </tr>
                            </table>
                <?
            }
            //======= show sublevels END ===========
            ?></td><?
            $col_check++;
        }
        ?>
            </tr>
        </table>
        <?
    }

//end of fuinction ShowParamValInCheckBox
    // ================================================================================================
    // Function : IsParams()
    // Date : 14.04.2006
    // Parms : $level - level of menu  (0 - first level)
    //         $use_parent_params - use or not parameters from parent categories
    // Returns : true,false / Void
    // Description : check exist or not parameters for current category
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function IsParams($level = 0, $use_parent_params = 1) {
        $rows = NULL;
        if ($use_parent_params == 1)
            $arr_top_levels = $this->get_top_levels_in_array($level, NULL);
        else
            if(!empty($level))$arr_top_levels[$level] = '';
//        var_dump($arr_top_levels);
        foreach ($arr_top_levels as $v => $k) {
            $q = "SELECT * FROM `" . TblModCatalogParams . "` WHERE `id_cat`='" . $v . "'";
            $res = $this->db->db_Query($q);
            //echo '<br> $q='.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result;
            if (!$res || !$this->db->result) return false;
            $rows = $rows + $this->db->db_GetNumRows();
        }
        return $rows;
    }

//end of function  IsParams()
    // ================================================================================================
    // Function : GetCategoryByIdParam()
    // Date : 01.06.2007
    // Parms : $id_param  - id of the parameter
    // Returns : true,false / Void
    // Description : return id of the category for parameter
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetCategoryByIdParam($id_param) {
        $q = "SELECT `id_cat` FROM `" . TblModCatalogParams . "` WHERE `id`='" . $id_param . "'";
        $res = $this->db->db_Query($q);
        //echo '<br> $q='.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res OR !$this->db->result)
            return false;
        $row = $this->db->db_FetchAssoc();
        return $row['id_cat'];
    }

//end of function  GetCategoryByIdParam()
    // ================================================================================================
    // Function : GetParams()
    // Date : 18.04.2006
    // Parms : $level - level of menu  (0 - first level)
    //         $use_parent_params - use or not parameters from parent categories
    // Returns : true,false / Void
    // Description : return all parameters for current category
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetParams($id_cat, $use_parent_params = 1) {


        if ($use_parent_params == 1)
            $arr_top_levels = $this->get_top_levels_in_array($id_cat, NULL);
        else
            $arr_top_levels[$id_cat] = '';

        $keys = array_keys($arr_top_levels);

        $cnt = count($keys);


        for ($k = 0; $k < $cnt; $k++) {
            $v = $keys[$k];
            $q = "SELECT `" . TblModCatalogParams . "`.*,
                   `" . TblModCatalogParamsSprName . "`.`name`,
                   `" . TblModCatalogParamsSprPrefix . "`.`name` AS `prefix`,
                   `" . TblModCatalogParamsSprSufix . "`.`name` AS `sufix`,
                   `" . TblModCatalogParamsSprDescr . "`.`name` AS `descr`,
                   `" . TblModCatalogParamsSprMTitle . "`.`name` AS `mtitle`,
                   `" . TblModCatalogParamsSprMDescr . "`.`name` AS `mdescr`,
                   `" . TblModCatalogParamsSprMKeywords . "`.`name` AS `mkeywords`
                 FROM `" . TblModCatalogParams . "`
                 LEFT JOIN `" . TblModCatalogParamsSprName . "` ON (`" . TblModCatalogParams . "`.`id` = `" . TblModCatalogParamsSprName . "`.`cod` AND `" . TblModCatalogParamsSprName . "`.`lang_id`='" . $this->lang_id . "')
                 LEFT JOIN `" . TblModCatalogParamsSprPrefix . "` ON (`" . TblModCatalogParams . "`.`id` = `" . TblModCatalogParamsSprPrefix . "`.`cod` AND `" . TblModCatalogParamsSprPrefix . "`.`lang_id`='" . $this->lang_id . "')
                 LEFT JOIN `" . TblModCatalogParamsSprSufix . "` ON (`" . TblModCatalogParams . "`.`id` = `" . TblModCatalogParamsSprSufix . "`.`cod` AND `" . TblModCatalogParamsSprSufix . "`.`lang_id`='" . $this->lang_id . "')
                 LEFT JOIN `" . TblModCatalogParamsSprDescr . "` ON (`" . TblModCatalogParams . "`.`id` = `" . TblModCatalogParamsSprDescr . "`.`cod` AND `" . TblModCatalogParamsSprDescr . "`.`lang_id`='" . $this->lang_id . "')
                 LEFT JOIN `" . TblModCatalogParamsSprMTitle . "` ON (`" . TblModCatalogParams . "`.`id` = `" . TblModCatalogParamsSprMTitle . "`.`cod` AND `" . TblModCatalogParamsSprMTitle . "`.`lang_id`='" . $this->lang_id . "')
                 LEFT JOIN `" . TblModCatalogParamsSprMDescr . "` ON (`" . TblModCatalogParams . "`.`id` = `" . TblModCatalogParamsSprMDescr . "`.`cod` AND `" . TblModCatalogParamsSprMDescr . "`.`lang_id`='" . $this->lang_id . "')
                 LEFT JOIN `" . TblModCatalogParamsSprMKeywords . "` ON (`" . TblModCatalogParams . "`.`id` = `" . TblModCatalogParamsSprMKeywords . "`.`cod` AND `" . TblModCatalogParamsSprMKeywords . "`.`lang_id`='" . $this->lang_id . "')
                 WHERE `" . TblModCatalogParams . "`.`id_cat`='" . $v . "'
                 ORDER BY `" . TblModCatalogParams . "`.`move`";
            $res = $this->db->db_Query($q);
//          echo '<br> $q=' . $q . ' $res=' . $res . ' $this->db->result=' . $this->db->result;
            if (!$res OR !$this->db->result)
                return false;
            $rows = $this->db->db_GetNumRows();
            //echo '<br>$rows=' . $rows;

            if (!isset($j))
                $j = 0;
            if (isset($i)) {
                $j = $i;
                $rows = $rows + $j;
            }

            for ($i = $j; $i < $rows; $i++) {
                $row[$i] = $this->db->db_FetchAssoc();
                $row[$i]['id_categ'] = $v;
                //echo '<br>$row[' . $i . ']=' . $row[$i];
            }
        }
        //echo '<br> $row[type]='.$row['type'];
        if (!isset($row))
            $row = NULL;
        //var_dump($row);
        return $row;
    }

//end of function  GetParams()
    // ================================================================================================
    // Function : GetParamAllValues()
    // Date : 12.10.2006
    // Parms :   $params_row - array with data about parameter
    //           $type - type of returned value. Can be: "str" or "array"
    // Returns : true,false / Void
    // Description : return all values of parameter
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetParamAllValues($params_row, $type = 'array') {
        $divider = '<br>';
        $tblname = $this->BuildNameOfValuesTable($params_row['id_categ'], $params_row['id']);

        $prefix = $this->Spr->GetNameByCod(TblModCatalogParamsSprPrefix, ($params_row['id']), $this->lang_id, 1);
        $sufix = $this->Spr->GetNameByCod(TblModCatalogParamsSprSufix, ($params_row['id']), $this->lang_id, 1);

        switch ($params_row['type']) {
            case '1':
                $val = NULL;
                break;
            case '2':
                //$val = $this->Spr->GetNameByCod(TblSysLogic,$val, $this->lang_id, 1);
                $val = $this->Spr->GetListName(TblSysLogic, $this->lang_id, 'array');
                break;
            case '3':
                //$val = $this->Spr->GetNameByCod($tblname,$val, $this->lang_id, 1);
                $val = $this->Spr->GetListName($tblname, $this->lang_id, 'array');
                break;
            case '4':
                // $val = $this->Spr->GetNamesInStr( $tblname, $this->lang_id, $val, ',' );
                $val = $this->Spr->GetListName($tblname, $this->lang_id, 'array');
                break;
            case '5':
                $val = NULL;
                break;
        }

        if ($type == 'str') {
            $tmp_str = $this->Spr->GetNameByCod(TblModCatalogParamsSprName, $params_row['id'], $this->lang_id, 1) . ': ' . $prefix . ' ' . $val . ' ' . $sufix;
            if (empty($str))
                $str = $tmp_str;
            else
                $str = $str . $divider . $tmp_str;
        }
        else {
            $str["param_name"] = $this->Spr->GetNameByCod(TblModCatalogParamsSprName, $params_row['id'], $this->lang_id, 1);
            $str["param_prefix"] = $prefix;
            $str["param_value"] = $val;
            $str["param_sufix"] = $sufix;
        }
        //echo '<br> $str='.$str;
        return $str;
    }

//end of function  GetParamAllValues()

    /*
      // ================================================================================================
      // Function : BuildNameOfValuesTable()
      // Date : 17.04.2006
      // Parms :
      //           $id = 0  - level of menu  (0 - first level)
      // Returns : true,false / Void
      // Description : build name of table where store values of parameter
      // Programmer : Igor Trokhymchuk
      // ================================================================================================
      function BuildNameOfValuesTable($id_cat = NULL, $id = NULL)
      {
      if ( !$id_cat ) $id_cat = $this->id_cat;
      if ( !$id ) $id = $this->id;
      return TblModCatalogParamsSPR.$id_cat.'_'.$id;
      } //end of function  BuildNameOfValuesTable()
     */

    // ================================================================================================
    // Function : GetTypeOfParam()
    // Date : 14.04.2006
    // Parms : $id - id of the parameter
    // Returns : true,false / Void
    // Description : return type of parameter
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetTypeOfParam($id) {
        $q = "SELECT `type` FROM `" . TblModCatalogParams . "` WHERE `id`='" . $id . "'";
        $res = $this->db->db_Query($q);
        //echo '<br> $q='.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res)
            return false;
        if (!$this->db->result)
            return false;
        $row = $this->db->db_FetchAssoc();
        //echo '<br> $row[type]='.$row['type'];
        return $row['type'];
    }

//end of function  GetTypeOfParam()

    /*
      // ================================================================================================
      // Function : IsParamsValues()
      // Date : 14.04.2006
      // Parms :
      // Returns : true,false / Void
      // Description : check exist or not values for selected parameter
      // Programmer : Igor Trokhymchuk
      // ================================================================================================
      function IsParamsValues( $id_cat, $id )
      {
      //$tblname = $this->BuildNameOfValuesTable($id_cat, $id);
      $rows = $this->Spr->GetCountValuesInSprOnLang( TblModCatalogParamsVal, $this->lang_id );
      return $rows;
      } //end of function  IsParamsValues()
     */

    // ================================================================================================
    // Function : SaveParamsValuesOfProp()
    // Date : 18.04.2006
    // Parms :
    // Returns : true,false / Void
    // Description : save parameters data of current position in catalogue to the table
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function SaveParamsValuesOfProp($id = NULL, $id_cat = NULL) {
        $params_row = $this->GetParams($id_cat);
        //print_r($this->arr_params);
        for ($i = 0; $i < count($params_row); $i++) {
            $q = "select * from `" . TblModCatalogParamsProp . "`
               where `id_prop`='" . $id . "' AND `id_param`='" . $params_row[$i]['id'] . "' order by `id`";
            $res = $this->db->db_Query($q);
            //echo '<br>q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
            if (!$res OR !$this->db->result)
                return false;
            $rows = $this->db->db_GetNumRows();
            if ($rows > 0) {
                if ($params_row[$i]['type'] == '4') {
                    $q = "DELETE FROM `" . TblModCatalogParamsProp . "` WHERE `id_prop`='" . $id . "' AND `id_param`='" . $params_row[$i]['id'] . "'";
                    $res = $this->db->db_Query($q);
                    //echo '<br>q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
                    if (!$res OR !$this->db->result)
                        return false;
                    if (isset($this->arr_params[$params_row[$i]['id']])) {
                        for ($j = 0; $j < count($this->arr_params[$params_row[$i]['id']]); $j++) {
                            $q = "INSERT INTO `" . TblModCatalogParamsProp . "` SET
                              `id_prop` = '" . $id . "',
                              `id_param` = '" . $params_row[$i]['id'] . "',
                              `val` = '" . addslashes($this->arr_params[$params_row[$i]['id']][$j]) . "'";
                            $res = $this->db->db_Query($q);
                            //echo '<br>q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
                            if (!$res OR !$this->db->result)
                                return false;
                        }
                    }
                }
                else {
                    if ($params_row[$i]['type'] == '2') {
                        if (!isset($this->arr_params[$params_row[$i]['id']]))
                            $val = 0;
                        else
                            $val = 1;
                    }else{
                        $val = addslashes($this->arr_params[$params_row[$i]['id']]);
                    }
                    $q = "UPDATE `" . TblModCatalogParamsProp . "` SET
                          `val`='" . $val . "'
                          WHERE `id_prop`='" . $id . "'
                          AND `id_param`='" . $params_row[$i]['id'] . "'";
                    $res = $this->db->db_Query($q);
                    //echo '<br>q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
                    if (!$res OR !$this->db->result)
                        return false;
                }
            }
            else {
                if ($params_row[$i]['type'] == '4') {
                    if (isset($this->arr_params[$params_row[$i]['id']])) {
                        for ($j = 0; $j < count($this->arr_params[$params_row[$i]['id']]); $j++) {
                            $q = "INSERT INTO `" . TblModCatalogParamsProp . "` SET
                              `id_prop` = '" . $id . "',
                              `id_param` = '" . $params_row[$i]['id'] . "',
                              `val` = '" . addslashes($this->arr_params[$params_row[$i]['id']][$j]) . "'";
                            $res = $this->db->db_Query($q);
                            //echo '<br>q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
                            if (!$res OR !$this->db->result)
                                return false;
                        }
                    }
                }
                else {
                    if ($params_row[$i]['type'] == '2') {
                        if (!isset($this->arr_params[$params_row[$i]['id']]))
                            $val = 0;
                        else
                            $val = 1;
                    }else{
                        $val = addslashes($this->arr_params[$params_row[$i]['id']]);
                    }
                    $q = "INSERT INTO `" . TblModCatalogParamsProp . "` SET
                          `id_prop` = '" . $id . "',
                          `id_param` = '" . $params_row[$i]['id'] . "',
                          `val` = '" . $val . "'";
                    $res = $this->db->db_Query($q);
                    //echo '<br>q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
                    if (!$res OR !$this->db->result)
                        return false;
                }
            }//end if
        }//end for
        return true;
    }

//end of function  SaveParamsValuesOfProp()
    // ================================================================================================
    // Function : GetParamsValuesOfProp()
    // Date : 18.04.2006
    // Parms :
    // Returns : true,false / Void
    // Description : return values of parameters for current position in catalogue
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetParamsValuesOfProp($id) {
        $q = "SELECT `" . TblModCatalogParamsProp . "`.*,
        `" . TblModCatalogParams . "`.`type`
         FROM
                `" . TblModCatalogParamsProp . "`
                LEFT JOIN `" . TblModCatalogParams . "` ON (`" . TblModCatalogParamsProp . "`.`id_param`=`" . TblModCatalogParams . "`.`id`)
             WHERE `id_prop`='" . $id . "' order by `id`";
        $res = $this->db->db_Query($q);
//        echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res)
            return false;
        if (!$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        $tmp_arr = NULL;
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc();
            if ($row['type'] == '4') {
                $tmp_arr[$row['id_param']][] = $row['val'];
                //echo '<br> $tmp_arr['.$row['id_param'].']['.$j.']='.$tmp_arr[$row['id_param']][$j];
            } else {
                $tmp_arr[$row['id_param']] = $row['val'];
                $j = 0;
            }
            //echo '<br> $tmp_arr['.$row['id_param'].']='.$tmp_arr[$row['id_param']];
        }
        return $tmp_arr;
    }

    /**
     * Class method GetNameOfParamMultiplesVal
     * get list of param values on needed language
     * @param $id_cat - id of the category of catalog
     * @param $id_param - id of the parameter for category $id_cat
     * @param $cod - cod of the parameter
     * @param $lang_id - id of the language
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.0, 10.11.2011
     */
    function GetNameOfParamMultiplesVal($id_cat, $id_param, $cod, $lang_id = _LANG_ID) {
        $tmp_db = DBs::getInstance();
        $cod=implode(',',$cod);
        $q = "SELECT `name` FROM `" . TblModCatalogParamsVal . "`
             WHERE `lang_id`='" . $lang_id . "'
             AND `id_cat`='" . $id_cat . "'
             AND `id_param`='" . $id_param . "'
             AND `cod` IN (" . $cod . ")
            ";

        $res = $tmp_db->db_Query($q);
//        echo '<br> $q='.$q.'  $DB_tmp->result='.$DB_tmp->result;
        if (!$res OR !$tmp_db->result)
            return false;
        $rows=$tmp_db->db_GetNumRows();
        $str='';
        for ($i = 0; $i < $rows; $i++) {
            $row = $tmp_db->db_FetchAssoc();
            if($i==0)
                $str.=$row['name'];
            else
                $str.=', '.$row['name'];
        }
        return $str;
    }

//end of function  GetParamsValuesOfProp()
    // ================================================================================================
    // Function : GetParamValue()
    // Date : 01.06.2007
    // Parms : $id_param - id of the parameter
    //         $id_prop  - id of the position of catalog
    // Returns : true,false / Void
    // Description : return values of parameter with id=$id_param
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetParamValue($id_param, $id_prop) {
        $q = "SELECT * FROM `" . TblModCatalogParamsProp . "` WHERE `id_param`='" . $id_param . "' AND `id_prop`='" . $id_prop . "'";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res OR !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        $tblname = $this->BuildNameOfValuesTable($this->GetCategoryByIdParam($id_param), $id_param);
        $tmp_arr = NULL;
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc();
            //echo '<br>$row[val]='.$row['val'].' $tblname='.$tblname;
            switch ($this->GetTypeOfParam($row['id_param'])) {
                case '1':
                    $tmp_arr[$row['id_param']] = $row['val'];
                    break;
                case '2':
                    $tmp_arr[$row['id_param']] = $this->Spr->GetNameByCod(TblSysLogic, $row['val'], $this->lang_id, 1);
                    break;
                case '3':
                    $tmp_arr[$row['id_param']] = $this->Spr->GetNameByCod($tblname, $row['val'], $this->lang_id, 1);
                    break;
                case '4':
                    $tmp_arr[$row['id_param']][$i] = $this->Spr->GetNameByCod($tblname, $row['val'], $this->lang_id, 1); // $this->Spr->GetNamesInStr( $tblname, $this->lang_id, NULL, ',' );
                    break;
                case '5':
                    $tmp_arr[$row['id_param']] = $row['val'];
                    break;
            }
        }
        //echo '<br>$tmp_arr='.$tmp_arr; print_r($tmp_arr);
        return $tmp_arr;
    }

//end of function  GetParamValue()
    // ================================================================================================
    // Function : DelParamsValuesOfProp()
    // Date : 18.04.2006
    // Parms :
    // Returns : true,false / Void
    // Description : return values of parameters for current position in catalogue
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function DelParamsValuesOfProp($id) {
        $q = "DELETE FROM `" . TblModCatalogParamsProp . "` WHERE `id_prop`='" . $id . "'";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res)
            return false;
        if (!$this->db->result)
            return false;
        return true;
    }

//end of function DelParamsValuesOfProp()
    // ================================================================================================
    // Function : DelParamsValuesOfPropByIdparam()
    // Date : 18.04.2006
    // Parms :
    // Returns : true,false / Void
    // Description : return values of parameters for current position in catalogue
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function DelParamsValuesOfPropByIdparam($id) {
        $q = "DELETE FROM `" . TblModCatalogParamsProp . "` WHERE `id_param`='" . $id . "'";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res)
            return false;
        if (!$this->db->result)
            return false;
        return true;
    }

//end of function DelParamsValuesOfPropByIdparam()

    /**
     * Class method DelParamsByIdCategory
     * delete all parameters and it list of values for current category
     * @param $id - id of the category of catalog
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.0, 17.11.2011
     */
    function DelParamsByIdCategory($id) {
        $tmp_db = DBs::getInstance();

        $q = "SELECT * FROM `" . TblModCatalogParams . "` WHERE `id_cat`='" . $id . "'";
        $res = $tmp_db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res OR !$tmp_db->result)
            return false;
        $rows = $tmp_db->db_GetNumRows();
        for ($i = 0; $i < $rows; $i++) {
            $row = $tmp_db->db_FetchAssoc();
            /*
              if($row['type']==3 OR $row['type']==4){
              $tmp_db2 = new DB();
              $tblname = $this->BuildNameOfValuesTable($id, $row['id']);
              $q = "DROP TABLE `".$tblname."`";
              $res = $tmp_db2->db_Query( $q );
              //echo '<br>q='.$q.' res='.$res.' $tmp_db2->result='.$tmp_db2->result;
              if ( !$res OR !$tmp_db2->result ) return false;
              }
             */
            $res = $this->Spr->DelFromSpr(TblModCatalogParamsSprName, $row['id']);
            if (!$res)
                return false;
            $res = $this->Spr->DelFromSpr(TblModCatalogParamsSprPrefix, $row['id']);
            if (!$res)
                return false;
            $res = $this->Spr->DelFromSpr(TblModCatalogParamsSprSufix, $row['id']);
            if (!$res)
                return false;
            $res = $this->Spr->DelFromSpr(TblModCatalogParamsSprDescr, $row['id']);
            if (!$res)
                return false;
            $res = $this->Spr->DelFromSpr(TblModCatalogParamsSprMTitle, $row['id']);
            if (!$res)
                return false;
            $res = $this->Spr->DelFromSpr(TblModCatalogParamsSprMDescr, $row['id']);
            if (!$res)
                return false;
            $res = $this->Spr->DelFromSpr(TblModCatalogParamsSprMKeywords, $row['id']);
            if (!$res)
                return false;
        }

        //delete parameter list for current parameter
        /*
          $q = "DELETE FROM `".TblModCatalogParamsVal."` WHERE `id_cat`='".$id."'";
          $res = $tmp_db->db_Query( $q );
          //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
          if ( !$res OR !$tmp_db->result ) return false;
         */

        $q = "DELETE FROM `" . TblModCatalogParams . "`, `" . TblModCatalogParamsVal . "`
             USING `" . TblModCatalogParams . "`, `" . TblModCatalogParamsVal . "`
             WHERE `" . TblModCatalogParams . "`.`id_cat`='" . $id . "'
             AND `" . TblModCatalogParams . "`.`id_cat`=`" . TblModCatalogParamsVal . "`.`id_cat`";
        $res = $tmp_db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res OR !$tmp_db->result)
            return false;
        return true;
    }

//end offunction DelParamsByIdCategory()
    // ================================================================================================
    // Function : IsParamInfluenceOnImage()
    // Date : 25.07.2006
    // Parms :   $id_param - id of the parameter
    // Returns : true,false / Void
    // Description : is parameter influence on image of goods
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function IsParamInfluenceOnImage($id_param = NULL) {
        $q = "SELECT `is_img` FROM `" . TblModCatalogParams . "` WHERE `id`='" . $id_param . "'";
        $res = $this->db->db_Query($q);
        //echo '<br> $q='.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res)
            return false;
        if (!$this->db->result)
            return false;
        $row = $this->db->db_FetchAssoc();
        return $row['is_img'];
    }

//end of function  IsParamInfluenceOnImage()
    // ================================================================================================
    // Function : GetCountOfParamsInfluenceOnImage()
    // Date : 25.07.2006
    // Parms :   $id_param - id of the parameter
    // Returns : true,false / Void
    // Description : return count of parameters influence on image of goods
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetCountOfParamsInfluenceOnImage($id_cat = NULL) {
        $rows = NULL;
        $arr_top_levels = $this->get_top_levels_in_array($id_cat, NULL);
        if (is_array($arr_top_levels)) {
            foreach ($arr_top_levels as $v => $k) {
                $q = "SELECT COUNT(`is_img`) FROM `" . TblModCatalogParams . "` WHERE `id_cat`='" . $v . "' AND `is_img`=1";
                $res = $this->db->db_Query($q);
                //echo '<br> $q='.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result;
                if (!$res)
                    return false;
                if (!$this->db->result)
                    return false;
                $row = $this->db->db_FetchAssoc();
                $rows = $rows + $row['COUNT(`is_img`)'];
            }
        }
        return $rows;
    }

//end of function  GetCountOfParamsInfluenceOnImage()
    // ================================================================================================
    // Function : GetParamsInfluenceOnImage()
    // Date : 25.07.2006
    // Parms :   $id_param - id of the parameter
    // Returns : true,false / Void
    // Description : return parameters influence on image of goods
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetParamsInfluenceOnImage($id_cat = NULL) {
        $arr_top_levels = $this->get_top_levels_in_array($id_cat, NULL);
        //echo '<br>$arr_top_levels='.$arr_top_levels;
        foreach ($arr_top_levels as $v => $k) {
            $q = "SELECT * FROM `" . TblModCatalogParams . "` WHERE `id_cat`='" . $v . "' AND `is_img`=1 order by `move`";
            $res = $this->db->db_Query($q);
            //echo '<br> $q='.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result;
            if (!$res)
                return false;
            if (!$this->db->result)
                return false;
            $rows = $this->db->db_GetNumRows();
            if (!isset($j))
                $j = 0;
            if (isset($i)) {
                $j = $i;
                $rows = $rows + $j;
            }

            for ($i = $j; $i < $rows; $i++) {
                $row[$i] = $this->db->db_FetchAssoc();
                $row[$i]['id_categ'] = $v;
                //echo '<br>$row['.$i.']='.$row[$i];
            }
        }
        //echo '<br> $row[type]='.$row['type'];
        if (!isset($row))
            $row = NULL;
        return $row;
    }

//end of function  GetParamsInfluenceOnImage()
    // ================================================================================================
    // Function : GetParamsValuesOfPropForImg()
    // Date : 25.07.2006
    // Parms :   $id_param - id of the parameter
    // Returns : true,false / Void
    // Description : return values of current parameter for current image of goods
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetParamsValuesOfPropForImg($id_img = NULL, $id_param = NULL) {
        $q = "SELECT * FROM `" . TblModCatalogParamsPropImg . "` WHERE 1 AND `id_img`='" . $id_img . "'";
        if (!empty($id_param))
            $q = $q . " AND `id_param`='" . $id_param . "'";
        $q = $q . " order by `id`";
        $res = $this->db->db_Query($q);
//        echo '<br>q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
        if (!$res)
            return false;
        if (!$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        $tmp_arr = NULL;
        $j = 0;
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc();
            if ($this->GetTypeOfParam($row['id_param']) == '4') {
                $tmp_arr[$row['id_param']][$j] = $row['val'];
                //echo '<br> $tmp_arr['.$row['id_param'].']['.$j.']='.$tmp_arr[$row['id_param']][$j];
                $j++;
            } else {
                $tmp_arr[$row['id_param']] = $row['val'];
                $j = 0;
            }
            //echo '<br> $tmp_arr['.$row['id_param'].']='.$tmp_arr[$row['id_param']];
        }
        return $tmp_arr;
    }

//end of function  GetParamsValuesOfPropForImg()
    // ================================================================================================
    // Function : SaveParamsValuesOfPropForImg()
    // Date : 18.04.2006
    // Parms :
    // Returns : true,false / Void
    // Description : save parameters data of current position in catalogue to the table
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function SaveParamsValuesOfPropForImg($id) {
        //echo '<br> $params='.$params;
        $params_row = $this->GetParamsInfluenceOnImage($this->GetCategory($this->id));
        for ($i = 0; $i < count($params_row); $i++) {
            $q = "select * from `" . TblModCatalogParamsPropImg . "`
               where `id_img`='" . $id . "' AND `id_param`='" . $params_row[$i]['id'] . "' order by `id`";
            $res = $this->db->db_Query($q);
            //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
            if (!$res)
                return false;
            if (!$this->db->result)
                return false;
            $rows = $this->db->db_GetNumRows();
            if ($rows > 0) {
                if ($params_row[$i]['type'] == '4') {
                    $q = "DELETE FROM `" . TblModCatalogParamsPropImg . "` WHERE `id_img`='" . $id . "' AND `id_param`='" . $params_row[$i]['id'] . "'";
                    $res = $this->db->db_Query($q);
                    //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
                    if (!$res)
                        return false;
                    if (!$this->db->result)
                        return false;
                    if (isset($this->arr_params[$id][$params_row[$i]['id']])) {
                        for ($j = 0; $j < count($this->arr_params[$id][$params_row[$i]['id']]); $j++) {
                            $q = "INSERT into `" . TblModCatalogParamsPropImg . "` values(NULL,'" . $id . "','" . $params_row[$i]['id'] . "','" . addslashes($this->arr_params[$id][$params_row[$i]['id']][$j]) . "')";
                            $res = $this->db->db_Query($q);
                            //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
                            if (!$res)
                                return false;
                            if (!$this->db->result)
                                return false;
                        }
                    }
                }
                else {
                    $q = "UPDATE `" . TblModCatalogParamsPropImg . "` set
                    `val`='" . addslashes($this->arr_params[$id][$params_row[$i]['id']]) . "'
                    WHERE `id_img`='" . $id . "' AND `id_param`='" . $params_row[$i]['id'] . "'";
                    $res = $this->db->db_Query($q);
                    //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
                    if (!$res)
                        return false;
                    if (!$this->db->result)
                        return false;
                }
            }
            else {
                if ($params_row[$i]['type'] == '4') {
                    if (isset($this->arr_params[$id][$params_row[$i]['id']])) {
                        for ($j = 0; $j < count($this->arr_params[$id][$params_row[$i]['id']]); $j++) {
                            $q = "INSERT into `" . TblModCatalogParamsPropImg . "` values(NULL,'" . $id . "','" . $params_row[$i]['id'] . "','" . addslashes($this->arr_params[$id][$params_row[$i]['id']][$j]) . "')";
                            $res = $this->db->db_Query($q);
                            //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
                            if (!$res)
                                return false;
                            if (!$this->db->result)
                                return false;
                        }
                    }
                }
                else {
                    $q = "INSERT into `" . TblModCatalogParamsPropImg . "` values(NULL,'" . $id . "','" . $params_row[$i]['id'] . "','" . addslashes($this->arr_params[$id][$params_row[$i]['id']]) . "')";
                    $res = $this->db->db_Query($q);
                    //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
                    if (!$res)
                        return false;
                    if (!$this->db->result)
                        return false;
                }
            }
        }
        return true;
    }

//end of function  SaveParamsValuesOfPropForImg()
    // ================================================================================================
    // Function : IsImageInfluenceOnParams()
    // Date : 26.07.2006
    // Parms :   $id_img / id of the image for curent position
    // Returns : true,false / Void
    // Description : is image has fixed parameter value
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function IsImageInfluenceOnParams($id_img) {
        $q = "SELECT * FROM `" . TblModCatalogParamsPropImg . "` WHERE 1 AND `id_img`='" . $id_img . "'";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res)
            return false;
        if (!$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        for ($i = 0; $i < count($rows); $i++) {
            $row = $this->db->db_FetchAssoc();
            if (!empty($row['val']))
                return true;
        }
        return false;
    }

//end of function IsImageInfluenceOnParams()
    // ================================================================================================
    // Function : IsExistParamValueForImageOfCurrProp()
    // Date : 26.07.2006
    // Parms :   $id_param   / id of the parameter
    //           $key_val    / value of the parameter
    //           $id         / id of the current position
    // Returns : true,false / Void
    // Description : is exist one or more fixed value $key_val of the parameter $id_param in any image of current position $id
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function IsExistParamValueForImageOfCurrProp($id_param, $key_val, $id) {
        $q = "SELECT `" . TblModCatalogParamsPropImg . "`.id_img, `" . TblModCatalogPropImg . "`.id FROM `" . TblModCatalogParamsPropImg . "`, `" . TblModCatalogPropImg . "` WHERE 1 AND `" . TblModCatalogParamsPropImg . "`.`id_param`='" . $id_param . "' AND `" . TblModCatalogParamsPropImg . "`.`val`='" . $key_val . "' AND `" . TblModCatalogPropImg . "`.id_prop='" . $id . "' AND `" . TblModCatalogParamsPropImg . "`.id_img=`" . TblModCatalogPropImg . "`.id";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res)
            return false;
        if (!$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        if ($rows > 0)
            return true;
        else
            return false;
    }

//end of function IsExistParamValueForImageOfCurrProp()
    // ================================================================================================
    // Function : GetParamsValuesOfPropForImgInStr()
    // Date : 26.07.2006
    // Parms :   $id_img     / id of the image (for image influence on parameters)
    //           $divider    / symbol to divide parameters one from one. (default devider is <br>)
    //           $type       / can be 'array ' or 'str'
    // Returns : true,false / Void
    // Description : return values of parameters in string for current image of catalogue
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetParamsValuesOfPropForImgInStr($id_img, $divider = '<br>', $type = 'array') {
        $params_row = $this->GetParamsInfluenceOnImage($this->id_cat);
        //echo '<br>$params_row1='; print_r($params_row);

        $value_param_img = $this->GetParamsValuesOfPropForImg($id_img, NULL);
        $str = NULL;
        for ($i = 0; $i < count($params_row); $i++) {
            $tblname = $this->BuildNameOfValuesTable($params_row[$i]['id_categ'], $params_row[$i]['id']);

            isset($value_param_img[$params_row[$i]['id']]) ? $val_from_table = $value_param_img[$params_row[$i]['id']] : $val_from_table = NULL;
            $val = $val_from_table;

            $prefix = $this->Spr->GetNameByCod(TblModCatalogParamsSprPrefix, ($params_row[$i]['id']), $this->lang_id, 1);
            $sufix = $this->Spr->GetNameByCod(TblModCatalogParamsSprSufix, ($params_row[$i]['id']), $this->lang_id, 1);
            switch ($params_row[$i]['type']) {
                case '1':
                    $val = $val;
                    break;
                case '2':
                    $val = $this->Spr->GetNameByCod(TblSysLogic, $val, $this->lang_id, 1);
                    break;
                case '3':
                    $val = $this->Spr->GetNameByCod($tblname, $val, $this->lang_id, 1);
                    break;
                case '4':
                    $val = $this->Spr->GetNamesInStr($tblname, _LANG_ID, $val, ',');
                    break;
                case '5':
                    $val = $val;
                    break;
            }
            if ($type == 'str') {
                if (!empty($val)) {
                    $tmp_str = '<b>' . $this->Spr->GetNameByCod(TblModCatalogParamsSprName, $params_row[$i]['id'], $this->lang_id, 1) . '</b>: ' . $prefix . ' ' . $val . ' ' . $sufix;
                    if (empty($str))
                        $str = $tmp_str;
                    else
                        $str = $str . $divider . $tmp_str;
                }
            }
            else {
                $str["param_name"] = $this->Spr->GetNameByCod(TblModCatalogParamsSprName, $params_row[$i]['id'], $this->lang_id, 1);
                $str["param_prefix"] = $prefix;
                $str["param_value"] = $val;
                $str["param_sufix"] = $sufix;
            }
        }
        //echo '<br> $str='.$str;
        return $str;
    }

//end of function  GetParamsValuesOfPropForImgInStr()
    // ================================================================================================
    // Function : IsImageInfluenceOnParams()
    // Date : 26.07.2006
    // Parms :   $id_prop    / id of the image for curent position
    //           $id_param   / id of the parameter
    //           $param_val  / value of the parameter
    // Returns : true,false  / Void
    // Description : return image for current position of catalogue with
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetImageByParamValue($id_prop = NULL, $id_param = NULL, $param_val = NULL) {
        $q = "SELECT `" . TblModCatalogParamsPropImg . "`.`id_img`
                 FROM `" . TblModCatalogParamsPropImg . "`, `" . TblModCatalogPropImg . "`
                 WHERE `" . TblModCatalogParamsPropImg . "`.`id_param`='" . $id_param . "'
                 AND `" . TblModCatalogParamsPropImg . "`.`val`='" . $param_val . "'
                 AND `" . TblModCatalogParamsPropImg . "`.`id_img`=`" . TblModCatalogPropImg . "`.id
                 AND `" . TblModCatalogPropImg . "`.`id_prop`='" . $id_prop . "'
                 LIMIT 1
                ";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res OR !$this->db->result)
            return false;
        $row = $this->db->db_FetchAssoc();
        return $row['id_img'];
    }

//end of function  IsImageInfluenceOnParams()
    // ================================================================================================
    // Function : GetFirstImgOfProp()
    // Date : 13.10.2006
    // Parms :   $id_prop    / id of the image for curent position
    // Returns : true,false  / Void
    // Description : return image for current position of catalogue with
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetFirstImgOfProp($id_prop) {
        $q = "SELECT `id` FROM `" . TblModCatalogPropImg . "` WHERE 1 AND `id_prop`='" . $id_prop . "' order by `move` LIMIT 1";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res)
            return false;
        if (!$this->db->result)
            return false;
        //$rows = $tmp_db->db_GetNumRows();
        $row = $this->db->db_FetchAssoc();
        $img = $row['id'];
        return $img;
    }
    function GetNameFirstImgOfProp($id_prop) {
        $q = "SELECT `path` FROM `" . TblModCatalogPropImg . "` WHERE 1 AND `id_prop`='" . $id_prop . "' order by `move` LIMIT 1";
        $res = $this->db->db_Query($q);
       // echo '<br>q='.$q.' res='.$res.' $tmp_db->result=';
        if (!$res)
            return false;
        if (!$this->db->result)
            return false;
        //$rows = $tmp_db->db_GetNumRows();
        $row = $this->db->db_FetchAssoc();
        $img = $row['path'];
        return $img;
    }

//end of function GetFirstImgOfProp()
    // ================================================================================================
    // Function : GetImageToShowByParams()
    // Date : 26.10.2006
    // Parms :   $id_prop    / id of the image for curent position
    // Returns : true,false  / Void
    // Description : return image to show with parameters influenses on image
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetImageToShowByParams() {
        $tmp_db1 = new DB();

        $q = "SELECT `id` FROM `" . TblModCatalogPropImg . "` WHERE 1 AND `id_prop`='" . $this->id . "' order by `move`";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res OR !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();

        $img = NULL;
        // цикл по всем изображения для текущего товара
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc();
            $q1 = "SELECT * FROM `" . TblModCatalogParamsPropImg . "` WHERE 1 AND `id_img`='" . $row['id'] . "'";
            $where = NULL;
            $img = $row['id'];
            if (is_array($this->arr_current_img_params_value)) {
                // цикл по выбранным парметрам
                foreach ($this->arr_current_img_params_value as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $key2 => $value2) {
                            $where = " AND `id_param`='" . $key . "' AND `val`='" . $value2 . "'";
                            $q2 = $q1 . $where;
                            $res = $tmp_db1->db_Query($q2);
                            if (!$res OR !$tmp_db1->result)
                                return false;
                            //echo '<br>q2='.$q2.' res='.$res.' $tmp_db1->result='.$tmp_db1->result;
                            $rows1 = $tmp_db1->db_GetNumRows();
                            //echo '<br>$rows1='.$rows1;
                            if ($rows1 == 0)
                                $img = NULL;
                        }
                    }
                    else {
                        $where = " AND `id_param`='" . $key . "' AND `val`='" . $value . "'";
                        $q2 = $q1 . $where;
                        $res = $tmp_db1->db_Query($q2);
                        if (!$res OR !$tmp_db1->result)
                            return false;
                        //echo '<br>q2='.$q2.' res='.$res.' $tmp_db1->result='.$tmp_db1->result;
                        $rows1 = $tmp_db1->db_GetNumRows();
                        //echo '<br>$rows1='.$rows1;
                        if ($rows1 == 0)
                            $img = NULL;
                    }
                } // end foreach
            } // end if
            //echo '<br>$img='.$img;
            if (!empty($img))
                break;
        } // end for
        //echo '<br>$img='.$img;
        return $img;
    }

//end of function GetImageToShowByParams()
    // ================================================================================================
    // Function : GetParameterValuesOfPropInStr()
    // Date : 24.06.2006
    // Parms :   $value      /
    //           $params_row /  array with data of parameter
    //           $type       / can be: 'array ' or 'str'
    // Returns : true,false / Void
    // Description : return values of parameters in string for current position of catalogue
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetParameterValuesOfPropInStr($value, $params_row, $type = 'array') {
        //echo '<br>$params_row1='; print_r($params_row);
        //echo '<br><br>$params_row[id_categ]='.$params_row['id_categ'];
        $divider = '<br>';
        $tblname = $this->BuildNameOfValuesTable($params_row['id_categ'], $params_row['id']);

        isset($value[$params_row['id']]) ? $val_from_table = $value[$params_row['id']] : $val_from_table = NULL;

        //if( $params_row!=NULL ) $this->Err!=NULL ? $val=$this->arr_params[$params_row['id']] : $val=$val_from_table;
        //else $val=$this->arr_params[$params_row['id']];

        $val = $val_from_table;

        $prefix = $this->Spr->GetNameByCod(TblModCatalogParamsSprPrefix, ($params_row['id']), $this->lang_id, 1);
        $sufix = $this->Spr->GetNameByCod(TblModCatalogParamsSprSufix, ($params_row['id']), $this->lang_id, 1);
        switch ($params_row['type']) {
            case '1':
                $val = $val;
                break;
            case '2':
                $val = $this->Spr->GetNameByCod(TblSysLogic, $val, $this->lang_id, 1);
                break;
            case '3':
                $val = $this->Spr->GetNameByCod($tblname, $val, $this->lang_id, 1);
                break;
            case '4':
                $val = $this->Spr->GetNamesInStr($tblname, $this->lang_id, $val, ',');
                break;
            case '5':
                $val = $val;
                break;
        }

        if ($type == 'str') {
            $tmp_str = $this->Spr->GetNameByCod(TblModCatalogParamsSprName, $params_row['id'], $this->lang_id, 1) . ': ' . $prefix . ' ' . $val . ' ' . $sufix;
            if (empty($str))
                $str = $tmp_str;
            else
                $str = $str . $divider . $tmp_str;
        }
        else {
            $str["param_name"] = $this->Spr->GetNameByCod(TblModCatalogParamsSprName, $params_row['id'], $this->lang_id, 1);
            $str["param_prefix"] = $prefix;
            $str["param_value"] = $val;
            $str["param_sufix"] = $sufix;
        }
        //echo '<br> $str='.$str;
        return $str;
    }

//end of function  GetParameterValuesOfPropInStr()
    // ================================================================================================
    // Function : SearchByParams()
    // Version : 1.0.0
    // Date : 18.04.2006
    // Parms :
    // Returns : true,false / Void
    // Description : make advansed search by name, short description, full description and number name
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 18.04.2006
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function SearchByParams($arr_manufac, $arr_params) {
        //$settings = $this->GetSettings();
        $params_row = $this->GetParams($this->id_cat);

        $sel_table = "`" . TblModCatalogProp . "`";

        $q = "SELECT `" . TblModCatalogProp . "`.id, `" . TblModCatalogProp . "`.id_cat, `" . TblModCatalogProp . "`.id_manufac, `" . TblModCatalogProp . "`.exist, `" . TblModCatalogProp . "`.number_name, `" . TblModCatalogProp . "`.price, `" . TblModCatalogProp . "`.opt_price, `" . TblModCatalogProp . "`.grnt, `" . TblModCatalogProp . "`.dt, `" . TblModCatalogProp . "`.move
                FROM " . $sel_table . "
                WHERE 1 AND `" . TblModCatalogProp . "`.`visible`='2'";

        $arr_id_prop[] = NULL;
        $str_id_prop = NULL;
        $str_params = NULL;
        for ($i = 0; $i < count($params_row); $i++) {
            $is_used = 0;
            $q_param = "SELECT `" . TblModCatalogParamsProp . "`.id_prop FROM `" . TblModCatalogParamsProp . "`
                            WHERE `id_param`='" . $params_row[$i]['id'] . "'";

            switch ($params_row[$i]['type']) {
                case '1': //integer
                    if (!empty($arr_params[$params_row[$i]['id']][0]) and empty($arr_params[$params_row[$i]['id']][1])) {
                        $q_param = $q_param . " AND `val`>=" . $arr_params[$params_row[$i]['id']][0];
                        $is_used = 1;
                    }
                    if (empty($arr_params[$params_row[$i]['id']][0]) and !empty($arr_params[$params_row[$i]['id']][1])) {
                        $q_param = $q_param . " AND `val`<=" . $arr_params[$params_row[$i]['id']][1];
                        $is_used = 1;
                    }
                    if (!empty($arr_params[$params_row[$i]['id']][0]) and !empty($arr_params[$params_row[$i]['id']][1])) {
                        $q_param = $q_param . " AND `val`>=" . intval($arr_params[$params_row[$i]['id']][0]) . " AND `val`<=" . intval($arr_params[$params_row[$i]['id']][1]);
                        $is_used = 1;
                    }
                    break;
                case '2': //logic
                    if (isset($arr_params[$params_row[$i]['id']]) and $arr_params[$params_row[$i]['id']] > 0) {
                        $q_param = $q_param . " AND `val`='" . $arr_params[$params_row[$i]['id']] . "'";
                        $is_used = 1;
                    }
                    break;
                case '3': //select from list
                    if (isset($arr_params[$params_row[$i]['id']]) and $arr_params[$params_row[$i]['id']] > 0) {
                        $q_param = $q_param . " AND `val`='" . $arr_params[$params_row[$i]['id']] . "'";
                        $is_used = 1;
                    }
                    break;
                case '4': //multi select
                    if (isset($arr_params[$params_row[$i]['id']]) and $arr_params[$params_row[$i]['id']] > 0) {
                        $str_multi_select = NULL;
                        for ($j = 0; $j < count($arr_params[$params_row[$i]['id']]); $j++) {
                            if (empty($str_multi_select))
                                $str_multi_select = $arr_params[$params_row[$i]['id']][$j];
                            else
                                $str_multi_select = $str_multi_select . ',' . $arr_params[$params_row[$i]['id']][$j];
                        }
                        $q_param = $q_param . " AND `val` IN ($str_multi_select)";
                        $is_used = 1;
                    }
                    break;
                case '5': //text
                    if (isset($arr_params[$params_row[$i]['id']]) and !empty($arr_params[$params_row[$i]['id']])) {
                        $q_param = $q_param . ' AND ' . $this->build_str_like('`val`', $arr_params[$params_row[$i]['id']]);
                        //if ( empty($str_params) ) $str_params = $params_row[$i]['id'];
                        //else $str_params = $str_params.','.$params_row[$i]['id'];
                        $is_used = 1;
                    }
                    break;
            }
            if ($is_used == 1) {
                if (empty($str_params))
                    $str_params = $params_row[$i]['id'];
                else
                    $str_params = $str_params . ',' . $params_row[$i]['id'];

                $q_param = $q_param . " ORDER BY `id_prop` ASC";
                $res = $this->db->db_Query($q_param);
                //echo '<br>$q_param='.$q_param.' res='.$res.' $tmp_db->result='.$tmp_db->result;
                if (!$res)
                    return false;
                if (!$this->db->result)
                    return false;
                $rows = $this->db->db_GetNumRows();
                //echo '<br> $rows='.$rows;
                for ($j = 0; $j < $rows; $j++) {
                    $row = $this->db->db_FetchAssoc();
                    if (empty($arr_id_prop[$row['id_prop']]))
                        $arr_id_prop[$row['id_prop']] = $params_row[$i]['id'];
                    else
                        $arr_id_prop[$row['id_prop']] = $arr_id_prop[$row['id_prop']] . ',' . $params_row[$i]['id'];
                }
            }
        }
        //echo '<br>$str_params='.$str_params;
        foreach ($arr_id_prop as $k => $v) {
            //echo '<br>'.$k.'='.$v;
            if ($v == $str_params) {
                if (empty($str_id_prop))
                    $str_id_prop = $k;
                else
                    $str_id_prop = $str_id_prop . ',' . $k;
            }
        }

        //================ create main query for search ===================
        $q = "SELECT * FROM `" . TblModCatalogProp . "` WHERE 1 ";

        //search by category
        $str_sub_levels = $this->getSubLevels($this->id_cat);
        if (empty($str_sub_levels))
            $str_sub_levels = $this->id_cat;
        $q = $q . " AND `id_cat` IN (" . $str_sub_levels . ")";

        //echo '<br> $str_id_prop='.$str_id_prop;
        if (!empty($str_id_prop)) {
            $q = $q . " AND `" . TblModCatalogProp . "`.id IN (" . $str_id_prop . ")";
            //if (strstr($str_id_prop,',') ) $q = $q." AND `".TblModCatalogProp."`.id IN ($str_id_prop)";
            //else $q = $q." AND `".TblModCatalogProp."`.id='$str_id_prop'";
        } else if (!empty($str_params))
            return false;

        // search by manufacturer
        if (count($arr_manufac) > 0) {
            for ($i = 0; $i < count($arr_manufac); $i++) {
                if (empty($str_manufac))
                    $str_manufac = $arr_manufac[$i];
                else
                    $str_manufac = $str_manufac . ',' . $arr_manufac[$i];
            }
            $q = $q . " AND `id_manufac` IN (" . $str_manufac . ")";
        }

        $q = $q . " ORDER BY `" . TblModCatalogProp . "`.id_cat, `" . TblModCatalogProp . "`.move";
        //================ end create main query for search ===================

        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
        if (!$res)
            return false;
        if (!$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        //echo '<br> $rows='.$rows;

        $arr = $this->CotvertDataToOutputArray($rows, "id_cat", "asc");
        return $arr;
    }

//end offunction SearchByParams()
    //---------------------------------- FUNCTION FOR SEARCH OF CONTENT ------------------------------------------
    // ================================================================================================
    // Function : QuickSearch()
    // Date : 22.12.2010
    // Returns : true,false / Void
    // Description : make quick search by name and number name
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function QuickSearch($search_keywords, $limit = 'nolimit') {
        //$settings = $this->GetSettings(1);
        $search_keywords = stripslashes($search_keywords);
        $sel_table = NULL;
        $str_like = NULL;
        $filter_cr = ' OR ';
        //--quick search only by name
        $str_like = $this->build_str_like(TblModCatalogPropSprName . '.name', $search_keywords);
        $search_keywords_no_space = str_replace(" ", "", $search_keywords);
        //$str_like .= $filter_cr.$this->build_str_like(TblModCatalogPropSprName.'.name', $str_like_no_space);
        $str_like .= $filter_cr.$this->build_str_like(TblModCatalogPropSprShort.'.name', $search_keywords);
        $str_like .= $filter_cr.$this->build_str_like(TblModCatalogPropSprFull.'.name', $search_keywords);
        //$sel_table = "`".TblModCatalogProp."`, `".TblModCatalogPropSprName."`";
        //print_r($this->treeCatList);
        $categs = implode(',', $this->treeCatList);
        //echo '<br />$categs='.$categs;

        $q = "SELECT
                `" . TblModCatalogProp . "`.*,
                `" . TblModCatalogPropSprName . "`.name,
                `".TblModPropGroupPrice."`.`price` as group_price,
                   `".TblModPropGroupPrice."`.`group_currency` as currency_group_price,
                MATCH `" . TblModCatalogPropSprName . "`.name AGAINST ('" . $search_keywords . "') as relev,
                MATCH `" . TblModCatalogPropSprName . "`.name AGAINST ('" . $search_keywords_no_space . "') as relev2,
                `" . TblModCatalogSprName . "`.name as cat_name,
                `" . TblModCatalogTranslit . "`.`translit`,
                `" . TblModCatalogPropImg . "`.`path` AS `first_img`,
                `" . TblModCatalogPropImgTxt . "`.`name` AS `first_img_alt`,
                `" . TblModCatalogPropImgTxt . "`.`text` AS `first_img_title`,
                `" . TblModCatalogPropSprShort . "`.name as short
              FROM `" . TblModCatalogProp . "`
                LEFT JOIN `" . TblModCatalogPropImg . "` ON (`" . TblModCatalogProp . "`.`id`=`" . TblModCatalogPropImg . "`.`id_prop` AND `" . TblModCatalogPropImg . "`.`move`='1')
                LEFT JOIN `" . TblModCatalogPropImgTxt . "` ON (`" . TblModCatalogPropImg . "`.`id`=`" . TblModCatalogPropImgTxt . "`.`cod` AND `" . TblModCatalogPropImgTxt . "`.lang_id='" . $this->lang_id . "')
                LEFT JOIN `" . TblModCatalogPropSprShort . "` ON (`" . TblModCatalogProp . "`.`id`=`" . TblModCatalogPropSprShort . "`.`cod` AND `" . TblModCatalogPropSprShort . "`.lang_id='" . $this->lang_id . "')
                LEFT JOIN `" . TblModCatalogPropSprFull . "` ON (`" . TblModCatalogProp . "`.`id`=`" . TblModCatalogPropSprFull . "`.`cod` AND `" . TblModCatalogPropSprFull . "`.lang_id='" . $this->lang_id . "'),
                `" . TblModCatalogPropSprName . "`,`" . TblModCatalogSprName . "`, `" . TblModCatalog . "`, `" . TblModCatalogTranslit . "`,`".TblModPropGroupPrice."`
              WHERE `" . TblModCatalogProp . "`.`id_cat`=`" . TblModCatalog . "`.`id`
              AND `" . TblModCatalogProp . "`.visible='2'
              AND `" . TblModCatalog . "`.`visible`='2'
              AND `" . TblModCatalogProp . "`.id=`" . TblModCatalogPropSprName . "`.cod
              AND `" . TblModCatalogProp . "`.id_cat=`" . TblModCatalogSprName . "`.cod
              AND `" . TblModCatalogPropSprName . "`.lang_id='" . $this->lang_id . "'
              AND `" . TblModCatalogSprName . "`.lang_id='" . $this->lang_id . "'
              AND `" . TblModCatalogProp . "`.id=`" . TblModCatalogTranslit . "`.`id_prop`
               AND
                `" . TblModCatalogProp . "`.id  =`".TblModPropGroupPrice."`.prod_id
                AND
                `".TblModPropGroupPrice."`.group_id = ".$this->group_user_id."
              AND `" . TblModCatalogTranslit . "`.`lang_id`='" . $this->lang_id . "'
              AND (" . $str_like . " OR LOWER(REPLACE(REPLACE(`" . TblModCatalogPropSprName . "`.name, ' ', ''), '-', '')) LIKE '%" . $search_keywords_no_space . "%' OR `" . TblModCatalogProp . "`.`cod_pli` LIKE '%" . $search_keywords . "%')
              AND `" . TblModCatalogProp . "`.id_cat IN (" . $categs . ")
              GROUP BY `" . TblModCatalogProp . "`.`id`
              ORDER BY relev2 DESC, relev DESC
             ";
        if ($limit == 'limit')
            $q = $q . " LIMIT " . $this->start . ", " . ($this->display);
        $res = $this->db->db_Query($q);
        //echo '<br>'.$q.' <br/>res='.$res.' <br/>$this->db->result='.$this->db->result;
        if (!$res or !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        //echo '<br>$rows='.$rows;
        $arr = array();
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc();
            $arr[$i] = $row;
        }
        return $arr;
    }

//end of function QuickSearch()
    // ================================================================================================
    // Function : AdvansedSearch()
    // Date : 18.04.2006
    // Parms :
    // Returns : true,false / Void
    // Description : make advansed search by name, short description, full description and number name
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function AdvansedSearch($search_keywords, $search_in_description, $inc_subcat, $pfrom, $pto, $dfrom, $dto) {
        //echo '<br> $search_keywords='.$search_keywords.' $this->id_cat='.$this->id_cat.' $this->manufac='.$this->id_manufac.' $pfrom='.$pfrom.' $pto='.$pto.' $dfrom='.$dfrom.' $dto='.$dto;
        //$settings = $this->GetSettings();
        $search_keywords = stripslashes($search_keywords);
        $pfrom = stripslashes($pfrom);
        $pto = stripslashes($pto);
        $dfrom = stripslashes($dfrom);
        $dto = stripslashes($dto);

        //echo '<br> $search_keywords='.$search_keywords.' $this->id_cat='.$this->id_cat.' $this->manufac='.$this->id_manufac.' $pfrom='.$pfrom.' $pto='.$pto.' $dfrom='.$dfrom.' $dto='.$dto;
        //if ( empty($search_keywords) AND empty($this->id_cat) AND empty($this->id_manufac) AND $pfrom==0 AND $pto==0 AND $dfrom=="гггг-мм-дд" AND $dto=="гггг-мм-дд" ) return false;

        $sel_table = NULL;
        $str_like = NULL;
        $filter_cr = ' OR ';

        if (!empty($search_keywords) AND $search_keywords != '')
            $str_like = $this->build_str_like(TblModCatalogPropSprName . '.name', $search_keywords);
        if ($search_in_description == 1 AND $search_keywords != '') {
            if (isset($this->settings['short_descr']) AND $this->settings['short_descr'] == '1')
                $str_like = $str_like . $filter_cr . $this->build_str_like(TblModCatalogPropSprShort . '.name', $search_keywords);
            if (isset($this->settings['full_descr']) AND $this->settings['full_descr'] == '1')
                $str_like = $str_like . $filter_cr . $this->build_str_like(TblModCatalogPropSprFull . '.name', $search_keywords);
            if (isset($this->settings['specif']) AND $this->settings['specif'] == '1')
                $str_like = $str_like . $filter_cr . $this->build_str_like(TblModCatalogPropSprSpecif . '.name', $search_keywords);
            if (isset($this->settings['reviews']) AND $this->settings['reviews'] == '1')
                $str_like = $str_like . $filter_cr . $this->build_str_like(TblModCatalogPropSprReviews . '.name', $search_keywords);
            if (isset($this->settings['support']) AND $this->settings['support'] == '1')
                $str_like = $str_like . $filter_cr . $this->build_str_like(TblModCatalogPropSprSupport . '.name', $search_keywords);
        }
        //echo '<br>str_like='.$str_like;

        $sel_table = "`" . TblModCatalogProp . "`";
        if (!empty($search_keywords) AND $search_keywords != '')
            $sel_table = $sel_table . ", `" . TblModCatalogPropSprName . "`";
        if ($search_in_description == 1 AND $search_keywords != '') {
            if (isset($this->settings['short_descr']) AND $this->settings['short_descr'] == '1')
                $sel_table = $sel_table . ", `" . TblModCatalogPropSprShort . "`";
            if (isset($this->settings['full_descr']) AND $this->settings['full_descr'] == '1')
                $sel_table = $sel_table . ", `" . TblModCatalogPropSprFull . "`";
            if (isset($this->settings['specif']) AND $this->settings['specif'] == '1')
                $sel_table = $sel_table . ", `" . TblModCatalogPropSprSpecif . "`";
            if (isset($this->settings['reviews']) AND $this->settings['reviews'] == '1')
                $sel_table = $sel_table . ", `" . TblModCatalogPropSprReviews . "`";
            if (isset($this->settings['support']) AND $this->settings['support'] == '1')
                $sel_table = $sel_table . ", `" . TblModCatalogPropSprSupport . "`";
        }
        // search in description
        if ($search_in_description == 1 AND $search_keywords != '') {
            $q = "SELECT `" . TblModCatalogProp . "`.id, `" . TblModCatalogProp . "`.id_cat, `" . TblModCatalogProp . "`.id_manufac, `" . TblModCatalogProp . "`.exist, `" . TblModCatalogProp . "`.number_name, `" . TblModCatalogProp . "`.price, `" . TblModCatalogProp . "`.opt_price, `" . TblModCatalogProp . "`.grnt, `" . TblModCatalogProp . "`.dt, `" . TblModCatalogProp . "`.move, `" . TblModCatalogProp . "`.visible
                FROM " . $sel_table . "
                WHERE 1 AND `" . TblModCatalogProp . "`.visible='2'";
            if (trim($str_like) != '')
                $q = $q . " AND (" . $str_like . ")";

            $q = $q . "
                AND `" . TblModCatalogPropSprName . "`.lang_id = '" . $this->lang_id . "'
                AND `" . TblModCatalogProp . "`.id = `" . TblModCatalogPropSprName . "`.cod";
            if (isset($this->settings['short_descr']) AND $this->settings['short_descr'] == '1')
                $q = $q . "
                AND `" . TblModCatalogPropSprShort . "`.lang_id = '" . $this->lang_id . "'
                AND `" . TblModCatalogProp . "`.id = `" . TblModCatalogPropSprShort . "`.cod";
            if (isset($this->settings['full_descr']) AND $this->settings['full_descr'] == '1')
                $q = $q . "
                AND `" . TblModCatalogPropSprFull . "`.lang_id = '" . $this->lang_id . "'
                AND `" . TblModCatalogProp . "`.id = `" . TblModCatalogPropSprFull . "`.cod";
            if (isset($this->settings['specif']) AND $this->settings['specif'] == '1')
                $q = $q . "
                AND `" . TblModCatalogPropSprSpecif . "`.lang_id = '" . $this->lang_id . "'
                AND `" . TblModCatalogProp . "`.id = `" . TblModCatalogPropSprSpecif . "`.cod";
            if (isset($this->settings['reviews']) AND $this->settings['reviews'] == '1')
                $q = $q . "
                AND `" . TblModCatalogPropSprReviews . "`.lang_id = '" . $this->lang_id . "'
                AND `" . TblModCatalogProp . "`.id = `" . TblModCatalogPropSprReviews . "`.cod";
            if (isset($this->settings['support']) AND $this->settings['support'] == '1')
                $q = $q . "
                AND `" . TblModCatalogPropSprSupport . "`.lang_id = '" . $this->lang_id . "'
                AND `" . TblModCatalogProp . "`.id = `" . TblModCatalogPropSprSupport . "`.cod";
        }
        else {
            $q = "SELECT `" . TblModCatalogProp . "`.id, `" . TblModCatalogProp . "`.id_cat, `" . TblModCatalogProp . "`.id_manufac, `" . TblModCatalogProp . "`.exist, `" . TblModCatalogProp . "`.number_name, `" . TblModCatalogProp . "`.price, `" . TblModCatalogProp . "`.opt_price, `" . TblModCatalogProp . "`.grnt, `" . TblModCatalogProp . "`.dt, `" . TblModCatalogProp . "`.move, `" . TblModCatalogProp . "`.visible
                FROM " . $sel_table . "
                WHERE 1 AND `" . TblModCatalogProp . "`.visible='2'";
            if (trim($str_like) != '')
                $q = $q . " AND (" . $str_like . ")";
            if (!empty($search_keywords))
                $q = $q . "
                AND `" . TblModCatalogPropSprName . "`.lang_id = '" . $this->lang_id . "'
                AND `" . TblModCatalogProp . "`.id = `" . TblModCatalogPropSprName . "`.cod";
        }

        // search by category
        if ($this->id_cat) {
            if ($inc_subcat == 1) {
                $level = $this->id_cat;
                $cat_str = $this->getSubLevels($level);
                if (!empty($cat_str))
                    $q = $q . " AND `" . TblModCatalogProp . "`.id_cat IN (" . $cat_str . ")";
                else
                    $q = $q . " AND `" . TblModCatalogProp . "`.id_cat='" . $this->id_cat . "'";
            }
            else
                $q = $q . " AND `" . TblModCatalogProp . "`.id_cat='" . $this->id_cat . "'";
        }

        // search by manufacturer
        if ($this->id_manufac) {
            $q = $q . " AND `" . TblModCatalogProp . "`.id_manufac='" . $this->id_manufac . "'";
        }

        // search by price
        if (isset($this->settings['price']) AND $this->settings['price'] == '1') {
            if ($pfrom > 0 AND $pto > 0) {
                $q = $q . " AND `" . TblModCatalogProp . "`.price BETWEEN " . $pfrom . " AND " . $pto . " AND `" . TblModCatalogProp . "`.price!=''";
            } else {
                if ($pfrom > 0 AND $pto == 0) {
                    $q = $q . " AND `" . TblModCatalogProp . "`.price>=" . $pfrom . " AND `" . TblModCatalogProp . "`.price!=''";
                }
                if ($pfrom == 0 AND $pto > 0) {
                    $q = $q . " AND `" . TblModCatalogProp . "`.price<=" . $pto . " AND `" . TblModCatalogProp . "`.price!=''";
                }
            }
        }

        // search by date
        if (isset($this->settings['dt']) AND $this->settings['dt'] == '1') {
            if ($dfrom > 0 AND $dto > 0) {
                $q = $q . " AND `" . TblModCatalogProp . "`.dt BETWEEN '" . $dfrom . "' AND '" . $dto . "' AND `" . TblModCatalogProp . "`.dt!=''";
            } else {
                if ($dfrom > 0 AND $dto == 0) {
                    $q = $q . " AND `" . TblModCatalogProp . "`.dt>='" . $dfrom . "' AND `" . TblModCatalogProp . "`.dt!=''";
                }
                if ($dfrom == 0 AND $dto > 0) {
                    $q = $q . " AND `" . TblModCatalogProp . "`.dt<='" . $dto . "' AND `" . TblModCatalogProp . "`.dt!=''";
                }
            }
        }

        $q = $q . " ORDER BY `" . TblModCatalogProp . "`.id_cat, `" . TblModCatalogProp . "`.move";

        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
        if (!$res OR !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        //echo '<br> $rows='.$rows;
        //$arr = $this->CotvertDataToOutputArray($rows, "id_cat", "asc");
        $arr = array();
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc();
            $arr[$i] = $row;
        }
        //print_r($arr);
        return $arr;
    }

//end offunction AdvansedSearch()
    // ================================================================================================
    // Function : build_str_like
    // Date : 19.01.2005
    // Parms : $find_field_name - name of the field by which we want to do search
    //         $field_value - value of the field
    // Returns : str_like_filter - builded string with special format;
    // Description : create the string for SQL-command SELECT for search in the text field by any word
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function build_str_like($find_field_name, $field_value) {
        $str_like_filter = NULL;
        /*
          // cut unnormal symbols
          $field_value=preg_replace("/[^\w\x7F-\xFF\s]/", " ", $field_value);
          // delete double spacebars
          $field_value=str_replace(" +", " ", $field_value);
         */
        $wordmas = explode(" ", $field_value);

        for ($i = 0; $i < count($wordmas); $i++) {
            $wordmas[$i] = trim($wordmas[$i]);
            if (EMPTY($wordmas[$i]))
                continue;
            if (!EMPTY($str_like_filter))
                $str_like_filter = $str_like_filter . " AND " . $find_field_name . " LIKE '%" . $wordmas[$i] . "%'";
            else
                $str_like_filter = $find_field_name . " LIKE '%" . $wordmas[$i] . "%'";
        }
        if ($i > 1)
            $str_like_filter = "(" . $str_like_filter . ")";
        //echo '<br>$str_like_filter='.$str_like_filter;
        return $str_like_filter;
    }

//end of function build_str_like()
//------------------------------------------------------------------------------------------------------------
//---------------------------------- OTHER FUNCTION FOR CATALOG ----------------------------------------------
//------------------------------------------------------------------------------------------------------------
    // ================================================================================================
    // Function : GetCategory
    // Version : 1.0.0
    // Date : 26.05.2006
    // Parms :  $id - id of the curent position in catalogue
    // Returns : $res / Void
    // Description : return category of the curent position
    // ================================================================================================
    function GetCategory($id) {
        $q = "select `id_cat` from `" . TblModCatalogProp . "` where `id`='" . $id . "'";
        $res = $this->db->db_Query($q);
        if (!$res)
            return false;
        if (!$this->db->result)
            return false;
        $row = $this->db->db_FetchAssoc($res);
        return $row['id_cat'];
    }

// end of function GetCategory
    // ================================================================================================
    // Function : GetPathToLevel()
    // Date : 07.05.2007
    // Parms :        $level - id of the category
    //                $devider - charactter beetwen levels
    //                $str - string with levels
    // Returns :      $str / string with name of the categoties to current level of catalogue
    // Description :  Return a path to current category
    // Programmer :  Igor Trokhymchuk
    // ================================================================================================
    function GetPathToLevel($level, $devider = ' > ', $str = NULL) {
        $name = $this->Spr->GetNameByCod(TblModCatalogSprName, $level, $this->lang_id, 1);
        //echo '<br>$str='.$str.' $name='.$name.' <br>';
        if (!empty($str))
            $str = $name . $devider . $str;
        else
            $str = $name . $str;

        $q = "SELECT * FROM " . TblModCatalog . " WHERE `id`='" . $level . "'";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res OR !$this->db->result)
            return false;
        $row = $this->db->db_FetchAssoc();
        if ($row['level'] > 0) {
            $str = $this->GetPathToLevel($row['level'], $devider, $str);
        }
        //$str = '<a href="'.$script.'&level=0">'.$this->Msg->show_text('TXT_ROOT_CATEGORY').' > </a>'.$str;
        return $str;
    }

// end of function GetPathToLevel()
    // ================================================================================================
    // Function : GetIdPropByMove
    // Date : 03.01.2007
    // Parms :  $move - display number of the curent position in catalogue
    // Returns : $res / Void
    // Description : return id of the curent position
    // ================================================================================================
    function GetIdPropByMove($move) {
        $q = "select `id` from `" . TblModCatalogProp . "` where `move`='" . $move . "'";
        $res = $this->db->db_Query($q);
        if (!$res)
            return false;
        if (!$this->db->result)
            return false;
        $row = $this->db->db_FetchAssoc($res);
        return $row['id'];
    }

// end of function GetIdPropByMove
    // ================================================================================================
    // Function : GetIdCatByMove
    // Date : 03.01.2007
    // Parms :  $move - display number of the curent position in catalogue
    // Returns : $res / Void
    // Description : return id of the category for curent position
    // ================================================================================================
    function GetIdCatByMove($move) {
        $this->db = new DB();
        $res = $this->db->db_Query($q);
        if (!$res)
            return false;
        if (!$this->db->result)
            return false;
        $row = $this->db->db_FetchAssoc($res);
        return $row['id_cat'];
    }

// end of function GetIdCatByMove
    // ================================================================================================
    // Function : GetPrice
    // Date : 03.04.2006
    // Parms :  $idd - id of the curent position in catalogue
    // Returns : $res / Void
    // Description : return price og the curent position
    // ================================================================================================
    function GetPrice($idd) {

        $db = DBs::getInstance();
        $q = "select `price` from `" . TblModPropGroupPrice . "` where `prod_id`='" . $idd . "' AND group_id='".$this->group_user_id."' ";
        //echo $q;

        $res = $db->db_Query($q);
        if (!$res)
            return false;
        if (!$db->result)
            return false;
        $row = $db->db_FetchAssoc($res);
        return $row['price'];

    }

    function GetPriceReletiv($idd) {
        $db = DBs::getInstance();
        $q = "select * from `" . TblModPropGroupPrice . "` where `prod_id`='" . $idd . "' AND group_id='".MOD_CATALOG_RETAIL_PRICE."' ";

        //echo $q;

        $res = $db->db_Query($q);
        if (!$res)
            return false;
        if (!$db->result)
            return false;
        $row = $db->db_FetchAssoc($res);
        return $row;
    }




   function GetPriceArr($idd) {

        $db = DBs::getInstance();
        $q = "select `price`,`opt_price`, `price_currency`, `opt_price_currency` from `" . TblModCatalogProp . "` where `id`='" . $idd . "'";
        $res = $db->db_Query($q);
        if (!$res)
            return false;
        if (!$db->result)
            return false;
        $row = $db->db_FetchAssoc($res);
        return $row;

    }

// end of function GetPrice()
    // ================================================================================================
    // Function : GetPriceCurrency
    // Date : 25.10.2007
    // Parms :  $idd - id of the curent position in catalogue
    // Returns : $res / Void
    // Description : return currency of price for the curent position
    // ================================================================================================
    function GetPriceCurrency($idd) {

        $db = DBs::getInstance();
        $q = "SELECT `group_currency` FROM `" . TblModPropGroupPrice . "` WHERE `prod_id`='" . $idd . "' AND group_id='".$this->group_user_id."' ";
        $res = $db->db_Query($q);
        if (!$res)
            return false;
        if (!$db->result)
            return false;
        $row = $db->db_FetchAssoc($res);
        return $row['group_currency'];
    }

// end of function GetPriceCurrency()
    // ================================================================================================
    // Function : GetPriceLevels()
    // Date : 28.09.2007
    // Parms :  $id_prop - id of he postion in catalogue
    //          $for_users - get price levels for users if $for_users=='user' or $for_users=='general'
    // Returns :      true,false / Void
    // Description : return array with Price levels
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function GetPriceLevels($id_prop, $for_users = NULL) {
        $q = "SELECT * FROM `" . TblModCatalogPriceLevels . "` WHERE `id_prop`='" . $id_prop . "'";
        switch ($for_users) {
            case 'user':
                $q = $q . " AND `id_user` IS NOT NULL AND `id_user`!='0' ORDER BY `id_user`,`id`";
                break;
            case 'general':
                $q = $q . " AND (`id_user` IS NULL OR `id_user`='0') ORDER BY `id`";
                break;
            default:
                if (empty($for_users))
                    $q = $q . " ORDER BY `id_user`,`id`";
                else
                    $q = $q . " AND `id_user`='" . $for_users . "' ORDER BY `id_user`,`id`";
                break;
        }
        //if($for_users=='user') $q = $q." AND `id_user` IS NOT NULL AND `id_user`!='0' ORDER BY `id_user`,`id`";
        //if($for_users=='general') $q = $q." AND (`id_user` IS NULL OR `id_user`='0') ORDER BY `id`";
        //if(empty($for_users)) $q = $q." ORDER BY `id_user`,`id`";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res OR !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        //$arr = array();
        $arr = NULL;
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc();
            $arr[$i] = $row;
        }
        //echo '<br>$arr='.$arr;print_r($arr);
        //echo '<br>intval($for_users)='.intval($for_users);
        if (empty($arr) AND intval($for_users) > 0) {
            $arr = $this->GetPriceLevels($id_prop, 'general');
        }
        return $arr;
    }

//end of function GetPriceLevels()
    // ================================================================================================
    // Function : GetPriceByQuantity()
    // Date : 25.10.2007
    // Parms :  $id_prop - id of he postion in catalogue
    //          $id_user - id of the user
    //          $quantity - quantity of positions
    // Returns :      true,false / Void
    // Description : return proce for position
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function GetPriceByQuantity($id_prop, $id_user = NULL, $quantity = NULL) {
        $q = "SELECT * FROM `" . TblModCatalogPriceLevels . "` WHERE `id_prop`='" . $id_prop . "'";
        if (!empty($id_user))
            $q = $q . " AND `id_user`='" . $id_user . "'";
        else
            $q = $q . " AND (`id_user` IS NULL OR `id_user`='0')";
        $q = $q . " AND (`qnt_from`<=" . $quantity . " AND `qnt_to`>=" . $quantity . ") ORDER BY `id`";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res OR !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        //echo '<br>$rows='.$rows;
        //if not set price for user $id_user in this Range, then search price in limits of price levels
        if ($rows == 0) {
            $q = "SELECT * FROM `" . TblModCatalogPriceLevels . "` WHERE `id_prop`='" . $id_prop . "'";
            if (!empty($id_user))
                $q = $q . " AND `id_user`='" . $id_user . "'";
            else
                $q = $q . " AND (`id_user` IS NULL OR `id_user`='0')";
            $q = $q . " AND `qnt_from`<=" . $quantity . " AND `qnt_to`='' ORDER BY `qnt_from` desc";
            $res = $this->db->db_Query($q);
            //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
            $rows = $this->db->db_GetNumRows();
            //echo '<br>$rows='.$rows;
            //if not set price for user $id_user, then get general price
            if ($rows == 0) {
                $q = "SELECT * FROM `" . TblModCatalogPriceLevels . "` WHERE `id_prop`='" . $id_prop . "'";
                $q = $q . " AND (`id_user` IS NULL OR `id_user`='0')";
                $q = $q . " AND `qnt_from`<=" . $quantity . " AND `qnt_to`>=" . $quantity . " ORDER BY `id`";
                //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
                $res = $this->db->db_Query($q);
                $rows = $this->db->db_GetNumRows();
                //echo '<br>$rows='.$rows;
                //if not set general price in this Range, then search general price in limits of price levels
                if ($rows == 0) {
                    $q = "SELECT * FROM `" . TblModCatalogPriceLevels . "` WHERE `id_prop`='" . $id_prop . "'";
                    $q = $q . " AND (`id_user` IS NULL OR `id_user`='0')";
                    $q = $q . " AND `qnt_from`<=" . $quantity . " AND `qnt_to`='' ORDER BY `qnt_from` desc";
                    //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
                    $res = $this->db->db_Query($q);
                    //$rows = $tmp_db->db_GetNumRows();
                    $row = $this->db->db_FetchAssoc();
                }
                else
                    $row = $this->db->db_FetchAssoc();
            }
            else
                $row = $this->db->db_FetchAssoc();
        }
        else
            $row = $this->db->db_FetchAssoc();
        return $row;
    }

//end of function GetPriceByQuantity()
    // ================================================================================================
    // Function : GetSubStrCutByWorld
    // Date : 27.04.2006
    // Parms :  $str - input string which must be croped
    //          $start - position of first symbol for crop
    //          $length - length of the output string
    // Returns : $str / Void
    // Description : make short string from long... cut it by worlds
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetSubStrCutByWorld($str, $start = NULL, $length = NULL) {
        //echo '<br> $str='.$str.' $start='.$start.' $length='.$length;
        $str = substr($str, $start, $length);
        //echo '<br> $str='.$str.' strlen($str)='.strlen($str).' $length='.$length.' strrpos($str, " ")='.strrpos($str, " ");
        $last_space_position = strrpos($str, " ");
        if (empty($last_space_position))
            return $str;
        $str = substr($str, 0, strrpos($str, " "));
        return $str;
    }

//end of function GetSubStrCutByWorld()


    // ================================================================================================
    // Function : GetNumberName
    // Date : 02.05.2006
    // Parms :  $id - id of the curent position in catalogue
    // Returns : $res / Void
    // Description : get value of  field 'numer_name' for seleted position in catalogue
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetNumberName($id) {
        $q = "SELECT `number_name` FROM `" . TblModCatalogProp . "` WHERE `id`='" . $id . "'";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res)
            return false;
        if (!$this->db->result)
            return false;
        //$rows = $tmp_db->db_GetNumRows();
        $row = $this->db->db_FetchAssoc();
        return $row['number_name'];
    }

//end of function GetNumberName()
    // ================================================================================================
    // Function : GetExistField
    // Date : 25.10.2007
    // Parms :  $id - id of the curent position in catalogue
    // Returns : $res / Void
    // Description : get value of  field 'exist' for seleted position in catalogue
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetExistField($id) {
        $q = "SELECT `exist` FROM `" . TblModCatalogProp . "` WHERE `id`='" . $id . "'";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res)
            return false;
        if (!$this->db->result)
            return false;
        //$rows = $tmp_db->db_GetNumRows();
        $row = $this->db->db_FetchAssoc();
        return $row['exist'];
    }

//end of function GetExistField()
    // ================================================================================================
    // Function : GetManufac
    // Date : 02.05.2006
    // Parms :  $id - id of the curent position in catalogue
    // Returns : $res / Void
    // Description : get value of  field 'numer_name' for seleted position in catalogue
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetManufac($id) {
        $q = "SELECT `id_manufac` FROM `" . TblModCatalogProp . "` WHERE `id`='" . $id . "'";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res)
            return false;
        if (!$this->db->result)
            return false;
        //$rows = $tmp_db->db_GetNumRows();
        $row = $this->db->db_FetchAssoc();
        return $row['id_manufac'];
    }

//end of function GetManufac()
    // ================================================================================================
    // Function : GetCountPositionsByManufac
    // Date : 15.08.2007
    // Parms :  $id_manufac - id of tmanufac
    // Returns : $res / Void
    // Description : get value of  field 'numer_name' for seleted position in catalogue
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetCountPositionsByManufac($id_manufac) {
        $q = "SELECT COUNT(id) FROM `" . TblModCatalogProp . "` WHERE `id_manufac`='" . $id_manufac . "'";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res)
            return false;
        if (!$this->db->result)
            return false;
        //$rows = $tmp_db->db_GetNumRows();
        $row = $this->db->db_FetchAssoc();
        return $row['COUNT(id)'];
    }

// end of function GetCountPositionsByManufac()
    // ================================================================================================
    // Function : GetArrManufacForCategory
    // Date : 19.05.2006
    // Parms :  $id_cat - id of the curent category of catalogue
    // Returns : $arr
    // Description : return arr of manufacturers for selected category
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetArrManufacForCategory($id_cat = NULL) {
        if (empty($id_cat))
            $id_cat = $this->$id_cat;
        $q = "SELECT DISTINCT `id_manufac` FROM `" . TblModCatalogProp . "` WHERE `id_cat`='" . $id_cat . "'";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res)
            return false;
        if (!$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        $arr = NULL;
        $arr[''] = '-----';
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc();
            $arr[$row['id_manufac']] = $this->Spr->GetNameByCod(TblModCatalogSprManufac, $row['id_manufac'], $this->lang_id, 1);
        }
        return $arr;
    }

//end of function GetArrManufacForCategory()
    // ================================================================================================
    // Function : GetArrModelsOfManufacForCategory
    // Date : 19.05.2006
    // Parms :  $id_cat - id of the curent category of catalogue
    //          $id_manufac = id of the manufacturer
    //          $sort - type of sortaion returned array
    //                  (move - default value, name)
    //          $asc_desc - sortation Asc or Desc
    //          $front_back - front/back
    // Returns : $arr
    // Description : return arr of content for selected category
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetArrModelsOfManufacForCategory($id_cat = NULL, $id_manufac = NULL, $sort = "move", $asc_desc = "asc", $search_in_subcategory = NULL, $arr = NULL, $front_back = 'front') {
        //echo '<br>$this->lang_id='.$this->lang_id;
        //if ( empty($id_manufac) ) $id_manufac = $this->$id_manufac;
        //$settings = $this->GetSettings(1);
        $tmp_db = new DB();

        $q = "SELECT `" . TblModCatalogProp . "`.* FROM `" . TblModCatalogProp . "`, `" . TblModCatalog . "` WHERE 1 AND `" . TblModCatalogProp . "`.`id_cat`='" . $id_cat . "' AND `" . TblModCatalogProp . "`.`id_cat`=`" . TblModCatalog . "`.`id`";
        if (!empty($id_manufac))
            $q = $q . " AND `" . TblModCatalogProp . "`.`id_manufac`='" . $id_manufac . "'";
        if ($front_back == 'front')
            $q = $q . " AND `" . TblModCatalog . "`.`visible`='2' AND `" . TblModCatalogProp . "`.`visible`='2'";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
        if (!$res OR !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        $arr = $this->CotvertDataToOutputArray($rows, $sort, $asc_desc);

        if ($search_in_subcategory == 1) {
            //$sub_levels = $this->get_sub_level_in_array($id_cat);
            $keys_sublevels = array_keys($this->GetTreeCatLevel($id_cat));
            $cnt_sublevels = count($keys_sublevels);
            //print_r($sub_levels);
            for ($counter_sub_levels = 0; $counter_sub_levels < $cnt_sublevels; $counter_sub_levels++) {
                $arr1 = $this->GetArrModelsOfManufacForCategory($keys_sublevels[$counter_sub_levels], $id_manufac, $sort, $asc_desc, $search_in_subcategory, $arr, $front_back);
                $arr = array_merge($arr, $arr1);
            }
        }
        //echo '<br><br>'; print_r($arr); echo '<br><br>';
        //sort($arr); reset($arr); print_r($arr); echo '<br><br>';
        //if ($sort == 'id') sort($arr);
        //if ($sort == 'move') sort($arr);
        //if ($sort == 'name') asort($arr);
        if (is_array($arr)) {
            if ($asc_desc == 'desc')
                krsort($arr);
            else
                ksort($arr);
            reset($arr);
        }
        //echo '<br>Arr:<br>'; print_r($arr); echo '<br><br>';
        return $arr;
    }

//end of function GetArrModelsOfManufacForCategory()
    // ================================================================================================
    // Function : GetProductsArrForSiteMap
    // Date : 13.01.2011
    // Returns : $productsArr
    // Description : return $productsArr of content for all categories
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GetProductsArrForSiteMap() {
        $q = "SELECT
                            `" . TblModCatalogProp . "`.id_cat,
                            `" . TblModCatalogPropSprName . "`.name,
                            `" . TblModCatalogTranslit . "`.`translit`
                    FROM
                            `" . TblModCatalogProp . "`, `" . TblModCatalogPropSprName . "` , `" . TblModCatalogTranslit . "`
                    WHERE
                            `" . TblModCatalogTranslit . "`.`lang_id`='" . $this->lang_id . "'
                      AND
                            `" . TblModCatalogTranslit . "`.`id_prop` = `" . TblModCatalogProp . "`.id
                      AND
                            `" . TblModCatalogProp . "`.id = `" . TblModCatalogPropSprName . "`.cod
                     AND
                            `" . TblModCatalogPropSprName . "`.lang_id='" . $this->lang_id . "'
                      AND
                            `" . TblModCatalogProp . "`.`visible`='2'
                       AND
                            `" . TblModCatalogPropSprName . "`.name != ''
                      ORDER BY
                        `" . TblModCatalogProp . "`.`move`
           ";
        $res = $this->db->db_Query($q);
        //echo '<br>'.$q.' <br/>res='.$res.' $this->db->result='.$this->db->result;
        if (!$res OR !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        $productsArr = array();
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc($res);
            $productsArr[$row['id_cat']][] = $row;
        }
        //print_r($productsArr);
        return $productsArr;
    }

//end of function GetProductsArrForSiteMap()
    // ================================================================================================
    // Function : CotvertDataToOutputArray
    // Date : 19.05.2006
    // Parms :  $rows - count if founded records stored in object $this->db
    //          $sort - type of sortaion returned array
    //                  (move - default value, name)
    //          $asc_desc - sortation Asc or Desc
    // Returns : $arr
    // Description : return arr of content for selected category
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function CotvertDataToOutputArray($rows, $sort = "move", $asc_desc = "asc") {
        $arr0 = NULL;
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc();
            //echo '<br>$row[id]='.$row['id'];
            switch ($sort) {
                case 'move':
                    $last_move = $this->GetMaxValueOfFieldMove();
                    //echo '<br>$last_move='.$last_move;
                    $zeros = NULL;
                    for ($j = 0; $j < (strlen($last_move) - strlen($row['move'])); $j++) {
                        $zeros = $zeros . '0';
                    }
                    $index_sort = $zeros . $row['move'] . '_' . $row['id'];
                    break;
                case 'name':
                    $index_sort = $this->Spr->GetNameByCod(TblModCatalogPropSprName, $row['id'], $this->lang_id, 1) . '_' . $row['number_name'];
                    break;
                case 'full_name':
                    $index_sort = $this->Spr->GetNameByCod(TblModCatalogSprManufac, $row['id_manufac'], $this->lang_id, 1) . ' ' . $this->Spr->GetNameByCod(TblModCatalogPropSprName, $row['id'], $this->lang_id, 1);
                    break;
                case 'xml_format':
                    $index_sort = '_' . $row['id'];
                    break;
                case 'dt':
                    $index_sort = $row['dt'] . '_' . $row['id'];
                    break;
                case 'random':
                    $index_sort = rand(1, $rows);
                    while (isset($arr0[$index_sort])) {
                        $index_sort = rand(1, $rows);
                    }
                    break;
                default:
                    $str_to_eval = '$index_sort = "_".$row[' . "'" . $sort . "'" . ']."_".$row[' . "'id'" . '];';
                    //echo '<br> $str_to_eval='.$str_to_eval;
                    eval($str_to_eval);
                    break;
            }
            //echo '<br> $index_sort='.$index_sort;
            $row_img = $this->GetPicture($row['id']);

            $arr0[$index_sort]["manufac_name"] = $this->Spr->GetNameByCod(TblModCatalogSprManufac, $row['id_manufac'], $this->lang_id, 1);
            $arr0[$index_sort]["name"] = $this->Spr->GetNameByCod(TblModCatalogPropSprName, $row['id'], $this->lang_id, 1);
            $arr0[$index_sort]["name_with_name_ind"] = $this->Spr->GetNameByCod(TblModCatalogSprNameInd, $row['id_cat'], $this->lang_id, 1) . ' ' . $this->Spr->GetNameByCod(TblModCatalogPropSprName, $row['id'], $this->lang_id, 1);
            $arr0[$index_sort]["full_name"] = $this->Spr->GetNameByCod(TblModCatalogSprManufac, $row['id_manufac'], $this->lang_id, 1) . ' ' . $this->Spr->GetNameByCod(TblModCatalogPropSprName, $row['id'], $this->lang_id, 1);
            $arr0[$index_sort]["category_name"] = $this->Spr->GetNameByCod(TblModCatalogSprName, $row['id_cat'], $this->lang_id, 1) . ' ' . $this->Spr->GetNameByCod(TblModCatalogPropSprName, $row['id'], $this->lang_id, 1);
            $arr0[$index_sort]["id"] = $row['id'];
            $arr0[$index_sort]["id_cat"] = $row['id_cat'];
            $arr0[$index_sort]["id_manufac"] = $row['id_manufac'];
            $arr0[$index_sort]["exist"] = $row['exist'];
            $arr0[$index_sort]["number_name"] = $row['number_name'];
            $arr0[$index_sort]["price"] = $row['price'];
            $arr0[$index_sort]["opt_price"] = $row['opt_price'];
            $arr0[$index_sort]["grnt"] = $row['grnt'];
            $arr0[$index_sort]["dt"] = $row['dt'];
            $arr0[$index_sort]["move"] = $row['move'];
            $arr0[$index_sort]["full_path_img"] = "http://" . NAME_SERVER . $this->settings['img_path'] . "/" . $row['id'] . "/" . $row_img['0']['path'];

            $short_descr = strip_tags($this->Spr->GetNameByCod(TblModCatalogPropSprShort, $row['id'], $this->lang_id, 1));
            $short_descr = str_replace('&nbsp;', ' ', $short_descr);
            $string = str_replace('&amp;', ' ', $short_descr);
            $string = str_replace('&#039;', '\'', $short_descr);
            $string = str_replace('&quot;', '\"', $short_descr);
            $arr0[$index_sort]["short_descr"] = $short_descr;

            $params_row = $this->GetParams($row['id_cat']);
            $value = $this->GetParamsValuesOfProp($row['id']);
            for ($ii = 0; $ii < count($params_row); $ii++) {
                $arr0[$index_sort]["params"]["_" . $ii] = $this->GetParameterValuesOfPropInStr($value, $params_row[$ii], 'array');
            }
            //$arr = array_merge($arr, $arr0);
            //echo '<br>Arr:<br>'; print_r($arr); echo '<br><br>';
        }
        //echo '<br><br>'; print_r($arr0); echo '<br><br>';
        //sort($arr); reset($arr); print_r($arr); echo '<br><br>';
        //if ($sort == 'id') sort($arr);
        //if ($sort == 'move') sort($arr);
        //if ($sort == 'name') asort($arr);
        if (is_array($arr0)) {
            if ($asc_desc == 'desc')
                krsort($arr0);
            else
                ksort($arr0);
            reset($arr0);
        }
        //echo '<br>Arr:<br>'; print_r($arr0); echo '<br><br>';
        return $arr0;
    }

//end of function CotvertDataToOutputArray()
    // ================================================================================================
    // Function : GetMaxValueOfFieldMove
    // Date : 19.05.2006
    // Parms :  $table  - name of the table
    // Returns : value
    // Description : return the biggest value
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetMaxValueOfFieldMove($table = TblModCatalogProp) {
        $q = "SELECT `move` FROM `" . $table . "` WHERE 1  ORDER BY `move` desc LIMIT 1";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res OR !$this->db->result)
            return false;
        //$rows = $tmp_db->db_GetNumRows();
        $row = $this->db->db_FetchAssoc();
        return $row['move'];
    }

// end of function GetMaxValueOfFieldMove();
    // ================================================================================================
    // Function : SetValueOfFieldMove
    // Date : 19.05.2006
    // Parms :  $table  - name of the table
    //          $id     - id of th current position
    //          $move   - new valueof field `move`
    // Returns : tru/false
    // Description : set new value $move to field `move` of table $table for current position $id
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function SetValueOfFieldMove($table = NULL, $id = NULL, $move = NULL) {
        $q = "UPDATE `" . $table . "` SET `move`='" . $move . "' WHERE `id`='" . $id . "'";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res OR !$this->db->result)
            return false;
        return true;
    }

// end of function GetMaxValueOfFieldMove();
//------------------------------------------------------------------------------------------------------------
//---------------------------------- FUNCTION FOR PICTURES OF CONTENT ----------------------------------------
//------------------------------------------------------------------------------------------------------------
    /**
    * Class method getPictureAbsPath
    * Write in html tree of catalog
    * @param integer $id_prop - id of the item position
    * @param string $imgName - name of image file
    * @return Absolute path to the image
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 04.12.2012
    */
    function getPictureAbsPath($id_prop, $imgName){
        return SITE_PATH.$this->settings['img_path'].'/'.$id_prop.'/'.$imgName;
    }

        /**
    * Class method getPictureRelPath
    * Write in html tree of catalog
    * @param integer $id_prop - id of the item position
    * @param string $imgName - name of image file
    * @return Relative path to the image
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 04.12.2012
    */
    function getPictureRelPath($id_prop, $imgName){
        return $this->settings['img_path'].'/'.$id_prop.'/'.$imgName;
    }

    // ================================================================================================
    // Function : GetPictureCount
    // Date : 18.11.2010
    // Parms : $id_item  / id of the item position
    // Returns :
    // Description : return count of images for current position with $id_item
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetPictureCount($id_item) {
        $q = "SELECT COUNT(`id`) AS `cnt` FROM `" . TblModCatalogPropImg . "` WHERE `id_prop`='" . $id_item . "'";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res or !$this->db->result)
            return false;
        $row = $this->db->db_FetchAssoc();
        return $row['cnt'];
    }

//end of function GetPictureCount()
    // ================================================================================================
    // Function : GetPicture
    // Date : 03.04.2006
    // Parms :  $id_prop - id of the current positin in catalogue
    // Returns : $res / Void
    // Description : return array with path to the pictures of current product
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetPicture($id_prop, $front_back = 'front', $colId = NULL, $strColId = NULL, $noCol = NULL) {
        $q = "SELECT `" . TblModCatalogPropImg . "`.*, `" . TblModCatalogPropImgTxt . "`.`name` AS `alt`, `" . TblModCatalogPropImgTxt . "`.`text` AS `title`
           FROM `" . TblModCatalogPropImg . "`
           LEFT JOIN `" . TblModCatalogPropImgTxt . "` ON (`" . TblModCatalogPropImg . "`.`id`=`" . TblModCatalogPropImgTxt . "`.`cod` AND `" . TblModCatalogPropImgTxt . "`.`lang_id`='" . $this->lang_id . "')
           WHERE `" . TblModCatalogPropImg . "`.`id_prop`='" . $id_prop . "'";
        if ($front_back == 'front')
            $q .= " AND `" . TblModCatalogPropImg . "`.`show`='1'";
        if (!empty($colId))
            $q.= " AND `" . TblModCatalogPropImg . "`.`colid`='" . $colId . "'";
        if (!empty($strColId))
            $q.= " AND `" . TblModCatalogPropImg . "`.`colid` IN (" . $strColId . ")";
        if ($noCol && $this->settings['imgColors'] == 1)
            $q.= " AND (`" . TblModCatalogPropImg . "`.`colid`='' OR `" . TblModCatalogPropImg . "`.`colid`='-1' OR `" . TblModCatalogPropImg . "`.`colid`='0')";
        $q = $q . " ORDER BY `" . TblModCatalogPropImg . "`.`move`";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
        if (!$res)
            return false;
        if (!$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        $mas = NULL;
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc();
            $mas[$i] = $row;
        }
        //echo '<br> $mas='.$mas['0']['path'];
        return $mas;
    }

// end of function GetPicture()
    // ================================================================================================
    // Function : GetPictureData
    // Date : 03.04.2006
    // Parms :  $id_img - id of the image
    // Returns : $res / Void
    // Description : return array with path to the pictures of current product
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetPictureData($id_img) {
        $q = "SELECT * FROM `" . TblModCatalogPropImg . "` WHERE `id`='" . $id_img . "'";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res OR !$this->db->result)
            return false;
        $row = $this->db->db_FetchAssoc();
        //echo '<br>$row[id]='.$row['id'];
        $q2 = "SELECT `name`, `text` FROM `" . TblModCatalogPropImgTxt . "` WHERE `cod`='" . $id_img . "' AND `lang_id`='" . $this->lang_id . "'";
        $res2 = $this->db->db_Query($q2);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res2 OR !$this->db->result)
            return false;
        $row2 = $this->db->db_FetchAssoc();
        $row['name'] = $row2['name'];
        $row['descr'] = $row2['text'];
        return $row;
    }

// end of function GetPictureData()
    // ================================================================================================
    // Function : GetIdPropByIdImg
    // Date : 26.07.2006
    // Parms :  $id_img - id of the image
    // Returns : $res / Void
    // Description : return alt for this image
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetIdPropByIdImg($id_img) {
        $q = "SELECT `id_prop` FROM `" . TblModCatalogPropImg . "` WHERE `id`='" . $id_img . "'";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res)
            return false;
        if (!$this->db->result)
            return false;
        $row = $this->db->db_FetchAssoc();
        return $row['id_prop'];
    }

// end of function GetIdPropByIdImg()
    // ================================================================================================
    // Function : GetPictureAlt
    // Date : 19.05.2006
    // Parms :  $id_img - id of the image
    // Returns : $res / Void
    // Description : return alt for this image
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetPictureAlt($id_img, $show_name = true) {
        $q = "SELECT `name` FROM `" . TblModCatalogPropImgTxt . "` WHERE `cod`='" . $id_img . "' AND `lang_id`='" . $this->lang_id . "'";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res OR !$this->db->result)
            return false;
        $row = $this->db->db_FetchAssoc();
        $alt = $row['name'];
        //echo '<br>$alt='.$alt;
        if (empty($alt) and $show_name) {
            $q = "SELECT `id_prop` FROM `" . TblModCatalogPropImg . "` WHERE `id`='" . $id_img . "'";
            $res = $this->db->db_Query($q);
            //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
            if (!$res OR !$this->db->result)
                return false;
            $row = $this->db->db_FetchAssoc();

            $alt = $this->Spr->GetNameByCod(TblModCatalogPropSprName, $row['id_prop'], $this->lang_id, 1);
            $id_cat = $this->GetCategory($row['id_prop']);
            //echo '<br>$id_cat='.$id_cat;
            $name_ind = $this->Spr->GetNameByCod(TblModCatalogSprNameInd, $id_cat, $this->lang_id, 1);
            $alt = $name_ind . ' ' . $alt;
        }
        //echo '<br> $alt='.$alt;
        return htmlspecialchars($alt, ENT_QUOTES);
    }

// end of function GetPictureAlt()
    // ================================================================================================
    // Function : GetPictureTitle
    // Date : 19.05.2006
    // Parms :  $id_img - id of the image
    // Returns : $res / Void
    // Description : return title for this image
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetPictureTitle($id_img) {
        $q = "SELECT `text` FROM `" . TblModCatalogPropImgTxt . "` WHERE `cod`='" . $id_img . "' AND `lang_id`='" . $this->lang_id . "'";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res OR !$this->db->result)
            return false;
        $row = $this->db->db_FetchAssoc();
        $alt = htmlspecialchars($row['text'], ENT_QUOTES);
        //echo '<br>$alt='.$alt;
        if (empty($alt)) {
            $alt = $this->GetPictureAlt($id_img);
        }
        //echo '<br> $title='.$alt;
        return $alt;
    }

// end of function GetPictureTitle()

    // ================================================================================================
    // Function : DelPicture
    // Date : 03.04.2006
    // Parms :  $id_img_del - file for upload
    // Returns : $res / Void
    // Description : Remove images from table
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function DelPicture($id_img_del, $id_prop = NULL) {
        $tmp_db = new Rights($this->user_id, $this->module);
        //$settings = $this->GetSettings();
        $del = 0;
        $pathToPropImgDir = SITE_PATH . $this->settings['img_path'] . '/' . $id_prop;
        for ($i = 0; $i < count($id_img_del); $i++) {
            $u = $id_img_del[$i];

            $q = "SELECT * FROM `" . TblModCatalogPropImg . "` WHERE `id`='" . $u . "'";
            $res = $tmp_db->db_Query($q);
            //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
            if (!$res OR !$tmp_db->result)
                return false;
            $row = $tmp_db->db_FetchAssoc();

            $path = $pathToPropImgDir . '/' . $row['path'];
            // delete file which store in the database
            if (file_exists($path)) {
                $res = unlink($path);
                if (!$res)
                    return false;
            }

            $path = SITE_PATH . $this->settings['img_path'] . '/' . $row['id_prop'];
            //echo '<br> $path='.$path;
            if (is_dir($path)) {
                $handle = @opendir($path);
                //echo '<br> $handle='.$handle;
                $cols_files = 0;
                while (($file = readdir($handle)) !== false) {
                    //echo '<br> $file='.$file;
                    $mas_file = explode(".", $file);
                    $mas_img_name = explode(".", $row['path']);
                    if (strstr($mas_file[0], $mas_img_name[0] . ADDITIONAL_FILES_TEXT) and $mas_file[1] == $mas_img_name[1]) {
                        $res = unlink($path . '/' . $file);
                        if (!$res)
                            return false;
                    }
                    if ($file == "." || $file == "..") {
                        $cols_files++;
                    }
                }
                //if ($cols_files==2) rmdir($path);
                closedir($handle);
            }
            $q = "DELETE
                    `" . TblModCatalogPropImg . "`,
                    `" . TblModCatalogPropImgTxt . "`
                  FROM
                    `" . TblModCatalogPropImg . "`
                    LEFT JOIN `" . TblModCatalogPropImgTxt . "` ON (`" . TblModCatalogPropImg . "`.`id` = `" . TblModCatalogPropImgTxt . "`.`cod`)
                  WHERE `" . TblModCatalogPropImg . "`.`id`='" . $u . "'
                 ";
            $res = $tmp_db->Query($q);
            //echo '<br>2q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
            if (!$res OR !$tmp_db->result)
                return false;

            $del = $del + 1;
        }

        $n = $this->GetPictureCount($id_prop);
        if ($n == 0 AND is_dir($pathToPropImgDir))
            $this->full_rmdir($pathToPropImgDir);

        //rebuild "move" of pictures
        if ($n > 0) {
            $this->RebuildPictureMove($id_prop);
        }

        return $del;
    }

// end of function DelPicture()

    /**
     * Class method RebuildPictureMove
     * rebuild values of "move" field in product pictures. It is needed after deletion of one or similar pictures of products
     * @return true/false
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.0, 17.03.2011
     */
    function RebuildPictureMove($id_prop) {
        if (empty($id_prop))
            return false;
        $q = "SELECT * FROM `" . TblModCatalogPropImg . "` WHERE `id_prop`='" . $id_prop . "' ORDER BY `id_prop` asc";
        $res = $this->db->db_Query($q);
        //echo '<br />$q='.$q.' <br />$res='.$res.' $db->result='.$db->result.'<br />$db->errdet='.$db->errdet;
        if (!$res OR !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows($res);
        //echo '<br />$rows='.$rows;
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc($res);
            $arr_img[$row['id_prop']][] = $row;
        }
        $keys = array_keys($arr_img);
        $n = count($keys);
        for ($i = 0; $i < $n; $i++) {
            $n2 = count($arr_img[$keys[$i]]);
            for ($j = 0; $j < $n2; $j++) {
                $q = "UPDATE `" . TblModCatalogPropImg . "` SET
                     `move`='" . ($j + 1) . "'
                     WHERE `id`='" . $arr_img[$keys[$i]][$j]['id'] . "'
                    ";
                $res = $this->db->db_Query($q);
                //echo '<br />$q='.$q.'<br />$res='.$res.' $db->result='.$db->result;
                if (!$res OR !$this->db->result)
                    return false;
            }
        }
        return true;
    }

//end of function RebuildPictureMove()
    // ================================================================================================
    // Function : DelThumbs
    // Date : 17.09.2007
    // Parms :  $id_cat - id of the category
    // Returns : $res / Void
    // Description : Remove small copies of images from all positions from $id_cat
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function DelThumbs($id_cat = 0) {
        $tmp_db = new Rights($this->user_id, $this->module);
        $del = 0;
        if (empty($id_cat))
            $id_cat = 0;
        $str_id_cat = $this->getSubLevels($id_cat);
        //echo '<br>$str_id_cat='.$str_id_cat;
        if (!empty($str_id_cat))
            $q = "SELECT * FROM `" . TblModCatalogProp . "` WHERE `id_cat` IN (" . $str_id_cat . ")";
        else
            $q = "SELECT * FROM `" . TblModCatalogProp . "` WHERE `id_cat`='" . $id_cat . "'";
        $res = $tmp_db->Query($q);
        //echo '<br>q='.$q.' res='.$res.'$tmp_db->result='.$tmp_db->result;
        if (!$res OR !$tmp_db->result)
            return false;
        $rows = $tmp_db->db_GetNumRows();
        //echo '<br>$rows='.$rows;
        for ($i = 0; $i < $rows; $i++) {
            $row = $tmp_db->db_FetchAssoc();
            $path = SITE_PATH . $this->settings['img_path'] . '/' . $row['id'];
            //echo '<br> $path='.$path;
            $handle = @opendir($path);
            //echo '<br> $handle='.$handle;
            if ($handle) {
                while (($file = readdir($handle)) !== false) {
                    //echo '<br> $file='.$file;
                    $mas_file = explode(".", $file);
                    //echo '<br>$mas_file[0]='.$mas_file[0].' $mas_file[1]='.$mas_file[1].' ADDITIONAL_FILES_TEXT='.ADDITIONAL_FILES_TEXT;
                    if (strstr($mas_file[0], ADDITIONAL_FILES_TEXT)) {
                        $res = unlink($path . '/' . $file);
                        //echo '<br>$res='.$res;
                        if (!$res)
                            return false;
                        $del++;
                        //echo '<br>$del='.$del;
                    }
                }//end while
            }//end if
        }//end for
        return $del;
    }

// end of function DelThumbs()
    // ================================================================================================
    // Function : full_rmdir
    // Date : 23.06.2007
    // Parms :  $dirname - directory for full del
    // Returns : $res / Void
    // Description : Full remove directory from disk (all files and subdirectory)
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function full_rmdir($dirname) {
        if ($dirHandle = opendir($dirname)) {
            $old_cwd = getcwd();
            chdir($dirname);

            while ($file = readdir($dirHandle)) {
                if ($file == '.' || $file == '..')
                    continue;

                if (is_dir($file)) {
                    if (!$this->full_rmdir($file))
                        return false;
                }
                else {
                    if (!unlink($file))
                        return false;
                }
            }

            closedir($dirHandle);
            chdir($old_cwd);
            if (!rmdir($dirname))
                return false;

            return true;
        }
        else
            return false;
    }

    // ================================================================================================
    // Function : GetFileData
    // Date : 07.08.2007
    // Parms :  $id_file - id of the file
    // Returns : $res / Void
    // Description : return array with path to the file of current product
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetFileData($id_file) {
        $q = "SELECT `" . TblModCatalogPropFiles . "`.*, `" . TblModCatalogPropFilesTxt . "`.`name`, `" . TblModCatalogPropFilesTxt . "`.`text`
           FROM `" . TblModCatalogPropFiles . "`
           LEFT JOIN `" . TblModCatalogPropFilesTxt . "` ON `" . TblModCatalogPropFiles . "`.`id`=`" . TblModCatalogPropFilesTxt . "`.`cod`
           WHERE `" . TblModCatalogPropFiles . "`.`id`='" . $id_file . "'
          ";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res OR !$this->db->result)
            return false;
        $row = $this->db->db_FetchAssoc();
        return $row;
    }

// end of function GetFileData()
    // ================================================================================================
    // Function : GetFileTitle
    // Date : 03.08.2007
    // Parms :  $id_img - id of the image
    // Returns : $res / Void
    // Description : return title for this file
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetFileTitle($id_file) {
        $alt = htmlspecialchars($this->Spr->GetNameByCod(TblModCatalogPropFilesSprTitle, $id_file, $this->lang_id, 1));
        //echo '<br>$alt='.$alt;
        return $alt;
    }

// end of function GetFileTitle()
    // ================================================================================================
    // Function : GetFileDescr
    // Date : 03.08.2007
    // Parms :  $id_img - id of the image
    // Returns : $res / Void
    // Description : return description for this file
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetFileDescr($id_file) {
        $alt = htmlspecialchars($this->Spr->GetNameByCod(TblModCatalogPropFilesSprDescr, $id_file, $this->lang_id, 1));
        //echo '<br>$alt='.$alt;
        return $alt;
    }

// end of function GetFileDescr()

// end of function DelFilesByIdProp()
    // ================================================================================================
    // Function : DelFiles
    // Date : 03.08.2007
    // Parms :  $id_img_del - file for upload
    // Returns : $res / Void
    // Description : Remove files from table
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function DelFiles($id_img_del, $id_prop) {
        $del = 0;
        $pathPropFilesDir = SITE_PATH . $this->settings['files_path'] . '/' . $id_prop;
        for ($i = 0; $i < count($id_img_del); $i++) {
            $u = $id_img_del[$i];

            $q = "SELECT * FROM `" . TblModCatalogPropFiles . "` WHERE `id`='" . $u . "'";
            $res = $this->db->db_Query($q);
            //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
            if (!$res OR !$this->db->result)
                return false;
            $row = $this->db->db_FetchAssoc();
            $path = $pathPropFilesDir . '/' . $row['path'];
            // delete file which store in the database
            if (file_exists($path)) {
                $res = unlink($path);
                if (!$res)
                    return false;
            }

            $q = "DELETE FROM `" . TblModCatalogPropFilesTxt . "` WHERE `cod`='" . $u . "'";
            $res = $this->db->db_Query($q);
            //echo '<br>2q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
            if (!$res OR !$this->db->result)
                return false;

            $q = "DELETE FROM `" . TblModCatalogPropFiles . "` WHERE `id`='" . $u . "'";
            $res = $this->db->db_Query($q);
            //echo '<br>2q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
            if (!$res OR !$this->db->result)
                return false;

            $del = $del + 1;

            //closedir($handle);
        }

        $n = $this->GetFilesCount($id_prop);
        if ($n == 0 AND is_dir($pathPropFilesDir))
            $this->full_rmdir($pathPropFilesDir);

        return $del;
    }

// end of function DelFiles()
    // ================================================================================================
    // Function : CheckImages
    // Date : 17.11.2006
    // Returns : $res / Void
    // Description : check uploaded images for size, type and other.
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function CheckImages() {
        //$this->Err = NULL;
        $max_image_width = MAX_IMAGE_WIDTH;
        $max_image_height = MAX_IMAGE_HEIGHT;
        $max_image_size = MAX_IMAGE_SIZE;
        $valid_types = array("gif", "GIF", "jpg", "JPG", "png", "PNG", "jpeg", "JPEG");
        //print_r($_FILES["image"]);
        if ($this->settings['imgColors'] == 1) {
            $colorsArr = array();
            if (strlen($this->colorsStr) > 0 && isset($this->colorsStr) && !empty($this->colorsStr)) {
                $colorsArr = explode(",", $this->colorsStr);
            } else {
                $colorsArr = $this->getColorsArr();
            }
            if (!isset($_FILES["image"]))
                return false;
            if (count($colorsArr) > 0) {


                //echo '<br><br>$cols='.$cols;
                for ($c = 0; $c < count($colorsArr); $c++) {
                    if (!isset($_FILES["image"]["name"][$colorsArr[$c]]))
                        continue;
                    $cols = count($_FILES["image"]["name"][$colorsArr[$c]]);
                    for ($i = 0; $i < $cols; $i++) {//colors
                        //echo '<br>$_FILES["image"]='.$_FILES["image"].' $_FILES["image"]["tmp_name"]["'.$i.'"]='.$_FILES["image"]["tmp_name"]["$i"].' $_FILES["image"]["size"]["'.$i.'"]='.$_FILES["image"]["size"]["$i"];
                        //echo '<br>$_FILES["image"]["name"][$i]='.$_FILES["image"]["name"][$i];
                        if (!empty($_FILES["image"]["name"][$colorsArr[$c]][$i])) {
                            //echo '<br>is_uploaded_file($_FILES["image"]["tmp_name"][$i])='.is_uploaded_file($_FILES["image"]["tmp_name"][$i]);
                            if (isset($_FILES["image"]) && is_uploaded_file($_FILES["image"]["tmp_name"][$colorsArr[$c]][$i]) && $_FILES["image"]["size"][$colorsArr[$c]][$i]) {
                                $filename = $_FILES['image']['tmp_name'][$colorsArr[$c]][$i];
                                $ext = substr($_FILES['image']['name'][$colorsArr[$c]][$i], 1 + strrpos($_FILES['image']['name'][$colorsArr[$c]][$i], "."));
                                //echo '<br>filesize($filename)='.filesize($filename).' $max_image_size='.$max_image_size;


                                if ($_FILES["image"]["size"][$colorsArr[$c]][$i] > $max_image_size) {
                                    $this->Err = $this->Err . $this->Msg->show_text('MSG_ERR_FILE_SIZE') . ' (' . $_FILES['image']['name'][$colorsArr[$c]][$i] . ')<br>';
                                    continue;
                                }

                                if (!in_array($ext, $valid_types)) {
                                    $this->Err = $this->Err . $this->Msg->show_text('MSG_ERR_FILE_TYPE') . ' (' . $_FILES['image']['name'][$colorsArr[$c]][$i] . ')<br>';
                                } else {
                                    $size = GetImageSize($filename);
                                    //echo '<br>$size='.$size.'$size[0]='.$size[0].' $max_image_width='.$max_image_width.' $size[1]='.$size[1].' $max_image_height='.$max_image_height;
                                    if (($size) && ($size[0] < $max_image_width) && ($size[1] < $max_image_height)) {
                                        //$alias = $this->Spr->GetNameByCod( TblModCatalogPropSprName, $this->id );
                                    } else {
                                        $this->Err = $this->Err . $this->Msg->show_text('MSG_ERR_FILE_PROPERTIES') . ' [' . $max_image_width . 'x' . $max_image_height . '] (' . $_FILES['image']['name'][$colorsArr[$c]][$i] . ')<br>';
                                    }
                                }
                            }
                            else
                                $this->Err = $this->Err . $this->Msg->show_text('MSG_ERR_FILE') . ' (' . $_FILES['image']['name'][$colorsArr[$c]][$i] . ')<br>';
                        }
                        //echo '<br>$i='.$i;
                    } // end for
                }//end colors for
            }
            //=====================end colors=================
        }else {
            $cols = count($_FILES["image"]["name"]);

            //echo '<br><br>$cols='.$cols;
            for ($i = 0; $i < $cols; $i++) {
                //echo '<br>$_FILES["image"]='.$_FILES["image"].' $_FILES["image"]["tmp_name"]["'.$i.'"]='.$_FILES["image"]["tmp_name"]["$i"].' $_FILES["image"]["size"]["'.$i.'"]='.$_FILES["image"]["size"]["$i"];
                //echo '<br>$_FILES["image"]["name"][$i]='.$_FILES["image"]["name"][$i];
                if (!empty($_FILES["image"]["name"][$i])) {
                    //echo '<br>is_uploaded_file($_FILES["image"]["tmp_name"][$i])='.is_uploaded_file($_FILES["image"]["tmp_name"][$i]);
                    if (isset($_FILES["image"]) && is_uploaded_file($_FILES["image"]["tmp_name"][$i]) && $_FILES["image"]["size"][$i]) {
                        $filename = $_FILES['image']['tmp_name'][$i];
                        $ext = substr($_FILES['image']['name'][$i], 1 + strrpos($_FILES['image']['name'][$i], "."));
                        //echo '<br>filesize($filename)='.filesize($filename).' $max_image_size='.$max_image_size;


                        if ($_FILES["image"]["size"][$i] > $max_image_size) {
                            $this->Err = $this->Err . $this->Msg->show_text('MSG_ERR_FILE_SIZE') . ' (' . $_FILES['image']['name']["$i"] . ')<br>';
                            continue;
                        }

                        if (!in_array($ext, $valid_types)) {
                            $this->Err = $this->Err . $this->Msg->show_text('MSG_ERR_FILE_TYPE') . ' (' . $_FILES['image']['name']["$i"] . ')<br>';
                        } else {
                            $size = GetImageSize($filename);
                            //echo '<br>$size='.$size.'$size[0]='.$size[0].' $max_image_width='.$max_image_width.' $size[1]='.$size[1].' $max_image_height='.$max_image_height;
                            if (($size) && ($size[0] < $max_image_width) && ($size[1] < $max_image_height)) {
                                //$alias = $this->Spr->GetNameByCod( TblModCatalogPropSprName, $this->id );
                            } else {
                                $this->Err = $this->Err . $this->Msg->show_text('MSG_ERR_FILE_PROPERTIES') . ' [' . $max_image_width . 'x' . $max_image_height . '] (' . $_FILES['image']['name']["$i"] . ')<br>';
                            }
                        }
                    }
                    else
                        $this->Err = $this->Err . $this->Msg->show_text('MSG_ERR_FILE') . ' (' . $_FILES['image']['name']["$i"] . ')<br>';
                }
                //echo '<br>$i='.$i;
            } // end for
        }//end if
        return $this->Err;
    }

// end of function CheckImages()

    function getColorsArr() {
        $q = "SELECT `colors` FROM `" . TblModCatalogProp . "` WHERE `id`='" . $this->id . "'";
        $res = $this->db->db_Query($q);
        $row = $this->db->db_FetchAssoc();
        if(!empty($row['colors'])) {
            $colorsArr = explode(",", $row['colors']);
        }
        $colorsArr[] = -1;
        $this->colorsStr = implode(',', $colorsArr);
        return $colorsArr;
    }

    function SaveOrderImg() {
        //print_r($this->imagesOrder);die();
        $move = 1;
        if ($this->settings['imgColors'] == 1) {
            if (strlen($this->colorsStr) > 0 && isset($this->colorsStr) && !empty($this->colorsStr)) {
                $colorsArr = explode(",", $this->colorsStr);
            } else {
                $colorsArr = $this->getColorsArr();
            }

            for ($c = 0; $c < count($colorsArr); $c++) {
                if (isset($this->imagesOrder[$colorsArr[$c]]) && strlen($this->imagesOrder[$colorsArr[$c]]) > 0) {
                    $imagesArr = explode(",", $this->imagesOrder[$colorsArr[$c]]);
                    foreach ($imagesArr as $key => $value) {
                        $q = "UPDATE `" . TblModCatalogPropImg . "` SET `move`='" . $move . "', `colid`='" . $colorsArr[$c] . "'
                            WHERE `id`='" . $value . "'";
                        $res = $this->db->db_Query($q);
                        $move++;
                    }
                }
            }
        } else {
            $imagesArr = explode(",", $this->imagesOrder);
            foreach ($imagesArr as $key => $value) {
                $q = "UPDATE `" . TblModCatalogPropImg . "` SET `move`='" . $move . "', `colid`='-1'
                                WHERE `id`='" . $value . "'";
                $res = $this->db->db_Query($q);
                $move++;
            }
        }
    }

    // ================================================================================================
    // Function : SavePicture
    // Date : 03.04.2006
    // Returns : $res / Void
    // Description : Save the file (image) to the folder  and save path in the database (table user_images)
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function SavePicture() {
        $this->Err = NULL;
        $max_image_width = STORE_IMAGE_WIDTH;
        $max_image_height = STORE_IMAGE_HEIGHT;
        $max_image_size = MAX_IMAGE_SIZE;
        $max_image_quantity = MAX_IMAGES_QUANTITY;
        $valid_types = array("gif", "GIF", "jpg", "JPG", "png", "PNG", "jpeg", "JPEG");
        ini_set('post_max_size', '64M');
        ini_set('upload_max_filesize', '64M');
        //print_r($_FILES["image"]);
        if ($this->settings['imgColors'] == 1) {
            if (strlen($this->colorsStr) > 0 && isset($this->colorsStr) && !empty($this->colorsStr)) {
                $colorsArr = explode(",", $this->colorsStr);
            }
            if (!isset($_FILES["image"]))
                return false;
            if (count($colorsArr) > 0) {//print_r($colorsArr);die();
                //echo '<br><br>$cols='.$cols;
                for ($c = 0; $c < count($colorsArr); $c++) {
                    if (!isset($_FILES["image"]["name"][$colorsArr[$c]]))
                        continue;
                    $cols = count($_FILES["image"]["name"][$colorsArr[$c]]);
                    for ($i = 0; $i < $cols; $i++) {
                        //echo '<br>$_FILES["image"]='.$_FILES["image"].' $_FILES["image"]["tmp_name"]["'.$i.'"]='.$_FILES["image"]["tmp_name"]["$i"].' $_FILES["image"]["size"]["'.$i.'"]='.$_FILES["image"]["size"]["$i"];
                        //echo '<br>$_FILES["image"]["name"][$i]='.$_FILES["image"]["name"][$i];

                        if (!empty($_FILES["image"]["name"][$colorsArr[$c]][$i])) {
                            if (isset($_FILES["image"]) && is_uploaded_file($_FILES["image"]["tmp_name"][$colorsArr[$c]][$i]) && $_FILES["image"]["size"][$colorsArr[$c]][$i]) {
                                $filename = $_FILES['image']['tmp_name'][$colorsArr[$c]][$i];
                                $ext = substr($_FILES['image']['name'][$colorsArr[$c]][$i], 1 + strrpos($_FILES['image']['name'][$colorsArr[$c]][$i], "."));
                                //echo '<br>filesize($filename)='.filesize($filename).' $max_image_size='.$max_image_size;

                                /*
                                  if (filesize($filename) > $max_image_size) {
                                  $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_SIZE').' '.intval($max_image_size/1000).'b ('.$_FILES['image']['name']["$i"].')<br>';
                                  continue;
                                  }
                                 */
                                if (!in_array($ext, $valid_types)) {
                                    $this->Err = $this->Err . $this->Msg->show_text('MSG_ERR_FILE_TYPE') . ' (' . $_FILES['image']['name']["$c"]["$i"] . ')<br>';
                                } else {
                                    $size = GetImageSize($filename);
                                    //echo '<br>$size='.$size.'$size[0]='.$size[0].' $max_image_width='.$max_image_width.' $size[1]='.$size[1].' $max_image_height='.$max_image_height;
                                    //if (($size) && ($size[0] < $max_image_width) && ($size[1] < $max_image_height)){
                                    //$alias = $this->Spr->GetNameByCod( TblModCatalogPropSprName, $this->id );
                                    $alias = $this->id;
                                    $uploaddir = SITE_PATH . $this->settings['img_path'] . '/' . $alias;
                                    $uploaddir_0 = $uploaddir;
                                    if (!file_exists($uploaddir))
                                        mkdir($uploaddir, 0777);
                                    else
                                        @chmod($uploaddir, 0777);
                                    $uploaddir2 = time() . $i . $c . '.' . $ext;
                                    $uploaddir = $uploaddir . "/" . $uploaddir2;
                                    //$uploaddir = $uploaddir."/".$_FILES['image']['name']["$i"];
                                    //$uploaddir2 = $_FILES['image']['name']["$i"];
                                    //echo '<br>$filename='.$filename.'<br> $uploaddir='.$uploaddir.'<br> $uploaddir2='.$uploaddir2;
                                    //if (@move_uploaded_file($filename, $uploaddir)) {
                                    if (@copy($filename, $uploaddir)) {
                                        //====== set next max value for move START ============
                                        $maxx = NULL;
                                        $q = "SELECT MAX(`move`) AS `maxx` FROM `" . TblModCatalogPropImg . "` WHERE `id_prop`='" . $this->id . "'";
                                        $res = $this->db->db_Query($q);
                                        $row = $this->db->db_FetchAssoc();
                                        $maxx = $row['maxx'] + 1;
                                        //====== set next max value for move END ==============
                                        if ($colorsArr[$c] == 'noimageidProp')
                                            $q = "INSERT INTO `" . TblModCatalogPropImg . "` values(NULL,'" . $this->id . "','" . $uploaddir2 . "','1', '" . $maxx . "',-1)";
                                        else
                                            $q = "INSERT INTO `" . TblModCatalogPropImg . "` values(NULL,'" . $this->id . "','" . $uploaddir2 . "','1', '" . $maxx . "'," . $colorsArr[$c] . ")";
                                        $res = $this->db->db_Query($q);
                                        if (!$this->db->result)
                                            $this->Err = $this->Err . $this->Msg->show_text('MSG_ERR_SAVE_FILE_TO_DB') . ' (' . $_FILES['image']['name'][$colorsArr[$c]]["$i"] . ')<br>';
                                        //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;

                                        if (($size) AND (($size[0] > $max_image_width) OR ($size[1] > $max_image_height))) {
                                            //============= resize original image to size from settings =============
                                            $thumb = new Thumbnail($uploaddir);
                                            if ($max_image_width == $max_image_height)
                                                $thumb->size_auto($max_image_width);
                                            else {
                                                $thumb->size_width($max_image_width);
                                                $thumb->size_height($max_image_height);
                                            }
                                            $thumb->quality = $max_image_quantity;
                                            $thumb->process();       // generate image
                                            $thumb->save($uploaddir); //make new image
                                            //=======================================================================
                                        }
                                    } else {
                                        $this->Err = $this->Err . $this->Msg->show_text('MSG_ERR_FILE_MOVE') . ' (' . $_FILES['image']['name']["$c"]["$i"] . ')<br>';
                                    }
                                    @chmod($uploaddir_0, 0755);
                                    //}
                                    //else {
                                    //   $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_PROPERTIES').' ['.$max_image_width.'x'.$max_image_height.']px ('.$_FILES['image']['name']["$i"].')<br>';
                                    //}
                                }
                            }
                            else
                                $this->Err = $this->Err . $this->Msg->show_text('MSG_ERR_FILE') . ' (' . $_FILES['image']['name']["$c"]["$i"] . ')<br>';
                        }
                        //echo '<br>$i='.$i;
                    } // end for
                }//end for colors
            }
        }else {
            $cols = count($_FILES["image"]["name"]);
            //print_r($_FILES["image"]["name"]);die();
            //$settings = $this->GetSettings();
            //echo '<br><br>$cols='.$cols;
            for ($i = 0; $i < $cols; $i++) {
                //echo '<br>$_FILES["image"]='.$_FILES["image"].' $_FILES["image"]["tmp_name"]["'.$i.'"]='.$_FILES["image"]["tmp_name"]["$i"].' $_FILES["image"]["size"]["'.$i.'"]='.$_FILES["image"]["size"]["$i"];
                //echo '<br>$_FILES["image"]["name"][$i]='.$_FILES["image"]["name"][$i];

                if (!empty($_FILES["image"]["name"][$i])) {
                    if (isset($_FILES["image"]) && is_uploaded_file($_FILES["image"]["tmp_name"][$i]) && $_FILES["image"]["size"][$i]) {
                        $filename = $_FILES['image']['tmp_name'][$i];
                        $ext = substr($_FILES['image']['name'][$i], 1 + strrpos($_FILES['image']['name'][$i], "."));
                        //echo '<br>filesize($filename)='.filesize($filename).' $max_image_size='.$max_image_size;

                        /*
                          if (filesize($filename) > $max_image_size) {
                          $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_SIZE').' '.intval($max_image_size/1000).'b ('.$_FILES['image']['name']["$i"].')<br>';
                          continue;
                          }
                         */
                        if (!in_array($ext, $valid_types)) {
                            $this->Err = $this->Err . $this->Msg->show_text('MSG_ERR_FILE_TYPE') . ' (' . $_FILES['image']['name']["$i"] . ')<br>';
                        } else {
                            $size = GetImageSize($filename);
                            //echo '<br>$size='.$size.'$size[0]='.$size[0].' $max_image_width='.$max_image_width.' $size[1]='.$size[1].' $max_image_height='.$max_image_height;
                            //if (($size) && ($size[0] < $max_image_width) && ($size[1] < $max_image_height)){
                            //$alias = $this->Spr->GetNameByCod( TblModCatalogPropSprName, $this->id );
                            $alias = $this->id;
                            $uploaddir = SITE_PATH . $this->settings['img_path'] . '/' . $alias;
                            $uploaddir_0 = $uploaddir;
                            if (!file_exists($uploaddir))
                                mkdir($uploaddir, 0777);
                            else
                                @chmod($uploaddir, 0777);
                            $uploaddir2 = time() . $i . '.' . $ext;
                            $uploaddir = $uploaddir . "/" . $uploaddir2;
                            //$uploaddir = $uploaddir."/".$_FILES['image']['name']["$i"];
                            //$uploaddir2 = $_FILES['image']['name']["$i"];
                            //echo '<br>$filename='.$filename.'<br> $uploaddir='.$uploaddir.'<br> $uploaddir2='.$uploaddir2;
                            //if (@move_uploaded_file($filename, $uploaddir)) {
                            if (@copy($filename, $uploaddir)) {
                                //====== set next max value for move START ============
                                $maxx = NULL;
                                $q = "SELECT MAX(`move`) AS `maxx` FROM `" . TblModCatalogPropImg . "` WHERE `id_prop`='" . $this->id . "'";
                                $res = $this->db->db_Query($q);
                                $row = $this->db->db_FetchAssoc();
                                $maxx = $row['maxx'] + 1;
                                //====== set next max value for move END ==============

                                $q = "INSERT INTO `" . TblModCatalogPropImg . "` values(NULL,'" . $this->id . "','" . $uploaddir2 . "','1', '" . $maxx . "',-1)";
                                $res = $this->db->db_Query($q);
                                if (!$this->db->result)
                                    $this->Err = $this->Err . $this->Msg->show_text('MSG_ERR_SAVE_FILE_TO_DB') . ' (' . $_FILES['image']['name']["$i"] . ')<br>';
                                //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;

                                if (($size) AND (($size[0] > $max_image_width) OR ($size[1] > $max_image_height))) {
                                    //============= resize original image to size from settings =============
                                    $thumb = new Thumbnail($uploaddir);
                                    if ($max_image_width == $max_image_height)
                                        $thumb->size_auto($max_image_width);
                                    else {
                                        $thumb->size_width($max_image_width);
                                        $thumb->size_height($max_image_height);
                                    }
                                    $thumb->quality = $max_image_quantity;
                                    $thumb->process();       // generate image
                                    $thumb->save($uploaddir); //make new image
                                    //=======================================================================
                                }
                            } else {
                                $this->Err = $this->Err . $this->Msg->show_text('MSG_ERR_FILE_MOVE') . ' (' . $_FILES['image']['name']["$i"] . ')<br>';
                            }
                            @chmod($uploaddir_0, 0755);
                            //}
                            //else {
                            //   $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_PROPERTIES').' ['.$max_image_width.'x'.$max_image_height.']px ('.$_FILES['image']['name']["$i"].')<br>';
                            //}
                        }
                    }
                    else
                        $this->Err = $this->Err . $this->Msg->show_text('MSG_ERR_FILE') . ' (' . $_FILES['image']['name']["$i"] . ')<br>';
                }
                //echo '<br>$i='.$i;
            } // end for
        }
        return $this->Err;
    }

// end of function SavePicture()

    /**
     * Resize catalog image to the given size. Either the width or the height can
     * be omitted and the image will be resized proportionally.
     * @param string $img - id of the picture, or relative path of the picture /images/mod_catalog_prod/24094/12984541610.jpg or name of the picture 12984541610.jpg
     * @param string $size  can be 'size_width=XXX', 'size_height=XXX', 'size_auto=XXX', 'size_square=XXX', 'size_rect=XXXxYYY' where XXX and YYY number in pixels
     * @param integer $quality quality of image to store
     * @param boolen $wtm - use watermark or not
     * @param string $parameters - other parameters for TAG <img> like border
     * @param integer $id_prop - id of the catalog position.
     * @param boolean $return_src - id of the catalog position.
     * @return  reletive path to resized img
     */
    function ShowCurrentImage($img = NULL, $size = NULL, $quality = NULL, $wtm = NULL, $parameters = NULL, $id_prop = NULL, $return_src = false)
    {
        if (!strstr($img, '.') AND !strstr($img, '/')) {
            $img_data = $this->GetPictureData($img);
            if (!isset($img_data['id_prop'])) {
                return false;
            }
            $img_with_path = $this->getPictureRelPath($img_data['id_prop'], $img_data['path']);
        }
        else {
            //$settings_img_path = $settings['img_path'].'/categories';
            $rpos = strrpos($img, '/');
            if ($rpos > 0) {
                $img_with_path = $img;
            }else {
                if (!$id_prop){
                    return false;
                }
                $img_with_path = $this->getPictureRelPath($id_prop, $img);
            }
            $alt = '';
            $title = '';
        }
        $imgSmall = ImageK::getResizedImg($img_with_path, $size, $quality, $wtm);
        if($return_src){
            return $imgSmall;
        }else{
            return '<img src="'.$imgSmall.'" '.$parameters.' />';
        }
    }// end of function ShowCurrentImage()

    /**
     * Resize catalog category image to the given size. Either the width or the height can
     * be omitted and the image will be resized proportionally.
     * @param string $img - id of the picture, or relative path of the picture /images/mod_catalog_prod/24094/12984541610.jpg or name of the picture 12984541610.jpg
     * @param string $size  can be 'size_width=XXX', 'size_height=XXX', 'size_auto=XXX', 'size_square=XXX', 'size_rect=XXXxYYY' where XXX and YYY number in pixels
     * @param integer $quality quality of image to store
     * @param boolen $wtm - use watermark or not
     * @param string $parameters - other parameters for TAG <img> like border
     * @param boolean $return_src - id of the catalog position.
     * @return  reletive path to resized img
     */
    function ShowCategoryImage($img = NULL, $size = NULL, $quality = NULL, $wtm = NULL, $parameters = NULL, $return_src = false) {
        if(empty($img)){
            return false;
        }
        $rpos = strrpos($img, '/');
        if ($rpos > 0) {
            $settings_img_path = substr($img, 0, $rpos);
            $img_name = substr($img, $rpos + 1, strlen($img) - $rpos);
            $img_with_path = $img;
        } else {
            $settings_img_path = $this->settings['img_path'] . '/categories'; // like /uploads/45
            $img_with_path = $settings_img_path . '/' . $img;
        }
        $alt = '';
        $title = '';
        //echo '<br>$img_with_path='.$img_with_path;
        $imgSmall = ImageK::getResizedImg($img_with_path, $size, $quality, $wtm);
        if($return_src){
            return $imgSmall;
        }else{
            return '<img src="'.$imgSmall.'" '.$parameters.' />';
        }
    }
//
    // ================================================================================================
    // Function : ShowCurrentImageCat
    // Date : 07.09.2010
    // Parms :  $img - id of the picture, or path of the picture
    //          $parameters - other parameters for TAG <img> like border
    // Returns : $res / Void
    // Description : Show images from category
    // Programmer : Oleg Morgalyuk
    // ================================================================================================
    function ShowCurrentImageCat($img, $width = NULL, $height = NULL, $hor_align = true, $ver_align = true, $quality = 85, $wtm = NULL, $parameters = NULL) {
        if (empty($img))
            return '';
        //echo '<br>img='.$img;
        $settings_img_path = $this->settings['img_path'] . '/categories'; // like /uploads/45
        $img_name = $img;  // like R1800TII_big.jpg
        $img_with_path = $settings_img_path . '/' . $img; // like /uploads/45/R1800TII_big.jpg
        //echo '<br>SITE_PATH.$settings_img_path='.SITE_PATH.$settings_img_path;
        //echo '<br>$img_with_path='.$img_with_path;
        //echo '<br>$img_name='.$img_name;

        $mas_img_name = explode(".", $img_with_path);

        if (isset($width) && isset($height)) {
            $img_name_new = $mas_img_name[0] . ADDITIONAL_FILES_TEXT . $width . 'x' . $height . '.' . $mas_img_name[1];
        } elseif (empty($size))
            $img_name_new = $mas_img_name[0] . '.' . $mas_img_name[1];
        //echo '$img_name_new='.$img_name_new;
        $img_full_path_new = SITE_PATH . $img_name_new;
        //if exist local small version of the image then use it
        if (file_exists($img_full_path_new)) {
            //echo 'exist';
            //echo '<br>$settings_img_path='.$settings_img_path.' $img_full_path='.$img_full_path;
            $str = '<img src="' . $img_name_new . '" ' . $parameters . ' />';
        }
        //else use original image on the server SITE_PATH and make small version on local server
        else {
            //echo 'Not  exist';
            $img_full_path = SITE_PATH . $img_with_path; // like z:/home/speakers/www/uploads/45/R1800TII_big.jpg
            //echo '<br> $img_full_path='.$img_full_path.'<br> $size_auto='.$size_auto;
            if (!file_exists($img_full_path))
                return false;

            $thumb = new Thumbnail($img_full_path);
//            echo '<br>$thumb->img[x_thumb]='.$thumb->img['x_thumb'].' $thumb->img[y_thumb]='.$thumb->img['y_thumb'];
            $src_x = $thumb->img['x_thumb'];
            $src_y = $thumb->img['y_thumb'];
            if (!empty($width) and !empty($height))
                $thumb->sizeEx($width, $height);
            //echo '<br>$thumb->img[x_thumb]='.$thumb->img['x_thumb'].' $thumb->img[y_thumb]='.$thumb->img['y_thumb'];
            //if original image smaller than thumbnail then use original image and don't create thumbnail
            if (($src_x <= $thumb->img['x_thumb']) && ($src_y <= $thumb->img['y_thumb'])) {
                $img_full_path = $settings_img_path . '/' . $img_name;
                //echo '<br>$settings_img_path='.$settings_img_path.' $img_full_path='.$img_full_path;
                if (!strstr($parameters, 'alt'))
                    $alt = $this->GetPictureAlt($img);
                if (!strstr($parameters, 'title'))
                    $title = $this->GetPictureTitle($img);
                if (!strstr($parameters, 'alt'))
                    $parameters = $parameters . ' alt="' . $alt . '"';
                if (!strstr($parameters, 'title'))
                    $parameters = $parameters . ' title=" ' . $title . ' "';
                $str = '<img src="' . $img_full_path . '" ' . $parameters . ' />';
            }
            else {
                $thumb->quality = $quality;                  //default 75 , only for JPG format
                //echo '<br>$wtm='.$wtm;

                $thumb->quality = $quality;                  //default 75 , only for JPG format
                if ($thumb->img['x_thumb'] >= $src_x AND $thumb->img['y_thumb'] <= $src_y) {
                    $this->img['x_thumb'] = $src_x;
                    $width = $src_x;
                }
                if ($thumb->img['x_thumb'] <= $src_x AND $thumb->img['y_thumb'] >= $src_y) {
                    $this->img['y_thumb'] = $src_y;
                    $height = $src_y;
                }

                if ($wtm == 'img') {
                    $thumb->img_watermark = NULL; //SITE_PATH.'/images/design/m01.png';        // [OPTIONAL] set watermark source file, only PNG format [RECOMENDED ONLY WITH GD 2 ]
                    $thumb->img_watermark_Valing = 'CENTER';           // [OPTIONAL] set watermark vertical position, TOP | CENTER | BOTTOM
                    $thumb->img_watermark_Haling = 'CENTER';           // [OPTIONAL] set watermark horizonatal position, LEFT | CENTER | RIGHT
                }
                if ($wtm == 'txt') {
                    if (defined('WATERMARK_TEXT'))
                        $thumb->txt_watermark = WATERMARK_TEXT;        // [OPTIONAL] set watermark text [RECOMENDED ONLY WITH GD 2 ]
                    else
                        $thumb->txt_watermark = '';
                    $thumb->txt_watermark_color = '000000';        // [OPTIONAL] set watermark text color , RGB Hexadecimal[RECOMENDED ONLY WITH GD 2 ]
                    $thumb->txt_watermark_font = 5;                // [OPTIONAL] set watermark text font: 1,2,3,4,5
                    $thumb->txt_watermark_Valing = 'TOP';           // [OPTIONAL] set watermark text vertical position, TOP | CENTER | BOTTOM
                    $thumb->txt_watermark_Haling = 'LEFT';       // [OPTIONAL] set watermark text horizonatal position, LEFT | CENTER | RIGHT
                    $thumb->txt_watermark_Hmargin = 10;          // [OPTIONAL] set watermark text horizonatal margin in pixels
                    $thumb->txt_watermark_Vmargin = 10;           // [OPTIONAL] set watermark text vertical margin in pixels
                }

                if (intval($img) > 0) {
                    $mas_img_name = explode(".", $img_name);
                    //$img_name_new = $mas_img_name[0].ADDITIONAL_FILES_TEXT.intval($thumb->img['x_thumb']).'x'.intval($thumb->img['y_thumb']).'.'.$mas_img_name[1];
                    if (isset($width) && isset($height)) {
                        $img_name_new = $mas_img_name[0] . ADDITIONAL_FILES_TEXT . $width . 'x' . $height . '.' . $mas_img_name[1];
                    }
                    $img_full_path_new = SITE_PATH . $settings_img_path . '/' . $img_name_new;
                    $img_src = $settings_img_path . '/' . $img_name_new;
                    $uploaddir = SITE_PATH . $settings_img_path;
                } else {
                    $mas_img_name = explode(".", $img_with_path);
                    //$img_name_new = $mas_img_name[0].ADDITIONAL_FILES_TEXT.intval($thumb->img['x_thumb']).'x'.intval($thumb->img['y_thumb']).'.'.$mas_img_name[1];
                    if (isset($width) && isset($height)) {
                        $img_name_new = $mas_img_name[0] . ADDITIONAL_FILES_TEXT . $width . 'x' . $height . '.' . $mas_img_name[1];
                    }
                    $img_full_path_new = SITE_PATH . $img_name_new;
                    $img_src = $img_name_new;
                    $uploaddir = substr($img_with_path, 0, strrpos($img_with_path, '/'));
                }
                //echo '<br>$img_name_new='.$img_name_new;
                //echo '<br>$img_full_path_new='.$img_full_path_new;
                //echo '<br>$img_src='.$img_src;
                //echo '<br>$uploaddir='.$uploaddir;
                //echo '<br>$uploaddir='.$uploaddir;
                if (!file_exists($img_full_path_new)) {
                    if (!file_exists($uploaddir))
                        mkdir($uploaddir, 0777);
                    if (file_exists($uploaddir))
                        @chmod($uploaddir, 0777);
                    $thumb->processEx($ver_align, $hor_align);       // generate image
                    //make new image like R1800TII_big.jpg -> R1800TII_big_autozoom_100x84.jpg
                    $thumb->save($img_full_path_new);
                    @chmod($uploaddir, 0755);
                    $params = "img=" . $img . "&" . $width;
                }
                $str = '<img src="' . $img_src . '" ' . $parameters . ' />';
            }//end else
        }//end else
        return $str;
    }

// end of function ShowCurrentImageCat()
    // ================================================================================================
    // Function : GetExtationOfFile()
    // Date : 08.02.2008
    // Parms :  $filename - file name
    // Returns :    $ext
    // Description : return extation of filename
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function GetExtationOfFile($filename) {
        return $ext = substr($filename, 1 + strrpos($filename, "."));
    }

//------------------------------------------------------------------------------------------------------------
//---------------------------------- FUNCTION FOR FILES OF CONTENT ----------------------------------------
//------------------------------------------------------------------------------------------------------------
    // ================================================================================================
    // Function : GetFilesCount
    // Date : 18.11.2010
    // Parms : $id_item  / id of the item position
    // Returns :
    // Description : return count of files for current position with $id_item
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetFilesCount($id_item) {
        $q = "SELECT COUNT(`id`) AS `cnt` FROM `" . TblModCatalogPropFiles . "` WHERE `id_prop`='" . $id_item . "'";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res or !$this->db->result)
            return false;
        $row = $this->db->db_FetchAssoc();
        return $row['cnt'];
    }

//end of function GetFilesCount()
    // ================================================================================================
    // Function : GetFiles
    // Date : 02.08.2007
    // Parms :  $id_prop - id of the current positin in catalogue
    // Returns : $res / Void
    // Description : return array with path to the files of current product
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetFiles($id_prop, $front_back = 'front') {
        $q = "SELECT `" . TblModCatalogPropFiles . "`.*, `" . TblModCatalogPropFilesTxt . "`.`name`, `" . TblModCatalogPropFilesTxt . "`.`text`
           FROM `" . TblModCatalogPropFiles . "`
           LEFT JOIN `" . TblModCatalogPropFilesTxt . "` ON `" . TblModCatalogPropFiles . "`.`id`=`" . TblModCatalogPropFilesTxt . "`.`cod`
           WHERE `" . TblModCatalogPropFiles . "`.`id_prop`='" . $id_prop . "'
          ";
        if ($front_back == 'front')
            $q = $q . " AND `" . TblModCatalogPropFiles . "`.`show`='1'";
        $q = $q . " ORDER BY `" . TblModCatalogPropFiles . "`.`move`";
        //$q="SELECT * FROM `".TblModCatalogPropFiles."` WHERE `id_prop`='".$id_prop."'";
        //if($front_back=='front') $q = $q." AND `show`='1'";
        //$q = $q." ORDER BY `move`";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res OR !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        $mas = NULL;
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc();
            $mas[$i] = $row;
        }
        //echo '<br> $mas='.$mas['0']['path'];
        return $mas;
    }

// end of function GetFiles()
    // ================================================================================================
    // Function : GetCountResponsesByIdProp
    // Date : 08.08.2007
    // Parms :  $id_prop - id of the current positin in catalogue
    // Returns : $res / Void
    // Description : return array with responses of current product
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetResponsesByIdProp($id_prop, $front_back = 'front') {
        $q = "SELECT * FROM `" . TblModCatalogResponse . "` WHERE `id_prop`='" . $id_prop . "'";
        if ($front_back == 'front')
            $q = $q . " AND `status`='3'";
        $q = $q . " ORDER BY `move`";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
        if (!$res OR !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        $mas = NULL;
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc();
            $mas[$i] = $row;
        }
        //echo '<br> $mas='.$mas['0']['path'];
        return $mas;
    }

// end of function GetCountResponsesByIdProp()
    // ================================================================================================
    // Function : GetCountResponsesByIdProp
    // Version : 1.0.0
    // Date : 08.08.2007
    // Parms :  $id_prop - id of the current positin in catalogue
    // Returns : $res / Void
    // Description : return count of responses of current product
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetCountResponsesByIdProp($id_prop, $front_back = 'front') {
        $q = "SELECT COUNT(`id`) FROM `" . TblModCatalogResponse . "` WHERE `id_prop`='" . $id_prop . "'";
        if ($front_back == 'front')
            $q = $q . " AND `status`='3'";
        $q = $q . " ORDER BY `move`";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
        if (!$res OR !$this->db->result)
            return false;
        //$rows = $tmp_db->db_GetNumRows();
        $row = $this->db->db_FetchAssoc();
        return $row['COUNT(`id`)'];
    }

// end of function GetCountResponsesByIdProp()
    // ================================================================================================
    // Function : GetAverageRatingByIdProp
    // Date : 08.08.2007
    // Parms :  $id_prop - id of the current positin in catalogue
    //          $front_back - ('front' od 'back')
    // Returns : $res / Void
    // Description : return round rating of current product
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetAverageRatingByIdProp( $id_prop ) {
        $q = "SELECT `rating` FROM `" . TblSysModComments . "` WHERE `id_item`='" . $id_prop . "' AND `status`='1'";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
        if (!$res OR !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        if ($rows == 0)
            return false;
        $rating = NULL;
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc();
            $rating = $rating + $row['rating'];
        }
        $rating = round($rating / $rows);
        return $rating;
    }

// end of function GetAverageRatingByIdProp()
    // ================================================================================================
    // Function : GetVotesByIdProp()
    // Date : 03.03.2008
    // Parms : $id  -id of the position
    // Description : get count of voutes for position
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetVotesByIdProp($id = 0) {
        $q = "SELECT COUNT(`id`) FROM `" . TblModCatalogResponse . "` WHERE `id_prop`='" . $id . "'";
        $res = $this->db->db_Query($q);
        //echo '<br>'.$q.'<br> res='.$res.'<br> $tmp_db->result='.$tmp_db->result;
        if (!$res or !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows($res);
        //echo '<br>$rows='.$rows;
        $row = $this->db->db_FetchAssoc($res);
        $cnt = $row['COUNT(`id`)'];
        //echo '<br>$cnt='.$cnt;
        return $cnt;
    }

//--- end of GetVotesByIdProp()
    // ================================================================================================
    // Function : GetRatingByIdProp()
    // Date : 03.03.2008
    // Parms : $id - id of the position
    // Returns : true,false / Void
    // Description : get balls for position
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetRatingByIdProp($id = 0) {
        $q = "SELECT SUM(`rating`) FROM `" . TblModCatalogResponse . "` WHERE `id_prop`='" . $id . "'";
        $res = $this->db->db_Query($q);
        //echo '<br>'.$q.'<br> res='.$res.'<br> $tmp_db->result='.$tmp_db->result;
        if (!$res or !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows($res);
        //echo '<br>$rows='.$rows;
        $row = $this->db->db_FetchAssoc($res);
        $cnt = $row['SUM(`rating`)'];
        //echo '<br>$cnt='.$cnt;
        return $cnt;
    }

//--- end of GetRatingByIdProp()
    // ================================================================================================
    // Function : GetListPositionsByBalls()
    // Date : 08.04.2008
    // Parms : $level, $limit
    // Returns :      true/false
    // Description :  get list of positions sort by balls
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function GetListPositionsSortByBalls($level = 0, $limit = 'limit') {
        $q = "SELECT SUM(`" . TblModCatalogResponse . "`.rating) as balls, `" . TblModCatalogProp . "`.* FROM `" . TblModCatalogResponse . "`, `" . TblModCatalogProp . "`
              WHERE 1";
        if ($level > 0)
            $q = $q . " AND `" . TblModCatalogProp . "`.id_cat='" . $level . "'";
        $q = $q . " AND `" . TblModCatalogProp . "`.visible='2'";
        $q = $q . " AND `" . TblModCatalogResponse . "`.id_prop=`" . TblModCatalogProp . "`.id";
        $q = $q . " GROUP BY `" . TblModCatalogProp . "`.id ORDER BY balls desc ";
        if ($limit == 'limit')
            $q = $q . " LIMIT " . $this->start . ", " . ($this->start + $this->display);
        $res = $this->db->db_Query($q);
        //echo '<br>'.$q.'<br> res='.$res.'<br> $this->db->result='.$this->db->result;
        if (!$res or !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows($res);
        return $rows;
    }

//end of function GetListPositionsByBalls();
//------------------------------------------------------------------------------------------------------------
//---------------------------- FUNCTION FOR SETTINGS OF CATALOG START --------------------------------------
//------------------------------------------------------------------------------------------------------------
    // ================================================================================================
    // Function : GetSettings()
    // Date : 15.09.2009
    // Parms :  $backend = 0: using on frontend,  $backend 1: using on $backend;
    // Returns : true,false / Void
    // Description : return all settings of Catalog
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GetSettings($backend = 0) {
        $q = "select * from `" . TblModCatalogSet . "` where 1";
        $res = $this->db->db_Query($q);
        if (!$this->db->result)
            return false;
        //echo '$q = '.$q;
        $row = $this->db->db_FetchAssoc();
        if ($backend == 0) {
            //echo '<br>$row[title]'.$row['title'] =  $this->Spr->GetNameByCod( TblModCatalogSetSprTitle, 1, $this->lang_id, 1 );
            //echo '<br>$row[description]'.$row['description'] = $this->Spr->GetNameByCod( TblModCatalogSetSprDescription, 1, $this->lang_id, 1 );
            //echo '<br>$row[keywords]'.$row['keywords'] = $this->Spr->GetNameByCod( TblModCatalogSetSprKeywords, 1, $this->lang_id, 1 );
        }
        return $row;
    }

// end of function GetSettings()



//------------------------------------------------------------------------------------------------------------
//---------------------------- FUNCTION FOR SETTINGS OF CATALOG  END ---------------------------------------
//------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------
//---------------------------- FUNCTION FOR TRANSLIT START ---------------------------------------------------
//------------------------------------------------------------------------------------------------------------
    // ================================================================================================
    // Function : GetIdCategByTranslit()
    // Date : 17.05.2007
    // Parms : $str - translit name of category
    //         $id_cat_parent - if of the parent category
    // Returns : true,false / Void
    // Description : return id of the category
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetIdCategByTranslit($str = NULL, $id_cat_parent = 0, $lang_id = NULL) {
        $str = strtolower($str);
        $q = "SELECT * FROM `" . TblModCatalogTranslit . "` WHERE 1 AND BINARY LOWER(`translit`) = BINARY '" . $str . "' AND `id_cat_parent`='" . $id_cat_parent . "' AND `id_prop` IS NULL ";
        if (!empty($lang_id))
            $q = $q . " AND `lang_id`='" . $lang_id . "'";
        $q .= " ORDER BY `id` DESC";
        $res = $this->db->db_Query($q);
        //echo '<br>$q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;
        if (!$res OR !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        if ($rows == 0) {
            $q = "SELECT * FROM `" . TblModCatalogTranslit . "` WHERE 1 AND BINARY LOWER(`translit`) = BINARY '" . $str . "' AND `id_cat_parent`='" . $id_cat_parent . "' AND `id_prop` IS NULL ";
            $q .= " ORDER BY `id` DESC";
            $res = $this->db->db_Query($q);
            //echo '<br>$q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;
            if (!$res OR !$this->db->result)
                return false;
        }
        $row = $this->db->db_FetchAssoc();
        $val = $row['id_cat'];
        //var_dump($row);
        //если язык для транслита еще не установлен или уже переустанолвено значение
        //в отличный от текущего языка, то устанавливаем новое значение.
        if($this->translit_lang_id==$this->lang_id || empty($this->translit_lang_id)){
            $this->translit_lang_id = $row['lang_id'];
        }
        return $val;
    }

// end of function GetIdCategByTranslit()
    // ================================================================================================
    // Function : GetIdPropByTranslit()
    // Date : 17.05.2007
    // Parms : $str - translit name of position in catalog
    //         $id_cat - id of the category
    // Returns : true,false / Void
    // Description : return id of the positioon
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetIdPropByTranslit($str = NULL, $id_cat, $lang_id = NULL) {
        $str = strtolower($str);
        $q = "SELECT * FROM `" . TblModCatalogTranslit . "` WHERE 1 AND BINARY LOWER(`translit`) = BINARY '" . $str . "' AND `id_cat`='" . $id_cat . "' AND `id_prop` IS NOT NULL";
        if (!empty($lang_id))
            $q = $q . " AND `lang_id`='" . $lang_id . "'";
        $q .= " ORDER BY `id` DESC";
        $res = $this->db->db_Query($q);
        //echo '<br>$q='.$q.' $res='.$res.' $db->result='.$db->result;
        if (!$res OR !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        if ($rows > 0) {
            $row = $this->db->db_FetchAssoc();
            $this->translit_prop_lang_id = $row['lang_id'];
            return $row['id_prop'];
        }
        $q = "SELECT * FROM `" . TblModCatalogTranslit . "` WHERE 1 AND BINARY LOWER(`translit`) = BINARY '" . $str . "' AND `id_cat`='" . $id_cat . "' AND `id_prop` IS NOT NULL";
        $q .= " ORDER BY `id` DESC";
        $res = $this->db->db_Query($q);
        //echo '<br>$q='.$q.' $res='.$res.' $db->result='.$db->result;
        if (!$res OR !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        if ($rows > 0) {
            $row = $this->db->db_FetchAssoc();
            $this->translit_prop_lang_id = $row['lang_id'];
            return $row['id_prop'];
        }

        //check in multi categories
        if ($rows == 0 AND isset($this->settings['multi_categs']) AND $this->settings['multi_categs'] == 1) {
            $q = "SELECT * FROM `" . TblModCatalogTranslit . "` WHERE 1 AND BINARY LOWER(`translit`) = BINARY '" . $str . "' AND `id_prop` IS NOT NULL";
            $q .= " ORDER BY `id` DESC";
            $res = $this->db->db_Query($q);
            //echo '<br>$q='.$q.' $res='.$res.' $db->result='.$db->result;
            if (!$res OR !$this->db->result)
                return false;
            $rows = $this->db->db_GetNumRows();
            $db2 = new DB();
            for ($i = 0; $i < $rows; $i++) {
                $row = $this->db->db_FetchAssoc();
                $q = "SELECT * FROM `" . TblModCatalogPropMultiCategs . "` WHERE `id_prop`='" . $row['id_prop'] . "' AND `id_cat`='" . $id_cat . "'";
                $q .= " ORDER BY `id` DESC";
                $res = $db2->db_Query($q);
                //echo '<br>$q='.$q.' $res='.$res.' $db2->result='.$db2->result;
                if (!$res OR !$db2->result)
                    return false;
                $rows2 = $db2->db_GetNumRows();
                if ($rows2 > 0) {
                    $row2 = $db2->db_FetchAssoc();
                    $this->translit_prop_lang_id = $row['lang_id'];
                    return $row2['id_prop'];
                }
            }
        }
        return false;
    }

// end of function GetIdPropByTranslit()
    // ================================================================================================
    // Function : IsExistTranslit()
    // Date : 18.05.2007
    // Parms :  $str        - string for checking
    //          $id_cat     - id of the category
    //          $id_cat_parent - id of the parent category
    //          $id_prop    - id of the current position
    // Returns : true,false / Void
    // Description :  check the name for exist in translit
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function IsExistTranslit($str = NULL, $id_cat = NULL, $id_cat_parent = NULL, $id_prop = NULL, $lang_id = NULL) {
        $q = "SELECT * FROM `" . TblModCatalogTranslit . "` WHERE BINARY `translit` = BINARY '" . $str . "'";
        if ($id_cat !== NULL)
            $q = $q . " AND `id_cat`='" . $id_cat . "'";
        //-------- если проверяется конкретная позиция,а не категория -------
        //if( $id_cat!=NULL AND !empty($id_prop) ) $q = $q." AND `id_cat`='".$id_cat."'";
        //-------------------------------------------------------------------
        if ($id_cat_parent !== NULL)
            $q = $q . " AND `id_cat_parent`='" . $id_cat_parent . "'";
        if ($id_prop == NULL)
            $q = $q . " AND `id_prop` IS NULL";
        else
            $q = $q . " AND `id_prop` IS NOT NULL";
        if (!empty($lang_id))
            $q = $q . " AND `lang_id`='" . $lang_id . "'";
        $res = $this->db->db_Query($q);
        //echo '<br>$q='.$q.' $res='.$res.' $db->result='.$db->result;
        if (!$res OR !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        //echo '<br>$rows='.$rows;
        $return = NULL;
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc();
            //echo '<br>$id_prop='.$id_prop.' $row[id_prop]='.$row['id_prop'].' $row[translit]='.$row['translit'];
            // проверка категории, если найденный транслит не пренадлежит данной категории $id_cat, то значит уже есть такой транслит
            // у другой категории $row['id_cat'] этого же уровня, поэтому возвращаем его.
            if ($id_cat != $row['id_cat']) {
                $return = $row['translit'];
                return $return;
            }

            // проверка конкретной позиции, если найденный транслит не пренадлежит данной позиции $id_prop, то значит уже есть такой транслит
            // у другой позиции $row['id_prop'], поэтому возвращаем его.
            if ($id_prop != $row['id_prop']) {
                $return = $row['translit'];
                return $return;
            }
        }// end for
        //echo '<br>$return='.$return;
        return $return;
    }

// end of function IsExistTranslit()
    // ================================================================================================
    // Function : GetTranslitById()
    // Date : 18.05.2007
    // Parms :  $id_cat     - id of the category
    //          $id_prop    - id of the current position
    // Returns : true,false / Void
    // Description :  return translit for category or current position
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function GetTranslitById($id_cat = NULL, $id_prop = NULL, $lang_id = NULL) {
        $q = "SELECT * FROM `" . TblModCatalogTranslit . "` WHERE `id_cat`='" . $id_cat . "'";
        if ($id_prop == NULL)
            $q = $q . " AND `id_prop` IS NULL";
        else
            $q = $q . " AND `id_prop`='" . $id_prop . "'";
        if (!empty($lang_id))
            $q = $q . " AND `lang_id`='" . $lang_id . "'";
        $q .= " ORDER BY `id` DESC";
        $res = $this->db->db_Query($q);
        //echo '<br>$q='.$q.' $res='.$res.' $db->result='.$db->result;
        if (!$res OR !$this->db->result)
            return false;
        $row = $this->db->db_FetchAssoc();
        //echo '<br>$row[translit]='.$row['translit'];
        //if no exist translit on current language then search for translit with no language
        if (empty($row['translit'])) {
            $q = "SELECT * FROM `" . TblModCatalogTranslit . "` WHERE `id_cat`='" . $id_cat . "'";
            if ($id_prop == NULL)
                $q = $q . " AND `id_prop` IS NULL";
            else
                $q = $q . " AND `id_prop`='" . $id_prop . "'";
            $q = $q . " AND `lang_id`='0'";
            $q .= " ORDER BY `id` DESC";
            $res = $this->db->db_Query($q);
            //echo '<br>$q='.$q.' $res='.$res.' $db->result='.$db->result;
            if (!$res OR !$this->db->result)
                return false;
            $row = $this->db->db_FetchAssoc();
        }
        //echo '<br>$row[translit]='.$row['translit'];
        return $row['translit'];
    }

// end of function GetTranslitById()
    // ================================================================================================
    // Function : SaveTranslit()
    // Date : 18.05.2007
    // Parms : $id_cat - id of the category
    //         $id_cat_parent - id of the parent category
    //         $name_ind - individual name of the category
    //         $name -  name of the category
    //         $old_id_cat - old value of level field
    //         $translit_old - old values of translit field
    // Returns : true,false / Void
    // Description :  save translit name of category
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function SaveTranslit($id_cat, $id_cat_parent, $name_ind = NULL, $name = NULL, $old_id_cat = NULL, $translit_old = NULL) {
        //echo '<br>$id_cat='.$id_cat.' $id_cat_parent='.$id_cat_parent.' $old_id_cat='.$old_id_cat.' $name_ind='.$name_ind.' $name='.$name;

        $db = DBs::getInstance();
        $Crypt = check_init('Crypt', 'Crypt');
        $lang = check_init('SysLang', 'SysLang');
        $default_lang = $lang->GetDefFrontLangID();

        //echo '<br>$name_ind='.print_r($name_ind);
        //echo '<br>$name='.print_r($name);

        $ln_arr = $lang->LangArray(_LANG_ID);
        while ($el = each($ln_arr)) {
            $lang_id = $el['key'];
            $translit = NULL;
            if (!isset($translit_old[$lang_id]) AND !isset($name_ind[$lang_id]) AND !isset($name[$lang_id]))
                continue;
            $translit_old[$lang_id] = $this->Form->GetRequestTxtData($translit_old[$lang_id], 1);
            $name_ind[$lang_id] = $this->Form->GetRequestTxtData($name_ind[$lang_id], 1);
            $name[$lang_id] = $this->Form->GetRequestTxtData($name[$lang_id], 1);
            //echo '<br/>$translit_old[$lang_id]='.$translit_old[$lang_id].' $name_ind[$lang_id]='.$name_ind[$lang_id];
            //if exist old translit $translit_old[$lang_id] and it = current translit $name_ind[$lang_id] then no needs to save translit.
            //Old translit must not to change automaticaly, only manualy!
            if ((!empty($translit_old[$lang_id]) AND $translit_old[$lang_id] == $name_ind[$lang_id]))
                continue;

            //generate translit only for new position of catalog
            if (empty($translit_old[$lang_id])){
                if (isset($name_ind[$lang_id]) AND !empty($name_ind[$lang_id])) {
                    $translit = $Crypt->GetTranslitStr(stripslashes($name_ind[$lang_id]));
                    $res = $this->IsExistTranslit($translit, NULL, $id_cat_parent, NULL, $lang_id);
                    if (!empty($res))
                        $translit = $translit . '-' . $id_cat;
                }
                else {
                    $translit = $Crypt->GetTranslitStr(stripslashes($name[$lang_id]));
                    $res = $this->IsExistTranslit($translit, NULL, $id_cat_parent, NULL, $lang_id);
                    if (!empty($res))
                        $translit = $translit . '-' . $id_cat;
                }
            }
            else {
                $translit = stripslashes($name_ind[$lang_id]);
            }
            //Если первые 4 симвлоа название категории = строке "page",то транслит
            //модифицируем, чт обы название категории не совпадало с постраничностью
            //для категории.
            if(substr($translit,0,4)=='page'){
                $translit = $id_cat.'-'.$translit;
            }

            //if move category $id_cat from category $old_id_cat to another category $id_cat_parent then update translit for this category $id_cat
            if ($id_cat_parent != $old_id_cat) {
                $q = "SELECT * FROM `" . TblModCatalogTranslit . "` WHERE `id_cat`='" . $id_cat . "' AND `id_cat_parent`='" . $old_id_cat . "' AND `id_prop` IS NULL AND `lang_id`='" . $lang_id . "'";
            } else {
                $q = "SELECT * FROM `" . TblModCatalogTranslit . "` WHERE `id_cat`='" . $id_cat . "' AND `id_cat_parent`='" . $id_cat_parent . "' AND `id_prop` IS NULL AND `lang_id`='" . $lang_id . "'";
            }
            $res = $db->db_Query($q);
            //echo '<br>$q='.$q.' $res='.$res.' $db->result='.$db->result;
            if (!$res OR !$db->result)
                return false;
            $rows = $db->db_GetNumRows();
            if ($rows > 0) {
                $row = $db->db_FetchAssoc();
                $q = "UPDATE `" . TblModCatalogTranslit . "` SET
                      `translit`='" . $translit . "',
                      `id_cat_parent`='" . $id_cat_parent . "'
                      WHERE `id`='" . $row['id'] . "'
                     ";
                $res = $db->db_Query($q);
                //echo '<br>$q='.$q.' $res='.$res.' $db->result='.$db->result;
                if (!$res OR !$db->result)
                    return false;
            }
            else {
                //check if exist translit with no language
                $q = "SELECT * FROM `" . TblModCatalogTranslit . "` WHERE `id_cat`='" . $id_cat . "' AND `id_cat_parent`='" . $id_cat_parent . "' AND `id_prop` IS NULL AND `lang_id`='0'";
                $res = $db->db_Query($q);
                //echo '<br>$q='.$q.' $res='.$res.' $db->result='.$db->result;
                if (!$res OR !$db->result)
                    return false;
                $rows = $db->db_GetNumRows();
                if ($rows > 0) {
                    $row = $db->db_FetchAssoc();
                    $q = "DELETE FROM  `" . TblModCatalogTranslit . "` WHERE `id`='" . $row['id'] . "'";
                    $res = $db->db_Query($q);
                    //echo '<br>$q='.$q.' $res='.$res.' $db->result='.$db->result;
                    if (!$res OR !$db->result)
                        return false;
                }
                $q = "INSERT INTO `" . TblModCatalogTranslit . "` SET
                      `translit`='" . $translit . "',
                      `id_cat`='" . $id_cat . "',
                      `id_cat_parent`='" . $id_cat_parent . "',
                      `id_prop`=NULL,
                      `lang_id`='" . $lang_id . "'
                     ";
                $res = $db->db_Query($q);
                //echo '<br>$q='.$q.' $res='.$res.' $db->result='.$db->result;
                if (!$res OR !$db->result)
                    return false;
            }
        }//end while
        return true;
    }

// end of function SaveTranslit()
    // ================================================================================================
    // Function : SaveTranslitProp()
    // Date : 18.05.2007
    // Parms :  $id_cat - if of the category
    //          $id - id of the current position
    //          $name_ind - translit name of the position
    //          $name -  name of the current position
    //          $translit_old - old values of translit field
    // Returns : true,false / Void
    // Description :  save translit name of current position
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function SaveTranslitProp($id_cat, $id_cat_parent, $id, $name_ind = NULL, $name = NULL, $translit_old = NULL) {
        $db = DBs::getInstance();
        $Crypt = check_init('Crypt', 'Crypt');
        $lang = check_init('SysLang', 'SysLang');
        $default_lang = $lang->GetDefFrontLangID();


        $ln_arr = $lang->LangArray(_LANG_ID);
        while ($el = each($ln_arr)) {
            $lang_id = $el['key'];
            if (!isset($name_ind[$lang_id]))
                continue;
            $translit = NULL;
            $translit_old[$lang_id] = $this->Form->GetRequestTxtData($translit_old[$lang_id], 1);
            $name_ind[$lang_id] = $this->Form->GetRequestTxtData($name_ind[$lang_id], 1);
            $name[$lang_id] = $this->Form->GetRequestTxtData($name[$lang_id], 1);
            //echo '<br/>$translit_old[$lang_id]='.$translit_old[$lang_id].' $name_ind[$lang_id]='.$name_ind[$lang_id];
            //echo '<br />$translit_old[$lang_id]='.$translit_old[$lang_id];
            //echo '<br />$name_ind[$lang_id]='.$name_ind[$lang_id].' $name[$lang_id]='.$name[$lang_id];
            //if exist old translit $translit_old[$lang_id] and it = current translit $name_ind[$lang_id] and not make copy of position, then no needs to save translit.
            //Old translit must not to change automaticaly, only manualy!
            if ((!empty($translit_old[$lang_id]) AND $translit_old[$lang_id] == $name_ind[$lang_id] AND empty($this->id_prop_copy)))
                continue;

            //generate translit only for new position of catalog or for copy of position
            if (empty($translit_old[$lang_id]) OR !empty($this->id_prop_copy)) {
                //First check translit field and make transliteration of it
                if (isset($name_ind[$lang_id]) AND !empty($name_ind[$lang_id])) {
                    $translit = $Crypt->GetTranslitStr(stripslashes($name_ind[$lang_id]));
                    if(CATALOG_TRASLIT) $res = $this->IsExistTranslit($translit, NULL, NULL, $id, $lang_id);
                    else $res = $this->IsExistTranslit($translit, $id_cat, $id_cat_parent, $id, $lang_id);
                    if (!empty($res))
                        $translit = $translit . '-' . $id;
                }
                //else check other field for generate translit and make transliteration of it
                elseif (isset($name[$lang_id]) AND !empty($name[$lang_id])) {
                    $translit = $Crypt->GetTranslitStr(stripslashes($name[$lang_id]));
                    if(CATALOG_TRASLIT) $res = $this->IsExistTranslit($translit, NULL, NULL, $id, $lang_id);
                    else $res = $this->IsExistTranslit($translit, $id_cat, $id_cat_parent, $id, $lang_id);
                    if (!empty($res))
                        $translit = $translit . '-' . $id;
                }
            }
            else {
                $translit = stripslashes($name_ind[$lang_id]);
            }

            if(is_numeric($translit)) $translit = 'item-'.$translit;
            //echo '<br />$translit000='.$translit;

            $q = "SELECT `id` FROM `" . TblModCatalogTranslit . "` WHERE `id_prop`='" . $id . "' AND `lang_id`='" . $lang_id . "'";
            $res = $db->db_Query($q);
            //echo '<br>$q='.$q.' $res='.$res.' $db->result='.$db->result;
            if (!$res OR !$db->result)
                return false;
            $rows = $db->db_GetNumRows();
            //--- Update translit
            if ($rows > 0) {
                if ($translit_old[$lang_id] == $translit)
                    continue;
                $row = $db->db_FetchAssoc();
                $q = "UPDATE `" . TblModCatalogTranslit . "` SET
                     `translit`='" . $translit . "',
                     `id_cat`='" . $id_cat . "',
                     `id_cat_parent`='" . $id_cat_parent . "'
                     WHERE `id`='" . $row['id'] . "'";
                $res = $db->db_Query($q);
                //echo '<br>$q='.$q.' $res='.$res.' $db->result='.$db->result;
                if (!$res OR !$db->result)
                    return false;
            }
            //--- Insert translit
            else {
                //check if exist translit with no language
                $q = "SELECT * FROM `" . TblModCatalogTranslit . "` WHERE `id_prop`='" . $id . "' AND `lang_id`='0'";
                $res = $db->db_Query($q);
                //echo '<br>$q='.$q.' $res='.$res.' $db->result='.$db->result;
                if (!$res OR !$db->result)
                    return false;
                $rows = $db->db_GetNumRows();
                if ($rows > 0) {
                    $row = $db->db_FetchAssoc();
                    $q = "DELETE FROM  `" . TblModCatalogTranslit . "` WHERE `id`='" . $row['id'] . "'";
                    $res = $db->db_Query($q);
                    //echo '<br>$q='.$q.' $res='.$res.' $db->result='.$db->result;
                    if (!$res OR !$db->result)
                        return false;
                }
                $q = "INSERT INTO `" . TblModCatalogTranslit . "` SET
                      `translit`='" . $translit . "',
                      `id_cat`='" . $id_cat . "',
                      `id_cat_parent`='" . $id_cat_parent . "',
                      `id_prop`='" . $id . "',
                      `lang_id`='" . $lang_id . "'
                     ";
                $res = $db->db_Query($q);
                //echo '<br>$q='.$q.' $res='.$res.' $db->result='.$db->result;
                if (!$res OR !$db->result)
                    return false;
            }
        }//end while
        return true;
    }

// end of function SaveTranslitProp()
    // ================================================================================================
    // Function : DelTranslit()
    // Date : 19.05.2007
    // Parms :  $id_cat     - id of the category
    //          $id_prop    - id of the current position
    // Returns : true,false / Void
    // Description :  delete translit from table
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function DelTranslit($id_cat = NULL, $id_prop = NULL) {
        if (empty($id_cat) AND empty($id_prop))
            return false;
        $q = "DELETE FROM `" . TblModCatalogTranslit . "` WHERE 1";
        if (!empty($id_cat))
            $q .= " AND `id_cat`='" . $id_cat . "'";
        if ($id_prop == NULL)
            $q .= " AND `id_prop` IS NULL";
        else
            $q .= " AND `id_prop`='" . $id_prop . "'";
        $res = $this->db->db_Query($q);
        //echo '<br>$q='.$q.' $res='.$res.' $db->result='.$db->result;
        if (!$res OR !$this->db->result)
            return false;
        return true;
    }

// end of function DelTranslit()
//------------------------------------------------------------------------------------------------------------
//---------------------------- FUNCTION FOR TRANSLIT END -----------------------------------------------------
//------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------
//---------------------------- FUNCTION FOR CATALOG STATISTIC START ------------------------------------------
//------------------------------------------------------------------------------------------------------------
    // ================================================================================================
    // Function : SetStat()
    // Date : 06.08.2007
    // Returns : true,false / Void
    // Description : Set Statistic Property's
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function SetStat() {
        $Stat = new Stat();
        $logon = new UserAuthorize();

        $this->id_stat = '';

        if (isset($_SERVER["REQUEST_TIME"])) {
            list($usec, $sec) = explode(" ", microtime());
            $microtime = ((float) $usec + (float) $sec);
            $this->time_gen = $microtime - $_SERVER["REQUEST_TIME"];
        }
        else
            $this->time_gen = '';
        //echo '<br>$_SERVER["REQUEST_TIME"]='.$_SERVER["REQUEST_TIME"].' time()='.time().' microtime='.$microtime.' $this->time_gen='.$this->time_gen;


        if (isset($_SERVER["REQUEST_URI"]))
            $this->page = $_SERVER["REQUEST_URI"];
        else
            $this->page = getenv("SCRIPT_NAME");
        //$_SERVER['HTTP_REFERER'] = "http://www.meta.ua/search.asp?q=%EA%EE%EC%EF%FC%FE%F2%E5%F0%ED%FB%E9+%F1%F2%EE%EB&m=";
        //ECHO '<BR>$_SERVER[HTTP_REFERER]:'.$_SERVER['HTTP_REFERER'];
        if (isset($_SERVER['HTTP_REFERER'])) {
            if (!strstr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']))
                $this->refer = $_SERVER['HTTP_REFERER'];
        }
        else
            $this->refer = '';

        $this->dt = date("Y-m-d");
        $this->tm = date("H:i:s");
        $this->ip = sprintf("%u", ip2long($_SERVER['REMOTE_ADDR'])); //3562199730;

        if (isset($_SERVER['REMOTE_HOST']))
            $this->host = $_SERVER['REMOTE_HOST'];
        else
            $this->host = '';

        $this->proxy = '';

        if (isset($_SERVER['HTTP_USER_AGENT']))
            $this->agent = $Stat->Get_Agent_Id($_SERVER['HTTP_USER_AGENT']);
        else
            $this->agent = '';

        $this->screen_res = '';

        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
            $this->lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        else
            $this->lang = '';

        $this->country = $Stat->GetCountryByIP($this->ip);
        $this->cnt = NULL;
        $this->id_user = $logon->user_id;

        /* Check this data in database */
        if ($this->CheckStat()) {
            /* Update statistic */
            $this->UpdateStat();
        } else {
            /* Save statistic */
            $this->SaveStat();
        }
    }

//--- end of SetStat()
    // ================================================================================================
    // Function : CheckStat()
    // Date : 06.08.2007
    // Returns : true,false / Void
    // Description : Check Statistic data in database
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function CheckStat() {
        $q = "SELECT * FROM `" . TblModCatalogStatLog . "`
         WHERE `dt`='" . $this->dt . "'
           AND `id_cat`='" . $this->id_cat . "'
           AND `id_prop`='" . $this->id . "'
           AND `id_img`='" . $this->id_img . "'
           AND `id_file`='" . $this->id_file . "'
           AND `id_manufac`='" . $this->id_manufac . "'
           AND `id_group`='" . $this->id_group . "'
           AND `ip`='" . $this->ip . "'
           AND `page`='" . $this->page . "'
           AND `refer`='" . $this->refer . "'
           AND `id_user`='" . $this->id_user . "'
         ORDER BY `dt`";
        $res = $this->db->db_Query($q);
        $rows = $this->db->db_GetNumRows($res);
        //echo '<br>'.$q.'<br> res='.$res.'<br> $this->db->result='.$this->db->result.' $rows='.$rows;
        if ($rows > 0) {
            $row = $this->db->db_FetchAssoc($res);
            $this->cnt = $row['cnt'];
            $this->id_stat = $row['id'];
            return true;
        }
        else
            return false;
    }

//--- end of CheckStat()
    // ================================================================================================
    // Function : UpdateStat()
    // Date : 06.08.2007
    // Returns : true,false / Void
    // Description : Update Statistic record in catalog
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function UpdateStat() {
        $q = "UPDATE `" . TblModCatalogStatLog . "` SET
    `time_gen`='" . $this->time_gen . "', `cnt`='" . ( $this->cnt + 1 ) . "', `tm`='" . $this->tm . "'";
        $q = $q . " WHERE `id`='" . $this->id_stat . "'";
        $res = $this->db->db_Query($q);
        //echo '<br>'.$q.'<br> res='.$res.'<br> $this->db->result='.$this->db->result;
        if ($res)
            return true;
        else
            return false;
    }

//--- end of UpdateStat()
    // ================================================================================================
    // Function : SaveStat()
    // Date : 06.08.2007
    // Returns : true,false / Void
    // Description : Save Statistic record in catalog
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function SaveStat() {
        $this->cnt = 1;
        $q = "INSERT INTO " . TblModCatalogStatLog . " VALUES(
    NULL,
    '" . $this->id_cat . "',
    '" . $this->id . "',
    '" . $this->id_img . "',
    '" . $this->id_file . "',
    '" . $this->id_manufac . "',
    '" . $this->id_group . "',
    '" . $this->time_gen . "',
    '" . $this->page . "',
    '" . $this->refer . "',
    '" . $this->dt . "',
    '" . $this->tm . "',
    '" . $this->ip . "',
    '" . $this->host . "',
    '" . $this->proxy . "',
    '" . $this->agent . "',
    '" . $this->screen_res . "',
    '" . $this->lang . "',
    '" . $this->country . "',
    '" . $this->cnt . "',
    '" . $this->id_user . "'";
        $q = $q . ")";

        $res = $this->db->db_Query($q);
        //echo '<br>'.$q.'<br> res='.$res.'<br> $this->db->result='.$this->db->result;
        //echo phpinfo();
        if ($res)
            return true;
        else
            return false;
    }

//--- end of SaveStat()
    // ================================================================================================
    // Function : GetCntViews()
    // Date : 14.02.2008
    // Parms :    $id_cat - id category
    //            $id_prop - id of the position
    //            $id_img - id of the image
    //            $id_file - id of the file
    //            $id_manufac - id of the manufacturer
    //            $id_group - id of the group
    //            $id_user - id of the user
    //            $dt_from - date from
    //            $dt_to - date to
    // Returns : true,false / Void
    // Description : get count of viewings
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetCntViews($id_cat = 0, $id_prop = 0, $id_img = 0, $id_file = 0, $id_manufac = 0, $id_group = 0, $id_user = 0, $dt_from = NULL, $dt_to = NULL) {
        $q = "SELECT SUM(`cnt`) FROM `" . TblModCatalogStatLog . "`
         WHERE 1";
        $q = $q . " AND `id_cat`='" . $id_cat . "'";
        $q = $q . " AND `id_prop`='" . $id_prop . "'";
        $q = $q . " AND `id_img`='" . $id_img . "'";
        $q = $q . " AND `id_file`='" . $id_file . "'";
        $q = $q . " AND `id_manufac`='" . $id_manufac . "'";
        $q = $q . " AND `id_group`='" . $id_group . "'";
        if (!empty($id_user))
            $q = $q . " AND `id_user`='" . $id_user . "'";
        if (!empty($dt_from))
            $q = $q . " AND `dt`>='" . $dt_from . "'";
        if (!empty($dt_to))
            $q = $q . " AND `dt`<='" . $dt_to . "'";

        $res = $this->db->db_Query($q);
        //echo '<br>'.$q.'<br> res='.$res.'<br> $tmp_db->result='.$tmp_db->result;
        if (!$res or !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows($res);
        //echo '<br>$rows='.$rows;
        $row = $this->db->db_FetchAssoc($res);
        $cnt = $row['SUM(`cnt`)'];
        //echo '<br>$cnt='.$cnt;
        return $cnt;
    }

//--- end of GetCntViews()
    // ================================================================================================
    // Function : GetCntViewsImg()
    // Date : 14.02.2008
    // Parms :
    //            $id_img - id of the image
    //            $id_user - id of the user
    //            $dt_from - date from
    //            $dt_to - date to
    // Returns : true,false / Void
    // Description : get count of viewings image
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetCntViewsImg($id_img = 0, $id_user = 0, $dt_from = NULL, $dt_to = NULL) {
        if ($id_user == NULL)
            $id_user = 0;
        $q = "SELECT SUM(`cnt`) FROM `" . TblModCatalogStatLog . "`
         WHERE 1";
        $q = $q . " AND `id_img`='" . $id_img . "'";
        if (!empty($id_user))
            $q = $q . " AND `id_user`='" . $id_user . "'";
        if (!empty($dt_from))
            $q = $q . " AND `dt`>='" . $dt_from . "'";
        if (!empty($dt_to))
            $q = $q . " AND `dt`<='" . $dt_to . "'";

        $res = $this->db->db_Query($q);
        //echo '<br>'.$q.'<br> res='.$res.'<br> $tmp_db->result='.$tmp_db->result;
        if (!$res or !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows($res);
        //echo '<br>$rows='.$rows;
        $row = $this->db->db_FetchAssoc($res);
        $cnt = $row['SUM(`cnt`)'];
        //echo '<br>$cnt='.$cnt;
        return $cnt;
    }

//--- end of GetCntViewsImg()
    // ================================================================================================
    // Function : GetCntViewsFile()
    // Date : 14.02.2008
    // Parms :
    //            $id_file - id of the file
    //            $id_user - id of the user
    //            $dt_from - date from
    //            $dt_to - date to
    // Returns : true,false / Void
    // Description : get count of viewings image
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetCntViewsFile($id_file = 0, $id_user = 0, $dt_from = NULL, $dt_to = NULL) {
        if ($id_user == NULL)
            $id_user = 0;
        $q = "SELECT SUM(`cnt`) FROM `" . TblModCatalogStatLog . "`
         WHERE 1";
        $q = $q . " AND `id_file`='" . $id_file . "'";
        if (!empty($id_user))
            $q = $q . " AND `id_user`='" . $id_user . "'";
        if (!empty($dt_from))
            $q = $q . " AND `dt`>='" . $dt_from . "'";
        if (!empty($dt_to))
            $q = $q . " AND `dt`<='" . $dt_to . "'";

        $res = $this->db->db_Query($q);
        //echo '<br>'.$q.'<br> res='.$res.'<br> $tmp_db->result='.$tmp_db->result;
        if (!$res or !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows($res);
        //echo '<br>$rows='.$rows;
        $row = $this->db->db_FetchAssoc($res);
        $cnt = $row['SUM(`cnt`)'];
        //echo '<br>$cnt='.$cnt;
        return $cnt;
    }

//--- end of GetCntViewsFile()
//------------------------------------------------------------------------------------------------------------
//---------------------------- FUNCTION FOR CATALOG STATISTIC END --------------------------------------------
//------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------
//---------------------------- FUNCTION FOR CATALOG TAGS START -----------------------------------------------
//------------------------------------------------------------------------------------------------------------
    // ================================================================================================
    // Function : GetTagsById()
    // Date : 14.02.2008
    // Parms :    id - id of the position
    // Returns : true,false / Void
    // Description : get tags for current position
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetTagsById($id) {
        $q = "SELECT * FROM `" . TblModCatalogTags . "` WHERE `id_prop`='" . $id_prop . "'";
        switch ($for_users) {
            case 'user':
                $q = $q . " AND `id_user` IS NOT NULL AND `id_user`!='0' ORDER BY `id_user`,`id`";
                break;
            case 'general':
                $q = $q . " AND (`id_user` IS NULL OR `id_user`='0') ORDER BY `id`";
                break;
            default:
                if (empty($for_users))
                    $q = $q . " ORDER BY `id_user`,`id`";
                else
                    $q = $q . " AND `id_user`='" . $for_users . "' ORDER BY `id_user`,`id`";
                break;
        }
        //if($for_users=='user') $q = $q." AND `id_user` IS NOT NULL AND `id_user`!='0' ORDER BY `id_user`,`id`";
        //if($for_users=='general') $q = $q." AND (`id_user` IS NULL OR `id_user`='0') ORDER BY `id`";
        //if(empty($for_users)) $q = $q." ORDER BY `id_user`,`id`";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res OR !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        //$arr = array();
        $arr = NULL;
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc();
            $arr[$i] = $row;
        }
        //echo '<br>$arr='.$arr;print_r($arr);
        //echo '<br>intval($for_users)='.intval($for_users);
        if (empty($arr) AND intval($for_users) > 0) {
            $arr = $this->GetPriceLevels($id_prop, 'general');
        }
        return $arr;
    }

//end of function GetTagsById()
//------------------------------------------------------------------------------------------------------------
//---------------------------- FUNCTION FOR CATALOG TAGS END -------------------------------------------------
//------------------------------------------------------------------------------------------------------------


    /*
      function SetDescr2(){
      $db = new DB();
      $q = "SELECT `id` FROM `".TblModCatalog."` WHERE 1";
      $res = $this->db->db_Query($q);
      echo '$res='.$res.' $this->db->result='.$this->db->result;
      if( !$res OR !$this->db->result ) return false;
      $rows = $this->db->db_GetNumRows($res);
      for($i=0; $i<$rows; $i++){
      $row = $this->db->db_FetchAssoc($res);
      $q = "INSERT INTO `".TblModCatalogSprDescr2."` SET
      `cod`='".$row['id']."',
      `lang_id`='1',
      `name`=''
      ";
      $res = $db->db_Query($q);
      echo '$res='.$res.' $db->result='.$db->result;
      if( !$res OR !$db->result ) return false;
      $q = "INSERT INTO `".TblModCatalogSprDescr2."` SET
      `cod`='".$row['id']."',
      `lang_id`='2',
      `name`=''
      ";
      $res = $db->db_Query($q);
      echo '$res='.$res.' $db->result='.$db->result;
      if( !$res OR !$db->result ) return false;
      $q = "INSERT INTO `".TblModCatalogSprDescr2."` SET
      `cod`='".$row['id']."',
      `lang_id`='3',
      `name`=''
      ";
      $res = $db->db_Query($q);
      echo '$res='.$res.' $db->result='.$db->result;
      if( !$res OR !$db->result ) return false;
      }
      return true;
      }
     */
    /*
      function SetImgTxt(){
      $db = new DB();
      $q = "SELECT `id` FROM `".TblModCatalogPropImg."` WHERE 1";
      $res = $this->db->db_Query($q);
      echo '$res='.$res.' $this->db->result='.$this->db->result;
      if( !$res OR !$this->db->result ) return false;
      $rows = $this->db->db_GetNumRows($res);
      for($i=0; $i<$rows; $i++){
      $row = $this->db->db_FetchAssoc($res);
      $q = "INSERT INTO `".TblModCatalogPropImgTxt."` SET
      `cod`='".$row['id']."',
      `lang_id`='1',
      `name`='',
      `text`=''
      ";
      $res = $db->db_Query($q);
      echo '$res='.$res.' $db->result='.$db->result;
      if( !$res OR !$db->result ) return false;
      $q = "INSERT INTO `".TblModCatalogPropImgTxt."` SET
      `cod`='".$row['id']."',
      `lang_id`='2',
      `name`='',
      `text`=''
      ";
      $res = $db->db_Query($q);
      echo '$res='.$res.' $db->result='.$db->result;
      if( !$res OR !$db->result ) return false;
      $q = "INSERT INTO `".TblModCatalogPropImgTxt."` SET
      `cod`='".$row['id']."',
      `lang_id`='3',
      `name`='',
      `text`=''
      ";
      $res = $db->db_Query($q);
      echo '$res='.$res.' $db->result='.$db->result;
      if( !$res OR !$db->result ) return false;
      }
      return true;
      }

      function SetImgMove(){
      $db = new DB();
      $q = "SELECT * FROM `".TblModCatalogPropImg."` WHERE 1 ORDER BY `id_prop` asc";
      $res = $db->db_Query($q);
      //echo '<br />$q='.$q.' <br />$res='.$res.' $db->result='.$db->result.'<br />$db->errdet='.$db->errdet;
      if( !$res OR !$db->result ) return false;
      $rows = $db->db_GetNumRows($res);
      //echo '<br />$rows='.$rows;
      for($i=0; $i<$rows; $i++){
      $row = $db->db_FetchAssoc($res);
      $arr_img[$row['id_prop']][]=$row;
      }
      $keys = array_keys($arr_img);
      $n = count($keys);
      for($i=0; $i<$n; $i++){
      $n2 = count($arr_img[$keys[$i]]);
      for($j=0;$j<$n2;$j++){
      $q = "UPDATE `".TblModCatalogPropImg."` SET
      `move`='".($j+1)."'
      WHERE `id`='".$arr_img[$keys[$i]][$j]['id']."'
      ";
      $res = $db->db_Query($q);
      //echo '<br />$q='.$q.'<br />$res='.$res.' $db->result='.$db->result;
      if( !$res OR !$db->result ) return false;
      }
      }
      return true;
      }//einf of function SetImgMove()

      function SetPropMove(){
      $db = new DB();
      $q = "SELECT * FROM `".TblModCatalogProp."` WHERE 1 ORDER BY `id_cat` asc";
      $res = $db->db_Query($q);
      //echo '<br />$q='.$q.' <br />$res='.$res.' $db->result='.$db->result.'<br />$db->errdet='.$db->errdet;
      if( !$res OR !$db->result ) return false;
      $rows = $db->db_GetNumRows($res);
      //echo '<br />$rows='.$rows;
      for($i=0; $i<$rows; $i++){
      $row = $db->db_FetchAssoc($res);
      $arr[$row['id_cat']][]=$row;
      }
      $keys = array_keys($arr);
      $n = count($keys);
      for($i=0; $i<$n; $i++){
      $n2 = count($arr[$keys[$i]]);
      for($j=0;$j<$n2;$j++){
      $q = "UPDATE `".TblModCatalogProp."` SET
      `move`='".($j+1)."'
      WHERE `id`='".$arr[$keys[$i]][$j]['id']."'
      ";
      $res = $db->db_Query($q);
      //echo '<br />$q='.$q.'<br />$res='.$res.' $db->result='.$db->result;
      if( !$res OR !$db->result ) return false;
      }
      }
      return true;
      }//einf of function SetImgMove()
     */
}

// end of class Catalog

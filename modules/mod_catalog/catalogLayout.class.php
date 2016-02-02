<?php
/**
 * CatalogLayout.class.php
 * class for display interface of Catalog module
 * @package Catalog Package of SEOCMS
 * @author Igor Trokhymchuk  <ihor@seotm.com>
 * @version 1.1, 05.04.2011
 * @copyright (c) 2010+ by SEOTM
 */

include_once(SITE_PATH . '/modules/mod_catalog/catalog.defines.php');

/**
 * Class CatalogLayout
 * class for display interface of Catalog module.
 * @author Igor Trokhymchuk  <ihor@seotm.com>
 * @version 1.1, 05.04.2011
 * @property FrontSpr $Spr
 * @property FrontForm $Form
 * @property db $db
 * @property SystemCurrencies $Currency
 * @property PageUser $PageUser
 *
 */
class CatalogLayout extends CatalogParamLayout
{

    public $db = NULL;
    public $Msg = NULL;
    public $Form = NULL;
    public $Spr = NULL;
    public $Currency = NULL;
    public $is_tags = NULL;
    public $is_comments = NULL;
    public $PageUser = NULL;

    public $task = NULL;


    /**
     * Class Constructor
     *
     * @param $user_id - id of the user
     * @param $module - id of the module
     * @return true/false
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.1, 05.04.2011
     */
    function __construct($user_id = NULL, $module = NULL)
    {
        //Check if Constants are overrulled
        ($user_id != "" ? $this->user_id = $user_id : $this->user_id = NULL);
        ($module != "" ? $this->module = $module : $this->module = 21);

        $this->lang_id = _LANG_ID;
        (defined("USE_TAGS") ? $this->is_tags = USE_TAGS : $this->is_tags = 0);
        (defined("USE_COMMENTS") ? $this->is_comments = USE_COMMENTS : $this->is_comments = 0);

        if (empty($this->db)) $this->db = DBs::getInstance();
        //if (empty($this->Msg)) $this->Msg = check_init('ShowMsg', 'ShowMsg');
        if (empty($this->Form)) $this->Form = check_init('FormCatalog', 'FrontForm', '"form_mod_catalogLayout"');
        if (empty($this->Spr)) $this->Spr = check_init('FrontSpr', 'FrontSpr', "'$this->user_id', '$this->module'");
        if (empty($this->Currency)) $this->Currency = check_init('SystemCurrencies', 'SystemCurrencies', "'$this->user_id', '$this->module', 'front'");
        if (empty($this->settings)) $this->settings = $this->GetSettings(1);

        $this->multi = check_init_txt('TblFrontMulti', TblFrontMulti);

        $lg= check_init('UserAuthorize', 'UserAuthorize');

        if( !isset($lg->user_type) || $lg->user_type<5 ) {
            $this->group_user_id = 5;
        }else{
            $this->group_user_id = $lg->user_type;
        }
        // for folders links
        $this->mod_rewrite = 1;

        //load all data of categories to arrays $this->treeCat, $this->treeCatLevels, $this->treeCatData
        $this->loadTree(true);

        //echo '<br />treeCatLevels=';print_r($this->treeCatLevels);
        //echo '<br />treeCatData=';print_r($this->treeCatData);

    } // End of CatalogLayout Constructor


    /**
     * Class method ShowCatalogTree
     * Checking show tree of catalog
     * @return html
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.1, 05.01.2011
     */
    function ShowCatalogTree()
    {
        $this->main_top_level = $this->getTopLevel($this->id_cat);
        ?>
        <div><? $this->showTree(); ?></div><?
    } //end of function ShowCatalogTree()

    /**
     * Class method showTree
     * Write in html tree of catalog
     * @param array $tree - pointer to array with index as counter
     * @param integer $level - level of catalog
     * @param bool $flag - flag for lyaout
     * @param integer $cnt_sub - count of sublevels
     * @return array with index as counter
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.1, 05.01.2011
     */
    function showTree($level = 0, $flag = 0, $cnt_sub = 0)
    {
        if (!$this->GetTreeCatLevel($level)) return $flag;


        $a_tree = $this->GetTreeCatLevel($level);
        //print_r($a_tree);
        if (empty($a_tree)) return $flag;
        $punkt = '';
        $class_li = 'close';
        $parent_level = 0;
        if ($flag == 0)
            $class = "";
        else {
            $class = "hidden";
            if (!empty($this->id_cat)) {
                $res = $this->isCatASubcatOfLevel($this->id_cat, $level);
                //echo '<br />$res = '.$res;
                if ($res) $class = "active";
            }
        }
        if ($class != "hidden") {
            echo "<ul>\r\n";
            if ($this->id_cat > 0) $parent_level = $this->treeCatData[$this->id_cat]['level'];
            //echo '<br/>$parent_level = '.$parent_level;
            //echo '<br/>$class='.$class;
            $keys = array_keys($a_tree);
            $n = count($keys);
            for ($i = 0; $i < $n; $i++) {

                //echo '<br />$keys[$i]='.$keys[$i];
                $row = $this->treeCatData[$keys[$i]];
                if ($row['id'] == 0) continue;
                //echo '<br />$row=';print_r($row);
                if ($row['id'] == $this->main_top_level) {
                    $class_li = "open";
                } else {
                    if ($row['id'] == $this->id_cat OR $row['id'] == $parent_level) $class_li = "active" . $cnt_sub;
                }
                //echo '<br/>$class_li='.$class_li;
                //$href = $this->Link($a_tree[$i]['id']);
                $href = $this->getUrlByTranslit($row['path']);
                $name = $row['name'];
                echo '<li class="' . $class_li . '">';
                $class_a = '';
                if ($class_li == 'open') $class_a = 'openA';
                $class_li = '';
                if ($this->id_cat == $row['id']) {
                    //echo '<br>$cnt_sub='.$cnt_sub;
                    if ($cnt_sub > 0) echo '<a class="selected ' . $class_a . '" href="' . $href . '">' . $name . '</a>';
                    else
                        echo '<a class="selected ' . $class_a . '" href="' . $href . '">' . $name . '</a>';
                } else
                    echo $punkt . '<a class="' . $class_a . '" href="' . $href . '">' . $name . '</a>';
                //echo '<br>$level='.$level.' $this->id_cat='.$this->id_cat.' $a_tree['.$i.'][level]='.$a_tree[$i]['level'];
                $flag = $this->showTree($row['id'], 1, ($cnt_sub + 1));
                echo "</li>\r\n";
            }
            //echo '<br />$flag='.$flag;
            if ($flag != 0)
                echo "</ul>\r\n";
        }
        return $flag;
    } //end of function showTree()


    // ================================================================================================
    // Function : ShowPathToLevel()
    // Version : 1.0.0
    // Date : 21.03.2006
    //
    // Parms :        $id - id of the record in the table
    // Returns :      $str / string with name of the categoties to current level of catalogue
    // Description :  Return as links path of the categories to selected level of catalogue
    // ================================================================================================
    // Programmer :  Igor Trokhymchuk
    // Date : 10.01.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function ShowPathToLevel($level, $str = NULL, $make_link = NULL, $is_last_element = true)
    {
        $devider = ' / ';
        if ($level > 0) {
            $use_micro_data = ($is_last_element) ? false : true;

            //$tmp_db = DBs::getInstance();
            $row = $this->treeCatData[$level];
            //del
            if($level==397){
                $row['name'] = str_replace("-", "", $row['name']);
            }

            $name = stripslashes($row['name']);
            $link = $this->getUrlByTranslit($row['path']);

            if (!empty($str)) {
                $str = FrontendPages::getMicroFormPathItem( $name, $link, $use_micro_data ).$devider.$str;
                //$str = '<a href="' . $link . '">' . $name . '</a>' . $devider . '<span class="spanShareName">' . $str . "</span>";
            } 
            else {
                if ($make_link == 1) {
                    $str = FrontendPages::getMicroFormPathItem( $name, $link, $use_micro_data);
                }
                else
                    $str = FrontendPages::getMicroFormPathItem( $name, null, $use_micro_data);
            }


            if ($row['level'] > 0) {
                return $this->ShowPathToLevel($row['level'], $str, NULL, false); //here $make_link = NULL, $last_element_flag = false);
            }


            //$str = '<a href="'.$script.'&level=0">'.$this->Msg->show_text('TXT_ROOT_CATEGORY').' > </a>'.$str;
            $str = FrontendPages::getMicroFormPathItem( $this->multi['TXT_FRONT_HOME_PAGE'], _LINK, false  ).$devider
                   .FrontendPages::getMicroFormPathItem( $this->multi['TXT_CATALOG'], _LINK.'catalog/' ).$devider
                   .$str;

        }
        else {
            $str = FrontendPages::getMicroFormPathItem( $this->multi['TXT_FRONT_HOME_PAGE'], _LINK, false) . $devider
                   .FrontendPages::getMicroFormPathItem( $this->multi['TXT_CATALOG'], null, false );
        }
        
        return $str;

        //echo '<a href="'._LINK.'">'.$this->multi['TXT_FRONT_HOME_PAGE'].'</a> '.$devider.' <a href="'._LINK.'catalog/">'.$this->multi['TXT_CATALOG'].'</a> '.$devider.' '.$str;
    } // end of function ShowPathToLevel()

    // ================================================================================================
    // Function : ShowMainCategories()
    // Version : 1.0.0
    // Date : 21.10.2009
    // Parms: $level
    // Returns : true,false / Void
    // Description : show main levels (categories) of catalogue on the front-end
    // Programmer : Yaroslav Gyryn
    // Date : 21.10.2009
    // ================================================================================================
    function ShowMainCategories(){

        $path = $this->ShowPathToLevel(0);

        if (is_array($this->GetTreeCatLevel(0))) {
            ?>
            <ul class="listCat">
                <?
                $keys = array_keys($this->GetTreeCatLevel(0));
                $n = count($keys);
                for ($i = 0; $i < $n; $i++) {
                    $row = $this->GetTreeCatData($keys[$i]);

                    if ($row['id'] == 0) continue;
                    if ($row['show_in_menu'] == 0) continue;

                    $img_cat = $row['img_cat'];
                    $name = stripslashes($row['name']);
                    $link = $this->getUrlByTranslit($row['path']);
                    //del

                    ?>
                    <li <?php echo ( isset($this->id_cat) && $this->id_cat==$row['id']) ? "class='sel-category'": "" ?>>
                        <div class="link-categ">
                        <a href="<?= $link; ?>" title="<?= htmlspecialchars($name); ?>"><span><?= $name; ?></span></a>
                        </div>
                        <div class="menu-param">
                            <?$this->showFilterMenu($row['id']);?>
                        </div>

                    </li>
                <?
                }// end for
                ?>
            </ul>
        <?
        }
        else
        {

        }
    }
    //--- end of function ShowMainCategories() -------------------------------------------------------------------------


function ShowMainCategories2($level){


        $path = $this->ShowPathToLevel($level);

        if (is_array($this->GetTreeCatLevel($level))) {
            ?>
            <ul class="listCat">
                <?
                $keys = array_keys($this->GetTreeCatLevel(0));
                $n = count($keys);
                for ($i = 0; $i < $n; $i++) {
                    $row = $this->GetTreeCatData($keys[$i]);

                    if ($row['id'] == 0) continue;
                    if ($row['show_in_menu'] == 0) continue;

                    $img_cat = $row['img_cat'];
                    $name = stripslashes($row['name']);
                    $link = $this->getUrlByTranslit($row['path']);
                    //del

                    ?>
                    <li <?php echo ( isset($this->id_cat) && $this->id_cat==$row['id']) ? "class='sel-category'": "" ?>>
                        <div class="link-categ">
                        <a href="<?= $link; ?>" title="<?= htmlspecialchars($name); ?>"><span><?= $name; ?></span></a>
                        </div>
                        <div class="menu-param">
                            <?$this->showFilterMenu($row['id']);?>
                        </div>

                    </li>
                <?
                }// end for
                ?>
            </ul>
        <?
        }

    }
    //--- end of function

    function ShowMainCategoriesImg($pathRender=false, $level=0)
    {

        if($pathRender){
            $this->PageUser->breadcrumb = $this->ShowPathToLevel($this->id_cat, NULL, true);
        }
        $path = $this->ShowPathToLevel($level);
        //var_dump( $level, $this->treeCatLevels );

        $tree = $this->GetTreeCatLevel($level);

        if (isset($this->settings['multi_categs']) AND $this->settings['multi_categs'] == 1) {
            $q = "
                SELECT `id_prop`
                FROM `" . TblModCatalogCatMultiCategs . "`
                WHERE `id_cat` = '".$this->id_cat."'
            ";
            $res = $this->db->db_Query($q);
            if ( $res && $this->db->db_GetNumRows() > 0 ){
                while ( $row = $this->db->db_FetchAssoc() ){
                    $tree[ $row['id_prop'] ] = '';
                }
            }
            //else echo $q;
        }
        //var_dump($tree);


        if ( is_array($tree) ): ?>
            <div class="listCatImg">
                <?
                $keys = array_keys($tree);
                $n = count($keys);
                for ($i = 0; $i < $n; $i++) {
                    $row = $this->GetTreeCatData($keys[$i]);

                    $imgSmall = "";
                    if ($row['id'] == 0) continue;
                    if ($row['show_in_menu'] == 0) continue;
                    $img_cat = $row['img_cat'];
                    $name = stripslashes($row['name']);
                    $link = $this->getUrlByTranslit($row['path']);
                    if (!empty($row['img_cat'])) {
                        $imgSmall = ImageK::getResizedImg("/images/mod_catalog_prod/categories/" . $row['img_cat'], 'size_width=187', 85, NULL);
                    }

                    ?>
                    <a href="<?= $link; ?>" title="<?= htmlspecialchars($name); ?>">
                        <?php if (!empty($row['img_cat'])): ?>
                            <img src="<?= $imgSmall ?>" alt="<?= $name ?>">
                        <?php endif; ?>
                        <div class="name-cat" title="<?= htmlspecialchars($name); ?>"><span><?= $name; ?></span></div>
                    </a>
                <?
                }// end for
                ?>
            </div>
        <? endif;
    }


    // ================================================================================================
    // Function : ShowContentCurentLevel()
    // Date : 05.04.2006
    // Returns : true,false / Void
    // Description : show content of curent level of catalogue on the front-end
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function ShowContentCurentLevel($lable=false)
    {
        $cat_data = $this->GetTreeCatData($this->id_cat);

        if($this->id_cat==397){
            $cat_data['name'] = str_replace("-", "", $cat_data['name']);
        }

        if (!isset($cat_data['h1']) OR empty($cat_data['h1'])) {
            $this->PageUser->h1 = $cat_data['name'];
        } else {
            $this->PageUser->h1 = $cat_data['h1'];
        }

        if($lable=='new'){
            $this->PageUser->h1 = "Новинки";
        }
        if($lable=='hit'){
            $this->PageUser->h1 = "Популярные товары";
        }
        if($lable=='share'){
            $this->PageUser->h1 = "Акции";
        }
        $this->PageUser->breadcrumb = $this->ShowPathToLevel($this->id_cat, NULL, 0);

        $params = '';
        $descr1 = '';
        $descr2 = '';
        $props = '';
        $levelsShort = '';

        if(!$lable){
            if (!isset($this->isContent))
                $this->isContent = $this->IsContent($this->id_cat);
            if ($this->isContent > 0) {

                //показывем описание категории только для первой странцы. Если же при постраничности перешли на вторую страницу и далее,
                //то описание не показывать, что бы один и тот же текст не дублитровался при постраничености.

                if (isset($this->settings['cat_descr']) AND $this->settings['cat_descr'] == '1' AND $this->page < 2) {
                    $descr1 = stripslashes($this->treeCatData[$this->id_cat]['descr']);
                }

                $levelsShort = $this->ShowLevelsNameShort($this->treeCatLevels[$this->id_cat], 4);
                $arr = $this->GetListPositionsSortByDate($this->id_cat, 'limit', $this->sort, $this->asc_desc, true, $this->id_param, '', '');
                //var_dump($arr); /*qwerty*/
                $props = $this->ShowListOfContentByPages( $arr );

                //показывем доп. описание категории только для первой странцы. Если же при постраничности перешли на вторую страницу и далее,
                //то описание не показывать, что бы один и тот же текст не дублитровался при постраничености.

                if (isset($this->settings['cat_descr2']) AND $this->settings['cat_descr2'] == '1' AND $this->page < 2) {
                    $descr2 = stripslashes($this->treeCatData[$this->id_cat]['descr2']);
                }

            } else {

                $props = View::factory('/modules/mod_catalog/templates/tpl_catalog_error.php')
                    ->bind('msq', $this->multi['MSG_ERR_NO_ANY_POSITIONS_IN_CATEGORY'])
                    ->bind('back', $this->multi['TXT_FRONT_GO_BACK']);
            }
        }
        else{
            //for fix pagination
            $this->isContent = 1;
            if ($lable == 'new') $this->catLink = '/catalog/novinki/';

            //show data with $lable parametr
            $levelsShort = $this->ShowLevelsNameShort($this->treeCatLevels[$this->id_cat], 4);
            $arr = $this->GetListPositionsSortByDate($this->id_cat, 'limit', $this->sort, $this->asc_desc, true, $this->id_param, '', '', $lable);
            $props = $this->ShowListOfContentByPages( $arr );
        }

        // related props for category:
        $relat_props = $this->GetRelatProp($this->id_cat, TblModCatalogPropRelatForCateg, true);
        if(isset($relat_props) and !empty($relat_props)) {
            ob_start();
            $this->ShowRelatProp($relat_props, true, true, true);
            $this->relPropsForCat = ob_get_clean();
        }
        // end related props for category.

        echo View::factory('/modules/mod_catalog/templates/tpl_catalog_current_level.php')
            ->bind('descr1', $descr1)
            ->bind('levelsShort', $levelsShort)
            ->bind('props', $props)
            ->bind('descr2', $descr2);

    } //end of function ShowContentCurentLevel()

    // ================================================================================================
    // Function : ShowLevelsName()
    // Date : 04.05.2006
    // Returns : true,false / Void
    // Description : show levels (categories) of catalogue on the front-end
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function ShowLevelsName(&$tree, $cols = 5)
    {
        if (!is_array($tree)) return;

        $settings = $this->settings;
        switch ($cols) {
            case '1':
                $width = 'width100';
                break;
            case '2':
                $width = 'width50';
                break;
            case '3':
                $width = 'width32';
                break;
            case '4':
                $width = 'width25';
                break;
            case '5':
                $width = 'width20';
                break;

        }
        ?>
        <div class="float-container">
            <?
            $rows = count($tree);
            $keys = array_keys($tree);
            for ($i = 0; $i < $rows; $i++) {
                $id_cat = $keys[$i];
                //echo '<br />$id_cat='.$id_cat;
                $cat_data = $this->GetTreeCatData($id_cat);
                $name = stripslashes($cat_data['name']);
                $img_cat = stripslashes($cat_data['img_cat']);
                $descr = stripslashes($cat_data['descr']);
                //$descr2 = stripslashes($row['descr2']);
                ?>

                <?
                $link = $this->getUrlByTranslit($cat_data['path'], NULL);
                ?>
                <div class="item float-to-left <?= $width; ?>">
                    <a href="<?= $link; ?>" title="<?= addslashes($name); ?>"><?= $name; ?></a>
                </div>

            <?
            }// end for
            ?>
        </div>
    <?
    } // end of function  ShowLevelsName()


    // ================================================================================================
    // Function : ShowLevelsNameShort()
    // Date : 04.05.2006
    // Returns : true,false / Void
    // Description : show levels (categories) of catalogue on the front-end
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function ShowLevelsNameShort(&$tree, $cols = 5)
    {
        if (!is_array($tree)) return;

        $settings = $this->settings;
        switch ($cols) {
            case '1':
                $width = 'width100';
                break;
            case '2':
                $width = 'width50';
                break;
            case '3':
                $width = 'width32';
                break;
            case '4':
                $width = 'width25';
                break;
            case '5':
                $width = 'width20';
                break;
        }

        $rows = count($tree);
        $cat_data = array();
        $keys = array_keys($tree);
        for ($i = 0; $i < $rows; $i++) {
            $id_cat = $keys[$i];
            //echo '<br />$id_cat='.$id_cat;
            $cat_data[$i] = $this->GetTreeCatData($id_cat);
            $cat_data[$i]['name'] = stripslashes($cat_data[$i]['name']);
            $cat_data[$i]['img_cat'] = stripslashes($cat_data[$i]['img_cat']);
            //$descr = stripslashes($cat_data['descr']);
            //$descr2 = stripslashes($row['descr2']);
            ?>
            <!-- show Name of the category -->
            <?
            if (!empty($cat_data[$i]['href'])) $cat_data[$i]['href'] = _LINK . $cat_data[$i]['href'];
            else $cat_data[$i]['href'] = $this->getUrlByTranslit($cat_data[$i]['path'], NULL);
            ?>

            <!-- show Image of the category -->
            <? //if (!empty($img_cat)) { echo $this->ShowCurrentImage($settings['img_path']."/categories/".$img_cat, 'size_auto=75', 85, NULL, "border=0");}?>
            <!-- show Description of the category -->
            <? //=$descr;?>
        <?
        }
        // end for

        return View::factory('/modules/mod_catalog/templates/tpl_catalog_levels_name.php')
            ->bind('width', $width)
            ->bind('rows', $rows)
            ->bind('cat_data', $cat_data);

    } // end of function  ShowLevelsNameShort()


    /*************************************************************************************************************/

    function ShowListOfContentByPages($arr = NULL, $search_keywords = NULL)
    {
//        var_dump($arr);
        $rows = count($arr);
        //echo '<br>$rows='.$rows;
        if ($rows == 0 or !is_array($arr)) {
            if ($this->task == 'make_advansed_search' or $this->task == 'quick_search' or $this->task == 'make_search_by_params') {
                return View::factory('/modules/mod_catalog/templates/tpl_catalog_error.php')
                    ->bind('msq', $this->multi['MSG_ERR_NO_ANY_POSITIONS_BY_REQUEST'])
                    ->bind('back', $this->multi['TXT_FRONT_GO_BACK']);
            } else {
                if (isset($this->treeCatData[$this->id_cat]['name'])) {
                    $category_name = stripslashes($this->treeCatData[$this->id_cat]['name']);
                    if (!$this->isSubLevels($this->id_cat, $this->treeCatLevels, $this->id_cat)) {
                        return View::factory('/modules/mod_catalog/templates/tpl_catalog_error.php')
                            ->bind('msq', $this->multi['MSG_ERR_NO_ANY_POSITIONS_BY_REQUEST'])
                            ->bind('back', $this->multi['TXT_FRONT_GO_BACK']);
                    } else {
                        // Выбор по параметрам фильтра
                        return View::factory('/modules/mod_catalog/templates/tpl_catalog_error.php')
                            ->bind('msq', $this->multi['MSG_ERR_NO_POSITIONS_BY_PARAM_IN_CATEGORY'])
                            ->bind('back', $this->multi['TXT_FRONT_GO_BACK']);
                    }
                } else {
                    return View::factory('/modules/mod_catalog/templates/tpl_catalog_error.php')
                        ->bind('msq', $this->multi['MSG_ERR_NO_ANY_POSITIONS_IN_CATEGORY'])
                        ->bind('back', $this->multi['TXT_FRONT_GO_BACK']);
                }
            }
        }
        else {
            //echo '<br>$search_keywords='.$search_keywords;
            if (empty($search_keywords)) $sore = $this->showSortPanel();
            else $sore = '';

            if (empty($search_keywords)) $compare = $this->showComparePanel();
            else $compare = '';

            $pagination = '';
            if (empty($this->search_keywords)) {

                    if ($rows >= $this->display or $this->page > 1) {
                        if (!isset($this->catLink) || empty($this->catLink)) {
                            $link = $this->Link($this->id_cat, NULL);
                        } else {
                            $link = $this->catLink;
                        }
                        if (!isset($this->id_param)) $this->id_param = NULL;

                        if (isset($this->rows_prop_for_nolimit)) {
                            $rows = $this->rows_prop_for_nolimit;
                        } else {
                            $rows = count($this->GetListPositionsSortByDate($this->id_cat, 'nolimit', null, 'asc', true, $this->id_param));
                        }

                        if (!empty($this->sort)) {
                            if ($this->asc_desc == 'asc')
                                $asc_desc = 'desc';
                            else
                                $asc_desc = 'asc';
                            if (!empty ($this->url_param))
                                $this->url_param .= '&sort=' . $this->sort . '&asc_desc=' . $asc_desc . '&exist=' . $this->exist;
                            else
                                $this->url_param = '?sort=' . $this->sort . '&asc_desc=' . $asc_desc . '&exist=' . $this->exist;
                        }

                        $paramLink = $this->makeParamLink('page', -1);
                    //echo '$paramLink='.$paramLink;
                        $pagination = $this->Form->WriteLinkPagesStatic($link, $rows, $this->display, $this->start, $this->sort, $this->page, $paramLink, true);
                    }

            } //показываем постраничность для результатов поиска в каталоге
            elseif ($this->task == 'quick_search') {

                $rows = count($this->QuickSearch($this->search_keywords, 'nolimit'));
                $link = _LINK . 'catalog/search/result/' . htmlentities(urlencode($this->search_keywords)) . '/';
                $pagination = $this->Form->WriteLinkPagesStatic($link, $rows, $this->display, $this->start, $this->sort, $this->page);

            }
            //переопределяем постраниченость для новинок
            if($this->task =='new' and ($rows >= $this->display or $this->page > 1)){
                $link = _LINK . 'catalog/novinki/';
                $paramLink = $this->makeParamLink('page', -1);
                $pagination = $this->Form->WriteLinkPagesStatic($link, $rows, $this->display, $this->start, $this->sort, $this->page, $paramLink, true);
            }

            $pagination .= '<div class="two-green-lines"></div>';

            $Comments = new CommentsLayout();
            foreach( $arr as &$prop ){
                $response = $Comments->getCommentsCountAndRating($this->module, $prop['id']);
                //var_dump($response);
                $prop['rating'] = $response['rating'];
                $prop['resp_count'] = $response['count'];
            }

            return View::factory('/modules/mod_catalog/templates/tpl_catalog_props_by_pages.php')
                ->bind('pagination', $pagination)
                ->bind('props', $arr)
                ->bind('Catalog', $this)
                ->bind('sore', $sore)
                ->bind('group_user_id', $this->group_user_id)
                ->bind('compare', $compare);

        }
    } //--- end of ShowListOfContentByPages()


    // ================================================================================================
    // Function : ShowListOfContentByPages()
    // Version : 1.0.0
    // Date : 03.03.2008
    // Parms : $id - id of the position
    // Returns : true,false / Void
    // Description : show list of positions by pages
    // ================================================================================================
    // Programmer : Ihor Trokhymchuk
    // Date : 17.02.2011
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function ShowListShortByPages($arr = NULL)
    {
        $rows = count($arr);
        //echo '<br>$rows='.$rows;
        $cat_data = $this->GetTreeCatData($this->id_cat);
        if ($rows == 0) {
            ?>
            <div class="err" align="center"><?
            $category_name = stripslashes($cat_data['name']);
            if ($this->task == 'make_advansed_search' or $this->task == 'quick_search' or $this->task == 'make_search_by_params') {
                $this->showErr($this->multi['MSG_ERR_NO_ANY_POSITIONS_BY_REQUEST'] . '<br /><a href="javascript:history.back()">' . $this->multi['TXT_FRONT_GO_BACK'] . '</a>');
            } else {
                if (!$this->isCatASubcatOfLevel($this->id_cat, $this->treeCatLevels, $this->id_cat)) {
                    echo $this->multi['MSG_ERR_NO_ANY_POSITIONS_IN_CATEGORY'] . ' <strong>' . $category_name . '</strong><br/><a href="javascript:history.back()">' . $this->multi['TXT_FRONT_GO_BACK'] . '</a>';
                }
            }
            ?></div><?
        } else {
            $settings = $this->settings;
            ?>
            <ul class="categoryContent">
                <?
                for ($i = 0; $i < $rows; $i++) {

                    $row = $arr[$i];
                    $img = $row['first_img'];
                    if( !empty($img) ){
                        $imgSmall = ImageK::getResizedImg( ImageK::getResizedImg($this->getPictureRelPath($row['id'], $img), 'size_height=70', 100, NULL), 'size_width=80', 100, NULL );
                    }


                    $name = stripslashes($row['name']);
                    $group_price = stripslashes($row['group_price']);

                    $link = $this->Link($row['id_cat'], $row['id']);
                    $cur_from = $row['currency_group_price'];

                    $group_price = $this->Currency->Converting($cur_from, _CURR_ID, $group_price, 2);
                    //echo '<br/>$group_price: '.$group_price;


                    ?>
                    <!-- Show Name of Position -->
                    <li>
                        <a href="<?= $link; ?>" title="<?= htmlspecialchars($name); ?>">
                        <div class="img-search-item">
                             <img src="<?=$imgSmall?>" alt="<?=$name?>">
                        </div>
                        <div class="text-search-item">
                            <div class="item-search-price"><?= $name; ?></div>
                            <span><?= $this->Currency->ShowPrice($group_price); ?></span>
                        </div>
                        </a>
                    </li>
                <?
                }
                ?>
            </ul>
            <?
            /*
            $arr = $this->GetListPositionsSortByDate($this->id_cat, 'nolimit', true);
            $rows = count($arr);
            $link = $this->Link($this->id_cat, NULL);
            */


            if ($this->task == 'quick_search') {
                $rows_all = $this->QuickSearch($this->search_keywords, 'nolimit');
                $link = _LINK . 'catalog/search/result/' . htmlentities(urlencode($this->search_keywords)) . '/';
                ?>
                <div
                    style="margin-top:30px; text-align:center;"><? $this->FrontForm->WriteLinkPagesStatic($link, $rows_all, $this->display, $this->start, $this->sort, $this->page); ?></div><?
            }
        }
    } //--- end of ShowListShortByPages()

    // ================================================================================================
    // Function : ShowRatingInfo()
    // Version : 1.0.0
    // Date : 08.04.2006
    // Parms :
    // Returns : true,false / Void
    // Description : show details of curent position of catalogue on the front-end
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 08.04.2006
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function ShowRatingInfo($id)
    {
        $rating = $this->GetAverageRatingByIdProp($id, 'front');
        if ($rating < 1) $rating = 0;
        ?>
        <span class="rat01">
        Р’СЃРµРіРѕ Р±Р°Р»РѕРІ: <?= $this->GetRatingByIdProp($id); ?>
            <br/>Р“РѕР»РѕСЃРѕРІ: <?= $this->GetVotesByIdProp($id); ?>
            <br/><?= $this->Msg->show_text('FLD_RATING') . ': ' . $rating; ?>
        </span>
    <?

    }

    //end of function ShowRatingInfo()

    // ================================================================================================
    // Function : ShowDetailsCurrentPosition()
    // Version : 1.0.0
    // Date : 08.04.2006
    // Parms :
    // Returns : true,false / Void
    // Description : show details of curent position of catalogue on the front-end
    // ================================================================================================
    // Programmer : Yaroslav Gyryn
    // Date : 25.10.2009
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function ShowDetailsCurrentPosition($id_img = NULL)
    {
        $arrSetProp = $this->getSetProp($this->id);

        $settings = $this->settings;
        $tmp_db = DBs::getInstance();
        $catData = $this->GetTreeCatData($this->id_cat);

        $filed_list = ", `" . TblModCatalogPropSprH1 . "`.`name` AS `h1`";
        $left_join = "\n LEFT JOIN `" . TblModCatalogPropSprH1 . "` ON (`" . TblModCatalogProp . "`.`id`=`" . TblModCatalogPropSprH1 . "`.`cod` AND `" . TblModCatalogPropSprH1 . "`.`lang_id`='" . $this->lang_id . "')";

        if (isset($settings['short_descr']) AND $settings['short_descr'] == '1') {
            $filed_list .= ", `" . TblModCatalogPropSprShort . "`.`name` AS `short`";
            $left_join .= "\n LEFT JOIN `" . TblModCatalogPropSprShort . "` ON (`" . TblModCatalogProp . "`.`id`=`" . TblModCatalogPropSprShort . "`.`cod` AND `" . TblModCatalogPropSprShort . "`.`lang_id`='" . $this->lang_id . "')";
        }
        if (isset($settings['full_descr']) AND $settings['full_descr'] == '1') {
            $filed_list .= ", `" . TblModCatalogPropSprFull . "`.`name` AS `full`";
            $left_join .= "\n LEFT JOIN `" . TblModCatalogPropSprFull . "` ON (`" . TblModCatalogProp . "`.`id`=`" . TblModCatalogPropSprFull . "`.`cod` AND `" . TblModCatalogPropSprFull . "`.`lang_id`='" . $this->lang_id . "')";
        }
        if (isset($settings['specif']) AND $settings['specif'] == '1') {
            $filed_list .= ", `" . TblModCatalogPropSprSpecif . "`.`name` AS `specif`";
            $left_join .= "\n LEFT JOIN `" . TblModCatalogPropSprSpecif . "` ON (`" . TblModCatalogProp . "`.`id`=`" . TblModCatalogPropSprSpecif . "`.`cod` AND `" . TblModCatalogPropSprSpecif . "`.`lang_id`='" . $this->lang_id . "')";

        }
        if (isset($settings['reviews']) AND $settings['reviews'] == '1') {
            $filed_list .= ", `" . TblModCatalogPropSprReviews . "`.`name` AS `reviews`";
            $left_join .= "\n LEFT JOIN `" . TblModCatalogPropSprReviews . "` ON (`" . TblModCatalogProp . "`.`id`=`" . TblModCatalogPropSprReviews . "`.`cod` AND `" . TblModCatalogPropSprReviews . "`.`lang_id`='" . $this->lang_id . "')";

        }
        if (isset($settings['support']) AND $settings['support'] == '1') {
            $filed_list .= ", `" . TblModCatalogPropSprSupport . "`.`name` AS `support`";
            $left_join .= "\n LEFT JOIN `" . TblModCatalogPropSprSupport . "` ON (`" . TblModCatalogProp . "`.`id`=`" . TblModCatalogPropSprSupport . "`.`cod` AND `" . TblModCatalogPropSprSupport . "`.`lang_id`='" . $this->lang_id . "')";
        }
        $filed_list .= ", `mod_catalog_param_prop`.`val` AS `valparam`";
        $left_join .= "\n LEFT JOIN `mod_catalog_param_prop` ON (`" . TblModCatalogProp . "`.`id`=`mod_catalog_param_prop`.`id_prop` AND `mod_catalog_param_prop`.`id_param` IN (".MOD_CATALOG_PARAM.") )";
        if($this->group_user_id!=MOD_CATALOG_RETAIL_PRICE){
            $q = "SELECT
                        `".TblModPropGroupPrice."`.`price`,
                        `".TblModPropGroupPrice."`.`group_currency` as retail_currency_group_price
                        FROM  `".TblModPropGroupPrice."`
                            WHERE `".TblModPropGroupPrice."`.`group_id`=".MOD_CATALOG_RETAIL_PRICE."
                            AND    `".TblModPropGroupPrice."`.prod_id = ". $this->id."";

            $res = $tmp_db->db_Query($q);

            if (!$res or !$tmp_db->result)
                return false;

            $rowRetail = $tmp_db->db_FetchAssoc();

        }
        $q = "SELECT
                `" . TblModCatalogProp . "`.*,
                   `".TblModPropGroupPrice."`.`price` as group_price
                   , `".TblModPropGroupPrice."`.`group_currency` as currency_group_price,
                `" . TblModCatalogPropSprName . "`.name
                $filed_list
             FROM `" . TblModCatalogProp . "`
                $left_join ,
                `" . TblModCatalogPropSprName . "`,
                `".TblModPropGroupPrice."`

             WHERE
                `" . TblModCatalogProp . "`.id = `" . TblModCatalogPropSprName . "`.cod
             AND
                `" . TblModCatalogPropSprName . "`.lang_id='" . $this->lang_id . "'
             AND
                `" . TblModCatalogProp . "`.id  =`".TblModPropGroupPrice."`.prod_id
             AND
                `".TblModPropGroupPrice."`.group_id = ".$this->group_user_id."
             AND
                `" . TblModCatalogProp . "`.id ='" . $this->id . "'


                ";

        $res = $tmp_db->db_Query($q);
        // echo '<br> $q='.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res or !$tmp_db->result)
            return false;

        $row = $tmp_db->db_FetchAssoc();
        if (isset($settings['img']) AND $settings['img'] == '1') $row_img = $this->GetPicture($row['id']);
        //var_dump($row_img);
        if (isset($settings['files']) AND $settings['files'] == '1') $row_files = $this->GetFiles($row['id']);
        $name = stripslashes($row['name']);

        if(isset( $row['valparam']) && !empty( $row['valparam'] ) ) {
            $manufac = $this->Spr->GetNameByCod( 'mod_catalog_param_val', $row['valparam'], $this->lang_id, 0 );
            echo "<div class='manufac'>".$manufac."</div>";
        }

        if (!isset($row['h1']) OR empty($row['h1'])) {
            $this->PageUser->h1 = $name;
        } else {
            $this->PageUser->h1 = stripslashes($row['h1']);;
        }
        $this->PageUser->breadcrumb = $this->ShowPathToLevel($this->id_cat, FrontendPages::getMicroFormPathItem($name, null,  false), 1, false);
        ?>
        <div class="body">
        <div class="tovarImage float-to-left width40">
            <!-- display image start-->

            <div class="prop-img-price">
                <?

                if (isset($row_img['0']['id'])) {
                    if (empty($id_img)) $id_img = $row_img[0]['id'];

                    ?>
                    <div class="img-big">

                        <?php

                        $cnt = count($row_img);
                        for ($i = 0; $i < $cnt; $i++) {
                            $path =  ImageK::getResizedImg($this->getPictureRelPath($this->id, $row_img[$i]['path']), 'size_height=1000', 85, 'img');

                            //$path = "http://" . NAME_SERVER . $settings['img_path'] . "/" . $this->id . "/" . $row_img[$i]['path'];

                            if ( empty($row_img[$i]['alt']) ) $row_img[$i]['alt'] = "Фото - ".$name;
                            if ( empty($row_img[$i]['title']) ) $row_img[$i]['title'] = $name." от компании «Ohrana.ua»";

                            $alt = htmlspecialchars(stripslashes($row_img[$i]['alt']));
                            $title = htmlspecialchars(stripslashes($row_img[$i]['title']));

                            //$path = "http://" . NAME_SERVER . $settings['img_path'] . "/" . $this->id . "/" . $row_img[$i]['path'];

                            ?>

                            <a href="<?= $path; ?>"  title="<?= $name; ?>" target="_blank" class="fancybox sel-img" rel="gal" id="i<?=$i;?>" >

                                <?
                                $imgSmall = ImageK::getResizedImg( ImageK::getResizedImg($this->getPictureRelPath($this->id, $row_img[$i]['path']), 'size_height=250', 85, NULL), 'size_width=297', 85, 'img' );
                                ?>
                                <img itemprop="image" src="<?= $imgSmall; ?>" alt='<?= $alt; ?>' title='<?= $title ?>'/>
                            </a>

                        <?
                        }?>

                    </div>
                <?} else { ?>
                    <img src="/images/design/no-image.jpg" alt="no-photo" title="no-photo" border="0"/><?
                }?>
                <div itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
                    <div class="tovar-detail">
                        <div class="price-btn-buy">
                            <?php if($row['group_price']!=0):?>
                                <?
                                $price_group = $this->Currency->Converting($row['currency_group_price'], _CURR_ID, $row['group_price'], 2);

                                if($row['currency_group_price'] == 1 && $this->group_user_id==5){
                                    $price_group = round($price_group);
                                }

                                ?> <div class="new_price"><span itemprop="price"><?= $this->Currency->ShowPrice($price_group); ?></span>
                                    <meta itemprop="priceCurrency" content="UAH"/>
                                    <link itemprop="availability" href="http://schema.org/InStock"/>
                                </div>
                                <?php

                                if($this->group_user_id!=MOD_CATALOG_RETAIL_PRICE):
                                    $retail_price = $this->Currency->Converting($rowRetail['retail_currency_group_price'], _CURR_ID, $rowRetail['price'], 2);

                                    if($rowRetail['retail_currency_group_price']==1) {
                                        $retail_price = round($retail_price);
                                    }

                                    ?>
                                    <div class="retail-price">
                                        Розница:<br>
                                        <?= $this->Currency->ShowPrice($retail_price); ?>
                                    </div>
                                <?php endif;?>
                                <?php
                                if(isset($row['shareprop']) AND !empty($row['shareprop']) AND $this->group_user_id==5) {
                                    ?>
                                    <div class="old-share-price">
                                        <?= $this->Currency->ShowPrice($row['oldprice']);?>
                                    </div>
                                <?
                                }
                                ?>
                                <div class="buyBlock">
                                    <?if( !empty($row['group_price']) && $row['exist'] ==1 ){?>
                                        <?php
                                        if($arrSetProp){
                                            $listProdSet = implode(',', $arrSetProp);
                                            $countList = implode(",", array_keys($arrSetProp) ) ;
                                            ?>
                                            <form action="#" method="post" name="catalog" id="catalog<?= $row['id'] ?>">
                                                <div class="quantityField">
                                                    <input type="text" size="2" value="1" class="quantity" onkeypress="return me()"
                                                           id="productId[<?= $row['id'] ?>]" name="productId[<?= $row['id'] ?>]" maxlength="2"/>
                                                </div>

                                                <div class="buybutton">
                                                    <a href="#" id="multiAdds<?= $row['id'] ?>" onclick="addToCartSet('catalog<?= $row['id'] ?>', 'cart', '<?= $row['id'] ?>', '<?=$countList?>', '<?=$listProdSet?>');return false;"><span>Купить</span></a>
                                                </div>
                                            </form>
                                        <?

                                        }else{
                                            ?>
                                            <form action="#" method="post" name="catalog" id="catalog<?= $row['id'] ?>">
                                                <div class="quantityField">
                                                    <input type="text" size="2" value="1" class="quantity" onkeypress="return me()"
                                                           id="productId[<?= $row['id'] ?>]" name="productId[<?= $row['id'] ?>]" maxlength="2"
                                                        />
                                                </div>

                                                <div class="buybutton">
                                                    <a href="#" id="multiAdds<?= $row['id'] ?>" onclick="addToCart('catalog<?= $row['id'] ?>', 'cart', '<?= $row['id'] ?>');return false;"><span>Купить</span></a>
                                                </div>
                                            </form>
                                        <?
                                        }

                                    }?>
                                </div>
                            <?php endif;?>
                        </div>
                        <div class="addToCart" id="al<?= $row['id']?>"></div>
                        <div class="prod-short">
                            <span itemprop="description"><?=$row['short']?></span>
                            <?php
                            $this->Comments = new CommentsLayout($this->module, $this->id);
                            $_prop = $this->Comments->getCommentsCountAndRating($this->module, $this->id);
                            ob_start();
                            $this->Comments->ShowComments();
                            $comments_html = ob_get_clean();

                            echo '<div class="rating-small-block">';
                            if ( $_prop['rating'] > 0 ){
                                echo '<ul class="stars">';
                                for ($j = 1; $j < 6; $j++) {
                                    if ($j == $_prop['rating']) echo '<li class="on"></li>';
                                    else echo '<li></li>';
                                }
                                echo '</ul><br/>';
                            }
                            if ( $_prop['count'] > 0 ) { ?>
                                <div itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">
                                    <meta itemprop="ratingValue" content="<?=$_prop['rating']?>"/>
                                    <meta itemprop="bestRating" content="5"/>
                                    <a href="#" onclick="goto_comments(); return false;">Отзывы (<span itemprop="reviewCount"><?=$_prop['count']?></span>)</a>
                                </div>
                            <?
                            }
                            echo '</div>';
                            ?>
                        </div>
                    </div>
                </div>
                <? if (isset($row_img['0']['id'])) :?>
                    <div class="image_carousel" style="float:left">
                        <div id="foo1" class="floatToLeft">
                            <?

                            $counter = 0;
                            for ($i = 0; $i < $cnt; $i++) {

                                $path = "http://" . NAME_SERVER . $settings['img_path'] . "/" . $this->id . "/" . $row_img[$i]['path'];
                                $alt = htmlspecialchars(stripslashes($row_img[$i]['alt']));
                                $title = htmlspecialchars(stripslashes($row_img[$i]['title']));
                                $counter++;
                                ?><a class="marginRight3 floatToLeft marginTop8" data-id="<?=$i?>" title="<?= $name; ?>" target="_blank"><?= $this->ShowCurrentImage($row_img[$i]['path'], 'size_rect=88x69', 95, NULL, "alt='" . $alt . "' title='" . $title . "'", $this->id); ?></a><?
                            } ?>
                        </div>
                        <div class="clearfix"></div>
                        <a class="prevP" id="foo1_prev" href="#"><span>prev</span></a>
                        <a class="nextP" id="foo1_next" href="#"><span>next</span></a>

                    </div>
                <?php endif;?>
            </div>

            <!--display image end-->
        </div>
        <?php
        if($arrSetProp) {
            $listIdProp = implode(',', array_keys($arrSetProp));
            $countSet = $this->getCountSet($this->id, $listIdProp);
            $sumSetcount = 0;

            foreach ($countSet as $k => $v) {
                $sumSetcount = $sumSetcount + $v;
            }
        }

        ?>
        <div class="fullDesc">
            <div class="top-tabs">
                <div class="btn-categ active-t">
                    <div class="wrapper-tab">
                        <div class="tab-item active-tab" data-item="0">
                            Описание               </div>
                    </div>
                </div>
                <?php  if($arrSetProp):?>
                    <div class="btn-categ">
                        <div class="wrapper-tab">
                            <div class="tab-item" data-item="3">Комплектация (<?=$sumSetcount?> шт.)</div>
                        </div>
                    </div>
                <?php endif;?>
                <div class="btn-categ">
                    <div class="wrapper-tab">
                        <div class="tab-item" data-item="1">Характеристики</div>
                    </div>
                </div>
                <div class="btn-categ">
                    <div class="wrapper-tab">
                        <div class="tab-item" data-item="2">Документы и файлы</div>
                    </div>
                </div>
                <div id="responseTab" class="btn-categ">
                    <div class="wrapper-tab">
                        <div class="tab-item" data-item="4">Отзывы (<?=$this->Comments->commentsCount?>)</div>
                    </div>
                </div>
            </div>
            <div class="categ-prop active-prod" id="p0">
                <br/>

                <?
                if (isset($settings['full_descr']) AND $settings['full_descr'] == '1') {
                    $val = stripslashes($row['full']);
                    if (!empty($val)){
                        echo $val;
                    }
                }

                if ( $this->Comments->commentsCount > 0 ){
                    echo '<div class="h1main"><div class="line2"></div><span>Последние отзывы</span></div>';
                    echo $this->Comments->last_comments;
                } ?>
            </div>
            <div class="categ-prop" id="p1">
                <?
                //$this->ShowParamsOfProp($this->id);

                $arrChar =  $this->getCharacteristic($this->id);

                if($arrChar) {
                    ?>
                    <table border="0" cellspacing="0" cellpadding="0" class="param-prop-inside">
                    <tbody>
                    <?
                    foreach($arrChar as $k=>$v) {
                        ?><tr>
                        <td><?=$v['name']?></td>
                        <td><?=$v['descr']?></td>
                        </tr><?
                    }
                    ?>
                    </tbody>
                    </table><?
                } ?>
            </div>
            <div class="categ-prop" id="p2">
                <div class="prod-file">

                    <?  $arr_fiels = $this->GetFiles($this->id, $front_back = 'front');
                    //print_r($arr_fiels);
                    $cnt = count($arr_fiels);
                    if ($cnt > 0) {

                        for ($i = 0; $i < $cnt; $i++) {
                            ?><div><?
                            $files = $arr_fiels[$i];
                            $file_filename = stripslashes($files['path']);
                            $file_path = 'http://' . NAME_SERVER . Catalog_Upload_Files_Path . '/' . $this->id . '/' . $file_filename;
                            $file_title = stripslashes($files['name']);
                            $file_text = stripslashes($files['text']);
                            if (!empty($file_title)) $file_name = $file_title;
                            else $file_name = $file_filename;
                            ?><a href="<?= $file_path; ?>" title="<?= $file_name; ?>"><span><?= $file_name; ?></span></a><?
                            if (!empty($file_text)) {
                                ?><div class="desc-file"><?= $file_text; ?></div><?
                            }
                            ?></div><?
                        }

                    }
                    ?>
                </div>
            </div>
            <?php  if($arrSetProp):?>
                <div class="categ-prop" id="p3">
                    <?php $this->showSetProp($arrSetProp, $countSet);?>
                </div>
            <?php endif;?>
            <div class="categ-prop" id="p4">
                <input id="showCommentsForm" type="button" class="green-button" onclick="$('#commentsForm').show(350);" value="Оставить отзыв"/>
                <hr class="dotted-line" style="width: 665px "/>
                <div id="comments-wrapper">
                    <?php echo $comments_html; ?>
                </div>
            </div>
        </div>
        <?php
        if ( isset($_GET['otzivy']) ):?>
        <script>
            goto_comments();
        </script>
        <? endif;


        $arrP = $this->GetRelatProp($this->id, TblModCatalogPropRelat, true); //aksesuary

        $arrP2 = $this->GetRelatProp($this->id, TblModCatalogPropRelat2, true); // pohozhye

        if(isset($arrP2) and !empty($arrP2)): ?>
            <!-- description -->
            <?
            ob_start();
            $this->ShowRelatProp( $arrP2, true,true);
            $this->leftProps = ob_get_clean(); ?>

        <?php endif;

        if(isset($arrP)):
            ?>
            <div class="relat-prop-bg">
                <div class="relat-prop">
                    <!-- description -->
                    <?
                    $this->ShowRelatProp( $arrP );
                    ?>

                </div>
                <div class="shadow-slide"></div>
            </div>
        <?php endif; ?>
        <!-- fullDescr-->
        <!--TovarDetail-->
        <? $this->id_img = NULL; ?>
        </div>
    <?

    } //end of function ShowDetailsCurrentPosition()




    function getCharacteristic() {
        $q = "SELECT * FROM `".TblModCatalogPropCharacteristic."` WHERE `prod_id`='".$this->id."' ORDER BY `id` ASC";

        $db = new DB();
        $res = $db->db_Query( $q );
        if( !$res  ) return false;
        $rows = $db->db_GetNumRows();

        if($rows>0) {

            $arr = array();
            for($i=0;$i<$rows; $i++) {
                $row = $db->db_FetchAssoc();
                $arr[] = $row;
            }

            return $arr;
        }
        return false;


    }


    function showSetProp($arrProp, $countSet) {

        if( is_array($arrProp) ) {

            $listIdProp = implode(',', array_keys($arrProp));




            $tmp_db = new DB();

            $q = "SELECT
                    `".TblModCatalogProp."`.*,
                    `" . TblModCatalogPropSprName . "`.name,
                    `" . TblModCatalogPropSprShort . "`.name AS `short`,
                    `" . TblModCatalogTranslit . "`.translit,
                    `".TblModCatalogPropImg."`.`path` AS `first_img`

                    FROM
                        `".TblModCatalogProp."`
                         LEFT JOIN `".TblModCatalogPropImg."` ON (`".TblModCatalogProp."`.`id`=`".TblModCatalogPropImg."`.`id_prop` AND `".TblModCatalogPropImg."`.`move`='1' AND `".TblModCatalogPropImg."`.`show`='1'),
                        `" . TblModCatalogPropSprName . "`,
                        `" . TblModCatalogPropSprShort . "`,
                        `" . TblModCatalogTranslit . "`

                    WHERE
                        `".TblModCatalogProp."`.`id` IN (".$listIdProp.")
                    AND `" . TblModCatalogPropSprName . "`.cod = `".TblModCatalogProp."`.`id`
                    AND `" . TblModCatalogPropSprName . "`.lang_id = '".$this->lang_id."'
                    AND `" . TblModCatalogPropSprShort . "`.cod = `".TblModCatalogProp."`.`id`
                    AND `" . TblModCatalogPropSprShort . "`.lang_id = '".$this->lang_id."'
                    AND `" . TblModCatalogTranslit . "`.`id_prop` = `".TblModCatalogProp."`.`id`


                        ";

           // echo $q;
            $res = $tmp_db->db_Query($q);

            if (!$res or !$tmp_db->result)
                return false;

            $rows = $tmp_db->db_GetNumRows();

           // echo $rows;
            if($rows > 0) {
                ?>

                <div class="set-product">

                <table>
                <?

                for($i=0; $i<$rows; $i++) {
                    $class= '';
                    if ($i%2 == 0){
                        $class = 'gray';
                    }


                    $row = $tmp_db->db_FetchAssoc();

                    if( !empty($row['first_img']) ){
                        $img = ImageK::getResizedImg( ImageK::getResizedImg($this->getPictureRelPath($row['id'], $row['first_img']), 'size_height=100', 85, NULL), 'size_width=147', 85, NULL );
                    }else{
                        $img = '/images/design/no-photo.jpg';
                    }


                    ?>
                    <tr class="<?php echo $class?>">
                        <td colspan="3">

                            <div class="link-item-set-product">
                                <a href="<?=$this->Link($row['id_cat'],$row['id'])?>"><?=$row['name']?> </a>
                            </div>
                        </td>
                    </tr>
                    <tr class="<?php echo $class?>">
                        <td>
                            <div class="img-item-set-product">
                                <a href="<?=$this->Link($row['id_cat'],$row['id'])?>"><img src="<?=$img?>" alt="<?=$row['name']?>"></a>
                            </div>
                        </td>
                        <td>


                                    <div class="desc-item-set-product">
                                        <?=$row['short']?>
                                    </div>


                        </td>
                        <td>
                            <div class="count-set">
                                <?=$countSet[ $row['id'] ]?> шт.
                            </div>
                        </td>
                    </tr>

                    <?


                }

                ?>
                </table>
                </div><?

            }



        }


    }




    function getCountSet($id,$setProp) {

        $q = "SELECT `id_prop2`, `count` FROM ".TblModCatalogSetProp." WHERE `id_prop1` = ".$id." AND `id_prop2` IN (".$setProp.") ";

        $tmp_db = new DB();

        $res = $tmp_db->db_Query($q);

        if (!$res or !$tmp_db->result)
            return false;

        $rows = $tmp_db->db_GetNumRows();

        if($rows > 0) {
           $arr = array();

            for($i=0;$i<$rows;$i++){

                $row = $tmp_db->db_FetchAssoc();
                $arr[ $row['id_prop2'] ] = $row['count'];


            }
            return $arr;
        }

        return false;

    }

    // ================================================================================================
    // Function : ShowPrintVersion()
    // Version : 1.0.0
    // Date : 23.07.2008
    // Parms :
    // Returns : true,false / Void
    // Description : show print version of page
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 23.07.2008
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function ShowPrintVersion()
    {
        $title = NULL;
        $description = NULL;
        $keywords = NULL;
        ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <!-- <html xmlns="http://www.w3.org/1999/xhtml" lang="ru" xml:lang="ru"> -->
        <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
            <meta http-equiv='Content-Type' content="application/x-javascript; charset=utf-8"/>
            <meta http-equiv="Content-Language" content="ru"/>
            <title>$title</title>
            <meta name="Description" content="<?= $description; ?>"/>
            <meta name="Keywords" content="<?= $keywords; ?>"/>
            <link href="/include/css/main1.css" type="text/css" rel="stylesheet"/>
            <link href="/include/css/screen.css" type="text/css" rel="stylesheet" media="screen"/>
            <!--[if IE ]>
            <link href="/include/css/browsers/ie.css" rel="stylesheet" type="text/css" media="screen"/>
            <![endif]-->
            <!--[if lt IE 8]>
            <link href="/include/css/browsers/ie7.css" rel="stylesheet" type="text/css" media="screen"/>
            <![endif]-->
            <!--[if lt IE 7]>
            <link href="/include/css/browsers/ie6.css" rel="stylesheet" type="text/css" media="screen"/>
            <script type="text/javascript" src="/include/js/iepngfix_tilebg.js"></script>
            <![endif]-->
            <!--[if lt IE 6]>
            <script src="/include/js/ie5.js" type="text/javascript"></script>
            <![endif]-->
        </head>

        <body style="background-color: white;">
        <?
        $settings = $this->GetSettings();
        $q = "SELECT
                `" . TblModCatalogProp . "`.id,
                `" . TblModCatalogProp . "`.id_cat,
                `" . TblModCatalogProp . "`.id_manufac,
                `" . TblModCatalogProp . "`.number_name,
                `" . TblModCatalogProp . "`.price,
                `" . TblModCatalogProp . "`.opt_price,
                `" . TblModCatalogProp . "`.art_num,
                `" . TblModCatalogProp . "`.barcode,
                `" . TblModCatalogPropSprName . "`.name
             FROM `" . TblModCatalogProp . "`, `" . TblModCatalogPropSprName . "`
             WHERE
                `" . TblModCatalogProp . "`.id = `" . TblModCatalogPropSprName . "`.cod
             AND
                `" . TblModCatalogPropSprName . "`.lang_id='" . $this->lang_id . "'
             AND
                `" . TblModCatalogProp . "`.id ='" . $this->id . "'";

        $res = $this->db->db_Query($q);
        //echo '<br> $q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;
        if (!$res or !$this->db->result) return false;

        $rows = $this->db->db_GetNumRows();
        $row = $this->db->db_FetchAssoc();
        $row_img = $this->GetPicture($row['id']);
        $row_files = $this->GetFiles($row['id']);
        $name = stripslashes($row['name']);
        ?>
        <h1 class="bgrnd"><?= $name; ?></h1>

        <div class="subBody">
            <div class="path"><? $this->ShowPathToLevel($this->id_cat, NULL, 0); ?></div>
            <div class="tovarImage float-to-left">
                <!-- display image start-->
                <?
                if (isset($row_img['0']['id'])) {
                    if (empty($id_img)) $id_img = $row_img['0']['id'];
                    $path = "http://" . NAME_SERVER . $settings['img_path'] . "/" . $this->id . "/" . $row_img['0']['path'];
                    ?>
                    <div class="float-to-left"><a href="<?= $path; ?>" rel="itemImg" title="<?= $name; ?>"
                                                  target="_blank"><?= $this->ShowCurrentImage($id_img, 'size_auto=300', 85, NULL, ""); ?></a>
                    </div>
                    <div id="thumb">
                        <?
                        $cnt = count($row_img);
                        for ($i = 1; $i < $cnt; $i++) {
                            $path = "http://" . NAME_SERVER . $settings['img_path'] . "/" . $this->id . "/" . $row_img[$i]['path'];
                            ?>
                            <a href="<?= $path; ?>" rel="itemImg" title="<?= $name; ?>"
                               target="_blank"><?= $this->ShowCurrentImage($row_img[$i]['id'], 'size_auto=50', 85, NULL, ""); ?></a>
                            <br/>
                        <?
                        }
                        ?>
                    </div>
                    <script type="text/javascript">
                        $("a[rel='itemImg']").colorbox();
                    </script>
                <?

                } else {
                    ?><img src="/images/design/no-photo<?= _LANG_ID; ?>.gif" alt="no-photo" title="no-photo"
                           border="0"/><?
                }
                ?>
                <!--display image end-->
            </div>

            <div class="tovarDetail">
                <?
                echo $this->Spr->GetNameByCod(TblModCatalogPropSprShort, $this->id, $this->lang_id, 1);
                if (!empty($row['art_num'])) {
                    ?><br/><?=
                    $this->multi['FLD_ART_NUM']; ?> <?=
                    stripslashes($row['art_num']);
                }
                if (!empty($row['barcode'])) {
                    ?><br/><?=
                    $this->multi['FLD_BARCODE']; ?> <?=
                    stripslashes($row['barcode']);
                }

                if (isset($settings['price']) AND $settings['price'] == '1') {
                    $price = $this->Currency->Converting($this->GetPriceCurrency($row['id']), _CURR_ID, $row['price'], 2);
                    ?>
                    <span class="price"><?= $this->Currency->ShowPrice($price); ?></span>
                    <br/>
                <?
                }
                ?>
            </div>
            <hr/>

            <div class="fullDesc">
                <!-- description -->
                <?
                if (isset($settings['full_descr']) AND $settings['full_descr'] == '1') {
                    $val = $this->Spr->GetNameByCod(TblModCatalogPropSprFull, $this->id, $this->lang_id, 1);
                    if (!empty($val)) {
                        ?><h3><?= $this->multi['FLD_FULL_DESCR']; ?></h3>
                        <div><?= $val; ?></div><?
                    }
                }
                ?>
                <!-- description -->
                <?
                if (isset($settings['full_descr']) AND $settings['full_descr'] == '1') {
                    $val = $this->Spr->GetNameByCod(TblModCatalogPropSprSpecif, $this->id, $this->lang_id, 1);
                    if (!empty($val)) {
                        ?><h3><?= $this->multi['FLD_SPECIF']; ?></h3>
                        <div><?= $val; ?></div>
                        <hr/>
                    <?
                    }
                }
                ?>
            </div>
        </div>
        <a href="javascript:window.close()"><u><?= $this->multi['TXT_CLOSE']; ?></u></a>

        </body>
        </html>

    <?
    } // end of function ShowPrintVersion()


    // ================================================================================================
    // Function : GetLinksToParamsNames ()
    // Version : 1.0.0
    // Programmer : Yaroslav Gyryn
    // Date : 15.06.2009
    // Parms :   $id_cat         // id of current category
    // Returns : str
    // Description : return names & values of parameters in string for current catalogue
    // ================================================================================================
    function GetLinksToParamsNames($id_cat, $spacer = ' - ', $showLink = true)
    {
        // echo '<br>$params='.$params.' $id_cat='.$id_cat;
        //if ( $params==0 ) return;
        $str = NULL;
        $params_row = $this->GetParams($id_cat);
        $link = $this->Link($id_cat);
        $param = NULL;
        //echo '<br>$params_row=';print_r($params_row);

        for ($i = 0; $i < count($params_row); $i++) {
            if ($params_row[$i]['modify'] != 1)
                continue;
            $paramCategory = $this->Spr->GetNameByCod(TblModCatalogParamsSprName, ($params_row[$i]['id']), $this->lang_id, 1);
            if ($paramCategory != 'РџСЂРѕРёР·РІРѕРґРёС‚РµР»СЊ')
                continue;
            $val = NULL;
            $str .= '';
            //echo '<br>$params_row['.$i.'][id]='.$params_row[$i]['id'];
            $tblname = $this->BuildNameOfValuesTable($params_row[$i]['id_categ'], $params_row[$i]['id']);
            switch ($params_row[$i]['type']) {
                case '2':
                    $val = $this->Spr->GetListName(TblSysLogic, $this->lang_id, 'array', 'move', 'asc', 'all');
                    break;
                case '3':
                case '4':
                    $val = $this->Spr->GetListName($tblname, $this->lang_id, 'array', 'move', 'asc', 'all');
                    break;
            }

            //$prefix = $this->Spr->GetNameByCod(TblModCatalogParamsSprPrefix,($params_row[$i]['id']), $this->lang_id, 1);
            //$sufix = $this->Spr->GetNameByCod(TblModCatalogParamsSprSufix,($params_row[$i]['id']), $this->lang_id, 1);
            //echo '<br> $val='.$val;print_r($val);
            if (is_array($val))
                foreach ($val as $k => $v) {
                    // Р¤РѕСЂРјР°С‚РёСЂРѕРІР°РЅРЅС‹Р№ РІС‹РІРѕРґ С‚РµРєСЃС‚Р° Р»РёР±Рѕ СЃСЃС‹Р»РєРё РїР°СЂР°РјРµС‚СЂР°
                    if ($str == '') {
                        if ($showLink)
                            $str = ' <a href="' . $link . '?' . PARAM_VAR_NAME . PARAM_VAR_SEPARATOR . $params_row[$i]['id'] . '=' . $v['cod'] . '">' . $v['name'] . '</a>';
                        else
                            $str = ' ' . $v['name'];
                    } else {
                        if ($showLink)
                            $str .= $spacer . '<a href="' . $link . '?' . PARAM_VAR_NAME . PARAM_VAR_SEPARATOR . $params_row[$i]['id'] . '=' . $v['cod'] . '">' . $v['name'] . '</a>';
                        else
                            $str .= $spacer . $v['name'];
                    }
                }
            $str .= ' ';
        }
        return $str;
    } //end of function GetLinksToParamsNames ()


    // ================================================================================================
    // Function : GetParamsNamesValuesOfPropInStr()
    // Version : 1.0.0
    // Programmer : Yaroslav Gyryn
    // Date : 15.06.2009
    // Parms :   $id_cat         // id of current category
    // Returns : str
    // Description : return names & values of parameters in string for current catalogue
    // ================================================================================================
    function GetParamsNamesValuesOfPropInStr($id_cat)
    {
        //$params = $this->IsParams( $id_cat );
        // echo '<br>$params='.$params.' $id_cat='.$id_cat;
        //if ( $params==0 ) return;
        $str = NULL;
        $params_row = $this->GetParams($id_cat);
        $link = $this->Link($this->id_cat);
        $param_str = NULL;
        $this->url_param = NULL;
        $param = NULL;
        $filtr = NULL;
        $sorting = '';
        //echo '<br>$params_row=';print_r($params_row);
        if (!empty($this->sort)) {
            $sorting = '&sort=' . $this->sort . '&asc_desc=' . $this->asc_desc . '&exist=' . $this->exist;
        }
        $n = count($params_row);
        for ($i = 0; $i < $n; $i++) {
            if ($params_row[$i]['modify'] != 1) continue;
            $val = NULL;
            $paramName = $this->Spr->GetNameByCod(TblModCatalogParamsSprName, ($params_row[$i]['id']), $this->lang_id, 1);
            /*if($paramName=="РџСЂРѕРёР·РІРѕРґРёС‚РµР»СЊ")
                continue;*/
            $str .= '<div class="paramBlock"><div class="paramName">' . $paramName . ':</div>';
            //echo '<br>$params_row['.$i.'][id]='.$params_row[$i]['id'];
            $tblname = $this->BuildNameOfValuesTable($params_row[$i]['id_categ'], $params_row[$i]['id']);
            switch ($params_row[$i]['type']) {
                case '1':
                    //$val = $v;
                    break;
                case '2':
                    $val = $this->Spr->GetListName(TblSysLogic, $this->lang_id, 'array', 'move', 'asc', 'all');
                    break;
                case '3':
                case '4':
                    $val = $this->Spr->GetListName($tblname, $this->lang_id, 'array', 'move', 'asc', 'all');
                    break;
                /*  case '5':
                        $val = $v;
                        break;*/
            }

            $prefix = $this->Spr->GetNameByCod(TblModCatalogParamsSprPrefix, ($params_row[$i]['id']), $this->lang_id, 1);
            $sufix = $this->Spr->GetNameByCod(TblModCatalogParamsSprSufix, ($params_row[$i]['id']), $this->lang_id, 1);
            //echo '<br> $val='.$val;print_r($val);
            $str .= '<div class="paramKey">';
            if (is_array($val)) {
                $showAll = false;

                // Р¤РѕСЂРјРёСЂРѕРІР°РЅРёРµ СЃС‚СЂРѕРєРё РїР°СЂР°РјРµС‚СЂРѕРІ
                //print_r($this->arr_current_img_params_value);
                if (is_array($this->arr_current_img_params_value)) {
                    $param_str = NULL;
                    //echo' <br>$params_row[$i][id] ='.$params_row[$i]['id'];
                    foreach ($this->arr_current_img_params_value as $key => $value) {
                        //echo' $key ='.$key;
                        if ($key != $params_row[$i]['id']) {
                            $param = '&' . PARAM_VAR_NAME . PARAM_VAR_SEPARATOR . $key . '=' . $value;
                            $param_str .= $param;
                            if (substr_count($this->url_param, $param) == 0)
                                $this->url_param .= $param;
                        }
                    }
                }

                foreach ($val as $k => $v) {

                    // РџСЂРѕРІРµСЂРєР° РёР»Рё РІС‹Р±СЂР°РЅ РєРѕРЅРєСЂРµС‚РЅС‹Р№ РїР°СЂР°РјРµС‚СЂ
                    $checked = false;
                    if (is_array($this->arr_current_img_params_value))
                        foreach ($this->arr_current_img_params_value as $key => $value)
                            if ($key == $params_row[$i]['id'] AND $value == $v['cod']) {
                                $checked = true;
                                break;
                            }

                    // Р¤РѕСЂРјР°С‚РёСЂРѕРІР°РЅРЅС‹Р№ РІС‹РІРѕРґ С‚РµРєСЃС‚Р° Р»РёР±Рѕ СЃСЃС‹Р»РєРё РїР°СЂР°РјРµС‚СЂР°
                    if ($checked == true) {
                        $str .= '<span class="paramSelected">' . $prefix . ' ' . $v['name'] . ' ' . $sufix . '</span> | ';
                        $showAll = true;
                    } else if ($param_str != NULL)
                        $str .= '<a href="' . $link . '?' . $param_str . '&' . PARAM_VAR_NAME . PARAM_VAR_SEPARATOR . $params_row[$i]['id'] . '=' . $v['cod'] . $sorting . '">' . $prefix . ' ' . $v['name'] . ' ' . $sufix . '</a> | ';
                    else
                        $str .= '<a href="' . $link . '?' . PARAM_VAR_NAME . PARAM_VAR_SEPARATOR . $params_row[$i]['id'] . '=' . $v['cod'] . $sorting . '">' . $prefix . ' ' . $v['name'] . ' ' . $sufix . '</a> | ';
                }

                // Р’С‹РІРѕРґ СЃСЃС‹Р»РєРё "Р’СЃРµ"
                if ($showAll == true) {
                    if ($param_str != NULL)
                        $str .= '<a href="' . $link . '?' . $param_str . $sorting . '">Р’СЃРµ</a>';

                    else
                        $str .= '<a href="' . $link . '?' . $sorting . '">Р’СЃРµ</a>';
                    $filtr = true;
                } else
                    $str .= '<span class="param_all">Р’СЃРµ</span>';
            }

            $str .= '</div></div><div class="next_line"></div>';
        }
        if ($filtr)
            $str .= '<div class="paramClear" align="right"><a href="' . $link . '?' . $sorting . '"<img src="/images/design/paramClearBtn.gif"</a></div>';
        return $str;
    } //end of function GetParamsNamesValuesOfPropInStr()

    // ================================================================================================
    // Function : ShowParamsOfProp()
    // Version : 1.0.0
    // Date : 21.04.2006
    // Parms :
    // Returns : true,false / Void
    // Description : show details parameters of curent position of catalogue on the front-end
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 08.04.2006
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function ShowParamsOfProp($id)
    {
        //--------------------------------------------------------------------------------------------------
        //------------------------------------ SHOW PARAMETERS ---------------------------------------------
        //--------------------------------------------------------------------------------------------------
        $this->id = $id;

        $params = $this->IsParams($this->id_cat);
        if ($params == 0) return true;
        ?>
        <table border="0" cellspacing="0" cellpadding="0"  class="param-prop-inside">
        <?

        $style1 = '';
        $style2 = '';
        $params_row = $this->GetParams($this->id_cat);
        $value = $this->GetParamsValuesOfProp($this->id);
        for ($i = 0; $i < count($params_row); $i++) {

            if ((float)$i / 2 == round($i / 2)) {
                echo '<TR CLASS="' . $style1 . '">';
            } else echo '<TR CLASS="' . $style2 . '">';

            isset($value[$params_row[$i]['id']]) ? $val_from_table = $value[$params_row[$i]['id']] : $val_from_table = NULL;
            if ($id != NULL) $this->Err != NULL ? $val = $this->arr_params[$params_row[$i]['id']] : $val = $val_from_table;
            else $val = $this->arr_params[$params_row[$i]['id']];
            if (count($val) == 0 OR empty($val)) continue;

            ?>
            <td><span class="bold-text"><?= stripslashes($params_row[$i]['name']); ?>:</span><?
            ?>
            <td><?
            $tblname = TblModCatalogParamsVal; //$this->BuildNameOfValuesTable($params_row[$i]['id_categ'], $params_row[$i]['id']);
            //echo '<br> $tblname='.$tblname;

            switch ($params_row[$i]['type']) {
                case '1':
                    ?>
                    <table border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td><?= stripslashes($params_row[$i]['prefix']); ?></td>
                            <td><?= $val . stripslashes($params_row[$i]['sufix']); ?>
                        </tr>
                    </table>
                    <?
                    //$this->Form->TextBox( 'arr_params['.$params_row[$i]['id'].']', $val, 15 );
                    break;
                case '2':
                    ?>
                    <table>
                        <tr>
                            <td><?= stripslashes($params_row[$i]['prefix']); ?></td>
                            <td><?= $this->Spr->GetNameByCod(TblSysLogic, $val, $this->lang_id, 1) . stripslashes($params_row[$i]['sufix']); ?>
                        </tr>
                    </table>
                    <?
                    //$this->Spr->ShowInComboBox( TblSysLogic, 'arr_params['.$params_row[$i]['id'].']', $val, 50 );
                    break;
                case '3':
                    ?>
                    <table>
                        <tr>
                            <td><?= stripslashes($params_row[$i]['prefix']); ?></td>
                            <td><?= strip_tags($this->GetNameOfParamVal($params_row[$i]['id_categ'], $params_row[$i]['id'], $val, $this->lang_id, 1)); ?>
                                <?= stripslashes($params_row[$i]['sufix']); ?>
                        </tr>
                    </table>
                    <?
                    //$this->Spr->ShowInComboBox( $tblname, 'arr_params['.$params_row[$i]['id'].']', $val, 50 );
                    break;
                case '4':
                    //echo '<br> count($val)='.count($val);
                    //if ( count($val)==0 ) {
                    ?>
                    <table>
                        <tr>
                            <td><?= stripslashes($params_row[$i]['prefix']); ?></td>
                            <td><?= $this->GetNameOfParamMultiplesVal($params_row[$i]['id_categ'], $params_row[$i]['id'], $val, $this->lang_id, 1); ?>
                            <td><?= stripslashes($params_row[$i]['sufix']); ?>
                        </tr>
                    </table>
                    <?
                    //}
                    //echo $this->Spr->GetNameByCod($tblname,$val, $this->lang_id, 1);
                    break;
                case '5':
                    ?>
                    <table>
                        <tr>
                            <td><?= stripslashes($params_row[$i]['prefix']); ?></td>
                            <td><?= $val; ?>
                                <?= stripslashes($params_row[$i]['sufix']); ?>
                        </tr>
                    </table>
                    <?
                    //$this->Form->TextBox( 'arr_params['.$params_row[$i]['id'].']', $val, 40 );
                    break;
            }
        }
        ?></table><?
        //--------------------------------------------------------------------------------------------------
        //---------------------------------- END SHOW PARAMETERS -------------------------------------------
        //--------------------------------------------------------------------------------------------------
    } // end of function ShowParamsOfProp()


    // ================================================================================================
    // Function : GetParamsValuesOfPropInTable()
    // Version : 1.0.0
    // Date : 18.04.2006
    // Parms :   $id         / id of curent position
    //           $divider    / symbol to divide parameters one from one. (default defider is <br>)
    //           $id_img     / id of the image (for image influence on parameters)
    // Returns : true,false / Void
    // Description : return values of parameters in string for current position of catalogue
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 18.04.2006
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function GetParamsValuesOfPropInTable($id, $id_img = NULL)
    {
        $id_cat = $this->GetCategory($id);
        $params = $this->IsParams($id_cat);
        if ($params == 0) return;

        $params_row = $this->GetParams($id_cat);
        $value = $this->GetParamsValuesOfProp($id);
        $str = NULL;
        ?>
        <table border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td></td>
        </tr>
        <?
        $j = 0;
        for ($i = 0; $i < count($params_row); $i++) {
            $tblname = $this->BuildNameOfValuesTable($params_row[$i]['id_categ'], $params_row[$i]['id']);

            if (!empty($id_img)) {
                $value_param_img = $this->GetParamsValuesOfPropForImg($id_img, $params_row[$i]['id']);
                //echo '<br> $value_param_img='; print_r($value_param_img);
                isset($value_param_img[$params_row[$i]['id']]) ? $val_from_table = $value_param_img[$params_row[$i]['id']] : $val_from_table = NULL;
                if (empty($val_from_table)) {
                    isset($value[$params_row[$i]['id']]) ? $val_from_table = $value[$params_row[$i]['id']] : $val_from_table = NULL;
                }
            } else {
                isset($value[$params_row[$i]['id']]) ? $val_from_table = $value[$params_row[$i]['id']] : $val_from_table = NULL;
            }
            $val = $val_from_table;

            //echo '<br> $val='.$val;

            $prefix = stripslashes($params_row[$i]['prefix']);
            $sufix = stripslashes($params_row[$i]['sufix']);
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
                    $val = str_replace("\n", "<br>", $val);
                    break;
            }
            if (empty($val)) continue;
            $j++;
            ?>
            <tr>
            <td><?= stripslashes($params_row[$i]['name']); ?>:&nbsp;<?= $prefix; ?></td>
            <td><img src="/images/design/spacer.gif" width="5" alt="" title=""/></td>
            <td><?= $val . ' ' . $sufix; ?></td>
            </tr><?
        }
        ?></table><?
        if ($j == 0) return false;
        //echo '<br> $str='.$str;
    } //end of function  GetParamsValuesOfPropInTable()


// ================================================================================================
// Function : ShowSearchForm()
// Version : 1.0.0
// Date : 05.04.2006
// Parms :
// Returns : true,false / Void
// Description : show search form of catalogue on the front-end
// ================================================================================================
// Programmer : Igor Trokhymchuk
// Date : 05.04.2006
// Reason for change : Reason Description / Creation
// Change Request Nbr:
// ================================================================================================
    function ShowSearchForm()
    {
        ?>
        <h1 class="bgrnd"><?= $this->multi['TXT_SEARCH_CATALOG']; ?></h1>
        <div class="body">
            <form name="quick_find" method="post" action="<?= _LINK ?>catalog/search/result/">
                <input type="hidden" name="task" value="quick_search">
                <!--input type="hidden" name="categ" value=""-->

                <?if (!empty($this->search_keywords))
                    $value = $this->search_keywords;
                else
                    $value = 'РЈРєР°Р¶РёС‚Рµ РЅР°РёРјРµРЅРѕРІР°РЅРёРµ';
                ?>
                <div>
                    <input type="text"
                           onblur="if(this.value=='') { this.value='РЈРєР°Р¶РёС‚Рµ РЅР°РёРјРµРЅРѕРІР°РЅРёРµ'; }"
                           onfocus="if(this.value=='РЈРєР°Р¶РёС‚Рµ РЅР°РёРјРµРЅРѕРІР°РЅРёРµ') { this.value=''; }"
                           name="search_keywords" value="<?= $value; ?>" size="50" maxlength="50">
                    <input type="submit" title="<?= $this->multi['TXT_SEARCH']; ?>"
                           value="<?= $this->multi['TXT_SEARCH']; ?>">
                </div>
            </form>
        </div>
        <?
        return true;
    } //end of function ShowSearchForm()


// ================================================================================================
// Function : ShowSearchResult()
// Version : 1.0.0
// Date : 25.04.2006
// Parms :  $rows - rows with data of result of search
// Returns : true,false / Void
// Description : show all images of current position of catalogue
// ================================================================================================
// Programmer : Igor Trokhymchuk
// Date : 25.04.2006
// Reason for change : Reason Description / Creation
// Change Request Nbr:
// ================================================================================================
    function showSearchResult($rows, $search_keywords = NULL)
    {
        ?>



                <?
                //$this->ShowListOfContentByPages($rows, $search_keywords);
                $this->ShowListShortByPages($rows, $search_keywords);
                ?>

    <?
    } //end of function ShowSearchResult()


    /**
     * Class method ShowCatalogMap
     * show catalog map for sitemap
     * @param $topLevel - level of category
     * @return true/false
     * @author Yaroslav Gyryn  <yaroslav@seotm.com>
     * @version 1.0, 17.01.2011
     */
    function ShowCatalogMap($topLevel = 0)
    {
        if (!isset($this->treeCatLevels[$topLevel])) return;
        $a_tree = $this->treeCatLevels[$topLevel];
        ?>
        <ul><?
        $keys = array_keys($a_tree);
        $n = count($keys);
        for ($i = 0; $i < $n; $i++) {
            $row = $this->treeCatData[$keys[$i]];
            $href = $this->getUrlByTranslit($row['path']);
            $name = stripslashes($row['name']);
            ?>
            <li><a href="<?= $href; ?>"><?= $name; ?></a><?
            $this->ShowCatalogMap($row['id']);

            //----------------- show content of the level ----------------------
            if (array_key_exists($row['id'], $this->catalogProducts)) {
                ?>
                <ul><?
                $keys2 = array_keys($this->catalogProducts[$row['id']]);
                $n2 = count($keys2);
                //foreach($this->catalogProducts[$row['id']] as $k=>$v){
                for ($j = 0; $j < $n2; $j++) {
                    $v = $this->catalogProducts[$row['id']][$keys2[$j]];
                    $link = $this->getUrlByTranslit($row['path'], $v['translit']);

                   // var_dump($row);
                    $link = $this->Link($row['id'],$v['translit']);
                    $name = stripslashes($v['name']);
                    if (!empty($name)) {
                        ?>
                        <li><a href="<?= $link; ?>" title="<?= $name ?>"><?= $name; ?></a><?
                    }
                }
                ?></ul><?
            }
            //------------------------------------------------------------------
            ?></li><?
        }
        ?></ul><?
    }

    // end of function ShowCatalogMap()


    // ================================================================================================
    // Function : ShowErr()
    // Version : 1.0.0
    // Date : 10.01.2006
    //
    // Parms :
    // Returns :      true,false / Void
    // Description :  Show errors
    // ================================================================================================
    // Programmer :  Igor Trokhymchuk
    // Date : 10.01.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function showErr($txt = NULL)
    {
        if (empty($txt)) $txt = $this->Err;
        if ($txt) {
            echo '
        <table border=0 cellspacing=0 cellpadding=0 class="err" width="98%" align=center>
         <tr><td>' . $txt . '</td></tr>
        </table>';
        }
    } //end of fuinction ShowErr()


    // ================================================================================================
    // Function : ShowLastPositions
    // Version : 1.0.0
    // Date : 14.05.2007
    //
    // Parms :  $rows - count of rows
    // Returns : $res / Void
    // Description : show last positions from catalogue
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 14.05.2007
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function ShowLastPositions($rows)
    {

        if (count($rows) == 0 or !is_array($rows)) return false;
        $settings = $this->GetSettings();
        $cols_in_row = 2;
        //echo '<br> count($rows)='.count($rows);
        //print_r($rows);

        ?>
        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
                <?
                $j = 0;
                $i = 0;
                foreach ($rows as $key => $value) {
                $i++;
                $img = $this->GetFirstImgOfProp($value['id']);

                if ($j == $cols_in_row) {
                ?></tr>
            <tr valign="top"><?
                $j = 0;
                }
                $name = $value['name'];

                // for folders links
                if ($this->mod_rewrite == 1) $link = $this->Link($value['id_cat'], NULL);
                else $link = "catalogcat_" . $value['id_cat'] . "_" . $this->lang_id . ".html";

                //count($rows)>2 ? $width="34%" : $width="50%";
                ?>

                <td>
                    <table border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <a href="<?= $link; ?>" title="<?= addslashes($name); ?>">
                                    <?
                                    if (!empty($img)) {
                                        echo $this->ShowCurrentImage($img, 'size_auto=150', '85', NULL, "border=0");
                                    }
                                    ?>
                                </a>
                            </td>
                        </tr>
                    </table>
                </td>
                <?
                $j++;
                } //end foreach

                ?>
            </tr>
        </table>
    <?
    } //end of function ShowLastPositions()

    // ================================================================================================
    // Function : ShowRelatCategs()
    // Version : 1.0.0
    // Date : 07.05.2007
    // Parms :
    // Returns : true,false / Void
    // Description : show relation categories for current category
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 07.05.2007
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function ShowRelatCategs($arr)
    {
        if (!is_array($arr) OR count($arr) == 0) return false;
        $col_in_row = 3;
        count($arr) == 1 ? $width = "100%" : (count($arr) > 2 ? $width = "33%" : $width = "50%");

        ?>
        <table border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td><h3>Р РЋР ??Р С•РЎвЂљРЎР‚Р С‘РЎвЂљР Вµ РЎвЂљР В°Р С”Р В¶Р Вµ:</h3></td>
            </tr>
            <tr>
                <?
                $i = 0;
                foreach ($arr as $key => $value) {
                if ($i == $col_in_row) {
                ?></tr>
            <tr><?
                $i = 0;
                }
                if ($value['id_cat1'] == $this->id_cat) $id_relat_cat = $value['id_cat2'];
                else $id_relat_cat = $value['id_cat1'];
                $str = $this->GetPathToLevel($id_relat_cat);
                ?>
                <td width="<?= $width; ?>" align="center" valign="middle">
                    <?= $str; ?>
                    <?
                    $this->ShowRandomContent($this->GetRandomContent2($id_relat_cat, 1, 100000));?>
                </td>
                <?
                $i++;
                }
                ?>
            </tr>
        </table>
    <?
    } //end of function ShowRelatCategs()


// ================================================================================================
// Function : ShowRelatProp()
// Version : 1.0.0
// Date : 14.05.2007
// Parms :
// Returns : true,false / Void
// Description : show relation positiona for current positionf of catalog
// ================================================================================================
// Programmer : Igor Trokhymchuk
// Date : 07.05.2007
// Reason for change : Reason Description / Creation
// Change Request Nbr:
// ================================================================================================
//ShowRelatProp($this->GetRelatProp($this->id))
function ShowRelatProp($arr, $slider=true, $vertical=false, $for_categ=false)
{
    $Crypt = check_init('Crypt', 'Crypt');
    if($slider):

        if($for_categ){
            $items_cnt = 3;
            $height = 1221;
        }
        else{
            $items_cnt = 2;
            $height = 675;
        }

        if($vertical){?>
            <div class="props-tab-left">
            <script>

                $(document).ready(function(){
                    $(".relat-prop-slider-left").carouFredSel({
                        direction: "up",
                        align: "top",
                        height: <?=$height?>,
                        items : <?=$items_cnt?>,
                        auto: {
                            play: true,
                            timeoutDuration: 10000
                        },
                        prev : {
                            button: "#hit-prev-left",
                            key: "up"
                        },
                        next : {
                            button: "#hit-next-left",
                            key: "down"
                        },
                        scroll: {
                            pauseOnHover: true
                        }

                    });

                });
            </script>
            <div class="relat-prop-slider-left">

        <?}
        else{?>
            <div class="props-tab">
            <div class="h1main">
                <div class="line2"></div>
                <span><?=$this->multi['_TXT_RELATE_PROP_']?></span>
            </div>
            <script>

                $(document).ready(function(){
                    $(".relat-prop-slider").carouFredSel({
                        items : 3,
                        auto: false,
                        prev : "#hit-prev",
                        next : "#hit-next"
                    });
                });
            </script>
            <div class="relat-prop-slider">
        <?php
        }
    endif;
    $i = 0;

    if ( $for_categ ) $id1 = $this->id_cat; //це такий костиль
    else $id1 = $this->id;

    foreach ($arr as $key => $value) {

        if ($value['id_prop1'] == $id1){
            $id_relat = $value['id_prop2'];
        }else{
            $id_relat = $value['id_prop1'];
        }

        $group_price = $this->GetPrice($id_relat);

        if ($this->mod_rewrite == 1){
            $link = $this->Link($this->GetCategory($id_relat), $id_relat);
        }else{
            $link = "catalog_" . $this->GetCategory($id_relat) . "_" . $id_relat . "_" . $this->lang_id . ".html";
        }

        $name = $this->Spr->GetNameByCod(TblModCatalogPropSprName, $id_relat);
        $short = $this->Spr->GetNameByCod(TblModCatalogPropSprReviews, $id_relat);
        ?>
        <div class="prod">
            <a class="prod-name" href="<?= $link;?>"><?=$name ?></a>
            <?
            $img = $this->GetNameFirstImgOfProp($id_relat);

            if (isset($img) AND !empty($img)) {
                $imgSmall = ImageK::getResizedImg($this->getPictureRelPath($id_relat, $img), 'size_auto=150', 85, NULL);
                ?>
                <a class="img-prod-link" href="<?= $link; ?>"><img src="<?= $imgSmall; ?>" alt="<?=$name?>" title=""/></a>
            <?

            }
            else { ?>
                <a href="<?= $link; ?>" alt="<?= $name; ?>" title="<?= $this->multi['TXT_NO_IMAGE'] ?>">
                    <img src="<?=ImageK::getResizedImg(DEFAULT_NO_IMAGE_PATH, 'size_auto=150', 85, NULL)?>" alt="no-photo" title="no-photo"/>
                </a>
            <?
            }
            ?>
            <div class="short-prop">
                <?= $Crypt->TruncateStr($short,300); ?>
            </div>
            <?
            $group_currency = $this->GetPriceCurrency($id_relat);
            $retail_price= $this->GetPriceReletiv($id_relat);
            $cur_from = $group_currency;
            if ($cur_from == 0)
                $cur_from = $this->def_currency;

            $price = $this->Currency->Converting($cur_from, _CURR_ID, stripslashes($group_price), 2);

            if($this->group_user_id == MOD_CATALOG_RETAIL_PRICE AND $cur_from==1) {
                $price = round($price);
            }

            $price = $this->Currency->ShowPrice($price);

            if($this->group_user_id!=MOD_CATALOG_RETAIL_PRICE) {

                $cur_from = $retail_price['group_currency'];
                if ($cur_from == 0){
                    $cur_from = $this->def_currency;
                }

                $priceR = $this->Currency->Converting($cur_from, _CURR_ID, stripslashes($retail_price['price']), 0);
                $priceR = $this->Currency->ShowPrice($priceR);
            }

            ?>
            <div class="prop-btm">

                <?if($group_price!=0):?>
                    <div>

                        <div class="price-prod">
                            <?php if( !empty($price) ):?>
                                <span><?=$price?></span>
                            <?endif;?>
                        </div>

                    </div>
                    <div>
                        <?
                        $list_id=$this->getSetProp($id_relat);
                        if( !isset($list_id) or empty($list_id) ):
                            ?>
                            <form action="#" method="post" name="catalog<?= $id_relat ?>" id="catalog<?= $id_relat ?>">
                                <input type="hidden" size="2" value="1" class="quantity" onkeypress="return me()" id="productId[<?= $id_relat ?>]" name="productId[<?= $id_relat ?>]" maxlength="2"/>
                                <div class="buybutton">
                                    <a href="#" onclick="addToCart('catalog<?= $id_relat?>', 'cart', <?= $id_relat?>);return false;" alt="<?= $this->multi['TXT_BUY']; ?>" title="<?= $this->multi['TXT_BUY']; ?>"><span><?= $this->multi['TXT_BUY'] ?></span></a>
                                </div>
                            </form>
                        <? else:
                            $listid = implode("," , $list_id);
                            $countList = implode(",", array_keys($list_id) ) ;
                            ?>
                            <form action="#" method="post" name="catalog" id="catalog<?=$id_relat;?>">

                                <input type="hidden" size="2" value="1" class="quantity" onkeypress="return me()" id="productId[<?=$id_relat;?>]" name="productId[<?=$id_relat;?>]" maxlength="2"/>
                                <div class="buybutton">
                                    <a href="#" id="multiAdds<?=$id_relat;?>" onclick="addToCartSet('catalog<?=$id_relat;?>', 'cart', '<?=$id_relat;?>', '<?=$countList?>', '<?=$listid?>');return false;">
                                        <span><?=$this->multi['TXT_BUY'];?></span>
                                    </a>
                                    <div id="al<?=$id_relat;?>"></div>
                                </div>
                            </form>
                        <? endif;?>
                        <div class="full-prod-link">
                            <div class="addToCart" id="al<?= $id_relat?>"></div> <a href="<?=$link?>"><?=$this->multi['TXT_DETAILS']?></a>
                        </div>
                    </div>
                <?php endif;?>
            </div>
        </div>
        <?
        $i++;
    }
    ?>
    <?php if($slider):?>

        </div>
        <?if($vertical){?>
            <div class="prop-prev-left" id="hit-prev-left" style="display: block;"></div>
            <div class="prop-next-left" id="hit-next-left" style="display: block;"></div>
        <?}else{?>
            <div class="prop-prev" id="hit-prev" style="display: block;"></div>
            <div class="prop-next" id="hit-next" style="display: block;"></div>
        <?}?>
    </div>

<? endif;
}



    // ================================================================================================
    // Function : ShowResponsesByIdProp()
    // Version : 1.0.0
    // Date : 08.08.2007
    // Parms : $id_prop - id of the position
    // Returns : true,false / Void
    // Description : show form with responses from users about goods
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 08.08.2007
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function ShowResponsesByIdProp($id_prop)
    {
        $tmp_db = DBs::getInstance();
        if (empty($id_prop)) return;

        $q = "SELECT * FROM `" . TblModCatalogResponse . "` WHERE `id_prop`='$id_prop' AND `status`='3' order by `dt` desc";
        $res = $tmp_db->db_Query($q);
        //echo '<br> $q='.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res) return false;
        if (!$tmp_db->result) return false;
        $rows = $tmp_db->db_GetNumRows();
        if ($rows > 0) {
            ?>
            <h2><?= $this->Msg->show_text('TXT_FRONT_USERS_RESPONSES'); ?></h2>
            <table border="0" cellpadding="0" cellspacing="0">
                <?
                for ($i = 0; $i < $rows; $i++) {
                    $row = $tmp_db->db_FetchAssoc();
                    ?>
                    <tr>
                        <td>
                            [<?= $row['dt'] ?>]&nbsp;<?=
                            stripslashes($row['name']);
                            if ($row['rating'] > 0) {
                                echo $this->Msg->show_text('TXT_FRONT_USER_RATING_IS'); ?>
                                <b><?= $row['rating']; ?></b><?
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?= stripslashes($row['response']) ?></td>
                    </tr>
                    <tr>
                        <td height="10"></td>
                    </tr>
                <?
                }
                ?>
            </table>
        <?
        }
        return true;
    } //end of function ShowResponsesByIdProp()

    // ================================================================================================
    // Function : ShowResponses()
    // Version : 1.0.0
    // Date : 08.08.2007
    // Parms :
    // Returns : true,false / Void
    // Description : show form with responses from users about goods
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 08.08.2007
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function ShowResponses()
    {
        $tmp_db = DBs::getInstance();
        ?><h1><?= $this->multi['TXT_FRONT_USERS_RESPONSES']; ?></h1><?
        $mas = $this->GetCatalogInArray(NULL, '--- ' . $this->multi['TXT_SELECT_POSITIONS'] . ' ---', NULL, NULL, 1, 'front');
        $name_fld = 'val';

        $scriplink = '/response.php?task=show_responses'; //'onChange="CheckCatalogPosition(this, this.value, '."'".$this->multi('ERR_SELECT_POSITION')."'".'); location='.$scriplink.'&'.$name_fld.'=this.value"'
        ?>
        <div><? $this->Form->SelectAct($mas, $name_fld, 'curcod=' . $this->id, "onChange=\"ret = CheckCatalogPosition(this, this.value, '" . $this->multi['ERR_SELECT_POSITION']. "'); if( ret== true) {location='$scriplink&$name_fld='+this.value} \""); ?></div><?


        if (empty($this->id)) return;

        $q = "SELECT * FROM `" . TblModCatalogResponse . "` WHERE `id_prop`=$this->id AND `status`='3' order by `dt` desc";
        $res = $tmp_db->db_Query($q);
        //echo '<br> $q='.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res) return false;
        if (!$tmp_db->result) return false;
        $rows = $tmp_db->db_GetNumRows();
        ?>
        <table border="0" cellpadding="0" cellspacing="0">
            <?
            if ($rows == 0) {
                ?>
                <tr>
                <td><?= $this->multi['TXT_FRONT_NO_RESPONSES']; ?></td>
                </tr><?
            }
            /*if ($this->task=="save_response") {?><tr><td><?=$this->Msg->show_text('TXT_FRONT_RESPONSES_IS_ADDED');?></td></tr><?}*/
            if ($this->task == "save_response") {
                ?>
                <tr>
                <td><?= $this->multi['TXT_FRONT_RESPONSES_IS_ADDED_NOW']; ?></td>
                </tr><?
            }

            for ($i = 0; $i < $rows; $i++) {
                $row = $tmp_db->db_FetchAssoc();
                ?>
                <tr>
                    <td>
                        [<?= $row['dt'] ?>]&nbsp;<?=
                        stripslashes($row['name']);
                        if ($row['rating'] > 0) {
                            echo $this->multi['TXT_FRONT_USER_RATING_IS']; ?><b><?= $row['rating']; ?></b><?
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><?= stripslashes($row['response']) ?></td>
                </tr>
                <tr>
                    <td height="10"></td>
                </tr>
            <?
            }
            ?>
        </table>
        <?=
        $this->ShowResponseForm(); ?>
        <?
        return true;
    } //end of function ShowResponses()

    // ================================================================================================
    // Function : ShowResponseForm()
    // Version : 1.0.0
    // Date : 08.08.2007
    // Parms :
    // Returns : true,false / Void
    // Description : show form to leave responses and rating
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 08.08.2007
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function ShowResponseForm()
    {
        $settings = $this->GetSettings();
        if (isset($settings['responses']) AND $settings['responses'] == '1') $is_response = true;
        else $is_response = 0;
        if (isset($settings['rating']) AND $settings['rating'] == '1') $is_rating = true;
        else $is_rating = 0;

        // for folders links
        if ($this->mod_rewrite == 1) $link = $this->Link($this->GetCategory($this->id), $this->id, 'response');
        else $link = "leave_comments.html";

        $v1 = rand(1, 9);
        $v2 = rand(1, 9);
        $sum = $v1 + $v2;

        $this->ShowJS();
        $this->Form->WriteFrontHeader('save_response', $link, $task = 'save_response', 'onsubmit="return check_form_response(this, this.my_gen_v.value, ' . $is_response . ', ' . $is_rating . ' );"')
        ?>
        <table border="0" cellpadding="1" cellspacing="0">
            <input type="hidden" name="curcod" value="<?= $this->id ?>">
            <input type="hidden" name="my_gen_v" value="<?= $sum; ?>"/>
            <tr>
                <td><h2><?= $this->Msg->show_text('TXT_FRONT_LEAVE_RESPONSES'); ?></h2></td>
            </tr>
            <tr>
                <td>
                    <table border="0" cellpadding="2" cellspacing="2">
                        <tr>
                            <td><?= $this->Msg->show_text('TXT_FRONT_USER_NAME'); ?>:&nbsp;<span
                                    class="input-requirement">*</span></td>
                            <td><? $this->Form->TextBox('name', $this->name, 'size="40"'); ?></td>
                        </tr>
                        <tr>
                            <td><?= $this->Msg->show_text('TXT_FRONT_USER_EMAIL'); ?>:&nbsp;<span
                                    class="input-requirement">*</span></td>
                            <td><? $this->Form->TextBox('email', $this->email, 'size="40"'); ?></td>
                        </tr>
                        <?
                        if ($is_response) {
                            ?>
                            <tr>
                                <td><?= $this->Msg->show_text('TXT_FRONT_USER_RESPONSE'); ?>:&nbsp;<span
                                        class="input-requirement">*</span></td>
                                <td><? $this->Form->TextArea('response', $this->response, 9, 60, NULL); ?></td>
                            </tr>
                        <? } ?>
                        <?
                        if ($is_rating) {
                            ?>
                            <tr>
                                <td><?= $this->Msg->show_text('TXT_FRONT_USER_RATING'); ?>:&nbsp;<span
                                        class="input-requirement">*</span></td>
                                <td>
                                    <?
                                    $this->Form->Radio('rating', 1, "0", "1");?>&nbsp;&nbsp;&nbsp;<?
                                    $this->Form->Radio('rating', 2, "0", "2");?>&nbsp;&nbsp;&nbsp;<?
                                    $this->Form->Radio('rating', 3, "0", "3");?>&nbsp;&nbsp;&nbsp;<?
                                    $this->Form->Radio('rating', 4, "0", "4");?>&nbsp;&nbsp;&nbsp;<?
                                    $this->Form->Radio('rating', 5, "0", "5");
                                    ?>
                                </td>
                            </tr>
                        <? } ?>
                        <tr>
                            <td colspan="2"><b><?= $this->Msg->show_text('TXT_FRONT_SPAM_PROTECTION'); ?>:&nbsp;<span
                                        class="input-requirement">*</span></b>
                                <b><?= $this->Msg->show_text('TXT_FRONT_SPAM_PROTECTION_SPECIFY_SUM'); ?>
                                    &nbsp;<?= $v1; ?>
                                    +<?= $v2; ?>?</b> <? $this->Form->TextBox('usr_v', NULL, 'size="2"'); ?></td>
                        </tr>
                        <tr>
                            <td colspan="2" align="left"><span
                                    class="input-requirement">*</span> <?= $this->Msg->show_text('TXT_FRONT_REQUIREMENT_FIELDS'); ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td><? $this->Form->Button('save_response', $this->Msg->show_text('TXT_FRONT_ADD_RESPONSE'), 50); ?></td>
            </tr>
            </form>
        </table>
        <?
        $this->Form->WriteFrontFooter();
    } //end of function ShowResponseForm()


    // ================================================================================================
    // Function : ShowJS()
    // Version : 1.0.0
    // Date : 08.08.2007
    // Parms :
    // Returns : true,false / Void
    // Description : show form with rating from users about goods
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 08.08.2007
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function ShowJS()
    {
        ?>
        <script type="text/javascript">
            var form = "";
            var submitted = false;
            var error = false;
            var error_message = "";

            function check_input(field_name, field_size, message) {
                if (form.elements[field_name] && (form.elements[field_name].type != "hidden")) {
                    var field_value = form.elements[field_name].value;

                    if (field_value == '' || field_value.length < field_size) {
                        error_message = error_message + "* " + message + "\n";
                        error = true;
                    }
                }
            }

            function check_radio(field_name, message) {
                var isChecked = false;

                if (form.elements[field_name] && (form.elements[field_name].type != "hidden")) {
                    var radio = form.elements[field_name];

                    for (var i = 0; i < radio.length; i++) {
                        if (radio[i].checked == true) {
                            isChecked = true;
                            break;
                        }
                    }

                    if (isChecked == false) {
                        error_message = error_message + "* " + message + "\n";
                        error = true;
                    }
                }
            }

            function check_select(field_name, field_default, message) {
                if (form.elements[field_name] && (form.elements[field_name].type != "hidden")) {
                    var field_value = form.elements[field_name].value;

                    if (field_value == field_default) {
                        error_message = error_message + "* " + message + "\n";
                        error = true;
                    }
                }
            }

            function check_antispam(field_name, usr_v, message) {
                if (form.elements[field_name] && (form.elements[field_name].type != "hidden")) {
                    var field_value = form.elements[field_name].value;

                    if (field_value == '' || field_value != usr_v) {
                        error_message = error_message + "* " + message + "\n";
                        error = true;
                    }
                }
            }

            function check_form_response(form_name, my_gen_v, response, rating) {
                error_message = '';
                if (submitted == true) {
                    alert("<?=$this->Msg->show_text('MSG_FRONT_ERR_FORM_ALREADY_SUBMITED');?>");
                    return false;
                }

                error = false;
                form = form_name;

                check_input("name", 2, "<?=$this->Msg->show_text('MSG_FRONT_ERR_SPECIFY_YOUR_NAME');?>");
                check_input("email", 2, "<?=$this->Msg->show_text('MSG_FRONT_ERR_SPECIFY_YOUR_EMAIL');?>");
                if (response == true) check_input("response", 5, "<?=$this->Msg->show_text('MSG_FRONT_ERR_SPECIFY_YOUR_RESPONSE');?>");
                if (rating == true) check_radio("rating", "<?=$this->Msg->show_text('MSG_FRONT_ERR_SPECIFY_YOUR_RATING');?>");
                check_antispam("usr_v", my_gen_v, "<?=$this->Msg->show_text('MSG_FRONT_ERR_SPECIFY_ANTISMAP_SUM');?>");

                if (error == true) {
                    alert(error_message);
                    return false;
                } else {
                    submitted = true;
                    return true;
                }
            }
        </script>
    <?
    } // end of functin ShowJS()


    // ================================================================================================
    // Function : BestProducts()
    // Date : 01.17.2011
    // Programmer : Yaroslav Gyryn
    // Description : Shows best products
    // ================================================================================================
    function BestProducts($limit = null, $fltr_id = 2, $informer = false, $order = false)
    {
        ob_start();
        switch ($fltr_id) {
            case '1':
                $ftl = "`" . TblModCatalogProp . "`.new ='1'"; // Display new

                break;
            case '2':
                $ftl = "`" . TblModCatalogProp . "`.best ='1'"; // Display best

                break;
            case '3':
                $ftl = "`" . TblModCatalogProp . "`.shareprop ='1'"; // Display best

                break;
        }
        $str = "";
        ?>

        <?
        if ($this->id_cat != "") $str = $this->getSubLevels($this->id_cat);
        else $str = implode( $this->GetTreeCatLevel() );

        $db = new DB();


        $q = "SELECT
                `" . TblModCatalogProp . "`.id,
                `" . TblModCatalogProp . "`.best,
                `" . TblModCatalogProp . "`.new,
                `" . TblModCatalogProp . "`.shareprop,
                `" . TblModCatalogProp . "`.id_cat,
                `" . TblModCatalogProp . "`.price,
                `" . TblModCatalogProp . "`.opt_price,
                `" . TblModCatalogProp . "`.price_currency,
                `" . TblModCatalogProp . "`.opt_price_currency,
                `" . TblModCatalogProp . "`.oldprice,
                `" . TblModCatalogPropSprName . "`.name,
                `" . TblModCatalogSprName . "`.name as cat_name,
                `" . TblModCatalogTranslit . "`.`translit`,
                `" . TblModCatalogPropSprShort . "`.`name` AS `short`,
                `" . TblModCatalogPropImg . "`.`path` AS `first_img`,
                `" . TblModCatalogPropImgTxt . "`.`name` AS `first_img_alt`,
                `".TblModPropGroupPrice."`.`price` as group_price,
                `".TblModPropGroupPrice."`.`group_currency` as currency_group_price,
                `" . TblModCatalogPropImgTxt . "`.`text` AS `first_img_title`
              FROM `" . TblModCatalogProp . "`
                LEFT JOIN `" . TblModCatalogPropImg . "` ON (`" . TblModCatalogProp . "`.`id`=`" . TblModCatalogPropImg . "`.`id_prop` AND `" . TblModCatalogPropImg . "`.`id`= (
                    SELECT
                    `" . TblModCatalogPropImg . "`.`id`
                    FROM `" . TblModCatalogPropImg . "`
                    WHERE
                    `" . TblModCatalogPropImg . "`.`id_prop`=`" . TblModCatalogProp . "`.id
                    AND `" . TblModCatalogPropImg . "`.`show`='1'
                    ORDER BY `" . TblModCatalogPropImg . "`.`move` asc LIMIT 1
                    ) )
                LEFT JOIN `" . TblModCatalogPropImgTxt . "` ON (`" . TblModCatalogPropImg . "`.`id`=`" . TblModCatalogPropImgTxt . "`.`cod` AND `" . TblModCatalogPropImgTxt . "`.lang_id='" . $this->lang_id . "'),
                `" . TblModCatalogPropSprName . "`,`" . TblModCatalogSprName . "`, `" . TblModCatalog . "`, `" . TblModCatalogTranslit . "`, `" . TblModCatalogPropSprShort . "`, `".TblModPropGroupPrice."`
              WHERE `" . TblModCatalogProp . "`.`id_cat`=`" . TblModCatalog . "`.`id`
              AND `" . TblModCatalogProp . "`.visible='2'
              AND
                `" . TblModCatalogProp . "`.id  =`".TblModPropGroupPrice."`.prod_id
              AND
                `".TblModPropGroupPrice."`.group_id = ".$this->group_user_id."
              AND `" . TblModCatalog . "`.`visible`='2'
              AND `" . TblModCatalogProp . "`.id=`" . TblModCatalogPropSprName . "`.cod
              AND `" . TblModCatalogProp . "`.id_cat=`" . TblModCatalogSprName . "`.cod
              AND `" . TblModCatalogPropSprName . "`.lang_id='" . $this->lang_id . "'
              AND `" . TblModCatalogSprName . "`.lang_id='" . $this->lang_id . "'
              AND `" . TblModCatalogProp . "`.id=`" . TblModCatalogTranslit . "`.`id_prop`
              AND `" . TblModCatalogTranslit . "`.`lang_id`='" . $this->lang_id . "'
              AND `" . TblModCatalogPropSprShort . "`.`lang_id`='" . $this->lang_id . "'
              AND `" . TblModCatalogPropSprShort . "`.`cod`= `" . TblModCatalogProp . "`.id
             ";


        $q = $q . " AND " . $ftl;

        $q = $q . " GROUP BY `" . TblModCatalogProp . "`.id ";
        if ($order){
            $q = $q.$order;
        }
        if ($limit) $q = $q . " limit " . $limit;
        //echo $q;

        $res = $db->db_Query($q);

        if (!$res)
            return false;
        $rows =$db->db_GetNumRows();
        if ($rows == 0) {

        } else {


            for ($i = 0; $i < $rows; $i++) {


                $row = $db->db_FetchAssoc();

                $opt_price = "";
                $price = "";

                $arrSetProp = $this->getSetProp($row['id']);

                $name = stripslashes($row['name']);
                $img = stripslashes($row['first_img']);
                $alt = stripcslashes($row['first_img_alt']);
                $title = stripcslashes($row['first_img_title']);
//                $short = strip_tags(stripcslashes($row['short']));
                $short = $this->Spr->GetNameByCod(TblModCatalogPropSprReviews, $row['id']);
                if (empty($alt)) $alt = "Фото - ".$name;
                if (empty($title)) $title = $name." от компании «Ohrana.ua»";



//                $link = $this->getUrlByTranslit($this->treeCatData[$row['id_cat']]['path'], $row['translit']);

                $link = $this->Link($row['id_cat'], $row['id']);
                ?>
                <div class="prod">
                <a class="prod-name" href="<?= $link; ?>"><?= $name ?></a>


                <? if (isset($img) AND !empty($img)) { ?>
                    <?$top=60;?>
                    <? $imgSmall = ImageK::getResizedImg($this->getPictureRelPath($row['id'], $img), 'size_auto=150', 85, NULL); ?>
                    <a class="img-prod-link" href="<?= $link; ?>">

                        <?php if($row['best']):
                            ?> <div class="hit-prop" style="top:<?=$top?>px"></div><?
                            $top= $top+18;
                            ?>

                        <?endif;?>
                        <?php if($row['new']):

                            ?>  <div class="new-prop" style="top:<?=$top?>px"></div><?
                            $top= $top+18;
                            ?>


                        <?endif;?>
                        <?php if($row['shareprop']):?>
                            <div class="shareprop-prop" style="top:<?=$top?>px"></div>
                        <?endif;?>
                        <img src="<?= $imgSmall; ?>"/>

                    </a>
                <?
                } else {
                    ?><a href="<?= $link; ?>" alt="<?= $name; ?>" title="<?= $this->multi['TXT_NO_IMAGE'] ?>"><img
                            src="/images/design/no-image.gif"/></a><?
                }
                ?>

                <?if(!$informer):?>
                    <div class="short-prop">
                        <?= $short; ?>
                    </div>
                <?endif;?>



                <?if ($row['group_price'] != 0) {
                    $cur_from = $row['currency_group_price'];
                    if ($cur_from == 0)
                        $cur_from = $this->def_currency;
                    $group_price = $this->Currency->Converting($cur_from, _CURR_ID, stripslashes($row['group_price']), 0);

                    /*
                    if($cur_from==1 AND $this->group_user_id==5) {
                        $group_price = round($group_price);
                    }
                    */

                    $group_price = $this->Currency->ShowPrice($group_price);

                }?>

                <div class="prop-btm">
                    <?php  if ($row['group_price'] != 0) :?>
                    <div>
                        <div class="price-prod">
                            <span><?=$group_price?></span>
                        </div>
                        <?php if($this->group_user_id==5 && $row['shareprop']==1 && isset($row['oldprice']) && !empty($row['oldprice']) ) :?>
                            <div class="old-share-price"><?=$this->Currency->ShowPrice($row['oldprice']);?></div>
                        <?php endif;?>
                    </div>
                        <div>
                            <?
                            if($arrSetProp){
                                $listProdSet = implode(',', $arrSetProp);
                                $countList = implode(",", array_keys($arrSetProp) ) ;
                                ?>
                                <form action="#" method="post" name="catalog" id="catalog<?= $row['id'] ?>">
                                    <div class="quantityField">
                                        <input type="hidden" size="2" value="1" class="quantity" onkeypress="return me()"
                                               id="productId[<?= $row['id'] ?>]" name="productId[<?= $row['id'] ?>]" maxlength="2"
                                            />
                                    </div>

                                    <div class="buybutton">
                                        <a href="#" id="multiAdds<?= $row['id'] ?>" onclick="addToCartSet('catalog<?= $row['id'] ?>', 'cart', '<?= $row['id'] ?>', '<?=$countList?>', '<?=$listProdSet?>');return false;"><span>Купить</span></a>
                                    </div>
                                </form>
                            <?
                            }else{
                                ?>
                                <form action="#" method="post" name="catalog" id="catalog<?= $row['id'] ?>">
                                    <div class="quantityField">
                                        <input type="hidden" size="2" value="1" class="quantity" onkeypress="return me()"
                                               id="productId[<?= $row['id'] ?>]" name="productId[<?= $row['id'] ?>]" maxlength="2"
                                            />
                                    </div>

                                    <div class="buybutton">
                                        <a href="#" id="multiAdds<?= $row['id'] ?>" onclick="addToCart('catalog<?= $row['id'] ?>', 'cart', '<?= $row['id'] ?>');return false;"><span>Купить</span></a>
                                    </div>
                                </form>
                            <?
                            }
                            ?>
                        </div>
                    <?php endif;?>
                <?if(!$informer):?>
                        <div class="full-prod-link">
                            <div class="addToCart" id="al<?= $row['id']?>"></div> <a href="<?=$link?>"><?=$this->multi['TXT_DETAILS']?></a>
                        </div>
                <?else:?>
                    <div class="full-prod-link">
                        <div class="addToCart" id="al_<?= $row['id']?>"></div>
                    </div>
                <?endif;?>
                </div>
                </div>
               <?
            } //end foreach
        } //end if
        return ob_get_clean();
    } //end of function currProducts

    function cropStr($str, $size)
    {
        if( !empty($str) )
        return mb_substr($str, 0, mb_strrpos(mb_substr($str, 0, $size, 'utf-8'), ' ', 'utf-8'), 'utf-8') . "...";
    }


    function showTab()
    {

        $hit = $this->BestProducts(30, 2);
        $new = $this->BestProducts(30, 1);
        $share = $this->BestProducts(30,3);

        echo View::factory('/modules/mod_catalog/templates/tpl_main_tab.php')
            ->bind('new', $new)
            ->bind('hit', $hit)
            ->bind('share', $share)
            ->bind('multi', $this->multi);

    }

    // ================================================================================================
    // Function : ShowActionsProducts()
    // Version : 1.0.0
    // Date : 20.10.2009
    //
    // Programmer : Yaroslav Gyryn
    // Params :
    // Returns : $res / Void
    // Description : Shows best products
    // ================================================================================================
    function ShowActionsProducts($limit = null)
    {
        $q = "SELECT
                    `" . TblModCatalogProp . "`.id,
                    `" . TblModCatalogProp . "`.id_cat,
                    `" . TblModCatalogProp . "`.price,
                    `" . TblModCatalogProp . "`.price_currency,
                    `" . TblModCatalogProp . "`.opt_price,
                    `" . TblModCatalogProp . "`.opt_price_currency,
                    `" . TblModCatalogPropSprName . "`.name,
                    `" . TblModCatalogSprName . "`.name as category
                 FROM
                    `" . TblModCatalogProp . "`, `" . TblModCatalogPropSprName . "`, `" . TblModCatalogSprName . "`
                 WHERE
                    `" . TblModCatalogProp . "`.id = `" . TblModCatalogPropSprName . "`.cod
                 AND
                    `" . TblModCatalogPropSprName . "`.lang_id='" . $this->lang_id . "'
                 AND
                    `" . TblModCatalogProp . "`.id_cat = `" . TblModCatalogSprName . "`.cod
                 AND
                    `" . TblModCatalogSprName . "`.lang_id='" . $this->lang_id . "'
                 AND
                    `" . TblModCatalogProp . "`.visible ='2'
        ";

        $q = $q . " AND ABS(`" . TblModCatalogProp . "`.opt_price) >0 AND ABS(`" . TblModCatalogProp . "`.opt_price) > ABS(`" . TblModCatalogProp . "`.price)";
        $q = $q . " ORDER BY RAND()";
        if ($limit) $q = $q . " limit " . $limit;

        $res = $this->db->db_Query($q);
        //echo '<br> $q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;
        if (!$res) return false;
        $rows = $this->db->db_GetNumRows();
        //echo '<br>$rows='.$rows;
        //$Currency = check_init('SystemCurrencies', ''();
        $currentValuta = $this->Spr->GetNameByCod(TblSysCurrenciesSprSufix, _CURR_ID, $this->lang_id, 1);
        ?>
        <!--Begin: list1-->
        <div class="list1">
            <h2>
                <img src="/images/design/list1.png" alt="" title=""/>
            </h2>

            <div class="body">
                <?
                for ($i = 0; $i < $rows; $i++) {
                    $row = $this->db->db_FetchAssoc();
                    $name = stripslashes($row['name']);
                    $price = stripslashes($row['price']);
                    $old_price = stripslashes($row['opt_price']);
                    $link = $this->Link($row['id_cat'], $row['id']);
                    ?>
                    <form action="#" method="post" name="catalog" id="catalog<?= $row['id'] ?>">
                        <input type="hidden" name="productId[<?= $row['id'] ?>]" value="1"/>

                        <div class="item">
                            <div class="left_2">
                                <h3><?= $name; ?></h3>

                                <div class="text">
                                </div>
                                <div class="items">
                                    <div class="left_3">
                                        <div class="old_price">
                                            <?
                                            if (!empty($old_price)) {
                                                $cur_from = $row['price_currency'];
                                                if ($cur_from == 0) $cur_from = $this->def_currency;
                                                $old_price = $this->Currency->Converting($cur_from, _CURR_ID, $old_price, 2);
                                                echo $this->Currency->ShowPrice($old_price);
                                            }
                                            ?>
                                        </div>
                                        <div class="price">
                                            <?
                                            if (!empty($price)) {
                                                $cur_from = $row['opt_price_currency'];
                                                if ($cur_from == 0) $cur_from = $this->def_currency;
                                                $price = $this->Currency->Converting($cur_from, _CURR_ID, $price, 2);
                                                echo $this->Currency->ShowPrice($price);
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="right_3">
                                        <ul>
                                            <li>
                                                <a href="#"
                                                   onclick="addToCart('catalog<?= $row['id'] ?>', 'cart');return false;"
                                                   title="Р—Р°РєР°Р·Р°С‚СЊ"><img src="/images/design/zakaz.png"
                                                                                 alt="Р—Р°РєР°Р·Р°С‚СЊ"
                                                                                 title="Р—Р°РєР°Р·Р°С‚СЊ"/></a>
                                            </li>
                                            <li>
                                                <a href="<?= $link; ?>" title="РџРѕРґСЂРѕР±РЅРµРµ"><img
                                                        src="/images/design/all.png" alt="РџРѕРґСЂРѕР±РЅРµРµ"
                                                        title="РџРѕРґСЂРѕР±РЅРµРµ"/></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="right_2">
                                <table cellspacing="0" cellpadding="0">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <?
                                            $img = $this->GetFirstImgOfProp($row['id']);
                                            if ($img) echo $this->ShowCurrentImageSquare($img, true, 100, 85);
                                            else echo 'РќРµС‚ С„РѕС‚Рѕ';
                                            ?>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <? /*
                                <div class="action">
                                    <img class="png" src="/images/design/action.png" alt="" title="" />
                                </div>
                                */
                                ?>
                            </div>
                        </div>
                    </form>
                <?
                }
                ?>
            </div>
        </div>
        <!--End: list1-->
    <?
    }
    //end of function ShowActionsProducts


} // end of class CatalogLayout
?>
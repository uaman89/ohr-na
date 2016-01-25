<?php
/**
 * pagesLayout.class.php
 * class for display interface of Dynamic Front-end Pages
 * @package Dynamic Pages Package of SEOCMS
 * @author Igor Trokhymchuk  <ihor@seotm.com>
 * @version 1.1, 05.08.2011
 * @copyright (c) 2010+ by SEOTM
 */
include_once(SITE_PATH . '/modules/mod_pages/pages.defines.php');

/**
 * Class FrontendPages
 * class for display interface of Dynamic Front-end Pages.
 * @author Igor Trokhymchuk  <ihor@seotm.com>
 * @version 1.1, 05.08.2011
 * @property CatalogLayout $Catalog
 * @property FrontSpr $Spr
 * @property FrontForm $Form
 * @property db $db
 * @property UploadImage $UploadImages
 * @property UploadClass $UploadFile
 * @property PageUser $PageUser
 */
class FrontendPages extends DynamicPages {

    public $page = NULL;
    public $module = NULL;
    public $is_tags = NULL;
    public $is_comments = NULL;
    public $main_page = NULL;
    public $mod_rewrite = 1;
    public $Spr = NULL;
    public $Form = NULL;
    public $db = NULL;
    public $PageUser = NULL;
    public $task = NULL;

    /**
     * Class Constructor
     *
     * @param $module - id of the module
     * @return true/false
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.1, 05.04.2011
     */
    function __construct($module = NULL) {
        ($module != "" ? $this->module = $module : $this->module = NULL);

        if (defined("_LANG_ID"))
            $this->lang_id = _LANG_ID;

        if (empty($this->db))
            $this->db = DBs::getInstance();
        if (empty($this->Spr))
            $this->Spr = check_init('FrontSpr', 'FrontSpr');
        if (empty($this->Form))
            $this->Form = check_init('FrontForm', 'FrontForm');
        if (empty($this->Msg))
            $this->Msg = check_init('ShowMsg', 'ShowMsg');

        $this->UploadFile = check_init('UploadClass', 'UploadClass', "'90', 'null', 'uploads/files/pages','".TblModUploadFiles."'");
        $this->UploadImages = check_init('UploadImage', 'UploadImage', "'90', 'null', 'uploads/images/pages', '".TblModUploadImg."'");
        //$this->UploadVideo = check_init('UploadVideo', 'UploadVideo', "'90', 'null', 'uploads/video/pages','".TblModUploadImg."'");
        // for folders links
        if (!isset($this->mod_rewrite) OR empty($this->mod_rewrite))
            $this->mod_rewrite = 1;

        (defined("USE_TAGS") ? $this->is_tags = USE_TAGS : $this->is_tags = 0); // использовать тэги
        (defined("USE_COMMENTS") ? $this->is_comments = USE_COMMENTS : $this->is_comments = 0); // возможность оставлять комментарии
        (defined("PAGES_USE_SHORT_DESCR") ? $this->is_short_descr = PAGES_USE_SHORT_DESCR : $this->is_short_descr = 0); // Краткое оисание страницы
        (defined("PAGES_USE_SPECIAL_POS") ? $this->is_special_pos = PAGES_USE_SPECIAL_POS : $this->is_special_pos = 0); // специальное размещение страницы
        (defined("PAGES_USE_IMAGE") ? $this->is_image = PAGES_USE_IMAGE : $this->is_image = 0); // изображение к странице
        (defined("PAGES_USE_IS_MAIN") ? $this->is_main_page = PAGES_USE_IS_MAIN : $this->is_main_page = 0); // главная страница сайта

        if (empty($this->multi))
            $this->multi = check_init_txt('TblFrontMulti', TblFrontMulti);

        $this->loadTree();
        $this->main_page = $this->MainPage();
        //echo '<br />treePageList=';print_r($this->treePageList);
        //echo '<br />treePageLevels=';print_r($this->treePageLevels);
        //echo '<br />treePageData=';print_r($this->treePageData);
        $this->loadSpecContent();
    }

// end of constructor FrontendPages()

    /**
     * Class method Link
     * build reletive|absolute URL link to page $id
     * @param integer $id - id of the page
     * @param boolean $add_domen_name If true then add domen name before page url (like http://www.seotm.com/news/)
     * @param string $lang id of the lang for build link
     * @return string $link - link to page
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.0, 12.04.2012
     */
    function Link($id, $add_domen_name = true, $lang = NULL) {
        $link = NULL;
        if (!empty($lang)) {
            //$Lang = new SysLang(NULL, "front");
            $Lang = check_init('SysLang', 'SysLang', 'NULL, "front"');
            $tmp_lang = $Lang->GetDefFrontLangID();
            if (($Lang->GetCountLang('front') > 1 OR isset($_GET['lang_st'])) AND $lang != $tmp_lang)
                $lang_prefix = "/" . $Lang->GetLangShortName($lang) . "/";
            else
                $lang_prefix = "/";
        } else {
            if (!defined("_LINK")) {
                //define("_LINK", "/");
                //$Lang = new SysLang(NULL, "front");
                $Lang = check_init('SysLang', 'SysLang', 'NULL, "front"');
                $tmp_lang = $Lang->GetDefFrontLangID();
                if (($Lang->GetCountLang('front') > 1 OR isset($_GET['lang_st'])) AND _LANG_ID != $tmp_lang) {
                    define("_LINK", "/" . $Lang->GetLangShortName(_LANG_ID) . "/");
                    $lang_prefix = "/" . $Lang->GetLangShortName(_LANG_ID) . "/";
                } else {
                    define("_LINK", "/");
                    $lang_prefix = "/";
                }
            } else
                $lang_prefix = _LINK;
        }


        //echo '<br>$this->mod_rewrite='.$this->mod_rewrite.' $lang_prefix='.$lang_prefix;
        if ($this->mod_rewrite == 1) {
            //$link = $this->GetNameById($id);
            $link = $this->treePageData[$id]['path'];
            //echo '<br>$link='.$link;

            if (!empty($link)) {
                //echo '<br>_LINK='._LINK.' strlen($lang_prefix)='.strlen($lang_prefix);
                if ($this->treePageData[$id]['ctrlscript'] == 1) {
                    //echo '<br>$lang_prefix='.$lang_prefix.' $link='.$link;
                    if ($add_domen_name)
                        $link = 'http://' . $_SERVER['SERVER_NAME'] . $lang_prefix . $link;
                    else
                        $link = $lang_prefix . $link;
                    //echo '<br>$link='.$link;
                } else {
                    //echo '<br>222';
                    //if page is not dynamic page and this is not link to the page of other site then show path to this site
                    if (!strstr($link, "http://")) {
                        $pos = strpos($link, '/');
                        if ($pos === 0)
                            $link = substr($link, 1);
                        if ($add_domen_name)
                            $link = 'http://' . $_SERVER['SERVER_NAME'] . $lang_prefix . $link;
                        else
                            $link = $lang_prefix . $link;
                    } else {
                        if ($this->is_main_page) {
                            if ($this->main_page == $id)
                                $link = $link . $lang_prefix;
                        }
                    }
                }
            }
            $link = $this->PrepareLink($link);
        }
        if (empty($link)) {
            if ($this->main_page == $id)
                $link = $lang_prefix;
            else
                $link = $lang_prefix . "index.php?page=" . $id;
        }
        //echo '<br>$link='.$link;
        return $link;
    }

//end of function Link()

    /**
     * Class method ShowPath
     * eturn path of names to the page
     * @param string $id_page - id of the page
     * @param string $path string with path for recursive execute
     * @param boolean $make_link - make link for last page in path or not
     * @param bool $is_last_element - if its last element in breadcrumbs, then dont wrap it in microdata
     * @return string path of names to the page
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.0, 05.04.2012
     */
    function ShowPath($id_page, $path = NULL, $make_link = false, $is_last_element = true) {
        $res = NULL;
        $devider = '&nbsp;&nbsp;/&nbsp;&nbsp;';
        $use_micro_data = ($is_last_element) ? false : true;

        $main_page_link = $this->getMicroFormPathItem( $this->multi['TXT_FRONT_HOME_PAGE'], _LINK, false);

        if ($id_page > 0) {
            $row = $this->treePageData[$id_page];
            $name = stripslashes($row['pname']);
            $link = $this->Link($row['id']);

            if (!empty($path)) {
                $path = $this->getMicroFormPathItem( $name, $link ) . $devider . ' ' . $path;
            } else {
                if ($make_link == 1) {
                    $path = $this->getMicroFormPathItem( $name, $link, $use_micro_data );
                } else
                    $path = $name;
            }

            if ($row['level'] > 0) {
                $path = $this->ShowPath($row['level'], $path, $make_link);
            } else
                $path = $main_page_link . $devider . $this->getMicroFormPathItem( $name, null, false );
        } else {
            $path = $main_page_link . $devider . ' ';
        }
        return $path;
    }

    //end of function ShowPath()

    static function getMicroFormPathItem( $title, $link = NULL, $useMicroData = true ){
        $str = '';
        if ( $useMicroData ){
            $str = '<div class="breadcrumb micro-path" itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb">';
        }

        if (!empty($link))
            $str .= '<a itemprop="url" href="'.$link.'">';

        $str .= '<span itemprop="title">'.$title.'</span>';

        if (!empty($link))
            $str .= '</a>';

        if ( $useMicroData )
            $str .= '</div>';

        return $str;
    }

    /**
     * Class method ShowHorisontalMenu
     * Shiow horizontal menu of site
     * @param integer $level - id of the page level
     * @param integer $cnt_sublevels count of sublevels
     * @param boolean $make_link - make link for last page in path or not
     * @return string path of names to the page
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.0, 05.04.2012
     */
    function ShowHorisontalMenu($level = 0, $cnt_sublevels = 10, $cnt = 0) {

        if (!isset($this->treePageLevels[$level]))
            return false;
        $rows = count($this->treePageLevels[$level]);
        $keys = array_keys($this->treePageLevels[$level]);
        if ($rows == 0)
            return false;
        if ($level == 0) {
            ?><ul class="horizontal-menu"><?
        } else {
            ?><ul><?
        }
        for ($i = 0; $i < $rows; $i++) {
            // $row = $arr_data[$i];
            $row = $this->treePageData[$keys[$i]];
            if ($row['visible'] == 0 OR empty($row['pname']))
                continue;
            $href = $this->Link($row['id']);



            $s = "";
            if ($this->page == $row['id'])
                $s = "current";
            $name = stripslashes($row['pname']);
            //echo '<br>$name='.$name.' $row[id]='.$row['id'];
            ?>
            <li>
                <a href="<?= $href; ?>" class="<?= $s ?>"><?= $name; ?></a>
                <?
                //echo '<br>$cnt_sublevels='.$cnt_sublevels.' $cnt='.$cnt;
                if (PAGE_CATALOG == $row['id']) {
                    $this->Catalog = check_init('CatalogLayout', 'CatalogLayout');
                    $this->Catalog->main_top_level = 0;
                    //$this->Catalog->showTreeAll();
                } elseif ($this->isSubLevels($row['id'], 'front')) {
                    $this->ShowHorisontalMenu($row['id'], $cnt_sublevels, $cnt);
                }
                ?>
            </li>
            <?
        }

        ?></ul><?
    }

    // end of function ShowHorisontalMenu()

    /**
     *  FrontendPages::ShowVerticalMenu()
     * @return true,false / Void
     * @author Ihor Trokhymchuk 21.02.2008
     * @author Sergey Panarin 05.01.2012
     */
    function ShowVerticalMenu($level = 0, $cnt_sublevels = 99, $cnt = 0) {
        if (!isset($this->treePageLevels[$level]))
            return false;
        $rows = count($this->treePageLevels[$level]);
        $keys = array_keys($this->treePageLevels[$level]);
        if ($rows == 0)
            return false;
                ?>
            <ul><?
            for ($i = 0; $i < $rows; $i++) {
                //$row = $arr_data[$i];
                $row = $this->treePageData[$keys[$i]];
                if ($row['visible'] == 0 OR empty($row['pname']))
                    continue;
                if ($this->main_page == $row['id'])
                    $href = "/";
                else
                    $href = $this->Link($row['id']);
                if ($this->page == $row['id']) {
                    $s = "item";
                } else {
                    $s = "general";
                }
                $name = stripslashes($row['pname']);
                //echo '<br>$name='.$name.' $row[id]='.$row['id'];
                    ?>
                    <li><?
                    ?><a href="<?= $href; ?>" class="<?= $s; ?>"><?= $name; ?></a><br/><?
                    ?></li><?
            //echo '<br>$cnt_sublevels='.$cnt_sublevels.' $cnt='.$cnt;
            if ($this->isSubLevels($row['id'], 'front')) {
                $cnt = $cnt + 1;

                if ($cnt < $cnt_sublevels) {
                            ?>
                            <ul>
                        <? $this->ShowVerticalMenu($row['id'], $cnt_sublevels, $cnt); ?>
                            </ul>
                        <?
                        $cnt = 0;
                    }
                }
            }
            ?></ul><?
        }

        // end of function ShowVerticalMenu()

        /**
         * FrontendPages::ShowFooterMenu()
         * @author Yaroslav Gyryn 21.10.2011
         * @return void
         */
        function ShowFooterMenu($level = 0) {
            if (!isset($this->treePageLevels[$level]))
                return false;
            $rows = count($this->treePageLevels[$level]);
            $keys = array_keys($this->treePageLevels[$level]);
            if ($rows == 0)
                return false;
            ?>
            <div id="footerNavBox">
                <ul>
            <?
            for ($i = 0; $i < $rows; $i++) {
                $row = $this->treePageData[$keys[$i]];
                if ($row['visible'] == 0 OR empty($row['pname']))
                    continue;
                if ($this->main_page == $row['id'])
                    $href = _LINK;
                else
                    $href = $this->Link($row['id']);
                ?>
                        <li><a <?
            if ($this->page == $row['id']) {
                echo ' class="current"';
            }
                ?> href="<?= $href; ?>"><?= stripslashes($row['pname']); ?></a>
                        </li>
                <?
            }// end for
            ?>
                </ul>
            </div>
            <?
        }

        //end of function ShowFooterMenu()

        /**
         * Class method ShowContent
         * show content of the dynamic page
         * @return content of the page
         * @author Igor Trokhymchuk  <ihor@seotm.com>
         * @version 1.0, 12.04.2012
         */
        function ShowContent() {
            if(isset($this->page_txt['h1']) AND !empty($this->page_txt['h1'])){
                $h1 = stripslashes($this->page_txt['h1']);
            }else{
                $h1 = stripslashes($this->page_txt['pname']);
            }
            if ($this->page != $this->main_page)
                $this->PageUser->breadcrumb = $this->ShowPath($this->page);

            if ($this->page != $this->main_page)
                $this->PageUser->h1 = $h1;
            else
                $this->PageUser->h1 = '';

            //$this->PageUser->title = '';

            //image gallery
            $images = $this->ShowUploadImagesList($this->page);
            //file gallery
            $files = $this->ShowUploadFileList($this->page);

            //comments
            ob_start();
            if ($this->is_comments == 1) {
                //$this->Comments = new CommentsLayout($this->module, $this->page);
               // $this->Comments->ShowComments();
            }
            $comments = ob_get_clean();

            //tags
            ob_start();
            if ($this->is_tags == 1) {
                $Tags = new FrontTags();
                if (count($Tags->GetSimilarItems($this->module, $this->page)) > 0) {
                    $Tags->ShowSimilarItems($this->module, $this->page);
                }
            }
            $tags = ob_get_clean();

            ob_start();
            if($this->task=='map'){
                $this->MAP();
            }
            $sitemap = ob_get_clean();

            //page content
            $sublevels = '';
            if ($this->treePageData[$this->page]['publish'] != 1 AND !$this->preview) {
                $content = $this->multi['_MSG_CONTENT_NOT_PUBLISH'];
            }else{
                $content = stripslashes($this->page_txt['content']);
                if (empty($content) AND empty($images) AND empty($files) AND empty($comments) AND empty($tags) AND empty($sitemap) AND $this->ShowSubLevelsInContent($this->page) == false) {
                    $content = $this->multi['_MSG_CONTENT_EMPTY'];
                }
                $sublevels = $this->ShowSubLevelsInContent($this->page);
            }

            echo View::factory('/modules/mod_pages/tpl_pages_page/tpl_pages_page.php')
                    ->bind('sublevels', $sublevels)
                    ->bind('images', $images)
                    ->bind('files', $files)
                    ->bind('comments', $comments)
                    ->bind('tags', $tags)
                    ->bind('multi', $this->multi)
                    ->bind('content', $content)
                    ->bind('sitemap', $sitemap);

            if($this->page==103){
                ?><div style="display: none" id="mform"><?
                $FeedbackLayout = check_init('FeedbackLayout', 'FeedbackLayout');
                $FeedbackLayout->show_form_montazh();

                ?></div>


            <?
                $this->showSlidePartner();
            }
        }

        // end of function ShowContent

        /**
         * Class method ShowSubLevelsInContent
         * show sublevels of the page $level in content part
         * @param integer $level - id of the page
         * @return sublevels of this page
         * @author Igor Trokhymchuk  <ihor@seotm.com>
         * @version 1.0, 12.04.2012
         */
        function ShowSubLevelsInContent($level) {
            if (!isset($this->treePageLevels[$level]))
                return false;
            $rows = count($this->treePageLevels[$level]);
            $keys = array_keys($this->treePageLevels[$level]);
            if ($rows == 0)
                return false;
            $arr = array();
            for ($i = 0; $i < $rows; $i++) {
                $row = $this->treePageData[$keys[$i]];

                if ($row['visible'] == 0 OR empty($row['pname']))
                    continue;
                $row['link'] = $this->Link($row['id']);
                $arr[$i] = $row;
            }
            return View::factory('/modules/mod_pages/tpl_pages_page/tpl_sublevels_pages.php')
                            ->bind('arr', $arr);
        }

        // end of function ShowSubLevelsInContent()

        /**
         * FrontendPages::MAP()
         * Show map of dynamic pages
         * @author Yaroslav
         * @param integer $level
         * @return
         */
        function MAP($level = 0) {
            if (!isset($this->treePageLevels[$level]))
                return false;
            $rows = count($this->treePageLevels[$level]);
            if ($rows == 0)
                return false;
            $keys = array_keys($this->treePageLevels[$level]);
            ?>
            <ul><?
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->treePageData[$keys[$i]];
            if ($row['visible'] == 0 OR empty($row['pname']))
                continue;
            $id = $row['id'];
            $name = $row['pname'];
            if ($this->MainPage() == $id)
                $href = "/";
            else
                $href = $this->Link($id);
                ?>
                    <li><a href="<?= $href; ?>"><?= $name; ?></a></li><?
            $this->MAP($id);

        } //end for
            if($level==0){
                if (defined("MOD_NEWS") AND MOD_NEWS ) { //News
                    $News = check_init('NewsLayout', 'NewsLayout');
                    $News->showModuleSiteMap();
                }

                if (defined("MOD_ARTICLE") AND MOD_ARTICLE ) { //Articles
                    $Article = check_init('ArticleLayout', 'ArticleLayout');
                    $Article->showModuleSiteMap();
                }

                if (defined("MOD_CATALOG") AND MOD_CATALOG) { //Catalog
                    if (!isset($this->Catalog))
                        $this->Catalog = check_init('CatalogLayout', 'CatalogLayout');
                    $this->Catalog->MAP();
                }

                if (defined("MOD_GALLERY") AND MOD_GALLERY) { //Gallery
                    $Gallery = check_init('GalleryLayout', 'GalleryLayout');
                    $Gallery->showModuleSiteMap();
                }

                if (defined("MOD_VIDEO") AND MOD_VIDEO) { //Video
                    $Video = check_init('VideoLayout', 'VideoLayout');
                    $Video->showModuleSiteMap();
                }

                if (defined("MOD_DICTINARY") AND MOD_DICTINARY) { //Dictionary
                    if (!isset($this->Dictionary))
                        $this->Dictionary = check_init('Dictionary', 'Dictionary');
                    $this->Dictionary->MAP();
                }

                if (defined("PAGE_COMMENT") AND PAGE_COMMENT) { //Комментарий
                    if (!isset($this->Comments)){
      //  $this->Comments = check_init('CommentsLayout', 'CommentsLayout');
}
                
                    //$this->Comments->GetMap();
                }
            }
            ?></ul><?
    }

    // end of function MAP()
    // ================================================================================================
    // Function : GetTitle()
    // Date : 18.08.2006
    // Returns : true,false / Void
    // Description :  return titleiption of the page
    // Programmer : Ihor Trohymchuk
    // ================================================================================================
    function GetTitle() {
        if (empty($this->page_txt['mtitle']))
            return stripslashes($this->page_txt['pname']);
        else
            return stripslashes($this->page_txt['mtitle']);
    }

//end of function GetTitle()
    // ================================================================================================
    // Function : GetDescription()
    // Date : 18.08.2006
    // Returns : true,false / Void
    // Description :  return description of the page
    // Programmer : Ihor Trohymchuk
    // ================================================================================================
    function GetDescription() {
        return stripslashes($this->page_txt['mdescr']);
    }

//end of function GetDescription()
    // ================================================================================================
    // Function : GetKeywords()
    // Date : 18.08.2006
    // Returns : true,false / Void
    // Description :  return kyewords of the page
    // Programmer : Ihor Trohymchuk
    // ================================================================================================
    function GetKeywords() {
        return stripslashes($this->page_txt['mkeywords']);
    }

//end of function GetKeywords()
    // ================================================================================================
    // Function : ShowSearchRes()
    // Date : 31.03.2008
    // Returns : true,false / Void
    // Description : Show Add form on fontend
    // Programmer : Ihor Trohymchuk
    // ================================================================================================
    function ShowSearchRes($arr_res) {
        $rows = count($arr_res);
        if ($rows > 0) {
                ?>
                <ul><?
            for ($i = 0; $i < $rows; $i++) {
                $row = $arr_res[$i];
                    ?>
                        <li><a href=<?= $this->Link($row['id']); ?> class="map"><?= stripslashes($row['pname']); ?></a></li>
                    <?
                }
                ?></ul><?
        } else {
            echo $this->Msg->show_text('SEARCH_NO_RES');
        }
    }

// end of function ShowSearchRes()
    // ================================================================================================
    // Function : ShowSearchResHead()
    // Date : 31.03.2008
    // Returns : true,false / Void
    // Description : Show Add form on fontend
    // Programmer : Ihor Trohymchuk
    // ================================================================================================
    function ShowSearchResHead($str) {
            ?>
            <div><?= $str; ?></div>
            <?
        }

// end of function ShowSearchResHead()
        // ================================================================================================
        // Function : UploadFileList()
        // Date : 30.05.2010
        // Parms : $pageId - id of the page
        // Returns : true,false / Void
        // Description : Show list of files attached to page with $pageId
        // Programmer : Yaroslav Gyryn
        // ================================================================================================
        function ShowUploadFileList($pageId) {
            $array = $this->UploadFile->GetListOfFilesFrontend($pageId, $this->lang_id);
            if (count($array) > 0) {
                ob_start();
                ?><div class="leftBlockHead"><?= $this->multi['_TXT_FILES_TO_PAGE'] ?>:</div><?
                $this->UploadFile->ShowListOfFilesFrontend($array, $pageId);
                return ob_get_clean();
            }else
                return '';
    }

    // ================================================================================================
    // Function : ShowUploadImageList()
    // Date : 30.05.2010
    // Parms : $pageId - id of the page
    // Returns : true,false / Void
    // Description : Show Upload Images List
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function ShowUploadImagesList($pageId) {
        $items = $this->UploadImages->GetPictureInArrayExSize($pageId, $this->lang_id, NULL, 175, 135, true, true, 85);
        $items_keys = array_keys($items);
        $items_count = count($items);
        if ($items_count > 0) {
            return View::factory('/modules/mod_pages/tpl_pages_page/tpl_pages_images.php')
                            ->bind('items', $items)
                            ->bind('items_count', $items_count)
                            ->bind('multi', $this->multi)
                            ->bind('lang_id', $this->lang_id)
                            ->bind('items_keys', $items_keys)
            ;
        } else {
            return '';
        }

        //$this->UploadImages->ShowMainPicture($pageId,$this->lang_id,'size_width=175 ', 85 ) ;
    }

    // ================================================================================================
    // Function : ShowRandomImage()
    // Date : 30.09.2010
    // Parms : $pageId - id of the page
    // Returns: void
    // Description :  Show Random Image
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function ShowRandomImage($pageId) {
        $page_txt = $this->GetPageData($pageId, $lang_id = NULL);
        $name = stripslashes($page_txt['pname']);
            ?>
            <div class="leftMenuHead">
                <h3><?= $name ?></h3>
            </div>
            <div class="image-block">
                    <?
                    $link = $this->Link($pageId);
                    $items = $this->UploadImages->GetFirstRandomPicture($pageId, $this->lang_id, 'size_width= 232', null);
                    $items_keys = array_keys($items);
                    $items_count = count($items);
                    if ($items_count > 0) {
                        /* $alt= $items[$items_keys]['name'][$this->lang_id];  // Заголовок
                          $title= $items[$items_keys]['text'][$this->lang_id];  // Описание */
                        $path = $items[$items_keys[0]]['path']; // Путь уменьшенной копии
                        //$path_org = $items[$items_keys['path_original'];   // Путь оригинального изображения
                        ?><a href="<?= $link; ?>" title="<?= $name ?>" alt="<?= $name ?>"><img src="<?= $path; ?>" alt="<?= $name ?>"
                                                                                   title="<?= $name ?>"></a><?
        }
        /* ?>
          <a href="<?=$link?>" title="<?=$this->multi['TXT_GALLERY_TITLE'];?>"><img src="/images/design/videoSmall.jpg"></a> */
        ?>
            </div>
        <?
    }



     /**
    * DynamicPages::GetSliderData()
    * Get all materials in array
    * @return $arr
    */
   function GetSliderData($limit=0){
       $q = "SELECT
             `".TblModPagesSlider."`.`id`,
             `".TblModPagesSlider."`.`img`,
             `".TblModPagesSlider."`.`name`,
             `".TblModPagesSlider."`.`href`,
             `".TblModPagesSlider."`.`descr`
         FROM
             `".TblModPagesSlider."`
         WHERE
             `".TblModPagesSlider."`.`lang_id`='".$this->lang_id."'
             AND
             `".TblModPagesSlider."`.`img` <> ''
             AND
             `".TblModPagesSlider."`.`visible` = '1'
         ORDER BY `move`
         ";
       if($limit>0){
           $q .= " LIMIT ".$limit;
       }
       $res = $this->db->db_Query( $q );
       //echo "<br> ".$q." <br/> res = ".$res;
       $rows = $this->db->db_GetNumRows($res);
       $arr = array();
       for($i=0; $i<$rows; $i++){
           $row = $this->db->db_FetchAssoc($res);
           $arr[$i] = $row;
           $arr[$i]['rel_path_img'] = Spr_Img_Path_Small.'/'.TblModPagesSlider.'/'.$this->lang_id.'/'.$row['img'];
       }
       return $arr;
   }


  /* ShowSlider()
     * @author Yarolsav
     * Show slider
     * @return void
     */
    function ShowSlider($limit = 5){
        $array = $this->GetSliderData($limit);
        $count = count($array);
        if($count==0)
            return;
        echo View::factory('/modules/mod_pages/tpl_pages_page/tpl_slider.php')
                ->bind('Catalog', $this)
                ->bind('array', $array)
                ->bind('count', $count)
                ->bind('lang_id',$this->lang_id);

    }


      /* ShowSliderFlex()
     * @author Yarolsav
     * Show slider
     * @return void
     */
    function ShowSliderFlex($limit = 5){
        $array = $this->GetSliderData($limit);
        $count = count($array);
        if($count==0)
            return;
        echo View::factory('/modules/mod_pages/tpl_pages_page/tpl_slider_flex.php')
                ->bind('Catalog', $this)
                ->bind('array', $array)
                ->bind('count', $count)
                ->bind('lang_id',$this->lang_id);
    }

    /**
     * Class method ShowSliderWide
     * show wide slider
     * @return html slider
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.0, 10.10.2013
     */
    function ShowSliderWide(){

        $array = $this->GetSliderData();
        $count = count($array);
        if($count==0)
            return;
        echo View::factory('/modules/mod_pages/tpl_pages_page/tpl_slider_wide.php')
                ->bind('Pages', $this)
                ->bind('array', $array)
                ->bind('count', $count);
        ?>

        <?
    }

    function showSlidePartner(){
        $dataPartners = $this->getPartner(true);
        $count = count($dataPartners);

        if($count==0){
            return false;
        }

        echo View::factory('/modules/mod_pages/tpl_pages_page/tpl_partner_slide.php')
            ->bind('data', $dataPartners)
            ->bind('count', $count);


    }
  }
// end of class FrontendPages
?>
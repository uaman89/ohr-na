<?php
/**
 * PageUser.class.php
 * Class definition for all Pages - user actions
 * @package Package of SEOCMS
 * @author Igor Trokhymchuk  <ihor@seotm.com>
 * @version 1.0, 30.09.2011
 * @copyright (c) 2010+ by SEOTM
 */

include_once(SITE_PATH . '/include/defines.php');

/**
 * Class PageUser
 * Class definition for all Pages - user actions
 * @author Igor Trokhymchuk  <ihor@seotm.com>
 * @version 1.1, 30.09.2011
 * @property ShareLayout $Share
 * @property FrontendPages $FrontendPages
 * @property UserAuthorize $Logon
 * @property UserShow $UserShow
 * @property OrderLayout $Order
 * @property FrontSpr $Spr
 * @property FrontForm $Form
 * @property db $db
 * @property TblFrontMulti $multi
 * @property CatalogLayout $Catalog
 * @property SysLang $Lang
 * @property NewsLayout $News
 * @property ArticleLayout $Article
 */


class PageUser extends Page
{

    public $user_id = NULL;
    public $module = NULL;
    public $multi = NULL;

    public $DBPDO = NULL;
    public $db = NULL;
    public $Lang = NULL;
    public $Form = NULL;
    public $Spr = NULL;
    public $Settings = NULL;
    public $Share = NULL;
    public $FrontendPages = NULL;
    public $Logon = NULL;
    public $UserShow = NULL;
    public $Order = NULL;
    public $Catalog = NULL;
    public $News = NULL;
    public $Article = NULL;
    public $Gallery = NULL;
    public $content = '';
    public $h1 = '';
    public $breadcrumb = '';
    public $title = '';
    public $showContent2Box = 'content2-box';


    /**
     * Class Constructor
     * Set the variabels
     * @return true/false
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.0, 30.09.2011
     */
    function __construct()
    {
        if (defined("MAKE_DEBUG") AND MAKE_DEBUG == 1) {
            $this->time_start = $this->getmicrotime();
        }
        //======================== Define Language START =============================

        // if change language, then save it new value to COOKIE and to the session
        if(isset($_GET['lang_pg'])){
            setcookie('lang_pg', "", time() - 60 * 60 * 24 * 31, '/');
            setcookie('lang_pg', intval($_GET['lang_pg']), time() + 60 * 60 * 24 * 30, '/');
            //$_SESSION['lang_pg'] = $_GET['lang_pg'];
        }

        // if change language with using .htaccess, then save it new value to COOKIE and to the session
        if(isset($_GET['lang_st'])){
            $new_lang_id = SysLang::GetLangCodByShortName($_GET['lang_st']);
            setcookie('lang_pg', "", time() - 60 * 60 * 24 * 31, '/');
            setcookie('lang_pg', $new_lang_id, time() + 60 * 60 * 24 * 30, '/');
            //$_SESSION['lang_pg'] = $new_lang_id;
        }

        // if exist language in COOKIE and language is not set in session then set it in session
        //if( isset($_COOKIE['lang_pg']) AND !empty($_COOKIE['lang_pg'])  ) $_SESSION['lang_pg'] = $_COOKIE['lang_pg'];

        // if change language then set it in session
        if(isset($_GET['lang_pg']) AND !empty($_GET['lang_pg'])) $_SESSION['lang_pg'] = intval($_GET['lang_pg']);

        // if change language with using .htaccess then set it in session
        if(isset($_GET['lang_st']) AND !empty($_GET['lang_st'])) $_SESSION['lang_pg'] = $new_lang_id;

        // if language set in session then define this language for a site
        $tmp_lang = SysLang::GetDefFrontLangID();

        if(isset($_SESSION['lang_pg']) AND !empty($_SESSION['lang_pg'])) {
            if (!defined("_LANG_ID")){
                define("_LANG_ID", $_SESSION['lang_pg']);
            }
        } // if language not set in session, then get default language from database
        else {
            // if default language set in the database then define this language for a site
            if(!empty($tmp_lang)) {
                if(!defined("_LANG_ID")){
                    define("_LANG_ID", $tmp_lang);
                }
            } // if default language not set in the database then define constant DEBUG_LANG from script /include/defines.php for a site
            else{
                if(!defined("_LANG_ID")){
                    define("_LANG_ID", DEBUG_LANG);
                }
            }
        }

        if (defined("_LANG_ID")) {
            $this->SetLang(_LANG_ID);
            if((SysLang::GetCountLang('front') > 1 OR isset($_GET['lang_st'])) AND _LANG_ID != $tmp_lang){
                define("_LINK", "/" . SysLang::GetLangShortName(_LANG_ID) . "/");
            }else{
                define("_LINK", "/");
            }
        } else {
            define("_LINK", "/en/");
        }
        //======================== Define Language END =============================


        if(empty($this->Settings)) $this->Settings = SysSettings::GetGlobalSettings();
        //if site status Offline thne show message and go out.
        if(!$this->Settings['site_status']){
            echo $this->Settings['txt'][_LANG_ID]['site_off_message'];
            exit();
        }


        //======================== Define Currency START =============================
        // if change Currency, then save it new value to COOKIE and to the session
        if (isset($_GET['curr_ch'])) {

            setcookie('curr_ch', "", time() - 60 * 60 * 24 * 31, '/');
            setcookie('curr_ch', intval($_GET['curr_ch']), time() + 60 * 60 * 24 * 30, '/');
            if (!defined("_CURR_ID")) define("_CURR_ID", intval($_GET['curr_ch']));
        }

        if (isset($_POST['curr_ch'])) {

            setcookie('curr_ch', "", time() - 60 * 60 * 24 * 31, '/');
            setcookie('curr_ch', intval($_POST['curr_ch']), time() + 60 * 60 * 24 * 30, '/');
            if (!defined("_CURR_ID")) define("_CURR_ID", intval($_POST['curr_ch']));
        }

        //echo "<br>_COOKIE['curr_ch'] = ".$_COOKIE['curr_ch'];
        if (isset($_COOKIE['curr_ch']) AND !empty($_COOKIE['curr_ch'])) {
            if (!defined("_CURR_ID")) define("_CURR_ID", intval($_COOKIE['curr_ch']));
        } else {
            $this->Currency = new SystemCurrencies();
            $def_currency = $this->Currency->GetDefaultCurrency();

            // if default Currency set in the database then define this Currency for a site
            if (!empty($def_currency)) if (!defined("_CURR_ID")) define("_CURR_ID", $def_currency);
            // if default Currency not set in the database then define constant DEBUG_CURRENCY from script /include/defines.php for a site
            else {
                if (!defined("_CURR_ID")) define("_CURR_ID", DEBUG_CURR);
            }
        }
        //======================== Define Currency END =============================


        // for feedback httpreferer
        if (isset($_SERVER['HTTP_REFERER']) AND !strstr($_SERVER['REQUEST_URI'], 'favicon.ico')) {
            $pos = strpos($_SERVER['HTTP_REFERER'], 'http://' . $_SERVER['HTTP_HOST']);
            //echo '<br />$pos='.$pos;
            if ($pos !== 0) {
                setcookie('refpage', $_SERVER['HTTP_REFERER'], time() + 60 * 60 * 24 * 1, '/');
                //echo '<br />set cookie!';
            }
        }
        //for contol user serfing by pages of site
        //if( isset($_SERVER['REQUEST_URI']) AND !strstr($_SERVER['REQUEST_URI'], 'images/design') AND !strstr($_SERVER['REQUEST_URI'], 'favicon.ico') ){
        //    setcookie('serfing['.time().']', $_SERVER['REQUEST_URI'], time()+60*60*24*3, '/');
        //}


        //================= Display amount of pages for catalog START ========================
        if (isset($_GET['display'])) {
            //echo 'GET[display] = '.$_GET['display'];
            setcookie('display', "", time() - 60 * 60 * 24 * 31, '/');
            setcookie('display', intval($_GET['display']), time() + 60 * 60 * 24 * 30, '/');
            if (!defined("_DISPLAY")) define("_DISPLAY", intval($_GET['display']));
        }
        if (isset($_POST['display'])) {
            //echo 'POST[display] = '.$_POST['display'];
            setcookie('display', "", time() - 60 * 60 * 24 * 31, '/');
            setcookie('display', intval($_POST['display']), time() + 60 * 60 * 24 * 30, '/');
            if (!defined("_DISPLAY")) define("_DISPLAY", intval($_POST['display']));
        }
        if (isset($_COOKIE['display']) AND !empty($_COOKIE['display'])) {
            //echo 'COOKIE = '.$_COOKIE['display'];
            if (!defined("_DISPLAY")) define("_DISPLAY", intval($_COOKIE['display']));
        }
        //================= Display amount of pages for catalog END ========================


        //Считываем кол-во запровос к базе данных до старта сессии, так как после старта сессии в переменную $_SESSION['cnt_db_queries']
        //подтянуться старые значения. Их нужно обновить новыми данными. Для этого сохраним текущее сзначение во временную переменную $tmp_cnt_db_queries,
        //а после старта сессии присвоим это значение в переменную $_SESSION['cnt_db_queries'].
        if (isset($_SESSION['cnt_db_queries'])) $tmp_cnt_db_queries = intval($_SESSION['cnt_db_queries']);
        else $tmp_cnt_db_queries = 0;

        //if session not started then start new session
        if (!isset($_SESSION['session_id'])) {
            //echo '<br>1111';
            //Если в куки сохранена сессия, то уставаливаем ее как текущюю. Это необходимо
            //для подтягивания данных по сессии при закрытии и последующем открытии браузера.
            if (isset($_COOKIE[SEOCMS_SESSNAME])) {
                $sss = addslashes(strip_tags($_COOKIE[SEOCMS_SESSNAME]));
                //session_id($sss);
            }
            if (!headers_sent()){
                session_start();
                $lifetime = time()+LOGOUT_USER_TIME;
                if ( strpos($_SERVER['HTTP_USER_AGENT'], 'msie') ) //нужны комментарии???
                    setcookie(session_name(),session_id(),$lifetime);
                else{
                    $cook = setcookie(session_name(),session_id(),$lifetime,'/');
                    //echo '<br>$cook='.$cook;
                }
            }
        }

        //Устанавливаем кол-во завросов к базе данных, которое произошло да страта сессии.
        if (defined("MAKE_DEBUG") AND MAKE_DEBUG == 1) {
            $_SESSION['cnt_db_queries'] = $tmp_cnt_db_queries;
        }

        //set encoding of the site
        $this->page_encode = SysLang::GetDefLangEncoding($this->GetLang());

        //============ Init all objects START ============
        $this->DBPDO = DBPDO::getInstance();
        if(empty($this->db)) $this->db = DBs::getInstance();
        if(empty($this->Lang)) $this->Lang = check_init('SysLang', 'SysLang', _LANG_ID . ', "front"');
        if(empty($this->multi)) $this->multi = check_init_txt('TblFrontMulti', TblFrontMulti);
        if(empty($this->Form)) $this->Form = check_init('FrontForm', 'FrontForm');
        if(empty($this->Spr)) $this->Spr = check_init('FrontSpr', 'FrontSpr');

        if(defined("MOD_USER") AND MOD_USER AND empty($this->Logon)){
            $this->Logon = check_init('UserAuthorize', 'UserAuthorize');
        }

        if (defined("MOD_PAGES") AND MOD_PAGES AND empty($this->FrontendPages)){
            $this->FrontendPages = check_init('FrontendPages', 'FrontendPages');
        }

        if (defined("MOD_NEWS") AND MOD_NEWS AND empty($this->News)){
            $this->News = check_init('NewsLayout', 'NewsLayout');
        }

        if (defined("MOD_ARTICLE") AND MOD_ARTICLE AND empty($this->Article)){
            $this->Article = check_init('ArticleLayout', 'ArticleLayout');
        }

        if (defined("MOD_CATALOG") AND MOD_CATALOG AND empty($this->Catalog)){
            $this->Catalog = check_init('CatalogLayout', 'CatalogLayout');
        }

        if (defined("MOD_ORDER") AND MOD_ORDER AND empty($this->Order)){
            $this->Order = check_init('OrderLayout', 'OrderLayout');
            //echo '<br>$this->Order='.$this->Order;
        }

        if (defined("MOD_POLL") AND MOD_POLL AND empty($this->Poll)){
            $this->Poll = check_init('PollUse', 'PollUse');
        }

        if (defined("MOD_GALLERY") AND MOD_GALLERY AND empty($this->Gallery)){
            $this->Gallery = check_init('GalleryLayout', 'GalleryLayout');
        }

        if (defined("MOD_VIDEO") AND MOD_VIDEO AND empty($this->Video)){
            $this->Video = check_init('VideoLayout', 'VideoLayout');
        }

        if (defined("MOD_DICTINARY") AND MOD_DICTINARY AND empty($this->Dictionary)){
            $this->Dictionary = check_init('Dictionary', 'Dictionary');
        }

        if (defined("MOD_BANNER") AND MOD_BANNER AND empty($this->Banner)){
            $this->Banner = check_init('Banner', 'Banner');
        }

        if (defined("MOD_ASKED") AND MOD_ASKED AND empty($this->Asked)){
            $this->Asked = check_init('AskedLayout', 'AskedLayout', "NULL, NULL, NULL, null");
        }

        if (defined("MOD_SEARCH") AND MOD_SEARCH AND empty($this->Search)){
            $this->Search = check_init('Search', 'Search');
        }

        if (defined("MOD_PUBLIC") AND MOD_PUBLIC AND empty($this->Public)){
            $this->PublicLayout = check_init('PublicLayout', 'PublicLayout');
        }

        //============ Init all objects END ============

        //Set default Meta data for site
        $this->SetTitle(META_TITLE);
        $this->SetDescription(META_DESCRIPTION);
        $this->SetKeywords(META_KEYWORDS);

    } // end of constructor PageUser()

    function out()
    {
        if ($this->Is_404()) {
            $content = 'Извините, но содержимое не найдено. Однако у нас много другой полезной информации про охранные сигнализации!';
            $contentsHtml = View::factory('/templates/tpl_404.php')
                ->bind('h1', $this->multi['MSG_404_PAGE_NOT_FOUND'])
                ->bind('content', $content);
        }elseif ($this->FrontendPages->page == $this->FrontendPages->main_page) {
            $contentsHtml = View::factory('/templates/tpl_main_page_content.php')
                ->bind('h1', $this->h1)
                ->bind('breadcrumb', $this->breadcrumb)
                ->bind('content', $this->content)
                ->bind('PageUser', $this);
        }else {
            $contentsHtml = View::factory('/templates/tpl_content.php')
                ->bind('h1', $this->h1)
                ->bind('breadcrumb', $this->breadcrumb)
                ->bind('content', $this->content)
                ->bind('showContent2Box',$this->showContent2Box)
                ->bind('PageUser', $this);
        }
        if (is_ajax) {
            echo $contentsHtml;
            return true;
        }
        $this->LangShortName = $this->Lang->GetLangShortName(_LANG_ID);
        $this->send_headers();
        echo View::factory('/templates/tpl_main.php')
            ->bind('PageUser', $this)
            ->bind('contentHtml', $contentsHtml);
    }


} //end of class PageUser
?>

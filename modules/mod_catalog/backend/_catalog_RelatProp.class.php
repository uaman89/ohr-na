<?php
/**
 * Class catalog_RelatProp
 * Class to display relat prop positions
 * @author Sergey Panarin <sp@seotm.com>
 * @version 1.0 21.01.12
 * @property FrontSpr $Spr
 * @property Form $Form
 * @property db $db
 * @property SystemCurrencies $Currencies
 * @property Rights $Right
 */
class catalog_RelatProp extends Catalog{

    public $db = NULL;
    public $Currencies = NULL;
    public $Spr = NULL;
    public $Form = NULL;
    public $settings = NULL;
    public $id_prop = NULL;
    public $Right = NULL;
    public $user_id = NULL;
    public $module = NULL;
    public $tbl = NULL;
    public $PropRows = 0;
    public $propStr = NULL;

    /**
     * @param null $user_id
     * @param null $module
     * @param null $display
     * @param null $sort
     * @param null $start
     * @param null $width
     * @author Bogdan Iglinsky
     */
    function __construct($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL) {
        //Check if Constants are overrulled
        ( $user_id  !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
        ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
        ( $display  !="" ? $this->display = $display  : $this->display = 20   );
        ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
        ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
        ( $width    !="" ? $this->width   = $width    : $this->width   = 750  );

        $this-> lang_id = _LANG_ID;

        if (empty($this->db)) $this->db = DBs::getInstance();
        if (empty($this->Right)) $this->Right = check_init('RightsPelatProp', 'Rights', "'".$this->user_id."', '".$this->module."'");
        if (empty($this->Form)) $this->Form = check_init('FormRelatProp', 'Form', "'form_mod_catalog_relat_prop'");
        if (empty($this->Spr)) $this->Spr = check_init('SysSpr', 'SysSpr', "'$this->user_id', '$this->module'");
        if (empty($this->settings)) $this->settings = $this->GetSettings();

        if (( isset($this->settings['price_currency']) AND $this->settings['price_currency']=='1' ) OR ( isset($this->settings['opt_price_currency']) AND $this->settings['opt_price_currency']=='1' ) ){
            $this->Currencies = check_init('SystemCurrencies', 'SystemCurrencies', "'".$this->user_id."', '".$this->module."', 'back'");
            $this->Currencies->defCurrencyData = $this->Currencies->GetDefaultData();
            $this->Currencies->GetShortNamesInArray('back');
        }

        if (empty($this->multi)) $this->multi = check_init_txt('TblBackMulti',TblBackMulti);
    } // End of Catalog_content Constructor

    /**
     *
     * @author Bogdan Iglinsky
     */
    function show($set=false){
        if(!$set){
            $name_panel = $this->multi['TXT_CONTROL_RELAT_PROP'].' для';
        }else{
            $name_panel = "Комплект ";
        }

        $name_panel .= ' <u><strong>'.$this->Spr->GetNameByCod( TblModCatalogPropSprName, $this->id_prop).'</strong></u>';
        AdminHTML::PanelSubH($name_panel );
        $relatPropArr=$this->GetData();
        ?><div id="RelatPropPositionsBox" class="PanelSimpleL">
        <div style="overflow: hidden">
            <form action="/admin/index.php?module=<?=$this->module?>&id_prop=<?=$this->id_prop?>" method="post" name="form_mod_catalog_relat_prop" id="form_mod_catalog_relat_prop" enctype="multipart/form-data">

                <?if(!$set){?>
                <?$this->Form->WriteSavePanel("",'save',NULL,'Сохранить порядок')?>
                <?$this->Form->WriteCancelPanel("")?>
                <?};?>
                <input type="hidden" name="task" id="task" value="">
                <input type="hidden" name="id_prop" value="<?=$this->id_prop?>">
                <?php

               if($set) {
                   $propStr = $this->showSameBlockForProp($relatPropArr, "", "Комплектация", $set);
               }else{
                   $propStr = $this->showSameBlockForProp($relatPropArr);
               }




                $relatPropArrOnThisprop=$this->GetDataOnThisprop();


                if(!$set) {

                    $propStrTmp = $this->showSameBlockForProp($relatPropArrOnThisprop,'onThisPropPanel',$this->multi['TXT_PRODUCTS_TO_WHICH_THIS_PRODUCTS_IS_FIED']);

                }else{

                    $propStrTmp ='';

                }



                ?>
            </form>
        </div>
        </div><div class="space"></div><?
        if(!$set){
            $this->Form->WriteSavePanel("",'save',NULL,'Сохранить порядок');
            $this->Form->WriteCancelPanel("");
        }

        $this->propStr=$propStr;
        $this->propStronThisPropPanel=$propStrTmp;
    }

    /**
     * @param $relatPropArr
     * @param string $name_Panel
     * @param string $nameText
     * @return string
     * @author Bogdan Iglinsky
     */
    function showSameBlockForProp($relatPropArr, $name_Panel = '' , $nameText = '', $set=false){
        $this->deleteonThisPropPanelTask =1;
        if(empty($nameText)) $nameText = $this->multi['TXT_GOOD_LINKED_TO_THIS_PRODUCT'];
        ?><fieldset title="<?=$nameText?>">

        <legend>

            <span style='vetical-align:middle; font-size:15px;'><img src='images/icons/meta.png' alt="<?=$nameText?>" title="<?=$nameText?>" border='0' /><?=$nameText?></span>
        </legend>
        <div class="clear"></div>
        <div>
            <input type="button" class="btn0" value="<?=$this->multi['TXT_SELL_ALL']?>"
                   title="<?=$this->multi['TXT_SELL_ALL']?>" onclick="sellAllRelatPropsInList<?=$name_Panel?>()"/>

            <input type="button" class="btn0" value="<?=$this->multi['TXT_REMOVE_ALL']?>"
                   title="<?=$this->multi['TXT_REMOVE_ALL']?>" onclick="removeAllRelatPropsInList<?=$name_Panel?>()"/>  <?
            if($set){

                ?>
                <a class="r-button" href="javascript:RemovePropSet();" onClick="if( !window.confirm('Удалить?')) return false;">
                    <span><span><img src="images/icons/delete.png" alt="" title="" align="center" name="delete" />Удалить</span></span>
                </a>
                <?

            }else{
                if(empty($name_Panel)){

                    $this->Form->WriteTopPanel("",2);
                }else{
                    $this->Form->WriteTopPanel("",2,'delete'.$name_Panel);
                }
            }
            ?>
            <br><br>

        </div>
        <ul id="sortableUl<?=$name_Panel?>" class="sortableUl" style="margin: 0px;padding: 0px;">
            <?
            $propStr="";
            $count = count($relatPropArr);
            for ($i = 0; $i < $count; $i++) {
                $row=$relatPropArr[$i];
                if($i==0) $propStr.=$row['prop_id'];
                else $propStr.=','.$row['prop_id'];
                ?><li  class="SingleRelatPropBox for-sel-all-In-list<?=$name_Panel?>" onclick="SelectDeselectProp<?=$name_Panel?>('#relatPropDel<?=$name_Panel?><?=$row['prop_id']?>')">
                <input id="relatPropDel<?=$name_Panel?><?=$row['prop_id']?>" class="relatPropCheck" type="checkbox" name="del<?=$name_Panel?>[<?=$row['prop_id']?>]" title="<?=$row['propName']?> "/>
                <?
                if ( isset($this->settings['img']) AND $this->settings['img']=='1' ) {

                    if(!empty($row['first_img_alt'])) $alt=$row['first_img_alt'];
                    elseif(!empty($row['propName'])) $alt=$row['propName'];

                    if(!empty($row['first_img_title'])) $title=$row['first_img_title'];
                    elseif(!empty($row['propName'])) $title=$row['propName'];
                    ?><div class="imageBox120x90"><?
                    if (isset($row['first_img'])) {
                        echo $this->ShowCurrentImage($row['first_img_id'], 'size_auto=80', 85, NULL, "border=0 alt='".$alt."' title='".$title."'");
                    }
                    ?></div><?
                }

                if(!empty($row['art_num'])){
                    ?><span><?
                    echo '<b>Артикул:</b>';
                    echo $row['art_num'];
                    ?></span><?
                }
                if(!empty($row['propName'])){
                    ?><b class="span-b">
                    <a alt="<?=$row['prop_id']?>" href="/admin/index.php?module=21&start=0&task=edit&id=<?=$row['prop_id']?>"><?
                        echo stripcslashes($row['propName']);
                        ?></a>
                    </b><?
                }
                ?></li><?
            }
            ?>

        </ul>
        <div class="addNewProp" onclick="showCatalog<?=$name_Panel?>()">
            <div class="addPropPlus"></div>
            <div class="ZP">Добавить</div>
        </div>
        <input id='propStr<?=$name_Panel?>' type="hidden" name="propStr<?=$name_Panel?>" value="<?=$propStr?>"/>
        <div class="clear"></div>
        <div><br>
            <input type="button" class="btn0" value="<?=$this->multi['TXT_SELL_ALL']?>"
                   title="<?=$this->multi['TXT_SELL_ALL']?>" onclick="sellAllRelatPropsInList<?=$name_Panel?>()"/>
            <input type="button" class="btn0" value="<?=$this->multi['TXT_REMOVE_ALL']?>"
                   title="<?=$this->multi['TXT_REMOVE_ALL']?>" onclick="removeAllRelatPropsInList<?=$name_Panel?>()"/>
            <?if(empty($name_Panel)){
                $this->Form->WriteTopPanel("",2);
            }else{
                $this->Form->WriteTopPanel("",2,'delete'.$name_Panel);
            }?>
            <br><br>
        </div>
        <script type="text/javascript">


            function RemovePropSet() {
                $url = $("#form_mod_catalog_relat_prop").attr('action')

                $.ajax({
                    url : $url,
                    type : "POST",
                    data :$('#form_mod_catalog_relat_prop').serialize()+"&ajax=1&task=delete",
                    beforeSend : function(){
                        $("#loader").show();
                    },
                    success : function(data){
                        $.fancybox(data);
                    }
                });
            }


            function showCatalog<?=$name_Panel?>(){
                $.fancybox({
                    href : "<?=$this->script."&task=CatalogShow".$name_Panel."&ajax=1&propStr".$name_Panel."="?>"+$("#propStr<?=$name_Panel?>").val(),
                    onComplete:function(){
                        makeTree<?=$name_Panel?>();
                    }
                });
            }
            function makeTree<?=$name_Panel?>(){
                $("#tree").treeview({
                    collapsed: true,
                    animated: "medium",
                    control:"#sidetreecontrol",
                    persist: "cookie",
                    cookieId: "catalogTreeView"
                });
            }
            function SelectDeselectProp<?=$name_Panel?>($id){
                $checkObj=$($id);
                $checkObj.attr('checked',!$checkObj.attr('checked'));
                if($checkObj.attr('checked')=='checked') $checkObj.parent().addClass("propSelected");
                else $checkObj.parent().removeClass("propSelected");
            }

            function AddRelatPropsTo<?=$name_Panel?>(){
                $url="<?=$this->script."&ajax=1&propStr="?>"+$("#propStr<?=$name_Panel?>").val();
                $.ajax({
                    url : $url,
                    type : "POST",
                    data : $("#addPropTo").serialize(),
                    beforeSend : function(){
                        $("#loader").show();
                    },
                    success : function(data){
                        $("#loader").hide();
                        $("#RelatPropPositionsBox").html($("#RelatPropPositionsBox",data).html());
                        $("#relatPropCatalogBox").html($("#relatPropCatalogBox",data).html());
                        initSortable<?=$name_Panel?>();
                        makeTree<?=$name_Panel?>();
                    }
                });
            }

            function reloadCatalogInner<?=$name_Panel?>($catId,$url){
                if($catId=='') $catId=<?=$this->id_cat?>;
                if(!$url) $url="<?=$this->script."&task=CatalogInnerShow".$name_Panel."&ajax=1&propStr="?>"+$("#propStr<?=$name_Panel?>").val()+"&id_cat="+$catId;
                $.ajax({
                    url : $url,
                    type : "POST",
                    beforeSend : function(){
                        $("#loader").show();
                    },
                    success : function(data){
                        $("#loader").hide();
                        $("#relatPropCatalog").html(data);
                    }
                });
            }
            function initSortable<?=$name_Panel?>(){
                $( "#sortableUl<?=$name_Panel?>" ).sortable({
                    placeholder: "ui-state-highlight",
                    update:function(event,ui){
                        $propOrderStr='';
                        $('#sortableUl li a').each(function(){
                            if($propOrderStr.length==0) $propOrderStr+=$(this).attr("alt");
                            else $propOrderStr+=','+$(this).attr("alt");
                        });
                        $("#propStr<?=$name_Panel?>").val($propOrderStr);
                    }
                });
                $( "#sortableUl<?=$name_Panel?>" ).disableSelection();
            }
            $(document).ready(function(){
                initSortable<?=$name_Panel?>();
            });

            function sellAllRelatProps<?=$name_Panel?>(){
                $('.for-sel-all input[type="checkbox"]').attr('checked','checked');
                $('.for-sel-all').addClass('propSelected');
            }

            function removeAllRelatProps<?=$name_Panel?>(){
//                        alert(!$('.for-sel-all input[type="checkbox"]').attr('checked'));
                $('.for-sel-all input[type="checkbox"]').attr('checked',false);
                $('.for-sel-all').removeClass('propSelected');
            }

            function sellAllRelatPropsInList<?=$name_Panel?>(){
                $('.for-sel-all-In-list<?=$name_Panel?> input[type="checkbox"]').attr('checked','checked');
                $('.for-sel-all-In-list<?=$name_Panel?>').addClass('propSelected');
            }

            function removeAllRelatPropsInList<?=$name_Panel?>(){
//                        alert(!$('.for-sel-all input[type="checkbox"]').attr('checked'));
                $('.for-sel-all-In-list<?=$name_Panel?> input[type="checkbox"]').attr('checked',false);
                $('.for-sel-all-In-list<?=$name_Panel?>').removeClass('propSelected');
            }

        </script>
        </fieldset><?
        return $propStr;
    }

    /**
     *
     * @author Bogdan Iglinsky
     */
    function showCatalogByPages(){
        ?>
        <div class="loader" id="loader" style="display: none; "><?=$this->multi['TXT_SAVE_TEXT_LOADER']?></div>
        <div id="relatPropCatalogBox" class="relatPropCatalogBox">
            <div class="relatPropCatalogMenu">
                <?$this->showCategManu();?>
            </div>
            <div id="relatPropCatalog" class="relatPropCatalog">
                <?$this->showCatalogPropPart();?>
            </div>
        </div>

    <?
    }

    /**
     *
     * @author Bogdan Iglinsky
     */
    function showCatalogPropPart(){

        $catName=$this->Spr->GetNameByCod(TblModCatalogSprName, $this->id_cat, $this->lang_id, 1);
        ?>
        <h1 style="margin-top: 25px;"><? empty($catName) ? print($this->multi['TXT_CATALOG']) : print($catName);?></h1>
        <div class="pages">
            <?


            $task = 'CatalogInnerShow';
            $propStr = "&propStr=".$this->propStr;
            //                echo '$this->showAterPanel='.$this->showAterPanel;
            if(isset($this->showAterPanel) && $this->showAterPanel==1){
                $task .= 'onThisPropPanel';
                $CatalogData=$this->GetDataOnThisprop('catalog');
                $this->GetDataOnThisprop('catalog','nolimit');
                $propStr = "&propStronThisPropPanel=".$this->propStronThisPropPanel;
            }else{
                $CatalogData=$this->GetData('catalog');
//                var_dump($CatalogData);
                $this->GetData('catalog','nolimit');
            }

            $this->Form->WriteLinkPages($this->script."&task=".$task.$propStr."&ajax=1&id_cat=".$this->id_cat,$this->PropRows,$this->display,$this->start,'',true);
            ?>
        </div>
        <form action="#" name="addPropTo" method="post" id="addPropTo" enctype="multipart/form-data">
            <?$task = 'addProp';
            //                echo '$this->showAterPanel='.$this->showAterPanel;
            if(isset($this->showAterPanel) && $this->showAterPanel==1) $task .= 'onThisPropPanel';?>
            <input type="hidden" name="task" value="<?=$task?>"/>
            <input type="hidden" name="id_cat" value="<?=$this->id_cat?>"/>
            <input id='propStr' type="hidden" name="propStr" value="<?=$propStr?>"/>
            <?
            for ($i = 0; $i < count($CatalogData); $i++) {
                $row=$CatalogData[$i];
                ?><div class="SingleRelatPropBox for-sel-all" onclick="SelectDeselectProp('#PropCheckBoxAdd<?=$row['prop_id']?>')">
                <input id="PropCheckBoxAdd<?=$row['prop_id']?>" class="relatPropCheck" type="checkbox" name="add[<?=$row['prop_id']?>]" title="<?=$row['propName']?> "/>
                <?
                if ( isset($this->settings['img']) AND $this->settings['img']=='1' ) {

                    if(!empty($row['first_img_alt'])) $alt=$row['first_img_alt'];
                    elseif(!empty($row['propName'])) $alt=$row['propName'];

                    if(!empty($row['first_img_title'])) $title=$row['first_img_title'];
                    elseif(!empty($row['propName'])) $title=$row['propName'];
                    ?><div class="imageBox120x90"><?
                    if (isset($row['first_img'])) {
                        echo $this->ShowCurrentImage($row['first_img_id'], 'size_auto=80', 85, NULL, "border=0 alt='".$alt."' title='".$title."'");
                    }
                    ?></div><?
                }

                if(!empty($row['art_num'])){
                    ?><span><?
                    echo '<b>Артикул:</b>';
                    echo $row['art_num'];
                    ?></span><?
                }
                if(!empty($row['propName'])){
                    ?><b class="span-b">
                    <a alt="<?=$row['prop_id']?>" href="/admin/index.php?module=21&start=0&task=edit&id=<?=$row['prop_id']?>"><?
                        echo $row['propName'];
                        ?></a>
                    </b><?
                }

                ?>

                </div><?
            }
            ?>
        </form>
        <div class="addPropBtnBox">
            <input type="button" class="btn0" value="<?=$this->multi['TXT_SELL_ALL']?>"
                   title="<?=$this->multi['TXT_SELL_ALL']?>" onclick="sellAllRelatProps()"/>
            <input type="button" class="btn0" value="<?=$this->multi['TXT_REMOVE_ALL']?>"
                   title="<?=$this->multi['TXT_REMOVE_ALL']?>" onclick="removeAllRelatProps()"/>
            <input type="button" class="btn0" value="<?=$this->multi['TXT_ADD_RELAT_PROPS']?>"
                   title="<?=$this->multi['TXT_ADD_RELAT_PROPS']?>" onclick="AddRelatPropsTo<?if(isset($this->showAterPanel) && $this->showAterPanel){
                echo 'onThisPropPanel';
            }?>()"/>
        </div>
    <?
    }

    /**
     *
     * @author Bogdan Iglinsky
     */
    function showCategManu(){
        $q = "select
                    `".TblModCatalog."`.id,
                    `".TblModCatalog."`.level,
                    `".TblModCatalogSprName."`.name
              from `".TblModCatalog."` LEFT JOIN `".TblModCatalogSprName."`
                    ON ( `".TblModCatalog."`.id = `".TblModCatalogSprName."`.cod AND `".TblModCatalogSprName."`.lang_id = '".$this->lang_id."')
              where `".TblModCatalog."`.id > 0
                    order by `level` asc, `move` ";
//        $q = "select * from `".TblModCatalog."` where 1 and `level`='".$level."' order by `move` ";
        $res = $this->db->db_Query($q);
//        echo '<br>$q='.$q.' $res='.$res;
        $rows = $this->db->db_GetNumRows($res);
        $levels = array();
        $names = array();
        for($i=0; $i<$rows; $i++)
        {
            $row = $this->db->db_FetchAssoc($res);
            $levels [$row['level']][] = $row ['id'];
            $names [$row['id']] = $row['name'];
        }
        $this->countArr = $this->GetArrayContentCount();
        ?>
        <script src="/admin/include/js/treeView/jquery.treeview.js" type="text/javascript"></script>
        <script src="/admin/include/js/treeView/jquery.cooki.js" type="text/javascript"></script>
        <link rel="stylesheet" href="/admin/include/js/treeView/jquery.treeview.css" />
        <div id="sidetreecontrol"><a href="?#"><?=$this->multi['TXT_COLLAPSE_ALL']?></a> | <a href="?#"><?=$this->multi['TXT_EXPAND_ALL']?></a></div>

        <?
        $this->showCategManuInner(0,$levels,$names);

    }

    /**
     * @param int $level
     * @param $levels
     * @param $names
     * @author Bogdan Iglinsky
     */
    function showCategManuInner($level=0,$levels,$names){
        if(!isset($levels[$level])) return;
        $count = count($levels[$level]);
        ?>
        <ul id="tree" class="filetree treeview">
            <?
            for ($i = 0; $i < $count; $i++) {
                $id = $levels[$level][$i];
                $is_sub_level = (isset($levels[$id]) && count($levels[$id]) > 0);
                if(isset($this->countArr[$id]))
                    $count_content = $this->countArr[$id];
                else
                    $count_content = 0;

                ?><li><?
                $reloadCatalogInner = 'reloadCatalogInner';
                if(isset($this->showAterPanel) && $this->showAterPanel){
                    $reloadCatalogInner .= 'onThisPropPanel';
                }
                if( $is_sub_level ) {
                    ?><a class="folder " href="#" onclick="<?=$reloadCatalogInner?>(<?=$id?>);return false;"><?=$names[$id];?></a><?
                    if( $count_content>0 ) {
                        ?><a href="#"  onclick="<?=$reloadCatalogInner?>(<?=$id?>);return false;"><?=$this->multi['FLD_CONTENT'];?></a><span class="not_href">&nbsp;[<?=$count_content;?>]</span><?
                    }
                }
                else {
                    if( $count_content>0 ) {
                        ?><a class="file " href="#"  onclick="<?=$reloadCatalogInner?>(<?=$id?>);return false;"><?=$names[$id];?></a><span class="not_href">&nbsp;[<?=$count_content;?>]</span><?
                    }
                    else {
                        ?><a class="file " href="#"  onclick="<?=$reloadCatalogInner?>(<?=$id?>);return false;"><?=$names[$id];?></a><?
                    }
                }
                if( $is_sub_level )
                    $this->showCategManuInner($id, $levels, $names);
                ?></li><?
            }
            ?>
        </ul>

    <?
    }


    /**
     * @param string $what
     * @param string $limit
     * @return array|bool
     * @author Bogdan Iglinsky
     */
    function GetData($what='relatPorsitions',$limit='limit'){
        if($what=='relatPorsitions'){
            $q="SELECT `".$this->tbl."`.*,
                `".TblModCatalogProp."`.`price`,
                `".TblModCatalogProp."`.`art_num`,
                `".TblModCatalogProp."`.`price_currency`,
                `".TblModCatalogProp."`.`id` AS `prop_id`,
                `".TblModCatalogPropSprName."`.`name` AS `propName`,
                `".TblModCatalogPropImg."`.`path` AS `first_img`,
                `".TblModCatalogPropImg."`.`id` AS `first_img_id`,
                `".TblModCatalogPropImgTxt."`.`name` AS `first_img_alt`,
                `".TblModCatalogPropImgTxt."`.`text` AS `first_img_title`
            FROM `".$this->tbl."`,`".TblModCatalogProp."`
                LEFT JOIN `".TblModCatalogPropImg."` ON (`".TblModCatalogProp."`.`id`=`".TblModCatalogPropImg."`.`id_prop` AND `".TblModCatalogPropImg."`.`move`='1' AND `".TblModCatalogPropImg."`.`show`='1')
                LEFT JOIN `".TblModCatalogPropImgTxt."` ON (`".TblModCatalogPropImg."`.`id`=`".TblModCatalogPropImgTxt."`.`cod` AND `".TblModCatalogPropImgTxt."`.lang_id='".$this->lang_id."')
                LEFT JOIN `".TblModCatalogPropSprName."` ON (`".TblModCatalogPropSprName."`.`cod`=`".TblModCatalogProp."`.`id` AND `".TblModCatalogPropSprName."`.`lang_id`='".$this->lang_id."')

            WHERE `".$this->tbl."`.`id_prop1`='".$this->id_prop."' AND `".TblModCatalogProp."`.`id`=`".$this->tbl."`.`id_prop2`
                ORDER BY `".$this->tbl."`.`move`
                ";
        }elseif($what=='catalog'){
            $q="SELECT
            `".TblModCatalogProp."`.`price`,
                `".TblModCatalogProp."`.`art_num`,
            `".TblModCatalogProp."`.`price_currency`,
            `".TblModCatalogProp."`.`id` AS `prop_id`,
            `".TblModCatalogPropSprName."`.`name` AS `propName`,
            `".TblModCatalogPropImg."`.`path` AS `first_img`,
            `".TblModCatalogPropImg."`.`id` AS `first_img_id`,
            `".TblModCatalogPropImgTxt."`.`name` AS `first_img_alt`,
            `".TblModCatalogPropImgTxt."`.`text` AS `first_img_title`
           FROM `".TblModCatalogProp."`
               LEFT JOIN `".TblModCatalogPropImg."` ON (`".TblModCatalogProp."`.`id`=`".TblModCatalogPropImg."`.`id_prop` AND `".TblModCatalogPropImg."`.`move`='1' AND `".TblModCatalogPropImg."`.`show`='1')
               LEFT JOIN `".TblModCatalogPropImgTxt."` ON (`".TblModCatalogPropImg."`.`id`=`".TblModCatalogPropImgTxt."`.`cod` AND `".TblModCatalogPropImgTxt."`.lang_id='".$this->lang_id."')
               LEFT JOIN `".TblModCatalogPropSprName."` ON (`".TblModCatalogPropSprName."`.`cod`=`".TblModCatalogProp."`.`id` AND `".TblModCatalogPropSprName."`.`lang_id`='".$this->lang_id."')
               ";
            if(!empty($this->propStr)) $q.=" WHERE `".TblModCatalogProp."`.`id` NOT IN (".$this->propStr.") AND `".TblModCatalogProp."`.`id`<>'".$this->id_prop."'";
            else  $q.=" WHERE  `".TblModCatalogProp."`.`id`<>'".$this->id_prop."'";
            if(!empty($this->id_cat)) $q.=" AND `".TblModCatalogProp."`.`id_cat`='".$this->id_cat."'";
            $q.=" ORDER BY `".TblModCatalogProp."`.`move`";
            if($limit=='limit')
                $q.=" LIMIT ".$this->start.", ".($this->display);


        }
        $res=$this->Right->Query($q, $this->user_id, $this->module);
        //echo '<br>res: '.$res;
        //echo '<br>$q: '.$q;
        //echo '<br>$this->propStr: '.$this->propStr;
        if(!$res) return false;
        $rows=$this->Right->db_GetNumRows();
        $this->PropRows=$rows;
        if($limit=='nolimit') return;
        $resArr=array();
        for ($i = 0; $i < $rows; $i++) {
            $row=$this->Right->db_FetchAssoc();
            $resArr[]=$row;
        }
        return $resArr;
    }

    /**
     * @param string $what
     * @param string $limit
     * @return array|bool
     * @author Bogdan Iglinsky
     */
    function GetDataOnThisprop($what='relatPorsitions',$limit='limit'){
        if($what=='relatPorsitions'){
            $q="SELECT `".$this->tbl."`.*,
                `".TblModCatalogProp."`.`price`,
                `".TblModCatalogProp."`.`art_num`,
                `".TblModCatalogProp."`.`price_currency`,
                `".TblModCatalogProp."`.`id` AS `prop_id`,
                `".TblModCatalogPropSprName."`.`name` AS `propName`,
                `".TblModCatalogPropImg."`.`path` AS `first_img`,
                `".TblModCatalogPropImg."`.`id` AS `first_img_id`,
                `".TblModCatalogPropImgTxt."`.`name` AS `first_img_alt`,
                `".TblModCatalogPropImgTxt."`.`text` AS `first_img_title`
            FROM `".$this->tbl."`,`".TblModCatalogProp."`
                LEFT JOIN `".TblModCatalogPropImg."` ON (`".TblModCatalogProp."`.`id`=`".TblModCatalogPropImg."`.`id_prop` AND `".TblModCatalogPropImg."`.`move`='1' AND `".TblModCatalogPropImg."`.`show`='1')
                LEFT JOIN `".TblModCatalogPropImgTxt."` ON (`".TblModCatalogPropImg."`.`id`=`".TblModCatalogPropImgTxt."`.`cod` AND `".TblModCatalogPropImgTxt."`.lang_id='".$this->lang_id."')
                LEFT JOIN `".TblModCatalogPropSprName."` ON (`".TblModCatalogPropSprName."`.`cod`=`".TblModCatalogProp."`.`id` AND `".TblModCatalogPropSprName."`.`lang_id`='".$this->lang_id."')

            WHERE `".$this->tbl."`.`id_prop2`='".$this->id_prop."' AND `".TblModCatalogProp."`.`id`=`".$this->tbl."`.`id_prop1`
                ORDER BY `".$this->tbl."`.`move`
                ";
        }elseif($what=='catalog'){
            $q="SELECT
            `".TblModCatalogProp."`.`price`,
                `".TblModCatalogProp."`.`art_num`,
            `".TblModCatalogProp."`.`price_currency`,
            `".TblModCatalogProp."`.`id` AS `prop_id`,
            `".TblModCatalogPropSprName."`.`name` AS `propName`,
            `".TblModCatalogPropImg."`.`path` AS `first_img`,
            `".TblModCatalogPropImg."`.`id` AS `first_img_id`,
            `".TblModCatalogPropImgTxt."`.`name` AS `first_img_alt`,
            `".TblModCatalogPropImgTxt."`.`text` AS `first_img_title`
           FROM `".TblModCatalogProp."`
               LEFT JOIN `".TblModCatalogPropImg."` ON (`".TblModCatalogProp."`.`id`=`".TblModCatalogPropImg."`.`id_prop` AND `".TblModCatalogPropImg."`.`move`='1' AND `".TblModCatalogPropImg."`.`show`='1')
               LEFT JOIN `".TblModCatalogPropImgTxt."` ON (`".TblModCatalogPropImg."`.`id`=`".TblModCatalogPropImgTxt."`.`cod` AND `".TblModCatalogPropImgTxt."`.lang_id='".$this->lang_id."')
               LEFT JOIN `".TblModCatalogPropSprName."` ON (`".TblModCatalogPropSprName."`.`cod`=`".TblModCatalogProp."`.`id` AND `".TblModCatalogPropSprName."`.`lang_id`='".$this->lang_id."')
               ";
            if(!empty($this->propStronThisPropPanel)) $q.=" WHERE `".TblModCatalogProp."`.`id` NOT IN (".$this->propStronThisPropPanel.") AND `".TblModCatalogProp."`.`id`<>'".$this->id_prop."'";
            else  $q.=" WHERE  `".TblModCatalogProp."`.`id`<>'".$this->id_prop."'";
            if(!empty($this->id_cat)) $q.=" AND `".TblModCatalogProp."`.`id_cat`='".$this->id_cat."'";
            $q.=" ORDER BY `".TblModCatalogProp."`.`move`";
            if($limit=='limit')
                $q.=" LIMIT ".$this->start.", ".($this->display);

        }
        $res=$this->Right->Query($q, $this->user_id, $this->module);
        if(!$res) return false;
        $rows=$this->Right->db_GetNumRows();
        $this->PropRows=$rows;
        if($limit=='nolimit') return;
        $resArr=array();
        for ($i = 0; $i < $rows; $i++) {
            $row=$this->Right->db_FetchAssoc();
            $resArr[]=$row;
        }
        return $resArr;
    }

    /**
     *
     * @author Bogdan Iglinsky
     */
    function save(){
        if(!empty($this->add) && is_array($this->add) && count($this->add)>0){
            $q="SELECT `id_prop1` FROM `".$this->tbl."` WHERE `id_prop1`='".$this->id_prop."'";
            $this->db->db_Query($q);
            $move=$this->db->db_GetNumRows();

            $keys=  array_keys($this->add);

            for ($i = 0; $i < count($keys); $i++) {
                $move++;
                $q="INSERT INTO `".$this->tbl."`
                   SET
                   `id_prop1`='".$this->id_prop."',
                   `id_prop2`='".$keys[$i]."',
                   `move`='".$move."'";
                $this->db->db_Query($q);
            }
        }
    }

    /**
     *
     * @author Bogdan Iglinsky
     */
    function saveonThisPropPanel(){
        if(!empty($this->add) && is_array($this->add) && count($this->add)>0){

            $keys=  array_keys($this->add);

            for ($i = 0; $i < count($keys); $i++) {
                $q="SELECT COUNT(*) FROM `".$this->tbl."` WHERE `id_prop1`='".$keys[$i]."'";
                $this->db->db_Query($q);
                $row = $this->db->db_GetNumRows();
                $move = $row['move']++;
                $q="INSERT INTO `".$this->tbl."`
                   SET
                   `id_prop2`='".$this->id_prop."',
                   `id_prop1`='".$keys[$i]."',
                   `move`='".$move."'";
                $this->db->db_Query($q);
            }
        }
    }

    /**
     *
     * @author Bogdan Iglinsky
     */
    function saveMove(){
        $q="DELETE FROM `".$this->tbl."` WHERE `id_prop1`='".$this->id_prop."'";
        $this->db->db_Query($q);
        //echo '<br>$q='.$q;
        //var_dump($this->propStr);
        if(isset($this->propStr)){
            $propArr=  explode(',', $this->propStr);
            $move=0;
            for ($i = 0; $i < count($propArr); $i++) {
                $q="SELECT *
                   FROM `".$this->tbl."`
                   WHERE (`id_prop1`='".$this->id_prop."' AND `id_prop2`='".$propArr[$i]."')";
                $this->db->db_Query($q);
                //echo '<br>$q='.$q;
                $rows = $this->db->db_GetNumRows();
                //echo '<br>$rows='.$rows;
                if($rows>0){
                    continue;
                }
                $move++;
                $q="INSERT IGNORE INTO `".$this->tbl."`
                   SET
                   `id_prop1`='".$this->id_prop."',
                   `id_prop2`='".$propArr[$i]."',
                   `move`='".$move."'";
                $this->db->db_Query($q);
                //echo '<br>$q='.$q;
            }
        }
    }

    /**
     *
     * @author Bogdan Iglinsky
     */
    function delete(){

        if(!empty($this->del) && is_array($this->del) && count($this->del)>0){
            $keys=  array_keys($this->del);
            for ($i = 0; $i < count($keys); $i++) {
                $q="DELETE FROM `".$this->tbl."`
                   WHERE `id_prop1`='".$this->id_prop."' AND `id_prop2`='".$keys[$i]."' ";
                $this->db->db_Query($q);
            }
        }
        if(!empty($this->delonThisProp) && is_array($this->delonThisProp) && count($this->delonThisProp)>0){
            $keys=  array_keys($this->delonThisProp);
//            var_dump($keys);
            for ($i = 0; $i < count($keys); $i++) {
                $q="DELETE FROM `".$this->tbl."`
                   WHERE `id_prop2`='".$this->id_prop."' AND `id_prop1`='".$keys[$i]."' ";
                $res = $this->db->db_Query($q);
//                echo '<br>$q='.$q.' $res='.$res;
            }
        }
    }

    /**
     *
     * @author Bogdan Iglinsky
     */
    function deleteonThisPropPanel(){
        if(!empty($this->delonThisProp) && is_array($this->delonThisProp) && count($this->delonThisProp)>0){
            $keys=  array_keys($this->delonThisProp);
//            var_dump($keys);
            for ($i = 0; $i < count($keys); $i++) {
                $q="DELETE FROM `".$this->tbl."`
                   WHERE `id_prop2`='".$this->id_prop."' AND `id_prop1`='".$keys[$i]."' ";
                $res = $this->db->db_Query($q);
//                echo '<br>$q='.$q.' $res='.$res;
            }
        }
    }


}

?>

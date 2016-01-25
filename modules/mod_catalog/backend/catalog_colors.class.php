<?php
/**
 * CatalogColors.class.php
 * class for display work with colors.
 * @package Catalog Package of SEOCMS
 * @author Sergey Panarin  <sp@seotm.com>
 * @version 1.0, 10.10.2011
 * @copyright (c) 2010+ by SEOTM
 */
include_once( SITE_PATH . '/modules/mod_catalog/catalog.defines.php' );

/**
 * Class CatalogColors
 * class for display work with colors.
 * @author Sergey Panarin  <sp@seotm.com>
 * @version 1.1, 10.10.2011
 * @property SystemCurrencies $Currencies
 */
class CatalogColors extends Catalog {

    public $id_prop = NULL;
    public $colors = array();
    public $treeColorsData = NULL;
    public $treeSizesData = NULL;
    public $treeSizesDataAll = NULL;
    public $treeSizesProp = NULL;
    public $SizeCatId = NULL;
    public $SizesCatName = "";
    public $propIdCat = NULL;
    public $dontUseSizes = NULL;
    public $Currencies = NULL;

    function __construct($user_id=NULL, $module=NULL, $idProp=NULL, $colorsStr=NULL, $propIdCat=NULL) {
        ( $user_id != "" ? $this->user_id = $user_id : $this->user_id = NULL );
        ( $module != "" ? $this->module = $module : $this->module = NULL );

        if (!empty($idProp))
            $this->id_prop = $idProp;

        if (!empty($propIdCat))
            $this->propIdCat = $propIdCat;

        if (!empty($colorsStr))
            $this->colors = explode(',', $colorsStr);

        $this->lang_id = _LANG_ID;
        if (empty($this->db))
            $this->db = DBs::getInstance();
        if (empty($this->Right))
            $this->Right = check_init('Rights', 'Rights', "'$this->user_id', '$this->module'");
        if (empty($this->Msg))
            $this->Msg = check_init('ShowMsg', 'ShowMsg');
        if (empty($this->Form))
            $this->Form = check_init('FormCatalog', 'Form', '"form_mod_catalog"');
        if (empty($this->Spr))
            $this->Spr = check_init('SysSpr', 'SysSpr', '$this->user_id, $this->module');
        if (empty($this->settings))
            $this->settings = $this->GetSettings();
        if (empty($this->multi))
            $this->multi = check_init_txt('TblBackMulti', TblBackMulti);
        if (( isset($this->settings['price_currency']) AND $this->settings['price_currency']=='1' ) OR ( isset($this->settings['opt_price_currency']) AND $this->settings['opt_price_currency']=='1' ) ){
            $this->Currencies = check_init('SystemCurrencies', 'SystemCurrencies');
            $this->Currencies->defCurrencyData = $this->Currencies->GetDefaultData();
            $this->Currencies->GetShortNamesInArray('back');
        }
        $this->getColorsData();
        $this->getColorsOfProp();
    }

    function getColorsData() {


        $q = "SELECT * FROM `" . TblModCatalogSprColors . "`
                WHERE  `lang_id`='" . $this->lang_id . "'
                ";
        $res = $this->db->db_Query($q);
        if( !$res OR !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        $this->treeColorsData = array();
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc();
            $this->treeColorsData[$row['cod']] = $row;
        }

        if ($this->settings['sizes'] == 1) {
            $this->getSizeCateg($this->propIdCat);
            if (!empty($this->id_prop)) {
                if (empty($this->propIdCat)) {
                    $q = "SELECT `id_cat` FROM `" . TblModCatalogProp . "` WHERE `" . TblModCatalogProp . "`.`id`='" . $this->id_prop . "'";
                    $res = $this->db->db_Query($q);
                    $rows = $this->db->db_GetNumRows();
                    $row = $this->db->db_FetchAssoc();
                    $this->propIdCat = $row['id_cat'];
                }
                //$this->getSizeCateg($this->propIdCat);
                $q = "SELECT `" . TblModCatalogPropSizes . "`.*
                FROM `" . TblModCatalogPropSizes . "`,`" . TblModCatalog . "`,`" . TblModCatalogSprSizes . "`,`" . TblModCatalogProp . "`
                WHERE `" . TblModCatalogPropSizes . "`.`id_prop`='" . $this->id_prop . "'
                    AND `" . TblModCatalog . "`.`id`=`" . TblModCatalogProp . "`.`id_cat`
                    AND `" . TblModCatalogProp . "`.`id`='" . $this->id_prop . "'
                    AND `" . TblModCatalogSprSizes . "`.`lang_id`='" . $this->lang_id . "'
                ";

                if (count($this->colors) > 0)
                    $q.=" AND `" . TblModCatalogPropSizes . "`.`id_color` IN (" . implode(',', $this->colors) . ",-1)";
                $res = $this->db->db_Query($q);
                //echo '<br>$q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;
                $rows = $this->db->db_GetNumRows();
                $this->treeSizesProp = array();
                for ($i = 0; $i < $rows; $i++) {
                    $row = $this->db->db_FetchAssoc();
                    $this->treeSizesProp[$row['id_prop']][$row['id_color']][$row['id_size']]['cnt'] = $row['cnt'];
                    $this->treeSizesProp[$row['id_prop']][$row['id_color']][$row['id_size']]['price'] = $row['price'];
                    $this->treeSizesProp[$row['id_prop']][$row['id_color']][$row['id_size']]['old_price'] = $row['old_price'];
                    $this->treeSizesProp[$row['id_prop']][$row['id_color']][$row['id_size']]['price_currency'] = $row['price_currency'];
                }

                $q = "SELECT `" . TblModCatalogSprSizes . "`.*
                FROM `" . TblModCatalogSprSizes . "`,`" . TblModCatalog . "`,`" . TblModCatalogProp . "`
                    WHERE
                    `" . TblModCatalog . "`.`id`=`" . TblModCatalogProp . "`.`id_cat`
                    AND `" . TblModCatalogProp . "`.`id`='" . $this->id_prop . "'
                    AND `" . TblModCatalogSprSizes . "`.`level`='" . $this->SizeCatId. "'
                    AND `" . TblModCatalogSprSizes . "`.`lang_id`='" . $this->lang_id . "'
                        ORDER BY `" . TblModCatalogSprSizes . "`.`move`
                ";
                $res = $this->db->db_Query($q);
                $rows = $this->db->db_GetNumRows();
                $this->treeSizesData = array();
                for ($i = 0; $i < $rows; $i++) {
                    $row = $this->db->db_FetchAssoc();
                    $this->treeSizesData[$row['cod']] = $row;
                }
		$q="SELECT `dontUseSizes` FROM `" . TblModCatalogProp . "` WHERE `id`='" . $this->id_prop . "'";
		$res = $this->db->db_Query($q);
		if($res){
		     $row = $this->db->db_FetchAssoc();
                    $this->dontUseSizes = $row['dontUseSizes'];
		}
            }else{
                $q = "SELECT
                        `" . TblModCatalogSprSizes . "`.*
                      FROM
                        `" . TblModCatalogSprSizes . "`,`" . TblModCatalog . "`
                      WHERE
                        `" . TblModCatalogSprSizes . "`.`level`='" . $this->SizeCatId. "'
                        AND `" . TblModCatalogSprSizes . "`.`lang_id`='" . $this->lang_id . "'
                      ORDER BY `" . TblModCatalogSprSizes . "`.`move`
                ";
                $res = $this->db->db_Query($q);
                $rows = $this->db->db_GetNumRows();
                $this->treeSizesData = array();
                for ($i = 0; $i < $rows; $i++) {
                    $row = $this->db->db_FetchAssoc();
                    $this->treeSizesData[$row['cod']] = $row;
                }

            }
            $q = "SELECT `" . TblModCatalogSprSizes . "`.*
                FROM `" . TblModCatalogSprSizes . "`
                    WHERE
                    `" . TblModCatalogSprSizes . "`.`lang_id`='" . $this->lang_id . "'
                        ORDER BY `" . TblModCatalogSprSizes . "`.`move`
                ";
            $res = $this->db->db_Query($q);
            $rows = $this->db->db_GetNumRows();
            $this->treeSizesDataAll = array();
            for ($i = 0; $i < $rows; $i++) {
                $row = $this->db->db_FetchAssoc();
		$this->treeSizesDataAll[$row['cod']] = $row;
            }
        }
    }

    function getSizeCateg($level=0) {
        $q = "SELECT `" . TblModCatalog . "`.*,
                    `" . TblModCatalogSprSizes . "`.`name` AS catSizeName
                            FROM `" . TblModCatalog . "`
                                LEFT JOIN `" . TblModCatalogSprSizes . "` ON (`" . TblModCatalogSprSizes . "`.`lang_id`='" . $this->lang_id . "' AND `" . TblModCatalogSprSizes . "`.`cod`=`" . TblModCatalog . "`.`id_size`)
                                WHERE `" . TblModCatalog . "`.`id`='" . $level . "'
                ";
        //echo $q;
        $res = $this->db->db_Query($q);
        $rows = $this->db->db_GetNumRows();
        $catSizeRow = $this->db->db_FetchAssoc();
        if ($rows == 1 && !empty($catSizeRow['id_size'])) {
            $this->SizeCatId = $catSizeRow['id_size'];
            $this->SizesCatName = $catSizeRow['catSizeName'];
        } else {
            if ($level != 0)
                $this->getSizeCateg($catSizeRow['level']);
        }
    }

    function getColorsOfProp($id=NULL) {

        // $this->colors[]=-1;
        if (empty($this->colors) || count($this->colors) == 0) {

            if (empty($this->id_prop) && !empty($id))
                $this->id_prop = $id;

            $q = "SELECT `" . TblModCatalogProp . "`.`colors` FROM `" . TblModCatalogProp . "`
            WHERE `" . TblModCatalogProp . "`.`id`='" . $this->id_prop . "'
            ";
            //echo $q;
            $res = $this->db->db_Query($q);
            if( !$res OR !$this->db->result ) return false;
            $row = $this->db->db_FetchAssoc();
            $rows = $this->db->db_GetNumRows();
            if (!empty($row['colors'])) {
                $this->colors = explode(",", trim($row['colors']));
                $isEmpty = false;
                foreach ($this->colors as $value) {
                    if ($value == -1)
                        $isEmpty = true;
                }

                if (!$isEmpty)
                    $this->colors[] = -1;
            }else {
                $this->colors = array();
                $this->colors[] = -1;
            }
            return $rows;
        }
    }

    function showImageDialog() {
        // $this->getColorsOfProp();
        ?>
        <tr>
            <td colspan="2">
                <? $this->showJS(); ?>
                <fieldset title="<?= $this->multi['FLD_COLORS'] ?>"><legend><img src='images/icons/colorsSmall.png' alt="<?= $this->multi['FLD_COLORS']; ?>" title="<?= $this->multi['FLD_COLORS']; ?>" border="0" /> <?= $this->multi['FLD_COLORS']; ?></legend>
                    <input type="hidden" name="idProp" id="idProp" value="<?= $this->id_prop ?>"/>

                    <input type="button" class="btn0" name="button" onclick="showDialog($('#idProp').val(),'showImageDialog',$('#colorsStr').val())" value="<?= $this->multi['FLD_ADD_REMOVE_COLORS_PROP'] ?>"/>
                    <input type="button" class="btn0" name="button" onclick="showDialog($('#idProp').val(),'colorsPositionDialog',$('#colorsStr').val())" value="<?= $this->multi['FLD_SORT_COLORS'] ?>"/>
                    <? $this->imageDialog() ?>
                </fieldset>
            </td>
        </tr>
        <?
    }

    function imageDialog() {
        ?>
        <div>

            <div id="colorsImageBoxContent">
                <? $tmpArr = $this->colors; ?>
                <input type="hidden" name="colorsStr" id="colorsStr" value="<?= implode(",", $tmpArr) ?>"/>
                <script type="text/javascript">
                    function initTabs(){
             var $colorsCount=<?= count($this->colors) ?>;
                        var $tabs = $( "#tabsColors" ).tabs({
                            cookie: {
                                // store cookie for a day, without, it would be a session cookie
                                expires: 1
                            }
                        });
                        $( ".sizesTabs" ).tabs({
                            cookie: {
                                expires: 1
                            }
                        });

                        $('form#form_mod_catalog').submit(function(){
                            $colors='';
                            $colorsCount=$('#colorsStr').val().split(',').length;
                            $( "#tabsColorsHeader li").each(function(i,elem){
                                //alert($(elem).html());
                                if(i!=$colorsCount-1)
                                    $colors+=$(elem).children("#colorIdIdent").val()+',';
                                else
                                    $colors+=$(elem).children("#colorIdIdent").val();
                            });
//                              alert($colors);
                            $('#colorsStr').val($colors);
                        });

                        $("<?
        for ($i = 0; $i <= count($this->colors); $i++) {
            if ($i == count($this->colors))
                echo "#sortableImage" . $i;
            else
                echo "#sortableImage" . $i . ",";
        }
        ?>").sortable({
                placeholder: "ui-state-highlight",
                update:function(event,ui){
                    $colorsCount=$('#colorsStr').val().split(',').length;
                    for(var $i=0;$i<=$colorsCount;$i++){
                        $sortedArr=$("#sortableImage"+$i).sortable('toArray');
                        $sortedStr=$sortedArr.join(',');
                        $("div#tabs-"+$i).children("#imagesOrder").val($sortedStr);
                    }

                }
            }).disableSelection();

            var $tab_items = $( "ul:first li", $tabs ).droppable({
                accept: ".sortableUl li",
                hoverClass: "ui-state-hover",
                drop: function( event, ui ) {
                    var $item = $( this );
                    var $list = $( $item.find( "a" ).attr( "href" ) ).find( ".sortableUl" );
                    $colorsCount=$('#colorsStr').val().split(',').length;
                    for(var $i=0;$i<=$colorsCount;$i++){
                        $("div#tabs-"+$i).children("#imagesOrder").val('');
                    }
                    ui.draggable.hide( "slow", function() {
                        $tabs.tabs( "select", $tab_items.index( $item ) );
                        $( this ).appendTo( $list ).show( "slow" );
                        $sortedArr=$("#sortableImage"+$tab_items.index( $item )).sortable('toArray');
                        $sortedStr=$sortedArr.join(',');
                        $("div#tabs-"+$tab_items.index( $item )).children("#imagesOrder").val($sortedStr);
                    });
                }
            });
        }

                    $(document).ready(function(){
                      initTabs();
                    });
                </script>
                <div class="tabsBoxPropColors">
                    <div id="tabsColors" >
                        <ul id="tabsColorsHeader">
                            <?
                            for ($c = 0; $c < count($this->colors); $c++) {
                                if(empty($this->colors[$c])) continue;
                                if ($this->colors[$c] != -1) {
                                    $img = NULL;
                                    if (!empty($this->treeColorsData[$this->colors[$c]]['img']))
                                        $img = "/images/spr/mod_catalog_spr_colors/" . $this->lang_id . "/" . $this->treeColorsData[$this->colors[$c]]['img'];
                                    ?>

                                    <li><input type="hidden" id="colorIdIdent" name="colorId" value="<?= $this->colors[$c] ?>"/><a href="#tabs-<?= $c ?>"><?
                    if (isset($img) && !empty($img))
                        echo $this->ShowCurrentImage($img, 'size_auto=17', 85, NULL, "border=0 alt='" . $this->treeColorsData[$this->colors[$c]]['name'] . "' title='" . $this->treeColorsData[$this->colors[$c]]['name'] . "'");
                    else
                        echo '<div style="float:left;width:17px;height:17px;background-color:#' . $this->treeColorsData[$this->colors[$c]]['colorsBit'] . '"></div>'
                                        ?>&nbsp;<?= $this->treeColorsData[$this->colors[$c]]['name'] ?></a></li>
                                            <?
                                        }else {
                                            $img_arr_without = $this->GetPicture($this->id_prop, 'back', NULL, NULL, true);
                                            ?><li><input type="hidden" id="colorIdIdent" name="colorId" value="-1"/><a style="line-height: 17px;" href="#tabs-<?= count($this->colors) ?>"><?= $this->multi['TXT_IMAGES_WITHIUT_COLORS'] ?></a></li><?
                }
            }
                                    ?>
                        </ul>
                        <?
                        for ($c = 0; $c < count($this->colors); $c++) {
                            if ($this->colors[$c] != -1) {
                                $img_arr = $this->GetPicture($this->id_prop, 'back', $this->colors[$c]);
                                ?><div id="tabs-<?= $c ?>" class="colorTabs">

                                    <input type="hidden" id="imagesOrder" class="imagesOrderAll" name="imagesOrder[<?= $this->colors[$c] ?>]" value=""/>
                                    <fieldset title="<?= $this->multi['FLD_IMAGES'] ?>"><legend><img src='images/icons/pictures.png' alt="<?= $this->multi['FLD_IMAGES']; ?>" title="<?= $this->multi['FLD_IMAGES']; ?>" border="0" /> <?= $this->multi['FLD_IMAGES']; ?></legend>

                                        <?
                                        if (count($img_arr) > 0) {
                                            ?><ul id="sortableImage<?= $c ?>" class="sortableUl"><?
                        $jtmp = 0;
                        for ($itmp = 0; $itmp < count($img_arr); $itmp++) {
                            if (isset($img_arr[$itmp])) {
                                echo '<li id="' . $img_arr[$itmp]['id'] . '"><div>' . $this->ShowCurrentImage($img_arr[$itmp]['id'], 'size_auto=100', 85, NULL, "border=0");
                                echo '&nbsp </div></li>';
                                $jtmp++;
                            }
                        }
                                            ?> </ul><?
                        echo '<br style="clear:both;"/><a href="' . $this->script . '&task=showpicture&id=' . $this->id_prop . '&colorsStr=' . implode(",", $tmpArr) . '">' . $this->multi['TXT_ADD_EDIT'] . '</a>' . ' [' . count($img_arr) . ']<br/><br/>';
                    } else {
                                            ?>
                                            <ul id="sortableImage<?= $c ?>" class="sortableUl"></ul><br style="clear:both;"/>
                                            <?
                                        }
                                        ?>
                                        <input type="hidden" name="MAX_FILE_SIZE" value="<?= MAX_IMAGE_SIZE; ?>">
                                        <?
                                        for ($i = 0; $i < UPLOAD_IMAGES_COUNT; $i++) {
                                            ?><INPUT TYPE="file" NAME="image[<?= $this->colors[$c] ?>][]" size="80" VALUE="<?= $this->img['name'][$i] ?>"/><br/><?
                    }
                                        ?>
                                    </fieldset>

                                    <?
				    if($this->settings['priceFromSizeColor']==1)
					$this->showSizesDialogPrice($this->colors[$c]);
                                    else
					$this->showSizesDialog($this->colors[$c]);

?>
                                </div><?
                                } else {
                                    ?><div id="tabs-<?= count($this->colors) ?>" class="colorTabs">

                                    <input type="hidden" id="imagesOrder" class="imagesOrderAll" name="imagesOrder[-1]" value=""/>
                                    <fieldset title="<?= $this->multi['FLD_IMAGES'] ?>"><legend><img src='images/icons/pictures.png' alt="<?= $this->multi['FLD_IMAGES']; ?>" title="<?= $this->multi['FLD_IMAGES']; ?>" border="0" /> <?= $this->multi['FLD_IMAGES']; ?></legend>
                                        <?
                                        if (count($img_arr_without) > 0) {
                                            ?><ul id="sortableImage<?= $c ?>" class="sortableUl"><?
                        $jtmp = 0;
                        for ($itmp = 0; $itmp < count($img_arr_without); $itmp++) {
                            if (isset($img_arr_without[$itmp])) {
                                echo '<li  id="' . $img_arr_without[$itmp]['id'] . '"><div>' . $this->ShowCurrentImage($img_arr_without[$itmp]['id'], 'size_auto=100', 85, NULL, "border=0");
                                echo '&nbsp</div></li>';
                                $jtmp++;
                            }
                        }
                                            ?> </ul><?
                            echo '<br style="clear:both;"/><a href="' . $this->script . '&task=showpicture&id=' . $this->id_prop . '&colorsStr=' . implode(",", $tmpArr) . '">' . $this->multi['TXT_ADD_EDIT'] . '</a>' . ' [' . count($img_arr_without) . ']<br/><br/>';
                        } else {
                                            ?>
                                            <ul id="sortableImage<?= $c ?>" class="sortableUl"></ul><br style="clear:both;"/>
                                            <?
                                        }
                                        ?>

                                        <input type="hidden" name="MAX_FILE_SIZE" value="<?= MAX_IMAGE_SIZE; ?>">
                                        <?
                                        for ($i = 0; $i < UPLOAD_IMAGES_COUNT; $i++) {//noimageidProp
                                            ?><INPUT TYPE="file" NAME="image[-1][]" size="80" VALUE="<?= $this->img['name'][$i] ?>"/><br/><?
                    }
                                        ?>
                                    </fieldset>

                                    <?
                                      if($this->settings['priceFromSizeColor']==1)
					$this->showSizesDialogPrice(-1);
                                    else
					$this->showSizesDialog(-1); ?>
                                </div><?
                                }
                            }
                            //if(count($img_arr_without)>0){
                            //   }
                            ?>
                    </div>
                </div>
            </div>

        </div>
        <?
    }

    function showColorsPositionDialog() {
        // $this->getColorsOfProp();
        //print_r($this->colors);
        $this->showJsAddCol();
        ?>
        <form id="sortableColorsForm" name="sortableColorsForm" enctype="multipart/form-data" action="#">
            <input type="hidden" name="id" value="<?= $this->id_prop ?>"/>
            <div class="colorsSortableBox">
                <ul id="sortableColors">
                    <?
                    for ($c = 0; $c < count($this->colors); $c++) {
                        if ($this->colors[$c] != -1) {
                            $img = NULL;
                            if (!empty($this->treeColorsData[$this->colors[$c]]['img']))
                                $img = "/images/spr/mod_catalog_spr_colors/" . $this->lang_id . "/" . $this->treeColorsData[$this->colors[$c]]['img'];
                            ?>

                            <li class="ui-state-default">
                                <input type="hidden" name="colorsStr[]" value="<?= $this->colors[$c] ?>"/>
                                <input type="hidden" id="colorIdIdent" name="colorId" value="<?= $this->colors[$c] ?>"/><a href="#tabs-<?= $c ?>"><?
                if (isset($img) && !empty($img))
                    echo $this->ShowCurrentImage($img, 'size_auto=17', 85, NULL, "border=0 alt='" . $this->treeColorsData[$this->colors[$c]]['name'] . "' title='" . $this->treeColorsData[$this->colors[$c]]['name'] . "'");
                else
                    echo '<div style="float:left;width:17px;height:17px;background-color:#' . $this->treeColorsData[$this->colors[$c]]['colorsBit'] . '"></div>'
                                ?>&nbsp;<?= $this->treeColorsData[$this->colors[$c]]['name'] ?></a></li>
                                    <?
                                }else {
                                    $img_arr_without = $this->GetPicture($this->id_prop, 'back', NULL, NULL, true);
                               ?><li class="ui-state-default">
                                    <input type="hidden" name="colorsStr[]" value="<?= $this->colors[$c] ?>"/>
                                    <input type="hidden" id="colorIdIdent" name="colorId" value="-1"/>
                                    <a style="line-height: 17px;" href="#tabs-<?= count($this->colors) ?>"><?= $this->multi['TXT_IMAGES_WITHIUT_COLORS'] ?></a>
                                </li><?
                }
            }
                            ?>
                </ul>
            </div>
        </form>
        <script type="text/javascript">
        $( "#sortableColors" ).sortable({
            placeholder: "ui-state-highlight",
            stop: function(event, ui) {
                 SendForm("/modules/mod_catalog/catalogcontent.backend.php?task=colorsPositionDialogFinish&module=21&lang_id=<?= $this->lang_id ?>",'sortableColorsForm');
            }
        });
        $( "#sortableColors" ).disableSelection();
        </script>
        <?
    }

    function showSizesDialogPrice($colorId) {

         if (( isset($this->settings['price_currency']) AND $this->settings['price_currency']=='1' ) ){
            $show_currencies = true;
            //$def_currency = $Currencies->GetDefaultCurrency();
            $def_currency = $this->Currencies->defCurrencyData['id'];
//            print_r($this->Currencies->defCurrencyData);
        }
        else
            $show_currencies = false;
        //echo '<br>$this->treeSizesData=';print_r($this->treeSizesData);
        //echo '<br>$this->treeSizesDataAll=';print_r($this->treeSizesDataAll);
        //$sizeCatNameTmp = '';
        //echo '<br>$this->SizeCatId='.$this->SizeCatId;
        if (!empty($this->treeSizesData) && !empty($this->SizeCatId) && $this->SizeCatId > 0) {
            //$sizeCatNameTmp = $this->SizesCatName;
            $propSizesArr = $this->treeSizesData;
        }else
            $propSizesArr = $this->treeSizesDataAll;
        ?>
        <div id="sizeBox" class="sizeDialogBox">
            <fieldset title="<?= $this->multi['FLD_SIZES'] . " (" . $this->SizesCatName . ")"; ?>">
                <legend>
                    <img style="margin-bottom: -7px;" src='images/icons/size.png' alt="<?= $this->multi['FLD_SIZES'] . " (" . $this->SizesCatName . ")"; ?>" title="<?= $this->multi['FLD_SIZES'] . " (" . $this->SizesCatName . ")"; ?>" border="0" /> <?= $this->multi['FLD_SIZES'] . " (" . $this->SizesCatName . ")"; ?>
		    <label>
			<?=$this->multi['FLD_USE_SIZES']?>:
			<input class="dontUseSizes"  type="checkbox" name="dontUseSizes[<?=$colorId?>]" value="1" <?if($this->dontUseSizes=='1') echo 'checked="checked"';?> onchange="
                        if($(this).attr('checked')=='checked'){
                            $('.dontUseSizes').attr('checked','checked');
//                            $('.sizesTabs').slideUp('fast');
                            $('.sizesTabs').each(function(){
                                $(this).slideUp('fast',function(){$(this).css('display','none');});

                            });
                            $('.noSizesPriceBox').each(function(){
                                $(this).slideDown('fast',function(){$(this).css('display','block');});
                            });
                        }else{
                            $('.dontUseSizes').removeAttr('checked');
//                            $('.sizesTabs').slideDown('fast');
                            $('.sizesTabs').each(function(){
                                $(this).slideDown('fast',function(){$(this).css('display','block');});
                            });
                            $('.noSizesPriceBox').each(function(){
                                $(this).slideUp('fast',function(){$(this).css('display','none');});
                            });
                        }"/>
		    </label>
		</legend>
                <div id="sizesTabs<?=$colorId?>"  class="sizesTabs <?if($this->dontUseSizes=='1') echo 'dontShow'?>">
                <ul class="sizesLi">
                    <?
                    $keys = array_keys($propSizesArr);
                    for ($i = 0; $i < count($keys); $i++) {

                        ?><li>
                                <?
                                if ($this->settings['sizesCount'] == 1) {

                                    ?>
                                    <a href="#sizesTabContent<?=$keys[$i]?>"><?= $propSizesArr[$keys[$i]]['name'] ?>: &nbsp;
                                    </a>
                                <? }else {
                                    $checked = "";
                                    if (isset($this->treeSizesProp[$this->id_prop][$colorId][$propSizesArr[$keys[$i]]['cod']]))
                                        $checked = 'checked';
                                    ?>
                                    <input class="sizeCheckBox" type='checkbox' <?= $checked ?> name="sizes[<?= $colorId ?>][<?=$propSizesArr[$keys[$i]]['cod']?>]" value="<?= $propSizesArr[$keys[$i]]['cod'] ?>"/>
                                    <a href="#sizesTabContent<?=$keys[$i]?>">
                                   <?= $propSizesArr[$keys[$i]]['name'] ?></a>
                                <? } ?>
                        </li>
                        <?
                    }
                    ?></ul><?
                    for ($i = 0; $i < count($keys); $i++){

                    ?>
                        <div id="sizesTabContent<?=$keys[$i]?>">
                            <?
                            $checked = "";
                                if (isset($this->treeSizesProp[$this->id_prop][$colorId][$propSizesArr[$keys[$i]]['cod']])){
                                    $checked = 'checked';
                                    $price_currency=$this->treeSizesProp[$this->id_prop][$colorId][$propSizesArr[$keys[$i]]['cod']]['price_currency'];
                                    $price=$this->treeSizesProp[$this->id_prop][$colorId][$propSizesArr[$keys[$i]]['cod']]['price'];
                                    $old_price=$this->treeSizesProp[$this->id_prop][$colorId][$propSizesArr[$keys[$i]]['cod']]['old_price'];
                                }else{
                                    $price_currency=$def_currency;
                                    $price=0;
                                    $old_price=0;
                                }
                            if ($this->settings['sizesCount'] == 1) {

                                if ($checked)
                                        $val = $this->treeSizesProp[$this->id_prop][$colorId][$propSizesArr[$keys[$i]]['cod']]['cnt'];
                                    else
                                        $val = 0;
                                ?>
                             <?=$this->multi['FLD_COUNT_CURRENT_SIZE']?>:<input type='text'  name="sizes[<?= $colorId ?>][<?= $propSizesArr[$keys[$i]]['cod'] ?>]" value="<?= $val ?>"/><br/>
                            <?}?>
                             <?=$this->multi['FLD_PRICE']?>:
                             <?
                              if( empty($price_currency) || $price_currency==0 ) $price_currency = $def_currency;
                               $this->Form->TextBox( "priceSize[". $colorId ."][". $propSizesArr[$keys[$i]]['cod']."]", stripslashes($price), 10 );
                                if($show_currencies) $this->Form->Select($this->Currencies->listShortNames, "price_currencySize[". $colorId ."][". $propSizesArr[$keys[$i]]['cod']."]", $price_currency);
                             ?>
                             <br/>
                             <?=$this->multi["FLD_OLD_PRICE"]?>:
                             <?
                              $this->Form->TextBox( "old_priceSize[". $colorId ."][". $propSizesArr[$keys[$i]]['cod']."]", stripslashes($old_price), 10 );
                             ?>
                             <br/>
                        </div>
                        <?
                    }
                   /* if (!empty($this->treeSizesData) && !empty($this->SizeCatId) && $this->SizeCatId > 0) {
                        $keys = array_keys($this->treeSizesDataAll);
                        for ($i = 0; $i < count($keys); $i++) {
                            if (!isset($this->treeSizesData[$keys[$i]]) && isset($this->treeSizesProp[$this->id_prop][$colorId][$this->treeSizesDataAll[$keys[$i]]['cod']])) {
                                $checked = 'checked';
                                ?><li>
                                    <label>
                                        <?
                                        if ($this->settings['sizesCount'] == 1) {
                                            if ($checked)
                                                $val = $this->treeSizesProp[$this->id_prop][$colorId][$this->treeSizesDataAll[$keys[$i]]['cod']]['cnt'];
                                            else
                                                $val = 0;
                                            ?>
                                            <?= $this->treeSizesDataAll[$keys[$i]]['name'] ?>: &nbsp;
                                            <input type='text'  name="sizes[<?= $colorId ?>][<?= $this->treeSizesDataAll[$keys[$i]]['cod'] ?>]" value="<?= $val ?>"/>
                                        <? }else { ?>
                                            <input type='checkbox' <?= $checked ?> name="sizes[<?= $colorId ?>][]" value="<?= $this->treeSizesDataAll[$keys[$i]]['cod'] ?>"/>
                                            <?= $this->treeSizesDataAll[$keys[$i]]['name'] ?>
                                        <? } ?>
                                    </label>
                                </li>
                                <?
                            }
                        }
                    }*/
                    ?>

                </div>
                <div id="noSizesPriceBox<?=$colorId?>" class="noSizesPriceBox <?if($this->dontUseSizes!='1') echo 'dontShow'?>">
                    <?
                    if (isset($this->treeSizesProp[$this->id_prop][$colorId][-1])){
                        $checked = 'checked';
                        $price_currency=$this->treeSizesProp[$this->id_prop][$colorId][-1]['price_currency'];
                        $price=$this->treeSizesProp[$this->id_prop][$colorId][-1]['price'];
                        $old_price=$this->treeSizesProp[$this->id_prop][$colorId][-1]['old_price'];
                    }else{
                        $price_currency=$def_currency;
                        $price=0;
                        $old_price=0;
                    }
                    if ($this->settings['sizesCount'] == 1) {

                    if ($checked)
                            $val = $this->treeSizesProp[$this->id_prop][$colorId][-1]['cnt'];
                        else
                            $val = 0;
                    ?>
                    <?=$this->multi['FLD_COUNT_CURRENT_SIZE']?>:<input type='text'  name="sizes[<?= $colorId ?>][-1]" value="<?= $val ?>"/><br/>
                <?}?>
                    <?=$this->multi['FLD_PRICE']?>:
                    <?
                    if( empty($price_currency) || $price_currency==0 ) $price_currency = $def_currency;
                    $this->Form->TextBox( "priceSize[". $colorId ."][-1]", stripslashes($price), 10 );
                    if($show_currencies) $this->Form->Select($this->Currencies->listShortNames, "price_currencySize[". $colorId ."][-1]", $price_currency);
                    ?>
                    <br/>
                    <?=$this->multi["FLD_OLD_PRICE"]?>:
                    <?
                    $this->Form->TextBox( "old_priceSize[". $colorId ."][-1]", stripslashes($old_price), 10 );
                    ?>
                    <br/>
                </div>
            </fieldset>
        </div>
        <?
    }

    function showSizesDialog($colorId) {

        //print_r($this->treeSizesData)
        //$sizeCatNameTmp = '';
        if (!empty($this->treeSizesData) && !empty($this->SizeCatId) && $this->SizeCatId > 0) {
            //$sizeCatNameTmp = $this->SizesCatName;
            $propSizesArr = $this->treeSizesData;
        }else
            $propSizesArr = $this->treeSizesDataAll;
        ?>
        <div id="sizeBox" class="sizeDialogBox">
            <fieldset title="<?= $this->multi['FLD_SIZES'] . " (" . $this->SizesCatName . ")"; ?>">
                <legend>
                    <img style="margin-bottom: -7px;" src='images/icons/size.png' alt="<?= $this->multi['FLD_SIZES'] . " (" . $this->SizesCatName . ")"; ?>" title="<?= $this->multi['FLD_SIZES'] . " (" . $this->SizesCatName . ")"; ?>" border="0" /> <?= $this->multi['FLD_SIZES'] . " (" . $this->SizesCatName . ")"; ?>
		    <label>
			<?=$this->multi['FLD_USE_SIZES']?>:
			<input class="dontUseSizes"  type="checkbox" name="dontUseSizes[<?=$colorId?>]" value="1" <?if($this->dontUseSizes=='1') echo 'checked="checked"';?> onchange="if($(this).attr('checked')=='checked'){ $('.dontUseSizes').attr('checked','checked'); $('.sizesLi').hide();}else{$('.dontUseSizes').removeAttr('checked'); $('.sizesLi').show();}"/>
		    </label>
		</legend>
                <ul class="sizesLi <?if($this->dontUseSizes=='1') echo 'dontShow'?>">
                    <?
                    $keys = array_keys($propSizesArr);
                    for ($i = 0; $i < count($keys); $i++) {
                        $checked = "";
                        if (isset($this->treeSizesProp[$this->id_prop][$colorId][$propSizesArr[$keys[$i]]['cod']]))
                            $checked = 'checked';
                        ?><li>
                            <label>
                                <?
                                if ($this->settings['sizesCount'] == 1) {
                                    if ($checked)
                                        $val = $this->treeSizesProp[$this->id_prop][$colorId][$propSizesArr[$keys[$i]]['cod']]['cnt'];
                                    else
                                        $val = 0;
                                    ?>
                                    <?= $propSizesArr[$keys[$i]]['name'] ?>: &nbsp;
                                    <input type='text'  name="sizes[<?= $colorId ?>][<?= $propSizesArr[$keys[$i]]['cod'] ?>]" value="<?= $val ?>"/>
                                <? }else { ?>
                                    <input type='checkbox' <?= $checked ?> name="sizes[<?= $colorId ?>][<?=$propSizesArr[$keys[$i]]['cod']?>]" value="<?= $propSizesArr[$keys[$i]]['cod'] ?>"/>
                                    <?= $propSizesArr[$keys[$i]]['name'] ?>
                                <? } ?>
                            </label>
                        </li>
                        <?
                    }
                    if (!empty($this->treeSizesData) && !empty($this->SizeCatId) && $this->SizeCatId > 0) {
                        $keys = array_keys($this->treeSizesDataAll);
                        for ($i = 0; $i < count($keys); $i++) {
                            if (!isset($this->treeSizesData[$keys[$i]]) && isset($this->treeSizesProp[$this->id_prop][$colorId][$this->treeSizesDataAll[$keys[$i]]['cod']])) {
                                $checked = 'checked';
                                ?><li>
                                    <label>
                                        <?
                                        if ($this->settings['sizesCount'] == 1) {
                                            if ($checked)
                                                $val = $this->treeSizesProp[$this->id_prop][$colorId][$this->treeSizesDataAll[$keys[$i]]['cod']]['cnt'];
                                            else
                                                $val = 0;
                                            ?>
                                            <?= $this->treeSizesDataAll[$keys[$i]]['name'] ?>: &nbsp;
                                            <input type='text'  name="sizes[<?= $colorId ?>][<?= $this->treeSizesDataAll[$keys[$i]]['cod'] ?>]" value="<?= $val ?>"/>
                                        <? }else { ?>
                                            <input type='checkbox' <?= $checked ?> name="sizes[<?= $colorId ?>][<?= $this->treeSizesDataAll[$keys[$i]]['cod']?>" value="<?= $this->treeSizesDataAll[$keys[$i]]['cod'] ?>"/>
                                            <?= $this->treeSizesDataAll[$keys[$i]]['name'] ?>
                                        <? } ?>
                                    </label>
                                </li>
                                <?
                            }
                        }
                    }
                    ?>
                </ul>
            </fieldset>
        </div>
        <?
    }

    function ColorsDialog() {
        $this->showJsAddCol();
        //$this->getColorsOfProp();
        ?>
        <div>
            <script type="text/javascript">
            $(document).ready(function(){
                $( "#tabs" ).tabs();
            });
            </script>
            <div class="loader" id="loader"><?= $this->multi['TXT_SAVE_TEXT_LOADER'] ?></div>
            <div class="tabsBox">
                <div id="tabs" clas>
                    <ul>
                        <li><a href="#tabs-1"><?= $this->multi['TXT_COLORS_USES'] ?></a></li>
                        <li><a href="#tabs-2"><?= $this->multi['TXT_COLORS_ADD'] ?></a></li>
                    </ul>
                    <div id="tabs-1" class="colorTabs">
                        <? $this->showPropColorsLayout(); ?>
                    </div>
                    <div id="tabs-2" class="colorTabs">
                        <? $this->showNotPropColorsLayout(); ?>
                    </div>

                </div>
            </div>
        </div>
        <?
    }

    function showPropColorsLayout() {
        $rows = 0;
        if (count($this->colors) > 0) {
            $q = "SELECT `" . TblModCatalogSprColors . "`.* FROM `" . TblModCatalogSprColors . "`
                WHERE `lang_id`=" . $this->lang_id . "
                ";


            $q.=" AND `" . TblModCatalogSprColors . "`.`cod` IN (" . implode(",", $this->colors) . ") ";
            $res = $this->db->db_Query($q);
            $rows = $this->db->db_GetNumRows();
        }
        ?>
        <div>
            <div id="PropColors" style="height: 100%">
                <form id="delColorsFromProp" name="delColorsFromProp" enctype="multipart/form-data" action="#">
                    <input type="hidden" name="colorsStr" id="colorsStr" value="<?= implode(",", $this->colors) ?>"/>
                    <input type="hidden" name="task" value="delColorsFromProp"/>
                    <input type="hidden" name="id" value="<?= $this->id_prop ?>"/>
                    <?
                    for ($i = 0; $i < $rows; $i++) {
                        $img = NULL;
                        $row = $this->db->db_FetchAssoc();
                        if (!empty($row['img']) && isset($row['img']))
                            $img = "/images/spr/mod_catalog_spr_colors/" . $this->lang_id . "/" . $row['img'];
                        ?>
                        <div class="singleColorBlock">
                            <div class="image-block" style="background-color: #<?= $row['colorsBit'] ?>" onclick="checkCheckBox('#checkBoxColors<?= $row['cod'] ?>')">
                                <? if (isset($img))
                                    echo $this->ShowCurrentImage($img, 'size_auto=100', 85, NULL, "border=0 alt='" . $row['name'] . "' title='" . $row['name'] . "'"); ?></div>
                            <input  type="checkbox" id="checkBoxColors<?= $row['cod'] ?>" class="checkBoxColors" name="delColor[<?= $row['cod'] ?>]" value="<?= $row['cod'] ?>" /><? echo $row['name']; ?>
                        </div>
                        <?
                    }
                    ?>
                    <div class="addColorBox">
                        <? if (count($this->colors) > 0) { ?>
                            <input type="button" class="btn0" value="<?= $this->multi['TXT_BTN_DEL_COLORS'] ?>" onclick="SendForm('/modules/mod_catalog/catalogcontent.backend.php?module=21&lang_id=<?= $this->lang_id ?>','delColorsFromProp')"/>
                        <? } ?>
                    </div>
                </form>
            </div>
        </div>
        <?
    }

    function showNotPropColorsLayout() {
        $q = "SELECT `" . TblModCatalogSprColors . "`.* FROM `" . TblModCatalogSprColors . "`
            WHERE `lang_id`=" . $this->lang_id . "
            ";
        if (count($this->colors) > 0) {
            $q.=" AND `" . TblModCatalogSprColors . "`.`cod` NOT IN (" . implode(",", $this->colors) . ") ";
        }

        $res = $this->db->db_Query($q);
        $rows = $this->db->db_GetNumRows();
        ?>
        <div>
            <div id="notPropColors">
                <form id="addColorsToProp" name="addColorsToProp" enctype="multipart/form-data" action="#">
                    <input type="hidden" name="colorsStr" id="colorsStr" value="<?= implode(",", $this->colors) ?>"/>
                    <input type="hidden" name="task" value="addColorToProp"/>
                    <input type="hidden" name="id" value="<?= $this->id_prop ?>"/>
                    <?
                    for ($i = 0; $i < $rows; $i++) {
                        $img = NULL;
                        $row = $this->db->db_FetchAssoc();
                        if (!empty($row['img']) && isset($row['img']))
                            $img = "/images/spr/mod_catalog_spr_colors/" . $this->lang_id . "/" . $row['img'];
                        ?>
                        <div class="singleColorBlock">
                            <div class="image-block" style="background-color: #<?= $row['colorsBit'] ?>" onclick="checkCheckBox('#checkBoxColors<?= $row['cod'] ?>')">
                                <? if (isset($img))
                                    echo $this->ShowCurrentImage($img, 'size_auto=100', 85, NULL, "border=0 alt='" . $row['name'] . "' title='" . $row['name'] . "'"); ?></div>
                            <input  type="checkbox" id="checkBoxColors<?= $row['cod'] ?>" class="checkBoxColors" name="newColor[<?= $row['cod'] ?>]" value="<?= $row['cod'] ?>" /><? echo $row['name']; ?>
                        </div>
                        <?
                    }
                    ?>
                    <div class="addColorBox">
                        <? if ($rows > 0) { ?>
                            <input type="button" class="btn0" value="<?= $this->multi['TXT_BTN_ADD_COLORS'] ?>" onclick="SendForm('/modules/mod_catalog/catalogcontent.backend.php?module=21&lang_id=<?= $this->lang_id ?>','addColorsToProp')"/>
                        <? } ?>
                    </div>
                </form>
            </div>
            <div id="newIdAndTaskBox">
                <input type="hidden" name="new_id" id="new_id_hidden" value="<?= $this->id_prop ?>"/>
                <input type="hidden" name="new_task" id="new_task_hidden" value="edit"/>
            </div>
        </div>
        <?
    }

    function addNewColorsToDatabase() {
        //$this->getColorsOfProp();


        if (isset($this->newColor) && count($this->newColor)) {

            foreach ($this->newColor as $value) {
                $this->colors[] = $value;
            }
            print_r($this->colors);
            //$this->newColor=$this->colors;
            if (isset($this->id_prop)) {
                $addColors = implode(',', $this->colors);

                $q = "UPDATE `" . TblModCatalogProp . "` SET
                `colors`='" . $addColors . "'
                 WHERE `id`='" . $this->id_prop . "'
                ";

                $res = $this->db->db_Query($q);
            }
        }

        //$this->getColorsOfProp();
        $this->showPropColorsLayout();
        $this->showNotPropColorsLayout();
        $this->imageDialog();
    }

    function DelColorsFromProp() {
        // $this->getColorsOfProp();

        if (isset($this->delColor) && count($this->delColor)) {
            foreach ($this->delColor as $delKey => $delCol) {
                if (isset($this->id_prop)) {
                    $q = "UPDATE `" . TblModCatalogPropImg . "` SET
                            `colid`='-1'
                             WHERE `colid`='" . $delCol . "' AND `id_prop`='" . $this->id_prop . "'
                            ";
                    $res = $this->db->db_Query($q);
                }
                for ($i = 0; $i < count($this->colors); $i++) {
                    if ($delCol == $this->colors[$i])
                        unset($this->colors[$i]);
                }
                $this->colors = explode(',', implode(',', $this->colors));
            }


            if (isset($this->id_prop)) {
                $colorsIdStr = implode(',', $this->colors);
                $q = "UPDATE `" . TblModCatalogProp . "` SET
                `colors`='" . $colorsIdStr . "'
                 WHERE `id`='" . $this->id_prop . "'
                ";

                $res = $this->db->db_Query($q);
            }
            // $this->getColorsOfProp();
            $this->showPropColorsLayout();
            $this->showNotPropColorsLayout();
            $this->imageDialog();
        }
    }

    function showJS() {
        ?>
        <script type="text/javascript">
        function showDialog($prop_id,task,colorsStr){
            var onclose="";
            //sortableColorsForm
//            if(task=='colorsPositionDialog'){
//                onclose=function(){
//
//                }
//            }
            $.fancybox({
                href:"/modules/mod_catalog/catalogcontent.backend.php?task="+task+"&module=21&lang_id=<?= $this->lang_id ?>&id="+$prop_id+"&colorsStr="+colorsStr,
                'overlayColor':'#2A2A2A'
            });
        }
        </script>
        <?
    }

    function showJsAddCol() {
        ?>
        <script type="text/javascript">
        function checkCheckBox($id){
            $checkObj=$($id);
            $checkObj.attr('checked',!$checkObj.attr('checked'));
        }



        function SendForm(addr,form){
            $.ajax({
                type: "POST",
                data: $('#'+form).serialize(),
                url: addr+"&id=<?=$this->id_prop?>",

                success:function(msg){
                    $("#loader").css("display","none");
                    $("#PropColors").html($("#PropColors",msg).html());
                    $("#notPropColors").html($("#notPropColors",msg).html());
                    $("#colorsImageBoxContent").html($("#colorsImageBoxContent").html()+$("#newIdAndTaskBox",msg).html());
                    $("#idProp").val($("#new_id_hidden",msg).val());
                    //$("#colorsStr").val($("#colorsStr",msg).val());
                    $("#colorsImageBoxContent").html($("#colorsImageBoxContent",msg).html());
                    initTabs();
                    //alert(msg);//idProp new_id_hidden
                },
                beforeSend: function() {
                    $("#loader").css("display","block");
                }
            });
        }
        </script>
        <?
    }

}
?>

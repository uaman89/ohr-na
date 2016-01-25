<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bogdan
 * Date: 21.08.13
 * Time: 16:53
 * To change this template use File | Settings | File Templates.
 */
class CatalogParamLayout extends CatalogModel
{
    /*============================   start filter panel  =========================*/

    // ================================================================================================
    // Function : showSortPanel()
    // Date : 21.08.2013
    // Returns : Content sortbar
    // Description : Generates link for sorting and sorting building panel
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function showSortPanel(){
        $link_href = $this->makeParamLink('sore');
        return View::factory('/modules/mod_catalog/templates/param/tpl_sore_panel.php')
            ->bind('name_block',$this->multi['TXT_SORT_FO'])
            ->bind('name_elem1',$this->multi['TXT_PRICE_ADD'])
            ->bind("link_href",$link_href)
            ->bind('name_elem2',$this->multi['TXT_PRICE_MINES'])
            ->bind("asc_desc",$this->asc_desc)
            ->bind('catLink', $this->catLink)
            ->bind('srt', $this->srt);
    }

    // ================================================================================================
    // Function : showComparePanel()
    // Date : 21.08.2013
    // Returns : panel comparison
    // Description : Assembles a panel of comparison
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function showComparePanel(){
        if(empty($this->id_cat) || !isset($_COOKIE['compare'.$this->id_cat])
            || empty($_COOKIE['compare'.$this->id_cat])) return false;
        $str_prop = $_COOKIE['compare'.$this->id_cat];
        if($str_prop[0]==',') $str_prop = substr($str_prop,1);
        if(empty($str_prop)) return false;
        $arr_content = $this->contentForCompare($str_prop);
        if(empty($arr_content)) return false;
        $keys = array_keys($arr_content);
        $count = count($arr_content);
        $arr_poradok = explode(',',$str_prop);
        return View::factory('/modules/mod_catalog/templates/param/tpl_compare_panel.php')
            ->bind('count',$count)
            ->bind('arr',$arr_content)
            ->bind('keys',$keys)
            ->bind('linkCat',$this->catLink)
            ->bind('id_cat',$this->id_cat)
            ->bind('arr_poradok',$arr_poradok);
    }


    // ================================================================================================
    // Function : showComparePanel()
    // Date : 21.08.2013
    // Returns : Compare Products
    // Description : Displays items to compare
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function compareProp(){
        $nameCookie = 'compare'.$this->id_cat;
        if(!isset($_COOKIE[$nameCookie]) || empty($_COOKIE[$nameCookie])){
            echo '<span class="empty-compare">Нет товаров для сравнения!</span>';
            return false;
        }
        $srt_prop = $_COOKIE[$nameCookie];
        $arr_prop = $this->arrForCompareProp($srt_prop);
        if(empty($arr_prop)) return false;
        $arr_param = $this->arrForCompareParam($srt_prop);
        if(!empty($arr_param) && is_array($arr_param)){
            $keys_param = array_keys($arr_param);
            $count_param = sizeof($keys_param);
            $compare_param = View::factory('/modules/mod_catalog/templates/compare/compare_param.php')
                ->bind('arr_move', $arr_move)
                ->bind('arr_param', $arr_param)
                ->bind('count',$count)
                ->bind('count_param',$count_param)
                ->bind('keys_param',$keys_param);
        }else{
            $compare_param = '';
        }
        $arr_move = explode(',',$srt_prop);
        $count = count($arr_move);
//        echo '$srt_prop='.$srt_prop;
        echo View::factory('/modules/mod_catalog/templates/compare/compare_prop.php')
            ->bind('arr_move', $arr_move)
            ->bind('arr_prop', $arr_prop)
            ->bind('count',$count)
            ->bind('Catalog',$this)
            ->bind('compare_param',$compare_param);

    }

    // ================================================================================================
    // Function : showFilter()
    // Date : 21.08.2013
    // Returns : Displays the filter panel
    // Description : Collect all the filter panel and displays them
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function showFilter() {

        $this->isContent = $this->IsContent($this->id_cat);
        if(isset($this->isContent) && $this->isContent>0){

            $this->initParamFilter();

            if(isset($this->settings['cat_params']) && $this->settings['cat_params']==1){
                $str = $this->ShowAllFilters();
            }else{
                $str = '';
            }

            if(isset($this->settings['manufac']) && $this->settings['manufac']==1){
                $strBrand = $this->ShowBrandFilter();
            }else{
                $strBrand = '';
            }
//          echo '$strBrand='.$strBrand;
            if(isset($this->settings['price']) && $this->settings['price']==1){
                $strPrice = $this->ShowPriceFilter();
            }else{
                $strPrice = '';
            }
//            echo '$strPrice='.$strPrice;

            if(!empty($str) || !empty($strPrice) || !empty($strBrand)){
                $strSel = $this->ShowSelectedFilters();
                echo View::factory('/modules/mod_catalog/templates/param/tpl_show_filter.php')
                    ->bind('name_block', $this->multi['TXT_FILTR_PROP'])
                    ->bind('catLink', $this->catLink)
                    ->bind('strSel', $strSel)
                    ->bind('str', $str)
                    ->bind('strBrand', $strBrand)
                    ->bind('strPrice', $strPrice);
            }

        }
    }//end of function showFilter()


    function getCategorySubMenu($id_cat) {

        $q = "SELECT * FROM `mod_menu` WHERE `category`= ".$id_cat." AND `lang_id`=".$this->lang_id." ORDER BY `move` ";

        $res = $this->db->db_Query($q);

        if(!$res) {
            return false;
        }

        $rows = $this->db->db_GetNumRows();

        if($rows>0) {
            $arrData = array();
            for($i=0; $i<$rows; $i++ ) {
                $arrData[] = $this->db->db_FetchAssoc();
            }

            return $arrData;
        }

        return false;


    }

    function getTextForSubLevel($id_cat) {
        $q = "SELECT * FROM `mod_menu_text` WHERE `category`= ".$id_cat." AND `lang_id`=".$this->lang_id." ORDER BY `move` ";

        $res = $this->db->db_Query($q);

        if(!$res) {
            return false;
        }

        $rows = $this->db->db_GetNumRows();

        if($rows > 0 ) {
            return $this->db->db_FetchAssoc();
        }

        return false;


    }

    function showFilterMenu($id_cat) {
            $dataMenu = $this->getCategorySubMenu($id_cat);
            $dataMenuText = $this->getTextForSubLevel($id_cat);

            if(!$dataMenu && !$dataMenuText) {
                return false;
            }





        echo View::factory('/modules/mod_catalog/templates/param/tpl_show_filter_menu.php')
            ->bind('dataMenu', $dataMenu)
            ->bind('dataMenuText', $dataMenuText);


//        $this->isContent = NULL;
//        $this->params_row = NULL;
//        $this->catLink = NULL;
//        $this->countOfPropNoLimit = NULL;
//        $this->propArrNoLimit = NULL;
//        //$this->param_arr = "";
//        //$this->arr_current_img_params_value=NULL;
//        $str = "";
//        $this->id_cat  = $cat;

    }



    // ================================================================================================
    // Function : ShowAllFilters()
    // Date : 21.08.2013
    // Returns : panel filters
    // Description : Builds a filter panel of the parameters
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function ShowAllFilters()
    {
        $str = NULL;
        $param_str = NULL;
        $param = NULL;
        $filtr = NULL;

        //echo '<br/>$this->propArrNoLimit: ';
        //var_dump($this->propArrNoLimit);
        $IdOfProps = $this->makeIdPropStr($this->propArrNoLimit[0]);

        //var_dump($this->arr_current_img_params_value);
        if (!empty($this->sort))
            $sorting = '&sort=' . $this->sort . '&asc_desc=' . $this->asc_desc . '&exist=' . $this->exist;

        if (!empty($this->from)  and !empty($this->to))
            $this->priceLevels = '&from=' . $this->from . '&to=' . $this->to;

        $n = count($this->params_row);
        //var_dump($this->params_row);
        //var_dump($this->propArrNoLimit);

        if($n==0) return false;
        for ($i = 0; $i < $n; $i++) {
            $row = $this->params_row[$i];
            if ($row['modify'] != 1) //Отображать в блоке параметров
            //continue;
            $val = NULL;
            $paramName = stripcslashes($row['name']);
            $id_param = $row['id'];

            $str .= '<div class="paramBlock"><div class="paramName"><div class="h1main param-title"><div class="line2"></div><span>' . $paramName . '</span></div></div>';

            $prefix = stripcslashes($row['prefix']);
            $sufix = stripcslashes($row['sufix']);
            $str .= '<div class="paramKey">';

            switch ($row['type']) {
                case '1':
                    $IdOfProps1 = $IdOfProps;
                    if (isset($this->propArrNoLimit[$id_param])) {
                        $IdOfProps1 = $this->makeIdPropStr($this->propArrNoLimit[$id_param]);
                    }
                    $valStart = $this->GetParamsPropValType1($id_param,$IdOfProps1);
                    //var_dump($valStart);
                    $val = $valStart;
                    if(isset($this->arr_current_img_params_value[$id_param]) && !empty($this->arr_current_img_params_value[$id_param])){
                        $sel_val = $this->arr_current_img_params_value[$id_param];
                        $val = explode('|',$sel_val);
                    }

                    if(empty($val[0])){
                        $val[0] = 0;
                        $val[1] = 0;
                    }
                    if(empty($valStart[0])){
                        $valStart[0] = 0;
                        $valStart[1] = 0;
                    }

                    $paramLink = $this->makeParamLink($this->params_row[$i]['id']);

                    ob_start();
                    $id_elem = PARAM_VAR_NAME . PARAM_VAR_SEPARATOR.$id_param;
                    ?><div class="param-key-one-item">
                    <div class="param-key-fon-fo-slider">
                        <div id="slider<?=$id_elem?>"></div>
                    </div>
                    <div class="param-key-input-type1">
                        <label form="<?=$id_elem?>_0"><?=$this->multi['FLD_FROM']?></label>
                        <input type="text" name="<?=$id_elem?>_0" id="<?=$id_elem?>_0" value="<?=$val[0]?>"/>

                        <label form="<?=$id_elem?>_1"><?=$this->multi['TXT_TO']?></label>
                        <input type="text" name="<?=$id_elem?>_1" id="<?=$id_elem?>_1" value="<?=$val[1]?>"/>

                        <label><?=$sufix?></label>
                    </div>
                    <div class="param-key-submit">
                        <input type="submit" value="Ок" name="paramType1"
                               onclick="return gelPropConetntByTypeParam1('<?=$this->catLink?>','<?=$paramLink?>','<?=$id_elem?>');" />
                    </div>
                    <script type="text/javascript">
                        //<![CDATA[
                        jQuery("#slider<?=$id_elem?>").slider({
                            min: <?=$valStart[0]?>,
                            max: <?=$valStart[1]?>,
                            values: [<?=$val[0]?>,<?=$val[1]?>],
                            range: true,
                            stop: function(event, ui) {
                                jQuery("input#<?=$id_elem?>_0").val(jQuery("#slider<?=$id_elem?>").slider("values",0));
                                jQuery("input#<?=$id_elem?>_1").val(jQuery("#slider<?=$id_elem?>").slider("values",1));
                            },
                            slide: function(event, ui){
                                jQuery("input#<?=$id_elem?>_0").val(jQuery("#slider<?=$id_elem?>").slider("values",0));
                                jQuery("input#<?=$id_elem?>_1").val(jQuery("#slider<?=$id_elem?>").slider("values",1));
                            }
                        });
                        //]]>
                    </script>
                    </div>
                    <div class="filters-off">
                    <a href="<?=$this->catLink.$paramLink?>" title="<?=$this->multi['TXT_KILL_FILTER']?>"
                       onclick="return gelPropConetnt('<?=$this->catLink?>','<?=$paramLink?>');"><?=$this->multi['TXT_KILL_FILTER']?></a>
                    </div><?
                    $str .= ob_get_clean();
                    break;
                case '3':
                case '4':
                    $IdOfProps1 = $IdOfProps;

                    if (isset($this->propArrNoLimit[$id_param])) {
                        $IdOfProps1 = $this->makeIdPropStr($this->propArrNoLimit[$id_param]);
                    }

                    //echo '<br>$IdOfProps1='.$IdOfProps1;

                    $val = $this->GetParamsPropVal($this->lang_id, $row['id_categ'], $id_param, $IdOfProps1);
                    if (is_array($val)) {
                        foreach ($val as $k => $v) {
                            // Форматированный вывод текста либо ссылки параметра
                            $checked = false;
                            if (is_array($this->arr_current_img_params_value) &&
                                isset($this->arr_current_img_params_value[$row['id']])) {
                                $row_sel_val_this_param = explode(',',$this->arr_current_img_params_value[$row['id']]);
                                if(in_array($k,$row_sel_val_this_param)){
                                    $checked = true;
                                }
                            }
                            $paramLink = $this->makeParamLink($this->params_row[$i]['id'], $v['cod']);

                            $checkedClass = '';
                            $checkedInput = '';
                            $disabled = '';
                            $showA = true;
                            $onclick = " onclick=\"return gelPropConetnt('".$this->catLink."','".$paramLink."');\"";

                            if (!isset($v['countOfProp']) || $v['countOfProp']==0) {
                                $checkedClass .= ' param-no-selected';
                                $showA = false;
                                $disabled = ' disabled';
                            }

                            if ($checked == true) {
                                $checkedClass = ' param-selected';
                                $showA = true;
                                $checkedInput = ' checked';
                            }

                            if(!$showA){
                                $onclick = '';
                            }
                            ob_start();
                            ?><div class="param-key-one-item<?=$checkedClass?>">
                            <div class="param-key-checkbox">
                                <div class="filter-checkbox"<?=$onclick?>
                                     id="checBox_<?=$id_param.'_'.$k?>_block"></div>
                                <input type="checkbox" name="<?=PARAM_VAR_NAME.PARAM_VAR_SEPARATOR.$id_param?>" value="<?=$k?>"
                                    <?=$checkedInput?> <?=$disabled?>
                                       id="checBox_<?=$id_param.'_'.$k?>_Input"/>
                                <script type="text/javascript">

                                        $('#checBox_<?=$id_param.'_'.$k?>_Input').hide();
                                        $('#checBox_<?=$id_param.'_'.$k?>_block').show();


                                </script>
                            </div>
                            <div class="param-key-label">
                                <?if($showA){
                                ?><a href="<?=$this->catLink . $paramLink?>"<?=$onclick?>><?
                                    }
                                    ?><label><?
                                        if(!empty($prefix)) echo $prefix.' ';
                                        echo stripcslashes($v['name']);
                                        if(!empty($sufix)) echo ' '.$sufix;
                                        ?></label><?

                                    if($showA){
                                    ?></a><?
                            }?>
                            </div>
                            </div><?
                            $str .= ob_get_clean();

                        }
                    }
                    break;
                case 5:
                    $val = '';
                    if(isset($this->arr_current_img_params_value[$id_param])
                        && !empty($this->arr_current_img_params_value[$id_param])){
                        $val = $this->arr_current_img_params_value[$id_param];
                    }

                    $paramLink = $this->makeParamLink($this->params_row[$i]['id']);

                    ob_start();
                    ?><div class="param-key-one-item">
                    <div class="param-key-input">
                        <input type="text" name="<?=PARAM_VAR_NAME . PARAM_VAR_SEPARATOR.$id_param?>" value="<?=$val?>"
                               id="<?=PARAM_VAR_NAME . PARAM_VAR_SEPARATOR.$id_param?>"/>
                    </div>
                    <div class="param-key-submit">
                        <input type="submit" value="Ок" name="paramType5"
                               onclick="return gelPropConetntByTypeParam5('<?=$this->catLink?>','<?=$paramLink?>','<?=PARAM_VAR_NAME . PARAM_VAR_SEPARATOR.$id_param?>');" />
                    </div>
                    </div><?
                    $str .= ob_get_clean();
                    break;
            }
            $str .= '</div></div>';
        }

        return $str;
    } //end of function ShowAllFilters()




    function ShowAllFiltersMenu($id_cat)
    {
        $str = NULL;
        $param_str = NULL;
        $param = NULL;
        $filtr = NULL;
        //$this->id_cat = $id_cat;


        $IdOfProps = $this->makeIdPropStr( $this->propArrNoLimit[0] );



        $n = count($this->params_row);


        if($n==0) return false;
        for ($i = 0; $i < $n; $i++) {

            $row = $this->params_row[$i];

            if ($row['modify'] != 1) //Отображать в блоке параметров
                continue;
            $val = NULL;
            $paramName = stripcslashes($row['name']);
            $id_param = $row['id'];




            $str .= '<div class="paramBlock"><div class="paramName"><div class="param-title"><div class="line2"></div><span>' . $paramName . '</span></div></div>';

            $prefix = stripcslashes($row['prefix']);
            $sufix = stripcslashes($row['sufix']);
            $str .= '<div class="paramKey">';
//            echo '<br>type='.$row['type'];
            switch ($row['type']) {
                case '1':
                    $IdOfProps1 = $IdOfProps;
                    if (isset($this->propArrNoLimit[$id_param])) {
                        $IdOfProps1 = $this->makeIdPropStr($this->propArrNoLimit[$id_param]);
                    }
                    $valStart = $this->GetParamsPropValType1($id_param,$IdOfProps1);
//                    var_dump($valStart);
                    $val = $valStart;
                    if(isset($this->arr_current_img_params_value[$id_param]) && !empty($this->arr_current_img_params_value[$id_param])){
                        $sel_val = $this->arr_current_img_params_value[$id_param];
                        $val = explode('|',$sel_val);
                    }

                    if(empty($val[0])){
                        $val[0] = 0;
                        $val[1] = 0;
                    }
                    if(empty($valStart[0])){
                        $valStart[0] = 0;
                        $valStart[1] = 0;
                    }

                    $paramLink = $this->makeParamLink($this->params_row[$i]['id']);

                    ob_start();
                    $id_elem = PARAM_VAR_NAME . PARAM_VAR_SEPARATOR.$id_param;
                    ?><div class="param-key-one-item">
                    <div class="param-key-fon-fo-slider">
                        <div id="slider<?=$id_elem?>"></div>
                    </div>
                    <div class="param-key-input-type1">
                        <label form="<?=$id_elem?>_0"><?=$this->multi['FLD_FROM']?></label>
                        <input type="text" name="<?=$id_elem?>_0" id="<?=$id_elem?>_0" value="<?=$val[0]?>"/>

                        <label form="<?=$id_elem?>_1"><?=$this->multi['TXT_TO']?></label>
                        <input type="text" name="<?=$id_elem?>_1" id="<?=$id_elem?>_1" value="<?=$val[1]?>"/>

                        <label><?=$sufix?></label>
                    </div>
                    <div class="param-key-submit">
                        <input type="submit" value="Ок" name="paramType1"
                               onclick="return gelPropConetntByTypeParam1('<?=$this->catLink?>','<?=$paramLink?>','<?=$id_elem?>');" />
                    </div>
                    <script type="text/javascript">
                        //<![CDATA[
                        jQuery("#slider<?=$id_elem?>").slider({
                            min: <?=$valStart[0]?>,
                            max: <?=$valStart[1]?>,
                            values: [<?=$val[0]?>,<?=$val[1]?>],
                            range: true,
                            stop: function(event, ui) {
                                jQuery("input#<?=$id_elem?>_0").val(jQuery("#slider<?=$id_elem?>").slider("values",0));
                                jQuery("input#<?=$id_elem?>_1").val(jQuery("#slider<?=$id_elem?>").slider("values",1));
                            },
                            slide: function(event, ui){
                                jQuery("input#<?=$id_elem?>_0").val(jQuery("#slider<?=$id_elem?>").slider("values",0));
                                jQuery("input#<?=$id_elem?>_1").val(jQuery("#slider<?=$id_elem?>").slider("values",1));
                            }
                        });
                        //]]>
                    </script>

                    </div>
                    <div class="filters-off">
                    <a href="<?=$this->catLink.$paramLink?>" title="<?=$this->multi['TXT_KILL_FILTER']?>"
                       onclick="return gelPropConetnt('<?=$this->catLink?>','<?=$paramLink?>');"><?=$this->multi['TXT_KILL_FILTER']?></a>
                    </div><?
                    $str .= ob_get_clean();
                    break;
                case '3':
                case '4':
                    $IdOfProps1 = $IdOfProps;

                    if (isset($this->propArrNoLimit[$id_param])) {

                        $IdOfProps1 = $this->makeIdPropStr($this->propArrNoLimit[$id_param]);

                    }


                    $val = $this->GetParamsPropVal($this->lang_id, $row['id_categ'], $id_param, $IdOfProps1);

                    if (is_array($val)) {

                        foreach ($val as $k => $v) {
                            // Форматированный вывод текста либо ссылки параметра
                            $checked = false;
                            if (is_array($this->arr_current_img_params_value) &&
                                isset($this->arr_current_img_params_value[$row['id']])) {
                                $row_sel_val_this_param = explode(',',$this->arr_current_img_params_value[$row['id']]);
                                if(in_array($k,$row_sel_val_this_param)){
                                    $checked = true;
                                }
                            }

                            $paramLink = $this->makeParamLink($this->params_row[$i]['id'], $v['cod']);

                            $checkedClass = '';
                            $checkedInput = '';
                            $disabled = '';
                            $showA = true;
                            $onclick = " onclick=\"return gelPropConetnt('".$this->catLink."','".$paramLink."', true, '".PARAM_VAR_NAME.PARAM_VAR_SEPARATOR.$id_param."=".$k."');\"";
                            if (!isset($v['countOfProp']) || $v['countOfProp']==0) {
                                $checkedClass .= ' param-no-selected';
                                $showA = false;
                                $disabled = ' disabled';
                            }

                            if ($checked == true) {
                                $checkedClass = ' param-selected';
                                $showA = true;
                                $checkedInput = ' checked';
                            }

                            if(!$showA){
                                $onclick = '';
                            }
                            ob_start();
                            ?><div class="param-key-one-item<?=$checkedClass?>">
                            <div class="param-key-checkbox">
                                <div class="filter-checkbox"
                                     id="checBox_<?=$id_param.'_'.$k?>_block_m"></div>
                                <input type="checkbox" name="<?=PARAM_VAR_NAME.PARAM_VAR_SEPARATOR.$id_param?>" value="<?=$k?>"
                                    <?=$checkedInput?> <?=$disabled?>
                                       id="checBox_<?=$id_param.'_'.$k?>_Input_m"/>
                                <script type="text/javascript">

                                    $('#checBox_<?=$id_param.'_'.$k?>_Input_m').hide();
                                    $('#checBox_<?=$id_param.'_'.$k?>_block_m').show();


                                </script>
                            </div>
                            <div class="param-key-label">
                                <?if($showA){
                                ?><a <?=$onclick?>><?
                                    }
                                    ?><label><?
                                        if(!empty($prefix)) echo $prefix.' ';
                                        echo stripcslashes($v['name']);
                                        if(!empty($sufix)) echo ' '.$sufix;
                                        ?></label><?

                                    if($showA){
                                    ?></a><?
                            }?>
                            </div>
                            </div><?
                            $str .= ob_get_clean();

                        }
                    }
                    break;
                case 5:
                    $val = '';
                    if(isset($this->arr_current_img_params_value[$id_param])
                        && !empty($this->arr_current_img_params_value[$id_param])){
                        $val = $this->arr_current_img_params_value[$id_param];
                    }

                    $paramLink = $this->makeParamLink($this->params_row[$i]['id']);

                    ob_start();
                    ?><div class="param-key-one-item">
                    <div class="param-key-input">
                        <input type="text" name="<?=PARAM_VAR_NAME . PARAM_VAR_SEPARATOR.$id_param?>" value="<?=$val?>"
                               id="<?=PARAM_VAR_NAME . PARAM_VAR_SEPARATOR.$id_param?>"/>
                    </div>
                    <div class="param-key-submit">
                        <input type="submit" value="Ок" name="paramType5"
                               onclick="return gelPropConetntByTypeParam5('<?=$this->catLink?>','<?=$paramLink?>','<?=PARAM_VAR_NAME . PARAM_VAR_SEPARATOR.$id_param?>');" />
                    </div>
                    </div><?
                    $str .= ob_get_clean();
                    break;
            }
            $str .= '</div></div>';
        }

        return $str;
    } //end of function ShowAllFilters()


    // ================================================================================================
    // Function : ShowAllFilters()
    // Date : 21.08.2013
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function makeIdPropStr($propArrNoLimit){
        $str="";
        if($propArrNoLimit!=false){
            $str=implode(",", $propArrNoLimit);
//       foreach ($propArrNoLimit as $key=>$value) {
//           $str.=",".$value['id'];
//           echo $value;
//       }
//       $str[0]=" ";
        }else return false;
        return $str;
    }

    // ================================================================================================
    // Function : ShowPriceFilter()
    // Date : 21.08.2013
    // Returns : The panel filter by price
    // Description : Assembles a panel filter by price
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function ShowPriceFilter(){

        //var_dump($this->propArrNoLimit);
        $IdOfProps1 = $this->makeIdPropStr($this->propArrNoLimit[-1]);
        $valStart = $this->GetParamsPropPrice($IdOfProps1);



        if(isset($this->from) && !empty($this->from)) $val[0] = $this->from;
        if(empty($val[0])) $val[0] = $valStart[0];

        if(isset($this->to) && !empty($this->to)) $val[1] = $this->to;
        if(empty($val[1])) $val[1] = $valStart[1];
//        var_dump($val);
        $paramLink = $this->makeParamLink('price',-1);
        $id_elem = 'priceVal';
        return View::factory('/modules/mod_catalog/templates/param/tpl_show_price_panel.php')
            ->bind('id_elem', $id_elem)
            ->bind('paramLink', $paramLink)
            ->bind('valStart', $valStart)
            ->bind('val', $val)
            ->bind('Catalog',$this);
    }

    // ================================================================================================
    // Function : ShowBrandFilter()
    // Date : 21.08.2013
    // Returns : The panel filter by manufacturer
    // Description : Assembles the filter panel by supplier
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function ShowBrandFilter(){
        $str = '';
//        var_dump($this->arr_brand_in_this_cat);
//        var_dump($this->row_sel_brand);
        if (is_array($this->arr_brand_in_this_cat)) {
            $keys = array_keys($this->arr_brand_in_this_cat);
            $rows = count($keys);
            for($i=0;$i<$rows;$i++){
                $cod_brand = $keys[$i];
                $row = $this->arr_brand_in_this_cat[$cod_brand];
                $name = stripcslashes($row['name']);
                // Форматированный вывод текста либо ссылки параметра
                $checked = false;
                if (isset($this->row_sel_brand) && !empty($this->row_sel_brand)) {
                    if(in_array($cod_brand,$this->row_sel_brand)){
                        $checked = true;
                    }
                }

                $paramLink = $this->makeParamLink('brand', -1,$cod_brand);

                $checkedClass = '';
                $checkedInput = '';
                $disabled = '';
                $showA = true;
                $onclick = " onclick=\"return gelPropConetnt('".$this->catLink."','".$paramLink."');\"";
                if (!isset($row['countOfProp']) || $row['countOfProp']==0) {
                    $checkedClass .= ' param-no-selected';
                    $showA = false;
                    $disabled = ' disabled';
                }

                if ($checked == true) {
                    $checkedClass = ' param-selected';
                    $showA = true;
                    $checkedInput = ' checked';
                }

                if(!$showA){
                    $onclick = '';
                }
                ob_start();
                ?><div class="param-key-one-item<?=$checkedClass?>">
                <div class="param-key-checkbox">
                    <div class="filter-checkbox"<?=$onclick?>
                         id="checBox_id_manufac<?=$cod_brand?>_block"></div>
                    <input type="checkbox" name="id_manufac" value="<?=$cod_brand?>"
                        <?=$checkedInput?> <?=$disabled?>
                           id="checBox_id_manufac<?=$cod_brand?>_Input"/>
                    <script type="text/javascript">
                        $('#checBox_id_manufac<?=$cod_brand?>_Input').hide();
                        $('#checBox_id_manufac<?=$cod_brand?>_block').show();
                    </script>
                </div>
                <div class="param-key-label">
                    <?if($showA){
                    ?><a href="<?=$this->catLink . $paramLink?>"<?=$onclick?>><?
                        }
                        ?><label><?=$name?></label><?
                        if($showA){
                        ?></a><?
                }?>
                </div>
                </div><?
                $str .= ob_get_clean();

            }
        }
        if(!empty($str)){
            $str = '<div class="paramBlock"><div class="paramName"> <div class="h1main param-title"><div class="line2"></div><span>'.$this->multi['TXT_BREND'].':</div>'.$str.'</span></div></div>';

        }
        return $str;
    }

    // ================================================================================================
    // Function : checHash()
    // Date : 21.08.2013
    // Returns : flips to the correct page
    // Description : Tests for the presence of the code page for a positive result throws to the correct page
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function checHash(){
        $this->catLinkInit();
        ?><script type="text/javascript">
            //<![CDATA[
            var hash = location.hash;
            //alert(hash);
            if(hash != ''){
                var hrefNew,href = '<?=$this->catLink?>';
                hash = location.href.split('#')[1];
                if(hash!='kill'){
                    hrefNew = href + hash;
                }else{
                    hrefNew = href;
                }
                location.href = hrefNew;
            }
            //]]>
        </script><?
    }


    // ================================================================================================
    // Function : initParamFilter()
    // Date : 21.08.2013
    // Description : Starts (initiates) filters
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function initParamFilter($id_tag = NULL){

        $this->paramsRowInit();
        $this->catLinkInit();
        $this->paramArrInit();
        $this->propArrNoLimitInit();
        if (!isset($this->isContent))
            $this->isContent = $this->IsContent($this->id_cat);
        $this->arrBrandInThisCatInit($id_tag);
//        $this->arrBrandInit();
    }


    function initParamFilterMenu($id_cat){

        $this->paramsRowInitMenu($id_cat);
        $this->catLinkInitMenu($id_cat);
        $this->paramArrInit();
        $this->propArrNoLimitInitMenu($id_cat);
        if (!isset($this->isContent))
            $this->isContent = $this->IsContent($id_cat);

       // $this->arrBrandInThisCatInit($id_tag);
//        $this->arrBrandInit();
    }

    // ================================================================================================
    // Function : arrBrandInThisCatInit()
    // Date : 21.08.2013
    // Description : Starts (initiates) filter by brand
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function arrBrandInThisCatInit($id_tag = NULL){
        if(!$id_tag)$IdOfProps1 = $this->makeIdPropStr($this->propArrNoLimit[-2]);
        else{
            $IdOfProps1 = $this->makeIdPropStrForTags($id_tag);
//            echo '$IdOfProps1='.$IdOfProps1;
        }

        if(!isset($this->arr_brand_in_this_cat))
            $this->arr_brand_in_this_cat = $this->GetParamsPropBrand($IdOfProps1);
//        var_dump($this->arr_brand_in_this_cat);
    }

    // ================================================================================================
    // Function : catLinkInit()
    // Date : 21.08.2013
    // Description : Given the filter builds home page category
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function catLinkInit(){
        if (!isset($this->catLink)) {
            if(!empty($this->id_cat)){
                if (!empty($this->treeCatData[$this->id_cat]['href']))
                    $this->catLink = $this->getUrlByTranslit($this->treeCatData[$this->id_cat]['href']);
                else
                    $this->catLink = $this->getUrlByTranslit($this->treeCatData[$this->id_cat]['path']);
            }else{
                $this->catLink = _LINK.'catalog/';
            }
//            echo '<br>$this->page='.$this->page;
            if($this->page=='all' || $this->page=='alltovar'){
                $this->catLink .= 'alltovar/';
            }
        }
    }

    function catLinkInitMenu($id_cat){

            if(!empty($id_cat)){
                if (!empty($this->treeCatData[$id_cat]['href']))
                    $this->catLink = $this->getUrlByTranslit($this->treeCatData[$id_cat]['href']);
                else
                    $this->catLink = $this->getUrlByTranslit($this->treeCatData[$id_cat]['path']);
            }else{
                $this->catLink = _LINK.'catalog/';
            }
//            echo '<br>$this->page='.$this->page;
            if($this->page=='all' || $this->page=='alltovar'){
                $this->catLink .= 'alltovar/';
            }

    }

    // ================================================================================================
    // Function : catLinkInit()
    // Date : 21.08.2013
    // Description : Initiation of an array of parameters
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function paramsRowInit(){

        if (!isset($this->params_row))
            $this->params_row = $this->GetParams($this->id_cat);
    }

    function paramsRowInitMenu($id_cat){
            $this->params_row = $this->GetParams($id_cat);
    }
    // ================================================================================================
    // Function : transformParamsRow()
    // Date : 21.08.2013
    // Description : Re-formed into an associative array of parameters
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function transformParamsRow(){
        $arr = array();
        $count = count($this->params_row);
        for($i=0;$i<$count;$i++){
            $arr[$this->params_row[$i]['id']] = $this->params_row[$i];
        }
        return $arr;
    }

    // ================================================================================================
    // Function : transformParamsRow()
    // Date : 21.08.2013
    // Description : Formation of reference parameters
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function paramArrInit() {

        if(isset($this->param_arr) || !empty($this->param_arr)) return false;
        $this->param_arr = array();
        // Формирование строки параметров
        if ( is_array($this->arr_current_img_params_value) ) {
            $param_str = NULL;
            $keys = array_keys($this->arr_current_img_params_value);
            $size = sizeof($keys);
            for($i=0;$i<$size;$i++){
                $key = $keys[$i];
                if(!isset($this->param_arr[$key])){
                    $value = $this->arr_current_img_params_value[$keys[$i]];
                    if($i>0) $this->url_param .= '&';
                    $this->url_param .= PARAM_VAR_NAME . PARAM_VAR_SEPARATOR . $key . '=' . $value;
                    $this->param_arr[$key] = $value;
                }
            }
        }
    }

    // ================================================================================================
    // Function : propArrNoLimitInit()
    // Date : 21.08.2013
    // Description : Array initialization parameters for each parameter
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function propArrNoLimitInit(){
        if (!isset($this->propArrNoLimit))
            $this->propArrNoLimit = $this->generateIdPropArra();
        $this->countOfPropNoLimit = count($this->propArrNoLimit[0]);
    }

    function propArrNoLimitInitMenu($id_cat){

        $this->propArrNoLimit = $this->generateIdPropArraMenu($id_cat);
        $this->countOfPropNoLimit = count($this->propArrNoLimit[0]);
    }

    // ================================================================================================
    // Function : propArrNoLimitInit()
    // Date : 21.08.2013
    // Description : Array initialization parameters by brand
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function arrBrandInit(){
        if(!isset($this->arr_brand)){
            $this->arr_brand = $this->getSpecArrBrand();
//            var_dump($this->arr_brand);
        }
    }

    // ================================================================================================
    // Function : GetParamsPropValType1()
    // Date : 21.08.2013
    // Description : Initialization of the minimum and maximum parameter for numeric values ​​in this category
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function GetParamsPropValType1( $paramId,$IdOfProps){
        $q="SELECT MAX(CAST(`".TblModCatalogParamsProp."`.`val` AS DECIMAL(10,0))) as max,
        MIN(CAST(`".TblModCatalogParamsProp."`.`val` AS DECIMAL(10,0))) as min
            FROM `".TblModCatalogParamsProp."`,`".TblModCatalogProp."`
            WHERE `".TblModCatalogParamsProp."`.`id_param`='".$paramId."'
            AND `".TblModCatalogParamsProp."`.`id_prop` in (".$IdOfProps.")
            AND `".TblModCatalogProp."`.`id` = `".TblModCatalogParamsProp."`.`id_prop`
            AND `".TblModCatalogProp."`.`visible` = '2'
            AND `".TblModCatalogProp."`.`exist` = '1'
            AND `".TblModCatalogParamsProp."`.`val` != ''
           ";
        $res = $this->db->db_Query( $q );
//        echo '<br> $q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;
        if ( !$res or !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        $arr[0] = 0;
        $arr[1] = 0;
        if($rows>0){
            $row = $this->db->db_FetchAssoc();
            $arr[0] = $row['min'];
            $arr[1] = $row['max'];
        }
        return $arr;

    }

    // ================================================================================================
    // Function : GetParamsPropValType1()
    // Date : 21.08.2013
    // Description : Initialization of the minimum and maximum parameter for Exalt in this category
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function GetParamsPropPrice($IdOfProps){

        $row = array();

        if(!isset($IdOfProps) || empty($IdOfProps)){
            return false;
        }
        $q = "SELECT `id`
              FROM `".TblModCatalogProp."`
              WHERE `id` in (".$IdOfProps.") AND
                `visible` = '2'
              AND `".TblModCatalogProp."`.`exist` = '1'

           ";



        $res = $this->db->db_Query( $q );
        $rows = $this->db->db_GetNumRows();

        for($i=0; $i<$rows; $i++) {
            $r =  $this->db->db_FetchAssoc();
            $row[] = $r['id'];

        }


        if(count($row)>0) {
            $IdOfProps = implode ( ", " , $row );
           // var_dump($IdOfProps);
        $arrCurrency = $this->Currency->GetShortNamesInArray();



            foreach( $arrCurrency as $k=>$v ){

                $q="SELECT
                         MAX(`price`) as max,
                         MIN(`price`) as min
                    FROM
                         `".TblModPropGroupPrice."`
                    WHERE
                       `prod_id` in (".$IdOfProps.")
                      AND `group_id` = '".$this->group_user_id."'
                      AND `price`!='0.00'
                      AND `group_currency` = '".$k."'
                ";



                $res = $this->db->db_Query( $q );
                if ( !$res or !$this->db->result ) return false;
                $rows = $this->db->db_GetNumRows();


                if($rows>0){

                    $row = $this->db->db_FetchAssoc();


                    $price[$k]['min'] =  (isset($row['min']) )? $row['min'] : 0;
                    $price[$k]['max'] =  (isset($row['max']) )? $row['max'] : 0;

                    $price[$k]['min'] =  $this->Currency->Converting($k, _CURR_ID, $price[$k]['min'], 2);
                    $price[$k]['max'] =  $this->Currency->Converting($k, _CURR_ID, $price[$k]['max'], 2);


                }

            }

           // var_dump($price);

            $max = 0;
            $min = 100000000;



            foreach($price as $k=>$v) {

                if( !empty($v['max']) AND $v['max']!=0) {
                    $max = ( $max<$v['max'] ) ? $v['max']: $max;
                }

                if( !empty($v['min']) AND $v['min']!=0) {
                    $min = ( $min > $v['min'] ) ? $v['min'] : $min;
                }




            }







            $arr[0] = $this->Currency->ShowPrice($min, false);
            $arr[1] =$this->Currency->ShowPrice($max, false);



            return $arr;

        }else{

            return false;

        }

    }

    // ================================================================================================
    // Function : GetParamsPropBrand()
    // Date : 21.08.2013
    // Description : Initialize an array parameter for the brand in this category
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function GetParamsPropBrand($IdOfProps){
        $q="SELECT DISTINCT `".TblModCatalogSprManufac."`.*";
        if(strlen($IdOfProps)!=0){
            $q.="  , (
                SELECT count(*)
                FROM `".TblModCatalogProp."`
                WHERE `".TblModCatalogProp."`.`id` IN (".$IdOfProps.")
                AND `".TblModCatalogProp."`.`id_manufac` = `".TblModCatalogSprManufac."`.`cod`
                AND `".TblModCatalogProp."`.`visible` = '2'
             ) AS countOfProp ";
        }
        $q .=" FROM `".TblModCatalogSprManufac."`,`".TblModCatalogProp."`
            WHERE `".TblModCatalogSprManufac."`.`lang_id` = '".$this->lang_id."' AND (";
        if(!empty($IdOfProps)){
            $q .=" `".TblModCatalogProp."`.`id`  IN (".$IdOfProps.")";
        }
        if(isset($this->row_sel_brand) && !empty($this->row_sel_brand)){
            $q .=" OR `".TblModCatalogProp."`.`id_manufac`  IN (";
            $count = count($this->row_sel_brand);
            for($i=0;$i<$count;$i++){
                if($i>0) $q.=",";
                $q.=$this->row_sel_brand[$i];
            }
            $q .=")";
        }
        $q .=") AND `".TblModCatalogProp."`.`id_manufac` = `".TblModCatalogSprManufac."`.`cod`
            ORDER BY `".TblModCatalogSprManufac."`.`move`
           ";
        $res = $this->db->db_Query( $q );
//       echo '<br> $q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;
        if ( !$res or !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        if($rows==0) return false;
        for($i=0;$i<$rows;$i++){
            $row = $this->db->db_FetchAssoc();
            $arr[$row['cod']] = $row;
        }
        return $arr;
    }

    // ================================================================================================
    // Function : GetParamsPropBrand()
    // Date : 21.08.2013
    // Description : Initialize an array parameter for the brand in this category
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function getSpecArrBrand(){
        $q="SELECT `".TblModCatalogSprManufac."`.*,
        `".TblModCatalogProp."`.`id`,`".TblModCatalogProp."`.`id_cat`
        FROM `".TblModCatalogSprManufac."`,`".TblModCatalogProp."`
            WHERE `".TblModCatalogSprManufac."`.`lang_id` = '".$this->lang_id."'
            AND `".TblModCatalogProp."`.`id_manufac` = `".TblModCatalogSprManufac."`.`cod`
            AND `".TblModCatalogProp."`.`visible` = '2'
            ORDER BY `".TblModCatalogSprManufac."`.`move`,`".TblModCatalogProp."`.`id_cat`
           ";
        $res = $this->db->db_Query( $q );
//        echo '<br> $q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;
        if ( !$res or !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        if($rows==0) return false;
        for($i=0;$i<$rows;$i++){
            $row = $this->db->db_FetchAssoc();
            $cod_brand = $row['cod'];
            if(!isset($arr[$cod_brand])){
                $arr[$cod_brand] = $row;
            }
            $arr[$cod_brand]['cat'][$row['id_cat']][$row['id']] = 1;
        }
        return $arr;
    }

    // ================================================================================================
    // Function : ShowSelectedFilters()
    // Date : 21.08.2013
    // Description : Shows all selected filters
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function ShowSelectedFilters()
    {
        $str = '';
        $param_str = NULL;
        $param = NULL;
        $filtr = NULL;

        if(!empty($this->arr_current_img_params_value)){
            $n = count($this->params_row);
            for ($i = 0; $i < $n; $i++) {
                $row = $this->params_row[$i];
                if(!isset($this->arr_current_img_params_value[$row['id']]) ||
                    empty($this->arr_current_img_params_value[$row['id']])) //Эсть такой параметр среди вибраних
                continue;
                if ($row['modify'] != 1) //Отображать в блоке параметров
                continue;
                $paramName = stripcslashes($row['name']);

                $prefix = stripcslashes($row['prefix']);
                $sufix = stripcslashes($row['sufix']);

                $val = NULL;
                $strText = '';
                switch ($this->params_row[$i]['type']) {
                    case '1':
                        $value = explode('|',$this->arr_current_img_params_value[$row['id']]);
                        $str_value = $this->multi['FLD_FROM'].' '.$value[0].' '.$this->multi['TXT_TO'].' '.$value[1].' '.$sufix;
                        $paramLink = $this->makeParamLink($row['id']);
                        $strText .= View::factory('/modules/mod_catalog/templates/param/tpl_show_sel_parma_one_item.php')
                            ->bind('paramLink', $paramLink)
                            ->bind('str_value', $str_value)
                            ->bind('Catalog',$this);
                        break;
                    case '3':
                    case '4':
                        $val = $this->GetParamsPropVal($this->lang_id, $this->params_row[$i]['id_categ'], $this->params_row[$i]['id'], '');

                        if (is_array($val)) {
                            $row_sel_val_this_param = explode(',',$this->arr_current_img_params_value[$row['id']]);
                            foreach ($val as $k => $v) {
                                // Форматированный вывод текста либо ссылки параметра
                                if(in_array($k,$row_sel_val_this_param)){
                                    $paramLink = $this->makeParamLink($row['id'], $v['cod']);
                                    $str_value = '';
                                    if(!empty($prefix)) $str_value .= $prefix.' ';
                                    $str_value .= stripcslashes($v['name']);
                                    if(!empty($sufix)) $str_value .= ' '.$sufix;
                                    $strText .= View::factory('/modules/mod_catalog/templates/param/tpl_show_sel_parma_one_item.php')
                                        ->bind('paramLink', $paramLink)
                                        ->bind('str_value', $str_value)
                                        ->bind('Catalog',$this);
                                }
                            }
                        }

                        break;
                    case '5':
                        $value = $this->arr_current_img_params_value[$row['id']];
                        $paramLink = $this->makeParamLink($row['id']);
                        $strText .= View::factory('/modules/mod_catalog/templates/param/tpl_show_sel_parma_one_item.php')
                            ->bind('paramLink', $paramLink)
                            ->bind('str_value', $name)
                            ->bind('Catalog',$this);
                        break;
                }
//                echo '<br>$strText='.$strText;
                if(!empty($strText)){
                    $str .= '<div class="paramSelBlock"><div class="paramNameSel">' . $paramName . ':</div>';
                    $str .= $strText.'</div>';
                }
            }
        }

        if (isset($this->from) and isset($this->to)) {
            $str .= '<div class="paramSelBlock"><div class="paramNameSel">Цена:</div>';
            $str_value = 'от ' . $this->from . ' до ' . $this->to . ' грн.';
            $paramLink = $this->makeParamLink('price',-1);
            $str .= View::factory('/modules/mod_catalog/templates/param/tpl_show_sel_parma_one_item.php')
                ->bind('paramLink', $paramLink)
                ->bind('str_value', $str_value)
                ->bind('Catalog',$this);
            $str .= '</div>';
        }

        if (!empty($this->id_manufac)) {
            $str_value = '';
            if (is_array($this->arr_brand_in_this_cat)) {
                $rows = count($this->row_sel_brand);
                for($i=0;$i<$rows;$i++){
                    $cod_brand = $this->row_sel_brand[$i];
                    if(!isset($this->arr_brand_in_this_cat[$cod_brand])
                    || empty($this->arr_brand_in_this_cat[$cod_brand])) continue;
                    $row = $this->arr_brand_in_this_cat[$cod_brand];
                    $name = stripcslashes($row['name']);
                    $paramLink = $this->makeParamLink('brand', -1,$cod_brand);
                    $str_value .= View::factory('/modules/mod_catalog/templates/param/tpl_show_sel_parma_one_item.php')
                        ->bind('paramLink', $paramLink)
                        ->bind('str_value', $name)
                        ->bind('Catalog',$this);
                }
            }

            if(!empty($str_value)){
                $str .= '<div class="paramSelBlock"><div class="paramNameSel">Производитель:</div>';
                $str .= $str_value;
                $str .= '</div>';
            }
        }


        if (!empty($this->arr_current_img_params_value) || !empty($this->from) || !empty($this->to) || !empty($this->id_manufac)) {
            $str .= '<div class="filters-off">
                <a href="'.$this->catLink.'" onclick="return gelPropConetnt(\''.$this->catLink.'\',\'\');">Сбросить все фильтры</a>
            </div>';
        }

        if(!empty($str)) $str =  '<div class="paramName">' . $this->multi['TXT_YUR_SEL'] . ':</div>' . $str;

        return $str;
    } //end of function ShowSelectedFilters()

    // ================================================================================================
    // Function : contentForCompare()
    // Date : 21.08.2013
    // Description : Data on goods for comparison
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function contentForCompare($str_prop){
        $q = "SELECT " . TblModCatalogPropSprName . ".*
                FROM " . TblModCatalogPropSprName . "
                WHERE `" . TblModCatalogPropSprName . "`.`lang_id` ='".$this->lang_id."'
                AND `" . TblModCatalogPropSprName . "`.`cod` in (".$str_prop.")";
        $res = $this->db->db_Query($q);
//        echo '<br>'.$q.' <br/>res='.$res.' <br/>$this->db->result='.$this->db->result;
        if (!$res or !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        //echo '<br>$rows='.$rows;
        if($rows==0) return false;
        $arr = array();
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc();
            $arr[$row['cod']] = stripcslashes($row['name']);
        }
        return $arr;
    }

    // ================================================================================================
    // Function : contentForCompare()
    // Date : 21.08.2013
    // Description : Data on goods for comparison
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function arrForCompareProp($str_prop){
        $this->Logon = check_init('UserAuthorize', 'UserAuthorize');
        $q = "SELECT " . TblModCatalogPropSprName . ".`name`,
                `".TblModCatalogProp."`.*,
                `".TblModCatalogPropImg."`.`path` AS `first_img`,
                `".TblModCatalogPropImgTxt."`.`name` AS `first_img_alt`,
                `".TblModCatalogPropImgTxt."`.`text` AS `first_img_title`,
                `".TblModCatalogSprManufac."`.`name` as `brand_name`,
                `".TblModCatalogPropSprFull."`.`name` as `full`,
                `".TblModCatalogTranslit."`.`translit`,
                `".TblModTmpOrder."`.`quantity`
                FROM `".TblModCatalogPropSprName."`, `".TblModCatalogProp."`
                LEFT JOIN `".TblModCatalogPropImg."` ON (`".TblModCatalogProp."`.`id`=`".TblModCatalogPropImg."`.`id_prop` AND `".TblModCatalogPropImg."`.`move`='1' AND `".TblModCatalogPropImg."`.`show`='1')
                LEFT JOIN `".TblModCatalogPropImgTxt."` ON (`".TblModCatalogPropImg."`.`id`=`".TblModCatalogPropImgTxt."`.`cod` AND `".TblModCatalogPropImgTxt."`.lang_id='".$this->lang_id."')
                LEFT JOIN `".TblModCatalogSprManufac."` ON (`".TblModCatalogProp."`.`id_manufac`=`".TblModCatalogSprManufac."`.`cod` AND `".TblModCatalogSprManufac."`.lang_id='".$this->lang_id."')
                LEFT JOIN `".TblModCatalogPropSprFull."` ON (`".TblModCatalogProp."`.`id`=`".TblModCatalogPropSprFull."`.`cod` AND `".TblModCatalogPropSprFull."`.lang_id='".$this->lang_id."')
                LEFT JOIN `".TblModCatalogTranslit."` ON (`".TblModCatalogProp."`.`id`=`".TblModCatalogTranslit."`.`id_prop` AND `".TblModCatalogTranslit."`.lang_id='".$this->lang_id."')
                LEFT JOIN `".TblModTmpOrder."` ON (`".TblModCatalogProp."`.`id`=`".TblModTmpOrder."`.`prod_id` AND `".TblModTmpOrder."`.`sessid`='".$this->Logon->session_id."')
                WHERE `".TblModCatalogPropSprName."`.`lang_id` ='".$this->lang_id."'
                AND `".TblModCatalogPropSprName."`.`cod` = `".TblModCatalogProp . "`.`id`
                AND `".TblModCatalogProp . "`.`id` in (".$str_prop.")";
        $res = $this->db->db_Query($q);
//        echo '<br>'.$q.' <br/>res='.$res.' <br/>$this->db->result='.$this->db->result;
        if (!$res or !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        //echo '<br>$rows='.$rows;
        if($rows==0) return false;
        $arr = array();
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc();
            $rowShow['name'] = stripcslashes($row['name']);
            $rowShow['name_special'] = htmlspecialchars($rowShow['name']);
            $rowShow['id_cat'] = $row['id_cat'];

            if(!empty($row['first_img'])){
                $rowShow['origin_img'] = $this->getPictureRelPath($row['id'], $row['first_img']);
                $rowShow['image'] = $this->ShowCurrentImage($row['first_img'],'size_auto=205',85,NULL,NULL,$row['id'],true);
                if(empty($row['first_img_alt'])) $row['first_img_alt'] = htmlspecialchars($rowShow['name']);
                if(empty($row['first_img_title'])) $row['first_img_title'] = htmlspecialchars($rowShow['name']);
            }else{
                $rowShow['origin_img'] = '';
                $rowShow['image'] = $this->ShowCurrentImage('/images/design/no-image.png','size_auto=205',85,NULL,NULL,$row['id'],true);
                $row['first_img_alt'] = 'Нет катртики';
                $row['first_img_title'] = 'Нет катртики';
            }
            $rowShow['first_img_alt'] = $row['first_img_alt'];
            $rowShow['first_img_title'] = $row['first_img_alt'];

            if(!empty($row['price'])){
                $rowShow['price'] = $this->Currency->ShowPrice($row['price']);
            }else{
                $rowShow['price'] = '';
            }
            if(!empty($row['opt_price'])){
                $rowShow['opt_price'] = $this->Currency->ShowPrice($row['opt_price']);
            }else{
                $rowShow['opt_price'] = '';
            }

            $rowShow['brand_name'] = stripcslashes($row['brand_name']);

            $rowShow['art_num'] = stripcslashes($row['art_num']);

            $rowShow['full'] = stripcslashes($row['full']);
            $rowShow['link'] = _LINK.$row['translit'].'.html';

//            $rowShow['quantity'] = $row['quantity'];
            if(empty($row['quantity'])) $rowShow['buy_class'] = 'empty-cart';
            else  $rowShow['buy_class'] = 'in-cart';
            $rowShow['exist'] = $row['exist'];

            $arr[$row['id']] = $rowShow;
        }
//        var_dump($arr);
        return $arr;
    }

    // ================================================================================================
    // Function : arrForCompareParam()
    // Date : 21.08.2013
    // Description : Data on goods for comparison
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function arrForCompareParam($str_prop){
        $this->Logon = check_init('UserAuthorize', 'UserAuthorize');
        $q = "SELECT `".TblModCatalogParamsSprName."`.`name` as `name_param`,
                `".TblModCatalogParamsSprPrefix."`.`name` as `pref`,
                `".TblModCatalogParamsSprSufix."`.`name` as `suf`,
                `".TblModCatalogParamsVal."`.`name` as `name_val`,
                `".TblModCatalogParamsProp . "`.`id_prop`,
                `".TblModCatalogParams."`.*,
                `".TblModCatalogParamsProp."`.`val`

                FROM `".TblModCatalogParams."`
                LEFT JOIN `".TblModCatalogParamsSprPrefix."`
                ON (`".TblModCatalogParams."`.`id`=`".TblModCatalogParamsSprPrefix."`.`cod`
                AND `".TblModCatalogParamsSprPrefix."`.`lang_id`='".$this->lang_id."')

                LEFT JOIN `".TblModCatalogParamsSprSufix."`
                ON (`".TblModCatalogParams."`.`id`=`".TblModCatalogParamsSprSufix."`.`cod`
                AND `".TblModCatalogParamsSprSufix."`.`lang_id`='".$this->lang_id."')
                ,`".TblModCatalogParamsSprName."`,`".TblModCatalogParamsProp."`
                LEFT JOIN `".TblModCatalogParamsVal."`
                ON (`".TblModCatalogParamsProp."`.`val`=`".TblModCatalogParamsVal."`.`cod`
                AND `".TblModCatalogParamsVal."`.`lang_id`='".$this->lang_id."')


                WHERE `".TblModCatalogParamsSprName."`.`lang_id` ='".$this->lang_id."'
                AND `".TblModCatalogParamsSprName."`.`cod` = `".TblModCatalogParamsProp . "`.`id_param`
                AND `".TblModCatalogParamsProp . "`.`id_prop` in (".$str_prop.")
                AND `".TblModCatalogParams."`.`id` = `".TblModCatalogParamsProp . "`.`id_param`
                AND `".TblModCatalogParams."`.`modify` = '1'
                ORDER BY `".TblModCatalogParams."`.`move`, `".TblModCatalogParamsProp . "`.`id_prop`,`".TblModCatalogParamsVal."`.`move`";
        $res = $this->db->db_Query($q);
//        echo '<br>'.$q.' <br/>res='.$res.' <br/>$this->db->result='.$this->db->result;
        if (!$res or !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        //echo '<br>$rows='.$rows;
        if($rows==0) return false;
        $arr = array();
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc();
            $name = '';
            if(!empty($row['pref'])) $name .= stripcslashes($row['pref']).' ';

            switch($row['type']){
                case 1:
                case 5:
                    $name .= stripcslashes($row['val']);
                    break;
                case 2:
                    break;
                case 3:
                    $name .= stripcslashes($row['name_val']);
                    break;
                case 4:
                    if(isset($arr[$row['id']]['val'][$row['id_prop']]) && !empty($arr[$row['id']]['val'][$row['id_prop']])){
                        $name .= $arr[$row['id']]['val'][$row['id_prop']].'; ';
                    }
                    $name .= stripcslashes($row['name_val']);
                    break;
            }

            if(!empty($row['suf'])) $name .= ' '.stripcslashes($row['suf']);

            $arr[$row['id']]['val'][$row['id_prop']] = $name;

            if(!isset($arr[$row['id']]['name_param']) || empty($arr[$row['id']]['name_param']))
                $arr[$row['id']]['name_param'] = stripcslashes($row['name_param']);
        }
//        var_dump($arr);
        return $arr;
    }
}
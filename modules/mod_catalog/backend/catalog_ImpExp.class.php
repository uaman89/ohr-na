<?php
// ================================================================================================
// System : CMS
// Module : catalog_ImpExp.class.php
// Date : 21.03.2006
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Purpose : Class definition for all actions with managment of content of the catalog
// ================================================================================================

include_once( SITE_PATH.'/modules/mod_catalog/catalog.defines.php' );

// ================================================================================================
//    Class             : CatalogImpExp
//    Date              : 21.03.2006
//    Constructor       : Yes
//    Parms             : session_id / session id
//                        usre_id    / UserID
//                        user_      /
//                        user_type  / id of group of user
//    Returns           : None
//    Description       : Class definition for all actions with managment of content of the catalog
//    Programmer        :  Igor Trokhymchuk
// ================================================================================================
 class CatalogImpExp extends Catalog {

    public $curr=null;
    public $imgNameAsKey = NULL;
    public $arr_treeCatLevels = NULL;
    public $arr_treePropLevels = NULL;
    public $move = NULL;
    public $ins_cat_counter = 0;  // Счетчик добавленных категорий
    public $upd_cat_counter = 0; // Счетчик обновленных категорий
    public $ins_counter = 0;
    public $ins_counter_err = 0;
    public $upd_counter = 0;
    public $upd_counter_err = 0;
    public $data_success = '';
    public $data_faild = '';
    public $arr_cat_db = array();
    public $arr_cat = array();
    public $del_counter = 0;

    // ================================================================================================
    //    Function          : CatalogImpExp (Constructor)
    //    Date              : 21.03.2006
    //    Parms             : usre_id   / User ID
    //                        module    / module ID
    //                        sort      / field by whith data will be sorted
    //                        display   / count of records for show
    //                        start     / first records for show
    //                        width     / width of the table in with all data show
    //    Returns           : Error Indicator
    //    Description       : Opens and selects a dabase
    // ================================================================================================
    function CatalogImpExp ($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL) {
        //Check if Constants are overrulled
        ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
        ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
        ( $display  !="" ? $this->display = $display  : $this->display = 20   );
        ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
        ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
        ( $width    !="" ? $this->width   = $width    : $this->width   = 750  );

        $this-> lang_id = _LANG_ID;

        if (empty($this->db)) $this->db = new DB();
        if (empty($this->Right)) $this->Right = new Rights($this->user_id, $this->module);
        if (empty($this->Msg)) $this->Msg = new ShowMsg();
        $this->Msg->SetShowTable(TblModCatalogSprTxt);
        if (empty($this->Form)) $this->Form = new Form('form_mod_catalog_ImpExp');
        if (empty($this->Spr)) $this->Spr = new  SysSpr($this->user_id, $this->module);

        $this->settings = $this->GetSettings(1);
        if (empty($this->multi)) $this->multi = $this->Spr->GetMulti(TblModCatalogSprTxt);

        //load all data of categories to arrays $this->treeCat, $this->treeCatLevels, $this->treeCatData
        $this->loadTree();
        //echo '<br />treeCatLevels=';print_r($this->treeCatLevels);
        //echo '<br />treeCatData=';print_r($this->treeCatData);

    } // End of CatalogImpExp Constructor

    function db_OptimizeDB() {
        $res = mysql_query('
            SHOW TABLE STATUS WHERE Data_free / Data_length > 0.1 AND Data_free > 102400
            ');

        while ($row = mysql_fetch_assoc($res)) {
            mysql_query('OPTIMIZE TABLE ' . $row['Name']);
        }
    }

    // ================================================================================================
    //    Function          : Form
    //    Date              : 29.11.2009
    //    Returns           : Error Indicator
    //    Description       : Show form for Import catalog
    // ================================================================================================
    function Form()
    {
        //echo 'this->user_id = '.$this->user_id;
        //echo 'this->module = '.$this->module;
        $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr;
        $script = $_SERVER['PHP_SELF']."?$script";
        AdminHTML::PanelSimpleH();
        ?>
        <div style="font-size: 11px;">

          <?/*<fieldset style="border: 1px solid #000000; padding: 5px;">
          <legend>Обновление цены товаров из .csv-файла</legend>
           <?
           // Write Form Header
           $this->Form->WriteHeaderFormImg( $script );
           $this->Form->Hidden("task", "update_price_cnt_csv");
           ?>
           <input type="file" name="filename2" size="50" />
           <br/>
           Кодировка файла:
           <select name="from_charset" >
            <option value="cp1251">cp1251</option>
            <option value="utf-8">utf-8</option>
           </select>
           <br/><input type="submit" value="Обновить цену товаров">
           <br/><br/>Внимание! Процесс импорта может занять 1-2 минуты. <br/>
           Будьте терпеливы и не прерывайте импорт.
           <?$this->Form->WriteFooter();?>
         </fieldset>*/?>

         <fieldset style="border: 1px solid #000000; padding: 5px;">
          <legend>Импорт товаров и категорий из .csv-файла из PriceListImporter</legend>
           <?
           $this->Form->WriteHeaderFormImg( $script );
           $this->Form->Hidden("task", "import_csv");
           ?>
           <input type="file" name="filename" size="50" />
           <br/>
           Кодировка файла:
           <select name="from_charset" >
            <option value="cp1251">cp1251</option>
            <option value="utf-8">utf-8</option>
           </select>
           <br/><input type="submit" value="Обновить товары и категории">
           <br/><br/>Внимание! Процесс импорта может занять 1-2 минуты.<br/>
           Будьте терпеливы и не прерывайте импорт.
           <?$this->Form->WriteFooter();?>
         </fieldset>

         <fieldset style="border: 1px solid #000000; padding: 5px;">
          <legend>Экспорт каталога товаров</legend>
           <form enctype="multipart/form-data" id="ExportPrice" name="ExportPrice" method="post" action="<?=$script;?>">
            <div style="float:left;">
               <input type="submit" name="export_to_xml" value="Экспорт каталога в Excel" onclick="Export('<?=$this->module;?>', 'export_to_xml', 'res_export_to_xml'); return false;" />
             <br/>
             <br/>
             <input type="submit" name="export_to_csv" value="Экспорт для PriceList Importer" title="Экспорт каталога для программы E-Trade PriceList Importer " onclick="Export('<?=$this->module;?>', 'export_to_csv', 'res_export_to_csv'); return false;" />
            </div>
            <div id="res_export_to_xml" style="height:25px;">&nbsp;</div>
            <br/>
            <div id="res_export_to_csv" style=""></div>
             </form>
           </fieldset>

           <?/*
           <fieldset style="border: 1px solid #000000; padding: 5px;">
              <legend>Установка кодов товаров и категорий по умолчанию</legend>
               <?
               $this->Form->WriteHeaderFormImg( $script );
               $this->Form->Hidden("task", "set_cod_pli");
               ?>
               <input type="submit" value="Установить">
               <?$this->Form->WriteFooter();?>
           </fieldset>
            */?>

           <fieldset style="border: 1px solid #000000; padding: 5px;">
               <legend>Импорт изображений к товарам из дериктории /import/images/</legend>
               <p>В качестве имени файла изображеня должен выступать Код товара. По этому коду будет производиться синхронизация. Например изображение "1007902.jpg" импортируется для товара с Кодом "1007902".<br/>
                  Если для одного товара необходимо импортировать несколько изображений, то имя файла должно состоять из Кода товара, далее символ "_" (нижнее подчеркивание) и далее номер изображения. Например 1007902_2.jpg, 1007902_3.jpg и т.д.</p>
               <form enctype="multipart/form-data" id="ExportPrice" name="ExportPrice" method="post" action="<?= $script; ?>">
                   <div style="float:left;">
                       <input type="submit" name="import_images" value="Импорт изображений" title="Импорт изображений" onclick="Export('<?= $this->module; ?>', 'import_images', 'import_images'); return false;" />
                   </div>
                   <div id="import_images">&nbsp;</div>
               </form>
           </fieldset>
        </div>
        <script type="text/javascript">
        function Export(module, task, div_id){
            Did = "#"+div_id;
            $.ajax({
               type: "POST",
               url: "/modules/mod_catalog/catalog_ImpExp.php",
               data: "module="+module+"&task="+task,
               beforeSend : function(){
                    $(Did).html( '<img src="/admin/images/ajax-loader.gif"/>');
                },
               success: function(html){
                 $(Did).html(html);
                 $(Did).show("slow");
               }
            });
        }
        </script>
        <?
        AdminHTML::PanelSimpleF();
    } //end of function Form()


    // ================================================================================================
    // Function : GetCategoriesToArray()
    // Version : 1.0.0
    // Date : 14.06.2010
    // Parms :
    // Returns : $arr_categ
    // Description :  Get Categories To Array
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GetCategoriesToArray() {

        $q = "SELECT
                    `".TblModCatalog."`.`id`,
                    `".TblModCatalog."`.`cod_pli`,
                    `".TblModCatalog."`.`level`,
                    `".TblModCatalogSprName."`.`name`
              FROM
                    `".TblModCatalog."`,
                    `".TblModCatalogSprName."`
              WHERE
                    `".TblModCatalog."`.`id`=`".TblModCatalogSprName."`.`cod`
              AND
                    `".TblModCatalogSprName."`.`lang_id`='"._LANG_ID."'
             ";
        $q .= " ORDER BY `".TblModCatalog."`.`move` asc";

        $res = $this->db->db_Query($q);
        //echo '<br>'.$q.'<br/> $res='.$res.' $this->db->result='.$this->db->result;
        if( !$res OR !$this->db->result) return false;
        $rows = $this->db->db_GetNumRows();
        //echo '<br>$rows='.$rows;
        $arr_categ = array();
        for($i=0; $i<$rows; $i++){
            $row = $this->db->db_FetchAssoc();
            $index = $row['cod_pli'];
            //$arr_categ[$index]=$row;     // массив категорий
            $arr_categ[$index]['id'] = $row['id'];
            $arr_categ[$index]['level'] = $row['level'];
            $arr_categ[$index]['name'] = stripslashes($row['name']);
        }
        //print_r($arr_categ);
        return $arr_categ;
    }



    // ================================================================================================
    // Function : GetProductsToArray()
    // Version : 1.0.0
    // Date : 14.06.2010
    // Parms :
    // Returns : $arr_categ
    // Description :  Get Products To Array
    // Programmer : Yaroslav Gyren
    // ================================================================================================
    function GetProductsToArray() {

        $q = "SELECT
                    `".TblModCatalogProp."`.`id`,
                    `".TblModCatalogProp."`.`cod_pli`,
                    `".TblModCatalogProp."`.`id_cat`,
                    `".TblModCatalogProp."`.`number_name`,
                    `".TblModCatalogProp."`.`exist`,
                    `".TblModCatalogProp."`.`price`,
                    `".TblModCatalogProp."`.`price_currency`,
                    `".TblModCatalogPropSprName."`.`name`
              FROM
                    `".TblModCatalogProp."`,
                    `".TblModCatalogPropSprName."`
              WHERE
                    `".TblModCatalogProp."`.`id`=`".TblModCatalogPropSprName."`.`cod`
              AND
                    `".TblModCatalogPropSprName."`.`lang_id`='"._LANG_ID."'
             ";
        $q .= " ORDER BY `".TblModCatalogProp."`.`move` asc";

        $res = $this->db->db_Query($q);
        //echo '<br>'.$q.'<br/> $res='.$res.' $this->db->result='.$this->db->result;
        if( !$res OR !$this->db->result) return false;
        $rows = $this->db->db_GetNumRows();
        //echo '<br>$rows='.$rows;
        $arr_prod = array();
        for($i=0; $i<$rows; $i++){
            $row = $this->db->db_FetchAssoc();
            $index =$row['cod_pli'];
            $arr_prod[$index]['id'] = $row['id'];
            $arr_prod[$index]['id_cat'] = $row['id_cat'];
            $arr_prod[$index]['number_name'] = stripslashes($row['number_name']);
            $arr_prod[$index]['name'] = stripslashes($row['name']);
            $arr_prod[$index]['exist'] = $row['exist'];
            $arr_prod[$index]['price'] = $row['price'];
            $arr_prod[$index]['price_currency'] = $row['price_currency'];
        }
        //print_r($arr_prod);
        return $arr_prod;
    }

    // ================================================================================================
    //    Function          : CheckIfCategoryExist
    //    Version           : 1.0.0
    //    Date              : 16.06.2010
    //    Parms             : $cat_name - category name
    //                           $cat_cod -  category cod
    //    Returns           : action: none | update | insert
    //    Description       : Check If Exist Category by id
    // ================================================================================================
    function CheckIfCategoryExist ($cat_name, $cat_id, $level)
    {
        if(isset($this->arr_cat_db[$cat_id]) ) {
            if ( $this->arr_cat_db[$cat_id]['name'] != $cat_name  or $this->arr_cat_db[$cat_id]['level'] != $level ) {
                //echo ' Update: '.$this->arr_cat_db[$cat_id]['name'].' to '.$cat_name.', '.$this->arr_cat_db[$cat_id]['level'].' to '.$level;
                echo $this->arr_cat_db[$cat_id]['id'];
                return $this->arr_cat_db[$cat_id]['id'];   // id product for to update
            }
            return -1; // none
        }
        return 0;   // insert
    }// end of function CheckIfCategoryExist()


    // ================================================================================================
    //    Function          : CheckIfCategoryExist
    //    Version           : 1.0.0
    //    Date              : 16.06.2010
    //    Parms             :  $prod_name - product name
    //                            $cod -  category cod
    //    Returns           : action: none | update | insert
    //    Description       : Check If Exist Product by id
    // ================================================================================================
    function CheckIfProductExist ($cod, $id_cat, $number_name, $prod_name, $exist, $price, $valuta)
    {
        $this->cause = NULL;
        if(isset($this->arr_prod_db[$cod]) ) {
            $this->chekcPole($prod_name,'name',$cod,'Обновлено название товара');
            $this->chekcPole($id_cat,'id_cat',$cod,'Обновлено код категории');
            $this->chekcPole($number_name,'number_name',$cod,'Смена числового названия');
            $this->chekcPole($exist,'exist',$cod,'Смена статуса наличия');
            $this->chekcPole($price,'price',$cod,'Обновлена цена товара');
            $this->chekcPole($valuta,'price_currency',$cod,'Обновлена валюта');

            if($this->cause) return $this->arr_prod_db[$cod]['id'];//update
            else return -1;  // none
        }
        return 0;  // insert
    } // end of function CheckIfProductExist()

     function chekcPole($new_pole,$name_old_pole,$cod,$name_cause = 'Не Установлено'){
         if ( $this->arr_prod_db[$cod][$name_old_pole] != $new_pole ){
             if(!empty($this->cause)) $this->cause .= ', ';
             $this->cause .= $name_cause;
         }
     }

    // ================================================================================================
    //    Function          : GetMaxMove
    //    Version           : 1.0.0
    //    Date              : 16.06.2010
    //    Parms            :
    //    Returns           :  $row['MAX(`move`)'];
    //    Description       : GetMaxMove
    // ================================================================================================
    function GetMaxMove($tblName)
    {
        $q = "SELECT MAX(`move`) FROM `".$tblName."` WHERE 1";
        $res = $this->db->db_Query($q);
        //echo "<br>q = .".$q." res = ".$res;
        $row = $this->db->db_FetchAssoc();
        return  $row['MAX(`move`)'];
    }

    // ================================================================================================
    //    Function        : Generate_Description
    //    Date              : 17.07.2010
    //    Description      : Generate_Description same as ID
    // ================================================================================================
   function Generate_Description()
   {
        $q = "SELECT
                    `".TblModCatalogProp."`.`id`
              FROM
                    `".TblModCatalogProp."` ";
        $q .= " ORDER BY `".TblModCatalogProp."`.`id` asc";
        $res = $this->db->db_Query($q);
        $rows1 = $this->db->db_GetNumRows();
        //echo "<br>q = ".$q." rows1 = ".$rows1;
        $db = new DB();
        $n= 0;
        for($i=0; $i<$rows1; $i++) {
            $row = $this->db->db_FetchAssoc();

            $q = "SELECT COUNT(cod) as count FROM
                        `".TblModCatalogPropSprShort."`
                         WHERE `cod` ='".$row['id']."'
                         AND `lang_id` ='"._LANG_ID."'
                         ";
             $res = $db->db_Query($q);
             $rows = $db->db_GetNumRows();
             $count_row = $db->db_FetchAssoc();

             if($count_row['count']==0) {
                $q = "INSERT INTO `".TblModCatalogPropSprShort."` SET
                  `cod`='".$row['id']."',
                  `lang_id`='"._LANG_ID."',
                  `name`= '' ";
                  $res_c = $db->db_Query($q);
                  /*if($n==0)
                    echo "<br>q = ".$q." res2 = ".$res_c;*/
                $n++;
            }
        }
        if($n > 0 )
            echo 'Успешно создано '.$n.' описаний.<br/>';
        else
            echo 'Описания не созданы.<br/>';
 }

    // ================================================================================================
    //    Function        : Set_Cod_PLI
    //    Version          : 1.0.0
    //    Date              : 16.06.2010
    //    Parms            :
    //    Returns          :
    //    Description      : Set_Cod_PLI same as ID
    // ================================================================================================
   function Generate_PLI()
   {
        $cat  = $this->Set_Cod_PLI(TblModCatalog);
        if($cat)
            echo 'Успешно обновлено '.$cat.' категорий.';
        else
            echo 'Ошибка обновления категорий.';

        $prod = $this->Set_Cod_PLI(TblModCatalogProp);
        if($prod)
            echo '<br/>Успешно обновлено '.$prod.' товаров.';
        else
            echo '<br/>Ошибка обновления товаров.';

   }

    // ================================================================================================
    //    Function        : Set_Cod_PLI
    //    Version          : 1.0.0
    //    Date              : 16.06.2010
    //    Parms            :
    //    Returns          :
    //    Description      : Set_Cod_PLI same as ID
    // ================================================================================================
     function Set_Cod_PLI($tblName)
    {
       $q = "SELECT id FROM `".$tblName."`";
        $res = $this->db->db_Query($q);
        //echo "<br>q = ".$q." res = ".$res;
        $rows = $this->db->db_GetNumRows();
        //echo '<br>$rows='.$rows;
        $db = new DB();

        for($i=0; $i<$rows; $i++) {
            $row = $this->db->db_FetchAssoc();
            $q2 ="UPDATE `".$tblName."` SET `cod_pli` =  '".$row['id']."' WHERE `id` ='".$row['id']."' ";
            $res2 = $db->db_Query($q2);
            if( !$res2 OR !$db->result)
                return false;
            //echo "<br>q2 = ".$q2." res2 = ".$res2;
        }
        return $rows;
     }

    // ================================================================================================
    // Function : xfgetcsv()
    // Date : 06.08.2010
    // Parms : $path - path to CSV file
    // Returns : true,false / Void
    // Description : similar to PHP function fgetcsv, but working correctly
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
     function xfgetcsv($f='', $x='', $s=';') {
        $Q='"';
        if ($csv_str = fgets($f)) {

            preg_match_all (
                  '~('.$Q.'[^'.$Q.']*?('.$Q.$Q.'[^'.$Q.']*?)*?'.$Q.'|[^'.$s.']*?)('.$s.'|$)~si'
                , $csv_str
                , $arr
            );
            if (empty ($arr[3][($last = sizeof ($arr[3]) - 1) - 1])) {
                unset ($arr[1][$last]);
            }
            $arr = $arr[1];
            return $arr;
        } else {
            return FALSE;
        }
    }//end of function xfgetcsv()

    // ================================================================================================
    // Function : CSVtoArr()
    // Date : 04.01.2011
    // Parms : $path - path to CSV file
    // Returns : true,false / Void
    // Description :  Create or Update descriptions of categories and goods from .csv-file to the database
    // Programmer : Yaroslav Gyrнn
    // ================================================================================================
    function CSVtoArr($path=NULL)
    {
        $row = 1;
        $cod_cat = 0;
        $this->move = null;
        $this->ins_cat_counter = 0;  // Счетчик добавленных категорий
        $this->upd_cat_counter = 0; // Счетчик обновленных категорий
        $this->ins_counter = 0;
        $this->ins_counter_err = 0;
        $this->upd_counter = 0;
        $this->upd_counter_err = 0;
        $this->data_success = '';
        $this->data_faild = '';
        $this->arr_cat_db = array();
        $this->arr_cat = array();
        $arr_prod =  array();

        // Считываем данные по категориям из БД и формируем массив с данными
        $this->arr_cat_db = $this->GetCategoriesToArray();
        //print_r($this->arr_cat_db);

        // Считываем данные из файла и формируем массивы с данными для сохранения в БД
        $handle = fopen($path, "r");
        //while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
        while (($data = $this->xfgetcsv($handle, 1000, ';')) !== FALSE) {
            //Пропускаем первую строку, так как это заголовок
            if($row<2) {
                $row++;
                continue;
            }
            //var_dump($data);
            $num = count($data);
            //echo "<p> $num полей в строке $row: <br /></p>\n";

            # Структура входного файла:
            # 0 Код категории товара (уникальное числовое значение)
            # 1 Код родительской категории
            # 2 Наименование категории товара
            # 3 Числовой уникальный код товара
            # 4 Артикул -  уникальное числовое значение
            # 5 Наименование товара
            # 6 Наличие
            # 7 Цена товара
            # 8 Валюта товара

            // Если нет всех данных, значит это категория
            if( empty($data[3]) AND empty($data[4]) AND empty($data[5] ) AND empty($data[6] ) AND empty($data[7] )  AND empty($data[8] ) ){
                $cod_cat =  $this->Conv(trim($data[0]));
                $this->arr_cat[$cod_cat]['level'] = $this->Conv(trim($data[1]));
                $this->arr_cat[$cod_cat]['name'] = $this->Conv(trim($data[2]));
                $this->arr_treeCatLevels[$cod_cat_parent][$cod_cat]='';
            }
            else { // Заполнение данных товара
                $cod_prod = $this->Conv(trim($data[3]));
                $cod_categ = $this->Conv(trim($data[0]));
                $arr_prod[$cod_prod]['cod_categ'] = $cod_categ;
                $this->arr_treePropLevels[$cod_categ][$cod_prod]='';
                $arr_prod[$cod_prod]['level'] = $this->Conv(trim($data[1]));
                $arr_prod[$cod_prod]['number_name'] = $this->Conv(trim($data[4]));
                $arr_prod[$cod_prod]['name'] = $this->Conv(trim($data[5]));
                $arr_prod[$cod_prod]['exist'] = $this->Conv(trim($data[6]));
                $arr_prod[$cod_prod]['price'] = $this->Conv(trim($data[7]));
                if(!empty($data[8]))
                    $arr_prod[$cod_prod]['valuta'] = $this->Conv(trim($data[8]));
                else
                    $arr_prod[$cod_prod]['valuta'] ='';
            }
            $row++;
        }
        fclose($handle);
        //echo '<br>Categories:';
        //print_r($arr_cat);
        //echo '<br>Products:';
        //print_r($arr_prod);

        //Создаем или обновляем категории каталога
        foreach($this->arr_cat as $cod=>$v) {
            $this->CatIteration($cod, $v);
        }//end of foreach
        //
        //Сортируем категории в соответствии с порядком в файле импорта.
        $res = $this->SortCategories(0);
        if($res) echo '<br>Сортировка категорий произведена.';
        else echo '<br>Сбой в сортировке категорий';

        //print_r($this->arr_cat);

        // Считываем данные по товарам из БД и формируем массив с данными
        $this->arr_prod_db = $this->GetProductsToArray();

        $move = null;
        //Обновляем позиции каталога
        //echo '<br/>Категорії <br/>';
        //print_r($this->arr_cat_db);
        //echo '<br/><br/>Товари<br/>';
        //print_r($arr_prod);
        foreach($arr_prod as $cod=>$v){
            //$id_cat = $v['cod_categ'];
            $id_cat = $this->arr_cat_db[$v['cod_categ']]['id'];
            $id_cat_cod_pli = $v['cod_categ'];
            //$level = $v['level'];
            //echo '<br/> $v[level]=',$v['level'];
            //echo "<br/>this->arr_cat_db[v['level']]['id'] =".$this->arr_cat_db[$v['level']]['id'];
            if($v['level']==0) {
                $level  = 0;
                //echo '$level = 0<br/>';
            }
            else
                $level = $this->arr_cat_db[$v['level']]['id'];
            $number_name = $v['number_name'];
            $prod_name = $v['name'];
            if($v['exist']=='+' or $v['exist']=='Есть' or $v['exist']=='есть')
                $exist= '1'; // є
            else
                $exist= '2'; //нема
            //$price = explode("грн", $v['price']);
            $price = $v['price'];
            $valuta = '1';
            if($v['valuta']=='$')
                $valuta = '1'; //usd
            if($v['valuta']=='UAH')
                $valuta = '5'; //грн
            /*if($v['valuta']=='RUB')
                $valuta = '6'; //рубли*/

            $action = $this->CheckIfProductExist($cod, $id_cat, $number_name, $prod_name, $exist, $price, $valuta);

            // Insert
            if($action == 0 )  {
                if($move == null)
                    $move = $this->GetMaxMove(TblModCatalogProp);
                $move+=1;

                $q = "INSERT INTO `".TblModCatalogProp."` SET
                      `cod_pli`='".$cod."',
                      `id_cat`='".$id_cat."',
                      `id_manufac`='0',
                      `id_group`='0',
                      `img`='',
                      `exist`='".addslashes($exist)."',
                      `number_name`='".addslashes($number_name)."',
                      `price`='".addslashes($price)."',
                      `opt_price`='',
                      `grnt`='',
                      `dt`='".date("Y-m-d")."',
                      `move`='".$move."',
                      `visible`='2',
                      `price_currency`='".$valuta."',
                      `opt_price_currency`='0',
                      `new`='0',
                      `best`='0',
                      `art_num`='0',
                      `barcode`='0'
                      ";
                $res = $this->db->db_Query($q);
                //echo "<br>Insert Prod  = ".$q.' res = '.$res.' $this->db->result='.$this->db->result;

                if( !$res OR !$this->db->result) {
                    $this->data_faild.='
                    <Row>
                     <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                     <Cell><Data ss:Type="String">'.$prod_name.'</Data></Cell>
                     <Cell><Data ss:Type="String">Ошибка добавления товара в каталог</Data></Cell>
                     <Cell><Data ss:Type="String">'.$q.' res = '.$res.' $this->db->result='.$this->db->result.' $this->db->db_GetErrNo()='.$this->db->db_GetErrNo().' $this->db->db_GetErrDetail()='.$this->db->db_GetErrDetail().'</Data></Cell>
                    </Row>
                    ';
                    $this->ins_counter_err++;
                    continue;
                }

                $id_prop = $this->db->db_GetInsertID();
                /*if( $id_prop != $cod){
                    $this->data_faild.='
                    <Row>
                     <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                     <Cell><Data ss:Type="String">'.$prod_name.'</Data></Cell>
                     <Cell><Data ss:Type="String">Ошибка при добавлении нового товара, несоответствие ID</Data></Cell>
                    </Row>
                    ';
                }*/

                $q = "INSERT INTO `".TblModCatalogPropSprName."` SET
                      `cod`='".$id_prop."',
                      `lang_id`='".$this->insert_lang_id."',
                      `name`='".addslashes($prod_name)."'";
                $res_c = $this->db->db_Query($q);

                //echo "<br>Insert Prod Name = ".$q.' $res_c = '.$res_c.' $this->db->result='.$this->db->result;

                if( !$res_c OR !$this->db->result){
                    echo "<br>Ошибка вставки наименования товара = ".$q.' $res_c = '.$res_c.' $this->db->result='.$this->db->result;
                    $this->data_faild.='
                    <Row>
                     <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                     <Cell><Data ss:Type="String">'.$prod_name.'</Data></Cell>
                     <Cell><Data ss:Type="String">Ошибка добавления наименования товара</Data></Cell>
                     <Cell><Data ss:Type="String">'.$q.' $res_c = '.$res_c.' $this->db->result='.$this->db->result.' $this->db->db_GetErrNo()='.$this->db->db_GetErrNo().' $this->db->db_GetErrDetail()='.$this->db->db_GetErrDetail().'</Data></Cell>
                    </Row>
                    ';
                    $this->ins_counter_err++;
                    continue;
                }


                $q = "INSERT INTO `".TblModCatalogPropSprShort."` SET
                      `cod`='".$id_prop."',
                      `lang_id`='".$this->insert_lang_id."',
                      `name`= '' ";
                $res_c = $this->db->db_Query($q);
                if( !$res_c OR !$this->db->result){
                    echo "<br>Ошибка вставки описания = ".$q.' $res_c = '.$res_c.' $this->db->result='.$this->db->result;
                    $this->data_faild.='
                    <Row>
                     <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                     <Cell><Data ss:Type="String">'.$prod_name.'</Data></Cell>
                     <Cell><Data ss:Type="String">Ошибка добавления описания товара</Data></Cell>
                     <Cell><Data ss:Type="String">'.$q.' $res_c = '.$res_c.' $this->db->result='.$this->db->result.' $this->db->db_GetErrNo()='.$this->db->db_GetErrNo().' $this->db->db_GetErrDetail()='.$this->db->db_GetErrDetail().'</Data></Cell>
                    </Row>
                    ';
                    $this->ins_counter_err++;
                    continue;
                }

                #TblModCatalogPropSprShort   - добавить в короткий опис, бо не виводится на frontendi.

                //save translit
                $name[$this->insert_lang_id] = $prod_name;
                $res = $this->SaveTranslitProp($id_cat, $level, $id_prop, $name);
                if( !$res){
                    $this->data_faild.='
                    <Row>
                     <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                     <Cell><Data ss:Type="String">'.$prod_name.'</Data></Cell>
                     <Cell><Data ss:Type="String">Ошибка создания транслитерации для товара</Data></Cell>
                     <Cell><Data ss:Type="String">'.$q.' $res = '.$res.' $this->db->result='.$this->db->result.' $this->db->db_GetErrNo()='.$this->db->db_GetErrNo().' $this->db->db_GetErrDetail()='.$this->db->db_GetErrDetail().'</Data></Cell>
                    </Row>
                    ';
                    $this->ins_counter_err++;
                    continue;
                }

                $this->data_success.='
                <Row>
                 <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                 <Cell><Data ss:Type="String">'.$prod_name.'</Data></Cell>
                 <Cell><Data ss:Type="String">Добавлен товар</Data></Cell>
                </Row>
                ';
                $this->ins_counter++;
            }

            // Update
            if($action> 0 ) {
                    $q = "UPDATE `".TblModCatalogProp."` SET
                          `id_cat`='".$id_cat."',
                          `exist`='".addslashes($exist)."',
                          `number_name`='".addslashes($number_name)."',
                          `price`='".addslashes($price)."',
                          `price_currency`='".$valuta."',
                          `dt`='".date("Y-m-d")."'
                          WHERE `id`='".$action."'";
                    $res = $this->db->db_Query($q);

                    if( !$res OR !$this->db->result){
                    //echo "<br>Error Update Prod  = ".$q.' res = '.$res.' $this->db->result='.$this->db->result;
                        $this->data_faild.='
                        <Row>
                         <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                         <Cell><Data ss:Type="String">'.$prod_name.'</Data></Cell>
                         <Cell><Data ss:Type="String">Ошибка обновления данных товара</Data></Cell>
                         <Cell><Data ss:Type="String">'.$q.' $res = '.$res.' $this->db->result='.$this->db->result.' $this->db->db_GetErrNo()='.$this->db->db_GetErrNo().' $this->db->db_GetErrDetail()='.$this->db->db_GetErrDetail().'</Data></Cell>
                        </Row>
                        ';
                        $this->upd_counter_err++;
                        continue;
                    }
                    $q = "UPDATE `".TblModCatalogTranslit."` SET
                    `id_cat`='".$id_cat."',
                    `id_cat_parent`='".$level."'
                    WHERE `id_prop`='".$action."'";
                    $res = $this->db->db_Query($q);

                    if( !$res OR !$this->db->result){
                    echo "<br>Error Update Prod  = ".$q.' res = '.$res.' $this->db->result='.$this->db->result;
                        $this->data_faild.='
                        <Row>
                         <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                         <Cell><Data ss:Type="String">'.$prod_name.'</Data></Cell>
                         <Cell><Data ss:Type="String">Ошибка обновления транслита товара</Data></Cell>
                         <Cell><Data ss:Type="String">'.$q.' $res = '.$res.' $this->db->result='.$this->db->result.' $this->db->db_GetErrNo()='.$this->db->db_GetErrNo().' $this->db->db_GetErrDetail()='.$this->db->db_GetErrDetail().'</Data></Cell>
                        </Row>
                        ';
                        $this->upd_counter_err++;
                        continue;
                    }

                    //------------- Update Prod Name SATRT -------------
                    /*
                    $q = "UPDATE `".TblModCatalogPropSprName."`
                            SET `name`='".addslashes($prod_name)."'
                            WHERE `cod`='".$action."'
                            AND `lang_id`='".$this->insert_lang_id."' ";
                    $res_c = $this->db->db_Query($q);
                    //echo "<br>Update Prod Name = ".$q.' $res_c = '.$res_c.' $this->db->result='.$this->db->result;


                    if( !$res_c OR !$this->db->result){
                        $this->data_faild.='
                        <Row>
                         <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                         <Cell><Data ss:Type="String">'.$prod_name.'</Data></Cell>
                         <Cell><Data ss:Type="String">Ошибка обновления наименования товара</Data></Cell>
                         <Cell><Data ss:Type="String">'.$q.' $res_c = '.$res_c.' $this->db->result='.$this->db->result.' $this->db->db_GetErrNo()='.$this->db->db_GetErrNo().' $this->db->db_GetErrDetail()='.$this->db->db_GetErrDetail().'</Data></Cell>
                        </Row>
                        ';
                        $this->upd_counter_err++;
                        continue;
                    }
                    */
                   //------------- Update Prod Name END -------------

                    $this->data_success.='
                    <Row>
                     <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                     <Cell><Data ss:Type="String">'.$prod_name.'</Data></Cell>
                     <Cell><Data ss:Type="String">Обновлен товар(Причина: '.$this->cause.')</Data></Cell>
                    </Row>
                    ';

                    //oобновляем в таблице транслита категорию, если товар перенес в другую категорию
                    if($id_cat!=$this->arr_prod_db[$cod]['id_cat']){
                        $name[$this->insert_lang_id] = $this->arr_prod_db[$cod]['name'];
                        $cat_name = $this->arr_cat_db[$id_cat_cod_pli]['name'];
                        $res = $this->SaveTranslitProp($id_cat, $level, $id_prop, $name, NULL);
                        if( !$res ){
                            $data_faild.='
                            <Row>
                             <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                             <Cell><Data ss:Type="String">'.$cat_name.'</Data></Cell>
                             <Cell><Data ss:Type="String">Ошибка обновления категории в транслитерации для обновленного товара</Data></Cell>
                             <Cell><Data ss:Type="String">'.$q.' $res = '.$res.' $this->db->result='.$this->db->result.' $this->db->db_GetErrNo()='.$this->db->db_GetErrNo().' $this->db->db_GetErrDetail()='.$this->db->db_GetErrDetail().'</Data></Cell>
                            </Row>
                            ';
                            continue;
                        }
                        $this->data_success.='
                        <Row>
                         <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                         <Cell><Data ss:Type="String">'.$cat_name.'</Data></Cell>
                         <Cell><Data ss:Type="String">Обновлена категория в транслитерации для товара</Data></Cell>
                        </Row>
                        ';
                    }
                    $this->upd_counter++;
            }//end if()

        }//end of foreach

        //Удаляем товары из базы данных сайта, которых нет в файле выгрузки
        //$this->del_counter = $this->deleteOldProp();
        //$this->del_counter = 0;

        //Сортируем товары в категории в соответствии с порядком в файле импорта.
        $res = $this->SortProp();
        if($res) echo '<br>Сортировка товаров произведена.';
        else echo '<br>Сбой в сортировке товаров.';

        //Оптимизируем базуданных после выгрузки.
        $this->db_OptimizeDB();

        $log_faild_file = SITE_PATH."/import/logs/faild/log.xls";
        $log_success_file = SITE_PATH."/import/logs/success/log.xls";
        if(file_exists($log_faild_file))
            unlink ($log_faild_file);
        if(file_exists($log_success_file))
            unlink ($log_success_file);

        //if(!empty($this->data_success))
        //    $this->save_log($this->data_success, 'success');
        if(!empty($this->data_faild))
            $this->save_log($this->data_faild, 'faild');
        ?>
        <div style="font-size: 11px;">
         Добавлено новых категорий:&nbsp;<?=$this->ins_cat_counter;?>
         <br/>Обновлено категорий:&nbsp;<?=$this->upd_cat_counter;?>
         <br/>Добавлено новых товаров:&nbsp;<?=$this->ins_counter;?>
         <?if($this->ins_counter_err > 0){
                ?><br/>Не удалось добавить новых товаров:<?= $this->ins_counter_err;
         }?>
         <br/>Обновлено товаров:&nbsp;<?=$this->upd_counter;?>
         <?if($this->upd_counter_err > 0){
            ?><br/>Не удалось обновить товаров:<?= $this->upd_counter_err;
         }
         ?><br/><?
         if( file_exists($log_faild_file)){
             ?><br>Просмотр ошибок импорта: <a href="<?=$log_faild_file;?>" target="_blank" title="Посмотреть лог-файл с ошибками импорта">log.xls</a><?
         }
         if( file_exists($log_success_file)){
             ?><br>Просмотр результатов импорта: <a href="<?=$log_success_file;?>" target="_blank" title="Посмотреть лог-файл успешного импорта">log.xls</a><?
         }
         ?>
        </div>
        <?
        return true;
    } // end of CSVtoArr

    /**
    * Class method SortCategories
    * sort categories in DB as in import file
    * @param integer $level - id of the category
    * @return true/false
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 17.08.2012
    */
    function SortCategories($level){
        if(!isset($this->arr_treeCatLevels[$level])) return false;
        $keys = array_keys($this->arr_treeCatLevels[$level]);
        $cnt = count($this->arr_treeCatLevels[$level]);
        for($i=0;$i<$cnt;$i++){
            $cod = $keys[$i];
            $id = $this->arr_cat_db[$cod]['id'];
            $q = "UPDATE `".TblModCatalog."` SET
                  `move` = '".($i+1)."'
                  WHERE `".TblModCatalog."`.`id`='".$id."'
                 ";
            $res = $this->db->db_Query($q);
            //echo '<br>'.$q.'<br/> $res='.$res.' $this->db->result='.$this->db->result;
            if(!$res) return false;
            $this->SortCategories($cod);
        }
        return true;
    }

    /**
    * Class` method SortProp
    * sort items in categories in DB as in import file
    * @return true/false
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 17.08.2012
    */
    function SortProp(){
        //echo '<br>$this->arr_treePropLevels'.$this->arr_treePropLevels;print_r($this->arr_treePropLevels);
        if(!isset($this->arr_treePropLevels)) return false;
        $keys = array_keys($this->arr_treePropLevels);
        $cnt = count($this->arr_treePropLevels);
        for($i=0;$i<$cnt;$i++){
            $cod_cat = $keys[$i];
            $keys_prop = array_keys($this->arr_treePropLevels[$cod_cat]);
            $cnt_prop = count($this->arr_treePropLevels[$cod_cat]);
            //echo '<br>$cnt_prop='.$cnt_prop;
            for($j=0;$j<$cnt_prop;$j++){
                $id = $this->arr_prod_db[$keys_prop[$j]]['id'];
                if(!$id) continue;
                $q = "UPDATE `".TblModCatalogProp."` SET
                      `move` = '".($j+1)."'
                      WHERE `".TblModCatalogProp."`.`id`='".$id."'
                     ";
                $res = $this->db->db_Query($q);
                //echo '<br>'.$q.'<br/> $res='.$res.' $this->db->result='.$this->db->result;
                if(!$res) return false;
            }
        }
        return true;
    }


    /**
    * Class` method deleteOldProp
    * delete postions from Catalog which not exist in import file
    * @param integer $level - id of the category
    * @return true/false
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 17.08.2012
    */
    function deleteOldProp() {
        $q = "select id
            from " . TblModCatalogProp . "
            where `id_group`='0'";
        $res = $this->db->db_Query($q);
        //echo '<br>'.$q.'<br/> $res='.$res.' $this->db->result='.$this->db->result;
        if (!$res OR !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        //echo '<br>$rows='.$rows;
        $arr = array();
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc();
            $arr[$i] = $row['id'];
        }
        $this->CatalogContent = new Catalog_content($this->user_id, $this->module);
        $count = $this->CatalogContent->DelContent($arr);
        return $count;
    }


    // ================================================================================================
    // Function : CatIteration()
    // Date : 04.01.2011
    // Parms : $cod, $v
    // Programmer : Yaroslav Gyryn, Ihor Throhymcjuk
    // ================================================================================================
    function CatIteration($cod, $v){
        $cat_name = $v['name'];
        //print_r($this->arr_cat_db);
        // Установка level
        if($v['level']==0)
            $level=0;
        else {
            if(isset($this->arr_cat_db[$v['level']]['id']))
                $level = $this->arr_cat_db[$v['level']]['id'];
            else{

                $level = $this->CatIteration($v['level'], $this->arr_cat[$v['level']]);
                if($level==false) $level=0;
            }
        }
        //echo '<br>$level='.$level;

        // Установка id_cat
        if(isset($this->arr_cat_db[$cod]['id']))
            $id_cat = $this->arr_cat_db[$cod]['id'];
        else
            $id_cat='';

        $action = $this->CheckIfCategoryExist($cat_name, $cod, $level);
        // Insert
        if($action == 0) {
            if($this->move == null)
                $this->move = $this->GetMaxMove(TblModCatalog);
            $this->move+=1;

            $q="INSERT INTO `".TblModCatalog."` SET
                `cod_pli`='".$cod."',
                `group`='0',
                `level`='".$level."',
                `move`='".$this->move."',
                `visible`='2'
                ";
            $res = $this->db->db_Query( $q );
            //echo "<br>Insert Cat = ".$q." res = ".$res;
            if( !$res OR !$this->db->result){
                $this->data_faild.='
                <Row>
                 <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                 <Cell><Data ss:Type="String">'.$cat_name.'</Data></Cell>
                 <Cell><Data ss:Type="String">Ошибка добавления новой категории</Data></Cell>
                 <Cell><Data ss:Type="String">'.$q.' $res = '.$res.' $this->db->result='.$this->db->result.' $this->db->db_GetErrNo()='.$this->db->db_GetErrNo().' $this->db->db_GetErrDetail()='.$this->db->db_GetErrDetail().'</Data></Cell>
                </Row>
                ';
                return false;
            }

            $id_cat = $this->db->db_GetInsertID();


            $q_cat = "INSERT INTO `".TblModCatalogSprName."` SET
                      `cod`='".$id_cat."',
                      `lang_id`='".$this->insert_lang_id."',
                      `name`='".addslashes($cat_name)."'";

            $res_c = $this->db->db_Query( $q_cat );// echo $q_cat."<br/>";
            //echo "<br>Insert Cat Name = ".$q_cat." res = ".$res_c;
            if( !$res_c OR !$this->db->result){
                $this->data_faild.='
                <Row>
                 <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                 <Cell><Data ss:Type="String">'.$cat_name.'</Data></Cell>
                 <Cell><Data ss:Type="String">Ошибка добавления наименования новой категории</Data></Cell>
                 <Cell><Data ss:Type="String">'.$q.' $res_c = '.$res_c.' $this->db->result='.$this->db->result.' $this->db->db_GetErrNo()='.$this->db->db_GetErrNo().' $this->db->db_GetErrDetail()='.$this->db->db_GetErrDetail().'</Data></Cell>
                </Row>
                ';
                return false;
            }

            $q_cat = "INSERT INTO `".TblModCatalogSprNameInd."` SET
                      `cod`='".$id_cat."',
                      `lang_id`='".$this->insert_lang_id."',
                      `name`='".addslashes($cat_name)."'";
            $res_c = $this->db->db_Query( $q_cat );// echo $q_cat."<br/>";
            //echo "<br>Insert Cat Ind Name = ".$q_cat." res = ".$res_c;
            if( !$res_c OR !$this->db->result){
                $this->data_faild.='
                <Row>
                 <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                 <Cell><Data ss:Type="String">'.$cat_name.'</Data></Cell>
                 <Cell><Data ss:Type="String">Ошибка добавления единичного наименования новой категории</Data></Cell>
                 <Cell><Data ss:Type="String">'.$q.' $res_c = '.$res_c.' $this->db->result='.$this->db->result.' $this->db->db_GetErrNo()='.$this->db->db_GetErrNo().' $this->db->db_GetErrDetail()='.$this->db->db_GetErrDetail().'</Data></Cell>
                </Row>
                ';
                return false;
            }

            $q = "INSERT INTO `".TblModCatalogSprDescr."` SET
                      `cod`='".$id_cat."',
                      `lang_id`='".$this->insert_lang_id."',
                      `name`=''";
                $res_c = $this->db->db_Query($q);//echo $q."<br/>";

                //echo "<br>Insert Prod Name = ".$q.' $res_c = '.$res_c.' $this->db->result='.$this->db->result;

                if( !$res_c OR !$this->db->result){
                    echo "<br>Ошибка вставки наименования товара = ".$q.' $res_c = '.$res_c.' $this->db->result='.$this->db->result;
                    $this->data_faild.='
                    <Row>
                     <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                     <Cell><Data ss:Type="String">'.$cat_name.'</Data></Cell>
                     <Cell><Data ss:Type="String">Ошибка добавления описания товара товара</Data></Cell>
                     <Cell><Data ss:Type="String">'.$q.' $res_c = '.$res_c.' $this->db->result='.$this->db->result.' $this->db->db_GetErrNo()='.$this->db->db_GetErrNo().' $this->db->db_GetErrDetail()='.$this->db->db_GetErrDetail().'</Data></Cell>
                    </Row>
                    ';
                    return false;
                }

                $q = "INSERT INTO `".TblModCatalogSprDescr2."` SET
                      `cod`='".$id_cat."',
                      `lang_id`='".$this->insert_lang_id."',
                      `name`=''";
                $res_c = $this->db->db_Query($q);//echo $q."<br/>";exit;

                //echo "<br>Insert Prod Name = ".$q.' $res_c = '.$res_c.' $this->db->result='.$this->db->result;

                if( !$res_c OR !$this->db->result){
                    echo "<br>Ошибка вставки наименования товара = ".$q.' $res_c = '.$res_c.' $this->db->result='.$this->db->result;
                    $this->data_faild.='
                    <Row>
                     <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                     <Cell><Data ss:Type="String">'.$cat_name.'</Data></Cell>
                     <Cell><Data ss:Type="String">Ошибка добавления полного описания товара товара</Data></Cell>
                     <Cell><Data ss:Type="String">'.$q.' $res_c = '.$res_c.' $this->db->result='.$this->db->result.' $this->db->db_GetErrNo()='.$this->db->db_GetErrNo().' $this->db->db_GetErrDetail()='.$this->db->db_GetErrDetail().'</Data></Cell>
                    </Row>
                    ';
                   return false;
                }


            //save category translit
            //$parent_id_cat = $this->GetCategory($id_cat);
            $parent_id_cat  = $level;
            $name[$this->insert_lang_id] = $cat_name;
            $res = $this->SaveTranslit($id_cat, $parent_id_cat, $name, NULL );
            if( !$res ){
                $this->data_faild.='
                <Row>
                 <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                 <Cell><Data ss:Type="String">'.$cat_name.'</Data></Cell>
                 <Cell><Data ss:Type="String">Ошибка создания транслитерации для категории</Data></Cell>
                 <Cell><Data ss:Type="String">'.$q.' $res = '.$res.' $this->db->result='.$this->db->result.' $this->db->db_GetErrNo()='.$this->db->db_GetErrNo().' $this->db->db_GetErrDetail()='.$this->db->db_GetErrDetail().'</Data></Cell>
                </Row>
                ';
                return false;
            }

            $this->data_success.='
            <Row>
             <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
             <Cell><Data ss:Type="String">'.$cat_name.'</Data></Cell>
             <Cell><Data ss:Type="String">Добавлена категория</Data></Cell>
            </Row>
            ';
            $this->ins_cat_counter++;

        //добавляем только что установленный id категории в массив с описанием категорий. Это пригодится при добавлении позиций в каталог
        $this->arr_cat_db[$cod]['id']=$id_cat;
        $this->arr_cat_db[$cod]['level']=$parent_id_cat;
        $this->arr_cat_db[$cod]['name']=$name;
        }

        // Update
        if($action  > 0) {
            //наименование категории не обновляем!
//            $q_cat = "UPDATE `".TblModCatalogSprName."` SET
//                      `name`='".add_slashes($cat_name)."'
//                      WHERE `cod`='".$action."'
//                      AND `lang_id`='".$this->insert_lang_id."'";
//
//            $res_c = $this->db->db_Query( $q_cat );
//            //echo "<br>Update Category name = ".$q_cat." res = ".$res_c;
//            if( !$res_c OR !$this->db->result){
//                $this->data_faild.='
//                <Row>
//                 <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
//                 <Cell><Data ss:Type="String">'.$cat_name.'</Data></Cell>
//                 <Cell><Data ss:Type="String">Ошибка обновления наименования категории</Data></Cell>
//                 <Cell><Data ss:Type="String">'.$q.' $res_c = '.$res_c.' $this->db->result='.$this->db->result.' $this->db->db_GetErrNo()='.$this->db->db_GetErrNo().' $this->db->db_GetErrDetail()='.$this->db->db_GetErrDetail().'</Data></Cell>
//                </Row>
//                ';
//                return false;
//            }

            //Обновлять только если изменился уровень для категории
            if($level!=$this->arr_cat_db[$cod]['level']){

                $q="UPDATE `".TblModCatalog."` SET
                    `level`='".$level."'
                    WHERE `id`='".$action."'";

                $res = $this->db->db_Query( $q );
                //echo "<br>Update Cat = ".$q." res = ".$res;

                if( !$res OR !$this->db->result){
                    $this->data_faild.='
                    <Row>
                     <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                     <Cell><Data ss:Type="String">'.$cat_name.'</Data></Cell>
                     <Cell><Data ss:Type="String">Ошибка обновления ID для родительской категории</Data></Cell>
                     <Cell><Data ss:Type="String">'.$q.' $res = '.$res.' $this->db->result='.$this->db->result.' $this->db->db_GetErrNo()='.$this->db->db_GetErrNo().' $this->db->db_GetErrDetail()='.$this->db->db_GetErrDetail().'</Data></Cell>
                    </Row>
                    ';
                }

                //save category translit
                $name[$this->insert_lang_id] = $this->arr_cat_db[$cod]['name'];
                //echo '<br>$cod='.$cod.' $id_cat='.$id_cat.' $level='.$level.' $name='.$name[$this->insert_lang_id].' $this->arr_cat_db[$cod][level]='.$this->arr_cat_db[$cod]['level'];
                $res = $this->SaveTranslit($id_cat, $level, $name, NULL, $this->arr_cat_db[$cod]['level'] );
                if( !$res ){
                    $data_faild.='
                    <Row>
                     <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                     <Cell><Data ss:Type="String">'.$cat_name.'</Data></Cell>
                     <Cell><Data ss:Type="String">Ошибка обновления транслитерации для обновленной категории</Data></Cell>
                     <Cell><Data ss:Type="String">'.$q.' $res = '.$res.' $this->db->result='.$this->db->result.' $this->db->db_GetErrNo()='.$this->db->db_GetErrNo().' $this->db->db_GetErrDetail()='.$this->db->db_GetErrDetail().'</Data></Cell>
                    </Row>
                    ';
                    continue;
                }

                $this->data_success.='
                <Row>
                 <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                 <Cell><Data ss:Type="String">'.$cat_name.'</Data></Cell>
                 <Cell><Data ss:Type="String">Обновлена категория</Data></Cell>
                </Row>
                ';
                $this->upd_cat_counter++;
            }

            $this->arr_cat_db[$cod]['level']=$level;
            //$this->arr_cat_db[$cod]['name']=$cat_name;
        }

        return $id_cat;
    }


    // ================================================================================================
    //    Function          : CheckIfProdExist
    //    Date              : 04.01.2011
    //    Parms             : $id_prop - number name of product
    //                        $prop_name - name of product
    //                        $id_cat - category of product
    //    Returns           : Error Indicator
    //    Description       : Check and update or inserrt products
    // ================================================================================================
    function CheckIfProdExist($number_name, $prop_name, $id_cat=null)
    {
        $this->row_for_update='';
        $q = "select * from `".TblModCatalogProp."` where `number_name`='".$number_name."'";
        $res = $this->db->db_Query($q);
        $rows = $this->db->db_GetNumRows();
        //echo "<br /><hr> q= ".$q." res = ".$res.' $rows='.$rows;
        if($rows>0){
            $row = $this->db->db_FetchAssoc();
            //echo "<br> row = ";
            //print_r($row);
            if(!empty($id_cat) && $id_cat==$row['id_cat']){
                $this->row_for_update=$row;
                return $row['id'];
            }
        }
        else{
            return false;
        }
    } // end of function  CheckIfProdExist

    // ================================================================================================
    // Function : Conv()
    // Version : 1.0.0
    // Date : 29.11.2009
    // Parms : $str - string to conv
    // Returns : true,false / Void
    // Description :  convert string from one charset to another
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function Conv($str)
    {
        //echo '<br/>$this->from_charset ='.$this->from_charset;
        //echo '<br/>$this->to_charset ='.$this->to_charset;
        if($this->to_charset!=$this->from_charset){
            $str = iconv($this->from_charset, $this->to_charset, $str);
        }
        return $str;
    }// end of fucntion function Conv()


    // ================================================================================================
    // Function : save_log()
    // Version : 1.0.0
    // Date : 19.06.2010
    // Parms :
    // Returns : true,false / file
    // Description : save log file of update price
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function save_log($data = '', $type='faild')
    {
        if($type=='faild'){
          $path = 'faild';
        }
        if($type=='success'){
          $path = 'success';
        }
    $head = '<?xml version="1.0"?>
    <?mso-application progid="Excel.Sheet"?>
    <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
     xmlns:o="urn:schemas-microsoft-com:office:office"
     xmlns:x="urn:schemas-microsoft-com:office:excel"
     xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
     xmlns:html="http://www.w3.org/TR/REC-html40">
     <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
      <Author>SEOTM</Author>
      <LastAuthor>SEOTM</LastAuthor>
      <Created>2013-11-19T15:26:17Z</Created>
      <Company>seotm.com</Company>
      <Version>12</Version>
     </DocumentProperties>
     <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
      <WindowHeight>8835</WindowHeight>
      <WindowWidth>15180</WindowWidth>
      <WindowTopX>0</WindowTopX>
      <WindowTopY>120</WindowTopY>
      <ProtectStructure>False</ProtectStructure>
      <ProtectWindows>False</ProtectWindows>
     </ExcelWorkbook>
     <Styles>
      <Style ss:ID="Default" ss:Name="Normal">
       <Alignment ss:Vertical="Bottom"/>
       <Borders/>
       <Font ss:FontName="Arial Cyr" x:CharSet="204"/>
       <Interior/>
       <NumberFormat/>
       <Protection/>
      </Style>
      <Style ss:ID="s24">
       <Alignment ss:Horizontal="Left" ss:Vertical="Top" ss:WrapText="1"/>
       <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
       </Borders>
       <Font x:CharSet="204" x:Family="Swiss"/>
      </Style>
      <Style ss:ID="s25">
       <Alignment ss:Horizontal="Right" ss:Vertical="Top"/>
       <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
       </Borders>
       <Font x:CharSet="204" x:Family="Swiss" ss:Size="8"/>
      </Style>
      <Style ss:ID="s28">
       <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
       <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
       </Borders>
       <Font x:CharSet="204" x:Family="Swiss"/>
       <Interior ss:Color="#008080" ss:Pattern="Solid"/>
      </Style>
      <Style ss:ID="s29">
       <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
       <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
       </Borders>
       <Font x:CharSet="204" x:Family="Swiss"/>
       <Interior ss:Color="#008080" ss:Pattern="Solid"/>
      </Style>
      <Style ss:ID="s34">
       <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
       <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
       </Borders>
       <Font x:CharSet="204" x:Family="Swiss" ss:Color="#FFFFFF" ss:Bold="1"/>
       <Interior ss:Color="#99CCFF" ss:Pattern="Solid"/>
      </Style>
      <Style ss:ID="s35">
       <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
       <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
       </Borders>
       <Font x:CharSet="204" x:Family="Swiss" ss:Color="#FFFFFF" ss:Bold="1"/>
       <Interior ss:Color="#99CCFF" ss:Pattern="Solid"/>
      </Style>
     </Styles>
     <Worksheet ss:Name="Лист1">
      <Table ss:ExpandedColumnCount="3" ss:ExpandedRowCount="10000" x:FullColumns="1"
       x:FullRows="1">
       <Column ss:AutoFitWidth="0" ss:Width="99"/>
       <Column ss:AutoFitWidth="0" ss:Width="180.75"/>
       <Column ss:AutoFitWidth="0" ss:Width="180.75"/>
       <Row>
        <Cell ss:StyleID="s34"><Data ss:Type="String">Код ТЦМ</Data></Cell>
        <Cell ss:StyleID="s34"><Data ss:Type="String">Наименование</Data></Cell>
        <Cell ss:StyleID="s35"><Data ss:Type="String">Статус</Data></Cell>
        <Cell ss:StyleID="s35"><Data ss:Type="String">Дополнительное описание</Data></Cell>
       </Row>
      ';
      $footer = '
      </Table>
      <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
       <PageSetup>
        <PageMargins x:Bottom="0.984251969" x:Left="0.78740157499999996"
         x:Right="0.78740157499999996" x:Top="0.984251969"/>
       </PageSetup>
       <Print>
        <ValidPrinterInfo/>
        <PaperSizeIndex>9</PaperSizeIndex>
        <HorizontalResolution>-3</HorizontalResolution>
        <VerticalResolution>0</VerticalResolution>
       </Print>
       <Selected/>
       <Panes>
        <Pane>
         <Number>3</Number>
         <ActiveRow>1</ActiveRow>
         <ActiveCol>2</ActiveCol>
        </Pane>
       </Panes>
       <ProtectObjects>False</ProtectObjects>
       <ProtectScenarios>False</ProtectScenarios>
      </WorksheetOptions>
     </Worksheet>
    </Workbook>
      ';
     $data = $head.$data.$footer;

     @mkdir(SITE_PATH."/import/logs/".$path, 0777);
     //@mkdir(SITE_PATH."/logs/".$path."/".$this->day, 0777);
     $hhh = fopen(SITE_PATH."/import/logs/".$path."/log.xls", "w");
     //$data = iconv('windows-1251', 'utf-8',$data);
     fwrite($hhh, $data);
     fclose($hhh);
     @chmod (SITE_PATH."/import/logs/", 0755);
     return true;
    }// end of function save_log()

    // ================================================================================================
    // Function : UpdatePriceCount()
    // Date : 08.01.2010
    // Parms : $path - path to CSV file
    // Returns : true,false / Void
    // Description :  update from .csv-file price and count of goods
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function UpdatePriceCount($path=NULL)
    {
        $row = 1;
        $arr_prod =  array();
        $str_id_prop = '';

        //считываем данные из файла и формируем массивы с данными для сохранения в базу данных
        $handle = fopen($path, "r");
        while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
            //var_dump($data);
            $num = count($data);
            //echo "<p> $num полей в строке $row: <br /></p>\n";

            //Если нет всех данных, значите это категория, а не товар
            if( empty($data[1]) ) continue;
            $cod_prod = $this->Conv(trim($data[2]));
            /*echo $data[0];   // Товар
            //echo $data[1];   // Категорія
            echo $data[2];   // ID
            echo '&nbsp;';
            echo $data[3];   // Цена*/
            $arr_prod[$cod_prod]['name'] = $this->Conv(trim($data[0]));
            //$arr_prod[$cod_prod]['cnt'] = $this->Conv(trim($data[5]));
            $arr_prod[$cod_prod]['price'] = $this->Conv(trim($data[3]));

            if(empty($str_id_prop)) $str_id_prop .= $cod_prod;
            else $str_id_prop .= ', '.$cod_prod;
            $row++;
        }
        fclose($handle);
        //echo '<br>Products:';
        //print_r($arr_prod);
        $data_success='';
        $data_faild = '';

        //Обновляем наличие и цены позиций каталога
        $upd_counter=0;
        $upd_counter_err=0;
        foreach($arr_prod as $cod=>$v) {
            //$cnt = $v['cnt'];
            $price =  str_replace(",", ".", trim($v['price']));
            //echo '<hr>';
            //print_r($this->row_for_update);
            //echo '<br>$cod='.$cod
            $q = "UPDATE `".TblModCatalogProp."` SET
                  `price`='".$price."'
                  WHERE `number_name`='".$cod."'";
            $res = $this->db->db_Query($q);
            //echo "<br>Update Prod  = ".$q.' res = '.$res.' $this->db->result='.$this->db->result;
            if( !$res OR !$this->db->result){
                $data_faild.='
                <Row>
                 <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                 <Cell><Data ss:Type="String">'.$v['name'].'</Data></Cell>
                 <Cell><Data ss:Type="String">Ошибка обновления данных товара</Data></Cell>
                 <Cell><Data ss:Type="String">'.$q.' $res = '.$res.' $this->db->result='.$this->db->result.' $this->db->db_GetErrNo()='.$this->db->db_GetErrNo().' $this->db->db_GetErrDetail()='.$this->db->db_GetErrDetail().'</Data></Cell>
                </Row>
                ';
                $upd_counter_err++;
                continue;
            }

            $data_success.='
            <Row>
             <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                 <Cell><Data ss:Type="String">'.$v['name'].'</Data></Cell>
             <Cell><Data ss:Type="String">Обновленно</Data></Cell>
            </Row>
            ';
            $upd_counter++;
        }//end of foreach
        if(!empty($data_success)) $this->save_log($data_success, 'success');
        if(!empty($data_faild)) $this->save_log($data_faild, 'faild');
        ?>
        <div>
         <br/>Обновлены цены в&nbsp;<?=$upd_counter;?> товаре(ах)
         <?if($upd_counter_err>0){?><br/>Не удалось обновить товаров:<?= $upd_counter_err; }

         if(!empty($data_faild))  {
             $log_file = SITE_PATH."/import/logs/faild/log.xls";
             if( file_exists($log_file)){
                 ?><br>Для детального просмотра ошибок смотрите <a href="/import/logs/faild/log.xls" target="_blank" title="Посмотреть лог-файл с ошибками импорта">лог-файл</a><?
             }
         }

         if(!empty($data_success))  {
             $log_file = SITE_PATH."/import/logs/success/log.xls";
             if( file_exists($log_file)){
                 ?><br>Для детального просмотра отчета обновления смотрите <a href="/import/logs/success/log.xls" target="_blank" title="Посмотреть лог-файл импорта">лог-файл</a><?
             }
         }
         //echo '<br/>$data_success ='.$data_success;
         //echo '<br/>$log_file ='.$log_file;

         ?>
        </div>
        <?
        return true;
    } // end of UpdatePriceCount


// ================================================================================================
// Function : GetStrForSearch()
// Date : 19.02.2008
// Returns : true,false / file
// Description : build search string
// Programmer : Yaroslav Gyryn
// ================================================================================================
function GetStrForSearch($mas, $start=0){
 $str = '';
 $count = sizeof($mas);
 for($i=$start;$i<$count;$i++){
       $str .= $mas[$i]." ";
     }
     $str = trim($str);
  return $str;
} // end of function GetStrForSearch


// ================================================================================================
//    Function          : CheckIfManufacExist
//    Version           : 1.0.0
//    Date              : 21.03.2006
//    Parms             : usre_id   / User ID
//                        module    / module ID
//    Returns           : Error Indicator
//    Description       : Opens and selects a dabase
// ================================================================================================
function CheckIfManufacExist($man_name){
  $q = "select * from `".TblModCatalogSprManufac."` where `name`='".$man_name."'";
  $res = $this->Right->Query($q, $this->user_id, $this->module);
  $rows = $this->Right->db_GetNumRows();
  $row = $this->Right->db_FetchAssoc();
  if($rows)
    return $row['cod'];
  else
    return false;
} // end of function  CheckIfManufacExist


// ================================================================================================
//    Function          : CheckIfGrpExist
//    Version           : 1.0.0
//    Date              : 21.03.2006
//    Parms             : usre_id   / User ID
//                        module    / module ID
//    Returns           : Error Indicator
//    Description       : Opens and selects a dabase
// ================================================================================================
function CheckIfGrpExist($man_name){
  $q = "select * from `".TblModCatalogSprGroup."` where `name`='".$man_name."'";
  $res = $this->Right->Query($q, $this->user_id, $this->module);
  $rows = $this->Right->db_GetNumRows();
  $row = $this->Right->db_FetchAssoc();
  if($rows)
    return $row['cod'];
  else
    return false;
} // end of function  CheckIfGrpExist


// ================================================================================================
// Function : GetShopSettings()
// Date: 7.10.2008
// Returns : true,false / Void
// Description : return all settings of ShopExport
// Reason for change : Reason Description / Creation
// ================================================================================================
function GetShopSettings(){
    $db = new DB();
    $q="select * from `".TblModShopSet."` where 1";
    $res = $db->db_Query( $q );
    if( !$db->result ) return false;
    $row = $db->db_FetchAssoc();
    return $row;
} // end of function GetShopSettings()


    // ================================================================================================
    // Function : ExportCatalogToCSV
    // Date : 06.06.2010
    // Returns : true,false / Void
    // Description : export products to CSV-file
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function ExportCatalogToCSV()
    {
        $arr_categ = array();
        $arr_prod = array();
        $csv_separator = ";";
        $csv_terminated = "\n";
        $csv_empty = '';
        $filename = 'catalog.csv';
        $outPutArray = '';

         // Выборка категорий
        $q = "SELECT
                    `".TblModCatalog."`.`id`,
                    `".TblModCatalog."`.`cod_pli`,
                    `".TblModCatalog."`.`level`,
                    `".TblModCatalogSprName."`.`name`
              FROM
                    `".TblModCatalog."`,
                    `".TblModCatalogSprName."`
              WHERE
                    `".TblModCatalog."`.`id`=`".TblModCatalogSprName."`.`cod`
              AND
                    `".TblModCatalogSprName."`.`lang_id`='"._LANG_ID."'
             ";
        $q .= " ORDER BY `".TblModCatalog."`.`move` asc";
        $res = $this->db->db_Query($q);
        //echo '<br>'.$q.'<br/> $res='.$res.' $this->db->result='.$this->db->result;
        if( !$res OR !$this->db->result) return false;
        $rows = $this->db->db_GetNumRows();
        //echo '<br>$rows='.$rows;
        for($i=0; $i<$rows; $i++){
            $row = $this->db->db_FetchAssoc();
            //echo '<br />$i='.$i.' $id='.$row['id'].' $level='.$row['level'];
            $index = stripslashes($row['id']);
            $arr_categ[$index]=$row;     // массив категорий
        }

        // Установка родительской категории в массиве продукции  сответствии с кодом PLI в категориях
        foreach($arr_categ as $k => $v ){
            if(array_key_exists($v['level'], $arr_categ))
                $arr_categ[$k]['level_pli']=$arr_categ[$v['level']]['cod_pli'];
            else
                $arr_categ[$k]['level_pli'] = 0;
        }

        // Выборка категорий и товаров
        $q = "SELECT
                    `".TblModCatalog."`.`cod_pli` AS 'cat_cod_pli',
                    `".TblModCatalogProp."`.*,
                    `".TblModCatalogPropSprName."`.`name` AS `prod_name`,
                    `".TblModCatalogSprName."`.`name` AS `cat_name`,
                    `".TblModCatalog."`.`level` AS `parent_level`
              FROM
                    `".TblModCatalogProp."`,
                    `".TblModCatalogPropSprName."`,
                    `".TblModCatalogSprName."`,
                    `".TblModCatalog."`
              WHERE
                     `".TblModCatalogProp."`.`id_cat`=`".TblModCatalog."`.`id`
              AND `".TblModCatalog."`.`id`=`".TblModCatalogSprName."`.`cod`
              AND `".TblModCatalogSprName."`.`lang_id`='"._LANG_ID."'
              AND `".TblModCatalogProp."`.`id`=`".TblModCatalogPropSprName."`.`cod`
              AND `".TblModCatalogPropSprName."`.`lang_id`='"._LANG_ID."'
             ";
        if( !empty($this->id_cat) ) $q .= " AND `".TblModCatalog."`.`id`='".$this->id_cat."'";
        $q .= " ORDER BY `".TblModCatalog."`.`move` asc, `".TblModCatalogProp."`.`move` asc";
        $res = $this->db->db_Query($q);
        //echo '<br>'.$q.'<br/> $res='.$res.' $this->db->result='.$this->db->result;
        if( !$res OR !$this->db->result) return false;
        $rows = $this->db->db_GetNumRows();
        //echo '<br>$rows='.$rows;

        for($i=0; $i<$rows; $i++){
            $row_data = $this->db->db_FetchAssoc();
            $index1 = stripslashes($row_data['id']);
            $arr_prod[$index1] = $row_data;
        }

        // Установка родительской категории в сответствии с кодом PLI
        foreach($arr_prod as $k => $v ){

            if(array_key_exists($v['parent_level'], $arr_categ))
                $arr_prod[$k]['parent_level_pli'] = $arr_categ[$v['parent_level']]['cod_pli'];
            else
                $arr_prod[$k]['parent_level_pli'] = 0;
        }
         //print_r($arr_prod);

        // Формирование заголовка таблицы
        $outPutArray .= $this->multi['FLD_ID'].' ('.$this->multi['FLD_CATEGORY'].')'.$csv_separator.
                              $this->multi['FLD_PARENT_COD'].$csv_separator.
                              $this->multi['FLD_CATEGORY'].$csv_separator.
                              $this->multi['FLD_ID'].$csv_separator.
                              $this->multi['FLD_NUMBER_NAME'].$csv_separator.
                              $this->multi['FLD_NAME'].$csv_separator.
                              'Наличие'.$csv_separator.
                              $this->multi['FLD_PRICE'].$csv_separator.
                              $cell->Data = 'Валюта'.$csv_terminated;


       /*  $filename = 'catalog.xml';
        $doc = new SimpleXMLElement(
         '<?xml version="1.0" encoding="utf-8"?>
         <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
          xmlns:o="urn:schemas-microsoft-com:office:office"
          xmlns:x="urn:schemas-microsoft-com:office:excel"
          xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
          xmlns:html="http://www.w3.org/TR/REC-html40"></Workbook>'
        );

        //<Worksheet ss:Name="Sheet1">
        $worksheetNode = $doc->addChild('Worksheet');
        $worksheetNode['ss:Name'] = 'optovichok';
        $worksheetNode->Table = '';//add a child with value '' by setter

        //<Row> Header
        $row = $worksheetNode->Table->addChild('Row');

        // Код категории товара (уникальное числовое значение)
        $cell = $row->addChild('Cell');
        $cell->Data = $this->Conv($this->multi['FLD_ID'].' ('.$this->multi['FLD_CATEGORY'].')'); //shorthand
       // $cell->Data['ss:Type'] = 'String';//shorthand

        // Код родительской категории
        $cell = $row->addChild('Cell');
        $cell->Data = $this->Conv($this->multi['FLD_PARENT_COD']); //shorthand
        //$cell->Data['ss:Type'] = 'String';//shorthand

        // Наименование категории товара
        $cell = $row->addChild('Cell');
        $cell->Data = $this->Conv($this->multi['FLD_CATEGORY']); //shorthand
        //$cell->Data['ss:Type'] = 'String';//shorthand

        // Числовой уникальный код товара
        $cell = $row->addChild('Cell');   // ID товара
        $cell->Data = $this->Conv($this->multi['FLD_ID']); //shorthand
       // $cell->Data['ss:Type'] = 'String';

        // Артикул -  уникальное числовое значение
        if ( isset($this->settings['number_name']) AND $this->settings['number_name']=='1' ) {
            $cell = $row->addChild('Cell'); //shorthand
            $cell->Data = $this->Conv($this->multi['FLD_NUMBER_NAME']);
        //    $cell->Data['ss:Type'] = 'String';
        }

        // Наименование товара
        if ( isset($this->settings['name']) AND $this->settings['name']=='1' ) {
            $cell = $row->addChild('Cell');
            $cell->Data = $this->Conv($this->multi['FLD_NAME']); //shorthand
        //    $cell->Data['ss:Type'] = 'String';//shorthand
        }

        // Наличие
        $cell = $row->addChild('Cell'); //shorthand
        $cell->Data = 'Наличие';
       // $cell->Data['ss:Type'] = 'String';

        if ( isset($this->settings['price']) AND $this->settings['price']=='1' ) {
            // Цена товара
            $cell = $row->addChild('Cell'); //shorthand
            $cell->Data = $this->Conv($this->multi['FLD_PRICE']);
          //  $cell->Data['ss:Type'] = 'String';

            // Валюта товара
            $cell = $row->addChild('Cell'); //shorthand
            $cell->Data = 'Валюта';
        //    $cell->Data['ss:Type'] = 'String';

        }*/
//      print_r($arr_categ);

        // Формирование данных по категориям
        foreach($arr_categ as $id=>$arr_categ2) {

            $outPutArray .= $arr_categ2['cod_pli'].$csv_separator.
                                  $arr_categ2['level_pli'].$csv_separator.
                                  $arr_categ2['name'].$csv_separator.
                                  $csv_empty.$csv_separator.
                                  $csv_empty.$csv_separator.
                                  $csv_empty.$csv_separator.
                                  $csv_empty.$csv_separator.
                                  $csv_empty.$csv_separator.
                                  $csv_empty.$csv_terminated;

            //$row = $worksheetNode->Table->addChild('Row');

            // Код категории
            /*$cell = $row->addChild('Cell');
            $cell->Data = $this->Conv($arr_categ2['id']);
            //$cell->Data['ss:Type'] = 'String';

            // Код родительской категории
            $cell = $row->addChild('Cell');
            $cell->Data = $this->Conv($arr_categ2['level']);
            //$cell->Data['ss:Type'] = 'String';

            // Наименование категории товара
            $cell = $row->addChild('Cell');
            $cell->Data = $this->Conv($arr_categ2['name']);
            //$cell->Data['ss:Type'] = 'String';

            $cell = $row->addChild('Cell');
            $cell->Data = '';

            $cell = $row->addChild('Cell');
            $cell->Data = '';

            $cell = $row->addChild('Cell');
            $cell->Data = '';

            $cell = $row->addChild('Cell');
            $cell->Data = '';

            $cell = $row->addChild('Cell');
            $cell->Data = '';

            $cell = $row->addChild('Cell');
            $cell->Data = '';*/

        }

        // Формирование данных по категориям и товарам
        foreach($arr_prod as $id=>$arr_prod2){
             $outPutArray.= $arr_prod2['cat_cod_pli'].$csv_separator.
                                  $arr_prod2['parent_level_pli'].$csv_separator.
                                  $arr_prod2['cat_name'].$csv_separator.
                                  $arr_prod2['cod_pli'].$csv_separator.
                                  $arr_prod2['number_name'].$csv_separator.
                                  $arr_prod2['prod_name'].$csv_separator;
            // Наличие
            if($arr_prod2['exist']=='1') // є
                $outPutArray.= '+'.$csv_separator;
            else
                $outPutArray.= '-'.$csv_separator;


            if($arr_prod2['price_currency']==1)
                $valuta = '$';
            else
                $valuta = 'UAH';

            $outPutArray.= $arr_prod2['price'].$csv_separator.
                                 $valuta.$csv_terminated;
            //echo '<br/>'.$outPutArray;
/*
            //<Row> Products details
            $row = $worksheetNode->Table->addChild('Row');

            // Код категории товара (уникальное числовое значение).
            $cell = $row->addChild('Cell');
            $cell->Data = $this->Conv($arr_prod2['id_cat']); //shorthand
            //$cell->Data['ss:Type'] = 'String';//shorthand

            // Код родительской категории
            $cell = $row->addChild('Cell');
            $cell->Data = $this->Conv($arr_prod2['parent_level']); //shorthand
            //$cell->Data['ss:Type'] = 'String';//shorthand

            // Наименование категории товара
            $cell = $row->addChild('Cell');
            $cell->Data = $this->Conv($arr_prod2['cat_name']); //shorthand
           // $cell->Data['ss:Type'] = 'String';//shorthand

            // Числовой уникальный код товара
            $cell = $row->addChild('Cell');        // ID товара
            $cell->Data = $this->Conv($arr_prod2['id']);
           // $cell->Data['ss:Type'] = 'String';

            // Артикул  - уникальное числовое значение)
            if ( isset($this->settings['number_name']) AND $this->settings['number_name']=='1' ) {
                $cell = $row->addChild('Cell'); //shorthand
                $cell->Data = $this->Conv($arr_prod2['number_name']);
            //    $cell->Data['ss:Type'] = 'String';
            }

            //Наименование товара
            if ( isset($this->settings['name']) AND $this->settings['name']=='1' ) {
                $cell = $row->addChild('Cell');
                $cell->Data = $this->Conv($arr_prod2['prod_name']); //shorthand
            //    $cell->Data['ss:Type'] = 'String';//shorthand
            }

            // Наличие
            $cell = $row->addChild('Cell'); //shorthand
            if($arr_prod2['exist']=='1') // є
                $cell->Data = '+';
            else
                $cell->Data = '-';
           // $cell->Data['ss:Type'] = 'String';

            if ( isset($this->settings['price']) AND $this->settings['price']=='1' ) {

                //Цена товара
                $cell = $row->addChild('Cell'); //shorthand
                $cell->Data = $arr_prod2['price'];
          //      $cell->Data['ss:Type'] = 'String';

                // Валюта
                $cell = $row->addChild('Cell');
                $cell->Data = '$';
         //       $cell->Data['ss:Type'] = 'String';
            }

           */
        } //end foreach by products
        //echo $outPutArray;

        $out = $this->Conv($outPutArray);

        $uploaddir = SITE_PATH.'/export';
        $fullpath = $uploaddir.'/'.$filename;
        if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777);
        else @chmod($uploaddir,0777);

        if (!$handle = fopen($fullpath, 'w')) {
             echo "Не могу открыть файл ($fullpath)";
             return;
        }
        // Записываем $out в наш открытый файл.
        if (fwrite($handle, $out) === FALSE) {
            echo "Не могу произвести запись в файл ($fullpath)";
            return;
        }
        //readfile($fullpath);
        fclose($handle);

        /*
        $uploaddir = SITE_PATH.'/export';
        if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777);
        else @chmod($uploaddir,0777);
        $res = $doc->asXML($uploaddir.'/'.$filename);
        @chmod($uploaddir,0755);
        */
        $path = '/export/'.$filename;
        $path_show = 'http://'.NAME_SERVER.$path;

        if($path){
            echo 'Скачать каталог <a href="http://'.NAME_SERVER.'/sys/report_download.php?path='.$path.'&module='.$this->module.'&task='.$this->task.'">'.$path_show.'</a>';
            return true;
        }
        else{
            echo 'Ошибка. Каталог не экспортировался';
            return false;
        }

    }//end of function ExportCatalogToCSV()



  // ================================================================================================
    // Function : ExportCatalogToExcelXML
    // Date : 02.11.2009
    // Returns : true,false / Void
    // Description : export products to Excel XML-file
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function ExportCatalogToExcelXML()
    {
        $q = "SELECT
                    `".TblModCatalogProp."`.*,
                    `".TblModCatalogPropSprName."`.`name` AS `prod_name`,
                    `".TblModCatalogSprName."`.`name` AS `cat_name`,
                    `".TblModCatalog."`.`level` AS `parent_level`
              FROM
                    `".TblModCatalogProp."`,
                    `".TblModCatalogPropSprName."`,
                    `".TblModCatalogSprName."`,
                    `".TblModCatalog."`
              WHERE
                     `".TblModCatalogProp."`.`id_cat`=`".TblModCatalog."`.`id`
              AND `".TblModCatalog."`.`id`=`".TblModCatalogSprName."`.`cod`
              AND `".TblModCatalogSprName."`.`lang_id`='"._LANG_ID."'
              AND `".TblModCatalogProp."`.`id`=`".TblModCatalogPropSprName."`.`cod`
              AND `".TblModCatalogPropSprName."`.`lang_id`='"._LANG_ID."'
             ";
        if( !empty($this->id_cat) ) $q .= " AND `".TblModCatalog."`.`id`='".$this->id_cat."'";
        $q .= " ORDER BY `".TblModCatalog."`.`move` asc, `".TblModCatalogProp."`.`move` asc";
        $res = $this->db->db_Query($q);
        //echo '<br>'.$q.'<br/> $res='.$res.' $this->db->result='.$this->db->result;
        if( !$res OR !$this->db->result) return false;
        $rows = $this->db->db_GetNumRows();
        //echo '<br>$rows='.$rows;
        $arr_prod = array();
        for($i=0;$i<$rows;$i++){
            $row_data = $this->db->db_FetchAssoc();
            $index1 = stripslashes($row_data['id']);
            $arr_prod[$index1]=$row_data;
        }
        $filename = 'catalog.xls';

        $doc = new SimpleXMLElement(
         '<?xml version="1.0" encoding="utf-8"?>
         <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
          xmlns:o="urn:schemas-microsoft-com:office:office"
          xmlns:x="urn:schemas-microsoft-com:office:excel"
          xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
          xmlns:html="http://www.w3.org/TR/REC-html40"></Workbook>'
        );

        //<Worksheet ss:Name="Sheet1">
        $worksheetNode = $doc->addChild('Worksheet');

        $worksheetNode['ss:Name'] = 'sheet1';

        $worksheetNode->Table = '';//add a child with value '' by setter

        //<Row> Header
        $row = $worksheetNode->Table->addChild('Row');

        // Код товара (уникальное числовое значение)
        if ( isset($this->settings['number_name']) AND $this->settings['number_name']=='1' ) {
            $cell = $row->addChild('Cell'); //shorthand
            $cell->Data = $this->Conv($this->multi['FLD_NUMBER_NAME']);
            $cell->Data['ss:Type'] = 'String';
        }

        // Наименование товара
        if ( isset($this->settings['name']) AND $this->settings['name']=='1' ) {
            $cell = $row->addChild('Cell');
            $cell->Data = $this->Conv($this->multi['FLD_NAME']); //shorthand
            $cell->Data['ss:Type'] = 'String';//shorthand
        }

        // Цена товара
        if ( isset($this->settings['price']) AND $this->settings['price']=='1' ) {
            $cell = $row->addChild('Cell'); //shorthand
            $cell->Data = $this->Conv($this->multi['FLD_PRICE']);
            $cell->Data['ss:Type'] = 'String';
        }

        // Код категории товара (уникальное числовое значение)
        $cell = $row->addChild('Cell');
        $cell->Data = $this->Conv($this->multi['FLD_ID'].' ('.$this->multi['FLD_CATEGORY'].')'); //shorthand
        $cell->Data['ss:Type'] = 'String';//shorthand

        // Код родительской категории
        $cell = $row->addChild('Cell');
        $cell->Data = $this->Conv($this->multi['FLD_PARENT_COD']); //shorthand
        $cell->Data['ss:Type'] = 'String';//shorthand

        // Наименование категории товара
        $cell = $row->addChild('Cell');
        $cell->Data = $this->Conv($this->multi['FLD_CATEGORY']); //shorthand
        $cell->Data['ss:Type'] = 'String';//shorthand

        /*$cell = $row->addChild('Cell');   // ID товара
        $cell->Data = $this->Conv($this->multi['FLD_ID']); //shorthand
        $cell->Data['ss:Type'] = 'String';//shorthand*/

        foreach($arr_prod as $id=>$arr_prod2){
            //<Row> Products details
            $row = $worksheetNode->Table->addChild('Row');

            // Код товара (уникальное числовое значение)
            if ( isset($this->settings['number_name']) AND $this->settings['number_name']=='1' ) {
                $cell = $row->addChild('Cell'); //shorthand
                $cell->Data = $this->Conv($arr_prod2['number_name']);
                $cell->Data['ss:Type'] = 'String';
            }

            //Наименование товара
            if ( isset($this->settings['name']) AND $this->settings['name']=='1' ) {
                $cell = $row->addChild('Cell');
                $cell->Data = $this->Conv($arr_prod2['prod_name']); //shorthand
                $cell->Data['ss:Type'] = 'String';//shorthand
            }

            //Цена товара
            if ( isset($this->settings['price']) AND $this->settings['price']=='1' ) {
                $cell = $row->addChild('Cell'); //shorthand
                $cell->Data = $arr_prod2['price'];
                $cell->Data['ss:Type'] = 'String';
            }

            // Код категории товара (уникальное числовое значение).
            $cell = $row->addChild('Cell');
            $cell->Data = $this->Conv($arr_prod2['id_cat']); //shorthand
            $cell->Data['ss:Type'] = 'String';//shorthand

            // Код родительской категории
            $cell = $row->addChild('Cell');
            $cell->Data = $this->Conv($arr_prod2['parent_level']); //shorthand
            $cell->Data['ss:Type'] = 'String';//shorthand

            // Наименование категории товара
            $cell = $row->addChild('Cell');
            $cell->Data = $this->Conv($arr_prod2['cat_name']); //shorthand
            $cell->Data['ss:Type'] = 'String';//shorthand

            /*$cell = $row->addChild('Cell');        // ID товара
            $cell->Data = $this->Conv($arr_prod2['id']); //shorthand
            $cell->Data['ss:Type'] = 'String';//shorthand*/

        }//end foreach by products

        $uploaddir = SITE_PATH.'/export';
        if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777);
        else @chmod($uploaddir,0777);
        $res = $doc->asXML($uploaddir.'/'.$filename);
        @chmod($uploaddir,0755);

        $path = '/export/'.$filename;
        $path_show = 'http://'.NAME_SERVER.$path;

        if($path){
            echo 'Скачать каталог <a href="http://'.NAME_SERVER.'/sys/report_download.php?path='.$path.'&module='.$this->module.'&task='.$this->task.'">'.$path_show.'</a>';
            return true;
        }
        else{
            echo 'Ошибка. Каталог не экспортировался';
            return false;
        }

    }//end of function ExportCatalogToExcelXML()


    // ================================================================================================
    // Function : GetData()
    // Date : 2.10.2008
    // Returns : true,false / Void
    // Description : Get Export Data
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GetData(){
        $arr = array();
        $CatalogL = new CatalogLayout();
        $c_settings = $this->GetSettings();  // Catalog Settings
        /*
         * Для примера создаем подключение к базе данных
         */
        mysql_connect(_HOST, _USER, _PASSWD);
        mysql_select_db(_DBNAME);

        $q = "set character_set_client='".DB_CHARACTER_SET_CLIENT."'";
        $res = mysql_query($q);
        //echo '<br>$q='.$q.' $res='.$res;

        $q = "set character_set_results='".DB_CHARACTER_SET_RESULT."'";
        $res = mysql_query($q);
        //echo '<br>$q='.$q.' $res='.$res;

        $q = "set collation_connection='".DB_COLLATION_CONNECTION."'";
        $res = mysql_query($q);
        //echo '<br>$q='.$q.' $res='.$res;

        $sSQL = "select
             `".TblModCatalogProp."`.id,
            `".TblModCatalogProp."`.id_cat,
            `".TblModCatalogProp."`.price,
            `".TblModCatalogProp."`.price_currency,
            `".TblModCatalogSprManufac."`.name as vendor,
            `".TblModCatalogPropSprName."`.name,
            `".TblModCatalogPropSprShort."`.name as description
        from
            `".TblModCatalogProp."`,
            `".TblModCatalogSprManufac."`,
            `".TblModCatalogPropSprName."`,
            `".TblModCatalogPropSprShort."`
        where
            (
            `".TblModCatalogProp."`.id_manufac=`".TblModCatalogSprManufac."`.cod
            OR
            `".TblModCatalogProp."`.id_manufac='0'
            )
            and
            `".TblModCatalogProp."`.id=`".TblModCatalogPropSprName."`.cod
             and
            `".TblModCatalogProp."`.id=`".TblModCatalogPropSprShort."`.cod
            and
            `".TblModCatalogSprManufac."`.lang_id='"._LANG_ID."'
            and
            `".TblModCatalogPropSprName."`.lang_id='"._LANG_ID."'
            and
            `".TblModCatalogPropSprShort."`.lang_id='"._LANG_ID."'
           group by `".TblModCatalogProp."`.id
           order by `".TblModCatalogProp."`.id_cat
           ";


        $result = mysql_query($sSQL);
        //echo "<br />sSQL = ".$sSQL." res = ".$result."<br><br><br>";

        while( $offer = mysql_fetch_assoc($result) ) {

             $click = $CatalogL->Link($offer['id_cat'], $offer['id']);
             //echo "<br>".$click;
            $row_img = $this->GetPicture($offer['id']);
            if(isset($row_img[0]['path'])){ $img = $row_img[0]['path'];
            //$img = $this->GetFirstImgOfProp($offer['id']);
            $settings_img_path = $c_settings['img_path'].'/'.$offer['id']; // like /uploads/45
                $img_with_path = $settings_img_path.'/'.$img; // like /uploads/45/R1800TII_big.jpg
                //echo "<br>".$img_with_path;
                } else $img_with_path = '';

              //  $name = $offer['name'];
                $tm = explode(" ", $offer['name']);
                if(isset($tm[0])) {$type = $tm[0];}
                else { $type = '';}
                //echo "<br>".$type;

                if(isset($offer['price']) and !empty($offer['price'])) $price = $offer['price'];
                else $price = '1';
               // echo "<br> price = ".$price;
                 $full =  strip_tags($offer['description'], "<p><br><li><table><tr><td><ol><ul><nobr>");
                 $full = str_replace("&nbsp;", " ", $full);
                 $full = str_replace("&nbsp", " ", $full);
                 $full = str_replace( "nbsp", " ", $full );

            array_push($arr,
                array(
                       'id'        => $offer['id'],                // ИД товара
                    'category'    => $offer['id_cat'],            // ИД категории в которой находится товар
                    'priority'    => "50",            // Приоритет - пока не используется
                    'img'        => $img_with_path,                // Адрес(URL) картинки
                    'click'        => $click,                 // Адрес(URL) куда должен перейти пользователь на ваш сайт при клике на товар
                    'type'        => $type,         // Тип товара, например - монитор
                    'name'    => $offer['name'],                // Название товара
                    'currency'    =>  $offer['price_currency'],        // Буквенный код валюты, например UAH
                    'price'    => $price,        // Цена товара в указаной валюте
                    'manufac'    => $offer['vendor'],
                    'description'    =>$full,    // Описание товара

                       )
            );
        }
            //   print_r($arr);
                return $arr;
    } // end of function GetData


    // ================================================================================================
    // Function : ExportPriceToExcel()
    // Date : 29.11.2009
    // Parms : $path - path to CSV file
    // Returns : true,false / Void
    // Description :  import or update descriptions of categories and goods from .csv-fiel to the databse
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function ExportPriceToExcel(){
        $arr = $this->GetData();
        //print_r($arr);
        $Currency = new SystemCurrencies();


        $str = '<?xml version="1.0" encoding="UTF-8"?><?mso-application progid="Excel.Sheet"?>
        <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:x2="http://schemas.microsoft.com/office/excel/2003/xml" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:html="http://www.w3.org/TR/REC-html40" xmlns:c="urn:schemas-microsoft-com:office:component:spreadsheet"><OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office"><Colors><Color><Index>3</Index><RGB>#c0c0c0</RGB></Color><Color><Index>4</Index><RGB>#ff0000</RGB></Color></Colors></OfficeDocumentSettings><ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel"><WindowHeight>9000</WindowHeight><WindowWidth>13860</WindowWidth><WindowTopX>240</WindowTopX><WindowTopY>75</WindowTopY><ProtectStructure>False</ProtectStructure><ProtectWindows>False</ProtectWindows></ExcelWorkbook><Styles><Style ss:ID="Default" ss:Name="Default"/><Style ss:ID="Result" ss:Name="Result"><Font ss:Bold="1" ss:Italic="1" ss:Underline="Single"/></Style><Style ss:ID="Result2" ss:Name="Result2"><Font ss:Bold="1" ss:Italic="1" ss:Underline="Single"/><NumberFormat ss:Format="Currency"/></Style><Style ss:ID="Heading" ss:Name="Heading"><Alignment ss:Horizontal="Center"/><Font ss:Bold="1" ss:Italic="1" ss:Size="16"/></Style><Style ss:ID="Heading1" ss:Name="Heading1"><Alignment ss:Horizontal="Center" ss:Rotate="90"/><Font ss:Bold="1" ss:Italic="1" ss:Size="16"/></Style><Style ss:ID="co1"/><Style ss:ID="co2"/><Style ss:ID="co3"/><Style ss:ID="co4"/><Style ss:ID="co5"/><Style ss:ID="co6"/><Style ss:ID="co7"/><Style ss:ID="co8"/><Style ss:ID="ta1"/><Style ss:ID="ce1"><Alignment ss:Horizontal="Left" ss:Vertical="Center"/></Style><Style ss:ID="ce2"><Alignment ss:Horizontal="Justify" ss:Vertical="Top" ss:Indent="0" ss:Rotate="0"/></Style><Style ss:ID="ce3"><Alignment ss:Horizontal="Justify" ss:Vertical="Top" ss:WrapText="1" ss:Indent="0" ss:Rotate="0"/></Style></Styles><ss:Worksheet ss:Name="Лист1"><Table ss:StyleID="ta1"><Column ss:Width="89.6598"/><Column ss:Width="246.9543"/><Column ss:Width="73.1055"/><Column ss:Width="37.7575"/><Column ss:Width="281.452"/><Column ss:Width="464.8535"/><Column ss:Span="249" ss:Width="50.2866"/>
        ';

        for($j=0;$j<count($arr);$j++){
        $id_cat = $this->getTopLevel($arr[$j]['category']);
        $category_name = $this->Spr->GetNameByCod( TblModCatalogSprName, $id_cat, 3, 1 );
        $price = $Currency->Converting($arr[$j]['currency'],  1, stripslashes($arr[$j]['price']), 2 );

        $str .='
        <Row ss:AutoFitHeight="0" ss:Height="36.5386">
	        <Cell ss:StyleID="ce1"><Data ss:Type="String">'.$category_name.'</Data></Cell>
	        <Cell ss:StyleID="ce1"><Data ss:Type="String">'.$arr[$j]['name'].'</Data></Cell>
	        <Cell ss:StyleID="ce1"><Data ss:Type="String">'.$arr[$j]['manufac'].'</Data></Cell>
	        <Cell ss:StyleID="ce1"><Data ss:Type="Number">'.$price.'</Data></Cell>
	        <Cell ss:StyleID="ce2"><Data ss:Type="String">'.htmlspecialchars(stripslashes($arr[$j]['description'])).'</Data></Cell>
	        <Cell><Data ss:Type="String">http://www.'.$_SERVER['SERVER_NAME'].$arr[$j]['click'].'</Data></Cell>
	        <Cell ss:Index="256"/>
        </Row>
        ';

        } // end for


        $str .='
        </Table><x:WorksheetOptions/></ss:Worksheet></Workbook>
        ';
        $uploaddir = SITE_PATH.'/export';
        if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777);
        else @chmod($uploaddir,0777);
        $uploadpath = $uploaddir.'/price.xls';
        $hhh = fopen($uploadpath, "w");
        fwrite($hhh, $str);
        fclose($hhh);
        @chmod($uploaddir,0755);
        $path = '/export/price.xls';
        echo 'Скачать прайс <a href="http://'.NAME_SERVER.'/sys/report_download.php?path='.$path.'&module='.$this->module.'&task='.$this->task.'">'.$path.'</a>';

    }//end of function ExportPriceToExcel()


    /**
    * Class method import_images
    * import images for catalog items from directory /import/images/
    * @return true/false
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 25.10.2012
    */
     function import_images() {
        $this->db = new DB();
        $dir = $_SERVER['DOCUMENT_ROOT'] . '/import/images/'; //путь, где лежать картинки для импорта
        $this->imgNameAsKey = 'art_num'; //имя поля, по которму будет происходить синхронизация имен картинок и товаром на сайте
        $arr_imeg = array(); //массив для хранения списка имен изображений
        $strProps = '';
        // Открыть заведомо существующий каталог и начать считывать его содержимое
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                $i = 0;
                while (($file = readdir($dh)) !== false) {
                    $i++;
                    if ($i < 3){
                        continue;
                    }
                    $tmp = ' ';
                    $res = strstr($file, $tmp);
                    //Проверяю наличие разделителя ' '(пробел) в названии файла.
                    //его наличе будет означать, что после кода товара (barcode)
                    //идет еще продолжение названия файла, которое для нас сейчас не важна.
                    //Мы его отсекаем.
                    if($res){
                        $art = str_replace($res, '', $file);
                    }else{
                        $art = $file;
                    }

                    //Отсекаю расширение файла.
                    $fileNoExttation = substr($art, 0, strrpos($art,"."));
                    if (empty($fileNoExttation)){
                        continue;
                    }
                    //После кода товара через подчеркивание идут след. фото
                    //к этому товару
                    $numImg = explode('_', $fileNoExttation);
                    $art = $numImg[0];
                    if (empty($art)){
                        continue;
                    }
                    //Сохранаяю в массив на индекс кода товара все изображения по этому товару
                    if (isset($numImg[1])){
                        $arr_imeg[$art][$numImg[1]] = $file;
                    }else{
                        $arr_imeg[$art][0] = $file;
                    }
                    //этот массив формирую только дя того, что бы потом удобно было
                    //перевести его в строку и подставить в запрос в конструкцию WHERE IN
                    $strProps[$art] = "'".$art."'";
                    //echo "<br>Файл: $file art=".$art." \n";
                }
                closedir($dh);
            }
        }
        //var_dump($arr_imeg);
        if(count($arr_imeg)==0){
            echo 'Нет изображений для импорта';
            return false;
        }
        //var_dump($strProps);
        $strProps = implode(",", $strProps);
        //echo '<br>$strProps='.$strProps;
        //echo '<br>11111111111';
        //выбираем из базы все товары по баркоду (которые есть в списке импортируемых изображений)
        //и к ним будем искать изображения в массиве $arr_imeg
        $q = "SELECT
                    `" . TblModCatalogProp . "`.`".$this->imgNameAsKey."`,
                    `" . TblModCatalogProp . "`.`id`
              FROM
                    `" . TblModCatalogProp . "`
              WHERE
              1
              AND `" . TblModCatalogProp . "`.`".$this->imgNameAsKey."` IN (".$strProps.")
              ORDER BY `" . TblModCatalogProp . "`.`move` asc";
        $res = $this->db->db_Query($q);
        //echo '<br>'.$q.'<br/> $res='.$res.' $this->db->result='.$this->db->result;
        if (!$res OR !$this->db->result){
            return false;
        }
        $rows_img = $this->db->db_GetNumRows();
        //echo '<br>$rows_img='.$rows_img;
        $row_prop_arr = array();
        for ($i = 0; $i < $rows_img; $i++) {
            $row = $this->db->db_FetchAssoc();
            $row_prop_arr[] = $row;
            //echo '<br>$row['barcode']='.$row['barcode'];
        }
        //echo '<br>$rows='.$rows;
        //die();
        $arr_res = array();
        $error = 0;
        $yes = 0;
        $updd = 0;
        for ($i = 0; $i < $rows_img; $i++) {
            $row_prop = $row_prop_arr[$i];
            //echo '<br />$i='.$i.' $id='.$row['id'].' $level='.$row['level'];
            $index = stripslashes($row_prop['id']);
            $cod_pli = stripslashes($row_prop[$this->imgNameAsKey]);
//            echo '<br/>$cod_pli='.$cod_pli;
            //если для товара есть изобрадение в массиве $arr_imeg, то закачаем его.
            if (isset($arr_imeg[$cod_pli])) {
                //формирую массив изображений товара с индексом Ид. товара.
                $arr_res[$index] = $arr_imeg[$cod_pli];
                $uploaddir = SITE_PATH . $this->settings['img_path'] . '/' . $index;
                if (!file_exists($uploaddir))
                    mkdir($uploaddir, 0777);
                else
                    @chmod($uploaddir, 0777);
                //сортируем изображения по индексу.
                ksort($arr_res[$index]);
                $keys = array_keys($arr_res[$index]);
                $size = sizeof($keys);
                for ($j = 0; $j < $size; $j++) {
                    $filename = $dir . $arr_res[$index][$keys[$j]];
                    $filenameCopy = $uploaddir . '/' . $arr_res[$index][$keys[$j]];
                    $uploaddir2 = $arr_res[$index][$keys[$j]];

                    //echo '<br>$filename='.$filename;
                    //echo '<br>$filenameCopy'.$filenameCopy;
                    //echo '<br>$index='.$index.'<br><br>';

                    //Ищу изображения по названию до пробела. Все что после пробела не инетресует.
                    //Это считаються одинаковые файлы (например "12345 price.jpg" и "12345 price01.jpg" - считать одним и тем же изображением).
                    //Это нужно что бы найти изображение, даже если в его навзании после пробела что-то измениили.
                    $tmp = ' ';
                    if(strstr($arr_res[$index][$keys[$j]], $tmp)){
                        $srchName = substr($arr_res[$index][$keys[$j]], 0, strpos($arr_res[$index][$keys[$j]], $tmp)+1 );
                    }else{
                        $srchName = $arr_res[$index][$keys[$j]];
                    }
                    $q = "SELECT id, path, move FROM `" . TblModCatalogPropImg . "` WHERE `id_prop`='" . $index . "' and `path` LIKE '%" . $srchName . "%'";
                    $res = $this->db->db_Query($q);
                    $rowsTmp = $this->db->db_GetNumRows();
                    //echo '<br>$q='.$q.' $res='.$res.' $this->db->result='.$this->db->result.' $rowsTmp='.$rowsTmp;
                    //Если по искомому шаблону найдены изображения ,то мы их удаляем, а закачаем новое
                    if ($rowsTmp > 0){
                        for($iii=0;$iii<$rowsTmp;$iii++){
                            $arrImgToDel[$iii] = $this->db->db_FetchAssoc();
                        }
                        for($iii=0;$iii<$rowsTmp;$iii++){
                            $row = $arrImgToDel[$iii];
                            $q = "DELETE FROM `" . TblModCatalogPropImg . "` WHERE `id`='" . $row['id'] . "'";
                            $res = $this->db->db_Query($q);
                            //echo '<br>$q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;
                            if(!$res OR !$this->db->result){
                                continue;
                            }
                            //физически удаляем файл только если успешно удалили из базы.
                            $pathToDel = $uploaddir . '/' . $row['path'];
                            if (unlink($pathToDel)) {
                                $updd++;
                            } else {
                                $error++;
                            }

                        }
                    }
                    if (@copy($filename, $filenameCopy)) {

                        //====== set next max value for move START ============
                        //устанавляваю порядок в соответствии с порядком, указанным в названиях файла.
                        $maxx = ($j+1);
                        //====== set next max value for move END ==============

                        $q = "INSERT INTO `" . TblModCatalogPropImg . "` values(NULL,'" . $index . "','" . $uploaddir2 . "','1', '" . $maxx . "', -1)";
                        $res = $this->db->db_Query($q);
                        //echo '<br>q='.$q.' res='.$res;
                        if($res){
                            $yes++;
                            unlink($filename);
                        }else{
                            $error++;
                        }
                    }
                    else {
                        $error++;
                    }
                }
            }
        }
        //print_r($arr_res);
        echo '<br>Импортировано = ' . $yes . ' фото';
        if ($updd > 0)
            echo ' (Было в базе(заменено) = ' . $updd . ' фото)';
        if ($error > 0)
            echo '<br>Ошибки при импорте = ' . $error . ' фото';
    }



    /**
    * Class method repairImagesMove
    * repair images move
    * @return true/false
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 25.10.2012
    */
    function repairImagesMove(){
        if(empty($this->imgNameAsKey)){
            return false;
        }
        $q="SELECT `mod_catalog_prop`.`id`, `mod_catalog_prop`.`".$this->imgNameAsKey."`
            FROM `mod_catalog_prop`
            WHERE
                    (SELECT COUNT(`mod_catalog_prop_img`.`id`)
                    FROM `mod_catalog_prop_img`
                    WHERE `mod_catalog_prop_img`.`move`=1
                            AND `mod_catalog_prop_img`.`id_prop`=`mod_catalog_prop`.`id`)>1
            ";
        $res = $this->db->db_Query($q);
        //echo '<br>$q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;
        if(!$res OR !$this->db->result) return false;

        $rows = $this->db->db_GetNumRows();
        if($rows>0){
            echo '<br>Есть проблемы с порядком изображений у '.$rows.'шт. товаров:';
        }
        for($i=0;$i<$rows;$i++){
            $row = $this->db->db_FetchAssoc();
            $arr_repair_props[$i]=$row;
            //echo '<br>№'.($i+1).' ид.: '.$row['id'].' код: '.$row[$this->imgNameAsKey];
        }
        for($i=0;$i<$rows;$i++){
            echo '<br>№'.($i+1).' ид.: '.$row['id'].' код: '.$row[$this->imgNameAsKey];
            $row = $arr_repair_props[$i];
            //по каждому товару вытягиваю все изображения
            $q = "SELECT *
                  FROM `mod_catalog_prop_img`
                  WHERE `id_prop` = '".$row['id']."'
                 ";
            $res = $this->db->db_Query($q);
            $rowsImg = $this->db->db_GetNumRows();
            //echo '<br>$q='.$q.' $res='.$res.' $this->db->result='.$this->db->result.' $rowsImg='.$rowsImg;
            $arr_repair_images = array();
            for($i2=0;$i2<$rowsImg;$i2++){
                $row = $this->db->db_FetchAssoc();
                $imgName = $row['path'];
                $tmp = ' ';
                $res = strstr($imgName, $tmp);
                $art = str_replace($res, '', $imgName);
                //после кода товара через подчеркивание идут след. фото
                //к этому товару
                $numImg = explode('_', $art);
                //если есть после разделител что-то еще значит это не первое изображение,
                //а второе, третье или так длаее.
                if(isset($numImg[1])){
                    $arr_repair_images[$numImg[1]]=$row['id'];
                }else{
                    $arr_repair_images[1]=$row['id'];
                }
                ksort($arr_repair_images);
            }
            $keys = array_keys($arr_repair_images);
            $cnt = count($arr_repair_images);
            for($i2=0;$i2<$cnt;$i2++){
                $q = "UPDATE `mod_catalog_prop_img` SET
                        `move`='".$keys[$i2]."'
                      WHERE
                        `id`='".$arr_repair_images[$keys[$i2]]."'
                     ";
                $res3 = $this->db->db_Query($q);
                //echo '<br>$q='.$q.' $res3='.$res3.' $this->db->result='.$this->db->result;
            }
            echo ' - Исправленно.';
        }
        return true;
    }


}// end of class CatalogImpExp

?>
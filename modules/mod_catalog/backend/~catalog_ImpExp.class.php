<?php
// ================================================================================================
// System : SEOCMS
// Module : catalog_ImpExp.class.php
// Version : 1.0.0
// Date : 21.03.2006
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : Class definition for all actions with managment of content of the catalog
//
// ================================================================================================

include_once( SITE_PATH.'/modules/mod_catalog/catalog.defines.php' );

// ================================================================================================
//    Class             : CatalogImpExp
//    Version           : 1.0.0
//    Date              : 21.03.2006
//
//    Constructor       : Yes
//    Parms             : session_id / session id
//                        usre_id    / UserID
//                        user_      /
//                        user_type  / id of group of user
//    Returns           : None                                               
//    Description       : Class definition for all actions with managment of content of the catalog 
// ================================================================================================
//    Programmer        :  Igor Trokhymchuk
//    Date              :  21.03.2006
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================
 class CatalogImpExp extends Catalog {
       
    var $curr=null;
    // ================================================================================================
    //    Function          : CatalogImpExp (Constructor)
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
            
            $this->settings = $this->GetSettings();
            $this->GetMultiTxtInArr();
    } // End of CatalogImpExp Constructor

    // ================================================================================================
    //    Function          : Form
    //    Version           : 1.0.0
    //    Date              : 29.11.2009
    //    Parms             : 
    //    Returns           : Error Indicator
    //
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
        <div>
         <fieldset style="border: 1px solid #000000; padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 5px">
          <legend>Импорт/Обновление товаров и категорий из .csv-файла</legend>
           <?php
           /* Write Form Header */
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
           <br/>Внимание! Процесс импорта может занять значительное кол-во времени (10-15 минут). Так что будьте терпеливы и не прерывайте импорт.
           <?phpphp$this->Form->WriteFooter();?>
         </fieldset>
        </div>
        <div>
         <fieldset style="border: 1px solid #000000; padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 5px">
          <legend>Обновление наличия товаров и цен из .csv-файла</legend>
           <?php
           /* Write Form Header */
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
           <br/><input type="submit" value="Обновить наличие товаров и цены">
           <?phpphp$this->Form->WriteFooter();?>
         </fieldset>
        </div>
        
        <div>
         <fieldset style="border: 1px solid #000000; padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 5px">
          <legend>Экспорт</legend>
           <form enctype="multipart/form-data" id="ExportPrice" name="ExportPrice" method="post" action="<?php echo $script;?>">
            <div style="float:left;">
             <?php/*
             Выберите категорию для Экспорта:
             <?php
             $arr_categs = $this->GetCatalogInArray(NULL, $this->Msg->show_text('TXT_SELECT_CATEGORY'), NULL, NULL, 0, 'back');
             $arr_categs['']=$this->Msg->show_text('TXT_ROOT_CATEGORY');
             //print_r($arr_categs);
             $this->Form->Select( $arr_categs, 'id_cat', $this->id_cat );
             */
             ?>
             <input type="submit" name="export_to_xml" value="Экспорт каталога в Excel со структурой категорий" onclick="Export('<?php echo $this->module;?>', 'export_to_xml', 'res_export_to_xml'); return false;" />
            </div>
            <div id="res_export_to_xml" style="height:25px;"></div>
            
            <div style="float:left;"><input type="submit" name="export_price" value="Экспорт каталога в виде Excel-прайса" onclick="Export('<?php echo $this->module;?>', 'export_price', 'res_export_price'); return false;" /></div>
            <div id="res_export_price"></div>

           </form>  
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
        <?php
        AdminHTML::PanelSimpleF();
    } //end of function Form()


    // ================================================================================================
    // Function : CSVtoArr()
    // Version : 1.0.0
    // Date : 29.11.2009
    // Parms : $path - path to CSV file 
    // Returns : true,false / Void
    // Description :  import or update descriptions of categories and goods from .csv-fiel to the databse
    // ================================================================================================
    // Programmer : Ihor Trokhymchuk
    // Date : 29.11.2009 
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function CSVtoArr($path=NULL)
    {
        $row = 1;
        $arr_cat = array();
        $arr_prod =  array();
        $cod_cat = 0; 
        
        //считываем данные из файла и формируем массивы с данными для сохранения в базу данных
        $handle = fopen($path, "r");
        while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
            //Пропускаем первые 4 строк, так как это заголовок
            if($row<5) {
                $row++;
                continue;
            }
            //var_dump($data);
            $num = count($data);
            //echo "<p> $num полей в строке $row: <br /></p>\n";        
            
            //Если нет всех данных, значите это категория, а не товар
            if( empty($data[2]) AND empty($data[3]) AND empty($data[4]) AND empty($data[5]) ){
                //$parent_cat = $cod_cat;
                $cod_cat =  $this->Conv(trim($data[0]));
                $arr_cat[$cod_cat]['name'] = $this->Conv(trim($data[1]));
                //$arr_cat[$cod_cat]['parent_cat'] = $parent_cat;
            }
            else{
                $cod_prod = $this->Conv(trim($data[0]));
                $arr_prod[$cod_prod]['name'] = $this->Conv(trim($data[1]));
                $arr_prod[$cod_prod]['unit'] = $this->Conv(trim($data[2]));
                $arr_prod[$cod_prod]['art_num'] = $this->Conv(trim($data[3]));
                $arr_prod[$cod_prod]['barcode'] = $this->Conv(trim($data[4]));
                $arr_prod[$cod_prod]['price'] = $this->Conv(trim($data[5]));
                $arr_prod[$cod_prod]['cat'] = $this->Conv(trim($cod_cat));
            }            
            $row++;
        }
        fclose($handle);
        
        //echo '<br>Categories:';
        //print_r($arr_cat);
        //echo '<br>Products:';
        //print_r($arr_prod);
        
        $data_success='';
        $data_faild = '';
        
        //Обновляем категории каталога
        $ins_cat_counter=0;
        foreach($arr_cat as $cod=>$v){
            $cat_name = addslashes($v['name']);
            
            $id_cat = $this->CheckIfCategoryExist($cat_name, $cod);
            //echo '<br>$t_cat='.$t_cat;
            if(!$id_cat){
                $q_m = "SELECT MAX(`move`) FROM `".TblModCatalog."` WHERE 1";
                $res_m = $this->db->db_Query($q_m);
                //echo "<br>q_m = .".$q_m." res = ".$res_m;
                $row_m = $this->db->db_FetchAssoc();
                $move = $row_m['MAX(`move`)']+1;
                $q="INSERT INTO `".TblModCatalog."` SET
                    `group`='0',
                    `level`='0',
                    `move`='".$move."',
                    `visible`='2'
                    ";
                $res = $this->db->db_Query( $q ); 
                //echo "<br>Insert Cat = ".$q." res = ".$res;
                if( !$res OR !$this->db->result){
                    $data_faild.='
                    <Row>
                     <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                     <Cell><Data ss:Type="String">'.$v['name'].'</Data></Cell>
                     <Cell><Data ss:Type="String">Ошибка добавления новой категории</Data></Cell>
                    </Row>
                    ';
                    continue;
                } 
                $id_cat = $this->db->db_GetInsertID();
            
                $q_cat = "INSERT INTO `".TblModCatalogSprName."` SET
                          `cod`='".$id_cat."',
                          `lang_id`='".$this->insert_lang_id."',
                          `name`='".$cat_name."'";
                $res_c = $this->db->db_Query( $q_cat ); 
                //echo "<br>Insert Cat Name = ".$q_cat." res = ".$res_c;
                if( !$res_c OR !$this->db->result){
                    $data_faild.='
                    <Row>
                     <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                     <Cell><Data ss:Type="String">'.$v['name'].'</Data></Cell>
                     <Cell><Data ss:Type="String">Ошибка добавления наименования новой категории</Data></Cell>
                    </Row>
                    ';
                    continue;
                } 
                
                $q_cat = "INSERT INTO `".TblModCatalogSprNameInd."` SET
                          `cod`='".$id_cat."',
                          `lang_id`='".$this->insert_lang_id."',
                          `name`='".$cod."'";
                $res_c = $this->db->db_Query( $q_cat ); 
                //echo "<br>Insert Cat Ind Name = ".$q_cat." res = ".$res_c;
                if( !$res_c OR !$this->db->result){
                    $data_faild.='
                    <Row>
                     <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                     <Cell><Data ss:Type="String">'.$v['name'].'</Data></Cell>
                     <Cell><Data ss:Type="String">Ошибка добавления кода ТМЦ для категории</Data></Cell>
                    </Row>
                    ';
                    continue;
                }
                
                //save category translit
                $parent_id_cat = $this->GetCategory($id_cat);
                $name[$this->insert_lang_id] = $cat_name;
                $res = $this->SaveTranslit($id_cat, $parent_id_cat, NULL, $name, $parent_id_cat );
                if( !$res ){
                    $data_faild.='
                    <Row>
                     <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                     <Cell><Data ss:Type="String">'.$v['name'].'</Data></Cell>
                     <Cell><Data ss:Type="String">Ошибка создания транслитерации для категории</Data></Cell>
                    </Row>
                    ';
                    continue;
                }
                /*
                $data_success.='
                <Row>
                 <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                 <Cell><Data ss:Type="String">'.$v['name'].'</Data></Cell>
                 <Cell><Data ss:Type="String">Добалена категория</Data></Cell>
                </Row>
                ';
                */
                $ins_cat_counter++;
            }
            //добавляем только что установленный id категории в массив с описанием категорий. Это пригодится при добавлении позиций в каталог
            $arr_cat[$cod]['id_cat']=$id_cat;  
        }//end of foreach 
        
        //Обновляем позиции каталога
        $ins_counter=0;
        $ins_counter_err=0;
        $upd_counter=0;
        $upd_counter_err=0;
        foreach($arr_prod as $cod=>$v){
            //находим id категории по коду ТМЦ
            $id_cat =$arr_cat[$v['cat']]['id_cat'];
            //если для товара не определена категория, то товар добавляем в специальную категорию "import - не определена категория" 
            if( empty($id_cat)) $id_cat = 274;
            
            $prop_name = addslashes($v['name']);
            $price = explode("грн", $v['price']);
            if(!isset($price[0])) $price =  str_replace(",", ".", trim($v['price']));
            else $price = str_replace(",", ".", trim($price[0]));
            
            $id_prop = $this->CheckIfProdExist($cod, $prop_name, $id_cat);
            if(!$id_prop){
                $q_p = "select MAX(`move`) from `".TblModCatalogProp."` where 1";
                $res_p = $this->db->db_Query($q_p);
                //echo "<br>q_p = .".$q_p." res_p = ".$res_p;
                $row_p = $this->db->db_FetchAssoc();
                $p_move = $row_p['MAX(`move`)']+1;
            
                $q = "INSERT INTO `".TblModCatalogProp."` SET
                      `id_cat`='".$id_cat."',
                      `id_manufac`='',
                      `id_group`='0',
                      `img`='',
                      `exist`='1',
                      `number_name`='".$cod."',
                      `price`='".$price."',
                      `opt_price`='',
                      `grnt`='',
                      `dt`='".date("Y-m-d")."',
                      `move`='".$p_move."',
                      `visible`='2',
                      `price_currency`='5',
                      `opt_price_currency`='5',
                      `new`='0',
                      `best`='0',
                      `art_num`='".$v['art_num']."',
                      `barcode`='".$v['barcode']."'";
                $res = $this->db->db_Query($q);
                //echo "<br>Insert Prod  = ".$q.' res = '.$res.' $this->db->result='.$this->db->result;
                $id_prop = $this->db->db_GetInsertID();
                if( !$res OR !$this->db->result) {
                    $data_faild.='
                    <Row>
                     <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                     <Cell><Data ss:Type="String">'.$v['name'].'</Data></Cell>
                     <Cell><Data ss:Type="String">Ошибка добавления нового товара в каталог</Data></Cell>
                    </Row>
                    ';
                    $ins_counter_err++;
                    continue;
                }
        
                $q = "INSERT INTO `".TblModCatalogPropSprName."` SET
                      `cod`='".$id_prop."',
                      `lang_id`='".$this->insert_lang_id."',
                      `name`='".$prop_name."'";
                $res_c = $this->db->db_Query($q);
                //echo "<br>Insert Prod Name = ".$q.' $res_c = '.$res_c.' $this->db->result='.$this->db->result;
                if( !$res_c OR !$this->db->result){
                    $data_faild.='
                    <Row>
                     <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                     <Cell><Data ss:Type="String">'.$v['name'].'</Data></Cell>
                     <Cell><Data ss:Type="String">Ошибка добавления наименования товара</Data></Cell>
                    </Row>
                    ';
                    $ins_counter_err++;
                    continue;
                }
                
                //save translit
                $parent_id_cat = $this->GetCategory($id_cat);
                $name[$this->insert_lang_id] = $prop_name;
                $res = $this->SaveTranslitProp($id_cat, $parent_id_cat, $id_prop, NULL, $name);
                if( !$res){
                    $data_faild.='
                    <Row>
                     <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                     <Cell><Data ss:Type="String">'.$v['name'].'</Data></Cell>
                     <Cell><Data ss:Type="String">Ошибка создания транслитерации для товара</Data></Cell>
                    </Row>
                    ';
                    $ins_counter_err++;
                    continue;
                }
                /*
                $data_success.='
                <Row>
                 <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                 <Cell><Data ss:Type="String">'.$v['name'].'</Data></Cell>
                 <Cell><Data ss:Type="String">Добавлено</Data></Cell>
                </Row>
                ';
                */
                $ins_counter++;
            }
            else{
                //echo '<hr>';
                //print_r($this->row_for_update);
                //echo '<br>$cod='.$cod
                if($cod!=$this->row_for_update['number_name'] OR $price!=$this->row_for_update['price'] OR $v['art_num']!=$this->row_for_update['art_num'] OR $v['barcode']!=$this->row_for_update['barcode']){
                    $q = "UPDATE `".TblModCatalogProp."` SET
                          `id_cat`='".$id_cat."',
                          `exist`='1',
                          `number_name`='".$cod."',
                          `price`='".$price."',
                          `dt`='".date("Y-m-d")."',
                          `art_num`='".$v['art_num']."',
                          `barcode`='".$v['barcode']."'
                          WHERE `id`='".$id_prop."'";
                    $res = $this->db->db_Query($q);
                    //echo "<br>Update Prod  = ".$q.' res = '.$res.' $this->db->result='.$this->db->result;
                    //$id_prop = $this->db->db_GetInsertID();
                    if( !$res OR !$this->db->result){
                        $data_faild.='
                        <Row>
                         <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                         <Cell><Data ss:Type="String">'.$v['name'].'</Data></Cell>
                         <Cell><Data ss:Type="String">Ошибка обновления данных товара</Data></Cell>
                        </Row>
                        ';
                        $upd_counter_err++;
                        continue;
                    }
                    /*
                    $data_success.='
                    <Row>
                     <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
                     <Cell><Data ss:Type="String">'.$v['name'].'</Data></Cell>
                     <Cell><Data ss:Type="String">Обновленно</Data></Cell>
                    </Row>
                    ';
                    */
                    $upd_counter++;
                }//end if    
            }//end else
            
        }//end of foreach 
        if(!empty($data_success)) $this->save_log($data_success, 'success');
        if(!empty($data_faild)) $this->save_log($data_faild, 'faild');
        ?>
        <div>
         Добавлено новых категорий:&nbsp;<?php echo $ins_cat_counter;?>
         <br/>Добавлено новых товаров:&nbsp;<?php echo $ins_counter;?>
         <?phpphpif($ins_counter_err>0){?><br/>Не удалось добавить новых товаров:<?php echo  $ins_counter_err; }?>
         <br/>Обновлено товаров:&nbsp;<?php echo $upd_counter;?>
         <?phpphpif($upd_counter_err>0){?><br/>Не удалось обновить товаров:<?php echo  $upd_counter_err; }?>
         <?php
         /*
         $log_file = SITE_PATH."/import/logs/faild/log.xml";
         if( file_exists($log_file)){
             ?><br>Для детального простомтра ошибок смотрите <a href="/import/logs/faild/log.xml" target="_blank" title="Посмотреть лог-файл с ошибками импорта">лог-файл</a><?php
         }
         */
         ?>
        </div>
        <?php
        return true;
    } // end of CSVtoArr
           

    // ================================================================================================
    //    Function          : CheckIfCategoryExist
    //    Version           : 1.0.0
    //    Date              : 21.03.2006
    //    Parms             : $cat_name - category name
    //                        $cat_cod -  category cod
    //    Returns           : Error Indicator
    //
    //    Description       : Check and update or insert categories
    // ================================================================================================
    function CheckIfCategoryExist($cat_name, $cat_cod)
    {
        $q = "select * from `".TblModCatalogSprNameInd."` where `name`='".$cat_cod."' and `lang_id`='".$this->insert_lang_id."'";
        $res = $this->db->db_Query($q);
        $rows = $this->db->db_GetNumRows();
        //echo "<br><hr> q cat 1 = ".$q." res = ".$res.' $rows='.$rows;
        if($rows>0){
            $row = $this->db->db_FetchAssoc();
            //echo "<hr> row = ";
            // print_r($row);
            if(!empty($row['cod'])){
                return $row['cod'];
            }
        }
        else {
            $q = "select * from `".TblModCatalogSprName."` where `name`='".$cat_name."' and `lang_id`='".$this->insert_lang_id."'";
            $res = $this->db->db_Query($q);
            $rows = $this->db->db_GetNumRows();
            //echo "<br><hr> q cat 2 = ".$q." res = ".$res.' $rows='.$rows;
            if($rows==1){
                $row = $this->db->db_FetchAssoc();
                //echo "<hr> row cat 2 = ";
                //print_r($row);
             
                $q_add = "select * from `".TblModCatalogSprNameInd."` where `cod`='".$row['cod']."' and `lang_id`='".$this->insert_lang_id."'";
                $res_add = $this->db->db_Query($q_add);
                $rows_add = $this->db->db_GetNumRows();
                //echo "<br><hr> q_add = ".$q_add." res = ".$res_add." rows_add = ".$rows_add;
                if($rows_add>0){
                    // $row_add = $this->db->db_FetchAssoc();
                    $q = "update `".TblModCatalogSprNameInd."` set `name`='".$cat_cod."' where `cod`='".$row['cod']."' and `lang_id`='".$this->insert_lang_id."'";
                    $res = $this->db->db_Query($q);
                    //  echo "<br><hr> q cat UP = ".$q." res = ".$res;
                }
                else {
                    $q = "insert into `".TblModCatalogSprNameInd."` set `name`='".$cat_cod."', `cod`='".$row['cod']."', `lang_id`='".$this->insert_lang_id."'";
                    $res = $this->db->db_Query($q);
                    // echo "<br><hr> q cat insert = ".$q." res = ".$res;
                }
                return $row['cod'];
            }
            //возвращаем категорию "import - не определена категория"
            //else{
            //   return 256;
            //}
        }
    }// end of function  CheckIfCategoryExist


    // ================================================================================================
    //    Function          : CheckIfProdExist
    //    Version           : 1.0.0
    //    Date              : 29.11.2009
    //    Parms             : $id_prop - number name of product
    //                        $prop_name - name of product
    //                        $id_cat - category of product
    //    Returns           : Error Indicator
    //
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
    // ================================================================================================
    // Programmer : Ihor Trokhymchuk
    // Date : 29.11.2009 
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function Conv($str)
    {
        if($this->to_charset!=$this->from_charset){
            $str = iconv($this->from_charset, $this->to_charset, $str);
        }
        return $str;
    }// end of fucntion function Conv()           
           

    // ================================================================================================
    // Function : save_log()
    // Version : 1.0.0
    // Date : 19.02.2008 
    // Parms : 
    // Returns : true,false / file
    // Description : save log file of update price
    // ================================================================================================
    // Programmer : Alex Kerest
    // Date : 19.02.2008
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function save_log($data = '', $type='faild')
    {
        if($type=='faild'){
          $path = 'faild';
        }
        if($type=='success'){
          $path = 'success';
        } 
    $head = '<?phpphpxml version="1.0"?>
    <?phpphpmso-application progid="Excel.Sheet"?>
    <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
     xmlns:o="urn:schemas-microsoft-com:office:office"
     xmlns:x="urn:schemas-microsoft-com:office:excel"
     xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
     xmlns:html="http://www.w3.org/TR/REC-html40">
     <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
      <Author>seotm</Author>
      <LastAuthor>seotm</LastAuthor>
      <Created>2008-02-19T15:26:17Z</Created>
      <Company>seotm.com</Company>
      <Version>11.5606</Version>
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
     $hhh = fopen(SITE_PATH."/import/logs/".$path."/log.xml", "w");
     //$data = iconv('windows-1251', 'utf-8',$data);
     fwrite($hhh, $data);
     fclose($hhh);
     @chmod (SITE_PATH."/import/logs/", 0777);
     return true;    
    }// end of function save_log()

    // ================================================================================================
    // Function : UpdatePriceCount()
    // Version : 1.0.0
    // Date : 08.01.2010
    // Parms : $path - path to CSV file 
    // Returns : true,false / Void
    // Description :  update from .csv-file price and count of goods
    // ================================================================================================
    // Programmer : Ihor Trokhymchuk
    // Date : 08.01.2010
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function UpdatePriceCount($path=NULL)
    {
        $row = 1;
        $arr_prod =  array();
        $str_id_prop = '';
        
        //считываем данные из файла и формируем массивы с данными для сохранения в базу данных
        $handle = fopen($path, "r");
        while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
            //Пропускаем первые 3 строк, так как это заголовок
            if($row<4) {
                $row++;
                continue;
            }
            //var_dump($data);
            $num = count($data);
            //echo "<p> $num полей в строке $row: <br /></p>\n";        
            
            //Если нет всех данных, значите это категория, а не товар
            if( empty($data[1]) ) continue;
            $cod_prod = $this->Conv(trim($data[1]));
            $arr_prod[$cod_prod]['name'] = $this->Conv(trim($data[4])); 
            $arr_prod[$cod_prod]['cnt'] = $this->Conv(trim($data[5]));
            $arr_prod[$cod_prod]['price'] = $this->Conv(trim($data[6]));
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
        foreach($arr_prod as $cod=>$v){
            $cnt = $v['cnt'];
            $price =  str_replace(",", ".", trim($v['price']));
            //echo '<hr>';
            //print_r($this->row_for_update);
            //echo '<br>$cod='.$cod
            $q = "UPDATE `".TblModCatalogProp."` SET
                  `exist`='".$cnt."',
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
                </Row>
                ';
                $upd_counter_err++;
                continue;
            }
            /*
            $data_success.='
            <Row>
             <Cell><Data ss:Type="String">'.$cod.'</Data></Cell>
             <Cell><Data ss:Type="String">'.$v['name'].'</Data></Cell>
             <Cell><Data ss:Type="String">Обновленно</Data></Cell>
            </Row>
            ';
            */
            $upd_counter++;
        }//end of foreach 
        if(!empty($data_success)) $this->save_log($data_success, 'success');
        if(!empty($data_faild)) $this->save_log($data_faild, 'faild');
        ?>
        <div>
         <br/>Обновлены наличие и/или цены в&nbsp;<?php echo $upd_counter;?> товаре(ах)
         <?phpphpif($upd_counter_err>0){?><br/>Не удалось обновить товаров:<?php echo  $upd_counter_err; }?>
         <?php
         /*
         $log_file = SITE_PATH."/import/logs/faild/log.xml";
         if( file_exists($log_file)){
             ?><br>Для детального простомтра ошибок смотрите <a href="/import/logs/faild/log.xml" target="_blank" title="Посмотреть лог-файл с ошибками импорта">лог-файл</a><?php
         }
         */
         ?>
        </div>
        <?php
        return true;
    } // end of UpdatePriceCount

    // ================================================================================================
    //    Function          : CheckIfProdExistS
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
    function CheckIfProdExistS($prod_name){
      $q = "select * from `".TblModCatalogPropSprName."` where 1 and `name` LIKE '%".$prod_name."%' and `lang_id`='3'";
      $res = $this->Right->Query($q, $this->user_id, $this->module);
      $rows = $this->Right->db_GetNumRows();
      //echo "<br> q = ".$q." res = ".$res." rows = ".$rows;
      if($rows==0){
        $tmp = explode(" ", $prod_name);
       // print_r($tmp);
        
         $str = $this->GetStrForSearch($tmp, 1); 
          //echo "<br> str = ".$str; 
         
         $q = "select * from `".TblModCatalogPropSprName."` where 1 and `name` LIKE '%".$str."%' and `lang_id`='3'";
         $res = $this->Right->Query($q, $this->user_id, $this->module);
         $rows = $this->Right->db_GetNumRows();
       //  echo "<br> q = ".$q." res = ".$res." rows = ".$rows;
         if($rows!=1){ 
            $str = $this->GetStrForSearch($tmp, 2); 
            //  echo "<br> str = ".$str; 
              if($str=='') return false; 
             $q = "select * from `".TblModCatalogPropSprName."` where 1 and `name` LIKE '%".$str."%' and `lang_id`='3'";
             $res = $this->Right->Query($q, $this->user_id, $this->module);
             $rows = $this->Right->db_GetNumRows();
             //echo "<br> q = ".$q." res = ".$res." rows = ".$rows; 
              if($rows!=1){ 
              $str = $this->GetStrForSearch($tmp, 3); 
           //   echo "<br> str = ".$str; 
              if($str=='') return false; 
             $q = "select * from `".TblModCatalogPropSprName."` where 1 and `name` LIKE '%".$str."%' and `lang_id`='3'";
             $res = $this->Right->Query($q, $this->user_id, $this->module);
             $rows = $this->Right->db_GetNumRows();
             //echo "<br> q = ".$q." res = ".$res." rows = ".$rows;
             
                if($rows!=1){ 
                  $str = $this->GetStrForSearch($tmp, 4); 
              //    echo "<br> str = ".$str; 
                 if($str=='') return false;
                 $q = "select * from `".TblModCatalogPropSprName."` where 1 and `name` LIKE '%".$str."%' and `lang_id`='3'";
                 $res = $this->Right->Query($q, $this->user_id, $this->module);
                 $rows = $this->Right->db_GetNumRows();
                // echo "<br> q = ".$q." res = ".$res." rows = ".$rows; 
                  } 
              }
         }
      }
      $row = $this->Right->db_FetchAssoc();
      if($rows==1)
      {
        return $row['cod'];
      }
      else return false;
    } // end of function  CheckIfProdExistS

    // ================================================================================================
    // Function : GetStrForSearch()
    // Version : 1.0.0
    // Date : 19.02.2008 
    // Parms : 
    // Returns : true,false / file
    // Description : build search string
    // ================================================================================================
    // Programmer : Alex Kerest
    // Date : 19.02.2008
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
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
    // Function : ExportCatalogToExcelXML
    // Version : 1.0.0
    // Date : 02.11.2009
    //
    // Parms :
    // Returns : true,false / Void
    // Description : export products to Excel XML-file
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 02.11.2009
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function ExportCatalogToExcelXML()
    {
        $q = "SELECT `".TblModCatalogProp."`.*, `".TblModCatalogPropSprName."`.`name` AS `prod_name`, `".TblModCatalogSprName."`.`name` AS `cat_name`
              FROM `".TblModCatalogProp."`, `".TblModCatalogPropSprName."`, `".TblModCatalogSprName."`, `".TblModCatalog."`
              WHERE `".TblModCatalogProp."`.`id_cat`=`".TblModCatalog."`.`id`
              AND `".TblModCatalog."`.`id`=`".TblModCatalogSprName."`.`cod`
              AND `".TblModCatalogSprName."`.`lang_id`='"._LANG_ID."'
              AND `".TblModCatalogProp."`.`id`=`".TblModCatalogPropSprName."`.`cod`
              AND `".TblModCatalogPropSprName."`.`lang_id`='"._LANG_ID."'
             ";
        if( !empty($this->id_cat) ) $q .= " AND `".TblModCatalog."`.`id`='".$this->id_cat."'"; 
        $q .= " ORDER BY `".TblModCatalog."`.`move` asc, `".TblModCatalogProp."`.`move` asc";
        $res = $this->db->db_Query($q);
        //echo '<br>$q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;
        if( !$res OR !$this->db->result) return false;
        $rows = $this->db->db_GetNumRows();
        //echo '<br>$rows='.$rows;
        
        for($i=0;$i<$rows;$i++){ 
            $row_data = $this->db->db_FetchAssoc();
            $index1 = stripslashes($row_data['id']);
            $arr_prod[$index1]=$row_data;
        }    
        $filename = 'catalog.xls'; 
        
        $doc = new SimpleXMLElement(  
         '<?phpphpxml version="1.0" encoding="utf-8"?>
         <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" 
          xmlns:o="urn:schemas-microsoft-com:office:office" 
          xmlns:x="urn:schemas-microsoft-com:office:excel" 
          xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" 
          xmlns:html="http://www.w3.org/TR/REC-html40"></Workbook>'  
        );  
           
        //<Worksheet ss:Name="Sheet1">  
        $worksheetNode = $doc->addChild('Worksheet');  
          
        $worksheetNode['ss:Name'] = 'Каталог товаров';  
          
        $worksheetNode->Table = '';//add a child with value '' by setter  
        
        //<Row> Header
        $row = $worksheetNode->Table->addChild('Row');  
        
        $cell = $row->addChild('Cell');  
        $cell->Data = $this->Conv($this->multi['FLD_ID'].' ('.$this->multi['FLD_CATEGORY'].')'); //shorthand  
        $cell->Data['ss:Type'] = 'String';//shorthand
            
        $cell = $row->addChild('Cell');  
        $cell->Data = $this->Conv($this->multi['FLD_CATEGORY']); //shorthand  
        $cell->Data['ss:Type'] = 'String';//shorthand
            
        $cell = $row->addChild('Cell');  
        $cell->Data = $this->Conv($this->multi['FLD_ID']); //shorthand  
        $cell->Data['ss:Type'] = 'String';//shorthand
        
        if ( isset($this->settings['name']) AND $this->settings['name']=='1' ) { 
            $cell = $row->addChild('Cell');  
            $cell->Data = $this->Conv($this->multi['FLD_NAME']); //shorthand  
            $cell->Data['ss:Type'] = 'String';//shorthand
        }
        
        if ( isset($this->settings['number_name']) AND $this->settings['number_name']=='1' ) {        
            $cell = $row->addChild('Cell'); //shorthand
            $cell->Data = $this->Conv($this->multi['FLD_NUMBER_NAME']);  
            $cell->Data['ss:Type'] = 'String';
        }
            
        if ( isset($this->settings['price']) AND $this->settings['price']=='1' ) {
            $cell = $row->addChild('Cell'); //shorthand
            $cell->Data = $this->Conv($this->multi['FLD_PRICE']);  
            $cell->Data['ss:Type'] = 'String';
        }
        
        foreach($arr_prod as $id=>$arr_prod2){
            //<Row> Products details
            $row = $worksheetNode->Table->addChild('Row');  
            
            $cell = $row->addChild('Cell');  
            $cell->Data = $this->Conv($arr_prod2['id_cat']); //shorthand  
            $cell->Data['ss:Type'] = 'String';//shorthand
            
            $cell = $row->addChild('Cell');  
            $cell->Data = $this->Conv($arr_prod2['cat_name']); //shorthand  
            $cell->Data['ss:Type'] = 'String';//shorthand
            
            $cell = $row->addChild('Cell');  
            $cell->Data = $this->Conv($arr_prod2['id']); //shorthand  
            $cell->Data['ss:Type'] = 'String';//shorthand
            
            if ( isset($this->settings['name']) AND $this->settings['name']=='1' ) {
                $cell = $row->addChild('Cell');  
                $cell->Data = $this->Conv($arr_prod2['prod_name']); //shorthand  
                $cell->Data['ss:Type'] = 'String';//shorthand
            }
            
            if ( isset($this->settings['number_name']) AND $this->settings['number_name']=='1' ) {    
                $cell = $row->addChild('Cell'); //shorthand
                $cell->Data = $this->Conv($arr_prod2['number_name']);  
                $cell->Data['ss:Type'] = 'String';
            }
            
            if ( isset($this->settings['price']) AND $this->settings['price']=='1' ) {
                $cell = $row->addChild('Cell'); //shorthand
                $cell->Data = $this->Conv($arr_prod2['price']);  
                $cell->Data['ss:Type'] = 'String';
            }
        }//end foreach by products  
        
        $uploaddir = SITE_PATH.'/export'; 
        if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777); 
        else @chmod($uploaddir,0777);
        $res = $doc->asXML($uploaddir.'/'.$filename);
        @chmod($uploaddir,0755);
        
        $path = '/export/'.$filename;
        $path_show = 'http://'.NAME_SERVER.$path;
        
        if($path){
            echo 'Скачать каталог <a href="http://'.NAME_SERVER.'/modules/mod_catalog/report_download.php?path='.$path.'&module='.$this->module.'&task='.$this->task.'">'.$path_show.'</a>';
            return true; 
        }
        else{
            echo 'Ошибка. Каталог не экспортировался';
            return false;
        }
        
    }//end of function ExportCatalogToExcelXML()
    
    // ================================================================================================
    // Function : loadTreeList()
    // Version : 1.0.0
    // Date : 30.03.2010
    // Parms :
    // Returns : true,false / Void
    // Description : load Tree of catalog into array
    // ================================================================================================
    // Programmer : Ihor Trokhymchuk
    // Date : 30.03.2010
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================ 
    function loadTreeList()
    {
        $db = new DB();
        $q = "SELECT 
                `".TblModCatalog."`.id, 
                `".TblModCatalog."`.level, 
                `".TblModCatalog."`.move, 
                `".TblModCatalogSprName."`.name
              FROM 
                `".TblModCatalog."`, `".TblModCatalogSprName."` 
              WHERE 
                `".TblModCatalog."`.id = `".TblModCatalogSprName."`.cod 
              AND
                `".TblModCatalogSprName."`.`lang_id`='".$this->lang_id."' 
              AND
                `".TblModCatalog."`.visible ='2' 
              ORDER BY 
                `move` ASC";
        
        $res = $db->db_Query($q);
        if(!$res OR !$db->result) 
            return false;
        //echo '$q ='.$q.' <br/>$res = '.$res;        
        $rows = $db->db_GetNUmRows($res);   
        if($rows==0) 
            return false;
            
        $tree = array();

        for($i = 0; $i < $rows; $i++){
            $row = $db->db_FetchAssoc($res);

            if(empty($tree[$row['level']])) {
                $tree[$row['level']] = array();
            }
            $tree[$row['level']][] = $row;
            //$this->tmpp_arr[$row['level']][$row['id']]='';
        }
        //print_r($this->tmpp_arr);
        return $this->makeTree($tree);
    }//end of function loadTreeList()

    // ================================================================================================
    // Function : makeTree()
    // Version : 1.0.0
    // Date : 30.03.2010
    // Parms :
    // Returns : true,false / Void
    // Description : make tree of catalog
    // ================================================================================================
    // Programmer : Ihor Trokhymchuk
    // Date : 30.03.2010
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================ 
    function makeTree(&$tree, $k_item = 0)
    {
        if(empty($tree[$k_item])) 
            return array();
        $a_tree = array();
        $n = count($tree[$k_item]);
        for($i = 0; $i < $n; $i++) {
            $row = $tree[$k_item][$i];
            $row['a_tree'] = $this->makeTree($tree, $tree[$k_item][$i]['id'] );
            $cnt_tree_in=0;
            $a_tree[] = $row;
        }
        //print_r($a_tree);
        return $a_tree;
    }//end of function makeTree()

    // ================================================================================================
    // Function : ExportCatalogTreeToExcelXML
    // Version : 1.0.0
    // Date : 02.11.2009
    //
    // Parms :
    // Returns : true,false / Void
    // Description : export products to Excel XML-file
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 02.11.2009
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function ExportCatalogTreeToExcelXML()
    {
        $this->tree = $this->loadTreeList();
        //print_r($this->tree);                    
        
        $q = "SELECT `".TblModCatalogProp."`.*, `".TblModCatalogPropSprName."`.`name` AS `prod_name`, `".TblModCatalogSprName."`.`name` AS `cat_name`
              FROM `".TblModCatalogProp."`, `".TblModCatalogPropSprName."`, `".TblModCatalogSprName."`, `".TblModCatalog."`
              WHERE `".TblModCatalogProp."`.`id_cat`=`".TblModCatalog."`.`id`
              AND `".TblModCatalog."`.`id`=`".TblModCatalogSprName."`.`cod`
              AND `".TblModCatalogSprName."`.`lang_id`='"._LANG_ID."'
              AND `".TblModCatalogProp."`.`id`=`".TblModCatalogPropSprName."`.`cod`
              AND `".TblModCatalogPropSprName."`.`lang_id`='"._LANG_ID."'
             ";
        if( !empty($this->id_cat) ) $q .= " AND `".TblModCatalog."`.`id`='".$this->id_cat."'"; 
        $q .= " ORDER BY `".TblModCatalog."`.`move` asc, `".TblModCatalogProp."`.`move` asc";
        $res = $this->db->db_Query($q);
        //echo '<br>$q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;
        if( !$res OR !$this->db->result) return false;
        $rows = $this->db->db_GetNumRows();
        //echo '<br>$rows='.$rows;
        
        for($i=0;$i<$rows;$i++){ 
            $row_data = $this->db->db_FetchAssoc();
            $index1 = stripslashes($row_data['id_cat']);
            $index2 = stripslashes($row_data['id']);
            $this->arr_prod[$index1][$index2]=$row_data;
        }
        //print_r($arr_prod);
        
             
        $filename = 'catalog.xls'; 
        
        $this->doc = new SimpleXMLElement(  
         '<?phpphpxml version="1.0" encoding="utf-8"?>
         <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" 
          xmlns:o="urn:schemas-microsoft-com:office:office" 
          xmlns:x="urn:schemas-microsoft-com:office:excel" 
          xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" 
          xmlns:html="http://www.w3.org/TR/REC-html40"></Workbook>'  
        );  
           
        //<Worksheet ss:Name="Sheet1">  
        $this->worksheetNode = $this->doc->addChild('Worksheet');  
          
        $this->worksheetNode['ss:Name'] = $this->Conv('Каталог продукции');  
          
        $this->worksheetNode->Table = '';//add a child with value '' by setter  

        //<Row> Header
        $row = $this->worksheetNode->Table->addChild('Row');
        
        $cell = $row->addChild('Cell');  
        $cell->Data = 'Категория'; //shorthand  
        $cell->Data['ss:Type'] = "String";
        
        $cell = $row->addChild('Cell');  
        $cell->Data = ''; //shorthand  
        $cell->Data['ss:Type'] = "String";
        
        $cell = $row->addChild('Cell');  
        $cell->Data = ''; //shorthand  
        $cell->Data['ss:Type'] = "String";
        
        $cell = $row->addChild('Cell');  
        $cell->Data = ''; //shorthand  
        $cell->Data['ss:Type'] = "String";
        
        $cell = $row->addChild('Cell');  
        $cell->Data = $this->Conv($this->multi['FLD_ID']); //shorthand  
        $cell->Data['ss:Type'] = "String";
        
         if ( isset($this->settings['name']) AND $this->settings['name']=='1' ) { 
             $cell = $row->addChild('Cell');  
             $cell->Data = $this->Conv($this->multi['FLD_NAME']); //shorthand  
             $cell->Data['ss:Type'] = 'String';//shorthand
         }
         
         if ( isset($this->settings['number_name']) AND $this->settings['number_name']=='1' ) {        
             $cell = $row->addChild('Cell'); //shorthand
             $cell->Data = $this->Conv($this->multi['FLD_NUMBER_NAME']);  
             $cell->Data['ss:Type'] = 'String';
         }
                    
         if ( isset($this->settings['price']) AND $this->settings['price']=='1' ) {
             $cell = $row->addChild('Cell'); //shorthand
             $cell->Data = $this->Conv($this->multi['FLD_PRICE']);  
             $cell->Data['ss:Type'] = 'String';
         }
        
        $this->WriteTree($this->tree);
        
        $uploaddir = SITE_PATH.'/export'; 
        if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777); 
        else @chmod($uploaddir,0777);
        $res = $this->doc->asXML($uploaddir.'/'.$filename);
        @chmod($uploaddir,0755);
        
        $path = '/export/'.$filename;
        $path_show = 'http://'.NAME_SERVER.$path;
        
        if($path){
            echo 'Скачать каталог <a href="http://'.NAME_SERVER.'/modules/mod_catalog/report_download.php?path='.$path.'&module='.$this->module.'&task='.$this->task.'">'.$path_show.'</a>';
            return true; 
        }
        else{
            echo 'Ошибка. Каталог не экспортировался';
            return false;
        }
        
    }//end of function ExportCatalogTreeToExcelXML()

    // ================================================================================================
    // Function : WriteTree()
    // Version : 1.0.0
    // Date : 30.03.2010
    // Parms :
    // Returns : true,false / Void
    // Description : show ierarchy of catalog
    // ================================================================================================
    // Programmer : Ihor Trokhymchuk
    // Date : 30.03.2010
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================    
    function WriteTree(&$a_tree, $level = 0, $flag = 0, $cnt_sub=0)
    {
        $n = count($a_tree);
        //echo '<br>$cnt_sub='.$cnt_sub;
        
        //кол-ва пустых ячеек, отводимых на построение иерархии. По умолчанию 5 уровней вложения.
        $cnt_empty_cells = 4;
        
        //проход по иерархии категорий
        for($i=0;$i<$n;$i++){
            //<Row> Header
            $row = $this->worksheetNode->Table->addChild('Row');
            
            //отображаю пустые ячейки для обозначения иерархии (вложений) категорий 
            for($j=0;$j<$cnt_sub;$j++){
                $cell = $row->addChild('Cell');  
                $cell->Data = ''; //shorthand  
                $cell->Data['ss:Type'] = 'String';//shorthand
            }
            
            //отображаю название категории
            $cell = $row->addChild('Cell');  
            $cell->Data = $this->Conv( stripslashes($a_tree[$i]['name']) ); //shorthand  
            $cell->Data['ss:Type'] = 'String';//shorthand
            
            //если в категори есть товары, то вывожу их
            if(isset($this->arr_prod[$a_tree[$i]['id']])){
                //<Row> Header
                $row = $this->worksheetNode->Table->addChild('Row');
                
                //отображаю пустые ячейки для обозначения иерархии (вложений) категорий
                for($j=0;$j<$cnt_empty_cells;$j++){
                    $cell = $row->addChild('Cell');  
                    $cell->Data = ''; //shorthand  
                    $cell->Data['ss:Type'] = 'String';//shorthand
                }
                
                foreach($this->arr_prod[$a_tree[$i]['id']] as $id=>$arr_prod2){
                    //<Row> Products details
                    $row = $this->worksheetNode->Table->addChild('Row');  
                    
                    for($j=0;$j<$cnt_empty_cells;$j++){
                        $cell = $row->addChild('Cell');  
                        $cell->Data = ''; //shorthand  
                        $cell->Data['ss:Type'] = 'String';//shorthand
                    }
                 
                    $cell = $row->addChild('Cell');  
                    $cell->Data = $this->Conv($arr_prod2['id']); //shorthand  
                    $cell->Data['ss:Type'] = 'Number';//shorthand
                    
                    if ( isset($this->settings['name']) AND $this->settings['name']=='1' ) {
                        $cell = $row->addChild('Cell');  
                        $cell->Data = $this->Conv( stripslashes($arr_prod2['prod_name']) ); //shorthand  
                        $cell->Data['ss:Type'] = 'String';//shorthand
                    }
                    
                    if ( isset($this->settings['number_name']) AND $this->settings['number_name']=='1' ) {    
                        $cell = $row->addChild('Cell'); //shorthand
                        $cell->Data = $this->Conv( stripslashes($arr_prod2['number_name']) );  
                        $cell->Data['ss:Type'] = 'String';
                    }
                    
                    if ( isset($this->settings['price']) AND $this->settings['price']=='1' ) {
                        $cell = $row->addChild('Cell'); //shorthand
                        $cell->Data = $this->Conv($arr_prod2['price']);  
                        $cell->Data['ss:Type'] = 'String';
                    }
                    
                }//end foreach by products
                
            }
            $this->WriteTree($a_tree[$i]['a_tree'], $a_tree[$i]['level'], 1, ($cnt_sub+1));
        }
    }//end of function WriteTree()
    
    
    // ================================================================================================
    // Function : GetData()
    // Version : 1.0.0
    // Date : 2.10.2008
    // Parms :
    // Returns : true,false / Void
    // Description : Get Export Data
    // ================================================================================================
    // Programmer : Alex Kerest
    // Date : 02.10.2008
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function GetData(){
        $arr = array();
        $CatalogL = new CatalogLayout();
        $c_settings = $this->GetSettings();  // Catalog Settings

        $sSQL = "
        SELECT 
            `".TblModCatalogProp."`.id,
            `".TblModCatalogProp."`.id_cat,
            `".TblModCatalogProp."`.price,
            `".TblModCatalogProp."`.price_currency,
            `".TblModCatalogPropSprName."`.name,
            `".TblModCatalogSprName."`.name AS cat_name
        FROM
            `".TblModCatalogProp."`,
            `".TblModCatalogPropSprName."`,
            `".TblModCatalog."`,
            `".TblModCatalogSprName."` 
        WHERE
            `".TblModCatalogProp."`.`id_cat`=`".TblModCatalog."`.`id`
            AND
            `".TblModCatalogProp."`.id=`".TblModCatalogPropSprName."`.cod
            AND
            `".TblModCatalog."`.id=`".TblModCatalogSprName."`.cod
            AND
            `".TblModCatalogPropSprName."`.lang_id='"._LANG_ID."'
            AND
            `".TblModCatalogSprName."`.lang_id='"._LANG_ID."'
        ORDER BY `".TblModCatalog."`.`move`, `".TblModCatalogProp."`.`move` 
        ";


        $res = $this->db->db_Query($sSQL);
        //echo "<br />sSQL = ".$sSQL." res = ".$res."<br><br><br>";
        $rows = $this->db->db_GetNumRows();
        for($i=0;$i<$rows;$i++){
            $offer = $this->db->db_FetchAssoc();
            $click = $CatalogL->Link($offer['id_cat'], $offer['id']);
            //echo "<br>".$click;
            //  $name = $offer['name'];
            $tm = explode(" ", $offer['name']);
            if(isset($tm[0])) {$type = $tm[0];}
            else { $type = '';}
            //echo "<br>".$type;
                
            if(isset($offer['price']) and !empty($offer['price'])) $price = $offer['price'];
            else $price = '1';
            // echo "<br> price = ".$price;
                
            array_push($arr,
                array(
                    'id'        => $offer['id'],                // ИД товара
                    'category'    => stripslashes($offer['cat_name']),            // ИД категории в которой находится товар
                    'click'        => $click,                 // Адрес(URL) куда должен перейти пользователь на ваш сайт при клике на товар
                    'name'    => stripslashes($offer['name']),                // Название товара
                    'currency'    =>  $offer['price_currency'],        // Буквенный код валюты, например UAH
                    'price'    => $price,        // Цена товара в указаной валюте
                     )
            );    
        }
        //   print_r($arr);
        return $arr;
    } // end of function GetData()

    
    // ================================================================================================
    // Function : ExportPriceToExcel()
    // Version : 1.0.0
    // Date : 29.11.2009
    // Parms : $path - path to CSV file 
    // Returns : true,false / Void
    // Description :  import or update descriptions of categories and goods from .csv-fiel to the databse
    // ================================================================================================
    // Programmer : Ihor Trokhymchuk
    // Date : 29.11.2009 
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function ExportPriceToExcel(){
        $arr = $this->GetData();
        //print_r($arr);
        $Currency = new SystemCurrencies(); 

        $doc = new SimpleXMLElement(  
         '<?phpphpxml version="1.0" encoding="utf-8"?>
         <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" 
          xmlns:o="urn:schemas-microsoft-com:office:office" 
          xmlns:x="urn:schemas-microsoft-com:office:excel" 
          xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" 
          xmlns:html="http://www.w3.org/TR/REC-html40"></Workbook>'  
        );  
           
        //<Worksheet ss:Name="Sheet1">  
        $worksheetNode = $doc->addChild('Worksheet');  
          
        $worksheetNode['ss:Name'] = 'Прайс';  
          
        $worksheetNode->Table = '';//add a child with value '' by setter  
        
        //<Row> Header
        $row = $worksheetNode->Table->addChild('Row');  
            
        $cell = $row->addChild('Cell');  
        $cell->Data = $this->Conv($this->multi['FLD_CATEGORY']); //shorthand  
        $cell->Data['ss:Type'] = 'String';//shorthand
            
        $cell = $row->addChild('Cell');  
        $cell->Data = $this->multi['FLD_ID']; //shorthand  
        $cell->Data['ss:Type'] = 'String';//shorthand
        
        if ( isset($this->settings['name']) AND $this->settings['name']=='1' ) { 
            $cell = $row->addChild('Cell');  
            $cell->Data = $this->Conv($this->multi['FLD_NAME']); //shorthand  
            $cell->Data['ss:Type'] = 'String';//shorthand
        }
        
        if ( isset($this->settings['price']) AND $this->settings['price']=='1' ) {
            $cell = $row->addChild('Cell'); //shorthand
            $cell->Data = $this->Conv($this->multi['FLD_PRICE']);  
            $cell->Data['ss:Type'] = 'String';
        }
        
        $cell = $row->addChild('Cell');  
        $cell->Data = $this->Conv('Ссылка на страницу'); //shorthand  
        $cell->Data['ss:Type'] = 'String';//shorthand
        
        for($j=0;$j<count($arr);$j++){
            $price = $Currency->Converting($arr[$j]['currency'],  1, stripslashes($arr[$j]['price']), 2 );

            //Row Data
            $row = $worksheetNode->Table->addChild('Row'); 
            
            $cell = $row->addChild('Cell');  
            $cell->Data = $this->Conv($arr[$j]['category']); //shorthand  
            $cell->Data['ss:Type'] = 'String';//shorthand
                
            $cell = $row->addChild('Cell');  
            $cell->Data = $arr[$j]['id']; //shorthand  
            $cell->Data['ss:Type'] = 'String';//shorthand
            
            if ( isset($this->settings['name']) AND $this->settings['name']=='1' ) { 
                $cell = $row->addChild('Cell');  
                $cell->Data = $this->Conv($arr[$j]['name']); //shorthand  
                $cell->Data['ss:Type'] = 'String';//shorthand
            }
            
            if ( isset($this->settings['price']) AND $this->settings['price']=='1' ) {
                $cell = $row->addChild('Cell'); //shorthand
                $cell->Data = $price;  
                $cell->Data['ss:Type'] = 'String';
            }
            
            $cell = $row->addChild('Cell');  
            $cell->Data = 'http://www.'.$_SERVER['SERVER_NAME'].$arr[$j]['click']; //shorthand  
            $cell->Data['ss:Type'] = 'String';//shorthand            
 
        } // end for 
        
        $filename = 'price.xls';
        $uploaddir = SITE_PATH.'/export'; 
        if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777); 
        else @chmod($uploaddir,0777);
        $res = $doc->asXML($uploaddir.'/'.$filename);
        @chmod($uploaddir,0755);
        
        $path = '/export/'.$filename;
        $path_show = 'http://'.NAME_SERVER.$path;
        
        if($path){
            echo 'Скачать прайс <a href="http://'.NAME_SERVER.'/modules/mod_catalog/report_download.php?path='.$path.'&module='.$this->module.'&task='.$this->task.'">'.$path_show.'</a>';
            return true; 
        }
        else{
            echo 'Ошибка. Прайс не экспортировался';
            return false;
        }
        
        /*
        $uploaddir = SITE_PATH.'/export'; 
        if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777); 
        else @chmod($uploaddir,0777);
        $uploadpath = $uploaddir.'/price.xls';
        $hhh = fopen($uploadpath, "w");
        fwrite($hhh, $str);
        fclose($hhh);
        @chmod($uploaddir,0755);
        
        $path = '/export/price.xls';
        echo 'Скачать прайс <a href="http://'.NAME_SERVER.'/modules/mod_catalog/report_download.php?path='.$path.'&module='.$this->module.'&task='.$this->task.'">'.$path.'</a>';
        */
    }//end of function ExportPriceToExcel()

}// end of class CatalogImpExp	   

?>
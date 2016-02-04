<?php
include_once( SITE_PATH.'/modules/mod_order/order.defines.php' );

class OrderImpExp extends Order {

    public $dateFrom;
    public $dateTo;
    public $from_charset;
    public $to_charset;
    public $order_status;

    function OrderImpExp ($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL) {
        //Check if Constants are overrulled

        $user_id  != "" ? $this->user_id = $user_id  : $this->user_id = NULL;
        $module   != "" ? $this->module  = $module   : $this->module  = NULL;

        $this-> lang_id = _LANG_ID;

        if (empty($this->db)) $this->db = new DB();
        if (empty($this->Right)) $this->Right = new Rights($this->user_id, $this->module);
        if (empty($this->Form)) $this->Form = new Form('form_mod_catalog_ImpExp');
        if (empty($this->Spr)) $this->Spr = new  SysSpr($this->user_id, $this->module);

        if (empty($this->multi)) $this->multi = check_init_txt('TblBackMulti',TblBackMulti);
    }
//--- End of Catalog_content Constructor -------------------------------------------------------------------------------


    function showFrom()
    {
        $script = 'module='.$this->module;
        $script = $_SERVER['PHP_SELF']."?$script";
        $def_date_from = date('Y-m-d', strtotime("-1 month"));
        $def_date_to = date('Y-m-d');
        $strOrdersToExport = ( !empty($this->arrOrderId) ) ? " '".implode("','", $this->arrOrderId)."' " : null;
        ?>
        <style>
            .date-picker-field{
                text-align: center;
            }
        </style>
        <fieldset style="border: 1px solid #000000; padding:5px; min-width: 300px;">
            <legend>Экспорт заказов: </legend>
            <form id="ExportOrders" name="export_orders" method="post" action="<?=$script;?>">
                <div style="padding: 5px 15px 0px 15px; text-align: center">
                    <input type="hidden" name="orders_to_export" value="<?=$strOrdersToExport?>">
                    <?/*
                    <b>статус заказа:</b>
                    <br/>
                    <input id="payed" type="checkbox" name="order_status[]" value="4" checked="checked"/><label for="paid">оплаченые</label>
                    <input id="canceled" type="checkbox" name="order_status[]" value="5"/><label for="canceled">отмененые</label>
                    <br/>
                    <br/>
                    <b>период:</b>
                    <br/>
                    <label for="dateFrom">от:</label>
                    <input id="dateFrom" class="date-picker-field" type="text" name="date_from" value="<?=$def_date_from?>"/>
                    <label for="dateFrom">до:</label>
                    <input id="dateTo" class="date-picker-field" type="text" name="date_to" value="<?=$def_date_to?>"/>
                    */?>
                    <br/>
                    <input type="submit"
                           name="export_to_csv"
                           value="Экспорт заказов в csv"
                           onclick="Export('<?=$this->module;?>', 'export_orders_csv', 'exportCVSOrdesResult'); return false;"
                           style="font-size: 16px;"
                        />
                    <br/>
                    <br/>
                    <div id="exportCVSOrdesResult" style="font-size: 14px; "></div>
                    <?/*
                    <input type="submit"
                           name="export_to_xls"
                           value="Экспорт заказов в xls"
                           onclick="Export('<?=$this->module;?>', 'export_orders', 'exportXLSOrdesResult'); return false;"
                           style="font-size: 16px;"
                        />
                     */?>
                    <br/>
                    <br/>
                    <div id="exportXLSOrdesResult" style="font-size: 14px; "></div>
                </div>


            </form>
        </fieldset>

        <script type="text/javascript">
            $(".date-picker-field").datepicker({ dateFormat: 'yy-mm-dd' });

            function Export(module, task, div_id){
                //alert(module+' '+task+' '+div_id);
                Did = "#"+div_id;
                var formData = $('#ExportOrders').serialize();
                $.ajax({
                    type: "POST",
                    url: "/modules/mod_order/order_ImpExp.php?module="+module+"&task="+task,
                    data: formData,
                    beforeSend : function(){
                        $(Did).html( '<img src="/admin/images/ajax-loader.gif"/>');
                    },
                    success: function(html){
                        $(Did).html(html);
                        $(Did).show("slow");
                    }/*,
                     error: function (result, status)
                     {
                     $(Did).html(status);
                     $(Did).css("display", "block");
                     $(Did).show("slow");
                     }*/
                });
            }
        </script>
        <?
        AdminHTML::PanelSimpleF();
    }
//--- end of function show ------------------------------------------------------------------------------------------------------------

    function getDataToExport(){
        //begin: get data fo export
        $str_filter = '';
        //0. check filter
        if ( !empty($this->strOrderId)){
            $str_filter = ' AND  `'.TblModOrderComments.'`.`id_order` IN ('.$this->strOrderId.') ';
        }
        else{
            echo 'Нет заказов для экспорта.<br> Пожалуйста, отметьте нужные заказы галочкой.';
            return false;
        }


        //1. get ordered products by selected period:
        $q = "
            SELECT
                `".TblModOrderComments."`.`date`,
                `".TblModOrderComments."`.`buyer_id`,
                `".TblModOrderComments."`.`addr`,
                `".TblModOrderComments."`.`comment`,
                `".TblModOrderComments."`.`pay_method`,
                `".TblModOrderComments."`.`delivery_method`,
                `".TblModOrder."`.`id_order`,
                `".TblModOrder."`.`prod_id`,
                `".TblModOrder."`.`price`,
                `".TblModOrder."`.`currency`,
                `".TblModOrder."`.`quantity`,
                `".TblModCatalogPropSprName."`.`name` ,
                `".TblModUser."`.`name` as `user_name`,
                `".TblModUser."`.`phone_mob`,
                `".TblSysUser."`.`email`
            FROM
                `".TblModOrderComments."`
                    LEFT JOIN `".TblModOrder."` ON ( `".TblModOrder."`.`id_order` = `".TblModOrderComments."`.`id_order` ),
                `".TblModCatalogPropSprName."`,
                `".TblModUser."`,
                `".TblSysUser."`

            WHERE
                `".TblModCatalogPropSprName."`.cod = `".TblModOrder."`.`prod_id`
                AND
                `".TblModUser."`.`sys_user_id` = `".TblModOrderComments."`.`buyer_id`
                AND
                `".TblSysUser."`.`id` = `".TblModOrderComments."`.`buyer_id`
                ".$str_filter."
            ORDER BY `date` DESC
        ";

//        echo '<br/>$q: '.$q;
        $res = $this->Right->db_Query($q);
//        var_dump($res);

        if (!$res){
            echo 'произошла ошибка.';
            return false;
        }

        //2. form arrays to manage data
        while ( $row = $this->Right->db_FetchAssoc() ){

            //create array of products by selected period
            $dataToExport[] = $row;
        }
//        var_dump($dataToExport);

        //end: get data fo export
        return $dataToExport;
    }
//--- end getDataToExport() ----------------------------------------------------------------------------------------------------------------------------------------------

    function exportOrdersToExcelXML(){

        $dataToExport = $this->getDataToExport();

        if ( !$dataToExport ) return false;


        if ( count($dataToExport) == 0 ) exit('не найдено заказов');

        //init helpers
        $SysCurrencies = new SystemCurrencies();
        $arrPayMethods = $this->Spr->GetMulti(TblModOrderSprPayMethod);
        $arrDeliveryMethods = $this->Spr->GetMulti(TblModOrderSprDelivery);


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

        //begin: form rows for products
        foreach ($dataToExport as $prod) {
            $row = $worksheetNode->Table->addChild('Row');

            //id_order
            $cell = $row->addChild('Cell');
            $cell->Data = $prod['id_order'];
            $cell->Data['ss:Type'] = 'String';

            //date
            $date = date('d.m.Y H:i', strtotime($prod['date']));
            $cell = $row->addChild('Cell');
            $cell->Data = $date;
            $cell->Data['ss:Type'] = 'String';

            //prod_id
            $cell = $row->addChild('Cell');
            $cell->Data = $prod['prod_id'];
            $cell->Data['ss:Type'] = 'String';

            //name
            $cell = $row->addChild('Cell');
            $cell->Data = $prod['name'];
            $cell->Data['ss:Type'] = 'String';

            //quantity
            $cell = $row->addChild('Cell');
            $cell->Data = $prod['quantity'];
            $cell->Data['ss:Type'] = 'String';

            //шт.
            $cell = $row->addChild('Cell');
            $cell->Data = 'шт';
            $cell->Data['ss:Type'] = 'String';

            //price
            $price = $SysCurrencies->Converting($prod['currency'], 5, $prod['price']); // to UAH
            $cell = $row->addChild('Cell');
            $cell->Data = $price;
            $cell->Data['ss:Type'] = 'String';

            //НДС
            $cell = $row->addChild('Cell');
            $cell->Data = 0; //так надо
            $cell->Data['ss:Type'] = 'String';

            //user_name
            $cell = $row->addChild('Cell');
            $cell->Data = $prod['user_name'];
            $cell->Data['ss:Type'] = 'String';

            //user_id
            $cell = $row->addChild('Cell');
            $cell->Data = $prod['buyer_id'];
            $cell->Data['ss:Type'] = 'String';

            //пусто - так надо
            $cell = $row->addChild('Cell');
            $cell->Data = '';
            $cell->Data['ss:Type'] = 'String';

            //пусто - и опять, так надо... все продумано - не сцыы
            $cell = $row->addChild('Cell');
            $cell->Data = '';
            $cell->Data['ss:Type'] = 'String';

            //user_phone
            $cell = $row->addChild('Cell');
            $cell->Data = $prod['phone_mob'];
            $cell->Data['ss:Type'] = 'String';

            //comment
            $payment  = ( isset($arrPayMethods[ $prod['pay_method'] ]) ) ? $arrPayMethods[ $prod['pay_method'] ] : null;
            $delivery = ( isset($arrDeliveryMethods[ $prod['delivery_method'] ]) ) ? $arrDeliveryMethods[ $prod['delivery_method'] ] : null;
            $comment = '
            Адресс:
            '.$prod['addr'].'
            Оплата:
            '.$payment.'
            Доставка:
            '.$delivery.'
            Комментарий:
            '.$prod['comment'].'
            ';
            $cell = $row->addChild('Cell');
            $cell->Data = $comment;
            $cell->Data['ss:Type'] = 'String';

        }
        //end: form rows for products


        $filename = $this->generateNameForExportFile().'.xls';

        $uploaddir = SITE_PATH.'/export';
        if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777);
        else @chmod($uploaddir,0777);
        $res = $doc->asXML($uploaddir.'/'.$filename);
        @chmod($uploaddir,0755);

        $path = '/export/'.$filename;
        $path_show = 'http://'.$_SERVER['SERVER_NAME'].$path;

        if($path){
            //echo 'Скачать файл xls:<br/> <a href="http://'.NAME_SERVER.'/modules/mod_catalog/report_download.php?path='.$path.'&module='.$this->module.'&task='.$this->task.'">'.$path_show.'</a>';
            echo 'Скачать файл xls:<br/> <a href="http://'.$_SERVER['SERVER_NAME'].''.$path.'">'.$path_show.'</a>';
            return true;
        }
        else{
            echo 'Ошибка. Каталог не экспортировался';
            return false;
        }
    }
//--- end exportOrdersToExcelXML() -------------------------------------------------------------------------------------


    function exportOrdersToCSV(){

        $dataToExport = $this->GetDataToExport();
        if ( !$dataToExport ) return false;


        //2.form string for output file
        $outputBody = '';
        $separator = ';';
        $endOfLine = "\n";

        //3. init helpers
        $SysCurrencies = new SystemCurrencies();
        $arrPayMethods = $this->Spr->GetMulti(TblModOrderSprPayMethod);
        $arrDeliveryMethods = $this->Spr->GetMulti(TblModOrderSprDelivery);


        //4.
        //begin: form rows for products
        foreach ($dataToExport as $prod) {

            //id_order
            $outputBody .= $prod['id_order'].$separator;

            //date
            $date = date('d.m.Y H:i', strtotime($prod['date']));
            $outputBody .= $date.$separator;

            //prod_id
            $outputBody .= $prod['prod_id'].$separator;

            //name
            $outputBody .= $this->formatCsvField( $prod['name'] ).$separator;

            //quantity
            $outputBody .= $prod['quantity'].$separator;

            //шт.
            $outputBody .= 'шт'.$separator;

            //price
            $price = $SysCurrencies->Converting($prod['currency'], 5, $prod['price']); // to UAH
            $outputBody .= $price.$separator;


            //НДС
            $outputBody .= '0'.$separator; //так надо


            //user_name
            $outputBody .= $this->formatCsvField( $prod['user_name'] ).$separator;

            //user_id
            $outputBody .= $prod['buyer_id'].$separator;

            //пусто - так надо
            $outputBody .= ''.$separator;


            //пусто - и опять, так надо... все продумано - не сцыы
            $outputBody .= ''.$separator;

            //user_phone
            $outputBody .= $prod['phone_mob'].$separator;

            //email
            $outputBody .= $prod['email'].$separator;

            //comment
            $payment  = ( isset($arrPayMethods[ $prod['pay_method'] ]) ) ? $arrPayMethods[ $prod['pay_method'] ] : null;
            $delivery = ( isset($arrDeliveryMethods[ $prod['delivery_method'] ]) ) ? $arrDeliveryMethods[ $prod['delivery_method'] ] : null;
            $comment = 'Оплата: '.$payment.'
Доставка: '.$delivery.'
Адресс: '.$prod['addr'].'
Комментарий: '.$prod['comment'];

            $outputBody .= $this->formatCsvField( $comment).$endOfLine;
        }
        //end: form rows for products

        //4. generate export file
        $filename = $this->generateNameForExportFile().'.csv';


        $uploaddir = SITE_PATH.'/export';
        $fullpath = $uploaddir.'/'.$filename;
        if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777);
        else @chmod($uploaddir,0777);

        if (!$handle = fopen($fullpath, 'w')) {
            echo "Не могу открыть файл ($fullpath)";
            return;
        }

        //$output = $this->Conv($outputBody,$this->from_charset,$this->to_charset);
        $output = iconv($this->from_charset, $this->to_charset, $outputBody);

        // Записываем $out в наш открытый файл.
        if (fwrite($handle, $output) === FALSE) {
            echo "Не могу произвести запись в файл ($fullpath)";
            return;
        }
        //readfile($fullpath);
        fclose($handle);

//        include_once( SITE_PATH.'/include/PHPExcel-1.8/Classes/PHPExcel.php' );
//        $objPHPExcel = $objReader->load('MyCSVFile.csv');
//        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $filename);
//        $objWriter->save("05featuredemo.xlsx");

        $path = '/export/'.$filename;
        $path_show = 'http://'.NAME_SERVER.$path;

        if($path){
            echo 'Скачать файл:<br/> <a href="'.$path_show.'">'.$path_show.'</a>';
            return true;
        }
        else{
            echo 'Ошибка. Каталог не экспортировался';
            return false;
        }
    }
//--- end of function exportOrdersToCSV ------------------------------------------------------------------------------------------------------------

function formatCsvField( $str ){
    $str = str_replace( '"', '""', $str ); //replace " to "".
    $str = '"'.$str.'"'; //wrap string in " "
    return $str;
}

function generateNameForExportFile(){
    $str = 'ordersExport_'.date('d-m-Y');
//    $arrStatusLabel = array( 4 => 'Paid', 5 => 'Canceled' );
//    if (isset($this->order_status)) {
//        foreach ($this->order_status as $status) {
//            $str .= $arrStatusLabel[$status];
//        }
//        $str .= '_';
//    }
//    $str .= $this->dateFrom.'_'.$this->dateTo;
    return $str;
}

}// end of class CatalogImpExp	   
?>
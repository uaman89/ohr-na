<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
<head>
    <META http-equiv="Content-Type" content="text/html; charset=utf-8">
    <META http-equiv="content-language" content="ru">
    <title><?=$multi['TXT_STATEMENT_ACCOUNT']?></title>
    <style>
        *{
            margin: 0;
            padding: 0;
        }
        .wrapper{
            margin: 0 auto;
            font-family: Tahoma, Arial;
            height: 29cm;
            width: 21cm;
            /*border: 1px solid red;*/
            overflow: hidden;
            box-sizing: border-box;
        }
        p {margin: 0; padding: 0;}
        table{border-collapse: collapse;}

        .prods-table{
            overflow: hidden;
            border-width: 1px;
        }

        .prods-table td{
            font-size: 0.32cm;
            text-align: center;
            padding: 2px 4px;
        }
        .prods-table td:nth-child(2){
            text-align: left;
        }
        .delivery-table-top{
            width: 100%;
            height: 16%;
            overflow: hidden;
        }
        .delivery-table-bottom{
            width: 75%;
            height: 3.2cm;
            overflow: hidden;
        }

        .delivery-table td{
            font-size: 0.3cm;
            padding: 1px 4px;
            height: 0.33cm;
            overflow: hidden;
        }
        .cut-line{
            height: 1px; clear: both; border-bottom:1px dashed grey;
            margin: 0.15cm 0;
        }
        .order-footer{
            margin-top: 0.1cm;
            position: relative;
            height: 0.6cm;
            overflow: hidden;
        }
        .order-footer .order-comment{
            position: absolute;
            left: 0;
            font-size: 0.25cm;
            max-width: 60%;
        }
        .order-footer .total-sum{
            position: absolute;
            right: 0;
            font-weight: bold;
            font-size: 0.4cm;
        }
        .user-card-place{
            width: 34mm;
            height: 9mm;
            border: 1px solid #E9E9E9;
            margin: 0 auto;
            font-weight: bold;
            text-align: left;
            font-size: 4mm;
            line-height: 4mm;
            overflow: hidden;
            padding: 2mm 1mm;
            position: relative;
        }
        .user-phone{
            letter-spacing: -0.06mm;
            margin-top: -1.6mm;
            line-height: 5mm;
            display: block;
            font-stretch: normal;
        }
        .user-pass{
            margin-top: 3.4mm;
            letter-spacing: 2.1px;
            display: block;
        }
        .ucp-wrapper{
            width: 25%;
            float: right;
        }
        @media all {
            .page-break	{ display: none; }
        }
        @media print {
            .page-break	{ display: block; page-break-before: always; }
            .page-break:last-child {
                page-break-after: none;
            }
        }
    </style>
</head>
<body style="background:white;">
<input type="submit" name="submit" value="<?=$multi['TXT_PRINT_ORDER']?>" onclick="this.style.display='none'; window.print();" style="display: block; margin:0 auto 10px;" />
<div class="wrapper">

    <?=$prods_table_top?>

    <? if ($waybillType == 'service'): ?>


    <br/>
    <?=$delivery_table_top?>
    <div style="height:9mm"></div>

    <?php if ($split_page): ?>
</div>
<div class="page-break"></div>
<div class="wrapper">
    <? else: ?>
        <div class="cut-line"></div>
    <? endif; ?>

    <div class="ucp-wrapper">
        <div class="user-card-place">
            <span class="user-phone"><?=$phone_mob?></span>
            <span class="user-pass"><?=$user_pass?></span>
        </div>

    </div>
    <?=$delivery_table_bottom?>
    <div class="cut-line" style="margin-bottom: 0.5cm; margin-top: 0.13cm"></div>
    <?=$prods_table_bottom?>

    <? endif; ?>
</div>

</body>
</html>
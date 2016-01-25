<table cellspacing="0" cellpadding="6" border="0" width="100%" class="full-cart-tbl">
<tr class="main-tr">
    <th colspan="2" class="thleft" style="text-align: center; border-left: 1px solid #e6e6e6; border-bottom: 1px solid #e6e6e6; border-top:1px solid #e6e6e6"><span><?=$multi['TXT_NAME_PROP'];?></span></th>
    <th style="border-top: 1px solid #e6e6e6; border-bottom: 1px solid #e6e6e6;"><span><?=$multi['FLD_QUANTITY'];?></span></th>
    <th style="border-top: 1px solid #e6e6e6; border-bottom: 1px solid #e6e6e6;"><span><?=$multi['TXT_PRICE'];?></span></th>
    <th class="thright" style="border-top: 1px solid #e6e6e6; border-bottom: 1px solid #e6e6e6; border-right: 1px solid #e6e6e6;"><span><?=$multi['FLD_SUMA']?></span></th>
</tr>
<?
for($i=0;$i<$rows;$i++):
    $row = $arr[$i];
    //var_dump($row);
    $name = $row['name'];
    ?>
    <tr id="propText<?=$row["id"];?>">
        <td>
            <div class="img-order-prop">
            <a href="<?=$row['link']?>" title="<?=$name?>" >
                <img src="<?=$row['src']?>" alt="<?=$row['img_alt']?>" title="<?=$row['img_title']?>">
            </a>
            </div>
        </td>
        <td>
            <div class="order-prop-name">
                <a href="<?=$row['link']?>" title="<?=$name?>"><span><?=$name?></span></a>
                <span class="short-descr"><?=$row['short']?></span>
                <div class="dots"></div>
            </div>
        </td>
        <td class="order-price"><?=$row['quantity']?></td>
        <td class="order-price"><?=$row['group_price']?></td>
        <td class="order-price"><?=$row['summa']?></td>
    </tr>
<? endfor; ?>
</table>
<?
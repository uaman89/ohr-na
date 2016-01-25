<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bogdan
 * Date: 09.08.13
 * Time: 13:16
 * To change this template use File | Settings | File Templates.
 */
for($i=0;$i<$count_param;$i++){
    $cod = $keys_param[$i];
    $name_param = $arr_param[$cod]['name_param'];
    $row_param_prop = $arr_param[$cod]['val'];
?><tr>
    <td><?=$name_param?></td>
    <?
    for($j=0;$j<$count;$j++){
        $cod_prop = $arr_move[$j];
        ?><td class="compare-prop-for-id<?=$cod_prop?>"><?
        if(isset($row_param_prop[$cod_prop])){
            echo $row_param_prop[$cod_prop];
        }
        ?></td><?
    }?>
<tr><?
}
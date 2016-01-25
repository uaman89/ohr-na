<?
$keys = array_keys($arr);
for ($i = 0; $i < count($keys); $i++) :
    if ($keys[$i] == _CURR_ID):
        ?>
    <span class="valutaSelected paddingLeft8px"><?=$arr[$keys[$i]]?></span>
    <?
    endif;
endfor;
?>
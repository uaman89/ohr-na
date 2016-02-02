<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bogdan
 * Date: 21.08.13
 * Time: 18:02
 * To change this template use File | Settings | File Templates.
 */
$onclick = " onclick=\"return gelPropConetnt('".$Catalog->catLink."','".$paramLink."');\"";
?><div class="param-sel-value">
    <div class="param-sel-value-checkbox">
        <div class="param-sel-value-checkbox" title="Сбросить"<?=$onclick?>></div>
    </div>
    <div class="param-sel-value-label">
        <a href="<?=$Catalog->catLink . $paramLink?>"<?=$onclick?>>
            <label><?=$str_value?></label>
        </a>
    </div>
</div><?

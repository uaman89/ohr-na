<?php
?><div class="new-prod-inform"><?
?><div class="new-prod-inform-title body-left-name"><?
echo $name_block;
?></div><?
?><div class="filter-block">
    <form name="find_by_param" method="post" action="<?=$catLink?>">
<!--        <div class="filter-select-block">--><?//=$strSel;?><!--</div>-->
        <div class="filter-block-price"><?=$strPrice;?></div>

        <div class="filter-block-all-param"><?=$str;?></div>
    </form>
    </div><?
?></div><?
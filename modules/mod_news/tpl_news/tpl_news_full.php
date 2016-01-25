<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bogdan
 * Date: 19.06.13
 * Time: 9:31
 * To change this template use File | Settings | File Templates.
 */

if(!empty($date)){?>
    <div class="news-date"><?=$date?></div><?
}

//if(!empty($images)){
//    ?><!--<div class="news-full-img">--><?//=$images?><!--</div>--><?//
//}

if(!empty($full)){
    ?><div class="news-full-text"><?=$full?></div><?
}
?>
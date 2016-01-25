<?//Call from function ShowSlider(){?>
<link rel="stylesheet" type="text/css" href="/include/js/coin-slider/coin-slider-styles.css" />
<script type="text/javascript" src="/include/js/coin-slider/coin-slider.js"></script>
<?/*<script type="text/javascript" src="/include/js/coin-slider/coin-slider.min.js"></script>*/?>
<script type="text/javascript">
$(document).ready(function() {
    var mySlider = (document.getElementById('sliderContainer'));
    if(mySlider!=null) {
        $('#coin-slider').coinslider({
            width: 723,
            height: 392,
            spw: 10,
            sph: 3,
            delay: 4000,
            sDelay:10,
            opacity :1.0,
            navigation: true,
            links : true
        });
    document.getElementById('sliderContainer').style.display = "block";
    }
});
</script>

<div class="slider">
    <div id="sliderContainer" style="display: none;">
        <div id="coin-slider" >
            <?
            for($i=0;$i<$count;$i++){
                $img = $array[$i]['rel_path_img'];
                if(empty($img))
                    continue;
                $id = $array[$i]['id'];
                $name = stripslashes($array[$i]['name']);
                $descr = strip_tags(stripslashes($array[$i]['descr']),'<a><br><strong><p>');
                $href = stripslashes($array[$i]['href']);
                $h1 = $name;
                if(!empty($href)) {
                    ?><a href="<?=$href?>" title="<?=htmlspecialchars($name);?>"><?
                }
                ?>
                <img src="<?=$img;?>" alt="<?=htmlspecialchars($name);?>" title="<?=htmlspecialchars($name);?>" />
                <span>
                    <span class="titleSlider"><?=$name?></span>
                    <span class="descr"><?=$descr;?>
                        <?/*<span class="priceSlider"><?=$h1?></span>*/?>
                    </span>
                </span>
                <?
                if(!empty($href)) {
                    ?></a><?
                }
            }
            ?>
        </div>
    </div>
</div>
<script type="text/javascript" language="javascript" src="/include/js/carouFredSel/jquery.carouFredSel-6.2.1-packed.js"></script>
<script>
    $(document).ready(function(){
        $("#foo3").carouFredSel({
            items 		: 1,
            auto: true,
            prev : "#foo1_prev",
            next : "#foo1_next",
            pagination : {
                    container   : "#foo3_pag",
                    keys        : true,
                    duration    : 1000
            },
            scroll: {
                pauseOnHover: true
            }


    });


    });


</script>

<div class="html_carousel">
    <div id="foo3">
        <?
        for($i=0;$i<$count;$i++){
            $img = $array[$i]['rel_path_img'];
            if(empty($img))
                continue;
            $name = stripslashes($array[$i]['name']);
            $descr = strip_tags(stripslashes($array[$i]['descr']),'<a>');
            $href = stripslashes($array[$i]['href']);

            ?>
            <div class="slide">
                <?php
                if(!empty($href)){
                ?><a href="<?=$href;?>" title="<?=htmlspecialchars($name);?>"><?
                    }
                    ?>
                    <img src="<?=$img;?>" alt="<?=htmlspecialchars($name);?>" width="1000" height="300" />
                    <div>
                        <h4><?=htmlspecialchars($name);?></h4>
                        <p><?=$descr;?></p>
                    </div>
                    <?php

                    if(!empty($href)){
                    ?></a><?
            }
            ?>
            </div>


        <?

        }
        ?>


    </div>
    <div class="clearfix"></div>
    <a class="prev" id="foo1_prev" href="#"><span>prev</span></a>
    <a class="next" id="foo1_next" href="#"><span>next</span></a>
    <div class="pagination" id="foo3_pag"></div>
    <div class="shadow-slide"></div>
</div>

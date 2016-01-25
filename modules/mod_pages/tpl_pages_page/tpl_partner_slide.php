<?if($count):?>

    <script>
        $(document).ready(function(){
            $("#partnerB").carouFredSel({

            });
        });

    </script>
    <div class="partner-montazh">
    <div class="props-tab" style="width: 715px">
        <div class="h1main"><div class="line2"></div><span>Наши бренды</span></div>

        <div class="partner_carousel">
            <div id="partnerB">
                <?for($i=0;$i<$count;$i++):?>

                    <?php echo "<a class='logoslider' style='margin-right:10px; width:165px; height: 117px; display:inline-block; text-align:center'  >" ?><div><img src="<?=$data[$i]['img']?>"  alt="<?=$data[$i]['name']?>"  /></div><? echo "</a>";?>

                <?endfor;?>

            </div>
            <div class="clearfix"></div>

        </div>


    </div>
        <div class="shadow"></div>
    </div>


<?endif;?>
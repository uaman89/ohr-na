<script>

    $(document).ready(function(){
        $("#hit").carouFredSel({
            items : 4,
            auto: false,
            prev : "#hit-prev",
            next : "#hit-next"


        });
        $("#new").carouFredSel({
            items : 4,
            auto: false,
            prev : "#new-prev",
            next : "#new-next"


        });
        $("#share").carouFredSel({
            items : 4,
            auto: false,
            prev : "#share-prev",
            next : "#share-next"


        });
    });


</script>
<div class="main-tab">
    <div class="top-tabs">
        <div class="btn-categ active-t">
            <div class="wrapper-tab">
                <div class="tab-item active-tab" data-item="0">
                    <?= $multi['_TXT_HIT_'] ?>
                </div>
            </div>
        </div>
        <div class="btn-categ">
            <div class="wrapper-tab">
                <div class="tab-item" data-item="1"><?= $multi['_TXT_NEW_'] ?></div>
            </div>
        </div>
        <div class="btn-categ">
            <div class="wrapper-tab">
                <div class="tab-item" data-item="2"><?= $multi['_TXT_SHARE_'] ?></div>
            </div>
        </div>

    </div>

    <div class="props-tab">

        <div class="categ-prop active-prod" id="p0">
            <div id="hit">
            <?=$hit;?>
            </div>
            <div class="prop-prev" id="hit-prev"></div>
            <div class="prop-next" id="hit-next"></div>
        </div>

        <div class="categ-prop new" id="p1">
            <div id="new">
                <?=$new;?>
            </div>
            <div class="prop-prev" id="new-prev"></div>
            <div class="prop-next" id="new-next"></div>
        </div>

        <div class="categ-prop share" id="p2">
            <div id="share">
            <?=$share;?>
            </div>
            <div class="prop-prev" id="share-prev"></div>
            <div class="prop-next" id="share-next"></div>
        </div>
    </div>

    <div class="shadow-slide"></div>
</div>

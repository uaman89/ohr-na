<div class="comments-popup-form-body gradient">
    <div class='popup-title'>
        <?=$popup_title?>
    </div>
    <?
        if ( $is_new_comment ) echo FormH::open('#', array('id' => 'commentsMakeFormId'));
        else{
            $form_id = 'commentsForm'.$level;
            echo FormH::open('#', array('id' => $form_id));
        }

    ?>

    <? if (!empty($level) AND !empty($level)):
        echo FormH::hidden('level', $level);
    ?>
        <?/*
        <div class="comments-response-comment-box">
            <div class="comments-respons-comment-inner">
                <span class="comments-name"><?=$commentsData['show_name']?></span><br/>
                <?=$commentsData['text']?>
            </div>
        </div> */?>
    <? endif; ?>

    <ul class='comments-inner-box'>
        <? if (empty($user_id)){}

        ?>
        <li>
            <? echo FormH::input('name', $name, array('placeholder' => 'Имя', 'id' => 'commentsNameId')) ?>
            <?
            if ( $is_new_comment ) echo FormH::input('email', $email, array( 'placeholder' => 'Email (не обязательно)'))
            ?>
            <? echo FormH::input('vote', '', array( 'type'=>'hidden', 'id'=>'vote')) ?>
            <br/>
            <? echo FormH::textarea('text', '', array('class' => 'comments-textarea', 'id' => 'commentsTextId')) ?>
        </li>
        <li class='buttons-box'>
            <? if ($is_new_comment): ?>
            <div class="rating">
                <span>Оцените товар:</span>
                <ul class="stars voting">
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                </ul>
            </div>
            <?
            endif;
            if ( $is_new_comment ) echo FormH::button('submit', 'Отправить', array('class' => 'comments-submit-btn', 'id' => 'commentsSubmitBtnId', 'type' => 'button'));
            else echo FormH::button('submit', 'Отправить', array('onclick' => 'submit_response("'.$form_id.'")', 'class' => 'comments-submit-btn', 'type' => 'button'));
            ?>
        </li>
    </ul>
    <? echo FormH::close(); ?>
</div>

<script type="text/javascript ">
    $("#commentsMakeFormId").validationEngine();
    $("#commentsCancelBtnId").click(function () {
        cmspopup.close();
    });

    function submit_response( form_id ){
        //console.log('form_id: ' + form_id); return;
        $('#'+form_id).ajaxSubmit({
            url:'/comments/add_comment/?module=<?=$module?>&id_item=<?=$id_item?>&page=<?=$page?>',
            success:function (responseText) {
                if (ajaxResponse(responseText))
                    cmspopup.close();
            }
        });
    }

    $("#commentsSubmitBtnId").click(function () {
        submit_response('commentsMakeFormId');
    });

    $('.voting li').click(function(){
        $('.voting li').removeClass('on');
        $(this).addClass( 'on' )
        $('#vote').val( $(this).index() + 1 );
    });
</script>
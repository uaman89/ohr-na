<div class="comments-popup-form-body gradient">
    <div class='popup-title'>
        Редактирование комментария
    </div>
    <? echo FormH::open('#', array('id' => 'commentsMakeFormId'));
       echo FormH::hidden('submit','submit');
    ?>

    <ul class='comments-inner-box'>
        <li>
            <? echo FormH::label('commentsTextId', 'Комментарий :', array('class' => 'comments-label')) ?><br/>
            <? echo FormH::textarea('text', $commentArr['text'], array('class' => 'comments-textarea', 'id' => 'commentsTextId')) ?>
        </li>
        <li class='buttons-box'>
            <?
            echo FormH::button('submit', 'Отправить', array('class' => 'comments-submit-btn btn gradient', 'id' => 'commentsSubmitBtnId', 'type' => 'button'));
            echo FormH::button('cancel', 'Отмена', array('class' => 'comments-cancel-btn btn gradient', 'id' => 'commentsCancelBtnId', 'type' => 'button'));
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
    $("#commentsSubmitBtnId").click(function () {
        $("#commentsMakeFormId").ajaxSubmit({
            url:'/myaccount/comments/edit/<?=$commentArr['id']?>/?page=<?=$page?> ',
            success:function (responseText) {
                if (ajaxResponse(responseText))
                    cmspopup.close();
            }
        });
    });
</script>
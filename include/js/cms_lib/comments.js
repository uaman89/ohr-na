$(document).ready(function () {
    $("#showCommentsForm, #showCommentsFormBottom").click(function () {
        $obg = this;
        cmspopup.show_modal("<div class='popup-loader'></div>",$obg);
        $.post('/comments/get_form/', 'module=' + module + '&id_item=' + id_item, function (responseText) {
            //cmspopup.show_modal(ajaxResponse(responseText), $obg);
            $('#commentsForm').html( ajaxResponse(responseText) + '<hr class="dotted-line"/>' );
            cmspopup.close();
        });
    });
});

function initResponseLink(){
    $(".comments-response-link").click(function (event) {
        event.preventDefault();
        $obg = this;
        $href = $(this).attr('href');
        cmspopup.show_modal("<div class='popup-loader'></div>",$obg);
        _this = this;
        $.get($href, 'module=' + module + '&id_item=' + id_item, function (responseText) {
            //cmspopup.show_modal(ajaxResponse(responseText), $obg);
            $(_this).parent().siblings('#answerForm').html( '<hr class="dotted-line"/>' + ajaxResponse(responseText) );
            cmspopup.close();
        });
    });
}

function initPaginationLink(){
    $("#paginationBoxId a").click(function (event) {
        event.preventDefault();
        $obg = this;
        $href = $(this).attr('href');
        cmspopup.show_modal("<div class='popup-loader'></div>",$obg);
        $.post($href, 'module=' + module + '&id_item=' + id_item, function (responseText) {
            ajaxResponse(responseText);
            cmspopup.close();
        });
    });
}


function initEditLink(){
    $(".edit-comment").click(function (event) {
        event.preventDefault();
        $obg = this;
        $href = $(this).attr('href');
        cmspopup.show_modal("<div class='popup-loader'></div>",$obg);
        $.post($href, '', function (responseText) {
            cmspopup.show_modal(ajaxResponse(responseText), $obg);
        });
    });
}
function initDelLink(){
    $(".del-comment").click(function (event) {
        event.preventDefault();
        $obg = this;
        $href = $(this).attr('href');
        cmspopup.show_modal("<div class='popup-loader'></div>",$obg);
        $.post($href, '', function (responseText) {
            ajaxResponse(responseText);
            cmspopup.close();
        });
    });
}


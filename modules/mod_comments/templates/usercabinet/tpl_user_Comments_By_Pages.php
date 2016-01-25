<?
if(!is_array($arr) OR count($arr)==0) return false;
?>
<? foreach ($arr as $row) :
    if(!isset($row['id'])) continue;
    $title=$arr['modules'][$row['id_module']][$row['id_item']]['name'];
    $href=$arr['modules'][$row['id_module']][$row['id_item']]['link'];
    ?>

<div id="blogRecordSingleItem<?=$row['id']?>" class='blog-single-item'>
    <?if ($row['edit']): ?>
        <div class="edit-blok">
            <div id="userLoaderRecord<?=$row['id']?>AjaxId" class='ajax-loader-record'></div>
                <a class="edit-comment" href="/myaccount/comments/edit/<?=$row['id']?>/?page=<?=$page?>" title="редактировать">редактировать</a>
            <a class="del-comment" href="/myaccount/comments/del/<?=$row['id']?>/?page=<?=$page?>" title="удалить">удалить</a>
        </div>
    <? endif;?>
    <div class="blog-single-content">
        <div>
            <span class="date-cabinet-comments"><?=$row['dt']?></span>
        </div>
        <div class="comments-cabinet-text"><?=$row['text']?></div>
        <div class="comments-user-cabinet-footer"><?=$title?> <a href="<?=$href?>" title="<?=$title?>">пост</a> <img src="/images/design/ico/post.jpg" alt=""/></div>
    </div>
</div>

<? endforeach; ?>
<div class="pagination-box">
    <?=$pagination?>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        initEditLink();
        initDelLink();
    });
</script>
<script type="text/javascript">
    VK.init({
        apiId:2385265,
        onlyWidgets:true
    });
</script>
<? /*
        В тело страницы необходимо добавить элемент DIV,
        в котором будут отображаться комментарии,
        задать ему уникальный id и добавить в него код инициализации виджета
        */
?>
<!-- Put this div tag to the place, where the Comments block will be -->
<div id="vk_comments"></div>
<script type="text/javascript">
    VK.Widgets.Comments("vk_comments", {limit:5, width:"686", attach:false});
    //VK.Widgets.Comments("vk_comments");
</script>
{literal}
<link rel="stylesheet" href="np/js/jquery-ui.css"/>
<link media="screen" rel="stylesheet" href="np/js/colorbox.css"/>
<link rel="stylesheet" href="np/js/button.css"/>

<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script type="text/javascript" src="np/js/jquery.colorbox-min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	$('input[name=address]').attr("id", 'address_np');
	var id = "#address_np";
	var w = $(id).width();
	var button = "<input type=button class='button_np' style='display:none' title='Подобрать на карте' value='' href='np/index.php' />";
	$(button).insertAfter(id);
	if ($("#deliveries_" + did).prop("checked")) {
		np_yes(w, id);
	}
	///////////////////////

	function np_yes(w, id) {
		$(id).addClass("npochta");
		$(id).attr("placeholder", 'Введите город и улицу');
        
        //// стили поля адресс
		$(id).css({
			"vertical-align": "middle",
			"float": "left",
            "padding-right": "0px",
            "padding-left": "0px",
			"margin-right": "2px",
			"widht": (w - 25)
		});
		$(".npochta").width(w - 25);
		$(".button_np").show();
	}
	//////////////////

	function np_no(w, id) {
		$(id).attr("placeholder", '');
		$(id).css({
			"vertical-align": "middle",
			"margin-right": "0px",
			"padding": "3px"
		});
		$(".npochta").width(w);
		$(id).removeClass("npochta");
		$(".button_np").hide();
	}
	$('input[name=delivery_id]').change(function() {
		if ($("#deliveries_" + did).prop("checked")) {
			np_yes(w, id);
		} else {
			np_no(w, id);
		}
	});
	////////////////////////////////////////////////////////////////
	$(".button_np").colorbox({
		width: "1000px",
		height: "650px",
		iframe: true,
		close: "x",
		scrolling: false,
		fixed: true,
		title: "<a target='_blank' href='http://fixend.ru/np'>Модуль Новая почта 2014</a>"
	});

/////////////////// если   lang = 1 в подборе работает только русский язык, если lang = 2 то русский и украинский
    window.lang = 2;
    
$.getScript('np/ajax.js');    
});

</script>
{/literal}
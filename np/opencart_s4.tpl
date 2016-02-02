<link rel="stylesheet" href="np/js/jquery-ui.css"/>
<link media="screen" rel="stylesheet" href="np/js/colorbox.css"/>
<link rel="stylesheet" href="np/js/button.css"/>

<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script type="text/javascript" src="np/js/jquery.colorbox-min.js"></script>

<script type="text/javascript">

$(document).ready(function() {
	////// id поля ввода адреса
	var id = "#payment_address_address_1";
	///// берем его ширину для того чтобы уменьшить и вместить кнопку с картой
	var w = $(id).width();
	//// вставляем кнопку после поля ввода адреса, она пока невидима    
	var button = "<input type=button class='button_np' style='display:none' title='Подобрать на карте' value='' href='np/index.php' />";
	$(button).insertAfter(id);
	/// проверяем есть ли у нас лишние поля. которые не нужны для модуля новая почта
	/// а так очищаем их сгенеренных названий, это ниже
	if ($('#checkout_customer_main_city').length > 0) $('#checkout_customer_main_city').val("");
	if ($('#checkout_customer_main_postcode').length > 0) $('#checkout_customer_main_postcode').val("");
	$(id).val("");
	///// проверяем выбран ли модуль новая почта
	if ($("#novaposhta\\.novaposhta").prop("checked")) {
		np_yes(w, id);
	}
	/////////////////////// если модуль выбран

	function np_yes(w, id) {
		$(id).addClass("npochta");
		$(id).attr("placeholder", 'Для подбора введите город или улицу');
		$(id).css({
			"vertical-align": "middle",
			"margin-right": "3px",
			"padding-right": "0px",
			"padding-left": "0px"
		});
		////// уменьшаем шиирину поял адреса и всталяем кнопку
		$(".npochta").width(w - 15);
		$(".button_np").show();
		//// прячем не нужные поля для модуля Новая почта
		$(id).closest("table").find('tr:eq(5)').hide();
		$(id).closest("table").find('tr:eq(2)').hide();
		$(id).closest("table").find('tr:eq(3)').hide();
		$(id).closest("table").find('tr:eq(4)').hide();
		//// заполняем обязательные поля
		if ($('#payment_address_country_id').length > 0) $("#payment_address_country_id [value='220']").attr("selected", "selected");
		if ($('#payment_address_zone_id').length > 0) $("#payment_address_zone_id [value='3491']").attr("selected", "selected");
		if ($('#payment_address_city').length > 0) $('#payment_address_city').val("Новая почта");
		if ($('#payment_address_postcode').length > 0) $('#payment_address_postcode').val("Нова пошта");
	}
	////////////////// если модуль не выбран

	function np_no(w, id) {
		///// востанавливаем текущие значения
		$(id).attr("placeholder", '');
		$(id).css({
			"vertical-align": "middle",
			"margin-right": "0px",
			"padding": "3px"
		});
		$(".npochta").width(w);
		$(id).removeClass("npochta");
		$(".button_np").hide();
		if ($('#checkout_customer_main_city').length > 0) $('#checkout_customer_main_city').val("");
		if ($('#checkout_customer_main_postcode').length > 0) $('#checkout_customer_main_postcode').val("");
		$(id).val("");
		$(id).closest("table").find('tr').show();
	}
	$('input[name=shipping_method]').change(function() {
		if ($("#novaposhta\\.novaposhta").prop("checked")) {
			np_yes(w, id);
		} else {
			np_no(w, id);
		}
	});
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
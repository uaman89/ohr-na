tinyMCEPopup.requireLangPack();

var ImagesDialog = {
	init : function(ed) {
		tinyMCEPopup.resizeToInnerSize();
	},

	insert : function(text) {
		var ed = tinyMCEPopup.editor, dom = ed.dom;
		tinyMCEPopup.execCommand('mceInsertContent', false, text);
		//tinyMCEPopup.close();
	}
};

tinyMCEPopup.onInit.add(ImagesDialog.init, ImagesDialog);

var images_onload = function() {
	
	//ЗАГРУЗКА
	$('#loader').show();
	//Строка адреса
	$.ajax({
		type: "POST",
		url: "connector/php/",
		data: "action=showpath&type=images&path=&default=1",
		success: function(data){
			$('#addr').html(data);
		}
	});
	//Каталог папок
	$.ajax({
		type: "POST",
		url: "connector/php/",
		data: "action=showtree&default=1",
		success: function(data){
			$('#tree').html(data);
		}
	});
	//Список файлов
	$.ajax({
		type: "POST",
		url: "connector/php/",
		data: "action=showdir&pathtype=images&path=&default=1",
		success: function(data){
			$('#loader').hide();
			//$('#files').html(data);
			$('#mainFiles').html('<div id="files">'+data+'</div>');
			showFootInfo();
		}
	});
	//Session ID для Flash-загрузчика
	var SID;
	$.ajax({
		type: "POST",
		url: "connector/php/",
		data: "action=SID",
		success: function(data){
			SID = data;
		}
	});
	
	
	//Адресная строка
	$('.addrItem div,.addrItem img').live('mouseover', function(){
		$(this).parent().animate({backgroundColor:'#b1d3fa'}, 100, 'swing', function(){
			
		});
	});
	$('.addrItem div,.addrItem img').live('mouseout', function(){
		$(this).parent().animate({backgroundColor:'#e4eaf1'}, 200, 'linear', function(){
			//alert('ck');
			$(this).css({'background-color':'transparent'});
			//alert('ck');
		});
	});
	$('.addrItem div,.addrItem img').live('mousedown', function(){
		$(this).parent().css({'background-color':'#679ad3'});
	});
	$('.addrItem div,.addrItem img').live('mouseup', function(){
		$(this).parent().css({'background-color':'#b1d3fa'});
		$.ajax({
			type: "POST",
			url: "connector/php/",
			data: "action=showtree&path="+$(this).parent().attr('path')+"&type="+$(this).parent().attr('pathtype'),
			success: function(data){
				//$('#loader').hide();
				$('#tree').html(data);
			}
		});
		$.ajax({
			type: "POST",
			url: "connector/php/",
			data: "action=showpath&type="+$(this).parent().attr('pathtype')+"&path="+$(this).parent().attr('path'),
			success: function(data){
				$('#addr').html(data);
			}
		});
		$.ajax({
			type: "POST",
			url: "connector/php/",
			data: "action=showdir&pathtype="+$(this).parent().attr('pathtype')+"&path="+$(this).parent().attr('path'),
			success: function(data){
				$('#loader').hide();
				//$('#files').html(data);
				$('#mainFiles').html('<div id="files">'+data+'</div>');
				showFootInfo();
			}
		});
	});
	
	//Кнопка "В начало"
	$('#toBeginBtn').mouseover(function(){
		$(this).children(0).attr('src','img/backActive.gif');
	});
	$('#toBeginBtn').mouseout(function(){
		$(this).children(0).attr('src','img/backEnabled.gif');
	});
	
	//Меню
	$('.folderClosed,.folderOpened,.folderS,.folderImages,.folderFiles').live('mouseover',function(){
		if(!$(this).hasClass('folderAct')) {
			$(this).addClass('folderHover');
		} else {
			$(this).addClass('folderActHover');
		}
	});
	$('.folderClosed,.folderOpened,.folderS,.folderImages,.folderFiles').live('mouseout',function(){
		if(!$(this).hasClass('folderAct')) {
			$(this).removeClass('folderHover');
		} else {
			$(this).removeClass('folderActHover');
		}
	});
	
	//Флаг загрузки
	var folderLoadFlag = false;
	//Открыть указанную папку
	function openFolder(type, path, callback) {
		$.ajax({
			type: "POST",
			url: "connector/php/",
			data: "action=showpath&type="+type+"&path="+path,
			success: function(data){
				$('#addr').html(data);
			}
		});
		$.ajax({
			type: "POST",
			url: "connector/php/",
			data: "action=showdir&pathtype="+type+"&path="+path,
			success: function(data){
				$('#loader').hide();
				//$('#files').html(data);
				$('#mainFiles').html('<div id="files">'+data+'</div>');
				showFootInfo();
				callback();
			}
		});
	}
	$('.folderClosed,.folderOpened,.folderS,.folderImages,.folderFiles').live('click',function(){
		
		//Запрет на переключение
		if(folderLoadFlag) return false;
		folderLoadFlag = true;
		
		$('#loader').show();
		$('.folderAct').removeClass('folderAct');
		$(this).removeClass('folderHover');
		$(this).addClass('folderAct');
			
		openFolder($(this).attr('pathtype'), $(this).attr('path'), function(){ folderLoadFlag = false; });
	});
	$('.folderImages,.folderFiles').live('dblclick',function(){
		$(this).next().slideToggle('normal');
	});
	$('.folderOpened,.folderS').live('dblclick',function(){
		if(!$(this).next().hasClass('folderOpenSection')) return false;
		if($(this).hasClass('folderS')) {
			$(this).removeClass('folderS').addClass('folderOpened');
		} else {
			$(this).removeClass('folderOpened').addClass('folderS');
		}
		$(this).next().slideToggle('normal');
	});
	
	//ДЕЙСТВИЯ МЕНЮ
	//Открыть загрузчик файлов
	$('#menuUploadFiles').click(function(){
		var path = getCurrentPath();
		var str = '';
		if(path.type=='images') {
			str = '<span>' + tinyMCEPopup.getLang('images_dlg.images') + ':</span>';
		} else if(path.type=='files') {
			str = '<span>' + tinyMCEPopup.getLang('images_dlg.files') + ':</span>';
		}
		str += path.path;
		$('#uploadTarget').html(str);
		
		$('#normalPathVal').val(path.path);
		$('#normalPathtypeVal').val(path.type);
		
		$('#upload').show();
	});
	//Создать папку
	var canCancelFolder = true;
	$('#menuCreateFolder').click(function(){
		$(this).hide();
		$('#menuCancelFolder,#menuSaveFolder').show();
		
		$('.folderAct').after('<div id="newFolderBlock"><input type="text" name="newfolder" id="newFolder" /></div>');
		$('#newFolderBlock').slideDown('fast', function(){
			$('#newFolderBlock input').focus().blur(cancelNewFolder).keypress(function(e){
				if(e.which == 13) {
					saveNewFolder();
				} else if (e.which == 27) {
					cancelNewFolder();
				} else if ((e.which >= 97 && e.which <= 122) || (e.which >= 65 && e.which <= 90) || (e.which >= 48 && e.which <= 57) || e.which == 8 || e.which == 95 || e.which == 45 || e.keyCode == 37 || e.keyCode == 39 || e.keyCode == 16) {
					//Значит все верно: a-Z0-9-_ и управление вводом
				} else {
					return false;
				}
				
			});
		});
		
	});
	//Отменить создание папки
	function cancelNewFolder(){
		if(!canCancelFolder) {
			canCancelFolder = true;
			return false;
		}
		$('#menuCancelFolder,#menuSaveFolder').hide();
		$('#menuCreateFolder').show();
		
		$('#newFolderBlock').slideUp('fast', function(){
			$(this).remove();
		});
	}
	$('#menuCancelFolder').click(cancelNewFolder);
	
	//Подтвердить создание папки
	function saveNewFolder(){
		canCancelFolder = false;
		
		if($('#newFolderBlock input').val() == '') {
			alert(tinyMCEPopup.getLang('images_dlg.enter_a_name_for_new_folder'));
			$('#newFolderBlock input').focus();
			return false;
		}
		
		$('#loader').show();
		$('#menuCancelFolder,#menuSaveFolder').hide();
		$('#menuCreateFolder').show();
		//Запрос на создание папки + сервер должен отдать новую структуру каталогов
		var pathtype = $('.folderAct').attr('pathtype');
		var path = $('.folderAct').attr('path');
		var path_new = $('#newFolderBlock input').val();
		var path_will = path+'/'+path_new;
		$.ajax({
			type: "POST",
			url: "connector/php/",
			data: "action=newfolder&type="+ pathtype +"&path="+ path +"&name=" + path_new,
			success: function(data){
				$('#loader').hide();
				var blocks = eval('('+data+')');
				if(blocks.error != '') {
					alert(blocks.error);
					$('#newFolderBlock input').focus();
				} else {
					$('#tree').html(blocks.tree);
					$('#addr').html(blocks.addr);
					canCancelFolder = true;
					
					//Открываем созданную папку
					$.ajax({
						type: "POST",
						url: "connector/php/",
						data: "action=showdir&pathtype="+pathtype+"&path="+$('.folderAct').attr('path'),
						success: function(data){
							$('#loader').hide();
							//$('#files').html(data);
							$('#mainFiles').html('<div id="files">'+data+'</div>');
						}
					});
				}
			}
		});
	}
	$('#menuSaveFolder').click(saveNewFolder).hover(function(){ canCancelFolder = false; }, function(){ canCancelFolder = true; });
	
	//Удалить папку
	$('#menuDelFolder').click(function() {
		var path = getCurrentPath();
		if(confirm(tinyMCEPopup.getLang('images_dlg.delete_folder') + ' '+path.path+'?')) {
			$('#loader').show();
			$.ajax({
				type: "POST",
				url: "connector/php/",
				data: "action=delfolder&pathtype="+path.type+"&path="+path.path,
				success: function(data){
					var result = eval('('+data+')');
					if(typeof(result.error) != 'undefined') {
						$('#loader').hide();
						alert(result.error);
					} else {
						//$('#mainFiles').html('<div id="files">'+result.ok+'</div>');
						//showFootInfo();
						$.ajax({
							type: "POST",
							url: "connector/php/",
							data: "action=showtree&path=&type="+path.type,
							success: function(data){
								//$('#loader').hide();
								$('#tree').html(data);
							}
						});
						openFolder(path.type, '', function(){ $('#loader').hide(); });
						
					}
					
				}
			});
		}
	});
	
	//Удалить файлы
	$('#menuDelFiles').click(function() {
		var files = $('.imageBlockAct');
		
		if(files.length == 0) {
			alert(tinyMCEPopup.getLang('images_dlg.select_files_to_delete'));
		} else if(files.length == 1) {
			if(confirm(tinyMCEPopup.getLang('images_dlg.delete_file') + ' '+files.attr('fname')+'.'+files.attr('ext')+'?')) {
				$('#loader').show();
				var path = getCurrentPath();
				$.ajax({
					type: "POST",
					url: "connector/php/",
					data: "action=delfile&pathtype="+path.type+"&path="+path.path+"&md5="+files.attr('md5')+"&filename="+files.attr('filename'),
					success: function(data){
						$('#loader').hide();
						//$('#files').html(data);
						if(data != 'error') {
							$('#mainFiles').html('<div id="files">'+data+'</div>');
							showFootInfo();
						} else {
							alert(data);
						}
					}
				});
			}
		} else {
			if(confirm(tinyMCEPopup.getLang('images_dlg.files_to_delete') + ': '+files.length+'\n\n' + tinyMCEPopup.getLang('images_dlg.continue_') + '?')) {
				$('#loader').show();
				var path = getCurrentPath();
				
				//Собираем строку запроса
				var actionStr = 'action=delfile&pathtype='+path.type+'&path='+path.path;
				$.each(files, function(i, item){
					actionStr += "&md5["+i+"]="+$(this).attr('md5')+"&filename["+i+"]="+$(this).attr('filename');
				});
				
				$.ajax({
					type: "POST",
					url: "connector/php/",
					data: actionStr,
					success: function(data){
						$('#loader').hide();
						//$('#files').html(data);
						if(data != 'error') {
							$('#mainFiles').html('<div id="files">'+data+'</div>');
							showFootInfo();
						} else {
							alert(data);
						}
					}
				});
			}
		}
	});
	
	
	//Файлы
	var ctrlState = false;
	$('.imageBlock0').live('mouseover', function(){
		if(!$(this).hasClass('imageBlockAct')) {
			$(this).addClass('imageBlockHover');
		} else {
			$(this).addClass('imageBlockActHover');
		}
	});
	$('.imageBlock0').live('mouseout', function(){
		if(!$(this).hasClass('imageBlockAct')) {
			$(this).removeClass('imageBlockHover');
		} else {
			$(this).removeClass('imageBlockActHover');
		}
	});
	
	$('#insertImage').click(function(){
		$('.imageBlockAct').trigger('dblclick');
		tinyMCEPopup.close();
	});
	
	$('.imageBlock0').live('dblclick', function(){
		var e = $(this);
		
		if(e.attr('type') == 'files')
		{
			var filesize = e.attr('fsizetext');
			var text = '<a href="'+e.attr('linkto')+'" '+addAttr+' title="'+e.attr('fname')+'">';
			text += e.attr('fname');
			text += '</a> ' + ' ('+filesize+') ';
		}
		else
		{
			if(e.attr('fmiddle')) {
				var addAttr = (e.attr('fclass')!=''?'class="'+e.attr('fclass')+'"':'')+' '+(e.attr('frel')!=''?'rel="'+e.attr('frel')+'"':'');
				var text = '<a href="'+e.attr('linkto')+'" '+addAttr+' title="'+e.attr('fname')+'">';
				text += '<img src="'+e.attr('fmiddle')+'" width="'+e.attr('fmiddlewidth')+'" height="'+e.attr('fmiddleheight')+'" alt="'+e.attr('fname')+'" />';
				text += '</a> ';
			} else {
				var text = '<img src="'+e.attr('linkto')+'" width="'+e.attr('fwidth')+'" height="'+e.attr('fheight')+'" alt="'+e.attr('fname')+'" /> ';
			}
		}
		
		ImagesDialog.insert(text);
		
		if($('.imageBlockAct').length == 1) {
			tinyMCEPopup.close();
		}
	});
	$('.imageBlock0').live('click', function(){
		if(ctrlState) {
			if($(this).hasClass('imageBlockActHover') || $(this).hasClass('imageBlockAct')) {
				$(this).removeClass('imageBlockAct');
				$(this).removeClass('imageBlockActHover');
			} else {
				$(this).removeClass('imageBlockHover');
				$(this).addClass('imageBlockAct');
			}
		} else {
			$('.imageBlockAct').removeClass('imageBlockAct');
			$(this).removeClass('imageBlockHover');
			$(this).addClass('imageBlockAct');
		}
		
		showFootInfo();
	});
	
	function selectAllFiles() {
		$('.imageBlock0').addClass('imageBlockAct');
		showFootInfo();
	}
	
	$(this).keydown(function(event){
		if(ctrlState && event.keyCode==65) selectAllFiles();
		if(event.keyCode==17) ctrlState = true;
	});
	$(this).keyup(function(event){
		if(event.keyCode==17) ctrlState = false;
	});
	$(this).blur(function(event){
		ctrlState = false;
	});
	
	
	
	//НИЖНЯЯ ПАНЕЛЬ
	//Показать текущую информацию
	function showFootInfo() {
		$('#fileNameEdit').show();
		$('#fileNameSave').hide();
		var file = $('.imageBlockAct');
		if(file.length > 1) {
			$('#footTableName, #footDateLabel, #footLinkLabel, #footDimLabel, #footDate, #footLink, #footDim').css('visibility','hidden');
			$('#footExt').text(tinyMCEPopup.getLang('images_dlg.files_selected') + ': '+fil
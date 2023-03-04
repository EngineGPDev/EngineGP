$('#send').ajaxForm({
	dataType: 'json',
	success: function(data)
	{
		$.each(data, function(i, val)
		{
			if(i == 'e')
				bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
					[{
						"label" : "OK",
						"class" : "btn-small btn-primary",
					}]
				);

			if(i == 's')
			{
				chat_sound('send');
				$('#text').val('');
				$('#login').val('')
				document.getElementById("text").removeAttribute("style");
				$('#img-input').html('');
				$('#uploaded-files').html('');
				$('#uploaded-files').css('display', 'none');
				$('#frm').html($('#frm').html());;
				chat_dialog(false);
			}
		});

		loading(0);

		return false;
	}
});

function chat_readers(go)
{
	$.get(home+'chat/section/read/id/1',
	function(readers)
	{	
		$('#chat_readers').html(readers);
		$('#chat_readers_modal').html(readers);

		if(!go)
			setTimeout(function() {chat_readers(false)}, 3000);
	});
}

function chat_writers(now)
{
	write = '';

	if(now)
		write = '/write/1';

	$.get(home+'chat/section/write/id/1'+write,
	function(writers)
	{	
		$('#chat_writers').html(writers);
		
		if(!now)
			setTimeout(function() {chat_writers(false)}, 3000);
	});
}

function chat_msg_send()
{
	if($('#text').val() == '')
	{
		chat_sound('err');
		return false;
	}

	loading(1);

	return true;
}

function chat_msg_edit(id, type)
{
	if(type == 'edit')
	{
		$.get(home+'chat/section/edit/id/'+id, function(data){
			bootbox.dialog('<h3 class="green">Выбранное сообщение</h3><div id="msg_edit_id" class="informer blue" style="height: 100px;overflow: scroll;">'+data+'</div><div style="padding: 10px;"><div onclick="bbcode(\'[spoiler]\', \'[/spoiler]\', \'edit\')" class="btn btn-short btn-gray right">Спойлер</div><div onclick="bbcode(\'[code]\', \'[/code]\', \'edit\')" class="btn btn-short btn-gray right">Подсветка</div><div onclick="bbcode(\'[quote]\', \'[/quote]\', \'edit\')" class="btn btn-short btn-gray right">Цитата</div><div onclick="bbcode(\'[url=http://\', \']Название[/url]\', \'edit\')" class="btn btn-short btn-gray right">Ссылка</div></div><div class="space"><div class="input_pad"><textarea id="edit" rows="4" type="text" placeholder="Введите текст" style="resize: auth;">'+data+'</textarea></div>',
				[{
					"label" : "Продолжить",
					callback : function(){chat_msg_edit(id, 'edit_go')},
				},{
					"label" : "Закрыть",
				}]
			);
		});
	}else{
		$.ajax({
			type: 'POST',
			url: home+'chat/section/edit/go',
			data: 'edit='+$('#edit').val()+'&id='+id,
			dataType: 'json',
			success: function(data)
			{
				$.each(data, function(i, val)
				{
					if(i == 'e')
						bootbox.dialog('<h3 class="red">Внимание!</h3>'+val,
							[{
								"label" : "OK",
							}]
						);

					if(i == 's')
					{
						chat_dialog(false);
						loading(0)
					}

				});
			}
		});
	}
}

function chat_msg_del(id, user)
{
	bootbox.dialog('<h3 class="red">Выберите операцию</h3>',
		[{
			"label" : "Удалить выбранное сообщение",
			"class" : "btn-small btn-primary",
			callback: function(){
				$('#msg_'+id).css('display', 'none');
				chat_msg_del_go(id, '?id=1')
			}
		},{
			"label" : "Удалить все отправленное сообщение",
			"class" : "btn-small btn-primary",
			callback: function(){
				$('.user_'+user).css('display', 'none');
				chat_msg_del_go(user, '?user=1')
			}
		},{
			"label" : "Отмена",
			"class" : "btn-small btn-primary"
		}]
	);

	return false;
}

function chat_msg_del_go(id, type)
{

	$.getJSON(home+'chat/section/delete/id/'+id+'/go/'+type, function(data)
	{
		$.each(data, function(i, val)
		{
			if(i == 'e')
				bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
					[{
						"label" : "OK",
						"class" : "btn-small btn-primary",
					}]
				);

			if(i == 's')
				chat_dialog(false);
		});
	});

	return false;
}

function chat_dialog(go)
{
	var dialog_form = document.getElementById('dialog');

	$.get(home+'chat/section/dialog', function(data){
		$('#dialog').html(data)

		$('.spoiler').click(function(){
			$(this).parent().children('div.spoiler_main').toggle(0);
		});

		$('pre code').each(function(i, block){
			hljs.highlightBlock(block);
		});
	});
	notice_load(true);

	if(chat_scroll_ativate)
		setTimeout(function() {dialog_form.scrollTop = dialog_form.scrollHeight;}, 300);

	if(chat_notice_ativate)
		chat_dialog_info();

	if(go)
	{
		spoilers = $('.spoiler_main');
		update = true;
		for(var i = 0; i < spoilers.length; i++)
		{
			if(spoilers[i].style.display == 'block')
			{
				setTimeout(function() {chat_dialog(true)}, 4000);

				return false;
			}
		}
		setTimeout(function() {chat_dialog(true)}, 4000);
	}
}

function notice_load(go)
{
	$.get(home+'chat/section/notice/check', function(data)
    {
        if(data == 0)
            document.getElementById('notice_chat').className = 'notice_chat__check';
        else
            document.getElementById('notice_chat').className = '';

        if(go)
        	notice_load(go);
    });
}

function chat_dialog_info(go)
{
	$.get(home+'chat/section/dialog/go', function(data){
		if(data != chat_dialog_id && data != '')
		{
			chat_dialog_id = data;
			chat_sound('notice')
		}
	});
}

function reply_chat(login)
{
	$('#login').val(login);
	$('#text').val(login+', '+$('#text').val());

	return false;
}

function chat_scroll_change()
{
	if(chat_scroll_ativate)
	{
		chat_scroll_ativate = false;
		$('#chat_scroll').html('включить скроллинг');
	}else{
		chat_scroll_ativate = true;
		$('#chat_scroll').html('отключить скроллинг');
	}
}

function chat_notice_change()
{
	if(chat_notice_ativate)
	{
		chat_notice_ativate = false;
		$('#chat_notice').html('включить уведомления');
	}else{
		chat_notice_ativate = true;
		$('#chat_notice').html('отключить уведомления');
	}
}

function chat_sound_change()
{
	if(chat_sound_ativate)
	{
		chat_sound_ativate = false;
		$('#chat_sound').html('включить звуки');
	}else{
		chat_sound_ativate = true;
		$('#chat_sound').html('отключить звуки');
	}
}

function chat_emoji(emoji)
{
	form = document.getElementById('text');
	begin = form.value.substr(0, form.selectionStart);
	end = form.value.substr(form.selectionEnd);
	var text = form.firstChild;
	form.value = begin+'[emoji_'+emoji+']'+end;
	selPos = '[emoji_'+emoji+']'.length+begin.length;
	form.setSelectionRange(begin.length, selPos);

	return false;
}

function chat_sound(track)
{
	if(!chat_sound_ativate)
		return false;

	var audio = new Audio();
	audio.preload = 'auto';
	audio.src = '/template/sections/chat/sound/chat_'+track+'.wav';
	audio.play();
}

function clearFileUpload(id)
{
	fileField = document.getElementById(id);
	parentNod = fileField.parentNode;
	tmpForm = document.createElement("form");
	parentNod.replaceChild(tmpForm,fileField);
	tmpForm.appendChild(fileField);
	tmpForm.reset();
	parentNod.replaceChild(fileField,tmpForm);
}

function bbcode(bbbegin, bbend, eID)
{
	form = document.getElementById(eID);
	begin = form.value.substr(0, form.selectionStart);
	end = form.value.substr(form.selectionEnd);
	sel = form.value.substr(form.selectionStart, form.selectionEnd-form.selectionStart);
	var text = form.firstChild;
	form.value = begin+bbbegin+sel+bbend+end;
	selPos = bbbegin.length+begin.length+sel.length+bbend.length;
	form.setSelectionRange(begin.length, selPos);

	return false;
}

jQuery.event.props.push('dataTransfer');

var maxFiles = 8;
var dataArray = [];

$('#uploaded-files').hide();

$('#drop-files').on('drop', function(e)
{
	var files = e.dataTransfer.files;

	if(files.length <= maxFiles)
	{
		for(i = 0; i < files.length; i++)
		{
			if(files[i].size < (1024*1024*1))
				loadInView(files[i]);
			else
				bootbox.dialog('<h3 class="green">Внимание!</h3>Слишком большой файл. Максимально 1024Kb.',
					[{
						"label" : "Хорошо"
					}]
				);
		}
	}else{
		bootbox.dialog('<h3 class="green">Внимание!</h3>Нельзя загружать больше '+maxFiles+' изображений.',
			[{
				"label" : "Хорошо"
			}]
		);
		files.length = 0;
	}

	return false;
});

$('#img').on('change', function()
{
   	var files = $(this)[0].files;

	if(files.length <= maxFiles)
	{
		for(i = 0; i < files.length; i++)
		{
			if(files[i].size < (1024*1024*1))
				loadInView(files[i]);
			else
				bootbox.dialog('<h3 class="green">Внимание!</h3>Слишком большой файл. Максимально 1024Kb.',
					[{
						"label" : "Хорошо"
					}]
				);
		}
	}else{
		// swal('Внимание!', 'Нельзя загружать больше '+maxFiles+' изображений.', 'warning');
		bootbox.dialog('<h3 class="green">Внимание!</h3>Нельзя загружать больше '+maxFiles+' изображений.',
			[{
				"label" : "Хорошо"
			}]
		);
		files.length = 0;
	}

	clearFileUpload('img');
});

var upload = 0;
var files = 0;

function loadInView(file)
{
	if(files < 0)
		files = 0;

	$('#uploaded-holder').css('display', 'inline-block');

	if(!file.type.match('image.*'))
	{
		$('#drop-files p').html('Файл не является изображением.');

		return false;
	}

	files = files+1;

	if(files <= maxFiles)
		$('#upload-button').css({'display' : 'block'});
	else{
		bootbox.dialog('<h3 class="green">Внимание!</h3>Нельзя загружать больше '+maxFiles+' изображений.',
			[{
				"label" : "Хорошо"
			}]
		);
		
		files = files-1;

		return false;
	}

	var fileReader = new FileReader();

	fileReader.onload = (function(file){
		return function(e){
			dataArray.push({name : file.name, value : this.result, check : null, sel : null});
			addImage((dataArray.length-1));
		};
	})(file);

	fileReader.readAsDataURL(file);

	return false;
}

function delImage(id)
{
	$(this).empty();
	dataArray.splice(id, 1);
	$('#dropped-files > .img-block').remove();
	addImage(-1);

	return false;
}

function addImage(ind)
{
	if(ind < 0 )
	{
		start = 0;
		end = dataArray.length;
		files = files-1;
	}else{
		start = ind;
		end = ind+1;
	}

	if(dataArray.length == 0)
	{
		$('#upload-button').hide();
		$('#uploaded-holder').hide();
	}

	for(i = start; i < end; i++)
		if($('#dropped-files > .img-block').length <= maxFiles)
			$('#dropped-files').append('<div class="img-block" id="img-'+i+'"><div class="image" style="background: url('+dataArray[i].value+'); background-size: contain;"></div><a href="#" onclick="return delImage(\''+i+'\')" class="drop-button">Удалить</a></div>'); 

	return false;
}

function restartFiles(go)
{
	$('#loading-bar .loading-color').css({'width' : '0%'});
	$('#loading').css({'display' : 'none'});
	$('#loading-content').html(' ');
	$('#upload-button').hide();
	$('#dropped-files > .img-block').remove();
	$('#uploaded-holder').hide();
	dataArray.length = 0;
	if(go != 1)
		files = upload;

	return false;
}

$('#dropped-files #upload-button .delete').click(restartFiles);

$('#upload-button .upload').click(function()
{
	$("#loading").show();

	var totalPercent = 100 / dataArray.length;
	var x = 0;

	$('#loading-content').html('Загружен '+dataArray[0].name);

	$.each(dataArray, function(index, file)
	{
		upload = upload + 1;

		$.post(home+'chat/section/upload', dataArray[index], function(data)
		{
			var fileName = dataArray[index].name;
			++x;

			$('#loading-bar .loading-color').css({'width' : totalPercent*(x)+'%'});

			if(totalPercent*(x) == 100)
			{
				$('#loading-content').html('Загрузка завершена.');
				setTimeout(restartFiles(1), 1000);
			}else if(totalPercent*(x) < 100)
				$('#loading-content').html('Загружается '+fileName);

			var dataSplit = data.split(':');

			if(dataSplit[1] == 'ok')
			{
				$('#uploaded-files').append('<div class="img-block"><div class="image" style="background: url('+home+'upload/'+dataSplit[0]+'); background-size: contain;"></div><a target="_blank" href="'+home+'upload/'+dataSplit[0]+'" class="success-button">Загружено</a></div>');
				$('#img-input').append('<input name="img[]" value="'+dataSplit[0]+'" type="hidden"></div>');
			}else
				$('#uploaded-files').append(data);
		});

		if(upload == maxFiles)
			$('#upload_block').hide();
	});

	$('#uploaded-files').show();

	return false;
});

$('#drop-files').on('dragenter', function()
{
	$(this).css({'border-color' : '#74b084'});

	return false;
});

$('#drop-files').on('drop', function()
{
	$(this).css({'border-color' : '#dcdcdc'});
	files = files-1;

	return false;
});
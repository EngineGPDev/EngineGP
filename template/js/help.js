$('#create').ajaxForm({
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
				location.href=home+'help/section/dialog/id/'+val;
		});

		loading(0);

		return false;
	}
});

$('#reply').ajaxForm({
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

			if(i == 'с')
			{
				bootbox.dialog('<h3 class="blue">Внимание</h3>'+val,
					[{
						"label" : "OK",
						"class" : "btn-small btn-primary",
					}]
				);

				location.reload();
			}

			if(i == 'i')
			{	
				bootbox.dialog('<h3 class="blue">Внимание</h3>'+val,
					[{
						"label" : "OK",
						"class" : "btn-small btn-primary",
					}]
				);

				dialog_update(false);
			}

			if(i == 's')
			{
				$('#text').val('');
				$('#text').html('');
				document.getElementById("text").removeAttribute("style");
				$('#img-input').html('');
				$('#uploaded-files').html('');
				$('#uploaded-files').css('display', 'none');
				$('#frm').html($('#frm').html());

				dialog_update(false);
			}
		});

		loading(0);

		return false;
	}
});

function help_open(id)
{
	loading(1);

	$.getJSON(home+'help/section/close/action/open/id/'+id,
	function(data)
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
				location.href=home+'help/section/dialog/id/'+id;

			loading(0)
		});
	});
}

function help_msg_del(id, msg)
{
	loading(1);

	$.getJSON(home+'help/section/dialog/action/remove/id/'+id+'/msg/'+msg,
	function(data)
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
				dialog_update(false);

			loading(0)
		});
	});

	return false;
}

function help_close(id)
{
	loading(1);

	$.getJSON(home+'help/section/open/action/close/id/'+id,
	function(data)
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
				location.reload();

			loading(0)
		});
	});
}

function help_readers()
{
	$.get(home+'help/section/dialog/action/read/id/'+help,
	function(readers)
	{
		$('#help_readers').html(readers);

		setTimeout(function() {help_readers()}, 9000);
	});
}

function help_writers(now)
{
	write = '';

	if($('#text').val() != '')
		write = '/write/1';

	$.get(home+'help/section/dialog/action/write/id/'+help+write,
	function(writers)
	{
		$('#help_writers').html(writers);

		if(!now)
			setTimeout(function() {help_writers(false)}, 9000);
	});
}

function dialog_update(go)
{
	if(go)
	{
		spoilers = $('.spoiler_main');
		update = true;
		for(var i = 0; i < spoilers.length; i++)
		{
			if(spoilers[i].style.display == 'block')
			{
				setTimeout(function() {dialog_update(true)}, 15000);

				return false;
			}
		}
	}

	loading(1);

	$.getJSON(home+'help/section/dialog/id/'+help+'/ajax',
	function(data)
	{
		$.each(data, function(i, val)
		{
			if(i == 'dialog')
			{
				$('#dialog').html(val);

				$('.spoiler').click(function(){
					$(this).parent().children('div.spoiler_main').toggle(0);
				});

				$('pre code').each(function(i, block){
					hljs.highlightBlock(block);
				});
			}

			if(i == 'status')
				$('#status').html(val);
		});

		loading(0);

		if(go)
			setTimeout(function() {dialog_update(true)}, 15000);
	});
}

// Переустановка сервера (подтверждение)
function help_delete(id)
{
	bootbox.dialog('<h3 class="red">Внимание</h3> Вы уверены, что хотите удалить этот вопрос?',
		[{
			"label" : "Подтвердить",
			"class" : "btn-small btn-primary",
			callback: function() {help_delete_go(id)}
		},{
			"label" : "Отмена",
			"class" : "btn-small btn-primary",
		}]
	);
}

function help_delete_go(id)
{
	loading(1);

	$.getJSON(home+'help/section/open/action/delete/id/'+id,
	function(data)
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
				location.reload();

			loading(0)
		});
	});
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

function bbcode(bbbegin, bbend)
{
	form = document.getElementById('text');
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
				alert('Слишком большой файл. Максимально 1024Kb.');
		}
	}else{
		alert('Нельзя загружать больше '+maxFiles+' изображений.');
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
				alert('Слишком большой файл. Максимально 1024Kb.');
		}
	}else{
		alert('Нельзя загружать больше '+maxFiles+' изображений.'); 
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
		alert('Нельзя загружать больше '+maxFiles+' изображений.');
		
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

		$.post(home+'help/section/upload', dataArray[index], function(data)
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

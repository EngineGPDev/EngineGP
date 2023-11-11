function browser(data)
{
	bootbox.dialog('<h3>Подробная информация</h3>'+data,
		[{
			"label" : "Продолжить",
		}]
	);
}

function passwd()
{
	$.get(home+'user/section/lk/passwd/1', function(passwd)
	{
		$('#passwd').val(passwd);
	});
}

function mail_notice(ation)
{
	$.get(home+'user/section/lk/subsection/settings/action/'+ation, function(passwd)
	{
		location.reload();
	});
}

function lk(type)
{
	loading(1);

	var url = '';
	var val = '';

	switch(type)
	{
		case 'mail':
			url = 'user/section/lk/subsection/action/type/mail/go';
			val = 'mail='+$('#mail').val();

			break;

		case 'passwd':
			url = 'user/section/lk/subsection/action/type/passwd/go';
			val = 'passwd='+$('#passwd').val();

			break;

		case 'phone':
			url = 'user/section/lk/subsection/action/type/phone/go';
			val = 'phone='+$('#phone').val();

			break;

		case 'confirm':
			url = 'user/section/lk/subsection/action/type/confirm_phone/go';
			val = '';
			form = '<div class="input_pad"><input id="smscode" placeholder="Введите проверочный код"></div>';

			break;

		case 'confirm_end':
			url = 'user/section/lk/subsection/action/type/confirm_phone_end/go';
			val = 'smscode='+$('#smscode').val();

			break;

		case 'contacts':
			url = 'user/section/lk/subsection/action/type/contacts/go';
			val = 'contacts='+$('#contacts').val();

			break;

		case 'wmr':
			url = 'user/section/lk/subsection/action/type/wmr/go';
			val = 'wmr='+$('#wmr').val();

			break;

		default:
			loading(0);

			return false;
	}
	
	$.ajax({
		type: 'POST',
		url: home+url,
		data: val,
		dataType: 'json',
		success: function(data)
		{
			$.each(data, function(i, val)
			{
				if(i == 'e' && val != '')
					bootbox.dialog(val,
						[{
							"label" : "Продолжить",
						}]
					);

				if(i == 's' && val != '')
				{
					if(type == 'confirm' || type == 'mail')
					{
						if(type == 'confirm')
							bootbox.dialog(form,
								[{
									"label" : "Подтвердить",
									callback: function() {lk('confirm_end');}
								}]
							);
						else
							bootbox.dialog(val,
								[{
									"label" : "Продолжить",
									callback: function() {
										location.href='http://'+data['mail'];
									}
								}]
							);
					}else
						location.reload();
				}
			});

			loading(0)
		}
	});
}

function security(type, id)
{
	loading(1);

	address = $('#'+type+'ip').val();

	if(id)
		address = id;

	switch(type)
	{
		case 'on':
			action = 'on';
			break;

		case 'off':
			action = 'off';
			break;

		case 'on_code':
			action = 'on_code';
			break;

		case 'off_code':
			action = 'off_code';
			break;

		case 'info':
			action = 'info';
			break;

		case 'add': case 'addsub':
			action = 'add';
			break;

		case 'del':
			action = 'del';
			break;

		default:
			return false;
	}

	subnetwork = '';

	if(type == 'addsub')
		subnetwork = '&subnetwork=true';

	$.ajax({
		type: 'POST',
		url: home+'user/section/lk/subsection/security/action/'+action,
		data: 'address='+address+subnetwork,
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

				if(i == 'i')
				{
					bootbox.dialog('<h3 class="blue">Внимание</h3>'+val,
						[{
							"label" : "Удалить",
							"class" : "btn-error",
							callback: function(){security('del', data['id'])}
						},{
							"label" : "Отмена"
						}]
					);
				}

				if(i == 's')
					location.reload();

				if(i == 'info')
					$('#whois').html(val);
			});

			loading(0)
		}
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

jQuery.event.props.push('dataTransfer');

var maxFiles = 1;
var dataArray = [];

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
		alert('Нельзя загружать больше '+maxFiles+' изображения.');
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
		alert('Нельзя загружать больше '+maxFiles+' изображения.'); 
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
		alert('Нельзя загружать больше '+maxFiles+' изображения.');
		
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
	$('#ava > div').remove();
	$('#avatar').css('display', 'block');
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
	{
		if($('#ava > div').length <= maxFiles)
		{
			$('#avatar').css('display', 'none');
			$('#ava').append('<div class="image" style="background: url('+dataArray[i].value+'); background-size: contain;"></div>'); 
		}
	}

	return false;
}

function restartFiles(go)
{
	$('#loading-bar .loading-color').css({'width' : '0%'});
	$('#loading').css({'display' : 'none'});
	$('#loading-content').html(' ');
	$('#upload-button').hide();
	$('#ava > div').remove();
	$('#avatar').css('display', 'block');
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

		$.post(home+'user/section/lk/subsection/settings/action/upload', dataArray[index], function(data)
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
				location.reload();
			else
				alert(data);
		});

	});

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

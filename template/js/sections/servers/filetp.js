function open_path(path)
{	
	path_open = path;

	loading(1);

	$.ajax({
		type: 'POST',
		url: home+'servers/id/'+server+'/section/filetp/go',
		data: 'path='+encodeURIComponent(JSON.stringify(path)),
		dataType: 'html',
		success: function(data)
		{
			$('#filetp').html(data);
			$('#infopath').html(path_open);

			loading(0);
		}
	});

	return false;
}

function cancel()
{
	$('#filetp_block').css('display', 'block');
	$('#filetp_edit').css('display', 'none');

	loading(0);
}

function create(type)
{
	if(type == 'folder')
		val = '<div class="input_pad"><input type="text" placeholder="Введите название папки" id="create_name"></div>';
	else
		val = '<div class="input_pad"><input type="text" placeholder="Введите название файла" id="create_name">'+
			'<br><br>'+
			'<textarea placeholder="Содержание файла" rows="5" id="create_text"></textarea></div>';
	bootbox.dialog(val,
		[{
			"label" : "Создать",
			"class" : "btn-small btn-success",
			callback: function() 
			{
				loading(1);

				$.ajax({
					type: 'POST',
					url: home+'servers/id/'+server+'/section/filetp/action/create/go/true/'+type,
					data: 'path='+encodeURIComponent(JSON.stringify(path_open))
						+'&name='+encodeURIComponent(JSON.stringify($('#create_name').val()))
						+'&text='+encodeURIComponent(JSON.stringify($('#create_text').val())),
					dataType: 'json',
					success: function(data)
					{
						$.each(data, function(i, val)
						{
							if(i == 'e' && val != '')
								bootbox.dialog(val,
									[{
										"label" : "OK",
										"class" : "btn-small btn-primary",
									}]
								);

							if(i == 's' && val != '')
								open_path(path_open);
						});

						loading(0)
					}
				});
			}
		},{
			"label" : "Отмена",
			"class" : "btn-small btn-primary",
		}]
	);
}

function del(type, name)
{
	loading(1);

	$.ajax({
		type: 'POST',
		url: home+'servers/id/'+server+'/section/filetp/action/delete/go/true/'+type,
		data: 'path='+encodeURIComponent(JSON.stringify(path_open))+'&name='+encodeURIComponent(JSON.stringify(name)),
		dataType: 'json',
		success: function(data)
		{
			$.each(data, function(i, val)
			{
				if(i == 'e' && val != '')
					bootbox.dialog(val,
						[{
							"label" : "OK",
							"class" : "btn-small btn-primary",
							//callback: function() {}
						}]
					);
				if(i == 's' && val != '')
					open_path(path_open);
			});

			loading(0)
		}
	});
}

function chmod(name)
{
	bootbox.dialog('<div class="input_pad"><input type="text" placeholder="Введите числовое значение" id="chmod"></div>',
		[{
			"label" : "Изменить",
			"class" : "btn-small btn-success",
			callback: function() 
			{
				loading(1);

				$.ajax({
					type: 'POST',
					url: home+'servers/id/'+server+'/section/filetp/action/chmod/go/true/',
					data: 'path='+encodeURIComponent(JSON.stringify(path_open))
						+'&name='+encodeURIComponent(JSON.stringify(name))
						+'&chmod='+$('#chmod').val(),
					dataType: 'json',
					success: function(data)
					{
						$.each(data, function(i, val)
						{
							if(i == 'e' && val != '')
								bootbox.dialog(val,
									[{
										"label" : "OK",
										"class" : "btn-small btn-primary",
									}]
								);

							if(i == 's' && val != '')
								open_path(path_open);
						});

						loading(0)
					}
				});
			}
		},{
			"label" : "Отмена",
			"class" : "btn-small btn-primary",
		}]
	);
}

function edit(path, name)
{
	loading(1);

	$.ajax({
		type: 'POST',
		url: home+'servers/id/'+server+'/section/filetp/action/edit/go',
		data: 'path='+encodeURIComponent(JSON.stringify(path))+'&name='+encodeURIComponent(JSON.stringify(name)),
		dataType: 'json',
		success: function(data)
		{
			$.each(data, function(i, val)
			{
				if(i == 'e' && val != '')
					bootbox.dialog(val,
						[{
							"label" : "OK",
							"class" : "btn-small btn-primary",
						}]
					);

				if(i == 's')
				{
					$('#filetp_block').css('display', 'none');
					$('#filetp_edit').css('display', 'block');

					sl = '/';
					if(path.slice(-1) == '/') sl = '';

					$('#filetp_file_name').html(path+sl+name);

					$('#filetp_data').val(val);
					$('#filetp_path').val(path);
					$('#filetp_file').val(name);

                    editor.setValue(val);
                    editor.refresh();
				}
			});

			loading(0);
		}
	});

	return false;
}

function edit_go()
{
	loading(1);

	$.ajax({
		type: 'POST',
		url: home+'servers/id/'+server+'/section/filetp/action/create/go',
		data: 'path='+encodeURIComponent(JSON.stringify($('#filetp_path').val()))
			+'&name='+encodeURIComponent(JSON.stringify($('#filetp_file').val()))
			+'&text='+encodeURIComponent(JSON.stringify(editor.getValue())),
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
					bootbox.dialog('<h3 class="green">Внимание</h3> Внесенные изменения сохранены.',
						[{
							"label" : "OK",
							"class" : "btn-small btn-primary",
						}]
					);

					$('#filetp_block').css('display', 'block');
					$('#filetp_edit').css('display', 'none');

					if($('#filetp_find').val().length > 2)
						find();
					else
						open_path(path_open);
				}
			});

			loading(0);
		}
	});

	return false;
}

function rename(name)
{
	bootbox.dialog('<div class="input_pad"><input type="text" value="'+name+'" placeholder="Введите название папки" id="rename_name"></div>',
		[{
			"label" : "Сохранить",
			"class" : "btn-small btn-success",
			callback: function() 
			{
				loading(1);
				$.ajax({
					type: 'POST',
					url: home+'servers/id/'+server+'/section/filetp/action/rename/go',
					data: 'path='+encodeURIComponent(JSON.stringify(path_open))+'&name='+encodeURIComponent(JSON.stringify(name))+'&newname='+encodeURIComponent(JSON.stringify($('#rename_name').val())),
					dataType: 'json',
					success: function(data)
					{
						$.each(data, function(i, val)
						{
							if(i == 'e' && val != '')
								bootbox.dialog(val,
									[{
										"label" : "OK",
										"class" : "btn-small btn-primary",
									}]
								);

							if(i == 's' && val != '')
								open_path(path_open);
						});
						loading(0);
					}
				});
			}
		},{
			"label" : "Отмена",
			"class" : "btn-small btn-primary",
		}]
	);
}

function find()
{
	loading(1);

	$.ajax({
		type: 'POST',
		url: home+'servers/id/'+server+'/section/filetp/action/search/go',
		data: 'find='+encodeURIComponent(JSON.stringify($('#filetp_find').val())),
		dataType: 'html',
		success: function(data)
		{
			$('#filetp').html(data);
			$('#infopath').html('Поиск');

			loading(0);
		}
	});
}

function logs()
{
	if(document.getElementById('filetp_logs').style.display == 'block')
	{
		$('#show_logs').html('показать логи операций');

		$('#filetp_logs').css('display', 'none');
	}else{
		loading(1);

		$.get(home+'servers/id/'+server+'/section/filetp/action/logs/go', function(data){
			$('#show_logs').html('скрыть логи операций');
			$('#filetp_logs').css('display', 'block');
			$('#filetp_logs_data').html(data);

			loading(0);
		});
	}
}

$('#filetp_find').keyup(function(){
	if($('#filetp_find').val().length > 2)
		find();
	else
		open_path(path_open);
});
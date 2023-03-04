// Запуск сервера
function server_start(id)
{
	wait[id] = true;

	loading(1);

	$('#status_'+id).html('Выполняется...');

	$.getJSON(home+'servers/section/action/id/'+id+'/action/start',
	function(data)
	{
		wait[id] = false;

		$.each(data, function(i, val)
		{
			if(i == 'e')
				bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
					[{
						"label" : "OK",
						"class" : "btn-small btn-primary",
					}]
				);

			update_info(id);
			update_status(id);

			loading(0)
		});
	});
}

// Перезапуск сервера
function server_restart(id)
{
	wait[id] = true;

	loading(1);

	$('#status_'+id).html('Выполняется...');

	$.getJSON(home+'servers/section/action/id/'+id+'/action/restart',
	function(data)
	{
		wait[id] = false;

		$.each(data, function(i, val)
		{
			if(i == 'e')
				bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
					[{
						"label" : "OK",
						"class" : "btn-small btn-primary",
					}]
				);

			update_info(id);
			update_status(id);

			loading(0)
		});
	});
}

// Выключение сервера
function server_stop(id)
{
	wait[id] = true;

	loading(1);

	$('#status_'+id).html('Выполняется...');

	$.getJSON(home+'servers/section/action/id/'+id+'/action/stop',
	function(data)
	{
		wait[id] = false;

		$.each(data, function(i, val)
		{
			if(i == 'e')
				bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
					[{
						"label" : "OK",
						"class" : "btn-small btn-primary",
					}]
				);

			update_info(id);
			update_status(id);

			loading(0)
		});
	});
}

// Смена карты (получение списка)
function server_change(id)
{
	if($('#maps_list_'+id).html() != '')
	{
		$('#maps_close_'+id).css('display', 'block');
		$('#maps_list_'+id).css('display', 'block');
	}else{
		loading(1);

		$.getJSON(home+'servers/section/action/id/'+id+'/action/change',
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

				if(i == 'maps')
				{
					$('#maps_close_'+id).css('display', 'block');
					$('#maps_list_'+id).css('display', 'block');
					$('#maps_list_'+id).html(val);
				}

				loading(0)
			});
		});
	}
}

// Смена карты
function server_change_map(id, map)
{
	wait[id] = true;

	loading(1);

	$('#status_'+id).html('Выполняется...');

	server_change_close(id);

	$.getJSON(home+'servers/section/action/id/'+id+'/action/change/change/'+map,
	function(data)
	{
		wait[id] = false;

		$.each(data, function(i, val)
		{
			if(i == 'e')
				bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
					[{
						"label" : "OK",
						"class" : "btn-small btn-primary",
					}]
				);

			update_info(id);
			update_status(id);

			loading(0)
		});
	});
}

// Скрытие списка карт
function server_change_close(id)
{
	$('#maps_close_'+id).css('display', 'none');
	$('#maps_list_'+id).css('display', 'none');
}

// Переустановка сервера (подтверждение)
function server_reinstall(id)
{
	bootbox.dialog('<h3 class="red">Внимание</h3> После переустановки, все текущие файлы будут удалены.',
		[{
			"label" : "Подтвердить",
			"class" : "btn-small btn-primary",
			callback: function() {server_reinstall_go(id);}
		},{
			"label" : "Отмена",
			"class" : "btn-small btn-primary",
		}]
		
	);
}

// Переустановка сервера
function server_reinstall_go(id)
{
	wait[id] = true;

	loading(1);

	$('#status_'+id).html('Выполняется...');

	$.getJSON(home+'servers/section/action/id/'+id+'/action/reinstall', function(data)
	{
		wait[id] = false;

		$.each(data, function(i, val)
		{
			if(i == 'e')
				bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
					[{
						"label" : "OK",
						"class" : "btn-small btn-primary",
					}]
				);

			update_info(id);
			update_status(id);

			loading(0)
		});
	});
}

// Обновление сервера
function server_update(id)
{
	wait[id] = true;

	loading(1);

	$('#status_'+id).html('Выполняется...');

	$.getJSON(home+'servers/section/action/id/'+id+'/action/update', function(data)
	{
		wait[id] = false;

		$.each(data, function(i, val)
		{
			if(i == 'e')
				bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
					[{
						"label" : "OK",
						"class" : "btn-small btn-primary",
					}]
				);

			update_info(id);
			update_status(id);

			loading(0)
		});
	});
}

// Обновление информации сервера
function update_info(id, go = false)
{
	if(wait[id] == true)
	{
		if(go)
			setTimeout(function() {update_info(id, true)}, 3000);

		return false;
	}

	$.getJSON(home+'servers/section/scan/id/'+id+'/mon', function(data)
	{
		$.each(data, function(i, val)
		{
			if(i == 'time' && $('#time_'+id).html() != val && val != '')
				$('#time_'+id).html(val);

			if(i == 'time_end' && $('#time_end_'+id).html() != val && val != '')
				$('#time_end_'+id).html(val);

			if(i == 'name' && $('#name_'+id).html() != val)
				$('#name_'+id).html(val);

			if(i == 'status' && $('#status_'+id).html() != val)
				$('#status_'+id).html(val);

			if(i == 'online' && $('#online_'+id).html() != val)
				$('#online_'+id).html(val);

			if(i == 'image' && $('#image_'+id).html() != val)
				$('#image_'+id).html(val);

			if(i == 'buttons' && $('#buttons_'+id).html() != val)
				$('#buttons_'+id).html(val);
		});
		
		if(go)
			setTimeout(function() {update_info(id, true)}, 2000);
	});
}

// Проверка статуса сервера
function update_status(id, go = false)
{
	$.get(home+'servers/section/scan/id/'+id+'/status', function(data)
	{
		if(go)
			setTimeout(function() {update_status(id, true)}, 2000);
	});
}
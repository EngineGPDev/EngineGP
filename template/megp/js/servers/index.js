// Запуск сервера
function server_start(id)
{
	wait = true;

	$('#status').html('Выполняется...');

	$.getJSON(home+'servers/section/action/id/'+id+'/action/start',
	function(data)
	{
		wait = false;

		$.each(data, function(i, val)
		{
			if(i == 'e')
				bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
					[{
						"label" : "OK",
						"class" : "btn-small btn-primary",
					}]
				);

			update_info(id, false);
			update_status(id, false);
			update_resources(id, false);
		});
	});
}

// Перезапуск сервера
function server_restart(id)
{
	wait = true;

	$('#status').html('Выполняется...');

	$.getJSON(home+'servers/section/action/id/'+id+'/action/restart',
	function(data)
	{
		wait = false;

		$.each(data, function(i, val)
		{
			if(i == 'e')
				bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
					[{
						"label" : "OK",
						"class" : "btn-small btn-primary",
					}]
				);

			update_info(id, false);
			update_status(id, false);
			update_resources(id, false);
		});
	});
}

// Выключение сервера
function server_stop(id)
{
	wait = true;

	$('#status').html('Выполняется...');

	$.getJSON(home+'servers/section/action/id/'+id+'/action/stop',
	function(data)
	{
		wait = false;

		$.each(data, function(i, val)
		{
			if(i == 'e')
				bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
					[{
						"label" : "OK",
						"class" : "btn-small btn-primary",
					}]
				);

			update_info(id, false);
			update_status(id, false);
			update_resources(id, false);
		});
	});
}

// Смена карты (получение списка)
function server_change(id)
{
	if($('#maps_list').html() != '')
	{
		$('#maps_close').css('display', 'block');
		$('#maps_list').css('display', 'block');
	}else{
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
					$('#maps_close').css('display', 'block');
					$('#maps_list').css('display', 'block');
					$('#maps_list').html(val);
				}
			});
		});
	}
}

// Смена карты
function server_change_map(id, map)
{
	wait = true;

	$('#status').html('Выполняется...');

	server_change_close();

	$.getJSON(home+'servers/section/action/id/'+id+'/action/change/change/'+map,
	function(data)
	{
		wait = false;

		$.each(data, function(i, val)
		{
			if(i == 'e')
				bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
					[{
						"label" : "OK",
						"class" : "btn-small btn-primary",
					}]
				);

			update_info(id, false);
			update_status(id, false);
			update_resources(id, false);
		});
	});
}

// Скрытие списка карт
function server_change_close()
{
	$('#maps_close').css('display', 'none');
	$('#maps_list').css('display', 'none');
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
	wait = true;

	$('#status').html('Выполняется...');

	$.getJSON(home+'servers/section/action/id/'+id+'/action/reinstall', function(data)
	{
		wait = false;

		$.each(data, function(i, val)
		{
			if(i == 'e')
				bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
					[{
						"label" : "OK",
						"class" : "btn-small btn-primary",
					}]
				);

			update_info(id, false);
			update_status(id, false);
			update_resources(id, false);
		});
	});
}

// Обновление сервера
function server_update(id)
{
	wait = true;

	$('#status').html('Выполняется...');

	$.getJSON(home+'servers/section/action/id/'+id+'/action/update', function(data)
	{
		wait = false;

		$.each(data, function(i, val)
		{
			if(i == 'e')
				bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
					[{
						"label" : "OK",
						"class" : "btn-small btn-primary",
					}]
				);

			update_info(id, false);
			update_status(id, false);
			update_resources(id, false);
		});
	});
}

// Обновление информации сервера
function update_info(id, go)
{
	if(wait)
	{
		if(go)
			setTimeout(function() {update_info(id, true)}, 3000);

		return false;
	}

	$.getJSON(home+'servers/section/scan/id/'+id+'/fmon', function(data)
	{
		$.each(data, function(i, val)
		{
			if(i == 'time_end' && $('#time_end').html() != val && val != '')
				$('#time_end').html(val);

			if(i == 'name' && $('#name').html() != val)
				$('#name').html(val);

			if(i == 'status' && $('#istatus').html() != val)
				$('#istatus').html(val);

			if(i == 'online' && $('#online').html() != val)
				$('#online').html(val);

			if(i == 'buttons' && $('#buttons').html() != val)
				$('#buttons').html(val);

			if(i == 'players' && $('#players').html() != val)
				$('#players').html(val);
		});

		if(go)
			setTimeout(function() {update_info(id, true)}, 2500);
	});
}

// Обновление информации нагрузки
function update_resources(id, go)
{
	$.getJSON(home+'servers/section/scan/id/'+id+'/resources', function(data)
	{
		$.each(data, function(i, val)
		{
			if(i == 'usr')
				$('#usr_load').html(val+'%');

			if(i == 'cpu')
				$('#cpu_load').html(val+'%');

			if(i == 'ram')
				$('#ram_load').html(val+'%');

			if(i == 'hdd')
				$('#hdd_load').html(val+'%');
		});

		if(go)
			setTimeout(function() {update_resources(id)}, 5000);
	});
}

// Проверка статуса сервера
function update_status(id, go)
{
	$.get(home+'servers/section/scan/id/'+id+'/status', function(data)
	{
		if(go)
			setTimeout(function() {update_status(id, true)}, 5000);
	});
}
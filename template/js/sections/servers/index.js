// Запуск сервера
function server_start(id)
{
	wait = true;

	loading(1);

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

			loading(0)
		});
	});
}

// Перезапуск сервера
function server_restart(id)
{
	wait = true;

	loading(1);

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

			loading(0)
		});
	});
}

// Выключение сервера
function server_stop(id)
{
	wait = true;

	loading(1);

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

			loading(0)
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
					$('#maps_close').css('display', 'block');
					$('#maps_list').css('display', 'block');
					$('#maps_list').html(val);
				}

				loading(0)
			});
		});
	}
}

// Смена карты
function server_change_map(id, map)
{
	wait = true;

	loading(1);

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

			loading(0)
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

	loading(1);

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

			loading(0)
		});
	});
}

// Обновление сервера
function server_update(id)
{
	wait = true;

	loading(1);

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

			loading(0)
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
			if(i == 'time' && $('#time').html() != val && val != '')
				$('#time').html(val);

			if(i == 'time_end' && $('#time_end').html() != val && val != '')
				$('#time_end').html(val);

			if(i == 'name' && $('#name').html() != val)
				$('#name').html(val);

			if(i == 'status' && $('#status').html() != val)
				$('#status').html(val);

			if(i == 'online' && $('#online').html() != val)
				$('#online').html(val);

			if(i == 'image' && $('#image').html() != val)
				$('#image').html(val);

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
				$load_usr.setProgress(val/100);

			if(i == 'cpu')
				$load_cpu.setProgress(val/100);

			if(i == 'ram')
				$load_ram.setProgress(val/100);

			if(i == 'hdd')
				$load_hdd.setProgress(val/100);
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
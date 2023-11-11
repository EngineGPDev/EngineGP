function system_load(go)
{
	if(go)
		loading(1);

	$.getJSON(home+'system/go/',
	function(data)
	{
		$.each(data, function(i, val)
		{
			$('#'+i).html(val);
		});

		loading(0);

		if(!go)
			setTimeout(function() {system_load(false)}, 3000);
	});
}

function system_restart(service)
{
	switch(service)
	{
		case 'apache2':
			type = 'apache2';
		break;
		case 'nginx':
			type = 'nginx';
		break;
		case 'mysql':
			type = 'mysql';
		break;
		case 'unit':
			type = 'локацию';
		break;
	}

	bootbox.dialog('<h3 class="green">Внимание</h3>Вы уверены что хотите перезагруить <u>'+type+'</u>',
	[{
		"label" : "Перезагрузить",
		callback: function(){system_restart_go(service)}
	},{
		"label" : "Отмена"
	}]);

	return false;
}

function system_restart_go(id, service)
{
	loading(1);

	$.getJSON(home+'system/service/'+service,
	function(data)
	{
		$.each(data, function(i, val)
		{
			if(i == 'e')
				bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
					[{
						"label" : "Продолжить"
					}]
				);

			if(i == 's')
				system_load(true);
		});

		loading(0);
	});
}
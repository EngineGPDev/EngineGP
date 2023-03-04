$('#unit').ajaxForm({
	dataType: 'json',
	success: function(data)
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
				location.href=home+'units/id/'+val;
		});

		loading(0)
	}
});

function units_load(id, go)
{
	if(go)
		loading(1);

	$.getJSON(home+'units/section/loading/id/'+id,
	function(data)
	{
		$.each(data, function(i, val)
		{
			$('#'+i+'_'+id).html(val);
		});

		loading(0);

		if(!go)
			setTimeout(function() {units_load(id, false)}, 3000);
	});
}

function units_restart(id, service)
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
		callback: function(){units_restart_go(id, service)}
	},{
		"label" : "Отмена"
	}]);

	return false;
}

function units_restart_go(id, service)
{
	loading(1);

	$.getJSON(home+'units/section/loading/id/'+id+'/service/'+service,
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
				units_load(id, true);
		});

		loading(0);
	});
}

function units_delete(id)
{
	bootbox.dialog('<h3 class="green">Внимание</h3> Вы уверены, что хотите удалить локацию?. <br>'
			+'При <i>"Удалить полностью"</i> - удаляются все услуги и их логи, а также тарифы.',
		[{
			"label" : "Удалить",
			callback : function(){units_delete_go(id, false)}
		},{
			"label" : "Удалить полностью",
			callback : function(){units_delete_go(id, true)}
		},{
			"label" : "Отмена",
		}]
	);

	return false;
}

function units_delete_go(id, all)
{
	
	loading(1);

	if(all) go = '/delete/all'; else go = '';

	$.ajax({
		type: 'POST',
		url: home+'units/section/delete/id/'+id+go,
		dataType: 'json',
		success: function(data)
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
					location.reload()
			});

			loading(0);
		}
	});
}
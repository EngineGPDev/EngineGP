$('#control').ajaxForm({
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
				location.reload();
		});

		loading(0)
	}
});

function control_search(go)
{
	if($('#search').val() == '')
	{
		$('#search_error').css('display', 'none');

		return;
	}

	loading(1);

	if(go) go = '/go'; else go = '';

	$.ajax({
		type: 'POST',
		url: home+'control/subsection/search'+url_search+go,
		data: 'text='+$('#search').val(),
		dataType: 'json',
		success: function(data)
		{
			$.each(data, function(i, val)
			{
				if(i == 'e')
				{
					if(val != '')
					{
						$('#search_error').css('display', 'inline-block');
						$('#search_error').html(val);
					}else
						$('#search_error').css('display', 'none');
				}

				if(i == 's')
				{
					$('#search_error').css('display', 'none');
					$('#search_result').html(val);
				}

				if(i == 'url')
					url_search = val;
			});

			loading(0);
		}
	});
}

function control_overdue(id, time)
{
	bootbox.dialog('<p>Установка даты:</p> <div class="inputs inputs-max"><input type="text" id="date_overdue" onclick="datepick(\'date_overdue\', \''+time+'\')"></div>',
		[{
			"label" : "Установить",
			callback : function(){control_overdue_go(id)}
		},{
			"label" : "Отмена",
		}]
	);

	return false;
}

function control_block(id, time)
{
	bootbox.dialog('<p>Установка даты:</p> <div class="inputs inputs-max"><input type="text" id="date_block" onclick="datepick(\'date_block\', \''+time+'\')"></div>',
		[{
			"label" : "Заблокировать",
			callback : function(){control_block_go(id)}
		},{
			"label" : "Отмена",
		}]
	);

	return false;
}

function datepick(input, time)
{
	if($('#'+input).val() != '')
		time = $('#'+input).val();

	$('#'+input).datetimepicker({value: time, format: 'd/m/Y H:i'});
}

function control_overdue_go(id)
{
	$.ajax({
		type: 'POST',
		url: home+'control/type/overdue/id/'+id+'/go',
		data: 'time='+$('#date_overdue').val(),
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
					location.reload();
			});
		}
	});
}

function control_block_go(id)
{
	$.ajax({
		type: 'POST',
		url: home+'control/type/block/id/'+id+'/go',
		data: 'time='+$('#date_block').val(),
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
					location.reload();
			});
		}
	});
}

function control_delete(id)
{
	bootbox.dialog('<h3 class="green">Внимание</h3> Вы уверены, что хотите удалить подключенный сервер?',
		[{
			"label" : "Удалить",
			callback : function(){control_delete_go(id)}
		},{
			"label" : "Отмена",
		}]
	);

	return false;
}

function control_delete_go(id)
{
	
	loading(1);

	$.ajax({
		type: 'POST',
		url: home+'control/section/delete/id/'+id,
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
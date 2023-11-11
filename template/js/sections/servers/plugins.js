function plugin_confirm(action, id)
{
	switch(action)
	{
		case 'install':
			text = 'Вы уверены, что хотите <u>установить</u> данный плагин?';
		break;

		case 'update':
			text = 'Вы уверены, что хотите <u>обновить</u> данный плагин?';
		break;

		case 'delete':
			text = 'Вы уверены, что хотите <u>удалить</u> данный плагин?';
		break;
	}

	bootbox.dialog('<h3 class="blue">Внимание</h3>'+text,
		[{
			"label" : "Подтвердить",
			"class" : "btn-success",
			callback: function(){
				switch(action)
				{
					case 'install':
						plugin_install(id);
					break;

					case 'update':
						plugin_update(id);
					break;

					case 'delete':
						plugin_delete(id);
				}
			}
		},{
			"label" : "Отмена"
		}]
	);
}

function plugin_install(id, next)
{
	loading(1);

	$.getJSON(home+'servers/id/'+server+'/section/plugins/subsection/install/plugin/'+id+'/go', function(arr)
	{
		$.each(arr, function(i, val)
		{
			if(i == 'e')
			{
				if(arr['pid'] !== undefined)
				{
					if(arr['required'] !== undefined)
						bootbox.dialog('<h3 class="red">Ошибка</h3>'+val+'<p>Родитель: <u>'+arr['pname']+'</u></p>',
							[{
								"label" : "Установить",
								'class' : 'btn-success',
								callback: function(){plugin_install(arr['pid'], id)}
							},{
								"label" : "Отмена"
							}]
						);
					else
						bootbox.dialog('<h3 class="red">Ошибка</h3>'+val+'<p>Плагин: <u>'+arr['pname']+'</u></p>',
							[{
								"label" : "Удалить",
								'class' : 'btn-error',
								callback: function(){plugin_delete(arr['pid'], id)}
							},{
								"label" : "Отмена"
							}]
						);
				}else
					bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
						[{
							"label" : "Продолжить",
						}]
					);
			}

			if(i == 's')
			{
				if(val == 'cfg')
				{
					$('#act_'+id).html('<a href="'+home+'servers/id/'+server+'/section/plugins/subsection/plugin/plugin/'+id+'"><div class="btn btn-info btn-fix btn-short">Настроить</div></a>');
					$('#act_'+id).append('<div onclick="plugin_confirm(\'delete\', \''+id+'\')" class="btn btn-error btn-fix btn-short margin-top">Удалить</div>');
				}else
					$('#act_'+id).html('<div onclick="plugin_confirm(\'delete\', \''+id+'\')" class="btn btn-error btn-fix btn-short margin-top">Удалить</div>');

				if(next)
					plugin_install(next);
			}
		});

		loading(0)
	});
}

function plugin_update(id)
{
	loading(1);

	$.getJSON(home+'servers/id/'+server+'/section/plugins/subsection/update/plugin/'+id+'/go', function(arr)
	{
		$.each(arr, function(i, val)
		{
			if(i == 'e')
			{
				if(arr['pid'] !== undefined)
				{
					if(arr['required'])
						bootbox.dialog('<h3 class="red">Ошибка</h3>'+val+'<p>Родитель: <u>'+arr['pname']+'</u></p>',
							[{
								"label" : "Установить",
								'class' : 'btn-error',
								callback: function(){plugin_install(arr['pid'], id)}
							},{
								"label" : "Отмена"
							}]
						);
					else
						bootbox.dialog('<h3 class="red">Ошибка</h3>'+val+'<p>Плагин: <u>'+arr['pname']+'</u></p>',
							[{
								"label" : "Удалить",
								'class' : 'btn-error',
								callback: function(){plugin_delete(arr['pid'], id)}
							},{
								"label" : "Отмена"
							}]
						);
				}else
					bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
						[{
							"label" : "Продолжить",
						}]
					);
			}

			if(i == 's')
				location.reload();
		});

		loading(0)
	});
}

function plugin_delete(id, next)
{
	loading(1);

	$.getJSON(home+'servers/id/'+server+'/section/plugins/subsection/delete/plugin/'+id+'/go', function(arr)
	{
		$.each(arr, function(i, val)
		{
			if(i == 'e')
				bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
					[{
						"label" : "Продолжить",
					}]
				);

			if(i == 'i')
			{
				bootbox.dialog('<h3 class="green">Внимание</h3>Необходимо установить следующий плагин: <u>'+arr['pname']+'</u></p>',
					[{
						"label" : "Установить",
						'class' : 'btn-success',
						callback: function(){plugin_install(i)}
					},{
						"label" : "Отмена"
					}]
				);
			}

			if(i == 's')
			{
				$('#act_'+id).html('<div onclick="plugin_confirm(\'install\', \''+id+'\')" class="btn btn-success btn-fix btn-short">Установить</div>');

				if(next)
					plugin_install(next);
			}
		});

		loading(0)
	});
}

function plugins_search(go)
{
	if($('#search').val() == '')
	{
		$('#search_block').css('display', 'none');
		return;
	}
	loading(1);

	if(go) go = '/go'; else go = '';

	$.ajax({
		type: 'POST',
		url: home+'servers/id/'+server+'/section/plugins/subsection/search'+go,
		data: 'text='+$('#search').val(),
		dataType: 'json',
		success: function(data)
		{
			$.each(data, function(i, val)
			{
				if(i == 'e')
					bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
						[{
							"label" : "Продолжить",
						}]
					);

				if(i == 's')
				{
					if(val != '')
					{
						$('#search_block').css('display', 'block');
						$('#search_result').html(val);
					}else
						$('#search_block').css('display', 'none');
				}
			});

			loading(0);
		}
	});
}
$('#install').ajaxForm({
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
				bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
					[{
						"label" : "Перейти",
						callback: function(){
							location.href=home+'servers/id/'+server+'/section/web/subsection/'+data['type']+'/action/manage';
						}
					}]
				);

			if(i == 's')
				location.href=home+'servers/id/'+server+'/section/web/subsection/csbans/action/manage';
		});

		loading(0)
	}
});

function passwd()
{
	loading(1);

	$.getJSON(home+'servers/id/'+server+'/section/web/subsection/csbans/action/passwd/go', function(arr)
	{
		$.each(arr, function(i, val)
		{
			if(i == 'e')
				bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
					[{
							"label" : "Продолжить",
					}]
				);

			if(i == 's')
				location.reload();
		});

		loading(0)
	});
}

function update()
{
	loading(1);

	$.getJSON(home+'servers/id/'+server+'/section/web/subsection/csbans/action/update/go', function(arr)
	{
		$.each(arr, function(i, val)
		{
			if(i == 'e')
				bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
					[{
							"label" : "Продолжить",
					}]
				);

			if(i == 's')
				bootbox.dialog('<h3 class="green">Внимание</h3>Запрос на обновление отправлен.',
					[{
							"label" : "Продолжить",
					}]
				);
		});

		loading(0)
	});
}

function delweb()
{
	loading(1);

	$.getJSON(home+'servers/id/'+server+'/section/web/subsection/csbans/action/delete/go', function(arr)
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
				bootbox.dialog('<h3 class="blue">Внимание</h3>'+val,
					[{
							"label" : "Продолжить",
					}]
				);

			if(i == 's')
				location.href=home+'servers/id/'+server+'/section/web';
		});

		loading(0)
	});
}

function connect()
{
	ser = $('#server').val();

	loading(1);

	$.getJSON(home+'servers/id/'+server+'/section/web/subsection/csbans/action/connect/server/'+ser+'/go', function(arr)
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
				bootbox.dialog('<h3 class="red">Внимание</h3>'+val,
					[{
						"label" : "Установить",
						'class' : 'btn-success',
						callback: function(){plugin_install(arr['pid'], false)}
					},{
						"label" : "Отмена"
					}]
				);

			if(i == 'r')
				bootbox.dialog('<h3 class="red">Внимание</h3>'+val,
					[{
						"label" : "Установить",
						'class' : 'btn-error',
						callback: function(){location.href=arr['url']}
					},{
						"label" : "Отмена"
					}]
				);

			if(i == 's')
				bootbox.dialog('<h3 class="red">Внимание</h3>Игровой сервер подключен к веб части.',
					[{
						"label" : "Продолжить",
						callback: function(){location.href=home+'servers/id/'+server}
					}]
				);
		});

		loading(0)
	});
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

				loading(0);
			}

			if(i == 's')
				connect();
		});
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

			if(i == 's' && next)
				plugin_install(next);
		});

		loading(0);
	});
}
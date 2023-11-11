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
				location.href=home+'servers/id/'+server+'/section/web/subsection/astats/action/manage';
		});

		loading(0)
	}
});

$('#form').ajaxForm({
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

			if(i == 'r')
				bootbox.dialog('<h3 class="red">Внимание</h3>'+val,
					[{
						"label" : "Продолжить",
						callback: function(){location.href=data['url']}
					},{
						"label" : "Отмена"
					}]
				);

			if(i == 's')
				bootbox.dialog('<h3 class="red">Внимание</h3>Выбранные игровые сервера подключены к веб части.',
					[{
						"label" : "Продолжить"
					}]
				);
		});

		loading(0)
	}
});

function update()
{
	loading(1);

	$.getJSON(home+'servers/id/'+server+'/section/web/subsection/astats/action/update/go', function(arr)
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

	$.getJSON(home+'servers/id/'+server+'/section/web/subsection/astats/action/delete/go', function(arr)
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
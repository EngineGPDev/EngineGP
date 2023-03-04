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
				location.href=home+'servers/id/'+server+'/section/web/subsection/hosting/action/manage';
		});

		loading(0)
	}
});

function passwd()
{
	loading(1);

	$.getJSON(home+'servers/id/'+server+'/section/web/subsection/hosting/action/passwd/go', function(arr)
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

function delweb()
{
	loading(1);

	$.getJSON(home+'servers/id/'+server+'/section/web/subsection/hosting/action/delete/go', function(arr)
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
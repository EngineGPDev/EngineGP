function web_search(go)
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
		url: home+'web/subsection/search'+go,
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
			});

			loading(0);
		}
	});
}

function web_delete(server, type)
{
	bootbox.dialog('<h3 class="green">Внимание</h3> Вы уверены, что хотите удалить услугу?',
		[{
			"label" : "Удалить",
			callback : function(){web_delete_go(server, type)}
		},{
			"label" : "Отмена",
		}]
	);

	return false;
}

function web_delete_go(server, type)
{
	loading(1);

	$.getJSON('/servers/id/'+server+'/section/web/subsection/'+type+'/action/delete/go', function(arr)
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
				location.reload();
		});

		loading(0)
	});
}
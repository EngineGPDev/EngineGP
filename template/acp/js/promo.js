$('#promo').ajaxForm({
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

function promo_search(go)
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
		url: home+'promo/subsection/search'+go,
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

function promo_delete(id)
{
	bootbox.dialog('<h3 class="green">Внимание</h3> Вы уверены, что хотите удалить акцию?',
		[{
			"label" : "Удалить",
			callback : function(){promo_delete_go(id)}
		},{
			"label" : "Отмена",
		}]
	);

	return false;
}

function promo_delete_go(id)
{
	loading(1);

	$.ajax({
		type: 'POST',
		url: home+'promo/section/delete/id/'+id,
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

function promo_use_delete(id)
{
	bootbox.dialog('<h3 class="green">Внимание</h3> Вы уверены, что хотите удалить лог использования акции?',
		[{
			"label" : "Удалить",
			callback : function(){promo_use_delete_go(id)}
		},{
			"label" : "Отмена",
		}]
	);

	return false;
}

function promo_use_delete_go(id)
{
	loading(1);

	$.ajax({
		type: 'POST',
		url: home+'promo/section/stats/delete/'+id,
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
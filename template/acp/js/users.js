$('#user').ajaxForm({
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
			{
				bootbox.dialog('<h3 class="green">Внимание</h3> Внесенные изменения сохранены.',
					[{
						"label" : "Продолжить",
						callback : function(){location.reload()}
					}]
				);
			}
		});

		loading(0)
	}
});

function users_sort(sort)
{
	switch(sort)
	{
		case 'id':
			if(sort_id == 'asc')
				sort_id = 'desc';
			else
				sort_id = 'asc';

			sorting = sort_id;

		break;

		case 'balance':
			sort_balance = sort_balance == 'asc' ? 'desc' : 'asc';
			sorting = sort_balance;

		break;

		case 'group':
			sort_group = sort_group == 'asc' ? 'desc' : 'asc';
			sorting = sort_group;

	}

	location.href=home+'users/sort/'+sort+'/sorting/'+sorting;
}

function users_search(go)
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
		url: home+'users/subsection/search'+go,
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

function users_delete(id)
{
	bootbox.dialog('<h3 class="green">Внимание</h3> Вы уверены, что хотите удалить этого пользователя? <br>'
			+'При <i>"Удалить полностью"</i> - удаляются все логи, услуги поступают в поток удаления.',
		[{
			"label" : "Удалить",
			callback : function(){users_delete_go(id, false)}
		},{
			"label" : "Удалить полностью",
			callback : function(){users_delete_go(id, true)}
		},{
			"label" : "Отмена",
		}]
	);

	return false;
}

function users_delete_go(id, all)
{
	
	loading(1);

	if(all) go = '/delete/all'; else go = '';

	$.ajax({
		type: 'POST',
		url: home+'users/section/delete/id/'+id+go,
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

function users_delete_signup(id)
{
	bootbox.dialog('<h3 class="green">Внимание</h3> Вы уверены, что хотите удалить подачу регистрации?',
		[{
			"label" : "Удалить",
			callback : function(){users_delete_signup_go(id)}
		},{
			"label" : "Отмена",
		}]
	);

	return false;
}

function users_delete_signup_go(id)
{
	
	loading(1);

	$.getJSON(home+'users/section/signup/delete/signup/id/'+id,
	function(data)
	{
		$.each(data, function(i, val)
		{
			if(i == 's')
				location.reload();
		});
	});
}
$('#tarif').ajaxForm({
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
				location.href=home+'tarifs/id/'+val
		});

		loading(0)
	}
});

function tarifs_ports(ports)
{
	$('#ports').val(ports);
}

function tarifs_sort(sort)
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

		case 'unit':
			sort_unit = sort_unit == 'asc' ? 'desc' : 'asc';
			sorting = sort_unit;

		break;

		case 'game':
			sort_game = sort_game == 'asc' ? 'desc' : 'asc';
			sorting = sort_game;

	}

	location.href=home+'tarifs/sort/'+sort+'/sorting/'+sorting;
}

function tarifs_search(go)
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
		url: home+'tarifs/subsection/search'+go,
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

function tarifs_delete(id)
{
	bootbox.dialog('<h3 class="green">Внимание</h3> Вы уверены, что хотите удалить тариф?',
		[{
			"label" : "Удалить",
			callback : function(){tarifs_delete_go(id)}
		},{
			"label" : "Отмена",
		}]
	);

	return false;
}

function tarifs_delete_go(id)
{
	
	loading(1);

	$.ajax({
		type: 'POST',
		url: home+'tarifs/section/delete/id/'+id,
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
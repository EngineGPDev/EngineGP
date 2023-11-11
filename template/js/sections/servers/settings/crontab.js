$('#form').ajaxForm({
	dataType: 'json',
	success: function(data)
	{
		$.each(data, function(i, val)
		{
			if(i == 'e')
			{
				bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
					[{
						"label" : "OK",
						"class" : "btn-small btn-primary",
					}]
				);
			}
			if(i == 's')
				location.reload();
		});
		loading(0)
	}
});

function crontab_delete(id)
{
	loading(1);

	$.ajax({
		type: 'POST',
		url: home+'servers/id/'+server+'/section/settings/subsection/crontab/action/delete/go',
		data: 'task='+id,
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
				if(i == 's')
					location.reload();
			});

			loading(0)
		}
	});
}

function cron_check_type()
{
	if($('#task').val() == 'console')
		$('#console').css('display', 'table-row');
	else
		$('#console').css('display', 'none');
}
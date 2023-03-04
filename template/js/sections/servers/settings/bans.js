function ban_action(type, val)
{
	loading(1);

	value = $('#ban_val').val();

	if(val)
		value = val;

	switch(type)
	{
		case 'ban':
			action = 'ban';
			break;
		case 'unban':
			action = 'unban';
			break;
		case 'info':
			action = 'info';
			break;
		default:
			return false;
	}

	amx = '';

	if($('#webbans').is(':checked'))
		amx = '&amxbans=1';

	$.ajax({
		type: 'POST',
		url: home+'servers/id/'+server+'/section/settings/subsection/bans/action/'+action+'/go',
		data: 'value='+value+amx,
		dataType: 'json',
		success: function(data)
		{
			$.each(data, function(i, val)
			{
				if(i == 'e')
					bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
						[{
							"label" : "OK",
							"class" : "btn-small btn-primary"
						}]
					);

				if(i == 's')
					location.reload();

				if(i == 'ban')
					bootbox.dialog('<h3 class="blue">Информация</h3>'+val,
						[{
							"label" : "Разбанить",
							"class" : "btn-success",
							callback: function(){ban_action('unban', value)}
						},{
							"label" : "Закрыть",
							"class" : ""
						}]
					);

				if(i == 'unban')
					bootbox.dialog('<h3 class="blue">Информация</h3>'+val,
						[{
							"label" : "Забанить",
							"class" : "btn-error",
							callback: function(){ban_action('ban', value)}
						},{
							"label" : "Закрыть",
							"class" : ""
						}]
					);
			});

			loading(0)
		}
	});
}
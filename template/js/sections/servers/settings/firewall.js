function firewall(type, id)
{
	loading(1);

	address = $('#'+type+'ip').val();

	if(id)
		address = id;

	switch(type)
	{
		case 'info':
			action = 'info';
			break;

		case 'block': case 'blocksub':
			action = 'block';
			break;

		case 'unblock':
			action = 'unblock';
			break;

		default:
			return false;
	}

	subnetwork = '';

	if(type == 'blocksub')
		subnetwork = '&subnetwork=true';

	$.ajax({
		type: 'POST',
		url: home+'servers/id/'+server+'/section/settings/subsection/firewall/action/'+action+'/go',
		data: 'address='+address+subnetwork,
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
				{
					bootbox.dialog('<h3 class="blue">Внимание</h3>'+val,
						[{
							"label" : "Разблокировать",
							"class" : "btn-success",
							callback: function(){firewall('unblock', data['id'])}
						},{
							"label" : "Отмена"
						}]
					);
				}

				if(i == 's')
					location.reload();

				if(i == 'info')
					$('#whois').html(val);
			});

			loading(0)
		}
	});
}

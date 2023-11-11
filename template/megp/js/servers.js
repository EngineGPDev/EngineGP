// Обновление информации сервера
function update_info(id, go = false)
{
	if(wait[id] == true)
	{
		if(go)
			setTimeout(function() {update_info(id, true)}, 3000);

		return false;
	}

	$.getJSON(home+'servers/section/scan/id/'+id+'/mon', function(data)
	{
		$.each(data, function(i, val)
		{
			if(i == 'time_end' && $('#time_end_'+id).html() != val && val != '')
				$('#time_end_'+id).html(val);

			if(i == 'name' && $('#name_'+id).html() != val)
				$('#name_'+id).html(val);

			if(i == 'status' && $('#status_'+id).html() != val)
				$('#status_'+id).html(val);

			if(i == 'online' && $('#online_'+id).html() != val)
				$('#online_'+id).html(val);
		});
		
		if(go)
			setTimeout(function() {update_info(id, true)}, 2000);
	});
}

// Проверка статуса сервера
function update_status(id, go = false)
{
	$.get(home+'servers/section/scan/id/'+id+'/status', function(data)
	{
		if(go)
			setTimeout(function() {update_status(id, true)}, 2000);
	});
}
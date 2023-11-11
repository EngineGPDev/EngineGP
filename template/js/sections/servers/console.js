$('#form_console').ajaxForm({
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
			{
				if(!update)
					update_switch();

				update_console();
			}
		});
	}
});

function set_command(command)
{
	loading(1);

	$.ajax({
		type: 'POST',
		url: '/servers/section/console/id/'+server+'/go',
		data: 'command='+command,
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
				{
					if(!update)
						update_switch();

					update_console();
				}
			});
		}
	});
}

var update = true;

function update_console(go = false)
{

	if(!update)
		return false;

	loading(1);

	$.get(home+"servers/section/console/go/1/id/"+server, function(data)
	{
		var console = document.getElementById("console");
		
		if(data)
		{
			console.innerHTML = data
			console.scrollTop = console.scrollHeight;

			loading(0);
		}
		
		if(go && update)
			setTimeout(function() {update_console(true)}, 2500);
	});
}

function update_switch()
{
	if($('#console_update').html() == '<i class="fa fa-toggle-on"></i>')
	{
		$('#console_update').html('<i class="fa fa-toggle-off"></i>');

		update = false;
	}else{
		$('#console_update').html('<i class="fa fa-toggle-on"></i>');

		update = true;
		update_console();
	}
}

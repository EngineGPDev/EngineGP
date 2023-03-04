function update_players_list()
{
	loading(1);

	$.getJSON(home+'servers/id/'+server+'/section/rcon/go', function(data)
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

			if(i == 'r')
				bootbox.dialog('<h3 class="red">Внимание</h3>'+val,
					[{
						"label" : "Установить",
						'class' : 'btn-success',
						callback: function(){location.href=data['url']}
					},{
						"label" : "Отмена"
					}]
				);

			if(i == 's')
			{
				$('#players').html(val);
				$('#sel_player_info').html('____________');
				$('#sel_player').val('0');
			}
				
		});

		loading(0)
	});
}

function select_player(id, name)
{
	$('#sel_player_info').html('#'+id+' '+name);
	$('#sel_player').val(id);
}

function firewall()
{
	id = $('#sel_player').val();

	if(id < 1)
		return false;

	loading(1);

	$.ajax({
		type: 'POST',
		url: home+'servers/id/'+server+'/section/settings/subsection/firewall/action/block/go',
		data: 'address='+$('#address_'+id).val(),
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
					rcon_kick();
			});

			loading(0)
		}
	});
}

function rcon_ban()
{
	id = $('#sel_player').val();

	if(id < 1)
		return false;

	loading(1);

	$.ajax({
		type: 'POST',
		url: home+'servers/id/'+server+'/section/settings/subsection/bans/action/ban/go',
		data: 'amxbans=1&value='+$('#steamid_'+id).val()+'&userid='+$('#userid_'+id).val(),
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
					update_players_list();
			});

			loading(0)
		}
	});
}

function rcon_kick()
{
	id = $('#sel_player').val();

	if(id < 1)
		return false;

	loading(1);

	$.ajax({
		type: 'POST',
		url: home+'servers/id/'+server+'/section/rcon/action/kick/go',
		data: 'player='+$('#steamid_'+id).val(),
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
					update_players_list();
			});
		}
	});
}

function rcon_kill()
{
	id = $('#sel_player').val();

	if(id < 1)
		return false;

	loading(1);

	$.ajax({
		type: 'POST',
		url: home+'servers/id/'+server+'/section/rcon/action/kill/go',
		data: 'player='+$('#nickname_'+id).val(),
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
					update_players_list();
			});
		}
	});
}
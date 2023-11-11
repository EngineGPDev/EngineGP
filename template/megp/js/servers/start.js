function settings_save(type)
{
	$.getJSON(home+'servers/id/'+server+'/section/settings/subsection/start/save/'+type+'/value/'+$('#'+type).val()+'/go/',
	function(data)
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
				bootbox.dialog('<h3 class="green">Внимание</h3> Внесенные изменения сохранены.',
					[{
						"label" : "OK",
						"class" : "btn-small btn-primary",
					}]
				);
		});
	});
}

function maplist()
{
	$.getJSON(home+'servers/id/'+server+'/section/settings/subsection/start/maps', function(data)
	{
		$.each(data, function(i, val)
		{
			if(i == 'maps')
				$('#map').html(val);
		});
	});
}
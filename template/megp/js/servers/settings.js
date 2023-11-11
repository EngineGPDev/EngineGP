$('#form').ajaxForm({
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
				bootbox.dialog('<h3 class="green">Внимание</h3> Внесенные изменения сохранены.',
					[{
						"label" : "OK",
						"class" : "btn-small btn-primary",
					}]
				);
		});
	}
});

function repack(id)
{
	$.getJSON(home+'servers/id/'+id+'/section/settings/subsection/pack/pack/'+$('#packs').val(), function(arr)
	{
		$.each(arr, function(i, val)
		{
			if(i == 'e')
				bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
					[{
						"label" : "Продолжить",
					}]
				);
			if(i == 's')
				location.reload();
		});
	});
}

function antiddos(id)
{
	$.getJSON(home+'servers/id/'+id+'/section/settings/subsection/antiddos/type/'+$('#rules').val()+'/go', function(arr)
	{
		$.each(arr, function(i, val)
		{
			if(i == 'e')
				bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
					[{
						"label" : "Продолжить",
					}]
				);
			if(i == 's')
				location.reload();
		});
	});
}
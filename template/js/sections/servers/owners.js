$('#add').ajaxForm({
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

function owner_rights(server, id)
{
	$.getJSON(home+'servers/id/'+server+'/section/owners/rights/'+id, function(data)
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
				$('#rights_block').css('display', 'block');
				$('#rights').html(val);
			}
		});
	});
}
function wiki_search(go)
{
	if($('#search').val() == '')
	{
		$('#search_result').css('display', 'none');

		return;
	}

	loading(1);

	$.ajax({
		type: 'POST',
		url: home+'wiki/section/search',
		data: 'text='+$('#search').val(),
		dataType: 'json',
		success: function(data)
		{
			$.each(data, function(i, val)
			{
				if(i == 's')
				{
					if(val != '')
					{
						$('#search_result').css('display', 'block');
						$('#search_result').html(val);
					}else
						$('#search_result').css('display', 'none');
				}
			});

			loading(0);
		}
	});
}
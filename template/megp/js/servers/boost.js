function buy_boost(boost)
{
	switch(boost)
	{
		case 'turboboost':
			site = 'https://turbo-boost.ru/';
			break;

		case 'csboost':
			site = 'http://csboost.net/';
			break;

		case 'zmcs':
			site = 'http://zmcs.ru/';
			break;

		case 'vipms':
			site = 'http://vipms-boost.ru/';
	}

	$.ajax({
		url: home+'servers/id/'+server+'/section/boost/site/'+boost+'/service/'+$('#period_'+boost).val()+'/go',
		dataType: 'json',
		success: function(data)
		{
			$.each(data, function(i, val)
			{
				if(i == 'e' && val != '')
				{
					bootbox.dialog(val,
						[{
							"label" : "Продолжить",
							"class" : "btn-small btn-primary",
						}]
					);
				}

				if(i == 's')
				{
					bootbox.dialog('Вы успешно купили раскрутку.',
						[{
							"label" : "Продолжить",
							"class" : "btn-small btn-primary",
							callback: function() {
								location.href = home+'servers/id/'+server;
							}
						},{
							"label" : "Мониторинг",
							"class" : "btn-small btn-primary",
							callback: function() {
								location.href = site;
							}
						}]
					);
				}
			});
		}
	});

	return false;
}
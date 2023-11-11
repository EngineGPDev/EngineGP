function loading(show)
{
	if(show)
		$('#loadinginfo').css('display', 'block');
	else
		$('#loadinginfo').css('display', 'none');
}

function cashback_output(id, type)
{
	if(type == 'mm')
		bootbox.dialog('Вы подтверждаете, что вывели деньги на эл. кошелек?',
			[{
				"label" : "Подтверждаю",
				callback: function() {
					cashback_output_go(id)
				}
			},{
				"label" : "Отмена"
			}]
		);
	else
		bootbox.dialog('Будет выполнен запрос через шлюз',
			[{
				"label" : "Продолжить",
				callback: function() {
					cashback_output_go(id)
				}
			},{
				"label" : "Отмена"
			}]
		);
}

function cashback_output_go(id)
{
	$.getJSON(home+'cashback/id/'+id, function(data)
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
				bootbox.dialog(val,
					[{
						"label" : "Продолжить",
						callback: function() {
							location.reload()
						}
					}]
				);
			}
		});
	});

	return false
}
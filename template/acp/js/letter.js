$('#letter').ajaxForm({
	dataType: 'json',
	success: function(data)
	{
		$.each(data, function(i, val)
		{
			if(i == 'e')
				bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
					[{
						"label" : "Продолжить"
					}]
				);

			if(i == 's')
			{
				bootbox.dialog('<h3 class="green">Внимание</h3> Рассылка выполнена. <br> Список почт, на которые не удалось отправить сообщение: '+val,
					[{
						"label" : "Продолжить",
						callback : function(){location.reload()}
					}]
				);
			}
		});

		loading(0)
	}
});

function checked_all()
{
	if(letter_all)
	{
		for(i=0; i < document.letter.length; i++)
		{
			if(document.letter.elements[i].type == 'checkbox')
				document.letter.elements[i].checked = false;
		}

		letter_all = false;
	}else{
		for(i=0; i < document.letter.length; i++)
		{
			if(document.letter.elements[i].type == 'checkbox')
				document.letter.elements[i].checked = true;
		}

		letter_all = true;
	}
}

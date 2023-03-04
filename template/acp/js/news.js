$('#news').ajaxForm({
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
				location.reload();
		});

		loading(0)
	}
});

function news_search(go)
{
	if($('#search').val() == '')
	{
		$('#search_error').css('display', 'none');

		return;
	}

	loading(1);

	if(go) go = '/go'; else go = '';

	$.ajax({
		type: 'POST',
		url: home+'news/subsection/search'+go,
		data: 'text='+$('#search').val(),
		dataType: 'json',
		success: function(data)
		{
			$.each(data, function(i, val)
			{
				if(i == 'e')
				{
					if(val != '')
					{
						$('#search_error').css('display', 'inline-block');
						$('#search_error').html(val);
					}else
						$('#search_error').css('display', 'none');
				}

				if(i == 's')
				{
					$('#search_error').css('display', 'none');
					$('#search_result').html(val);
				}
			});

			loading(0);
		}
	});
}

function news_delete(id)
{
	bootbox.dialog('<h3 class="green">Внимание</h3> Вы уверены, что хотите удалить новость?',
		[{
			"label" : "Удалить",
			callback : function(){news_delete_go(id)}
		},{
			"label" : "Отмена",
		}]
	);

	return false;
}

function news_delete_go(id)
{
	loading(1);

	$.ajax({
		type: 'POST',
		url: home+'news/section/delete/id/'+id,
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
					location.reload()
			});

			loading(0);
		}
	});
}

function bbcode(bbbegin, bbend, ta)
{
	form = document.getElementById(ta);
	begin = form.value.substr(0, form.selectionStart);
	end = form.value.substr(form.selectionEnd);
	sel = form.value.substr(form.selectionStart, form.selectionEnd-form.selectionStart);
	var text = form.firstChild;
	form.value = begin+bbbegin+sel+bbend+end;
	selPos = bbbegin.length+begin.length+sel.length+bbend.length;
	form.setSelectionRange(begin.length, selPos);

	return false;
}
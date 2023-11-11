$('#wiki').ajaxForm({
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

function wiki_search(go)
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
		url: home+'wiki/subsection/search'+go,
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

function wiki_delete(id)
{
	bootbox.dialog('<h3 class="green">Внимание</h3> Вы уверены, что хотите удалить ответ?',
		[{
			"label" : "Удалить",
			callback : function(){wiki_delete_go(id)}
		},{
			"label" : "Отмена",
		}]
	);

	return false;
}

function wiki_delete_go(id)
{
	loading(1);

	$.ajax({
		type: 'POST',
		url: home+'wiki/section/delete/id/'+id,
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

function wiki_cat_delete(id)
{
	bootbox.dialog('<h3 class="green">Внимание</h3> Вы уверены, что хотите удалить категорию?',
		[{
			"label" : "Удалить",
			callback : function(){wiki_cat_delete_go(id)}
		},{
			"label" : "Отмена",
		}]
	);

	return false;
}

function wiki_cat_delete_go(id)
{
	loading(1);

	$.ajax({
		type: 'POST',
		url: home+'wiki/section/delete/type/cat/id/'+id,
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

function bbcode(bbbegin, bbend)
{
	form = document.getElementById('text');
	begin = form.value.substr(0, form.selectionStart);
	end = form.value.substr(form.selectionEnd);
	sel = form.value.substr(form.selectionStart, form.selectionEnd-form.selectionStart);
	var text = form.firstChild;
	form.value = begin+bbbegin+sel+bbend+end;
	selPos = bbbegin.length+begin.length+sel.length+bbend.length;
	form.setSelectionRange(begin.length, selPos);

	return false;
}
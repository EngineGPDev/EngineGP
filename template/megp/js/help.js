$('#create').ajaxForm({
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
				location.href=home+'help/section/dialog/id/'+val;
		});

		return false;
	}
});

$('#reply').ajaxForm({
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

			if(i == 'с')
			{
				bootbox.dialog('<h3 class="blue">Внимание</h3>'+val,
					[{
						"label" : "OK",
						"class" : "btn-small btn-primary",
					}]
				);

				location.reload();
			}

			if(i == 'i')
			{	
				bootbox.dialog('<h3 class="blue">Внимание</h3>'+val,
					[{
						"label" : "OK",
						"class" : "btn-small btn-primary",
					}]
				);

				dialog_update(false);
			}

			if(i == 's')
			{
				$('#text').val('');
				$('#text').html('');
				document.getElementById("text").removeAttribute("style");

				dialog_update(false);
			}
		});

		return false;
	}
});

function help_open(id)
{
	$.getJSON(home+'help/section/close/action/open/id/'+id,
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
				location.href=home+'help/section/dialog/id/'+id;
		});
	});
}

function help_msg_del(id, msg)
{
	$.getJSON(home+'help/section/dialog/action/remove/id/'+id+'/msg/'+msg,
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
				dialog_update(false);
		});
	});

	return false;
}

function help_close(id)
{
	$.getJSON(home+'help/section/open/action/close/id/'+id,
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
				location.reload();
		});
	});
}

function help_readers()
{
	$.get(home+'help/section/dialog/action/read/id/'+help,
	function(readers)
	{
		setTimeout(function() {help_readers()}, 9000);
	});
}

function help_writers(now)
{
	write = '';

	if($('#text').val() != '')
		write = '/write/1';

	$.get(home+'help/section/dialog/action/write/id/'+help+write,
	function(writers)
	{
		if(!now)
			setTimeout(function() {help_writers(false)}, 9000);
	});
}

function dialog_update(go)
{
	if(go)
	{
		spoilers = $('.spoiler_main');
		update = true;
		for(var i = 0; i < spoilers.length; i++)
		{
			if(spoilers[i].style.display == 'block')
			{
				setTimeout(function() {dialog_update(true)}, 15000);

				return false;
			}
		}
	}

	$.getJSON(home+'help/section/dialog/id/'+help+'/ajax',
	function(data)
	{
		$.each(data, function(i, val)
		{
			if(i == 'dialog')
			{
				$('#dialog').html(val);

				$('.spoiler').click(function(){
					$(this).parent().children('div.spoiler_main').toggle(0);
				});
			}

			if(i == 'status')
				$('#help_status').html(val);
		});

		if(go)
			setTimeout(function() {dialog_update(true)}, 15000);
	});
}

// Переустановка сервера (подтверждение)
function help_delete(id)
{
	bootbox.dialog('<h3 class="red">Внимание</h3> Вы уверены, что хотите удалить этот вопрос?',
		[{
			"label" : "Подтвердить",
			"class" : "btn-small btn-primary",
			callback: function() {help_delete_go(id)}
		},{
			"label" : "Отмена",
			"class" : "btn-small btn-primary",
		}]
	);
}

function help_delete_go(id)
{
	$.getJSON(home+'help/section/open/action/delete/id/'+id,
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
				location.reload();
		});
	});
}
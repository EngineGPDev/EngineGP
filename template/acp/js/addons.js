$('#addons').ajaxForm({
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

function maps_reset_game()
{
	$('#game').prop('selectedIndex', 0);
}

function maps_list()
{
	$.get(home+'addons/section/updmp/get/list/unit/'+$('#unit').val()+'/game/'+$('#game').val(),
	function(data)
	{
		$('#maps').val(data);
	});

	return false;
}

function maps_update()
{
	loading(1);

	$.get(home+'addons/section/updmp/unit/'+$('#unit').val()+'/game/'+$('#game').val()+'/go',
	function(data)
	{
		location.reload();
	});

	return false;
}

function plugins_category()
{
	$.get(home+'addons/section/addpl/get/cat/game/'+$('#game').val(),
	function(data)
	{
		$('#category').html('<option value="0">Выберете категорию</option>'+data);
	});

	return false;
}

function plugins_update_del(id)
{
	bootbox.dialog('<h3 class="green">Внимание</h3> Вы уверены, что хотите удалить обновление плагина?',
		[{
			"label" : "Удалить",
			callback : function(){plugins_update_del_go(id)}
		},{
			"label" : "Отмена",
		}]
	);

	return false;
}

function plugins_update_del_go(id)
{
	$.get(home+'addons/section/delete/type/update/id/'+id,
	function(data)
	{
		location.reload();
	});
}

function cats_delete(id)
{
	$.get(home+'addons/section/delete/type/cat/id/'+id,
	function(data)
	{
		location.reload();
	});

	return false;
}

function plugins_sort(sort)
{
	switch(sort)
	{
		case 'id':
			if(sort_id == 'asc')
				sort_id = 'desc';
			else
				sort_id = 'asc';

			sorting = sort_id;

		break;

		case 'cat':
			sort_cat = sort_cat == 'asc' ? 'desc' : 'asc';
			sorting = sort_cat;

		break;

		case 'game':
			sort_game = sort_game == 'asc' ? 'desc' : 'asc';
			sorting = sort_game;

	}

	location.href=home+'addons/sort/'+sort+'/sorting/'+sorting;
}

function plugins_search(go)
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
		url: home+'addons/subsection/search'+go,
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

function plugins_delete(id)
{
	bootbox.dialog('<h3 class="green">Внимание</h3> Вы уверены, что хотите удалить плагин?',
		[{
			"label" : "Удалить",
			callback : function(){plugins_delete_go(id)}
		},{
			"label" : "Отмена",
		}]
	);

	return false;
}

function plugins_delete_go(id)
{
	loading(1);

	$.ajax({
		type: 'POST',
		url: home+'addons/section/delete/type/plugin/id/'+id,
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

function add_plugin_type()
{
	if($("#update").val() == 0)
	{
		$('#new_plugin').css('display', 'table-row');
		$('#upd_plugin').css('display', 'none');
	}else{
		$('#new_plugin').css('display', 'none');
		$('#upd_plugin').css('display', 'table-row');
	}
}

function config_files_form()
{
	if(!$("#cfa").prop('checked'))
		$('#config_files_form').css('display', 'table');
	else{
		$('#config_files_form').css('display', 'none');
		$('#config_files_all').html('');
	}
}

var cf = 999999;
var cc = 999999;
var cw = 999999;
var cwe = 999999;
var fd = 999999;

function config_files_add()
{
	cf += 1;

	$('#config_files_all').append('<tr id="cf_'+cf+'">'
		+'<td><input name="config_files_file['+cf+']" placeholder="Введите полный путь к файлу" type="text"></td>'
		+'<td><input name="config_files_sort['+cf+']" type="text"></td>'
		+'<td class="text-center"><a href="#" onclick="return config_files_del(\''+cf+'\')" class="text-red">Удалить</a></td>'
		+'</tr>');
}

function config_clear_add()
{
	cc += 1;

	$('#config_clear_all').append('<tr id="cc_'+cc+'">'
		+'<td><input name="config_clear_file['+cc+']" placeholder="Введите полный путь к файлу" type="text"></td>'
		+'<td><input name="config_clear_text['+cc+']" placeholder="Текст" type="text"></td>'
		+'<td class="text-center"><input name="config_clear_regex['+cc+']" type="checkbox"></td>'
		+'<td class="text-center"><a href="#" onclick="return config_clear_del(\''+cc+'\')" class="text-red">Удалить</a></td>'
		+'</tr>');
}

function config_write_add()
{
	cw += 1;

	$('#config_write_all').append('<tr id="cw_'+cw+'">'
		+'<td><input name="config_write_file['+cw+']" placeholder="Введите полный путь к файлу" type="text"></td>'
		+'<td><input name="config_write_text['+cw+']" placeholder="Текст" type="text"></td>'
		+'<td class="text-center"><input name="config_write_top['+cw+']" type="checkbox"></td>'
		+'<td class="text-center"><a href="#" onclick="return config_write_del(\''+cw+'\')" class="text-red">Удалить</a></td>'
		+'</tr>');
}

function config_write_del_add()
{
	cwe += 1;

	$('#config_write_del_all').append('<tr id="cwe_'+cwe+'">'
		+'<td><input name="config_write_del_file['+cwe+']" placeholder="Введите полный путь к файлу" type="text"></td>'
		+'<td><input name="config_write_del_text['+cwe+']" placeholder="Текст" type="text"></td>'
		+'<td class="text-center"><input name="config_write_del_top['+cwe+']" type="checkbox"></td>'
		+'<td class="text-center"><a href="#" onclick="return config_write_del_del(\''+cwe+'\')" class="text-red">Удалить</a></td>'
		+'</tr>');
}

function files_delete_add()
{
	fd += 1;

	$('#files_delete_all').append('<tr id="fd_'+fd+'">'
		+'<td><input name="files_delete_file['+fd+']" placeholder="Введите полный путь к файлу" type="text"></td>'
		+'<td class="text-center"><a href="#" onclick="return files_delete_del(\''+fd+'\')" class="text-red">Удалить</a></td>'
		+'</tr>');
}

function config_files_del(id)
{
	$('#cf_'+id).empty();

	return false;
}

function config_clear_del(id)
{
	$('#cc_'+id).empty();

	return false;
}

function config_write_del(id)
{
	$('#cw_'+id).empty();

	return false;
}

function config_write_del_del(id)
{
	$('#cwe_'+id).empty();

	return false;
}

function files_delete_del(id)
{
	$('#fd_'+id).empty();

	return false;
}
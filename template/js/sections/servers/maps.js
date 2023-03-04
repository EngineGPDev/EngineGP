$('#maps').ajaxForm({
	dataType: 'json',
	success: function(data)
	{
		$.each(data, function(i, val)
		{
			if(i == 'e')
			{
				bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
					[{
						"label" : "OK",
						"class" : "btn-small btn-primary",
					}]
				);
			}
			if(i == 's')
			{	bootbox.dialog('<h3 class="green">Внимание</h3>Выбранные карты удалены.',
					[{
						"label" : "OK",
						"class" : "btn-small btn-primary",
					}]
				);
				hidden_map_sel()
			}
		});
		loading(0);
	}
});

$('.install').ajaxForm({
	dataType: 'json',
	success: function(data)
	{
		$.each(data, function(i, val)
		{
			if(i == 'e')
			{
				bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
					[{
						"label" : "OK",
						"class" : "btn-small btn-primary",
					}]
				);
			}
			if(i == 's')
			{	bootbox.dialog('<h3 class="green">Внимание</h3>Выбранные карты установлены.',
					[{
						"label" : "OK",
						"class" : "btn-small btn-primary",
					}]
				);
				hidden_map_sel()
			}
		});
		loading(0);
	}
});

$('.form').ajaxForm({
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

		loading(0)
	}
});

function maps_search(go)
{
	if($('#search').val() == '')
	{
		$('#search_block').css('display', 'none');
		return;
	}
	loading(1);

	if(go) go = '/go'; else go = '';

	$.ajax({
		type: 'POST',
		url: home+'servers/id/'+server+'/section/maps/subsection/search'+go,
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
						$('#search_block').css('display', 'block');
						$('#search_result').html(val);
					}else
						$('#search_block').css('display', 'none');
				}

				if(i == 'maps')
				{
					$('#search_block').css('display', 'block');
					$('#search_result').html(val);
				}

				if(i == 'mapsjs')
					install = val;
			});

			loading(0);
		}
	});
}

function hidden_map_sel()
{
	$.each(maps, function(i, map)
	{
		if(!$('#block_'+map+' i').hasClass('fa fa-square-o'))
			$('#form_'+map).css('display', 'none');
	});
}

function select_map(map)
{
	block = document.getElementById('block_'+map).getElementsByTagName('i')[0];
	if($('#block_'+map+' i').hasClass('fa fa-square-o'))
	{
		block.className = "fa fa-check-square-o";
		$('#'+map).val('1');
	}else{
		block.className = "fa fa-square-o";
		$('#'+map).val('0');
	}
}

function select_map_all(arr)
{
	if(arr == 'search') array = install; else array = maps;

	$.each(array, function(i, map)
	{
		block = document.getElementById('block_'+map).getElementsByTagName('i')[0];
		if($('#block_'+map+' i').hasClass('fa fa-square-o'))
		{
			block.className = "fa fa-check-square-o";
			$('#'+map).val('1');
		}
	});
}

function diselect_map_all(arr)
{
	if(arr == 'search') array = install; else array = maps;

	$.each(array, function(i, map)
	{
		block = document.getElementById('block_'+map).getElementsByTagName('i')[0];
		if(!$('#block_'+map+' i').hasClass('fa fa-square-o'))
		{
			block.className = "fa fa-square-o";
			$('#'+map).val('0');
		}
	});
}

function genlist(form)
{
	loading(1);

	$.getJSON(home+'servers/id/'+server+'/section/maps/subsection/listing/gen/maps/go', function(arr)
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
				$('#'+form).val(val);
		});

		loading(0)
	});
}
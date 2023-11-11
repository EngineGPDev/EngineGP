$('#extend').ajaxForm({
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
	}
});

function upd_extend()
{
	time = $('#time').val();

	if(time < 1)
	{
		$('#info_extend').html('0');

		return false;
	}

	$.ajax({
		type: 'POST',
		url: home+'servers/id/'+server+'/section/tarif/subsection/extend',
		data: 'time='+time,
		dataType: 'json',
		success: function(data)
		{
			$.each(data, function(i, val)
			{
				if(i == 's' && val != '')
					$('#info_extend').html(val);
			});

			promo();
		}
	});
}

function promo()
{
	time = $('#time').val();

	if(time < 1)
	{
		$('#info_extend').html('0');
		$('#info_promo').css('display', 'none');
		$('#info_extend').css('text-decoration', 'none');

		return false;
	}

	if($('#promo').val() == '')
	{
		$('#info_promo').css('display', 'none');
		$('#info_extend').css('text-decoration', 'none');
		return false;
	}

	$.ajax({
		type: 'POST',
		url: home+'servers/id/'+server+'/section/tarif/subsection/extend/promo',
		data: 'time='+time+'&promo='+$('#promo').val(),
		dataType: 'json',
		success: function(data)
		{
			$('#info_promo').css('display', 'inline-block');

			if(data['e'] != undefined)
			{
				$('#info_promo').html(data['e']);
				$('#info_extend').css('text-decoration', 'none');
			}else{
				if(data['discount'] == 1)
				{
					$('#info_extend').css('text-decoration', 'line-through');
					$('#info_promo').html('Цена с учетом промо-кода: '+data['sum']+' '+data['cur']);
				}else{
					$('#info_extend').css('text-decoration', 'none');
					$('#info_promo').html('Подарочные дни: '+data['days']);
				}
			}
		}
	});

	return false
}

function upd_plan()
{
	plan = $('#tarif').val();

	if(plan < 1)
	{
		$('#info_plan').html('');

		return false;
	}

	$.getJSON(home+'servers/id/'+server+'/section/tarif/subsection/plan/plan/'+plan, function(data)
	{
		$.each(data, function(i, val)
		{
			if(i == 's' && val != '')
				$('#info_plan').html('Сервер будет арендован до: '+val);
		});
	});
}

function upd_plan_go()
{
	plan = $('#tarif').val();

	if(plan < 1)
		return false;

	$.getJSON(home+'servers/id/'+server+'/section/tarif/subsection/plan/plan/'+plan+'/go', function(data)
	{
		$.each(data, function(i, val)
		{
			if(i == 'e')
				bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
					[{
						"label" : "Продолжить",
					}]
				);

			if(i == 's')
				location.reload();
		});
	});
}

function upd_slots()
{
	slots = $('#slots').val();

	if(slots < 1)
		return false;

	$.getJSON(home+'servers/id/'+server+'/section/tarif/subsection/slots/slots/'+slots, function(data)
	{
		$.each(data, function(i, val)
		{
			if(i == 's')
				$('#info_slots').html(val);
		});
	});
}

function upd_slots_add()
{
	slots = $('#slots').val();

	if(slots < 1)
		return false;

	$.getJSON(home+'servers/id/'+server+'/section/tarif/subsection/slots/slots/'+slots, function(data)
	{
		$.each(data, function(i, val)
		{
			if(i == 's')
				$('#info_slots').html(val);
		});
	});
}

function upd_slots_go()
{
	slots = $('#slots').val();

	if(slots < 1)
		return false;

	$.getJSON(home+'servers/id/'+server+'/section/tarif/subsection/slots/slots/'+slots+'/go', function(data)
	{
		$.each(data, function(i, val)
		{
			if(i == 'e')
				bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
					[{
						"label" : "Продолжить",
					}]
				);

			if(i == 's')
				location.reload();
		});
	});
}
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

		loading(0)
	}
});

$('body').on('click', '.lcs_wrap', function(){
	if($('#address').prop('checked'))
	{
		$('#address').prop('checked', false);
		upd_extend(false);
	}else{
		$('#address').prop('checked', true);
		upd_extend(true);
	}
});

function upd_extend(add)
{
	time = $('#time').val();

	if(time < 1)
	{
		$('#info_extend').html('0');

		return false;
	}

	loading(1);

	address = '';

	if(add || $('#address').prop('checked'))
		address = '&address=1';

	$.ajax({
		type: 'POST',
		url: home+'servers/id/'+server+'/section/tarif/subsection/extend',
		data: 'time='+time+address,
		dataType: 'json',
		success: function(data)
		{
			$.each(data, function(i, val)
			{
				if(i == 's' && val != '')
					$('#info_extend').html(val);
			});

			promo();

			loading(0)
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

	address = '';

	if($('#address').prop('checked'))
		address = '&address=1';

	$.ajax({
		type: 'POST',
		url: home+'servers/id/'+server+'/section/tarif/subsection/extend/promo',
		data: 'time='+time+'&promo='+$('#promo').val()+address,
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
}

function upd_plan()
{
	plan = $('#tarif').val();

	if(plan < 1)
	{
		$('#info_plan').html('');

		return false;
	}

	loading(1);

	$.getJSON(home+'servers/id/'+server+'/section/tarif/subsection/plan/plan/'+plan, function(data)
	{
		$.each(data, function(i, val)
		{
			if(i == 's' && val != '')
				$('#info_plan').html('Сервер будет арендован до: '+val);
		});

		loading(0)
	});
}

function upd_plan_go()
{
	plan = $('#tarif').val();

	if(plan < 1)
		return false;

	loading(1);

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

		loading(0)
	});
}

function upd_slots()
{
	slots = $('#slots').val();

	if(slots < 1)
		return false;

	loading(1);

	$.getJSON(home+'servers/id/'+server+'/section/tarif/subsection/slots/slots/'+slots, function(data)
	{
		$.each(data, function(i, val)
		{
			if(i == 's')
				$('#info_slots').html(val);
		});

		loading(0)
	});
}

function upd_slots_add()
{
	slots = $('#slots').val();

	if(slots < 1)
		return false;

	loading(1);

	$.getJSON(home+'servers/id/'+server+'/section/tarif/subsection/slots/slots/'+slots, function(data)
	{
		$.each(data, function(i, val)
		{
			if(i == 's')
				$('#info_slots').html(val);
		});

		loading(0)
	});
}

function upd_slots_go()
{
	slots = $('#slots').val();

	if(slots < 1)
		return false;

	loading(1);

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

		loading(0)
	});
}

function upd_address()
{
	loading(1);

	$.getJSON(home+'servers/id/'+server+'/section/tarif/subsection/address/aid/'+$('#address').val(), function(data)
	{
		$.each(data, function(i, val)
		{
			if(i == 's')
				$('#info_address').html(val);
		});

		loading(0)
	});
}

function upd_address_go()
{
	loading(1);

	$.getJSON(home+'servers/id/'+server+'/section/tarif/subsection/address/aid/'+$('#address').val()+'/go', function(data)
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

		loading(0)
	});
}

function upd_unit()
{
	unit = $('#unit').val();

	if(unit < 1)
	{
		$('#packs').css('display', 'none');
		('#info_unit').html('');

		return false;
	}

	loading(1);

	$.getJSON(home+'servers/id/'+server+'/section/tarif/subsection/unit/uid/'+unit, function(data)
	{
		$.each(data, function(i, val)
		{
			if(i == 's')
				$('#info_unit').html('Сервер будет арендован до: '+val);

			if(i == 'p')
			{
				$('#packs').css('display', 'table-row');
				$('#pack').html(val);
			}
		});

		loading(0)
	});
}

function upd_unit_go()
{
	unit = $('#unit').val();

	if(unit < 1)
	{
		$('#packs').css('display', 'none');
		('#info_unit').html('');

		return false;
	}

	loading(1);

	$.getJSON(home+'servers/id/'+server+'/section/tarif/subsection/unit/uid/'+unit+'/pack/'+$('#pack').val()+'/go', function(data)
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
				location.href=home+'servers/id/'+server;
		});

		loading(0)
	});
}

function addextend()
{
	loading(1);

	$.getJSON(home+'servers/id/'+server+'/section/tarif/subsection/addextend/go', function(data)
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

		loading(0)
	});
}
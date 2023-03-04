$('#cs').ajaxForm({
	dataType: 'json',
	success: function(data)
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
				location.href = home+'servers/id/'+data['id'];
		});

		loading(0);
	}
});

function change_data(data)
{
	$.getJSON('services/section/cs/id/'+$('#unit').val()+'/tarif/'+$('#tarifs').val()+'/get/'+data, function(arr)
	{
		$.each(arr, function(id, val)
		{
			$('#'+id).html(val);
		});

		upd_price();
		promo();
	});
}

function upd_price()
{
	$.getJSON('services/section/cs/id/'+$('#unit').val()+'/tarif/'+$('#tarifs').val()+'/slots/'+$('#slots').val()+'/time/'+$('#time').val()+'/fps/'+$('#fps').val()+'/get/price', function(arr)
	{
		$.each(arr, function(id, val)
		{
			$('#'+id).html(val);
		});
		
		promo();
	});
}

function promo()
{
	if($('#promo').val() == '')
	{
		$('#promo_tr').css('display', 'none');
		$('#sum_info').css('text-decoration', 'none');
		return false;
	}

	$.getJSON('services/section/cs/id/'+$('#unit').val()+'/tarif/'+$('#tarifs').val()+'/slots/'+$('#slots').val()+'/time/'+$('#time').val()+'/fps/'+$('#fps').val()+'/cod/'+$('#promo').val()+'/get/promo', function(arr)
	{
		$('#promo_tr').css('display', 'table-row');
		
		if(arr['e'] != undefined)
		{
			$('#promo_info').html(arr['e']);
			$('#sum_info').css('text-decoration', 'none');
		}else{
			if(arr['discount'] == 1)
			{
				$('#sum_info').css('text-decoration', 'line-through');
				$('#promo_info').html('Цена с учетом промо-кода: '+arr['sum']+' '+arr['cur']);
			}else{
				$('#sum_info').css('text-decoration', 'none');
				$('#promo_info').html('Подарочные дни: '+arr['days']);
			}
		}
	});
}
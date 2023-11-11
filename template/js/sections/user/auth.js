$('#auth').ajaxForm({
	dataType: 'json',
	success: function(data)
	{
		$.each(data, function(i, val)
		{
			if(i == 'e')
			{
				bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
					[{
						"label" : "Продолжить",
					}]
				);

				document.getElementById("captcha_img").src = home+'user/section/auth/captcha?'+Math.random();

				$('#captcha').val('');
			}

			if(i == 'i')
			{
				bootbox.dialog('<h3 class="blue">Внимание</h3>'+val,
					[{
						"label" : "Продолжить",
					}]
				);

				$('#security_code').css('display', 'table-row');

				document.getElementById("captcha_img").src = home+'user/section/auth/captcha?'+Math.random();

				$('#captcha').val('');
			}

			if(i == 's')
				location.reload();
		});
		loading(0);
	}
});

$('#captcha')[0].onkeyup = function(){
    this.value = this.value.toUpperCase();
};
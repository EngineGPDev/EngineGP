$('#recovery').ajaxForm({
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

				document.getElementById("captcha_img").src = home+'user/section/recovery/captcha?'+Math.random();

				$('#captcha').val('');
			}

			if(i == 's')
				bootbox.dialog('<h3 class="green">Внимание</h3>'+val,
					[{
						"label" : "Продолжить",
						callback: function()
						{
							location.href="http://"+data['mail'];
						}
					}]
				);
		});
		loading(0);
	}
});

$('#captcha')[0].onkeyup = function(){
    this.value = this.value.toUpperCase();
};
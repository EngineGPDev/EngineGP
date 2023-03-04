function loading(show)
{
	if(show)
		$('#loadinginfo').css('display', 'block');
	else
		$('#loadinginfo').css('display', 'none');
}

function help_notice_check()
{
	$.getJSON(home+'help/section/notice/go',
	function(data)
	{
		$.each(data, function(i, val)
		{
			if(i == 'empty')
			{
				hnt = false; help_notice_title(true);
				$('#help_notice').css('display', 'none');
			}

			if(i == 'reply')
			{
				if(val)
				{
					if(val > getCookie('help') || getCookie('help') == undefined)
					{
						setCookie('help', val, {expires: 86400});
						help_notice_sound();
					}
				}

				$('#help_notice').css('display', 'inline-block');
			}

			if(i == 'notice')
			{
				help_notice_sound();
				$('#help_notice').css('display', 'block');
				$('#help_notice_block').css('display', 'block');
				$('#help_notice_block').html(val);
			}
		});
	});

	setTimeout(function() {help_notice_check()}, 10000);
}

function help_notice_sound()
{

	hnt = true; help_notice_title();

	var audio = new Audio();
	audio.preload = 'auto';
	audio.src = '/notice.wav';
	audio.play();
}

function help_notice_title(stop)
{
	if(document.title == title && !stop)
		document.title = 'Новое сообщение';
	else
		document.title = title;

	if(hnt)
		setTimeout(function() {help_notice_title()}, 1000);
}

function setCookie(name, value, options)
{
	options = options || {};

	var expires = options.expires;

	if(typeof expires == 'number' && expires)
	{
		var d = new Date();
		d.setTime(d.getTime() + expires * 1000);
		expires = options.expires = d;
	}

	if(expires && expires.toUTCString)
		options.expires = expires.toUTCString();

	value = encodeURIComponent(value);

	var updatedCookie = name + '=' + value + '; path=/';

	for (var propName in options)
	{
		updatedCookie += '; ' + propName;
		var propValue = options[propName];

		if(propValue !== true)
			updatedCookie += '=' + propValue;
	}

  document.cookie = updatedCookie;
}

function getCookie(name)
{
	var matches = document.cookie.match(new RegExp(
		"(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
	));

	return matches ? decodeURIComponent(matches[1]) : undefined;
}

function deleteCookie(name)
{
	setCookie(name, '', {expires: -1})
}

$(document).ready(function(){
	$('.spoiler').click(function(){
		$(this).parent().children('div.spoiler_main').toggle(0);
	});

	hljs.initHighlightingOnLoad();
});
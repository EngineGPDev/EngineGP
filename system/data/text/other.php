<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$text = array(
		'mcache' => 'Пожалуйста, дождитесь выполнения предыдущего запроса.',
		'captcha' => 'Неправильно указан код проверки либо закончился срок его жизни.',
	);
?>
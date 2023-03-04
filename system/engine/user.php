<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

    // Подключение раздела
	if(!in_array($section, array('auth', 'recovery', 'replenish', 'signup', 'lk', 'quit')))
		include(ENG.'404.php');

	$aTitle = array(
		'auth' => 'Авторизация',
		'recovery' => 'Восстановление',
		'replenish' => 'Пополнение баланса',
		'signup' => 'Регистрация',
		'lk' => 'Личный кабинет',
		'quit' => 'Выход'
	);

	$title = $aTitle[$section];

	if($section == 'lk')
		$html->nav($title, $cfg['http'].'user/section/lk');
	else
		$html->nav($title);

	include(SEC.'user/'.$section.'.php');
?>

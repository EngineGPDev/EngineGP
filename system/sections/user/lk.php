<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	// Проверка на авторизацию
    sys::noauth();

	// Генерация пароля
    if(isset($url['passwd']))
        sys::out(sys::passwd(10));

	$aTitle = array(
		'index' => 'Профиль',
        'settings' => 'Настройки',
        'auth' => 'Логи авторизаций',
        'logs' => 'История операций',
        'security' => 'Безопасность'
	);

	$url['subsection'] = isset($url['subsection']) ? $url['subsection'] : 'index';

    // Подключение раздела
    if(in_array($url['subsection'], array('index', 'settings', 'auth', 'logs', 'security', 'action', 'cashback')))
    {
		$title = isset($aTitle[$url['subsection']]) ? $aTitle[$url['subsection']] : '';
        $html->nav($title);

		include(LIB.'users.php');

		users::nav($url['subsection']);

        include(SEC.'user/lk/'.$url['subsection'].'.php');
    }else
        include(ENG.'404.php');
?>
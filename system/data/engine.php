<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

    // Массив главных разделов
	$aRoute = array(
		'index',
		'servers',
		'services',
		'control',
		'user',
		'replenish',
		'help',
		'news',
		'api',
		'wiki',
		'pages',
		'contacts',
		'agreement',
		'plugins',
		'unitpay',
		'freekassa',
		'webmoney',
		'autocontrol',
		'graph',
		'api_v1',
		'check',
		'monitoring',
		'chat',
		'jobs',
		'partners',
	);

    // Массив главных разделов megp
	$amRoute = array(
		'index',
		'servers',
		'services',
		'replenish',
		'help',
		'news',
		'auth',
		'quit',
		'recovery'
	);

    // Массив регулярных выражений
	$aValid = array(
		'login' => '/^[A-Za-z0-9_]{4,16}$/',
		'mail' => '/^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i',
		'name' => '/^[А-Я]{1,1}[а-я]{2,15}$/u',
		'lastname' => '/^[А-Я]{1,1}[а-я]{2,15}$/u',
		'patronymic' => '/^[А-Я]{1,1}[а-я]{2,15}$/u',
		'phone' => '/^380+[0-9]{9,9}$|^77+[0-9]{9,9}$|^79+[0-9]{9,9}$|^375+[0-9]{9,9}$/m',
		'contacts' => '/^(http|https):\/\/(new\.vk|vk)\.com\/[A-Za-z\_\.]{1,2}[A-Za-z0-9\_\.]{4,32}$|^[A-Za-z][A-Za-z0-9\.\-\_]{6,32}$/',
		'passwd' => '/^[A-Za-z0-9]{6,26}$/',
		'cslogs' => '/^L[A-Za-z0-9\.]/',
		'csamxlogs' => '/^[A-Za-z0-9_\.-]/',
		'csssmlogs' => '/^[A-Za-z0-9_\.-]/',
		'address' => '/^(25[0-5]|2[0-4][0-9]|[0-1][0-9]{2}|[0-9]{2}|[0-9])(\.(25[0-5]|2[0-4][0-9]|[0-1][0-9]{2}|[0-9]{2}|[0-9])){3}:[0-9]{4,5}$/',
	);

	// Массив данных для регистрации
	$aSignup = array(
		// Массив дополнительных полей
		'input' => array(
			'login' => true,
			'mail' => true,
			'name' => true,
			'lastname' => true,
			'patronymic' => false,
			'phone' => false,
			'contacts' => false,
			'passwd' => true
		),
		// Массив описания полей
		'info' => array(
			'login' => 'Логин',
			'mail' => 'Почта',
			'name' => 'Имя',
			'lastname' => 'Фамилия',
			'patronymic' => 'Отчество',
			'phone' => 'Телефон',
			'contacts' => 'Контакты',
			'passwd' => 'Пароль'
		),
		// Массив типа полей
		'type' => array(
			'login' => 'text',
			'mail' => 'text',
			'name' => 'text',
			'lastname' => 'text',
			'patronymic' => 'text',
			'phone' => 'text',
			'contacts' => 'text',
			'passwd' => 'password'
		),
		// Массив подсказки полей
		'placeholder' => array(
			'login' => 'Введите логин',
			'mail' => 'Введите почту',
			'name' => 'Введите имя',
			'lastname' => 'Введите фамилию',
			'patronymic' => 'Введите отчество',
			'phone' => 'Введите номер',
			'contacts' => 'Введите skype или vk',
			'passwd' => 'Введите пароль'
		)
	);

	// Данные для вывода информаци отправителя
	$iHelp = 0; // 0 = Имя/Отчество || 1 = Имя || 2 = Логин | 3 = Почта

	// Вывод времени сообщения
	$tHelp = 0; // 0 - вариант: * минут назад (макс 10мин) || 1 - дата. время (* минут назад)

	// Названия игр
	$aGname = array(
		'cs' => 'CS: 1.6',
        'css' => 'CS: Source',
		'csgo' => 'CS: Global Offensive',
        'cssold' => 'CS: Source v34',
        'mc' => 'MineCraft',
        'mta' => 'GTA: MTA',
        'samp' => 'GTA: SAMP',
        'crmp' => 'GTA: CRMP'
    );

	// Роутер подразделов
	$aRouteSub = array(
		'settings' => array(
			'api',
			'pack',
			'firewall',
			'crontab',
			'startlogs',
			'file'
		),
	);

	// Директория логов StartLogs
	$aSLdir = array(
		'cs' => 'cstrike/oldstart',
		'cssold' => 'cstrike/oldstart',
		'css' => 'cstrike/oldstart',
		'csgo' => 'csgo/oldstart',
		'mc' => 'oldstart',
		'mta' => 'mods/deathmatch/oldstart',
		'samp' => 'oldstart',
		'crmp' => 'oldstart'
	);

	// Директория логов StartLogs ftp (указывать изходя из настроек ftp)
	$aSLdirFtp = array(
		'cs' => 'oldstart',
		'cssold' => 'oldstart',
		'css' => 'oldstart',
		'csgo' => 'csgo/oldstart',
		'mc' => 'oldstart',
		'mta' => 'mods/deathmatch/oldstart',
		'samp' => 'oldstart',
		'crmp' => 'oldstart'
	);

	// Права для совладельцев
	$aOwners = array(
		'cs' => array('start' , 'stop', 'restart', 'change', 'reinstall', 'update', 'console', 'settings', 'plugins', 'maps', 'filetp', 'tarif', 'copy', 'graph'),
		'cssold' => array('start' , 'stop', 'restart', 'change', 'reinstall', 'update', 'console', 'settings', 'plugins', 'maps', 'filetp', 'tarif', 'copy', 'graph'),
		'css' => array('start' , 'stop', 'restart', 'change', 'reinstall', 'update', 'console', 'settings', 'plugins', 'maps', 'filetp', 'tarif', 'copy', 'graph'),
		'csgo' => array('start' , 'stop', 'restart', 'change', 'reinstall', 'update', 'console', 'settings', 'plugins', 'maps', 'filetp', 'tarif', 'copy', 'graph'),
		'mc' => array('start', 'stop', 'restart', 'reinstall', 'console', 'settings', 'plugins', 'filetp', 'tarif', 'copy', 'graph'),
		'mta' => array('start', 'stop', 'restart', 'reinstall', 'console', 'settings', 'plugins', 'filetp', 'tarif', 'copy', 'graph'),
		'samp' => array('start', 'stop', 'restart', 'reinstall', 'console', 'settings', 'plugins', 'filetp', 'tarif', 'copy', 'graph'),
		'crmp' => array('start', 'stop', 'restart', 'reinstall', 'console', 'settings', 'plugins', 'filetp', 'tarif', 'copy', 'graph'),
	);

	$aOwnersI = array(
		'start' => 'Включение',
		'stop' => 'Выключение',
		'restart' => 'Перезагрузка',
		'change' => 'Смена карты',
		'reinstall' => 'Переустановка',
		'update' => 'Обновление',
		'console' => 'Раздел "Консоль"',
		'settings' => 'Раздел "Настройки"',
		'plugins' => 'Раздел "Плагины"',
		'maps' => 'Раздел "Карты"',
		'filetp' => 'Раздел "FileTP"',
		'tarif' => 'Раздел "Тариф"',
		'copy' => 'Раздел "Копии"',
		'graph' => 'Раздел "Графики"'
	);
?>
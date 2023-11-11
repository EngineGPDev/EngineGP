<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$aScfg = array(
		'hostname' => 'Название игрового сервера.',
		'rcon' => 'Управление RCON командами.',
		'rcon_password' => 'Пароль для упраления сервером через RCON команды.',
		'password' => 'Пароль для входа на сервер.',
		'lanmode' => 'Тип игрового сервера.',
		'gamemode0' => 'Моды на сервере.',
		'filterscripts' => 'Сценарии на сервере.',
		'announce' => 'Отображение сервера в интернете.',
		'weburl' => 'Адрес сайта севера.',
		'maxnpc' => 'Максимальное число NPC подключаемых к серверу.',
		'onfoot_rate' => 'Время в миллисекундах за которое сервер обновляет данные о пешем игроке.',
		'incar_rate' => 'Время в миллисекундах за которое сервер обновляет данные о игроке находящемся в транспорте.',
		'weapon_rate' => 'Время в миллисекундах за которое сервер обновляет данные о стрельбе игрока.',
		'stream_distance' => 'Дистанция для обновления стримера игроков.',
		'stream_rate' => 'Время в миллисекундах за которое сервер обновляет игроков в стримере.',
		'logqueries' => 'Логирование всех запросов.'
	);

	$aScfg_form = array(
		'hostname' => '<input value="[hostname]" name="config[\'hostname\']">',
		'rcon_password' => '<input value="[rcon_password]" name="config[\'rcon_password\']">',
		'password' => '<input value="[password]" name="config[\'password\']">',
		'gamemode0' => '<input value="[gamemode0]" name="config[\'gamemode0\']">',
		'filterscripts' => '<input value="[filterscripts]" name="config[\'filterscripts\']">',
		'maxnpc' => '<input value="[maxnpc]" name="config[\'maxnpc\']">',
		'onfoot_rate' => '<input value="[onfoot_rate]" name="config[\'onfoot_rate\']">',
		'incar_rate' => '<input value="[incar_rate]" name="config[\'incar_rate\']">',
		'weapon_rate' => '<input value="[weapon_rate]" name="config[\'weapon_rate\']">',
		'stream_distance' => '<input value="[stream_distance]" name="config[\'stream_distance\']">',
		'stream_rate' => '<input value="[stream_rate]" name="config[\'stream_rate\']">',
		'lagcompmode' => '<select name="config[\'lagcompmode\']">'
						.'<option value="0"> 0 выкл. компенсацию пинга</option>'
						.'<option value="1"> 1 вкл. компенсацию пинга</option>'
						.'<option value="2"> 2 вкл. компенсацию пинга только для обновления позиций.</option></select>',
		'rcon' => '<select name="config[\'rcon\']">'
						.'<option value="0">Запрещено</option>'
						.'<option value="1">Разрешено</option></select>',
		'announce' => '<select name="config[\'announce\']">'
						.'<option value="0">Выключено</option>'
						.'<option value="1">Включено</option></select>',
		'logqueries' => '<select name="config[\'logqueries\']">'
						.'<option value="0">Выключено</option>'
						.'<option value="1">Включено</option></select>',
		'lanmode' => '<select name="config[\'lanmode\']">'
						.'<option value="0">Интернет</option>'
						.'<option value="1">Локальный</option></select>'
	);
?>
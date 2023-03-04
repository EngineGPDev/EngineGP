<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$aScfg = array(
		'hostname' => 'Название игрового сервера.',
		'rcon_password' => 'Пароль для упраления сервером через RCON команды.',
		'sv_password' => 'Пароль для входа на сервер.',
		'sv_pure' => 'Режим проверки соответствия файлов моделей на клиенте.',
		'host_name_store' => 'Передача названия сервера в GOTV.',
		'host_info_show' => 'Передача информации о сервере.',
		'host_players_show' => 'Передача информации о игроках на сервере.',
		'sv_steamgroup' => 'Группа в steam сервера.',
		'sv_downloadurl' => 'Место, из которого клиенты могут скачать недостающие файлы.<br>Использовать, <u>если не включен FastDL</u>.',
		'mapgroup' => 'Набор карт на сервере.',
		'sv_hibernate_when_empty' => 'Через сколько секунд переводить сервер в спящий режим.<br><u>0 = Cпящий режим выключен</u>.',
		'sv_setsteamaccount' => 'Токен для игрового сервера (без него на сервер не смогут зайти игроки).'
	);

	$aScfg_form = array(
		'hostname' => '<input value="[hostname]" name="config[\'hostname\']">',
		'rcon_password' => '<input value="[rcon_password]" name="config[\'rcon_password\']">',
		'sv_password' => '<input value="[sv_password]" name="config[\'sv_password\']">',
		'sv_pure' => '<select name="config[\'sv_pure\']">'
						.'<option value="-1">-1 (чистый сервер)</option>'
						.'<option value="0"> 0 (Правила из pure_server_minimal.txt)</option>'
						.'<option value="1"> 1 (Правила из pure_server_full.txt и pure_server_whitelist.txt)</option>'
						.'<option value="2"> 2 (Правила из pure_server_full.txt)</option></select>',
		'host_name_store' => '<select name="config[\'host_name_store\']">'
								.'<option value="0">Запрещено</option>'
								.'<option value="1">Разрешено</option></select>',
		'host_info_show' => '<select name="config[\'host_info_show\']">'
								.'<option value="2">Передавать всю информацию</option>'
								.'<option value="1">Передавать всю информацию, кроме информации о игроках</option>'
								.'<option value="0">Не передавать никакую информацию</option></select>',
		'host_players_show' => '<select name="config[\'host_players_show\']">'
								.'<option value="2">Передавать всю информацию о игроках</option>'
								.'<option value="1">Передавать только MaxPlayers и время</option>'
								.'<option value="0">Не передавать никакую информацию</option></select>',
		'sv_steamgroup' => '<input value="[sv_steamgroup]" name="config[\'sv_steamgroup\']">',
		'sv_downloadurl' => '<input value="[sv_downloadurl]" name="config[\'sv_downloadurl\']">',
		'mapgroup' => '<input value="[mapgroup]" name="config[\'mapgroup\']">',
		'sv_hibernate_when_empty' => '<input value="[sv_hibernate_when_empty]" name="config[\'sv_hibernate_when_empty\']">',
		'sv_setsteamaccount' => '<input value="[sv_setsteamaccount]" name="config[\'sv_setsteamaccount\']">'
	);
?>
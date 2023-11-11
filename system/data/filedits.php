<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

    // Массив файлов для редактирования (раздел "настройки")
	$aEdits = array(
		'cs' => array(
			'all' => array(
				'files' => array(
					'autoexec.cfg',
					'fastdl.cfg',
					'plugins.ini',
					'users.ini',
					'motd.txt',
				),
				'path' => array(
					'autoexec.cfg' => 'cstrike/',
					'fastdl.cfg' => 'cstrike/',
					'plugins.ini' => 'cstrike/addons/amxmodx/configs/',
					'users.ini' => 'cstrike/addons/amxmodx/configs/',
					'motd.txt' => 'cstrike/',
				),
				'desc' => array(
					'autoexec.cfg' => 'Автоподключаемый конфигурационный файл.',
					'fastdl.cfg' => 'Быстрая закачка файлов с сервера.',
					'plugins.ini' => 'Список плагинов на сервере.',
					'users.ini' => 'Список админов на сервере.',
					'motd.txt' => 'Окно приветствия на сервере.',
				)
			),
			
		),

		'cssold' => array(
			'all' => array(
				'files' => array(
					'autoexec.cfg',
					'fastdl.cfg',
					'admins_simple.ini',
				),
				'path' => array(
					'autoexec.cfg' => 'cstrike/cfg/',
					'fastdl.cfg' => 'cstrike/cfg/',
					'admins_simple.ini' => 'cstrike/addons/sourcemod/configs/',
				),
				'desc' => array(
					'autoexec.cfg' => 'Автоподключаемый конфигурационный файл.',
					'fastdl.cfg' => 'Быстрая закачка файлов с сервера.',
					'admins_simple.ini' => 'Список админов на сервере.',
				)
			)
		),

		'css' => array(
			'all' => array(
				'files' => array(
					'autoexec.cfg',
					'fastdl.cfg',
					'admins_simple.ini',
				),
				'path' => array(
					'autoexec.cfg' => 'cstrike/cfg/',
					'fastdl.cfg' => 'cstrike/cfg/',
					'admins_simple.ini' => 'cstrike/addons/sourcemod/configs/',
				),
				'desc' => array(
					'autoexec.cfg' => 'Автоподключаемый конфигурационный файл.',
					'fastdl.cfg' => 'Быстрая закачка файлов с сервера.',
					'admins_simple.ini' => 'Список админов на сервере.',
				)
			)
		),

		'csgo' => array(
			'all' => array(
				'files' => array(
					'autoexec.cfg',
					'fastdl.cfg',
					'webapi_authkey.txt',
				),
				'path' => array(
					'autoexec.cfg' => 'csgo/cfg/',
					'fastdl.cfg' => 'csgo/cfg/',
					'webapi_authkey.txt' => 'csgo/',
				),
				'desc' => array(
					'autoexec.cfg' => 'Автоподключаемый конфигурационный файл.',
					'fastdl.cfg' => 'Быстрая закачка файлов с сервера.',
					'webapi_authkey.txt' => 'API ключ для установки карт из мастерской <u>WorkShop</u>.',
				)
			)
		),

		'mta' => array(
			'all' => array(
				'files' => array(
					'mtaserver.conf',
					'acl.xml',
					'vehiclecolors.conf'
				),
				'path' => array(
					'mtaserver.conf' => 'mods/deathmatch/',
					'acl.xml' => 'mods/deathmatch/',
					'vehiclecolors.conf' => 'mods/deathmatch/'
				),
				'desc' => array(
					'mtaserver.conf' => 'Основной конфигурационный файл сервера.',
					'acl.xml' => 'Настройки прав на игровом сервере.',
					'vehiclecolors.conf' => 'Настройки цветов автомобилей на игровом сервере.'
				)
			)
		),

		'mc' => array(
			'all' => array(
				'files' => array(
					'ops.txt',
					'permissions.yml',
					'white-list.txt',
					'banned-players.txt',
					'banned-ips.txt'
				),
				'path' => array(
					'ops.txt' => '/',
					'permissions.yml' => '/',
					'white-list.txt' => '/',
					'banned-players.txt' => '/',
					'banned-ips.txt' => '/'
				),
				'desc' => array(
					'ops.txt' => 'Файл в котором прописываются админы.',
					'permissions.yml' => 'Список разрешений',
					'white-list.txt' => 'Белый список игроков.',
					'banned-players.txt' => 'Забаненные игроки.',
					'banned-ips.txt' => 'Забаненные IP адреса.'
				)
			)
		)
	);

	if(isset($aEditslist))
	{
		$dir = isset($ctrlmod) ? 'control/' : '';

		// Генерация общего списка редактируемых файлов
		if(isset($aEdits[$server['game']]['all']['files']))
			foreach($aEdits[$server['game']]['all']['files'] as $file)
			{
				$html->get('edits_list', 'sections/'.$dir.'servers/games/settings');
					$html->set('id', $id);
					$html->set('name', $file);
					$html->set('desc', $aEdits[$server['game']]['all']['desc'][$file]);

					if(isset($ctrlmod))
						$html->set('server', $sid);
				$html->pack('edits');
			}

		if(!isset($ctrlmod))
		{
			// Генерация списка редактируемых файлов по тарифу
			if(isset($aEdits[$server['game']][$tarif['name']]['files']))
				foreach($aEdits[$server['game']][$tarif['name']]['files'] as $file)
				{
					$html->get('edits_list', 'sections/servers/games/settings');
						$html->set('id', $id);
						$html->set('name', $file);
						$html->set('desc', $aEdits[$server['game']][$tarif['name']]['desc'][$file]);
					$html->pack('edits');
				}
		}
	}
?>
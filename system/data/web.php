<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$aSub = array(
		'csbans',
		'bp',
		'csstats',
		'astats',
		'sourcebans',
		'mysql',
		'hosting'
	);

	$aAction = array(
		'csbans' => array(
			#'update',
			'passwd',
			'delete',
			'connect'
		),

		'csstats' => array(
			#'update',
			'delete',
			'connect'
		),

		'bp' => array(
			#'update',
			'settings',
			'passwd',
			'delete'
		),

		'astats' => array(
			#'update',
			'delete',
			'connect'
		),

		'sourcebans' => array(
			#'update',
			'passwd',
			'delete',
			'connect'
		),

		'mysql' => array(
			'install',
			'passwd',
			'delete'
		),

		'hosting' => array(
			'install',
			'passwd',
			'delete'
		)
	);

	// Бесплатные доп. услуги
	$aWeb = array(
		'cs' => array(
			'csbans' => true,

			'bp' => true,

			'csstats' => true,
			'astats' => true,

			'mysql' => true,
			'hosting' => true
		),

		'cssold' => array(
			'sourcebans' => false,

			'mysql' => true,
			'hosting' => true
		),

		'css' => array(
			'sourcebans' => true,

			'mysql' => true,
			'hosting' => true
		),

		'csgo' => array(
			'sourcebans' => true,

			'mysql' => true,
			'hosting' => true
		),

		'samp' => array(
			'mysql' => true,
			'hosting' => true
		),

		'crmp' => array(
			'mysql' => true,
			'hosting' => true
		),

		'mta' => array(
			'mysql' => true,
			'hosting' => true
		),

		'mc' => array(
			'mysql' => true,
			'hosting' => true
		)
	);

	$aWebOne = array(
		'cs' => array(
			'csbans' => array(),

			'bp' => array(),

			'csstats' => array('astats'),
			'astats' => array('csstats'),

			'mysql' => array(),
			'hosting' => array()
		),

		'cssold' => array(
			'sourcebans' => array(),
			'mysql' => array(),
			'hosting' => array()
		),

		'css' => array(
			'sourcebans' => array(),
			'mysql' => array(),
			'hosting' => array()
		),

		'csgo' => array(
			'sourcebans' => array(),
			'mysql' => array(),
			'hosting' => array()
		),

		'samp' => array(
			'mysql' => array(),
			'hosting' => array()
		),

		'mta' => array(
			'mysql' => array(),
			'hosting' => array()
		),

		'mc' => array(
			'mysql' => array(),
			'hosting' => array()
		)
	);

	$aWebInstall = array(
		/*
			'unit' ==> одна услуга на одной локации

			'user' ==> одна услуга на одного пользователя

			'server' ==> каждая услуга на каждый игровой сервер
		*/
		'system' => array(
			'csbans' => 'server',
			'csstats' => 'server',
			'bp' => 'server',
			'astats' => 'server',
			'sourcebans' => 'server',
			'mysql' => 'server',
			'hosting' => 'server'
		),

		'cs' => array(
			'csbans' => 'unit',

			'bp' => 'user',

			'csstats' => 'server',
			'astats' => 'unit',

			'mysql' => 'server',
			'hosting' => 'user'
		),

		'cssold' => array(
			'sourcebans' => 'unit',

			'mysql' => 'server',
			'hosting' => 'user'
		),

		'css' => array(
			'sourcebans' => 'unit',

			'mysql' => 'server',
			'hosting' => 'user'
		),

		'csgo' => array(
			'sourcebans' => 'unit',

			'mysql' => 'server',
			'hosting' => 'user'
		),

		'samp' => array(
			'mysql' => 'server',
			'hosting' => 'user'
		),

		'crmp' => array(
			'mysql' => 'server',
			'hosting' => 'user'
		),

		'mta' => array(
			'mysql' => 'server',
			'hosting' => 'user'
		),

		'mc' => array(
			'mysql' => 'server',
			'hosting' => 'user'
		)
	);

	$aWebname = array(
		'csbans' => 'СS:Bans 1.3',

		'bp' => 'Buy Privileges',

		'csstats' => 'CsStats',
		'astats' => 'AStats',

		'sourcebans' => 'SourceBans',

		'mysql' => 'MySQL',
		'hosting' => 'WebHosting'
	);

	$aWebDesc = array(
		'csbans' => 'система контроля наказаний игроков на серверах (замена amxbans).',

		'bp' => 'многофункциональная система продажи привилегий.',

		'csstats' => 'подробная статистика игроков, для одного сервера.',
		'astats' => 'выводит топ игроков на веб странице.',

		'sourcebans' => 'система контроля наказаний игроков на серверах.',

		'mysql' => 'свободная реляционная система управления базами данных.',
		'hosting' => 'услуга для размещения сайта, форума или обычных файлов в сети.'
	);

	$aWebType = array(
		'csbans' => 'bans',
		'sourcebans' => 'bans',

		'csstats' => 'stats',
		'astats' => 'stats',

		'bp' => 'other',
		'mysql' => 'other',
		'hosting' => 'other'
	);

	$aWebTypeInfo = array(
		'cs' => array(
			'bans' => 'Системы управления банами',
			'stats' => 'Статистика',
			'other' => 'Прочее'
		),

		'cssold' => array(
			'bans' => 'Системы управления банами',
			'stats' => 'Статистика',
			'other' => 'Прочее'
		),

		'css' => array(
			'bans' => 'Системы управления банами',
			'stats' => 'Статистика',
			'other' => 'Прочее'
		),

		'csgo' => array(
			'bans' => 'Системы управления банами',
			'stats' => 'Статистика',
			'other' => 'Прочее'
		),

		'samp' => array(
			'other' => 'Прочее'
		),

		'crmp' => array(
			'other' => 'Прочее'
		),

		'mta' => array(
			'other' => 'Прочее'
		),

		'mc' => array(
			'other' => 'Прочее'
		)
	);

	$aWebParam = array(
		'csbans' => array(
			'passwd' => 10,
			'desing' => array(
				'default' => 'Default'
			),
		),

		'csstats' => array(
			'desing' => array(
				'default' => 'Default'
			),
		),

		'astats' => array(
			'desing' => array(
				'default' => 'Default'
			),
		),

		'sourcebans' => array(
			'passwd' => 10,
			'desing' => array(
				'default' => 'Default'
			),
		),

		'mysql' => array(
			'passwd' => 10,
		),

		'hosting' => array(
			'passwd' => 10,
		)
	);

	$aWebVHTtype = true; // Разрешен ли всем вирт. хостинг
	$aWebVHT = array( // Массив списка tarif_id зависит от значения VHT, если VHT true, то перечисленным id тарифов недоступен вирт. хост или наоборот.
	
	);

	$aWebUnit = array(
		'address' => '127.0.0.1:22', // ip:22 web сервера
		'passwd' => 'kgdfgjksad', // пароль ssh root
		'pma' => '127.0.0.1', // Домен || ip без http / pma / index.php и т.д.
		'domains' => array(
			'domain.ru',
		),
		'subdomains' => array( // список поддоменов, которые нельзя создать
			'panel', 'admin'
		),

		'isp' => array(
			'panel' => 'ip/manager', // https://_ЗНАЧЕНИЕ_ (панель управления вирт. хостинга ISP MANAGER PRO 4)

			'domain' => array(
				'create' => 'http://IP:1500/?authinfo=root:password&out=json&name=[subdomain].[domain].&sdtype=A&addr=[ip]&prio=&wght=&port=&func=domain.sublist.edit&elid=&plid=[domain]&sok=ok',
				'delete' => 'http://IP:1500/?authinfo=root:password&out=json&func=domain.sublist.delete&elid=[subdomain].+A++[ip]&plid=[domain]&sok=ok',
			),

			'account' => array(
				'create' => 'http://IP:1500/?authinfo=root:password&out=json&name=[login]&passwd=[passwd]&confirm=[passwd]&owner=root&ip=IP&domain=[domain]&preset=default&email=[mail]&phpmod=on&func=user.edit&elid=&sok=ok',
				'passwd' => 'http://IP:1500/?authinfo=root:password&out=json&name=[login]&passwd=[passwd]&confirm=[passwd]&preset=default&email=[mail]&disklimit=1000&phpmod=on&func=user.edit&elid=[login]&sok=ok',
				'delete' => 'http://IP:1500/?authinfo=root:password&out=json&func=user.delete&elid=[login]&sok=ok',
			),

			'crontab' => array(
				'bp' => array(
					'install' => 'http://IP:1500/?authinfo=root:password&out=json&min=*&hour=*&mday=*&month=*&wday=*&name=/usr/bin/wget+http://[subdomain].[domain]/cron.php?cron=874319&period=daily&crmin=all&evmin=02&semin=&crhour=all&evhour=02&sehour=&crmday=all&evmday=02&semday=&crmonth=all&evmonth=02&semonth=&crwday=all&evwday=02&sewday=&hideout=on&func=cron.edit&elid=&sok=ok',
					'delete' => 'http://IP:1500/?authinfo=root:password&out=json&elid=[data]&func=cron.delete&sok=ok'
				)
			)
		),

		'unit' => array(
			'csbans' => 'remote',
			'csstats' => 'remote',
			'astats' => 'remote',
			'sourcebans' => 'remote',
			'mysql' => 'remote'
		),

		'path' => array(
			'local' => array(
				'csbans' => '/path/web/csbans/',
				'csstats' => '/path/web/csstats/',
				'astats' => '/path/web/astats/',
				'sourcebans' => '/path/web/sourcebans/'
			),

			'remote' => array(
				'csbans' => '/path/web/csbans/',
				'csstats' => '/path/web/csstats/',
				'astats' => '/path/web/astats/',
				'sourcebans' => '/path/web/sourcebans/'
			),
		),

		'install' => array(
			'local' => array(
				'csbans' => '/var/www/',
				'csstats' => '/var/www/',
				'astats' => '/var/www/',
				'sourcebans' => '/var/www/'
			),

			'remote' => array(
				'csbans' => '/var/www/web/',
				'csstats' => '/var/www/web/',
				'astats' => '/var/www/web/',
				'sourcebans' => '/var/www/web/'
			)
		)
	);

	$aWebConnect = array(
		'csbans' => array(
			'cs' => 0  // id плагина
		),

		'csstats' => array(
			'cs' => 0
		),

		'sourcebans' => array(
			'cssold' => 0,
			'css' => 0,
			'csgo' => 0,
		)
	);

	$aWebChmod = array(
		'csbans' => 'chmod 777 assets protected/runtime',
		'csstats' => '',
		'astats' => 'chmod 777 ftpcache',
		'sourcebans' => 'chmod 777 demos themes_c'
	);

	$aWebSQL = array(
		'csbans' => array(
			'install' => array(
				"INSERT INTO amx_webadmins set id='1', username='admin', password=MD5('[passwd]'), level='1', email='[mail]'",
			),

			'connect' => array(
				"DELETE FROM amx_serverinfo WHERE address='[address]'",
				"INSERT INTO amx_serverinfo set timestamp='[time]', hostname='[name]', rcon='[rcon]', address='[address]', gametype='cstrike', amxban_version='1.6', motd_delay='10', amxban_menu='1'"
			),

			'passwd' => array(
				"UPDATE amx_webadmins set password=MD5('[passwd]') WHERE id='1' LIMIT 1"
			)
		),

		'sourcebans' => array(
			'install' => array(
				"INSERT INTO sb_admins set aid='1', user='admin', authid='', password=SHA1(SHA1('SourceBans[passwd]')), gid='-1', email='[mail]', extraflags='-513'"
			),

			'connect' => array(
				"DELETE FROM sb_servers  WHERE ip='[ip]' and port='[port]'",
				"INSERT INTO sb_servers set ip='[ip]', port='[port]', rcon='[rcon]', modid='3', enabled='1'"
			),

			'passwd' => array(
				"UPDATE sb_admins set password=SHA1(SHA1('SourceBans[passwd]')) WHERE aid='1' LIMIT 1"
			)
		),

		'csstats' => array()
	);

	$aWebdbConf = array(
		'csbans' => array(
			'file' => '/include/db.config.inc.php',
			'chmod' => 0644
		),

		'csstats' => array(
			'file' => '/include/config.php',
			'chmod' => 0644
		),

		'sourcebans' => array(
			'file' => '/config.php',
			'chmod' => 0644
		)
	);

	$aWebothPath = array(
		
	);
?>
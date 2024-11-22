<?php
/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @copyright Copyright (c) 2018-present Solovev Sergei <inbox@seansolovev.ru>
 *
 * @link      https://github.com/EngineGPDev/EngineGP for the canonical source repository
 *
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE MIT License
 */

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

$aSub = [
    'csbans',
    'bp',
    'csstats',
    'astats',
    'sourcebans',
    'mysql',
    'hosting',
];

$aAction = [
    'csbans' => [
        #'update',
        'passwd',
        'delete',
        'connect',
    ],

    'csstats' => [
        #'update',
        'delete',
        'connect',
    ],

    'bp' => [
        #'update',
        'settings',
        'passwd',
        'delete',
    ],

    'astats' => [
        #'update',
        'delete',
        'connect',
    ],

    'sourcebans' => [
        #'update',
        'passwd',
        'delete',
        'connect',
    ],

    'mysql' => [
        'install',
        'passwd',
        'delete',
    ],

    'hosting' => [
        'install',
        'passwd',
        'delete',
    ],
];

// Бесплатные доп. услуги
$aWeb = [
    'cs' => [
        'csbans' => true,

        'bp' => true,

        'csstats' => true,
        'astats' => true,

        'mysql' => true,
        'hosting' => true,
    ],

    'cssold' => [
        'sourcebans' => false,

        'mysql' => true,
        'hosting' => true,
    ],

    'css' => [
        'sourcebans' => true,

        'mysql' => true,
        'hosting' => true,
    ],

    'csgo' => [
        'sourcebans' => true,

        'mysql' => true,
        'hosting' => true,
    ],

    'cs2' => [
        'sourcebans' => true,

        'mysql' => true,
        'hosting' => true,
    ],

    'samp' => [
        'mysql' => true,
        'hosting' => true,
    ],

    'crmp' => [
        'mysql' => true,
        'hosting' => true,
    ],

    'mta' => [
        'mysql' => true,
        'hosting' => true,
    ],

    'mc' => [
        'mysql' => true,
        'hosting' => true,
    ],
];

$aWebOne = [
    'cs' => [
        'csbans' => [],

        'bp' => [],

        'csstats' => ['astats'],
        'astats' => ['csstats'],

        'mysql' => [],
        'hosting' => [],
    ],

    'cssold' => [
        'sourcebans' => [],
        'mysql' => [],
        'hosting' => [],
    ],

    'css' => [
        'sourcebans' => [],
        'mysql' => [],
        'hosting' => [],
    ],

    'csgo' => [
        'sourcebans' => [],
        'mysql' => [],
        'hosting' => [],
    ],

    'cs2' => [
        'sourcebans' => [],
        'mysql' => [],
        'hosting' => [],
    ],

    'samp' => [
        'mysql' => [],
        'hosting' => [],
    ],

    'mta' => [
        'mysql' => [],
        'hosting' => [],
    ],

    'mc' => [
        'mysql' => [],
        'hosting' => [],
    ],
];

$aWebInstall = [
    /*
        'unit' ==> одна услуга на одной локации

        'user' ==> одна услуга на одного пользователя

        'server' ==> каждая услуга на каждый игровой сервер
    */
    'system' => [
        'csbans' => 'server',
        'csstats' => 'server',
        'bp' => 'server',
        'astats' => 'server',
        'sourcebans' => 'server',
        'mysql' => 'server',
        'hosting' => 'server',
    ],

    'cs' => [
        'csbans' => 'unit',

        'bp' => 'user',

        'csstats' => 'server',
        'astats' => 'unit',

        'mysql' => 'server',
        'hosting' => 'user',
    ],

    'cssold' => [
        'sourcebans' => 'unit',

        'mysql' => 'server',
        'hosting' => 'user',
    ],

    'css' => [
        'sourcebans' => 'unit',

        'mysql' => 'server',
        'hosting' => 'user',
    ],

    'csgo' => [
        'sourcebans' => 'unit',

        'mysql' => 'server',
        'hosting' => 'user',
    ],

    'cs2' => [
        'sourcebans' => 'unit',

        'mysql' => 'server',
        'hosting' => 'user',
    ],

    'samp' => [
        'mysql' => 'server',
        'hosting' => 'user',
    ],

    'crmp' => [
        'mysql' => 'server',
        'hosting' => 'user',
    ],

    'mta' => [
        'mysql' => 'server',
        'hosting' => 'user',
    ],

    'mc' => [
        'mysql' => 'server',
        'hosting' => 'user',
    ],
];

$aWebname = [
    'csbans' => 'СS:Bans 1.3',

    'bp' => 'Buy Privileges',

    'csstats' => 'CsStats',
    'astats' => 'AStats',

    'sourcebans' => 'SourceBans',

    'mysql' => 'MySQL',
    'hosting' => 'WebHosting',
];

$aWebDesc = [
    'csbans' => 'система контроля наказаний игроков на серверах (замена amxbans).',

    'bp' => 'многофункциональная система продажи привилегий.',

    'csstats' => 'подробная статистика игроков, для одного сервера.',
    'astats' => 'выводит топ игроков на веб странице.',

    'sourcebans' => 'система контроля наказаний игроков на серверах.',

    'mysql' => 'свободная реляционная система управления базами данных.',
    'hosting' => 'услуга для размещения сайта, форума или обычных файлов в сети.',
];

$aWebType = [
    'csbans' => 'bans',
    'sourcebans' => 'bans',

    'csstats' => 'stats',
    'astats' => 'stats',

    'bp' => 'other',
    'mysql' => 'other',
    'hosting' => 'other',
];

$aWebTypeInfo = [
    'cs' => [
        'bans' => 'Системы управления банами',
        'stats' => 'Статистика',
        'other' => 'Прочее',
    ],

    'cssold' => [
        'bans' => 'Системы управления банами',
        'stats' => 'Статистика',
        'other' => 'Прочее',
    ],

    'css' => [
        'bans' => 'Системы управления банами',
        'stats' => 'Статистика',
        'other' => 'Прочее',
    ],

    'csgo' => [
        'bans' => 'Системы управления банами',
        'stats' => 'Статистика',
        'other' => 'Прочее',
    ],

    'cs2' => [
        'bans' => 'Системы управления банами',
        'stats' => 'Статистика',
        'other' => 'Прочее',
    ],

    'samp' => [
        'other' => 'Прочее',
    ],

    'crmp' => [
        'other' => 'Прочее',
    ],

    'mta' => [
        'other' => 'Прочее',
    ],

    'mc' => [
        'other' => 'Прочее',
    ],
];

$aWebParam = [
    'csbans' => [
        'passwd' => 10,
        'desing' => [
            'default' => 'Default',
        ],
    ],

    'csstats' => [
        'desing' => [
            'default' => 'Default',
        ],
    ],

    'astats' => [
        'desing' => [
            'default' => 'Default',
        ],
    ],

    'sourcebans' => [
        'passwd' => 10,
        'desing' => [
            'default' => 'Default',
        ],
    ],

    'mysql' => [
        'passwd' => 10,
    ],

    'hosting' => [
        'passwd' => 10,
    ],
];

$aWebVHTtype = true; // Разрешен ли всем вирт. хостинг
$aWebVHT = [ // Массив списка tarif_id зависит от значения VHT, если VHT true, то перечисленным id тарифов недоступен вирт. хост или наоборот.

];

$aWebUnit = [
    'address' => '127.0.0.1:22', // ip:22 web сервера
    'passwd' => 'kgdfgjksad', // пароль ssh root
    'pma' => '127.0.0.1', // Домен || ip без http / pma / index.php и т.д.
    'domains' => [
        'domain.ru',
    ],
    'subdomains' => [ // список поддоменов, которые нельзя создать
        'panel', 'admin',
    ],

    'isp' => [
        'panel' => 'ip/manager', // https://_ЗНАЧЕНИЕ_ (панель управления вирт. хостинга ISP MANAGER PRO 4)

        'domain' => [
            'create' => 'http://IP:1500/?authinfo=root:password&out=json&name=[subdomain].[domain].&sdtype=A&addr=[ip]&prio=&wght=&port=&func=domain.sublist.edit&elid=&plid=[domain]&sok=ok',
            'delete' => 'http://IP:1500/?authinfo=root:password&out=json&func=domain.sublist.delete&elid=[subdomain].+A++[ip]&plid=[domain]&sok=ok',
        ],

        'account' => [
            'create' => 'http://IP:1500/?authinfo=root:password&out=json&name=[login]&passwd=[passwd]&confirm=[passwd]&owner=root&ip=IP&domain=[domain]&preset=default&email=[mail]&phpmod=on&func=user.edit&elid=&sok=ok',
            'passwd' => 'http://IP:1500/?authinfo=root:password&out=json&name=[login]&passwd=[passwd]&confirm=[passwd]&preset=default&email=[mail]&disklimit=1000&phpmod=on&func=user.edit&elid=[login]&sok=ok',
            'delete' => 'http://IP:1500/?authinfo=root:password&out=json&func=user.delete&elid=[login]&sok=ok',
        ],

        'crontab' => [
            'bp' => [
                'install' => 'http://IP:1500/?authinfo=root:password&out=json&min=*&hour=*&mday=*&month=*&wday=*&name=/usr/bin/wget+http://[subdomain].[domain]/cron.php?cron=874319&period=daily&crmin=all&evmin=02&semin=&crhour=all&evhour=02&sehour=&crmday=all&evmday=02&semday=&crmonth=all&evmonth=02&semonth=&crwday=all&evwday=02&sewday=&hideout=on&func=cron.edit&elid=&sok=ok',
                'delete' => 'http://IP:1500/?authinfo=root:password&out=json&elid=[data]&func=cron.delete&sok=ok',
            ],
        ],
    ],

    'unit' => [
        'csbans' => 'remote',
        'csstats' => 'remote',
        'astats' => 'remote',
        'sourcebans' => 'remote',
        'mysql' => 'remote',
    ],

    'path' => [
        'local' => [
            'csbans' => '/path/web/csbans/',
            'csstats' => '/path/web/csstats/',
            'astats' => '/path/web/astats/',
            'sourcebans' => '/path/web/sourcebans/',
        ],

        'remote' => [
            'csbans' => '/path/web/csbans/',
            'csstats' => '/path/web/csstats/',
            'astats' => '/path/web/astats/',
            'sourcebans' => '/path/web/sourcebans/',
        ],
    ],

    'install' => [
        'local' => [
            'csbans' => '/var/www/',
            'csstats' => '/var/www/',
            'astats' => '/var/www/',
            'sourcebans' => '/var/www/',
        ],

        'remote' => [
            'csbans' => '/var/www/web/',
            'csstats' => '/var/www/web/',
            'astats' => '/var/www/web/',
            'sourcebans' => '/var/www/web/',
        ],
    ],
];

$aWebConnect = [
    'csbans' => [
        'cs' => 0,  // id плагина
    ],

    'csstats' => [
        'cs' => 0,
    ],

    'sourcebans' => [
        'cssold' => 0,
        'css' => 0,
        'csgo' => 0,
    ],
];

$aWebChmod = [
    'csbans' => 'chmod 777 assets protected/runtime',
    'csstats' => '',
    'astats' => 'chmod 777 ftpcache',
    'sourcebans' => 'chmod 777 demos themes_c',
];

$aWebSQL = [
    'csbans' => [
        'install' => [
            "INSERT INTO amx_webadmins set id='1', username='admin', password=MD5('[passwd]'), level='1', email='[mail]'",
        ],

        'connect' => [
            "DELETE FROM amx_serverinfo WHERE address='[address]'",
            "INSERT INTO amx_serverinfo set timestamp='[time]', hostname='[name]', rcon='[rcon]', address='[address]', gametype='cstrike', amxban_version='1.6', motd_delay='10', amxban_menu='1'",
        ],

        'passwd' => [
            "UPDATE amx_webadmins set password=MD5('[passwd]') WHERE id='1' LIMIT 1",
        ],
    ],

    'sourcebans' => [
        'install' => [
            "INSERT INTO sb_admins set aid='1', user='admin', authid='', password=SHA1(SHA1('SourceBans[passwd]')), gid='-1', email='[mail]', extraflags='-513'",
        ],

        'connect' => [
            "DELETE FROM sb_servers  WHERE ip='[ip]' and port='[port]'",
            "INSERT INTO sb_servers set ip='[ip]', port='[port]', rcon='[rcon]', modid='3', enabled='1'",
        ],

        'passwd' => [
            "UPDATE sb_admins set password=SHA1(SHA1('SourceBans[passwd]')) WHERE aid='1' LIMIT 1",
        ],
    ],

    'csstats' => [],
];

$aWebdbConf = [
    'csbans' => [
        'file' => '/include/db.config.inc.php',
        'chmod' => 0644,
    ],

    'csstats' => [
        'file' => '/include/config.php',
        'chmod' => 0644,
    ],

    'sourcebans' => [
        'file' => '/config.php',
        'chmod' => 0644,
    ],
];

$aWebothPath = [];

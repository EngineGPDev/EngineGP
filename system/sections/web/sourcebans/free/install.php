<?php

/*
 * Copyright 2018-2025 Solovev Sergei
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

// Установка
if ($go) {
    include(LIB . 'web/free.php');

    $aData = [];

    $aData['subdomain'] = isset($_POST['subdomain']) ? strtolower($_POST['subdomain']) : sys::outjs(['e' => 'Необходимо указать адрес.'], $nmch);
    $aData['domain'] = isset($_POST['domain']) ? strtolower($_POST['domain']) : sys::outjs(['e' => 'Необходимо выбрать домен.'], $nmch);
    $aData['desing'] = isset($_POST['desing']) ? strtolower($_POST['desing']) : sys::outjs(['e' => 'Необходимо выбрать шаблон.'], $nmch);
    $aData['passwd'] = $_POST['passwd'] ?? sys::passwd($aWebParam[$url['subsection']]['passwd']);

    $aData['type'] = $url['subsection'];
    $aData['server'] = array_merge($server, ['id' => $id]);

    $aData['config_sql'] = '"Databases"' . PHP_EOL
        . '{' . PHP_EOL
        . '	"driver_default"		"mysql"' . PHP_EOL
        . '' . PHP_EOL
        . '	"sourcebans"' . PHP_EOL
        . '	{' . PHP_EOL
        . '		"driver"	"mysql"' . PHP_EOL
        . '		"host"		"[host]"' . PHP_EOL
        . '		"database"	"[login]"' . PHP_EOL
        . '		"user"		"[login]"' . PHP_EOL
        . '		"pass"		"[passwd]"' . PHP_EOL
        . '		//"timeout"	"0"' . PHP_EOL
        . '		"port"		"3306"' . PHP_EOL
        . '	}' . PHP_EOL
        . '' . PHP_EOL
        . '	"storage-local"' . PHP_EOL
        . '	{' . PHP_EOL
        . '		"driver"	"sqlite"' . PHP_EOL
        . '		"database"	"sourcemod-local"' . PHP_EOL
        . '	}' . PHP_EOL
        . '}';

    $aData['config_php'] = '<?php' . PHP_EOL
        . '	if(!defined(\'IN_SB\'))' . PHP_EOL
        . '	{' . PHP_EOL
        . '		header(\'Content-Type: text/html; charset=UTF-8\');' . PHP_EOL
        . '		die(\'Несанкционированное прямое подключение.\');' . PHP_EOL
        . '	}' . PHP_EOL
        . '	define(\'DB_HOST\', "[host]");' . PHP_EOL
        . '	define(\'DB_USER\', "[login]");' . PHP_EOL
        . '	define(\'DB_PASS\', "[passwd]");' . PHP_EOL
        . '	define(\'DB_NAME\', "[login]");' . PHP_EOL
        . '	define(\'DB_PREFIX\', "sb");' . PHP_EOL
        . '	define(\'DB_PORT\', "3306");' . PHP_EOL
        . '	?>';

    web::install($aData, $nmch);
}

$html->nav('Установка ' . $aWebname[$url['subsection']]);

$desing = '';

// Генерация списка шаблонов
foreach ($aWebParam[$url['subsection']]['desing'] as $name => $desc) {
    $desing .= '<option value="' . $name . '">' . $desc . '</option>';
}

$domains = '';

// Генерация списка доменов
foreach ($aWebUnit['domains'] as $domain) {
    $domains .= '<option value="' . $domain . '">.' . $domain . '</option>';
}

$html->get('install', 'sections/web/' . $url['subsection'] . '/free');

$html->set('id', $id);

$html->set('desing', $desing);
$html->set('domains', $domains);

$html->pack('main');

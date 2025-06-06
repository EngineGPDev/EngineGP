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

use EngineGP\System;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

// Установка
if ($go) {
    $aData = [];

    $aData['subdomain'] = isset($_POST['subdomain']) ? strtolower($_POST['subdomain']) : System::outjs(['e' => 'Необходимо указать адрес.'], $name_mcache);
    $aData['domain'] = isset($_POST['domain']) ? strtolower($_POST['domain']) : System::outjs(['e' => 'Необходимо выбрать домен.'], $name_mcache);
    $aData['desing'] = isset($_POST['desing']) ? strtolower($_POST['desing']) : System::outjs(['e' => 'Необходимо выбрать шаблон.'], $name_mcache);
    $aData['passwd'] = $_POST['passwd'] ?? System::passwd($aWebParam[$url['subsection']]['passwd']);

    $aData['type'] = $url['subsection'];
    $aData['server'] = array_merge($server, ['id' => $id]);

    $sql->query('SELECT `mail`, `contacts` FROM `users` WHERE `id`="' . $server['user'] . '" LIMIT 1');
    $us = $sql->get();

    if ($us['contacts'] == '') {
        System::outjs(['e' => 'Укажите в профиле контактную информацию']);
    }

    if (strpos($us['contacts'], 'ttp', 1)) {
        $vk = $us['contacts'];
        $skype = '';
    } else {
        $vk = '';
        $skype = $us['contacts'];
    }

    $aData['config_sql'] = '';

    $aData['config_php'] = '<?php' . PHP_EOL
        . "\t" . 'if(!defined("BLOCK")) exit;' . PHP_EOL
        . "\t" . 'class DBcfg {' . PHP_EOL
        . "\t\t" . 'static $dbopt = array(' . PHP_EOL
        . "\t\t\t" . '\'db_host\' => "[host]",' . PHP_EOL
        . "\t\t\t" . '\'db_user\' => "[login]",' . PHP_EOL
        . "\t\t\t" . '\'db_pass\' => "[passwd]",' . PHP_EOL
        . "\t\t\t" . '\'db_name\' => "[login]",' . PHP_EOL
        . "\t\t\t" . '\'db_prefix\' => "bp"' . PHP_EOL
        . "\t\t" . ');' . PHP_EOL
        . "\t" . '}' . PHP_EOL
        . "\t" . '@mb_internal_encoding("UTF-8");' . PHP_EOL
        . "\t" . '@date_default_timezone_set("Europe/Moscow");' . PHP_EOL
        . "\t" . '$site_name = "Online Market";' . PHP_EOL
        . "\t" . '$url = "http://' . $aData['subdomain'] . '.' . $aData['domain'] . '/";' . PHP_EOL
        . "\t" . '$num_page = "15";' . PHP_EOL
        . "\t" . '$cron = "874319";' . PHP_EOL
        . "\n\t" . '$wmr_on = "0";' . PHP_EOL
        . "\t" . '$purse = "";' . PHP_EOL
        . "\t" . '$secret_key = "";' . PHP_EOL
        . "\t" . '$uni_on = "0";' . PHP_EOL
        . "\t" . '$uni_purse = "";' . PHP_EOL
        . "\t" . '$uni_secret_key = "";' . PHP_EOL
        . "\t" . '$robo_on = "0";' . PHP_EOL
        . "\t" . '$robo_login = "";' . PHP_EOL
        . "\t" . '$robo_pass1 = "";' . PHP_EOL
        . "\t" . '$robo_pass2 = "";' . PHP_EOL
        . "\t" . '$curr = "руб.";' . PHP_EOL
        . "\t" . '$adm_login = "admin";' . PHP_EOL
        . "\t" . '$adm_pass = "[passwd]";' . PHP_EOL
        . "\t" . '$adm_ip = "";' . PHP_EOL
        . "\t" . '$to = "' . $us['mail'] . '";' . PHP_EOL
        . "\t" . '$vk = "' . $vk . '";' . PHP_EOL
        . "\t" . '$skype = "' . $skype . '";' . PHP_EOL
        . "\t" . 'require_once "core.php";' . PHP_EOL
        . "\t" . '$db = new DataBase();' . PHP_EOL
        . "\t" . '$eng = new Engine();' . PHP_EOL
        . "\t" . '$at = new Auth();' . PHP_EOL
        . '?>';

    include(LIB . 'web/free.php');

    web::install($aData, $name_mcache);
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

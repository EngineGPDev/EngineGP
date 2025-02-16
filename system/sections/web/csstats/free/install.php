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
    include(LIB . 'web/free.php');

    $aData = [];

    $aData['subdomain'] = isset($_POST['subdomain']) ? strtolower($_POST['subdomain']) : System::outjs(['e' => 'Необходимо указать адрес.'], $nmch);
    $aData['domain'] = isset($_POST['domain']) ? strtolower($_POST['domain']) : System::outjs(['e' => 'Необходимо выбрать домен.'], $nmch);
    $aData['desing'] = isset($_POST['desing']) ? strtolower($_POST['desing']) : System::outjs(['e' => 'Необходимо выбрать шаблон.'], $nmch);

    $aData['type'] = $url['subsection'];
    $aData['server'] = array_merge($server, ['id' => $id]);

    $aData['config_sql'] = 'csstats_key "[key]"' . PHP_EOL
        . 'csstats_url "[domain]"' . PHP_EOL
        . 'csstats_sort "-2"' . PHP_EOL
        . 'csstats_double "0"' . PHP_EOL
        . 'csstats_host "[host]"' . PHP_EOL
        . 'csstats_user "[login]"' . PHP_EOL
        . 'csstats_pass "[passwd]"' . PHP_EOL
        . 'csstats_db "[login]"' . PHP_EOL
        . 'csstats_table_players "csstats_players"' . PHP_EOL
        . 'csstats_table_settings "csstats_settings"' . PHP_EOL
        . 'csstats_delete_time "15 120 2"' . PHP_EOL
        . 'csstats_insert_time "60 1"' . PHP_EOL
        . 'csstats_update_time "30 0"' . PHP_EOL
        . 'csstats_slow "0"';

    $aData['config_php'] = '<?php' . PHP_EOL
        . '    $show_top = "aropbcdefghijklmnq";' . PHP_EOL
        . '    $show_pages = 50;' . PHP_EOL
        . '    $DefaultSort = "place";' . PHP_EOL
        . '    $center = 1;' . PHP_EOL
        . '    $Pogony[0] = 46;' . PHP_EOL
        . '    $Pogony[1] = 16;' . PHP_EOL
        . '    $csstats_host = "[host]";' . PHP_EOL
        . '    $csstats_user = "[login]";' . PHP_EOL
        . '    $csstats_pass = "[passwd]";' . PHP_EOL
        . '    $csstats_db = "[login]";' . PHP_EOL
        . '    $csstats_table_players = "csstats_players";' . PHP_EOL
        . '    $csstats_table_settings = "csstats_settings";' . PHP_EOL
        . '    $server_address[] = "[address]";' . PHP_EOL
        . '?>';

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

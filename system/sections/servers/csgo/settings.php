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

$sql->query('SELECT `uid`, `unit`, `tarif`, `pack`, `ddos` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
$server = array_merge($server, $sql->get());

$html->nav($server['address'], $cfg['http'] . 'servers/id/' . $id);

$aSub = ['start', 'server', 'admins', 'bans', 'firewall', 'crontab', 'startlogs', 'debug', 'logs', 'smlogs', 'pack', 'file', 'antiddos', 'api'];

// Если выбран подраздел
if (isset($url['subsection']) and in_array($url['subsection'], $aSub)) {
    $html->nav('Настройки', $cfg['http'] . 'servers/id/' . $id . '/section/settings');

    if ($go) {
        $nmch = System::rep_act('server_settings_go_' . $id, 10);
    }

    if (in_array($url['subsection'], $aRouteSub['settings'])) {
        include(SEC . 'servers/games/settings/' . $url['subsection'] . '.php');
    } else {
        include(SEC . 'servers/' . $server['game'] . '/settings/' . $url['subsection'] . '.php');
    }
} else {
    $html->nav('Настройки');

    if ($mcache->get('server_settings_' . $id) != '') {
        $html->arr['main'] = $mcache->get('server_settings_' . $id);
    } else {
        $sql->query('SELECT `name`, `packs` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
        $tarif = $sql->get();

        $aEditslist = 1;
        include(DATA . 'filedits.php');

        // Построение списка доступных сборок
        $aPacks = System::b64djs($tarif['packs']);

        $packs = '<option value="' . $server['pack'] . '">' . $aPacks[$server['pack']] . '</option>';
        unset($aPacks[$server['pack']]);

        foreach ($aPacks as $pack => $desc) {
            $packs .= '<option value="' . $pack . '">' . $desc . '</option>';
        }

        $antiddos = '<option value="0">Индивидуальная защита отключена</option>'
            . '<option value="1">Индивидуальная защита (Заблокировать всех кроме: RU, UA)</option>'
            . '<option value="2">Индивидуальная защита (Заблокировать всех кроме: AM, BY, UA, RU, KZ)</option>';

        include(SEC . 'servers/' . $server['game'] . '/settings/start.php');

        $html->get('settings', 'sections/servers/' . $server['game']);
        $html->set('id', $id);
        $html->set('packs', $packs);
        $html->set('antiddos', str_replace($server['ddos'], $server['ddos'] . '" selected="select', $antiddos));
        $html->set('start', $html->arr['start']);
        if (isset($html->arr['edits'])) {
            $html->set('edits', $html->arr['edits']);
            $html->unit('edits', 1);
        } else {
            $html->unit('edits');
        }

        $sql->query('SELECT `key` FROM `api` WHERE `server`="' . $id . '" LIMIT 1');
        if ($sql->num()) {
            $api = $sql->get();

            $html->set('api', $api['key']);
            $html->unit('api', 1, 1);
        } else {
            $html->unit('api', 0, 1);
        }
        $html->pack('main');

        $mcache->set('server_settings_' . $id, $html->arr['main'], false, 60);
    }
}

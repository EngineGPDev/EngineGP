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

$html->nav('Бан листы');

$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
$unit = $sql->get();

$sql->query('SELECT `install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
$tarif = $sql->get();

include(LIB . 'ssh.php');

if (!$ssh->auth($unit['passwd'], $unit['address'])) {
    System::back($cfg['http'] . 'servers/id/' . $id . '/section/settings');
}

// Путь к файлам (banned_user.cfg / banned_ip.cfg)
$folder = $tarif['install'] . $server['uid'] . '/csgo';

// Если бан/разбан/проверка
if ($go) {
    $aData = [];

    $aData['value'] = isset($_POST['value']) ? trim($_POST['value']) : System::outjs(['e' => System::text('servers', 'bans')], $nmch);
    $aData['userid'] = isset($_POST['userid']) ? System::int($_POST['userid']) : false;
    $aData['amxbans'] = isset($_POST['amxbans']) ? true : false;

    // Проверка входных данных
    if (System::valid($aData['value'], 'steamid') and System::valid($aData['value'], 'steamid3') and System::valid($aData['value'], 'ip')) {
        System::outjs(['e' => System::text('servers', 'bans')], $nmch);
    }

    // Если указан steamid
    if (System::valid($aData['value'], 'ip')) {
        // бан
        if (isset($url['action']) and $url['action'] == 'ban') {
            // Если включен sourcebans
            if ($aData['amxbans'] and $aData['userid']) {
                $ssh->set("sudo -u server" . $server['uid'] . " tmux send-keys -t s_" . $server['uid'] . " \"sm_ban 0 " . $aData['userid'] . " EGP\" C-m");
            } else {
                $ssh->set("sudo -u server" . $server['uid'] . " tmux send-keys -t s_" . $server['uid'] . " \"banid 0.0 " . $aData['value'] . " kick\" C-m");
            }

            $ssh->set('cd ' . $folder . ' && sudo -u server' . $server['uid'] . ' fgrep ' . $aData['value'] . ' banned_user.cfg | awk \'{print $3}\'');

            if ($aData['value'] != trim($ssh->get())) {
                $ssh->set('sudo -u server' . $server['uid'] . ' sh -c "echo \"banid 0.0 ' . $aData['value'] . '\" >> ' . $folder . '/banned_user.cfg"');
            }

            System::outjs(['s' => 'ok'], $nmch);

            // разбан
        } elseif (isset($url['action']) and $url['action'] == 'unban') {
            // Убираем запись из banned_user.cfg
            $ssh->set('cd ' . $folder . ' && sudo -u server' . $server['uid'] . ' sh -c "cat banned_user.cfg | grep -v ' . $aData['value'] . ' > temp_banned.cfg; echo "" >> temp_banned.cfg && cat temp_banned.cfg > banned_user.cfg; rm temp_banned.cfg"');

            // Если включен sourcebans
            if ($aData['amxbans']) {
                $ssh->set("sudo -u server" . $server['uid'] . " tmux send-keys -t s_" . $server['uid'] . " \"sm_unban " . $aData['value'] . "\" C-m");
            } else {
                $ssh->set("sudo -u server" . $server['uid'] . " tmux send-keys -t s_" . $server['uid'] . " \"removeid " . $aData['value'] . "\" C-m");
                $ssh->set("sudo -u server" . $server['uid'] . " tmux send-keys -t s_" . $server['uid'] . " \"writeid\" C-m");
            }

            System::outjs(['s' => 'ok'], $nmch);
            // проверка
        } else {
            $ssh->set('cd ' . $folder . ' && sudo -u server' . $server['uid'] . ' fgrep ' . $aData['value'] . ' banned_user.cfg | awk \'{print $3}\'');

            if ($aData['value'] == trim($ssh->get())) {
                System::outjs(['ban' => 'Данный SteamID <u>найден</u> в файле banned_user.cfg'], $nmch);
            }

            System::outjs(['unban' => 'Данный SteamID <u>не найден</u> в файле banned_user.cfg'], $nmch);
        }
    } else {
        // бан
        if (isset($url['action']) and $url['action'] == 'ban') {
            // Если включен sourcebans
            if ($aData['amxbans']) {
                $ssh->set("sudo -u server" . $server['uid'] . " tmux send-keys -t s_" . $server['uid'] . " \"sm_ban 0 " . $aData['value'] . " EGP\" C-m");
            } else {
                $ssh->set("sudo -u server" . $server['uid'] . " tmux send-keys -t s_" . $server['uid'] . " \"addip 0.0 " . $aData['value'] . " EGP\" C-m");
            }

            $ssh->set('cd ' . $folder . ' && sudo -u server' . $server['uid'] . ' fgrep ' . $aData['value'] . ' banned_ip.cfg | awk \'{print $3}\'');

            if ($aData['value'] != trim($ssh->get())) {
                $ssh->set('sudo -u server' . $server['uid'] . ' sh -c "echo \"addip 0.0 ' . $aData['value'] . '\" >> ' . $folder . '/banned_ip.cfg"');
            }

            System::outjs(['s' => 'ok'], $nmch);

            // разбан
        } elseif (isset($url['action']) and $url['action'] == 'unban') {
            // Убираем запись из banned_ip.cfg
            $ssh->set('cd ' . $folder . ' && sudo -u server' . $server['uid'] . ' sh -c "cat banned_ip.cfg | grep -v ' . $aData['value'] . ' > temp_listip.cfg; echo "" >> temp_listip.cfg && cat temp_listip.cfg > banned_ip.cfg; rm temp_listip.cfg"');

            // Если включен sourcebans
            if ($aData['amxbans']) {
                $ssh->set("sudo -u server" . $server['uid'] . " tmux send-keys -t s_" . $server['uid'] . " \"amx_unban " . $aData['value'] . "\" C-m");
            } else {
                $ssh->set("sudo -u server" . $server['uid'] . " tmux send-keys -t s_" . $server['uid'] . " \"removeip " . $aData['value'] . "\" C-m");
                $ssh->set("sudo -u server" . $server['uid'] . " tmux send-keys -t s_" . $server['uid'] . " \"writeip\" C-m");
            }

            System::outjs(['s' => 'ok'], $nmch);
            // проверка
        } else {
            $ssh->set('cd ' . $folder . ' && sudo -u server' . $server['uid'] . ' fgrep ' . $aData['value'] . ' banned_ip.cfg | awk \'{print $3}\'');

            if ($aData['value'] == trim($ssh->get())) {
                System::outjs(['ban' => 'Данный IP <u>найден</u> в файле banned_ip.cfg'], $nmch);
            }

            System::outjs(['unban' => 'Данный IP <u>не найден</u> в файле banned_ip.cfg'], $nmch);
        }
    }
}

// Содержимое banned_user.cfg
$ssh->set('cd ' . $folder . ' && cat banned_user.cfg | awk \'{print $3}\' | egrep "^\[U:[01]:[0-9]{3,12}\]$"');
$aBanned = explode("\n", $ssh->get());

// Содержимое banned_ip.cfg
$ssh->set('cd ' . $folder . ' && cat banned_ip.cfg | awk \'{print $3}\' | egrep "(25[0-5]|2[0-4][0-9]|[0-1][0-9]{2}|[0-9]{2}|[0-9])(\.(25[0-5]|2[0-4][0-9]|[0-1][0-9]{2}|[0-9]{2}|[0-9])){3}"');
$aListip = explode("\n", $ssh->get());

if (isset($aBanned[count($aBanned) - 1]) and $aBanned[count($aBanned) - 1] == '') {
    unset($aBanned[count($aBanned) - 1]);
}

if (isset($aListip[count($aListip) - 1]) and $aListip[count($aListip) - 1] == '') {
    unset($aListip[count($aListip) - 1]);
}

// Построение списка забаненых по steamid
foreach ($aBanned as $line => $steam) {
    $html->get('bans_list', 'sections/servers/games/settings');

    $html->set('value', trim($steam));

    $html->pack('banned');
}

// Построение списка забаненых по ip
foreach ($aListip as $line => $ip) {
    $html->get('bans_list', 'sections/servers/games/settings');

    $html->set('value', trim($ip));

    $html->pack('listip');
}

$html->get('bans', 'sections/servers/' . $server['game'] . '/settings');

$html->set('id', $id);
$html->set('banned', $html->arr['banned'] ?? '');
$html->set('listip', $html->arr['listip'] ?? '');

$html->pack('main');

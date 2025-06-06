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
use EngineGP\Model\Game;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

$sql->query('SELECT `uid`, `slots`, `slots_start`, `map_start`, `vac`, `fps`, `fastdl`, `autorestart`, `pingboost` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
$server = array_merge($server, $sql->get());

$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
$unit = $sql->get();

$sql->query('SELECT `install`, `fps`, `price` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
$tarif = $sql->get();

include(LIB . 'games/tarifs.php');
include(LIB . 'games/' . $server['game'] . '/tarif.php');

// Вывод списка карт
if (isset($url['maps'])) {
    Game::maplist($id, $unit, $tarif['install'] . $server['uid'] . '/cstrike/maps', $server['map_start'], false);
}

// Сохранение
if ($go and $url['save']) {
    $value = isset($url['value']) ? System::int($url['value']) : System::outjs(['s' => 'ok'], $nmch);

    switch ($url['save']) {
        case 'map':
            $map = isset($url['value']) ? trim($url['value']) : System::outjs(['s' => 'ok'], $nmch);

            if ($map != $server['map_start']) {
                Game::maplist($id, $unit, $tarif['install'] . $server['uid'] . '/cstrike/maps', $map, true, $nmch);
            }

            $mcache->delete('server_settings_' . $id);
            System::outjs(['s' => 'ok'], $nmch);

            // no break
        case 'vac':
            if ($value != $server['vac']) {
                $sql->query('UPDATE `servers` set `vac`="' . $value . '" WHERE `id`="' . $id . '" LIMIT 1');
            }

            $mcache->delete('server_settings_' . $id);
            System::outjs(['s' => 'ok'], $nmch);

            // no break
        case 'slots':
            $slots = $value > $server['slots'] ? $server['slots'] : $value;
            $slots = $value < 2 ? 2 : $slots;

            if ($slots != $server['slots_start']) {
                $sql->query('UPDATE `servers` set `slots_start`="' . $slots . '" WHERE `id`="' . $id . '" LIMIT 1');
            }

            $mcache->delete('server_settings_' . $id);
            System::outjs(['s' => 'ok'], $nmch);

            // no break
        case 'autorestart':
            if ($value != $server['autorestart']) {
                $sql->query('UPDATE `servers` set `autorestart`="' . $value . '" WHERE `id`="' . $id . '" LIMIT 1');
            }

            $mcache->delete('server_settings_' . $id);
            System::outjs(['s' => 'ok'], $nmch);

            // no break
        case 'fps':
            if (!tarif::price($tarif['price']) and in_array($value, explode(':', $tarif['fps']))) {
                $sql->query('UPDATE `servers` set `fps`="' . $value . '" WHERE `id`="' . $id . '" LIMIT 1');
            }

            $mcache->delete('server_settings_' . $id);
            System::outjs(['s' => 'ok'], $nmch);

            // no break
        case 'pingboost':
            if ($cfg['change_pingboost'] and in_array($value, [1, 2, 3])) {
                $sql->query('UPDATE `servers` set `pingboost`="' . $value . '" WHERE `id`="' . $id . '" LIMIT 1');
            }

            $mcache->delete('server_settings_' . $id);
            System::outjs(['s' => 'ok'], $nmch);

            // no break
        case 'fastdl':
            include(LIB . 'ssh.php');

            if (!$ssh->auth($unit['passwd'], $unit['address'])) {
                System::outjs(['e' => System::text('error', 'ssh')], $nmch);
            }

            if ($value) {
                $fastdl = 'sv_downloadurl "http://' . System::first(explode(':', $unit['address'])) . ':8080/fast_' . $server['uid'] . '"' . PHP_EOL
                    . 'sv_consistency 1' . PHP_EOL
                    . 'sv_allowupload 1' . PHP_EOL
                    . 'sv_allowdownload 1';

                // Временый файл
                $temp = System::temp($fastdl);

                $ssh->setfile($temp, $tarif['install'] . $server['uid'] . '/cstrike/fastdl.cfg');
                $ssh->set('chmod 0644' . ' ' . $tarif['install'] . $server['uid'] . '/cstrike/fastdl.cfg');

                $ssh->set('chown server' . $server['uid'] . ':servers ' . $tarif['install'] . $server['uid'] . '/cstrike/fastdl.cfg;'
                    . 'ln -s ' . $tarif['install'] . $server['uid'] . '/cstrike /var/nginx/fast_' . $server['uid'] . ';'
                    . 'sed -i ' . "'s/exec fastdl.cfg//g'" . ' ' . $tarif['install'] . $server['uid'] . '/cstrike/server.cfg;'
                    . 'echo "exec fastdl.cfg" >> ' . $tarif['install'] . $server['uid'] . '/cstrike/server.cfg');

                unlink($temp);
            } else {
                $ssh->set('sed -i ' . "'s/exec fastdl.cfg//g'" . ' ' . $tarif['install'] . $server['uid'] . '/cstrike/server.cfg;'
                        . 'rm ' . $tarif['install'] . $server['uid'] . '/cstrike/fastdl.cfg; rm /var/nginx/fast_' . $server['uid']);
            }

            $sql->query('UPDATE `servers` set `fastdl`="' . $value . '" WHERE `id`="' . $id . '" LIMIT 1');

            $mcache->delete('server_settings_' . $id);
            System::outjs(['s' => 'ok'], $nmch);
    }
}

// Генерация списка слот
$slots = '';

for ($slot = 2; $slot <= $server['slots']; $slot += 1) {
    $slots .= '<option value="' . $slot . '">' . $slot . ' шт.</option>';
}

// Античит VAC
$vac = $server['vac'] ? '<option value="1">Включен</option><option value="0">Выключен</option>' : '<option value="0">Выключен</option><option value="1">Включен</option>';

// Быстрая скачака
$fastdl = $server['fastdl'] ? '<option value="1">Включен</option><option value="0">Выключен</option>' : '<option value="0">Выключен</option><option value="1">Включен</option>';

// Авторестарт при зависании
$autorestart = $server['autorestart'] ? '<option value="1">Включен</option><option value="0">Выключен</option>' : '<option value="0">Выключен</option><option value="1">Включен</option>';

$fps = '<option value="' . $server['fps'] . '">' . $server['fps'] . ' FPS</option>';

if (!tarif::price($tarif['price'])) {
    $aFps = explode(':', $tarif['fps']);

    unset($aFps[array_search($server['fps'], $aFps)]);

    if (count($aFps)) {
        foreach ($aFps as $value) {
            $fps .= '<option value="' . $value . '">' . $value . ' FPS</option>';
        }
    }
}

if ($cfg['change_pingboost']) {
    $pingboost = str_replace($server['pingboost'] . '"', $server['pingboost'] . '" selected="select"', '<option value="1">PINGBOOST 1</option><option value="2">PINGBOOST 2</option><option value="3">PINGBOOST 3</option>');
}

$html->get('start', 'sections/servers/' . $server['game'] . '/settings');

$html->set('id', $id);
$html->set('map', $server['map_start']);
$html->set('vac', $vac);
$html->set('fastdl', $fastdl);
$html->set('autorestart', $autorestart);
$html->set('slots', str_replace('"' . $server['slots_start'] . '"', '"' . $server['slots_start'] . '" selected="select"', $slots));

if ($cfg['change_pingboost']) {
    $html->unit('pingboost', true);
    $html->set('pingboost', $pingboost);
} else {
    $html->unit('pingboost');
}

if (!tarif::price($tarif['price'])) {
    $html->unit('fps', true);
    $html->set('fps', $fps);
} else {
    $html->unit('fps');
}

$html->pack('start');

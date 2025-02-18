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

$sql->query('SELECT `uid`, `slots`, `slots_start`, `autorestart` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
$server = array_merge($server, $sql->get());

// Сохранение
if ($go and $url['save']) {
    $value = isset($url['value']) ? System::int($url['value']) : System::outjs(['s' => 'ok'], $nmch);

    switch ($url['save']) {
        case 'slots':
            $slots = $value > $server['slots'] ? $server['slots'] : $value;
            $slots = $value < 2 ? 2 : $slots;

            if ($slots != $server['slots_start']) {
                $sql->query('UPDATE `servers` set `slots_start`="' . $slots . '" WHERE `id`="' . $id . '" LIMIT 1');
            }

            $mcache->delete('server_settings_' . $id);
            System::outjs(['s' => 'ok'], $nmch);
    }
}

// Генерация списка слот
$slots = '';

for ($slot = 2; $slot <= $server['slots']; $slot += 1) {
    $slots .= '<option value="' . $slot . '">' . $slot . ' шт.</option>';
}

// Авторестарт при зависании
$autorestart = $server['autorestart'] ? '<option value="1">Включен</option><option value="0">Выключен</option>' : '<option value="0">Выключен</option><option value="1">Включен</option>';

$html->get('start', 'sections/servers/' . $server['game'] . '/settings');

$html->set('id', $id);
$html->set('autorestart', $autorestart);
$html->set('slots', str_replace('"' . $server['slots_start'] . '"', '"' . $server['slots_start'] . '" selected="select"', $slots));

$html->pack('start');

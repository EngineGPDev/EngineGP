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

$sql->query('SELECT `uid`, `slots`, `slots_start`, `autorestart` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
$server = array_merge($server, $sql->get());

include(LIB . 'games/games.php');

// Сохранение
if ($go and $url['save']) {
    $value = isset($url['value']) ? sys::int($url['value']) : sys::outjs(['s' => 'ok'], $nmch);

    switch ($url['save']) {
        case 'slots':
            $slots = $value > $server['slots'] ? $server['slots'] : $value;
            $slots = $value < 2 ? 2 : $slots;

            if ($slots != $server['slots_start']) {
                $sql->query('UPDATE `servers` set `slots_start`="' . $slots . '" WHERE `id`="' . $id . '" LIMIT 1');
            }

            $mcache->delete('server_settings_' . $id);
            sys::outjs(['s' => 'ok'], $nmch);

            // no break
        case 'autorestart':
            if ($value != $server['autorestart']) {
                $sql->query('UPDATE `servers` set `autorestart`="' . $value . '" WHERE `id`="' . $id . '" LIMIT 1');
            }

            $mcache->delete('server_settings_' . $id);
            sys::outjs(['s' => 'ok'], $nmch);
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

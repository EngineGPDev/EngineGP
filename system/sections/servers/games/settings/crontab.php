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

$html->nav('Планировщик задач');

$sql->query('SELECT `autostop` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
$server = array_merge($server, $sql->get());

if ($go) {
    $sql->query('SELECT `address`, `passwd` FROM `panel` LIMIT 1');
    $panel = $sql->get();

    include(LIB . 'ssh.php');

    if (!$ssh->auth($panel['passwd'], $panel['address'])) {
        System::outjs(['e' => System::text('error', 'ssh')], $nmch);
    }

    // Удаление задания
    if (isset($url['action']) and $url['action'] == 'delete') {
        $task = isset($_POST['task']) ? System::int($_POST['task']) : System::outjs(['s' => 'ok'], $nmch);

        $sql->query('SELECT `cron` FROM `crontab` WHERE `id`="' . $task . '" AND `server`="' . $id . '" LIMIT 1');
        if (!$sql->num()) {
            System::outjs(['s' => 'ok'], $nmch);
        }

        $cron = $sql->get();

        $crontab = preg_quote($cron['cron'], '/');

        $ssh->set('crontab -l | grep -v "' . $crontab . '" | crontab -');

        $sql->query('DELETE FROM `crontab` WHERE `id`="' . $task . '" LIMIT 1');

        System::outjs(['s' => 'ok'], $nmch);
    }

    // Добавление задания
    $sql->query('SELECT `id` FROM `crontab` WHERE `server`="' . $id . '" LIMIT 5');
    if ($sql->num() == $cfg['crontabs']) {
        System::outjs(['e' => System::text('servers', 'crontab')], $nmch);
    }

    $data = [];

    $data['task'] = $_POST['task'] ?? 'start';

    $task = in_array($server['game'], ['samp', 'crmp']) ? ['start', 'restart', 'stop'] : ['start', 'restart', 'stop', 'console'];

    if (!in_array($data['task'], $task)) {
        $data['task'] = 'start';
    }

    $data['commands'] = isset($_POST['commands']) ? base64_encode(htmlspecialchars($_POST['commands'])) : '';
    $data['allhour'] = isset($_POST['allhour']) ? true : false;
    $data['hour'] = $_POST['hour'] ?? '00';
    $data['minute'] = $_POST['minute'] ?? '00';
    $data['week'] = (isset($_POST['week']) and is_array($_POST['week'])) ? $_POST['week'] : [];

    $sql->query('INSERT INTO `crontab` set `server`="' . $id . '"');
    $cid = $sql->id();

    $cron_rule = Game::crontab($id, $cid, $data);

    $ssh->set('(crontab -l; echo "' . $cron_rule . '") | crontab -');

    $time = Game::crontab_time($data['allhour'], $data['hour'], $data['minute']);
    $week = Game::crontab_week($data['week']);

    $sql->query('UPDATE `crontab` set `server`="' . $id . '", `task`="' . $data['task'] . '", `cron`="' . $cron_rule . '", `week`="' . $week . '", `time`="' . $time . '", `commands`="' . $data['commands'] . '" WHERE `id`="' . $cid . '" LIMIT 1');

    System::outjs(['s' => 'ok'], $nmch);
}

$aTask = [
    'start' => 'Включение сервера',
    'stop' => 'Выключение сервера',
    'restart' => 'Перезагрузка сервера',
    'console' => 'Отправка команд на сервер',
];

$sql->query('SELECT `id`, `task`, `week`, `time` FROM `crontab` WHERE `server`="' . $id . '" ORDER BY `id` ASC');
while ($crontab = $sql->get()) {
    $html->get('crontab_list', 'sections/servers/games/settings');
    $html->set('id', $crontab['id']);
    $html->set('task', $aTask[$crontab['task']]);
    $html->set('week', $crontab['week']);
    $html->set('time', $crontab['time']);
    $html->pack('crontab');
}

$html->get('crontab', 'sections/servers/' . $server['game'] . '/settings');
$html->set('id', $id);
$html->set('time', date('H:i:s', $start_point));

if ($server['autostop']) {
    $html->unit('!autostop');
} else {
    $html->unit('!autostop', 1);
}

$html->set('crontab', $html->arr['crontab'] ?? '');
$html->pack('main');

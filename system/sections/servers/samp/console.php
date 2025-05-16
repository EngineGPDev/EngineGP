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

$sql->query('SELECT `uid`, `unit`, `tarif`, `time_start` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
$server = array_merge($server, $sql->get());

if ($go) {
    $sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
    $unit = $sql->get();

    $sql->query('SELECT `install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
    $tarif = $sql->get();

    include(LIB . 'ssh.php');

    if (isset($server['status']) && $server['status'] == 'off') {
        System::out(System::text('servers', 'off'));
    }

    if (!$ssh->auth($unit['passwd'], $unit['address'])) {
        System::out(System::text('error', 'ssh'));
    }

    $dir = $tarif['install'] . $server['uid'] . '/';

    $filecmd = $dir . 'egpconsole.log';

    $command = 'sudo -u server' . $server['uid'] . ' tmux capture-pane -t s_' . $server['uid'] . ' \; save-buffer ' . $filecmd . ' && cat ' . $filecmd;

    $output = $ssh->get($command);

    System::out(htmlspecialchars($output, ENT_QUOTES | ENT_SUBSTITUTE, ''));
}

$html->nav($server['address'], $cfg['http'] . 'servers/id/' . $id);
$html->nav('Консоль');

$html->get('console', 'sections/servers/' . $server['game']);
$html->set('id', $id);
$html->pack('main');

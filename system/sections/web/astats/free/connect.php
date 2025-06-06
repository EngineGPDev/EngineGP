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

if (!$go) {
    exit;
}

$aData = [];

$aData['server'] = $_POST['server'] ?? System::outjs(['e' => 'Необходимо выбрать игровой сервер.'], $nmch);
$aData['type'] = $url['subsection'];

switch ($aWebInstall[$server['game']][$aData['type']]) {
    case 'server':
        $sql->query('SELECT `unit`, `domain`, `login` FROM `web` WHERE `type`="' . $aData['type'] . '" AND `server`="' . $server['id'] . '" LIMIT 1');

        break;

    case 'user':
        $sql->query('SELECT `unit`, `domain`, `login` FROM `web` WHERE `type`="' . $aData['type'] . '" AND `user`="' . $server['user'] . '" LIMIT 1');

        break;

    case 'unit':
        $sql->query('SELECT `unit`, `domain`, `login` FROM `web` WHERE `type`="' . $aData['type'] . '" AND `user`="' . $server['user'] . '" AND `unit`="' . $server['unit'] . '" LIMIT 1');

        break;
}

if (!$sql->num()) {
    System::outjs(['e' => 'Дополнительная услуга не установлена.'], $nmch);
}

$web = $sql->get();

$aData['config'] = '<?php' . PHP_EOL . 'return array(' . PHP_EOL;

$i = 0;

include(LIB . 'web/free.php');

foreach ($aData['server'] as $sid) {
    $sql->query('SELECT `id`, `uid`, `unit`, `user`, `address`, `port`, `game`, `ftp_use`, `ftp`, `ftp_root`, `ftp_passwd` FROM `servers` WHERE `id`="' . $sid . '" AND `user`="' . $server['user'] . '" AND `game`="cs" LIMIT 1');
    if (!$sql->num()) {
        continue;
    }

    $server = $sql->get();

    $address = $server['address'];

    if (!$server['ftp_use']) {
        continue;
    }

    if (!$server['ftp']) {
        System::outjs(['r' => 'Для подключения игрового сервера необходимо включить FileTP.', 'url' => $cfg['http'] . 'servers/id/' . $sid . '/section/filetp'], $nmch);
    }

    $stack = web::stack($aData, '`login`');

    if (!$sql->num($stack)) {
        continue;
    }

    // Каталог логов сервера
    $dir = ($cfg['ftp']['root'][$server['game']] || $server['ftp_root']) ? '/cstrike/addons/amxmodx/data' : '/addons/amxmodx/data';

    $i += 1;

    $aData['config'] .= $i . ' => array(' . PHP_EOL
        . '\'ip\' => \'' . $address[0] . '\',' . PHP_EOL
        . '\'port\' => ' . $address[1] . ',' . PHP_EOL
        . '\'engine\' => \'GOLDSOURCE\',' . PHP_EOL
        . '\'ftp_host\' => \'' . $address[0] . '\',' . PHP_EOL
        . '\'ftp_port\' => 21,' . PHP_EOL
        . '\'ftp_user\' => \'' . $server['uid'] . '\',' . PHP_EOL
        . '\'ftp_pass\' => \'' . $server['ftp_passwd'] . '\',' . PHP_EOL
        . '\'ftp_path\' => \'' . $dir . '\'' . PHP_EOL
        . '),' . PHP_EOL;

}

include(LIB . 'ssh.php');

$unit = web::unit($aWebUnit, $aData['type'], $web['unit']);

if (!$ssh->auth($unit['passwd'], $unit['address'])) {
    System::outjs(['e' => System::text('ssh', 'error')], $nmch);
}

// Директория дополнительной услуги
$install = $aWebUnit['install'][$aWebUnit['unit'][$aData['type']]][$aData['type']] . $web['domain'];

$temp = System::temp($aData['config'] . ');');
$ssh->setfile($temp, $install . '/config/servers.config.php');
$ssh->set('chmod 0644' . ' ' . $install . '/config/servers.config.php');

unlink($temp);

System::outjs(['s' => 'ok'], $nmch);

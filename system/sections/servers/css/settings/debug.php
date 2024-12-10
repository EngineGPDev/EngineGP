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

$html->nav('Отладочный лог');

$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
$unit = $sql->get();

$sql->query('SELECT `install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
$tarif = $sql->get();

include(LIB . 'ssh.php');

if (!$ssh->auth($unit['passwd'], $unit['address'])) {
    sys::back($cfg['http'] . 'servers/id/' . $id . '/section/settings');
}

// Чтение файла - oldstart.log
$file = $tarif['install'] . $server['uid'] . '/debug.log';

$ssh->set('echo "" >> ' . $file . ' && cat ' . $file . ' | grep "CRASH: " | grep -ve "^#\|^[[:space:]]*$"');

$html->get('debug', 'sections/servers/games/settings');

$html->set('log', htmlspecialchars($ssh->get()));

$html->pack('main');

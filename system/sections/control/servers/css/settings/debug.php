<?php
/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @link      https://github.com/EngineGPDev/EngineGP
 * @link      https://gitforge.ru/EngineGP/EngineGP
 * @copyright Copyright (c) Solovev Sergei <inbox@seansolovev.ru>
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE
 * @license   https://gitforge.ru/EngineGP/EngineGP/src/branch/main/LICENSE
 */

if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

$html->nav('Отладочный лог');

$sql->query('SELECT `address`, `passwd` FROM `control` WHERE `id`="' . $id . '" LIMIT 1');
$unit = $sql->get();

include(LIB . 'ssh.php');

if (!$ssh->auth($unit['passwd'], $unit['address']))
    sys::back($cfg['http'] . 'control/id/' . $id . '/server/' . $sid . '/section/settings');

// Чтение файла - oldstart.log
$file = '/servers/' . $server['uid'] . '/debug.log';

$ssh->set('echo "" >> ' . $file . ' && cat ' . $file . ' | grep "CRASH: " | grep -ve "^#\|^[[:space:]]*$"');

$html->get('debug', 'sections/control/servers/games/settings');

$html->set('log', htmlspecialchars($ssh->get(), NULL, ''));

$html->pack('main');

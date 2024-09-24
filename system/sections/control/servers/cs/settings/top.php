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

$sql->query('SELECT `address`, `passwd` FROM `control` WHERE `id`="' . $id . '" LIMIT 1');
$unit = $sql->get();

include(LIB . 'ssh.php');

if (!$ssh->auth($unit['passwd'], $unit['address'])) {
    sys::back($cfg['http'] . 'control/id/' . $id . '/server/' . $sid . '/section/settings');
}

// Удаление файла csstats.dat
$ssh->set('rm /servers' . $server['uid'] . '/cstrike/addons/amxmodx/data/csstats.dat');

if (in_array($server['status'], ['working', 'start', 'restart', 'change'])) {
    shell_exec('php cron.php ' . $cfg['cron_key'] . ' control_server_action restart cs ' . $sid);
}

sys::outjs(['s' => 'ok']);

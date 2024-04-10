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

$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
$unit = $sql->get();

$sql->query('SELECT `install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
$tarif = $sql->get();

include(LIB . 'ssh.php');

if (!$ssh->auth($unit['passwd'], $unit['address']))
    sys::back($cfg['http'] . 'servers/id/' . $id . '/section/settings');

// Удаление файла csstats.dat
$ssh->set('rm ' . $tarif['install'] . $server['uid'] . '/cstrike/addons/amxmodx/data/csstats.dat');

if (in_array($server['status'], array('working', 'start', 'restart', 'change'))) {
    shell_exec('php cron.php ' . $cfg['cron_key'] . ' server_action restart cs ' . $id);

    sys::outjs(array('s' => 'ok'));
}

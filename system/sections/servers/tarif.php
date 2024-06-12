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

if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

$sql->query('SELECT `uid`, `unit`, `user`, `tarif`, `address`, `port`, `game`, `status`, `slots`, `slots_start`, `plugins_use`, `ftp_use`, `console_use`, `stats_use`, `copy_use`, `web_use`, `time`, `test`, `fps`, `tickrate`, `ram`, `ram_fix` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
$server = $sql->get();

sys::nav($server, $id, 'tarif');

if ($server['status'] == 'blocked') {
    if ($go)
        sys::out('Раздел недоступен');

    include(SEC . 'servers/noaccess.php');
} else
    include(SEC . 'servers/' . $server['game'] . '/tarif.php');

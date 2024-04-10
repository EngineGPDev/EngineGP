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

$sql->query('SELECT `unit`, `address`, `game`, `status`, `plugins_use`, `ftp_use`, `console_use`, `stats_use`, `copy_use`, `web_use`, `time` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
$server = $sql->get();

sys::nav($server, $id, 'console');

include(sys::route($server, 'console', $go));

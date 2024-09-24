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

$sql->query('SELECT `game` FROM `control_servers` WHERE `id`="' . $sid . '" LIMIT 1');
$server = $sql->get();

include(LIB . 'control/' . $server['game'] . '/scan.php');

// Запрошена информация (статус, онлайн, название)
if (isset($url['mon'])) {
    sys::outjs(scan::mon($sid));
}

// Запрошена информация (статус, онлайн, название, игроки)
if (isset($url['fmon'])) {
    sys::outjs(scan::mon($sid, true));
}

// Запрошена информация (cpu, ram, hdd)
if (isset($url['resources'])) {
    sys::outjs(scan::resources($sid));
}

// Запрошена информация (работает, меняется карта, переустанавливается)
if (isset($url['status'])) {
    sys::outjs(scan::status($sid));
}

exit;

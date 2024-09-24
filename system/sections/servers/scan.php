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

$sql->query('SELECT `game` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
$server = $sql->get();

include(LIB . 'games/' . $server['game'] . '/scan.php');

// Запрошена информация (статус, онлайн, название)
if (isset($url['mon'])) {
    sys::outjs(scan::mon($id));
}

// Запрошена информация (статус, онлайн, название, игроки)
if (isset($url['fmon'])) {
    sys::outjs(scan::mon($id, true));
}

// Запрошена информация (cpu, ram, hdd)
if (isset($url['resources'])) {
    sys::outjs(scan::resources($id));
}

// Запрошена информация (работает, меняется карта, переустанавливается)
if (isset($url['status'])) {
    sys::outjs(scan::status($id));
}

exit;

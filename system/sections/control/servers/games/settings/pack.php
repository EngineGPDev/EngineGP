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

$aPacks = $cfg['control_packs'][$server['game']];

$pack = isset($url['pack']) ? $url['pack'] : exit;

if ($pack == $server['pack']) {
    sys::outjs(array('s' => 'ok'));
}

// Проверка сборки
if (!array_key_exists($pack, $aPacks)) {
    sys::outjs(array('e' => 'Сборка не найдена.'));
}

$sql->query('UPDATE `control_servers` set `pack`="' . $pack . '" WHERE `id`="' . $sid . '" LIMIT 1');

sys::outjs(array('s' => 'ok'), 'ctrl_server_settings_' . $sid);

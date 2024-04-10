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

$aPacks = $cfg['control_packs'][$server['game']];

$pack = isset($url['pack']) ? $url['pack'] : exit;

if ($pack == $server['pack'])
    sys::outjs(array('s' => 'ok'));

// Проверка сборки
if (!array_key_exists($pack, $aPacks))
    sys::outjs(array('e' => 'Сборка не найдена.'));

$sql->query('UPDATE `control_servers` set `pack`="' . $pack . '" WHERE `id`="' . $sid . '" LIMIT 1');

sys::outjs(array('s' => 'ok'), 'ctrl_server_settings_' . $sid);

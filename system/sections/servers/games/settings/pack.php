<?php
/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @copyright Copyright (c) 2018-present Solovev Sergei <inbox@seansolovev.ru>
 *
 * @link      https://github.com/EngineGPDev/EngineGP for the canonical source repository
 * @link      https://gitforge.ru/EngineGP/EngineGP for the canonical source repository
 *
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE MIT License
 * @license   https://gitforge.ru/EngineGP/EngineGP/src/branch/main/LICENSE MIT License
 */

if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

$sql->query('SELECT `packs` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
$tarif = $sql->get();

$aPacks = sys::b64djs($tarif['packs'], true);

$pack = isset($url['pack']) ? $url['pack'] : exit;

if ($pack == $server['pack'])
    sys::outjs(array('s' => 'ok'));

// Проверка сборки
if (!array_key_exists($pack, $aPacks))
    sys::outjs(array('e' => 'Сборка не найдена.'));

$sql->query('UPDATE `servers` set `pack`="' . $pack . '" WHERE `id`="' . $id . '" LIMIT 1');

sys::outjs(array('s' => 'ok'), 'server_settings_' . $id);

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

$sql->query('SELECT `id` FROM `servers` WHERE `tarif`="' . $id . '" LIMIT 1');
if ($sql->num())
    sys::outjs(array('e' => 'Нельзя удалить тариф с серверами.'));

$sql->query('DELETE FROM `tarifs` WHERE `id`="' . $id . '" LIMIT 1');

sys::outjs(array('s' => 'ok'));

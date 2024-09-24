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

$sql->query('SELECT `id` FROM `servers` WHERE `tarif`="' . $id . '" LIMIT 1');
if ($sql->num()) {
    sys::outjs(['e' => 'Нельзя удалить тариф с серверами.']);
}

$sql->query('DELETE FROM `tarifs` WHERE `id`="' . $id . '" LIMIT 1');

sys::outjs(['s' => 'ok']);

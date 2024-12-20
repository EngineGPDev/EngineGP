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

if (!isset($url['plugin']) || !isset($url['ip']) || !isset($url['port'])) {
    sys::out('bad params');
}

$address = $url['ip'] . ':' . $url['port'];

if (sys::valid($address, 'other', $aValid['address'])) {
    sys::out('bad address');
}

if (sys::valid($url['plugin'], 'md5')) {
    sys::out('bad plugin');
}

$sql->query('SELECT `id` FROM `servers` WHERE `address`="' . $address . '" LIMIT 1');
if (!$sql->num()) {
    sys::out('bad server');
}

$server = $sql->get();

$sql->query('SELECT `id` FROM `plugins_buy` WHERE `key`="' . $url['plugin'] . '" AND `server`="' . $server['id'] . '" LIMIT 1');
if ($sql->num()) {
    sys::out('true');
}

sys::out('false');

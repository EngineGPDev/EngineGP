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

if (!$go || !isset($url['server'])) {
    exit;
}

include(LIB . 'web/free.php');

$aData = [];

$aData['server'] = sys::int($url['server']);
$aData['type'] = $url['subsection'];
$aData['user'] = $server['user'];
$aData['file'] = params::$aDirGame[$server['game']] . '/addons/sourcemod/configs/databases.cfg';
$aData['cfg'] = params::$aDirGame[$server['game']] . '/cfg/server.cfg';

$aData['orcfg'] = [];
$aData['orsql'] = [];

web::connect($aData, $nmch);

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

if (!$go || !isset($url['server']))
    exit;

$key = isset($url['key']) ? $url['key'] : exit;

if (isset($key[32]))
    sys::outjs(array('e' => 'Длина ключа не должна превышать 32 символа.'), $nmch);

include(LIB . 'web/free.php');

$aData = array();

$aData['server'] = sys::int($url['server']);
$aData['type'] = $url['subsection'];
$aData['user'] = $server['user'];
$aData['file'] = 'cstrike/addons/amxmodx/configs/csstats_mysql.cfg';
$aData['cfg'] = 'cstrike/server.cfg';

$aData['orcfg'] = array(
    'key' => $key
);

$aData['orsql'] = array();

web::connect($aData, $nmch);

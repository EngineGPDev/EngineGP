<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

if (!$go || !isset($url['server']))
    exit;

require(LIB . 'web/free.php');

$aData = array();

$aData['server'] = sys::int($url['server']);
$aData['type'] = $url['subsection'];
$aData['user'] = $server['user'];
$aData['file'] = params::$aDirGame[$server['game']] . '/addons/sourcemod/configs/databases.cfg';
$aData['cfg'] = params::$aDirGame[$server['game']] . '/cfg/server.cfg';

$aData['orcfg'] = array();
$aData['orsql'] = array();

web::connect($aData, $nmch);

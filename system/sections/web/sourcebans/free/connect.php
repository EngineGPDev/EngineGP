<?php

/*
 * Copyright 2018-2025 Solovev Sergei
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use EngineGP\System;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

if (!$go || !isset($url['server'])) {
    exit;
}

include(LIB . 'web/free.php');

$aData = [];

$aData['server'] = System::int($url['server']);
$aData['type'] = $url['subsection'];
$aData['user'] = $server['user'];
$aData['file'] = params::$aDirGame[$server['game']] . '/addons/sourcemod/configs/databases.cfg';
$aData['cfg'] = params::$aDirGame[$server['game']] . '/cfg/server.cfg';

$aData['orcfg'] = [];
$aData['orsql'] = [];

web::connect($aData, $nmch);

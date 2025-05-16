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

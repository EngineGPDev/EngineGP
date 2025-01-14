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

$sql->query('SELECT `game` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
$server = $sql->get();

include(LIB . 'games/' . $server['game'] . '/scan.php');

// Запрошена информация (статус, онлайн, название)
if (isset($url['mon'])) {
    sys::outjs(scan::mon($id));
}

// Запрошена информация (статус, онлайн, название, игроки)
if (isset($url['fmon'])) {
    sys::outjs(scan::mon($id, true));
}

// Запрошена информация (cpu, ram, hdd)
if (isset($url['resources'])) {
    sys::outjs(scan::resources($id));
}

// Запрошена информация (работает, меняется карта, переустанавливается)
if (isset($url['status'])) {
    sys::outjs(scan::status($id));
}

exit;

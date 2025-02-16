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

$sql->query('SELECT `id` FROM `copy` WHERE `server`="' . $id . '" ORDER BY `id` DESC LIMIT 3');
if ($sql->num() > 2) {
    System::outjs(['e' => 'Для создания новой копии необходимо удалить старые.'], $nmch);
}

$sql->query('SELECT `id` FROM `copy` WHERE `server`="' . $id . '" AND `status`="0" LIMIT 1');
if ($sql->num()) {
    System::outjs(['e' => 'Для создания новой копии дождитесь создания предыдущей.'], $nmch);
}

$aSel = [];

$aData = $_POST['copy'] ?? System::outjs(['e' => 'Для создания копии необходимо выбрать директории/файлы.'], $nmch);

foreach (params::$section_copy[$server['game']]['aCopy'] as $name => $info) {
    if (!isset($aData['\'' . $name . '\''])) {
        continue;
    }

    $aSel[] = $name;
}

if (!count($aSel)) {
    System::outjs(['e' => 'Для создания копии необходимо выбрать директории/файлы.'], $nmch);
}

$copy = '';
$info = '';
$plugins = '';

foreach ($aSel as $name) {
    $copy .= isset(params::$section_copy[$server['game']]['aCopyDir'][$name]) ? params::$section_copy[$server['game']]['aCopyDir'][$name] . ' ' : '';
    $copy .= isset(params::$section_copy[$server['game']]['aCopyFile'][$name]) ? params::$section_copy[$server['game']]['aCopyFile'][$name] . ' ' : '';

    $info .= $name . ', ';
}

$name_copy = md5($start_point . $id . $server['game']);

$ssh->set('cd ' . $tarif['install'] . $server['uid'] . ' && tmux new-session -ds copy_' . $server['uid'] . ' sh -c "tar -cf ' . $name_copy . '.tar ' . $copy . '; mv ' . $name_copy . '.tar /copy"');

$sql->query('SELECT `plugin`, `upd` FROM `plugins_install` WHERE `server`="' . $id . '"');
while ($plugin = $sql->get()) {
    $plugins .= $plugin['plugin'] . '.' . $plugin['upd'] . ',';
}

$sql->query('INSERT INTO `copy` set `user`="' . $server['user'] . '_' . $server['unit'] . '", `game`="' . $server['game'] . '", `server`="' . $id . '", `pack`="' . $server['pack'] . '", `name`="' . $name_copy . '", `info`="' . substr($info, 0, -2) . '",  `plugins`="' . substr($plugins, 0, -1) . '", `date`="' . $start_point . '", `status`="0"');

// Очистка кеша
$mcache->delete('server_copy_' . $id);

System::outjs(['s' => 'ok'], $nmch);

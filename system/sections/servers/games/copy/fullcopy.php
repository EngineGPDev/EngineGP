<?php

/*
 * Copyright 2018-2024 Solovev Sergei
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

$sql->query('SELECT `id` FROM `copy` WHERE `server`="' . $id . '" AND `info`="' . params::$section_copy[$server['game']]['CopyFull'] . '" LIMIT 1');
if ($sql->num()) {
    sys::outjs(['e' => 'Для создания новой копии необходимо удалить старую.'], $nmch);
}

$name_copy = md5($start_point . $id . $server['game']);

$ssh->set('cd ' . $tarif['install'] . $server['uid'] . ' && tmux new-session -ds copy_' . $server['uid'] . ' sh -c "tar -cf ' . $name_copy . '.tar ' . params::$section_copy[$server['game']]['CopyFull'] . '; mv ' . $name_copy . '.tar /copy"');

$plugins = '';

$sql->query('SELECT `plugin`, `upd` FROM `plugins_install` WHERE `server`="' . $id . '"');
while ($plugin = $sql->get()) {
    $plugins .= $plugin['plugin'] . '.' . $plugin['upd'] . ',';
}

$sql->query('INSERT INTO `copy` set `user`="' . $server['user'] . '_' . $server['unit'] . '", `game`="' . $server['game'] . '", `server`="' . $id . '", `pack`="' . $server['pack'] . '", `name`="' . $name_copy . '", `info`="' . params::$section_copy[$server['game']]['CopyFull'] . '",  `plugins`="' . substr($plugins, 0, -1) . '", `date`="' . $start_point . '", `status`="0"');

// Очистка кеша
$mcache->delete('server_copy_' . $id);

sys::outjs(['s' => 'ok'], $nmch);

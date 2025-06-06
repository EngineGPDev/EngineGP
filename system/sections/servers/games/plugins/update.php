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

if (!$go) {
    exit();
}

$pid = isset($url['plugin']) ? System::int($url['plugin']) : exit;

$sql->query('SELECT `id` FROM `plugins_update` WHERE `plugin`="' . $pid . '" ORDER BY `id` DESC LIMIT 1');

if (!$sql->num()) {
    exit();
}

$plugin = $sql->get();

// Проверка установки плагина
$sql->query('SELECT `id` FROM `plugins_install` WHERE `server`="' . $id . '" AND `plugin`="' . $pid . '" LIMIT 1');
if (!$sql->num()) {
    exit();
}

// Проверка установки обновления плагина
$sql->query('SELECT `id` FROM `plugins_install` WHERE `server`="' . $id . '" AND `plugin`="' . $pid . '" AND `upd`="' . $plugin['id'] . '" LIMIT 1');
if ($sql->num()) {
    System::outjs(['e' => 'Данный плагин уже обновлен']);
}

// Данные обновления
$sql->query('SELECT `id`, `cfg`, `incompatible`, `required` FROM `plugins_update` WHERE `id`="' . $plugin['id'] . '" LIMIT 1');

$plugin = $sql->get();

include(LIB . 'games/plugins.php');

// Проверка на наличие несовместимости с уже установленными плагинами
plugins::incompatible($id, $plugin['incompatible'], $nmch);

// Проверка на наличие необходимых установленых плагинов для устанавливаемого плагина
plugins::required($id, $plugin['required'], $nmch);

$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
$unit = $sql->get();

if (!isset($ssh)) {
    include(LIB . 'ssh.php');
}

if (!$ssh->auth($unit['passwd'], $unit['address'])) {
    System::outjs(['e' => System::text('error', 'ssh')], $nmch);
}

$sql->query('SELECT `install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
$tarif = $sql->get();

// Директория игр. сервера
$dir = $tarif['install'] . $server['uid'] . '/';

// Установка файлов на сервер
$ssh->set('cd ' . $dir . ' && tmux new-session -ds update_' . $start_point . ' sudo -u server' . $server['uid'] . ' sh -c "wget --no-check-certificate ' . $cfg['plugins'] . 'update/' . $plugin['id'] . '.zip && unzip -o ' . $plugin['id'] . '.zip; rm ' . $plugin['id'] . '.zip"');

// Удаление файлов
$sql->query('SELECT `file` FROM `plugins_delete` WHERE `update`="' . $plugin['id'] . '"');
while ($delete = $sql->get()) {
    $ssh->set('sudo -u server' . $server['uid'] . ' rm ' . $dir . $delete['file']);
}

unset($delete);

// Удаление текста из файлов
$sql->query('SELECT `text`, `file`, `regex` FROM `plugins_clear` WHERE `update`="' . $plugin['id'] . '"');
while ($clear = $sql->get()) {
    plugins::clear($clear, $server['uid'], $dir);
}

unset($clear);

// Добавление текста в файлы
$sql->query('SELECT `text`, `file`, `top` FROM `plugins_write` WHERE `update`="' . $plugin['id'] . '" ORDER BY `id` ASC');
while ($write = $sql->get()) {
    plugins::write($write, $server['uid'], $dir);
}

// Обновление данных в базе
$sql->query('UPDATE `plugins_install` set `upd`="' . $plugin['id'] . '", `time`="' . $start_point . '" WHERE `server`="' . $id . '" AND `plugin`="' . $pid . '" LIMIT 1');

// Очистка кеша
$mcache->delete('server_plugins_' . $id);

if ($plugin['cfg']) {
    System::outjs(['s' => 'cfg'], $nmch);
}

System::outjs(['s' => 'ok'], $nmch);

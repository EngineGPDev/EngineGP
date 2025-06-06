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
use EngineGP\Model\Parameters;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

if (!$go) {
    exit;
}

$pid = isset($url['plugin']) ? System::int($url['plugin']) : exit;

$sql->query('SELECT `name`, `cfg`, `upd`, `incompatible`, `choice`, `required`, `packs`, `price` FROM `plugins` WHERE `id`="' . $pid . '" AND `game`="' . $server['game'] . '" LIMIT 1');

if (!$sql->num()) {
    exit;
}

$plugin = $sql->get();

// Проверка установки плагина
$sql->query('SELECT `id` FROM `plugins_install` WHERE `server`="' . $id . '" AND `plugin`="' . $pid . '" LIMIT 1');
if ($sql->num()) {
    System::outjs(['e' => 'Данный плагин уже установлен']);
}

$upd = false;

// Если есть более поздняя версия плагина
if ($plugin['upd']) {
    $sql->query('SELECT `name`, `id`, `cfg`, `incompatible`, `choice`, `required`, `packs`, `price` FROM `plugins_update` WHERE `plugin`="' . $pid . '" ORDER BY `id` DESC LIMIT 1');
    if ($sql->num()) {
        $plugin = array_merge($plugin, $sql->get());

        $upd = true;
    }
}

$buy = false;

// Если платный плагин
if ($plugin['price']) {
    // Проверка покупки
    $sql->query('SELECT `id` FROM `plugins_buy` WHERE `plugin`="' . $pid . '" AND `server`="' . $id . '" LIMIT 1');
    if ($sql->num()) {
        $buy = true;
    } else {
        // Проверка баланса
        if ($user['balance'] < $plugin['price']) {
            System::outjs(['e' => 'У вас не хватает ' . (round($plugin['price'] - $user['balance'], 2)) . ' ' . $cfg['currency']], $nmch);
        }
    }
}

// Проверка на доступность плагина к установленной на сервере сборке
$packs = strpos($plugin['packs'], ':') ? explode(':', $plugin['packs']) : [$plugin['packs']];
if (!in_array($server['pack'], $packs) and $plugin['packs'] != 'all') {
    exit;
}

include(LIB . 'games/plugins.php');

// Проверка на наличие несовместимости с уже установленными плагинами
plugins::incompatible($id, $plugin['incompatible'], $nmch);

// Проверка на наличие необходимых установленых плагинов для устанавливаемого дополнения
plugins::required($id, $plugin['required'], $plugin['choice'], $nmch);

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

if ($upd) {
    $qsql = 'WHERE `update`="' . $plugin['id'] . '" ORDER BY `id` ASC';
    $fzip = 'u' . $plugin['id'];
    $upd = $plugin['id'];
} else {
    $qsql = 'WHERE `plugin`="' . $pid . '" AND `update`="0" ORDER BY `id` ASC';
    $fzip = $pid;
    $upd = 0;
}

// Директория игр. сервера
$dir = $tarif['install'] . $server['uid'] . '/';

// Установка файлов на сервер
$ssh->set('cd ' . $dir . ' && tmux new-session -ds install_' . $start_point . ' sudo -u server' . $server['uid'] . ' sh -c "'
    . 'wget --no-check-certificate ' . $cfg['plugins'] . 'install/' . $fzip . '.zip && unzip -o ' . $fzip . '.zip; rm ' . $fzip . '.zip;'
    . 'find . -type d -exec chmod 700 {} \;;'
    . 'find . -type f -exec chmod 600 {} \;;'
    . 'chmod 500 ' . Parameters::$aFileGame[$server['game']] . '"');

// Удаление файлов
$sql->query('SELECT `file` FROM `plugins_delete` ' . $qsql);
while ($delete = $sql->get()) {
    $ssh->set('sudo -u server' . $server['uid'] . ' rm ' . $dir . $delete['file']);
}

// Удаление текста из файлов
$sql->query('SELECT `text`, `file`, `regex` FROM `plugins_clear` ' . $qsql);
while ($clear = $sql->get()) {
    plugins::clear($clear, $server['uid'], $dir);
}

// Добавление текста в файлы
$sql->query('SELECT `text`, `file`, `top` FROM `plugins_write` ' . $qsql);
while ($write = $sql->get()) {
    plugins::write($write, $server['uid'], $dir);
}

// Если платный плагин
if (!$buy and $plugin['price']) {
    $sql->query('UPDATE `users` set `balance`=`balance`-"' . $plugin['price'] . '" WHERE `id`="' . $user['id'] . '" LIMIT 1');

    $sql->query('INSERT INTO `plugins_buy` set `plugin`="' . $pid . '", `key`="' . md5(strip_tags($plugin['name'])) . '", `server`="' . $id . '", `price`="' . $plugin['price'] . '", `time`="' . $start_point . '"');

    // Запись логов
    $sql->query('INSERT INTO `logs` set `user`="' . $user['id'] . '", `text`="' . System::updtext(
        System::text('logs', 'buy_plugin'),
        ['plugin' => strip_tags($plugin['name']), 'money' => $plugin['price'], 'id' => $id]
    ) . '", `date`="' . $start_point . '", `type`="buy", `money`="' . $plugin['price'] . '"');
}

// Запись данных в базу
$sql->query('INSERT INTO `plugins_install` set `server`="' . $id . '", `plugin`="' . $pid . '", `upd`="' . $upd . '", `time`="' . $start_point . '"');

// Очистка кеша
$mcache->delete('server_plugins_' . $id);

if ($plugin['cfg']) {
    System::outjs(['s' => 'cfg'], $nmch);
}

System::outjs(['s' => 'ok'], $nmch);

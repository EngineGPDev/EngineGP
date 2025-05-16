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

$sql->query('SELECT `unit`, `tarif`, `slots_start`, `online`, `players`, `name`, `pack`, `map`, `time`, `date`, `overdue` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
$server = array_merge($server, $sql->get());

$html->nav($server['address'] . ':' . $server['port']);

$sql->query('SELECT `name` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
$unit = $sql->get();

$sql->query('SELECT `name`, `packs` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
$tarif = $sql->get();

$btn = System::buttons($id, $server['status'], $server['game']);

$time_end = $server['status'] == 'overdue' ? 'Удаление через: ' . System::date('min', $server['overdue'] + $cfg['server_delete'] * 86400) : 'Осталось: ' . System::date('min', $server['time']);

$html->get('index', 'sections/servers/' . $server['game']);

$html->set('id', $id);
$html->set('unit', $unit['name']);
$html->set('tarif', $tarif['name']);

$tarif['packs'] = System::b64djs($tarif['packs']);

$html->set('pack', $tarif['packs'][$server['pack']]);
$html->set('address', $server['address'] . ':' . $server['port']);
$html->set('game', $aGname[$server['game']]);
$html->set('slots', $server['slots_start']);
$html->set('online', $server['online']);
$html->set('players', base64_decode($server['players'] ?? ''));
$html->set('name', $server['name']);
$html->set('status', System::status($server['status'], $server['game'], $server['map']));
$html->set('img', System::status($server['status'], $server['game'], 'samp', 'img'));
$html->set('time_end', $time_end);
$html->set('time', System::today($server['time']));
$html->set('date', System::today($server['date']));

$html->set('btn', $btn);

$html->pack('main');

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

namespace EngineGP\Model;

use EngineGP\System;

class Api
{
    public static function data($id)
    {
        global $sql, $cfg;

        $sql->query('SELECT `unit`, `tarif`, `address`, `port`, `game`, `slots_start`, `online`, `players`, `status`, `name`, `map`, `pack`, `fps`, `tickrate`, `ram`, `time`, `date`, `overdue` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
        if (!$sql->num()) {
            return ['e' => 'сервер не найден'];
        }

        $server = $sql->get();

        $sql->query('SELECT `name` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
        if (!$sql->num()) {
            return ['e' => 'локация не найдена'];
        }

        $unit = $sql->get();

        $sql->query('SELECT `name`, `packs` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
        if (!$sql->num()) {
            return ['e' => 'тариф не найден'];
        }

        $tarif = $sql->get();
        $packs = System::b64djs($tarif['packs']);

        $time_end = $server['status'] == 'overdue' ? 'Удаление через: ' . System::date('min', $server['overdue'] + $cfg['server_delete'] * 86400) : 'Осталось: ' . System::date('min', $server['time']);

        return [
            'id' => $id,
            'address' => $server['address'] . ':' . $server['port'],
            'unit' => $unit['name'],
            'tarif' => Game::info_tarif($server['game'], $tarif['name'], ['fps' => $server['fps'], 'tickrate' => $server['tickrate'], 'ram' => $server['ram']]),
            'game' => $server['game'],
            'name' => $server['name'],
            'slots' => $server['slots_start'],
            'online' => $server['online'],
            'players' => $server['players'],
            'status' => System::status($server['status'], $server['game'], $server['map']),
            'img' => System::status($server['status'], $server['game'], $server['map'], 'img'),
            'time_end' => $time_end,
            'time' => System::today($server['time']),
            'date' => System::today($server['date']),
            'pack' => $packs[$server['pack']],
        ];
    }

    public static function load($id)
    {
        global $sql, $cfg;

        $sql->query('SELECT `online`, `slots_start`, `ram_use`, `cpu_use`, `hdd_use` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
        if (!$sql->num()) {
            return ['e' => 'сервер не найден'];
        }

        $server = $sql->get();

        $online = 100 / $server['slots_start'] * $server['online'];
        $online = $online > 100 ? 100 : $online;

        return [
            'id' => $id,
            'cpu' => $server['cpu_use'],
            'ram' => $server['ram_use'],
            'hdd' => $server['hdd_use'],
            'onl' => $online,
        ];
    }

    public static function console($id, $cmd)
    {
        global $sql, $cfg;

        $aGames = ['cs', 'css', 'cssold', 'csgo', 'cs2', 'mc', 'mta'];

        $sql->query('SELECT `game` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
        if (!$sql->num()) {
            return 'сервер не найден';
        }

        $server = $sql->get();

        if (!in_array($server['game'], $aGames)) {
            return 'Игра не поддерживает команды';
        }

        $go = true;

        $_POST['command'] = isset($cmd[0]) ? urldecode($cmd) : '';

        include(SEC . 'servers/' . $server['game'] . '/console.php');
    }
}

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

$owners = $sql->query('SELECT `server` FROM `owners` WHERE `user`="' . $user['id'] . '" AND `time`>"' . $start_point . '" ORDER BY `id` ASC');

$n = $sql->num($owners);

$aUnits = [];
$aTarifs = [];

// Проверка массивов в кеше
if (is_array($mcache->get('owners_aut_' . $user['id'])) and $mcache->get('owners_nser_' . $user['id']) == $n) {
    $aUT = $mcache->get('owners_aut_' . $user['id']);
    $aUnits = $aUT[0];
    $aTarifs = $aUT[1];
} else {
    while ($owner = $sql->get($owners)) {
        $server_sql = $sql->query('SELECT `unit`, `tarif` FROM `servers` WHERE `id`="' . $owner['server'] . '"');

        while ($server = $sql->get($server_sql)) {
            if (!array_key_exists($server['unit'], $aUnits)) {
                $sql->query('SELECT `name` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
                $unit = $sql->get();

                $aUnits[$server['unit']] = [
                    'name' => $unit['name'],
                ];
            }

            if (!array_key_exists($server['tarif'], $aTarifs)) {
                $sql->query('SELECT `name`, `packs` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
                $tarif = $sql->get();

                $aTarifs[$server['tarif']] = [
                    'name' => $tarif['name'],
                    'packs' => System::b64djs($tarif['packs']),
                ];
            }
        }
    }

    // Запись массивов в кеш
    $mcache->set('owners_aut_' . $user['id'], [$aUnits, $aTarifs], false, 60);

    // Запись кол-во серверов в кеш
    $mcache->set('owners_nser_' . $user['id'], $n, false, 60);
}

$owners = $sql->query('SELECT `id`, `server`, `time` FROM `owners` WHERE `user`="' . $user['id'] . '" ORDER BY `id` ASC');

while ($owner = $sql->get($owners)) {
    if ($owner['time'] < $start_point) {
        $sql->query('DELETE FROM `owners` WHERE `id`="' . $owner['id'] . '" LIMIT 1');

        continue;
    }

    $sql->query('SELECT '
        . '`id`,'
        . '`unit`,'
        . '`tarif`,'
        . '`address`,'
        . '`game`,'
        . '`slots_start`,'
        . '`online`,'
        . '`status`,'
        . '`name`,'
        . '`pack`,'
        . '`fps`,'
        . '`tickrate`,'
        . '`ram`,'
        . '`map`,'
        . '`time`,'
        . '`date`,'
        . '`overdue`'
        . ' FROM `servers` WHERE `id`="' . $owner['server'] . '" LIMIT 1');

    while ($server = $sql->get()) {
        $btn = System::buttons($server['id'], $server['status'], $server['game']);

        $time_end = $server['status'] == 'overdue' ? 'Удаление через: ' . System::date('min', $server['overdue'] + $cfg['server_delete'] * 86400) : 'Осталось: ' . System::date('min', $server['time']);

        $html->get('list', 'sections/servers');

        $html->set('id', $server['id']);
        $html->set('unit', $aUnits[$server['unit']]['name']);
        $html->set(
            'tarif',
            games::info_tarif(
                $server['game'],
                $aTarifs[$server['tarif']]['name'],
                ['fps' => $server['fps'], 'tickrate' => $server['tickrate'], 'ram' => $server['ram']]
            )
        );

        $html->set('pack', $aTarifs[$server['tarif']]['packs'][$server['pack']]);
        $html->set('address', $server['address']);
        $html->set('game', $aGname[$server['game']]);
        $html->set('slots', $server['slots_start']);
        $html->set('online', $server['online']);
        $html->set('name', $server['name']);
        $html->set('fps', $server['fps']);
        $html->set('status', System::status($server['status'], $server['game'], $server['map']));
        $html->set('img', System::status($server['status'], $server['game'], $server['map'], 'img'));
        $html->set('time_end', $time_end);
        $html->set('time', System::today($server['time']));
        $html->set('date', System::today($server['date']));

        $html->set('btn', $btn);

        $html->pack('list');

        $wait_servers .= $server['id'] . ':false,';
        $updates_servers .= 'setTimeout(function() {update_info(\'' . $server['id'] . '\', true)}, 5000); setTimeout(function() {update_status(\'' . $server['id'] . '\', true)}, 10000);';
    }
}

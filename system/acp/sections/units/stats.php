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

use EngineGP\AdminSystem;
use EngineGP\Model\Parameters;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

$list = '';

$all_money = 0;
$all_money_month = 0;

$units = $sql->query('SELECT `id`, `name`, `show` FROM `units` ORDER BY `id` ASC');
while ($unit = $sql->get($units)) {
    $servers = $sql->query('SELECT `id`, `user` FROM `servers` WHERE `unit`="' . $unit['id'] . '"');
    $all_servers = $sql->num($servers);

    $money_all = 0;
    $money_month = 0;

    $time = date('j', $start_point) * 86400;

    while ($server = $sql->get($servers)) {
        $sql->query('SELECT `id` FROM `users` WHERE `id`="' . $server['user'] . '" AND `group`="user" LIMIT 1');
        if (!$sql->num()) {
            continue;
        }

        $sql->query('SELECT `money`, `date` FROM `logs` WHERE `user`="' . $server['user'] . '" AND (`type`="buy" OR `type`="extend") AND `text` LIKE \'%(сервер: #' . $server['id'] . ')%\'');
        while ($logs = $sql->get()) {
            $money_all += $logs['money'];

            if ($logs['date'] >= ($start_point - $time)) {
                $money_month += $logs['money'];
            }
        }
    }

    $all_money += $money_all;
    $all_money_month += $money_month;

    $sql->query('SELECT `id` FROM `servers` WHERE `unit`="' . $unit['id'] . '" AND `time`<"' . $start_point . '"');
    $overdue_servers = $sql->num();

    $list .= '<tr>';
    $list .= '<td>' . $unit['id'] . '</td>';
    $list .= '<td><a href="' . $cfg['http'] . 'acp/units/id/' . $unit['id'] . '">' . $unit['name'] . '</a></td>';
    $list .= '<td>' . ($unit['show'] == '1' ? 'Доступна' : 'Недоступна') . '</td>';
    $list .= '<td>' . $all_servers . ' шт.</td>';
    $list .= '<td>' . $overdue_servers . ' шт.</td>';
    $list .= '<td>' . $money_all . ' ' . $cfg['currency'] . '</td>';
    $list .= '<td>' . $money_month . ' ' . $cfg['currency'] . '</td>';
    $list .= '</tr>';
}

$html->get('stats', 'sections/units');

$html->set('list', $list);

$html->set('month', mb_strtolower(Parameters::$aNameMonth[AdminSystem::int(date('n', $start_point))], 'UTF-8'));

$html->set('all_money', $all_money);
$html->set('all_money_month', $all_money_month);
$html->set('cur', $cfg['currency']);

$html->pack('main');

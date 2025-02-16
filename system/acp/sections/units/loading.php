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

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

if ($id) {
    $sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $id . '" LIMIT 1');
    $unit = $sql->get();

    include(LIB . 'ssh.php');

    if (isset($url['service']) and in_array($url['service'], ['nginx', 'mysql', 'unit', 'geo', 'ungeo'])) {
        if (!$ssh->auth($unit['passwd'], $unit['address'])) {
            AdminSystem::outjs(['e' => 'Не удалось создать связь с локацией']);
        }

        switch ($url['service']) {
            case 'unit':
                $ssh->set('tmux new-session -ds reboot reboot');
                break;

            case 'geo':
                $ssh->set('iptables-restore < /etc/firewall/geo.conf; iptables-restore << ' . $cfg['iptables']);

                $sql->query('UPDATE `units` set `ddos`="1" WHERE `id`="' . $id . '" LIMIT 1');
                break;

            case 'ungeo':
                $ssh->set('iptables-restore < /etc/firewall/ungeo.conf; iptables-restore << ' . $cfg['iptables'] . '; iptables-restore << /root/' . $cfg['iptables'] . '_geo');

                $sql->query('UPDATE `units` set `ddos`="0" WHERE `id`="' . $id . '" LIMIT 1');
                break;

            default:
                $ssh->set('tmux new-session -ds sr_' . $url['service'] . ' service ' . $url['service'] . ' restart');
        }

        AdminSystem::outjs(['s' => 'ok']);
    }

    $aData = [
        'cpu' => '0%',
        'ram' => '0%',
        'hdd' => '0%',
        'nginx' => 'unknown',
        'mysql' => 'unknown',
        'uptime' => 'unknown',
        'ssh' => 'error',
    ];

    if (!$ssh->auth($unit['passwd'], $unit['address'])) {
        AdminSystem::outjs($aData);
    }

    $aData['ssh'] = '<i class="fa fa-retweet pointer" id="units_restart(\'unit\')" onclick="return units_restart(\'' . $id . '\', \'unit\')"></i>';

    $stat_ram = $ssh->get('echo `cat /proc/meminfo | grep MemTotal | awk \'{print $2}\'; cat /proc/meminfo | grep MemFree | awk \'{print $2}\'; cat /proc/meminfo | grep Buffers | awk \'{print $2}\'; cat /proc/meminfo | grep Cached | grep -v SwapCached | awk \'{print $2}\'`');
    $aData['ram'] = ceil(AdminSystem::ram_load($stat_ram)) . '%';

    $aData['hdd'] = $ssh->get('df -P / | awk \'{print $5}\' | tail -1');

    $aData['nginx'] = AdminSystem::status($ssh->get('service nginx status')) ? 'Работает' : '<a href="#" onclick="return units_restart(\'' . $id . '\', \'nginx\')">Поднять</a>';
    $aData['mysql'] = AdminSystem::status($ssh->get('service mysql status')) ? 'Работает' : '<a href="#" onclick="return units_restart\'' . $id . '\', (\'mysql\')">Поднять</a>';

    $time = ceil($ssh->get('cat /proc/uptime | awk \'{print $1}\''));
    $aData['uptime'] = AdminSystem::uptime_load($time);

    $aData['cpu'] = AdminSystem::cpu_load($ssh->get('echo "`ps -A -o pcpu | tail -n+2 | paste -sd+ | bc | awk \'{print $0}\'` `cat /proc/cpuinfo | grep processor | wc -l | awk \'{print $1}\'`"')) . '%';

    AdminSystem::outjs($aData);
}

$loads = '';
$list = '';

$sql->query('SELECT `id`, `name` FROM `units` ORDER BY `id` ASC');
while ($unit = $sql->get()) {
    $list .= '<tr>';
    $list .= '<td>' . $unit['id'] . '</td>';
    $list .= '<td><a href="' . $cfg['http'] . 'acp/units/id/' . $unit['id'] . '">' . $unit['name'] . '</a></td>';
    $list .= '<td id="cpu_' . $unit['id'] . '" class="text-center"><i class="fa fa-spinner fa-pulse fa-1x fa-fw margin-bottom"></i></td>';
    $list .= '<td id="ram_' . $unit['id'] . '" class="text-center"><i class="fa fa-spinner fa-pulse fa-1x fa-fw margin-bottom"></i></td>';
    $list .= '<td id="hdd_' . $unit['id'] . '" class="text-center"><i class="fa fa-spinner fa-pulse fa-1x fa-fw margin-bottom"></i></td>';
    $list .= '<td id="nginx_' . $unit['id'] . '" class="text-center"><i class="fa fa-spinner fa-pulse fa-1x fa-fw margin-bottom"></i></td>';
    $list .= '<td id="mysql_' . $unit['id'] . '" class="text-center"><i class="fa fa-spinner fa-pulse fa-1x fa-fw margin-bottom"></i></td>';
    $list .= '<td id="uptime_' . $unit['id'] . '" class="text-center"><i class="fa fa-spinner fa-pulse fa-1x fa-fw margin-bottom"></i></td>';
    $list .= '<td id="ssh_' . $unit['id'] . '" class="text-center"><i class="fa fa-spinner fa-pulse fa-1x fa-fw margin-bottom"></i></td>';
    $list .= '</tr>';

    $loads .= 'units_load(\'' . $unit['id'] . '\', false);';
}

$html->get('loading', 'sections/units');

$html->set('list', $list);
$html->set('loads', $loads);

$html->pack('main');

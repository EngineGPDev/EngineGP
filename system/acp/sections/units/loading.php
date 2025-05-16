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
use EngineGP\Infrastructure\RemoteAccess\SshClient;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

if ($id) {
    $aData = [
        'cpu' => '0%',
        'ram' => '0%',
        'hdd' => '0%',
        'nginx' => 'unknown',
        'mysql' => 'unknown',
        'uptime' => 'unknown',
        'ssh' => 'error',
    ];

    $sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $id . '" LIMIT 1');
    $unit = $sql->get();

    $sshClient = new SshClient($unit['address'], 'root', $unit['passwd']);

    try {
        $sshClient->connect();
    } catch (\Exception $e) {
        AdminSystem::outjs($aData);
    }

    if (isset($url['service']) and in_array($url['service'], ['nginx', 'mysql', 'unit', 'geo', 'ungeo'])) {
        switch ($url['service']) {
            case 'unit':
                $sshClient->execute('tmux new-session -ds reboot reboot');
                break;

            case 'geo':
                $sshClient->execute('iptables-restore < /etc/firewall/geo.conf; iptables-restore << ' . $cfg['iptables']);

                $sql->query('UPDATE `units` set `ddos`="1" WHERE `id`="' . $id . '" LIMIT 1');
                break;

            case 'ungeo':
                $sshClient->execute('iptables-restore < /etc/firewall/ungeo.conf; iptables-restore << ' . $cfg['iptables'] . '; iptables-restore << /root/' . $cfg['iptables'] . '_geo');

                $sql->query('UPDATE `units` set `ddos`="0" WHERE `id`="' . $id . '" LIMIT 1');
                break;

            default:
                $sshClient->execute('tmux new-session -ds sr_' . $url['service'] . ' service ' . $url['service'] . ' restart');
        }

        AdminSystem::outjs(['s' => 'ok']);
    }

    $aData['ssh'] = '<i class="fa fa-retweet pointer" id="units_restart(\'unit\')" onclick="return units_restart(\'' . $id . '\', \'unit\')"></i>';

    $stat_ram = $sshClient->execute('echo `cat /proc/meminfo | grep MemTotal | awk \'{print $2}\'; cat /proc/meminfo | grep MemFree | awk \'{print $2}\'; cat /proc/meminfo | grep Buffers | awk \'{print $2}\'; cat /proc/meminfo | grep Cached | grep -v SwapCached | awk \'{print $2}\'`', false);
    $aData['ram'] = ceil(AdminSystem::ram_load($stat_ram)) . '%';

    $aData['hdd'] = $sshClient->execute('df -P / | awk \'{print $5}\' | tail -1', false);

    $aData['nginx'] = AdminSystem::status($sshClient->execute('service nginx status', false)) ? 'Работает' : '<a href="#" onclick="return units_restart(\'' . $id . '\', \'nginx\')">Поднять</a>';
    $aData['mysql'] = AdminSystem::status($sshClient->execute('service mysql status', false)) ? 'Работает' : '<a href="#" onclick="return units_restart\'' . $id . '\', (\'mysql\')">Поднять</a>';

    $time = ceil($sshClient->execute('cat /proc/uptime | awk \'{print $1}\'', false));
    $aData['uptime'] = AdminSystem::uptime_load($time);

    $aData['cpu'] = AdminSystem::cpu_load($sshClient->execute('echo "`ps -A -o pcpu | tail -n+2 | paste -sd+ | bc | awk \'{print $0}\'` `cat /proc/cpuinfo | grep processor | wc -l | awk \'{print $1}\'`"', false)) . '%';

    sys::outjs($aData);
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

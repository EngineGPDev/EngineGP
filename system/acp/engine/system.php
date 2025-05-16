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

if ($go) {
    $sql->query('SELECT `address`, `passwd` FROM `panel` LIMIT 1');
    $unit = $sql->get();

    $sshClient = new SshClient($unit['address'], 'root', $unit['passwd']);

    $aData = [
        'verPanel' => '4.0.0-snapshot',
        'cpu' => '0%',
        'ram' => '0%',
        'hdd' => '0%',
        'nginx' => 'error',
        'mysql' => 'error',
        'uptime' => 'error',
        'ssh' => 'error',
    ];

    try {
        $sshClient->connect();

        if (isset($url['service']) and in_array($url['service'], ['nginx', 'mysql', 'unit'])) {
            if ($url['service'] == 'unit') {
                $sshClient->execute('tmux new-session -ds reboot reboot');
            } else {
                $sshClient->execute('tmux new-session -ds sr_' . $url['service'] . ' service ' . $url['service'] . ' restart');
            }

            AdminSystem::outjs(['s' => 'ok']);
        }

        $stat_ram = $sshClient->execute('echo `cat /proc/meminfo | grep MemTotal | awk \'{print $2}\'; cat /proc/meminfo | grep MemFree | awk \'{print $2}\'; cat /proc/meminfo | grep Buffers | awk \'{print $2}\'; cat /proc/meminfo | grep Cached | grep -v SwapCached | awk \'{print $2}\'`', false);
        $time = ceil($sshClient->execute('cat /proc/uptime | awk \'{print $1}\'', false));

        $aData['cpu'] = AdminSystem::cpu_load($sshClient->execute('echo "`ps -A -o pcpu | tail -n+2 | paste -sd+ | bc | awk \'{print $0}\'` `cat /proc/cpuinfo | grep processor | wc -l | awk \'{print $1}\'`"', false)) . '%';
        $aData['ram'] = ceil(AdminSystem::ram_load($stat_ram)) . '%';
        $aData['hdd'] = $sshClient->execute('df -P / | awk \'{print $5}\' | tail -1', false);
        $aData['nginx'] = '<a href="#" onclick="return system_restart(\'nginx\')">Перезагрузить</a>';
        $aData['mysql'] = '<a href="#" onclick="return system_restart(\'mysql\')">Перезагрузить</a>';
        $aData['uptime'] = AdminSystem::uptime_load($time);
        $aData['ssh'] = '<i class="fa fa-retweet pointer" id="system_restart(\'unit\')" onclick="return system_restart(\'unit\')"></i>';
    } catch (\Exception $e) {
        echo $e->getMessage();
    } finally {
        AdminSystem::outjs($aData);
        $sshClient->disconnect();
    }
}

$html->get('index', 'sections/system');

$html->pack('main');

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

if ($go) {
    $sql->query('SELECT `address`, `passwd` FROM `panel` LIMIT 1');
    $unit = $sql->get();

    include(LIB . 'ssh.php');

    if (isset($url['service']) and in_array($url['service'], ['nginx', 'mysql', 'unit'])) {
        if (!$ssh->auth($unit['passwd'], $unit['address'])) {
            sys::outjs(['e' => 'Не удалось создать связь с сервером']);
        }

        if ($url['service'] == 'unit') {
            $ssh->set('tmux new-session -ds reboot reboot');
        } else {
            $ssh->set('tmux new-session -ds sr_' . $url['service'] . ' service ' . $url['service'] . ' restart');
        }

        sys::outjs(['s' => 'ok']);
    }

    $aData = [
        'verPanel' => '4.0.0-beta.5',
        'cpu' => '0%',
        'ram' => '0%',
        'hdd' => '0%',
        'nginx' => '<a href="#" onclick="return system_restart(\'nginx\')">Перезагрузить</a>',
        'mysql' => '<a href="#" onclick="return system_restart(\'mysql\')">Перезагрузить</a>',
        'uptime' => 'unknown',
        'ssh' => 'error',
    ];

    if (!$ssh->auth($unit['passwd'], $unit['address'])) {
        sys::outjs($aData);
    }

    $aData['ssh'] = '<i class="fa fa-retweet pointer" id="system_restart(\'unit\')" onclick="return system_restart(\'unit\')"></i>';

    $stat_ram = $ssh->get('echo `cat /proc/meminfo | grep MemTotal | awk \'{print $2}\'; cat /proc/meminfo | grep MemFree | awk \'{print $2}\'; cat /proc/meminfo | grep Buffers | awk \'{print $2}\'; cat /proc/meminfo | grep Cached | grep -v SwapCached | awk \'{print $2}\'`');
    $aData['ram'] = ceil(sys::ram_load($stat_ram)) . '%';

    $aData['hdd'] = $ssh->get('df -P / | awk \'{print $5}\' | tail -1');

    $time = ceil($ssh->get('cat /proc/uptime | awk \'{print $1}\''));
    $aData['uptime'] = sys::uptime_load($time);

    $aData['cpu'] = sys::cpu_load($ssh->get('echo "`ps -A -o pcpu | tail -n+2 | paste -sd+ | bc | awk \'{print $0}\'` `cat /proc/cpuinfo | grep processor | wc -l | awk \'{print $1}\'`"')) . '%';

    sys::outjs($aData);
}

$html->get('index', 'sections/system');

$html->pack('main');

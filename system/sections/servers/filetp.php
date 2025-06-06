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
use EngineGP\Model\Ftp;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

$sql->query('SELECT `uid`, `unit`, `address`, `game`, `status`, `plugins_use`, `ftp_use`, `console_use`, `stats_use`, `copy_use`, `web_use`, `time` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
$server = $sql->get();

if (!$server['ftp_use']) {
    System::back($cfg['http'] . 'servers/id/' . $id);
}

System::nav($server, $id, 'filetp');

$frouter = explode('/', System::route($server, 'filetp', $go));

if (end($frouter) == 'noaccess.php') {
    include(SEC . 'servers/noaccess.php');
} else {
    $sql->query('SELECT `uid`, `unit`, `tarif`, `ftp`, `ftp_root`, `ftp_passwd`, `ftp_on`, `hdd` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
    $server = array_merge($server, $sql->get());

    $sql->query('SELECT `address` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
    $unit = $sql->get();
    $ip = System::first(explode(':', $unit['address']));

    $sql->query('SELECT `install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
    $tarif = $sql->get();

    $html->nav($server['address'], $cfg['http'] . 'servers/id/' . $id);
    $html->nav('FileTP');

    // Корневой каталог сервера
    if ($cfg['ftp']['root'][$server['game']] || $server['ftp_root']) {
        // Путь для Proftpd
        $homedir = $tarif['install'] . $server['uid'];

        // Путь для файлового менеджера
        $dir = $cfg['ftp']['dir'][$server['game']];
    } else {
        // Путь для Proftpd
        $homedir = $tarif['install'] . $server['uid'] . $cfg['ftp']['home'][$server['game']];

        // Путь для файлового менеджера
        $dir = '/';
    }

    $aData = [
        'root' => $dir,
        'host' => $ip,
        'login' => $server['uid'],
        'passwd' => $server['ftp_passwd'],
    ];

    if ($go) {
        if (isset($url['action']) and in_array($url['action'], ['on', 'off', 'change', 'logs'])) {
            $sql->query('SELECT `passwd`, `sql_login`, `sql_passwd`, `sql_port`, `sql_ftp` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
            $unit = array_merge($unit, $sql->get());

            include(LIB . 'ssh.php');

            // Проверка соединения с ssh сервером
            if (!$ssh->auth($unit['passwd'], $unit['address'])) {
                System::back($cfg['http'] . 'servers/id/' . $id . '/section/filetp');
            }
        } else {
            $ftp = new Ftp();

            // Проверка соединения с ftp сервером
            if (!$ftp->auth($aData['host'], $aData['login'], $aData['passwd'])) {
                if (isset($url['action'])) {
                    if ($url['action'] == 'search') {
                        System::out('Не удалось соединиться с ftp-сервером.');
                    }

                    System::outjs(['e' => 'Не удалось соединиться с ftp-сервером.']);
                }

                System::out();
            }
        }

        // Выполнение операций
        if (isset($url['action'])) {
            switch ($url['action']) {
                case 'on':
                    if ($server['ftp']) {
                        System::back($cfg['http'] . 'servers/id/' . $id . '/section/filetp');
                    }

                    $used = System::int($ssh->get('cd ' . $tarif['install'] . $server['uid'] . ' && du -b | tail -1'));

                    if ($used < 1) {
                        System::back($cfg['http'] . 'help/action/create');
                    }

                    $bytes = $server['hdd'] * 1048576;

                    $server['ftp_passwd'] = isset($server['ftp_passwd'][1]) ? $server['ftp_passwd'] : System::passwd(8);

                    $qSql = 'DELETE FROM users WHERE username=\'' . $server['uid'] . '\';'
                        . 'DELETE FROM quotalimits WHERE name=\'' . $server['uid'] . '\';'
                        . 'DELETE FROM quotatallies WHERE name=\'' . $server['uid'] . '\';'
                        . 'INSERT INTO users set username=\'' . $server['uid'] . '\', password=\'' . $server['ftp_passwd'] . '\', uid=\'' . $server['uid'] . '\', gid=\'1000\', homedir=\'' . $homedir . '\', shell=\'/bin/false\';'
                        . 'INSERT INTO quotalimits set name=\'' . $server['uid'] . '\', quota_type=\'user\', per_session=\'false\', limit_type=\'hard\', bytes_in_avail=\'' . $bytes . '\';'
                        . 'INSERT INTO quotatallies set name=\'' . $server['uid'] . '\', quota_type=\'user\', bytes_in_used=\'' . $used . '\'';

                    $ssh->set('tmux new-session -ds ftp' . $server['uid'] . ' mysql -P ' . $unit['sql_port'] . ' -u' . $unit['sql_login'] . ' -p' . $unit['sql_passwd'] . ' --database ' . $unit['sql_ftp'] . ' -e "' . $qSql . '"');

                    $sql->query('UPDATE `servers` SET `ftp`="1", `ftp_on`="1", `ftp_passwd`="' . $server['ftp_passwd'] . '" WHERE `id`="' . $id . '" LIMIT 1');

                    $mcache->delete('server_filetp_' . $id);

                    System::back($cfg['http'] . 'servers/id/' . $id . '/section/filetp');

                    // no break
                case 'change':
                    if (!$server['ftp']) {
                        System::back($cfg['http'] . 'servers/id/' . $id . '/section/filetp');
                    }

                    $passwd = System::passwd(8);

                    $qSql = "UPDATE users set password='" . $passwd . "' WHERE username='" . $server['uid'] . "' LIMIT 1";

                    $ssh->set('tmux new-session -ds ftp' . $server['uid'] . ' mysql -P ' . $unit['sql_port'] . ' -u' . $unit['sql_login'] . ' -p' . $unit['sql_passwd'] . ' --database ' . $unit['sql_ftp'] . ' -e ' . '"' . $qSql . '"');

                    $sql->query('UPDATE `servers` SET `ftp_passwd`="' . $passwd . '" WHERE `id`="' . $id . '" LIMIT 1');

                    $mcache->delete('server_filetp_' . $id);

                    System::back($cfg['http'] . 'servers/id/' . $id . '/section/filetp');

                    // no break
                case 'off':
                    if (!$server['ftp']) {
                        System::back($cfg['http'] . 'servers/id/' . $id . '/section/filetp');
                    }

                    $qSql = 'DELETE FROM users WHERE username=\'' . $server['uid'] . '\';'
                        . 'DELETE FROM quotalimits WHERE name=\'' . $server['uid'] . '\';'
                        . 'DELETE FROM quotatallies WHERE name=\'' . $server['uid'] . '\'';

                    $ssh->set('tmux new-session -ds ftp' . $server['uid'] . ' mysql -P ' . $unit['sql_port'] . ' -u' . $unit['sql_login'] . ' -p' . $unit['sql_passwd'] . ' --database ' . $unit['sql_ftp'] . ' -e "' . $qSql . '"');

                    $sql->query('UPDATE `servers` SET `ftp`="0" WHERE `id`="' . $id . '" LIMIT 1');

                    $mcache->delete('server_filetp_' . $id);

                    System::back($cfg['http'] . 'servers/id/' . $id . '/section/filetp');

                    // no break
                case 'rename':
                    $ftp->rename(json_decode($_POST['path']), json_decode($_POST['name']), json_decode($_POST['newname']));

                    // no break
                case 'edit':
                    $ftp->edit_file(json_decode($_POST['path']), json_decode($_POST['name']));

                    // no break
                case 'create':
                    if (isset($url['folder'])) {
                        $ftp->mkdir(json_decode($_POST['path']), json_decode($_POST['name']));
                    }

                    $ftp->touch(json_decode($_POST['path']), json_decode($_POST['name']), json_decode($_POST['text']));

                    // no break
                case 'delete':
                    if (isset($url['folder'])) {
                        $ftp->rmdir(json_decode($_POST['path']), json_decode($_POST['name']));
                    }

                    $ftp->rmfile(json_decode($_POST['path']) . '/' . json_decode($_POST['name']));

                    // no break
                case 'chmod':
                    $ftp->chmod(json_decode($_POST['path']), json_decode($_POST['name']), System::int($_POST['chmod']));

                    // no break
                case 'search':
                    $text = isset($_POST['find']) ? System::first(explode('.', json_decode($_POST['find']))) : System::out();

                    if (!isset($text[2])) {
                        System::out('Для выполнения поиска, необходимо больше данных');
                    }

                    $ftp->search($text, $id);

                    // no break
                case 'logs':
                    $logs = $mcache->get('filetp_logs_' . $id);

                    if (!$logs) {
                        $ftp = new Ftp();

                        $logs = $ftp->logs($ssh->get('cat /var/log/proftpd/xferlog | grep "/' . $server['uid'] . '/" | awk \'{print $2"\\\"$3"\\\"$4"\\\"$5"\\\"$7"\\\"$8"\\\"$9"\\\"$12}\' | tail -50'), $server['uid']);

                        $mcache->set('filetp_logs_' . $id, $logs, false, 300);
                    }

                    System::out($logs);
            }
        }

        if (!isset($_POST['path'])) {
            $_POST['path'] = json_encode($aData['root']);
        }

        System::out($ftp->view($ftp->read(json_decode($_POST['path'])), $id));
    }

    if ($mcache->get('server_filetp_' . $id) != '') {
        $html->arr['main'] = $mcache->get('server_filetp_' . $id);
    } else {
        if ($server['ftp']) {
            $html->get('filetp_on', 'sections/servers/games/filetp');

            $html->set('address', 'ftp://' . $aData['login'] . ':' . $aData['passwd'] . '@' . $aData['host']);
            $html->set('server', $aData['host']);
            $html->set('login', $aData['login']);
            $html->set('passwd', $aData['passwd']);
            $html->set('path', $aData['root']);
        } else {
            $html->get('filetp_off', 'sections/servers/games/filetp');
        }

        $html->set('id', $id);

        $html->pack('main');

        $mcache->set('server_filetp_' . $id, $html->arr['main'], false, 10);
    }
}

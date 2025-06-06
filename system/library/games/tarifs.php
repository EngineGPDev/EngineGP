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
use EngineGP\Model\Game;
use EngineGP\Model\Parameters;
use EngineGP\Infrastructure\RemoteAccess\SshClient;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

class tarifs
{
    public static function extend_address($game, $sid)
    {
        global $cfg, $sql, $html, $start_point;

        $sql->query('SELECT `aid`, `time` FROM `address_buy` WHERE `server`="' . $sid . '" LIMIT 1');
        if (!$sql->num()) {
            return null;
        }

        $ip_buy = $sql->get();

        $sql->query('SELECT `ip`, `price` FROM `address` WHERE `id`="' . $ip_buy['aid'] . '" LIMIT 1');

        $ip_buy = array_merge($ip_buy, $sql->get());

        $html->get('extend_address', 'sections/servers/games/tarif');
        $html->set('address', $ip_buy['ip'] . ':' . Parameters::$aDefPort[$game]);
        $html->set('iptime', System::date('max', $ip_buy['time']));
        $html->set('ipprice', $ip_buy['price']);
        $html->set('cur', $cfg['currency']);
        $html->pack('extend_address');

        return null;
    }

    public static function address($server, $sid)
    {
        global $cfg, $sql, $html;

        $sql->query('SELECT `address` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
        $sUnit = $sql->get();

        if (System::first(explode(':', $sUnit['address'])) != System::first(explode(':', $server['address']))) {
            if ($cfg['buy_address'][$server['game']]) {
                tarif::address_extend($server['address'], $sid);
            }

            return null;
        }

        $options = '<option value="0">Выберите выделенный адрес</option>';

        $sql->query('SELECT `id`, `ip`, `price` FROM `address` WHERE `unit`="' . $server['unit'] . '" AND `buy`="0"');
        if (!$sql->num()) {
            return null;
        }

        while ($ip = $sql->get()) {
            $options .= '<option value="' . $ip['id'] . '">' . $ip['ip'] . ':' . Parameters::$aDefPort[$server['game']] . '</option>';
        }

        $html->get('address', 'sections/servers/games/tarif');
        if ($cfg['buy_address'][$server['game']]) {
            $html->unit('!mounth');
            $html->unit('mounth', 1);
        } else {
            $html->unit('!mounth', 1);
            $html->unit('mounth');
        }

        $html->set('id', $sid);
        $html->set('options', $options);
        $html->set('address', $server['address']);
        $html->set('cur', $cfg['currency']);
        $html->pack('main');

        return null;
    }

    public static function address_extend($address, $sid)
    {
        global $cfg, $sql, $html;

        $sql->query('SELECT `aid`, `time` FROM `address_buy` WHERE `server`="' . $sid . '" LIMIT 1');

        if (!$sql->num()) {
            return null;
        }

        $add = $sql->get();

        $sql->query('SELECT `price` FROM `address` WHERE `id`="' . $add['aid'] . '" LIMIT 1');

        if (!$sql->num()) {
            return null;
        }

        $add = array_merge($add, $sql->get());

        $html->get('address_extend', 'sections/servers/games/tarif');
        $html->set('address', $address);
        $html->set('time', System::date('max', $add['time']));
        $html->set('price', $add['price']);
        $html->set('cur', $cfg['currency']);
        $html->pack('main');

        return null;
    }

    public static function address_add_sum($address, $server)
    {
        global $sql;

        if (!$address) {
            return 0;
        }

        $ip = System::first(explode(':', $server['address']));

        $sql->query('SELECT `address` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
        $unit = $sql->get();

        if ($address and System::first(explode(':', $unit['address'])) != $ip) {
            $sql->query('SELECT `price` FROM `address` WHERE `ip`="' . $ip . '" LIMIT 1');

            if ($sql->num()) {
                $add = $sql->get();

                return $add['price'];
            }
        }

        return 0;
    }

    public static function slots($server, $aSlots, $sid)
    {
        global $cfg, $html, $start_point;

        $options = '<option value="0">Выберите количество слот</option>';

        // С уменьшением (min ==> max) || закончился срок аренды
        if (($cfg['change_slots'][$server['game']]['days'] and $cfg['change_slots'][$server['game']]['down']) || $server['time'] < $start_point) {
            for ($i = $aSlots['min']; $i <= $aSlots['max']; $i += 1) {
                $options .= '<option value="' . $i . '">' . $i . ' шт.</option>';
            }

            $html->get('slots', 'sections/servers/games/tarif');
        } else {
            if ($server['slots'] == $aSlots['max']) {
                return null;
            }

            $max = $aSlots['max'] - $server['slots'];

            if ($max < 1) {
                return null;
            }

            for ($i = 1; $i <= $max; $i += 1) {
                $options .= '<option value="' . $i . '">' . $i . ' шт.</option>';
            }

            $html->get('slots_buy', 'sections/servers/games/tarif');
        }

        $html->set('id', $sid);
        $html->set('options', $options);
        $html->set('slots', $server['slots']);
        $html->set('cur', $cfg['currency']);
        $html->pack('main');

        return null;
    }

    public static function price($plan)
    {
        $aPrice = explode(':', $plan);

        $check = $aPrice[0];

        unset($aPrice[0]);

        if (!count($aPrice)) {
            return false;
        }

        foreach ($aPrice as $price) {
            if ($check != $price) {
                return true;
            }
        }

        return false;
    }

    public static function unit_old($tarif, $unit, $server, $mcache)
    {
        global $sql, $user, $start_point;

        $sshClient = new SshClient($unit['address'], 'root', $unit['passwd']);

        // Убить процессы
        $sshClient->execute('kill -9 `ps aux | grep s_' . $server['uid'] . ' | grep -v grep | awk ' . "'{print $2}'" . ' | xargs;'
            . 'lsof -i@:' . $server['address'] . ' | awk ' . "'{print $2}'" . ' | xargs`; sudo -u server' . $server['uid'] . ' tmux kill-session -t server' . $server['uid']);

        // Директория игрового сервера
        $install = $tarif['install'] . $server['uid'];

        $copys = 'tmux new-session -ds r_copy_' . $server['uid'] . ' sh -c "';

        $scopy = $sql->query('SELECT `id`, `name` FROM `copy` WHERE `server`="' . $server['id'] . '"');
        while ($copy = $sql->get($scopy)) {
            $copys .= 'rm /copy/' . $copy['name'] . '.tar;';

            $sql->query('DELETE FROM `copy` WHERE `id`="' . $copy['id'] . '" LIMIT 1');
        }

        $copys .= '";';

        $sshClient->execute($copys // Удаление резервных копий
            . 'tmux new-session -ds r_' . $server['uid'] . ' rm -r ' . $install . ';' // Удаление директории сервера
            . 'userdel server' . $server['uid']); // Удаление пользователя сервера c локации

        // Удаление ftp доступа
        $qSql = 'DELETE FROM users WHERE username=\'' . $server['uid'] . '\';'
            . 'DELETE FROM quotalimits WHERE name=\'' . $server['uid'] . '\';'
            . 'DELETE FROM quotatallies WHERE name=\'' . $server['uid'] . '\'';

        $sshClient->execute('tmux new-session -ds ftp' . $server['uid'] . ' mysql -P ' . $unit['sql_port'] . ' -u' . $unit['sql_login'] . ' -p' . $unit['sql_passwd'] . ' --database ' . $unit['sql_ftp'] . ' -e "' . $qSql . '"');

        $sql->query('UPDATE `servers` SET `ftp`="0" WHERE `id`="' . $server['id'] . '" LIMIT 1');

        // Очистка правил FireWall
        Game::iptables($server['id'], 'remove', null, null, null, null, false, $ssh);

        $sshClient->disconnect();

        // Удаление заданий из crontab
        $sql->query('SELECT `address`, `passwd` FROM `panel` LIMIT 1');
        $panel = $sql->get();

        $sshClient = new SshClient($panel['address'], 'root', $panel['passwd']);

        $crons = $sql->query('SELECT `id`, `cron` FROM `crontab` WHERE `server`="' . $server['id'] . '"');
        while ($cron = $sql->get($crons)) {
            $crontab = preg_quote($cron['cron'], '/');

            $sshClient->execute('crontab -l | grep -v "' . $crontab . '" | crontab -');

            $sql->query('DELETE FROM `crontab` WHERE `id`="' . $cron['id'] . '" LIMIT 1');
        }

        // Удаление установок игрового сервера
        $sql->query('DELETE FROM `admins_' . $server['game'] . '` WHERE `server`="' . $server['id'] . '" LIMIT 1');
        $sql->query('DELETE FROM `plugins_install` WHERE `server`="' . $server['id'] . '" LIMIT 1');

        // Обновление данных выделенного адреса
        $sql->query('SELECT `id`, `aid` FROM `address_buy` WHERE `server`="' . $server['id'] . '" LIMIT 1');
        if ($sql->num()) {
            $add = $sql->get();

            $sql->query('UPDATE `address` set `buy`="0" WHERE `id`="' . $add['aid'] . '" LIMIT 1');
            $sql->query('DELETE FROM `address_buy` WHERE `id`="' . $add['id'] . '" LIMIT 1');
        }

        return null;
    }
}

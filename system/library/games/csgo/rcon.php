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
use xPaw\SourceQuery\SourceQuery;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

class rcon
{
    public static function cmd($server, $cmd = 'status')
    {
        $sq = new SourceQuery();

        $ip = $server['address'];
        $port = $server['port_rcon'];

        $sq->Connect($ip, $port, 3, SourceQuery::SOURCE);

        $sq->SetRconPassword(rcon::rcon_passwd($server));

        $out = $sq->Rcon($cmd);

        $sq->Disconnect();

        return $out;
    }

    public static function players($data)
    {
        $aPlayers = [];
        $n = 1;

        $lines = explode("\n", $data);

        foreach ($lines as $line) {
            if (strpos($line, '#') === false) {
                continue;
            }

            $start = strpos($line, '"') + 1;
            $end = strrpos($line, '"');

            $userid = System::int(substr($line, 0, $start));

            $name = htmlspecialchars(substr($line, $start, $end - $start));

            $line = trim(substr($line, $end + 1));

            $aData = array_values(array_diff(explode(' ', $line), ['', ' ']));

            $steamid = trim($aData[0]);
            $ip = trim(System::first(explode(':', $aData[5])));

            if ((System::valid($steamid, 'steamid') and System::valid($steamid, 'steamid3')) || System::valid($ip, 'ip')) {
                continue;
            }

            $aPlayers[$n]['userid'] = $userid;
            $aPlayers[$n]['name'] = $name;
            $aPlayers[$n]['steamid'] = $steamid;
            $aPlayers[$n]['time'] = trim($aData[1]);
            $aPlayers[$n]['ping'] = trim($aData[2]);
            $aPlayers[$n]['ip'] = $ip;

            $whois = rcon::country($ip);

            $aPlayers[$n]['ico'] = $whois['ico'];
            $aPlayers[$n]['country'] = $whois['name'];

            $n += 1;
        }

        return $aPlayers;
    }

    public static function rcon_passwd($server)
    {
        global $cfg, $sql, $user, $nmch;

        include(LIB . 'ssh.php');

        $sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
        $unit = $sql->get();

        if (!$ssh->auth($unit['passwd'], $unit['address'])) {
            System::outjs(['e' => System::text('error', 'ssh')]);
        }

        $sql->query('SELECT `install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
        $tarif = $sql->get();

        $ssh->set('cat ' . $tarif['install'] . '/' . $server['uid'] . '/csgo/cfg/server.cfg | grep rcon_password');
        $get = explode(' ', str_replace('"', '', trim($ssh->get())));
        $rcon = trim(end($get));

        if (!isset($rcon[0])) {
            System::outjs(['r' => 'Необходимо установить rcon пароль (rcon_password).', 'url' => $cfg['http'] . 'servers/id/' . $server['id'] . '/section/settings/subsection/server'], $nmch);
        }

        return $rcon;
    }

    public static function country($ip)
    {
        global $SxGeo;

        $cData = $SxGeo->getCityFull($ip);
        $ico = System::country($cData['country']['iso']);

        return ['ico' => $ico, 'name' => empty($cData['country']['name_ru']) ? 'Не определена' : $cData['country']['name_ru']];
    }
}

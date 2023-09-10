<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

class rcon
{
    public static function cmd($server, $cmd = 'status')
    {
        require(LIB . 'games/query/Buffer.php');
        require(LIB . 'games/query/BaseSocket.php');
        require(LIB . 'games/query/Socket.php');
        require(LIB . 'games/query/GoldSourceRcon.php');
        require(LIB . 'games/query/SourceQuery.php');

        $sq = new SourceQuery();

        [$ip, $port] = explode(':', (string) $server['address']);

        $sq->Connect($ip, $port, 3, SourceQuery::GOLDSOURCE);

        $sq->SetRconPassword(rcon::rcon_passwd($server));

        $out = $sq->Rcon($cmd);

        $sq->Disconnect();

        return $out;
    }

    public static function players($data)
    {
        $aPlayers = [];
        $n = 1;

        $lines = explode("\n", (string) $data);

        foreach ($lines as $line) {
            if (!str_contains($line, '#'))
                continue;

            $start = strpos($line, '"') + 1;
            $end = strrpos($line, '"');

            $name = htmlspecialchars(substr($line, $start, $end - $start));

            $line = trim(substr($line, $end + 1));

            $aData = array_values(array_diff(explode(' ', $line), ['', ' ']));

            $steamid = trim($aData[1]);
            $ip = trim((string) sys::first(explode(':', $aData[6])));

            if (sys::valid($steamid, 'steamid') || sys::valid($ip, 'ip'))
                continue;

            $aPlayers[$n]['name'] = $name;
            $aPlayers[$n]['steamid'] = $steamid;
            $aPlayers[$n]['frags'] = trim($aData[2]);
            $aPlayers[$n]['time'] = trim($aData[3]);
            $aPlayers[$n]['ping'] = trim($aData[4]);
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
        global $cfg, $sql, $user;

        require(LIB . 'ssh.php');

        $sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
        $unit = $sql->get();

        if (!$ssh->auth($unit['passwd'], $unit['address']))
            sys::outjs(['e' => sys::text('error', 'ssh')]);

        $sql->query('SELECT `install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
        $tarif = $sql->get();

        $ssh->set('cat ' . $tarif['install'] . $server['uid'] . '/cstrike/server.cfg | grep rcon_password');
        $get = explode(' ', str_replace('"', '', trim((string) $ssh->get())));
        $rcon = trim(end($get));

        if (!isset($rcon[0]))
            sys::outjs(['r' => 'Необходимо установить rcon пароль (rcon_password).', 'url' => $cfg['http'] . 'servers/id/' . $server['id'] . '/section/settings/subsection/server'], $nmch);

        return $rcon;
    }

    public static function country($ip)
    {
        global $SxGeo;

        $cData = $SxGeo->getCityFull($ip);
        $ico = sys::country($cData['country']['iso']);

        return ['ico' => $ico, 'name' => empty($cData['country']['name_ru']) ? 'Не определена' : $cData['country']['name_ru']];
    }
}


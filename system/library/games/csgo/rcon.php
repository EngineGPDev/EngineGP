<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class rcon
	{
		public static function cmd($server, $cmd = 'status')
		{
			include(LIB.'games/query/Buffer.php');
			include(LIB.'games/query/BaseSocket.php');
			include(LIB.'games/query/Socket.php');
			include(LIB.'games/query/SourceRcon.php');
			include(LIB.'games/query/SourceQuery.php');

			$sq = new SourceQuery();

			list($ip, $port) = explode(':', $server['address']);

			$sq->Connect($ip, $port, 3, SourceQuery::SOURCE);

			$sq->SetRconPassword(rcon::rcon_passwd($server));

			$out = $sq->Rcon($cmd);

			$sq->Disconnect();

			return $out;
		}

		public static function players($data)
		{
			$aPlayers = array();
			$n = 1;

			$lines = explode("\n", $data);

			foreach($lines as $line)
			{
				if(strpos($line, '#') === FALSE)
					continue;

				$start = strpos($line, '"')+1;
				$end = strrpos($line, '"');

				$userid = sys::int(substr($line, 0, $start));

				$name = htmlspecialchars(substr($line, $start, $end-$start));

				$line = trim(substr($line, $end + 1));

				$aData = array_values(array_diff(explode(' ', $line), array('', ' ')));

				$steamid = trim($aData[0]);
				$ip = trim(sys::first(explode(':', $aData[5])));

				if((sys::valid($steamid, 'steamid') AND sys::valid($steamid, 'steamid3')) || sys::valid($ip, 'ip'))
					continue;

				$aPlayers[$n]['userid'] = $userid;
				$aPlayers[$n]['name'] = $name;
				$aPlayers[$n]['steamid'] = $steamid;
				$aPlayers[$n]['time'] = trim($aData[1]);
				$aPlayers[$n]['ping'] = trim($aData[2]);
				$aPlayers[$n]['ip'] = $ip;

				$whois = rcon::country($ip);

				$aPlayers[$n]['ico'] = $whois['ico'];
				$aPlayers[$n]['country'] = $whois['name'];

				$n+=1;
			}

			return $aPlayers;
		}

		public static function rcon_passwd($server)
		{
			global $cfg, $sql, $user;

			include(LIB.'ssh.php');

			$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="'.$server['unit'].'" LIMIT 1');
            $unit = $sql->get();

			if(!$ssh->auth($unit['passwd'], $unit['address']))
                sys::outjs(array('e' => sys::text('error', 'ssh')));

			$sql->query('SELECT `install` FROM `tarifs` WHERE `id`="'.$server['tarif'].'" LIMIT 1');
			$tarif = $sql->get();

			$ssh->set('cat '.$tarif['install'].'/'.$server['uid'].'/csgo/cfg/server.cfg | grep rcon_password');
			$get = explode(' ', str_replace('"', '', trim($ssh->get())));
			$rcon = trim(end($get));

			if(!isset($rcon{0}))
				sys::outjs(array('r' => 'Необходимо установить rcon пароль (rcon_password).', 'url' => $cfg['http'].'servers/id/'.$server['id'].'/section/settings/subsection/server'), $nmch);

			return $rcon;
		}

		public static function country($ip)
		{
			global $SxGeo;

			$cData = $SxGeo->getCityFull($ip);
			$ico = sys::country($cData['country']['iso']);

			return array('ico' => $ico, 'name' => empty($cData['country']['name_ru']) ? 'Не определена' : $cData['country']['name_ru']);
		}
	}
?>
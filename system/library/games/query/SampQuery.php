<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class SampQuery
	{
		private $stack = null;

		public function __construct($ip, $port = 7777)
		{
			$this->stack = fsockopen('udp://'.$ip, $port, $errorNum, $errorString, 2);

			socket_set_timeout($this->stack, 2);
		}

		public function getInfo()
		{
			@fwrite($this->stack, $this->assemblePacket('i'));

			fread($this->stack, 11);

			$serverInfo = array();

			$serverInfo['password'] = sys::int(ord(fread($this->stack, 1)));

			$serverInfo['players'] = sys::int($this->toInt(fread($this->stack, 2)));

			$serverInfo['maxplayers'] = sys::int($this->toInt(fread($this->stack, 2)));

			$strLen = ord(fread($this->stack, 4));

			if(!$strLen)
				return -1;

			$serverInfo['hostname'] = fread($this->stack, $strLen);

			$strLen = ord(fread($this->stack, 4));
			$serverInfo['gamemode'] = fread($this->stack, $strLen);

			$strLen = ord(fread($this->stack, 4));
			$serverInfo['map'] = fread($this->stack, $strLen);

			return $serverInfo;
		}

		public function getDetailedPlayers()
		{
			@fwrite($this->stack, $this->assemblePacket('d'));
			fread($this->stack, 11);

			$playerCount = ord(fread($this->stack, 2));
			$players = array();

			for($i = 0; $i < $playerCount; ++$i)
			{
				$player['playerid'] = ord(fread($this->stack, 1));

				$strLen = ord(fread($this->stack, 1));
				$player['nickname'] = fread($this->stack, $strLen);

				$player['ping'] = $this->toInt(fread($this->stack, 4));

				$players[$i] = $player;

				unset($player);
			}

			return $players;
		}

		private function toInt($string)
		{
			if($string === '')
				return null;

			$int = 0;
			$int += (ord($string[0]));

			if(isset($string[1]))
				$int += (ord($string[1]) << 8);

			if(isset($string[2]))
				$int += (ord($string[2]) << 16);

			if(isset($string[3]))
				$int += (ord($string[3]) << 24);

			if($int >= 4294967294)
				$int -= 4294967296;

			return $int;
		}

		private function assemblePacket($type)
		{
			$packet = 'SAMP';
			$packet .= chr(strtok($this->server, '.'));
			$packet .= chr(strtok('.'));
			$packet .= chr(strtok('.'));
			$packet .= chr(strtok('.'));
			$packet .= chr($this->port & 0xFF);
			$packet .= chr($this->port >> 8 & 0xFF);
			$packet .= $type;

			return $packet;
		}

		public function connect()
		{
			$connected = false;
			fwrite($this->stack, $this->assemblePacket('p0101'));

			if(fread($this->stack, 10))
			{
				if(fread($this->stack, 5) == 'p0101')
					$connected = true;
			}

			return $connected;
		}
	}
?>
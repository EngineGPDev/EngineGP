<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class McQuery
	{
		private $stack = null;

		public function __construct($ip, $port = 25565)
		{
			$this->stack = fsockopen('udp://'.$ip, $port, $errorNum, $errorString, 2);

			socket_set_timeout($this->stack, 2);
		}

		public function getInfo($pl = false)
		{
			$Data = $this->WriteData(0x00, $this->GetChallenge().Pack('c*', 0x00, 0x00, 0x00, 0x00));

			$server_info = array();
			
			$Data = substr($Data, 11);
			$Data = explode("\x00\x00\x01player_\x00\x00", $Data);
			$aData = explode("\x00", $Data[0]);

			$last = '';

			$keys = Array(
				'hostname' => 'hostname',
				'version' => 'version',
				'plugins' => 'plugins',
				'map' => 'map',
				'numplayers' => 'online'
			);

			foreach($aData as $index => $val)
			{
				if(~$index & 1)
				{
					if(!array_key_exists($val, $keys))
					{
						$last = false;
						continue;
					}

					$last = $keys[$val];
					$server_info[$last] = '';

				}elseif($last != false)
					$server_info[$last] = $val;
			}

			if(!count($server_info))
				return NULL;

			if(!$pl)
				return $server_info;

			$server_info['players_list'] = explode("\x00", substr($Data[1], 0, -2));

			if(!isset($server_info['players_list'][1]))
				$server_info['players_list'] = array();

			return $server_info;
		}

		private function GetChallenge()
		{
			$Data = $this->WriteData(0x09);
			
			if($Data === false)
				return NULL;

			return Pack('N', $Data);
		}

		private function WriteData($Command, $Append = '')
		{
			$Command = Pack('c*', 0xFE, 0xFD, $Command, 0x01, 0x02, 0x03, 0x04).$Append;
			$Length = StrLen($Command);
			
			if($Length !== fwrite($this->stack, $Command, $Length))
				return NULL;
			
			$Data = fread($this->stack, 2048);
			
			if($Data === false)
				return NULL;
			
			if(StrLen($Data) < 5 || $Data[0] != $Command[2])
				return false;

			return SubStr($Data, 5);
		}
	}
?>
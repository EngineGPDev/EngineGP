<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class MtaQuery
	{
		private $stack = null;

		public function __construct($ip, $port = 22003)
		{
			$this->stack = fsockopen('udp://'.$ip, $port+123, $errorNum, $errorString, 2);

			socket_set_timeout($this->stack, 2);
		}

		public function getInfo($pl = false)
		{
			fwrite($this->stack, 's');

			$buffer = fread($this->stack, 4096);

			if(!$buffer)
				return NULL;

			$buffer = substr($buffer, 4);

			$server_info = array();

			$server_info['gamename'] = $this->cut_pascal($buffer, 1, -1);
			$server_info['hostport'] = $this->cut_pascal($buffer, 1, -1);
			$server_info['hostname'] = $this->cut_pascal($buffer, 1, -1);
			$server_info['gamemode'] = $this->cut_pascal($buffer, 1, -1);
			$server_info['map'] = $this->cut_pascal($buffer, 1, -1);
			$server_info['version'] = $this->cut_pascal($buffer, 1, -1);
			$server_info['password'] = $this->cut_pascal($buffer, 1, -1);
			$server_info['players'] = $this->cut_pascal($buffer, 1, -1);
			$server_info['playersmax'] = $this->cut_pascal($buffer, 1, -1);

			while($buffer && $buffer[0] != "\x01")
			{
				$item_key = strtolower($this->cut_pascal($buffer, 1, -1));
				$item_value = $this->cut_pascal($buffer, 1, -1);

				$server_info[$item_key] = $item_value;
			}

			if(!$pl)
				return $server_info;

			$buffer = substr($buffer, 1);

			$i = 1;

			while($buffer)
			{
				$bit_flags = $this->cut_byte($buffer, 1); 

				$field_list = array('name', '', '', '', 'ping', '');

				foreach($field_list as $item_key)
				{
					$item_value = $this->cut_pascal($buffer, 1, -1);

					if(!$item_key)
						continue;

					$server_info['players_list'][$i][$item_key] = $item_value;
				}

				$i+=1;
			}

			return $server_info;
		}

		private function cut_pascal(&$buffer, $start_byte = 1, $length_adjust = 0, $end_byte = 0)
		{
			$length = ord(substr($buffer, 0, $start_byte)) + $length_adjust;
			$string = substr($buffer, $start_byte, $length);
			$buffer = substr($buffer, $start_byte + $length + $end_byte);

			return $string;
		}

		private function cut_byte(&$buffer, $length)
		{
			$string = substr($buffer, 0, $length);
			$buffer = substr($buffer, $length);

			return $string;
		}
	}
?>
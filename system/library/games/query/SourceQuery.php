<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class SourceQuery
	{
		const GOLDSOURCE = 0;
		const SOURCE = 1;
		const A2S_PING = 0x69;
		const A2S_INFO = 0x54;
		const A2S_PLAYER = 0x55;
		const A2S_RULES = 0x56;
		const A2S_SERVERQUERY_GETCHALLENGE = 0x57;
		const S2A_PING = 0x6A;
		const S2A_CHALLENGE = 0x41;
		const S2A_INFO = 0x49;
		const S2A_INFO_OLD = 0x6D;
		const S2A_PLAYER = 0x44;
		const S2A_RULES = 0x45;
		const S2A_RCON = 0x6C;
		const SERVERDATA_EXECCOMMAND = 2;
		const SERVERDATA_AUTH = 3;
		const SERVERDATA_RESPONSE_VALUE = 0;
		const SERVERDATA_AUTH_RESPONSE  = 2;

		private $Rcon;
		private $Socket;
		private $Connected;
		private $Challenge;
		private $UseOldGetChallengeMethod;

		public function __construct(BaseSocket $Socket = null)
		{
			$this->Socket = $Socket ?: new Socket();
		}
		
		public function __destruct()
		{
			$this->Disconnect();
		}

		public function Connect($Address, $Port, $Timeout = 3, $Engine = self::SOURCE)
		{
			$this->Disconnect();
			
			if(!is_int($Timeout) || $Timeout < 0)
				return false;

			$this->Socket->Open($Address, (int)$Port, $Timeout, (int)$Engine);

			$this->Connected = true;
		}

		public function SetUseOldGetChallengeMethod($Value)
		{
			$Previous = $this->UseOldGetChallengeMethod;

			$this->UseOldGetChallengeMethod = $Value === true;

			return $Previous;
		}

		public function Disconnect()
		{
			$this->Connected = false;
			$this->Challenge = 0;

			$this->Socket->Close();

			if($this->Rcon)
			{
				$this->Rcon->Close();

				$this->Rcon = null;
			}
		}

		public function Ping()
		{
			if(!$this->Connected)
				return false;

			$this->Socket->Write(self::A2S_PING);
			$Buffer = $this->Socket->Read();

			return $Buffer->GetByte() === self::S2A_PING;
		}

		public function GetInfo()
		{
			if(!$this->Connected)
				return false;

			$this->Socket->Write(self::A2S_INFO, "Source Engine Query\0");
			$Buffer = $this->Socket->Read();

			$Type = $Buffer->GetByte();

			if($Type === self::S2A_INFO_OLD && $this->Socket->Engine === self::GOLDSOURCE)
			{
				$Server['Address'] = $Buffer->GetString();
				$Server['HostName'] = $Buffer->GetString();
				$Server['Map'] = $Buffer->GetString();
				$Server['ModDir'] = $Buffer->GetString();
				$Server['ModDesc'] = $Buffer->GetString();
				$Server['Players'] = $Buffer->GetByte();
				$Server['MaxPlayers'] = $Buffer->GetByte();
				$Server['Protocol'] = $Buffer->GetByte();
				$Server['Dedicated'] = Chr($Buffer->GetByte());
				$Server['Os'] = Chr($Buffer->GetByte());
				$Server['Password'] = $Buffer->GetByte() === 1;
				$Server['IsMod'] = $Buffer->GetByte() === 1;

				if($Server['IsMod'])
				{
					$Mod['Url'] = $Buffer->GetString();
					$Mod['Download'] = $Buffer->GetString();
					$Buffer->Get(1);
					$Mod['Version'] = $Buffer->GetLong();
					$Mod['Size'] = $Buffer->GetLong();
					$Mod['ServerSide'] = $Buffer->GetByte() === 1;
					$Mod['CustomDLL'] = $Buffer->GetByte() === 1;
				}

				$Server['Secure'] = $Buffer->GetByte() === 1;
				$Server['Bots'] = $Buffer->GetByte();

				if(isset($Mod))
					$Server['Mod'] = $Mod;

				return $Server;
			}

			if($Type !== self::S2A_INFO)
				return false;

			if($Type !== self::S2A_INFO)
				return false;

			$Server['Protocol'] = $Buffer->GetByte();
			$Server['HostName'] = $Buffer->GetString();
			$Server['Map'] = $Buffer->GetString();
			$Server['ModDir'] = $Buffer->GetString();
			$Server['ModDesc'] = $Buffer->GetString();
			$Server['AppID'] = $Buffer->GetShort();
			$Server['Players'] = $Buffer->GetByte();
			$Server['MaxPlayers'] = $Buffer->GetByte();
			$Server['Bots'] = $Buffer->GetByte();
			$Server['Dedicated'] = Chr($Buffer->GetByte());
			$Server['Os'] = Chr($Buffer->GetByte());
			$Server['Password'] = $Buffer->GetByte() === 1;
			$Server['Secure'] = $Buffer->GetByte() === 1;

			if($Server['AppID'] === 2400)
			{
				$Server['GameMode'] = $Buffer->GetByte();
				$Server['WitnessCount'] = $Buffer->GetByte();
				$Server['WitnessTime'] = $Buffer->GetByte();
			}

			$Server['Version'] = $Buffer->GetString();

			if($Buffer->Remaining() > 0)
			{
				$Server['ExtraDataFlags'] = $Flags = $Buffer->GetByte();

				if($Flags & 0x80)
					$Server['GamePort'] = $Buffer->GetShort();

				if($Flags & 0x10)
				{
					$SteamIDLower = $Buffer->GetUnsignedLong();
					$SteamIDInstance = $Buffer->GetUnsignedLong();
					$SteamID = 0;

					if(PHP_INT_SIZE === 4)
					{
						if(extension_loaded('gmp'))
						{
							$SteamIDLower = gmp_abs($SteamIDLower);
							$SteamIDInstance = gmp_abs($SteamIDInstance);
							$SteamID = gmp_strval(gmp_or($SteamIDLower, gmp_mul($SteamIDInstance, gmp_pow(2, 32))));
						}else
							return false;
					}else
						$SteamID = $SteamIDLower | ($SteamIDInstance << 32);

					$Server['SteamID'] = $SteamID;

					unset($SteamIDLower, $SteamIDInstance, $SteamID);
				}

				if($Flags & 0x40)
				{
					$Server['SpecPort'] = $Buffer->GetShort();
					$Server['SpecName'] = $Buffer->GetString();
				}

				if($Flags & 0x20)
					$Server['GameTags'] = $Buffer->GetString();

				if($Flags & 0x01)
					$Server['GameID'] = $Buffer->GetUnsignedLong() | ($Buffer->GetUnsignedLong() << 32);

				if($Buffer->Remaining() > 0)
					return false;
			}

			return $Server;
		}

		public function GetPlayers()
		{
			if(!$this->Connected)
				return false;

			$this->GetChallenge(self::A2S_PLAYER, self::S2A_PLAYER);

			$this->Socket->Write(self::A2S_PLAYER, $this->Challenge);
			$Buffer = $this->Socket->Read(14000);
			$Type = $Buffer->GetByte();

			if($Type !== self::S2A_PLAYER)
				return false;

			$Players = [];
			$Count = $Buffer->GetByte();

			while($Count-- > 0 && $Buffer->Remaining() > 0)
			{
				$Player['Id'] = $Buffer->GetByte();
				$Player['Name'] = $Buffer->GetString();
				$Player['Frags'] = $Buffer->GetLong();
				$Player['Time'] = (int)$Buffer->GetFloat();
				$Player['TimeF'] = GMDate(($Player['Time'] > 3600 ? "H:i:s" : "i:s"), $Player['Time']);

				$Players[] = $Player;
			}

			return $Players;
		}

		public function GetRules()
		{
			if(!$this->Connected)
				return false;

			$this->GetChallenge(self::A2S_RULES, self::S2A_RULES);
			$this->Socket->Write(self::A2S_RULES, $this->Challenge);
			$Buffer = $this->Socket->Read();
			$Type = $Buffer->GetByte();

			if($Type !== self::S2A_RULES)
				return false;

			$Rules = [];
			$Count = $Buffer->GetShort();

			while($Count-- > 0 && $Buffer->Remaining() > 0)
			{
				$Rule = $Buffer->GetString();
				$Value = $Buffer->GetString();

				if(!empty($Rule))
					$Rules[$Rule] = $Value;
			}

			return $Rules;
		}

		private function GetChallenge($Header, $ExpectedResult)
		{
			if($this->Challenge)
				return;

			if($this->UseOldGetChallengeMethod)
				$Header = self::A2S_SERVERQUERY_GETCHALLENGE;

			$this->Socket->Write($Header, "\xFF\xFF\xFF\xFF");
			$Buffer = $this->Socket->Read();
			$Type = $Buffer->GetByte();

			switch($Type)
			{
				case self::S2A_CHALLENGE:
				{
					$this->Challenge = $Buffer->Get(4);

					return;
				}
				case $ExpectedResult:
				{
					return;
				}
				case 0:
				{
					return;
				}
				default:
				{
					return;
				}
			}
		}

		public function SetRconPassword($Password)
		{
			if(!$this->Connected)
			{
				return false;
			}

			switch($this->Socket->Engine)
			{
				case SourceQuery::GOLDSOURCE:
				{
					$this->Rcon = new GoldSourceRcon($this->Socket);

					break;
				}
				case SourceQuery::SOURCE:
				{
					$this->Rcon = new SourceRcon($this->Socket);

					break;
				}
			}

			$this->Rcon->Open();
			$this->Rcon->Authorize($Password);
		}

		public function Rcon($Command)
		{
			if(!$this->Connected)
			{
				return false;
			}
			
			if($this->Rcon === null)
			{
				return false;
			}

			return $this->Rcon->Command($Command);
		}
	}
<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class Buffer
	{

		private $Buffer;
		private $Length;
		private $Position;

		public function Set($Buffer)
		{
			$this->Buffer = $Buffer;
			$this->Length = StrLen($Buffer);
			$this->Position = 0;
		}

		public function Remaining()
		{
			return $this->Length - $this->Position;
		}

		public function Get($Length = -1)
		{
			if($Length === 0)
				return '';

			$Remaining = $this->Remaining();

			if($Length === -1)
				$Length = $Remaining;

			else if($Length > $Remaining)
				return '';

			$Data = SubStr($this->Buffer, $this->Position, $Length);

			$this->Position += $Length;

			return $Data;
		}

		public function GetByte()
		{
			return Ord($this->Get(1));
		}

		public function GetShort()
		{
			if($this->Remaining() < 2)
				return false;

			$Data = UnPack('v', $this->Get(2));

			return $Data[ 1 ];
		}

		public function GetLong()
		{
			if($this->Remaining() < 4)
				return false;

			$Data = UnPack('l', $this->Get(4));

			return $Data[ 1 ];
		}

		public function GetFloat()
		{
			if($this->Remaining() < 4)
				return false;

			$Data = UnPack('f', $this->Get(4));

			return $Data[ 1 ];
		}

		public function GetUnsignedLong()
		{
			if($this->Remaining() < 4)
				return false;

			$Data = UnPack('V', $this->Get(4));

			return $Data[ 1 ];
		}

		public function GetString()
		{
			$ZeroBytePosition = StrPos($this->Buffer, "\0", $this->Position);

			if($ZeroBytePosition === false)
				return '';

			$String = $this->Get($ZeroBytePosition - $this->Position);

			$this->Position++;

			return $String;
		}
	}
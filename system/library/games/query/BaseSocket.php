<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	abstract class BaseSocket
	{
		public $Socket;
		public $Engine;
		public $Address;
		public $Port;
		public $Timeout;

		public function __destruct()
		{
			$this->Close();
		}

		abstract public function Close();
		abstract public function Open($Address, $Port, $Timeout, $Engine);
		abstract public function Write($Header, $String = '');
		abstract public function Read($Length = 1400);

		protected function ReadInternal($Buffer, $Length, $SherlockFunction)
		{
			if($Buffer->Remaining() === 0)
				return false;

			if($Buffer->Remaining() === 0)
				return false;

			$Header = $Buffer->GetLong();

			if($Header === -2)
			{
				$Packets = [];
				$IsCompressed = false;
				$ReadMore = false;

				do
				{
					$RequestID = $Buffer->GetLong();

					switch($this->Engine)
					{
						case SourceQuery::GOLDSOURCE:
						{
							$PacketCountAndNumber = $Buffer->GetByte();
							$PacketCount = $PacketCountAndNumber & 0xF;
							$PacketNumber = $PacketCountAndNumber >> 4;

							break;
						}

						case SourceQuery::SOURCE:
						{
							$IsCompressed = ($RequestID & 0x80000000) !== 0;
							$PacketCount = $Buffer->GetByte();
							$PacketNumber = $Buffer->GetByte() + 1;

							if($IsCompressed)
							{
								$Buffer->GetLong();
								
								$PacketChecksum = $Buffer->GetUnsignedLong();
							}else
								$Buffer->GetShort();

							break;
						}
					}

					$Packets[$PacketNumber] = $Buffer->Get();

					$ReadMore = $PacketCount > sizeof($Packets);
				}

				while($ReadMore && $SherlockFunction($Buffer, $Length));

				$Data = Implode($Packets);

				if($IsCompressed)
				{
					if(!Function_Exists('bzdecompress'))
						return false;

					$Data = bzdecompress($Data);

					if(CRC32($Data) !== $PacketChecksum)
						return false;
				}

				$Buffer->Set(SubStr($Data, 4));
			}else
				return false;

			return $Buffer;
		}
	}
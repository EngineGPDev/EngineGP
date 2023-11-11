<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class GoldSourceRcon
	{
		private $Socket;

		private $RconPassword;
		private $RconRequestId;
		private $RconChallenge;

		public function __construct($Socket)
		{
			$this->Socket = $Socket;
		}

		public function Close()
		{
			$this->RconChallenge = 0;
			$this->RconRequestId = 0;
			$this->RconPassword  = 0;
		}

		public function Open()
		{
			//
		}

		public function Write($Header, $String = '')
		{
			$Command = Pack('cccca*', 0xFF, 0xFF, 0xFF, 0xFF, $String);
			$Length  = StrLen($Command);

			return $Length === FWrite($this->Socket->Socket, $Command, $Length);
		}

		public function Read($Length = 1400)
		{
			$Buffer = $this->Socket->Read();

			$StringBuffer = '';
			$ReadMore = false;

			do
			{
				$ReadMore = $Buffer->Remaining() > 0;

				if($ReadMore)
				{
					if($Buffer->GetByte() !== SourceQuery::S2A_RCON)
						sys::outjs(array('e' => 'неправильный rcon запрос.'));

					$Packet = $Buffer->Get();
					$StringBuffer .= $Packet;

					$ReadMore = StrLen($Packet) > 1000;

					if($ReadMore)
						$Buffer = $this->Socket->Read();
				}
			}

			while($ReadMore);

			$Trimmed = trim($StringBuffer);

			if($Trimmed === 'Bad rcon_password.')
				sys::outjs(array('e' => 'rcon_password из server.cfg не подходит.'));

			else if($Trimmed === 'You have been banned from this server.')
				sys::outjs(array('e' => 'Игровой сервер заблокировал доступ.'));

			$Buffer->Set($Trimmed);

			return $Buffer;
		}

		public function Command($Command)
		{
			if(!$this->RconChallenge)
				return false;

			$this->Write(0, 'rcon ' . $this->RconChallenge . ' "' . $this->RconPassword . '" ' . $Command . "\0");
			$Buffer = $this->Read();

			if($Buffer)
				return $Buffer->Get();

			return $Buffer;
		}

		public function Authorize($Password)
		{
			$this->RconPassword = $Password;

			$this->Write(0, 'challenge rcon');
			$Buffer = $this->Socket->Read();

			if($Buffer->Get(14) !== 'challenge rcon')
				sys::outjs(array('e' => 'Не удалось выполнить rcon запрос.'));

			$this->RconChallenge = Trim($Buffer->Get());
		}
	}
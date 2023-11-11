<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class ssh
	{
		var $conn;
		var $stream;

		public function auth($passwd, $address)
		{
			if($this->connect($address) AND $this->auth_pwd('root', $passwd))
				return true;

			return false;
		}

		public function connect($address)
		{
			list($host, $port) = explode(':', $address);

			if($port == '')
				$port = 22;

			ini_set('default_socket_timeout', '3');

			if($this->conn = ssh2_connect($host, $port))
			{
				ini_set('default_socket_timeout', '180');

				return true;
			}

			return false;
		}

		public function setfile($localFile, $remoteFile, $permision)
		{
			if(@ssh2_scp_send($this->conn, $localFile, $remoteFile, $permision))
				return true;

			return false;
		}

		public function getfile($remoteFile, $localFile)
		{
			if(@ssh2_scp_recv($this->conn, $remoteFile, $localFile))
				return true;

			return false;
		}

		public function set($cmd)
		{
			$this->stream = ssh2_exec($this->conn, $cmd);

			stream_set_blocking($this->stream, true);
		}

		public function auth_pwd($u, $p)
		{
			if(@ssh2_auth_password($this->conn, $u, $p))
				return true;

			return false;
		}

		public function get($cmd = false)
		{
			if($cmd)
			{
				$this->stream = ssh2_exec($this->conn, $cmd);

				stream_set_blocking($this->stream, true);
			}

			$line = '';

			while($get = fgets($this->stream))
				$line.= $get;

			return $line;
		}

		public function esc()
		{
			if(function_exists('ssh2_disconnect'))
				ssh2_disconnect($this->conn);
			else{
				@fclose($this->conn);
				unset($this->conn);
			}

			return NULL;
		}
	}

	$ssh = new ssh;
?>
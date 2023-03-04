<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

    class smtp
    {
        public $smtp_username;
        public $smtp_password;
        public $smtp_host;
        public $smtp_from;
        public $smtp_port;
        public $smtp_charset;

        public function __construct($smtp_username, $smtp_password, $smtp_host, $smtp_from, $smtp_port = 25, $smtp_charset = 'utf-8')
        {
            $this->smtp_username = $smtp_username;
            $this->smtp_password = $smtp_password;
            $this->smtp_host = $smtp_host;
            $this->smtp_from = $smtp_from;
            $this->smtp_port = $smtp_port;
            $this->smtp_charset = $smtp_charset;
        }

        function send($mailTo, $subject, $message, $headers)
        {
            global $cfg;

            $contentMail = 'Date: '.date('D, d M Y H:i:s')." UT\r\n";
            $contentMail .= 'Subject: =?'.$this->smtp_charset.'?B?'.base64_encode($subject)."=?=\r\n";
            $contentMail .= $headers."\r\n";
            $contentMail .= $message."\r\n";

			try
            {
                if(!$socket = @fsockopen($this->smtp_host, $this->smtp_port, $errorNumber, $errorDescription, 30))
                    throw new Exception($errorNumber.'.'.$errorDescription);

                if(!$this->_parseServer($socket, '220'))
                    throw new Exception('Connection error');

                $server_name = $cfg['url'];

                fputs($socket, 'HELO '.$server_name."\r\n");

                if(!$this->_parseServer($socket, '250'))
                {
                    fclose($socket);
                    throw new Exception('Error of command sending: HELO');
                }

                fputs($socket, 'AUTH LOGIN'."\r\n");

                if(!$this->_parseServer($socket, '334'))
                {
                    fclose($socket);
                    throw new Exception('Autorization error');
                }

                fputs($socket, base64_encode($this->smtp_username)."\r\n");

                if(!$this->_parseServer($socket, '334'))
                {
                    fclose($socket);
                    throw new Exception('Autorization error');
                }

                fputs($socket, base64_encode($this->smtp_password)."\r\n");

                if(!$this->_parseServer($socket, '235'))
                {
                    fclose($socket);
                    throw new Exception('Autorization error');
                }

                fputs($socket, 'MAIL FROM: <'.$this->smtp_username.">\r\n");

                if(!$this->_parseServer($socket, '250'))
                {
                    fclose($socket);
                    throw new Exception('Error of command sending: MAIL FROM');
                }

                $mailTo = ltrim($mailTo, '<');
                $mailTo = rtrim($mailTo, '>');

                fputs($socket, 'RCPT TO: <'.$mailTo.">\r\n");

                if(!$this->_parseServer($socket, '250'))
                {
                    fclose($socket);
                    throw new Exception('Error of command sending: RCPT TO');
                }

                fputs($socket, 'DATA'."\r\n");  

                if(!$this->_parseServer($socket, "354"))
                {
                    fclose($socket);
                    throw new Exception('Error of command sending: DATA');
                }

                fputs($socket, $contentMail."\r\n.\r\n");

                if(!$this->_parseServer($socket, '250'))
                {
                    fclose($socket);
                    throw new Exception('E-mail didn\'t sent');
                }

                fputs($socket, 'QUIT'."\r\n");
                fclose($socket);
            }

            catch(Exception $e)
            {
                return $e->getMessage();
            }

            return true;
        }

        private function _parseServer($socket, $response)
        {
            while(@substr($responseServer, 3, 1) != ' ')
                if(!($responseServer = fgets($socket, 256)))
                    return false;

            if(!(substr($responseServer, 0, 3) == $response))
                return false;

            return true;
        }
    }
?>
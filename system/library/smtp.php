<?php
/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @copyright Copyright (c) 2018-present Solovev Sergei <inbox@seansolovev.ru>
 *
 * @link      https://github.com/EngineGPDev/EngineGP for the canonical source repository
 *
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE MIT License
 */

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

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
        $transport = Transport::fromDsn("smtp://{$this->smtp_username}:{$this->smtp_password}@{$this->smtp_host}:{$this->smtp_port}");
        $mailer = new Mailer($transport);

        $email = (new Email())
            ->from($this->smtp_from)
            ->to($mailTo)
            ->subject($subject)
            ->html($message);

        try {
            $mailer->send($email);
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}

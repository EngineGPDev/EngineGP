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

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

use phpseclib3\Net\SSH2;
use phpseclib3\Net\SFTP;

class ssh
{
    public $conn;
    public $sftp;
    public $stream;
    private $alternativeInterfaces = ['enp3s0', 'enp0s31f6', 'enp0s3', 'ens3', 'eth0'];

    public function auth($passwd, $address)
    {
        if ($this->connect($address) and $this->auth_pwd('root', $passwd)) {
            return true;
        }

        return false;
    }

    public function connect($address)
    {
        if (strpos($address, ':') !== false) {
            list($host, $port) = explode(':', $address);
        } else {
            $host = $address;
            $port = 22;
        }

        // SSH2
        $this->conn = new SSH2($host, $port);
        $this->conn->setTimeout(180);

        // SFTP
        $this->sftp = new SFTP($host, $port);
        $this->sftp->setTimeout(180);

        return $this->conn && $this->sftp;
    }

    public function setfile($localFile, $remoteFile)
    {
        if ($this->sftp->isConnected() && $this->sftp->isAuthenticated()) {
            if ($this->sftp->put($remoteFile, $localFile, SFTP::SOURCE_LOCAL_FILE)) {
                return true;
            }
        }

        return false;
    }

    public function getfile($remoteFile, $localFile)
    {
        if ($this->sftp->isConnected() && $this->sftp->isAuthenticated()) {
            if ($this->sftp->get($remoteFile, $localFile)) {
                return true;
            }
        }

        return false;
    }

    public function set($cmd)
    {
        $this->stream = $this->conn->exec($cmd);
    }

    public function auth_pwd($u, $p)
    {
        if ($this->conn->login($u, $p) && $this->sftp->login($u, $p)) {
            return true;
        }

        return false;
    }

    public function get($cmd = false)
    {
        if ($cmd) {
            $this->stream = $this->conn->exec($cmd);
        }

        return $this->stream;
    }

    public function esc()
    {
        $this->conn->disconnect();
    }

    public function getInternalIp()
    {
        foreach ($this->alternativeInterfaces as $interface) {
            $command = "ip addr show $interface 2>/dev/null | grep 'inet ' | awk '{print \$2}' | cut -d/ -f1";
            $internal_ip = $this->get($command);
            if (!empty(trim($internal_ip))) {
                return trim($internal_ip);
            }
        }
    }
}

$ssh = new ssh();

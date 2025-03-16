<?php

/*
 * Copyright 2018-2025 Solovev Sergei
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace EngineGP\Infrastructure\RemoteAccess;

use phpseclib3\Net\SFTP;

/**
 * The SftpClient class is responsible for working with files over the SFTP protocol.
 */
class SftpClient extends RemoteConnection
{
    protected ?SFTP $sftp = null;

    /**
     * Establishes an SFTP connection.
     *
     * @throws \Exception if authentication failed
     */
    public function connect(): bool
    {
        $this->sftp = new SFTP($this->host, $this->port);
        if (!$this->sftp->login($this->username, $this->password)) {
            throw new \Exception("SFTP: Authorization error");
        }
        return true;
    }

    /**
     * Downloads a file from a remote server.
     *
     * @param string $remoteFile The path to the file on the server
     * @param string $localFile The path to save locally
     * @return bool Success of the operation
     * @throws \Exception
     */
    public function getFile(string $remoteFile, string $localFile): bool
    {
        if ($this->sftp === null) {
            $this->connect();
        }
        return $this->sftp->get($remoteFile, $localFile);
    }

    /**
     * Uploads a local file to the server.
     *
     * @param string $localFile The path to the local file
     * @param string $remoteFile The path to save the file on the server
     * @return bool Success of the operation
     * @throws \Exception
     */
    public function putFile(string $localFile, string $remoteFile): bool
    {
        if ($this->sftp === null) {
            $this->connect();
        }
        return $this->sftp->put($remoteFile, $localFile, SFTP::SOURCE_LOCAL_FILE);
    }

    /**
     * Closes the SFTP connection.
     */
    public function disconnect(): void
    {
        if ($this->sftp !== null) {
            $this->sftp->disconnect();
            $this->sftp = null;
        }
    }
}

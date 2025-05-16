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

use phpseclib3\Net\SSH2;

/**
 * The SSHClient class is responsible for executing SSH commands.
 */
class SshClient extends RemoteConnection
{
    protected ?SSH2 $ssh = null;

    /**
     * Establishes an SSH connection.
     *
     * @throws \Exception if authentication failed
     */
    public function connect(): bool
    {
        $this->ssh = new SSH2($this->host, $this->port);
        if (!$this->ssh->login($this->username, $this->password)) {
            throw new \Exception("SSH: Authorization error");
        }
        return true;
    }

    /**
     * Executes the command on the remote server via SSH.
     *
     * @param string $command The command to execute
     * @param bool $silent Executing a command in silent mode
     * @return string|null The result of the command execution
     * @throws \Exception
     */
    public function execute(string $command, bool $silent = true): ?string
    {
        if ($this->ssh === null) {
            $this->connect();
        }

        $output = $this->ssh->exec($command);

        return $silent ? null : $output;
    }

    /**
     * Closes the SSH connection.
     */
    public function disconnect(): void
    {
        if ($this->ssh !== null) {
            $this->ssh->disconnect();
            $this->ssh = null;
        }
    }
}

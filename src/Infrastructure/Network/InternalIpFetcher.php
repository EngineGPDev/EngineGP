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

namespace EngineGP\Infrastructure\Network;

use EngineGP\Infrastructure\RemoteAccess\SshClient;

/**
 * A class for getting an internal IP address.
 */
class InternalIpFetcher
{
    private SshClient $sshClient;

    public function __construct(SshClient $sshClient)
    {
        $this->sshClient = $sshClient;
    }

    /**
     * Receives IPv4.
     *
     * @return string
     * @throws \Exception
     */
    public function getInternalIp(): string
    {
        $command = "ip -o -4 addr show | awk '\$2 != \"lo\" {split(\$4, a, \"/\"); print a[1]}' | head -n 1";
        $internalIP = trim($this->sshClient->execute($command, false));

        if (empty($internalIP)) {
            throw new \Exception("Failed to retrieve internal IP");
        }

        return $internalIP;
    }
}

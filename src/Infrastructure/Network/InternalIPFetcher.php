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
    private array $alternativeInterfaces = ['enp3s0', 'enp0s31f6', 'enp0s3', 'ens3', 'eth0'];
    private SshClient $sshClient;

    public function __construct(SshClient $sshClient)
    {
        $this->sshClient = $sshClient;
    }

    public function getInternalIp()
    {
        foreach ($this->alternativeInterfaces as $interface) {
            $command = "ip addr show $interface 2>/dev/null | grep 'inet ' | awk '{print \$2}' | cut -d/ -f1";
            $internalIP = $this->sshClient->execute($command, false);
            if (!empty(trim($internalIP))) {
                return trim($internalIP);
            }
        }
    }
}

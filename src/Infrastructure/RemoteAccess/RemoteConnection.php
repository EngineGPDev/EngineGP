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

/**
 * The RemoteConnection class stores the general connection data.
 */
class RemoteConnection
{
    protected string $host;
    protected int $port;
    protected string $username;
    protected string $password;

    public function __construct(string $address, string $username, string $password)
    {
        $parts = explode(':', $address);
        $this->host     = $parts[0];
        $this->port     = isset($parts[1]) ? (int)$parts[1] : 22;
        $this->username = $username;
        $this->password = $password;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}

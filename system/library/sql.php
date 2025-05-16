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

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

class mysql
{
    public $sql_id = false;
    public $sql_connect = false;
    public $query = false;
    public $query_id = false;
    public $mysqlerror = '';

    public function connect_mysql($c, $u, $p, $n)
    {
        try {
            $this->sql_id = new mysqli($c, $u, $p, $n);

            if ($this->sql_id->connect_errno) {
                $error = $this->sql_id->connect_error;
                $this->out_error($error);
            }

            $this->sql_id->set_charset('utf8mb4');
            $this->sql_connect = true;
        } catch (Exception $e) {
            $this->out_error($e->getMessage());
        }
    }

    public function query($query)
    {
        if (!$this->sql_connect) {
            $this->connect_mysql(CONNECT_DATABASE, USER_DATABASE, PASSWORD_DATABASE, NAME_DATABASE);
        }

        $this->query_id = $this->sql_id->query($query);
        if (!$this->query_id && $this->sql_id->error && defined('ERROR_DATABASE') && ERROR_DATABASE) {
            $this->out_error($this->sql_id->error, $query);
        }

        return $this->query_id;
    }

    public function get($query_id = false)
    {
        if (!$query_id) {
            $query_id = $this->query_id;
        }

        if (!$query_id) {
            return null;
        }

        return $query_id->fetch_assoc();
    }

    public function num($query_id = false)
    {
        if (!$query_id) {
            $query_id = $this->query_id;
        }

        if (!$query_id) {
            return 0;
        }

        return $query_id->num_rows;
    }

    public function id()
    {
        return $this->sql_id->insert_id;
    }

    public function esc()
    {
        if ($this->sql_id) {
            $this->sql_id->close();
        }
    }

    private function out_error($error, $query = '')
    {
        global $go;

        if (isset($go) && $go) {
            sys::outjs(['e' => 'Query: ' . $query . '<br>Error: ' . $error]);
        }

        if ($query != '') {
            echo 'Query: ' . $query . '<br>';
        }

        echo 'Error: ' . $error;

        exit();
    }
}

$sql = new mysql();

<?php

/*
 * Copyright 2018-2024 Solovev Sergei
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

$sql->query('SELECT `key` FROM `api` WHERE `server`="' . $id . '" LIMIT 1');
if ($sql->num()) {
    $sql->query('DELETE FROM `api` WHERE `server`="' . $id . '" LIMIT 1');
} else {
    $sql->query('INSERT INTO `api` set `server`="' . $id . '", `key`="' . md5(sys::passwd(10)) . '"');
}

$mcache->delete('server_settings_' . $id);

sys::back($cfg['http'] . 'servers/id/' . $id . '/section/settings');

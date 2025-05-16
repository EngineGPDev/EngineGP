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

use EngineGP\System;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

$nmch = 'server_copy_check_' . $id;

if ($mcache->get($nmch)) {
    System::outjs(['e' => System::text('other', 'mcache')]);
}

$mcache->set($nmch, true, false, 10);

$copys = $sql->query('SELECT `id` FROM `copy` WHERE `user`="' . $server['user'] . '_' . $server['unit'] . '" AND `status`="0"');
if (!$sql->num($copys)) {
    System::outjs(['e' => 'no find'], $nmch);
}

while ($copy = $sql->get($copys)) {
    if (!System::int($ssh->get('ps aux | grep copy_' . $server['uid'] . ' | grep -v grep | awk \'{print $2}\''))) {
        $sql->query('UPDATE `copy` set `status`="1" WHERE `id`="' . $copy['id'] . '" LIMIT 1');
    }
}

// Очистка кеша
$mcache->delete('server_copy_' . $id);

System::outjs(['s' => 'ok'], $nmch);

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

$sql->query('SELECT `game`, `status` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
$server = $sql->get();

if (!isset($url['action'])) {
    System::outjs(['e' => 'Неверный запрос для выполнения операции']);
}

$nmch = 'server_action_' . $id;

if ($mcache->get($nmch)) {
    System::outjs(['e' => System::text('other', 'mcache')]);
}

$mcache->set($nmch, true, false, 10);

include(LIB . 'games/' . $server['game'] . '/action.php');

switch ($url['action']) {
    case 'stop':
        if (!in_array($server['status'], ['working', 'start', 'restart', 'change'])) {
            System::outjs(['e' => System::text('error', 'ser_stop')], $nmch);
        }

        System::outjs(action::stop($id), $nmch);

        // no break
    case 'start':
        if ($server['status'] != 'off') {
            System::outjs(['e' => System::text('error', 'ser_start')], $nmch);
        }

        System::outjs(action::start($id), $nmch);

        // no break
    case 'restart':
        if (!in_array($server['status'], ['working', 'start', 'restart', 'change'])) {
            System::outjs(['e' => System::text('error', 'ser_restart')], $nmch);
        }

        System::outjs(action::start($id, 'restart'), $nmch);

        // no break
    case 'change':
        if ($server['status'] != 'working') {
            if ($server['status'] == 'change') {
                System::outjs(['e' => System::text('other', 'mcache')], $nmch);
            }

            System::outjs(['e' => System::text('error', 'ser_change')], $nmch);
        }

        if (isset($url['change'])) {
            System::outjs(action::change($id, $url['change']), $nmch);
        }

        System::outjs(action::change($id), $nmch);

        // no break
    case 'reinstall':
        if ($server['status'] != 'off') {
            System::outjs(['e' => System::text('error', 'ser_reinstall')], $nmch);
        }

        System::outjs(action::reinstall($id), $nmch);

        // no break
    case 'update':
        if ($server['status'] != 'off') {
            System::outjs(['e' => System::text('error', 'ser_update')], $nmch);
        }

        System::outjs(action::update($id), $nmch);
}

exit;

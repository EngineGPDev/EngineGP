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

$sql->query('SELECT `game`, `status` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
$server = $sql->get();

if (!isset($url['action'])) {
    sys::outjs(['e' => 'Неверный запрос для выполнения операции']);
}

$nmch = 'server_action_' . $id;

if ($mcache->get($nmch)) {
    sys::outjs(['e' => sys::text('other', 'mcache')]);
}

$mcache->set($nmch, true, false, 10);

include(LIB . 'games/' . $server['game'] . '/action.php');

switch ($url['action']) {
    case 'stop':
        if (!in_array($server['status'], ['working', 'start', 'restart', 'change'])) {
            sys::outjs(['e' => sys::text('error', 'ser_stop')], $nmch);
        }

        sys::outjs(action::stop($id), $nmch);

        // no break
    case 'start':
        if ($server['status'] != 'off') {
            sys::outjs(['e' => sys::text('error', 'ser_start')], $nmch);
        }

        sys::outjs(action::start($id), $nmch);

        // no break
    case 'restart':
        if (!in_array($server['status'], ['working', 'start', 'restart', 'change'])) {
            sys::outjs(['e' => sys::text('error', 'ser_restart')], $nmch);
        }

        sys::outjs(action::start($id, 'restart'), $nmch);

        // no break
    case 'change':
        if ($server['status'] != 'working') {
            if ($server['status'] == 'change') {
                sys::outjs(['e' => sys::text('other', 'mcache')], $nmch);
            }

            sys::outjs(['e' => sys::text('error', 'ser_change')], $nmch);
        }

        if (isset($url['change'])) {
            sys::outjs(action::change($id, $url['change']), $nmch);
        }

        sys::outjs(action::change($id), $nmch);

        // no break
    case 'reinstall':
        if ($server['status'] != 'off') {
            sys::outjs(['e' => sys::text('error', 'ser_reinstall')], $nmch);
        }

        sys::outjs(action::reinstall($id), $nmch);

        // no break
    case 'update':
        if ($server['status'] != 'off') {
            sys::outjs(['e' => sys::text('error', 'ser_update')], $nmch);
        }

        sys::outjs(action::update($id), $nmch);
}

exit;

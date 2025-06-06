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

$options = '';

switch ($aWebInstall[$server['game']][$url['subsection']]) {
    case 'server':
        $sql->query('SELECT `domain`, `passwd`, `login`, `date` FROM `web` WHERE `type`="' . $url['subsection'] . '" AND `server`="' . $id . '" LIMIT 1');

        $options = '<option value="' . $id . '">#' . $id . ' ' . $server['name'] . ' (' . $server['address'] . ')</option>';

        break;

    case 'user':
        $sql->query('SELECT `id`, `address`, `name` FROM `servers` WHERE `user`="' . $server['user'] . '" AND (`status`!="overdue" OR `status`!="block")');
        while ($sers = $sql->get()) {
            $options .= '<option value="' . $sers['id'] . '">#' . $sers['id'] . ' ' . $sers['name'] . ' (' . $sers['address'] . ')</option>';
        }

        $sql->query('SELECT `domain`, `passwd`, `login`, `date` FROM `web` WHERE `type`="' . $url['subsection'] . '" AND `user`="' . $server['user'] . '" LIMIT 1');

        break;

    case 'unit':
        $sql->query('SELECT `id`, `address`, `name` FROM `servers` WHERE `unit`="' . $server['unit'] . '" AND `user`="' . $server['user'] . '" AND (`status`!="overdue" OR `status`!="block")');
        while ($sers = $sql->get()) {
            $options .= '<option value="' . $sers['id'] . '">#' . $sers['id'] . ' ' . $sers['name'] . ' (' . $sers['address'] . ')</option>';
        }

        $sql->query('SELECT `domain`, `passwd`, `login`, `date` FROM `web` WHERE `type`="' . $url['subsection'] . '" AND `user`="' . $server['user'] . '" AND `unit`="' . $server['unit'] . '" LIMIT 1');

        break;
}

if (!$sql->num()) {
    System::back($cfg['http'] . 'servers/id/' . $id . '/section/web/subsection/' . $url['subsection'] . '/action/install');
}

$web = $sql->get();

$html->nav('Управление ' . $aWebname[$url['subsection']]);

$html->get('manage', 'sections/web/' . $url['subsection'] . '/free');

$html->set('id', $id);

$html->set('url', $aWebUnit['isp']['panel']);
$html->set('login', $web['login']);
$html->set('passwd', $web['passwd']);
$html->set('date', System::today($web['date']));

$html->pack('main');

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

$sql->query('SELECT `domain`, `passwd`, `config`, `date` FROM `web` WHERE `type`="' . $url['subsection'] . '" AND `server`="' . $id . '" LIMIT 1');

if (!$sql->num()) {
    System::back($cfg['http'] . 'servers/id/' . $id . '/section/web/subsection/' . $url['subsection'] . '/action/install');
}

$web = $sql->get();

$html->nav('Управление ' . $aWebname[$url['subsection']]);

$html->get('manage', 'sections/web/' . $url['subsection'] . '/free');

$html->set('id', $id);

$html->set('url', $web['domain']);
$html->set('passwd', $web['passwd']);
$html->set('config', base64_decode($web['config']));
$html->set('servers', '<option value="' . $id . '">#' . $id . ' ' . $server['name'] . ' (' . $server['address'] . ')</option>');

if (in_array('update', $aAction[$url['subsection']])) {
    $html->unit('update', 1);
} else {
    $html->unit('update');
}

$html->pack('main');

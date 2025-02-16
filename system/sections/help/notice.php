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

if ($user['group'] == 'user') {
    $sql->query('SELECT `id` FROM `help` WHERE `user`="' . $user['id'] . '" AND `status`="0" AND `close`="0" LIMIT 1');
} else {
    $sql->query('SELECT `id` FROM `help` WHERE `status`="1" AND `close`="0" LIMIT 1');
}

if (!$sql->num()) {
    System::outjs(['empty' => '']);
}

if ($user['group'] != 'user') {
    $sql->query('SELECT `time` FROM `help` WHERE `status`="1" AND `close`="0" ORDER BY `time` DESC LIMIT 1');
    if ($sql->num()) {
        $help = $sql->get();

        System::outjs(['reply' => $help['time']]);
    }

    System::outjs(['empty' => '']);
}

$help = $sql->get();

$sql->query('SELECT `text`, `time` FROM `help_dialogs` WHERE `help`="' . $help['id'] . '" AND `user`!="' . $user['id'] . '" AND `time`>"' . ($start_point - 15) . '" ORDER BY `id` DESC LIMIT 1');
if (!$sql->num()) {
    System::outjs(['reply' => '']);
}

$msg = $sql->get();

if (strip_tags($msg['text'], '<br>,<p>') != $msg['text']) {
    System::outjs(['reply' => '']);
}

include(LIB . 'help.php');

$html->get('notice', 'sections/help');

$html->set('id', $help['id']);
$html->set('home', $cfg['http']);
$html->set('text', $msg['text']);
$html->set('ago', help::ago($msg['time']));

$html->pack('notice');

System::outjs(['notice' => $html->arr['notice']]);

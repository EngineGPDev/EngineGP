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

$info = '<i class="fa fa-gamepad"></i> Список серверов';

$aSection = [
    'index',
    'cs',
    'cssold',
    'css',
    'csgo',
    'cs2',
    'rust',
    'samp',
    'crmp',
    'mta',
    'mc',
    'overdue',
    'delete',
];

if (!in_array($section, $aSection)) {
    $section = 'index';
}

$del = $cfg['server_delete'] * 86400;
$time = $start_point - $del;

$html->get('menu', 'sections/servers');

$html->unit('s_' . $section, true);

unset($aSection[array_search($section, $aSection)]);

foreach ($aSection as $noactive) {
    $html->unit('s_' . $noactive);
}

$sql->query('SELECT `id` FROM `servers` WHERE `user`!="-1"');
$html->set('all', $sql->num());

$sql->query('SELECT `id` FROM `servers` WHERE `user`!="-1" AND `game`="cs"');
$html->set('cs', $sql->num());

$sql->query('SELECT `id` FROM `servers` WHERE `user`!="-1" AND `game`="cssold"');
$html->set('cssold', $sql->num());

$sql->query('SELECT `id` FROM `servers` WHERE `user`!="-1" AND `game`="css"');
$html->set('css', $sql->num());

$sql->query('SELECT `id` FROM `servers` WHERE `user`!="-1" AND `game`="csgo"');
$html->set('csgo', $sql->num());

$sql->query('SELECT `id` FROM `servers` WHERE `user`!="-1" AND `game`="cs2"');
$html->set('cs2', $sql->num());

$sql->query('SELECT `id` FROM `servers` WHERE `user`!="-1" AND `game`="rust"');
$html->set('rust', $sql->num());

$sql->query('SELECT `id` FROM `servers` WHERE `user`!="-1" AND `game`="samp"');
$html->set('samp', $sql->num());

$sql->query('SELECT `id` FROM `servers` WHERE `user`!="-1" AND `game`="crmp"');
$html->set('crmp', $sql->num());

$sql->query('SELECT `id` FROM `servers` WHERE `user`!="-1" AND `game`="mta"');
$html->set('mta', $sql->num());

$sql->query('SELECT `id` FROM `servers` WHERE `user`!="-1" AND `game`="mc"');
$html->set('mc', $sql->num());

$sql->query('SELECT `id` FROM `servers` WHERE `user`!="-1" AND `time`<"' . $start_point . '" AND `overdue`>"' . $time . '"');
$html->set('overdue', $sql->num());

$html->pack('menu');

include(SEC . 'servers/' . $section . '.php');

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

$info = '<i class="fa fa-gift"></i> Управление акциями';

$aSection = [
    'index',
    'add',
    'end',
    'stats',
    'delete',
];

if (!in_array($section, $aSection)) {
    $section = 'index';
}

$html->get('menu', 'sections/promo');

$html->unit('s_' . $section, true);

unset($aSection[array_search($section, $aSection)]);

foreach ($aSection as $noactive) {
    $html->unit('s_' . $noactive);
}

$sql->query('SELECT `id` FROM `promo` WHERE `time`>"' . $start_point . '"');
$html->set('active', $sql->num());

$sql->query('SELECT `id` FROM `promo` WHERE `time`<"' . $start_point . '"');
$html->set('end', $sql->num());

$html->pack('menu');

include(SEC . 'promo/' . $section . '.php');

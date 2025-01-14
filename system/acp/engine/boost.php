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

include(DATA . 'boost.php');

$info = '<i class="fa fa-cloud"></i> Статистика BOOST CS: 1.6';

$aSection = $aBoost['cs']['boost'];

if ($section == 'search') {
    include(SEC . 'boost/search.php');
}

if (!in_array($section, $aSection)) {
    $section = 'index';
}

$html->get('menu', 'sections/boost');

$boosts = '';

if ($section != 'index') {
    $html->unit('s_index');
} else {
    $html->unit('s_index', true);
}

foreach ($aSection as $service) {
    if ($section == $service) {
        $boosts .= '<li><a href="[acp]boost/section/' . $section . '" class="active"><i class="fa fa-list-ol"></i> ' . $aBoost['cs'][$section]['site'] . '</a></li>';
    } else {
        $boosts .= '<li><a href="[acp]boost/section/' . $service . '"><i class="fa fa-list-ol"></i> ' . $aBoost['cs'][$service]['site'] . '</a></li>';
    }
}

$html->set('boosts', $boosts);

$html->pack('menu');

$inc = $section != 'index' ? 'service' : 'index';

include(SEC . 'boost/' . $inc . '.php');

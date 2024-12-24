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

include(LIB . 'games/games.php');
include(LIB . 'games/tarifs.php');
include(LIB . 'games/' . $server['game'] . '/tarif.php');

// Выполнение операции
if (isset($url['subsection']) and in_array($url['subsection'], $aSub)) {
    $nmch = sys::rep_act('server_tarif_go_' . $id, 10);

    if (file_exists(SEC . 'servers/' . $server['game'] . '/tarif/' . $url['subsection'] . '.php')) {
        include(SEC . 'servers/' . $server['game'] . '/tarif/' . $url['subsection'] . '.php');
    } else {
        include(SEC . 'servers/games/tarif/' . $url['subsection'] . '.php');
    }
}

$html->nav($server['address'], $cfg['http'] . 'servers/id/' . $id);
$html->nav('Тариф');

// Общий шаблон раздела
$html->get('tarif', 'sections/servers/games');

$html->set('id', $id);

$html->pack('main');

// Шаблон продления
if ($cfg['settlement_period']) {
    tarif::extend_sp($server, $tarif, $id);
} else {
    $options = games::parse_time($tarif['discount'], $server['tarif'], explode(':', $tarif['timext']), 'extend');

    tarif::extend($options, $server, $tarif['name'], $id);
}

// Если не тестовый период
if (!$server['test']) {
    $sql->query('SELECT `name` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
    $unit = $sql->get();

    // Шаблон смены тарифа (если аренда не менее 1 дня и цены планов различны)
    if ($server['time'] > $start_point + 86400 and tarif::price($tarif['price'])) {
        tarif::plan($server, $tarif['name'], $id);
    }

    // Шаблон изменения кол-ва слот
    if ($tarif['slots_min'] != $tarif['slots_max']) {
        tarif::slots($server, ['min' => $tarif['slots_min'], 'max' => $tarif['slots_max']], $id);
    }

    // Шаблон изменения локации (если аренда не менее 1 дня)
    if ($server['time'] > $start_point + 86400) {
        tarif::unit($server, $unit['name'], $tarif['name'], $id);
    }

    // Шаблон покупки/аренды выделенного адреса
    if ($server['port'] != 27015) {
        tarif::address($server, $id);
    }
}

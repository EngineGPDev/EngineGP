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

use EngineGP\AdminSystem;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

$sql->query('SELECT * FROM `tarifs` WHERE `id`="' . $id . '" LIMIT 1');
$tarif = $sql->get();

$games = '<option value="cs">Counter-Strike: 1.6</option><option value="cssold">Counter-Strike: Source v34</option><option value="css">Counter-Strike: Source</option>'
    . '<option value="csgo">Counter-Strike: Global Offensive</option><option value="cs2">Counter-Strike: 2</option><option value="rust">RUST</option><option value="samp">San Andreas Multiplayer</option><option value="crmp">GTA: Criminal Russia</option>'
    . '<option value="mta">Multi Theft Auto</option><option value="mc">Minecraft</option>';

$test = $tarif['test'] ? '<option value="1">Доступно</option><option value="0">Недоступно</option>' : '<option value="0">Недоступно</option><option value="1">Доступно</option>';
$discount = $tarif['discount'] ? '<option value="1">Включены</option><option value="0">Без скидок</option>' : '<option value="0">Без скидок</option><option value="1">Включены</option>';
$autostop = $tarif['autostop'] ? '<option value="1">Включено</option><option value="0">Выключено</option>' : '<option value="0">Выключено</option><option value="1">Включено</option>';
$show = $tarif['show'] ? '<option value="1">Доступна</option><option value="0">Недоступна</option>' : '<option value="0">Недоступна</option><option value="1">Доступна</option>';

$units = '<option value="0">Выберите локацию</option>';

$sql->query('SELECT `id`, `name` FROM `units` ORDER BY `id` ASC');
while ($unit = $sql->get()) {
    $units .= '<option value="' . $unit['id'] . '">#' . $unit['id'] . ' ' . $unit['name'] . '</option>';
}

$games = str_replace('"' . $tarif['game'] . '"', '"' . $tarif['game'] . '" selected="select"', $games);

$html->get('copy', 'sections/tarifs');

if ($tarif['game'] == 'cssold') {
    $sprice = '';

    $aPrice = AdminSystem::b64djs($tarif['price']);

    foreach ($aPrice as $price) {
        $sprice .= $price . ':';
    }

    $sprice = isset($sprice[0]) ? substr($sprice, 0, -1) : '';

    $tarif['price'] = $sprice;
}

foreach ($tarif as $field => $val) {
    $html->set($field, $val);
}

$html->set('units', $units);
$html->set('games', $games);
$html->set('test', $test);
$html->set('discount', $discount);
$html->set('autostop', $autostop);
$html->set('show', $show);

foreach (['ftp', 'plugins', 'console', 'stats', 'copy', 'web'] as $section) {
    if ($tarif[$section]) {
        $html->unit($section, 1);
    } else {
        $html->unit($section);
    }
}

$packs = '';

$aPacks = AdminSystem::b64djs($tarif['packs']);

foreach ($aPacks as $name => $fullname) {
    $packs .= '"' . $name . '":"' . $fullname . '",';
}

$packs = isset($packs[0]) ? substr($packs, 0, -1) : '';

$html->set('packs', $packs);

$plugins = '';

$aPlugins = AdminSystem::b64djs($tarif['plugins_install']);

if (is_array($aPlugins)) {
    foreach ($aPlugins as $pack => $list) {
        $plugins .= '"' . $pack . '":"' . $list . '",';
    }
}

$plugins = isset($plugins[0]) ? substr($plugins, 0, -1) : '';

$html->set('plugins_install', $plugins);

$html->pack('main');

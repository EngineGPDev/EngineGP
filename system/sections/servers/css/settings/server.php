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

$html->nav('Параметры server.cfg');

$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
$unit = $sql->get();

$sql->query('SELECT `install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
$tarif = $sql->get();

include(LIB . 'ssh.php');

if (!$ssh->auth($unit['passwd'], $unit['address'])) {
    if ($go) {
        sys::outjs(['e' => sys::text('error', 'ssh')], $nmch);
    }

    sys::back($cfg['http'] . 'servers/id/' . $id . '/section/settings');
}

include(DATA . 'scfg/' . $server['game'] . '.php');

// Сохранение изменений
if ($go) {
    $servercfg = $_POST['config'] ?? '';

    $config = '';

    $config_end = $servercfg['\'other\''];

    unset($servercfg['\'other\'']);

    foreach ($servercfg as $cvar => $val) {
        if ($val != '') {
            $config .= str_replace("'", '', $cvar) . ' "' . $val . '"' . "\n";
        }
    }

    // Временый файл
    $temp = sys::temp($config . $config_end);

    $ssh->setfile($temp, $tarif['install'] . $server['uid'] . '/cstrike/cfg/server.cfg');
    $ssh->set('chmod 0644' . ' ' . $tarif['install'] . $server['uid'] . '/cstrike/cfg/server.cfg');

    $ssh->set('chown server' . $server['uid'] . ':servers ' . $tarif['install'] . $server['uid'] . '/cstrike/cfg/server.cfg');

    unlink($temp);

    sys::outjs(['s' => 'ok'], $nmch);
}

// Чтение файла - server.cfg
$file = $tarif['install'] . $server['uid'] . '/cstrike/cfg/server.cfg';

$ssh->set('echo "" >> ' . $file . ' && cat ' . $file . ' | grep -ve "^#\|^[[:space:]]*$"');

$fScfg = explode("\n", strip_tags($ssh->get()));

$servercfg = [];
$other = '';

// Убираем пробелы и генерируем массив
foreach ($fScfg as $line) {
    // имя квара
    $cvar = sys::first(explode(' ', $line));

    if ($cvar == '') {
        continue;
    }

    // убираем имя квара и оставляем только значение
    $value = str_replace($cvar . ' ', "", $line);

    // выбираем только то, что нам нужно
    preg_match_all('~([^"]+)~', $value, $cvar_value, PREG_SET_ORDER);

    // Исключаем комментарии
    if ($cvar == '//') {
        continue;
    }

    $val = sys::first(explode(' //', $cvar_value[0][1]));

    // Добавляем данные в массив
    if (array_key_exists($cvar, $aScfg)) {
        $servercfg[$cvar] = trim($val);
    } else {
        $other .= $line . "\n";
    }
}

foreach ($aScfg as $name => $desc) {
    if (!isset($servercfg[$name])) {
        $servercfg[$name] = '';
    }

    // Формирование формы
    if (strpos($aScfg_form[$name], 'select')) {
        $form = str_replace('value="' . $servercfg[$name] . '"', 'value="' . $servercfg[$name] . '" selected="select"', $aScfg_form[$name]);
    } else {
        $form = str_replace('[' . $name . ']', $servercfg[$name], $aScfg_form[$name]);
    }

    $html->get('servercfg_list', 'sections/servers/games/settings');

    $html->set('name', $name);
    $html->set('desc', $desc);
    $html->set('form', $form);

    $html->pack('list');
}

$html->get('servercfg', 'sections/servers/' . $server['game'] . '/settings');

$html->set('id', $id);
$html->set('cfg', $html->arr['list']);
$html->set('other', $other);

$html->pack('main');

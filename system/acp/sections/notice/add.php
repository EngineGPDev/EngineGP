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

if ($go) {
    $aData = [];

    $aData['text'] = isset($_POST['text']) ? trim($_POST['text']) : '';
    $aData['color'] = isset($_POST['color']) ? trim($_POST['color']) : '';
    $aData['type'] = isset($_POST['type']) ? trim($_POST['type']) : '';
    $aData['unit'] = isset($_POST['unit']) ? sys::int($_POST['unit']) : '';
    $aData['server'] = isset($_POST['server']) ? sys::int($_POST['server']) : '';
    $aData['time'] = isset($_POST['time']) ? trim($_POST['time']) : '';

    $aData['time'] = sys::checkdate($aData['time']);

    if ($aData['type'] == 'unit') {
        $sql->query('SELECT `id` FROM `units` WHERE `id`="' . $aData['unit'] . '" LIMIT 1');
        if (!$sql->num()) {
            sys::outjs(['e' => 'Указанная локация не найдена']);
        }

        $aData['server'] = 0;
    } elseif ($aData['type'] == 'server') {
        $sql->query('SELECT `id` FROM `servers` WHERE `id`="' . $aData['server'] . '" LIMIT 1');
        if (!$sql->num()) {
            sys::outjs(['e' => 'Указанный сервер не найден']);
        }

        $aData['unit'] = 0;
    } else {
        sys::outjs(['e' => 'Выберите получателя уведомления']);
    }

    $sql->query('INSERT INTO `notice` set '
        . '`unit`="' . $aData['unit'] . '",'
        . '`server`="' . $aData['server'] . '",'
        . '`text`="' . htmlspecialchars($aData['text']) . '",'
        . '`color`="' . $aData['color'] . '",'
        . '`time`="' . $aData['time'] . '"');

    sys::outjs(['s' => 'ok']);
}

$units = '';

$sql->query('SELECT `id`, `name` FROM `units` ORDER BY `id` ASC');
while ($unit = $sql->get()) {
    $units .= '<option value="' . $unit['id'] . '">' . $unit['name'] . '</option>';
}

$html->get('add', 'sections/notice');

$html->set('units', $units);

$html->pack('main');

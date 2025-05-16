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

if ($go) {
    $aData = [];

    $aData['name'] = isset($_POST['name']) ? trim($_POST['name']) : '';
    $aData['text'] = isset($_POST['text']) ? trim($_POST['text']) : '';

    if (in_array('', $aData)) {
        AdminSystem::outjs(['e' => 'Необходимо заполнить все поля']);
    }

    $name = md5(time() . rand(5, 100) . rand(10, 20) . rand(1, 20) . rand(40, 80));

    $file = fopen(FILES . 'pages/' . $name, "w");

    fputs($file, $aData['text']);

    fclose($file);

    $sql->query('INSERT INTO `pages` set `name`="' . htmlspecialchars($aData['name']) . '", `file`="' . $name . '"');

    AdminSystem::outjs(['s' => 'ok']);
}

$html->get('add', 'sections/pages');

$html->pack('main');

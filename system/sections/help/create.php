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
use EngineGP\View\Help;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

if ($go) {
    $nmch = 'create_help_' . $user['id'];

    // Проверка сессии
    if ($mcache->get($nmch)) {
        System::outjs(['e' => $text['mcache']], $nmch);
    }

    // Создание сессии
    $mcache->set($nmch, 1, false, 10);

    $aData = [];

    $aData['service'] = isset($_POST['service']) ? explode('_', $_POST['service']) : exit();
    $aData['title'] = isset($_POST['title']) ? strip_tags(trim($_POST['title'])) : '';
    $aData['text'] = $_POST['text'] ?? exit();
    $aData['images'] = $_POST['img'] ?? [];

    $aData['img'] = [];

    /*
        Проверка входных данных
    */

    // Проверка услуги
    if (count($aData['service']) != 2) {
        if ($aData['service'][0] != 'none') {
            System::outjs(['e' => 'Необходимо выбрать услугу связанную с вопросом.'], $nmch);
        }

        $aData['type'] = 'none';
        $aData['service'] = 0;
    } else {
        if (!in_array($aData['service'][0], ['server', 'hosting'])) {
            System::outjs(['e' => 'Необходимо выбрать услугу связанную с вопросом.'], $nmch);
        }

        $aData['type'] = $aData['service'][0];
        $aData['service'] = System::int($aData['service'][1]);

        switch ($aData['type']) {
            case 'server':
                $sql->query('SELECT `id` FROM `servers` WHERE `id`="' . $aData['service'] . '" AND `user`="' . $user['id'] . '" LIMIT 1');
                break;

            case 'hosting':
                $sql->query('SELECT `id` FROM `hosting` WHERE `id`="' . $aData['service'] . '" AND `user`="' . $user['id'] . '" LIMIT 1');
        }

        if (!$sql->num()) {
            System::outjs(['e' => 'Выбранная услуга не найдена в базе.'], $nmch);
        }

        // Защита от дублирования темы вопроса
        $sql->query('SELECT `id` FROM `help` WHERE `user`="' . $user['id'] . '" AND `type`="' . $aData['type'] . '" AND `service`="' . $aData['service'] . '" AND `close`="0" LIMIT 1');
        if ($sql->num()) {
            System::outjs(['e' => 'По выбранной услуге уже есть открытый диалог.'], $nmch);
        }
    }

    // Проверка заголовка, если указан
    if (!empty($aData['title'])) {
        if (iconv_strlen($aData['title'], 'UTF-8') < 3 || iconv_strlen($aData['title'], 'UTF-8') > 40) {
            System::outjs(['e' => 'Длина загловка не должна быть менее 3 и не превышать 40 символов.'], $nmch);
        }
    }

    // Проверка сообщения
    if (iconv_strlen($aData['text'], 'UTF-8') < 10 || iconv_strlen($aData['text'], 'UTF-8') > 1000) {
        System::outjs(['e' => 'Длина сообщения не должна быть менее 10 и не превышать 1000 символов.'], $nmch);
    }

    // Обработка сообщения
    $aData['text'] = Help::text($aData['text']);

    // Проверка изображений
    if (is_array($aData['images']) and count($aData['images'])) {
        foreach ($aData['images'] as $img) {
            $key = explode('.', $img);

            if (!is_array($key) || System::valid($key[0], 'md5') || !in_array($key[1], ['png', 'gif', 'jpg', 'jpeg', 'bmp'])) {
                continue;
            }

            $sql->query('SELECT `id` FROM `help_upload` WHERE `name`="' . $img . '" LIMIT 1');
            if (!$sql->num()) {
                continue;
            }

            $image = $sql->get();

            $sql->query('UPDATE `help_upload` set `status`="1" WHERE `id`="' . $image['id'] . '" LIMIT 1');

            $aData['img'][] = $img;
        }
    }

    // Проверка открытых сообщений
    $sql->query('SELECT `id` FROM `help` WHERE `user`="' . $user['id'] . '" AND `close`="0" LIMIT 3');
    if ($sql->num() == 3) {
        System::outjs(['e' => 'У вас уже открыто 3 вопроса, чтобы создать новый необходимо их закрыть.'], $nmch);
    }

    $sql->query('INSERT INTO `help` set '
        . '`user`="' . $user['id'] . '",'
        . '`type`="' . $aData['type'] . '",'
        . '`service`="' . $aData['service'] . '",'
        . '`status`="1",'
        . '`date`="' . $start_point . '",'
        . '`time`="' . $start_point . '",'
        . '`title`="' . htmlspecialchars($aData['title']) . '",'
        . '`close`="0"');

    $help = $sql->id();

    $sql->query('INSERT INTO `help_dialogs` set '
        . '`help`="' . $help . '",'
        . '`user`="' . $user['id'] . '",'
        . '`text`="' . $aData['text'] . '",'
        . '`img`="' . System::b64js($aData['img']) . '",'
        . '`time`="' . $start_point . '"');

    System::outjs(['s' => $help], $nmch);
}

$services = '';

$sql->query('SELECT `id`, `address` FROM `servers` WHERE `user`="' . $user['id'] . '" LIMIT 10');
while ($server = $sql->get()) {
    $services .= '<option value="server_' . $server['id'] . '">Игровой сервер #' . $server['id'] . ' (' . $server['address'] . ')</option>';
}

$html->get('create', 'sections/help');

$html->set('id', $user['id']);
$html->set('services', $services);

$html->pack('main');

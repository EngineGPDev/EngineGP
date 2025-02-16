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

$nmch = 'reply_help_' . $user['id'];

// Проверка сессии
if ($mcache->get($nmch)) {
    System::outjs(['e' => System::text('other', 'mcache')], $nmch);
}

// Создание сессии
$mcache->set($nmch, 1, false, 10);

if ($id) {
    if (in_array($user['group'], ['admin', 'support'])) {
        $sql->query('SELECT `user` FROM `help` WHERE `id`="' . $id . '" AND `close`="0" LIMIT 1');
    } else {
        $sql->query('SELECT `user` FROM `help` WHERE `id`="' . $id . '" AND `close`="0" AND `user`="' . $user['id'] . '" LIMIT 1');
    }

    if (!$sql->num()) {
        System::outjs(['с' => 'Вопрос не открыт чтобы вести диалог.'], $nmch);
    }

    $help = $sql->get();
} else {
    System::outjs(['e' => 'Вопрос не найден в базе.'], $nmch);
}

$aData = [];

$aData['text'] = $_POST['text'] ?? System::outjs(['e' => 'Сообщение не найдено.'], $nmch);
$aData['images'] = $_POST['img'] ?? [];

$aData['img'] = [];

// Проверка сообщения
if (iconv_strlen($aData['text'], 'UTF-8') < 2 || iconv_strlen(str_replace([' ', "\t", "\n"], '', $aData['text']), 'UTF-8') > 1000) {
    System::outjs(['e' => 'Длина сообщения не должна быть менее 2 и не превышать 1000 символов.'], $nmch);
}

include(LIB . 'help.php');

// Обработка сообщения
$aData['text'] = help::text($aData['text']);

// Проверка изображений
if (is_array($aData['images']) and count($aData['images'])) {
    foreach ($aData['images'] as $img) {
        $key = explode('.', $img);

        if (!is_array($key) || System::valid($key[0], 'md5') || !in_array($key[1], ['png', 'gif', 'jpg', 'bmp'])) {
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

// Система контроля спама
if ($user['group'] == 'user') {
    $i = 0;
    $n = 3;
    $sql->query('SELECT `user` FROM `help_dialogs` WHERE `help`="' . $id . '" ORDER BY `id` DESC LIMIT 3');
    while ($msg = $sql->get()) {
        if (!$i and !$msg['user']) {
            System::outjs(['i' => 'Пожалуйста, дождитесь ответа технической поддержки.'], $nmch);
        }

        $i += 1;

        if ($msg['user'] == $help['user']) {
            $n -= 1;
        }
    }

    if (!$n) {
        $sql->query('INSERT INTO `help_dialogs` set `help`="' . $id . '", `user`="0", `text`="Пожалуйста, дождитесь ответа технической поддержки.", `img`="", `time`="' . $start_point . '"');

        System::outjs(['i' => 'Пожалуйста, дождитесь ответа технической поддержки.'], $nmch);
    }
}

$sql->query('SELECT `text` FROM `help_dialogs` WHERE `help`="' . $id . '" ORDER BY `id` DESC LIMIT 1');
$msg = $sql->get();

if (md5($msg['text']) == md5($aData['text'])) {
    System::outjs(['e' => 'Такое сообщение уже отправлено.'], $nmch);
}

$sql->query('INSERT INTO `help_dialogs` set '
    . '`help`="' . $id . '",'
    . '`user`="' . $user['id'] . '",'
    . '`text`="' . $aData['text'] . '",'
    . '`img`="' . System::b64js($aData['img']) . '",'
    . '`time`="' . $start_point . '"');

if ($user['group'] != 'user') {
    $sql->query('UPDATE `help` set `status`="0" WHERE `id`="' . $id . '" LIMIT 1');
} else {
    $sql->query('UPDATE `help` set `status`="1" WHERE `id`="' . $id . '" LIMIT 1');
    $sql->query('UPDATE `help` set `notice_admin`="2" WHERE `id`="' . $id . '" LIMIT 1');
}

System::outjs(['s' => 'ok'], $nmch);

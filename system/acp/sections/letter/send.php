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

set_time_limit(1200);

$aData = [];

$aData['title'] = isset($_POST['title']) ? trim($_POST['title']) : sys::outjs(['e' => 'Необходимо указать заголовок']);
$aData['text'] = isset($_POST['text']) ? trim($_POST['text']) : sys::outjs(['e' => 'Необходимо указать сообщение']);
$aData['users'] = $_POST['users'] ?? sys::outjs(['e' => 'Необходимо указать получателей']);

if ($aData['title'] == '' || $aData['text'] == '') {
    sys::outjs(['e' => 'Необходимо заполнить все поля']);
}

if (!is_array($aData['users']) || !count($aData['users'])) {
    sys::outjs(['e' => 'Необходимо указать минимум одного получателя']);
}

$noletter = '';

include(LIB . 'smtp.php');

foreach ($aData['users'] as $id => $cheked) {
    if ($cheked != 'on') {
        continue;
    }

    $sql->query('SELECT `mail` FROM `users` WHERE `id`="' . sys::int($id) . '" LIMIT 1');
    $us = $sql->get();

    $tpl = file_get_contents(DATA . 'mail.ini');

    $text = str_replace(
        ['[name]', '[text]', '[http]', '[img]', '[css]'],
        [$cfg['name'], $aData['text'], $cfg['http'], $cfg['http'] . 'template/images/', $cfg['http'] . 'template/css/'],
        $tpl
    );

    $smtp = new smtp();

    if (!$smtp->send($us['mail'], strip_tags($aData['title']), $text)) {
        $noletter .= '<p>' . $us['mail'] . '</p>';
    }
}

if ($noletter == '') {
    $noletter = 'отправлено всем.';
}

sys::outjs(['s' => $noletter]);

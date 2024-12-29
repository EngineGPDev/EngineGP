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

$sql->query('SELECT `notice_news`, `notice_help` FROM `users` WHERE `id`="' . $user['id'] . '" LIMIT 1');
$user = array_merge($user, $sql->get());

if (isset($url['action']) and in_array($url['action'], ['upload', 'news', 'help', 'important'])) {
    switch ($url['action']) {
        case 'upload':
            $file = $_POST['value'] ?? exit;
            $name = $_POST['name'] ?? exit;

            $pname = explode('.', $name);
            $type = strtolower(end($pname));

            if (!in_array($type, ['png', 'gif', 'jpg', 'bmp'])) {
                exit('Допустимый формат изображений: png, gif, jpg, bmp.');
            }

            $aData = explode(',', $file);

            if (file_put_contents(ROOT . 'upload/avatars/' . $user['id'] . '.' . $type, base64_decode(str_replace(' ', '+', $aData[1])))) {
                exit($user['id'] . ':ok');
            }

            exit('Ошибка загрузки: убедитесь, что изображение не повреждено и имеет правильный формат.');

        case 'news':
            $notice = $user['notice_news'] ? 0 : 1;

            $sql->query('UPDATE `users` set `notice_news`="' . $notice . '" WHERE `id`="' . $user['id'] . '" LIMIT 1');

            sys::outjs(['s' => 'ok']);

            // no break
        case 'help':
            $notice = $user['notice_help'] ? 0 : 1;

            $sql->query('UPDATE `users` set `notice_help`="' . $notice . '" WHERE `id`="' . $user['id'] . '" LIMIT 1');

            sys::outjs(['s' => 'ok']);
    }
}

$html->get('settings', 'sections/user/lk');

$html->set('id', $user['id']);
$html->set('ava', users::ava($user['id']));

if ($user['notice_news']) {
    $html->unit('notice_news', true);
} else {
    $html->unit('notice_news');
}

if ($user['notice_help']) {
    $html->unit('notice_help', true);
} else {
    $html->unit('notice_help');
}

$html->pack('main');

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

    $aData['name'] = isset($_POST['name']) ? trim($_POST['name']) : '';
    $aData['text'] = isset($_POST['text']) ? sys::bbc(trim($_POST['text'])) : '';
    $aData['cat'] = isset($_POST['cat']) ? sys::int($_POST['cat']) : '';
    $aData['tags'] = isset($_POST['tags']) ? trim($_POST['tags']) : '';

    if (in_array('', $aData)) {
        sys::outjs(['e' => 'Необходимо заполнить все поля']);
    }

    if (sys::strlen($aData['tags']) > 100) {
        sys::outjs(['e' => 'Теги не должен превышать 100 символов.']);
    }

    $sql->query('SELECT `id` FROM `wiki_category` WHERE `id`="' . $aData['cat'] . '" LIMIT 1');
    if (!$sql->num()) {
        sys::outjs(['e' => 'Указанная категория не найдена']);
    }

    $sql->query('INSERT INTO `wiki` set '
        . '`name`="' . htmlspecialchars($aData['name']) . '",'
        . '`cat`="' . $aData['cat'] . '",'
        . '`tags`="' . htmlspecialchars($aData['tags']) . '",'
        . '`date`="' . $start_point . '"');

    $id = $sql->id();

    $sql->query('INSERT INTO `wiki_answer` set '
        . '`wiki`="' . $id . '",'
        . '`cat`="' . $aData['cat'] . '",'
        . '`text`="' . htmlspecialchars($aData['text']) . '"');

    sys::outjs(['s' => 'ok']);
}

$cats = '';

$sql->query('SELECT `id`, `name` FROM `wiki_category` ORDER BY `id` ASC');
while ($cat = $sql->get()) {
    $cats .= '<option value="' . $cat['id'] . '">' . $cat['name'] . '</option>';
}

$html->get('add', 'sections/wiki');

$html->set('cats', $cats);

$html->pack('main');

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

$sql->query('SELECT `name`, `text`, `full_text`, `tags` FROM `news` WHERE `id`="' . $id . '" LIMIT 1');
$news = $sql->get();

if ($go) {
    $aData = [];

    $aData['name'] = isset($_POST['name']) ? trim($_POST['name']) : htmlspecialchars_decode($news['name']);
    $aData['text'] = isset($_POST['text']) ? AdminSystem::bbc(trim($_POST['text'])) : htmlspecialchars_decode($news['text']);
    $aData['full'] = isset($_POST['full']) ? AdminSystem::bbc(trim($_POST['full'])) : htmlspecialchars_decode($news['full_text']);
    $aData['tags'] = isset($_POST['tags']) ? trim($_POST['tags']) : htmlspecialchars_decode($news['tags']);

    if (in_array('', $aData)) {
        AdminSystem::outjs(['e' => 'Необходимо заполнить все поля']);
    }

    if (AdminSystem::strlen($aData['name']) > 50) {
        AdminSystem::outjs(['e' => 'Заголовок не должен превышать 50 символов.']);
    }

    if (AdminSystem::strlen($aData['tags']) > 100) {
        AdminSystem::outjs(['e' => 'Теги не должен превышать 100 символов.']);
    }

    $sql->query('UPDATE `news` set '
        . '`name`="' . htmlspecialchars($aData['name']) . '",'
        . '`text`="' . htmlspecialchars($aData['text']) . '",'
        . '`full_text`="' . htmlspecialchars($aData['full']) . '",'
        . '`tags`="' . htmlspecialchars($aData['tags']) . '" WHERE `id`="' . $id . '" LIMIT 1');

    AdminSystem::outjs(['s' => 'ok']);
}

$html->get('news', 'sections/news');

$html->set('id', $id);
$html->set('name', htmlspecialchars_decode($news['name']));
$html->set('text', htmlspecialchars_decode($news['text']));
$html->set('full', htmlspecialchars_decode($news['full_text']));
$html->set('tags', htmlspecialchars_decode($news['tags']));

$html->pack('main');

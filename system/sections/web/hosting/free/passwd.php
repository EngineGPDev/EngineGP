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

if (!$go) {
    exit;
}

// Проверка на наличие уже установленной выбранной услуги
switch ($aWebInstall[$server['game']][$url['subsection']]) {
    case 'server':
        $sql->query('SELECT `id`, `login`, `user` FROM `web` WHERE `type`="' . $url['subsection'] . '" AND `server`="' . $id . '" LIMIT 1');
        break;

    case 'user':
        $sql->query('SELECT `id`, `login`, `user` FROM `web` WHERE `type`="' . $url['subsection'] . '" AND `user`="' . $server['user'] . '" LIMIT 1');
        break;

    case 'unit':
        $sql->query('SELECT `id`, `login`, `user` FROM `web` WHERE `type`="' . $url['subsection'] . '" AND `user`="' . $server['user'] . '" AND `unit`="' . $server['unit'] . '" LIMIT 1');
        break;
}

if (!$sql->num()) {
    System::outjs(['i' => 'Дополнительная услуга не установлена.'], $nmch);
}

$web = $sql->get();

$sql->query('SELECT `mail` FROM `users` WHERE `id`="' . $web['user'] . '" LIMIT 1');
if (!$sql->num()) {
    System::outjs(['e' => 'Необходимо указать пользователя доп. услуги.'], $nmch);
}

$u = $sql->get();

$passwd = System::passwd($aWebParam[$url['subsection']]['passwd']);

// Смена пароля вирт. хостинга
$result = json_decode(file_get_contents(System::updtext(
    $aWebUnit['isp']['account']['passwd'],
    ['login' => $web['login'],
        'mail' => $u['mail'],
        'hdd' => $aWebUnit['isp']['hdd'],
        'passwd' => $passwd]
)), true);

if (!isset($result['result']) || strtolower($result['result']) != 'ok') {
    System::outjs(['e' => 'Не удалось изменить пароль виртуального хостинга, обратитесь в тех.поддержку.'], $nmch);
}

// Обновление данных
$sql->query('UPDATE `web` set `passwd`="' . $passwd . '" WHERE `id`="' . $web['id'] . '" LIMIT 1');

System::outjs(['s' => 'ok'], $nmch);

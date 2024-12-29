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

if (!$go) {
    exit;
}

if ($user['group'] != 'admin') {
    sys::outjs(['i' => 'Чтобы удалить услугу, создайте вопрос выбрав свой сервер с причиной удаления.'], $nmch);
}

// Проверка на наличие установленной услуги
switch ($aWebInstall[$server['game']][$url['subsection']]) {
    case 'server':
        $sql->query('SELECT `id`, `login` FROM `web` WHERE `type`="' . $url['subsection'] . '" AND `server`="' . $id . '" LIMIT 1');
        break;

    case 'user':
        $sql->query('SELECT `id`, `login` FROM `web` WHERE `type`="' . $url['subsection'] . '" AND `user`="' . $server['user'] . '" LIMIT 1');
        break;

    case 'unit':
        $sql->query('SELECT `id`, `login` FROM `web` WHERE `type`="' . $url['subsection'] . '" AND `user`="' . $server['user'] . '" AND `unit`="' . $server['unit'] . '" LIMIT 1');
        break;
}

if (!$sql->num()) {
    sys::outjs(['i' => 'Дополнительная услуга не установлена.'], $nmch);
}

$web = $sql->get();

// Удаление вирт. хостинга
$result = json_decode(file_get_contents(sys::updtext($aWebUnit['isp']['account']['delete'], ['login' => $web['login']])), true);

if (!isset($result['result']) || strtolower($result['result']) != 'ok') {
    sys::outjs(['e' => 'Не удалось удалить виртуальный хостинг.'], $nmch);
}

// Обновление данных
$sql->query('DELETE FROM `web` WHERE `id`="' . $web['id'] . '" LIMIT 1');

sys::outjs(['s' => 'ok'], $nmch);

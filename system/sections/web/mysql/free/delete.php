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

if ($user['group'] != 'admin') {
    System::outjs(['i' => 'Чтобы удалить услугу, создайте вопрос выбрав свой сервер с причиной удаления.'], $nmch);
}

switch ($aWebInstall[$server['game']][$url['subsection']]) {
    case 'server':
        $sql->query('SELECT `id`, `uid`, `unit`, `login` FROM `web` WHERE `type`="' . $url['subsection'] . '" AND `server`="' . $id . '" LIMIT 1');

        break;

    case 'user':
        $sql->query('SELECT `id`, `uid`, `unit`, `login` FROM `web` WHERE `type`="' . $url['subsection'] . '" AND `user`="' . $server['user'] . '" LIMIT 1');

        break;

    case 'unit':
        $sql->query('SELECT `id`, `uid`, `unit`, `login` FROM `web` WHERE `type`="' . $url['subsection'] . '" AND `user`="' . $server['user'] . '" AND `unit`="' . $server['unit'] . '" LIMIT 1');

        break;
}

if (!$sql->num()) {
    System::outjs(['e' => 'Дополнительная услуга не установлена.'], $nmch);
}

$web = $sql->get();

if ($aWebUnit['unit'][$url['subsection']] == 'local') {
    $sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $web['unit'] . '" LIMIT 1');
    $unit = $sql->get();
} else {
    $unit = [
        'address' => $aWebUnit['address'],
        'passwd' => $aWebUnit['passwd'],
    ];
}

include(LIB . 'ssh.php');

if (!$ssh->auth($unit['passwd'], $unit['address'])) {
    System::outjs(['e' => System::text('error', 'ssh')], $nmch);
}

$ssh->set("mysql --login-path=local -e \"DROP DATABASE IF EXISTS " . $web['login'] . "; DROP USER " . $web['login'] . "\"");

$sql->query('DELETE FROM `web` WHERE `id`="' . $web['id'] . '" LIMIT 1');

System::outjs(['s' => 'ok'], $nmch);

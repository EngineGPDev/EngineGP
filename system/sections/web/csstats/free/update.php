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

switch ($aWebInstall[$server['game']][$url['subsection']]) {
    case 'server':
        $sql->query('SELECT `id`, `uid`, `desing`, `domain`, `update` FROM `web` WHERE `server`="' . $id . '" LIMIT 1');

        break;

    case 'user':
        $sql->query('SELECT `id`, `uid`, `desing`, `domain`, `update` FROM `web` WHERE `user`="' . $server['user'] . '" LIMIT 1');

        break;

    case 'unit':
        $sql->query('SELECT `id`, `uid`, `desing`, `domain`, `update` FROM `web` WHERE `user`="' . $server['user'] . '" AND `unit`="' . $server['unit'] . '" LIMIT 1');

        break;
}

if (!$sql->num()) {
    sys::outjs(['e' => 'Дополнительная услуга не установлена.'], $nmch);
}

$web = $sql->get();

// Проверка времени последнего обновления
include(LIB . 'games/games.php');

$upd = $web['update'] + 86400;

if ($upd > $start_point) {
    sys::outjs(['e' => 'Для повторного обновления должно пройти: ' . games::date('max', $upd)]);
}

include(LIB . 'ssh.php');

if ($aWebUnit['unit'][$url['subsection']] == 'local') {
    $sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
    $unit = $sql->get();
} else {
    $unit = [
        'address' => $aWebUnit['address'],
        'passwd' => $aWebUnit['passwd'],
    ];
}

if (!$ssh->auth($unit['passwd'], $unit['address'])) {
    sys::outjs(['e' => sys::text('error', 'ssh')], $nmch);
}

$install = $aWebUnit['install'][$aWebUnit['unit'][$url['subsection']]][$url['subsection']] . $web['domain'];

$path = $aWebUnit['path'][$aWebUnit['unit'][$url['subsection']]][$url['subsection']] . $web['desing'];

$ssh->set('cat ' . $install . '/include/db.config.inc.php > ' . $path . '/include/db.config.inc.php;'
    . 'cd ' . $install . ' && sudo -u web' . $web['uid'] . ' tmux new-session -ds u_w_' . $web['uid'] . ' sh -c "YES | cp -rf ' . $path . '/. .; ' . $aWebChmod[$url['subsection']] . '"');

$sql->query('UPDATE `web` set `update`="' . $start_point . '" WHERE `id`="' . $web['id'] . '" LIMIT 1');

sys::outjs(['s' => 'ok'], $nmch);

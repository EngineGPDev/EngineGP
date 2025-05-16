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

$cid = isset($url['cid']) ? System::int($url['cid']) : System::outjs(['e' => 'Выбранная копия не найдена.'], $nmch);

$sql->query('SELECT `name`, `status` FROM `copy` WHERE `id`="' . $cid . '" AND `user`="' . $server['user'] . '_' . $server['unit'] . '" AND `game`="' . $server['game'] . '" LIMIT 1');
if (!$sql->num()) {
    System::outjs(['e' => 'Выбранная копия не найдена.'], $nmch);
}

$copy = $sql->get();

if (!$copy['status']) {
    System::outjs(['e' => 'Дождитесь создания резервной копии.'], $nmch);
}

$ssh->set('tmux new-session -ds rem_copy_' . $cid . ' rm /copy/' . $copy['name'] . '.tar');

$sql->query('DELETE FROM `copy` WHERE `id`="' . $cid . '" LIMIT 1');

// Очистка кеша
$mcache->delete('server_copy_' . $id);

System::outjs(['s' => 'ok'], $nmch);

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

if (isset($url['delete']) and $url['delete'] == 'all') {
    $sql->query('DELETE FROM `auth` WHERE `user`="' . $id . '"');
    $sql->query('DELETE FROM `logs` WHERE `user`="' . $id . '"');

    $helps = $sql->query('SELECT `id` FROM `help` WHERE `user`="' . $id . '"');
    while ($help = $sql->get($helps)) {
        $sql->query('DELETE FROM `help_dialogs` WHERE `help`="' . $help['id'] . '"');
        $sql->query('DELETE FROM `help` WHERE `id`="' . $help['id'] . '" LIMIT 1');
    }

    $uploads = $sql->query('SELECT `id`, `name` FROM `help_upload` WHERE `user`="' . $id . '"');
    while ($upload = $sql->get($uploads)) {
        @unlink(ROOT . 'upload/' . $upload['name']);

        $sql->query('DELETE FROM `help_upload` WHERE `id`="' . $upload['id'] . '" LIMIT 1');
    }

    $sql->query('DELETE FROM `logs_sys` WHERE `user`="' . $id . '"');
    $sql->query('DELETE FROM `owners` WHERE `user`="' . $id . '"');
    $sql->query('DELETE FROM `promo_use` WHERE `user`="' . $id . '"');
    $sql->query('DELETE FROM `recovery` WHERE `user`="' . $id . '"');
    $sql->query('DELETE FROM `security` WHERE `user`="' . $id . '"');

    $sql->query('UPDATE `servers` set `user`="0" WHERE `user`="' . $id . '"');
    $sql->query('UPDATE `web` set `user`="0" WHERE `user`="' . $id . '"');
}

$sql->query('DELETE FROM `users` WHERE `id`="' . $id . '" LIMIT 1');

AdminSystem::outjs(['s' => 'ok']);

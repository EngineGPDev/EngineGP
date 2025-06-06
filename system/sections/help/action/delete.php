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

if ($user['group'] != 'admin') {
    System::outjs(['e' => 'У вас нет доступа к данному действию.']);
}

if ($id) {
    $sql->query('DELETE FROM `help` WHERE `id`="' . $id . '" LIMIT 1');

    $dialogs = $sql->query('SELECT `id`, `img` FROM `help_dialogs` WHERE `help`="' . $id . '"');
    while ($dialog = $sql->get($dialogs)) {
        $aImg = System::b64djs($dialog['img']);

        foreach ($aImg as $img) {
            $sql->query('DELETE FROM `help_upload` WHERE `name`="' . $img . '" LIMIT 1');

            $filePath = ROOT . 'upload/' . $img;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $sql->query('DELETE FROM `help_dialogs` WHERE `id`="' . $dialog['id'] . '" LIMIT 1');
    }

    System::outjs(['s' => 'ok']);
}

System::outjs(['e' => 'Вопрос не найден в базе.']);

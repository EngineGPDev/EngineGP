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

$aGroup = [
    'admin' => 'Администратор',
    'support' => 'Техническая поддержка',
    'user' => 'Клиент',
];

$write_st = isset($url['write']) ? true : false;

if ($id) {
    $nmch = 'write_help_' . $id;

    $cache = $mcache->get($nmch);

    // Если кеш создан
    if ($cache) {
        if ($write_st) {
            $cache[$user['id']] = $user['group'] . '|' . $start_point;
        } else {
            unset($cache[$user['id']]);
        }

        $mcache->replace($nmch, $cache, false, 10);
    } else {
        if ($write_st) {
            $mcache->set($nmch, [$user['id'] => $user['group'] . '|' . $start_point], false, 10);
        }
    }

    if ($user['group'] == 'user') {
        System::out('У вас нет доступа к данной информации.');
    }

    // Обработка кеша
    $cache = $mcache->get($nmch);

    $write_now = '';

    if (is_array($cache)) {
        foreach ($cache as $writer => $data) {
            [$group, $time] = explode('|', $data);

            if ($time + 9 > $start_point) {
                $write_now .= '<a href="#' . $writer . '" target="_blank">#' . $writer . ' (' . $aGroup[$group] . ')</a>, ';
            }
        }
    }

    if (isset($write_now[1])) {
        $write_now = substr($write_now, 0, -2);
    }

    System::out($write_now);
}

System::out('Необходимо передать номер вопроса.');

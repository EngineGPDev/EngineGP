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

$sql->query('SELECT `packs` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
$tarif = $sql->get();

$aPacks = System::b64djs($tarif['packs'], true);

$pack = $url['pack'] ?? exit;

if ($pack == $server['pack']) {
    System::outjs(['s' => 'ok']);
}

// Проверка сборки
if (!array_key_exists($pack, $aPacks)) {
    System::outjs(['e' => 'Сборка не найдена.']);
}

$sql->query('UPDATE `servers` set `pack`="' . $pack . '" WHERE `id`="' . $id . '" LIMIT 1');

System::outjs(['s' => 'ok'], 'server_settings_' . $id);

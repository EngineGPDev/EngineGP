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

$sql->query('SELECT * FROM `java_versions` WHERE `id`="' . $id . '" LIMIT 1');
$javaVersions = $sql->get();

if ($go) {
    $aData = [];

    $aData['unit'] = isset($_POST['unit']) ? trim($_POST['unit']) : $javaVersions['unit'];
    $aData['name'] = isset($_POST['name']) ? trim($_POST['name']) : $javaVersions['name'];
    $aData['executable_file'] = isset($_POST['executable_file']) ? trim($_POST['executable_file']) : $javaVersions['executable_file'];
    $aData['status'] = $_POST['status'] ?? $javaVersions['status'];

    if (in_array('', $aData)) {
        AdminSystem::outjs(['e' => 'Необходимо заполнить все поля']);
    }

    $sql->query('UPDATE `java_versions` set '
        . '`unit`="' . $aData['unit'] . '",'
        . '`name`="' . htmlspecialchars($aData['name']) . '",'
        . '`executable_file`="' . $aData['executable_file'] . '",'
        . '`status`="' . $aData['status'] . '" WHERE `id`="' . $id . '" LIMIT 1');

    AdminSystem::outjs(['s' => $id]);
}

foreach ($javaVersions as $i => $val) {
    $html->set($i, $val);
}

$unit = '<option value="0">Выберите локацию</option>';

$sql->query('SELECT `id`, `name` FROM `units` ORDER BY `id` ASC');
while ($units = $sql->get()) {
    $unit .= '<option value="' . $units['id'] . '">#' . $units['id'] . ' ' . $units['name'] . '</option>';
}

$html->get('edit', 'sections/java');

$html->set('unit', str_replace('"' . $javaVersions['unit'] . '"', '"' . $javaVersions['unit'] . '" selected="select"', $unit));
$html->set('status', $javaVersions['status'] == 1 ? '<option value="1">Доступна</option><option value="0">Недоступна</option>' : '<option value="0">Недоступна</option><option value="1">Доступна</option>');

$html->pack('main');

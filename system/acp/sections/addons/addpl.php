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

$aGames = ['cs', 'cssold', 'css', 'csgo', 'cs2', 'samp', 'crmp', 'mta', 'mc'];
$types = ['cfg', 'txt', 'ini', 'js'];

if (isset($url['get'])) {
    if ($url['get'] == 'cat') {
        $game = $url['game'] ?? AdminSystem::out();

        if (!in_array($game, $aGames)) {
            AdminSystem::out();
        }

        $cats = '';

        $sql->query('SELECT `id`, `name` FROM `plugins_category` WHERE `game`="' . $game . '" ORDER BY `sort` ASC');
        while ($cat = $sql->get()) {
            $cats .= '<option value="' . $cat['id'] . '">' . $cat['name'] . '</option>';
        }

        AdminSystem::out($cats);
    }
}

if ($go) {
    $aData = [];

    $aData['name'] = isset($_POST['name']) ? trim($_POST['name']) : '';
    $aData['game'] = isset($_POST['game']) ? trim($_POST['game']) : '';
    $aData['cat'] = isset($_POST['category']) ? (is_numeric($_POST['category']) ? ceil((float)$_POST['category']) : 0) : 0;
    $aData['status'] = isset($_POST['status']) ? (is_numeric($_POST['status']) ? ceil((float)$_POST['status']) : 0) : 0;
    $aData['packs'] = isset($_POST['packs']) ? trim($_POST['packs']) : '';
    $aData['desc'] = isset($_POST['desc']) ? trim($_POST['desc']) : '';
    $aData['info'] = isset($_POST['info']) ? trim($_POST['info']) : '';
    $aData['images'] = isset($_POST['images']) ? trim($_POST['images']) : '';
    $aData['incompatible'] = isset($_POST['incompatible']) ? trim($_POST['incompatible']) : '';
    $aData['choice'] = isset($_POST['choice']) ? trim($_POST['choice']) : '';
    $aData['required'] = isset($_POST['required']) ? trim($_POST['required']) : '';
    $aData['update'] = isset($_POST['update']) ? (is_numeric($_POST['update']) ? ceil((float)$_POST['update']) : 0) : 0;
    $aData['delete'] = isset($_POST['delete']) ? (is_numeric($_POST['delete']) ? ceil((float)$_POST['delete']) : 0) : 0;
    $aData['aecfg'] = $_POST['aecfg'] ?? 0;
    $aData['sort'] = isset($_POST['sort']) ? (is_numeric($_POST['sort']) ? ceil((float)$_POST['sort']) : 0) : 0;
    $aData['price'] = isset($_POST['price']) ? (is_numeric($_POST['price']) ? ceil((float)$_POST['price']) : 0) : 0;

    $aData['config_files_file'] = $_POST['config_files_file'] ?? [];
    $aData['config_files_sort'] = $_POST['config_files_sort'] ?? [];
    $aData['config_clear_file'] = $_POST['config_clear_file'] ?? [];
    $aData['config_clear_text'] = $_POST['config_clear_text'] ?? [];
    $aData['config_write_file'] = $_POST['config_write_file'] ?? [];
    $aData['config_write_text'] = $_POST['config_write_text'] ?? [];
    $aData['config_write_top'] = $_POST['config_write_top'] ?? [];
    $aData['config_write_del_file'] = $_POST['config_write_del_file'] ?? [];
    $aData['config_write_del_text'] = $_POST['config_write_del_text'] ?? [];
    $aData['config_write_del_top'] = $_POST['config_write_del_top'] ?? [];
    $aData['files_delete_file'] = $_POST['files_delete_file'] ?? [];

    $aData['cfg'] = 0;

    if ($aData['name'] == '') {
        AdminSystem::outjs(['e' => 'Необходимо указать название']);
    }

    if (AdminSystem::strlen($aData['name']) > 50) {
        AdminSystem::outjs(['e' => 'Длина названия не должна превышать 50 символов.']);
    }

    if (!in_array($aData['game'], $aGames)) {
        AdminSystem::outjs(['e' => 'Необходимо выбрать игру']);
    }

    include(LIB . 'zip.php');

    $sql->query('SELECT `id` FROM `plugins` WHERE `id`="' . $aData['update'] . '" LIMIT 1');
    if ($sql->num()) {
        $sql->query('INSERT INTO `plugins_update` set `plugin`="0", `name`="' . htmlspecialchars($aData['name']) . '", `status`="' . $aData['status'] . '", `cfg`="0", `upd`="0"');

        $id = $aData['update'];
        $aData['update'] = $sql->id();
    } else {
        $sql->query('INSERT INTO `plugins` set `name`="' . htmlspecialchars($aData['name']) . '", `cat`="' . $aData['cat'] . '", `game`="' . $aData['game'] . '", `status`="' . $aData['status'] . '", `cfg`="0", `upd`="0", `sort`="' . $aData['sort'] . '"');

        $id = $sql->id();
        $aData['update'] = 0;
    }

    $edit = [];

    if (!$aData['update']) {
        if (!move_uploaded_file($_FILES['file']['tmp_name'], FILES . 'plugins/install/' . $id . '.zip')) {
            $sql->query('DELETE FROM `plugins` WHERE `id`="' . $id . '" LIMIT 1');

            AdminSystem::outjs(['e' => 'Неудалось загрузить архив']);
        }

        $zip = new ZipArchive();

        if ($zip->open(FILES . 'plugins/install/' . $id . '.zip') !== true) {
            $sql->query('DELETE FROM `plugins` WHERE `id`="' . $id . '" LIMIT 1');

            unlink(FILES . 'plugins/install/' . $id . '.zip');

            AdminSystem::outjs(['e' => 'Неудалось открыть архив']);
        }

        $count = $zip->numFiles;

        $rm = '';

        for ($i = 0; $i < $count; $i += 1) {
            $stat = $zip->statIndex($i);

            $check = count(explode('.', $stat['name']));

            if ($check < 2) {
                continue;
            }

            $rm .= 'rm ' . $stat['name'] . ';' . PHP_EOL;

            $type = explode('.', $stat['name']);

            if ($aData['aecfg'] and in_array(end($type), $types)) {
                $edit[] = $stat['name'];
            }
        }

        $file = fopen(FILES . 'plugins/delete/' . $id . '.rm', "w");
        fputs($file, $rm);
        fclose($file);
    } else {
        if (!move_uploaded_file($_FILES['new_file']['tmp_name'], FILES . 'plugins/install/u' . $aData['update'] . '.zip')) {
            $sql->query('DELETE FROM `plugins_update` WHERE `id`="' . $aData['update'] . '" LIMIT 1');

            AdminSystem::outjs(['e' => 'Неудалось загрузить архив']);
        }

        if (!move_uploaded_file($_FILES['upd_file']['tmp_name'], FILES . 'plugins/update/' . $aData['update'] . '.zip')) {
            $sql->query('DELETE FROM `plugins_update` WHERE `id`="' . $aData['update'] . '" LIMIT 1');

            unlink(FILES . 'plugins/install/u' . $aData['update'] . '.zip');

            AdminSystem::outjs(['e' => 'Неудалось загрузить архив обновления']);
        }

        $zip = new ZipArchive();

        if ($zip->open(FILES . 'plugins/install/u' . $aData['update'] . '.zip') !== true) {
            $sql->query('DELETE FROM `plugins_update` WHERE `id`="' . $aData['update'] . '" LIMIT 1');

            unlink(FILES . 'plugins/install/u' . $aData['update'] . '.zip');
            unlink(FILES . 'plugins/update/' . $aData['update'] . '.zip');

            AdminSystem::outjs(['e' => 'Неудалось открыть архив']);
        }

        $count = $zip->numFiles;

        $rm = '';

        for ($i = 0; $i < $count; $i += 1) {
            $stat = $zip->statIndex($i);

            $check = count(explode('.', $stat['name']));

            if ($check < 2) {
                continue;
            }

            $rm .= 'rm ' . $stat['name'] . ';' . PHP_EOL;

            $type = explode('.', $stat['name']);

            if ($aData['aecfg'] and in_array(end($type), $types)) {
                $edit[] = $stat['name'];
            }
        }

        $file = fopen(FILES . 'plugins/delete/u' . $aData['update'] . '.rm', "w");
        fputs($file, $rm);
        fclose($file);
    }

    $aPacks = explode(':', $aData['packs']);

    $spacks = '';

    foreach ($aPacks as $packs) {
        $spacks .= trim($packs) . ':';
    }

    $spacks = isset($spacks[0]) ? substr($spacks, 0, -1) : '';

    $aData['packs'] = $spacks == '' ? 'all' : $spacks;

    $aIncom = explode(':', $aData['incompatible']);

    $incoms = '';

    foreach ($aIncom as $incom) {
        $incom = trim($incom);

        if (!is_numeric($incom)) {
            continue;
        }

        $incoms .= intval($incom) . ':';
    }

    $incoms = isset($incoms[0]) ? substr($incoms, 0, -1) : '';

    $aData['incompatible'] = $incoms;

    $aChoice = explode(' ', $aData['choice']);

    $choice = '';

    foreach ($aChoice as $chpl) {
        $aChpl = explode(':', $chpl);

        foreach ($aChpl as $idchpl) {
            $idchpl = trim($idchpl);

            if (!is_numeric($idchpl)) {
                continue;
            }

            $choice .= intval($idchpl) . ':';
        }

        $choice .= ' ';
    }

    $choice = isset($choice[0]) ? substr(trim($choice), 0, -1) : '';

    $aData['choice'] = $choice;

    $aRequi = explode(':', $aData['required']);

    $requis = '';

    foreach ($aRequi as $requi) {
        $requi = trim($requi);

        if (!is_numeric($requi)) {
            continue;
        }

        $requis .= intval($requi) . ':';
    }

    $requis = isset($requis[0]) ? substr($requis, 0, -1) : '';

    $aData['required'] = $requis;

    if (!$aData['aecfg']) {
        $n = 0;

        foreach ($aData['config_files_file'] as $i => $file) {
            if ($file == '') {
                continue;
            }

            $n += 1;

            $aData['config_files_sort'][$i] = $aData['config_files_sort'][$i] ? intval($aData['config_files_sort'][$i]) : $n;

            $sql->query('INSERT INTO `plugins_config` set `plugin`="' . $id . '", `update`="' . $aData['update'] . '", `file`="' . $file . '", `sort`="' . $n . '"');
        }

        if ($n) {
            $aData['cfg'] = 1;
        }
    } else {
        $n = 0;

        foreach ($edit as $file) {
            $n += 1;

            $sql->query('INSERT INTO `plugins_config` set `plugin`="' . $id . '", `update`="' . $aData['update'] . '", `file`="' . $file . '", `sort`="' . $n . '"');
        }

        if ($n) {
            $aData['cfg'] = 1;
        }
    }

    foreach ($aData['config_clear_file'] as $i => $file) {
        if ($aData['config_clear_text'][$i] == '' || $file == '') {
            continue;
        }

        $regex = (string)$aData['config_clear_regex'] == 'on' ? 1 : 0;

        $text = htmlspecialchars(trim($aData['config_clear_text'][$i]));

        $sql->query('INSERT INTO `plugins_clear` set `plugin`="' . $id . '", `update`="' . $aData['update'] . '", `text`="' . $text . '", `file`="' . $file . '", `regex`="' . $regex . '"');
    }

    foreach ($aData['config_write_file'] as $i => $file) {
        if ($aData['config_write_text'][$i] == '' || $file == '') {
            continue;
        }

        $top = (string)$aData['config_write_top'][$i] == 'on' ? 1 : 0;

        $text = htmlspecialchars(trim($aData['config_write_text'][$i]));

        $sql->query('INSERT INTO `plugins_write` set `plugin`="' . $id . '", `update`="' . $aData['update'] . '", `text`="' . $text . '", `file`="' . $file . '", `top`="' . $top . '"');
    }

    foreach ($aData['config_write_del_file'] as $i => $file) {
        if ($aData['config_write_del_text'][$i] == '' || $file == '') {
            continue;
        }

        $top = (string)$aData['config_write_del_top'][$i] == 'on' ? 1 : 0;

        $text = htmlspecialchars(trim($aData['config_write_del_text'][$i]));

        $sql->query('INSERT INTO `plugins_write_del` set `plugin`="' . $id . '", `update`="' . $aData['update'] . '", `text`="' . $text . '", `file`="' . $file . '", `top`="' . $top . '"');
    }

    foreach ($aData['files_delete_file'] as $file) {
        if ($file == '') {
            continue;
        }

        $sql->query('INSERT INTO `plugins_delete` set `plugin`="' . $id . '", `update`="' . $aData['update'] . '", `file`="' . $file . '"');
    }

    if ($aData['delete']) {
        $sql->query('INSERT INTO `plugins_delete_ins` set `plugin`="' . $id . '", `update`="' . $aData['update'] . '", `file`="' . $aData['delete'] . '"');
    }

    if ($aData['update']) {
        $sql->query('UPDATE `plugins_update` set '
            . '`plugin`="' . $id . '",'
            . '`desc`="' . htmlspecialchars($aData['desc']) . '",'
            . '`info`="' . htmlspecialchars($aData['info']) . '",'
            . '`images`="' . htmlspecialchars($aData['images']) . '",'
            . '`incompatible`="' . $aData['incompatible'] . '",'
            . '`choice`="' . $aData['choice'] . '",'
            . '`required`="' . $aData['required'] . '",'
            . '`cfg`="' . $aData['cfg'] . '",'
            . '`price`="' . $aData['price'] . '",'
            . '`packs`="' . $aData['packs'] . '" WHERE `id`="' . $aData['update'] . '"');

        $sql->query('UPDATE `plugins` set `upd`="' . $aData['update'] . '" WHERE `id`="' . $id . '" LIMIT 1');

        $sql->query('UPDATE `plugins_update` set `upd`="' . $aData['update'] . '" WHERE `id`!="' . $aData['update'] . '" AND `plugin`="' . $id . '" AND `upd`="0" ORDER BY `id` DESC LIMIT 1');
    } else {
        $sql->query('UPDATE `plugins` set'
                . '`desc`="' . htmlspecialchars($aData['desc']) . '",'
                . '`info`="' . htmlspecialchars($aData['info']) . '",'
                . '`images`="' . htmlspecialchars($aData['images']) . '",'
                . '`incompatible`="' . $aData['incompatible'] . '",'
                . '`choice`="' . $aData['choice'] . '",'
                . '`required`="' . $aData['required'] . '",'
                . '`cfg`="' . $aData['cfg'] . '",'
                . '`price`="' . $aData['price'] . '",'
                . '`packs`="' . $aData['packs'] . '" WHERE `id`="' . $id . '"');
    }

    AdminSystem::outjs(['s' => 'ok']);
}

$html->get('addpl', 'sections/addons');
$html->pack('main');

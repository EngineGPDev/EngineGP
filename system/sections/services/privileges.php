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

if ($go) {
    $nmch = 'privileges' . System::ip();

    if ($mcache->get($nmch)) {
        System::outjs(['e' => System::text('other', 'mcache')], $nmch);
    }

    $mcache->set($nmch, 1, false, 10);

    $aData = [];

    $aData['address'] = $_POST['address'] ?? System::outjs(['e' => 'Необходимо указать адрес сервера'], $nmch);
    $aData['type'] = $_POST['type'] ?? System::outjs(['e' => 'Необходимо указать тип авторизации на сервере'], $nmch);
    $aData['data'] = isset($_POST['data']) ? str_replace('"', '', $_POST['data']) : System::outjs(['e' => 'Необходимо указать данные авторизации'], $nmch);
    $aData['passwd'] = $_POST['passwd'] ?? '';
    $aData['service'] = isset($_POST['service']) ? System::int($_POST['service']) : System::outjs(['e' => 'Необходимо указать услугу'], $nmch);
    $aData['time'] = isset($_POST['time']) ? System::int($_POST['time']) : System::outjs(['e' => 'Необходимо указать период'], $nmch);
    $aData['mail'] = $_POST['mail'] ?? System::outjs(['e' => 'Необходимо указать почту'], $nmch);

    if (!in_array($aData['type'], ['a', 'ca', 'de'])) {
        System::outjs(['e' => 'Неправильно передан тип авторизации на сервере'], $nmch);
    }

    switch ($aData['type']) {
        case 'a':
            if ($aData['data'] == '') {
                System::outjs(['e' => 'Необходимо указать ник'], $nmch);
            }
            break;
        case 'ca':
            if (System::valid($aData['data'], 'steamid') || System::valid($aData['data'], 'steamid3')) {
                System::outjs(['e' => 'Неправильный формат SteamID'], $nmch);
            }
            break;
        default:
            if (System::valid($aData['data'], 'ip')) {
                System::outjs(['e' => 'Неправильный формат IP'], $nmch);
            }
    }

    if (System::valid($aData['address'], 'other', $aValid['address'])) {
        System::outjs(['e' => 'Адрес игрового сервера имеет неверный формат'], $nmch);
    }

    $sql->query('SELECT `id`, `name`, `game` FROM `servers` WHERE `address`="' . $aData['address'] . '" LIMIT 1');
    if (!$sql->num()) {
        System::outjs(['e' => 'Игровой сервер не найден в базе'], $nmch);
    }

    $server = $sql->get();

    $sql->query('SELECT `id` FROM `admins_' . $server['game'] . '` WHERE `server`="' . $server['id'] . '" AND `value`="' . htmlspecialchars($aData['data']) . '" LIMIT 1');
    if ($sql->num()) {
        System::outjs(['e' => 'Привилегия для данного игрока уже установлена, дождитесь её завершения.'], $nmch);
    }

    if ($aData['type'] != 'de' and System::valid($aData['passwd'], 'other', $aValid['passwd'])) {
        System::outjs(['e' => 'Неправильный формат пароля, используйте латинские буквы и цифры от 6 до 20 символов'], $nmch);
    }

    if (System::valid($aData['mail'], 'other', $aValid['mail'])) {
        System::outjs(['e' => 'Неправильный формат почты'], $nmch);
    }

    $sql->query('SELECT `flags`, `immunity`, `data` FROM `privileges_list` WHERE `id`="' . $aData['service'] . '" AND `server`="' . $server['id'] . '" LIMIT 1');
    if (!$sql->num()) {
        System::outjs(['e' => 'Указанная услуга не найдена'], $nmch);
    }

    $privilege = $sql->get();

    $data = System::b64djs($privilege['data']);

    if (!array_key_exists($aData['time'], $data)) {
        System::outjs(['e' => 'Неправильно указан период'], $nmch);
    }

    $price = $data[$aData['time']];

    $time = !$aData['time'] ? $start_point + 172800000 : $start_point + $aData['time'] * 86400;

    if ($server['game'] == 'cs') {
        $text = '"' . $aData['data'] . '" "' . $aData['passwd'] . '" "' . $privilege['flags'] . '" "' . $aData['type'] . '"';
        $sqlq = 'INSERT INTO `admins_' . $server['game'] . '` set'
            . '`server`="' . $server['id'] . '",'
            . '`value`="' . htmlspecialchars($aData['data']) . '",'
            . '`active`="1",'
            . '`passwd`="' . $aData['passwd'] . '",'
            . '`flags`="' . $privilege['flags'] . '",'
            . '`type`="' . $aData['type'] . '",'
            . '`time`="' . $time . '",'
            . '`text`="' . htmlspecialchars($text) . '",'
            . '`info`="Онлайн покупка"';
    } else {
        $text = '"' . $aData['data'] . '" "' . $aData['immunity'] . ':' . $privilege['flags'] . '" "' . $aData['passwd'] . '"';
        $sqlq = 'INSERT INTO `admins_' . $server['game'] . '` set'
            . '`server`="' . $server['id'] . '",'
            . '`value`="' . $aData['data'] . '",'
            . '`active`="1",'
            . '`passwd`="' . htmlspecialchars($aData['passwd']) . '",'
            . '`flags`="' . $aData['flags'] . '",'
            . '`immunity`="' . $privilege['immunity'] . '",'
            . '`time`="' . $time . '",'
            . '`text`="' . htmlspecialchars($text) . '",'
            . '`info`="Онлайн покупка"';
    }

    $sql->query('SELECT `key` FROM `privileges_buy` WHERE '
        . '`server`="' . $server['id'] . '" AND'
        . '`text`="' . base64_encode($text) . '" AND'
        . '`price`="' . $price . '" AND'
        . '`mail`="' . $aData['mail'] . '" AND'
        . '`status`="0" LIMIT 1');

    if (!$sql->num()) {
        $key = System::key();

        $sql->query('INSERT INTO `privileges_buy` set '
            . '`server`="' . $server['id'] . '",'
            . '`text`="' . base64_encode($text) . '",'
            . '`sql`="' . base64_encode($sqlq) . '",'
            . '`price`="' . $price . '",'
            . '`key`="' . $key . '",'
            . '`date`="' . $start_point . '",'
            . '`mail`="' . $aData['mail'] . '",'
            . '`status`="0"');
    } else {
        $pay = $sql->get();
        $key = $pay['key'];
    }

    $html->get('pay', 'sections/services/privileges');

    $html->set('cur', $cfg['currency']);
    $html->set('wmr', $cfg['webmoney_wmr']);
    $html->set('key', $key);
    $html->set('sum', $price);

    $html->pack('pay');

    System::outjs(['s' => $html->arr['pay']], $nmch);
}

if (isset($url['select'])) {
    if ($url['select'] == 'time') {
        $service = isset($url['service']) ? System::int($url['service']) : System::out();

        $sql->query('SELECT  `data` FROM `privileges_list` WHERE `id`="' . $service . '" LIMIT 1');
        $list = $sql->get();

        $time = '';

        $data = System::b64djs($list['data']);

        if (isset($data[0])) {
            $time = '<option value="0">Навсегда / ' . $data[0] . ' ' . $cfg['currency'] . '</option>';

            unset($data[0]);
        }

        foreach ($data as $days => $price) {
            $time .= '<option value="' . $days . '">' . $days . ' ' . System::day($time) . ' / ' . $price . ' ' . $cfg['currency'] . '</option>';
        }

        System::out($time);
    }

    $address = isset($_POST['address']) ? trim($_POST['address']) : System::outjs(['e' => 'Необходимо указать адрес игрового сервера']);

    if (System::valid($address, 'other', $aValid['address'])) {
        System::outjs(['e' => 'Указанный адрес имеет неверный формат']);
    }

    $sql->query('SELECT `id`, `name` FROM `servers` WHERE `address`="' . $address . '" LIMIT 1');
    if (!$sql->num()) {
        System::outjs(['e' => 'Игровой сервер не найден в базе']);
    }

    $server = $sql->get();

    $sql->query('SELECT `active` FROM `privileges` WHERE `server`="' . $server['id'] . '" LIMIT 1');
    if (!$sql->num()) {
        System::outjs(['e' => 'Игровой сервер не предоставляет услуги']);
    }

    $privilege = $sql->get();

    if (!$privilege['active']) {
        System::outjs(['e' => 'Игровой сервер времено не предоставляет услуги']);
    }

    $name = '';

    $sql->query('SELECT  `id`, `name` FROM `privileges_list` WHERE `server`="' . $server['id'] . '" ORDER BY `id` ASC LIMIT 5');
    while ($list = $sql->get()) {
        $name .= '<option value="' . $list['id'] . '">' . $list['name'] . '</option>';
    }

    $sql->query('SELECT  `data` FROM `privileges_list` WHERE `server`="' . $server['id'] . '" ORDER BY `id` ASC LIMIT 1');
    $list = $sql->get();

    $time = '';

    $data = System::b64djs($list['data']);

    if (isset($data[0])) {
        $time = '<option value="0">Навсегда / ' . $data[0] . ' ' . $cfg['currency'] . '</option>';

        unset($data[0]);
    }

    foreach ($data as $days => $price) {
        $time .= '<option value="' . $days . '">' . $days . ' ' . System::day($time) . ' / ' . $price . ' ' . $cfg['currency'] . '</option>';
    }

    $html->get('form', 'sections/services/privileges');

    $html->set('home', $cfg['http']);
    $html->set('name', $server['name']);
    $html->set('address', $address);
    $html->set('services', $name);
    $html->set('time', $time);

    $html->pack('form');

    System::outjs(['s' => $html->arr['form']]);
}

$html->get('index', 'sections/services/privileges');

$html->pack('main');

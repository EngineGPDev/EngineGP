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

$sql->query('SELECT `mail`, `new_mail`, `confirm_mail`, `wmr`, `phone`, `confirm_phone`, `contacts` FROM `users` WHERE `id`="' . $user['id'] . '" LIMIT 1');
$user = array_merge($user, $sql->get());

if ($go) {
    $name_mcache = 'lk_' . $user['id'];

    // Проверка сессии
    if ($mcache->get($name_mcache)) {
        System::outjs(['e' => $text['mcache']], $name_mcache);
    }

    // Создание сессии
    $mcache->set($name_mcache, 1, false, 10);

    if (!isset($url['type'])) {
        exit;
    }

    switch ($url['type']) {
        case 'contacts':
            $contacts = $_POST['contacts'] ?? '';

            if ($contacts != '') {
                if (System::valid($contacts, 'other', $aValid['contacts'])) {
                    System::outjs(['e' => System::text('input', 'contacts_valid')], $name_mcache);
                }
            }

            // Запись контактов в базу, если не совпадает с текущими данными
            if ($contacts != $user['contacts']) {
                $sql->query('UPDATE `users` set `contacts`="' . $contacts . '" WHERE `id`="' . $user['id'] . '" LIMIT 1');
            }

            // Выхлоп удачного выполнения операции
            System::outjs(['s' => 'ok'], $name_mcache);

            // no break
        case 'passwd':
            $passwd = $_POST['passwd'] ?? '';

            if (System::valid($passwd, 'other', $aValid['passwd'])) {
                System::outjs(['e' => System::text('input', 'passwd_valid')], $name_mcache);
            }

            $passwd = System::passwdkey($passwd);

            // Обновление пароля в базе
            $sql->query('UPDATE `users` set `passwd`="' . $passwd . '" WHERE `id`="' . $user['id'] . '" LIMIT 1');

            // Выхлоп удачного выполнения операции
            System::outjs(['s' => 'ok'], $name_mcache);

            // no break
        case 'mail':
            $mail = $_POST['mail'] ?? '';

            // Проверка введенной почты
            if (System::valid($mail, 'other', $aValid['mail'])) {
                System::outjs(['e' => System::text('input', 'mail_valid')], $name_mcache);
            }

            if ($mail == $user['mail']) {
                System::outjs(['e' => System::text('input', 'similar')], $name_mcache);
            }

            // Проверка почты на занятость
            $sql->query('SELECT `id` FROM `users` WHERE `mail`="' . $mail . '" LIMIT 1');
            if ($sql->num()) {
                System::outjs(['e' => System::text('input', 'mail_use')], $name_mcache);
            }

            // Генерация кода
            $key = System::key('mail_change' . $user['id']);

            // Отправка письма на старую почту
            if (System::mail('Смена почты', System::updtext(System::text('mail', 'change'), ['site' => $cfg['name'], 'url' => $cfg['http'] . 'user/section/lk/subsection/action/type/confirm_mail/confirm/' . $key . '/go/1']), $user['mail'])) {
                $sql->query('UPDATE `users` set `new_mail`="' . $mail . '", `confirm_mail`="' . $key . '" WHERE `id`="' . $user['id'] . '" LIMIT 1');

                System::outjs(['s' => System::text('output', 'oldmail'), 'mail' => System::mail_domain($user['mail'])], $name_mcache);
            }

            // Выхлоп: неудалось отправить письмо
            System::outjs(['e' => System::text('error', 'mail')], $name_mcache);

            // no break
        case 'confirm_mail':
            $key = $url['confirm'] ?? '';

            if ($key != $user['confirm_mail']) {
                System::outhtml(System::text('output', 'confirm_key_error'), 4, $cfg['http'] . 'user/section/lk', $name_mcache);
            }

            // Проверка почты на занятость
            $sql->query('SELECT `id` FROM `users` WHERE `mail`="' . $user['confirm_mail'] . '" LIMIT 1');
            if ($sql->num()) {
                System::outhtml(System::text('input', 'mail_use'), 4, $cfg['http'] . 'user/section/lk', $name_mcache);
            }

            $sql->query('UPDATE `users` set `mail`="' . $user['new_mail'] . '", `new_mail`="", `confirm_mail`="" WHERE `id`="' . $user['id'] . '" LIMIT 1');

            // Выхлоп удачного выполнения операции
            System::outhtml(System::text('output', 'confirm_mail_done'), 4, $cfg['http'] . 'user/section/lk', $name_mcache);

            // no break
        case 'phone':
            // Проверка, подтвержден ли номер
            if ($user['confirm_phone'] == '1') {
                System::outjs(['e' => System::text('output', 'confirm_phone')], $name_mcache);
            }

            $phone = isset($_POST['phone']) ? str_replace('+', '', trim($_POST['phone'])) : '';

            // Проверка введенного номера
            if ($phone != '') {
                if (System::valid($phone, 'other', $aValid['phone'])) {
                    System::outjs(['e' => System::text('input', 'phone_valid')], $name_mcache);
                }
            }

            // Запись номера, если не совпадает с текущим
            if ($phone != $user['phone']) {
                $sql->query('UPDATE `users` set `phone`="' . $phone . '" WHERE `id`="' . $user['id'] . '" LIMIT 1');
            }

            // Выхлоп удачного выполнения операции
            System::outjs(['s' => 'ok'], $name_mcache);

            // no break
        case 'confirm_phone':
            // Проверка, подтвержден ли номер
            if ($user['confirm_phone'] == '1') {
                System::outjs(['e' => System::text('output', 'confirm_phone_done')], $name_mcache);
            }

            if ($user['phone'] == '') {
                System::outjs(['e' => System::text('input', 'phone')], $name_mcache);
            }

            // Проверка, отправлялось ли сообщение
            if (strlen($user['confirm_phone']) == 6) {
                System::outjs(['s' => 'ok'], $name_mcache);
            }

            // Генерация кода подтверждения
            $code = System::smscode();

            // Отправка кода подтверждения на номер
            if (System::sms('code: ' . $code, $user['phone'])) {
                $sql->query('UPDATE `users` set `confirm_phone`="' . $code . '" WHERE `id`="' . $user['id'] . '" LIMIT 1');

                System::outjs(['s' => 'ok'], $name_mcache);
            }

            // Выхлоп: неудалось отправить сообщение
            System::outjs(['e' => System::text('output', 'confirm_phone_error')], $name_mcache);

            // no break
        case 'confirm_phone_end':
            // Проверка, подтвержден ли номер
            if ($user['confirm_phone'] == '1') {
                System::outjs(['e' => System::text('output', 'confirm_phone_done')], $name_mcache);
            }

            if ($user['phone'] == '') {
                System::outjs(['e' => System::text('input', 'phone')], $name_mcache);
            }

            $code = isset($_POST['smscode']) ? System::int($_POST['smscode']) : '';

            if ($code != $user['confirm_phone']) {
                System::outjs(['e' => System::text('output', 'confirm_key_error')], $name_mcache);
            }

            $sql->query('UPDATE `users` set `confirm_phone`="1" WHERE `id`="' . $user['id'] . '" LIMIT 1');

            // Выхлоп удачного выполнения операции
            System::outjs(['s' => 'ok'], $name_mcache);

            // no break
        case 'wmr':
            $wmr = $_POST['wmr'] ?? '';

            // Проверка наличия указанного кошелька
            if (isset($user['wmr'][0]) and in_array($user['wmr'][0], ['R', 'Z', 'U'])) {
                System::outjs(['e' => System::text('input', 'wmr_confirm')], $name_mcache);
            }

            if (System::valid($wmr, 'wm')) {
                System::outjs(['e' => System::text('input', 'wmr_valid')], $name_mcache);
            }

            // Обновление кошелька в базе
            $sql->query('UPDATE `users` set `wmr`="' . $wmr . '" WHERE `id`="' . $user['id'] . '" LIMIT 1');

            // Выхлоп удачного выполнения операции
            System::outjs(['s' => 'ok'], $name_mcache);
    }
}

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

// Проверка на авторизацию
System::auth();

// Генерация новой капчи
if (isset($url['captcha'])) {
    System::captcha('signup', $uip);
}

$aData = [];

// Сбор данных из $_POST в $aData
if (isset($_POST['mail'])) {
    foreach ($aSignup['input'] as $name => $add) {
        if (!$add) {
            continue;
        }

        $aData[$name] = isset($_POST[$name]) ? trim($_POST[$name]) : '';
    }
}

// Регистрация
if ($go) {
    $nmch = 'go_signup_' . $uip;

    if ($mcache->get($nmch)) {
        System::outjs(['e' => System::text('other', 'mcache')], $nmch);
    }

    $mcache->set($nmch, 1, false, 15);

    // Проверка капчи
    if (!isset($_POST['captcha']) || System::captcha_check('signup', $uip, $_POST['captcha'])) {
        System::outjs(['e' => System::text('other', 'captcha')], $nmch);
    }

    // Проверка входных данных
    foreach ($aData as $input => $val) {
        // Если не заполнено поле
        if ($val == '') {
            System::outjs(['e' => System::text('input', 'all')], $nmch);
        }

        // Проверка данных на валидность
        if (System::valid($val, 'other', $aValid[$input])) {
            System::outjs(['e' => System::text('input', $input . '_valid')], $nmch);
        }
    }

    // Проверка логина на занятость
    if (isset($aData['login'])) {
        $sql->query('SELECT `id` FROM `users` WHERE `login`="' . $aData['login'] . '" LIMIT 1');
        if ($sql->num()) {
            System::outjs(['e' => System::text('input', 'login_use')], $nmch);
        }
    }

    if (!isset($aData['mail'])) {
        System::outjs(['e' => System::text('input', 'mail_valid')], $nmch);
    }

    // Проверка почты на занятость
    $sql->query('SELECT `id` FROM `users` WHERE `mail`="' . $aData['mail'] . '" LIMIT 1');
    if ($sql->num()) {
        System::outjs(['e' => System::text('input', 'mail_use')], $nmch);
    }

    // Проверка телефона на занятость
    if (isset($aData['phone'])) {
        $sql->query('SELECT `id` FROM `users` WHERE `phone`="' . $aData['phone'] . '" LIMIT 1');
        if ($sql->num()) {
            System::outjs(['e' => System::text('input', 'phone_use')], $nmch);
        }
    }

    // Проверка контактов на занятость
    if (isset($aData['contacts'])) {
        $sql->query('SELECT `id` FROM `users` WHERE `contacts`="' . $aData['contacts'] . '" LIMIT 1');
        if ($sql->num()) {
            System::outjs(['e' => System::text('input', 'use_contacts')], $nmch);
        }
    }

    // Проверка почты на подачу регистрации
    $sql->query('SELECT `id`, `key` FROM `signup` WHERE `mail`="' . $aData['mail'] . '" LIMIT 1');
    if ($sql->num()) {
        $signup = $sql->get();
        $sql->query('UPDATE `signup` set `date`="' . $start_point . '" WHERE `id`="' . $signup['id'] . '" LIMIT 1');

        // Повторная отправка письма на почту
        System::mail(
            'Регистрация',
            System::updtext(
                System::text('mail', 'signup'),
                [
                    'site' => $cfg['name'],
                    'url' => $cfg['http'] . 'user/section/signup/confirm/' . $signup['key'],
                ]
            ),
            $aData['mail']
        );
        System::outjs(['s' => System::text('output', 'remail'), 'mail' => System::mail_domain($aData['mail'])], $nmch);
    }

    // Генерация ключа
    $key = System::key('signup_' . $uip);

    $data = System::b64js($aData);

    // Запись данных в базу
    $sql->query('INSERT INTO `signup` set `mail`="' . $aData['mail'] . '", `key`="' . $key . '", `data`="' . $data . '", `date`="' . $start_point . '"');

    // Отправка сообщения на почту
    if (System::mail('Регистрация', System::updtext(System::text('mail', 'signup'), ['site' => $cfg['name'], 'url' => $cfg['http'] . 'user/section/signup/confirm/' . $key]), $aData['mail'])) {
        System::outjs(['s' => System::text('output', 'mail'), 'mail' => System::mail_domain($aData['mail'])], $nmch);
    }

    // Выхлоп: не удалось отправить письмо
    System::outjs(['e' => System::text('error', 'mail')], $nmch);
}

// Завершение регистрации
if (isset($url['confirm']) && !System::valid($url['confirm'], 'md5')) {
    $sql->query('SELECT `id`, `data` FROM `signup` WHERE `key`="' . $url['confirm'] . '" LIMIT 1');
    if ($sql->num()) {
        $signup = $sql->get();

        $aData = System::b64djs($signup['data']);

        foreach ($aSignup['input'] as $name => $add) {
            $aNData[$name] = $aData[$name] ?? '';
        }

        unset($aData);

        // Если регистрация без указания логина
        if (empty($aNData['login'])) {
            $lchar = false;

            while (1) {
                $aNData['login'] = System::login($aNData['mail'], $lchar);

                $sql->query('SELECT `id` FROM `users` WHERE `login`="' . $aNData['login'] . '" LIMIT 1');
                if (!$sql->num()) {
                    break;
                }

                $lchar = true;
            }
        }

        // Если регистрация без указания пароля
        if (empty($aNData['passwd'])) {
            $aNData['passwd'] = System::passwd(10);
        }

        $part = null;

        // Реферал
        if (isset($_COOKIE['referrer'])) {
            $part = ', `part`="' . System::int($_COOKIE['referrer']) . '"';
        }

        // Запись данных в базу
        $sql->query('INSERT INTO `users` set '
            . '`login`="' . $aNData['login'] . '",'
            . '`passwd`="' . System::passwdkey($aNData['passwd']) . '",'
            . '`mail`="' . $aNData['mail'] . '",'
            . '`name`="' . $aNData['name'] . '",'
            . '`lastname`="' . $aNData['lastname'] . '",'
            . '`patronymic`="' . $aNData['patronymic'] . '",'
            . '`phone`="' . $aNData['phone'] . '",'
            . '`contacts`="' . $aNData['contacts'] . '",'
            . '`balance`="0", `group`="user", `date`="' . $start_point . '"' . $part);

        $sql->query('DELETE FROM `signup` WHERE `id`="' . $signup['id'] . '" LIMIT 1');

        // Отправка сообщения на почту
        if (System::mail('Завершение регистрации', System::updtext(System::text('mail', 'signup_end'), ['site' => $cfg['name'], 'login' => $aNData['login'], 'passwd' => $aNData['passwd']]), $aNData['mail'])) {
            System::outhtml(System::text('output', 'signup'), 5, 'http://' . System::mail_domain($aNData['mail']));
        }

        // Выхлоп: не удалось отправить письмо
        System::outjs(['e' => System::text('error', 'mail')], $nmch);
    }

    System::outhtml(System::text('error', 'signup'), 5);
}

// Генерация формы
foreach ($aSignup['input'] as $name => $add) {
    if (!$add) {
        continue;
    }

    $html->get('signup', 'sections/user/inputs');
    $html->set('name', $name);
    $html->set('info', $aSignup['info'][$name]);
    $html->set('type', $aSignup['type'][$name]);
    $html->set('placeholder', $aSignup['placeholder'][$name]);
    $html->pack('inputs');
}

$html->get('signup', 'sections/user');

$inputsjs = '';

foreach ($aSignup['input'] as $name => $add) {
    if (!$add) {
        continue;
    }

    $inputsjs .= '"' . $name . '",';
}

$html->set('inputs', $html->arr['inputs'], true);
$html->set('inputsjs', $inputsjs);

$html->pack('main');

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

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

// Проверка на авторизацию
sys::auth();

// Генерация новой капчи
if (isset($url['captcha'])) {
    sys::captcha('recovery', $uip);
}

// Восстановление
if ($go) {
    $nmch = 'go_recovery_' . $uip;

    if ($mcache->get($nmch)) {
        sys::outjs(['e' => sys::text('all', 'mcache')], $nmch);
    }

    $mcache->set($nmch, 1, false, 15);

    // Проверка капчи
    if (!isset($_POST['captcha']) || sys::captcha_check('recovery', $uip, $_POST['captcha'])) {
        sys::outjs(['e' => sys::text('other', 'captcha')], $nmch);
    }

    $aData = [];

    $aData['login'] = $_POST['login'] ?? '';

    // Проверка логина/почты на валидность
    if (sys::valid($aData['login'], 'other', $aValid['mail']) && sys::valid($aData['login'], 'other', $aValid['login'])) {
        $out = 'login';

        // Если в логине указана почта
        if (sys::ismail($aData['login'])) {
            $out = 'mail';
        }

        sys::outjs(['e' => sys::text('input', $out . '_valid')], $nmch);
    }

    $sql_q = '`login`';

    // Если в логине указана почта
    if (sys::ismail($aData['login'])) {
        $sql_q = '`mail`';
    }

    // Проверка существования пользователя
    $sql->query('SELECT `id`, `mail` FROM `users` WHERE ' . $sql_q . '="' . $aData['login'] . '" LIMIT 1');
    if (!$sql->num()) {
        sys::outjs(['e' => sys::text('input', 'recovery')], $nmch);
    }

    $user = $sql->get();

    $link = 'user/section/recovery/confirm/';

    // Проверка подачи запроса на восстановление
    $sql->query('SELECT `id`, `key` FROM `recovery` WHERE `user`="' . $user['id'] . '" LIMIT 1');
    if ($sql->num()) {
        $recovery = $sql->get();
        $sql->query('UPDATE `recovery` set `date`="' . $start_point . '" WHERE `id`="' . $recovery['id'] . '" LIMIT 1');

        // Повторная отправка письма на почту
        if (sys::mail('Восстановление доступа', sys::updtext(sys::text('mail', 'recovery'), ['site' => $cfg['name'], 'url' => $cfg['http'] . $link . $recovery['key']]), $user['mail'])) {
            sys::outjs(['s' => sys::text('output', 'remail'), 'mail' => sys::mail_domain($user['mail'])], $nmch);
        }

        // Выхлоп: не удалось отправить письмо
        sys::outjs(['e' => sys::text('error', 'mail')], $nmch);
    }

    // Генерация ключа
    $key = sys::key('recovery_' . $uip);

    // Запись данных в базу
    $sql->query('INSERT INTO `recovery` set `user`="' . $user['id'] . '", `mail`="' . $user['mail'] . '", `key`="' . $key . '", `date`="' . $start_point . '"');

    // Отправка письма на почту
    if (sys::mail('Восстановление доступа', sys::updtext(sys::text('mail', 'recovery'), ['site' => $cfg['name'], 'url' => $cfg['http'] . $link . $key]), $user['mail'])) {
        sys::outjs(['s' => sys::text('output', 'mail'), 'mail' => sys::mail_domain($user['mail'])], $nmch);
    }

    // Выхлоп: не удалось отправить письмо
    sys::outjs(['e' => sys::text('error', 'mail')], $nmch);
}

// Завершение восстановления
if (isset($url['confirm']) && !sys::valid($url['confirm'], 'md5')) {
    $sql->query('SELECT `id`, `user`, `mail` FROM `recovery` WHERE `key`="' . $url['confirm'] . '" LIMIT 1');
    if ($sql->num()) {
        $data = $sql->get();
        $passwd = sys::passwd(10);

        $sql->query('SELECT `security_ip` FROM `users` WHERE `id`="' . $data['user'] . '" LIMIT 1');
        $user = $sql->get();

        // Если включена защита по ip
        if ($user['security_ip']) {
            $sql->query('SELECT `id` FROM `security` WHERE `user`="' . $data['user'] . '" AND `address`="' . $uip . '" LIMIT 1');

            if (!$sql->num()) {
                $sql->query('INSERT INTO `security` set `user`="' . $data['user'] . '", `address`="' . $uip . '", `time`="' . $start_point . '"');
            }
        }

        $sql->query('UPDATE `users` set `passwd`="' . sys::passwdkey($passwd) . '" WHERE `id`="' . $data['user'] . '" LIMIT 1');
        $sql->query('DELETE FROM `recovery` WHERE `id`="' . $data['id'] . '" LIMIT 1');

        if (sys::mail('Восстановление доступа', sys::updtext(sys::text('mail', 'recovery_end'), ['site' => $cfg['name'], 'passwd' => $passwd]), $data['mail'])) {
            sys::outhtml('Операция по восстановлению успешно выполнена, на вашу почту отправлен новый пароль.', 5, 'http://' . sys::mail_domain($data['mail']));
        }

        sys::outhtml(sys::text('error', 'mail'), 5);
    }

    sys::outhtml(sys::text('error', 'recovery'), 5);
}

$html->get('recovery', 'sections/user');
$html->pack('main');

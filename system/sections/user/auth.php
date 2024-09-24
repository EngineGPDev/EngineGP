<?php
/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @copyright Copyright (c) 2018-present Solovev Sergei <inbox@seansolovev.ru>
 *
 * @link      https://github.com/EngineGPDev/EngineGP for the canonical source repository
 *
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE MIT License
 */

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

// Проверка на авторизацию
sys::auth();

// Генерация новой капчи
if (isset($url['captcha'])) {
    sys::captcha('auth', $uip);
}

// Авторизация
if ($go) {
    $nmch = 'go_auth_' . $uip;

    if ($mcache->get($nmch)) {
        sys::outjs(['e' => sys::text('other', 'mcache')], $nmch);
    }

    $mcache->set($nmch, 1, false, 15);

    // Проверка капчи
    if (!isset($_POST['captcha']) || sys::captcha_check('auth', $uip, $_POST['captcha'])) {
        sys::outjs(['e' => sys::text('other', 'captcha')], $nmch);
    }

    $aData = [];

    $aData['login'] = $_POST['login'] ?? '';
    $aData['passwd'] = $_POST['passwd'] ?? '';

    // Проверка входных данных
    foreach ($aData as $val) {
        if ($val == '') {
            sys::outjs(['e' => sys::text('input', 'all')], $nmch);
        }
    }

    // Проверка логина/почты на валидность
    if (sys::valid($aData['login'], 'other', $aValid['mail']) and sys::valid($aData['login'], 'other', $aValid['login'])) {
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
    $sql->query('SELECT `id`, `login`, `mail`, `security_ip`, `security_code`, `passwd` FROM `users` WHERE ' . $sql_q . '="' . $aData['login'] . '" LIMIT 1');
    if (!$sql->num()) {
        sys::outjs(['e' => sys::text('input', 'auth')], $nmch);
    }

    $user = $sql->get();

    // Проверка пароля
    if (!sys::passwdverify($aData['passwd'], $user['passwd'])) {
        sys::outjs(['e' => sys::text('input', 'auth')], $nmch);
    }

    $subnetwork = sys::whois($uip);

    // Если включена защита по ip
    if ($user['security_ip']) {
        $sql->query('SELECT `id` FROM `security` WHERE `user`="' . $user['id'] . '" AND `address`="' . $uip . '" LIMIT 1');

        if (!$sql->num()) {
            if ($subnetwork != 'не определена') {
                $sql->query('SELECT `id` FROM `security` WHERE `user`="' . $user['id'] . '" AND `address`="' . $subnetwork . '" LIMIT 1');

                if (!$sql->num()) {
                    sys::outjs(['e' => 'Ваш ip адрес не найден в числе указаных адресов для авторизации.'], $nmch);
                }
            } else {
                sys::outjs(['e' => 'Ваш ip адрес не найден в числе указаных адресов для авторизации.'], $nmch);
            }
        }
    }

    // Если включена защита по коду
    if ($user['security_code']) {
        $code = $_POST['code'] ?? '';

        if ($code == '' || $code != $mcache->get('auth_code_security_' . $user['id'])) {
            $ncod = sys::code();

            // Отправка сообщения на почту
            if (sys::mail('Авторизация', sys::updtext(sys::text('mail', 'security_code'), ['site' => $cfg['name'], 'code' => $ncod]), $user['mail'])) {
                $mcache->set('auth_code_security_' . $user['id'], $ncod, false, 180);

                if ($code == '') {
                    sys::outjs(['i' => 'На вашу почту отправлено письмо с кодом подтверждения.', 'mail' => sys::mail_domain($user['mail'])], $nmch);
                }

                sys::outjs(['i' => 'На вашу почту отправлено письмо с кодом подтверждения снова.', 'mail' => sys::mail_domain($user['mail'])], $nmch);
            }

            // Выхлоп: не удалось отправить письмо
            sys::outjs(['e' => sys::text('error', 'mail')], $nmch);
        }
    }

    $_SERVER['HTTP_USER_AGENT'] = mb_substr($_SERVER['HTTP_USER_AGENT'], 0, 200);

    // Обновление информации о пользователе
    $sql->query('UPDATE `users` set `ip`="' . $uip . '", `browser`="' . sys::browser($_SERVER['HTTP_USER_AGENT']) . '", `time`="' . $start_point . '" WHERE `id`="' . $user['id'] . '" LIMIT 1');

    // Логирование ip
    $sql->query('INSERT INTO `auth` set `user`="' . $user['id'] . '", `ip`="' . $uip . '", `date`="' . $start_point . '", `browser`="' . sys::hb64($_SERVER['HTTP_USER_AGENT']) . '"');

    // Запись сессии пользователя
    $_SESSION['user_id'] = $user['id'];

    // Выхлоп удачной авторизации
    sys::outjs(['s' => 'ok'], $nmch);
}

$html->get('auth', 'sections/user');
$html->pack('main');

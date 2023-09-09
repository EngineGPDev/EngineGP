<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

// Проверка на авторизацию
sys::auth();

sys::cookie('egp_login', 'quit', -1);
sys::cookie('egp_passwd', 'quit', -1);
sys::cookie('egp_authkeycheck', 'quit', -1);

// Генерация новой капчи
if (isset($url['captcha']))
    sys::captcha('auth', $uip);

// Авторизация
if ($go) {
    $nmch = 'go_auth_' . $uip;

    if ($mcache->get($nmch))
        sys::outjs(array('e' => sys::text('other', 'mcache')), $nmch);

    $mcache->set($nmch, 1, false, 15);

    // Проверка капчи
    if (!isset($_POST['captcha']) || sys::captcha_check('auth', $uip, $_POST['captcha']))
        sys::outjs(array('e' => sys::text('other', 'captcha')), $nmch);

    $aData = array();

    $aData['login'] = isset($_POST['login']) ? $_POST['login'] : '';
    $aData['passwd'] = isset($_POST['passwd']) ? sys::passwdkey($_POST['passwd']) : '';

    // Проверка входных данных
    foreach ($aData as $val)
        if ($val == '')
            sys::outjs(array('e' => sys::text('input', 'all')), $nmch);

    // Проверка логина/почты на валидность
    if (sys::valid($aData['login'], 'other', $aValid['mail']) and sys::valid($aData['login'], 'other', $aValid['login'])) {
        $out = 'login';

        // Если в логине указана почта
        if (sys::ismail($aData['login']))
            $out = 'mail';

        sys::outjs(array('e' => sys::text('input', $out . '_valid')), $nmch);
    }

    $sql_q = '`login`';

    // Если в логине указана почта
    if (sys::ismail($aData['login']))
        $sql_q = '`mail`';

    // Проверка существования пользователя
    $sql->query('SELECT `id`, `login`, `mail`, `security_ip`, `security_code` FROM `users` WHERE ' . $sql_q . '="' . $aData['login'] . '" AND `passwd`="' . $aData['passwd'] . '" LIMIT 1');
    if (!$sql->num())
        sys::outjs(array('e' => sys::text('input', 'auth')), $nmch);

    $user = $sql->get();

    $subnetwork = sys::whois($uip);

    // Если включена защита по ip
    if ($user['security_ip']) {
        $sql->query('SELECT `id` FROM `security` WHERE `user`="' . $user['id'] . '" AND `address`="' . $uip . '" LIMIT 1');

        if (!$sql->num()) {
            if ($subnetwork != 'не определена') {
                $sql->query('SELECT `id` FROM `security` WHERE `user`="' . $user['id'] . '" AND `address`="' . $subnetwork . '" LIMIT 1');

                if (!$sql->num())
                    sys::outjs(array('e' => 'Ваш ip адрес не найден в числе указаных адресов для авторизации.'), $nmch);
            } else
                sys::outjs(array('e' => 'Ваш ip адрес не найден в числе указаных адресов для авторизации.'), $nmch);
        }
    }

    // Если включена защита по коду
    if ($user['security_code']) {
        $code = isset($_POST['code']) ? $_POST['code'] : '';

        if ($code == '' || $code != $mcache->get('auth_code_security_' . $user['id'])) {
            $ncod = sys::code();

            // Отправка сообщения на почту
            if (sys::mail('Авторизация', sys::updtext(sys::text('mail', 'security_code'), array('site' => $cfg['name'], 'code' => $ncod)), $user['mail'])) {
                $mcache->set('auth_code_security_' . $user['id'], $ncod, false, 180);

                if ($code == '')
                    sys::outjs(array('i' => 'На вашу почту отправлено письмо с кодом подтверждения.', 'mail' => sys::mail_domain($user['mail'])), $nmch);

                sys::outjs(array('i' => 'На вашу почту отправлено письмо с кодом подтверждения снова.', 'mail' => sys::mail_domain($user['mail'])), $nmch);
            }

            // Выхлоп: не удалось отправить письмо
            sys::outjs(array('e' => sys::text('error', 'mail')), $nmch);
        }
    }

    $_SERVER['HTTP_USER_AGENT'] = mb_substr($_SERVER['HTTP_USER_AGENT'], 0, 200);

    // Обновление информации о пользователе
    $sql->query('UPDATE `users` set `ip`="' . $uip . '", `browser`="' . sys::browser($_SERVER['HTTP_USER_AGENT']) . '", `time`="' . $start_point . '" WHERE `id`="' . $user['id'] . '" LIMIT 1');

    // Логирование ip
    $sql->query('INSERT INTO `auth` set `user`="' . $user['id'] . '", `ip`="' . $uip . '", `date`="' . $start_point . '", `browser`="' . sys::hb64($_SERVER['HTTP_USER_AGENT']) . '"');

    // Запись cookie пользователю
    sys::cookie('egp_login', $user['login'], 14);
    sys::cookie('egp_passwd', $aData['passwd'], 14);
    sys::cookie('egp_authkeycheck', md5($user['login'] . $uip . $aData['passwd']), 14);

    // Выхлоп удачной авторизации
    sys::outjs(array('s' => 'ok'), $nmch);
}

$html->get('auth', 'sections/user');
$html->pack('main');

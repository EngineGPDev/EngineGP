<?php
/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @link      https://github.com/EngineGPDev/EngineGP
 * @link      https://gitforge.ru/EngineGP/EngineGP
 * @copyright Copyright (c) Solovev Sergei <inbox@seansolovev.ru>
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE
 * @license   https://gitforge.ru/EngineGP/EngineGP/src/branch/main/LICENSE
 */

if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

$text = array(
    'mail' => 'На указанную почту отправлено письмо с дальнейшей инструкцией.',
    'remail' => 'На указанную почту повторно отправлено письмо с подтверждением.',
    'signup' => 'Вы успешно зарегистрировались, на вашу почту отправлены логин и пароль для авторизации на сайте.',
    'oldmail' => 'На старую почту отправлено письмо с инструкцией.',

    'confirm_key_error' => 'Неправильный код подтверждения операции.',
    'confirm_mail_done' => 'Операция смены почты успешно выполнена.',
    'confirm_phone' => 'Ваш номер подтвержден, сменить его нельзя.',
    'confirm_phone_done' => 'Ваш номер уже подтвержден.',
    'confirm_phone_error' => 'Не удалось подтвердить номер.',

    'auth' => 'Вы уже авторизованы, обновите страницу.',
    'noauth' => 'Необходимо авторизоваться.',
);

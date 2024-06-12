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

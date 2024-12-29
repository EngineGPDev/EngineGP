<?php

/*
 * Copyright 2018-2024 Solovev Sergei
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

$text = [
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
];

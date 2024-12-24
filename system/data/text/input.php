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
    'all' => 'Необходимо заполнить все поля.',
    'login_valid' => 'Неправильный формат логина.',
    'mail_valid' => 'Неправильный формат почты.',
    'name_valid' => 'Неправильный формат имени.',
    'lastname_valid' => 'Неправильный формат фамилии.',
    'patronymic_valid' => 'Неправильный формат отчества.',
    'contacts_valid' => 'Неправильный формат контактов.<br> Пример: vk - <u>https://vk.com/enginegamespanel</u> или skype - <u>enginegamepanel</u>.',
    'passwd_valid' => 'Неправильный формат пароля, используйте латинские буквы и цифры от 4 до 20 символов.',
    'phone_valid' => 'Указанный номер имеет неверный формат, пример: RU - <u>79260010203</u>, KZ - <u>77058197322</u>.',
    'wmr_valid' => 'Указанный кошелек имеет неверный формат, пример: <u>R123456789100</u>.',

    'mail_use' => 'Почта занята другим пользователем.',
    'login_use' => 'Логин занят другим пользователем.',
    'phone_use' => 'Телефон занят другим пользователем.',
    'contacts_use' => 'Контакты заняты другим пользователем.',

    'auth' => 'Неправильный логин или пароль.',
    'recovery' => 'Пользователь не найден.',
    'word' => 'В сообщении присутствует длинное слово.',
    'similar' => 'Указанные данные совпадают с текущими.',
    'phone_confirm' => 'Изменение подтвержденного номера невозможно.',
    'wmr_confirm' => 'Изменение кошелька невозможно.',
    'phone' => 'Для подтверждения номера, необходимо его указать.',
    'code' => 'Указанный код неправильный.',
];

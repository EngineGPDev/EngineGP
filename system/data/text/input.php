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

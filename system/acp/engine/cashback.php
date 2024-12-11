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

use Symfony\Component\Dotenv\Dotenv;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

// Загружаем .env
$dotenv = new Dotenv();
$dotenv->load(ROOT . '/.env');

if ($_ENV['RUN_MODE'] === 'dev') {
    // Включение отображения ошибок в режиме разработки
    ini_set('display_errors', true);
    ini_set('html_errors', true);
    ini_set('error_reporting', E_ALL);
} else {
    // Отключение отображения ошибок в продакшене
    ini_set('display_errors', false);
    ini_set('html_errors', false);
    ini_set('error_reporting', 0);
}

$nmc = 'cashback_' . $id;

// Проверка сессии
if ($mcache->get($nmc)) {
    sys::outjs(['e' => $text['mcache']], $nmc);
}

// Создание сессии
$mcache->set($nmc, 1, false, 10);

if ($id) {
    $sql->query('SELECT `user`, `money`, `purse`, `status` FROM `cashback` WHERE `id`="' . $id . '" LIMIT 1');
    $cb = $sql->get();

    if (!$cb['status']) {
        sys::outjs(['e' => 'Данная заявка уже была обработана'], $nmc);
    }

    $purse = $cb['purse'][0] == 'R' ? 'webmoney' : 'qiwi';

    // Запрос на шлюз
    if ($cfg['part_gateway'] == 'unitpay') {
        $sum = $cb['money'] - ($cb['money'] / 100 * $cfg['part_output_proc']);

        $json = file_get_contents('https://unitpay.ru/api?method=massPayment&params[sum]=' . $sum . '&params[purse]=' . $cb['purse'] . '&params[login]=' . $cfg['unitpay_mail'] . '&params[transactionId]=' . $id . ' &params[secretKey]=' . $cfg['unitpay_api'] . '&params[paymentType]=' . $purse);

        $array = json_decode($json, true);

        // Упешный вывод средств
        if (is_array($array) and isset($array['result']) and in_array($array['result']['status'], ['success', 'not_completed '])) {
            $sql->query('UPDATE `cashback` set `status`="0" WHERE `id`="' . $id . '" LIMIT 1');
            $sql->query('INSERT INTO `logs` set `user`="' . $cb['user'] . '", `text`="' . sys::updtext(
                sys::text('logs', 'cashback'),
                ['purse' => $purse, 'money' => $cb['money']]
            ) . '", `date`="' . $start_point . '", `type`="cashback", `money`="' . $cb['money'] . '"');

            sys::outjs(['s' => 'Запрос на вывод средств был успешно выполнен'], $nmc);
        }

        if (!is_array($array)) {
            sys::outjs(['e' => 'Неудалось выполнить запрос'], $nmc);
        }

        sys::outjs(['e' => $array['error']['message']], $nmc);
    }

    $sql->query('UPDATE `cashback` set `status`="0" WHERE `id`="' . $id . '" LIMIT 1');
    $sql->query('INSERT INTO `logs` set `user`="' . $cb['user'] . '", `text`="' . sys::updtext(
        sys::text('logs', 'cashback'),
        ['purse' => $purse, 'money' => $cb['money']]
    ) . '", `date`="' . $start_point . '", `type`="cashback", `money`="' . $cb['money'] . '"');

    sys::outjs(['s' => 'Запрос на вывод средств был успешно выполнен в ручном режиме'], $nmc);
}

sys::outjs(['e' => 'Не передан идентификатор заявки'], $nmc);

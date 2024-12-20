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
sys::noauth();

if (isset($url['key']) && isset($url['sum'])) {
    sys::out(md5($cfg['freekassa_id'] . ':' . intval($url['sum']) . ':' . $cfg['freekassa_key_1'] . ':1'));
}

// Генерация списка операций
$qLog = $sql->query('SELECT `text`, `date` FROM `logs` WHERE `user`="' . $user['id'] . '" AND `type`="replenish" ORDER BY `id` DESC LIMIT 10');
while ($aLog = $sql->get($qLog)) {
    $html->get('replenish', 'sections/user/lk/logs');
    $html->set('text', $aLog['text']);
    $html->set('date', sys::today($aLog['date'], true));
    $html->pack('logs');
}

$html->get('replenish', 'sections/user');
$html->set('id', $user['id']);
$html->set('wmr', $cfg['webmoney_wmr']);
$html->set('freekassa', $cfg['freekassa_id']);
$html->set('balance', round($user['balance'], 2));
$html->set('cur', $cfg['currency']);
$html->set('logs', $html->arr['logs'] ?? 'Нет логов операций', true);
$html->pack('main');

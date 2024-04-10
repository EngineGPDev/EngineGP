<?php
/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @copyright Copyright (c) 2018-present Solovev Sergei <inbox@seansolovev.ru>
 *
 * @link      https://github.com/EngineGPDev/EngineGP for the canonical source repository
 * @link      https://gitforge.ru/EngineGP/EngineGP for the canonical source repository
 *
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE MIT License
 * @license   https://gitforge.ru/EngineGP/EngineGP/src/branch/main/LICENSE MIT License
 */

if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

// Генерация списка операций
$qLog = $sql->query('SELECT `text`, `date` FROM `logs` WHERE `user`="' . $user['id'] . '" ORDER BY `id` DESC LIMIT 40');
while ($aLog = $sql->get($qLog)) {
    $html->get('list', 'sections/user/lk/logs');
    $html->set('text', $aLog['text']);
    $html->set('date', sys::today($aLog['date'], true));
    $html->pack('logs');
}

$html->get('logs', 'sections/user/lk');

$html->set('logs', isset($html->arr['logs']) ? $html->arr['logs'] : 'Нет логов операций', true);

$html->pack('main');

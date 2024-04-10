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

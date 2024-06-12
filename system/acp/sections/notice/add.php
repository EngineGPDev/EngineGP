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

if ($go) {
    $aData = array();

    $aData['text'] = isset($_POST['text']) ? trim($_POST['text']) : '';
    $aData['color'] = isset($_POST['color']) ? trim($_POST['color']) : '';
    $aData['type'] = isset($_POST['type']) ? trim($_POST['type']) : '';
    $aData['unit'] = isset($_POST['unit']) ? sys::int($_POST['unit']) : '';
    $aData['server'] = isset($_POST['server']) ? sys::int($_POST['server']) : '';
    $aData['time'] = isset($_POST['time']) ? trim($_POST['time']) : '';

    $aData['time'] = sys::checkdate($aData['time']);

    if ($aData['type'] == 'unit') {
        $sql->query('SELECT `id` FROM `units` WHERE `id`="' . $aData['unit'] . '" LIMIT 1');
        if (!$sql->num())
            sys::outjs(array('e' => 'Указанная локация не найдена'));

        $aData['server'] = 0;
    } elseif ($aData['type'] == 'server') {
        $sql->query('SELECT `id` FROM `servers` WHERE `id`="' . $aData['server'] . '" LIMIT 1');
        if (!$sql->num())
            sys::outjs(array('e' => 'Указанный сервер не найден'));

        $aData['unit'] = 0;
    } else
        sys::outjs(array('e' => 'Выберите получателя уведомления'));

    $sql->query('INSERT INTO `notice` set '
        . '`unit`="' . $aData['unit'] . '",'
        . '`server`="' . $aData['server'] . '",'
        . '`text`="' . htmlspecialchars($aData['text']) . '",'
        . '`color`="' . $aData['color'] . '",'
        . '`time`="' . $aData['time'] . '"');

    sys::outjs(array('s' => 'ok'));
}

$units = '';

$sql->query('SELECT `id`, `name` FROM `units` ORDER BY `id` ASC');
while ($unit = $sql->get())
    $units .= '<option value="' . $unit['id'] . '">' . $unit['name'] . '</option>';

$html->get('add', 'sections/notice');

$html->set('units', $units);

$html->pack('main');

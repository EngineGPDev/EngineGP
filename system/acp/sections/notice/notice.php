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

$sql->query('SELECT * FROM `notice` WHERE `id`="' . $id . '" LIMIT 1');
$notice = $sql->get();

if ($go) {
    $aData = [];

    $aData['text'] = isset($_POST['text']) ? trim($_POST['text']) : htmlspecialchars_decode($notice['text']);
    $aData['color'] = isset($_POST['color']) ? trim($_POST['color']) : $notice['color'];
    $aData['type'] = isset($_POST['type']) ? trim($_POST['type']) : $notice['type'];
    $aData['unit'] = isset($_POST['unit']) ? sys::int($_POST['unit']) : $notice['unit'];
    $aData['server'] = isset($_POST['server']) ? sys::int($_POST['server']) : $notice['server'];
    $aData['time'] = isset($_POST['time']) ? trim($_POST['time']) : '';

    $aData['time'] = sys::checkdate($aData['time']);

    if ($aData['type'] == 'unit') {
        $sql->query('SELECT `id` FROM `units` WHERE `id`="' . $aData['unit'] . '" LIMIT 1');
        if (!$sql->num()) {
            sys::outjs(['e' => 'Указанная локация не найдена']);
        }

        $aData['server'] = 0;
    } elseif ($aData['type'] == 'server') {
        $sql->query('SELECT `id` FROM `servers` WHERE `id`="' . $aData['server'] . '" LIMIT 1');
        if (!$sql->num()) {
            sys::outjs(['e' => 'Указанный сервер не найден']);
        }

        $aData['unit'] = 0;
    } else {
        sys::outjs(['e' => 'Выберите получателя уведомления']);
    }

    $sql->query('UPDATE `notice` set '
        . '`unit`="' . $aData['unit'] . '",'
        . '`server`="' . $aData['server'] . '",'
        . '`text`="' . htmlspecialchars($aData['text']) . '",'
        . '`color`="' . $aData['color'] . '",'
        . '`time`="' . $aData['time'] . '" WHERE `id`="' . $id . '" LIMIT 1');

    sys::outjs(['s' => 'ok']);
}

$units = '';

$sql->query('SELECT `id`, `name` FROM `units` ORDER BY `id` ASC');
while ($unit = $sql->get()) {
    $units .= '<option value="' . $unit['id'] . '">' . $unit['name'] . '</option>';
}

$html->get('notice', 'sections/notice');

$html->set('id', $notice['id']);
$html->set('text', htmlspecialchars_decode($notice['text']));
$html->set('time', date('d/m/Y H:i', $notice['time']));

if ($notice['unit']) {
    $html->set('type', '<option value="unit">Всем на локации</option><option value="server">Определенному серверу</option>');
    $html->set('units', str_replace('"' . $notice['unit'] . '"', '"' . $notice['unit'] . '" selected', $units));
    $html->set('server', '');

    $html->unit('unit');
    $html->unit('server', true);
} else {
    $html->set('type', '<option value="server">Определенному серверу</option><option value="unit">Всем на локации</option>');
    $html->set('units', $units);
    $html->set('server', $notice['server']);

    $html->unit('unit', true);
    $html->unit('server');
}

$html->set('colors', str_replace('"' . $notice['color'] . '"', '"' . $notice['color'] . '" selected', '<option value="red">Красный</option><option value="green">Зеленый</option><option value="blue">Синий</option>'));

$html->pack('main');

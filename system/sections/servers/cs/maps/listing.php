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

$html->nav('Списки карт');

$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
$unit = $sql->get();

if (!isset($ssh)) {
    include(LIB . 'ssh.php');
}

if (!$ssh->auth($unit['passwd'], $unit['address'])) {
    if ($go) {
        sys::outjs(['e' => sys::text('error', 'ssh')], $nmch);
    }

    sys::back($cfg['http'] . 'servers/id/' . $id . '/section/maps');
}

$sql->query('SELECT `install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
$tarif = $sql->get();

// Директория сервера
$dir = $tarif['install'] . $server['uid'] . '/cstrike/';

// Генерация списка
if ($go and isset($url['gen'])) {
    $ssh->set('cd ' . $dir . 'maps/ && ls | grep -e "\.bsp$"');

    $maps = $ssh->get();

    $aMaps = explode("\n", str_ireplace(['./', '.bsp'], '', $maps));

    sort($aMaps);
    reset($aMaps);

    $list = '';

    foreach ($aMaps as $index => $map) {
        $aMap = explode('/', $map);
        $name = end($aMap);
        if (strlen($name) < 4) {
            continue;
        }

        $list .= $map . "\n";
    }

    sys::outjs(['s' => $list], $nmch);
}

$aFiles = [
    'mapcycle' => 'mapcycle.txt',
    'maps' => 'addons/amxmodx/configs/maps.ini',
];

// Сохранение
if ($go and isset($url['file'])) {
    if (!array_key_exists($url['file'], $aFiles)) {
        exit;
    }

    $data = $_POST['data'] ?? '';

    $temp = sys::temp($data);

    // Отправление файла на сервер
    $ssh->setfile($temp, $dir . $aFiles[$url['file']]);
    $ssh->set('chmod 0644' . ' ' . $dir . $aFiles[$url['file']]);

    // Смена владельца/группы файла
    $ssh->set('chown server' . $server['uid'] . ':servers ' . $dir . $aFiles[$url['file']]);

    unlink($temp);

    sys::outjs(['s' => 'ok'], $nmch);
}

$ssh->set('sudo -u server' . $server['uid'] . ' sh -c "touch ' . $dir . $aFiles['mapcycle'] . '; cat ' . $dir . $aFiles['mapcycle'] . '"');
$mapcycle = $ssh->get();

$ssh->set('sudo -u server' . $server['uid'] . ' sh -c "touch ' . $dir . $aFiles['maps'] . '; cat ' . $dir . $aFiles['maps'] . '"');
$maps = $ssh->get();

$html->get('listing', 'sections/servers/' . $server['game'] . '/maps');

$html->set('id', $id);

$html->set('mapcycle', $mapcycle);
$html->set('maps', $maps);

$html->pack('main');

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
if (!$go) {
    exit;
}

$sql->query('SELECT `unit`, `tarif`, `map_start` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
$server = array_merge($server, $sql->get());

$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
$unit = $sql->get();

if (!isset($ssh)) {
    include(LIB . 'ssh.php');
}

if (!$ssh->auth($unit['passwd'], $unit['address'])) {
    sys::outjs(['e' => sys::text('error', 'ssh')], $nmch);
}

$sql->query('SELECT `install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
$tarif = $sql->get();

$dir = $tarif['install'] . $server['uid'] . '/game/csgo/';

// Генерация списка карт
$ssh->set('cd ' . $dir . 'maps/ && ls | grep -iE "\.vpk$"');

$maps = $ssh->get();

$aMaps = explode("\n", str_ireplace('.vpk', '', $maps));

// Массив переданных карт
$in_aMaps = $_POST['maps'] ?? [];

// Обработка выборки
foreach ($in_aMaps as $name => $sel) {
    if ($sel) {
        $map = str_replace(["\\", "'", "'", '-_-'], ['', '', '', '$'], $name);

        // Проверка наличия карты
        if (!in_array($map, $aMaps)) {
            continue;
        }

        // Проверка: является ли карта стартовой
        if ($server['map_start'] == $map) {
            continue;
        }

        $ssh->set('cd /path/maps/' . $server['game'] . '/' . sys::map($map) . ' && du -a | grep -iE "\.[a-z]{1,3}$" | awk \'{print $2}\'');

        $aFiles = explode("\n", str_replace('./', '', $ssh->get()));

        if (isset($aFiles[count($aFiles) - 1]) and $aFiles[count($aFiles) - 1] == '') {
            unset($aFiles[count($aFiles) - 1]);
        }

        $files = '';

        foreach ($aFiles as $file) {
            $files .= $dir . $file . ' ';
        }

        $rm = '';
        $aFlrm = explode(' ', $dir . 'maps/' . $map . '.* ' . trim($files));

        foreach ($aFlrm as $flrm) {
            $rm .= sys::map($flrm) . ' ';
        }

        $ssh->set('sudo -u server' . $server['uid'] . ' tmux new-session -ds md' . $start_point . $id . ' sh -c \'rm ' . trim($rm) . '\'');
    }
}

sys::outjs(['s' => 'ok'], $nmch);

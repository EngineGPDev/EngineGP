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

if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

// Редактируемый файл
$file = isset($url['file']) ? $url['file'] : sys::back($cfg['http'] . 'servers/id/' . $id . '/section/settings');

include(DATA . 'filedits.php');

// Проверка наличия в конфиге
if (!in_array($file, $aEdits[$server['game']]['all']['files']) && !in_array($file, $aEdits[$server['game']][$tarif['name']]['files']))
    sys::back($cfg['http'] . 'servers/id/' . $id . '/section/settings');

$html->nav('Редактирование файла: ' . $file);

$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
$unit = $sql->get();

$sql->query('SELECT `install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
$tarif = $sql->get();

include(LIB . 'ssh.php');

if (!$ssh->auth($unit['passwd'], $unit['address'])) {
    if ($go)
        sys::outjs(array('e' => sys::text('error', 'ssh')), $nmch);

    sys::back($cfg['http'] . 'servers/id/' . $id . '/section/settings');
}

// Полный путь файла
$path = $tarif['install'] . $server['uid'] . '/' . $aEdits[$server['game']]['all']['path'][$file] . $file;
if ($go) {
    $data = isset($_POST['data']) ? $_POST['data'] : '';

    $temp = sys::temp($data);

    // Отправление файла на сервер
    $ssh->setfile($temp, $path, 0644);

    // Смена владельца/группы файла
    $ssh->set('chown server' . $server['uid'] . ':servers ' . $path);

    unlink($temp);

    sys::outjs(array('s' => 'ok'), $nmch);
}

$ssh->set('sudo -u server' . $server['uid'] . ' sh -c "touch ' . $path . '; cat ' . $path . '"');

$html->get('file', 'sections/servers/games/settings');

$html->set('id', $id);
$html->set('file', $file);
$html->set('data', htmlspecialchars($ssh->get()));

$html->pack('main');

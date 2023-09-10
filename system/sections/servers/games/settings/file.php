<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

// Редактируемый файл
$file = $url['file'] ?? sys::back($cfg['http'] . 'servers/id/' . $id . '/section/settings');

require(DATA . 'filedits.php');

// Проверка наличия в конфиге
if (!in_array($file, $aEdits[$server['game']]['all']['files']) && !in_array($file, $aEdits[$server['game']][$tarif['name']]['files']))
    sys::back($cfg['http'] . 'servers/id/' . $id . '/section/settings');

$html->nav('Редактирование файла: ' . $file);

$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
$unit = $sql->get();

$sql->query('SELECT `install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
$tarif = $sql->get();

require(LIB . 'ssh.php');

if (!$ssh->auth($unit['passwd'], $unit['address'])) {
    if ($go)
        sys::outjs(['e' => sys::text('error', 'ssh')], $nmch);

    sys::back($cfg['http'] . 'servers/id/' . $id . '/section/settings');
}

// Полный путь файла
$path = $tarif['install'] . $server['uid'] . '/' . $aEdits[$server['game']]['all']['path'][$file] . $file;
if ($go) {
    $data = $_POST['data'] ?? '';

    $temp = sys::temp($data);

    // Отправление файла на сервер
    $ssh->setfile($temp, $path, 0644);

    // Смена владельца/группы файла
    $ssh->set('chown server' . $server['uid'] . ':servers ' . $path);

    unlink($temp);

    sys::outjs(['s' => 'ok'], $nmch);
}

$ssh->set('sudo -u server' . $server['uid'] . ' sh -c "touch ' . $path . '; cat ' . $path . '"');

$html->get('file', 'sections/servers/games/settings');

$html->set('id', $id);
$html->set('file', $file);
$html->set('data', htmlspecialchars((string) $ssh->get()));

$html->pack('main');

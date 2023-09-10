<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

$sql->query('SELECT * FROM `units` WHERE `id`="' . $id . '" LIMIT 1');
$unit = $sql->get();

if ($go) {
    $aData = [];

    $aData['name'] = isset($_POST['name']) ? trim((string) $_POST['name']) : $unit['name'];
    $aData['address'] = isset($_POST['address']) ? trim((string) $_POST['address']) : $unit['address'];
    $aData['passwd'] = isset($_POST['passwd']) ? trim((string) $_POST['passwd']) : $unit['passwd'];
    $aData['sql_login'] = isset($_POST['sql_login']) ? trim((string) $_POST['sql_login']) : $unit['sql_login'];
    $aData['sql_passwd'] = isset($_POST['sql_passwd']) ? trim((string) $_POST['sql_passwd']) : $unit['sql_passwd'];
    $aData['sql_port'] = isset($_POST['sql_port']) ? sys::int($_POST['sql_port']) : $unit['sql_port'];
    $aData['sql_ftp'] = isset($_POST['sql_ftp']) ? trim((string) $_POST['sql_ftp']) : $unit['sql_ftp'];
    $aData['cs'] = $_POST['cs'] ?? $unit['cs'];
    $aData['cssold'] = $_POST['cssold'] ?? $unit['cssold'];
    $aData['css'] = $_POST['css'] ?? $unit['css'];
    $aData['csgo'] = $_POST['csgo'] ?? $unit['csgo'];
    $aData['samp'] = $_POST['samp'] ?? $unit['samp'];
    $aData['crmp'] = $_POST['crmp'] ?? $unit['crmp'];
    $aData['mta'] = $_POST['mta'] ?? $unit['mta'];
    $aData['mc'] = $_POST['mc'] ?? $unit['mc'];
    $aData['ram'] = isset($_POST['ram']) ? sys::int($_POST['ram']) : $unit['ram'];
    $aData['test'] = isset($_POST['test']) ? sys::int($_POST['test']) : $unit['test'];
    $aData['show'] = $_POST['show'] ?? $unit['show'];
    $aData['sort'] = isset($_POST['sort']) ? sys::int($_POST['sort']) : $unit['sort'];
    $aData['domain'] = isset($_POST['domain']) ? trim((string) $_POST['domain']) : $unit['domain'];

    foreach (['cs', 'cssold', 'css', 'csgo', 'samp', 'crmp', 'mta', 'mc'] as $game)
        $aData[$game] = (string)$aData[$game] == 'on' ? '1' : '0';

    if (in_array('', $aData))
        sys::outjs(['e' => 'Необходимо заполнить все поля']);

    require(LIB . 'ssh.php');

    if (!$ssh->auth($aData['passwd'], $aData['address']))
        sys::outjs(['e' => 'Не удалось создать связь с локацией']);

    $sql->query('UPDATE `units` set '
        . '`name`="' . htmlspecialchars((string) $aData['name']) . '",'
        . '`address`="' . $aData['address'] . '",'
        . '`passwd`="' . $aData['passwd'] . '",'
        . '`sql_login`="' . $aData['sql_login'] . '",'
        . '`sql_passwd`="' . $aData['sql_passwd'] . '",'
        . '`sql_port`="' . $aData['sql_port'] . '",'
        . '`sql_ftp`="' . $aData['sql_ftp'] . '",'
        . '`cs`="' . $aData['cs'] . '",'
        . '`cssold`="' . $aData['cssold'] . '",'
        . '`css`="' . $aData['css'] . '",'
        . '`csgo`="' . $aData['csgo'] . '",'
        . '`samp`="' . $aData['samp'] . '",'
        . '`crmp`="' . $aData['crmp'] . '",'
        . '`mta`="' . $aData['mta'] . '",'
        . '`mc`="' . $aData['mc'] . '",'
        . '`ram`="' . $aData['ram'] . '",'
        . '`test`="' . $aData['test'] . '",'
        . '`show`="' . $aData['show'] . '",'
        . '`sort`="' . $aData['sort'] . '",'
        . '`domain`="' . $aData['domain'] . '" WHERE `id`="' . $id . '" LIMIT 1');

    sys::outjs(['s' => $id]);
}

$html->get('unit', 'sections/units');

foreach ($unit as $i => $val)
    $html->set($i, $val);

foreach (['cs', 'cssold', 'css', 'csgo', 'samp', 'crmp', 'mta', 'mc'] as $game) {
    if ($unit[$game])
        $html->unit('game_' . $game, 1);
    else
        $html->unit('game_' . $game);
}

$html->set('show', $unit['show'] == 1 ? '<option value="1">Доступна</option><option value="0">Недоступна</option>' : '<option value="0">Недоступна</option><option value="1">Доступна</option>');

$html->pack('main');

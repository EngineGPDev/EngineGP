<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

$html->nav('Управление администраторами');

if ($go) {
    $sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
    $unit = $sql->get();

    $sql->query('SELECT `install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
    $tarif = $sql->get();

    require(LIB . 'ssh.php');

    if (!$ssh->auth($unit['passwd'], $unit['address']))
        sys::outjs(['e' => sys::text('error', 'ssh')], $nmch);

    $aData = [];

    $aData['active'] = $_POST['active'] ?? '';
    $aData['value'] = $_POST['value'] ?? '';
    $aData['passwd'] = $_POST['passwd'] ?? '';
    $aData['flags'] = $_POST['flags'] ?? '';
    $aData['type'] = $_POST['type'] ?? '';
    $aData['time'] = $_POST['time'] ?? '';
    $aData['info'] = $_POST['info'] ?? '';

    // Удаление текущих записей
    $sql->query('DELETE FROM `admins_' . $server['game'] . '` WHERE `server`="' . $id . '"');

    $usini = '';

    foreach ($aData['value'] as $index => $val) {
        if ($val != '') {
            $type = $aData['type'][$index] ?? 'a';
            if (!in_array($type, ['c', 'ce', 'de', 'a']))
                $type = 'a';

            $aDate = isset($aData['time'][$index]) ? explode('.', (string) $aData['time'][$index]) : explode('.', date('d.m.Y', $start_point));

            if (!isset($aDate[1], $aDate[0], $aDate[2]) || !checkdate($aDate[1], $aDate[0], $aDate[2]))
                $aDate = explode('.', date('d.m.Y', $start_point));

            $time = mktime(0, 0, 0, $aDate[1], $aDate[0], $aDate[2]);

            $aData['active'][$index] = isset($aData['active'][$index]) ? 1 : 0;
            $aData['passwd'][$index] ??= '';
            $aData['flags'][$index] ??= '';
            $aData['info'][$index] ??= '';

            $text = '"' . $val . '" "' . $aData['passwd'][$index] . '" "' . $aData['flags'][$index] . '" "' . $type . '"';

            $sql->query('INSERT INTO `admins_' . $server['game'] . '` set'
                . '`server`="' . $id . '",'
                . '`value`="' . htmlspecialchars((string) $val) . '",'
                . '`active`="' . $aData['active'][$index] . '",'
                . '`passwd`="' . htmlspecialchars((string) $aData['passwd'][$index]) . '",'
                . '`flags`="' . htmlspecialchars((string) $aData['flags'][$index]) . '",'
                . '`type`="' . $type . '",'
                . '`time`="' . $time . '",'
                . '`text`="' . htmlspecialchars($text) . '",'
                . '`info`="' . htmlspecialchars((string) $aData['info'][$index]) . '"');

            if ($aData['active'][$index])
                $usini .= $text . PHP_EOL;
        }
    }

    $temp = sys::temp($usini);

    $ssh->setfile($temp, $tarif['install'] . $server['uid'] . '/cstrike/addons/amxmodx/configs/users.ini', 0644);

    unlink($temp);

    $ssh->set('chown server' . $server['uid'] . ':servers ' . $tarif['install'] . $server['uid'] . '/cstrike/addons/amxmodx/configs/users.ini');

    $ssh->set("sudo -u server" . $server['uid'] . " screen -p 0 -S s_" . $server['uid'] . " -X eval 'stuff \"amx_reloadadmins\"\015'");

    sys::outjs(['s' => 'ok'], $nmch);
}

// Построение списка добавленных админов
$sql->query('SELECT `id`, `value`, `active`, `passwd`, `flags`, `type`, `time`, `info` FROM `admins_' . $server['game'] . '` WHERE `server`="' . $id . '" ORDER BY `id` ASC');
while ($admin = $sql->get()) {
    $type = match ($admin['type']) {
        'c' => '<option value="c">SteamID/Пароль</option><option value="a">Ник/Пароль</option><option value="ce">SteamID</option><option value="de">IP Адрес</option>',
        'ce' => '<option value="ce">SteamID</option><option value="a">Ник/Пароль</option><option value="c">SteamID/Пароль</option><option value="de">IP Адрес</option>',
        'de' => '<option value="de">IP Адрес</option><option value="a">Ник/Пароль</option><option value="c">SteamID/Пароль</option><option value="ce">SteamID</option>',
        default => '<option value="a">Ник/Пароль</option><option value="c">SteamID/Пароль</option><option value="ce">SteamID</option><option value="de">IP Адрес</option>',
    };

    $html->get('list', 'sections/servers/' . $server['game'] . '/settings/admins');

    if ($admin['active'])
        $html->unit('active', 1);
    else
        $html->unit('active');

    $html->set('id', $admin['id']);
    $html->set('value', $admin['value']);
    $html->set('passwd', $admin['passwd']);
    $html->set('flags', $admin['flags']);
    $html->set('type', $type);
    $html->set('time', date('d.m.y', $admin['time']));
    $html->set('info', $admin['info']);

    $html->pack('admins');
}

$sql->query('SELECT `id` FROM `admins_' . $server['game'] . '` WHERE `server`="' . $id . '" ORDER BY `id` DESC LIMIT 1');
$max = $sql->get();

[$ip, $port] = explode(':', (string) $server['address']);

$html->get('admins', 'sections/servers/' . $server['game'] . '/settings');

$html->set('id', $id);
$html->set('admins', $html->arr['admins'] ?? '');
$html->set('index', $max['id'] < 1 ? 0 : $max['id']);
$html->set('address', 'ip/' . $ip . '/port/' . $port);

$sql->query('SELECT `active` FROM `privileges` WHERE `server`="' . $id . '" LIMIT 1');
if ($sql->num()) {
    $privilege = $sql->get();

    if ($privilege['active'])
        $html->unit('privileges', 1);
    else
        $html->unit('privileges');
} else
    $html->unit('privileges');

$html->pack('main');

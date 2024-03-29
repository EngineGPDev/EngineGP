<?php
if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

$html->nav('Логи');

$sql->query('SELECT `address`, `passwd` FROM `control` WHERE `id`="' . $id . '" LIMIT 1');
$unit = $sql->get();

include(LIB . 'ssh.php');

if (!$ssh->auth($unit['passwd'], $unit['address']))
    sys::back($cfg['http'] . 'control/id/' . $id . '/server/' . $sid . '/section/settings');

// Путь к логам
$folder = '/servers/' . $server['uid'] . '/cstrike/logs';

// Если выбран лог
if (isset($url['log'])) {
    if (sys::valid($url['log'], 'other', $aValid['cslogs']))
        sys::back($cfg['http'] . 'control/id/' . $id . '/server/' . $sid . '/section/settings/subsection/logs');

    $ssh->set('sudo -u server' . $server['uid'] . ' cat ' . $folder . '/' . $url['log']);

    $html->get('view', 'sections/control/servers/games/settings/logs');

    $html->set('id', $id);
    $html->set('server', $sid);
    $html->set('name', $url['log']);
    $html->set('log', htmlspecialchars($ssh->get(), NULL, ''));
    $html->set('uri', 'logs');

    $html->pack('main');
} else {
    if (isset($url['delall'])) {
        $ssh->set('cd ' . $folder . ' && rm *.log');

        sys::back($cfg['http'] . 'control/id/' . $id . '/server/' . $sid . '/section/settings/subsection/logs');
    }

    $ssh->set('cd ' . $folder . ' && du -ab --time | grep -e .log$ | awk \'{print $2" "$3"@"$1"@"$4}\' | sort -Mr');

    // Массив данных
    $aData = explode("\n", $ssh->get());

    if (isset($aData[count($aData) - 1]))
        unset($aData[count($aData) - 1]);

    // Построение списка
    foreach ($aData as $line => $log) {
        $aLog = explode('@', $log);

        // Название
        $name = explode('/', $aLog[2]);

        if (count($name) > 2)
            continue;

        // Дата
        $date = sys::unidate($aLog[0]);

        // Вес
        $size = sys::size($aLog[1]);

        $html->get('list', 'sections/control/servers/games/settings/logs');

        $html->set('id', $id);
        $html->set('server', $sid);
        $html->set('name', end($name));
        $html->set('uri', 'logs/log/' . end($name));
        $html->set('date', $date);
        $html->set('size', $size);

        $html->pack('logs');
    }

    $html->get('logs', 'sections/control/servers/games/settings');

    $html->set('id', $id);
    $html->set('server', $sid);
    $html->set('uri', '');
    $html->set('logs', isset($html->arr['logs']) ? $html->arr['logs'] : '');

    $html->pack('main');
}
?>
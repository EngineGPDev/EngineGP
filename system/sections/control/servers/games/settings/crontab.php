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

$html->nav('Планировщик задач');

if ($go) {
    $sql->query('SELECT `address`, `passwd` FROM `panel` LIMIT 1');
    $panel = $sql->get();

    include(LIB . 'ssh.php');

    if (!$ssh->auth($panel['passwd'], $panel['address'])) {
        sys::outjs(['e' => sys::text('error', 'ssh')], $nmch);
    }

    // Удаление задания
    if (isset($url['action']) and $url['action'] == 'delete') {
        $task = isset($_POST['task']) ? sys::int($_POST['task']) : sys::outjs(['s' => 'ok'], $nmch);

        $sql->query('SELECT `cron` FROM `control_crontab` WHERE `id`="' . $task . '" AND `server`="' . $sid . '" LIMIT 1');
        if (!$sql->num()) {
            sys::outjs(['s' => 'ok'], $nmch);
        }

        $cron = $sql->get();

        $crontab = preg_quote($cron['cron'], '/');

        $ssh->set('crontab -l | grep -v "' . $crontab . '" | crontab -');

        $sql->query('DELETE FROM `control_crontab` WHERE `id`="' . $task . '" LIMIT 1');

        sys::outjs(['s' => 'ok'], $nmch);
    }

    // Добавление задания
    $sql->query('SELECT `id` FROM `control_crontab` WHERE `server`="' . $sid . '" LIMIT 5');
    if ($sql->num() == $cfg['crontabs']) {
        sys::outjs(['e' => sys::text('servers', 'crontab')], $nmch);
    }

    $data = [];

    $data['task'] = $_POST['task'] ?? 'start';

    $task = in_array($server['game'], ['samp', 'crmp']) ? ['start', 'restart', 'stop'] : ['start', 'restart', 'stop', 'console'];

    if (!in_array($data['task'], $task)) {
        $data['task'] = 'start';
    }

    $data['commands'] = isset($_POST['commands']) ? base64_encode(htmlspecialchars($_POST['commands'])) : '';
    $data['allhour'] = isset($_POST['allhour']) ? true : false;
    $data['hour'] = $_POST['hour'] ?? '00';
    $data['minute'] = $_POST['minute'] ?? '00';
    $data['week'] = (isset($_POST['week']) and is_array($_POST['week'])) ? $_POST['week'] : [];

    $sql->query('INSERT INTO `control_crontab` set `server`="' . $sid . '"');
    $cid = $sql->id();

    include(LIB . 'games/games.php');

    $cron_rule = ctrl::crontab($sid, $cid, $data);

    $ssh->set('(crontab -l; echo "' . $cron_rule . '") | crontab -');

    $time = games::crontab_time($data['allhour'], $data['hour'], $data['minute']);
    $week = games::crontab_week($data['week']);

    $sql->query('UPDATE `control_crontab` set `server`="' . $sid . '", `task`="' . $data['task'] . '", `cron`="' . $cron_rule . '", `week`="' . $week . '", `time`="' . $time . '", `commands`="' . $data['commands'] . '" WHERE `id`="' . $cid . '" LIMIT 1');

    sys::outjs(['s' => 'ok'], $nmch);
}

$aTask = [
    'start' => 'Включение сервера',
    'stop' => 'Выключение сервера',
    'restart' => 'Перезагрузка сервера',
    'console' => 'Отправка команд на сервер',
];

$sql->query('SELECT `id`, `task`, `week`, `time` FROM `control_crontab` WHERE `server`="' . $sid . '" ORDER BY `id` ASC');
while ($crontab = $sql->get()) {
    $html->get('crontab_list', 'sections/control/servers/games/settings');
    $html->set('id', $crontab['id']);
    $html->set('task', $aTask[$crontab['task']]);
    $html->set('week', $crontab['week']);
    $html->set('time', $crontab['time']);
    $html->pack('crontab');
}

$html->get('crontab', 'sections/control/servers/' . $server['game'] . '/settings');
$html->set('id', $id);
$html->set('server', $sid);
$html->set('time', date('H:i:s', $start_point));
$html->set('crontab', $html->arr['crontab'] ?? '');
$html->pack('main');

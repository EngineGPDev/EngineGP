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

$sql->query('SELECT `uid`, `unit`, `tarif`, `time_start` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
$server = array_merge($server, $sql->get());

if ($go) {
    $sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
    $unit = $sql->get();

    $sql->query('SELECT `install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
    $tarif = $sql->get();

    include(LIB . 'ssh.php');

    $command = isset($_POST['command']) ? sys::cmd($_POST['command']) : '';

    if (isset($server['status']) && $server['status'] == 'off') {
        if ($command) {
            sys::outjs(array('e' => sys::text('servers', 'off')));
        }

        sys::out(sys::text('servers', 'off'));
    }

    if (!$ssh->auth($unit['passwd'], $unit['address'])) {
        if ($command) {
            sys::outjs(array('e' => sys::text('error', 'ssh')));
        }

        sys::out(sys::text('error', 'ssh'));
    }

    $dir = $tarif['install'] . $server['uid'] . '/cstrike/';

    $filecmd = $dir . 'egpconsole.log';

    if ($command) {
        // Команда для отправки команды в screen
        $ssh->set('sudo -u server' . $server['uid'] . ' screen -p 0 -S s_' . $server['uid'] . ' -X eval \'stuff "' . $command . '"\015\';'
            . 'sudo -u server' . $server['uid'] . ' screen -p 0 -S s_' . $server['uid'] . ' -X eval \'stuff \015\'');

        sys::outjs(array('s' => 'ok'));
    }

    $command = 'sudo -u server' . $server['uid'] . ' screen -p 0 -S s_' . $server['uid'] . ' -X hardcopy -h ' . $filecmd . ' && cat ' . $filecmd;

    $output = $ssh->get($command);

    sys::out(htmlspecialchars($output, ENT_QUOTES, ''));
}

$html->nav($server['address'], $cfg['http'] . 'servers/id/' . $id);
$html->nav('Консоль');

$html->get('console', 'sections/servers/' . $server['game']);
$html->set('id', $id);
$html->pack('main');

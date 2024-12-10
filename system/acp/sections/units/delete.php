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

if (isset($url['delete']) and $url['delete'] == 'all') {
    $sql->query('SELECT `address`, `passwd` FROM `panel` LIMIT 1');
    $panel = $sql->get();

    include(LIB . 'ssh.php');

    if (!$ssh->auth($panel['passwd'], $panel['address'])) {
        sys::outjs(['e' => 'PANEL не удалось создать связь.']);
    }

    $servers = $sql->query('SELECT `id`, `user`, `game` FROM `servers` WHERE `unit`="' . $id . '"');
    while ($server = $sql->get($servers)) {
        $crons = $sql->query('SELECT `id`, `cron` FROM `crontab` WHERE `server`="' . $server['id'] . '"');
        while ($cron = $sql->get($crons)) {
            $crontab = preg_quote($cron['cron'], '/');

            $ssh->set('crontab -l | grep -v "' . $crontab . '" | crontab -');

            $sql->query('DELETE FROM `crontab` WHERE `id`="' . $cron['id'] . '" LIMIT 1');
        }

        $helps = $sql->query('SELECT `id` FROM `help` WHERE `type`="server" AND `service`="' . $server['id'] . '"');
        while ($help = $sql->get($helps)) {
            $sql->query('DELETE FROM `help_dialogs` WHERE `help`="' . $help['id'] . '"');
            $sql->query('DELETE FROM `help` WHERE `id`="' . $help['id'] . '" LIMIT 1');
        }

        $sql->query('DELETE FROM `admins_' . $server['game'] . '` WHERE `server`="' . $server['id'] . '" LIMIT 1');
        $sql->query('DELETE FROM `address_buy` WHERE `server`="' . $server['id'] . '"');
        $sql->query('DELETE FROM `logs_sys` WHERE `server`="' . $server['id'] . '"');
        $sql->query('DELETE FROM `owners` WHERE `server`="' . $server['id'] . '"');
        $sql->query('DELETE FROM `copy` WHERE `server`="' . $server['id'] . '"');
        $sql->query('DELETE FROM `plugins_install` WHERE `server`="' . $server['id'] . '"');
        $sql->query('DELETE FROM `graph` WHERE `server`="' . $server['id'] . '" LIMIT 1');
        $sql->query('DELETE FROM `graph_day` WHERE `server`="' . $server['id'] . '"');
        $sql->query('DELETE FROM `graph_hour` WHERE `server`="' . $server['id'] . '"');
        $sql->query('DELETE FROM `web` WHERE `server`="' . $server['id'] . '"');
    }

    $sql->query('DELETE FROM `address` WHERE `unit`="' . $id . '"');
    $sql->query('DELETE FROM `tarifs` WHERE `unit`="' . $id . '"');
} else {
    $sql->query('SELECT `id` FROM `servers` WHERE `unit`="' . $id . '" LIMIT 1');
    if ($sql->num()) {
        sys::outjs(['e' => 'Нельзя удалить локацию с серверами.']);
    }
}

$sql->query('DELETE FROM `units` WHERE `id`="' . $id . '" LIMIT 1');

sys::outjs(['s' => 'ok']);

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

include(LIB . 'games/actions.php');

class action extends actions
{
    public static function start($id, $type = 'start')
    {
        global $cfg, $sql, $user, $start_point;

        $sql->query('SELECT `uid`, `unit`, `tarif`, `game`, `address`, `slots_start`, `name`, `tickrate`, `time_start` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
        $server = $sql->get();

        $sql->query('SELECT `install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
        $tarif = $sql->get();

        include(LIB . 'ssh.php');

        $sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
        $unit = $sql->get();

        // Проверка SSH соединения с локацией
        if (!$ssh->auth($unit['passwd'], $unit['address'])) {
            return array('e' => sys::text('error', 'ssh'));
        }

        list($ip, $port) = explode(':', $server['address']);
        $internalIp = $ssh->getInternalIp();

        // Убить процессы
        $ssh->set('kill -9 `ps aux | grep s_' . $server['uid'] . ' | grep -v grep | awk ' . "'{print $2}'" . ' | xargs;'
            . 'lsof -i@' . $server['address'] . ' | awk ' . "'{print $2}'" . ' | grep -v PID | xargs`; sudo -u server' . $server['uid'] . ' screen -wipe');

        // Определяем identity директорию сервера
        $server_identity = "server" . $server['uid'];

        // Параметры запуска
        $bash = 'export LD_LIBRARY_PATH=$LD_LIBRARY_PATH:`dirname $0`/RustDedicated_Data/Plugins:`dirname $0`/RustDedicated_Data/Plugins/x86_64 && '
            . './RustDedicated -batchmode +server.ip ' . $internalIp . ' +server.port ' . $port . ' +server.tickrate ' . $server['tickrate'] . ' +server.identity ' . $server_identity . ' +server.maxplayers ' . $server['slots_start'];

        // Временный файл
        $temp = sys::temp($bash);

        // Обновление файла start.sh
        $ssh->setfile($temp, $tarif['install'] . $server['uid'] . '/start.sh', 0500);

        // Строка запуска
        $ssh->set('cd ' . $tarif['install'] . $server['uid'] . ';' // переход в директорию игрового сервера
            . 'chown server' . $server['uid'] . ':1000 start.sh;' // Обновление владельца файла start.sh
            . 'sudo -u server' . $server['uid'] . ' screen -dmS s_' . $server['uid'] . ' sh -c "./start.sh"'); // Запуск игрового сервера

        // Обновление информации в базе
        $sql->query('UPDATE `servers` set `status`="' . $type . '", `online`="0", `players`="", `core_use`="0", `time_start`="' . $start_point . '", `stop`="1" WHERE `id`="' . $id . '" LIMIT 1');

        unlink($temp);

        // Сброс кеша
        actions::clmcache($id);

        sys::reset_mcache('server_scan_mon_pl_' . $id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => $type, 'online' => 0, 'players' => ''));
        sys::reset_mcache('server_scan_mon_' . $id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => $type, 'online' => 0));

        return array('s' => 'ok');
    }

    public static function update($id)
    {
        global $cfg, $sql, $user, $start_point;

        include(LIB . 'ssh.php');

        $sql->query('SELECT `uid`, `unit`, `tarif`, `game`, `name`, `ftp`, `update`, `core_fix` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
        $server = $sql->get();

        // Проверка времени обновления
        $update = $server['update'] + $cfg['update'][$server['game']] * 60;

        if ($update > $start_point and $user['group'] != 'admin')
            return array('e' => sys::updtext(sys::text('servers', 'update'), array('time' => sys::date('max', $update))));

        $sql->query('SELECT `address`, `passwd`, `sql_login`, `sql_passwd`, `sql_port`, `sql_ftp` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
        $unit = $sql->get();

        $sql->query('SELECT `install`, `plugins_install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
        $tarif = $sql->get();

        // Проверка ssh соедниения пу с локацией
        if (!$ssh->auth($unit['passwd'], $unit['address']))
            return array('e' => sys::text('error', 'ssh'));

        $taskset = '';

        // Если включена система автораспределения и не установлен фиксированный поток
        if ($cfg['cpu_route'] and !$server['core_fix']) {
            $proc_stat = array();

            $proc_stat[0] = $ssh->get('cat /proc/stat');
        }

        // Директория игрового сервера
        $install = $tarif['install'] . $server['uid'];

        // Если система автораспределения продолжить парсинг загрузки процессора
        if (isset($proc_stat)) {
            $proc_stat[1] = $ssh->get('cat /proc/stat');

            // Ядро/поток, на котором будет запущен игровой сервер (поток выбран с рассчетом наименьшей загруженности в момент запуска игрового сервера)
            $core = sys::cpu_idle($server['unit'], $proc_stat, false); // число от 1 до n (где n число ядер/потоков в процессоре (без нулевого)

            if (!is_numeric($core))
                return array('e' => 'Не удается выполнить операцию, нет свободного потока.');

            $taskset = 'taskset -c ' . $core;
        }

        if ($server['core_fix']) {
            $core = $server['core_fix'] - 1;
            $taskset = 'taskset -c ' . $core;
        }

        $ssh->set('cd ' . $cfg['steamcmd'] . ' && ' . $taskset . ' screen -dmS u_' . $server['uid'] . ' sh -c "'
            . './steamcmd.sh +login anonymous +force_install_dir "' . $install . '" +app_update 258550 +quit;'
            . 'cd ' . $install . ';'
            . 'chown -R server' . $server['uid'] . ':servers .;'
            . 'find . -type d -exec chmod 700 {} \;;'
            . 'find . -type f -exec chmod 600 {} \;;'
            . 'chmod 500 ' . params::$aFileGame[$server['game']] . '"');

        $core = !isset($core) ? 0 : $core + 1;

        // Обновление информации в базе
        $sql->query('UPDATE `servers` set `status`="update", `update`="' . $start_point . '", `core_use`="' . $core . '" WHERE `id`="' . $id . '" LIMIT 1');

        // Логирование
        $sql->query('INSERT INTO `logs_sys` set `user`="' . $user['id'] . '", `server`="' . $id . '", `text`="' . sys::text('syslogs', 'update') . '", `time`="' . $start_point . '"');

        // Сброс кеша
        actions::clmcache($id);

        sys::reset_mcache('server_scan_mon_pl_' . $id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'update', 'online' => 0, 'players' => ''));
        sys::reset_mcache('server_scan_mon_' . $id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'update', 'online' => 0));

        return array('s' => 'ok');
    }
}

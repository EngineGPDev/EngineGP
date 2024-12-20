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

$aData = [];

$aData['server'] = $_POST['server'] ?? sys::outjs(['e' => 'Необходимо выбрать игровой сервер.'], $nmch);
$aData['type'] = $url['subsection'];

switch ($aWebInstall[$server['game']][$aData['type']]) {
    case 'server':
        $sql->query('SELECT `unit`, `domain`, `login` FROM `web` WHERE `type`="' . $aData['type'] . '" AND `server`="' . $server['id'] . '" LIMIT 1');

        break;

    case 'user':
        $sql->query('SELECT `unit`, `domain`, `login` FROM `web` WHERE `type`="' . $aData['type'] . '" AND `user`="' . $server['user'] . '" LIMIT 1');

        break;

    case 'unit':
        $sql->query('SELECT `unit`, `domain`, `login` FROM `web` WHERE `type`="' . $aData['type'] . '" AND `user`="' . $server['user'] . '" AND `unit`="' . $server['unit'] . '" LIMIT 1');

        break;
}

if (!$sql->num()) {
    sys::outjs(['e' => 'Дополнительная услуга не установлена.'], $nmch);
}

$web = $sql->get();

$aData['config'] = '<?php' . PHP_EOL . 'return array(' . PHP_EOL;

$i = 0;

include(LIB . 'web/free.php');

foreach ($aData['server'] as $sid) {
    $sql->query('SELECT `id`, `uid`, `unit`, `user`, `address`, `port`, `game`, `ftp_use`, `ftp`, `ftp_root`, `ftp_passwd` FROM `servers` WHERE `id`="' . $sid . '" AND `user`="' . $server['user'] . '" AND `game`="cs" LIMIT 1');
    if (!$sql->num()) {
        continue;
    }

    $server = $sql->get();

    $address = $server['address'];

    if (!$server['ftp_use']) {
        continue;
    }

    if (!$server['ftp']) {
        sys::outjs(['r' => 'Для подключения игрового сервера необходимо включить FileTP.', 'url' => $cfg['http'] . 'servers/id/' . $sid . '/section/filetp'], $nmch);
    }

    $stack = web::stack($aData, '`login`');

    if (!$sql->num($stack)) {
        continue;
    }

    // Каталог логов сервера
    $dir = ($cfg['ftp']['root'][$server['game']] || $server['ftp_root']) ? '/cstrike/addons/amxmodx/data' : '/addons/amxmodx/data';

    $i += 1;

    $aData['config'] .= $i . ' => array(' . PHP_EOL
        . '\'ip\' => \'' . $address[0] . '\',' . PHP_EOL
        . '\'port\' => ' . $address[1] . ',' . PHP_EOL
        . '\'engine\' => \'GOLDSOURCE\',' . PHP_EOL
        . '\'ftp_host\' => \'' . $address[0] . '\',' . PHP_EOL
        . '\'ftp_port\' => 21,' . PHP_EOL
        . '\'ftp_user\' => \'' . $server['uid'] . '\',' . PHP_EOL
        . '\'ftp_pass\' => \'' . $server['ftp_passwd'] . '\',' . PHP_EOL
        . '\'ftp_path\' => \'' . $dir . '\'' . PHP_EOL
        . '),' . PHP_EOL;

}

include(LIB . 'ssh.php');

$unit = web::unit($aWebUnit, $aData['type'], $web['unit']);

if (!$ssh->auth($unit['passwd'], $unit['address'])) {
    sys::outjs(['e' => sys::text('ssh', 'error')], $nmch);
}

// Директория дополнительной услуги
$install = $aWebUnit['install'][$aWebUnit['unit'][$aData['type']]][$aData['type']] . $web['domain'];

$temp = sys::temp($aData['config'] . ');');
$ssh->setfile($temp, $install . '/config/servers.config.php');
$ssh->set('chmod 0644' . ' ' . $install . '/config/servers.config.php');

unlink($temp);

sys::outjs(['s' => 'ok'], $nmch);

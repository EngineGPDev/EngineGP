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

// Проверка на наличие установленной услуги
switch ($aWebInstall[$server['game']][$url['subsection']]) {
    case 'server':
        $sql->query('SELECT `id`, `login` FROM `web` WHERE `type`="' . $url['subsection'] . '" AND `server`="' . $id . '" LIMIT 1');
        break;

    case 'user':
        $sql->query('SELECT `id`, `login` FROM `web` WHERE `type`="' . $url['subsection'] . '" AND `user`="' . $server['user'] . '" LIMIT 1');
        break;

    case 'unit':
        $sql->query('SELECT `id`, `login` FROM `web` WHERE `type`="' . $url['subsection'] . '" AND `user`="' . $server['user'] . '" AND `unit`="' . $server['unit'] . '" LIMIT 1');
        break;
}

if (!$sql->num()) {
    sys::outjs(array('e' => 'Дополнительная услуга не установлена.'), $nmch);
}

$web = $sql->get();

include(LIB . 'ssh.php');

if ($aWebUnit['unit'][$url['subsection']] == 'local') {
    $sql->query('SELECT `address`, `passwd`, `domain` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
    $unit = $sql->get();
} else {
    $unit = array(
        'address' => $aWebUnit['address'],
        'passwd' => $aWebUnit['passwd'],
    );
}

if (!$ssh->auth($unit['passwd'], $unit['address'])) {
    sys::outjs(array('e' => sys::text('ssh', 'error')), $nmch);
}

$passwd = sys::passwd($aWebParam[$url['subsection']]['passwd']);

$ssh->set('mysql --login-path=local -e "SET PASSWORD FOR  \'' . $web['login'] . '\'@\'%\' = PASSWORD(\'' . $passwd . '\');"');

// Обновление данных
$sql->query('UPDATE `web` set `passwd`="' . $passwd . '" WHERE `id`="' . $web['id'] . '" LIMIT 1');

sys::outjs(array('s' => 'ok'), $nmch);

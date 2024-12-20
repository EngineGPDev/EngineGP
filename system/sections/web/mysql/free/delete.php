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

if ($user['group'] != 'admin') {
    sys::outjs(['i' => 'Чтобы удалить услугу, создайте вопрос выбрав свой сервер с причиной удаления.'], $nmch);
}

switch ($aWebInstall[$server['game']][$url['subsection']]) {
    case 'server':
        $sql->query('SELECT `id`, `uid`, `unit`, `login` FROM `web` WHERE `type`="' . $url['subsection'] . '" AND `server`="' . $id . '" LIMIT 1');

        break;

    case 'user':
        $sql->query('SELECT `id`, `uid`, `unit`, `login` FROM `web` WHERE `type`="' . $url['subsection'] . '" AND `user`="' . $server['user'] . '" LIMIT 1');

        break;

    case 'unit':
        $sql->query('SELECT `id`, `uid`, `unit`, `login` FROM `web` WHERE `type`="' . $url['subsection'] . '" AND `user`="' . $server['user'] . '" AND `unit`="' . $server['unit'] . '" LIMIT 1');

        break;
}

if (!$sql->num()) {
    sys::outjs(['e' => 'Дополнительная услуга не установлена.'], $nmch);
}

$web = $sql->get();

if ($aWebUnit['unit'][$url['subsection']] == 'local') {
    $sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $web['unit'] . '" LIMIT 1');
    $unit = $sql->get();
} else {
    $unit = [
        'address' => $aWebUnit['address'],
        'passwd' => $aWebUnit['passwd'],
    ];
}

include(LIB . 'ssh.php');

if (!$ssh->auth($unit['passwd'], $unit['address'])) {
    sys::outjs(['e' => sys::text('error', 'ssh')], $nmch);
}

$ssh->set("mysql --login-path=local -e \"DROP DATABASE IF EXISTS " . $web['login'] . "; DROP USER " . $web['login'] . "\"");

$sql->query('DELETE FROM `web` WHERE `id`="' . $web['id'] . '" LIMIT 1');

sys::outjs(['s' => 'ok'], $nmch);

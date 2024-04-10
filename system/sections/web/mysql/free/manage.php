<?php
/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @copyright Copyright (c) 2018-present Solovev Sergei <inbox@seansolovev.ru>
 *
 * @link      https://github.com/EngineGPDev/EngineGP for the canonical source repository
 * @link      https://gitforge.ru/EngineGP/EngineGP for the canonical source repository
 *
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE MIT License
 * @license   https://gitforge.ru/EngineGP/EngineGP/src/branch/main/LICENSE MIT License
 */

if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

switch ($aWebInstall[$server['game']][$url['subsection']]) {
    case 'server':
        $sql->query('SELECT `domain`, `passwd`, `login`, `date` FROM `web` WHERE `type`="' . $url['subsection'] . '" AND `server`="' . $id . '" LIMIT 1');

        break;

    case 'user':
        $sql->query('SELECT `domain`, `passwd`, `login`, `date` FROM `web` WHERE `type`="' . $url['subsection'] . '" AND `user`="' . $server['user'] . '" LIMIT 1');

        break;

    case 'unit':
        $sql->query('SELECT `domain`, `passwd`, `login`, `date` FROM `web` WHERE `type`="' . $url['subsection'] . '" AND `user`="' . $server['user'] . '" AND `unit`="' . $server['unit'] . '" LIMIT 1');

        break;
}

if (!$sql->num())
    sys::back($cfg['http'] . 'servers/id/' . $id . '/section/web/subsection/' . $url['subsection'] . '/action/install');

$web = $sql->get();

$html->nav('Управление ' . $aWebname[$url['subsection']]);

$html->get('manage', 'sections/web/' . $url['subsection'] . '/free');

$html->set('id', $id);

$html->set('url', $web['domain']);
$html->set('login', $web['login']);
$html->set('passwd', $web['passwd']);

$html->pack('main');

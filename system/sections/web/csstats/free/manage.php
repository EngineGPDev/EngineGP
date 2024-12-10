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

$sql->query('SELECT `domain`, `passwd`, `config`, `date` FROM `web` WHERE `type`="' . $url['subsection'] . '" AND `server`="' . $id . '" LIMIT 1');

if (!$sql->num()) {
    sys::back($cfg['http'] . 'servers/id/' . $id . '/section/web/subsection/' . $url['subsection'] . '/action/install');
}

$web = $sql->get();

$html->nav('Управление ' . $aWebname[$url['subsection']]);

$html->get('manage', 'sections/web/' . $url['subsection'] . '/free');

$html->set('id', $id);

$html->set('url', $web['domain']);
$html->set('passwd', $web['passwd']);
$html->set('config', base64_decode($web['config']));
$html->set('servers', '<option value="' . $id . '">#' . $id . ' ' . $server['name'] . ' (' . $server['address'] . ')</option>');

if (in_array('update', $aAction[$url['subsection']])) {
    $html->unit('update', 1);
} else {
    $html->unit('update');
}

$html->pack('main');

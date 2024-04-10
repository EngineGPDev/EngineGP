<?php
/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @link      https://github.com/EngineGPDev/EngineGP
 * @link      https://gitforge.ru/EngineGP/EngineGP
 * @copyright Copyright (c) Solovev Sergei <inbox@seansolovev.ru>
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE
 * @license   https://gitforge.ru/EngineGP/EngineGP/src/branch/main/LICENSE
 */

if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

$sql->query('SELECT `domain`, `passwd`, `config`, `date` FROM `web` WHERE `type`="' . $url['subsection'] . '" AND `server`="' . $id . '" LIMIT 1');

if (!$sql->num())
    sys::back($cfg['http'] . 'servers/id/' . $id . '/section/web/subsection/' . $url['subsection'] . '/action/install');

$web = $sql->get();

$html->nav('Управление ' . $aWebname[$url['subsection']]);

$html->get('manage', 'sections/web/' . $url['subsection'] . '/free');

$html->set('id', $id);

$html->set('url', $web['domain']);
$html->set('passwd', $web['passwd']);
$html->set('config', base64_decode($web['config']));
$html->set('servers', '<option value="' . $id . '">#' . $id . ' ' . $server['name'] . ' (' . $server['address'] . ')</option>');

if (in_array('update', $aAction[$url['subsection']]))
    $html->unit('update', 1);
else
    $html->unit('update');

$html->pack('main');

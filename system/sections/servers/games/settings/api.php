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

$sql->query('SELECT `key` FROM `api` WHERE `server`="' . $id . '" LIMIT 1');
if ($sql->num())
    $sql->query('DELETE FROM `api` WHERE `server`="' . $id . '" LIMIT 1');
else
    $sql->query('INSERT INTO `api` set `server`="' . $id . '", `key`="' . md5(sys::passwd(10)) . '"');

$mcache->delete('server_settings_' . $id);

sys::back($cfg['http'] . 'servers/id/' . $id . '/section/settings');

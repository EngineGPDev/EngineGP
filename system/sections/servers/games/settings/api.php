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

$sql->query('SELECT `key` FROM `api` WHERE `server`="' . $id . '" LIMIT 1');
if ($sql->num())
    $sql->query('DELETE FROM `api` WHERE `server`="' . $id . '" LIMIT 1');
else
    $sql->query('INSERT INTO `api` set `server`="' . $id . '", `key`="' . md5(sys::passwd(10)) . '"');

$mcache->delete('server_settings_' . $id);

sys::back($cfg['http'] . 'servers/id/' . $id . '/section/settings');

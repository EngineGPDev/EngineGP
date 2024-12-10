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

$sql->query('SELECT `file` FROM `pages` WHERE `id`="' . $id . '" LIMIT 1');
$page = $sql->get();

unlink(FILES . 'pages/' . $page['file']);

$sql->query('DELETE FROM `pages` WHERE `id`="' . $id . '" LIMIT 1');

sys::outjs(['s' => 'ok']);

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

if (isset($url['type']) and $url['type'] == 'cat')
    $sql->query('DELETE FROM `wiki_category` WHERE `id`="' . $id . '" LIMIT 1');
else {
    $sql->query('DELETE FROM `wiki` WHERE `id`="' . $id . '" LIMIT 1');
    $sql->query('DELETE FROM `wiki_answer` WHERE `wiki`="' . $id . '" LIMIT 1');
}

sys::outjs(array('s' => 'ok'));

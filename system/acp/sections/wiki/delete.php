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

if (isset($url['type']) and $url['type'] == 'cat')
    $sql->query('DELETE FROM `wiki_category` WHERE `id`="' . $id . '" LIMIT 1');
else {
    $sql->query('DELETE FROM `wiki` WHERE `id`="' . $id . '" LIMIT 1');
    $sql->query('DELETE FROM `wiki_answer` WHERE `wiki`="' . $id . '" LIMIT 1');
}

sys::outjs(array('s' => 'ok'));

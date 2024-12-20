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

$text = isset($_POST['text']) ? str_ireplace('.bsp', '', $_POST['text']) : '';

$mkey = md5($text . $id);

$cache = $mcache->get($mkey);

if (is_array($cache)) {
    if ($go) {
        sys::outjs($cache, $nmch);
    }

    sys::outjs($cache);
}

if (!isset($text[2])) {
    if ($go) {
        sys::outjs(['e' => 'Для выполнения поиска, необходимо больше данных'], $nmch);
    }

    sys::outjs(['e' => '']);
}

// Поиск по картам
if ($text[0] == '^') {
    $sql->query('SELECT `id`, `name` FROM `maps` WHERE `unit`="' . $server['unit'] . '" AND `game`="' . $server['game'] . '" AND `name` REGEXP FROM_BASE64(\'' . base64_encode(str_replace('_', '\_', $text) . '') . '\') ORDER BY `name` ASC LIMIT 12');
    $text = substr($text, 1);
} else {
    $sql->query('SELECT `id`, `name` FROM `maps` WHERE `unit`="' . $server['unit'] . '" AND `game`="' . $server['game'] . '" AND `name` LIKE FROM_BASE64(\'' . base64_encode('%' . str_replace('_', '\_', $text) . '%') . '\') ORDER BY `name` ASC LIMIT 12');
}

if (!$sql->num()) {
    if ($go) {
        sys::outjs(['e' => 'По вашему запросу ничего не найдено'], $nmch);
    }

    sys::outjs(['e' => 'По вашему запросу ничего не найдено']);
}

$i = 0;
$mapsjs = '';

while ($map = $sql->get()) {
    $i += 1;

    $mapsjs[$i] = 's' . $map['id'];

    $html->get('map_search', 'sections/servers/games/maps');

    $html->set('id', 's' . $map['id']);
    $html->set('img', sys::img($map['name'], $server['game']));
    $html->set('name', sys::find($map['name'], $text));

    $html->pack('maps');
}

$mcache->set($mkey, ['maps' => $html->arr['maps'], 'mapsjs' => $mapsjs], false, 15);

sys::outjs(['maps' => $html->arr['maps'], 'mapsjs' => $mapsjs]);

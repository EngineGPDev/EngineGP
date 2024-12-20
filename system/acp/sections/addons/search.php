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

$text = isset($_POST['text']) ? trim($_POST['text']) : '';

$mkey = md5($text . $id);

$cache = $mcache->get($mkey);

$nmch = null;

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

if (substr($text, 0, 5) == 'game=') {
    $game = trim(substr($text, 5));

    if (in_array($game, ['cs', 'cssold', 'css', 'csgo', 'cs2', 'samp', 'crmp', 'mta', 'mc'])) {
        $plugins = $sql->query('SELECT `id`, `cat`, `game`, `name`, `status` FROM `plugins` WHERE `game`="' . $game . '" ORDER BY `id` ASC');
    }
} elseif ($text[0] == 'i' and $text[1] == 'd') {
    $plugins = $sql->query('SELECT `id`, `cat`, `game`, `name`, `status` FROM `plugins` WHERE `id`="' . sys::int($text) . '" LIMIT 1');
} else {
    $like = '`id` LIKE FROM_BASE64(\'' . base64_encode('%' . str_replace('_', '\_', $text) . '%') . '\') OR'
        . '`name` LIKE FROM_BASE64(\'' . base64_encode('%' . str_replace('_', '\_', $text) . '%') . '\') OR'
        . '`desc` LIKE FROM_BASE64(\'' . base64_encode('%' . str_replace('_', '\_', $text) . '%') . '\') OR'
        . '`info` LIKE FROM_BASE64(\'' . base64_encode('%' . str_replace('_', '\_', $text) . '%') . '\') OR'
        . '`packs` LIKE FROM_BASE64(\'' . base64_encode('%' . str_replace('_', '\_', $text) . '%') . '\')';

    $plugins = $sql->query('SELECT `id`, `cat`, `game`, `name`, `status` FROM `plugins` WHERE ' . $like . ' ORDER BY `id` ASC LIMIT 10');
}

if (!$sql->num($plugins)) {
    if ($go) {
        sys::outjs(['e' => 'По вашему запросу ничего не найдено'], $nmch);
    }

    sys::outjs(['e' => 'По вашему запросу ничего не найдено']);
}

$list = '';

$status = [0 => 'Стабильный', 2 => 'Нестабильный', 1 => 'Тестируемый'];

while ($plugin = $sql->get($plugins)) {
    $sql->query('SELECT `name` FROM `plugins_category` WHERE `id`="' . $plugin['cat'] . '" LIMIT 1');
    $cat = $sql->get();

    $list .= '<tr>';
    $list .= '<td>' . $plugin['id'] . '</td>';
    $list .= '<td><a href="' . $cfg['http'] . 'acp/addons/id/' . $plugin['id'] . '">' . $plugin['name'] . '</a></td>';
    $list .= '<td>' . $cat['name'] . '</td>';
    $list .= '<td>' . $status[$plugin['status']] . '</td>';
    $list .= '<td>' . strtoupper($plugin['game']) . '</td>';
    $list .= '<td><a href="#" onclick="return plugins_delete(\'' . $plugin['id'] . '\')" class="text-red">Удалить</a></td>';
    $list .= '</tr>';
}

$mcache->set($mkey, ['s' => $list], false, 15);

sys::outjs(['s' => $list]);

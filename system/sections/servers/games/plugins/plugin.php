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

$pid = isset($url['plugin']) ? sys::int($url['plugin']) : sys::back($cfg['http'] . 'servers/id/' . $id . '/section/plugins');

$sql->query('SELECT `id`, `upd` FROM `plugins_install` WHERE `server`="' . $id . '" AND `plugin`="' . $pid . '" LIMIT 1');

if (!$sql->num())
    sys::back($cfg['http'] . 'servers/id/' . $id . '/section/plugins');

$install = $sql->get();

// Если установленно обновление
if ($install['upd'])
    $sql->query('SELECT `name`, `info`, `images`, `upd` FROM `plugins_update` WHERE `id`="' . $install['upd'] . '" LIMIT 1');
else
    $sql->query('SELECT `name`, `info`, `images`, `upd` FROM `plugins` WHERE `id`="' . $pid . '" LIMIT 1');

if (!$sql->num())
    sys::back($cfg['http'] . 'servers/id/' . $id . '/section/plugins');

$plugin = $sql->get();

$html->nav('Плагины', $cfg['http'] . 'servers/id/' . $id . '/section/plugins');
$html->nav($plugin['name']);

// Если есть кеш
if ($mcache->get('server_plugin_' . $pid . $id) != '')
    $html->arr['main'] = $mcache->get('server_plugin_' . $pid . $id);
else {
    include(LIB . 'games/plugins.php');

    // Построение списка редактируемых файлов
    $aConf = array();

    $sql->query('SELECT `id`, `file` FROM `plugins_config` WHERE (`plugin`="' . $pid . '" AND `update`="0") OR (`plugin`="' . $pid . '" AND `update`="' . $install['upd'] . '") ORDER BY `sort`, `id` ASC');
    while ($config = $sql->get()) {
        // Исключить дублирование, путем проверки массива файлов
        if (in_array($config['file'], $aConf))
            continue;

        $aConf[] = $config['file'];

        // Данные файла
        $file = explode('/', $config['file']);

        $html->get('config_list', 'sections/servers/games/plugins');

        $html->set('id', $id);
        $html->set('fid', $config['id']);
        $html->set('name', end($file));
        $html->set('file', $config['file']);

        $html->pack('configs');
    }

    $images = plugins::images($plugin['images'], $pid);

    $html->get('configs', 'sections/servers/games/plugins');

    $html->set('id', $id);
    $html->set('name', $plugin['name']);
    $html->set('info', htmlspecialchars_decode($plugin['info']));

    // Картинки
    if (!empty($images)) {
        $html->unit('images', 1);
        $html->set('images', $images);
    } else
        $html->unit('images');

    // Редактируемые файлы
    if (isset($html->arr['configs'])) {
        $html->set('configs', $html->arr['configs']);
        $html->unit('configs', 1);
    } else
        $html->unit('configs');

    $html->pack('main');

    $mcache->set('server_plugin_' . $pid . $id, $html->arr['main'], false, 60);
}

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

$sql->query('SELECT `id` FROM `control_copy` WHERE `server`="' . $sid . '" AND `info`="' . params::$section_copy[$server['game']]['CopyFull'] . '" LIMIT 1');
if ($sql->num()) {
    sys::outjs(['e' => 'Для создания новой копии необходимо удалить старую.'], $nmch);
}

$name_copy = md5($id . $start_point . $sid . $server['game']);

$ssh->set('cd /servers/' . $server['uid'] . ' && screen -dmS copy_' . $server['uid'] . ' sh -c "tar -cf ' . $name_copy . '.tar ' . params::$section_copy[$server['game']]['CopyFull'] . '; mv ' . $name_copy . '.tar /copy"');

$plugins = '';

$sql->query('SELECT `plugin`, `upd` FROM `control_plugins_install` WHERE `server`="' . $sid . '"');
while ($plugin = $sql->get()) {
    $plugins .= $plugin['plugin'] . '.' . $plugin['upd'] . ',';
}

$sql->query('INSERT INTO `control_copy` set `user`="' . $ctrl['user'] . '_' . $id . '", `game`="' . $server['game'] . '", `server`="' . $sid . '", `pack`="' . $server['pack'] . '", `name`="' . $name_copy . '", `info`="' . params::$section_copy[$server['game']]['CopyFull'] . '",  `plugins`="' . substr($plugins, 0, -1) . '", `date`="' . $start_point . '", `status`="0"');

// Очистка кеша
$mcache->delete('ctrl_server_copy_' . $sid);

sys::outjs(['s' => 'ok'], $nmch);

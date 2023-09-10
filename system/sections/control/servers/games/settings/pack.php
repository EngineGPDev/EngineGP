<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

$aPacks = $cfg['control_packs'][$server['game']];

$pack = $url['pack'] ?? exit;

if ($pack == $server['pack'])
    sys::outjs(['s' => 'ok']);

// Проверка сборки
if (!array_key_exists($pack, $aPacks))
    sys::outjs(['e' => 'Сборка не найдена.']);

$sql->query('UPDATE `control_servers` set `pack`="' . $pack . '" WHERE `id`="' . $sid . '" LIMIT 1');

sys::outjs(['s' => 'ok'], 'ctrl_server_settings_' . $sid);

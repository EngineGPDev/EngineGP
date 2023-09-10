<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

$sql->query('SELECT `packs` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
$tarif = $sql->get();

$aPacks = sys::b64djs($tarif['packs']);

$pack = $url['pack'] ?? exit;

if ($pack == $server['pack'])
    sys::outjs(['s' => 'ok']);

// Проверка сборки
if (!array_key_exists($pack, $aPacks))
    sys::outjs(['e' => 'Сборка не найдена.']);

$sql->query('UPDATE `servers` set `pack`="' . $pack . '" WHERE `id`="' . $id . '" LIMIT 1');

sys::outjs(['s' => 'ok'], 'server_settings_' . $id);

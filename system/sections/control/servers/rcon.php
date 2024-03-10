<?php
if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

$sql->query('SELECT `uid`, `unit`, `address`, `game`, `status` FROM `control_servers` WHERE `id`="' . $sid . '" LIMIT 1');
$server = $sql->get();

ctrl::nav($server, $id, $sid, 'rcon');

include(ctrl::route($server, 'rcon', $go));

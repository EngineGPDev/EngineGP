<?php
if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

$sql->query('SELECT `uid`, `unit`, `user`, `address`, `game`, `status`, `slots_start`, `plugins_use`, `ftp_use`, `console_use`, `stats_use`, `copy_use`, `web_use`, `time` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
$server = $sql->get();

sys::nav($server, $id, 'graph');

include(sys::route($server, 'graph', $go));
?>
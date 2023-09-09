<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

$sql->query('SELECT `name`, `slots_min`, `slots_max`, `install`, `timext`, `discount`, `price` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
$tarif = $sql->get();

// Подразделы
$aSub = array('extend', 'address', 'addextend', 'unit', 'slots');

require(SEC . 'servers/games/tarif.php');

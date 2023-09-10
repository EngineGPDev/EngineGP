<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

$sql->query('DELETE FROM `notice` WHERE `id`="' . $id . '" LIMIT 1');

sys::outjs(['s' => 'ok']);

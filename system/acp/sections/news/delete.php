<?php
if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

$sql->query('DELETE FROM `news` WHERE `id`="' . $id . '" LIMIT 1');

sys::outjs(array('s' => 'ok'));
?>
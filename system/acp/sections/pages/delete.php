<?php
if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

$sql->query('SELECT `file` FROM `pages` WHERE `id`="' . $id . '" LIMIT 1');
$page = $sql->get();

unlink(FILES . 'pages/' . $page['file']);

$sql->query('DELETE FROM `pages` WHERE `id`="' . $id . '" LIMIT 1');

sys::outjs(array('s' => 'ok'));
?>
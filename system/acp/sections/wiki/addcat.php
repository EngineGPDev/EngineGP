<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

if ($go) {
    $aData = [];

    $aData['name'] = isset($_POST['name']) ? trim((string) $_POST['name']) : '';
    $aData['sort'] = isset($_POST['sort']) ? sys::int($_POST['sort']) : 0;

    if (in_array('', $aData))
        sys::outjs(['e' => 'Необходимо заполнить все поля']);

    $sql->query('INSERT INTO `wiki_category` set '
        . '`name`="' . htmlspecialchars($aData['name']) . '",'
        . '`sort`="' . $aData['sort'] . '"');

    sys::outjs(['s' => 'ok']);
}

$html->get('addcat', 'sections/wiki');

$html->pack('main');

<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

$sql->query('SELECT * FROM `wiki_category` WHERE `id`="' . $id . '" LIMIT 1');
$wiki = $sql->get();

if ($go) {
    $aData = [];

    $aData['name'] = isset($_POST['name']) ? trim((string) $_POST['name']) : htmlspecialchars_decode((string) $wiki['name']);
    $aData['sort'] = isset($_POST['sort']) ? sys::int($_POST['sort']) : $wiki['sort'];

    if (in_array('', $aData))
        sys::outjs(['e' => 'Необходимо заполнить все поля']);

    $sql->query('UPDATE `wiki_category` set '
        . '`name`="' . htmlspecialchars($aData['name']) . '",'
        . '`sort`="' . $aData['sort'] . '" WHERE `id`="' . $id . '" LIMIT 1');

    sys::outjs(['s' => 'ok']);
}

$html->get('cat', 'sections/wiki');

$html->set('name', htmlspecialchars_decode((string) $wiki['name']));
$html->set('id', $wiki['id']);
$html->set('sort', $wiki['sort']);

$html->pack('main');

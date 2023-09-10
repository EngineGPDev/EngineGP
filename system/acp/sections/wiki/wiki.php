<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

$sql->query('SELECT `name`, `cat`, `tags` FROM `wiki` WHERE `id`="' . $id . '" LIMIT 1');
$wiki = $sql->get();

$sql->query('SELECT `text` FROM `wiki_answer` WHERE `wiki`="' . $id . '" LIMIT 1');
$wiki = array_merge($wiki, $sql->get());

if ($go) {
    $aData = [];

    $aData['name'] = isset($_POST['name']) ? trim((string) $_POST['name']) : htmlspecialchars_decode((string) $wiki['name']);
    $aData['text'] = isset($_POST['text']) ? sys::bbc(trim((string) $_POST['text'])) : htmlspecialchars_decode((string) $wiki['text']);
    $aData['cat'] = isset($_POST['cat']) ? sys::int($_POST['cat']) : $wiki['cat'];
    $aData['tags'] = isset($_POST['tags']) ? trim((string) $_POST['tags']) : htmlspecialchars_decode((string) $wiki['tags']);

    if (in_array('', $aData))
        sys::outjs(['e' => 'Необходимо заполнить все поля']);

    if (sys::strlen($aData['tags']) > 100)
        sys::outjs(['e' => 'Теги не должен превышать 100 символов.']);

    $sql->query('SELECT `id` FROM `wiki_category` WHERE `id`="' . $aData['cat'] . '" LIMIT 1');
    if (!$sql->num())
        sys::outjs(['e' => 'Указанная категория не найдена']);

    $sql->query('UPDATE `wiki` set '
        . '`name`="' . htmlspecialchars($aData['name']) . '",'
        . '`cat`="' . $aData['cat'] . '",'
        . '`tags`="' . htmlspecialchars($aData['tags']) . '",'
        . '`date`="' . $start_point . '" WHERE `id`="' . $id . '" LIMIT 1');

    $sql->query('UPDATE `wiki_answer` set '
        . '`cat`="' . $aData['cat'] . '",'
        . '`text`="' . htmlspecialchars((string) $aData['text']) . '" WHERE `wiki`="' . $id . '" LIMIT 1');

    sys::outjs(['s' => 'ok']);
}

$cats = '';

$sql->query('SELECT `id`, `name` FROM `wiki_category` ORDER BY `id` ASC');
while ($cat = $sql->get())
    $cats .= '<option value="' . $cat['id'] . '">' . $cat['name'] . '</option>';

$html->get('wiki', 'sections/wiki');

$html->set('id', $id);
$html->set('name', htmlspecialchars_decode((string) $wiki['name']));
$html->set('text', htmlspecialchars_decode((string) $wiki['text']));
$html->set('tags', htmlspecialchars_decode((string) $wiki['tags']));
$html->set('cats', str_replace('"' . $wiki['cat'] . '"', '"' . $wiki['cat'] . '" selected', $cats));

$html->pack('main');

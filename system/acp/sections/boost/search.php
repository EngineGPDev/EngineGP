<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

$text = isset($_POST['text']) ? trim((string) $_POST['text']) : '';

$mkey = md5($text . $id);

$cache = $mcache->get($mkey);

if (is_array($cache)) {
    if ($go)
        sys::outjs($cache, $nmch);

    sys::outjs($cache);
}

if (!isset($text[2])) {
    if ($go)
        sys::outjs(['e' => 'Для выполнения поиска, необходимо больше данных'], $nmch);

    sys::outjs(['e' => '']);
}

$check = explode('=', $text);

if (in_array($check[0], ['server', 'user'])) {
    $val = trim($check[1]);

    switch ($check[0]) {
        case 'server':
            $sql->query('SELECT * FROM `boost` WHERE `server`="' . sys::int($val) . '" ORDER BY `id` DESC');
            break;

        case 'user':
            $sql->query('SELECT * FROM `boost` WHERE `user`="' . sys::int($val) . '" ORDER BY `id` DESC');
    }
} elseif ($text[0] == 'i' and $text[1] == 'd')
    $sql->query('SELECT * FROM `boost` WHERE `id`="' . sys::int($text) . '" LIMIT 1');
else {
    $like = '`id` LIKE FROM_BASE64(\'' . base64_encode('%' . str_replace('_', '\_', $text) . '%') . '\') OR'
        . '`site` LIKE FROM_BASE64(\'' . base64_encode('%' . str_replace('_', '\_', $text) . '%') . '\') OR'
        . '`circles` LIKE FROM_BASE64(\'' . base64_encode('%' . str_replace('_', '\_', $text) . '%') . '\') OR'
        . '`money` LIKE FROM_BASE64(\'' . base64_encode('%' . str_replace('_', '\_', $text) . '%') . '\')';

    $sql->query('SELECT * FROM `boost` WHERE ' . $like . ' ORDER BY `id` DESC LIMIT 40');
}

if (!$sql->num()) {
    if ($go)
        sys::outjs(['e' => 'По вашему запросу ничего не найдено'], $nmch);

    sys::outjs(['e' => 'По вашему запросу ничего не найдено']);
}

$list = '';

while ($log = $sql->get()) {
    $list .= '<tr>';
    $list .= '<td>' . $log['id'] . '</td>';
    $list .= '<td>Покупка кругов: ' . $log['circles'] . ' шт. на сайте: ' . $aBoost['cs'][$log['site']]['site'] . ', списана сумма: ' . $log['money'] . ' ' . $cfg['currency'] . '</td>';
    $list .= '<td class="text-center"><a href="' . $cfg['http'] . 'acp/users/id/' . $log['user'] . '">USER_' . $log['user'] . '</a></td>';
    $list .= '<td class="text-center"><a href="' . $cfg['http'] . 'acp/servers/id/' . $log['server'] . '">SERVER_' . $log['server'] . '</a></td>';
    $list .= '<td class="text-center">' . date('d.m.Y - H:i:s', $log['date']) . '</td>';
    $list .= '</tr>';
}

$mcache->set($mkey, ['s' => $list], false, 15);

sys::outjs(['s' => $list]);

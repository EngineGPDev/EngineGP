<?php
if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

// Проверка прав
if (isset($url['rights']) && $url['rights']) {
    $sql->query('SELECT `rights` FROM `owners` WHERE `id`="' . sys::int($url['rights']) . '" AND `server`="' . $id . '" LIMIT 1');

    if (!$sql->num())
        sys::outjs(array('e' => 'Совладелец не найден.'));

    $owner = $sql->get();

    $aRights = sys::b64djs($owner['rights']);

    $rights = '';

    foreach ($aOwnersI as $access => $info)
        if ($aRights[$access]) $rights .= $info . ', ';

    sys::outjs(array('s' => substr($rights, 0, -2)));
}

// Удаление совладельца
if (isset($url['delete']) && $url['delete']) {
    $sql->query('SELECT `rights` FROM `owners` WHERE `id`="' . sys::int($url['delete']) . '" AND `server`="' . $id . '" LIMIT 1');

    if ($sql->num())
        $sql->query('DELETE FROM `owners` WHERE `id`="' . sys::int($url['delete']) . '" AND `server`="' . $id . '" LIMIT 1');

    sys::back($cfg['http'] . 'servers/id/' . $id . '/section/owners');
}

// Добавление совладельца
if ($go) {
    $nmch = sys::rep_act('server_owners_go_' . $id, 5);

    $aData = (isset($_POST['owner']) && is_array($_POST['owner'])) ? $_POST['owner'] : array();

    $aDate = isset($aData['\'time\'']) ? explode('.', $aData['\'time\'']) : explode('.', date('d.m.Y', $start_point));
    $aTime = explode(':', date('H:i:s', $start_point));

    if (!isset($aDate[1], $aDate[0], $aDate[2]) || !checkdate($aDate[1], $aDate[0], $aDate[2]))
        sys::outjs(array('e' => 'Дата доступа указана неверно.'), $nmch);

    $time = mktime($aTime[0], $aTime[1], $aTime[2], $aDate[1], $aDate[0], $aDate[2]) + 3600;

    if ($time < $start_point)
        sys::outjs(array('e' => 'Время доступа не может быть меньше 60 минут.'), $nmch);

    // Проверка пользователя
    if (!isset($aData['\'user\'']))
        sys::outjs(array('e' => 'Необходимо указать пользователя.'), $nmch);

    if (is_numeric($aData['\'user\'']))
        $sql->query('SELECT `id` FROM `users` WHERE `id`="' . $aData['\'user\''] . '" LIMIT 1');
    else {
        if (sys::valid($aData['\'user\''], 'other', $aValid['login']))
            sys::outjs(array('e' => sys::text('input', 'login_valid')), $nmch);

        $sql->query('SELECT `id` FROM `users` WHERE `login`="' . $aData['\'user\''] . '" LIMIT 1');
    }

    if (!$sql->num())
        sys::outjs(array('e' => 'Пользователь не найден в базе.'), $nmch);

    $uowner = $sql->get();

    if ($server['user'] == $uowner['id'])
        sys::outjs(array('e' => 'Владельца сервера нельзя добавить в совладельцы.'), $nmch);

    $owner = $sql->query('SELECT `id` FROM `owners` WHERE `server`="' . $id . '" AND `user`="' . $uowner['id'] . '" LIMIT 1');

    $upd = $sql->num($owner);

    // Если не обновление доступа совладельца, проверить кол-во
    if (!$upd) {
        $sql->query('SELECT `id` FROM `owners` WHERE `server`="' . $id . '" AND `time`>"' . $start_point . '" LIMIT 5');

        if ($sql->num() == 5)
            sys::outjs(array('e' => 'Вы добавили максимально кол-во совладельцев.'), $nmch);
    }

    $aRights = array();

    $check = 0;

    foreach ($aOwners[$server['game']] as $access) {
        $aRights[$access] = isset($aData['\'' . $access . '\'']) ? 1 : 0;

        $check += $aRights[$access];
    }

    if (!$check)
        sys::outjs(array('e' => 'Необходимо включить минимум одно разрешение.'), $nmch);

    if ($upd)
        $sql->query('UPDATE `owners` set `rights`="' . sys::b64js($aRights) . '", `time`="' . $time . '" WHERE `server`="' . $id . '" AND `user`="' . $uowner['id'] . '" LIMIT 1');
    else
        $sql->query('INSERT INTO `owners` set `server`="' . $id . '", `user`="' . $uowner['id'] . '", `rights`="' . sys::b64js($aRights) . '", `time`="' . $time . '"');

    $sql->query('DELETE FROM `owners` WHERE `server`="' . $id . '" AND `time`<"' . $start_point . '" LIMIT 5');

    sys::outjs(array('s' => 'ok'), $nmch);
}

$html->nav($server['address'], $cfg['http'] . 'servers/id/' . $id);
$html->nav('Друзья');

$owners = $sql->query('SELECT `id`, `user`, `rights`, `time` FROM `owners` WHERE `server`="' . $id . '" AND `time`>"' . $start_point . '" ORDER BY `id` ASC LIMIT 5');

if ($sql->num())
    require(LIB . 'games/games.php');

while ($owner = $sql->get($owners)) {
    $sql->query('SELECT `login` FROM `users` WHERE `id`="' . $owner['user'] . '" LIMIT 1');
    if (!$sql->num())
        continue;

    $uowner = $sql->get();

    $rights = games::owners(sys::b64djs($owner['rights']));

    $html->get('owners', 'sections/servers/games/owners');
    $html->set('id', $id);
    $html->set('oid', $owner['id']);
    $html->set('user', $uowner['login']);
    $html->set('rights', $rights);
    $html->set('time', date('d.m.Y - H:i', $owner['time']));
    $html->pack('owners');
}

foreach ($aOwnersI as $access => $info) {
    $html->get('access', 'sections/servers/games/owners');
    $html->set('access', $access);
    $html->set('info', $info);
    $html->pack('access');
}

$html->get('index', 'sections/servers/games/owners');
$html->set('id', $id);
$html->set('time', date('d.m.Y', $start_point));
$html->set('access', $html->arr['access']);
$html->set('owners', isset($html->arr['owners']) ? $html->arr['owners'] : 'Для данного сервера совладельцы отсутсвуют.');
$html->pack('main');

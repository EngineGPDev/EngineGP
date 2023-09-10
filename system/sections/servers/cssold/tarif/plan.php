<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

if (!isset($nmch))
    $nmch = false;

$plan = $url['plan'] ?? sys::outjs(['e' => 'Переданые не все данные'], $nmch);

$aPrice = sys::b64djs($tarif['price']);

// Проверка плана
if (!array_key_exists($plan, $aPrice))
    sys::outjs(['e' => 'Переданы неверные данные'], $nmch);

[$tickrate, $fps] = explode('_', (string) $plan);

if ($tickrate == $server['tickrate'] and $fps == $server['fps'])
    sys::outjs(['e' => 'Смысла в этой операции нет'], $nmch);

if (!tarif::price($tarif['price']))
    sys::outjs(['e' => 'Чтобы изменить тариф, перейдите в настройки запуска'], $nmch);

if ($server['time'] < $start_point + 86400)
    $time = $server['time'];
else {
    // Цена за 1 день (по новому тарифному плану)
    $price = $aPrice[$plan] / 30 * $server['slots'];

    // Цена аренды за остаток дней
    $price_old = $aPrice[$server['tickrate'] . '_' . $server['fps']] / 30 * $server['slots'];

    // Остаток дней аренды
    $days = ($server['time'] - $start_point) / 86400;

    $time = date('H:i:s', $server['time']);
    $date = date('d.m.Y', round($start_point + $days * $price_old / $price * 86400 - 86400));

    $aDate = explode('.', $date);
    $aTime = explode(':', $time);

    $time = mktime($aTime[0], $aTime[1], $aTime[2], $aDate[1], $aDate[0], $aDate[2]);
}

// Выполнение смена тарифного плана
if ($go) {
    sys::benefitblock($id, $nmch);

    $sql->query('UPDATE `servers` set `time`="' . $time . '", `fps`="' . $fps . '", `tickrate`="' . $tickrate . '" WHERE `id`="' . $id . '" LIMIT 1');

    if (in_array($server['status'], ['working', 'start', 'restart', 'change'])) {
        require(LIB . 'games/' . $server['game'] . '/action.php');

        action::start($id, 'restart');
    }

    // Запись логов
    $sql->query('INSERT INTO `logs_sys` set `user`="' . $user['id'] . '", `server`="' . $id . '", `text`="' . sys::text('syslogs', 'change_plan') . '", `time`="' . $start_point . '"');

    sys::outjs(['s' => 'ok'], $nmch);
}

// Выхлоп информации
sys::outjs(['s' => date('d.m.Y - H:i', $time) . ' (' . sys::date('min', $time) . ')'], $nmch);

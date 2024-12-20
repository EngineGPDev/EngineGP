<?php

/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @copyright Copyright (c) 2018-present Solovev Sergei <inbox@seansolovev.ru>
 *
 * @link      https://github.com/EngineGPDev/EngineGP for the canonical source repository
 *
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE MIT License
 */

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

include(DATA . 'boost.php');

if ($go) {
    $aData = [];

    $aData['site'] = $url['site'] ?? sys::outjs(['e' => 'Необходимо указать сервис.']);

    // Проверка сервиса
    if (!in_array($aData['site'], $aBoost[$server['game']]['boost'])) {
        sys::outjs(['e' => 'Указанный сервис по раскрутке не найден.']);
    }

    if (isset($url['rating'])) {
        $rating = $url['rating'] == 'up' ? '1' : '-1';

        $sql->query('SELECT `id` FROM `boost_rating` WHERE `boost`="' . $aData['site'] . '" AND `user`="' . $user['id'] . '" AND `rating`="' . $rating . '" LIMIT 1');
        if ($sql->num()) {
            sys::out('err');
        }

        $sql->query('DELETE FROM `boost_rating` WHERE `boost`="' . $aData['site'] . '" AND `user`="' . $user['id'] . '" LIMIT 1');
        $sql->query('INSERT INTO `boost_rating` set `boost`="' . $aData['site'] . '", `rating`="' . $rating . '", `user`="' . $user['id'] . '"');

        $sql->query('SELECT SUM(`rating`) FROM `boost_rating` WHERE `boost`="' . $aData['site'] . '"');
        $sum = $sql->get();

        $rating = (int)$sum['SUM(`rating`)'];

        sys::out($rating, 'server_boost_' . $id);
    }

    $aData['service'] = isset($url['service']) ? sys::int($url['service']) : sys::outjs(['e' => 'Необходимо указать номер услуги.']);

    // Проверка номера услуги
    if (!in_array($aData['service'], $aBoost[$server['game']][$aData['site']]['services'])) {
        sys::outjs(['e' => 'Неправильно указан номер услуги.']);
    }

    // Определение суммы
    $sum = $aBoost[$server['game']][$aData['site']]['price'][$aData['service']];

    // Проверка баланса
    if ($user['balance'] < $sum) {
        sys::outjs(['e' => 'У вас не хватает ' . (round($sum - $user['balance'], 2)) . ' ' . $cfg['currency']], $name_mcache);
    }

    include(LIB . 'games/boost.php');

    $boost = new boost($aBoost[$server['game']][$aData['site']]['key'], $aBoost[$server['game']][$aData['site']]['api']);

    $buy = $boost->$aBoost[$server['game']][$aData['site']]['type'](['period' => $aData['service'], 'address' => $server['address']]);

    if (is_array($buy)) {
        sys::outjs(['e' => $buy['error']]);
    }

    // Списание средств с баланса пользователя
    $sql->query('UPDATE `users` set `balance`="' . ($user['balance'] - $sum) . '" WHERE `id`="' . $user['id'] . '" LIMIT 1');

    include(LIB . 'games/games.php');

    // Реф. система
    games::part($user['id'], $sum);

    $sql->query('INSERT INTO `logs` set `user`="' . $user['id'] . '", `text`="' . sys::updtext(
        sys::text('logs', 'buy_boost'),
        ['circles' => $aBoost[$server['game']][$aData['site']]['circles'][$aData['service']],
                'money' => $sum, 'site' => $aBoost[$server['game']][$aData['site']]['site'], 'id' => $id]
    ) . '", `date`="' . $start_point . '", `type`="boost", `money`="' . $sum . '"');

    $sql->query('INSERT INTO `boost` set `user`="' . $user['id'] . '", `server`="' . $id . '", `site`="' . $aData['site'] . '", `circles`="' . $aBoost[$server['game']][$aData['site']]['circles'][$aData['service']] . '", `money`="' . $sum . '", `date`="' . $start_point . '"');

    sys::outjs(['s' => 'ok'], $name_mcache);
}

$html->nav($server['address'], $cfg['http'] . 'servers/id/' . $id);
$html->nav('Раскрутка');

if ($mcache->get('server_boost_' . $id) != '') {
    $html->arr['main'] = $mcache->get('server_boost_' . $id);
} else {
    $html->get('boost', 'sections/servers/' . $server['game']);

    $html->set('id', $id);
    $html->set('address', $server['address']);

    foreach ($aBoost[$server['game']]['boost'] as $boost) {
        $sql->query('SELECT SUM(`rating`) FROM `boost_rating` WHERE `boost`="' . $boost . '"');
        $sum = $sql->get();

        $rating = (int)$sum['SUM(`rating`)'];

        $html->set($boost, $rating);
    }

    $html->pack('main');

    $mcache->set('server_boost_' . $id, $html->arr['main'], false, 4);
}

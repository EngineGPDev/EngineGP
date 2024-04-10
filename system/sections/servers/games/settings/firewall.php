<?php
/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @copyright Copyright (c) 2018-present Solovev Sergei <inbox@seansolovev.ru>
 *
 * @link      https://github.com/EngineGPDev/EngineGP for the canonical source repository
 * @link      https://gitforge.ru/EngineGP/EngineGP for the canonical source repository
 *
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE MIT License
 * @license   https://gitforge.ru/EngineGP/EngineGP/src/branch/main/LICENSE MIT License
 */

if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

$html->nav('Блокировка на оборудовании');

if (isset($url['action'])) {
    include(LIB . 'games/games.php');

    // Получение информации адреса
    if ($url['action'] == 'info')
        games::iptables_whois($nmch);

    // Добавление / удаление правил
    if ($go && in_array($url['action'], array('block', 'unblock'))) {
        $address = isset($_POST['address']) ? trim($_POST['address']) : sys::outjs(array('e' => sys::text('servers', 'firewall')), $nmch);
        $snw = isset($_POST['subnetwork']) ? true : false;

        sys::outjs(games::iptables($id, $url['action'], $address, explode(':', $server['address']), $server['unit'], $snw), $nmch);
    }
}

$sql->query('SELECT `id`, `sip` FROM `firewall` WHERE `server`="' . $id . '" ORDER BY `id` ASC');

while ($firewall = $sql->get()) {
    $html->get('list', 'sections/servers/games/settings/firewall');
    $html->set('id', $firewall['id']);
    $html->set('address', $firewall['sip']);
    $html->pack('firewall');
}

$html->get('firewall', 'sections/servers/games/settings');
$html->set('id', $id);
$html->set('firewall', isset($html->arr['firewall']) ? $html->arr['firewall'] : '');
$html->pack('main');

<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

$html->nav('Блокировка на оборудовании');

if (isset($url['action'])) {
    require(LIB . 'games/games.php');

    // Получение информации адреса
    if ($url['action'] == 'info')
        games::iptables_whois($nmch);

    // Добавление / удаление правил
    if ($go && in_array($url['action'], ['block', 'unblock'])) {
        $address = isset($_POST['address']) ? trim((string) $_POST['address']) : sys::outjs(['e' => sys::text('servers', 'firewall')], $nmch);
        $snw = isset($_POST['subnetwork']) ? true : false;

        sys::outjs(ctrl::iptables($sid, $url['action'], $address, explode(':', (string) $server['address']), $id, $snw), $nmch);
    }
}

$sql->query('SELECT `id`, `sip` FROM `control_firewall` WHERE `server`="' . $sid . '" ORDER BY `id` ASC');
while ($firewall = $sql->get()) {
    $html->get('list', 'sections/control/servers/games/settings/firewall');
    $html->set('id', $firewall['id']);
    $html->set('address', $firewall['sip']);
    $html->pack('firewall');
}

$html->get('firewall', 'sections/control/servers/games/settings');
$html->set('id', $id);
$html->set('server', $sid);
$html->set('firewall', $html->arr['firewall'] ?? '');
$html->pack('main');

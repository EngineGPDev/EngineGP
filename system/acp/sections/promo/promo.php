<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT * FROM `promo` WHERE `id`="'.$id.'" LIMIT 1');
	$promo = $sql->get();

	if($go)
	{
		$aData = array();

		$aData['cod'] = isset($_POST['cod']) ? trim($_POST['cod']) : $promo['cod'];
		$aData['value'] = isset($_POST['value']) ? trim($_POST['value']) : $promo['value'];
		$aData['discount'] = isset($_POST['discount']) ? sys::int($_POST['discount']) : $promo['discount'];
		$aData['hits'] = isset($_POST['hits']) ? sys::int($_POST['hits']) : $promo['hits'];
		$aData['use'] = isset($_POST['use']) ? sys::int($_POST['use']) : $promo['use'];
		$aData['extend'] = isset($_POST['extend']) ? sys::int($_POST['extend']) : $promo['extend'];
		$aData['user'] = isset($_POST['user']) ? sys::int($_POST['user']) : $promo['user'];
		$aData['server'] = isset($_POST['server']) ? sys::int($_POST['server']) : $promo['server'];
		$aData['time'] = isset($_POST['time']) ? trim($_POST['time']) : date('d/m/Y H:i', $promo['time']);
		$aData['data'] = isset($_POST['data']) ? trim($_POST['data']) : $promo['data'];

		$aData['time'] = sys::checkdate($aData['time']);

		if(sys::valid($aData['cod'], 'promo'))
			sys::outjs(array('e' => 'Неправильный формат промо-кода'));

		$sql->query('SELECT `id` FROM `promo` WHERE `id`!="'.$id.'" AND `cod`="'.$aData['cod'].'" AND `tarif`="'.$promo['tarif'].'" LIMIT 1');
		if($sql->num())
			sys::outjs(array('e' => 'Указанный код используется в другой акции'));

		if($aData['user'])
		{
			$sql->query('SELECT `id` FROM `users` WHERE `id`="'.$aData['user'].'" LIMIT 1');
			if(!$sql->num())
				sys::outjs(array('e' => 'Указанный пользователь не найден'));
		}else
			$aData['user'] = 0;

		if($aData['server'])
		{
			$sql->query('SELECT `id` FROM `servers` WHERE `id`="'.$aData['server'].'" LIMIT 1');
			if(!$sql->num())
				sys::outjs(array('e' => 'Указанный сервер не найден'));
		}else
			$aData['server'] = 0;

		if($aData['discount'])
			$proc = strpos($aData['value'], '%') ? '%' : '';

		$aData['value'] = sys::int($aData['value']).$proc;

		$sql->query('UPDATE `promo` set '
			.'`cod`="'.$aData['cod'].'",'
			.'`value`="'.$aData['value'].'",'
			.'`discount`="'.$aData['discount'].'",'
			.'`data`="'.base64_encode('{'.$aData['data'].'}').'",'
			.'`hits`="'.$aData['hits'].'",'
			.'`use`="'.$aData['use'].'",'
			.'`extend`="'.$aData['extend'].'",'
			.'`user`="'.$aData['user'].'",'
			.'`server`="'.$aData['server'].'",'
			.'`time`="'.$aData['time'].'" WHERE `id`="'.$id.'" LIMIT 1');

		sys::outjs(array('s' => 'ok'));
	}

	$sql->query('SELECT `id`, `unit`, `name`, `game` FROM `tarifs` WHERE `id`="'.$promo['tarif'].'" LIMIT 1');
	$tarif = $sql->get();

	$sql->query('SELECT `id`, `name` FROM `units` WHERE `id`="'.$tarif['unit'].'" LIMIT 1');
	$unit = $sql->get();

	$html->get('promo', 'sections/promo');

		$html->set('id', $promo['id']);
		$html->set('cod', $promo['cod']);
		$html->set('value', $promo['value']);
		$html->set('data', str_replace(array('{', '}'), '', base64_decode($promo['data'])));
		$html->set('hits', $promo['hits']);
		$html->set('use', $promo['use']);
		$html->set('user', $promo['user']);
		$html->set('server', $promo['server']);
		$html->set('time', date('d/m/Y H:i', $promo['time']));

		$html->set('discount', $promo['discount'] ? '<option value="1">Скидка</option><option value="0">Подарочные дни</option>' : '<option value="0">Подарочные дни</option><option value="1">Скидка</option>');
		$html->set('extend', $promo['extend'] ? '<option value="1">Для продления</option><option value="0">Для аренды</option>' : '<option value="0">Для аренды</option><option value="1">Для продления</option>');

		$html->set('tarif', $unit['name'].' / #'.$tarif['id'].' '.$tarif['name'].' ('.strtoupper($tarif['game']).')');

	$html->pack('main');
?>
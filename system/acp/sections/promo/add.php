<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if($go)
	{
		$aData = array();

		$aData['cod'] = isset($_POST['cod']) ? trim($_POST['cod']) : '';
		$aData['value'] = isset($_POST['value']) ? trim($_POST['value']) : '';
		$aData['discount'] = isset($_POST['discount']) ? sys::int($_POST['discount']) : 0;
		$aData['hits'] = isset($_POST['hits']) ? sys::int($_POST['hits']) : '';
		$aData['use'] = isset($_POST['use']) ? sys::int($_POST['use']) : '';
		$aData['extend'] = isset($_POST['extend']) ? sys::int($_POST['extend']) : 0;
		$aData['user'] = isset($_POST['user']) ? sys::int($_POST['user']) : '';
		$aData['server'] = isset($_POST['server']) ? sys::int($_POST['server']) : '';
		$aData['time'] = isset($_POST['time']) ? trim($_POST['time']) : '';
		$aData['data'] = isset($_POST['data']) ? trim($_POST['data']) : '';
		$aData['tarifs'] = isset($_POST['tarifs']) ? $_POST['tarifs'] : '';

		$aData['time'] = sys::checkdate($aData['time']);

		if(sys::valid($aData['cod'], 'promo'))
			sys::outjs(array('e' => 'Неправильный формат промо-кода'));

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

		if(!is_array($aData['tarifs']) || !count($aData['tarifs']))
			sys::outjs(array('e' => 'Необходимо указать минимум один тариф'));

		if($aData['discount'])
			$proc = strpos($aData['value'], '%') ? '%' : '';

		$aData['value'] = sys::int($aData['value']).$proc;

		foreach($aData['tarifs'] as $id => $on)
		{
			$sql->query('SELECT `id` FROM `promo` WHERE `cod`="'.$aData['cod'].'" AND `tarif`="'.$id.'" LIMIT 1');
			if($sql->num())
				continue;

			$sql->query('INSERT INTO `promo` set '
				.'`cod`="'.$aData['cod'].'",'
				.'`value`="'.$aData['value'].'",'
				.'`discount`="'.$aData['discount'].'",'
				.'`data`="'.base64_encode('{'.$aData['data'].'}').'",'
				.'`hits`="'.$aData['hits'].'",'
				.'`use`="'.$aData['use'].'",'
				.'`extend`="'.$aData['extend'].'",'
				.'`tarif`="'.$id.'",'
				.'`user`="'.$aData['user'].'",'
				.'`server`="'.$aData['server'].'",'
				.'`time`="'.$aData['time'].'"');
		}
		
		sys::outjs(array('s' => 'ok'));
	}

	$tarifs = '';

	$units = $sql->query('SELECT `id`, `name` FROM `units` ORDER BY `id` ASC');
	while($unit = $sql->get($units))
	{
		$sql->query('SELECT `id`, `name`, `game` FROM `tarifs` WHERE `unit`="'.$unit['id'].'" ORDER BY `id` ASC');
		while($tarif = $sql->get())
			$tarifs .= '<label> '.$unit['name'].' / #'.$tarif['id'].' '.$tarif['name'].' ('.strtoupper($tarif['game']).') <input type="checkbox" name="tarifs['.$tarif['id'].']"></label>';
	}

	$html->get('add', 'sections/promo');

		$html->set('tarifs', $tarifs);

	$html->pack('main');
?>
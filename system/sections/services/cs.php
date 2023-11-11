<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	include(LIB.'games/games.php');

	// Обработка заказа
	if($go)
	{
		// Проверка на авторизацию
		sys::noauth();

		if($mcache->get('buy_server'))
			sleep(1.5);

		$mcache->set('buy_server', true, false, 3);

		include(LIB.'games/'.$section.'/service.php');

		// Входные данные
		$aData = array(
			'unit' => isset($_POST['unit']) ? sys::int($_POST['unit']) : 0,
			'tarif' => isset($_POST['tarif']) ? sys::int($_POST['tarif']) : 0,
			'pack' => isset($_POST['pack']) ? $_POST['pack'] : '',
			'fps' => isset($_POST['fps']) ? sys::int($_POST['fps']) : 0,
			'slots' => isset($_POST['slots']) ? sys::int($_POST['slots']) : 0,
			'time' => isset($_POST['time']) ? sys::int($_POST['time']) : 30,
			'test' => (isset($_POST['time']) AND $_POST['time'] == 'test') ? true : false,
			'promo' => isset($_POST['promo']) ? $_POST['promo'] : false,
		);

		// Массвив данных
		$aSDATA = service::buy($aData);

		// Процесс выдачи игрового сервера
		$id = service::install($aSDATA);

		sys::outjs(array('s' => 'ok', 'id' => $id));
	}

	include(LIB.'games/services.php');

	$check = false;

	// Проверка наличия доступной локации
	$sql->query(services::unit($section));
	if($sql->num())
	{
		// Выбранная локация
		if(isset($url['get']) AND in_array($url['get'], array('tarifs', 'data')))
			$sql->query('SELECT `id`, `test` FROM `units` WHERE `id`="'.$id.'" LIMIT 1');

		$select_unit = $sql->get();

		// Генерация списка локаций
		$units = services::units($section);

		// Генерация списка тарифов
		$tarifs = services::tarifs($section, $select_unit['id']);

		if(isset($url['get']) AND in_array($url['get'], array('price', 'promo')))
		{
			$aGet = array(
				'tarif' => sys::int($url['tarif']),
				'fps' => sys::int($url['fps']),
				'slots' => sys::int($url['slots']),
				'time' => sys::int($url['time']),
				'user' => $user['id']
			);

			$sql->query('SELECT `price`, `fps`, `discount` FROM `tarifs` WHERE `id`="'.$aGet['tarif'].'" LIMIT 1');
			$tarif = $sql->get();

			$aPrice = explode(':', $tarif['price']);

			// Определение цены
			$price = $aPrice[array_search($aGet['fps'], explode(':', $tarif['fps']))];

			// Выхлоп цены за выбранные параметры
			if($url['get'] == 'price')
			{
				// Если выбран тестовый период
				if($url['time'] == 'test')
					sys::outjs(array('sum' => 0));

				sys::outjs(array(
					'sum' => games::define_sum($tarif['discount'], $price, $aGet['slots'], $aGet['time'])
				));
			}

			// Выхлоп цены с учетом промо-кода
			if($url['get'] == 'promo')
				games::define_promo(
					$url['cod'],
					$aGet,
					$tarif['discount'],
					games::define_sum($tarif['discount'], $price, $aGet['slots'], $aGet['time'])
				);
		}

		// Генерация сборок/слот/периодов
		if(isset($url['get']) AND $url['get'] == 'data')
			$sql->query('SELECT `id`, `name`, `price`, `slots_min`, `slots_max`, `packs`, `fps`, `time`, `test`, `discount` FROM `tarifs` WHERE `id`="'.sys::int($url['tarif']).'" LIMIT 1');
		else
			$sql->query('SELECT `id`, `name`, `price`, `slots_min`, `slots_max`, `packs`, `fps`, `time`, `test`, `discount` FROM `tarifs` WHERE `game`="'.$section.'" AND `unit`="'.$select_unit['id'].'" AND `show`="1" ORDER BY `sort` ASC LIMIT 1');

		if($sql->num())
		{
			$select_tarif = $sql->get();

			$aTarif = games::parse_tarif($select_tarif, $select_unit);

			if(isset($url['get']))
			{
				// Выхлоп при выборе локации
				if($url['get'] == 'tarifs')
					sys::outjs(array_merge(array('tarifs' => $tarifs), $aTarif));

				// Выхлоп при выборе тарифа
				if($url['get'] == 'data')
					sys::outjs($aTarif);
			}

			$html->get($section, 'sections/services/games');
				$html->set('units', $units);
				$html->set('tarifs', $tarifs);
				$html->set('packs', $aTarif['packs']);
				$html->set('fps', $aTarif['fps']);
				$html->set('slots', $aTarif['slots']);
				$html->set('time', $aTarif['time']);
				$html->set('cur', $cfg['currency']);
				$html->set('date', date('d.m.Y', $start_point));

				if($cfg['settlement_period'])
					$html->unit('settlement_period', true, true);
				else
					$html->unit('settlement_period', false, true);

				$html->unit('informer', false, true);
			$html->pack('main');

			$check = true;
		}
	}

	if(!$check)
	{
		$html->get($section, 'sections/services/games');
			$html->unit('informer', true, true);
		$html->pack('main');
	}
?>
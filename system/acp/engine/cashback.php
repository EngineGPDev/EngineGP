<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	@ini_set('display_errors', TRUE);
	@ini_set('html_errors', TRUE);
	@ini_set('error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE ^ E_STRICT);

	$nmc = 'cashback_'.$id;

	// Проверка сессии
	if($mcache->get($nmc))
		sys::outjs(array('e' => $text['mcache']), $nmc);

	// Создание сессии
	$mcache->set($nmc, 1, false, 10);

	if($id)
	{
		$sql->query('SELECT `user`, `money`, `purse`, `status` FROM `cashback` WHERE `id`="'.$id.'" LIMIT 1');
		$cb = $sql->get();

		if(!$cb['status'])
			sys::outjs(array('e' => 'Данная заявка уже была обработана'), $nmc);

		$purse = $cb['purse']{0} == 'R' ?  'webmoney' : 'qiwi';

		// Запрос на шлюз
		if($cfg['part_gateway'] == 'unitpay')
		{
			$sum = $cb['money']-($cb['money']/100*$cfg['part_output_proc']);

			$json = file_get_contents('https://unitpay.ru/api?method=massPayment&params[sum]='.$sum.'&params[purse]='.$cb['purse'].'&params[login]='.$cfg['unitpay_mail'].'&params[transactionId]='.$id.' &params[secretKey]='.$cfg['unitpay_api'].'&params[paymentType]='.$purse);

			$array = json_decode($json, true);

			// Упешный вывод средств
			if(is_array($array) AND isset($array['result']) AND in_array($array['result']['status'], array('success', 'not_completed ')))
			{
				$sql->query('UPDATE `cashback` set `status`="0" WHERE `id`="'.$id.'" LIMIT 1');
				$sql->query('INSERT INTO `logs` set `user`="'.$cb['user'].'", `text`="'.sys::updtext(sys::text('logs', 'cashback'),
				array('purse' => $purse, 'money' => $cb['money'])).'", `date`="'.$start_point.'", `type`="cashback", `money`="'.$cb['money'].'"');

				sys::outjs(array('s' => 'Запрос на вывод средств был успешно выполнен'), $nmc);
			}

			if(!is_array($array))
				sys::outjs(array('e' => 'Неудалось выполнить запрос'), $nmc);

			sys::outjs(array('e' => $array['error']['message']), $nmc);
		}

		$sql->query('UPDATE `cashback` set `status`="0" WHERE `id`="'.$id.'" LIMIT 1');
		$sql->query('INSERT INTO `logs` set `user`="'.$cb['user'].'", `text`="'.sys::updtext(sys::text('logs', 'cashback'),
			array('purse' => $purse, 'money' => $cb['money'])).'", `date`="'.$start_point.'", `type`="cashback", `money`="'.$cb['money'].'"');

		sys::outjs(array('s' => 'Запрос на вывод средств был успешно выполнен в ручном режиме'), $nmc);
	}

	sys::outjs(array('e' => 'Не передан идентификатор заявки'), $nmc);
?>
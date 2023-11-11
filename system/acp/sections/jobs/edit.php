<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT * FROM `jobs` WHERE `id`="'.$id.'" LIMIT 1');
	if(!$sql->num())
		header('Location: '.$cfg['http'].'acp/jobs');
	$jobs = $sql->get();

	if($go)
	{	
		$aData = [];

		$data = ['name', 'job', 'desc', 'status'];
		foreach($data as $idata)
			$aData[$idata] = isset($_POST[$idata]) ? $_POST[$idata] : '';

		if(in_array('', $aData))
			sys::outjs(array('e' => 'Необходимо заполнить все поля!'));
		
		$sql->query('UPDATE `jobs` set'
			.'`name`="'.$aData['name'].'",'
			.'`job`="'.$aData['job'].'",'
			.'`desc`="'.$aData['desc'].'",'
			.'`status`="'.$aData['status'].'",'
			.'`date`="'.$start_point.'"');

		sys::outjs(array('s' => 'ok'));
	}

	$html->get('edit', 'sections/jobs');
		$status = $jobs['status'] ? '<option value="1">Доступна</option><option value="0">Недоступна</option>' : '<option value="0">Недоступна</option><option value="1">Доступна</option>';
		$html->set('status', $status);

		$data = ['id', 'name', 'job', 'desc'];
		foreach($data as $idata)
			$html->set($idata, $jobs[$idata]);
	$html->pack('main');
?>
<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if($id)
	{
		$sql->query('SELECT * FROM `jobs_app` WHERE `id`="'.$id.'" LIMIT 1');
		$jobs_app = $sql->get();

		if($go)
		{
			$aData = [];

			$data = ['user', 'text', 'contact', 'job'];
			foreach($data as $idata)
				$aData[$idata] = isset($_POST[$idata]) ? $_POST[$idata] : '';

			$sql->query('UPDATE `jobs_app` set'
				.'`user`="'.$aData['user'].'",'
				.'`text`="'.$aData['text'].'",'
				.'`contact`="'.$aData['contact'].'",'
				.'`job`="'.$aData['job'].'"');

			sys::outjs(array('s' => 'ok'));
		}

		$html->get('request_edit', 'sections/jobs');
			$data = ['id', 'user', 'text', 'contact', 'job'];
			foreach($data as $idata)
				$html->set($idata, $jobs_app[$idata]);
		$html->pack('main');
	}else{
		$sql->query('SELECT * FROM `jobs_app` ORDER BY `id` ASC');
		while($jobs = $sql->get())
		{
			$status = [
				'1' => 'Доступна',
				'0' => 'Недоступна'
			];

			$list .= '<tr>';
				$list .= '<td>'.$jobs['id'].'</td>';
				$list .= '<td><a href="[acp]users/id/'.$jobs['user'].'">user_'.$jobs['user'].'</a></td>';
				$list .= '<td>'.sys::strlen($jobs['text']) > 0 ? '<td>'.$jobs['text'].'</td>' : '<td><a href="[acp]jobs/section/request/id/'.$jobs['id'].'#text">Ответить</a></td>'.'</td>';
				$list .= '<td>'.$jobs['contact'].'</td>';
				$list .= '<td><a href="[acp]jobs/edit/section/id/'.$jobs['job'].'">job_'.$jobs['job'].'</a></td>';
				$list .= '<td>'.sys::today($jobs['date']).'</td>';
				$list .= '<td><div class="text-red" style="cursor: pointer;" onclick="del(\''.$jobs['id'].'\', \'confirm\')">удалить</div></td>';
				$list .= '<td><a href="[acp]jobs/section/request/id/'.$jobs['id'].'" class="green">Изменить</a></td>';
			$list .= '</tr>';
			
			if(isset($url['del']))
			{
				$sql->query('DELETE FROM `jobs_app` WHERE `id`="'.$url['del'].'" LIMIT 1');
				sys::outjs(array('s' => 'ok'));

			}
		}

		$html->get('request', 'sections/jobs');
			$html->set('list', $list);
		$html->pack('main');
	}
?>
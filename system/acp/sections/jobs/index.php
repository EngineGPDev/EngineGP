<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if($url['edit']){
		include(SEC.'jobs/edit.php');
	}else{
		$sql->query('SELECT * FROM `jobs` ORDER BY `id` ASC');
		while($jobs = $sql->get())
		{
			$status = [
				'1' => 'Доступна',
				'0' => 'Недоступна'
			];

			$list .= '<tr>';
				$list .= '<td>'.$jobs['id'].'</td>';
				$list .= '<td>'.$jobs['name'].'</td>';
				$list .= '<td>'.$jobs['job'].'</td>';
				$list .= '<td>'.$jobs['desc'].'</td>';
				$list .= '<td>'.$status[$jobs['status']].'</td>';
				$list .= '<td>'.sys::today($jobs['date']).'</td>';
				$list .= '<td><div class="text-red" style="cursor: pointer;" onclick="del(\''.$jobs['id'].'\', \'confirm\')">удалить</div></td>';
				$list .= '<td><a href="[acp]jobs/edit/section/id/'.$jobs['id'].'" class="green">Изменить</a></td>';
			$list .= '</tr>';
		}

		if(isset($url['del']))
		{
			$sql->query('DELETE FROM `jobs` WHERE `id`="'.$url['del'].'" LIMIT 1');
			sys::outjs(array('s' => 'ok'));
		}

		$html->get('index', 'sections/jobs');
			$html->set('list', $list);
		$html->pack('main');
	}
?>
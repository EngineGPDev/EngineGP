<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if($id)
		include(SEC.'units/unit.php');
	else{
		$list = '';

		$sql->query('SELECT `id`, `name`, `address`, `show`, `domain` FROM `units` ORDER BY `id` ASC');
		while($unit = $sql->get())
		{
			$list .= '<tr>';
				$list .= '<td>'.$unit['id'].'</td>';
				$list .= '<td><a href="'.$cfg['http'].'acp/units/id/'.$unit['id'].'">'.$unit['name'].'</a></td>';
				$list .= '<td>'.$unit['address'].'</td>';
				$list .= '<td>'.($unit['show'] == '1' ? 'Доступна' : 'Недоступна').'</td>';
				$list .= '<td>'.$unit['domain'].'</td>';
				$list .= '<td><a href="#" onclick="return units_delete(\''.$unit['id'].'\')" class="text-red">Удалить</a></td>';
			$list .= '</tr>';
		}

		$html->get('index', 'sections/units');

			$html->set('list', $list);

		$html->pack('main');
	}
?>
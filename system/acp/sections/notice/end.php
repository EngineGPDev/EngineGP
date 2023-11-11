<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if(isset($url['subsection']) AND $url['subsection'] == 'search')
		include(SEC.'notice/search.php');

	if($id)
		include(SEC.'notice/notice.php');
	else{
		$list = '';

		$sql->query('SELECT `id` FROM `notice`');

		$aPage = sys::page($page, $sql->num(), 20);

		sys::page_gen($aPage['ceil'], $page, $aPage['page'], 'acp/notice');

		$notices = $sql->query('SELECT `id`, `unit`, `server`, `text`, `time` FROM `notice` WHERE `time`<"'.$start_point.'" ORDER BY `id` ASC LIMIT '.$aPage['num'].', 20');
		while($notice = $sql->get($notices))
		{
			if($notice['unit'])
			{
				$sql->query('SELECT `name` FROM `units` WHERE `id`="'.$notice['unit'].'" LIMIT 1');
				$unit = $sql->get();

				$name = $unit['name'];
			}else
				$name = '<a href="'.$cfg['http'].'acp/servers/id/'.$notice['server'].'">SERVER_'.$notice['server'].'</a>';

			$list .= '<tr>';
				$list .= '<td>'.$notice['id'].'</td>';
				$list .= '<td class="w50p">Адресовано: '.$name.'</td>';
				$list .= '<td>Завершен: '.date('d.m.Y - H:i:s', $notice['time']).'</td>';
				$list .= '<td class="text-center"><a href="'.$cfg['http'].'acp/notice/id/'.$notice['id'].'">Редактировать</a></td>';
				$list .= '<td class="text-center"><a href="#" onclick="return notice_delete(\''.$notice['id'].'\')" class="text-red">Удалить</a></td>';
			$list .= '</tr>';

			$list .= '<tr>';
				$list .= '<td colspan="5">'.$notice['text'].'</td>';
			$list .= '</tr>';
		}

		$html->get('index', 'sections/notice');

			$html->set('list', $list);

			$html->set('pages', isset($html->arr['pages']) ? $html->arr['pages'] : '');

		$html->pack('main');
	}
?>
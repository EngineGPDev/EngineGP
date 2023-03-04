<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if($go)
	{
		$sql->query('SELECT `key` FROM `graph` WHERE `server`="'.$id.'" LIMIT 1');
		$graph = $sql->get();

		$nmch = 'server_graph_full_'.$id;

		$time = isset($url['time']) ? $url['time'] : 'day';

		if(!in_array($time, array('day', 'week', 'month')))
			$time = 'day';

		// Выхлоп кеш графика
		if($mcache->get($nmch) AND file_exists(TEMP.(md5($graph['key'].'full_'.$time)).'.png'))
		{
			header('Content-type: image/png');
			exit(file_get_contents(TEMP.(md5($graph['key'].'full_'.$time)).'.png'));
		}

		include(LIB.'games/graph/pData.php');
		include(LIB.'games/graph/pDraw.php');
		include(LIB.'games/graph/pImage.php');

		include(LIB.'games/graph.php');

		graph::full($id, $server['slots_start'], $graph['key'], $time);

		$mcache->set($nmch, true, false, 300);

		header('Content-type: image/png');
		exit(file_get_contents(TEMP.(md5($graph['key'].'full_'.$time)).'.png'));
	}

	$html->nav($server['address'], $cfg['http'].'servers/id/'.$id);
    $html->nav('Графики');

    if($mcache->get('server_graph_'.$id) != '')
        $html->arr['main'] = $mcache->get('server_graph_'.$id);
    else{
		$sql->query('SELECT `key` FROM `graph` WHERE `server`="'.$id.'" LIMIT 1');

		// Если отсутствует ключ, создать
		if(!$sql->num())
		{
			// Генерация ключа
			$key = md5($id.sys::key('graph'));

			$sql->query('INSERT INTO `graph` set `server`="'.$id.'", `key`="'.$key.'", `time`="0"');
		}else{
			$graph = $sql->get();

			$key = $graph['key'];
		}

        $html->get('graph', 'sections/servers/games');

            $html->set('id', $id);

            $html->set('key', $key);
            $html->set('address', $server['address']);
            $html->set('_img', '[img]');

        $html->pack('main');

        $mcache->set('server_graph_'.$id, $html->arr['main'], false, 4);
    }
?>
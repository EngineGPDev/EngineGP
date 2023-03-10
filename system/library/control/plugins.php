<?php
    if(!DEFINED('EGP'))
        exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

    class plugins
    {
        public static function images($images, $plugin)
        {
            global $html;

            if(empty($images)) return NULL;

            if(isset($html->arr['images']))
                unset($html->arr['images']);

            $aImg = explode("\n", $images);

            foreach($aImg as $img)
            {
                $html->get('plugin_images', 'sections/control/servers/games/plugins');

                    $html->set('id', $plugin);
                    $html->set('img', $img);

                $html->pack('images');
            }

            return isset($html->arr['images']) ? $html->arr['images'] : '';
        }

        public static function status($status)
        {
            global $html;

            if(!$status)
            {
                $html->unit('unstable');
                $html->unit('stable', 1);
                $html->unit('testing');
            }elseif($status == 2){
                $html->unit('unstable');
                $html->unit('stable');
                $html->unit('testing', 1);
            }else{
                $html->unit('unstable', 1);
                $html->unit('stable');
                $html->unit('testing');
            }

            return NULL;
        }

        public static function required($id, $required, $choice, $mcache)
        {
            global $sql;

            if($required == '')
                return NULL;

            $aRequi = explode(':', $required);

            foreach($aRequi as $pl)
            {
                $sql->query('SELECT `id` FROM `control_plugins_install` WHERE `server`="'.$sid.'" AND `plugin`="'.$pl.'" LIMIT 1');
                if(!$sql->num())
                {
                    $sql->query('SELECT `name` FROM `plugins` WHERE `id`="'.$pl.'" LIMIT 1');
                    $plRequi = $sql->get();

                    if($choice != '')
                    {
                        $aChoice = explode(' ', $choice);

                        foreach($aChoice as $plugins)
                        {
                            $aPlugins = explode(':', $plugins);

                            if(in_array($pl, $aPlugins))
                            {
                                $options = '';
                                foreach($aPlugins as $plugin)
                                {
                                    $sql->query('SELECT `name`, `upd` FROM `plugins` WHERE `id`="'.$plugin.'" LIMIT 1');
                                    $data = $sql->get();

                                    if($data['upd'])
                                    {
                                        $sql->query('SELECT `name` FROM `plugins_update` WHERE `plugin`="'.$plugin.'" ORDER BY `id` DESC LIMIT 1');
                                        $data = $sql->get();
                                    }

                                    $options .= '<option value="'.$plugin.'">'.strip_tags($data['name']).'</option>';
                                }

                                if($options != '')
                                    sys::outjs(array('e' => '?????? ?????????????? ?????????????? ?????????????????? ?????????????????? ???????????? ???? ????????????????', 'required' => true, 'pid' => $pl, 'select' => $options), $mcache);
                            }
                        }
                    }

                    sys::outjs(array('e' => '?????? ?????????????? ?????????????? ?????????????????? ?????????????????? ????????????????', 'required' => true, 'pid' => $pl, 'pname' => htmlspecialchars_decode($plRequi['name'])), $mcache);
                }
            }

            return NULL;
        }

        public static function incompatible($id, $incompatible, $mcache)
        {
            global $sql;

            if($incompatible == '')
                return NULL;

            $aIncomp = explode(':', $incompatible);

            foreach($aIncomp as $pl)
            {
                $sql->query('SELECT `id` FROM `control_plugins_install` WHERE `server`="'.$sid.'" AND `plugin`="'.$pl.'" LIMIT 1');
                if($sql->num())
                {
                    $sql->query('SELECT `name` FROM `plugins` WHERE `id`="'.$pl.'" LIMIT 1');
                    $plIncomp = $sql->get();

                    sys::outjs(array('e' => '???????????? ???????????? ?????????????????????? ?? ?????? ?????????????????????????? ????????????????', 'pid' => $pl, 'pname' => htmlspecialchars_decode($plIncomp['name'])), $mcache);
                }
            }

            return NULL;
        }

        public static function clear($clear, $uid, $dir)
        {
            global $ssh;

            // ???????? ???????????????????? ??????????????????
            if(isset($clear['regex']) AND $clear['regex'])
            {
                $file = preg_replace($clear['text'], '', $ssh->get('sudo -u server'.$uid.' cat '.$dir.$clear['file']));

                // ?????????????????? ????????
                $temp = sys::temp($file);

                $ssh->setfile($temp, $dir.$clear['file'], 0644);

                unlink($temp);

                $query = 'chown server'.$uid.':servers '.$dir.$clear['file'].';';

            }else
                // ???????????????? ???????????? ???? ??????????
                $query = 'sudo -u server'.$uid.' sed -i '."'s/".str_replace('/', '\/', htmlspecialchars_decode($clear['text']))."//g'".' '.$dir.$clear['file'].';';

            $ssh->set($query.'sudo -u server'.$uid.' sed -i '."'/./!d'".' '.$dir.$clear['file']);

            return NULL;
        }

        public static function write($write, $uid, $dir)
        {
            global $ssh;

            // ?????????????? (???????????????? ???????????? ???????????? ???? ???????????? ????????????)
            $query = 'sudo -u server'.$uid.' echo "" >> '.$dir.$write['file'].';';

            // ?????????????????? ????????????????????????, ?????????? ???????????????? ???????????????????????? ????????????
            $query .= 'sudo -u server'.$uid.' sed -i '."'s/".str_replace('/', '\/', htmlspecialchars_decode($write['text']))."//g'".' '.$dir.$write['file'].';';

            // ???????????????????? ???????????? ?? ???????????? ??????????
            if($write['top'])
                $query .= 'sudo -u server'.$uid.' touch '.$dir.$write['file'].'; sudo -u server'.$uid.' sed -i '."'1i ".str_replace(array('/', "'", '\"'), array('\/', "\'", '"'), htmlspecialchars_decode($write['text']))."'".' '.$dir.$write['file'].';';
            else
                // ???????????????????? ???????????? ?? ?????????? ??????????
                $query .= 'sudo -u server'.$uid.' touch '.$dir.$write['file'].'; sudo -u server'.$uid.' echo "'.str_replace('"', '\"', htmlspecialchars_decode($write['text'])).'" >> '.$dir.$write['file'].';';

            $ssh->set($query.'sudo -u server'.$uid.' sed -i '."'/./!d'".' '.$dir.$clear['file']);

            return NULL;
        }
    }
?>
<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class html
	{
		var $dir = TPL;
		var $template = null;
		var $data = array();
		var $unitblock = array();

		public function set($name, $var, $unset = false)
		{
			$this->data['['.$name.']'] = $var;

			if($unset)
				unset($this->arr[$name]);

			return NULL;
		}

		public function unit($name, $var = false, $mirror = false)
		{
			$block = str_replace($name, "'\\|".$name."\\|(.*?)\\|_".$name."\\|'si", $name);

			$var = $var ? '\\1' : '';

			$this->unitblock[$block] = $var;

			if($mirror)
			{
				$block = str_replace($name, "'\\|!".$name."\\|(.*?)\\|_!".$name."\\|'si", $name);

				$var = !$var ? '\\1' : '';

				$this->unitblock[$block] = $var;
			}

			return NULL;
		}

		public function nav($name, $link = false)
		{
			$this->get('nav');
				if($link)
				{
					$this->set('link', $link);
					$this->unit('link', 1, 1);
				}else
					$this->unit('link', 0, 1);
				$this->set('name', $name);
			$this->pack('nav');

			return NULL;
		}

		public function get($name, $path = '')
		{
			global $device, $cfg;

			$path_root = $device == '!mobile' ? '' : 'megp/';

			$path = $path_root.$path;

			if($path != '')
				$name = str_replace('//', '/', $path.'/'.$name);

			if(!file_exists($this->dir.'/'.$name.'.html'))
			{
				$route = explode('/', $name);
				$namefile = end($route);
				$dir = $this->dir.str_replace($namefile, '', $name);

				die('Error: html file <u>'.$namefile.'.html</u> not found in: <u>'.$dir.'</u>');
			}

			$this->template = file_get_contents($this->dir.'/'.$name.'.html');
			$this->select_template = $this->template;

			return NULL;
		}

		private function delete()
		{
			unset($this->data);
			unset($this->unitblock);

			$this->select_template = $this->template;

			return NULL;
		}

		public function pack($compile)
		{
			if(isset($this->unitblock))
			{
				$find_preg = array();
				$replace_preg = array();

				foreach($this->unitblock as $key_find => $key_replace)
				{
					$find_preg[] = $key_find;
					$replace_preg[] = $key_replace;
				}

				$this->select_template = preg_replace($find_preg, $replace_preg, $this->select_template);
			}

			$find = array();
			$replace = array();

			if(isset($this->data))
			{
				foreach($this->data as $key_find => $key_replace)
				{
					$find[] = $key_find;
					$replace[] = $key_replace;
				}
			}

			$this->select_template = str_replace($find, $replace, $this->select_template);

			if(isset($this->arr[$compile]))
				$this->arr[$compile] .= $this->select_template;
			else
				$this->arr[$compile] = $this->select_template;

			$this->delete();

			return NULL;
		}

		public function upd($old = array(), $new = array(), $name)
		{
			$this->arr[$name] = str_replace($old, $new, $this->arr[$name]);

			return NULL;
		}

		public function unitall($arr = array(), $name, $var = false, $mirror = false)
		{
			$block = str_replace($name, "'\\|".$name."\\|(.*?)\\|_".$name."\\|'si", $name);

			$var = $var ? '\\1' : '';

			$this->unitblock[$block] = $var;

			if($mirror)
			{
				$block = str_replace($name, "'\\|!".$name."\\|(.*?)\\|_!".$name."\\|'si", $name);

				$var = !$var ? '\\1' : '';

				$this->unitblock[$block] = $var;
			}

			if(count($this->unitblock))
			{
				foreach($this->unitblock as $key_find => $key_replace)
				{
					$find_preg[] = $key_find;
					$replace_preg[] = $key_replace;
				}

				$this->arr[$arr] = preg_replace($find_preg, $replace_preg, $this->arr[$arr]);
			}

			return NULL;
		}
	}

	$html = new html;
?>
<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

class html
{
    public $dir = TPL;
    public $template = null;
    public $data = [];
    public $unitblock = [];
    public $arr;
    public $select_template;

    public function set($name, $var, $unset = false)
    {
        $this->data['[' . $name . ']'] = $var;

        if ($unset)
            unset($this->arr[$name]);

        return NULL;
    }

    public function unit($name, $var = false, $mirror = false)
    {
        $block = str_replace($name, "'\\|" . $name . "\\|(.*?)\\|_" . $name . "\\|'si", (string) $name);

        $var = $var ? '\\1' : '';

        $this->unitblock[$block] = $var;

        if ($mirror) {
            $block = str_replace($name, "'\\|!" . $name . "\\|(.*?)\\|_!" . $name . "\\|'si", (string) $name);

            $var = !$var ? '\\1' : '';

            $this->unitblock[$block] = $var;
        }

        return NULL;
    }

    public function nav($name, $link = false)
    {
        $this->get('nav');
        if ($link) {
            $this->set('link', $link);
            $this->unit('link', 1, 1);
        } else
            $this->unit('link', 0, 1);
        $this->set('name', $name);
        $this->pack('nav');

        return NULL;
    }

    public function get($name, $path = '')
    {
        global $cfg;

        if ($path != '')
            $name = str_replace('//', '/', $path . '/' . $name);

        $this->template = file_get_contents($this->dir . '/' . $name . '.html');
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
        if (isset($this->unitblock)) {
            $find_preg = [];
            $replace_preg = [];

            foreach ($this->unitblock as $key_find => $key_replace) {
                $find_preg[] = $key_find;
                $replace_preg[] = $key_replace;
            }

            $this->select_template = preg_replace($find_preg, $replace_preg, (string) $this->select_template);
        }

        $find = [];
        $replace = [];

        if (isset($this->data)) {
            foreach ($this->data as $key_find => $key_replace) {
                $find[] = $key_find;
                $replace[] = $key_replace;
            }
        }

        $this->select_template = str_replace($find, $replace, (string) $this->select_template);

        if (isset($this->arr[$compile]))
            $this->arr[$compile] .= $this->select_template;
        else
            $this->arr[$compile] = $this->select_template;

        $this->delete();

        return NULL;
    }

    public function upd($name, $old = [], $new = [])
    {
        $this->arr[$name] = str_replace($old, $new, (string) $this->arr[$name]);

        return NULL;
    }

    public function unitall($name, $arr = [], $var = false, $mirror = false)
    {
        $block = str_replace($name, "'\\|" . $name . "\\|(.*?)\\|_" . $name . "\\|'si", (string) $name);

        $var = $var ? '\\1' : '';

        $this->unitblock[$block] = $var;

        if ($mirror) {
            $block = str_replace($name, "'\\|!" . $name . "\\|(.*?)\\|_!" . $name . "\\|'si", (string) $name);

            $var = !$var ? '\\1' : '';

            $this->unitblock[$block] = $var;
        }

        if (count($this->unitblock)) {
            foreach ($this->unitblock as $key_find => $key_replace) {
                $find_preg[] = $key_find;
                $replace_preg[] = $key_replace;
            }

            $this->arr[$arr] = preg_replace($find_preg, $replace_preg, (string) $this->arr[$arr]);
        }

        return NULL;
    }
}

$html = new html;

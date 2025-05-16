<?php

/*
 * Copyright 2018-2025 Solovev Sergei
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace EngineGP\View;

class Html
{
    public $dir = TPL;
    public $template = null;
    public $select_template = null;
    public $arr = [];
    public $data = [];
    public $unitblock = [];

    public function set($name, $var, $unset = false)
    {
        $this->data['[' . $name . ']'] = $var;

        if ($unset) {
            unset($this->arr[$name]);
        }

        return null;
    }

    public function unit($name, $var = false, $mirror = false)
    {
        $block = str_replace($name, "'\\|" . $name . "\\|(.*?)\\|_" . $name . "\\|'si", $name);

        $var = $var ? '\\1' : '';

        $this->unitblock[$block] = $var;

        if ($mirror) {
            $block = str_replace($name, "'\\|!" . $name . "\\|(.*?)\\|_!" . $name . "\\|'si", $name);

            $var = !$var ? '\\1' : '';

            $this->unitblock[$block] = $var;
        }

        return null;
    }

    public function nav($name, $link = false)
    {
        $this->get('nav');
        if ($link) {
            $this->set('link', $link);
            $this->unit('link', 1, 1);
        } else {
            $this->unit('link', 0, 1);
        }
        $this->set('name', $name);
        $this->pack('nav');

        return null;
    }

    public function get($name, $path = '')
    {
        global $cfg;

        if ($path != '') {
            $name = str_replace('//', '/', $path . '/' . $name);
        }

        if (!file_exists($this->dir . '/' . $name . '.html')) {
            $route = explode('/', $name);
            $namefile = end($route);
            $dir = $this->dir . str_replace($namefile, '', $name);

            die('Error: html file <u>' . $namefile . '.html</u> not found in: <u>' . $dir . '</u>');
        }

        $this->template = file_get_contents($this->dir . '/' . $name . '.html');
        $this->select_template = $this->template;

        return null;
    }

    private function delete()
    {
        unset($this->data);
        unset($this->unitblock);

        $this->select_template = $this->template;

        return null;
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

            $this->select_template = preg_replace($find_preg, $replace_preg, $this->select_template);
        }

        $find = [];
        $replace = [];

        if (isset($this->data)) {
            foreach ($this->data as $key_find => $key_replace) {
                $find[] = $key_find;
                $replace[] = $key_replace;
            }
        }

        $this->select_template = str_replace($find, $replace, $this->select_template);

        if (isset($this->arr[$compile])) {
            $this->arr[$compile] .= $this->select_template;
        } else {
            $this->arr[$compile] = $this->select_template;
        }

        $this->delete();

        return null;
    }

    public function upd($name, $old = [], $new = [])
    {
        $this->arr[$name] = str_replace($old, $new, $this->arr[$name]);

        return null;
    }

    public function unitall($name, $arr = [], $var = false, $mirror = false)
    {
        $block = str_replace($name, "'\\|" . $name . "\\|(.*?)\\|_" . $name . "\\|'si", $name);

        $var = $var ? '\\1' : '';

        $this->unitblock[$block] = $var;

        if ($mirror) {
            $block = str_replace($name, "'\\|!" . $name . "\\|(.*?)\\|_!" . $name . "\\|'si", $name);

            $var = !$var ? '\\1' : '';

            $this->unitblock[$block] = $var;
        }

        if (count($this->unitblock)) {
            foreach ($this->unitblock as $key_find => $key_replace) {
                $find_preg[] = $key_find;
                $replace_preg[] = $key_replace;
            }

            $this->arr[$arr] = preg_replace($find_preg, $replace_preg, $this->arr[$arr]);
        }

        return null;
    }
}

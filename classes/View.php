<?php

/**
 * Copyright 2017 Christoph M. Becker
 *
 * This file is part of Uploader_XH.
 *
 * Uploader_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Upload_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with Uploader_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Uploader;

class View
{
    /** @var string */
    private $templateFolder;

    /** @var array<string,string> */
    private $lang;

    /**
     * @var array
     */
    private $data = array();

    /** @param array<string,string> $lang */
    public function __construct(string $templateFolder, array $lang)
    {
        $this->templateFolder = $templateFolder;
        $this->lang = $lang;
    }

    /**
     * @param string $name
     * @return string
     */
    public function __get($name)
    {
        return $this->data[$name];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * @param string $name
     * @return string
     */
    public function __call($name, array $args)
    {
        return $this->escape($this->data[$name]);
    }

    /**
     * @param string $key
     * @return string
     */
    protected function text($key)
    {
        $args = func_get_args();
        array_shift($args);
        return $this->escape(vsprintf($this->lang[$key], $args));
    }

    /**
     * @param string $key
     * @param int $count
     */
    protected function plural($key, $count)
    {
        if ($count == 0) {
            $key .= '_0';
        } else {
            $key .= XH_numberSuffix($count);
        }
        $args = func_get_args();
        array_shift($args);
        return $this->escape(vsprintf($this->lang[$key], $args));
    }

    /**
     * @param array<string,mixed> $_data
     * @return void
     */
    public function render(string $_template, array $_data)
    {
        $this->data = $_data;
        include "{$this->templateFolder}{$_template}.php";
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    protected function escape($value)
    {
        if ($value instanceof HtmlString) {
            return $value;
        } else {
            return XH_hsc($value);
        }
    }
}

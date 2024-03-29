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

    /** @param array<string,string> $lang */
    public function __construct(string $templateFolder, array $lang)
    {
        $this->templateFolder = $templateFolder;
        $this->lang = $lang;
    }

    /**
     * @param string $key
     * @param mixed $args
     * @return string
     */
    public function text($key, ...$args)
    {
        return $this->escape(vsprintf($this->lang[$key], $args));
    }

    /**
     * @param array<string,mixed> $_data
     * @return string
     */
    public function render(string $_template, array $_data)
    {
        extract($_data);
        ob_start();
        include "{$this->templateFolder}{$_template}.php";
        return (string) ob_get_clean();
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function escape($value)
    {
        if ($value instanceof HtmlString) {
            return $value;
        } else {
            return XH_hsc($value);
        }
    }

    /** @param mixed $value */
    public function json($value): string
    {
        return (string) json_encode($value, JSON_HEX_APOS | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}

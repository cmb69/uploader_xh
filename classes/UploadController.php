<?php

/**
 * Copyright 2011-2017 Christoph M. Becker
 *
 * This file is part of Uploader_XH.
 *
 * Uploader_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Uploader_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Uploader_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Uploader;

class UploadController
{
    /**
     * @param string $params A query string.
     * @param string $anchor A fragment identifier.
     * @return string (X)HTML.
     */
    protected function renderTypeSelect($params)
    {
        global $pth, $plugin_tx;

        $o = '<select id="uploader-type" title="'
            . $plugin_tx['uploader']['label_type'] . '" data-url="'
            . $this->getSelectOnchangeUrl('type', $params) . '">'
            . "\n";
        foreach ($this->getTypes() as $type) {
            if (isset($pth['folder'][$type])) {
                $sel = $type == $this->getType() ? ' selected="selected"' : '';
                $o .= '<option value="' . $type . '"' . $sel . '>' . $type
                    . '</option>' . "\n";
            }
        }
        $o .= '</select>' . "\n";
        return $o;
    }

    /**
     * @param string $params A query string.
     * @param string $anchor A fragment identifier.
     * @return string (X)HTML.
     */
    protected function renderSubdirSelect($params)
    {
        global $plugin_tx;

        return '<select id="uploader-subdir" title="'
            . $plugin_tx['uploader']['label_subdir'] . '"'
            . ' data-url="' . $this->getSelectOnchangeUrl('subdir', $params) . '">' . "\n"
            . '<option>/</option>' . "\n"
            . $this->renderSubdirSelectRec('')
            . '</select>' . "\n";
    }

    /**
     * @param string $parent A parent folder.
     * @return string (X)HTML.
     */
    protected function renderSubdirSelectRec($parent)
    {
        global $pth;

        $o = '';
        $dn = $pth['folder'][$this->getType()] . $parent;
        if (($dh = opendir($dn)) !== false) {
            while (($fn = readdir($dh)) !== false) {
                if (strpos($fn, '.') !== 0
                    && is_dir($pth['folder'][$this->getType()] . $parent . $fn)
                ) {
                    $dir = $parent . $fn . '/';
                    $sel = ($dir == $this->getSubfolder())
                        ? ' selected="selected"'
                        : '';
                    $o .= '<option value="' . $dir . '"' . $sel . '>' . $dir
                        . '</option>' . "\n";
                    $o .= $this->renderSubdirSelectRec($dir);
                }
            }
            closedir($dh);
        } else {
            e('cntopen', 'folder', $dn);
        }
        return $o;
    }

    /**
     * @param string $params A query string.
     * @param string $anchor A fragment identifier.
     * @return string (X)HTML.
     */
    protected function renderResizeSelect($params)
    {
        global $plugin_tx;

        $o = '<select id="uploader-resize" title="'
            . $plugin_tx['uploader']['label_resize'] . '"'
            . ' data-url="' . $this->getSelectOnchangeUrl('resize', $params) . '">' . "\n";
        foreach ($this->getSizes() as $size) {
            $sel = $size == $this->getResizeMode() ? ' selected="selected"' : '';
            $o .= '<option value="' . $size . '"' . $sel . '>' . $size . '</option>'
                . "\n";
        }
        $o .= '</select>' . "\n";
        return $o;
    }

    protected function getSelectOnchangeUrl($param, $params)
    {
        global $sn;

        $url = $sn . '?' . $params;
        if ($param != 'type') {
            $url .= '&amp;uploader_type=' . urlencode($this->getType());
        }
        if ($param != 'subdir') {
            $url .= '&amp;uploader_subdir=' . urlencode($this->getSubfolder());
        }
        if ($param != 'resize') {
            $url .= '&amp;uploader_resize=' . urlencode($this->getResizeMode());
        }
        $url .= '&amp;uploader_' . $param . '=';
        return $url;
    }

    /**
     * @return string
     */
    protected function getType()
    {
        global $pth;

        if (isset($_GET['uploader_type'])
            && in_array($_GET['uploader_type'], $this->getTypes())
            && isset($pth['folder'][$_GET['uploader_type']])
        ) {
            return $_GET['uploader_type'];
        } else {
            return 'images';
        }
    }

    /**
     * @return array
     */
    protected function getTypes()
    {
        return array('images', 'downloads', 'media', 'userfiles');
    }

    /**
     * @return string
     */
    protected function getSubfolder()
    {
        global $pth;

        $subdir = isset($_GET['uploader_subdir'])
            ? preg_replace('/\.\.[\/\\\\]?/', '', stsl($_GET['uploader_subdir']))
            : '';
        if (isset($_GET['uploader_subdir'])
            && is_dir($pth['folder'][$this->getType()] . $subdir)
        ) {
            return $subdir;
        } else {
            return '';
        }
    }

    /**
     * @return string
     */
    protected function getResizeMode()
    {
        global $plugin_cf;

        if (isset($_GET['uploader_resize'])
            && in_array($_GET['uploader_resize'], $this->getSizes())
        ) {
            return $_GET['uploader_resize'];
        } else {
            return $plugin_cf['uploader']['resize_default'];
        }
    }

    /**
     * @return array
     */
    protected function getSizes()
    {
        return array('', 'small', 'medium', 'large');
    }

    protected function appendScript($filename)
    {
        global $bjs;

        $bjs .= '<script type="text/javascript" src="' . XH_hsc($filename) . '"></script>';
    }
}

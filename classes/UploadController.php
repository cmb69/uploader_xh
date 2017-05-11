<?php

/**
 * Copyright 2011-2017 Christoph M. Becker
 *
 * This file is part of Uploader_XH.
 *
 * Uploader_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Uploader_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with Uploader_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Uploader;

class UploadController
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $lang;

    /**
     * @var string
     */
    protected $pluginFolder;

    public function __construct()
    {
        global $pth, $plugin_cf, $plugin_tx;

        $this->config = $plugin_cf['uploader'];
        $this->lang = $plugin_tx['uploader'];
        $this->pluginFolder = "{$pth['folder']['plugins']}uploader/";
    }

    protected function getTypeOptions()
    {
        global $pth;

        $result = [];
        foreach ($this->getTypes() as $type) {
            if (isset($pth['folder'][$type])) {
                $result[$type] = $type === $this->getType() ? 'selected' : '';
            }
        }
        return $result;
    }

    protected function getSubdirOptions($parent = null)
    {
        global $pth;

        $result = [];
        if (!isset($parent)) {
            $result['/'] = '';
        }
        $dn = $pth['folder'][$this->getType()] . $parent;
        if (($dh = opendir($dn)) !== false) {
            while (($fn = readdir($dh)) !== false) {
                if (strpos($fn, '.') !== 0
                    && is_dir($pth['folder'][$this->getType()] . $parent . $fn)
                ) {
                    $dir = $parent . $fn . '/';
                    $result[$dir] = $dir === $this->getSubfolder() ? 'selected' : '';
                    $result = array_merge($result, $this->getSubdirOptions($dir));
                }
            }
            closedir($dh);
        }
        return $result;
    }

    protected function getResizeOptions()
    {
        $result = [];
        foreach ($this->getSizes() as $size) {
            $result[$size] = $size === $this->getResizeMode() ? 'selected' : '';
        }
        return $result;
    }

    protected function getSelectOnchangeUrl()
    {
        global $sn;

        return (new Url($sn, $_GET))
            ->with('uploader_type', $this->getType())
            ->with('uploader_subdir', $this->getSubfolder())
            ->with('uploader_resize', $this->getResizeMode());
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
            ? preg_replace('/\.\.[\/\\\\]?/', '', $_GET['uploader_subdir'])
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
        if (isset($_GET['uploader_resize'])
            && in_array($_GET['uploader_resize'], $this->getSizes())
        ) {
            return $_GET['uploader_resize'];
        } else {
            return $this->config['resize_default'];
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

    protected function getJsonConfig()
    {
        global $sn;

        $type = $this->getType();
        $subdir = $this->getSubfolder();
        $resize = $this->getResizeMode();
        $url = (new Url($sn, $_GET))->with('function', 'uploader_upload')
            ->with('uploader_type', $type)->with('uploader_subdir', $subdir);
        $config = array(
            'url' => (string) $url,
            'filters' => [
                'max_file_size' => $this->config['size_max'],
                'mime_types' => [[
                    'title' => $this->lang['title_' . $type],
                    'extensions' => $this->config['ext_' . $type]
                ]],
            ],
            'flash_swf_url' => "{$this->pluginFolder}lib/Moxie.swf",
            'silverlight_xap_url' => "{$this->pluginFolder}lib/Moxie.xap",
            'file_data_name' => 'uploader_file'
        );
        $config['chunk_size'] = strtolower(ini_get('upload_max_filesize')) . 'b';
        if ($resize != '') {
            $config['resize'] = array(
                'width' => $this->config['resize-' . $resize . '_width'],
                'height' => $this->config['resize-' . $resize . '_height'],
                'quality' => $this->config['resize-' . $resize . '_quality']
            );
        }
        return json_encode($config);
    }

    public function uploadAction()
    {
        global $pth;

        $dir = $pth['folder'][$this->getType()] . $this->getSubfolder();
        $filename = isset($_POST['name']) ? $_POST['name'] : '';
        $chunks = isset($_POST['chunks']) ? $_POST['chunks'] : 0;
        $chunk = isset($_POST['chunk']) ? $_POST['chunk'] : 0;
        $receiver = new Receiver($dir, $filename, $chunks, $chunk);
        if (isset($_FILES['uploader_file']['tmp_name'])
            && is_uploaded_file($_FILES['uploader_file']['tmp_name'])
        ) {
            try {
                $receiver->handleUpload($_FILES['uploader_file']['tmp_name']);
                echo '{"jsonrpc" : "2.0", "result" : null, "id" : "id"}';
            } catch (ReadException $ex) {
                header('HTTP/1.1 500 Internal Server Error');
                echo '{"jsonrpc": "2.0", "error": {"code": 101, "message":'
                    . ' "Failed to open input stream."}, "id" : "id"}';
            } catch (WriteException $ex) {
                header('HTTP/1.1 500 Internal Server Error');
                echo '{"jsonrpc": "2.0", "error": {"code": 102, "message":'
                    . ' "Failed to open output stream."}, "id" : "id"}';
            }
        } else {
            header('HTTP/1.1 400 Bad Request');
            echo '{"jsonrpc": "2.0", "error": {"code": 103, "message":',
                '"Failed to move uploaded file."}, "id" : "id"}';
        }
        exit();
    }
}

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
     * @var string[]
     */
    protected static $types = ['images', 'downloads', 'media', 'userfiles'];

    /**
     * @var string[]
     */
    protected static $sizes = ['', 'small', 'medium', 'large'];

    /**
     * @var int
     */
    protected static $serial = 0;

    /**
     * @var bool
     */
    protected static $hasRequiredScripts = false;

    /**
     * @var array
     */
    protected $config;

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

        self::$serial++;
        $this->config = $plugin_cf['uploader'];
        $this->lang = $plugin_tx['uploader'];
        $this->pluginFolder = "{$pth['folder']['plugins']}uploader/";
    }

    public function defaultAction()
    {
        $this->requireScripts();
        echo '<div class="uploader_placeholder" data-serial="' . self::$serial . '"></div>';
    }

    public function widgetAction()
    {
        if (self::$serial != $_GET['uploader_serial']) {
            return;
        }
        while (ob_get_level()) {
            ob_end_clean();
        }
        $view = new View('widget');
        $selectChangeUrl = $this->getSelectOnchangeUrl();
        $view->typeSelectChangeUrl = $selectChangeUrl->with('uploader_type', 'FIXME');
        $view->typeOptions = $this->getTypeOptions();
        $view->subdirSelectChangeUrl = $selectChangeUrl->with('uploader_subdir', 'FIXME');
        $view->subdirOptions = $this->getSubdirOptions();
        $view->resizeSelectChangeUrl = $selectChangeUrl->with('uploader_resize', 'FIXME');
        $view->resizeOptions = $this->getResizeOptions();
        $view->pluploadConfig = $this->getJsonConfig();
        $view->render();
        exit;
    }

    protected function getTypeOptions()
    {
        global $pth;

        $result = [];
        if (!isset($this->type) || $this->type === '*') {
            foreach (self::$types as $type) {
                if (isset($pth['folder'][$type])) {
                    $result[$type] = $type === $this->getType() ? 'selected' : '';
                }
            }
        }
        return $result;
    }

    protected function getSubdirOptions()
    {
        global $pth;

        $result = array_flip((new FileSystemService)->getSubdirsOf($pth['folder'][$this->getType()]));
        foreach ($result as $dirname => &$selected) {
            $selected = $dirname === $this->getSubfolder() ? 'selected' : '';
        }
        return $result;
    }

    protected function getResizeOptions()
    {
        $result = [];
        if (!isset($this->resize) || $this->resize === '*') {
            foreach (self::$sizes as $size) {
                $result[$size] = $size === $this->getResizeMode() ? 'selected' : '';
            }
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
            && in_array($_GET['uploader_type'], self::$types)
            && isset($pth['folder'][$_GET['uploader_type']])
        ) {
            return $_GET['uploader_type'];
        } else {
            return 'images';
        }
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
            return '/';
        }
    }

    /**
     * @return string
     */
    protected function getResizeMode()
    {
        if (isset($_GET['uploader_resize'])
            && in_array($_GET['uploader_resize'], self::$sizes)
        ) {
            return $_GET['uploader_resize'];
        } else {
            return $this->config['resize_default'];
        }
    }

    protected function requireScripts()
    {
        global $pth;

        if (!self::$hasRequiredScripts) {
            include_once "{$pth['folder']['plugins']}jquery/jquery.inc.php";
            include_jQuery();
            $this->appendScript("{$this->pluginFolder}lib/plupload.full.min.js");
            $this->appendScript("{$this->pluginFolder}uploader.min.js");
            self::$hasRequiredScripts = true;
        }
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
                'max_file_size' => "{$this->config['size_max']}b",
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

        if (self::$serial != $_GET['uploader_serial']) {
            return;
        }
        $dir = $pth['folder'][$this->getType()] . $this->getSubfolder();
        $filename = isset($_POST['name']) ? $_POST['name'] : '';
        $chunks = isset($_POST['chunks']) ? $_POST['chunks'] : 0;
        $chunk = isset($_POST['chunk']) ? $_POST['chunk'] : 0;
        $receiver = new Receiver($dir, $filename, $chunks, $chunk, $this->config['size_max']);
        header('Content-Type: text/plain; charset=UTF-8');
        if (isset($_FILES['uploader_file']['tmp_name'])
            && is_uploaded_file($_FILES['uploader_file']['tmp_name'])
            && $this->isUploadAllowed()
        ) {
            $this->doUpload($receiver);
        } else {
            header('HTTP/1.1 403 Forbidden');
            echo $this->lang['error_forbidden'];
        }
        exit;
    }

    private function doUpload(Receiver $receiver)
    {
        try {
            $receiver->handleUpload($_FILES['uploader_file']['tmp_name']);
            echo $this->lang['label_done'];
        } catch (FilesizeException $ex) {
            header('HTTP/1.1 403 Forbidden');
            echo $this->lang['error_forbidden'];
        } catch (ReadException $ex) {
            header('HTTP/1.1 500 Internal Server Error');
            echo $this->lang['error_read'];
        } catch (WriteException $ex) {
            header('HTTP/1.1 500 Internal Server Error');
            echo $this->lang['error_write'];
        }
    }
}

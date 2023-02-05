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

/**
 * @phpstan-type FileFolders array{images:string,downloads:string,media:string,userfiles:string}
 */
class UploadController
{
    /**
     * @var string[]
     */
    private static $types = ['images', 'downloads', 'media', 'userfiles'];

    /**
     * @var string[]
     */
    private static $sizes = ['', 'small', 'medium', 'large'];

    /**
     * @var int
     */
    private static $serial = 0;

    /**
     * @var bool
     */
    private static $hasRequiredScripts = false;

    /** @var bool */
    private $isUserWidget;

    /**
     * @var array<string,string>
     */
    private $config;

    /**
     * @var array<string,string>
     */
    private $lang;

    /**
     * @var string
     */
    private $pluginFolder;

    /** @var FileFolders */
    private $fileFolders;

    /** @var string */
    private $scriptName;

    /** @var string */
    private $type;

    /** @var string */
    private $subdir;

    /** @var string */
    private $resize;

    /**
     * @param array<string,string> $config
     * @param array<string,string> $lang
     * @param FileFolders $fileFolders
     */
    public function __construct(
        bool $isUserWidget,
        array $config,
        array $lang,
        string $pluginFolder,
        array $fileFolders,
        string $scriptName,
        string $type = "*",
        string $subdir = "*",
        string $resize = "*"
    ) {
        self::$serial++;
        $this->isUserWidget = $isUserWidget;
        $this->config = $config;
        $this->lang = $lang;
        $this->pluginFolder = $pluginFolder;
        $this->fileFolders = $fileFolders;
        $this->scriptName = $scriptName;
        $this->type = $type;
        $this->subdir = $subdir;
        $this->resize = $resize;
    }

    /** @return void */
    public function defaultAction()
    {
        $this->requireScripts();
        echo '<div class="uploader_placeholder" data-serial="' . XH_hsc((string) self::$serial) . '"></div>';
    }

    /** @return void */
    public function widgetAction()
    {
        if (self::$serial != $_GET['uploader_serial']) {
            return;
        }
        while (ob_get_level()) {
            ob_end_clean();
        }
        $view = new View("{$this->pluginFolder}views/", $this->lang);
        $selectChangeUrl = $this->getSelectOnchangeUrl();
        $data = [
            'typeSelectChangeUrl' => $selectChangeUrl->with('uploader_type', 'FIXME'),
            'typeOptions' => $this->getTypeOptions(),
            'subdirSelectChangeUrl' => $selectChangeUrl->with('uploader_subdir', 'FIXME'),
            'subdirOptions' => $this->getSubdirOptions(),
            'resizeSelectChangeUrl' => $selectChangeUrl->with('uploader_resize', 'FIXME'),
            'resizeOptions' => $this->getResizeOptions(),
            'pluploadConfig' => $this->getJsonConfig(),
        ];
        echo $view->render('widget', $data);
        exit;
    }

    /** @return array<string,string> */
    private function getTypeOptions(): array
    {
        $result = [];
        if ($this->type === '*') {
            foreach (self::$types as $type) {
                if (isset($this->fileFolders[$type])) {
                    $result[$type] = $type === $this->getType() ? 'selected' : '';
                }
            }
        }
        return $result;
    }

    /** @return array<string,string> */
    private function getSubdirOptions(): array
    {
        $result = [];
        if ($this->subdir === '*') {
            $subdirs = (new FileSystemService)->getSubdirsOf($this->fileFolders[$this->getType()]);
            foreach ($subdirs as $dirname) {
                $result[$dirname] = $dirname === $this->getSubfolder() ? 'selected' : '';
            }
        }
        return $result;
    }

    /** @return array<string,string> */
    private function getResizeOptions(): array
    {
        $result = [];
        if ($this->resize === '*') {
            foreach (self::$sizes as $size) {
                $result[$size] = $size === $this->getResizeMode() ? 'selected' : '';
            }
        }
        return $result;
    }

    private function getSelectOnchangeUrl(): Url
    {
        return (new Url($this->scriptName, $_GET))
            ->with('uploader_type', $this->getType())
            ->with('uploader_subdir', $this->getSubfolder())
            ->with('uploader_resize', $this->getResizeMode());
    }

    /**
     * @return string
     */
    private function getType()
    {
        if ($this->type !== '*') {
            return $this->type;
        } elseif (isset($_GET['uploader_type'])
            && in_array($_GET['uploader_type'], self::$types)
            && isset($this->fileFolders[$_GET['uploader_type']])
        ) {
            return $_GET['uploader_type'];
        } else {
            return 'images';
        }
    }

    /**
     * @return string
     */
    private function getSubfolder()
    {
        if ($this->subdir !== '*') {
            return $this->subdir;
        }
        $subdir = isset($_GET['uploader_subdir'])
            ? preg_replace('/\.\.[\/\\\\]?/', '', $_GET['uploader_subdir'])
            : '';
        if (isset($_GET['uploader_subdir'])
            && is_dir($this->fileFolders[$this->getType()] . $subdir)
        ) {
            return $subdir;
        } else {
            return '/';
        }
    }

    /**
     * @return string
     */
    private function getResizeMode()
    {
        if ($this->resize !== '*') {
            return $this->resize;
        } elseif (isset($_GET['uploader_resize'])
            && in_array($_GET['uploader_resize'], self::$sizes)
        ) {
            return $_GET['uploader_resize'];
        } else {
            return $this->config['resize_default'];
        }
    }

    /** @return void */
    private function requireScripts()
    {
        if (!self::$hasRequiredScripts) {
            include_once "{$this->pluginFolder}../jquery/jquery.inc.php";
            include_jQuery();
            $this->appendScript("{$this->pluginFolder}lib/plupload.full.min.js");
            $this->appendScript("{$this->pluginFolder}uploader.min.js");
            self::$hasRequiredScripts = true;
        }
    }

    /** @return void */
    private function appendScript(string $filename)
    {
        global $bjs;

        $bjs .= '<script type="text/javascript" src="' . XH_hsc($filename) . '"></script>';
    }

    private function getJsonConfig(): string
    {
        $type = $this->getType();
        $subdir = $this->getSubfolder();
        $resize = $this->getResizeMode();
        $url = (new Url($this->scriptName, $_GET))->with('function', 'uploader_upload')
            ->with('uploader_type', $type)->with('uploader_subdir', $subdir)
            ->with('uploader_resize', $resize);
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
            if (is_array($resize)) { // @phpstan-ignore-line
                $config['resize'] = $resize;
            } else {
                $config['resize'] = array(
                    'width' => $this->config['resize-' . $resize . '_width'],
                    'height' => $this->config['resize-' . $resize . '_height'],
                    'quality' => $this->config['resize-' . $resize . '_quality']
                );
            }
        }
        return json_encode($config);
    }

    /** @return void */
    public function uploadAction()
    {
        if (self::$serial != $_GET['uploader_serial']) {
            return;
        }
        $dir = $this->fileFolders[$this->getType()] . $this->getSubfolder();
        $filename = isset($_POST['name']) ? $_POST['name'] : '';
        $chunks = isset($_POST['chunks']) ? $_POST['chunks'] : 0;
        $chunk = isset($_POST['chunk']) ? $_POST['chunk'] : 0;
        $receiver = new Receiver($dir, $filename, $chunks, $chunk, (int) $this->config['size_max']);
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

    /** @return void */
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

    /** @return bool */
    private function isUploadAllowed()
    {
        if ($this->isUserWidget) {
            return ($this->type === '*' || $this->getType() === $this->type)
                && ($this->subdir === '*' || $this->getSubfolder() === $this->subdir)
                && isset($_POST['name'])
                && $this->isExtensionAllowed($_POST['name'])
                && isset($_FILES['uploader_file']['tmp_name'])
                && filesize($_FILES['uploader_file']['tmp_name']) <= $this->config['size_max'];
        }
        return true;
    }

    /**
     * @param string $filename
     * @return bool
     */
    private function isExtensionAllowed($filename)
    {
        return in_array(
            strtolower(pathinfo($filename, PATHINFO_EXTENSION)),
            explode(',', $this->config['ext_' . $this->getType()])
        );
    }
}

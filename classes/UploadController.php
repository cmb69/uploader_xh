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
    private const TYPES = ['images', 'downloads', 'media', 'userfiles'];

    private const SIZES = ['', 'small', 'medium', 'large'];

    /**
     * @var int
     */
    private $serial = 0;

    /**
     * @var bool
     */
    private $hasRequiredScripts = false;

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

    /** @var Jquery */
    private $jquery;

    /** @var FileSystemService */
    private $fileSystemService;

    /** @var string */
    private $uploadMaxFilesize;

    /**
     * @param array<string,string> $config
     * @param array<string,string> $lang
     * @param FileFolders $fileFolders
     */
    public function __construct(
        array $config,
        array $lang,
        string $pluginFolder,
        array $fileFolders,
        string $scriptName,
        Jquery $jquery,
        FileSystemService $fileSystemService,
        string $uploadMaxFilesize
    ) {
        $this->config = $config;
        $this->lang = $lang;
        $this->pluginFolder = $pluginFolder;
        $this->fileFolders = $fileFolders;
        $this->scriptName = $scriptName;
        $this->jquery = $jquery;
        $this->fileSystemService = $fileSystemService;
        $this->uploadMaxFilesize = $uploadMaxFilesize;
    }

    public function defaultAction(?string $type = null, ?string $subdir = null, ?string $resize = null): Response
    {
        $this->requireScripts();
        return new Response(
            '<div class="uploader_placeholder" data-serial="' . XH_hsc((string) ++$this->serial) . '"></div>'
        );
    }

    public function widgetAction(?string $type = null, ?string $subdir = null, ?string $resize = null): Response
    {
        if (++$this->serial != $_GET['uploader_serial']) {
            return new Response("");
        }
        $view = new View("{$this->pluginFolder}views/", $this->lang);
        $selectChangeUrl = $this->getSelectOnchangeUrl($type, $subdir, $resize);
        $data = [
            'typeSelectChangeUrl' => $selectChangeUrl->with('uploader_type', 'FIXME'),
            'typeOptions' => $this->getTypeOptions($type),
            'subdirSelectChangeUrl' => $selectChangeUrl->with('uploader_subdir', 'FIXME'),
            'subdirOptions' => $this->getSubdirOptions($type, $subdir),
            'resizeSelectChangeUrl' => $selectChangeUrl->with('uploader_resize', 'FIXME'),
            'resizeOptions' => $this->getResizeOptions($resize),
            'pluploadConfig' => $this->getJsonConfig($type, $subdir, $resize),
        ];
        return new Response($view->render('widget', $data), "text/html");
    }

    /** @return array<string,string> */
    private function getTypeOptions(?string $type): array
    {
        $result = [];
        if (!isset($type) || $type === '*') {
            foreach (self::TYPES as $atype) {
                $result[$atype] = $atype === $this->getType($type) ? 'selected' : '';
            }
        }
        return $result;
    }

    /** @return array<string,string> */
    private function getSubdirOptions(?string $type, ?string $subdir): array
    {
        $result = [];
        if (!isset($subdir) || $subdir === '*') {
            $subdirs = $this->fileSystemService->getSubdirsOf($this->fileFolders[$this->getType($type)]);
            foreach ($subdirs as $dirname) {
                $result[$dirname] = $dirname === $this->getSubfolder($type, $subdir) ? 'selected' : '';
            }
        }
        return $result;
    }

    /** @return array<string,string> */
    private function getResizeOptions(?string $resize): array
    {
        $result = [];
        if (!isset($resize) || $resize === '*') {
            foreach (self::SIZES as $size) {
                $result[$size] = $size === $this->getResizeMode($resize) ? 'selected' : '';
            }
        }
        return $result;
    }

    private function getSelectOnchangeUrl(?string $type, ?string $subdir, ?string $redir): Url
    {
        return (new Url($this->scriptName, $_GET))
            ->with('uploader_type', $this->getType($type))
            ->with('uploader_subdir', $this->getSubfolder($type, $subdir))
            ->with('uploader_resize', $this->getResizeMode($redir));
    }

    /**
     * @return string
     */
    private function getType(?string $type)
    {
        if (isset($type) && $type !== '*') {
            return $type;
        } elseif (
            isset($_GET['uploader_type'])
            && in_array($_GET['uploader_type'], self::TYPES)
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
    private function getSubfolder(?string $type, ?string $subdir)
    {
        if (isset($subdir) && $subdir !== '*') {
            return $subdir;
        }
        $subdir = isset($_GET['uploader_subdir'])
            ? preg_replace('/\.\.[\/\\\\]?/', '', $_GET['uploader_subdir'])
            : '';
        if (
            isset($_GET['uploader_subdir'])
            && $this->fileSystemService->isDir($this->fileFolders[$this->getType($type)] . $subdir)
        ) {
            return $subdir;
        } else {
            return '/';
        }
    }

    /**
     * @return string
     */
    private function getResizeMode(?string $resize)
    {
        if (isset($resize) && $resize !== '*') {
            return $resize;
        } elseif (
            isset($_GET['uploader_resize'])
            && in_array($_GET['uploader_resize'], self::SIZES)
        ) {
            return $_GET['uploader_resize'];
        } else {
            return $this->config['resize_default'];
        }
    }

    /** @return void */
    private function requireScripts()
    {
        if (!$this->hasRequiredScripts) {
            $this->jquery->include();
            $this->appendScript("{$this->pluginFolder}lib/plupload.full.min.js");
            $this->appendScript("{$this->pluginFolder}uploader.min.js");
            $this->hasRequiredScripts = true;
        }
    }

    /** @return void */
    private function appendScript(string $filename)
    {
        global $bjs;

        $bjs .= '<script type="text/javascript" src="' . XH_hsc($filename) . '"></script>';
    }

    /** @return mixed */
    private function getJsonConfig(?string $type, ?string $subdir, ?string $resize)
    {
        $type = $this->getType($type);
        $subdir = $this->getSubfolder($type, $subdir);
        $resize = $this->getResizeMode($resize);
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
        $config['chunk_size'] = strtolower($this->uploadMaxFilesize) . 'b';
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
        return $config;
    }

    public function uploadAction(?string $type = null, ?string $subdir = null, ?string $resize = null): Response
    {
        if (++$this->serial != $_GET['uploader_serial']) {
            return new Response("");
        }
        $dir = $this->fileFolders[$this->getType($type)] . $this->getSubfolder($type, $subdir);
        $filename = isset($_POST['name']) ? $_POST['name'] : '';
        $chunks = isset($_POST['chunks']) ? $_POST['chunks'] : 0;
        $chunk = isset($_POST['chunk']) ? $_POST['chunk'] : 0;
        $receiver = new Receiver($dir, $filename, $chunks, $chunk, (int) $this->config['size_max']);
        if (
            isset($_FILES['uploader_file']['tmp_name'])
            && is_uploaded_file($_FILES['uploader_file']['tmp_name'])
            && $this->isUploadAllowed($type, $subdir, $resize)
        ) {
            return $this->doUpload($receiver);
        } else {
            return new Response($this->lang['error_forbidden'], "text/plain", 403);
        }
    }

    private function doUpload(Receiver $receiver): Response
    {
        try {
            $receiver->handleUpload($_FILES['uploader_file']['tmp_name']);
            return new Response($this->lang['label_done'], "text/plain");
        } catch (FilesizeException $ex) {
            return new Response($this->lang['error_forbidden'], "text/plain", 403);
        } catch (ReadException $ex) {
            return new Response($this->lang['error_read'], "text/plain", 500);
        } catch (WriteException $ex) {
            return new Response($this->lang['error_write'], "text/plain", 500);
        }
    }

    /** @return bool */
    private function isUploadAllowed(?string $type, ?string $subdir, ?string $resize)
    {
        if ($type !== null && $subdir !== null && $resize !== null) {
            return ($type === '*' || $this->getType($type) === $type)
                && ($subdir === '*' || $this->getSubfolder($type, $subdir) === $subdir)
                && isset($_POST['name'])
                && $this->isExtensionAllowed($_POST['name'], $type)
                && isset($_FILES['uploader_file']['tmp_name'])
                && filesize($_FILES['uploader_file']['tmp_name']) <= $this->config['size_max'];
        }
        return true;
    }

    /**
     * @param string $filename
     * @return bool
     */
    private function isExtensionAllowed($filename, ?string $type)
    {
        return in_array(
            strtolower(pathinfo($filename, PATHINFO_EXTENSION)),
            explode(',', $this->config['ext_' . $this->getType($type)])
        );
    }
}

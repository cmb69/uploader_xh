<?php

/**
 * Copyright (c) Christoph M. Becker
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

use Plib\Jquery;
use Plib\Request;
use Plib\Response;
use Plib\Url;
use Plib\View;

/**
 * @phpstan-type FileFolders array{images:string,downloads:string,media:string,userfiles:string}
 */
class UploadController
{
    private const TYPES = ['images', 'downloads', 'media', 'userfiles'];

    private const SIZES = ['', 'small', 'medium', 'large'];

    /** @var int */
    private $serial;

    /** @var array<string,string> */
    private $config;

    /** @var string */
    private $pluginFolder;

    /** @var FileFolders */
    private $fileFolders;

    /** @var Jquery */
    private $jquery;

    /** @var FileSystemService */
    private $fileSystemService;

    /** @var Receiver */
    private $receiver;

    /** @var string */
    private $uploadMaxFilesize;

    /** @var View */
    private $view;

    /**
     * @param array<string,string> $config
     * @param FileFolders $fileFolders
     */
    public function __construct(
        int $serial,
        array $config,
        string $pluginFolder,
        array $fileFolders,
        Jquery $jquery,
        FileSystemService $fileSystemService,
        Receiver $receiver,
        string $uploadMaxFilesize,
        View $view
    ) {
        $this->serial = $serial;
        $this->config = $config;
        $this->pluginFolder = $pluginFolder;
        $this->fileFolders = $fileFolders;
        $this->jquery = $jquery;
        $this->fileSystemService = $fileSystemService;
        $this->receiver = $receiver;
        $this->uploadMaxFilesize = $uploadMaxFilesize;
        $this->view = $view;
    }

    public function __invoke(Request $request, ?string $type, ?string $subdir, ?string $resize, bool $admin): Response
    {
        global $function;

        if ($function === 'uploader_upload') {
            return $this->uploadAction($request, $type, $subdir, $resize);
        }
        if ($request->get("uploader_serial") !== null) {
            return $this->widgetAction($request, $type, $subdir, $resize);
        }
        return $this->defaultAction($request, $admin);
    }

    private function defaultAction(Request $request, bool $admin): Response
    {
        $this->jquery->include();
        $uploader = $this->pluginFolder . "uploader.min.js";
        if (!is_file($uploader)) {
            $uploader = $this->pluginFolder . "uploader.js";
        }
        $response = Response::create($this->view->render("main", [
            "admin" => $admin,
            "serial" => $this->serial,
            "plupload" => $request->url()->path($this->pluginFolder . "lib/plupload.full.min.js")
                ->with("v", "2.3.9")->relative(),
            "uploader" => $request->url()->path($uploader)->with("v", "1.0beta2")->relative(),
        ]));
        if ($admin) {
            $response = $response->withTitle("Uploader – " . $this->view->text("menu_main"));
        }
        return $response;
    }

    private function widgetAction(Request $request, ?string $type, ?string $subdir, ?string $resize): Response
    {
        if ($this->serial != $request->get("uploader_serial")) {
            return Response::create();
        }
        $selectChangeUrl = $this->getSelectOnchangeUrl($request, $type, $subdir, $resize);
        $data = [
            'typeSelectChangeUrl' => $selectChangeUrl->with('uploader_type', 'FIXME'),
            'typeOptions' => $this->getTypeOptions($request, $type),
            'subdirSelectChangeUrl' => $selectChangeUrl->with('uploader_subdir', 'FIXME'),
            'subdirOptions' => $this->getSubdirOptions($request, $type, $subdir),
            'resizeSelectChangeUrl' => $selectChangeUrl->with('uploader_resize', 'FIXME'),
            'resizeOptions' => $this->getResizeOptions($request, $resize),
            'pluploadConfig' => $this->getJsonConfig($request, $type, $subdir, $resize),
        ];
        return Response::create($this->view->render('widget', $data))->withContentType("text/html");
    }

    /** @return array<string,string> */
    private function getTypeOptions(Request $request, ?string $type): array
    {
        $result = [];
        if (!isset($type) || $type === '*') {
            foreach (self::TYPES as $atype) {
                $result[$atype] = $atype === $this->getType($request, $type) ? 'selected' : '';
            }
        }
        return $result;
    }

    /** @return array<string,string> */
    private function getSubdirOptions(Request $request, ?string $type, ?string $subdir): array
    {
        $result = [];
        if (!isset($subdir) || $subdir === '*') {
            $subdirs = $this->fileSystemService->getSubdirsOf($this->fileFolders[$this->getType($request, $type)]);
            foreach ($subdirs as $dirname) {
                $result[$dirname] = $dirname === $this->getSubfolder($request, $type, $subdir) ? 'selected' : '';
            }
        }
        return $result;
    }

    /** @return array<string,string> */
    private function getResizeOptions(Request $request, ?string $resize): array
    {
        $result = [];
        if (!isset($resize) || $resize === '*') {
            foreach (self::SIZES as $size) {
                $result[$size] = $size === $this->getResizeMode($request, $resize) ? 'selected' : '';
            }
        }
        return $result;
    }

    private function getSelectOnchangeUrl(Request $request, ?string $type, ?string $subdir, ?string $redir): Url
    {
        return $request->url()
            ->with('uploader_type', $this->getType($request, $type))
            ->with('uploader_subdir', $this->getSubfolder($request, $type, $subdir))
            ->with('uploader_resize', $this->getResizeMode($request, $redir));
    }

    private function getType(Request $request, ?string $type): string
    {
        if ($type !== null && $type !== '*') {
            return $type;
        }
        $type = $request->get("uploader_type") ?? "";
        return in_array($type, self::TYPES, true)
            ? $type
            : "images";
    }

    private function getSubfolder(Request $request, ?string $type, ?string $subdir): string
    {
        if ($subdir !== null && $subdir !== '*') {
            return $subdir;
        }
        $subdir = (string) preg_replace('/\.\.[\/\\\\]?/', "", $request->get("uploader_subdir") ?? "");
        $folder = $this->fileFolders[$this->getType($request, $type)];
        return $this->fileSystemService->isDir($folder . $subdir)
            ? $subdir
            : "/";
    }

    private function getResizeMode(Request $request, ?string $resize): string
    {
        if ($resize !== null && $resize !== '*') {
            return $resize;
        }
        $resize = $request->get("uploader_resize") ?? "";
        return in_array($resize, self::SIZES, true)
            ? $resize
            : $this->config['resize_default'];
    }

    /** @return mixed */
    private function getJsonConfig(Request $request, ?string $type, ?string $subdir, ?string $resize)
    {
        $type = $this->getType($request, $type);
        $subdir = $this->getSubfolder($request, $type, $subdir);
        $resize = $this->getResizeMode($request, $resize);
        $url = $request->url()->with('function', 'uploader_upload')
            ->with('uploader_type', $type)->with('uploader_subdir', $subdir)
            ->with('uploader_resize', $resize);
        $config = array(
            'url' => $url->relative(),
            'filters' => [
                'max_file_size' => "{$this->config['size_max']}b",
                'mime_types' => [[
                    'title' => $this->view->plain("title_" . $type),
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

    private function uploadAction(Request $request, ?string $type, ?string $subdir, ?string $resize): Response
    {
        if ($this->serial != $request->get("uploader_serial")) {
            return Response::create();
        }
        $dir = $this->fileFolders[$this->getType($request, $type)] . $this->getSubfolder($request, $type, $subdir);
        $filename = isset($_POST['name']) ? $_POST['name'] : '';
        $chunks = isset($_POST['chunks']) ? $_POST['chunks'] : 0;
        $chunk = isset($_POST['chunk']) ? $_POST['chunk'] : 0;
        if (
            isset($_FILES['uploader_file']['tmp_name'])
            && is_uploaded_file($_FILES['uploader_file']['tmp_name'])
            && $this->isUploadAllowed($request, $type, $subdir, $resize)
        ) {
            return $this->doUpload(
                $this->receiver,
                $dir,
                $filename,
                $_FILES['uploader_file']['tmp_name'],
                $chunks,
                $chunk
            );
        } else {
            return Response::error(403, $this->view->plain("error_forbidden"));
        }
    }

    private function doUpload(
        Receiver $receiver,
        string $dir,
        string $filename,
        string $tmpName,
        int $chunks,
        int $chunk
    ): Response {
        try {
            $receiver->handleUpload($dir, $filename, $tmpName, $chunks, $chunk);
            return Response::create($this->view->plain("label_done"))->withContentType("text/plain");
        } catch (FilesizeException $ex) {
            return Response::error(403, $this->view->plain("error_forbidden"));
        } catch (ReadException $ex) {
            return Response::error(500, $this->view->plain("error_read"));
        } catch (WriteException $ex) {
            return Response::error(500, $this->view->plain("error_write"));
        }
    }

    private function isUploadAllowed(Request $request, ?string $type, ?string $subdir, ?string $resize): bool
    {
        if ($type !== null && $subdir !== null && $resize !== null) {
            return ($type === '*' || $this->getType($request, $type) === $type)
                && ($subdir === '*' || $this->getSubfolder($request, $type, $subdir) === $subdir)
                && isset($_POST['name'])
                && $this->isExtensionAllowed($request, $_POST['name'], $type)
                && isset($_FILES['uploader_file']['tmp_name'])
                && filesize($_FILES['uploader_file']['tmp_name']) <= $this->config['size_max'];
        }
        return true;
    }

    private function isExtensionAllowed(Request $request, string $filename, ?string $type): bool
    {
        return in_array(
            strtolower(pathinfo($filename, PATHINFO_EXTENSION)),
            explode(',', $this->config['ext_' . $this->getType($request, $type)])
        );
    }
}

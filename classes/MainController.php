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

class MainController extends UploadController
{
    private $type;

    private $subdir;

    private $resize;

    public function __construct($type, $subdir, $resize)
    {
        parent::__construct();
        $this->type = $type;
        $this->subdir = $subdir;
        $this->resize = $resize;
    }

    public function defaultAction()
    {
        global $pth, $su;
        static $run = 0;

        if (!file_exists($pth['folder']['images'] . $this->subdir)) {
            mkdir($pth['folder']['images'] . $this->subdir, 0777, true);
        }
        if (!$run) {
            $this->appendScript("{$this->pluginFolder}lib/plupload.full.min.js");
            $this->appendScript("{$this->pluginFolder}uploader.min.js");
        }
        $view = new View('widget');
        $view->typeSelect = new HtmlString($this->type == '*' ? $this->renderTypeSelect($su) : '');
        $view->subdirSelect = new HtmlString($this->subdir == '*' ? $this->renderSubdirSelect($su) : '');
        $view->resizeSelect = new HtmlString($this->resize == '*' ? $this->renderResizeSelect($su) : '');
        $view->pluploadConfig = new HtmlString($this->getJsonConfig());
        $run++;
        $view->render();
    }
}

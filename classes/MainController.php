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
        global $su;
        static $run = 0;

        if (!$run) {
            $this->appendScript("{$this->pluginFolder}lib/plupload.full.min.js");
            $this->appendScript("{$this->pluginFolder}uploader.min.js");
        }
        $view = new View('widget');
        $view->typeSelectChangeUrl = new HtmlString($this->getSelectOnchangeUrl('type', $su));
        $view->typeOptions = $this->type === '*' ? $this->getTypeOptions() : null;
        $view->subdirSelectChangeUrl = new HtmlString($this->getSelectOnchangeUrl('subdir', $su));
        $view->subdirOptions = $this->subdir === '*' ? $this->getSubdirOptions() : null;
        $view->resizeSelectChangeUrl = new HtmlString($this->getSelectOnChangeUrl('resize', $su));
        $view->resizeOptions = $this->resize === '*' ? $this->getResizeOptions() : null;
        $view->pluploadConfig = $this->getJsonConfig();
        $run++;
        $view->render();
    }
}

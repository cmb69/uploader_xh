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

class MainAdminController extends UploadController
{
    public function defaultAction()
    {
        $this->appendScript("{$this->pluginFolder}lib/plupload.full.min.js");
        $this->appendScript("{$this->pluginFolder}uploader.min.js");
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
    }
}

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
        $view->typeSelectChangeUrl = $this->getSelectOnchangeUrl('type', '&uploader&admin=plugin_main&action=plugin_text');
        $view->typeOptions = $this->getTypeOptions();
        $view->subdirSelectChangeUrl = $this->getSelectOnchangeUrl('subdir', '&uploader&admin=plugin_main&action=plugin_text');
        $view->subdirOptions = $this->getSubdirOptions();
        $view->resizeSelectChangeUrl = $this->getSelectOnChangeUrl('resize', '&uploader&admin=plugin_main&action=plugin_text');
        $view->resizeOptions = $this->getResizeOptions();
        $view->pluploadConfig = $this->getJsonConfig();
        $view->render();
    }
}

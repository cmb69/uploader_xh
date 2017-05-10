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

class MainAdminController extends UploadController
{
    public function defaultAction()
    {
        global $pth;

        $pluginFolder = "{$pth['folder']['plugins']}uploader/";
        $this->appendScript("{$pluginFolder}lib/plupload.full.min.js");
        $this->appendScript("{$pluginFolder}uploader.min.js");
        $view = new View('container');
        $view->typeSelect = new HtmlString(
            $this->renderTypeSelect('&amp;uploader&amp;admin=plugin_main&amp;action=plugin_text')
        );
        $view->subdirSelect = new HtmlString(
            $this->renderSubdirSelect('&amp;uploader&amp;admin=plugin_main&amp;action=plugin_text')
        );
        $view->resizeSelect = new HtmlString(
            $this->renderResizeSelect('&amp;uploader&amp;admin=plugin_main&amp;action=plugin_text')
        );
        $view->pluploadWidget = new HtmlString((new Widget)->render());
        $view->render();
    }
}

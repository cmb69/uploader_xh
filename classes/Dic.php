<?php

/**
 * Copyright 2011-2023 Christoph M. Becker
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

class Dic
{
    public static function makeUploadController(): UploadController
    {
        global $pth, $sn, $plugin_cf, $plugin_tx;
        static $instance = null;

        if ($instance === null) {
            $instance = new UploadController(
                $plugin_cf['uploader'],
                $plugin_tx['uploader'],
                "{$pth['folder']['plugins']}uploader/",
                [
                    'images' => $pth['folder']['images'],
                    'downloads' => $pth['folder']['downloads'],
                    'media' => $pth['folder']['media'],
                    'userfiles' => $pth['folder']['userfiles']
                ],
                $sn,
                new Jquery(),
                new FileSystemService(),
                (string) ini_get('upload_max_filesize')
            );
        }
        return $instance;
    }
}

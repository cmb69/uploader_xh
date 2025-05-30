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

use Plib\CsrfProtector;
use Plib\Jquery;
use Plib\SystemChecker;
use Plib\View;
use Uploader\Model\FileSystemService;
use Uploader\Model\Receiver;

class Dic
{
    public static function makeUploadController(): UploadController
    {
        global $pth, $plugin_cf;
        static $serial = 0;

        return new UploadController(
            ++$serial,
            $plugin_cf['uploader'],
            "{$pth['folder']['plugins']}uploader/",
            [
                'images' => $pth['folder']['images'],
                'downloads' => $pth['folder']['downloads'],
                'media' => $pth['folder']['media'],
                'userfiles' => $pth['folder']['userfiles']
            ],
            new Jquery($pth["folder"]["plugins"] . "jquery/"),
            new FileSystemService(),
            new Receiver((int) $plugin_cf["uploader"]["size_max"]),
            new CsrfProtector(),
            (string) ini_get('upload_max_filesize'),
            self::view()
        );
    }

    public static function makeInfoController(): InfoController
    {
        global $pth;

        return new InfoController(
            $pth["folder"]["plugins"] . "uploader/",
            new SystemChecker(),
            self::view()
        );
    }

    private static function view(): View
    {
        global $pth, $plugin_tx;

        return new View($pth["folder"]["plugins"] . "uploader/views/", $plugin_tx["uploader"]);
    }
}

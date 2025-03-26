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

use Plib\Request;
use Uploader\Dic;

/**
 * @var string $admin
 * @var string $function
 * @var string $o
 */

XH_registerStandardPluginMenuItems(true);

if (XH_wantsPluginAdministration('uploader')) {
    $o .= print_plugin_admin('on');
    switch ($admin) {
        case '':
            $o .= Dic::makeInfoController()->defaultAction();
            break;
        case 'plugin_main':
            $o .=  Dic::makeUploadController()(Request::current(), null, null, null)();
            break;
        default:
            $o .= plugin_admin_common();
    }
}

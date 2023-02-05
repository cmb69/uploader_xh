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

class Plugin
{
    const VERSION = '1.0beta2';

    /** @return void */
    public function run()
    {
        global $adm;

        if ($adm) {
            XH_registerStandardPluginMenuItems(true);
            if (XH_wantsPluginAdministration('uploader')) {
                $this->handleAdministration();
            }
        }
    }

    /** @return void */
    private function handleAdministration()
    {
        global $pth, $plugin_tx, $admin, $o;

        $o .= print_plugin_admin('on');
        switch ($admin) {
            case '':
                $controller = new InfoController(
                    $pth['folder']['plugins'],
                    $plugin_tx['uploader'],
                    new SystemChecker()
                );
                $o .= $controller->defaultAction();
                break;
            case 'plugin_main':
                $o .= $this->handleMainAdministration();
                break;
            default:
                $o .= plugin_admin_common();
        }
    }

    /**
     * @return string
     */
    private function handleMainAdministration()
    {
        global $pth, $sn, $function, $plugin_cf, $plugin_tx;

        $controller = new UploadController(
            false,
            $plugin_cf['uploader'],
            $plugin_tx['uploader'],
            "{$pth['folder']['plugins']}uploader/",
            [
                'images' => $pth['folder']['images'],
                'downloads' => $pth['folder']['downloads'],
                'media' => $pth['folder']['media'],
                'userfiles' => $pth['folder']['userfiles'],
            ],
            $sn
        );
        if ($function === 'uploader_upload') {
            $action = 'uploadAction';
        } elseif (isset($_GET['uploader_serial'])) {
            $action = 'widgetAction';
        } else {
            $action = 'defaultAction';
        }
        ob_start();
        $controller->{$action}();
        return ob_get_clean();
    }
}

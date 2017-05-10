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
    const VERSION = '@UPLOADER_VERSION@';

    public function run()
    {
        global $adm;

        if ($adm) {
            if (function_exists('XH_registerStandardPluginMenuItems')) {
                XH_registerStandardPluginMenuItems(true);
            }
            if ($this->isAdministrationRequested()) {
                $this->handleAdministration();
            }
        }
    }

    /**
     * @return bool
     */
    private function isAdministrationRequested()
    {
        global $uploader;

        return function_exists('XH_wantsPluginAdministration')
            && XH_wantsPluginAdministration('uploader')
            || isset($uploader) && $uploader == 'true';
    }

    private function handleAdministration()
    {
        global $admin, $action, $plugin, $o;

        $o .= print_plugin_admin('on');
        switch ($admin) {
            case '':
                ob_start();
                (new InfoController)->defaultAction();
                $o .= ob_get_clean();
                break;
            case 'plugin_main':
                $o .= $this->handleMainAdministration();
                break;
            default:
                $o .= plugin_admin_common($action, $admin, $plugin);
        }
    }

    /**
     * @return string
     */
    private function handleMainAdministration()
    {
        global $function;

        $controller = new MainAdminController;
        if ($function === 'uploader_upload') {
            $action = 'uploadAction';
        } else {
            $action = 'defaultAction';
        }
        ob_start();
        $controller->{$action}();
        return ob_get_clean();
    }
}

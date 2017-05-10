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

class Controller
{
    /**
     * Returns the uploader widget.
     *
     * @param string $type      The upload type ('images', 'downloads', 'media' or
     *                          'userfiles'). '*' displays a selectbox.
     * @param string $subdir    The subfolder of the configured folder of the type.
     *                          '*' displays a selectbox.
     * @param string $resize    The resize mode ('', 'small', 'medium' or 'large').
     *                          '*' displays a selectbox.
     *
     * @return string (X)HTML.
     */
    public static function main($type, $subdir, $resize)
    {
        ob_start();
        (new MainController($type, $subdir, $resize))->defaultAction();
        return ob_get_clean();
    }

    /**
     * @return string
     */
    private static function getType()
    {
        global $pth;

        if (isset($_GET['uploader_type'])
            && in_array($_GET['uploader_type'], self::getTypes())
            && isset($pth['folder'][$_GET['uploader_type']])
        ) {
            return $_GET['uploader_type'];
        } else {
            return 'images';
        }
    }

    /**
     * @return array
     */
    private static function getTypes()
    {
        return array('images', 'downloads', 'media', 'userfiles');
    }

    /**
     * @return string
     */
    private static function getSubfolder()
    {
        global $pth;

        $subdir = isset($_GET['uploader_subdir'])
            ? preg_replace('/\.\.[\/\\\\]?/', '', $_GET['uploader_subdir'])
            : '';
        if (isset($_GET['uploader_subdir'])
            && is_dir($pth['folder'][self::getType()] . $subdir)
        ) {
            return $subdir;
        } else {
            return '';
        }
    }

    private static function handleAdministration()
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
                $o .= self::handleMainAdministration();
                break;
            default:
                $o .= plugin_admin_common($action, $admin, $plugin);
        }
    }

    /**
     * @return string
     */
    private static function handleMainAdministration()
    {
        ob_start();
        (new MainAdminController)->defaultAction();
        return ob_get_clean();
    }

    private static function handleUpload()
    {
        global $pth;

        $dir = $pth['folder'][self::getType()] . self::getSubfolder();
        $filename = isset($_POST['name']) ? $_POST['name'] : '';
        $chunks = isset($_POST['chunks']) ? $_POST['chunks'] : 0;
        $chunk = isset($_POST['chunk']) ? $_POST['chunk'] : 0;
        $receiver = new Receiver($dir, $filename, $chunks, $chunk);
        $receiver->emitHeaders();
        if (isset($_FILES['uploader_file']['tmp_name'])
            && is_uploaded_file($_FILES['uploader_file']['tmp_name'])
        ) {
            echo $receiver->handleUpload($_FILES['uploader_file']['tmp_name']);
        } else {
            header('HTTP/1.1 400 Bad Request');
            echo '{"jsonrpc": "2.0", "error": {"code": 103, "message":',
                '"Failed to move uploaded file."}, "id" : "id"}';
        }
        exit();
    }

    public static function dispatch()
    {
        global $adm, $function;

        if ($adm) {
            if (function_exists('XH_registerStandardPluginMenuItems')) {
                XH_registerStandardPluginMenuItems(true);
            }
            if ($function == 'uploader_upload') {
                self::handleUpload();
            } elseif (self::isAdministrationRequested()) {
                self::handleAdministration();
            }
        }
    }

    /**
     * @return bool
     */
    private static function isAdministrationRequested()
    {
        global $uploader;

        return function_exists('XH_wantsPluginAdministration')
            && XH_wantsPluginAdministration('uploader')
            || isset($uploader) && $uploader == 'true';
    }
}

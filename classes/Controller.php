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
            ? preg_replace('/\.\.[\/\\\\]?/', '', stsl($_GET['uploader_subdir']))
            : '';
        if (isset($_GET['uploader_subdir'])
            && is_dir($pth['folder'][self::getType()] . $subdir)
        ) {
            return $subdir;
        } else {
            return '';
        }
    }

    /**
     * @return array
     */
    private static function systemChecks()
    {
        global $pth, $tx, $plugin_tx;

        $ptx = $plugin_tx['uploader'];
        $phpVersion = '5.4.0';
        $checks = array();
        $checks[sprintf($ptx['syscheck_phpversion'], $phpVersion)]
            = version_compare(PHP_VERSION, $phpVersion) >= 0 ? 'ok' : 'fail';
        foreach (array('ctype') as $ext) {
            $checks[sprintf($ptx['syscheck_extension'], $ext)]
                = extension_loaded($ext) ? 'ok' : 'fail';
        }
        $checks[$ptx['syscheck_encoding']]
            = strtoupper($tx['meta']['codepage']) == 'UTF-8' ? 'ok' : 'warn';
        $checks[$ptx['syscheck_jquery']]
            = file_exists($pth['folder']['plugins'] . 'jquery/jquery.inc.php')
            ? 'ok' : 'fail';
        $folders = array();
        foreach (array('config/', 'css/', 'languages/') as $folder) {
            $folders[] = $pth['folder']['plugins'] . 'uploader/' . $folder;
        }
        foreach ($folders as $folder) {
            $checks[sprintf($ptx['syscheck_writable'], $folder)]
                = is_writable($folder) ? 'ok' : 'warn';
        }
        return $checks;
    }

    private static function handleAdministration()
    {
        global $admin, $action, $plugin, $o, $pth;

        $o .= print_plugin_admin('on');
        switch ($admin) {
            case '':
                $view = new View('info');
                $view->logo = "{$pth['folder']['plugins']}uploader/uploader.png";
                $view->version = UPLOADER_VERSION;
                $view->checks = self::systemChecks();
                $view->iconFolder = "{$pth['folder']['plugins']}uploader/images/";
                $o .= (string) $view;
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
            if (self::isAdministrationRequested()) {
                self::handleAdministration();
            } elseif ($function == 'uploader_upload') {
                self::handleUpload();
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

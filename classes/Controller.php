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
        global $bjs, $pth, $su;
        static $run = 0;

        if (!file_exists($pth['folder']['images'] . $subdir)) {
            mkdir($pth['folder']['images'] . $subdir, 0777, true);
        }
        include_once $pth['folder']['plugins'] . 'uploader/init.php';
        $url = '?function=uploader_widget&uploader_type='
            . ($type == '*' ? self::getType() : $type) . '&uploader_subdir='
            . ($subdir == '*' ? self::getSubfolder() : $subdir)
            . '&uploader_resize='
            . ($resize == '*' ? self::getResizeMode() : $resize);
        if (!$run) {
            $bjs .= '<script type="text/javascript" src="' . "{$pth['folder']['plugins']}uploader/uploader.min.js"
                . '"></script>';
        }
        $anchor = 'uploader_container' . $run;
        $view = new View('container');
        $view->anchor = $anchor;
        $view->iframeSrc = $url;
        $view->typeSelect = new HtmlString($type == '*' ? self::renderTypeSelect($su) : '');
        $view->subdirSelect = new HtmlString($subdir == '*' ? self::renderSubdirSelect($su) : '');
        $view->resizeSelect = new HtmlString($resize == '*' ? self::renderResizeSelect($su) : '');
        $run++;
        return (string) $view;
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
     * @return string
     */
    private static function getResizeMode()
    {
        global $plugin_cf;

        if (isset($_GET['uploader_resize'])
            && in_array($_GET['uploader_resize'], self::getSizes())
        ) {
            return $_GET['uploader_resize'];
        } else {
            return $plugin_cf['uploader']['resize_default'];
        }
    }

    /**
     * @return array
     */
    private static function getSizes()
    {
        return array('', 'small', 'medium', 'large');
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
        global $bjs, $pth;

        $bjs .= '<script type="text/javascript" src="' . "{$pth['folder']['plugins']}uploader/uploader.min.js"
            . '"></script>';
        $view = new View('admin-container');
        $view->typeSelect = new HtmlString(
            self::renderTypeSelect('&amp;uploader&amp;admin=plugin_main&amp;action=plugin_text')
        );
        $view->subdirSelect = new HtmlString(
            self::renderSubdirSelect('&amp;uploader&amp;admin=plugin_main&amp;action=plugin_text')
        );
        $view->resizeSelect = new HtmlString(
            self::renderResizeSelect('&amp;uploader&amp;admin=plugin_main&amp;action=plugin_text')
        );
        $view->iframeSrc = '?function=uploader_widget&uploader_type=' . self::getType()
            . '&uploader_subdir=' . self::getSubfolder() . '&uploader_resize=' . self::getResizeMode();
        return (string) $view;
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
            } elseif ($function == 'uploader_widget') {
                $widget = new Widget();
                echo $widget->render();
                exit;
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

    /**
     * @param string $params A query string.
     * @param string $anchor A fragment identifier.
     * @return string (X)HTML.
     */
    private static function renderTypeSelect($params)
    {
        global $pth, $plugin_tx;

        $o = '<select id="uploader-type" title="'
            . $plugin_tx['uploader']['label_type'] . '" data-url="' . self::getSelectOnchangeUrl('type', $params) . '">'
            . "\n";
        foreach (self::getTypes() as $type) {
            if (isset($pth['folder'][$type])) {
                $sel = $type == self::getType() ? ' selected="selected"' : '';
                $o .= '<option value="' . $type . '"' . $sel . '>' . $type
                    . '</option>' . "\n";
            }
        }
        $o .= '</select>' . "\n";
        return $o;
    }

    /**
     * @param string $params A query string.
     * @param string $anchor A fragment identifier.
     * @return string (X)HTML.
     */
    private static function renderSubdirSelect($params)
    {
        global $plugin_tx;

        return '<select id="uploader-subdir" title="'
            . $plugin_tx['uploader']['label_subdir'] . '"'
            . ' data-url="' . self::getSelectOnchangeUrl('subdir', $params) . '">' . "\n"
            . '<option>/</option>' . "\n"
            . self::renderSubdirSelectRec('')
            . '</select>' . "\n";
    }

    /**
     * @param string $parent A parent folder.
     * @return string (X)HTML.
     */
    private static function renderSubdirSelectRec($parent)
    {
        global $pth;

        $o = '';
        $dn = $pth['folder'][self::getType()] . $parent;
        if (($dh = opendir($dn)) !== false) {
            while (($fn = readdir($dh)) !== false) {
                if (strpos($fn, '.') !== 0
                    && is_dir($pth['folder'][self::getType()] . $parent . $fn)
                ) {
                    $dir = $parent . $fn . '/';
                    $sel = ($dir == self::getSubfolder())
                        ? ' selected="selected"'
                        : '';
                    $o .= '<option value="' . $dir . '"' . $sel . '>' . $dir
                        . '</option>' . "\n";
                    $o .= self::renderSubdirSelectRec($dir);
                }
            }
            closedir($dh);
        } else {
            e('cntopen', 'folder', $dn);
        }
        return $o;
    }

    /**
     * @param string $params A query string.
     * @param string $anchor A fragment identifier.
     * @return string (X)HTML.
     */
    private static function renderResizeSelect($params)
    {
        global $plugin_tx;

        $o = '<select id="uploader-resize" title="'
            . $plugin_tx['uploader']['label_resize'] . '"'
            . ' data-url="' . self::getSelectOnchangeUrl('resize', $params) . '">' . "\n";
        foreach (self::getSizes() as $size) {
            $sel = $size == self::getResizeMode() ? ' selected="selected"' : '';
            $o .= '<option value="' . $size . '"' . $sel . '>' . $size . '</option>'
                . "\n";
        }
        $o .= '</select>' . "\n";
        return $o;
    }

    private static function getSelectOnchangeUrl($param, $params)
    {
        global $sn;

        $url = $sn . '?' . $params;
        if ($param != 'type') {
            $url .= '&amp;uploader_type=' . urlencode(self::getType());
        }
        if ($param != 'subdir') {
            $url .= '&amp;uploader_subdir=' . urlencode(self::getSubfolder());
        }
        if ($param != 'resize') {
            $url .= '&amp;uploader_resize=' . urlencode(self::getResizeMode());
        }
        $url .= '&amp;uploader_' . $param . '=';
        return $url;
    }
}

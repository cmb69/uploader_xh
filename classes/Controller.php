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
     * @param bool   $collapsed Whether the uploader widget should be collapsed.
     *
     * @return string (X)HTML.
     */
    public static function main($type, $subdir, $resize, $collapsed)
    {
        global $pth, $su;
        static $run = 0;

        if (!file_exists($pth['folder']['images'] . $subdir)) {
            mkdir($pth['folder']['images'] . $subdir, 0777, true);
        }
        if ($collapsed) {
            self::toggle(
                $run,
                !($type == '*' && isset($_GET['uploader_type'])
                || $subdir == '*' && isset($_GET['uploader_subdir'])
                || $resize == '*' && isset($_GET['uploader_resize']))
            );
        }
        include_once $pth['folder']['plugins'] . 'uploader/init.php';
        $url = '?function=uploader_widget&amp;uploader_type='
            . ($type == '*' ? self::getType() : $type) . '&amp;uploader_subdir='
            . ($subdir == '*' ? self::getSubfolder() : $subdir)
            . '&amp;uploader_resize='
            . ($resize == '*' ? self::getResizeMode() : $resize);
        $anchor = 'uploader_container' . $run;
        $o = '<div id="' . $anchor . '">' . "\n"
            . '<div class="uploader_controls">'
            . ($type == '*' ? self::renderTypeSelect($su, $anchor) : '')
            . ($subdir == '*' ? self::renderSubdirSelect($su, $anchor) : '')
            . ($resize == '*' ? self::renderResizeSelect($su, $anchor) : '')
            . '</div>' . "\n"
            . '<iframe src="' . $url . '" frameBorder="0" class="uploader"></iframe>'
            . "\n"
            . '</div>' . "\n";
        $run++;
        return $o;
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
        global $pth;

        include_once $pth['folder']['plugins'] . 'uploader/init.php';
        return '<div class="uploader_controls">'
            . self::renderTypeSelect(
                '&amp;uploader&amp;admin=plugin_main&amp;action=plugin_text'
            )
            . self::renderSubdirSelect(
                '&amp;uploader&amp;admin=plugin_main&amp;action=plugin_text'
            )
            . self::renderResizeSelect(
                '&amp;uploader&amp;admin=plugin_main&amp;action=plugin_text'
            )
            . '</div>' . "\n"
            . '<iframe class="uploader" frameBorder="0" src="'
            . '?function=uploader_widget&amp;uploader_type='
            . self::getType() . '&amp;uploader_subdir=' . self::getSubfolder()
            . '&amp;uploader_resize=' . self::getResizeMode() . '"></iframe>' . "\n";
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
     * Hides the element with the given $id, and allows to toggle its visibility.
     *
     * @param int  $run       A running number.
     * @param bool $collapsed Whether the element is initially collapsed.
     */
    private static function toggle($run, $collapsed)
    {
        global $pth, $hjs, $plugin_tx;

        $ptx = $plugin_tx['uploader'];
        include_once $pth['folder']['plugins'] . 'jquery/jquery.inc.php';
        include_jquery();
        $hide = $collapsed ? '.hide()' : '';
        $hidetxt = $collapsed ? $ptx['label_expand'] : $ptx['label_collapse'];
        $hjs .= <<<SCRIPT
<script type="text/javascript">
/* <![CDATA[ */
function uploader_toggle$run() {
    elt = jQuery('#uploader_container$run');
    elt.toggle();
    elt.prev().children('a').html(
        elt.is(':visible') ? '{$ptx['label_collapse']}' : '{$ptx['label_expand']}'
    );
}

jQuery(function() {
    setTimeout(function() {
        jQuery('#uploader_container$run').before(
            '<div class="uploader_toggle">' +
            '<a href="javascript:uploader_toggle$run()">$hidetxt</a></div>'
        )$hide;
    }, 100)
})
/* ]]> */
</script>

SCRIPT;
    }

    /**
     * @param string $params A query string.
     * @param string $anchor A fragment identifier.
     * @return string (X)HTML.
     */
    private static function renderTypeSelect($params, $anchor = null)
    {
        global $pth, $plugin_tx;

        $o = '<select id="uploader-type" title="'
            . $plugin_tx['uploader']['label_type'] . '" onchange="'
            . self::renderSelectOnchange('type', $params, $anchor) . '">'
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
    private static function renderSubdirSelect($params, $anchor = null)
    {
        global $plugin_tx;

        return '<select id="uploader-subdir" title="'
            . $plugin_tx['uploader']['label_subdir'] . '"'
            . ' onchange="' . self::renderSelectOnchange('subdir', $params, $anchor)
            . '">' . "\n"
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
    private static function renderResizeSelect($params, $anchor = null)
    {
        global $plugin_tx;

        $o = '<select id="uploader-resize" title="'
            . $plugin_tx['uploader']['label_resize'] . '"'
            . ' onchange="' . self::renderSelectOnchange('resize', $params, $anchor)
            . '">' . "\n";
        foreach (self::getSizes() as $size) {
            $sel = $size == self::getResizeMode() ? ' selected="selected"' : '';
            $o .= '<option value="' . $size . '"' . $sel . '>' . $size . '</option>'
                . "\n";
        }
        $o .= '</select>' . "\n";
        return $o;
    }

    /**
     * @param string $param  A kind.
     * @param string $params A query string.
     * @param string $anchor A fragment identifier.
     * @return string
     */
    private static function renderSelectOnchange($param, $params, $anchor = null)
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
        $js = 'window.location.href=\''  .$url
            . '\'+encodeURIComponent(document.getElementById(\'uploader-' . $param
            . '\').value)';
        if (isset($anchor)) {
            $js .= '+\'#'.$anchor.'\'';
        }
        return $js;
    }
}

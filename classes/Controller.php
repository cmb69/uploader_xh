<?php

/**
 * Controller of Uploader_XH.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Uploader
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2011-2015 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Uploader_XH
 */

/**
 * The controller.
 *
 * @category CMSimple_XH
 * @package  Uploader
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Uploader_XH
 */
class Uploader_Controller
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
     *
     * @global array  The paths of system files and folders.
     * @global string The current page URL.
     *
     * @staticvar int $run The running number.
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
     * Returns the requested upload type.
     *
     * @return string
     */
    protected static function getType()
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
     * Returns the upload types.
     *
     * @return array
     */
    protected static function getTypes()
    {
        return array('images', 'downloads', 'media', 'userfiles');
    }

    /**
     * Returns the requested subfolder.
     *
     * @return string
     *
     * @global array The paths of system files and folders.
     */
    protected static function getSubfolder()
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
     * Returns the requested resize mode.
     *
     * @return string
     *
     * @global array The configuration of the plugins.
     */
    protected static function getResizeMode()
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
     * Returns the upload sizes.
     *
     * @return array
     */
    protected static function getSizes()
    {
        return array('', 'small', 'medium', 'large');
    }

    /**
     * Returns the localization of a string.
     *
     * @param string $key The key of the string.
     *
     * @return string
     *
     * @global array The localization of the plugins.
     */
    protected static function l10n($key)
    {
        global $plugin_tx;

        return $plugin_tx['uploader'][$key];
    }
    /**
     * Returns the path of the plugin logo.
     *
     * @return string
     *
     * @global array The paths of system files and folders.
     */
    protected static function logoPath()
    {
        global $pth;

        return $pth['folder']['plugins'] . 'uploader/uploader.png';
    }

    /**
     * Returns the path of a system check state icon.
     *
     * @param string $state A system check state.
     *
     * @return string
     *
     * @global array The paths of system files and folders.
     */
    protected static function stateIconPath($state)
    {
        global $pth;

        return $pth['folder']['plugins'] . 'uploader/images/' . $state . '.png';
    }

    /**
     * Returns the system checks.
     *
     * @return array
     *
     * @global array The paths of system files and folders.
     * @global array The localization of the core.
     * @global array The localization of the plugins.
     */
    protected static function systemChecks()
    {
        global $pth, $tx, $plugin_tx;

        $ptx = $plugin_tx['uploader'];
        $phpVersion = '4.0.7';
        $checks = array();
        $checks[sprintf($ptx['syscheck_phpversion'], $phpVersion)]
            = version_compare(PHP_VERSION, $phpVersion) >= 0 ? 'ok' : 'fail';
        foreach (array('ctype', 'pcre', 'session') as $ext) {
            $checks[sprintf($ptx['syscheck_extension'], $ext)]
                = extension_loaded($ext) ? 'ok' : 'fail';
        }
        $checks[$ptx['syscheck_magic_quotes']]
            = !get_magic_quotes_runtime() ? 'ok' : 'fail';
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

    /**
     * Renders a view template.
     *
     * @param string $template The name of the template.
     *
     * @return string
     *
     * @global array The paths of system files and folders.
     * @global array The configuration of the core.
     */
    protected static function render($template)
    {
        global $pth, $cf;

        $template = $pth['folder']['plugins'] . 'uploader/views/'
            . $template . '.php';
        ob_start();
        include $template;
        $o = ob_get_clean();
        if (!$cf['xhtml']['endtags']) {
            $o = str_replace('/>', '>', $o);
        }
        return $o;
    }

    /**
     * Handles the plugin administration.
     *
     * @return void
     *
     * @global string The value of the "admin" GET or POST parameter.
     * @global string The value of the "action" GET or POST parameter.
     * @global string The name of the plugin.
     * @global string The (X)HTML to be placed in the contents area.
     */
    protected static function handleAdministration()
    {
        global $admin, $action, $plugin, $o;

        $o .= print_plugin_admin('on');
        switch ($admin) {
        case '':
            $o .= self::render('info');
            break;
        case 'plugin_main':
            $o .= self::handleMainAdministration();
            break;
        default:
            $o .= plugin_admin_common($action, $admin, $plugin);
        }
    }

    /**
     * Handles the main administration.
     *
     * @return string (X)HTML.
     *
     * @global array The paths of system files and folders.
     */
    protected static function handleMainAdministration()
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

    /**
     * Handles a file upload.
     *
     * @return void
     *
     * @global array The paths of system files and folders.
     */
    protected static function handleUpload()
    {
        global $pth;

        $dir = $pth['folder'][$_GET['type']] . $_GET['subdir'];
        $filename = isset($_POST['name']) ? $_POST['name'] : '';
        $chunks = isset($_REQUEST['chunks']) ? $_REQUEST['chunks'] : 0;
        $chunk = isset($_REQUEST['chunk']) ? $_REQUEST['chunk'] : 0;
        $contentType = isset($_SERVER["CONTENT_TYPE"])
            ? $_SERVER["CONTENT_TYPE"]
            : $_SERVER["HTTP_CONTENT_TYPE"];
        $receiver = new Uploader_Receiver($dir, $filename, $chunks, $chunk);
        $receiver->emitHeaders();
        if (strpos($contentType, 'multipart') !== false) {
            if (isset($_FILES['file']['tmp_name'])
                && is_uploaded_file($_FILES['file']['tmp_name'])
            ) {
                echo $receiver->handleUpload($_FILES['file']['tmp_name']);
            } else {
                echo '{"jsonrpc": "2.0", "error": {"code": 103, "message":',
                    '"Failed to move uploaded file."}, "id" : "id"}';
            }
        } else {
            echo $receiver->handleUpload('php://input');
        }
        exit();
    }

    /**
     * Dispatches on Uploader related requests.
     *
     * @return void
     *
     * @global bool   Whether the user is logged in as admin.
     * @global string Whether the plugin administration is requested.
     */
    public static function dispatch()
    {
        global $adm, $function, $uploader;

        if ($adm && isset($uploader) && $uploader == 'true') {
            self::handleAdministration();
        } elseif ($adm && $function == 'uploader_widget') {
            $widget = new Uploader_Widget();
            echo $widget->render();
            exit;
        } elseif ($adm && $function == 'uploader_upload') {
            self::handleUpload();
        }
    }

    /**
     * Hides the element with the given $id, and allows to toggle its visibility.
     *
     * @param int  $run       A running number.
     * @param bool $collapsed Whether the element is initially collapsed.
     *
     * @return void
     *
     * @global array  The paths of system files and folders.
     * @global string The (X)HTML fragment to insert into the head element.
     * @global array  The localization of the plugins.
     */
    protected static function toggle($run, $collapsed)
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
     * Renders a type select element.
     *
     * @param string $params A query string.
     * @param string $anchor A fragment identifier.
     *
     * @return string (X)HTML.
     *
     * @global array  The paths of system files and folders.
     * @global string The script name.
     * @global array  The localization of the plugins.
     */
    protected static function renderTypeSelect($params, $anchor = null)
    {
        global $pth, $sn, $plugin_tx;

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
     * Renders the subfolder select element.
     *
     * @param string $params A query string.
     * @param string $anchor A fragment identifier.
     *
     * @return string (X)HTML.
     *
     * @global array  The paths of system files and folders.
     * @global string The script name.
     * @global array  The localization of the plugins.
     */
    protected static function renderSubdirSelect($params, $anchor = null)
    {
        global $pth, $sn, $plugin_tx;

        return '<select id="uploader-subdir" title="'
            . $plugin_tx['uploader']['label_subdir'] . '"'
            . ' onchange="' . self::renderSelectOnchange('subdir', $params, $anchor)
            . '">' . "\n"
            . '<option>/</option>' . "\n"
            . self::renderSubdirSelectRec('')
            . '</select>' . "\n";
    }

    /**
     * Renders a level of a subfolder select element.
     *
     * @param string $parent A parent folder.
     *
     * @return string (X)HTML.
     *
     * @global array The paths of system files and folders.
     */
    protected static function renderSubdirSelectRec($parent)
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
     * Renders the resize select element.
     *
     * @param string $params A query string.
     * @param string $anchor A fragment identifier.
     *
     * @return string (X)HTML.
     *
     * @global array The localization of the plugins.
     */
    protected static function renderResizeSelect($params, $anchor = null)
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
     * Renders the value of an onchange attribute.
     *
     * @param string $param  A kind.
     * @param string $params A query string.
     * @param string $anchor A fragment identifier.
     *
     * @return string
     *
     * @global string The script name.
     */
    protected static function renderSelectOnchange($param, $params, $anchor = null)
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

?>

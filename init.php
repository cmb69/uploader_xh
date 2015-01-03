<?php

/**
 * Initialization of Uploader_XH.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Uploader
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2011-2015 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Uploader_XH
 */

if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
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
function Uploader_selectOnchange($param, $params, $anchor = null)
{
    global $sn;

    $url = $sn . '?' . $params;
    if ($param != 'type') {
        $url .= '&amp;uploader_type=' . urlencode(UPLOADER_TYPE);
    }
    if ($param != 'subdir') {
        $url .= '&amp;uploader_subdir=' . urlencode(UPLOADER_SUBDIR);
    }
    if ($param != 'resize') {
        $url .= '&amp;uploader_resize=' . urlencode(UPLOADER_RESIZE);
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
 * @global array  The uploader types.
 */
function Uploader_typeSelect($params, $anchor = null)
{
    global $pth, $sn, $plugin_tx, $uploader_types;

    $o = '<select id="uploader-type" title="' . $plugin_tx['uploader']['label_type']
        . '" onchange="' . Uploader_selectOnchange('type', $params, $anchor) . '">'
        . "\n";
    foreach ($uploader_types as $type) {
        if (isset($pth['folder'][$type])) {
            $sel = $type == UPLOADER_TYPE ? ' selected="selected"' : '';
            $o .= '<option value="' . $type . '"' . $sel . '>' . $type
                . '</option>' . "\n";
        }
    }
    $o .= '</select>' . "\n";
    return $o;
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
function Uploader_subdirSelectRec($parent)
{
    global $pth;

    $o = '';
    $dn = $pth['folder'][UPLOADER_TYPE] . $parent;
    if (($dh = opendir($dn)) !== false) {
        while (($fn = readdir($dh)) !== false) {
            if (strpos($fn, '.') !== 0
                && is_dir($pth['folder'][UPLOADER_TYPE] . $parent . $fn)
            ) {
                $dir = $parent . $fn . '/';
                $sel = $dir == UPLOADER_SUBDIR ? ' selected="selected"' : '';
                $o .= '<option value="' . $dir . '"' . $sel . '>' . $dir
                    . '</option>' . "\n";
                $o .= Uploader_subdirSelectRec($dir);
            }
        }
        closedir($dh);
    } else {
        e('cntopen', 'folder', $dn);
    }
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
function Uploader_subdirSelect($params, $anchor = null)
{
    global $pth, $sn, $plugin_tx;

    return '<select id="uploader-subdir" title="'
        . $plugin_tx['uploader']['label_subdir'] . '"'
        . ' onchange="' . Uploader_selectOnchange('subdir', $params, $anchor)
        . '">' . "\n"
        . '<option>/</option>' . "\n"
        . Uploader_subdirSelectRec('')
        . '</select>' . "\n";
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
 * @global array The uploader sizes.
 */
function Uploader_resizeSelect($params, $anchor = null)
{
    global $plugin_tx, $uploader_sizes;

    $o = '<select id="uploader-resize" title="'
        . $plugin_tx['uploader']['label_resize'] . '"'
        . ' onchange="' . Uploader_selectOnchange('resize', $params, $anchor)
        . '">' . "\n";
    foreach ($uploader_sizes as $size) {
        $sel = $size == UPLOADER_RESIZE ? ' selected="selected"' : '';
        $o .= '<option value="' . $size . '"' . $sel . '>' . $size . '</option>'
            . "\n";
    }
    $o .= '</select>' . "\n";
    return $o;
}

/**
 * Initializes the uploader session.
 *
 * @return void
 *
 * @global array  The paths of system files and folders.
 * @global string The current language.
 * @global array  The configuration of the core.
 * @global array  The localization of the core.
 * @global array  The configuration of the plugins.
 * @global array  The localization of the plugins.
 * @global array  The uploader types.
 * @global array  The uploader sizes.
 */
function Uploader_init()
{
    global $pth, $sl, $cf, $tx, $plugin_cf, $plugin_tx, $uploader_types,
        $uploader_sizes;

    $pcf = $plugin_cf['uploader'];
    $ptx = $plugin_tx['uploader'];
    $uploader_types = array('images', 'downloads', 'media', 'userfiles');
    $uploader_sizes = array('', 'small', 'medium', 'large');
    define(
        'UPLOADER_TYPE',
        isset($_GET['uploader_type'])
        && in_array($_GET['uploader_type'], $uploader_types)
        && isset($pth['folder'][$_GET['uploader_type']])
        ? $_GET['uploader_type']
        : 'images'
    );
    $subdir = isset($_GET['uploader_subdir'])
        ? preg_replace('/\.\.[\/\\\\]?/', '', stsl($_GET['uploader_subdir']))
        : '';
    define(
        'UPLOADER_SUBDIR',
        isset($_GET['uploader_subdir'])
        && is_dir($pth['folder'][UPLOADER_TYPE] . $subdir)
        ? $subdir
        : ''
    );
    define(
        'UPLOADER_RESIZE',
        isset($_GET['uploader_resize'])
        && in_array($_GET['uploader_resize'], $uploader_sizes)
        ? $_GET['uploader_resize']
        : $pcf['resize_default']
    );
}

/**
 * Include config and language file, if not already done.
 */
global $sl, $pth, $plugin_cf;

if (!isset($plugin_cf['uploader'])) {
    include $pth['folder']['plugins'] . 'uploader/config/config.php';
}
if (!isset($plugin_tx['uploader'])) {
    include $pth['folder']['plugins'] . 'uploader/languages/' . $sl . '.php';
}

/**
 * Initialize the uploader.
 */
Uploader_init();

?>

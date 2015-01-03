<?php

/**
 * Front-end functionality of Uploader_XH.
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
 * The plugin version.
 */
define('UPLOADER_VERSION', '1alpha8');

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
function Uploader_toggle($run, $collapsed)
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
 * Returns the uploader widget.
 *
 * @param string $type      The upload type ('images', 'downloads', 'media' or
 *                          'userfiles'). Use '*' to display a selectbox to the user.
 * @param string $subdir    The subfolder of the configured folder of the type.
 *                          Use '*' to display a selectbox to the user.
 * @param string $resize    The resize mode ('', 'small', 'medium' or 'large').
 *                          Use '*' to display a selectbox to the user.
 * @param bool   $collapsed Whether the uploader widget should be collapsed.
 *
 * @return string (X)HTML.
 *
 * @global array  The paths of system files and folders.
 * @global string The current page URL.
 *
 * @staticvar int $run The running number.
 *
 * @access public
 */
function uploader($type = 'images', $subdir = '', $resize = '', $collapsed = false)
{
    global $pth, $su;
    static $run = 0;

    if (!file_exists($pth['folder']['images'] . $subdir)) {
        mkdir($pth['folder']['images'] . $subdir, 0777, true);
    }
    if ($collapsed) {
        Uploader_toggle(
            $run,
            !($type == '*' && isset($_GET['uploader_type'])
            || $subdir == '*' && isset($_GET['uploader_subdir'])
            || $resize == '*' && isset($_GET['uploader_resize']))
        );
    }
    include_once $pth['folder']['plugins'] . 'uploader/init.php';
    $url = $pth['folder']['plugins'] . 'uploader/uploader.php?uploader_type='
        . ($type == '*' ? UPLOADER_TYPE : $type) . '&amp;uploader_subdir='
        . ($subdir == '*' ? UPLOADER_SUBDIR : $subdir) . '&amp;uploader_resize='
        . ($resize == '*' ? UPLOADER_RESIZE : $resize);
    $anchor = 'uploader_container' . $run;
    $o = '<div id="' . $anchor . '">' . "\n"
        . '<div class="uploader_controls">'
        . ($type == '*' ? Uploader_typeSelect($su, $anchor) : '')
        . ($subdir == '*' ? Uploader_subdirSelect($su, $anchor) : '')
        . ($resize == '*' ? Uploader_resizeSelect($su, $anchor) : '')
        . '</div>' . "\n"
        . '<iframe src="' . $url . '" frameBorder="0" class="uploader"></iframe>'
        . "\n"
        . '</div>' . "\n";
    $run++;
    return $o;
}

/**
 * Returns the collapsed uploader widget. This is a convenience function.
 *
 * @param string $type   The upload type ('images', 'downloads', 'media' or
 *                       'userfiles'). Use '*' to display a selectbox to the user.
 * @param string $subdir The subfolder of the configured folder of the type.
 *                       Use '*' to display a selectbox to the user.
 * @param string $resize The resize mode ('', 'small', 'medium' or 'large').
 *                       Use '*' to display a selectbox to the user.
 *
 * @return string (X)HTML.
 *
 * @access public
 */
// @codingStandardsIgnoreStart
function uploader_collapsed($type = 'images', $subdir = '', $resize = '')
{
// @codingStandardsIgnoreEnd
    return uploader($type, $subdir, $resize, true);
}

/**
 * Handles the main administration.
 *
 * @return string (X)HTML.
 *
 * @global array The paths of system files and folders.
 * @global array The upload types.
 * @global array The upload sizes.
 */
function Uploader_adminMain()
{
    global $pth, $uploader_types, $uploader_sizes;

    include_once $pth['folder']['plugins'] . 'uploader/init.php';
    return '<div class="uploader_controls">'
        . Uploader_typeSelect(
            '&amp;uploader&amp;admin=plugin_main&amp;action=plugin_text'
        )
        . Uploader_subdirSelect(
            '&amp;uploader&amp;admin=plugin_main&amp;action=plugin_text'
        )
        . Uploader_resizeSelect(
            '&amp;uploader&amp;admin=plugin_main&amp;action=plugin_text'
        )
        . '</div>' . "\n"
        . '<iframe class="uploader" frameBorder="0" src="'
        . $pth['folder']['plugins'] . 'uploader/uploader.php?uploader_type='
        . UPLOADER_TYPE . '&amp;uploader_subdir=' . UPLOADER_SUBDIR
        . '&amp;uploader_resize=' . UPLOADER_RESIZE . '"></iframe>' . "\n";
}

require_once $pth['folder']['plugin_classes'] . 'controller.php';
$_Uploader = new Uploader_Controller();
$_Uploader->dispatch();

?>

<?php

/**
 * Front-end functionality of Uploader_XH.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Uploader
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2011-2017 Christoph M. Becker <http://3-magi.net>
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
define('UPLOADER_VERSION', '@UPLOADER_VERSION@');

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
function uploader($type = 'images', $subdir = '', $resize = '', $collapsed = false)
{
    return Uploader_Controller::main($type, $subdir, $resize, $collapsed);
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
    return Uploader_Controller::main($type, $subdir, $resize, true);
}

Uploader_Controller::dispatch();

?>

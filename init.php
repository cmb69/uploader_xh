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
 * Include config and language file, if not already done.
 */
global $sl, $pth, $plugin_cf;

if (!isset($plugin_cf['uploader'])) {
    include $pth['folder']['plugins'] . 'uploader/config/config.php';
}
if (!isset($plugin_tx['uploader'])) {
    include $pth['folder']['plugins'] . 'uploader/languages/' . $sl . '.php';
}

?>

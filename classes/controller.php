<?php

/**
 * Controller of Uploader_XH.
 *
 * PHP versions 4 and 5
 *
 * @category  CMSimple_XH
 * @package   Uploader
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2011-2013 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Uploader_XH
 */

/**
 * The controller class.
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
     * Returns the localization of a string.
     *
     * @param string $key The key of the string.
     *
     * @return string
     *
     * @global array The localization of the plugins.
     *
     * @access protected
     */
    function l10n($key)
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
     *
     * @access protected
     */
    function logoPath()
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
     *
     * @access protected
     */
    function stateIconPath($state)
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
     *
     * @access protected
     */
    function systemChecks()
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
     *
     * @access protected
     */
    function render($template)
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
     *
     * @access protected
     */
    function handleAdministration()
    {
        global $admin, $action, $plugin, $o;

        $o .= print_plugin_admin('off');
        switch ($admin) {
        case '':
            $o .= $this->render('info');
            break;
	case 'plugin_main':
	    $o .= uploader_admin_main();
            break;
        default:
            $o .= plugin_admin_common($action, $admin, $plugin);
        }
    }

    /**
     * Dispatches on Uploader related requests.
     *
     * @return void
     *
     * @global bool   Whether the user is logged in as admin.
     * @global string Whether the plugin administration is requested.
     *
     * @access public
     */
    function dispatch()
    {
        global $adm, $uploader;

        if ($adm && isset($uploader) && $uploader == 'true') {
            $this->handleAdministration();
        }
    }


}

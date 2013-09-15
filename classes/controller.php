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
            $o .= uploader_version().tag('hr').uploader_system_check();
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

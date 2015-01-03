<?php

/**
 * The uploader widget.
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
 * The uploader widget.
 *
 * @category CMSimple_XH
 * @package  Uploader
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Uploader_XH
 */
class Uploader_Widget
{
    /**
     * The upload type ('images', 'downloads', 'media' or 'userfiles').
     *
     * @var string
     */
    protected $type;

    /**
     * The subfolder of the configured folder of the type.
     *
     * @var string
     */
    protected $subdir;

    /**
     * The resize mode ('', 'small', 'medium' or 'large').
     *
     * @var string
     */
    protected $resize;

    /**
     * The resize width.
     *
     * @var int
     */
    protected $width;

    /**
     * The resize height.
     *
     * @var int
     */
    protected $height;

    /**
     * The resize quality.
     *
     * @var int
     */
    protected $quality;

    /**
     * The lib folder path.
     *
     * @var string
     */
    protected $libFolder;

    /**
     * The image folder path.
     *
     * @var string
     */
    protected $imageFolder;

    /**
     * Initializes a new instance.
     *
     * @global array The paths of system files and folders.
     */
    public function __construct()
    {
        global $pth;

        if (session_id() != "") {
            session_start();
        }

        if (!isset($_SESSION['uploader_runtimes'])) {
            header('HTTP/1.0 403 Forbidden');
            exit;
        }

        $this->type = isset($_GET['uploader_type'])
            && isset($_SESSION['uploader_folder'][$_GET['uploader_type']])
            ? $_GET['uploader_type']
            : 'images';
        $subdir = !isset($_GET['uploader_subdir'])
            ? ''
            : preg_replace('/\.\.[\/\\\\]?/', '', stsl($_GET['uploader_subdir']));
        $this->subdir = is_dir($_SESSION['uploader_folder'][$this->type] . $subdir)
            ? $subdir
            : '';
        $allowedSizes = array('small', 'medium', 'large', 'custom');
        $this->resize = isset($_GET['uploader_resize'])
            && in_array($_GET['uploader_resize'], $allowedSizes)
            ? $_GET['uploader_resize']
            : '';
        foreach (array('width', 'height', 'quality') as $name) {
            if ($this->resize == 'custom' && !empty($_GET['uploader_' . $name])
                && ctype_digit($_GET['uploader_' . $name])
            ) {
                $this->{$name} = $_GET['uploader_' . $name];
            }
        }
        $this->libFolder = $pth['folder']['plugins'] . 'uploader/lib/';
        $this->imageFolder = $pth['folder']['plugins'] . 'uploader/images/';
    }

    /**
     * Renders the view template.
     *
     * @return string
     *
     * @global array The paths of system files and folders.
     * @global array The configuration of the core.
     */
    public function render()
    {
        global $pth, $cf;

        $template = $pth['folder']['plugins'] . 'uploader/views/widget.php';
        ob_start();
        include $template;
        $o = ob_get_clean();
        if (!$cf['xhtml']['endtags']) {
            $o = str_replace('/>', '>', $o);
        }
        return $o;
    }

}

?>

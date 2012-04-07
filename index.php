<?php


/**
 * Front-end functionality of Uploader_XH.
 * Copyright (c) 2011-2012 Christoph M. Becker (see license.txt)
 */


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


define('UPLOADER_VERSION', '1alpha4');


/**
 * Returns the uploader widget.
 *
 * @access public
 * @param string $type  The upload type ('images', 'downloads', 'media' or 'userfiles').
 * @param string $subdir  The subfolder of the configured folder of the type.
 * @param string $resize  The resize mode ('', 'small', 'medium' or 'large').
 * @return string  The (X)HTML.
 */
function uploader($type = 'images', $subdir = '', $resize = '') {
    global $pth;

    include_once $pth['folder']['plugins'].'uploader/init.php';
    $url = $pth['folder']['plugins'].'uploader/uploader.php?type='.$type.'&amp;subdir='.$subdir.'&amp;resize='.$resize;
    $o = '<iframe src="'.$url.'" frameBorder="0" class="uploader"></iframe>'."\n";
    return $o;
}

?>

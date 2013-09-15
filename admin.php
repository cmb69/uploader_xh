<?php

/**
 * Back-end functionality of Uploader_XH.
 * Copyright (c) 2011-2012 Christoph M. Becker (see license.txt)
 */


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


require_once $pth['folder']['plugin_classes'] . 'controller.php';
$_Uploader = new Uploader_Controller();
$_Uploader->dispatch();

?>

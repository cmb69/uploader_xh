<?php


/**
 * Front-end functionality of Uploader_XH.
 * Copyright (c) 2011-2012 Christoph M. Becker (see license.txt)
 */


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


define('UPLOADER_VERSION', '1alpha8');


/**
 * Hides the element with the given $id, and allows to toggle its visibility.
 *
 * @global string $hjs
 * @param string $id  The id of the element to toggle.
 * @return void
 */
function uploader_toggle($run, $collapsed) {
    global $pth, $hjs, $plugin_tx;

    $ptx = $plugin_tx['uploader'];
    include_once $pth['folder']['plugins'].'jquery/jquery.inc.php';
    include_jquery();
    $hide = $collapsed ? '.hide()' : '';
    $hidetxt = $collapsed ? $ptx['label_expand'] : $ptx['label_collapse'];
    $hjs .= <<<SCRIPT
<script type="text/javascript">
/* <![CDATA[ */
function uploader_toggle$run() {
    elt = jQuery('#uploader_container$run');
    elt.toggle();
    elt.prev().children('a').html(elt.is(':visible') ? '{$ptx['label_collapse']}' : '{$ptx['label_expand']}');
}

jQuery(function() {
    setTimeout(function() {
	jQuery('#uploader_container$run').before('<div class="uploader_toggle"><a href="javascript:uploader_toggle$run()">$hidetxt</a></div>')$hide;
    }, 100)
})
/* ]]> */
</script>

SCRIPT;
}
/**
 * Returns the uploader widget.
 *
 * @access public
 * @param string $type  The upload type ('images', 'downloads', 'media' or 'userfiles'). Use '*' to display a selectbox to the user.
 * @param string $subdir  The subfolder of the configured folder of the type. Use '*' to display a selectbox to the user.
 * @param string $resize  The resize mode ('', 'small', 'medium' or 'large'). Use '*' to display a selectbox to the user.
 * @param bool $collapsed  Whether the uploader widget should be collapsed.
 * @return string  The (X)HTML.
 */
function uploader($type = 'images', $subdir = '', $resize = '', $collapsed = FALSE) {
    global $pth, $su;
    static $run = 0;

    if (!file_exists($pth['folder']['images'] . $subdir)) {
	mkdir($pth['folder']['images'] . $subdir, 0777, true); // TODO: $recursive parameter only since PHP 5
    }
    if ($collapsed) {
	uploader_toggle($run, !($type == '*' && isset($_GET['uploader_type'])
		|| $subdir == '*' && isset($_GET['uploader_subdir'])
		|| $resize == '*' && isset($_GET['uploader_resize'])));
    }
    include_once $pth['folder']['plugins'].'uploader/init.php';
    $url = $pth['folder']['plugins'].'uploader/uploader.php?uploader_type='
	    .($type == '*' ? UPLOADER_TYPE : $type).'&amp;uploader_subdir='
	    .($subdir == '*' ? UPLOADER_SUBDIR : $subdir).'&amp;uploader_resize='
	    .($resize == '*' ? UPLOADER_RESIZE : $resize);
    $anchor = 'uploader_container'.$run;
    $o = '<div id="'.$anchor.'">'."\n"
	    .'<div class="uploader_controls">'
	    .($type == '*' ? uploader_type_select($su, $anchor) : '')
	    .($subdir == '*' ? uploader_subdir_select($su, $anchor) : '')
	    .($resize == '*' ? uploader_resize_select($su, $anchor) : '')
	    .'</div>'."\n"
	    .'<iframe src="'.$url.'" frameBorder="0" class="uploader"></iframe>'."\n"
	    .'</div>'."\n";
    $run++;
    return $o;
}


/**
 * Returns the collapsed uploader widget.
 * This is a convenience function.
 *
 * @access public
 * @param string $type  The upload type ('images', 'downloads', 'media' or 'userfiles'). Use '*' to display a selectbox to the user.
 * @param string $subdir  The subfolder of the configured folder of the type. Use '*' to display a selectbox to the user.
 * @param string $resize  The resize mode ('', 'small', 'medium' or 'large'). Use '*' to display a selectbox to the user.
 * @return string  The (X)HTML.
 */
function uploader_collapsed($type = 'images', $subdir = '', $resize = '') {
    return uploader($type, $subdir, $resize, TRUE);
}


function uploader_admin_main() {
    global $pth, $uploader_types, $uploader_sizes;

    include_once $pth['folder']['plugins'].'uploader/init.php';
    return '<div class="uploader_controls">'
	    .uploader_type_select('&amp;uploader&amp;admin=plugin_main&amp;action=plugin_text')
	    .uploader_subdir_select('&amp;uploader&amp;admin=plugin_main&amp;action=plugin_text')
	    .uploader_resize_select('&amp;uploader&amp;admin=plugin_main&amp;action=plugin_text').'</div>'."\n"
	    .'<iframe class="uploader" frameBorder="0" src="'
		.$pth['folder']['plugins'].'uploader/uploader.php?uploader_type='
		.UPLOADER_TYPE.'&amp;uploader_subdir='.UPLOADER_SUBDIR.'&amp;uploader_resize='.UPLOADER_RESIZE.'"></iframe>'."\n";
}


?>

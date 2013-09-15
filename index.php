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

/**
 * Returns (x)html plugin version information.
 *
 * @return string
 */
function uploader_version() {
    global $pth;

    return '<h1><a href="http://3-magi.net/?CMSimple_XH/Uploader_XH">Uploader_XH</a></h1>'."\n"
	    .tag('img src="'.$pth['folder']['plugins'].'uploader/uploader.png" width="128"'
	    .' height="128" alt="Plugin icon" class="uploader_plugin_icon"')
	    .'<p style="margin-top: 1em">Version: '.UPLOADER_VERSION.'</p>'."\n"
	    .'<p>Copyright &copy; 2011-2012 <a href="http://3-magi.net/">Christoph M. Becker</a></p>'."\n"
	    .'<p>Uploader_XH is powered by '
	    .'<a href="http://www.plupload.com/">Plupload</a>.</p>'."\n"
	    .'<p class="uploader_license">This program is free software: you can redistribute it and/or modify'
	    .' it under the terms of the GNU General Public License as published by'
	    .' the Free Software Foundation, either version 3 of the License, or'
	    .' (at your option) any later version.</p>'."\n"
	    .'<p class="uploader_license">This program is distributed in the hope that it will be useful,'
	    .' but WITHOUT ANY WARRANTY; without even the implied warranty of'
	    .' MERCHAN&shy;TABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the'
	    .' GNU General Public License for more details.</p>'."\n"
	    .'<p class="uploader_license">You should have received a copy of the GNU General Public License'
	    .' along with this program.  If not, see'
	    .' <a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.</p>'."\n";
}


/**
 * Returns requirements information.
 *
 * @return string
 */
function uploader_system_check() { // RELEASE-TODO
    global $pth, $tx, $plugin_tx;

    define('UPLOADER_PHP_VERSION', '4.0.7');
    $ptx = $plugin_tx['uploader'];
    $imgdir = $pth['folder']['plugins'].'uploader/images/';
    $ok = tag('img src="'.$imgdir.'ok.png" alt="ok"');
    $warn = tag('img src="'.$imgdir.'warn.png" alt="warning"');
    $fail = tag('img src="'.$imgdir.'fail.png" alt="failure"');
    $o = '<h4>'.$ptx['syscheck_title'].'</h4>'
	    .(version_compare(PHP_VERSION, UPLOADER_PHP_VERSION) >= 0 ? $ok : $fail)
	    .'&nbsp;&nbsp;'.sprintf($ptx['syscheck_phpversion'], UPLOADER_PHP_VERSION)
	    .tag('br')."\n";
    foreach (array('ctype', 'date', 'pcre', 'session') as $ext) {
	$o .= (extension_loaded($ext) ? $ok : $fail)
		.'&nbsp;&nbsp;'.sprintf($ptx['syscheck_extension'], $ext).tag('br')."\n";
    }
    $o .= (!get_magic_quotes_runtime() ? $ok : $fail)
	    .'&nbsp;&nbsp;'.$ptx['syscheck_magic_quotes'].tag('br').tag('br')."\n";
    $o .= (strtoupper($tx['meta']['codepage']) == 'UTF-8' ? $ok : $warn)
	    .'&nbsp;&nbsp;'.$ptx['syscheck_encoding'].tag('br')."\n";
    $o .= (file_exists($pth['folder']['plugins'].'jquery/jquery.inc.php') ? $ok : $fail)
	    .'&nbsp;&nbsp;'.$ptx['syscheck_jquery'].tag('br').tag('br')."\n";
    foreach (array('config/', 'css/', 'languages/') as $folder) {
	$folder = $pth['folder']['plugins'].'uploader/'.$folder;
	$o .= (is_writable($folder) ? $ok : $warn)
		.'&nbsp;&nbsp;'.sprintf($ptx['syscheck_writable'], $folder).tag('br')."\n";
    }
    return $o;
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

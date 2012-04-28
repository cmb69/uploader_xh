<?php

/**
 * Back-end functionality of Uploader_XH.
 * Copyright (c) 2011-2012 Christoph M. Becker (see license.txt)
 */


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
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


/**
 * Plugin administration
 */
if (!empty($uploader)) {
    $o .= print_plugin_admin('on');

    switch ($admin) {
	case '':
	    $o .= uploader_version().tag('hr').uploader_system_check();
	    break;
	case 'plugin_main':
	    $o .= uploader_admin_main();
	    break;
	default:
	    $o .= plugin_admin_common($plugin, $admin, $action);
    }
}

?>

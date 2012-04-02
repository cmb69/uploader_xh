<?php

/**
 * Back-end functionality of Uploader_XH.
 * Copyright (c) 2011-2012 Christoph M. Becker (see license.txt)
 */


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


define('UPLOADER_VERSION', '1alpha3');


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
	    .'<p>Version: '.UPLOADER_VERSION.'</p>'."\n"
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
	    .tag('br').tag('br')."\n";
    foreach (array('date', 'pcre', 'session') as $ext) {
	$o .= (extension_loaded($ext) ? $ok : $fail)
		.'&nbsp;&nbsp;'.sprintf($ptx['syscheck_extension'], $ext).tag('br')."\n";
    }
    $o .= tag('br').(!get_magic_quotes_runtime() ? $ok : $fail)
	    .'&nbsp;&nbsp;'.$ptx['syscheck_magic_quotes'].tag('br')."\n";
    $o .= (strtoupper($tx['meta']['codepage']) == 'UTF-8' ? $ok : $warn)
	    .'&nbsp;&nbsp;'.$ptx['syscheck_encoding'].tag('br').tag('br')."\n";
    foreach (array('config/', 'css/', 'languages/', 'lib/i18n/') as $folder) {
	$folder = $pth['folder']['plugins'].'uploader/'.$folder;
	$o .= (is_writable($folder) ? $ok : $warn)
		.'&nbsp;&nbsp;'.sprintf($ptx['syscheck_writable'], $folder).tag('br')."\n";
    }
    return $o;
}


function uploader_select_onchange($param) {
    global $sn;

    $url = $sn.'?&amp;uploader&amp;admin=plugin_main&amp;action=plugin_text';
    if ($param != 'type') {$url .= '&amp;type='.UPLOADER_TYPE;}
    if ($param != 'subdir') {$url .= '&amp;subdir='.UPLOADER_SUBDIR;}
    if ($param != 'resize') {$url .= '&amp;resize='.UPLOADER_RESIZE;}
    $url .= '&amp;'.$param.'=';
    return 'window.location.href=\''.$url.'\'+document.getElementById(encodeURIComponent(\'uploader-'.$param.'\')).value';
}


function uploader_type_select() {
    global $pth, $sn, $plugin_tx, $uploader_types;

    $o = '<select id="uploader-type" title="'.$plugin_tx['uploader']['label_type'].'"'
	    .' onchange="'.uploader_select_onchange('type').'">'."\n";
    foreach ($uploader_types as $type) {
	if (isset($pth['folder'][$type])) {
	    $sel = $type == UPLOADER_TYPE ? ' selected="selected"' : '';
	    $o .= '<option value="'.$type.'"'.$sel.'>'.$type.'</option>'."\n";
	}
    }
    $o .= '</select>'."\n";
    return $o;
}


function uploader_subdir_select_rec($parent) {
    global $pth;

    $o = '';
    $dn = $pth['folder'][UPLOADER_TYPE].$parent;
    $dh = opendir($dn); // TODO: error handling
    while (($fn = readdir($dh)) !== FALSE) {
	if (strpos($fn, '.') !== 0 && is_dir($pth['folder'][UPLOADER_TYPE].$parent.$fn)) {
	    $dir = $parent.$fn.'/';
	    $sel = $dir == UPLOADER_SUBDIR ? ' selected="selected"' : '';
	    $o .= '<option value="'.$dir.'"'.$sel.'>'.$dir.'</option>'."\n";
	    $o .= uploader_subdir_select_rec($dir);
	}
    }
    closedir($dh);
    return $o;
}


function uploader_subdir_select() {
    global $pth, $sn, $plugin_tx;

    return '<select id="uploader-subdir" title="'.$plugin_tx['uploader']['label_subdir'].'"'
	    .' onchange="'.uploader_select_onchange('subdir').'">'."\n"
	    .'<option>/</option>'."\n"
	    .uploader_subdir_select_rec('')
	    .'</select>'."\n";
}


function uploader_resize_select() {
    global $plugin_tx, $uploader_sizes;

    $o = '<select id="uploader-resize" title="'.$plugin_tx['uploader']['label_resize'].'"'
	    .' onchange="'.uploader_select_onchange('resize').'">'."\n";
    foreach ($uploader_sizes as $size) {
	$sel = $size == UPLOADER_RESIZE ? ' selected="selected"' : '';
	$o .= '<option value="'.$size.'"'.$sel.'>'.$size.'</option>'."\n";
    }
    $o .= '</select>'."\n";
    return $o;
}



function uploader_admin_main() {
    global $pth, $uploader_types, $uploader_sizes;

    include_once $pth['folder']['plugins'].'uploader/init.php';
    define('UPLOADER_TYPE',
	    isset($_GET['type']) && in_array($_GET['type'], $uploader_types) && isset($pth['folder'][$_GET['type']])
	    ? $_GET['type'] : 'images');
    define('UPLOADER_SUBDIR',
	    isset($_GET['subdir']) && file_exists($pth['folder'][UPLOADER_TYPE].$_GET['subdir'])
	    ? $_GET['subdir'] : '');
    define('UPLOADER_RESIZE',
	    isset($_GET['resize']) && in_array($_GET['resize'], $uploader_sizes)
	    ? $_GET['resize'] : '');
    return '<div id="uploader-controls">'.uploader_type_select().uploader_subdir_select().uploader_resize_select().'</div>'."\n"
	    .'<iframe frameBorder="0" width="100%" height="400px" src="'
		.$pth['folder']['plugins'].'uploader/uploader.php?type='
		.UPLOADER_TYPE.'&amp;subdir='.UPLOADER_SUBDIR.'&amp;resize='.UPLOADER_RESIZE.'"></iframe>'."\n";
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

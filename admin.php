<?php

/**
 * Back-end functionality of Uploader_XH.
 * Copyright (c) 2011 Christoph M. Becker (see license.txt)
 */
 
// utf-8-marker: äöüß


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


define('UPLOADER_VERSION', '1alpha1');


/**
 * Returns (x)html plugin version information.
 *
 * @return string
 */
function uploader_version() {
    return '<h1>Uploader_XH</h1>'."\n"
	    .'<p>Version: '.UPLOADER_VERSION.'</p>'."\n"
	    .'<p>Copyright &copy; 2011 Christoph M. Becker</p>'."\n"
	    .'<p>Uploader_XH is powered by '
	    .'<a href="http://www.plupload.com/">Plupload</a>.</p>'."\n"
	    .'<p style="text-align:justify">This program is free software: you can redistribute it and/or modify'
	    .' it under the terms of the GNU General Public License as published by'
	    .' the Free Software Foundation, either version 3 of the License, or'
	    .' (at your option) any later version.</p>'."\n"
	    .'<p style="text-align:justify">This program is distributed in the hope that it will be useful,'
	    .' but WITHOUT ANY WARRANTY; without even the implied warranty of'
	    .' MERCHAN&shy;TABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the'
	    .' GNU General Public License for more details.</p>'."\n"
	    .'<p style="text-align:justify">You should have received a copy of the GNU General Public License'
	    .' along with this program.  If not, see'
	    .' <a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.</p>'."\n";
}


/**
 * Returns requirements information.
 *
 * @return string
 */
function uploader_system_check() {
    //TODO
    return '<h4>System Check</h4>'."\n"
	    .'<p>Hopefully everything\'s alright. ;-)</p>'."\n";
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
    
    $htm = '<select id="uploader-type" title="'.$plugin_tx['uploader']['label_type'].'"'
	    .' onchange="'.uploader_select_onchange('type').'">'."\n";
    foreach ($uploader_types as $type) {
	if (isset($pth['folder'][$type])) {
	    $sel = $type == UPLOADER_TYPE ? ' selected="selected"' : '';
	    $htm .= '<option value="'.$type.'"'.$sel.'>'.$type.'</option>'."\n";
	}
    }
    $htm .= '</select>'."\n";
    return $htm;
}


function uploader_subdir_select_rec($parent) {
    global $pth;

    $htm = '';
    $dn = $pth['folder'][UPLOADER_TYPE].$parent;
    $dh = opendir($dn);
    while (($fn = readdir($dh)) !== FALSE) {
	if (strpos($fn, '.') !== 0 && is_dir($pth['folder'][UPLOADER_TYPE].$parent.$fn)) {
	    $dir = $parent.$fn.'/';
	    $sel = $dir == UPLOADER_SUBDIR ? ' selected="selected"' : '';
	    $htm .= '<option value="'.$dir.'"'.$sel.'>'.$dir.'</option>'."\n";
	    $htm .= uploader_subdir_select_rec($dir);
	}
    }
    closedir($dh);
    return $htm;
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
    
    $htm = '<select id="uploader-resize" title="'.$plugin_tx['uploader']['label_resize'].'"'
	    .' onchange="'.uploader_select_onchange('resize').'">'."\n";
    foreach ($uploader_sizes as $size) {
	$sel = $size == UPLOADER_RESIZE ? ' selected="selected"' : '';
	$htm .= '<option value="'.$size.'"'.$sel.'>'.$size.'</option>'."\n";
    }
    $htm .= '</select>'."\n";
    return $htm;
}



function uploader_admin_main() {
    global $pth, $uploader_types, $uploader_sizes;
    
    include_once($pth['folder']['plugins'].'uploader/init.php');
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
	    .'<iframe frameborder="0" width="100%" height="400px" src="'
		.$pth['folder']['plugins'].'uploader/uploader.php?type='
		.UPLOADER_TYPE.'&amp;subdir='.UPLOADER_SUBDIR.'&amp;resize='.UPLOADER_RESIZE.'"></iframe>'."\n";
}


/**
 * Plugin administration
 */
initvar('uploader');
if (!empty($uploader)) {
    initvar('admin');
    initvar('action');
    
    $o .= print_plugin_admin('on');
    
    switch ($admin) {
	case '':
	    $o .= uploader_version().uploader_system_check();
	    break;
	case 'plugin_main':
	    $o .= uploader_admin_main();
	    break;
	default:
	    $o .= plugin_admin_common($plugin, $admin, $action);
    }
}

?>

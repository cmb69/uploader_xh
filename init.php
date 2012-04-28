<?php

/**
 * Initialization of Uploader_XH.
 *
 * Copyright (c) by 2011-2012 Christoph M. Becker
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


function uploader_select_onchange($param, $params, $anchor = NULL) {
    global $sn;

    $url = $sn.'?'.$params;
    if ($param != 'type') {$url .= '&amp;uploader_type='.urlencode(UPLOADER_TYPE);}
    if ($param != 'subdir') {$url .= '&amp;uploader_subdir='.urlencode(UPLOADER_SUBDIR);}
    if ($param != 'resize') {$url .= '&amp;uploader_resize='.urlencode(UPLOADER_RESIZE);}
    $url .= '&amp;uploader_'.$param.'=';
    $js = 'window.location.href=\''.$url.'\'+encodeURIComponent(document.getElementById(\'uploader-'.$param.'\').value)';
    if (isset($anchor)) {$js .= '+\'#'.$anchor.'\'';}
    return $js;
}


function uploader_type_select($params, $anchor = NULL) {
    global $pth, $sn, $plugin_tx, $uploader_types;

    $o = '<select id="uploader-type" title="'.$plugin_tx['uploader']['label_type'].'"'
	    .' onchange="'.uploader_select_onchange('type', $params, $anchor).'">'."\n";
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


function uploader_subdir_select($params, $anchor = NULL) {
    global $pth, $sn, $plugin_tx;

    return '<select id="uploader-subdir" title="'.$plugin_tx['uploader']['label_subdir'].'"'
	    .' onchange="'.uploader_select_onchange('subdir', $params, $anchor).'">'."\n"
	    .'<option>/</option>'."\n"
	    .uploader_subdir_select_rec('')
	    .'</select>'."\n";
}


function uploader_resize_select($params, $anchor = NULL) {
    global $plugin_tx, $uploader_sizes;

    $o = '<select id="uploader-resize" title="'.$plugin_tx['uploader']['label_resize'].'"'
	    .' onchange="'.uploader_select_onchange('resize', $params, $anchor).'">'."\n";
    foreach ($uploader_sizes as $size) {
	$sel = $size == UPLOADER_RESIZE ? ' selected="selected"' : '';
	$o .= '<option value="'.$size.'"'.$sel.'>'.$size.'</option>'."\n";
    }
    $o .= '</select>'."\n";
    return $o;
}


/**
 * Initializes the uploader session.
 *
 * @return void
 */
function uploader_init() {
    global $pth, $sl, $cf, $tx, $plugin_cf, $plugin_tx, $uploader_types, $uploader_sizes;

    $uploader_types = array('images', 'downloads', 'media', 'userfiles');
    $uploader_sizes = array('', 'small', 'medium', 'large');
    define('UPLOADER_TYPE',
	    isset($_GET['uploader_type']) && in_array($_GET['uploader_type'], $uploader_types) && isset($pth['folder'][$_GET['uploader_type']])
	    ? $_GET['uploader_type'] : 'images');
    $subdir = isset($_GET['uploader_subdir']) ? preg_replace('/\.\.[\/\\\\]?/', '', stsl($_GET['uploader_subdir'])) : '';
    define('UPLOADER_SUBDIR',
	    isset($_GET['uploader_subdir']) && is_dir($pth['folder'][UPLOADER_TYPE].$subdir)
	    ? $subdir : '');
    define('UPLOADER_RESIZE',
	    isset($_GET['uploader_resize']) && in_array($_GET['uploader_resize'], $uploader_sizes)
	    ? $_GET['uploader_resize'] : '');
    $pcf = $plugin_cf['uploader'];
    $ptx = $plugin_tx['uploader'];
    if (session_id() == '') {session_start();}
    $_SESSION['uploader_runtimes'] = $pcf['runtimes'];
    foreach ($uploader_types as $type) {
	if (isset($pth['folder'][$type])) {
	    $_SESSION['uploader_folder'][$type] = dirname($_SERVER['SCRIPT_FILENAME']).'/'.$pth['folder'][$type];
	    $_SESSION['uploader_title'][$type] = $tx['title'][$type];
	    $_SESSION['uploader_exts'][$type] = $pcf['ext_'.$type];
	} else {
	    unset($_SESSION['uploader_folder'][$type], $_SESSION['uploader_title'][$type],
		    $_SESSION['uploader_exts'][$type]);
	}
    }
    $_SESSION['uploader_max_size'] = $pcf['size_max'];
    $_SESSION['uploader_lang'] = strlen($sl) == 2 ? $sl : $cf['language']['default'];
    $_SESSION['uploader_chunking'] = empty($pcf['size_chunk'])
	    ? '' : 'chunk_size: \''.$pcf['size_chunk'].'\','."\n";
    foreach (array_slice($uploader_sizes, 1) as $size) {
	foreach (array('width', 'height', 'quality') as $attr) {
	    $_SESSION['uploader_resize'][$size][$attr] = $pcf['resize-'.$size.'_'.$attr];
	}
    }
    $_SESSION['uploader_message']['no_js'] = $ptx['message_no_js'];
}


/**
 * Include config and language file, if not already done.
 */
if (!isset($plugin_cf['uploader'])) {
    include $pth['folder']['plugins'].'config/config.php';
}
if (!isset($plugin_tx['uploader'])) {
    include $pth['folder']['plugins'].'languages/'.$sl.'.php';
}


/**
 * Initialize the uploader.
 */
uploader_init();

?>

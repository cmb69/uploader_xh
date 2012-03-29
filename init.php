<?php

/**
 * Initialization of Uploader_XH.
 *
 * Copyright (c) by 2011 Christoph M. Becker
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

// utf-8-marker: äöüß


$uploader_types = array('images', 'downloads', 'media', 'userfiles');
$uploader_sizes = array('', 'small', 'medium', 'large');


/**
 * Initializes the uploader session.
 *
 * @return void
 */
function uploader_init() {
    global $pth, $sl, $cf, $plugin_cf, $plugin_tx, $uploader_types, $uploader_sizes;

    $pcf = $plugin_cf['uploader'];
    $ptx = $plugin_tx['uploader'];
    if (!isset($_SESSION)) {session_start();}
    $uploader =& $_SESSION['uploader'];
    $uploader = NULL;
    $uploader['runtimes'] = $pcf['runtimes'];
    foreach ($uploader_types as $type) {
	if (isset($pth['folder'][$type])) {
	    $uploader['folder'][$type] = dirname($_SERVER['SCRIPT_FILENAME']).'/'.$pth['folder'][$type];
	    $uploader['title'][$type] = $ptx['title_'.$type];
	    $uploader['exts'][$type] = $pcf['ext_'.$type];
	}
    }
    $uploader['max_size'] = $pcf['size_max'];
    $uploader['lang'] = strlen($sl) == 2 ? $sl : $cf['language']['default'];
    $uploader['chunking'] = empty($pcf['size_chunk']) ? '' : 'chunk_size : \''.$pcf['size_chunk'].'\','."\n";
    //$uploader['title'] = $ptx['title_'.UPLOADER_TYPE];
    //$uploader['exts'] = $pcf['ext_'.UPLOADER_TYPE];
    foreach (array_slice($uploader_sizes, 1) as $size) {
	foreach (array('width', 'height', 'quality') as $attr) {
	    $uploader['resize'][$size][$attr] = $pcf['resize-'.$size.'_'.$attr];
	}
    }
}


uploader_init();

?>

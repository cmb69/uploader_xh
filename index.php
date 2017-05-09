<?php

/**
 * Copyright 2011-2017 Christoph M. Becker
 *
 * This file is part of Uploader_XH.
 *
 * Uploader_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Uploader_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Uploader_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

/**
 * The plugin version.
 */
define('UPLOADER_VERSION', '@UPLOADER_VERSION@');

/**
 * Returns the uploader widget.
 *
 * @param string $type      The upload type ('images', 'downloads', 'media' or
 *                          'userfiles'). '*' displays a selectbox.
 * @param string $subdir    The subfolder of the configured folder of the type.
 *                          '*' displays a selectbox.
 * @param string $resize    The resize mode ('', 'small', 'medium' or 'large').
 *                          '*' displays a selectbox.
 * @param bool   $collapsed Whether the uploader widget should be collapsed.
 *
 * @return string (X)HTML.
 */
function uploader($type = 'images', $subdir = '', $resize = '', $collapsed = false)
{
    return Uploader\Controller::main($type, $subdir, $resize, $collapsed);
}

/**
 * Returns the collapsed uploader widget. This is a convenience function.
 *
 * @param string $type   The upload type ('images', 'downloads', 'media' or
 *                       'userfiles'). Use '*' to display a selectbox to the user.
 * @param string $subdir The subfolder of the configured folder of the type.
 *                       Use '*' to display a selectbox to the user.
 * @param string $resize The resize mode ('', 'small', 'medium' or 'large').
 *                       Use '*' to display a selectbox to the user.
 *
 * @return string (X)HTML.
 *
 * @access public
 */
function uploader_collapsed($type = 'images', $subdir = '', $resize = '')
{
    return Uploader\Controller::main($type, $subdir, $resize, true);
}

Uploader\Controller::dispatch();

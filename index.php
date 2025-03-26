<?php

/**
 * Copyright 2011-2017 Christoph M. Becker
 *
 * This file is part of Uploader_XH.
 *
 * Uploader_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Uploader_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with Uploader_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

use Uploader\Dic;

const UPLOADER_VERSION = "1.0beta2";

/**
 * Returns the uploader widget.
 *
 * @param string $type      The upload type ('images', 'downloads', 'media' or
 *                          'userfiles'). '*' displays a selectbox.
 * @param string $subdir    The subfolder of the configured folder of the type.
 *                          '*' displays a selectbox.
 * @param string $resize    The resize mode ('', 'small', 'medium' or 'large').
 *                          '*' displays a selectbox.
 *
 * @return string|never
 */
function uploader($type = 'images', $subdir = '', $resize = '')
{
    return Dic::makeUploadController()($type, $subdir, $resize)();
}

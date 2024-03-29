<?php

/**
 * Copyright 2023 Christoph M. Becker
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

const CMSIMPLE_XH_VERSION = "CMSimple_XH 1.7.5";
const UPLOADER_VERSION = "1.0beta2";

require_once "../../cmsimple/functions.php";

require_once "./classes/FilesizeException.php";
require_once "./classes/FileSystemService.php";
require_once "./classes/HtmlString.php";
require_once "./classes/InfoController.php";
require_once "./classes/Jquery.php";
require_once "./classes/Receiver.php";
require_once "./classes/ReadException.php";
require_once "./classes/Response.php";
require_once "./classes/UploadController.php";
require_once "./classes/SystemChecker.php";
require_once "./classes/Url.php";
require_once "./classes/View.php";
require_once "./classes/WriteException.php";

<?php

const CMSIMPLE_XH_VERSION = "CMSimple_XH 1.7.5";
const UPLOADER_VERSION = "1.0beta2";

require_once "../../cmsimple/functions.php";

require_once "../plib/classes/Jquery.php";
require_once "../plib/classes/Request.php";
require_once "../plib/classes/Response.php";
require_once "../plib/classes/SystemChecker.php";
require_once "../plib/classes/UploadedFile.php";
require_once "../plib/classes/Url.php";
require_once "../plib/classes/View.php";
require_once "../plib/classes/FakeRequest.php";
require_once "../plib/classes/FakeSystemChecker.php";

require_once "./classes/Dic.php";
require_once "./classes/FilesizeException.php";
require_once "./classes/FileSystemService.php";
require_once "./classes/InfoController.php";
require_once "./classes/Receiver.php";
require_once "./classes/ReadException.php";
require_once "./classes/UploadController.php";
require_once "./classes/WriteException.php";

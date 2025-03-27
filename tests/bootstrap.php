<?php

const CMSIMPLE_XH_VERSION = "CMSimple_XH 1.7.5";
const UPLOADER_VERSION = "1.0";

require_once "../../cmsimple/functions.php";

require_once "../plib/classes/CsrfProtector.php";
require_once "../plib/classes/Jquery.php";
require_once "../plib/classes/Request.php";
require_once "../plib/classes/Response.php";
require_once "../plib/classes/SystemChecker.php";
require_once "../plib/classes/UploadedFile.php";
require_once "../plib/classes/Url.php";
require_once "../plib/classes/View.php";
require_once "../plib/classes/FakeRequest.php";
require_once "../plib/classes/FakeSystemChecker.php";

require_once "./classes/model/FilesizeException.php";
require_once "./classes/model/FileSystemService.php";
require_once "./classes/model/Receiver.php";
require_once "./classes/model/ReadException.php";
require_once "./classes/model/WriteException.php";
require_once "./classes/Dic.php";
require_once "./classes/InfoController.php";
require_once "./classes/UploadController.php";

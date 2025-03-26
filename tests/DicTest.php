<?php

namespace Uploader;

use PHPUnit\Framework\TestCase;

class DicTest extends TestCase
{
    public function setUp(): void
    {
        global $sn, $pth, $plugin_cf, $plugin_tx;

        $sn = "/";
        $pth = ["folder" => ["downloads" => "", "images" => "", "media" => "", "plugins" => "", "userfiles" => ""]];
        $plugin_cf = ["uploader" => []];
        $plugin_tx = ["uploader" => []];
    }

    public function testMakesUploadController(): void
    {
        $this->assertInstanceOf(UploadController::class, Dic::makeUploadController());
    }

    public function testMakesInfoController(): void
    {
        $this->assertInstanceOf(InfoController::class, Dic::makeInfoController());
    }
}

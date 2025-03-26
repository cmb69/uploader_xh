<?php

namespace Uploader;

use PHPUnit\Framework\TestCase;

class DicTest extends TestCase
{
    public function setUp(): void
    {
        global $pth, $plugin_cf, $plugin_tx;

        $pth = ["folder" => ["downloads" => "", "images" => "", "media" => "", "plugins" => "", "userfiles" => ""]];
        $plugin_cf = ["uploader" => ["size_max" => ""]];
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

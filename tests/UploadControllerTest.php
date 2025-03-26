<?php

namespace Uploader;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\FakeRequest;
use Plib\Jquery;
use Plib\View;

class UploadControllerTest extends TestCase
{
    /** @var UploadController */
    private $sut;

    /** @var Jquery&MockObject */
    private $jquery;

    /** @var FileSystemService&MockObject */
    private $fileSystemService;

    public function setUp(): void
    {
        $plugin_cf = XH_includeVar("./config/config.php", 'plugin_cf');
        $conf = $plugin_cf['uploader'];
        $plugin_tx = XH_includeVar("./languages/en.php", 'plugin_tx');
        $lang = $plugin_tx['uploader'];
        $fileFolders = [
            'images' => 'irrelevant_images',
            'downloads' => 'irrelevant_downloads',
            'media' => 'irrelevant_media',
            'userfiles' => 'irrelevant_userfiles',
        ];
        $this->jquery = $this->createStub(Jquery::class);
        $this->fileSystemService = $this->createStub(FileSystemService::class);
        $this->sut = new UploadController(
            1,
            $conf,
            "./",
            $fileFolders,
            $this->jquery,
            $this->fileSystemService,
            "2M",
            new View("./views/", $lang)
        );
    }

    public function testDefaultActionRendersPlaceholder(): void
    {
        $response = ($this->sut)(new FakeRequest(), null, null, null, true);
        $this->assertSame("Uploader – Upload", $response->title());
        Approvals::verifyHtml($response->output());
    }

    public function testWidgetActionRendersWidget(): void
    {
        $this->fileSystemService->method('getSubdirsOf')->willReturn(["/"]);
        $request = new FakeRequest(["url" => "http://example.com/?&uploader_serial=1"]);
        $response = ($this->sut)($request, null, null, null, false);
        Approvals::verifyHtml($response->output());
    }

    public function testIgnoresUnrelatedRequests(): void
    {
        $this->fileSystemService->method('getSubdirsOf')->willReturn(["/"]);
        $request = new FakeRequest(["url" => "http://example.com/?&uploader_serial=17"]);
        $response = ($this->sut)($request, null, null, null, false);
        $this->assertSame("", $response->output());
    }

    public function testRendersDynamicWidget(): void
    {
        $this->fileSystemService->method('getSubdirsOf')->willReturn(["/foo"]);
        $this->fileSystemService->method("isDir")->willReturn(true);
        $request = new FakeRequest([
            "url" => "http://example.com/?&uploader_type=downloads&uploader_subdir=%2Ffoo&uploader_serial=1",
        ]);
        $response = ($this->sut)($request, null, null, null, false);
        Approvals::verifyHtml($response->output());
    }
}
